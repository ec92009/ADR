# Assurances de Rueil Working Summary

## Current Site State

- Repository: `ec92009/ADR`.
- GitHub Pages source of truth: `https://ec92009.github.io/ADR/`.
- Live WordPress site: `https://assurancesderueil.fr/`.
- GitHub Pages source-truth version marker: `v135.0`.
- Live WordPress version marker: `v134.0` pending the `v135.0` anti-spam deployment.
- Live WordPress support plugin candidate: `ADR Site Fixes` `135.0`.
- The live WordPress pages now use the approved GH.io-style page shells through `ADR Site Fixes` MU-plugin modules, with WordPress kept only where it must remain dynamic.
- The live child-theme `functions.php` has been reduced from roughly `5,938` lines / `351,567` editor characters to `91` lines / `2,936` editor characters.
- The live WordPress pages have been reconciled against the GH.io source-of-truth mock for high-resolution photography, day/night persistence, the contact/phone form pass, contact message storage/export, and quote payload preservation.
- The GH.io `v119.7` source update keeps static mock forms from posting to live MetForm endpoints, changes phone fields to text inputs with telephone keyboard hints, and preserves international prefixes such as `+34`.
- GH.io and live WordPress both keep the day/night switch choice across page navigation through `adr-theme-persistence.js`.
- GH.io and live WordPress both use the higher-resolution JPEG photography in `assets/adr-photo-*-v119-5.jpg`.
- Public page titles are normalized to `Assurances de Rueil`.
- The live quote page remains backed by MetForm form `2073`; the live contact page remains backed by MetForm form `7487`. Both feed storage, private CSV export, admin delivery, and branded visitor acknowledgements through `ADR Site Fixes`.
- New quote/contact requests now store the requester IP when available and expose `IP demandeur`, `Géolocalisation IP`, and `Cloudflare Ray ID` columns in TSV exports. The local TSV downloader formats populated requester-IP cells as `https://ipinfo.io/<IP address>`. Geolocation is populated only if the origin receives Cloudflare visitor-location headers; the current operational decision is not to add an external IP-geolocation microservice.

## Conversation Summary, 2026-06-27

- Fixed trailing separator behavior in the mock-page list/tag UI and treated that local mock pass as the source for later publishes.
- Promoted the approved GH.io `v119.3` state as the source of truth, then normalized public browser titles to `Assurances de Rueil` and removed remaining awkward `à Rueil-Malmaison` title copy from the page chrome.
- Split live WordPress behavior into the `ADR Site Fixes` Must-Use plugin after confirming the child-theme `functions.php` is too large for reliable browser-editor round trips.
- Replaced low-resolution photos with higher-resolution `adr-photo-*-v119-5.jpg` assets on GH.io, then pushed those image and day/night persistence changes to live WordPress.
- Added and aligned a required `Téléphone *` field on the contact page, changed quote/contact phone fields to `type="text"` with `inputmode="tel"`, and preserved international prefixes such as `+34`.
- Fixed the contact form's `Envoi non autorisé` path by removing stale backend MetForm reCAPTCHA metadata for form `7487` while keeping the public form layout clean.
- Replaced the old plain/centered quote acknowledgement email and then extended the same branded acknowledgement treatment to the contact form.
- Latest live verification includes user-confirmed real submissions: both contact and quote forms sent visitor acknowledgements, Manu received the admin emails, and the private CSV export looked convincing.

## Conversation Summary, 2026-06-28

- Split the oversized live child-theme `functions.php` into the `ADR Site Fixes` MU-plugin modules and confirmed the live theme file is down to `91` lines / `2,936` editor characters.
- Generalized the request export/admin notification path so both quote form `2073` and contact form `7487` are represented in the private CSV and admin emails.
- User-confirmed the real live contact and quote paths: Manu received emails, the user received responses after submitting forms, and the CSV output was convincing.
- Advanced the live MU-plugin to `120.0` so contact form `7487` stores the free-text message under canonical `message`, preserves legacy `mf-textarea`, includes the message in the private CSV `Message` column, and forwards it in the admin email to `contact@assurancesderueil.fr`.
- Advanced the live MU-plugin to `120.2` after stamped testing showed quote form `2073` dropped `telephone` and `type_devis` from storage/CSV even though the browser payload contained them; the saved MetForm entry now merges live posted fields before emails/CSV read them.
- Verified `v120.2` live with stamped contact row `8203` and quote row `8204`: both forms displayed success, Gmail confirmations arrived, quote acknowledgement includes `Téléphone +34 636 63 03 38` and `Demande Assurance de prêt`, the quote email still does not echo birthdate, and the private CSV contains the quote phone, type, and French birthdate `11-JUIN-1957`.

