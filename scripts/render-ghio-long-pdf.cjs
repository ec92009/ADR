#!/usr/bin/env node

const fs = require("node:fs");
const os = require("node:os");
const path = require("node:path");
const { spawnSync } = require("node:child_process");

function loadPlaywright() {
  try {
    return require("playwright");
  } catch (error) {
    const bundled = path.join(
      os.homedir(),
      ".cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright"
    );
    return require(bundled);
  }
}

const { chromium } = loadPlaywright();

const repoRoot = path.resolve(__dirname, "..");
const siteBase = (process.env.ADR_GHIO_BASE || "https://ec92009.github.io/ADR").replace(/\/+$/, "");
const outputDir = process.env.ADR_PDF_OUT_DIR
  ? path.resolve(process.env.ADR_PDF_OUT_DIR)
  : path.join(repoRoot, "output/pdf/ghio-daily");
const bundledPython = path.join(
  os.homedir(),
  ".cache/codex-runtimes/codex-primary-runtime/dependencies/python/bin/python3"
);
const pythonBin = process.env.ADR_PYTHON || (fs.existsSync(bundledPython) ? bundledPython : "python3");

const pages = [
  ["Accueil", "/"],
  ["Cabinet", "/cabinet.html"],
  ["Assurance de prêt", "/assurance-de-pret.html"],
  ["Particuliers", "/particuliers.html"],
  ["Professionnels", "/professionnels.html"],
  ["Courtier / Contact", "/courtier.html"],
  ["Demande de devis", "/demande-de-devis.html"],
  ["Mentions légales", "/mentions-legales.html"],
  ["Politique de confidentialité", "/politique-de-confidentialite.html"],
  ["Politique de cookies UE", "/politique-de-cookies-ue.html"],
  ["Cookies traceurs", "/cookies-traceurs.html"],
].map(([title, route]) => ({ title, route, url: `${siteBase}${route}` }));

function parisParts(date) {
  const formatter = new Intl.DateTimeFormat("en-CA", {
    timeZone: "Europe/Paris",
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    hour12: false,
  });
  const parts = Object.fromEntries(formatter.formatToParts(date).map((part) => [part.type, part.value]));
  return {
    date: `${parts.year}-${parts.month}-${parts.day}`,
    stamp: `${parts.year}-${parts.month}-${parts.day}T${parts.hour}-${parts.minute}-${parts.second}`,
    human: `${parts.year}-${parts.month}-${parts.day} ${parts.hour}:${parts.minute}:${parts.second} Europe/Paris`,
  };
}

function slug(value) {
  return value
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-|-$/g, "");
}

function versionSlug(version) {
  const normalized = /^v/i.test(version) ? version : `v${version}`;
  return normalized.replace(/[^a-z0-9._-]+/gi, "-").replace(/^-|-$/g, "") || "version-unknown";
}

function uniquePathWithoutOverwrite(targetPath, fallbackSuffix) {
  if (!fs.existsSync(targetPath)) return targetPath;

  const parsed = path.parse(targetPath);
  let candidate = path.join(parsed.dir, `${parsed.name}-${fallbackSuffix}${parsed.ext}`);
  let counter = 2;
  while (fs.existsSync(candidate)) {
    candidate = path.join(parsed.dir, `${parsed.name}-${fallbackSuffix}-${counter}${parsed.ext}`);
    counter += 1;
  }
  return candidate;
}

