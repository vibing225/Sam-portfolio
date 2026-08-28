(function () {
  function getRootPath() {
    const isProjectsPage = window.location.pathname.includes('/projects/');
    return isProjectsPage ? '../' : './';
  }

  function applyRootVars(html) {
    return html.replace(/\{\{root\}\}/g, getRootPath());
  }

  function loadComponent(selector, url) {
    const node = document.querySelector(selector);
    if (!node) return;

    fetch(url)
      .then((response) => {
        if (!response.ok) throw new Error('Failed to load include');
        return response.text();
      })
      .then((html) => {
        node.innerHTML = applyRootVars(html);
        document.dispatchEvent(new CustomEvent('components:loaded'));
      })
      .catch((error) => {
        console.error('Include load error:', error);
      });
  }

  document.addEventListener('DOMContentLoaded', () => {
    loadComponent('[data-include="header"]', `${getRootPath()}includes/header.html`);
    loadComponent('[data-include="footer"]', `${getRootPath()}includes/footer.html`);
  });
})();
