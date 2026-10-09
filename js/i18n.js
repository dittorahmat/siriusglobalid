const I18N = window.SGI_I18N || {id:{},en:{}};
const LANG_KEY = "sgi-lang";
function currentLang() { return localStorage.getItem(LANG_KEY) || "id"; }
function applyLang(lang) {
  const dict = I18N[lang] || I18N.id;
  document.documentElement.lang = lang === "id" ? "id" : "en";
  document.querySelectorAll("[data-i18n]").forEach((el) => {
    const k = el.getAttribute("data-i18n");
    if (dict[k] !== undefined) el.textContent = dict[k];
  });
  document.querySelectorAll("[data-i18n-html]").forEach((el) => {
    const k = el.getAttribute("data-i18n-html");
    if (dict[k] !== undefined) el.innerHTML = dict[k];
  });
  document.querySelectorAll("[data-i18n-ph]").forEach((el) => {
    const k = el.getAttribute("data-i18n-ph");
    if (dict[k] !== undefined) el.setAttribute("placeholder", dict[k]);
  });
  const t = document.querySelector("[data-i18n-title]");
  if (t) { const k = t.getAttribute("data-i18n-title"); if (dict[k]) document.title = dict[k]; }
  document.querySelectorAll(".lang-toggle button").forEach((b) => {
    b.setAttribute("aria-pressed", b.dataset.lang === lang ? "true" : "false");
  });
  localStorage.setItem(LANG_KEY, lang);
}
document.addEventListener("DOMContentLoaded", () => {
  applyLang(currentLang());
  document.querySelectorAll(".lang-toggle button").forEach((b) => {
    b.addEventListener("click", () => applyLang(b.dataset.lang));
  });
});
