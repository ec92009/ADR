# Backlog

1. Share the GitHub Pages quote route with Manuel and collect feedback/sign-off on the new form.
2. Confirm the final legal/consent wording, especially the simplified required contact permission.
3. Preserve all legacy MetForm payload keys and add clearer alias keys so future `wp_postmeta` consumers have both old and improved schema values.
4. Back up live WordPress MetForm form `2073` immediately before making form changes.
5. Implement the approved form structure in Elementor/MetForm, avoiding classic-editor textarea edits.
6. Verify the live quote form render publicly on desktop and mobile without submitting a real customer lead.
7. Confirm the MetForm admin email recipient in WordPress settings, then with explicit approval test with safe dummy data and verify both WordPress entry storage and full-payload email delivery to the configured target.
8. Document the live form change in `docs/live-wordpress-change-log.md` and store before/after backups in `wp-backups/`.
9. Review the refreshed live site with Manuel for visual polish and any content that should be toned down or expanded.
10. Revisit anti-spam strategy after the simplified form is live, using the least intrusive option that does not block real prospects.
11. Re-review SEO / AIO after the refreshed mock content is approved, including page titles, meta descriptions, schema, internal links, and any AI-search answerability gaps.
12. Eventually restore the EN/FR switch, with a proper bilingual SEO / AIO pass rather than only toggling visible copy.

## Completed

- 2026-06-27: Pushed the approved GH.io `v119.1` source-of-truth content to live WordPress through the child-theme `functions.php` normalizer, including the homepage cleanup, Manu wording requests, footer version marker, and partner-logo strip.
- 2026-06-27: Replaced the user-facing MetForm quote acknowledgement email with the `ADR Site Fixes` Must-Use plugin (`v119.3.1`), keeping the email module out of the oversized child-theme `functions.php`.
- 2026-06-27: Reconciled the live WordPress homepage against the GH.io `v119.3` source-of-truth mock. The only shell differences are expected production URLs/absolute asset paths; service-card copy, sub-bubbles, phone CTA, partner section, title, and footer version marker match.
- 2026-06-27: Published GH.io preview `v119.4` source changes for persistent day/night switching across root mock pages.
- 2026-06-27: Prepared GH.io preview `v119.5` with regenerated higher-resolution local JPEG photography for the root and pretty-path static pages.
- 2026-06-27: Ported GH.io `v119.5` to live WordPress through the split `ADR Site Fixes` MU-plugin (`119.5.0`), including persistent day/night behavior, high-resolution photo URLs, and footer/source markers.
- 2026-06-27: Published GH.io source update `v119.6` for the Courtier/contact form: added a required phone field, removed the stale reCAPTCHA requirement from the static form, and added telephone input mode to the quote-page phone field.
- 2026-06-27: Ported the GH.io `v119.6` contact/phone polish to live WordPress through `ADR Site Fixes` (`119.6.0`): injected the required Courtier phone field, removed stale contact reCAPTCHA output, aligned the phone field, added quote phone `inputmode="tel"`, and repaired the prior JSON-LD photo-dimension artifact.
- 2026-06-27: Published GH.io source update `v119.7` for static form safety: contact and quote mocks no longer POST to live MetForm endpoints, phone fields use text inputs with telephone keyboard hints, and `+34`-style prefixes are preserved in rendered checks.
- 2026-06-27: Ported the GH.io `v119.7` form-safety patch to live WordPress through `ADR Site Fixes` (`119.7.0`): backend MetForm metadata for contact form `7487` removes the stale recaptcha field, inserts the backend `telephone` field, and keeps the frontend phone-preserver guard in sync with GH.io.
