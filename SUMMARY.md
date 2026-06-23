# Assurances de Rueil Working Summary

## Current Site State

- Repository: `ec92009/ADR`.
- Main static preview: `https://ec92009.github.io/ADR/`.
- Live WordPress site: `https://assurancesderueil.fr/`.
- Current GitHub Pages version marker: `v114.0`.
- Refreshed WordPress homepage and major pages are live.
- Quote form captcha issue was resolved by removing the broken reCAPTCHA widget through Elementor/MetForm.
- Legacy `/contact/` now redirects with a WordPress Redirection 301 to `/courtier-en-assurances-de-rueil-malmaison/`.
- Original service/cabinet details were restored into the refreshed WordPress pages and documented with before/after backups.

## Safety Baseline

- UpdraftPlus backup was created before live WordPress replacement work.
- Live WordPress edits are documented in `docs/live-wordpress-change-log.md`.
- Original-detail restoration audit is in `docs/audits/original-detail-restoration-2026-06-24.md`.
- Before/after WordPress page HTML backups are stored under `wp-backups/`.
- Avoid editing Elementor/MetForm forms through the classic-editor source textarea when possible.

## Form Mock Conversation

- The user wanted to rethink the secure quote form before changing live WordPress HTML.
- A standalone mock was created at `form-mock/index.html`.
- Shareable mock URL after GitHub Pages deploy: `https://ec92009.github.io/ADR/form-mock/`.
- Local preview command:

```sh
python3 -m http.server 8124
```

- Preview URL: `http://localhost:8124/form-mock/`.
- The mock is explicitly not wired to send data and is marked `noindex, nofollow`.

## Approved Mock Direction So Far

- First visible block asks only:
  1. Civilité
  2. Nom
  3. Prénom
  4. Adresse e-mail
- `Type de devis désiré` is full-width and acts as the gate.
- Extra fields remain hidden until a quote type is selected.
- Revealed fields include:
  1. Téléphone
  2. Contact préféré: E-mail, Téléphone, WhatsApp
  3. Banque
  4. Profession
  5. Date de naissance
  6. Êtes-vous fumeur ?
  7. Adresse, Code postal, Ville
- The date of birth year dropdown runs from current year minus 16 through current year minus 100.
- The original technical definition of non-smoker is included under fumeur.

## Consent Decisions

- The original pre-refresh form text said a counselor may call the user, but the original form did not ask for a phone number.
- The mock fixes that by asking for an optional phone number after a quote type is selected.
- Callback/call consent is optional.
- E-mail contact permission is required.
- RGPD consent is required.
- The `Envoyer` button stays disabled until e-mail contact permission and RGPD consent are both checked.
- The original pre-refresh French callback and RGPD wording has been preserved in the mock for traceability.

## Validation Already Done

- Public checks confirmed restored live pages contain the expected detail headings.
- Public `/contact/` redirect checked: one redirect to the refreshed contact page with HTTP 200.
- Mock browser checks confirmed:
  - extra fields are hidden before quote type selection;
  - extra fields are visible after quote type selection;
  - required consent gating enables `Envoyer` only after both required boxes are checked;
  - earlier mobile check showed no horizontal overflow.

## Next Working Principle

Keep using the mock until Manuel agrees with the structure and wording. Once approved, back up live MetForm form `2073`, implement via Elementor/MetForm, then verify the public page without submitting a real lead.
