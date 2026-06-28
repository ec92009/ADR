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

### Quote form captcha resolution

- Opened MetForm form `2073` in Elementor.
- Confirmed the full form structure was still present in the Elementor preview.
- Selected the `reCAPTCHA` widget in Elementor's structure panel and removed it with Elementor's own `Supprimer` command.
- Saved the form through Elementor, restoring the public MetForm render without reintroducing captcha.
- Public verification URL:
  - `https://assurancesderueil.fr/demande-de-devis-assurance-a-rueil-malmaison/?nocache=captcha-disabled-verify-2`
- Verification result:
  - all expected quote-form fields render publicly;
  - one visible `Envoyer` submit button is present;
  - no `g-recaptcha-response`, `.g-recaptcha`, `mf-recaptcha`, or visible captcha text remains on the public page.

### Rollback notes

- The form content backup above contains the pre-intervention MetForm content from form `2073`.
- WordPress revisions for form `2073` should also be available in the admin editor.
- If the quote form render becomes incomplete again, restore form `2073` from the WordPress revision before the classic-editor captcha-removal attempt, then remove the reCAPTCHA widget only through Elementor.

## 2026-06-24

### Original detail restoration

- Goal: keep the refreshed visual design while restoring the fuller original business/service detail for Manuel's review.
- Captured `before` and `after` HTML backups for the live WordPress pages:
  - `wp-backups/page-7754-assurance-de-pret-a-rueil-malmaison-before-original-detail-2026-06-24.html`
  - `wp-backups/page-7754-assurance-de-pret-a-rueil-malmaison-after-original-detail-2026-06-24.html`
  - `wp-backups/page-7331-assurance-particuliers-rueil-malmaison-before-original-detail-2026-06-24.html`
  - `wp-backups/page-7331-assurance-particuliers-rueil-malmaison-after-original-detail-2026-06-24.html`
  - `wp-backups/page-2180-assurance-entreprise-rueil-malmaison-before-original-detail-2026-06-24.html`
  - `wp-backups/page-2180-assurance-entreprise-rueil-malmaison-after-original-detail-2026-06-24.html`
  - `wp-backups/page-7358-cabinet-de-courtage-en-assurances-rueil-malmaison-before-original-detail-2026-06-24.html`
  - `wp-backups/page-7358-cabinet-de-courtage-en-assurances-rueil-malmaison-after-original-detail-2026-06-24.html`
- Added restored-detail sections marked `adr-original-detail-v1` to:
  - Assurance de prêt: operation, subscription inputs, comparison points, exclusions, and broker guidance.
  - Particuliers: habitation, loyers impayés, santé/GAV, automobile/mobility, prévoyance, and borrower-insurance profile examples.
  - Professionnels: multirisques, loyers impayés, borrower insurance, responsabilité civile/professionnelle, prévoyance collective, fleet insurance, and executive unemployment insurance.
  - Cabinet: four-generation Rueil-Malmaison presence, client types, custom insurance objective, availability, and opening hours.

### Legacy contact URL

- Installed/configured the WordPress Redirection plugin enough to create a reversible admin-managed compatibility rule.
- Left optional permalink monitoring, redirect/404 logging, and IP collection off during Redirection setup.
- Added a 301 redirect:
  - source: `/contact/`
  - target: `/courtier-en-assurances-de-rueil-malmaison/`
- Public verification:
  - `/contact/` returns `301` with `x-redirect-by: redirection`;
  - following the redirect reaches `/courtier-en-assurances-de-rueil-malmaison/` with HTTP `200`;
  - the live contact page includes the original address, phone, fax, email, and opening hours.

### Verification

- Public checks confirmed the restored detail headings on Assurance de prêt, Particuliers, Professionnels, and Cabinet.
- Public checks confirmed the refreshed contact form still exposes the expected visible fields.
- Detailed audit recorded in `docs/audits/original-detail-restoration-2026-06-24.md`.

## 2026-06-25

### Public version footer

- Goal: add a discreet, site-wide version marker to the public WordPress site footer so the live site can be distinguished from local/static preview builds.
- Added a child-theme `wp_footer` hook in `instive-child/functions.php`.
- Initial public marker:
  - `ADR v117.0`
- Current public marker:
  - `v117.14`
- Hook/style marker:
  - `adr-version-footer-v1`
- Local backup captured before editing:
  - `wp-backups/functions-instive-child-before-version-footer-2026-06-25.php`
- Later rollback snapshots:
  - `wp-backups/functions-instive-child-before-live-quote-form-v117-1-2026-06-25.php`
  - `wp-backups/functions-instive-child-before-brand-plural-v117-2-2026-06-25.php`
  - `wp-backups/functions-instive-child-after-live-quote-form-v117-2-2026-06-25.php`
- Public verification URLs:
  - `https://assurancesderueil.fr/?nocache=version-footer-verify-1`
  - `https://assurancesderueil.fr/demande-de-devis-assurance-a-rueil-malmaison/?nocache=version-footer-verify-1`
- Verification result:
  - both initial public pages included `ADR v117.0`;
  - after the later quote-form/footer refinements, the public marker is `v117.14`;
  - the marker is positioned inside the existing `.copy-right` blue footer strip on the right;
  - the footer uses the header icon asset plus an `Assurances de Rueil` wordmark, avoiding the old singular image text;
  - public source includes `adr-version-footer-v1`.

### Live quote form switch

- Goal: show the refreshed quote form to regular visitors on the public WordPress quote page without editing fragile Elementor/MetForm form `2073` directly.
- Added a child-theme, page-scoped `wp_footer` hook for page `7427`.
- Hook/style/script marker:
  - `adr-live-quote-form-v117-2`
- Behavior:
  - the original MetForm shortcode remains in the HTML as a fallback;
  - after the refreshed form initializes, the old MetForm block is hidden;
  - the refreshed form submits to the existing MetForm endpoint for form `2073`;
  - the form reads the live `data-form-nonce` from the MetForm wrapper at runtime;
  - legacy MetForm keys are preserved and additive alias keys are included.
- Public verification:
  - the quote page source includes `adr-live-quote-form-v117-2`;
  - the rendered page exposes the refreshed form;
  - selecting a quote type reveals the extra fields;
  - `Envoyer` stays disabled until both required consent boxes are checked.

### Brand plural sweep

