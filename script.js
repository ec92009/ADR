const root = document.documentElement;
const version = root.dataset.version || "112.6";
const siteRoot = root.dataset.siteRoot || "";
const quotePath = "demande-de-devis-assurance-a-rueil-malmaison/";
const quoteHref = `${siteRoot}${quotePath}`;

const translations = {
  fr: {
    headline: "Courtier en assurances à Rueil-Malmaison",
    lead: "Assurances de Rueil vous accompagne : prêt immobilier, auto, habitation, santé et pro.",
    quote: "Demander un devis",
    discover: "Nous découvrir",
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
    pageCabinet: "Cabinet",
    pageLoan: "Assurance de prêt",
    pagePersonal: "Particuliers",
    pagePro: "Professionnels",
    pageContact: "Contact",
    pageLegal: "Infos légales",
    cabinetPageTitle: "Cabinet de courtage en assurances à Rueil-Malmaison",
    cabinetPageText:
      "Implanté dans les Hauts-de-Seine depuis quatre générations, le cabinet accompagne particuliers, entreprises, artisans, commerçants, professions libérales et PME avec une équipe à taille humaine.",
    cabinetPoint1: "Courtier indépendant au service des intérêts de l'assuré.",
    cabinetPoint2: "Sélection de contrats auprès de partenaires reconnus comme Axa, April ou Generali.",
    cabinetPoint3: "Suivi complet, y compris lors de la gestion d'un sinistre.",
    loanPageTitle: "Assurance de prêt à Rueil-Malmaison",
    loanPageText:
      "L'assurance de prêt prend le relais de l'emprunteur en cas de défaillance et peut couvrir PTIA, ITT, IPT, IPP, décès ou perte d'emploi selon les garanties.",
    loanTag: "Décès",
    personalPageTitle: "Assurance particuliers à Rueil-Malmaison",
    personalPageText:
      "Protection des biens, de la personne et des proches avec des contrats habitation, santé, retraite, prévoyance, véhicule, loyers impayés et assurance emprunteur.",
    personalPoint1: "Garanties incendie, dégâts des eaux, vol, tempête et catastrophes naturelles.",
    personalPoint2: "Solutions pour propriétaires occupants, non occupants et résidences secondaires.",
    personalPoint3: "Assistance famille en France et à l'étranger.",
    proPageTitle: "Assurance entreprise à Rueil-Malmaison",
    proPageText:
      "Contrats multirisques adaptés à l'activité, aux locaux, aux machines, marchandises, stocks, matériel informatique et responsabilités professionnelles.",
    proTag1: "Artisan",
    proTag2: "Commerçant",
    proTag3: "Profession libérale",
    proTag4: "Entreprise industrielle",
    contactPageTitle: "Nous contacter",
    contactPageText:
      "Les Assurances de Rueil, 75 avenue Victor Hugo, 92500 Rueil-Malmaison. Ouvert du lundi au vendredi, de 9H00 à 12H30 et de 14H00 à 18H30.",
    legalPageTitle: "Mentions, confidentialité et cookies",
    legalPageText:
      "Le site original publie ses mentions légales, sa politique de confidentialité RGPD, sa politique de cookies et ses informations de traceurs.",
    legalMentions: "Mentions légales",
    legalPrivacy: "Politique de confidentialité",
    legalCookies: "Cookies & traceurs",
    legalCookieEu: "Politique de cookies (UE)",
    settingsTitle: "Réglages",
    settingsVersion: `Version v${version}`,
    settingsLanguage: "Langue",
    settingsTheme: "Thème",
    settingsTransparency: "Transparence",
    settingsTranslucency: "Translucidité",
    day: "Jour",
    night: "Nuit",
  },
  en: {
    headline: "Insurance broker in Rueil-Malmaison",
    lead: "Assurances de Rueil supports your mortgage, car, home, health and professional insurance needs.",
    quote: "Request a quote",
    discover: "About the agency",
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
    pageCabinet: "Agency",
    pageLoan: "Loan insurance",
    pagePersonal: "Individuals",
    pagePro: "Professionals",
    pageContact: "Contact",
    pageLegal: "Legal info",
    cabinetPageTitle: "Insurance brokerage agency in Rueil-Malmaison",
    cabinetPageText:
      "Established in Hauts-de-Seine for four generations, the agency supports individuals, companies, tradespeople, retailers, liberal professions and SMEs with a close-knit experienced team.",
    cabinetPoint1: "Independent broker focused on the insured client's interests.",
    cabinetPoint2: "Contract selection from recognized partners such as Axa, April and Generali.",
    cabinetPoint3: "Complete follow-up, including claim-management support.",
    loanPageTitle: "Loan insurance in Rueil-Malmaison",
    loanPageText:
      "Borrower insurance can take over if the borrower cannot pay and may cover PTIA, ITT, IPT, IPP, death or job loss depending on guarantees.",
    loanTag: "Death",
    personalPageTitle: "Personal insurance in Rueil-Malmaison",
    personalPageText:
      "Protection for property, people and loved ones through home, health, retirement, income protection, vehicle, unpaid rent and borrower insurance contracts.",
    personalPoint1: "Fire, water damage, theft, storm and natural disaster guarantees.",
    personalPoint2: "Solutions for owner-occupiers, non-occupying owners and second homes.",
    personalPoint3: "Family assistance in France and abroad.",
    proPageTitle: "Business insurance in Rueil-Malmaison",
    proPageText:
      "Multi-risk contracts adapted to the activity, premises, machinery, goods, stock, IT equipment and professional responsibilities.",
    proTag1: "Tradesperson",
    proTag2: "Retailer",
    proTag3: "Liberal profession",
    proTag4: "Industrial company",
    contactPageTitle: "Contact us",
    contactPageText:
      "Les Assurances de Rueil, 75 avenue Victor Hugo, 92500 Rueil-Malmaison. Open Monday to Friday, 9:00-12:30 and 14:00-18:30.",
    legalPageTitle: "Legal, privacy and cookies",
    legalPageText:
      "The original site publishes legal notices, its GDPR privacy policy, cookie policy and tracking information.",
    legalMentions: "Legal notice",
    legalPrivacy: "Privacy policy",
    legalCookies: "Cookies & tracking",
    legalCookieEu: "Cookie policy (EU)",
    settingsTitle: "Settings",
    settingsVersion: `Version v${version}`,
    settingsLanguage: "Language",
    settingsTheme: "Theme",
    settingsTransparency: "Transparency",
    settingsTranslucency: "Translucency",
    day: "Day",
    night: "Night",
  },
};

