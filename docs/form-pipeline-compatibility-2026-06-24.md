# Quote Form Pipeline Compatibility - 2026-06-24

This note records the current submission contract for MetForm form `2073` and the safest next phase for changing the form while preserving any unknown downstream consumer.

## Manuel Decision Update

On 2026-06-24, Manuel confirmed that he is comfortable receiving by email all information a user willingly shares through the form.

Implementation consequence:

- Keep storing submissions in WordPress/MetForm entries so `wp_postmeta` remains available for future processing.
- Preserve all legacy keys, because an unknown downstream consumer may rely on them.
- Add clearer additive keys/aliases for the refreshed form.
- For the interim production path, forward the complete submitted payload to the configured email target.
- The public business email visible across the site is `contact@assurancesderueil.fr`.
- The exact MetForm admin notification recipient is not exposed in public HTML; confirm it in WordPress form settings before final launch.
- The static quote route can submit to MetForm endpoint `https://assurancesderueil.fr/wp-json/metform/v1/entries/insert/2073` with form nonce `f95577a433`. Empty/no-nonce POSTs are rejected with `Envoi non autorisé`, while a form-nonce probe created WordPress entry `8188`.

## Current Live Path

- Public quote page: `https://assurancesderueil.fr/demande-de-devis-assurance-a-rueil-malmaison/`
- Live wrapper ID: `metform-wrap-2073-2073`
- Live form ID: `2073`
- Live submit endpoint: `https://assurancesderueil.fr/wp-json/metform/v1/entries/insert/2073`
- Live plugin version observed in assets: MetForm `4.0.1`
- Public WordPress REST route `wp/v2/metform-entry` reports entry metadata, but the sampled public shape did not expose submitted payload, content, or meta values.
- MetForm form settings are not exposed in public HTML or unauthenticated REST responses. The next pipeline hop can only be confirmed from the database or admin UI.

## WordPress Storage Shape

MetForm stores submitted entries as WordPress posts plus postmeta:

```text
wp_posts
  post_type = metform-entry
  post_status = publish

wp_postmeta
  post_id = entry post ID
  meta_key = metform_entries__form_id
  meta_value = 2073

  meta_key = metform_entries__form_data
  meta_value = serialized submitted form data

  meta_key = metform_entries__file_upload_new
  meta_value = serialized upload metadata, if any
```

Form `2073` settings are stored separately:

```text
wp_postmeta
  post_id = 2073
  meta_key = metform_form__form_setting
  meta_value = serialized settings array
```

That settings array is the place to verify `store_entries`, `enable_admin_notification`, `admin_email_to`, `mf_rest_api_url`, Zapier webhook, Slack webhook, Google Sheets, SMS, and similar integrations.

## Legacy Payload Keys To Preserve

Until the settings and any historical entry consumers are audited, treat these keys as a compatibility contract:

| Legacy key | Meaning | Expected values or format |
| --- | --- | --- |
| `mf-checkbox` | Civilite | `Madame` or `Monsieur` |
| `mf-date` | Date de naissance | MetForm currently expects `m-d-Y`, with public JS presenting day/month/year |
| `fumeur` | Fumeur | `Oui` or `Non` |
| `mf-select` | Profession | Existing profession option labels |
| `banque` | Banque | Free text |
| `nom` | Nom | Free text |
| `prenom` | Prenom | Free text |
| `mf-email` | Email | Email address |
| `adresse` | Adresse | Free text |
| `code-postal` | Code postal | Free text |
| `mf-text` | Ville | Free text |
| `mf-gdpr-consent` | Legacy consent flag | Ambiguous, because the old form used this name twice |

## Proposed Additive Keys

Add clearer keys while still submitting every legacy key above:

| New key | Purpose |
| --- | --- |
| `schema_version` | Version the payload contract, for example `adr_quote_v2` |
| `type_devis` | Route the request: pret, habitation, auto, sante, professionnel, loyers, autre |
| `contact_preference` | email, telephone, whatsapp |
| `telephone` | Optional phone number |
| `civilite` | Alias of `mf-checkbox` |
| `date_naissance` | Canonical `YYYY-MM-DD` alias of `mf-date` |
| `profession` | Alias of `mf-select` |
| `email` | Alias of `mf-email` |
| `ville` | Alias of `mf-text` |
| `code_postal` | Alias of `code-postal` |
| `contact_consent` | Distinct contact permission |
| `rgpd_consent` | Distinct privacy/RGPD consent |
| `consent_version` | Text/version accepted at submission time |
| `source_url` | Page URL that produced the request |

## Implementation Recommendation

Because unknown integrations may receive the raw submitted payload before MetForm stores the entry, the safest change is to submit both old keys and new aliases from the form itself.

Recommended pattern:

1. Keep visible MetForm input names on the legacy keys wherever possible.
2. Add MetForm hidden fields for the new additive keys.
3. Use small front-end sync code to mirror values into the alias fields before submit.
4. Add a server-side `metform_filter_before_store_form_data` fallback to fill aliases if JavaScript fails.
5. Keep `store_entries` enabled unless the DB settings prove an external system is authoritative.

This preserves old downstream behavior while making new exports and future automation cleaner.

## Notification Channel

Manuel has approved email transmission of the full user-submitted payload, so the interim notification path can use the existing MetForm admin email behavior, provided the form copy and consent make clear that the user is choosing to share the submitted information with Assurances de Rueil.

In MetForm 4.0.1, `send_admin_email()` appends formatted submitted form data to the email body before adding the edit link. Earlier this was treated as a privacy concern; after Manuel's confirmation, it is acceptable for the current project phase.

Recommended pattern:

1. Store the full submission in WordPress/MetForm entries.
2. Preserve legacy `wp_postmeta` payload keys and add new alias keys.
3. Submit through the existing MetForm endpoint so WordPress storage and the configured MetForm notification run together.
4. Include entry/admin link if MetForm already provides it.
5. Avoid sending extra copies to additional channels unless Manuel explicitly asks for them.

## Next Phase

1. Read only form `2073` settings from `wp_postmeta`.
2. Decode the serialized settings and check for email, REST API, Zapier, Slack, Google Sheets, SMS, and storage flags.
3. Inspect historical entry payload keys only, not values, to confirm whether the stored data shape matches the visible form.
4. Keep the project framed as a look refresh with form UX cleanup, not a new operational system.
5. Implement the approved form in Elementor/MetForm with legacy keys preserved and additive aliases hidden/synced.
6. Configure the admin email target to receive the full submitted payload.
7. Test once with explicit approval using dummy data, then verify:
   - entry exists;
   - all legacy keys are filled;
   - all additive keys are filled;
   - notification contains the full dummy payload and reaches the configured email target.

## Safe DB Queries

Use the real table prefix, not necessarily `wp_`.

```sql
SELECT meta_value
FROM wp_postmeta
WHERE post_id = 2073
  AND meta_key = 'metform_form__form_setting';
```

```sql
SELECT COUNT(*)
FROM wp_postmeta
WHERE meta_key = 'metform_entries__form_id'
  AND meta_value = '2073';
```

To inspect payload shape without exposing values, unserialize one `metform_entries__form_data` value in a trusted admin shell and print only array keys.