## Conversation Summary, 2026-07-01

- Reworked the daily PDF automation from GH.io-only reporting into `Render ADR site and DB`, targeting the official `https://assurancesderueil.fr/` site.
- Extended `scripts/render-ghio-long-pdf.cjs` with an `official` profile so it uses the live WordPress slugs and writes `output/pdf/official-daily/assurances-de-rueil-official-*.pdf` artifacts while preserving the GH.io profile.
- Added `scripts/download-site-contacts-tsv.cjs`, which downloads the private quote/contact export, normalizes it to TSV, filters to the last 7 days, and writes latest/versioned TSV artifacts.
- Extended the private request export endpoint with `format=tsv` and `days=7` support so the DB export can be generated directly by WordPress once the MU-plugin is deployed.
- Verified the official PDF render on 2026-07-01: `v120.2`, `21` pages, `11103032` bytes, with latest PDF and manifest updated.
- Verified the 7-day contacts TSV on 2026-07-01: `2825` bytes and `31` lines, with the latest TSV copy updated.
- Updated the automation prompt so Friday runs send Manu `manuelveludo1@gmail.com` a French signed Gmail message from the connected `ec92009@gmail.com` account with the latest PDF and TSV attached.
- Created sample Gmail drafts during review; the canonical current sample is the signed French draft ending `-- Elie`.

## Conversation Summary, 2026-07-03

- Published the approved `v125.2` GH.io/source-of-truth refresh to the live WordPress site through `ADR Site Fixes`.
- The live update includes Manuel's latest page-copy pass, the restored desktop sticky mini-nav, static mini-nav behavior for PDF/print export only, and the long-heading overflow fix for cards such as `Accompagnement`.
- Deployment used replacement bootstrap `ADR_MU_PLUGIN_REPLACE_BOOTSTRAP_V125_2`, pinned to GitHub commit `83892fa`; an initial generated fragment had padded file-map keys that wrote an empty MU root file, then an immediate corrected repair pass overwrote it with exact paths.
- Live WordPress verification confirms `ADR Site Fixes` appears as version `125.2`, `functions.php` is back to `91` lines / `2,936` editor characters, affected public pages return HTTP 200, show `v125.2`, include `adr-live-visual-refresh-v125-2` and `adr-theme-persistence-v125-2`, and contain no temporary bootstrap marker.

## Conversation Summary, 2026-07-12

- Implemented requester IP reporting for live quote/contact requests in `ADR Site Fixes`, covering MetForm quote form `2073` and contact form `7487`.
- Added optional Cloudflare visitor-location metadata capture (`CF-IPCountry`, city, region, postal code, timezone, latitude/longitude, and `CF-Ray`) while keeping IP/geolocation technical data on a 30-day cleanup schedule.
- Updated admin email and TSV export output so new request rows include `IP demandeur`, `Géolocalisation IP`, and `Cloudflare Ray ID`.
- Updated the privacy-policy copy to state that IP/geolocation data is collected only for diagnostics, maintenance, security, and abuse prevention; it is not used for commercial profiling.
- Corrected the visible version according to the canonical versioning SOP: 2026-07-12 is `v134.0`, not a same-day `v125.x` bump.
- Deployed the live WordPress update through a replacement bootstrap marked `ADR_MU_PLUGIN_REPLACE_BOOTSTRAP_V134_0`, after user-assisted Safari paste into the WordPress theme editor.
- Verified the live site after deployment: official pages show `v134.0`, privacy copy includes the technical-data caveat, and the TSV headers include the new IP/geolocation/Ray columns.
- Reran the official automation after deployment. The post-fix official PDF was `assurances-de-rueil-official-v134.0-2026-07-12.pdf`, `21` pages, `11026033` bytes. The refreshed contacts TSV was initially `3787` bytes; after adding IPinfo URL formatting, the regenerated TSV is `3805` bytes with `21` parsed data rows.
- Confirmed that current TSV rows may have blank IP/geolocation values until post-deployment submissions are captured; existing historical rows cannot be backfilled from data that was not stored.
- Decided not to add an external IP-geolocation API or microservice. If an individual IP address needs a one-off lookup, use IPinfo, MaxMind GeoIP demo, or RIPEstat manually after the fact. Implemented the low-friction TSV convenience path by formatting populated requester-IP cells as `https://ipinfo.io/<IP address>` without making any API calls.

