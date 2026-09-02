/* ===========================================================
   Alpha Moussa Sow — Main interactivity
   Nav toggle, scroll-reveal (IntersectionObserver), marquee
   duplication for seamless infinite loop, filter tabs.
   Respects prefers-reduced-motion throughout.
   =========================================================== */

function syncNavState() {
  const pagePath = window.location.pathname.split('/').pop() || 'index.html';

  document.querySelectorAll('.nav-link, .bottom-nav-item').forEach((link) => {
    const href = link.getAttribute('href') || '';
    const hrefPage = href.split('/').pop() || 'index.html';
    const isActive = hrefPage === pagePath;

    link.classList.toggle('active', isActive);
    if (isActive) {
      link.setAttribute('aria-current', 'page');
    } else {
      link.removeAttribute('aria-current');
    }
  });
}

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/\"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function setupRevealObserver() {
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const revealEls = document.querySelectorAll('.reveal');

  if (reduceMotion || !('IntersectionObserver' in window)) {
    revealEls.forEach((el) => el.classList.add('is-visible'));
    return;
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      const delay = entry.target.getAttribute('data-reveal-delay');
      if (delay) entry.target.style.transitionDelay = `${delay}ms`;
      entry.target.classList.add('is-visible');
      observer.unobserve(entry.target);
    });
  }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });

  revealEls.forEach((el) => observer.observe(el));
}

function renderFeaturedProjects() {
  const container = document.querySelector('#featured-projects');
  if (!container) return;

  const isProjectsPage = window.location.pathname.includes('/projects/');
  const apiUrl = isProjectsPage ? '../api/projects.php?featured=1&limit=3' : 'api/projects.php?featured=1&limit=3';

  fetch(apiUrl)
    .then((response) => {
      if (!response.ok) throw new Error('Failed to load featured projects');
      return response.json();
    })
    .then((payload) => {
      const projects = Array.isArray(payload.projects) ? payload.projects : [];
      if (!projects.length) {
        container.innerHTML = `
          <div class="placeholder-card reveal reveal-up">
            <span class="eyebrow">03</span>
            <div class="skeleton-block w-80" style="margin-top:1rem;"></div>
            <div class="skeleton-block w-60"></div>
            <div class="skeleton-block w-40"></div>
          </div>
        `;
        setupRevealObserver();
        return;
      }

      container.innerHTML = projects.slice(0, 3).map((project, index) => {
        const techs = (project.technologies || 'HTML, CSS, JavaScript')
          .split(',')
          .map((item) => item.trim())
          .filter(Boolean)
          .slice(0, 3)
          .join(' • ');

        const normalizedImage = project.image_path
          ? (project.image_path.startsWith('http') ? project.image_path : '/' + String(project.image_path).replace(/^\/+/, ''))
          : '';

        const image = normalizedImage
          ? `<img src="${escapeHtml(normalizedImage)}" alt="${escapeHtml(project.title)}" style="width:100%; height:100%; object-fit:cover; display:block;">`
          : `<span>${escapeHtml(project.category || 'Projet')}</span>`;

        return `
          <article class="story-card reveal reveal-up" data-reveal-delay="${index * 100}">
            <div class="project-image" style="margin-bottom: 1rem; min-height: 180px; border-radius: 16px; background: linear-gradient(135deg, rgba(232,163,61,0.15), rgba(59,130,246,0.12)); border: 1px solid rgba(255,255,255,0.08); display:flex; align-items:center; justify-content:center; color: var(--text-dim); font-size: 0.8rem; letter-spacing: 0.08em; text-transform: uppercase; overflow:hidden;">
              ${image}
            </div>
            <span class="eyebrow">${escapeHtml(project.category || 'Projet')}</span>
            <h3 style="margin-top:0.5rem; margin-bottom:0.6rem;">${escapeHtml(project.title)}</h3>
            <p class="text-dim" style="margin-bottom:1rem;">${escapeHtml(project.short_description || 'Description à venir.')}</p>
            <div class="mini-stat">${escapeHtml(techs)}</div>
          </article>
        `;
      }).join('');

      setupRevealObserver();
    })
    .catch((error) => {
      console.error('Featured projects error:', error);
      container.innerHTML = `
        <div class="placeholder-card reveal reveal-up">
          <span class="eyebrow">03</span>
          <div class="skeleton-block w-80" style="margin-top:1rem;"></div>
          <div class="skeleton-block w-60"></div>
          <div class="skeleton-block w-40"></div>
        </div>
      `;
      setupRevealObserver();
    });
}

function initSite() {
  if (document.body.dataset.siteInitialized === 'true') return;
  document.body.dataset.siteInitialized = 'true';

  syncNavState();
  setupRevealObserver();

  /* ---------- Marquee: duplicate track content for seamless loop ---------- */
  document.querySelectorAll('.marquee-track').forEach((track) => {
    if (track.dataset.cloned) return;
    track.innerHTML += track.innerHTML;
    track.dataset.cloned = 'true';
  });

  /* ---------- Projects filter tabs (visual only at skeleton stage) ---------- */
  const filterTabs = document.querySelectorAll('.filter-tab');
  filterTabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      filterTabs.forEach((t) => t.setAttribute('aria-pressed', 'false'));
      tab.setAttribute('aria-pressed', 'true');
      // Real filtering wired once the Projects grid is populated from MySQL.
    });
  });

  /* ---------- Contact form: lightweight client-side validation (visual only) ---------- */
  const contactForm = document.querySelector('#contact-form');
  if (contactForm) {
    const statusEl = contactForm.querySelector('.form-status');

    contactForm.addEventListener('submit', (e) => {
      let valid = true;
      contactForm.querySelectorAll('[required]').forEach((field) => {
        const errorEl = field.closest('.field')?.querySelector('.field-error');
        if (!field.value.trim()) {
          valid = false;
          if (errorEl) errorEl.style.display = 'block';
        } else if (errorEl) {
          errorEl.style.display = 'none';
        }
      });

      if (!valid) {
        e.preventDefault();
        return;
      }

      if (statusEl) {
        statusEl.hidden = false;
        statusEl.textContent = '✓ Votre client mail va s’ouvrir pour envoyer le message.';
      }
    });
  }

  renderFeaturedProjects();

  /* ---------- Footer year ---------- */
  const yearEl = document.querySelector('#current-year');
  if (yearEl) yearEl.textContent = new Date().getFullYear();
}

document.addEventListener('DOMContentLoaded', initSite);
document.addEventListener('components:loaded', initSite);
