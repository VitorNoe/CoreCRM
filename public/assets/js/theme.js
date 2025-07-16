// theme.js - alternância e leitura do tema do sistema
(function() {
  function setTheme(theme) {
    document.body.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
  }

  function getSystemTheme() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  function getSavedTheme() {
    return localStorage.getItem('theme');
  }

  function applyTheme() {
    var saved = getSavedTheme();
    if (saved === 'dark' || saved === 'light') {
      setTheme(saved);
    } else {
      setTheme(getSystemTheme());
    }
  }

  window.toggleTheme = function() {
    var current = document.body.getAttribute('data-theme');
    setTheme(current === 'dark' ? 'light' : 'dark');
  };

  applyTheme();

  // Atualiza tema se sistema mudar
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
    if (!getSavedTheme()) setTheme(e.matches ? 'dark' : 'light');
  });
})();
