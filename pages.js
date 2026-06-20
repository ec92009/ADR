const pageRoot = document.documentElement;
const pageSiteRoot = pageRoot.dataset.siteRoot || "";
const pageVersion = pageRoot.dataset.version || "112.3";
const pageMount = document.querySelector("[data-content-page]");

const professionOptions = {
  fr: [
    "Cadres",
    "Employes, A. de maitrise",
    "Ouvriers",
    "Professions medicales",
    "Professions paramedicales",
    "Professions liberales",
    "Commercants et leurs salaries",
    "Artisans hors BTP et leurs salaries",
    "Artisans du BTP",
    "Professions agricoles et peri-agricoles",
    "Professions du transport",
    "Retraites",
    "Sans profession",
  ],
  en: [
    "Executives",
    "Employees, supervisors",
    "Workers",
    "Medical professions",
    "Paramedical professions",
    "Liberal professions",
    "Retailers and employees",
    "Tradespeople outside construction",
    "Construction tradespeople",
    "Agricultural professions",
    "Transport professions",
    "Retired",
    "No profession",
  ],
};

const pages = {
  "demande-de-devis-assurance-a-rueil-malmaison": {
    image: "assets/devis-simulation.jpg",
    type: "quote",
    fr: {
      eyebrow: "Demande de devis",
      title: "Demande de devis assurance a Rueil-Malmaison",
      lead:
        "Obtenez une simulation personnalisee pour votre assurance de pret. Le formulaire reprend les informations demandees sur le site original afin de preparer une reponse rapide du cabinet.",
      introTitle: "Votre demande",
      introText:
        "Les champs portent sur votre profil emprunteur, votre banque et vos coordonnees. Les informations sont utiles au courtier pour comparer les garanties et revenir vers vous avec une proposition adaptee.",
      step1: "Profil emprunteur",
      step2: "Coordonnees",
      step3: "Consentement RGPD",
      civility: "Civilite",
      madame: "Madame",
      monsieur: "Monsieur",
      birthDate: "Votre date de naissance",
      smoker: "Etes-vous fumeur ?",
      yes: "Oui",
      no: "Non",
      smokerHelp:
        "Est non-fumeur toute personne n'ayant pas fume ni consomme de produit contenant de la nicotine au cours des 24 derniers mois.",
      profession: "Votre profession",
      select: "Selectionner",
      bank: "Votre banque",
      lastName: "Nom",
      firstName: "Prenom",
      email: "E-mail",
      address: "Adresse",
      postalCode: "Code postal",
      city: "Ville",
      consentCall:
        "En cliquant sur Envoyer, j'accepte qu'un conseiller Assurances de Rueil m'appelle pour m'accompagner dans le choix de mon assurance.",
      consentRgpd:
        "J'accepte le traitement de mes donnees personnelles conformement au RGPD.",
      submit: "Envoyer",
      previewMessage:
        "Preview uniquement : la version finale devra reconnecter ce formulaire au systeme de reception du cabinet.",
    },
    en: {
      eyebrow: "Quote request",
      title: "Insurance quote request in Rueil-Malmaison",
      lead:
        "Get a personalized simulation for borrower insurance. This preview keeps the original form's requested information so the agency can prepare a fast answer.",
      introTitle: "Your request",
      introText:
        "The fields cover your borrower profile, bank and contact details. They help the broker compare guarantees and come back with a tailored proposal.",
      step1: "Borrower profile",
      step2: "Contact details",
      step3: "GDPR consent",
      civility: "Title",
      madame: "Ms",
      monsieur: "Mr",
      birthDate: "Date of birth",
      smoker: "Do you smoke?",
      yes: "Yes",
      no: "No",
      smokerHelp:
        "A non-smoker is someone who has not smoked or used nicotine products during the previous 24 months.",
      profession: "Profession",
      select: "Select",
      bank: "Your bank",
      lastName: "Last name",
      firstName: "First name",
      email: "Email",
      address: "Address",
      postalCode: "Postcode",
      city: "City",
      consentCall:
        "By clicking Send, I agree that an Assurances de Rueil adviser may call me to help me choose my insurance.",
      consentRgpd: "I accept the processing of my personal data in accordance with GDPR.",
      submit: "Send",
      previewMessage:
        "Preview only: the final version will need to reconnect this form to the agency's intake system.",
    },
  },
  "cabinet-de-courtage-en-assurances-rueil-malmaison": {
    image: "assets/partners.jpg",
    fr: {
      eyebrow: "Cabinet",
      title: "Cabinet de courtage en assurances a Rueil-Malmaison",
      lead:
        "Implante dans les Hauts-de-Seine depuis quatre generations, le cabinet accompagne particuliers, entreprises, artisans, commercants, professions liberales et PME.",
      cards: [
        ["Independance", "Le statut de courtier permet de selectionner les contrats en fonction des interets de l'assure."],
        ["Partenaires reconnus", "Le cabinet compare des solutions proposees par des partenaires comme Axa, April ou Generali."],
        ["Suivi complet", "L'equipe accompagne la negociation des garanties, les prix et le bon deroulement des sinistres."],
      ],
      cta: "Demander un devis",
    },
    en: {
      eyebrow: "Agency",
      title: "Insurance brokerage agency in Rueil-Malmaison",
      lead:
        "Established in Hauts-de-Seine for four generations, the agency supports individuals, companies, tradespeople, retailers, liberal professions and SMEs.",
      cards: [
        ["Independent advice", "As a broker, the agency selects contracts according to the insured client's interests."],
        ["Recognized partners", "The team compares solutions from partners such as Axa, April and Generali."],
        ["Full follow-up", "Support covers guarantee negotiation, pricing and claims-management follow-up."],
      ],
      cta: "Request a quote",
    },
  },
  "assurance-de-pret-a-rueil-malmaison": {
    image: "assets/hero-insurance.jpg",
    fr: {
      eyebrow: "Assurance de pret",
      title: "Assurance de pret a Rueil-Malmaison",
      lead:
        "L'assurance emprunteur prend le relais en cas de defaillance et peut couvrir PTIA, ITT, IPT, IPP, deces ou perte d'emploi selon les garanties.",
      cards: [
        ["Garanties", "Protection contre la perte totale et irreversible d'autonomie, l'incapacite de travail, l'invalidite, le deces et parfois la perte d'emploi."],
        ["Liberte de choix", "L'emprunteur peut comparer son contrat bancaire avec une assurance externe repondant aux exigences de la fiche standardisee."],
        ["Accompagnement", "Assurances de Rueil adapte les garanties et options au profil de l'emprunteur pour proposer une offre personnalisee."],
      ],
      cta: "Simuler mon assurance",
    },
    en: {
      eyebrow: "Loan insurance",
      title: "Loan insurance in Rueil-Malmaison",
      lead:
        "Borrower insurance can take over repayments when the borrower cannot pay and may cover disability, incapacity, death or job loss depending on guarantees.",
      cards: [
        ["Guarantees", "Protection can include total loss of autonomy, temporary incapacity, disability, death and sometimes job loss."],
        ["Choice", "Borrowers can compare their bank's policy with an external insurance policy matching required guarantees."],
        ["Guidance", "Assurances de Rueil adapts guarantees and options to the borrower profile for a personalized proposal."],
      ],
      cta: "Run my simulation",
    },
  },
  "assurance-particuliers-rueil-malmaison": {
    image: "assets/family-city.jpg",
    fr: {
      eyebrow: "Particuliers",
      title: "Assurance particuliers a Rueil-Malmaison",
      lead:
        "Protection des biens, de la personne et des proches avec des contrats habitation, sante, retraite, prevoyance, vehicule, loyers impayes et emprunteur.",
      cards: [
        ["Habitation", "Garanties incendie, degats des eaux, vol, tempete, catastrophes naturelles et adaptation aux particularites du bien."],
        ["Loyers impayes", "Couverture des loyers impayes, frais de contentieux et deteriorations immobilieres selon le dossier."],
        ["Famille et mobilite", "Solutions de responsabilite civile, assistance famille en France et a l'etranger, sante, retraite, prevoyance et vehicule."],
      ],
      cta: "Proteger mon foyer",
    },
    en: {
      eyebrow: "Individuals",
      title: "Personal insurance in Rueil-Malmaison",
      lead:
        "Protection for property, people and loved ones through home, health, retirement, income protection, vehicle, unpaid rent and borrower insurance.",
      cards: [
        ["Home", "Fire, water damage, theft, storm and natural disaster cover, adapted to each property."],
        ["Unpaid rent", "Cover for unpaid rent, legal costs and property damage depending on the file."],
        ["Family and mobility", "Civil liability, family assistance in France and abroad, health, retirement, income protection and vehicle cover."],
      ],
      cta: "Protect my household",
    },
  },
  "assurance-entreprise-rueil-malmaison": {
    image: "assets/business.jpg",
    fr: {
      eyebrow: "Professionnels",
      title: "Assurance entreprise a Rueil-Malmaison",
      lead:
        "Contrats multirisques adaptes a l'activite, aux locaux, aux machines, marchandises, stocks, informatique et responsabilites professionnelles.",
      cards: [
        ["Multirisques", "Couverture contre incendie, evenements climatiques, degats des eaux, vol, vandalisme, bris de glace et bris de machine."],
        ["Profils", "Solutions pour artisans, commercants, professions liberales et entreprises industrielles."],
        ["Investissements", "Assurance emprunteur pour les prets professionnels et investissements, selon le profil et le contrat retenu."],
      ],
      cta: "Assurer mon activite",
    },
    en: {
      eyebrow: "Professionals",
      title: "Business insurance in Rueil-Malmaison",
      lead:
        "Multi-risk policies adapted to the activity, premises, machinery, goods, stock, IT equipment and professional responsibilities.",
      cards: [
        ["Multi-risk", "Cover against fire, weather events, water damage, theft, vandalism, glass breakage and machine breakage."],
        ["Profiles", "Solutions for tradespeople, retailers, liberal professions and industrial companies."],
        ["Investments", "Borrower insurance for professional loans and investments, depending on profile and policy."],
      ],
      cta: "Insure my business",
    },
  },
  "courtier-en-assurances-de-rueil-malmaison": {
    image: "assets/partners.jpg",
    fr: {
      eyebrow: "Contact",
      title: "Les Assurances de Rueil",
      lead: "75 avenue Victor Hugo, 92500 Rueil-Malmaison. Le cabinet est ouvert du lundi au vendredi, de 9H00 a 12H30 et de 14H00 a 18H30.",
      cards: [
        ["Telephone", "+33 1 47 51 06 69"],
        ["Fax", "+33 1 47 51 00 78"],
        ["Adresse", "75 avenue Victor Hugo, 92500 Rueil-Malmaison"],
      ],
      cta: "Demander un devis",
    },
    en: {
      eyebrow: "Contact",
      title: "Les Assurances de Rueil",
      lead: "75 avenue Victor Hugo, 92500 Rueil-Malmaison. The agency is open Monday to Friday, 9:00-12:30 and 14:00-18:30.",
      cards: [
        ["Phone", "+33 1 47 51 06 69"],
        ["Fax", "+33 1 47 51 00 78"],
        ["Address", "75 avenue Victor Hugo, 92500 Rueil-Malmaison"],
      ],
      cta: "Request a quote",
    },
  },
  "mentions-legales": {
    image: "assets/partners.jpg",
    fr: {
      eyebrow: "Informations legales",
      title: "Mentions legales",
      lead:
        "ASSURANCES DE RUEIL, 75 avenue Victor Hugo, 92500 Rueil-Malmaison. Societe de courtage en assurances, SARL au capital de 16 007 euros, RCS Nanterre 689 801 769, ORIAS n° 07 001948.",
      cards: [
        ["Controle", "Courtier soumis a l'autorite de controle prudentiel et de resolution, 4 Place de Budapest, CS 92459, 75436 Paris Cedex 09."],
        ["Hebergement", "Le site original est heberge par Infomaniak."],
        ["Donnees", "Les donnees personnelles peuvent faire l'objet de demandes d'acces, rectification ou suppression aupres du cabinet."],
      ],
      cta: "Contacter le cabinet",
    },
    en: {
      eyebrow: "Legal information",
      title: "Legal notice",
      lead:
        "ASSURANCES DE RUEIL, 75 avenue Victor Hugo, 92500 Rueil-Malmaison. Insurance brokerage company, SARL with capital of EUR 16,007, RCS Nanterre 689 801 769, ORIAS no. 07 001948.",
      cards: [
        ["Supervision", "Broker subject to ACPR supervision, 4 Place de Budapest, CS 92459, 75436 Paris Cedex 09."],
        ["Hosting", "The original site is hosted by Infomaniak."],
        ["Data", "Personal-data access, correction or deletion requests can be sent to the agency."],
      ],
      cta: "Contact the agency",
    },
  },
  "politique-de-confidentialite": {
    image: "assets/hero-insurance.jpg",
    fr: {
      eyebrow: "Confidentialite",
      title: "Politique de confidentialite",
      lead:
        "La politique RGPD du site explique comment les informations transmises via formulaires ou cookies sont protegees et utilisees pour la mission de courtier.",
      cards: [
        ["Donnees collectees", "Nom, prenom, date et lieu de naissance, coordonnees, informations de dossier et donnees utiles a la demande d'assurance."],
        ["Finalite", "Repondre aux demandes, accompagner la souscription et transmettre les elements necessaires aux partenaires concernes avec accord."],
        ["Droits", "Acces, rectification, suppression et opposition peuvent etre demandes au cabinet selon les conditions RGPD."],
      ],
      cta: "Demander un devis",
    },
    en: {
      eyebrow: "Privacy",
      title: "Privacy policy",
      lead:
        "The site's GDPR policy explains how information sent through forms or cookies is protected and used for the broker's mission.",
      cards: [
        ["Collected data", "Name, date and place of birth, contact details, file information and insurance-request details."],
        ["Purpose", "Answer requests, support subscriptions and share necessary file elements with relevant partners when agreed."],
        ["Rights", "Access, correction, deletion and objection requests can be sent to the agency under GDPR conditions."],
      ],
      cta: "Request a quote",
    },
  },
  "cookies-traceurs": {
    image: "assets/family-city.jpg",
    fr: {
      eyebrow: "Cookies",
      title: "Cookies & traceurs",
      lead:
        "Le site original utilise des cookies pour le fonctionnement, les preferences, la navigation, la mesure d'audience, la publicite ciblee et certaines interactions.",
      cards: [
        ["Techniques", "Cookies necessaires au fonctionnement, a la navigation et aux mesures de securite du site."],
        ["Preferences", "Cookies pouvant memoriser des choix comme la langue ou certaines informations de session."],
        ["Mesure et tiers", "Cookies d'audience, publicite ou reseaux sociaux pouvant etre deposes par des services tiers."],
      ],
      cta: "Retour au devis",
    },
    en: {
      eyebrow: "Cookies",
      title: "Cookies & tracking",
      lead:
        "The original site uses cookies for operation, preferences, navigation, audience measurement, targeted advertising and some interactions.",
      cards: [
        ["Technical", "Cookies required for site operation, navigation and security measures."],
        ["Preferences", "Cookies may remember choices such as language or session information."],
        ["Measurement and third parties", "Audience, advertising or social-media cookies may be set by third-party services."],
      ],
      cta: "Back to quote",
    },
  },
  "politique-de-cookies-ue": {
    image: "assets/family-city.jpg",
    fr: {
      eyebrow: "Cookies UE",
      title: "Politique de cookies (UE)",
      lead:
        "La page d'origine est le document de declaration cookies genere par l'outil de consentement du site pour la region UE.",
      cards: [
        ["Necessaires", "Cookies indispensables au fonctionnement et a la securite du site."],
        ["Fonctionnels", "Cookies utiles aux fonctionnalites additionnelles et preferences."],
        ["Performance et analyse", "Cookies pouvant aider a comprendre l'utilisation du site et ameliorer l'experience."],
      ],
      cta: "Demander un devis",
    },
    en: {
      eyebrow: "EU cookies",
      title: "Cookie policy (EU)",
      lead:
        "The source page is the cookie declaration generated by the site's consent tool for the EU region.",
      cards: [
        ["Necessary", "Cookies required for site operation and security."],
        ["Functional", "Cookies supporting additional features and preferences."],
        ["Performance and analytics", "Cookies that may help understand site usage and improve the experience."],
      ],
      cta: "Request a quote",
    },
  },
};

