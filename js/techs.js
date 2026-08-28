(function () {
  const marqueeTrack = document.getElementById('tech-marquee-track');
  const skillStack = document.getElementById('skill-stack');

  if (!marqueeTrack && !skillStack) return;

  const isProjectsPage = window.location.pathname.includes('/projects/');
  const rootPrefix = isProjectsPage ? '../' : './';
  const apiUrl = isProjectsPage ? '../api/technologies.php' : './api/technologies.php';

  function resolveLogoSource(value) {
    if (!value) return '';
    if (/^https?:\/\//i.test(value)) return value;
    return `${rootPrefix}${value.replace(/^\//, '')}`;
  }

  function renderTechs(items) {
    const safeItems = Array.isArray(items) ? items : [];

    if (!safeItems.length) return;

    const marqueeMarkup = safeItems
      .map((item) => {
        const logo = resolveLogoSource(item.logo_url || item.logo_path || '');
        const image = logo ? `<img src="${logo}" alt="" />` : '<span class="tech-badge">#</span>';
        return `<span class="marquee-item tech-item">${image}${item.name}</span>`;
      })
      .join('');

    if (marqueeTrack) {
      marqueeTrack.innerHTML = marqueeMarkup + marqueeMarkup;
    }

    if (skillStack) {
      skillStack.innerHTML = safeItems
        .map((item) => {
          const logo = resolveLogoSource(item.logo_url || item.logo_path || '');
          const image = logo ? `<img src="${logo}" alt="" />` : '<span class="tech-badge">#</span>';
          return `<span class="skill-chip">${image}${item.name}</span>`;
        })
        .join('');
    }
  }

  fetch(apiUrl)
    .then((response) => {
      if (!response.ok) throw new Error('Failed to load technologies');
      return response.json();
    })
    .then((data) => {
      if (Array.isArray(data)) {
        renderTechs(data);
      }
    })
    .catch(() => {
      // Keep default static stack if no DB-driven data exists
    });
})();
