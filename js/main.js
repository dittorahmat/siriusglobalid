document.addEventListener("DOMContentLoaded", () => {
  // Inject site-config placeholders
  try {
    document.querySelectorAll("[data-config]").forEach((el) => {
      const k = el.getAttribute("data-config");
      if (SITE[k] !== undefined) {
        if (el.tagName === "A" && (k === "phoneHref")) el.href = SITE[k];
        else if (el.tagName === "A" && k === "email") el.href = "mailto:" + SITE[k];
        else el.textContent = SITE[k];
      }
    });
    const map = document.querySelector("[data-map]");
    if (map && SITE.mapEmbed) map.src = SITE.mapEmbed;
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
    btn.addEventListener("click", () => {
      const open = menu.classList.toggle("open");
      btn.setAttribute("aria-expanded", open ? "true" : "false");
    });
    menu.querySelectorAll("a").forEach((a) => a.addEventListener("click", () => menu.classList.remove("open")));
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
    pills.forEach((p) => p.addEventListener("click", () => {
      pills.forEach((x) => x.classList.remove("active"));
      p.classList.add("active");
      const f = p.dataset.filter;
      cards.forEach((c) => { c.style.display = (f === "all" || c.dataset.cat === f) ? "" : "none"; });
    }));
  }

  // Contact form (front-end only)
  const form = document.querySelector("[data-contact-form]");
  if (form) {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      let valid = true;
      let firstBad = null;
      form.querySelectorAll("[required]").forEach((inp) => {
        const wrap = inp.closest(".field");
        const bad = !inp.value.trim() || (inp.type === "email" && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(inp.value));
        if (wrap) wrap.classList.toggle("invalid", bad);
        inp.setAttribute("aria-invalid", bad ? "true" : "false");
        if (bad && !firstBad) firstBad = inp;
        if (bad) valid = false;
      });
      if (!valid) { firstBad?.focus(); return; }
      const ok = form.querySelector("[data-form-ok]");
      if (ok) ok.hidden = false;
      form.querySelectorAll("input, textarea, select").forEach((i) => { if (i.type !== "submit") i.value = ""; });
      form.querySelector("input")?.focus();
    });
  }
});
