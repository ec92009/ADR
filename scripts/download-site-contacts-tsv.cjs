#!/usr/bin/env node

const fs = require("node:fs");
const net = require("node:net");
const path = require("node:path");

const repoRoot = path.resolve(__dirname, "..");
const sourcePath = path.join(repoRoot, "wp-live-plugin/adr-site-fixes/includes/quote-requests-export.php");
const outputDir = process.env.ADR_CONTACTS_TSV_OUT_DIR
  ? path.resolve(process.env.ADR_CONTACTS_TSV_OUT_DIR)
  : path.join(repoRoot, "output/pdf/official-daily");
const siteBase = (process.env.ADR_SITE_BASE || "https://assurancesderueil.fr").replace(/\/+$/, "");
const days = Number.parseInt(process.env.ADR_CONTACTS_DAYS || "7", 10);

function parisDate(date) {
  const formatter = new Intl.DateTimeFormat("en-CA", {
    timeZone: "Europe/Paris",
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  });
  const parts = Object.fromEntries(formatter.formatToParts(date).map((part) => [part.type, part.value]));
  return `${parts.year}-${parts.month}-${parts.day}`;
}

function readAccessKey() {
  const source = fs.readFileSync(sourcePath, "utf8");
  const match = source.match(/define\(\s*'ADR_QUOTE_REQUESTS_ACCESS_KEY'\s*,\s*'([^']+)'\s*\)/);
  if (!match) {
    throw new Error(`Unable to find ADR_QUOTE_REQUESTS_ACCESS_KEY in ${sourcePath}`);
  }
  return match[1];
}

function parseDelimited(text, delimiter) {
  const rows = [];
  let row = [];
  let field = "";
  let inQuotes = false;

  for (let index = 0; index < text.length; index += 1) {
    const char = text[index];
    const next = text[index + 1];

    if (inQuotes) {
      if (char === '"' && next === '"') {
        field += '"';
        index += 1;
      } else if (char === '"') {
        inQuotes = false;
      } else {
        field += char;
      }
      continue;
    }

    if (char === '"') {
      inQuotes = true;
    } else if (char === delimiter) {
      row.push(field);
      field = "";
    } else if (char === "\n") {
      row.push(field);
      rows.push(row);
      row = [];
      field = "";
    } else if (char !== "\r") {
      field += char;
    }
  }

  if (field !== "" || row.length > 0) {
    row.push(field);
    rows.push(row);
  }

  return rows;
}

function formatDelimitedRow(row, delimiter) {
  return row
    .map((field) => {
      const value = String(field ?? "");
      if (value.includes('"') || value.includes("\n") || value.includes("\r") || value.includes(delimiter)) {
        return `"${value.replace(/"/g, '""')}"`;
      }
      return value;
    })
    .join(delimiter);
}

function parseFrenchExportDate(value) {
  const match = String(value || "").match(/^(\d{2})-([A-Z]+)-(\d{4})(?:\s+(\d{2}):(\d{2}))?/);
  if (!match) return null;

  const months = {
    JAN: 0,
    FEV: 1,
    MAR: 2,
    AVR: 3,
    MAI: 4,
    JUIN: 5,
    JUIL: 6,
    AOUT: 7,
    SEP: 8,
    OCT: 9,
    NOV: 10,
    DEC: 11,
  };
  const month = months[match[2]];
  if (month === undefined) return null;

  return new Date(
    Number.parseInt(match[3], 10),
    month,
    Number.parseInt(match[1], 10),
    Number.parseInt(match[4] || "0", 10),
    Number.parseInt(match[5] || "0", 10)
  );
}

function requesterIpInfoUrl(value) {
  const ip = String(value || "").trim();
  if (ip === "" || ip.startsWith("https://ipinfo.io/")) {
    return ip;
  }

  return net.isIP(ip) ? `https://ipinfo.io/${ip}` : ip;
}

function addRequesterIpInfoUrls(rows) {
  const headers = rows[0] || [];
  const ipIndex = headers.findIndex((header) => String(header).trim() === "IP demandeur");
  if (ipIndex === -1) {
    return rows;
  }

  return rows.map((row, index) => {
    if (index === 0) {
      return row;
    }

    const nextRow = [...row];
    nextRow[ipIndex] = requesterIpInfoUrl(nextRow[ipIndex]);
    return nextRow;
  });
}

function normalizeLastDaysTsv(body, daysBack) {
  const text = body.toString("utf8").replace(/^\uFEFF/, "");
  const firstLine = text.split(/\r?\n/, 1)[0] || "";
  const delimiter = firstLine.includes("\t") ? "\t" : ";";
  const rows = parseDelimited(text, delimiter).filter((row) => row.some((field) => String(field).trim() !== ""));
  if (rows.length === 0) {
    throw new Error("Contact export did not contain any rows");
  }

  const headers = rows[0];
  const dateIndex = headers.findIndex((header) => String(header).trim().toLowerCase() === "date");
  const cutoff = new Date();
  cutoff.setDate(cutoff.getDate() - daysBack);

  const filtered = dateIndex === -1
    ? rows
    : [headers, ...rows.slice(1).filter((row) => {
        const parsed = parseFrenchExportDate(row[dateIndex]);
        return parsed ? parsed >= cutoff : true;
      })];
  const linked = addRequesterIpInfoUrls(filtered);

  return Buffer.from(`\uFEFF${linked.map((row) => formatDelimitedRow(row, "\t")).join("\n")}\n`, "utf8");
}

async function main() {
  if (!Number.isInteger(days) || days < 1) {
    throw new Error("ADR_CONTACTS_DAYS must be a positive integer");
  }

  const outputDate = parisDate(new Date());
  const outputPath = path.join(outputDir, `assurances-de-rueil-contacts-last-${days}-days-${outputDate}.tsv`);
  const latestPath = path.join(outputDir, `assurances-de-rueil-contacts-last-${days}-days-latest.tsv`);
  const url = new URL(`${siteBase}/demandes-de-devis/`);
  url.searchParams.set("format", "tsv");
  url.searchParams.set("days", String(days));
  url.searchParams.set("key", readAccessKey());

  fs.mkdirSync(outputDir, { recursive: true });

  const response = await fetch(url, {
    headers: {
      "Accept": "text/tab-separated-values,text/plain,*/*",
      "User-Agent": "AssurancesDeRueil TSV export automation",
    },
  });

  if (!response.ok) {
    throw new Error(`TSV download failed with HTTP ${response.status}`);
  }

  const downloaded = Buffer.from(await response.arrayBuffer());
  if (downloaded.length === 0) {
    throw new Error("TSV download returned an empty response");
  }

  const body = normalizeLastDaysTsv(downloaded, days);

  fs.writeFileSync(outputPath, body);
  fs.copyFileSync(outputPath, latestPath);

  process.stdout.write(`TSV saved: ${outputPath}\n`);
  process.stdout.write(`Latest copy: ${latestPath}\n`);
  process.stdout.write(`Bytes: ${body.length}\n`);
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
