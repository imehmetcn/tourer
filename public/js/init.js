// Global variables initialization for original JS files
window.ln = window.ln || {};
window.setting = window.setting || {};
window.controller = window.controller || {};

// Initialize basic settings
window.setting = {
  language: 'en',
  currency: 'EUR',
  // Add other settings as needed
};

// Initialize language object
window.ln = {
  en: {},
  tr: {},
  es: {},
  fr: {}
};

// Initialize controller
window.controller = {
  search: {},
  home: {}
};

console.log('Global variables initialized');