# WordPress Theme Editor Publish Workflow

This documents the browser-assisted path used on 2026-06-27 to publish approved GH.io refreshes and small live WordPress fixes.

Important: the child-theme `functions.php` is now too large for reliable browser-editor round trips. Browser/editor reads and pastes can truncate the file. Use the full-file paste workflow only for small files or when an exact hash check proves the editor value is complete.

## Preconditions

- GitHub/GH.io remains the source of truth for approved static content.
- The WordPress admin session must already be authenticated in the built-in browser.
- The editor page is:
  `https://assurancesderueil.fr/wp-admin/theme-editor.php?file=functions.php&theme=instive-child`
- Create a local before snapshot from the current editor content before editing.

## Local Candidate

1. Copy the current live snapshot to a candidate under `wp-backups/`.
2. Edit the candidate locally, not directly in the browser.
3. Lint with MAMP PHP:

```sh
/Applications/MAMP/bin/php/php8.3.14/bin/php -l wp-backups/<candidate>.php
```

4. If the change is an output normalizer rule, dry-run the relevant captured public HTML with small WordPress function stubs where practical.

## Browser-Assisted Upload

Use this workflow only when the target file is small enough for reliable browser editing. For the current oversized `instive-child/functions.php`, prefer the MU-plugin split workflow below.

1. Write the candidate PHP to the built-in browser clipboard.
2. Focus the CodeMirror editor.
3. Send `Meta+A`, then `Meta+V`.
4. Verify a unique new marker is visible in the editor before submitting. For `v119.1`, useful markers were:
   - `ADR_QUOTE_REQUESTS_VERSION`, `119.1`
   - `adr-partners-v119-1`
5. Click `Mettre à jour le fichier`.
6. Verify the editor textarea after navigation includes the new marker and excludes the old version marker.
7. Reload the editor if needed to clear transient admin UI state. On 2026-06-27, a browser search term briefly appeared in the submit button's DOM value, but it was not present in `functions.php`; reload restored the normal button label.

## Oversized `functions.php`

- Do not trust browser-extracted copies of the full child-theme `functions.php`; one captured value was silently truncated and failed local lint.
- Do not keep adding new behavior to `functions.php` when a small plugin or MU-plugin will do.
- If emergency editing is unavoidable:
  1. reconstruct or use a locally linted full source file;
  2. compute a simple page-side hash before submitting;
  3. submit only after the editor hash matches the locally linted seed;
  4. verify the live editor after save.
- Temporarily disabling syntax highlighting in the WordPress profile exposes the real textarea and can help, but restore the profile setting immediately afterward.
- When a one-shot bootstrap is prepended to the file, give it an opening and closing PHP wrapper and verify cleanup afterward. If a self-removal matcher is too strict, use a second tiny cleanup wrapper anchored to the start of `functions.php` rather than replacing the whole file.

## MU-Plugin Split Workflow

Use this for new live behavior that does not belong in the page mock itself.

1. Create the module locally under `wp-live-plugin/<plugin-name>/`.
2. Keep the entry file small and include feature files from `includes/`.
3. Prefix classes/functions/constants to avoid collisions with the child theme.
4. Lint every PHP file with MAMP PHP:

```sh
/Applications/MAMP/bin/php/php8.3.14/bin/php -l wp-live-plugin/<plugin-name>/<file>.php
```

5. Install through a small one-shot bootstrap only when no FTP/WP-CLI route is available.
6. The bootstrap must:
   - write files under `wp-content/mu-plugins/`;
   - be locally linted with the exact embedded payload;
   - self-remove from `functions.php`;
   - leave a local after snapshot in `wp-backups/`.
7. Verify in WordPress admin under `plugins.php?plugin_status=mustuse`.
8. Verify public pages still return HTTP 200 and the changed feature marker is present.
9. Document rollback. For `ADR Site Fixes`, rollback is removing `wp-content/mu-plugins/adr-site-fixes.php` and `wp-content/mu-plugins/adr-site-fixes/`.

## Public Verification

Use cache-busted public requests and check for:

- footer version marker, e.g. `v119.1`;
- no prior version marker;
- expected public text replacements;
- removal of obsolete homepage recap/FAQ blocks;
- expected injected assets or sections;
- quote form script/consent version markers when the quote form changed.

## Visual Parity Gate

After every WordPress publish, perform a visual inspection before considering the push complete:

1. Open the GitHub Pages source-of-truth page and the matching public WordPress page side by side in Safari or Chrome.
2. Compare the visible layout, copy density, CTA treatments, service cards, icon colors, partner section, footer version, and mobile/desktop behavior where the change touches responsive layout.
3. Capture or review at least the homepage plus every page materially affected by the publish.
4. If the pages differ materially, treat the WordPress publish as incomplete even when source markers and version strings are correct.

Store the after snapshot under `wp-backups/` and update `docs/live-wordpress-change-log.md`.
