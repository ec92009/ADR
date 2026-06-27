# Backlog

1. Back up and document the live MetForm definitions/settings for forms `2073` and `7487`, including recipients, field keys, confirmation settings, and anti-spam settings.
2. Replace the browser theme-editor bootstrap workflow with a safer repeatable deploy path for the MU-plugin, such as FTP/SFTP, WP-CLI, or a small authenticated package upload.
3. Review the live site with Manuel on desktop and mobile, covering home, cabinet, assurance de prêt, particuliers, professionnels, contact, quote, and footer/version details.
4. Do a full SEO/AIO pass after visual sign-off: titles, meta descriptions, schema image URLs/dimensions, internal links, sitemap, `llms.txt`, and stale `Rueil-Malmaison` wording where it no longer helps.
5. Finalize legal and consent wording across the forms, acknowledgement emails, privacy policy, cookie policy, and any GDPR data-rights copy.
6. Revisit anti-spam now that stale reCAPTCHA is removed from the contact form; prefer the least intrusive option that does not block real prospects.
7. Eventually restore the EN/FR switch with real bilingual content, hreflang/canonical handling, and a separate bilingual SEO/AIO pass.

## Completed

- 2026-06-27: Fixed trailing separator behavior in the mock-page list/tag UI.
- 2026-06-27: Pushed the approved GH.io `v119.1` source-of-truth content to live WordPress through the child-theme `functions.php` normalizer, including homepage cleanup, Manu wording requests, footer version marker, and partner-logo strip.
- 2026-06-27: Replaced the user-facing MetForm quote acknowledgement email with the `ADR Site Fixes` Must-Use plugin (`119.3.1`), keeping the email module out of the oversized child-theme `functions.php`.
- 2026-06-27: Reconciled the live WordPress homepage against the GH.io `v119.3` source-of-truth mock. The only shell differences are expected production URLs/absolute asset paths.
- 2026-06-27: Normalized public browser titles to `Assurances de Rueil`.
- 2026-06-27: Published GH.io preview `v119.4` source changes for persistent day/night switching across root mock pages.
- 2026-06-27: Prepared GH.io preview `v119.5` with regenerated higher-resolution local JPEG photography for the root and pretty-path static pages.
- 2026-06-27: Ported GH.io `v119.5` to live WordPress through the split `ADR Site Fixes` MU-plugin (`119.5.0`), including persistent day/night behavior, high-resolution photo URLs, and footer/source markers.
- 2026-06-27: Published GH.io source update `v119.6` for the Courtier/contact form: added a required phone field, removed the stale reCAPTCHA requirement from the static form, and added telephone input mode to the quote-page phone field.
- 2026-06-27: Ported the GH.io `v119.6` contact/phone polish to live WordPress through `ADR Site Fixes` (`119.6.0`): injected the required Courtier phone field, removed stale contact reCAPTCHA output, aligned the phone field, added quote phone `inputmode="tel"`, and repaired the prior JSON-LD photo-dimension artifact.
- 2026-06-27: Published GH.io source update `v119.7` for static form safety: contact and quote mocks no longer POST to live MetForm endpoints, phone fields use text inputs with telephone keyboard hints, and `+34`-style prefixes are preserved in rendered checks.
- 2026-06-27: Ported the GH.io `v119.7` form-safety patch to live WordPress through `ADR Site Fixes` (`119.7.0`): backend MetForm metadata for contact form `7487` removes the stale reCAPTCHA field, inserts the backend `telephone` field, and keeps the frontend phone-preserver guard in sync with GH.io.
- 2026-06-27: Extended the branded acknowledgement email module to contact form `7487` through `ADR Site Fixes` (`119.7.1`), with subject `Votre message - Assurances de Rueil`.
- 2026-06-27: Split the oversized live child-theme `functions.php` into `ADR Site Fixes` modules (`119.8.1`), including page-shell normalization, quote/contact form adapters, quote/contact request export/admin email handling, and branded user acknowledgement emails; live `functions.php` is now 91 lines / 2,936 editor characters.
- 2026-06-28: Real live contact and quote form submissions were verified by the user: visitor acknowledgements arrived, Manu received the admin emails, and the private CSV export looked convincing.
