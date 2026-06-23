# Assurances de Rueil redesign preview

Static GitHub Pages preview for the current Assurances de Rueil redesign candidate.

- Current visible version: `v114.0`
- Main preview: synthesis candidate promoted from option 4
- Parked chooser: `chooser.html`
- Converted routes: quote request, cabinet, loan insurance, personal insurance, business insurance, contact, legal, privacy, cookies, and EU cookie policy
- Settings: day/night, FR/EN, transparency, and translucency
- Quote form: static preview of the original MetForm fields; production launch needs live form submission wiring
- Quote form mock: `form-mock/` contains the proposed simplified secure-form flow for review before changing the live WordPress/MetForm form
- SEO/AIO prep: production-style titles, descriptions, canonical URLs, Open Graph/Twitter tags, schema.org JSON-LD, visible FAQ blocks, `robots.txt`, `sitemap.xml`, and `llms.txt`

Live site after Pages deploys:

https://ec92009.github.io/ADR/

Shareable quote-form mock:

https://ec92009.github.io/ADR/form-mock/

Local mock preview:

```sh
python3 -m http.server 8124
```

Then open `http://localhost:8124/form-mock/`.
