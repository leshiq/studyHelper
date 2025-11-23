// Theme Management
(function() {
    'use strict';

    const userPreference = document.documentElement.getAttribute('data-theme-preference') || 'auto';
    
    function getSystemTheme() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    
    function getActiveTheme() {
        if (userPreference === 'auto') {
            return getSystemTheme();
        }
        return userPreference;
    }
    
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
    }
    
    // Apply theme on load
    applyTheme(getActiveTheme());
    
    // Listen for system theme changes if user preference is auto
    if (userPreference === 'auto') {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            applyTheme(e.matches ? 'dark' : 'light');
        });
    }
})();