function settingsIcon() {
  return `
    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
      <path d="M12 15.2a3.2 3.2 0 1 0 0-6.4 3.2 3.2 0 0 0 0 6.4Z" />
      <path d="M19.4 15a1.8 1.8 0 0 0 .4 2l.1.1a2.1 2.1 0 0 1-3 3l-.1-.1a1.8 1.8 0 0 0-2-.4 1.8 1.8 0 0 0-1.1 1.7V21a2.1 2.1 0 0 1-4.2 0v-.2a1.8 1.8 0 0 0-1.1-1.7 1.8 1.8 0 0 0-2 .4l-.1.1a2.1 2.1 0 1 1-3-3l.1-.1a1.8 1.8 0 0 0 .4-2 1.8 1.8 0 0 0-1.7-1.1H2a2.1 2.1 0 0 1 0-4.2h.2a1.8 1.8 0 0 0 1.7-1.1 1.8 1.8 0 0 0-.4-2l-.1-.1a2.1 2.1 0 1 1 3-3l.1.1a1.8 1.8 0 0 0 2 .4 1.8 1.8 0 0 0 1.1-1.7V2a2.1 2.1 0 0 1 4.2 0v.2a1.8 1.8 0 0 0 1.1 1.7 1.8 1.8 0 0 0 2-.4l.1-.1a2.1 2.1 0 1 1 3 3l-.1.1a1.8 1.8 0 0 0-.4 2 1.8 1.8 0 0 0 1.7 1.1h.2a2.1 2.1 0 0 1 0 4.2h-.2a1.8 1.8 0 0 0-1.8 1.2Z" />
    </svg>
  `;
}

function ensureSharedChrome() {
  if (!document.querySelector(".candidate-floating-nav")) {
    document.body.insertAdjacentHTML(
      "afterbegin",
      `
        <header class="candidate-floating-nav glass-panel" aria-label="Navigation principale">
          <a class="site-brand candidate-brand" href="${siteRoot || "./"}">
            <img src="${siteRoot}assets/logo.png" alt="" width="58" height="58" />
            <span>Assurances de Rueil</span>
          </a>
          <div class="nav-actions candidate-actions">
            <a class="button button-primary" href="${quoteHref}" data-i18n="quote">Demander un devis</a>
            <button class="settings-button" type="button" aria-label="Ouvrir les réglages" aria-expanded="false" data-settings-open>
              ${settingsIcon()}
            </button>
          </div>
        </header>
      `
    );
  }

  if (!document.querySelector("[data-settings-popover]")) {
    document.body.insertAdjacentHTML(
      "beforeend",
      `
        <div class="settings-popover" role="dialog" aria-modal="true" aria-labelledby="settings-title" hidden data-settings-popover>
          <div class="settings-panel glass-panel">
            <button class="settings-close" type="button" aria-label="Fermer les réglages" data-settings-close>x</button>
            <h2 id="settings-title" data-i18n="settingsTitle">Réglages</h2>
            <p data-i18n="settingsVersion" data-version-text>Version v${version}</p>
            <div class="settings-group">
              <span data-i18n="settingsLanguage">Langue</span>
              <div class="settings-segment">
                <button type="button" data-lang-choice="fr">FR</button>
                <button type="button" data-lang-choice="en">EN</button>
              </div>
            </div>
            <div class="settings-group">
              <span data-i18n="settingsTheme">Thème</span>
              <div class="settings-segment">
                <button type="button" data-theme-choice="day" data-i18n="day">Jour</button>
                <button type="button" data-theme-choice="night" data-i18n="night">Nuit</button>
              </div>
            </div>
            <label class="settings-range">
              <span data-i18n="settingsTransparency">Transparence</span>
              <input type="range" min="58" max="96" value="82" data-glass-alpha />
            </label>
            <label class="settings-range">
              <span data-i18n="settingsTranslucency">Translucidité</span>
              <input type="range" min="8" max="28" value="18" data-glass-blur />
            </label>
          </div>
        </div>
      `
    );
  }

  if (!document.querySelector("[data-sticky-cta]")) {
    document.body.insertAdjacentHTML(
      "beforeend",
      `<a class="synthesis-sticky-cta" href="${quoteHref}" data-sticky-cta data-i18n="quote">Demander un devis</a>`
    );
  }
}