const footerLinks = {
  fr: [
    ["Cabinet", "cabinet-de-courtage-en-assurances-rueil-malmaison"],
    ["Assurance de pret", "assurance-de-pret-a-rueil-malmaison"],
    ["Particuliers", "assurance-particuliers-rueil-malmaison"],
    ["Professionnels", "assurance-entreprise-rueil-malmaison"],
    ["Contact", "courtier-en-assurances-de-rueil-malmaison"],
    ["Mentions legales", "mentions-legales"],
    ["Confidentialite", "politique-de-confidentialite"],
    ["Cookies", "cookies-traceurs"],
  ],
  en: [
    ["Agency", "cabinet-de-courtage-en-assurances-rueil-malmaison"],
    ["Loan insurance", "assurance-de-pret-a-rueil-malmaison"],
    ["Individuals", "assurance-particuliers-rueil-malmaison"],
    ["Professionals", "assurance-entreprise-rueil-malmaison"],
    ["Contact", "courtier-en-assurances-de-rueil-malmaison"],
    ["Legal notice", "mentions-legales"],
    ["Privacy", "politique-de-confidentialite"],
    ["Cookies", "cookies-traceurs"],
  ],
};

function pageLang() {
  return pageRoot.lang === "en" ? "en" : "fr";
}

function pageAsset(path) {
  return `${pageSiteRoot}${path}`;
}

