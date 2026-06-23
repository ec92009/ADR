# Live WordPress Change Log

This file tracks live WordPress/database changes made on assurancesderueil.fr that are not automatically represented by the static GitHub Pages preview.

## 2026-06-23

### Safety baseline

- A WordPress/UpdraftPlus backup was created before live-site replacement work.
- The static redesign preview remains versioned in this repository and published through GitHub Pages.
- Live WordPress page/form edits are stored in the WordPress database, so they need explicit notes or exports here to be reproducible.

### Public site changes completed earlier

- Promoted the refreshed homepage to the live WordPress front page after previewing in Safari.
- Applied the refreshed visual treatment to major pages.
- Replaced the quote form date picker with simple day/month/year selects.
- Made Civilité and Fumeur choices mutually exclusive in the quote form.
- Restored French accents across the refreshed copy.
- Removed broken Contact Form 7 reCAPTCHA keys that were incorrectly loading a Google OAuth client id as a captcha site key.
- Switched MetForm captcha setting from v3 to v2 during troubleshooting, then decided to disable captcha rather than block real quote submissions.

### Quote form captcha intervention

- Goal: remove the broken MetForm reCAPTCHA requirement from quote form `2073`.
- Local backup captured before editing:
  - `wp-backups/metform-2073-content-before-disable-recaptcha-2026-06-23.txt`
- Attempted a classic-editor edit of MetForm form `2073` to remove the `g-recaptcha-response` validation tail.
- WordPress confirmed `Publication mise à jour`, but the public render then showed the MetForm wrapper without the form fields. This means the classic editor save interfered with Elementor/MetForm rendering.
- Current recovery direction: restore/preserve the Elementor form structure and remove the reCAPTCHA widget from the Elementor form builder instead of editing the classic textarea directly.

### Rollback notes

- The form content backup above contains the pre-intervention MetForm content from form `2073`.
- WordPress revisions for form `2073` should also be available in the admin editor.
- If the quote form render is incomplete, restore form `2073` from the WordPress revision before the captcha-removal attempt, then remove the reCAPTCHA widget through Elementor.