- Goal: remove the singular brand phrase `Assurance de Rueil` / `Assurance De Rueil` from public site output while preserving valid generic service phrases such as `assurance de prêt`.
- Added a narrow frontend output buffer marked `adr-brand-normalization-v1`.
- Replaced exact brand phrases only:
  - `Assurance De Rueil-Malmaison` to `Assurances De Rueil-Malmaison`;
  - `Assurance de Rueil-Malmaison` to `Assurances de Rueil-Malmaison`;
  - `Assurance De Rueil` to `Assurances De Rueil`;
  - `Assurance de Rueil` to `Assurances de Rueil`.
- Verification covered the homepage, quote page, cabinet, assurance de prêt, particuliers, professionnels, contact, mentions légales, privacy, cookies traceurs, and EU cookies pages.
- Verification result:
  - exact singular brand count is `0` on the checked public pages.

### Footer logo and Safari form controls

- Goal: align the footer brand with the header logo and normalize the public quote form controls in Safari.
- Version progression:
  - `v117.3`: footer logo changed from the old singular asset/faux `AR` mark to the header icon asset plus an `Assurances de Rueil` wordmark.
  - `v117.4`: footer logo layout tightened and live quote form fields normalized for Safari.
  - `v117.5`: footer logo pill changed to the footer blue with white wordmark text; footer strip height increased.
  - `v117.6`: footer logo border removed and the logo wrapper was expanded toward the full strip.
  - `v117.7`: footer strip background explicitly set to `#0A4464`.
  - `v117.8`: footer Bootstrap column positioning neutralized inside `.copy-right` so the logo centers within the actual strip.
  - `v117.9`: quote-page step-card headings constrained so long titles such as `Accompagnement` do not overflow their cards.
  - `v117.10`: quote-page contact row desktop columns made even and the phone field was centered in the middle column.
  - `v117.11`: phone-field height selector tightened so the rendered telephone input matches the radio tile height.
  - `v117.12`: quote-page contact row changed to the stacked layout everywhere: contact-preference tiles first, full-width telephone field below, smoker tiles below that.
  - `v117.13`: birthdate selects moved into the first section, the first-section label changed to `Informations nécessaires`, and the refreshed form now sends the legacy `mf-date` key through `FormData` instead of exposing a hidden `input[name="mf-date"]` that the old date helper could decorate.
  - `v117.14`: birthdate selects moved back behind the quote-type gate, remain optional, and appear visibly only once after a quote type is selected.
- Hook/style/script markers:
  - `adr-version-footer-v1`
  - `adr-live-quote-form-v117-14`
- Local rollback snapshots:
  - `wp-backups/functions-instive-child-before-footer-logo-v117-3-2026-06-25.php`
  - `wp-backups/functions-instive-child-after-footer-logo-v117-3-2026-06-25.php`
  - `wp-backups/functions-instive-child-after-footer-logo-form-controls-v117-4-2026-06-25.php`
  - `wp-backups/functions-instive-child-after-footer-pill-v117-5-2026-06-25.php`
  - `wp-backups/functions-instive-child-after-footer-pill-contained-v117-6-2026-06-25.php`
  - `wp-backups/functions-instive-child-after-footer-pill-merged-v117-7-2026-06-25.php`
  - `wp-backups/functions-instive-child-after-footer-pill-column-fix-v117-8-2026-06-25.php`
  - `wp-backups/functions-instive-child-after-step-card-heading-v117-9-2026-06-25.php`
  - `wp-backups/functions-instive-child-after-phone-field-v117-10-2026-06-25.php`
  - `wp-backups/functions-instive-child-after-phone-field-v117-11-2026-06-25.php`
  - `wp-backups/functions-instive-child-after-phone-field-vertical-v117-12-2026-06-25.php`
  - `wp-backups/functions-instive-child-after-date-placement-v117-13-2026-06-25.php`
  - `wp-backups/functions-instive-child-after-birthdate-optional-gated-v117-14-2026-06-25.php`
- Verification result:
  - public quote page source includes `adr-live-quote-form-v117-14`;
  - public quote page source includes `v117.14` and no `v117.13` markers;
  - refreshed form source includes no `name="mf-date"` input and still sets `payload.set('mf-date', ...)` before submission;
  - refreshed form source has no required marker or `required` attribute on `jour_naissance`, `mois_naissance`, or `annee_naissance`;
  - public quote page source includes `height: 64px !important` and `-webkit-appearance: none` for refreshed form controls;
  - rendered `Votre banque` input and `Votre profession` select both measure `64px` high after selecting a quote type;
  - footer image source is `cropped-flaticon2.png`;
  - footer wordmark reads `Assurances de Rueil`;
  - footer logo and strip both render as `rgb(10, 68, 100)`;
  - footer logo border width renders as `0px`;
  - footer logo is vertically contained inside the 78px `.copy-right` strip;
  - rendered step-card headings do not overflow at the default browser width or at a temporary 650px viewport;
  - at temporary 1528px, default, and 650px viewports, the contact section renders as a one-column stack with contact-preference tiles first, full-width telephone field below, and smoker tiles below that;
  - the telephone input renders at `46px`, matching the radio tile height;
  - rendered first fieldset reads `Informations nécessaires`;
  - before selecting a quote type, rendered live birthdate rows visible to the visitor: `0`;
  - after selecting a quote type, rendered live birthdate rows visible to the visitor: `1`, inside the `Informations utiles` fieldset;
  - rendered birthdate selects are optional and no stray `.adr-birthdate-selects` row is visible in the refreshed form;
  - public footer marker reads `v117.14`.

### Secret CSV and admin email

- Goal: give the cabinet a private CSV export of all MetForm quote requests and make future admin notification emails easier to read.
- Version:
  - `v117.15`: initial secret CSV export and ordered admin email.
  - `v117.16`: CSV export limited to quote entries dated `2026-01-01` or later.
- Hook/function markers:
  - `adr-quote-requests-v117-16`
  - `adr_update_quote_admin_email`
  - `adr-live-quote-form-v117-16`