function pageRoute(slug) {
  return `${pageSiteRoot}${slug}/`;
}

function escapeHtml(value) {
  return String(value)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;");
}

function renderCards(copy) {
  return copy.cards
    .map(
      ([title, text], index) => `
        <article class="content-card glass-panel">
          <span class="section-number">${String(index + 1).padStart(2, "0")}</span>
          <h2>${escapeHtml(title)}</h2>
          <p>${escapeHtml(text)}</p>
        </article>
      `
    )
    .join("");
}

function renderFooter() {
  return `
    <footer class="site-footer glass-panel">
      ${footerLinks[pageLang()]
        .map(([label, slug]) => `<a href="${pageRoute(slug)}">${escapeHtml(label)}</a>`)
        .join("")}
    </footer>
  `;
}

function renderStandardPage(data, copy) {
  return `
    <section class="content-hero glass-panel">
      <div>
        <p class="section-number">${escapeHtml(copy.eyebrow)}</p>
        <h1>${escapeHtml(copy.title)}</h1>
        <p>${escapeHtml(copy.lead)}</p>
        <div class="hero-actions">
          <a class="button button-primary" href="${pageRoute("demande-de-devis-assurance-a-rueil-malmaison")}">${escapeHtml(copy.cta)}</a>
          <a class="button button-light" href="${pageSiteRoot || "./"}">Assurances de Rueil</a>
        </div>
      </div>
      <img src="${pageAsset(data.image)}" alt="" />
    </section>
    <section class="content-card-grid" aria-label="${escapeHtml(copy.title)}">
      ${renderCards(copy)}
    </section>
    ${renderFooter()}
  `;
}