function findVersion(html) {
  const footerMatch = html.match(/class=["'][^"']*\badr-version-footer\b[^"']*["'][^>]*>\s*(v\d+\.\d+)/i);
  if (footerMatch) return footerMatch[1];

  const labelMatch = html.match(/Version du site[\s\S]{0,160}?(v\d+\.\d+)/i);
  if (labelMatch) return labelMatch[1];

  return "version inconnue";
}

async function renderCover(browser, tmpDir, generated, version) {
  const cover = await browser.newPage({ viewport: { width: 1440, height: 1200 }, deviceScaleFactor: 1 });
  const pageList = pages
    .map((entry, index) => `<li><strong>${index + 1}. ${entry.title}</strong><br><span>${entry.url}</span></li>`)
    .join("");
  await cover.setContent(`<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Assurances de Rueil - export GH.io</title>
  <style>
    * { box-sizing: border-box; }
    html, body { margin: 0; min-height: 100%; }
    body {
      font-family: Arial, Helvetica, sans-serif;
      color: #08213b;
      background: #f3f7fb;
      padding: 96px;
    }
    .sheet {
      min-height: 1844px;
      border: 2px solid #c7d7e8;
      border-radius: 28px;
      background: #fff;
      padding: 76px 84px;
      box-shadow: 0 30px 90px rgba(10, 68, 100, 0.16);
    }
    h1 {
      margin: 0 0 24px;
      color: #003478;
      font-size: 76px;
      line-height: 0.98;
      letter-spacing: 0;
      text-transform: uppercase;
    }
    .meta {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 18px;
      margin: 48px 0 56px;
    }
    .meta div {
      border-left: 8px solid #d7a53c;
      background: #edf4fb;
      padding: 22px 26px;
      border-radius: 12px;
      font-size: 24px;
      line-height: 1.38;
    }
    .label {
      display: block;
      color: #607187;
      font-size: 17px;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      margin-bottom: 8px;
    }
    h2 {
      color: #003478;
      font-size: 36px;
      margin: 0 0 26px;
      text-transform: uppercase;
    }
    ol { margin: 0; padding-left: 32px; }
    li {
      margin: 0 0 18px;
      padding-left: 10px;
      font-size: 23px;
      line-height: 1.35;
    }
    li span {
      color: #607187;
      font-size: 18px;
      word-break: break-all;
    }
  </style>
</head>
<body>
  <main class="sheet">
    <h1>Assurances de Rueil<br>Export GH.io</h1>
    <section class="meta">
      <div><span class="label">Source</span>${siteBase}/</div>
      <div><span class="label">Version détectée</span>${version}</div>
      <div><span class="label">Généré le</span>${generated.human}</div>
      <div><span class="label">Pages exportées</span>${pages.length}</div>
    </section>
    <h2>Sommaire</h2>
    <ol>${pageList}</ol>
  </main>
</body>
</html>`, { waitUntil: "load" });
  const coverPath = path.join(tmpDir, "00-cover.pdf");
  await cover.pdf({
    path: coverPath,
    width: "1440px",
    height: "2036px",
    margin: { top: "0", right: "0", bottom: "0", left: "0" },
    printBackground: true,
  });
  await cover.close();
  return coverPath;
}

async function renderPage(browser, entry, index, tmpDir, cacheToken) {
  const page = await browser.newPage({ viewport: { width: 1440, height: 1200 }, deviceScaleFactor: 1 });
  page.setDefaultTimeout(90_000);
  const url = `${entry.url}${entry.url.includes("?") ? "&" : "?"}pdf=${cacheToken}`;
  await page.goto(url, { waitUntil: "domcontentloaded", timeout: 90_000 });
  await page.waitForLoadState("networkidle", { timeout: 20_000 }).catch(() => {});
  await page.addStyleTag({
    content: `
      html, body { scroll-behavior: auto !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
      #cookie-law-info-bar,
      #cookie-law-info-again,
      .cli_settings_button,
      .cli-modal,
      .wt-cli-cookie-bar-container,
      .cky-consent-container,
      .cky-modal,
      .cky-overlay,
      .cky-btn-revisit-wrapper,
      .cli-modal-backdrop,
      .cli-popupbar-overlay,
      .modal-backdrop,
      .mfp-bg,
      #cliSettingsPopup,
      .elementor-popup-modal,
      .grecaptcha-badge {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
      }
      html, body {
        overflow: visible !important;
        opacity: 1 !important;
        filter: none !important;
      }
      body.page-id-8093 .xs-breadcrumb,
      body.page-id-8093 #sidebar,
      body.page-id-8093 .col-lg-4:has(#sidebar),
      body.page-id-8093 .entry-footer {
        display: none !important;
      }
      body.page-id-8093 #main-content,
      body.page-id-8093 #main-content > .container,
      body.page-id-8093 #main-content .row,
      body.page-id-8093 #main-content .col-lg-8,
      body.page-id-8093 #main-content .single-content,
      body.page-id-8093 #main-content .entry-content,
      body.page-id-8093 #main-content article,
      body.page-id-8093 #main-content .post-body {
        width: 100% !important;
        max-width: none !important;
        flex: none !important;
        margin: 0 !important;
        padding: 0 !important;
      }
    `,
  });
  await page.emulateMedia({ media: "screen" });
  const html = await page.content();
  const pdfPath = path.join(tmpDir, `${String(index + 1).padStart(2, "0")}-${slug(entry.title)}.pdf`);
  await page.pdf({
    path: pdfPath,
    width: "1440px",
    height: "2036px",
    margin: { top: "0", right: "0", bottom: "0", left: "0" },
    printBackground: true,
    preferCSSPageSize: false,
  });
  await page.close();
  return { pdfPath, version: index === 0 ? findVersion(html) : null };
}

function mergePdfs(inputPaths, outputPath, metadata) {
  const mergeScript = path.join(path.dirname(outputPath), "merge-pdfs.py");
  fs.writeFileSync(mergeScript, `from pathlib import Path
import sys
from pypdf import PdfReader, PdfWriter

out = Path(sys.argv[1])
inputs = [Path(item) for item in sys.argv[2:]]
writer = PdfWriter()
for input_path in inputs:
    reader = PdfReader(str(input_path))
    for page in reader.pages:
        writer.add_page(page)
writer.add_metadata({
    "/Title": "${metadata.title.replace(/"/g, '\\"')}",
    "/Subject": "${metadata.subject.replace(/"/g, '\\"')}",
    "/Creator": "AssurancesDeRueil scripts/render-ghio-long-pdf.cjs",
})
out.parent.mkdir(parents=True, exist_ok=True)
with out.open("wb") as handle:
    writer.write(handle)
`);
  const result = spawnSync(pythonBin, [mergeScript, outputPath, ...inputPaths], { stdio: "inherit" });
  if (result.status !== 0) {
    throw new Error(`PDF merge failed with exit code ${result.status}`);
  }
}

async function main() {
  const generated = parisParts(new Date());
  const cacheToken = `${generated.stamp}-${process.pid}`;
  const tmpDir = path.join(repoRoot, "tmp/pdfs", `ghio-daily-${cacheToken}`);
  fs.mkdirSync(tmpDir, { recursive: true });
  fs.mkdirSync(outputDir, { recursive: true });

  const browser = await chromium.launch({ headless: true });
  const rendered = [];
  let version = "version inconnue";
  try {
    for (let index = 0; index < pages.length; index += 1) {
      const entry = pages[index];
      process.stdout.write(`Rendering ${index + 1}/${pages.length}: ${entry.url}\n`);
      const result = await renderPage(browser, entry, index, tmpDir, cacheToken);
      rendered.push(result.pdfPath);
      if (result.version) version = result.version;
    }
    const coverPath = await renderCover(browser, tmpDir, generated, version);
    const mergedTmp = path.join(tmpDir, "merged.pdf");
    mergePdfs([coverPath, ...rendered], mergedTmp, {
      title: `Assurances de Rueil GH.io ${generated.date}`,
      subject: `${siteBase}/ ${version}`,
    });

    const durableStem = `assurances-de-rueil-ghio-${versionSlug(version)}-${generated.date}`;
    const fallbackSuffix = generated.stamp.replace(/:/g, "-");
    const datedPdf = uniquePathWithoutOverwrite(path.join(outputDir, `${durableStem}.pdf`), fallbackSuffix);
    const latestPdf = path.join(outputDir, "assurances-de-rueil-ghio-latest.pdf");
    const manifestPath = datedPdf.replace(/\.pdf$/i, ".json");
    const latestManifestPath = path.join(outputDir, "assurances-de-rueil-ghio-latest.json");
    fs.renameSync(mergedTmp, datedPdf);
    fs.copyFileSync(datedPdf, latestPdf);
    const stat = fs.statSync(datedPdf);
    const manifest = `${JSON.stringify({
      generatedAt: generated.human,
      source: `${siteBase}/`,
      version,
      pages,
      pdf: datedPdf,
      latestPdf,
      bytes: stat.size,
    }, null, 2)}\n`;
    fs.writeFileSync(manifestPath, manifest);
    fs.writeFileSync(latestManifestPath, manifest);
    process.stdout.write(`PDF saved: ${datedPdf}\n`);
    process.stdout.write(`Latest copy: ${latestPdf}\n`);
    process.stdout.write(`Manifest: ${manifestPath}\n`);
    process.stdout.write(`Latest manifest: ${latestManifestPath}\n`);
    process.stdout.write(`Bytes: ${stat.size}\n`);
  } finally {
    await browser.close();
  }
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
