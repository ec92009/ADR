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