- Behavior:
  - `https://assurancesderueil.fr/demandes-de-devis/?key=...` returns a CSV attachment when the long shared key is present;
  - the same path without the key returns `403`;
  - both successful and denied responses send `X-Robots-Tag: noindex, nofollow`;
  - the CSV is filtered at the WordPress query level with `ADR_QUOTE_REQUESTS_MIN_DATE = 2026-01-01 00:00:00`;
  - the CSV uses semicolon delimiters and includes: Date, ID, Civilité, Nom, Prénom, E-mail, Téléphone, Communication, Type de devis, Naissance, Fumeur, Banque, Profession, Adresse, Consentement;
  - the admin email sent to `contact@assurancesderueil.fr` is rebuilt with ordered rows: Civilité, Nom, Prénom, E-mail, Téléphone when present, Choix de communication, Type de devis, Date de naissance, Êtes-vous fumeur ?, Votre banque, Votre profession, Adresse, Consentement;
  - the admin email includes a button labeled `Télécharger le CSV des demandes`;
  - the secret key is intentionally not documented here; it is embedded in the live child-theme PHP and the local rollback snapshot.
- Local rollback snapshot:
  - `wp-backups/functions-instive-child-after-secret-table-email-v117-15-2026-06-25.php`
  - `wp-backups/functions-instive-child-after-secret-csv-2026-filter-v117-16-2026-06-25.php`
- Verification result:
  - the live theme editor content contains `adr-quote-requests-v117-16`, `ADR_QUOTE_REQUESTS_MIN_DATE`, `v117.16`, and no `v117.15` marker;
  - homepage source includes public footer marker `v117.16`;
  - public quote page source includes `adr-live-quote-form-v117-16`, `v117.16`, and no `v117.15` marker;
  - the secret CSV URL returns `HTTP 200`, `Content-Type: text/csv; charset=utf-8`, and `Content-Disposition: attachment`;
  - the downloaded CSV had 5 rows including the header at verification time, down from 8 before the 2026 date filter;
  - parsed CSV dates were all `2026-01-01` or later;
  - the no-key route returned `HTTP 403` with `X-Robots-Tag: noindex, nofollow`;
  - the canonical recipient remains `contact@assurancesderueil.fr`.

### Web-By-Elie credits

- Goal: add a discreet site-refresh/maintenance credit without changing the global footer.
- Version:
  - `v118.0`: appends a `Crédits du site` block only on Mentions légales and Contact via `the_content`.
- Hook/style markers:
  - `adr-webbyelie-credits-v118-0`
  - `adr_append_webbyelie_credit`
  - `adr-live-quote-form-v118-0`
- Behavior:
  - Mentions légales receives: `Refonte du site et maintenance : Web-By-Elie.com.`;
  - Contact receives: `Site rafraîchi et maintenu par Web-By-Elie.`;
  - both credit links point to `https://web-by-elie.com/`;
  - the footer content is otherwise unchanged; only the existing visible version marker advances to `v118.0`.
- Local rollback snapshot:
  - `wp-backups/functions-instive-child-after-webbyelie-credits-v118-0-2026-06-26.php`
- Verification result:
  - Mentions légales source includes `adr-webbyelie-credits-v118-0`, `adr-webbyelie-credit-legal`, `Web-By-Elie.com`, and `v118.0`;
  - Contact source includes `adr-webbyelie-credits-v118-0`, `adr-webbyelie-credit-contact`, `Web-By-Elie`, and `v118.0`;
  - Homepage source includes `v118.0` and no `Web-By-Elie` or `adr-webbyelie-credit` markers;
  - public quote page source includes `adr-live-quote-form-v118-0`, `adr_quote_consent_2026-06-26_v118.0`, and `v118.0`.

### Courtier wording

- Goal: switch public-facing `Société de courtage` wording back to `Courtier` wording at Manuel's request.
- Version:
  - `v118.1`: output-normalized `Société de courtage` / `société de courtage` / ASCII `Societe de courtage` phrases to `Courtier` / `courtier` equivalents across public HTML, Yoast meta, OpenGraph, and JSON-LD.
  - `v118.2`: fixed French agreement fallout from the first pass, including `Courtier indépendante`, `d’une courtier`, and `Une courtier`.
  - `v118.3`: shortened the first homepage FAQ question from `Quel est le rôle d’un courtier en assurances à Rueil-Malmaison ?` to `Quel est le rôle d’un courtier en assurances ?`.
  - `v118.4`: changed the second homepage FAQ question to `Quels types de contrats sont proposés par Assurances de Rueil ?`.
- Hook/style/script markers:
  - `adr-public-wording-normalization-v118-4`
  - `adr-live-quote-form-v118-4`
- Behavior:
  - homepage title/meta/schema now use `Courtier en assurances à Rueil-Malmaison`;
  - homepage hero now uses `Courtier indépendant depuis quatre générations` and `Courtier en assurances à Rueil-Malmaison`;
  - homepage FAQ now uses `Quel est le rôle d’un courtier...` and `Un courtier compare...`;
  - homepage FAQ now uses `Quels types de contrats sont proposés par Assurances de Rueil ?`;
  - Cabinet page sentence now reads `Le statut de courtier permet...`;
  - Mentions légales now use `courtier en assurances` / `Courtier en assurances` in the editor/legal identity blocks.
- Local rollback snapshots:
  - `wp-backups/functions-instive-child-after-courtier-wording-v118-1-2026-06-26.php`
  - `wp-backups/functions-instive-child-after-courtier-wording-v118-2-2026-06-26.php`
  - `wp-backups/functions-instive-child-after-faq-courtier-shortening-v118-3-2026-06-26.php`
  - `wp-backups/functions-instive-child-after-faq-contract-question-v118-4-2026-06-26.php`
- Verification result:
  - public sweep across sitemap pages found `0` matches for `société de courtage` / `societe de courtage`;
  - public sweep found `0` matches for the bad agreement strings `courtier indépendante`, `une courtier`, and `d’une courtier`;
  - homepage source includes `Courtier en assurances à Rueil-Malmaison`, `Courtier indépendant`, `d’un courtier`, `Un courtier`, and `v118.4`;
  - homepage source includes `Quel est le rôle d’un courtier en assurances ?` and no longer includes the old `Quel est le rôle... à Rueil-Malmaison` FAQ wording;
  - homepage source includes `Quels types de contrats sont proposés par Assurances de Rueil ?` and no longer includes `Quels contrats Assurances de Rueil peut-il accompagner`;
  - public quote page source includes `adr-live-quote-form-v118-4`, `adr_quote_consent_2026-06-26_v118.4`, and `v118.4`.

### Homepage and service-page cleanup

