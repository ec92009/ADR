# Quote Form Mock - 2026-06-24

This document records the proposed secure-form redesign before any live WordPress/MetForm change.

## Status

- Scope: mock only.
- File: `form-mock/index.html`.
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

1. Téléphone
2. Contact préféré: E-mail, Téléphone, WhatsApp
3. Banque
4. Profession
5. Date de naissance
6. Êtes-vous fumeur ?
7. Adresse, Code postal, Ville

## Original Copy Preserved

The mock uses the exact pre-refresh French copy for:

- callback consent:
  `En cliquant sur « Envoyer », j’accepte qu’un conseiller Assurances de Rueil, m’appelle pour m’accompagner dans le choix de mon assurance.`
- RGPD consent:
  `J'accepte le traitement de mes données personnelles conformément au RGPD. EN SAVOIR PLUS`
- non-smoker definition:
  `Est non-fumeur toute personne certifiant qu’elle n’a fumé ni cigarette, ni cigarette électronique, ni pipe, ni cigare, ni consommé de produits contenant de la nicotine (patch, gomme…) au cours des 24 derniers mois, et qu’elle n’a pas arrêté de fumer à la demande expresse du corps médical.`

The original form included callback consent text but did not include a phone field. The mock resolves this mismatch by adding an optional phone number once a quote type is selected.

## Consent Behavior

- Callback/call consent is optional.
- Permission to contact by e-mail is required.
- RGPD consent is required.
- The mock `Envoyer` button is disabled until both required consent boxes are checked.

## Validation Performed

- Local HTML parse check passed.
- Browser check confirmed the gated extra section is hidden before selecting a quote type.
- Browser check confirmed the gated extra section appears after selecting a quote type.
- Browser check confirmed `Envoyer` remains disabled until both required consent boxes are checked.
- Mobile viewport check showed no horizontal overflow during the earlier mock pass.

## Implementation Notes For Live WordPress

- Do not edit the live MetForm form through the classic editor textarea; previous captcha work showed that can disturb Elementor/MetForm rendering.
- Implement the approved form structure through Elementor/MetForm controls where possible.
- Back up form `2073` before changes.
- Verify public submission rendering without sending a real customer lead.
