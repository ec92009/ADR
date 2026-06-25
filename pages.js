const pageRoot = document.documentElement;
const pageSiteRoot = pageRoot.dataset.siteRoot || "";
const pageVersion = pageRoot.dataset.version || "117.0";
const pageMount = document.querySelector("[data-content-page]");
const quoteSubmitEndpoint = "https://assurancesderueil.fr/wp-json/metform/v1/entries/insert/2073";
const quoteFormNonce = "f95577a433";

const professionOptions = {
  fr: [
    "Cadres",
    "Employés, A. de maîtrise",
    "Ouvriers",
    "Professions médicales",
    "Professions paramédicales",
    "Professions libérales",
    "Commerçants et leurs salariés",
    "Artisans hors BTP et leurs salariés",
    "Artisans du BTP",
    "Professions agricoles et péri-agricoles",
    "Professions du transport",
    "Retraités",
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
      title: "Demande de devis assurance à Rueil-Malmaison",
      lead:
        "Transmettez les informations utiles à votre demande de devis. Le cabinet reçoit les éléments que vous choisissez de partager afin de préparer une réponse adaptée.",
      introTitle: "Votre demande en trois temps",
      introText:
        "Le formulaire commence par les coordonnées indispensables. Les informations complémentaires apparaissent ensuite selon le type de devis souhaité.",
      step1: "Informations obligatoires",
      step2: "Informations utiles",
      step3: "Consentements",
      civility: "Civilité",
      madame: "Madame",
      monsieur: "Monsieur",
      quoteType: "Type de devis désiré",
      optionalNote: "Sélectionnez un type de devis pour afficher les informations complémentaires utiles à la demande.",
      contactPreference: "Contact préféré",
      contactEmail: "E-mail",
      contactPhone: "Téléphone",
      contactWhatsapp: "WhatsApp",
      phone: "Téléphone",
      birthDate: "Date de naissance",
      day: "Jour",
      month: "Mois",
      year: "Année",
      smoker: "Êtes-vous fumeur ?",
      yes: "Oui",
      no: "Non",
      smokerHelp:
        "Est non-fumeur toute personne certifiant qu'elle n'a fumé ni cigarette, ni cigarette électronique, ni pipe, ni cigare, ni consommé de produits contenant de la nicotine au cours des 24 derniers mois, et qu'elle n'a pas arrêté de fumer à la demande expresse du corps médical.",
      profession: "Votre profession",
      select: "Sélectionner",
      bank: "Votre banque",
      lastName: "Nom",
      firstName: "Prénom",
      email: "E-mail",
      address: "Adresse",
      postalCode: "Code postal",
      city: "Ville",
      consentCall:
        "En cliquant sur « Envoyer », j'accepte qu'Assurances de Rueil me contacte par",
      consentRgpd:
        "J'accepte le traitement de mes données personnelles conformément au RGPD. EN SAVOIR PLUS",
      submit: "Envoyer",
      sending: "Envoi en cours...",
      successFallback: "Merci pour votre message.",
      errorFallback: "L'envoi n'a pas abouti. Vous pouvez aussi écrire à contact@assurancesderueil.fr.",
      previewMessage:
        "Votre demande a bien été transmise au cabinet.",
    },
    en: {
      eyebrow: "Quote request",
      title: "Insurance quote request in Rueil-Malmaison",
      lead:
        "Share the information useful for your quote request. The agency receives the details you choose to provide so it can prepare a tailored answer.",
      introTitle: "Your request in three steps",
      introText:
        "The form starts with essential contact details. Additional fields appear after you select the desired quote type.",
      step1: "Required information",
      step2: "Useful information",
      step3: "Consents",
      civility: "Title",
      madame: "Ms",
      monsieur: "Mr",
      quoteType: "Desired quote type",
      optionalNote: "Select a quote type to show the additional information useful for the request.",
      contactPreference: "Preferred contact",
      contactEmail: "Email",
      contactPhone: "Phone",
      contactWhatsapp: "WhatsApp",
      phone: "Phone",
      birthDate: "Date of birth",
      day: "Day",
      month: "Month",
      year: "Year",
      smoker: "Do you smoke?",
      yes: "Yes",
      no: "No",
      smokerHelp:
        "A non-smoker is any person certifying that they have not smoked cigarettes, electronic cigarettes, a pipe or cigars, nor consumed products containing nicotine in the last 24 months, and that they did not stop smoking at the express request of the medical profession.",
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
        "By clicking Submit, I agree that Assurances de Rueil may contact me by",
      consentRgpd: "I agree to the processing of my personal data under GDPR. LEARN MORE",
      submit: "Send",
      sending: "Sending...",
      successFallback: "Thank you for your message.",
      errorFallback: "The request could not be sent. You can also email contact@assurancesderueil.fr.",
      previewMessage:
        "Your request has been sent to the agency.",
    },
  },
  "cabinet-de-courtage-en-assurances-rueil-malmaison": {
    image: "assets/partners.jpg",
    fr: {
      eyebrow: "Cabinet",
      title: "Cabinet de courtage en assurances à Rueil-Malmaison",
      lead:
        "Implanté dans les Hauts-de-Seine depuis quatre générations, le cabinet accompagne particuliers, entreprises, artisans, commerçants, professions libérales et PME.",
      cards: [
        ["Indépendance", "Le statut de courtier permet de sélectionner les contrats en fonction des intérêts de l'assuré."],
        ["Partenaires reconnus", "Le cabinet compare des solutions proposées par des partenaires comme Axa, April ou Generali."],
        ["Suivi complet", "L'équipe accompagne la négociation des garanties, les prix et le bon déroulement des sinistres."],
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
      eyebrow: "Assurance de prêt",
      title: "Assurance de prêt à Rueil-Malmaison",
      lead:
        "L'assurance emprunteur prend le relais en cas de défaillance et peut couvrir PTIA, ITT, IPT, IPP, décès ou perte d'emploi selon les garanties.",
      cards: [
        ["Garanties", "Protection contre la perte totale et irréversible d'autonomie, l'incapacité de travail, l'invalidité, le décès et parfois la perte d'emploi."],
        ["Liberté de choix", "L'emprunteur peut comparer son contrat bancaire avec une assurance externe répondant aux exigences de la fiche standardisée."],
        ["Accompagnement", "Assurances de Rueil adapte les garanties et options au profil de l'emprunteur pour proposer une offre personnalisée."],
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
      title: "Assurance particuliers à Rueil-Malmaison",
      lead:
        "Protection des biens, de la personne et des proches avec des contrats habitation, santé, retraite, prévoyance, véhicule, loyers impayés et emprunteur.",
      cards: [
        ["Habitation", "Garanties incendie, dégâts des eaux, vol, tempête, catastrophes naturelles et adaptation aux particularités du bien."],
        ["Loyers impayés", "Couverture des loyers impayés, frais de contentieux et détériorations immobilières selon le dossier."],
        ["Famille et mobilité", "Solutions de responsabilité civile, assistance famille en France et à l'étranger, santé, retraite, prévoyance et véhicule."],
      ],
      cta: "Protéger mon foyer",
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
      title: "Assurance entreprise à Rueil-Malmaison",
      lead:
        "Contrats multirisques adaptés à l'activité, aux locaux, aux machines, marchandises, stocks, informatique et responsabilités professionnelles.",
      cards: [
        ["Multirisques", "Couverture contre incendie, événements climatiques, dégâts des eaux, vol, vandalisme, bris de glace et bris de machine."],
        ["Profils", "Solutions pour artisans, commerçants, professions libérales et entreprises industrielles."],
        ["Investissements", "Assurance emprunteur pour les prêts professionnels et investissements, selon le profil et le contrat retenu."],
      ],
      cta: "Assurer mon activité",
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
      lead: "75 avenue Victor Hugo, 92500 Rueil-Malmaison. Le cabinet est ouvert du lundi au vendredi, de 9H00 à 12H30 et de 14H00 à 18H30.",
      cards: [
        ["Téléphone", "+33 1 47 51 06 69"],
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
      eyebrow: "Informations légales",
      title: "Mentions légales",
      lead:
        "ASSURANCES DE RUEIL, 75 avenue Victor Hugo, 92500 Rueil-Malmaison. Société de courtage en assurances, SARL au capital de 16 007 euros, RCS Nanterre 689 801 769, ORIAS n° 07 001948.",
      cards: [
        ["Contrôle", "Courtier soumis à l'autorité de contrôle prudentiel et de résolution, 4 Place de Budapest, CS 92459, 75436 Paris Cedex 09."],
        ["Hébergement", "Le site original est hébergé par Infomaniak."],
        ["Données", "Les données personnelles peuvent faire l'objet de demandes d'accès, rectification ou suppression auprès du cabinet."],
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
      eyebrow: "Confidentialité",
      title: "Politique de confidentialité",
      lead:
        "La politique RGPD du site explique comment les informations transmises via formulaires ou cookies sont protégées et utilisées pour la mission de courtier.",
      cards: [
        ["Données collectées", "Nom, prénom, date et lieu de naissance, coordonnées, informations de dossier et données utiles à la demande d'assurance."],
        ["Finalité", "Répondre aux demandes, accompagner la souscription et transmettre les éléments nécessaires aux partenaires concernés avec accord."],
        ["Droits", "Accès, rectification, suppression et opposition peuvent être demandés au cabinet selon les conditions RGPD."],
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
        "Le site original utilise des cookies pour le fonctionnement, les préférences, la navigation, la mesure d'audience, la publicité ciblée et certaines interactions.",
      cards: [
        ["Techniques", "Cookies nécessaires au fonctionnement, à la navigation et aux mesures de sécurité du site."],
        ["Préférences", "Cookies pouvant mémoriser des choix comme la langue ou certaines informations de session."],
        ["Mesure et tiers", "Cookies d'audience, publicité ou réseaux sociaux pouvant être déposés par des services tiers."],
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
        "La page d'origine est le document de déclaration cookies généré par l'outil de consentement du site pour la région UE.",
      cards: [
        ["Nécessaires", "Cookies indispensables au fonctionnement et à la sécurité du site."],
        ["Fonctionnels", "Cookies utiles aux fonctionnalités additionnelles et préférences."],
        ["Performance et analyse", "Cookies pouvant aider à comprendre l'utilisation du site et améliorer l'expérience."],
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
    ["Assurance de prêt", "assurance-de-pret-a-rueil-malmaison"],
    ["Particuliers", "assurance-particuliers-rueil-malmaison"],
    ["Professionnels", "assurance-entreprise-rueil-malmaison"],
    ["Contact", "courtier-en-assurances-de-rueil-malmaison"],
    ["Mentions légales", "mentions-legales"],
    ["Confidentialité", "politique-de-confidentialite"],
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

const cardIconsByRoute = {
  "cabinet-de-courtage-en-assurances-rueil-malmaison": ["professional", "professional", "personal"],
  "assurance-de-pret-a-rueil-malmaison": ["loan", "loan", "personal"],
  "assurance-particuliers-rueil-malmaison": ["loan", "loan", "personal"],
  "assurance-entreprise-rueil-malmaison": ["professional", "professional", "loan"],
  "courtier-en-assurances-de-rueil-malmaison": ["personal", "professional", "loan"],
  "mentions-legales": ["professional", "loan", "personal"],
  "politique-de-confidentialite": ["personal", "professional", "loan"],
  "cookies-traceurs": ["professional", "personal", "loan"],
  "politique-de-cookies-ue": ["professional", "personal", "loan"],
};

const iconAssets = {
  loan: "assets/loan.gif",
  personal: "assets/personal.gif",
  professional: "assets/professional.gif",
};

const pageFaqs = {
  "demande-de-devis-assurance-a-rueil-malmaison": {
    fr: [
      [
        "Comment demander un devis d'assurance à Rueil-Malmaison ?",
        "Vous pouvez transmettre vos coordonnées, votre profil et les informations utiles à votre dossier afin qu'un conseiller Assurances de Rueil vous rappelle avec une proposition adaptée.",
      ],
      [
        "Quelles informations préparer pour une assurance de prêt ?",
        "Il est utile de préparer votre date de naissance, votre profession, votre banque, le montant du prêt et les garanties attendues par l'établissement prêteur.",
      ],
      [
        "Le devis engage-t-il automatiquement le client ?",
        "Non. La demande sert à préparer une simulation et un échange avec le cabinet avant toute souscription.",
      ],
    ],
    en: [
      [
        "How can I request an insurance quote in Rueil-Malmaison?",
        "You can share your contact details, profile and file information so an Assurances de Rueil adviser can call back with a tailored proposal.",
      ],
      [
        "What should I prepare for borrower insurance?",
        "Prepare your date of birth, profession, bank, loan amount and the guarantees required by the lender.",
      ],
      [
        "Does requesting a quote commit the client?",
        "No. The request prepares a simulation and a discussion with the agency before any subscription.",
      ],
    ],
  },
  "cabinet-de-courtage-en-assurances-rueil-malmaison": {
    fr: [
      [
        "Pourquoi passer par un courtier en assurances à Rueil-Malmaison ?",
        "Un courtier compare plusieurs solutions et défend les intérêts de l'assuré, avec un accompagnement local pour choisir les garanties et suivre les sinistres.",
      ],
      [
        "Le cabinet accompagne-t-il particuliers et professionnels ?",
        "Oui. Assurances de Rueil accompagne les particuliers, artisans, commerçants, professions libérales, PME et entreprises industrielles.",
      ],
      [
        "Où se trouve Assurances de Rueil ?",
        "Le cabinet est situé au 75 avenue Victor Hugo, 92500 Rueil-Malmaison.",
      ],
    ],
    en: [
      [
        "Why use an insurance broker in Rueil-Malmaison?",
        "A broker compares several solutions and works in the insured client's interest, with local guidance for guarantees and claims follow-up.",
      ],
      [
        "Does the agency support individuals and professionals?",
        "Yes. Assurances de Rueil supports individuals, tradespeople, retailers, liberal professions, SMEs and industrial companies.",
      ],
      [
        "Where is Assurances de Rueil located?",
        "The agency is located at 75 avenue Victor Hugo, 92500 Rueil-Malmaison.",
      ],
    ],
  },
  "assurance-de-pret-a-rueil-malmaison": {
    fr: [
      [
        "À quoi sert une assurance de prêt immobilier ?",
        "Elle peut prendre le relais du remboursement en cas de décès, perte totale et irréversible d'autonomie, incapacité, invalidité ou perte d'emploi selon le contrat.",
      ],
      [
        "Peut-on choisir une assurance emprunteur différente de celle de la banque ?",
        "Oui. L'emprunteur peut comparer une assurance externe si les garanties répondent aux exigences de la banque.",
      ],
      [
        "Assurances de Rueil peut-il comparer les garanties ?",
        "Oui. Le cabinet aide à adapter les garanties au profil de l'emprunteur et aux demandes de l'établissement prêteur.",
      ],
    ],
    en: [
      [
        "What is borrower insurance used for?",
        "It can take over repayment after death, total loss of autonomy, incapacity, disability or job loss depending on the policy.",
      ],
      [
        "Can borrowers choose insurance outside the bank?",
        "Yes. Borrowers can compare an external insurance policy when its guarantees meet the bank's requirements.",
      ],
      [
        "Can Assurances de Rueil compare guarantees?",
        "Yes. The agency helps adapt guarantees to the borrower's profile and the lender's requirements.",
      ],
    ],
  },
  "assurance-particuliers-rueil-malmaison": {
    fr: [
      [
        "Quelles assurances pour les particuliers sont proposées ?",
        "Le cabinet accompagne les contrats habitation, santé, retraite, prévoyance, véhicule, loyers impayés et assurance emprunteur.",
      ],
      [
        "L'assurance habitation peut-elle être adaptée au logement ?",
        "Oui. Les garanties peuvent tenir compte du bien, de son usage, de sa localisation et du profil du propriétaire ou occupant.",
      ],
      [
        "Le cabinet accompagne-t-il les familles expatriées ou en déplacement ?",
        "Des solutions d'assistance famille en France et à l'étranger peuvent être étudiées selon les besoins.",
      ],
    ],
    en: [
      [
        "Which personal insurance policies are available?",
        "The agency supports home, health, retirement, income protection, vehicle, unpaid rent and borrower insurance policies.",
      ],
      [
        "Can home insurance be adapted to the property?",
        "Yes. Guarantees can account for the property, its use, its location and the owner or occupant profile.",
      ],
      [
        "Can the agency support families travelling or living abroad?",
        "Family assistance solutions in France and abroad can be reviewed according to the need.",
      ],
    ],
  },
  "assurance-entreprise-rueil-malmaison": {
    fr: [
      [
        "Quelles entreprises peuvent être assurées ?",
        "Assurances de Rueil accompagne artisans, commerçants, professions libérales, PME et entreprises industrielles.",
      ],
      [
        "Que couvre une assurance multirisque professionnelle ?",
        "Elle peut couvrir les locaux, machines, marchandises, stocks, matériel informatique, bris, incendie, dégâts des eaux et responsabilités professionnelles.",
      ],
      [
        "Le cabinet peut-il accompagner un prêt professionnel ?",
        "Oui. Une assurance emprunteur peut être étudiée pour des prêts professionnels ou investissements selon le profil et le contrat.",
      ],
    ],
    en: [
      [
        "Which businesses can be insured?",
        "Assurances de Rueil supports tradespeople, retailers, liberal professions, SMEs and industrial companies.",
      ],
      [
        "What can business multi-risk insurance cover?",
        "It can cover premises, machinery, goods, stock, IT equipment, breakage, fire, water damage and professional liabilities.",
      ],
      [
        "Can the agency support a professional loan?",
        "Yes. Borrower insurance can be reviewed for professional loans or investments depending on the profile and contract.",
      ],
    ],
  },
  "courtier-en-assurances-de-rueil-malmaison": {
    fr: [
      [
        "Comment contacter Assurances de Rueil ?",
        "Le cabinet peut être joint au +33 1 47 51 06 69 ou par e-mail à contact@assurancesderueil.fr.",
      ],
      [
        "Quels sont les horaires d'ouverture ?",
        "Le cabinet est ouvert du lundi au vendredi, de 9H00 à 12H30 et de 14H00 à 18H30.",
      ],
      [
        "Quelle est l'adresse du cabinet ?",
        "Assurances de Rueil se trouve au 75 avenue Victor Hugo, 92500 Rueil-Malmaison.",
      ],
    ],
    en: [
      [
        "How can I contact Assurances de Rueil?",
        "The agency can be reached at +33 1 47 51 06 69 or by email at contact@assurancesderueil.fr.",
      ],
      [
        "What are the opening hours?",
        "The agency is open Monday to Friday, 9:00-12:30 and 14:00-18:30.",
      ],
      [
        "What is the agency address?",
        "Assurances de Rueil is located at 75 avenue Victor Hugo, 92500 Rueil-Malmaison.",
      ],
    ],
  },
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

function cardIconPath(slug, index) {
  const fallback = ["loan", "personal", "professional"];
  const iconKey = cardIconsByRoute[slug]?.[index] || fallback[index % fallback.length];
  return pageAsset(iconAssets[iconKey] || iconAssets.loan);
}

function renderCards(slug, copy) {
  return copy.cards
    .map(
      ([title, text], index) => `
        <article class="content-card content-icon-panel glass-panel">
          <img src="${cardIconPath(slug, index)}" alt="" />
          <div>
            <h2>${escapeHtml(title)}</h2>
            <p>${escapeHtml(text)}</p>
          </div>
        </article>
      `
    )
    .join("");
}

function renderFaq(slug) {
  const items = pageFaqs[slug]?.[pageLang()] || [];
  if (!items.length) return "";

  const title = pageLang() === "fr" ? "Questions fréquentes" : "Frequently asked questions";
  return `
    <section class="content-faq glass-panel" aria-label="${escapeHtml(title)}">
      <h2>${escapeHtml(title)}</h2>
      <div class="content-faq-list">
        ${items
          .map(
            ([question, answer]) => `
              <details>
                <summary>${escapeHtml(question)}</summary>
                <p>${escapeHtml(answer)}</p>
              </details>
            `
          )
          .join("")}
      </div>
    </section>
  `;
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

function renderStandardPage(slug, data, copy) {
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
      ${renderCards(slug, copy)}
    </section>
    ${renderFaq(slug)}
    ${renderFooter()}
  `;
}

function renderQuoteInput(label, name, type = "text", attrs = "") {
  return `
    <label class="quote-field">
      <span>${escapeHtml(label)}</span>
      <input type="${type}" name="${escapeHtml(name)}" ${attrs} />
    </label>
  `;
}

function renderRequiredQuoteInput(label, name, type = "text", attrs = "") {
  return renderQuoteInput(`${label} *`, name, type, `${attrs} required`);
}

function renderHiddenQuoteFields() {
  return `
    <input type="hidden" name="form_nonce" value="${quoteFormNonce}" />
    <input type="hidden" name="schema_version" value="adr_quote_v2" />
    <input type="hidden" name="source_url" value="" />
    <input type="hidden" name="consent_version" value="adr_quote_consent_2026-06-24" />
    <input type="hidden" name="civilite" value="" />
    <input type="hidden" name="email" value="" />
    <input type="hidden" name="profession" value="" />
    <input type="hidden" name="ville" value="" />
    <input type="hidden" name="code_postal" value="" />
    <input type="hidden" name="date_naissance" value="" />
    <input type="hidden" name="mf-date" value="" />
    <input type="hidden" name="mf-gdpr-consent" value="" />
  `;
}

function quoteTypeOptions() {
  const labels =
    pageLang() === "fr"
      ? [
          ["pret", "Assurance de prêt"],
          ["habitation", "Assurance habitation"],
          ["auto", "Assurance automobile"],
          ["sante", "Santé / prévoyance"],
          ["professionnel", "Assurance professionnelle"],
          ["loyers", "Loyers impayés"],
          ["autre", "Autre demande"],
        ]
      : [
          ["pret", "Loan insurance"],
          ["habitation", "Home insurance"],
          ["auto", "Car insurance"],
          ["sante", "Health / income protection"],
          ["professionnel", "Business insurance"],
          ["loyers", "Unpaid rent"],
          ["autre", "Other request"],
        ];

  return labels.map(([value, label]) => `<option value="${value}">${escapeHtml(label)}</option>`).join("");
}

function monthOptions(copy) {
  const months =
    pageLang() === "fr"
      ? [
          ["01", "Janvier"],
          ["02", "Février"],
          ["03", "Mars"],
          ["04", "Avril"],
          ["05", "Mai"],
          ["06", "Juin"],
          ["07", "Juillet"],
          ["08", "Août"],
          ["09", "Septembre"],
          ["10", "Octobre"],
          ["11", "Novembre"],
          ["12", "Décembre"],
        ]
      : [
          ["01", "January"],
          ["02", "February"],
          ["03", "March"],
          ["04", "April"],
          ["05", "May"],
          ["06", "June"],
          ["07", "July"],
          ["08", "August"],
          ["09", "September"],
          ["10", "October"],
          ["11", "November"],
          ["12", "December"],
        ];

  return `<option value="">${escapeHtml(copy.month)}</option>${months
    .map(([value, label]) => `<option value="${value}">${escapeHtml(label)}</option>`)
    .join("")}`;
}

function yearOptions(copy) {
  const currentYear = new Date().getFullYear();
  const years = [`<option value="">${escapeHtml(copy.year)}</option>`];
  for (let year = currentYear - 16; year >= currentYear - 100; year -= 1) {
    years.push(`<option value="${year}">${year}</option>`);
  }
  return years.join("");
}

function renderBirthDateSelects(copy) {
  const days = [`<option value="">${escapeHtml(copy.day)}</option>`];
  for (let day = 1; day <= 31; day += 1) {
    days.push(`<option value="${day}">${day}</option>`);
  }

  return `
    <div class="quote-field quote-date-field">
      <span>${escapeHtml(copy.birthDate)}</span>
      <div class="quote-date-selects">
        <select name="jour_naissance" aria-label="${escapeHtml(copy.day)}">
          ${days.join("")}
        </select>
        <select name="mois_naissance" aria-label="${escapeHtml(copy.month)}">
          ${monthOptions(copy)}
        </select>
        <select name="annee_naissance" aria-label="${escapeHtml(copy.year)}">
          ${yearOptions(copy)}
        </select>
      </div>
    </div>
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

      <form class="quote-form glass-panel" data-preview-form data-submit-endpoint="${quoteSubmitEndpoint}">
        ${renderHiddenQuoteFields()}
        <fieldset>
          <legend>${escapeHtml(copy.step1)}</legend>
          <div class="quote-field">
            <span>${escapeHtml(copy.civility)} *</span>
            <div class="choice-row">
              <label><input type="radio" name="mf-checkbox" value="Madame" required /> ${escapeHtml(copy.madame)}</label>
              <label><input type="radio" name="mf-checkbox" value="Monsieur" /> ${escapeHtml(copy.monsieur)}</label>
            </div>
          </div>
          <div class="quote-two">
            ${renderRequiredQuoteInput(copy.lastName, "nom", "text", 'autocomplete="family-name"')}
            ${renderRequiredQuoteInput(copy.firstName, "prenom", "text", 'autocomplete="given-name"')}
          </div>
          ${renderRequiredQuoteInput(copy.email, "mf-email", "email", 'autocomplete="email"')}
          <label class="quote-field">
            <span>${escapeHtml(copy.quoteType)}</span>
            <select name="type_devis" data-quote-type>
              <option value="">${escapeHtml(copy.select)}</option>
              ${quoteTypeOptions()}
            </select>
          </label>
        </fieldset>

        <fieldset class="quote-extra" data-quote-extra hidden>
          <legend>${escapeHtml(copy.step2)}</legend>
          <p class="quote-note">${escapeHtml(copy.optionalNote)}</p>
          <div class="quote-contact-row">
            <div class="quote-field">
              <span>${escapeHtml(copy.contactPreference)}</span>
              <div class="choice-row">
                <label><input type="radio" name="contact_preference" value="email" checked /> ${escapeHtml(copy.contactEmail)}</label>
                <label><input type="radio" name="contact_preference" value="telephone" /> ${escapeHtml(copy.contactPhone)}</label>
                <label><input type="radio" name="contact_preference" value="whatsapp" /> ${escapeHtml(copy.contactWhatsapp)}</label>
              </div>
            </div>
            ${renderQuoteInput(copy.phone, "telephone", "tel", 'autocomplete="tel" placeholder="+33"')}
            <div class="quote-field">
              <span>${escapeHtml(copy.smoker)}</span>
              <div class="choice-row">
                <label><input type="radio" name="fumeur" value="Oui" /> ${escapeHtml(copy.yes)}</label>
                <label><input type="radio" name="fumeur" value="Non" /> ${escapeHtml(copy.no)}</label>
              </div>
              <small>${escapeHtml(copy.smokerHelp)}</small>
            </div>
          </div>
          <div class="quote-two">
            ${renderQuoteInput(copy.bank, "banque", "text", 'autocomplete="organization"')}
            <label class="quote-field">
              <span>${escapeHtml(copy.profession)}</span>
              <select name="mf-select">
                <option value="">${escapeHtml(copy.select)}</option>
                ${options}
              </select>
            </label>
          </div>
          ${renderBirthDateSelects(copy)}
          ${renderQuoteInput(copy.address, "adresse", "text", 'autocomplete="street-address"')}
          <div class="quote-two">
            ${renderQuoteInput(copy.postalCode, "code-postal", "text", 'autocomplete="postal-code"')}
            ${renderQuoteInput(copy.city, "mf-text", "text", 'autocomplete="address-level2"')}
          </div>
        </fieldset>

        <fieldset>
          <legend>${escapeHtml(copy.step3)}</legend>
          <label class="consent-row">
            <input type="checkbox" name="contact_consent" value="Oui" data-contact-consent required />
            <span>${escapeHtml(copy.consentCall)} <strong data-contact-channel>${escapeHtml(copy.contactEmail)}</strong>. *</span>
          </label>
          <label class="consent-row">
            <input type="checkbox" name="rgpd_consent" value="Oui" data-rgpd-consent required />
            <span>${escapeHtml(copy.consentRgpd)} *</span>
          </label>
        </fieldset>

        <button class="button button-primary" type="submit" data-submit disabled>${escapeHtml(copy.submit)}</button>
        <p class="form-status" hidden data-form-status>${escapeHtml(copy.previewMessage)}</p>
      </form>
    </section>
    ${renderFaq("demande-de-devis-assurance-a-rueil-malmaison")}
    ${renderFooter()}
  `;
}

function attachPreviewForm(copy) {
  const form = pageMount.querySelector("[data-preview-form]");
  if (!form) return;
  const quoteType = form.querySelector("[data-quote-type]");
  const quoteExtra = form.querySelector("[data-quote-extra]");
  const contactPreferences = Array.from(form.querySelectorAll('input[name="contact_preference"]'));
  const contactChannel = form.querySelector("[data-contact-channel]");
  const contactConsent = form.querySelector("[data-contact-consent]");
  const rgpdConsent = form.querySelector("[data-rgpd-consent]");
  const submitButton = form.querySelector("[data-submit]");
  const submitLabel = submitButton?.textContent || copy.submit;

  function formValue(name) {
    const field = form.elements.namedItem(name);
    if (!field) {
      return "";
    }
    if (typeof RadioNodeList !== "undefined" && field instanceof RadioNodeList) {
      return field.value || "";
    }
    return field.value || "";
  }

  function setFormValue(name, value) {
    const field = form.elements.namedItem(name);
    if (!field || (typeof RadioNodeList !== "undefined" && field instanceof RadioNodeList)) {
      return;
    }
    field.value = value;
  }

  function birthdateParts() {
    const day = formValue("jour_naissance").padStart(2, "0");
    const month = formValue("mois_naissance");
    const year = formValue("annee_naissance");
    if (!day || !month || !year || day === "00") {
      return { legacy: "", canonical: "" };
    }
    return {
      legacy: `${month}-${day}-${year}`,
      canonical: `${year}-${month}-${day}`,
    };
  }

  function contactLabel(value) {
    const labels = {
      email: copy.contactEmail,
      telephone: copy.contactPhone,
      whatsapp: copy.contactWhatsapp,
    };
    return labels[value] || labels.email;
  }

  function syncPayloadFields() {
    const birthdate = birthdateParts();
    setFormValue("source_url", window.location.href);
    setFormValue("civilite", formValue("mf-checkbox"));
    setFormValue("email", formValue("mf-email"));
    setFormValue("profession", formValue("mf-select"));
    setFormValue("ville", formValue("mf-text"));
    setFormValue("code_postal", formValue("code-postal"));
    setFormValue("mf-date", birthdate.legacy);
    setFormValue("date_naissance", birthdate.canonical);
    setFormValue("mf-gdpr-consent", rgpdConsent?.checked ? "Oui" : "");
  }

  function updateContactConsentText() {
    const selected = contactPreferences.find((input) => input.checked)?.value || "email";
    if (contactChannel) {
      contactChannel.textContent = contactLabel(selected);
    }
    syncPayloadFields();
  }

  function updateSubmitState() {
    if (submitButton) {
      submitButton.disabled = !(contactConsent?.checked && rgpdConsent?.checked);
    }
    syncPayloadFields();
  }

  quoteType?.addEventListener("change", () => {
    if (quoteExtra) {
      quoteExtra.hidden = quoteType.value === "";
    }
    syncPayloadFields();
  });
  contactPreferences.forEach((input) => input.addEventListener("change", updateContactConsentText));
  contactConsent?.addEventListener("change", updateSubmitState);
  rgpdConsent?.addEventListener("change", updateSubmitState);
  form.addEventListener("input", syncPayloadFields);
  form.addEventListener("change", syncPayloadFields);
  form.addEventListener("submit", async (event) => {
    event.preventDefault();
    syncPayloadFields();
    if (!form.reportValidity()) {
      return;
    }
    const status = form.querySelector("[data-form-status]");
    if (submitButton) {
      submitButton.disabled = true;
      submitButton.textContent = copy.sending;
    }
    if (status) {
      status.hidden = false;
      status.textContent = copy.sending;
    }
    try {
      const response = await fetch(form.dataset.submitEndpoint, {
        method: "POST",
        body: new FormData(form),
      });
      const result = await response.json();
      if (!response.ok || !result.status) {
        const errors = Array.isArray(result.error) ? result.error.join(" ") : "";
        throw new Error(errors || copy.errorFallback);
      }
      if (status) {
        status.textContent = result.data?.message || copy.successFallback;
      }
      form.reset();
      if (quoteExtra) {
        quoteExtra.hidden = true;
      }
      updateContactConsentText();
    } catch (error) {
      if (status) {
        status.textContent = error.message || copy.errorFallback;
      }
    } finally {
      if (submitButton) {
        submitButton.textContent = submitLabel;
      }
      updateSubmitState();
    }
  });
  updateContactConsentText();
  updateSubmitState();
  syncPayloadFields();
}

function renderPage() {
  if (!pageMount) return;
  const slug = pageMount.dataset.contentPage;
  const data = pages[slug];
  if (!data) return;

  const copy = data[pageLang()] || data.fr;
  pageMount.innerHTML = data.type === "quote" ? renderQuotePage(data, copy) : renderStandardPage(slug, data, copy);
  attachPreviewForm(copy);
}

renderPage();
window.addEventListener("adr:languagechange", renderPage);
