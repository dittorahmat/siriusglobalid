// main.js theme - port js/main.js, ID-only.
// Bedanya dari versi PHP/JSON: tidak ada i18n runtime. SITE diisi dari
// wp_localize_script (SGI_SETTINGS); fallback bila script dimuat statis.
var SITE = window.SGI_SETTINGS || {
  phoneHref: "https://wa.me/6281510481010"
};

document.addEventListener("DOMContentLoaded", () => {
  // Tahun berjalan
  try {
    const y = document.querySelector("[data-year]");
    if (y) y.textContent = new Date().getFullYear();
  } catch (e) { /* noop */ }

  // Sticky shadow
  const header = document.querySelector(".site-header");
  const onScroll = () => header && header.classList.toggle("scrolled", window.scrollY > 8);
  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  // Mobile menu
  const btn = document.querySelector("[data-menu-btn]");
  const menu = document.querySelector("[data-mobile-menu]");
  if (btn && menu) {
    const close = () => { menu.classList.remove("open"); btn.setAttribute("aria-expanded", "false"); };
    btn.addEventListener("click", () => {
      const open = menu.classList.toggle("open");
      btn.setAttribute("aria-expanded", open ? "true" : "false");
    });
    document.addEventListener("keydown", (e) => { if (e.key === "Escape" && menu.classList.contains("open")) { close(); btn.focus(); } });
    menu.querySelectorAll("a").forEach((a) => a.addEventListener("click", close));
  }

  // Reveal on scroll
  const io = new IntersectionObserver((entries) => {
    entries.forEach((en) => { if (en.isIntersecting) { en.target.classList.add("in"); io.unobserve(en.target); } });
  }, { threshold: 0.12 });
  document.querySelectorAll(".reveal").forEach((el) => io.observe(el));

  // Counters
  const cio = new IntersectionObserver((entries) => {
    entries.forEach((en) => {
      if (!en.isIntersecting) return;
      const el = en.target; cio.unobserve(el);
      const target = parseFloat(el.dataset.count || "0");
      const suffix = el.dataset.suffix || "";
      const dur = 1200; const t0 = performance.now();
      const tick = (t) => {
        const p = Math.min(1, (t - t0) / dur);
        const eased = 1 - Math.pow(1 - p, 3);
        const val = target * eased;
        el.textContent = (target % 1 !== 0 ? val.toFixed(1) : Math.round(val)) + suffix;
        if (p < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    });
  }, { threshold: 0.4 });
  document.querySelectorAll("[data-count]").forEach((el) => cio.observe(el));

  // FAQ
  document.querySelectorAll(".faq button").forEach((b) => {
    b.addEventListener("click", () => {
      const item = b.closest(".faq");
      const wasOpen = item.classList.contains("open");
      document.querySelectorAll(".faq.open").forEach((f) => f.classList.remove("open"));
      if (!wasOpen) item.classList.add("open");
      b.setAttribute("aria-expanded", wasOpen ? "false" : "true");
    });
  });

  // Portfolio filter
  const pills = document.querySelectorAll("[data-filter]");
  const cards = document.querySelectorAll("[data-cat]");
  if (pills.length && cards.length) {
    pills.forEach((p) => {
      if (!p.hasAttribute("aria-pressed")) p.setAttribute("aria-pressed", p.classList.contains("active") ? "true" : "false");
    });
    pills.forEach((p) => p.addEventListener("click", () => {
      pills.forEach((x) => { x.classList.remove("active"); x.setAttribute("aria-pressed", "false"); });
      p.classList.add("active");
      p.setAttribute("aria-pressed", "true");
      const f = p.dataset.filter;
      cards.forEach((c) => { c.style.display = (f === "all" || c.dataset.cat === f) ? "" : "none"; });
    }));
  }

  // Preselect service from ?layanan= (deep links from service pages)
  try {
    const params = new URLSearchParams(window.location.search);
    const svc = params.get("layanan");
    const need = document.getElementById("f-need");
    if (svc && need && Array.from(need.options).some((o) => o.value === svc)) need.value = svc;
  } catch (e) { /* noop */ }

  // Contact form -> structured WhatsApp deep link (no backend needed)
  const form = document.querySelector("[data-contact-form]");
  if (form) {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      let valid = true;
      let firstBad = null;
      form.querySelectorAll("[required]").forEach((inp) => {
        const wrap = inp.closest(".field");
        let bad;
        if (inp.type === "checkbox") bad = !inp.checked;
        else if (inp.type === "email") bad = !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(inp.value.trim());
        else bad = !inp.value.trim();
        if (wrap) wrap.classList.toggle("invalid", bad);
        inp.setAttribute("aria-invalid", bad ? "true" : "false");
        if (bad && !firstBad) firstBad = inp;
        if (bad) valid = false;
      });
      if (!valid) { firstBad?.focus(); return; }
      const get = (id) => ((document.getElementById(id) || {}).value || "").trim();
      const textOf = (id) => {
        const sel = document.getElementById(id);
        return sel ? sel.options[sel.selectedIndex].text : "-";
      };
      const lines = [
        "Halo Sirius Global Indonesia,",
        "",
        "Nama: " + get("f-name"),
        "Perusahaan: " + (get("f-company") || "-"),
        "Email: " + get("f-email"),
        "WhatsApp: " + (get("f-wa") || "-"),
        "Layanan: " + textOf("f-need"),
        "Budget: " + textOf("f-budget"),
        "Target: " + textOf("f-time"),
        "",
        "Kebutuhan:",
        get("f-msg"),
      ];
      const base = (typeof SITE !== "undefined" && SITE.phoneHref) || "https://wa.me/6281510481010";
      const url = base + "?text=" + encodeURIComponent(lines.join("\n"));
      const link = form.querySelector("[data-form-wa]");
      if (link) link.href = url;
      const ok = form.querySelector("[data-form-ok]");
      if (ok) ok.hidden = false;
      window.open(url, "_blank", "noopener");
      if (link) link.focus({ preventScroll: false });
    });
  }
});