- Goal: reduce repeated `Rueil-Malmaison` wording in visible hero/FAQ copy, remove fax from public contact surfaces, update opening hours, and keep `Loyers impayés` on Particuliers rather than Professionnels.
- Version:
  - `v118.5`: shortened the homepage hero H1 to `Courtier en assurances`, removed the fax chip/paragraph, and updated public opening hours to `9:00-12:30` and `14:00-18:00`.
  - `v118.6`: removed the duplicated `Loyers impayés` detailed card from Professionnels without affecting the Particuliers page.
  - `v118.7`: removed repetitive hero kickers on Assurance de prêt, Particuliers, and Professionnels; shortened those visible H1s by removing `à Rueil-Malmaison`; removed `loyers impayés` from the Professionnels meta description.
  - `v118.8`: added exact fallback block removals for the homepage location FAQ and the Professionnels `Loyers impayés` card.
  - `v118.9`: fixed the output normalizer's early `return str_replace(...)` so the later cleanup rules actually run.
- Hook/style/script markers:
  - `adr-public-wording-normalization-v118-9`
  - `adr-live-quote-form-v118-9`
- Local rollback snapshots:
  - `wp-backups/functions-instive-child-after-home-contact-pro-part-v118-5-2026-06-26.php`
  - `wp-backups/functions-instive-child-after-pro-loyers-removal-v118-6-2026-06-26.php`
  - `wp-backups/functions-instive-child-after-hero-faq-pro-cleanup-v118-7-2026-06-26.php`
  - `wp-backups/functions-instive-child-after-exact-block-removal-v118-8-2026-06-26.php`
  - `wp-backups/functions-instive-child-after-normalizer-return-fix-v118-9-2026-06-26.php`
- Verification result:
  - homepage source includes public footer marker `v118.9` and no `v118.8` markers;
  - homepage source no longer includes `Le cabinet est-il situé à Rueil-Malmaison ?`;
  - homepage and contact surfaces no longer include `Fax`, `+33 1 47 51 00 78`, or `33147510078`;
  - homepage opening hours render as `9:00 à 12:30 et de 14:00 à 18:00` / `9:00-12:30 and 14:00-18:00`;
  - Assurance de prêt visible H1 renders as `Assurance de prêt` and its repetitive hero kicker is removed;
  - Particuliers visible H1 renders as `Assurance particuliers`, its repetitive hero kicker is removed, and `Loyers impayés` remains present on that page;
  - Professionnels visible H1 renders as `Assurance entreprise`, its repetitive hero kicker is removed, and `Loyers impayés` / `loyers impayés` count is `0` in the public source;
  - public quote page source includes `adr-live-quote-form-v118-9`, `adr_quote_consent_2026-06-26_v118.9`, and `v118.9`.

### Rollback notes

- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-exact-block-removal-v118-8-2026-06-26.php` to roll back only the `v118.9` normalizer return fix.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-hero-faq-pro-cleanup-v118-7-2026-06-26.php` to roll back the `v118.8` exact fallback block removals while keeping the visible hero cleanup.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-pro-loyers-removal-v118-6-2026-06-26.php` to roll back the `v118.7` hero/kicker/meta cleanup while keeping the Pro `Loyers impayés` removal rule.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-home-contact-pro-part-v118-5-2026-06-26.php` to roll back the `v118.6` unconditional Pro `Loyers impayés` removal rule while keeping the homepage/contact cleanup.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-faq-contract-question-v118-4-2026-06-26.php` to roll back all `v118.5`-`v118.9` homepage/contact/service-page cleanup while keeping the `v118.4` Courtier/FAQ wording.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-faq-courtier-shortening-v118-3-2026-06-26.php` to roll back only the `v118.4` second FAQ question change while keeping the first FAQ shortening and Courtier wording.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-courtier-wording-v118-2-2026-06-26.php` to roll back the `v118.3`/`v118.4` FAQ question changes while keeping the Courtier wording and grammar cleanup.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-webbyelie-credits-v118-0-2026-06-26.php` to roll back all `v118.1`-`v118.4` Courtier/FAQ wording changes while keeping the Web-By-Elie page credits, secret CSV route, 2026 date filter, and admin email rewrite.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-secret-csv-2026-filter-v117-16-2026-06-25.php` to roll back only the `v118.0` Web-By-Elie page credits while keeping the secret CSV route, 2026 date filter, and admin email rewrite.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-secret-table-email-v117-15-2026-06-25.php` to roll back only the `v117.16` 2026 date filter while keeping the secret CSV route and admin email rewrite.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-birthdate-optional-gated-v117-14-2026-06-25.php` to roll back the secret CSV route and admin email rewrite while keeping the `v117.14` public quote-form layout.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-date-placement-v117-13-2026-06-25.php` to roll back only the v117.14 optional/gated birthdate placement.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-phone-field-vertical-v117-12-2026-06-25.php` to roll back only the birthdate placement/`Informations nécessaires` wording change.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-phone-field-v117-11-2026-06-25.php` to roll back only the stacked contact row layout while keeping the phone-height selector.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-phone-field-v117-10-2026-06-25.php` to roll back the final stronger phone-height selector as well.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-step-card-heading-v117-9-2026-06-25.php` to roll back all phone-field contact row refinements.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-footer-pill-column-fix-v117-8-2026-06-25.php` to roll back only the step-card heading overflow fix.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-footer-pill-merged-v117-7-2026-06-25.php` to roll back only the footer Bootstrap column positioning fix.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-footer-pill-contained-v117-6-2026-06-25.php` to roll back the explicit footer strip color.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-footer-pill-v117-5-2026-06-25.php` to roll back the footer wrapper/outline removal.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-footer-logo-form-controls-v117-4-2026-06-25.php` to roll back all footer pill color/height refinements while keeping the Safari form-control normalization.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-after-footer-logo-v117-3-2026-06-25.php` to roll back only the Safari form-control normalization while keeping the footer logo update.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-before-footer-logo-v117-3-2026-06-25.php` to roll back the footer logo update as well while keeping the refreshed quote form and brand plural sweep.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-before-brand-plural-v117-2-2026-06-25.php` to roll back only the brand plural sweep while keeping the refreshed live quote form.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-before-live-quote-form-v117-1-2026-06-25.php` to roll back both the live quote form switch and later footer/brand refinements.
- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-before-version-footer-2026-06-25.php` to return to the pre-version-footer child theme.
- These changes do not alter the Elementor page content or MetForm form `2073`.

