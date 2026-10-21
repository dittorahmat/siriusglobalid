/* admin.js - tab halaman Tampilan > Konten Situs.
   Tanpa JS semua panel tetap tampil; script ini hanya mengaktifkan mode tab.
   Tidak ada dependensi eksternal. */
(function () {
  'use strict';

  var root = document.querySelector('.sgi-admin');
  if (!root) return;

  var tabs = Array.prototype.slice.call(root.querySelectorAll('[data-sgi-tab]'));
  var panels = Array.prototype.slice.call(root.querySelectorAll('[data-sgi-panel]'));
  if (!tabs.length || !panels.length) return;

  var field = root.querySelector('input[name="sgi_tab"]');
  var current = field ? field.value : tabs[0].getAttribute('data-sgi-tab');
  var known = tabs.map(function (t) {
    return t.getAttribute('data-sgi-tab');
  });
  if (known.indexOf(current) === -1) current = known[0];

  // ARIA tab semantics hanya dipasang saat JS aktif; tanpa JS tombol tab
  // disembunyikan lewat <noscript> dan semua panel tampil.
  var nav = root.querySelector('.sgi-tabs');
  if (nav) {
    nav.setAttribute('role', 'tablist');
    nav.setAttribute('aria-orientation', 'horizontal');
  }
  tabs.forEach(function (tab) {
    tab.setAttribute('role', 'tab');
    tab.setAttribute('type', 'button');
  });
  panels.forEach(function (panel) {
    panel.setAttribute('role', 'tabpanel');
  });

  function activate(key, moveFocus) {
    tabs.forEach(function (tab) {
      var on = tab.getAttribute('data-sgi-tab') === key;
      tab.classList.toggle('nav-tab-active', on);
      tab.setAttribute('aria-selected', on ? 'true' : 'false');
      tab.setAttribute('tabindex', on ? '0' : '-1');
      if (on && moveFocus) tab.focus();
    });
    panels.forEach(function (panel) {
      panel.classList.toggle('is-active', panel.getAttribute('data-sgi-panel') === key);
    });
    if (field) field.value = key;
    try {
      var url = new URL(window.location.href);
      url.searchParams.set('page', 'sgi');
      url.searchParams.set('tab', key);
      window.history.replaceState({}, '', url.toString());
    } catch (e) {
      /* riwayat URL opsional: jangan gagalkan pergantian tab */
    }
  }

  root.classList.add('sgi-tabs--js');
  activate(current, false);

  tabs.forEach(function (tab, index) {
    tab.addEventListener('click', function (event) {
      event.preventDefault();
      activate(tab.getAttribute('data-sgi-tab'), false);
    });
    tab.addEventListener('keydown', function (event) {
      var next = null;
      if (event.key === 'ArrowRight') next = (index + 1) % tabs.length;
      else if (event.key === 'ArrowLeft') next = (index - 1 + tabs.length) % tabs.length;
      else if (event.key === 'Home') next = 0;
      else if (event.key === 'End') next = tabs.length - 1;
      else if (event.key === ' ' || event.key === 'Spacebar') {
        event.preventDefault();
        activate(tab.getAttribute('data-sgi-tab'), true);
        return;
      }
      if (next === null) return;
      event.preventDefault();
      activate(tabs[next].getAttribute('data-sgi-tab'), true);
    });
  });
})();
