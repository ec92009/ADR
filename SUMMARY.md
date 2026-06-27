# Assurances de Rueil Working Summary

## Current Site State

- Repository: `ec92009/ADR`.
- GitHub Pages source of truth: `https://ec92009.github.io/ADR/`.
- Live WordPress site: `https://assurancesderueil.fr/`.
- GitHub Pages source-truth version marker: `v119.4`.
- Live WordPress version marker before this preview fix is ported: `v119.3`.
- The live WordPress pages now use the approved GH.io-style page shells through the child-theme output normalizer, with WordPress kept only where it must remain dynamic.
- The live WordPress homepage has been reconciled against the GH.io `v119.3` source-of-truth mock; only expected production URLs/absolute asset paths differ.
- The GH.io `v119.4` preview keeps the day/night switch choice across page navigation through `adr-theme-persistence.js`.
- Public page titles are normalized to `Assurances de Rueil`.
- The live quote page remains backed by MetForm form `2073` for storage and notifications.

## Source Of Truth

- GitHub/GH.io is the working source of truth for static content, visual approvals, review diffs, commits, and handoff history.
- Live WordPress is currently a dynamic delivery layer:
  - child-theme `functions.php` normalizes/replaces public page output;
  - dynamic residuals refresh MetForm nonces at render time;
  - new behavior should be split into modules rather than further growing `functions.php`.
- The latest live WordPress deployment details are in `docs/live-wordpress-change-log.md`.
- The theme-editor workflow and size-limit warning are in `docs/wordpress-theme-editor-publish-workflow.md`.

## Quote Form State

- The public quote page renders the refreshed form shell marked `adr-live-quote-form-v119-3`.
- It preserves legacy MetForm keys and adds clearer alias keys for future consumers.
- The form reads the rendered wrapper's current `data-form-nonce` and `data-wp-nonce`.
- The submit request includes `X-WP-Nonce`, which fixed the previous `Envoi non autorisé` failure.
- The public smoke check after the fix confirmed:
  - page HTTP 200;
  - footer marker `v119.3`;
  - dynamic nonce attributes present;
  - `headers: wpNonce ? { 'X-WP-Nonce': wpNonce } : {}` present in the form script.
- No real lead/test email was sent during the final verification unless explicitly authorized later.

## Quote Email State

- The user-facing quote acknowledgement email was replaced on 2026-06-27.
- The fix lives outside the oversized child-theme file as a Must-Use plugin:
  - WordPress name: `ADR Site Fixes`
  - version: `119.3.1`
  - local source: `wp-live-plugin/adr-site-fixes/`
- It applies only to MetForm form `2073`.
- It replaces the old centered/plain confirmation with a branded left-aligned email, plural `Assurances de Rueil`, cleaner legal copy, and a privacy-policy link.
- It normalizes:
  - subject: `Votre demande de devis - Assurances de Rueil`;
  - From: `Assurances de Rueil <contact@assurancesderueil.fr>`;
  - Reply-To: `Assurances de Rueil <contact@assurancesderueil.fr>`.
- Rollback is to remove `wp-content/mu-plugins/adr-site-fixes.php` and `wp-content/mu-plugins/adr-site-fixes/` from the WordPress server.

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
  - self-remove from `functions.php`;
  - leave an after snapshot;
  - be verified through WordPress admin and public HTTP checks.

## Recent Verification

- The live homepage source and rendered DOM include:
  - `adr-home-clean-mock`;
  - the three approved service cards;
  - service tag bubbles for loan, personal, and professional insurance;
  - the phone CTA;
  - the four partner logos;
  - footer marker `v119.3`.
- The GH.io `v119.4` local preview was verified across `particuliers.html`, `professionnels.html`, and `index.html`: the day/night checkbox state and computed theme colors persist across page navigation in both directions.
- `ADR Site Fixes` appears in WordPress Must-Use plugins as version `119.3.1`.
- `instive-child/functions.php` no longer contains the bootstrap block after self-removal.
- The live quote page remains HTTP 200.
- PHP lint passes for:
  - `wp-live-plugin/adr-site-fixes/adr-site-fixes.php`;
  - `wp-live-plugin/adr-site-fixes/includes/quote-user-email.php`;
  - `wp-backups/functions-instive-child-after-adr-site-fixes-mu-seed-v119-3-2026-06-27.php`.

## Open Work

- Use `BACKLOG.md` for the current numbered backlog.
- After GH.io review/sign-off, port the `v119.4` day/night persistence behavior to live WordPress.
- The highest-risk technical debt is the oversized `functions.php`; future work should move behavior into modules or a proper plugin rather than extending that file.