## 2026-06-27

### GH.io `v119.1` publish to live WordPress

- Goal: push the approved GitHub Pages `v119.1` brouillon/state into the public WordPress site while keeping the change reversible through the child-theme `functions.php` layer.
- Method:
  - created a live before snapshot from the WordPress theme editor;
  - built and linted a local candidate with MAMP PHP;
  - published through the authenticated WordPress theme editor using the browser-assisted CodeMirror paste workflow documented in `docs/wordpress-theme-editor-publish-workflow.md`;
  - reloaded the editor after publish to confirm the admin submit button label returned to `Mettre à jour le fichier`.
- Version:
  - `v119.1`
- Hook/style/script markers:
  - `adr-public-wording-normalization-v119-1`
  - `adr-live-quote-form-v119-1`
  - `adr-partners-v119-1`
- Behavior:
  - homepage footer marker now renders as `v119.1`;
  - homepage no longer renders the obsolete `Pages reprises du site original` mini-nav/recap or FAQ section;
  - homepage service labels now include `Assurance de prêt`, `Assurances des particuliers`, and `Assurances des professionnels`;
  - homepage now includes the partner-logo strip for Thelem, AXA, April, and Generali, using the approved GH.io image assets;
  - quote page title is normalized to `Demande de devis d’assurance`;
  - Particuliers page title is normalized to `Assurances des particuliers`;
  - Professionnels page title is normalized to `Assurances entreprise`;
  - the quote-form JS marker and consent version are updated to `119.1` / `adr_quote_consent_2026-06-27_v119.1`.
- Local rollback snapshots:
  - before publish: `wp-backups/functions-instive-child-before-v119-2-wordpress-publish-2026-06-27.php`
  - after publish: `wp-backups/functions-instive-child-after-v119-1-wordpress-publish-2026-06-27.php`
- Verification result:
  - public homepage source includes `v119.1` and no longer includes `v118.9`;
  - public homepage source no longer includes `Pages reprises du site original` or `Questions fréquentes`;
  - public homepage source includes `adr-partners-v119-1`, `Partenaires assureurs`, and all four partner asset filenames;
  - public homepage source includes `Assurance de prêt`, `Assurances des particuliers`, and `Assurances des professionnels`;
  - public homepage source no longer includes `Assurances de prêts`, `Assurance des particuliers`, `Assurance particuliers`, or `Assurance à Rueil`;
  - public quote, assurance de prêt, particuliers, and professionnels pages include `v119.1` and no longer include `v118.9`;
  - public quote page includes `Demande de devis d’assurance` and no longer includes `Demande de devis assurance`.

### Rollback notes

- Restore `instive-child/functions.php` from `wp-backups/functions-instive-child-before-v119-2-wordpress-publish-2026-06-27.php` to roll back the `v119.1` WordPress publish and return to the previous `v118.9` live child-theme behavior.

### Quote form nonce and source-truth `v119.3`

- Goal: keep the approved GitHub Pages `v119.3` page shells as the live WordPress source of truth while preserving dynamic MetForm submission behavior.
- Method:
  - published the `v119.3` child-theme candidate through the WordPress theme editor;
  - added a dynamic residual pass so the rendered MetForm wrapper receives fresh `wp_rest` and `form_nonce` values instead of stale static GH.io nonces;
  - updated the public quote form fetch to send the current `X-WP-Nonce` header.
- Verification result:
  - public quote page source includes `adr-live-quote-form-v119-3`, `adr_quote_consent_2026-06-27_v119.3`, and footer marker `v119.3`;
  - public quote page source includes `data-wp-nonce`, `data-form-nonce`, and `headers: wpNonce ? { 'X-WP-Nonce': wpNonce } : {}`;
  - an intercepted mobile submission reached `wp-json/metform/v1/entries/insert/2073` with the nonce header and form nonce present, without sending a real lead.

### Quote acknowledgement email MU-plugin

- Goal: replace MetForm's plain, centered user confirmation email for quote form `2073` with a professional branded acknowledgement.
- Method:
  - created a split live module under `wp-live-plugin/adr-site-fixes/`;
  - installed it as the WordPress Must-Use plugin `ADR Site Fixes` version `119.3.1` using a one-shot, self-removing bootstrap block in `functions.php`;
  - restored profile syntax highlighting after the seed operation.
- Behavior:
  - the user-facing confirmation email body is replaced through `metform_confirmation_user_email_body` only for form `2073`;
  - the outgoing subject becomes `Votre demande de devis - Assurances de Rueil`;
  - From/Reply-To are normalized to `Assurances de Rueil <contact@assurancesderueil.fr>`;
  - copy is left-aligned, uses plural `Assurances de Rueil`, includes a short request summary when available, and links to the privacy policy.
- Verification result:
  - `ADR Site Fixes` appears in WordPress Must-Use plugins with version `119.3.1`;
  - public quote page remains HTTP 200 and still includes dynamic nonce/script markers;
  - `instive-child/functions.php` no longer contains the bootstrap block after self-removal and still includes the `v119.3` nonce-header form script.
- Local after snapshot:
  - `wp-backups/functions-instive-child-after-adr-site-fixes-mu-seed-v119-3-2026-06-27.php`

### Rollback notes

- To roll back only the acknowledgement-email change, remove `wp-content/mu-plugins/adr-site-fixes.php` and the `wp-content/mu-plugins/adr-site-fixes/` directory from the WordPress server.
- Do not restore an older `functions.php` for this rollback; the email module now lives outside the child theme.

### Homepage source-truth reconciliation

- Goal: close the visual-parity backlog item for the live homepage versus the GitHub Pages `v119.3` source-of-truth mock.
- Method:
  - fetched the cache-busted GH.io homepage and live WordPress homepage;
  - compared the `adr-home-clean-mock` shell in both sources;
  - checked rendered DOM geometry and content markers through the in-app browser at desktop and mobile-sized viewports.
- Result:
  - no live code patch was needed;
  - both sources include the same approved homepage shell, service-card copy, service tag bubbles, phone CTA treatment, partner section, normalized `<title>Assurances de Rueil</title>`, and footer marker `v119.3`;
  - all four partner logos load;
  - horizontal overflow is `0` in the rendered checks;
  - the only homepage-shell source differences are expected production URL rewrites and absolute GH.io asset paths on live WordPress.

