const translations = {
  fr: {
    previewSub: "3 propositions de refonte",
    option1: "Option 1",
    option1Label: "Classique confiance",
    option2: "Option 2",
    option2Label: "Local éditorial",
    option3: "Option 3",
    option3Label: "Premium digital",
    navServices: "Services",
    navContact: "Contact",
    headline: "Courtier en assurances à Rueil-Malmaison",
    lead: "Assurances de Rueil vous accompagne : prêt immobilier, auto, habitation, santé et pro.",
    quote: "Demander un devis",
    discover: "Nous découvrir",
    phoneBy: "Par téléphone",
    loanTitle: "Assurances de prêts",
    loanText: "Vous êtes sur le point de souscrire un crédit ? Découvrez l'assurance de prêt.",
    personalTitle: "Assurance des particuliers",
    personalText: "Santé, retraite, prévoyance, assurance de prêts et véhicule.",
    proTitle: "Assurances des professionnels",
    proText: "Artisan, commerçant, profession libérale et entreprise industrielle.",
    learnMore: "En savoir plus",
    formTitle: "Par formulaire",
    formLine: "Complétez notre formulaire, nous vous contacterons dans les meilleurs délais.",
    thisWay: "C'est par ici !",
    day: "Jour",
    night: "Nuit",
    lang: "FR",
  },
  en: {
    previewSub: "3 redesign proposals",
    option1: "Option 1",
    option1Label: "Classic trust",
    option2: "Option 2",
    option2Label: "Local editorial",
    option3: "Option 3",
    option3Label: "Premium digital",
    navServices: "Services",
    navContact: "Contact",
    headline: "Insurance broker in Rueil-Malmaison",
    lead: "Assurances de Rueil supports your mortgage, car, home, health and professional insurance needs.",
    quote: "Request a quote",
    discover: "About the agency",
    phoneBy: "By phone",
    loanTitle: "Loan insurance",
    loanText: "About to take out a loan? Discover borrower insurance.",
    personalTitle: "Personal insurance",
    personalText: "Health, retirement, income protection, loan insurance and vehicle cover.",
    proTitle: "Professional insurance",
    proText: "Tradespeople, retailers, liberal professions and industrial companies.",
    learnMore: "Learn more",
    formTitle: "By form",
    formLine: "Complete our form and we will contact you as soon as possible.",
    thisWay: "This way",
    day: "Day",
    night: "Night",
    lang: "EN",
  },
};

const root = document.documentElement;
const lookButtons = Array.from(document.querySelectorAll("[data-look-target]"));
const looks = Array.from(document.querySelectorAll(".site-look"));
const i18nNodes = Array.from(document.querySelectorAll("[data-i18n]"));
const themeToggle = document.querySelector("[data-theme-toggle]");
const langToggle = document.querySelector("[data-lang-toggle]");
const themeLabel = document.querySelector("[data-theme-label]");
const langLabel = document.querySelector("[data-lang-label]");

const state = {
  look: localStorage.getItem("adr-redesign-look") || "classic",
  theme: localStorage.getItem("adr-redesign-theme") || "day",
  lang: localStorage.getItem("adr-redesign-lang") || "fr",
};

function setLook(look) {
  state.look = look;
  root.dataset.look = look;
  localStorage.setItem("adr-redesign-look", look);

  lookButtons.forEach((button) => {
    const active = button.dataset.lookTarget === look;
    button.classList.toggle("is-active", active);
    button.setAttribute("aria-current", active ? "page" : "false");
  });

  looks.forEach((section) => {
    section.classList.toggle("is-active", section.dataset.look === look);
  });
}

function setTheme(theme) {
  state.theme = theme;
  root.dataset.theme = theme;
  localStorage.setItem("adr-redesign-theme", theme);
  const isNight = theme === "night";
  themeToggle.setAttribute("aria-checked", String(isNight));
  themeLabel.textContent = isNight ? translations[state.lang].night : translations[state.lang].day;
}

function setLang(lang) {
  state.lang = lang;
  root.lang = lang;
  localStorage.setItem("adr-redesign-lang", lang);
  i18nNodes.forEach((node) => {
    const value = translations[lang][node.dataset.i18n];
    if (value) node.textContent = value;
  });
  langToggle.setAttribute("aria-checked", String(lang === "en"));
  langLabel.textContent = translations[lang].lang;
  setTheme(state.theme);
}

lookButtons.forEach((button) => {
  button.addEventListener("click", () => setLook(button.dataset.lookTarget));
});

themeToggle.addEventListener("click", () => {
  setTheme(state.theme === "day" ? "night" : "day");
});

langToggle.addEventListener("click", () => {
  setLang(state.lang === "fr" ? "en" : "fr");
});

setLook(state.look);
setLang(state.lang);
setTheme(state.theme);
