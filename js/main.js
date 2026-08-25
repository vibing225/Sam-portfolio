/* ===========================================================
   SamInnova — Main interactivity
   Nav toggle, scroll-reveal (IntersectionObserver), marquee
   duplication for seamless infinite loop, filter tabs.
   Respects prefers-reduced-motion throughout.
   =========================================================== */

document.addEventListener("DOMContentLoaded", () => {
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ---------- Mobile nav ---------- */
  const navToggle = document.querySelector(".nav-toggle");
  const navLinks = document.querySelector(".nav-links");
  if (navToggle && navLinks) {
    navToggle.addEventListener("click", () => {
      const isOpen = navToggle.getAttribute("aria-expanded") === "true";
      navToggle.setAttribute("aria-expanded", String(!isOpen));
      navLinks.classList.toggle("is-open", !isOpen);
    });
    navLinks.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => {
        navToggle.setAttribute("aria-expanded", "false");
        navLinks.classList.remove("is-open");
      });
    });
  }

  /* ---------- Scroll reveal ---------- */
  const revealEls = document.querySelectorAll(".reveal");
  if (reduceMotion || !("IntersectionObserver" in window)) {
    revealEls.forEach((el) => el.classList.add("is-visible"));
  } else {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const delay = entry.target.getAttribute("data-reveal-delay");
            if (delay) entry.target.style.transitionDelay = `${delay}ms`;
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15, rootMargin: "0px 0px -60px 0px" }
    );
    revealEls.forEach((el) => observer.observe(el));
  }

  /* ---------- Marquee: duplicate track content for seamless loop ---------- */
  document.querySelectorAll(".marquee-track").forEach((track) => {
    if (track.dataset.cloned) return;
    track.innerHTML += track.innerHTML;
    track.dataset.cloned = "true";
  });

  /* ---------- Projects filter tabs (visual only at skeleton stage) ---------- */
  const filterTabs = document.querySelectorAll(".filter-tab");
  filterTabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      filterTabs.forEach((t) => t.setAttribute("aria-pressed", "false"));
      tab.setAttribute("aria-pressed", "true");
      // Real filtering wired once the Projects grid is populated from MySQL.
    });
  });

  /* ---------- Contact form: lightweight client-side validation (visual only) ---------- */
  const contactForm = document.querySelector("#contact-form");
  if (contactForm) {
    contactForm.addEventListener("submit", (e) => {
      e.preventDefault();
      let valid = true;
      contactForm.querySelectorAll("[required]").forEach((field) => {
        const errorEl = field.closest(".field")?.querySelector(".field-error");
        if (!field.value.trim()) {
          valid = false;
          if (errorEl) errorEl.style.display = "block";
        } else if (errorEl) {
          errorEl.style.display = "none";
        }
      });
      if (valid) {
        contactForm.querySelector(".form-status")?.removeAttribute("hidden");
        contactForm.reset();
      }
    });
  }

  /* ---------- Footer year ---------- */
  const yearEl = document.querySelector("#current-year");
  if (yearEl) yearEl.textContent = new Date().getFullYear();
});
