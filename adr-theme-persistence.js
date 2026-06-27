(function () {
  var storageKey = "adr-source-theme";

  function readPreference() {
    try {
      return window.localStorage.getItem(storageKey);
    } catch (error) {
      return null;
    }
  }

  function writePreference(value) {
    try {
      window.localStorage.setItem(storageKey, value);
    } catch (error) {
      // Storage can be unavailable in private or restricted browsing modes.
    }
  }

  function syncToggle() {
    var toggle = document.getElementById("adr-theme-toggle");
    if (!toggle) {
      return;
    }

    var preference = readPreference();
    if (preference === "day" || preference === "night") {
      toggle.checked = preference === "night";
    }

    toggle.setAttribute("aria-checked", toggle.checked ? "true" : "false");

    if (toggle.dataset.adrThemePersistenceReady === "true") {
      return;
    }

    toggle.dataset.adrThemePersistenceReady = "true";
    toggle.addEventListener("change", function () {
      writePreference(toggle.checked ? "night" : "day");
      toggle.setAttribute("aria-checked", toggle.checked ? "true" : "false");
    });
  }

  syncToggle();

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", syncToggle, { once: true });
  }

  window.addEventListener("pageshow", syncToggle);
})();