## Source Of Truth

- GitHub/GH.io is the working source of truth for static content, visual approvals, review diffs, commits, and handoff history.
- Live WordPress is currently a dynamic delivery layer:
  - `ADR Site Fixes` normalizes/replaces public page output;
  - dynamic residuals refresh MetForm nonces at render time;
  - child-theme `functions.php` now holds only the base enqueue and Web-By-Elie credit block.
- The latest live WordPress deployment details are in `docs/live-wordpress-change-log.md`.
- The theme-editor workflow and size-limit warning are in `docs/wordpress-theme-editor-publish-workflow.md`.
- The daily official-site PDF and DB export automation is named `Render ADR site and DB`; it stores generated artifacts under `output/pdf/official-daily/`, which is intentionally ignored by Git.
- On this Mac, PHP linting should use MAMP when shell `php` is absent: `/Applications/MAMP/bin/php/php8.4.1/bin/php` for current checks and `/Applications/MAMP/bin/php/php7.4.33/bin/php` for WordPress/PHP 7.4 compatibility checks. This is now recorded in the parent `~/Dev/AGENTS.md`.

## Quote Form State

- The public quote page renders the refreshed form shell marked `adr-live-quote-form-v134-0` in live output.
- It preserves legacy MetForm keys and adds clearer alias keys for future consumers.
- The form reads the rendered wrapper's current `data-form-nonce` and `data-wp-nonce`.
- The submit request includes `X-WP-Nonce`, which fixed the previous `Envoi non autorisé` failure.
- The public smoke check after the fix confirmed:
  - page HTTP 200;
  - footer marker `v120.2`;
  - dynamic nonce attributes present;
  - `headers: wpNonce ? { 'X-WP-Nonce': wpNonce } : {}` present in the form script.
- Real live quote submission was later verified by the user: the visitor acknowledgement arrived, Manu received the admin email, WordPress storage/CSV output looked convincing, and the phone field preserved international-prefix input.
- Live WordPress `v125.2` keeps quote/contact phone controls as `type="text"` with `inputmode="tel"`, matching the GH.io source change for international prefixes such as `+34`.
- Live WordPress `v125.2` preserves quote `telephone` and `type_devis` in the saved MetForm entry, visitor acknowledgement, admin email normalizer, and private CSV export.

## User Acknowledgement Email State

- The user-facing quote acknowledgement email was replaced on 2026-06-27; the contact-page acknowledgement was added to the same branded treatment later that day.
- The fix lives outside the oversized child-theme file as a Must-Use plugin:
  - WordPress name: `ADR Site Fixes`
  - version: `134.0`
  - local source: `wp-live-plugin/adr-site-fixes/`
- It applies to MetForm form `2073` for quote requests and form `7487` for the contact page.
- It replaces the old centered/plain confirmations with branded left-aligned emails, plural `Assurances de Rueil`, cleaner legal copy, and a privacy-policy link.
- It normalizes both acknowledgements to:
  - From: `Assurances de Rueil <contact@assurancesderueil.fr>`;
  - Reply-To: `Assurances de Rueil <contact@assurancesderueil.fr>`.
- It uses separate subjects:
  - quote: `Votre demande de devis - Assurances de Rueil`;
  - contact: `Votre message - Assurances de Rueil`.
