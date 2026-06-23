# Original Detail Restoration Audit - 2026-06-24

## Goal

Restore the detailed business/service information from the original assurancesderueil.fr site into the refreshed WordPress pages, while keeping the updated visual direction intact for Manuel's review.

## Source Material Checked

- Archived original homepage and internal pages captured locally from the public site / web archive during the restoration pass.
- Current live WordPress pages on `https://assurancesderueil.fr/`.
- Current static GitHub Pages preview in this repository.

Relevant archived source pages:

- `cabinet-de-courtage-en-assurances-rueil-malmaison`
- `assurance-de-pret-a-rueil-malmaison`
- `assurance-particuliers-rueil-malmaison`
- `assurance-entreprise-rueil-malmaison`
- legacy `/contact/`
- `demande-de-devis-assurance-a-rueil-malmaison`

## WordPress Pages Updated

Backups were captured before each live WordPress page edit. The paired `before` and `after` files are stored under `wp-backups/`.

- Page `7754`, Assurance de prêt:
  - restored details on borrower insurance operation, subscription inputs, guarantee comparison points, exclusions, and broker guidance.
- Page `7331`, Particuliers:
  - restored details on habitation, loyers impayés, santé/GAV, automobile/mobility, prévoyance, and assurance de prêt profile examples.
- Page `2180`, Professionnels:
  - restored details on multirisques professionnelle, loyers impayés, assurance emprunteur professionnelle, responsabilité civile/professionnelle, prévoyance collective, flotte automobile, and assurance chômage du dirigeant.
- Page `7358`, Cabinet:
  - restored details on the four-generation Rueil-Malmaison cabinet, client types, custom insurance objective, availability/reactivity, and opening hours.

The restored section is marked in each page backup with `adr-original-detail-v1`.

## Contact Compatibility

The archived site exposed a legacy `/contact/` page. After the refresh this URL returned 404, while the current contact page lives at:

`/courtier-en-assurances-de-rueil-malmaison/`

Fix applied in WordPress Redirection plugin:

- Source: `/contact/`
- Target: `/courtier-en-assurances-de-rueil-malmaison/`
- Status: `301`
- Optional Redirection setup logging/IP collection was left off.

The live contact page was checked for the original contact details:

- 75 avenue Victor Hugo, 92500 Rueil-Malmaison
- +33 1 47 51 06 69
- +33 1 47 51 00 78
- contact@assurancesderueil.fr
- Monday-Friday / lundi-vendredi opening hours: 9H00-12H30 and 14H00-18H30

## Public Verification

Verified after the live edits:

- `/contact/` returns `301` with `x-redirect-by: redirection`.
- Following `/contact/` resolves to `/courtier-en-assurances-de-rueil-malmaison/` with HTTP `200`.
- The restored detail headings are present publicly on the four updated pages.
- The public contact page contains the original address, phone, fax, email, and hours.

## Rollback

Rollback options remain:

- WordPress page revisions for the edited pages.
- Restore the paired `before` HTML backups under `wp-backups/`.
- Remove or disable the `/contact/` rule in Tools > Redirection if the legacy URL needs a different target later.
