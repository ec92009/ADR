# Assurances de Rueil Working Summary

## Current Site State

- Repository: `ec92009/ADR`.
- GitHub Pages source of truth: `https://ec92009.github.io/ADR/`.
- Live WordPress site: `https://assurancesderueil.fr/`.
- GitHub Pages source-truth version marker: `v119.7`.
- Live WordPress version marker: `v119.7`.
- The live WordPress pages now use the approved GH.io-style page shells through the child-theme output normalizer, with WordPress kept only where it must remain dynamic.
- The live WordPress pages have been reconciled against the GH.io `v119.7` source-of-truth mock for high-resolution photography, day/night persistence, and the contact/phone form pass.
- The GH.io `v119.7` source update keeps static mock forms from posting to live MetForm endpoints, changes phone fields to text inputs with telephone keyboard hints, and preserves international prefixes such as `+34`.
- GH.io and live WordPress both keep the day/night switch choice across page navigation through `adr-theme-persistence.js`.
- GH.io and live WordPress both use the higher-resolution JPEG photography in `assets/adr-photo-*-v119-5.jpg`.
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

- The public quote page renders the refreshed form shell marked `adr-live-quote-form-v119-7` in live output.
- It preserves legacy MetForm keys and adds clearer alias keys for future consumers.
- The form reads the rendered wrapper's current `data-form-nonce` and `data-wp-nonce`.
- The submit request includes `X-WP-Nonce`, which fixed the previous `Envoi non autorisé` failure.
- The public smoke check after the fix confirmed:
  - page HTTP 200;
  - footer marker `v119.7`;
  - dynamic nonce attributes present;
  - `headers: wpNonce ? { 'X-WP-Nonce': wpNonce } : {}` present in the form script.
- No real lead/test email was sent during the final verification unless explicitly authorized later.
- Live WordPress `v119.7` changes quote/contact phone controls to `type="text"` with `inputmode="tel"`, matching the GH.io source change for international prefixes such as `+34`.

## User Acknowledgement Email State

- The user-facing quote acknowledgement email was replaced on 2026-06-27; the contact-page acknowledgement was added to the same branded treatment later that day.
- The fix lives outside the oversized child-theme file as a Must-Use plugin:
  - WordPress name: `ADR Site Fixes`
  - version: `119.7.1`
  - local source: `wp-live-plugin/adr-site-fixes/`
- It applies to MetForm form `2073` for quote requests and form `7487` for the contact page.
- It replaces the old centered/plain confirmations with branded left-aligned emails, plural `Assurances de Rueil`, cleaner legal copy, and a privacy-policy link.
- It normalizes both acknowledgements to:
  - From: `Assurances de Rueil <contact@assurancesderueil.fr>`;
  - Reply-To: `Assurances de Rueil <contact@assurancesderueil.fr>`.
- It uses separate subjects:
  - quote: `Votre demande de devis - Assurances de Rueil`;
  - contact: `Votre message - Assurances de Rueil`.
- Rollback for the visual refresh only is to remove the `includes/live-visual-refresh.php` require from `wp-content/mu-plugins/adr-site-fixes.php` or restore the `ADR Site Fixes` MU-plugin to the previous `119.3.1` files. Removing the whole MU-plugin also rolls back the quote acknowledgement email.

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
  - footer marker `v119.7`.
- The GH.io `v119.4` local preview was verified across `particuliers.html`, `professionnels.html`, and `index.html`: the day/night checkbox state and computed theme colors persist across page navigation in both directions.
- The GH.io `v119.7` local preview was verified on `courtier.html`: the new `Téléphone *` field renders as `type="text"`, entering `+34 636 63 03 38` preserves the full value, and empty static submits show a preview message instead of `Something went wrong. Envoi non autorisé.`
- `ADR Site Fixes` appears in WordPress Must-Use plugins as version `119.7.1`.
- `instive-child/functions.php` no longer contains the bootstrap block after self-removal.
- Synthetic email verification confirms MetForm contact form `7487` now produces marker `adr-contact-user-email-v119-7-1`, subject `Votre message - Assurances de Rueil`, and preserves `+34 636 63 03 38` in the acknowledgement body. A real live contact submission was not sent during this verification.
- The live contact and quote pages remain HTTP 200.
- Public live verification on Courtier/contact confirms:
  - `adr-live-visual-refresh-v119-7`;
  - footer marker `v119.7`;
  - `adr-theme-persistence-v119-7`;
  - the injected `Téléphone *` field marker `elementor-element-adr-phone`;
  - backend MetForm form `7487` metadata filtering removes `mf-recaptcha` and inserts `telephone`;
  - alignment CSS marker `adr-contact-phone-align-v1`;
  - no stale `mf-recaptcha`, `g-recaptcha`, `recaptcha-support`, `reCAPTCHA`, or temporary bootstrap marker.
- Public live verification on Demande de devis confirms:
  - `adr-live-quote-form-v119-7`;
  - `version = '119.7'`;
  - `adr_quote_consent_2026-06-27_v119.7`;
  - `type="text"` and `inputmode="tel"` on the quote phone field;
  - no temporary bootstrap marker.
- Public live spot checks on Home and Particuliers confirm `adr-live-visual-refresh-v119-7`, `adr-theme-persistence-v119-7`, footer marker `v119.7`, and the approved `adr-photo-*-v119-5.jpg` assets.
- The editor cleanup check confirmed page-side `instive-child/functions.php` length `351567` and no `ADR_MU_PLUGIN_BOOTSTRAP` marker after self-removal. A full local after-snapshot was not kept because the browser/editor export path truncated the copied file.
- PHP lint passes for:
  - `wp-live-plugin/adr-site-fixes/adr-site-fixes.php`;
  - `wp-live-plugin/adr-site-fixes/includes/quote-user-email.php`;
  - `wp-live-plugin/adr-site-fixes/includes/live-visual-refresh.php`.

## Open Work

- Use `BACKLOG.md` for the current numbered backlog.
- The highest-risk technical debt is the oversized `functions.php`; future work should move behavior into modules or a proper plugin rather than extending that file.