- The admin notification/export path now handles both MetForm form `2073` and contact form `7487`:
  - private CSV route includes a `Source` column for `Devis` vs `Contact`;
  - private CSV route includes a `Message` column, preferring canonical `message` and falling back to legacy `mf-textarea`;
  - contact admin notifications are normalized to `Nouveau message depuis le site - Assurances de Rueil`;
  - contact admin notifications include the visitor's message when present;
  - `contacts@assurancesderueil.fr` is accepted as an alias, with canonical delivery to `contact@assurancesderueil.fr`.
- Rollback for only the contact acknowledgement patch is to restore `ADR Site Fixes` to the previous `119.7.0` files.
- Rollback for the visual refresh only is to remove the `includes/live-visual-refresh.php` require from `wp-content/mu-plugins/adr-site-fixes.php` or restore the `ADR Site Fixes` MU-plugin to the previous `119.3.1` files. Removing the whole MU-plugin also rolls back the quote and contact acknowledgement emails.

## WordPress Editing Notes

- Avoid pasting the full child-theme `functions.php` through the browser editor. It is too large and browser/editor reads can truncate.
- Syntax highlighting can be temporarily disabled in the WordPress profile as an emergency path, but it should not be the normal deployment path.
- For small live behavior changes, prefer:
  1. build a local module;
  2. lint it with MAMP PHP;
  3. install as a small plugin or MU-plugin;
  4. document the rollback path.
- If a one-shot bootstrap is unavoidable, it must:
  - be locally linted with the exact payload;
  - either self-remove from `functions.php` or write the final slim `functions.php` payload directly;
  - leave an after snapshot;
  - be verified through WordPress admin and public HTTP checks.

## Recent Verification

- The live homepage source and rendered DOM include:
  - `adr-home-clean-mock`;
  - the three approved service cards;
  - service tag bubbles for loan, personal, and professional insurance;
  - the phone CTA;
  - the four partner logos;
  - footer marker `v125.2`.
- The GH.io `v119.4` local preview was verified across `particuliers.html`, `professionnels.html`, and `index.html`: the day/night checkbox state and computed theme colors persist across page navigation in both directions.
- The GH.io `v119.7` local preview was verified on `courtier.html`: the new `Téléphone *` field renders as `type="text"`, entering `+34 636 63 03 38` preserves the full value, and empty static submits show a preview message instead of `Something went wrong. Envoi non autorisé.`
- `ADR Site Fixes` appears in WordPress Must-Use plugins as version `134.0`.
- `ADR Site Fixes` was deployed as version `125.2` through replacement bootstrap `ADR_MU_PLUGIN_REPLACE_BOOTSTRAP_V125_2`, pinned to GitHub commit `83892fa`.
- `instive-child/functions.php` no longer contains any split/replacement bootstrap marker and is now `91` lines / `2,936` editor characters.
- Synthetic email verification confirms MetForm contact form `7487` now produces marker `adr-contact-user-email-v120-0`, subject `Votre message - Assurances de Rueil`, and preserves `+34 636 63 03 38` in the acknowledgement body.
- Real live form verification on 2026-06-28 confirms both contact and quote forms send visitor responses, deliver admin emails to Manu, and produce a convincing private CSV export.
- The live contact and quote pages remain HTTP 200.
- Public live verification on Courtier/contact confirms:
  - `adr-live-visual-refresh-v120-0`;
  - footer marker `v120.0`;
  - `adr-theme-persistence-v120-0`;
  - the injected `Téléphone *` field marker `elementor-element-adr-phone`;
  - backend MetForm form `7487` metadata filtering removes `mf-recaptcha` and inserts `telephone`;
  - alignment CSS marker `adr-contact-phone-align-v1`;
  - no stale `mf-recaptcha`, `g-recaptcha`, `recaptcha-support`, `reCAPTCHA`, or temporary bootstrap marker.
- Public live verification on Demande de devis confirms:
  - `adr-live-quote-form-v120-0`;
  - `version = '120.0'`;
  - `adr_quote_consent_2026-06-28_v120.0`;
  - `type="text"` and `inputmode="tel"` on the quote phone field;
  - no temporary bootstrap marker.