function renderInput(label, name, type = "text") {
  return `
    <label class="quote-field">
      <span>${escapeHtml(label)}</span>
      <input type="${type}" name="${escapeHtml(name)}" required />
    </label>
  `;
}

function renderQuotePage(data, copy) {
  const options = professionOptions[pageLang()]
    .map((option) => `<option>${escapeHtml(option)}</option>`)
    .join("");

  return `
    <section class="content-hero quote-hero glass-panel">
      <div>
        <p class="section-number">${escapeHtml(copy.eyebrow)}</p>
        <h1>${escapeHtml(copy.title)}</h1>
        <p>${escapeHtml(copy.lead)}</p>
      </div>
      <img src="${pageAsset(data.image)}" alt="" />
    </section>

    <section class="quote-layout">
      <aside class="quote-intro glass-panel">
        <h2>${escapeHtml(copy.introTitle)}</h2>
        <p>${escapeHtml(copy.introText)}</p>
        <ol>
          <li>${escapeHtml(copy.step1)}</li>
          <li>${escapeHtml(copy.step2)}</li>
          <li>${escapeHtml(copy.step3)}</li>
        </ol>
      </aside>

      <form class="quote-form glass-panel" data-preview-form>
        <fieldset>
          <legend>${escapeHtml(copy.step1)}</legend>
          <div class="quote-field">
            <span>${escapeHtml(copy.civility)}</span>
            <div class="choice-row">
              <label><input type="radio" name="civilite" value="madame" required /> ${escapeHtml(copy.madame)}</label>
              <label><input type="radio" name="civilite" value="monsieur" /> ${escapeHtml(copy.monsieur)}</label>
            </div>
          </div>
          ${renderInput(copy.birthDate, "date-naissance", "date")}
          <div class="quote-field">
            <span>${escapeHtml(copy.smoker)}</span>
            <div class="choice-row">
              <label><input type="radio" name="fumeur" value="oui" required /> ${escapeHtml(copy.yes)}</label>
              <label><input type="radio" name="fumeur" value="non" /> ${escapeHtml(copy.no)}</label>
            </div>
            <small>${escapeHtml(copy.smokerHelp)}</small>
          </div>
          <label class="quote-field">
            <span>${escapeHtml(copy.profession)}</span>
            <select name="profession" required>
              <option value="">${escapeHtml(copy.select)}</option>
              ${options}
            </select>
          </label>
          ${renderInput(copy.bank, "banque")}
        </fieldset>

        <fieldset>
          <legend>${escapeHtml(copy.step2)}</legend>
          <div class="quote-two">
            ${renderInput(copy.lastName, "nom")}
            ${renderInput(copy.firstName, "prenom")}
          </div>
          ${renderInput(copy.email, "email", "email")}
          ${renderInput(copy.address, "adresse")}
          <div class="quote-two">
            ${renderInput(copy.postalCode, "code-postal")}
            ${renderInput(copy.city, "ville")}
          </div>
        </fieldset>

        <fieldset>
          <legend>${escapeHtml(copy.step3)}</legend>
          <label class="consent-row">
            <input type="checkbox" required />
            <span>${escapeHtml(copy.consentCall)}</span>
          </label>
          <label class="consent-row">
            <input type="checkbox" required />
            <span>${escapeHtml(copy.consentRgpd)}</span>
          </label>
        </fieldset>

        <button class="button button-primary" type="submit">${escapeHtml(copy.submit)}</button>
        <p class="form-status" hidden data-form-status>${escapeHtml(copy.previewMessage)}</p>
      </form>
    </section>
    ${renderFooter()}
  `;
}

function attachPreviewForm(copy) {
  const form = pageMount.querySelector("[data-preview-form]");
  if (!form) return;
  form.addEventListener("submit", (event) => {
    event.preventDefault();
    const status = form.querySelector("[data-form-status]");
    if (status) {
      status.hidden = false;
      status.textContent = copy.previewMessage;
    }
  });
}

function renderPage() {
  if (!pageMount) return;
  const slug = pageMount.dataset.contentPage;
  const data = pages[slug];
  if (!data) return;

  const copy = data[pageLang()] || data.fr;
  document.title = `${copy.title} - Assurances de Rueil v${pageVersion}`;
  pageMount.innerHTML = data.type === "quote" ? renderQuotePage(data, copy) : renderStandardPage(data, copy);
  attachPreviewForm(copy);
}

renderPage();
window.addEventListener("adr:languagechange", renderPage);