### Live visual refresh MU-plugin `v119.5`

- Goal: port the approved GH.io `v119.5` photography and day/night persistence pass to live WordPress without growing the oversized child-theme `functions.php`.
- Method:
  - extended the split `ADR Site Fixes` Must-Use plugin to version `119.5.0`;
  - added `includes/live-visual-refresh.php`, an output-buffer module that runs after the child-theme page-shell normalizer;
  - installed the updated MU-plugin through a small, linted one-shot bootstrap in the theme editor;
  - temporarily disabled syntax highlighting to expose the real textarea, then restored the profile setting;
  - removed temporary bootstrap wrappers from `instive-child/functions.php` and confirmed the theme file returned to its original 351,567-character editor length.
- Behavior:
  - replaces legacy WordPress photo URLs with the GH.io `assets/adr-photo-*-v119-5.jpg` images;
  - updates OpenGraph/JSON-LD image dimensions for the new photos;
  - upgrades public footer/source markers from `v119.3` to `v119.5`;
  - injects the existing `adr-theme-persistence.js` script as `adr-theme-persistence-v119-5`.
- Verification result:
  - WordPress Must-Use plugins lists `ADR Site Fixes` as version `119.5.0`;
  - public cache-busted checks passed on the homepage, Particuliers, Professionnels, Cabinet, Assurance de prêt, Demande de devis, and Courtier pages;
  - each checked page includes `adr-live-visual-refresh-v119-5`, footer marker `v119.5`, `adr-theme-persistence-v119-5`, and the expected page-specific `adr-photo-*-v119-5.jpg` URL;
  - no checked public page includes `ADR_MU_PLUGIN_BOOTSTRAP` or `ADR_THEME_BOOTSTRAP_CLEANUP`;
  - `instive-child/functions.php` no longer contains either temporary bootstrap marker after cleanup.

### Rollback notes

- To roll back only the `v119.5` visual refresh, restore the `ADR Site Fixes` MU-plugin from the previous `119.3.1` files or remove the `includes/live-visual-refresh.php` require from `wp-content/mu-plugins/adr-site-fixes.php`.
- Do not remove the whole `ADR Site Fixes` MU-plugin unless also rolling back the branded quote acknowledgement email.

### Live contact/phone polish MU-plugin `v119.6`

- Goal: port the approved GH.io `v119.6` Courtier/contact form polish to live WordPress without editing the oversized child-theme logic directly.
- Method:
  - extended the split `ADR Site Fixes` Must-Use plugin to version `119.6.0`;
  - kept the high-resolution `adr-photo-*-v119-5.jpg` assets in place while upgrading live source/footer markers to `v119.6`;
  - added an output-buffer pass for the live Courtier/contact MetForm wrapper that injects the required `Téléphone *` field, removes stale reCAPTCHA widget/script output, and adds alignment CSS matching the GH.io layout;
  - added `inputmode="tel"` to the live quote-page phone input so international prefixes such as `+34` are not fighting the mobile keyboard;
  - repaired the prior JSON-LD photo-dimension artifact caused by PHP replacement backreference ambiguity;
  - installed the updated MU-plugin through a small, linted one-shot bootstrap in the theme editor.
- Verification result:
  - WordPress Must-Use plugins lists `ADR Site Fixes` as version `119.6.0`;
  - public cache-busted Courtier/contact source includes `adr-live-visual-refresh-v119-6`, `adr-theme-persistence-v119-6`, footer marker `v119.6`, `elementor-element-adr-phone`, and `adr-contact-phone-align-v1`;
  - public cache-busted Courtier/contact source no longer includes `mf-recaptcha`, `g-recaptcha`, `recaptcha-support`, `reCAPTCHA`, the malformed `"58692` JSON-LD fragment, or any `ADR_MU_PLUGIN_BOOTSTRAP` marker;
  - public cache-busted Demande de devis source includes `adr-live-quote-form-v119-6`, `version = '119.6'`, `adr_quote_consent_2026-06-27_v119.6`, and `inputmode="tel"`;
  - public cache-busted Home and Particuliers spot checks include `adr-live-visual-refresh-v119-6`, `adr-theme-persistence-v119-6`, footer marker `v119.6`, and the expected `adr-photo-*-v119-5.jpg` URLs;
  - `instive-child/functions.php` returned to its page-side editor length of `351567` and no longer contains `ADR_MU_PLUGIN_BOOTSTRAP` after self-removal.
- Note:
  - a full local after-snapshot was not kept for this deploy because the browser/editor export path truncated the copied file, which is the failure mode this workflow is designed to avoid. The cleanup check used page-side length and marker verification instead.

### Rollback notes

- To roll back only the `v119.6` contact/phone polish, restore the `ADR Site Fixes` MU-plugin from the previous `119.5.0` files.
- Do not remove the whole `ADR Site Fixes` MU-plugin unless also rolling back the branded quote acknowledgement email.

### Live contact submit and phone preservation MU-plugin `v119.7`

- Goal: fix the remaining contact-form `Something went wrong. Envoi non autorisé.` response and the phone field dropping an international `+34` prefix, while keeping GH.io as the source of truth.
- Root cause:
  - GH.io static contact/quote mocks could still attempt live MetForm POSTs with frozen nonces;
  - live WordPress removed visible contact reCAPTCHA output, but MetForm form `7487` still contained an `mf-recaptcha` widget in backend Elementor metadata, so REST submissions were rejected before normal validation;
  - browser telephone controls could normalize typed international numbers in a way that removed the leading country prefix.
- Method:
  - published GH.io `v119.7` static changes that add a `adr-form-phone-preserver-v119-7` guard to contact and quote forms;
  - changed quote/contact phone controls from `type="tel"` to `type="text"` with `inputmode="tel"` and `autocomplete="tel"`;
  - extended `ADR Site Fixes` to version `119.7.0`;
  - added a narrow `get_post_metadata` filter for MetForm contact form `7487` that removes only `mf-recaptcha` nodes and inserts a backend `telephone` field before `adresse`;
  - added a `metform_filter_before_store_form_data` fallback to preserve posted `telephone` values.
- Local verification:
  - PHP lint passes for all `ADR Site Fixes` files;
  - local rendered `courtier.html` keeps `+34 636 63 03 38`, displays the static preview status on submit, and does not show the old unauthorized message;
  - local rendered `demande-de-devis.html` keeps `+34 636 63 03 38` after the quote type reveals the phone field;
  - synthetic MetForm metadata test confirms `mf-recaptcha` is removed and backend `telephone` is present.
