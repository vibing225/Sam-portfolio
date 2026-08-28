const SAMITheme = (() => {
  const STORAGE_KEY = "portfolio-theme";

  function safeGetItem(key) {
    try {
      return window.localStorage.getItem(key);
    } catch (error) {
      return null;
    }
  }

  function safeSetItem(key, value) {
    try {
      window.localStorage.setItem(key, value);
    } catch (error) {
      // Graceful fallback: keep the UI working even without storage.
    }
  }

  function getTheme() {
    const saved = safeGetItem(STORAGE_KEY);
    if (saved === "light" || saved === "dark") {
      return saved;
    }

    if (window.matchMedia && window.matchMedia("(prefers-color-scheme: light)").matches) {
      return "light";
    }

    return "dark";
  }

  function apply(theme) {
    const nextTheme = theme === "light" ? "light" : "dark";
    document.documentElement.setAttribute("data-theme", nextTheme);

    document.querySelectorAll(".theme-toggle").forEach((button) => {
      const isLight = nextTheme === "light";
      button.setAttribute("aria-pressed", String(isLight));
      button.setAttribute("aria-label", isLight ? "Switch to dark mode" : "Switch to light mode");
      button.textContent = isLight ? "☀" : "☾";
    });
  }

  function setTheme(theme) {
    const nextTheme = theme === "light" ? "light" : "dark";
    safeSetItem(STORAGE_KEY, nextTheme);
    apply(nextTheme);
  }

  function bindThemeToggle() {
    document.querySelectorAll(".theme-toggle").forEach((button) => {
      button.onclick = () => {
        const currentTheme = document.documentElement.getAttribute("data-theme") === "light" ? "light" : "dark";
        setTheme(currentTheme === "light" ? "dark" : "light");
      };
    });
  }

  function init() {
    apply(getTheme());
    bindThemeToggle();
  }

  return { init, apply, setTheme, getTheme, bindThemeToggle };
})();

window.SAMITheme = SAMITheme;
document.addEventListener("DOMContentLoaded", SAMITheme.init);
document.addEventListener("components:loaded", SAMITheme.bindThemeToggle);
