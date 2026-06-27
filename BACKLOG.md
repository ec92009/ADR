# Backlog

1. After GH.io review/sign-off, port the `v119.4` day/night persistence behavior to live WordPress.
2. Share the GitHub Pages quote route with Manuel and collect feedback/sign-off on the new form.
3. Confirm the final legal/consent wording, especially the simplified required contact permission.
4. Preserve all legacy MetForm payload keys and add clearer alias keys so future `wp_postmeta` consumers have both old and improved schema values.
5. Back up live WordPress MetForm form `2073` immediately before making form changes.
6. Implement the approved form structure in Elementor/MetForm, avoiding classic-editor textarea edits.
7. Verify the live quote form render publicly on desktop and mobile without submitting a real customer lead.
8. Confirm the MetForm admin email recipient in WordPress settings, then with explicit approval test with safe dummy data and verify both WordPress entry storage and full-payload email delivery to the configured target.
9. Document the live form change in `docs/live-wordpress-change-log.md` and store before/after backups in `wp-backups/`.
10. Review the refreshed live site with Manuel for visual polish and any content that should be toned down or expanded.
11. Revisit anti-spam strategy after the simplified form is live, using the least intrusive option that does not block real prospects.
12. Re-review SEO / AIO after the refreshed mock content is approved, including page titles, meta descriptions, schema, internal links, and any AI-search answerability gaps.
13. Eventually restore the EN/FR switch, with a proper bilingual SEO / AIO pass rather than only toggling visible copy.

## Completed

- 2026-06-27: Pushed the approved GH.io `v119.1` source-of-truth content to live WordPress through the child-theme `functions.php` normalizer, including the homepage cleanup, Manu wording requests, footer version marker, and partner-logo strip.
- 2026-06-27: Replaced the user-facing MetForm quote acknowledgement email with the `ADR Site Fixes` Must-Use plugin (`v119.3.1`), keeping the email module out of the oversized child-theme `functions.php`.
- 2026-06-27: Reconciled the live WordPress homepage against the GH.io `v119.3` source-of-truth mock. The only shell differences are expected production URLs/absolute asset paths; service-card copy, sub-bubbles, phone CTA, partner section, title, and footer version marker match.
- 2026-06-27: Published GH.io preview `v119.4` source changes for persistent day/night switching across root mock pages.