- Live verification result:
  - WordPress Must-Use plugins lists `ADR Site Fixes` as version `119.7.0`;
  - public cache-busted Courtier/contact source includes `adr-live-visual-refresh-v119-7`, `adr-theme-persistence-v119-7`, footer marker `v119.7`, `elementor-element-adr-phone`, `type="text"`, and `adr-form-phone-preserver-v119-7`;
  - public cache-busted Courtier/contact source no longer includes visible `mf-recaptcha`, `g-recaptcha`, `recaptcha-support`, `reCAPTCHA`, old error text, or temporary bootstrap markers;
  - rendered live Courtier/contact keeps `+34 636 63 03 38` in the phone field without submitting the form;
  - public cache-busted Demande de devis source includes `adr-live-quote-form-v119-7`, `version = '119.7'`, `adr_quote_consent_2026-06-27_v119.7`, and `type="text"` on the quote phone field;
  - public homepage source includes `adr-live-visual-refresh-v119-7`, `adr-theme-persistence-v119-7`, footer marker `v119.7`, and the approved `adr-photo-home-hero-v119-5.jpg`;
  - `instive-child/functions.php` returned to its page-side editor length of `351567`, CodeMirror syntax highlighting was restored, and no `ADR_MU_PLUGIN_BOOTSTRAP_V119_7` or `ADR_THEME_BOOTSTRAP_CLEANUP_V119_7` marker remains.
- Rollback notes:
  - to roll back only `v119.7`, restore the `ADR Site Fixes` MU-plugin from the `119.6.0` files and revert GH.io static files to `v119.6`;
  - do not remove the whole `ADR Site Fixes` MU-plugin unless also rolling back the branded quote acknowledgement email.

### Contact acknowledgement email MU-plugin patch `119.7.1`

- Goal: give the Courtier/contact page the same branded user acknowledgement email as the quote form, instead of MetForm's old centered/plain confirmation.
- Root cause:
  - the branded acknowledgement module introduced for `v119.3` was intentionally scoped only to quote MetForm form `2073`;
  - the contact page uses MetForm form `7487`, so successful contact submissions still used the default MetForm user-confirmation template.
- Method:
  - extended the `ADR Site Fixes` Must-Use plugin to version `119.7.1`;
  - generalized the acknowledgement module to handle both form `2073` and form `7487`;
  - added a contact-specific body marker `adr-contact-user-email-v119-7-1` and subject `Votre message - Assurances de Rueil`;
  - kept the quote subject as `Votre demande de devis - Assurances de Rueil`;
  - installed the updated MU-plugin through a small, linted, self-removing bootstrap, without leaving changes in the oversized child-theme `functions.php`.
- Verification result:
  - PHP lint passes for all `ADR Site Fixes` files and the generated bootstrap payload;
  - synthetic MetForm email verification confirms form `7487` returns the branded `Message bien reçu` body, contact marker, contact subject, and preserves `+34 636 63 03 38`;
  - WordPress Must-Use plugins lists `ADR Site Fixes` as version `119.7.1`;
  - `instive-child/functions.php` returned to its page-side editor length of `351567` with no `ADR_MU_PLUGIN_BOOTSTRAP_V119_7_1` marker;
  - public cache-busted Courtier/contact and Demande de devis pages still render, keep title `Assurances de Rueil`, keep footer marker `v119.7`, and keep phone controls as `type="text"` with `inputmode="tel"`.
- Note:
  - no real live contact submission was sent during verification; that would send an actual acknowledgement email and lead/admin notification.
- Rollback notes:
  - to roll back only this contact acknowledgement patch, restore the `ADR Site Fixes` MU-plugin from the previous `119.7.0` files;
  - do not remove the whole MU-plugin unless also rolling back the quote acknowledgement, visual refresh, contact phone, and phone-preservation fixes.

### Child-theme `functions.php` split into MU-plugin modules `119.8.1`

- Goal: move the first four large live behavior blocks out of the oversized child-theme `functions.php` and into versioned `ADR Site Fixes` modules, while preserving the approved `v119.7` public site.
- Method:
  - extended `ADR Site Fixes` to version `119.8.1`;
  - extracted the private request CSV/admin email layer into `includes/quote-requests-export.php`;
  - extracted the public page-shell/output normalizer into `includes/page-shell-normalizer.php`;
  - extracted the live quote-form adapter into `includes/form-adapters.php`;
  - renamed/generalized `includes/quote-user-email.php` to `includes/public-user-email.php`;
  - updated the MU-plugin loader to require the split modules explicitly;
  - reduced live `instive-child/functions.php` to the base enqueue and Web-By-Elie credit block.
- Contact form admin path:
  - MetForm contact form `7487` is included in the private CSV export alongside quote form `2073`;
  - the CSV now includes a `Source` column for `Devis` vs `Contact`;
  - contact admin notifications are rewritten to `Nouveau message depuis le site - Assurances de Rueil`;
  - `contacts@assurancesderueil.fr` is accepted as an alias, with canonical delivery to `contact@assurancesderueil.fr`.
- Deployment note:
  - the prepend-only bootstrap was not saved after visual inspection showed it could replace the visible editor instead of prepending to the existing file;
  - the final deployment used a replacement-safe bootstrap, which installed the MU-plugin files from pinned GitHub commit `e6d7dd2`, verified SHA-256 hashes, and wrote the slim `functions.php` payload directly.
- Verification result:
  - PHP lint passes for all `ADR Site Fixes` PHP files;
  - local synthetic mail harness confirms contact form `7487` admin mail is normalized, preserves `+34 636 63 03 38`, and does not rewrite user acknowledgement emails addressed to the visitor;
  - WordPress Must-Use plugins lists `ADR Site Fixes` as version `119.8.1`;
  - live `instive-child/functions.php` is `91` lines / `2,936` editor characters;
  - live `functions.php` contains no `ADR_MU_PLUGIN_REPLACE_BOOTSTRAP_V119_8_1`, `ADR_MU_PLUGIN_SPLIT_BOOTSTRAP_V119_8`, `adr-quote-requests-v118-9`, `adr-public-wording-normalization-v119-3`, or `adr-live-quote-form-v119-3` markers;
  - public Home, Courtier/contact, and Demande de devis pages return HTTP 200;
  - public pages still include `adr-live-visual-refresh-v119-7` and footer marker `v119.7`;
  - quote and contact phone fields still render as `type="text"` with telephone input hints.