ensureSharedChrome();

const i18nNodes = Array.from(document.querySelectorAll("[data-i18n]"));
const settingsOpen = document.querySelector("[data-settings-open]");
const settingsClose = document.querySelector("[data-settings-close]");
const settingsPopover = document.querySelector("[data-settings-popover]");
const themeChoices = Array.from(document.querySelectorAll("[data-theme-choice]"));
const langChoices = Array.from(document.querySelectorAll("[data-lang-choice]"));
const glassAlpha = document.querySelector("[data-glass-alpha]");
const glassBlur = document.querySelector("[data-glass-blur]");
const stickyCta = document.querySelector("[data-sticky-cta]");

const state = {
  theme: localStorage.getItem("adr-main-theme") || "night",
  lang: localStorage.getItem("adr-main-lang") || "fr",
  glassAlpha: localStorage.getItem("adr-main-glass-alpha") || "82",
  glassBlur: localStorage.getItem("adr-main-glass-blur") || "18",
};

function setTheme(theme) {
  state.theme = theme;
  root.dataset.theme = theme;
  localStorage.setItem("adr-main-theme", theme);

  themeChoices.forEach((button) => {
    button.classList.toggle("is-active", button.dataset.themeChoice === theme);
  });
}

function setLang(lang) {
  state.lang = lang;
  root.lang = lang;
  localStorage.setItem("adr-main-lang", lang);

  i18nNodes.forEach((node) => {
    const value = translations[lang][node.dataset.i18n];
    if (value) node.textContent = value;
  });

  langChoices.forEach((button) => {
    button.classList.toggle("is-active", button.dataset.langChoice === lang);
  });

  window.dispatchEvent(new CustomEvent("adr:languagechange", { detail: { lang } }));
}

function setGlass(alpha, blur) {
  state.glassAlpha = String(alpha);
  state.glassBlur = String(blur);
  localStorage.setItem("adr-main-glass-alpha", state.glassAlpha);
  localStorage.setItem("adr-main-glass-blur", state.glassBlur);
  root.style.setProperty("--glass-alpha", Number(alpha) / 100);
  root.style.setProperty("--glass-blur", `${blur}px`);
  if (glassAlpha) glassAlpha.value = state.glassAlpha;
  if (glassBlur) glassBlur.value = state.glassBlur;
}

function openSettings() {
  if (!settingsPopover || !settingsOpen) return;
  settingsPopover.hidden = false;
  settingsOpen.setAttribute("aria-expanded", "true");
  settingsClose?.focus();
}

function closeSettings({ restoreFocus = true } = {}) {
  if (!settingsPopover || !settingsOpen) return;
  settingsPopover.hidden = true;
  settingsOpen.setAttribute("aria-expanded", "false");
  if (restoreFocus) settingsOpen.focus();
}

function updateStickyCta() {
  if (!stickyCta) return;
  stickyCta.classList.toggle("is-visible", window.scrollY > 520);
}

themeChoices.forEach((button) => {
  button.addEventListener("click", () => setTheme(button.dataset.themeChoice));
});

langChoices.forEach((button) => {
  button.addEventListener("click", () => setLang(button.dataset.langChoice));
});

glassAlpha?.addEventListener("input", () => setGlass(glassAlpha.value, state.glassBlur));
glassBlur?.addEventListener("input", () => setGlass(state.glassAlpha, glassBlur.value));
settingsOpen?.addEventListener("click", openSettings);
settingsClose?.addEventListener("click", () => closeSettings());
settingsPopover?.addEventListener("click", (event) => {
  if (event.target === settingsPopover) closeSettings({ restoreFocus: false });
});
window.addEventListener("keydown", (event) => {
  if (event.key === "Escape" && settingsPopover && !settingsPopover.hidden) closeSettings();
});
window.addEventListener("scroll", updateStickyCta, { passive: true });

setLang(state.lang);
setTheme(state.theme);
setGlass(state.glassAlpha, state.glassBlur);
updateStickyCta();
