# Quote Form Mock - 2026-06-24

This document records the proposed secure-form redesign before any live WordPress/MetForm change.

## Status

- Scope: standalone mock plus static quote-route implementation.
- File: `form-mock/index.html`.
- Static quote route: `demande-de-devis-assurance-a-rueil-malmaison/index.html` renders the same flow through `pages.js`.
- Shareable GitHub Pages URL: `https://ec92009.github.io/ADR/form-mock/`.
- Local preview: `http://localhost:8124/form-mock/` when serving the repository with `python3 -m http.server 8124`.
- Live WordPress form: unchanged.
- Robots: `noindex, nofollow`.

## Proposed Flow

The mock keeps the form visually aligned with the refreshed Assurances de Rueil design, while reducing friction at the top of the form.

Always-visible required identity fields:

1. Civilité
2. Nom
3. Prénom
4. Adresse e-mail

Always-visible optional gate:

1. Type de devis désiré

Fields revealed only after selecting `Type de devis désiré`:

1. Contact préféré: E-mail par défaut, Téléphone, WhatsApp
2. Téléphone
3. Êtes-vous fumeur ?
4. Banque
5. Profession
6. Date de naissance
7. Adresse, Code postal, Ville

Desktop layout note: Contact préféré, Téléphone, and Êtes-vous fumeur are grouped in one row, with Fumeur on the right. The phone helper note was removed as redundant. Date de naissance sits in its own full-width row below that group.

## Original Copy Preserved

The mock preserves exact pre-refresh French copy for:

- RGPD consent:
  `J'accepte le traitement de mes données personnelles conformément au RGPD. EN SAVOIR PLUS`
- non-smoker definition:
  `Est non-fumeur toute personne certifiant qu’elle n’a fumé ni cigarette, ni cigarette électronique, ni pipe, ni cigare, ni consommé de produits contenant de la nicotine (patch, gomme…) au cours des 24 derniers mois, et qu’elle n’a pas arrêté de fumer à la demande expresse du corps médical.`

The original form included callback consent text but did not include a phone field. The mock resolves this mismatch by adding an optional phone number once a quote type is selected, then simplifying the contact consent into a dynamic sentence keyed to the selected contact preference:

`En cliquant sur « Envoyer », j’accepte qu’Assurances de Rueil me contacte par [contact préféré].`

## Consent Behavior

- Contact permission is required.
- E-mail is the default preferred contact method, because it is the only contact detail guaranteed by the required fields.
- Contact consent text updates dynamically to `par e-mail`, `par téléphone`, or `par WhatsApp`.
- RGPD consent is required.
- The mock `Envoyer` button is disabled until both required consent boxes are checked.

## Validation Performed

- Local HTML parse check passed.
- Browser check confirmed the gated extra section is hidden before selecting a quote type.
- Browser check confirmed the gated extra section appears after selecting a quote type.
- Browser check confirmed e-mail is the default contact preference.
- Browser check confirmed the contact-consent sentence updates when Téléphone or WhatsApp is selected.
- Browser check confirmed the latest desktop row order: Contact préféré, Téléphone, Êtes-vous fumeur.
- Browser check confirmed `Envoyer` remains disabled until both required consent boxes are checked.
- Browser check after the payload update confirmed legacy field names and hidden alias fields sync correctly for civilité, email, profession, date de naissance, code postal, ville, RGPD consent, schema version, consent version, and source URL.
- Mobile viewport check showed no horizontal overflow during the earlier mock pass.
- Browser check on the static quote route confirmed the new form replaced the older all-fields-visible form, the gated section opens after quote type selection, consent gating works, legacy/additive payload keys sync, and mobile width has no horizontal overflow.
- Static quote route now posts to the live MetForm endpoint for form `2073`; wiring check confirmed endpoint, form nonce, and initial disabled submit state without sending a full dummy lead.

## Implementation Notes For Live WordPress

- Do not edit the live MetForm form through the classic editor textarea; previous captcha work showed that can disturb Elementor/MetForm rendering.
- Implement the approved form structure through Elementor/MetForm controls where possible.
- Back up form `2073` before changes.
- Preserve the legacy MetForm field names documented in `docs/form-pipeline-compatibility-2026-06-24.md`, and add hidden alias fields for the cleaner refreshed schema.
- Keep WordPress/MetForm entry storage enabled so submissions continue to land in `wp_postmeta`.
- Manuel confirmed on 2026-06-24 that the interim email notification may include the full user-submitted payload.
- Verify public submission rendering without sending a real customer lead, then run one explicitly approved dummy submission to confirm entry storage and full-payload email delivery.