- Rollback notes:
  - restore the previous `ADR Site Fixes` MU-plugin files from GitHub before `119.8.1` and restore `instive-child/functions.php` from the backup written under `wp-content/mu-plugins/adr-site-fixes-backups/` if the split needs to be reversed;
  - because the old behavior now lives in MU-plugin modules, do not paste the old full child-theme file unless deliberately rolling back the whole split.

### Live contact and quote form verification, 2026-06-28

- User-reported real-world verification:
  - live contact form `7487` sends the branded visitor acknowledgement and its admin notification was received by Manu;
  - live quote form `2073` sends the branded visitor acknowledgement and its admin notification was received by Manu;
  - the private CSV export was reviewed and looked convincing;
  - no further code change was needed for this verification.
- Note:
  - verification used real emails submitted by the user; no extra agent-submitted test was needed.

### Contact message storage/export/admin delivery MU-plugin `120.0`

- Goal: preserve the free-text message from contact form `7487` everywhere the cabinet expects to see it.
- Method:
  - extended `ADR Site Fixes` to version `120.0` under the canonical 2026-06-28 visible version;
  - added a `metform_filter_before_store_form_data` pass for contact form `7487`;
  - stores the posted contact message under canonical `message` and keeps legacy `mf-textarea` as a fallback/compatibility key;
  - keeps `telephone` preservation in the same storage pass;
  - updates the private CSV/export normalizer so the `Message` column prefers `message` and falls back to `mf-textarea`;
  - updates contact admin delivery so messages are sent to canonical `contact@assurancesderueil.fr`; `contacts@assurancesderueil.fr` remains accepted only as an alias/detection input.
- Deployment note:
  - installed through a linted replacement bootstrap marked `ADR_MU_PLUGIN_REPLACE_BOOTSTRAP_V120_0`;
  - the bootstrap fetched pinned GitHub commit `64fdba7`, verified SHA-256 hashes, wrote the MU-plugin files, and restored the slim child-theme `functions.php`.
- Verification result:
  - PHP lint passes for the changed `ADR Site Fixes` files and generated bootstrap payload;
  - local synthetic harness verifies contact storage aliases `telephone`, `message`, and `mf-textarea`;
  - local synthetic harness verifies the contact admin email body includes `Message`, the CSV headers include `Message`, and canonical admin delivery is `contact@assurancesderueil.fr`;
  - WordPress Must-Use plugins lists `ADR Site Fixes` as version `120.0`;
  - public Home, Courtier/contact, and Demande de devis pages return HTTP 200;
  - public Home includes `adr-live-visual-refresh-v120-0`, `adr-theme-persistence-v120-0`, and footer marker `v120.0`;
  - public Courtier/contact includes `Message`, `Téléphone`, `adr-form-phone-preserver-v120-0`, `adr-live-visual-refresh-v120-0`, `adr-theme-persistence-v120-0`, and footer marker `v120.0`;
  - public Demande de devis includes `adr-live-quote-form-v120-0`, `adr_quote_consent_2026-06-28_v120.0`, `adr-form-phone-preserver-v120-0`, `adr-live-visual-refresh-v120-0`, `adr-theme-persistence-v120-0`, and footer marker `v120.0`;
  - no checked public response includes `Something went wrong. Envoi non autorisé.` or the replacement bootstrap marker.
- Rollback notes:
  - to roll back only the `120.0` contact-message storage/admin-delivery change, restore the `ADR Site Fixes` MU-plugin files from the previous `119.8.1` commit;
  - do not paste the old full child-theme `functions.php` unless deliberately rolling back the entire MU-plugin split.

### Quote live payload preservation MU-plugin `120.2`

- Goal: fix the live quote form `2073` storage gap where the browser posted `telephone` and `type_devis`, but the saved MetForm entry and private CSV dropped those two fields.
- Method:
  - extended `ADR Site Fixes` to version `120.2`;
  - added a shared live-payload merge helper for quote/contact request fields;
  - merges posted live-form fields back into `metform_entries__form_data` on MetForm entry meta creation/update;
  - reuses the same merge helper for visitor acknowledgement emails so the email, admin email, and CSV read from aligned data;
  - kept the quote birthdate format as French `DD-MMM-YYYY`, e.g. `11-JUIN-1957`;
  - bumped active GH.io/static and live version markers/cache-busts to `v120.2`.
- Deployment note:
  - installed through replacement bootstrap `ADR_MU_PLUGIN_REPLACE_BOOTSTRAP_V120_2`;
  - bootstrap fetched pinned GitHub commit `64efedb`, verified SHA-256 hashes, wrote the MU-plugin files, and restored the slim child-theme `functions.php`.
- Verification result:
  - MAMP PHP lint passes on PHP `8.4.1` and PHP `7.4.33` for the changed MU-plugin files and generated bootstrap;
  - public Home, Courtier/contact, and Demande de devis pages return HTTP 200, show `v120.2`, include the expected `v120-2` markers, and do not include `Something went wrong. Envoi non autorisé.` or a replacement-bootstrap marker;
  - live `instive-child/functions.php` is `91` lines / `2,936` editor characters and contains no replacement bootstrap marker;
  - live contact test row `8203` used `COHEN - 2026-06-28 12-14-09 - CONTACT`, displayed a success message, sent the branded visitor acknowledgement, preserved `+34 636 63 03 38`, stored the address over two CSV lines, and included the stamped message in the CSV `Message` column;
  - live quote test row `8204` used `COHEN - 2026-06-28 12-14-09 - DEVIS`, displayed a success message, sent the branded visitor acknowledgement, did not echo the birthdate to the visitor, and stored `Téléphone: +34 636 63 03 38`, `Type de devis: Assurance de prêt`, `Naissance: 11-JUIN-1957`, `Communication: E-mail`, `Fumeur: Non`, `Banque: Banque test`, and `Profession: Cadres` in the CSV.
- Rollback notes:
  - to roll back only the `120.2` quote-payload preservation change, restore the `ADR Site Fixes` MU-plugin files from the previous `120.1` deployment;
  - do not paste the old full child-theme `functions.php` unless deliberately rolling back the entire MU-plugin split.
