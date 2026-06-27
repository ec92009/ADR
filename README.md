# Assurances de Rueil redesign source of truth

Static GitHub Pages build for the current Assurances de Rueil redesign source of truth.

- Current visible version: `v119.5`
- Canonical source of truth: `https://ec92009.github.io/ADR/`
- Downstream target: WordPress/live-site work should follow this GitHub Pages build unless explicitly superseded
- Main preview: refreshed static mock promoted to the GitHub Pages root
- Parked chooser: `chooser.html`
- Converted routes: quote request, cabinet, loan insurance, personal insurance, business insurance, contact, legal, privacy, cookies, and EU cookie policy
- Settings: day/night preview toggle that persists page to page; French-only copy for this review build
- Images: regenerated local higher-resolution JPEG photography for the page heroes and partner/comparison image
- Quote form: the main quote route now uses the simplified gated form with legacy MetForm keys plus cleaner alias fields, and submits to the existing WordPress MetForm endpoint for form `2073`
- Quote form mock: `form-mock/` remains a standalone noindex reference for the simplified secure-form flow
- SEO/AIO prep: production-style titles, descriptions, canonical URLs, Open Graph/Twitter tags, schema.org JSON-LD, pre-rendered route body content, `robots.txt`, refreshed `sitemap.xml`, and `llms.txt`

Live site after Pages deploys:

https://ec92009.github.io/ADR/

Shareable quote-form route:

https://ec92009.github.io/ADR/demande-de-devis.html

Standalone quote-form mock:

https://ec92009.github.io/ADR/form-mock/

Local mock preview:

```sh
python3 -m http.server 8124
```

Then open `http://localhost:8124/form-mock/`.
