// Toast notification system
(function() {
  'use strict';

  function ensureContainer() {
    const id = 'siteToastContainer';
    let container = document.getElementById(id);
    if (container) return container;

    container = document.createElement('div');
    container.id = id;
    container.style.position = 'fixed';
    container.style.zIndex = '1080';
    container.style.right = '1rem';
    container.style.bottom = '1rem';
    container.style.display = 'flex';
    container.style.flexDirection = 'column';
    container.style.gap = '0.5rem';
    container.style.pointerEvents = 'none';
    document.body.appendChild(container);
    return container;
  }

  function createToast(msg, opts = {}) {
    const container = ensureContainer();
    const t = document.createElement('div');
    t.className = 'site-toast';
    t.style.minWidth = '180px';
    t.style.maxWidth = '320px';
    t.style.background = opts.background || 'rgba(0,0,0,0.85)';
    t.style.color = opts.color || '#fff';
    t.style.padding = '0.6rem 0.8rem';
    t.style.borderRadius = '6px';
    t.style.boxShadow = '0 6px 18px rgba(0,0,0,0.2)';
    t.style.opacity = '0';
    t.style.transition = 'opacity 180ms ease, transform 220ms ease';
    t.style.transform = 'translateY(8px)';
    t.style.cursor = 'pointer';
    t.style.pointerEvents = 'auto';
    t.textContent = msg || '';
    container.appendChild(t);

    // Show
    requestAnimationFrame(() => {
      t.style.opacity = '1';
      t.style.transform = 'translateY(0)';
    });

    const duration = (opts.duration && Number(opts.duration)) || 2200;
    let timeout = setTimeout(hide, duration);

    function hide() {
      clearTimeout(timeout);
      t.style.opacity = '0';
      t.style.transform = 'translateY(8px)';
      setTimeout(() => {
        try {
          container.removeChild(t);
        } catch (e) {}
      }, 260);
    }

    // Allow click to dismiss
    t.addEventListener('click', hide);
    return { hide };
  }

  window.Toast = {
    show: function(msg, opts) {
      return createToast(msg, opts);
    }
  };
})();