- Public live verification after the `120.0` message-storage deploy confirms:
  - Home, Courtier/contact, and Demande de devis return HTTP 200;
  - `adr-live-visual-refresh-v120-0`, `adr-theme-persistence-v120-0`, and footer marker `v120.0` remain present;
  - Courtier/contact includes `Message`, `Téléphone`, and `adr-form-phone-preserver-v120-0`;
  - quote/contact phone controls remain `type="text"` with telephone hints;
  - no public response includes `ADR_MU_PLUGIN_REPLACE_BOOTSTRAP`.
- Public live spot checks on Home and Particuliers confirm `adr-live-visual-refresh-v120-0`, `adr-theme-persistence-v120-0`, footer marker `v120.0`, and the approved `adr-photo-*-v119-5.jpg` assets.
- PHP lint passes for:
  - `wp-live-plugin/adr-site-fixes/adr-site-fixes.php`;
  - `wp-live-plugin/adr-site-fixes/includes/form-adapters.php`;
  - `wp-live-plugin/adr-site-fixes/includes/page-shell-normalizer.php`;
  - `wp-live-plugin/adr-site-fixes/includes/public-user-email.php`;
  - `wp-live-plugin/adr-site-fixes/includes/quote-requests-export.php`;
  - `wp-live-plugin/adr-site-fixes/includes/live-visual-refresh.php`.
- MAMP PHP lint passes for the `120.2` changed files and bootstrap on PHP `8.4.1` and PHP `7.4.33`.
- Public live verification after the `120.2` deploy confirms Home, Courtier/contact, and Demande de devis are HTTP 200, show `v120.2`, include the expected `v120-2` markers, and contain neither the unauthorized submit message nor the replacement bootstrap marker.
- Live stamped test `2026-06-28 12-14-09` confirms:
  - contact CSV row `8203` has `Téléphone: +34 636 63 03 38`, two-line address, and the contact `Message`;
  - quote CSV row `8204` has `Téléphone: +34 636 63 03 38`, `Type de devis: Assurance de prêt`, `Naissance: 11-JUIN-1957`, `Communication: E-mail`, `Fumeur: Non`, `Banque: Banque test`, and `Profession: Cadres`;
  - visitor Gmail confirmations arrived for both forms, and the quote confirmation includes phone/type without echoing birthdate.
- Official-site PDF automation verification on 2026-07-01 confirms `assurances-de-rueil-official-v120.2-2026-07-01.pdf` has `21` pages and `11103032` bytes.
- Contacts TSV automation verification on 2026-07-01 confirms `assurances-de-rueil-contacts-last-7-days-2026-07-01.tsv` has `2825` bytes and `31` lines.
- Public live verification after the `125.2` deploy confirms Home, Assurance de prêt, Particuliers, Professionnels, Courtier/contact, and Demande de devis are HTTP 200, show `v125.2`, include the expected `v125-2` markers, normalize page titles to `Assurances de Rueil`, and contain no replacement-bootstrap marker.
- Browser measurement on live Assurance de prêt confirms `.adr-mini-nav` is sticky on desktop and the `Accompagnement` card heading has `overflow-wrap: anywhere` with no measured overflow.
- Browser measurement on live Professionnels confirms `Assurance emprunteur professionnelle` is present and does not overflow.
- Public live verification after the `134.0` deploy confirms Home and policy pages show `v134.0`, include `adr-live-visual-refresh-v134-0` and `adr-theme-persistence-v134-0`, and contain no temporary bootstrap marker.
- Privacy-page verification after the `134.0` deploy confirms the diagnostic/maintenance/security-only IP/geolocation caveat, the no-commercial-profiling caveat, and the 30-day technical retention statement are present.
- Contacts TSV verification after the `134.0` deploy confirms `IP demandeur`, `Géolocalisation IP`, and `Cloudflare Ray ID` headers are present. A later same-day refresh with IPinfo URL formatting produced `21` parsed data rows, one populated IPinfo URL row, and no raw IP cells.
- Official-site PDF automation verification after the `134.0` deploy confirms `assurances-de-rueil-official-v134.0-2026-07-12.pdf` has `21` pages and `11026033` bytes; the versioned/latest PDF and manifest matched byte-for-byte.

## Open Work

- Use `BACKLOG.md` for the current numbered backlog.
- The highest-risk technical debt is now the browser theme-editor bootstrap path; future work should use FTP/SFTP, WP-CLI, or package upload for repeatable MU-plugin deployments.
