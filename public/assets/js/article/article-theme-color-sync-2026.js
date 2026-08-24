(function () {
    const root = document.documentElement;

    function previewThemeColor() {
        const color = new URLSearchParams(window.location.search).get('theme_color') || '';

        return /^#[0-9a-f]{6}$/i.test(color) ? color : '';
    }

    function syncThemeColor() {
        let savedColor = previewThemeColor();

        if (!savedColor) {
            try {
                savedColor = window.localStorage.getItem('theme-color') || '';
            } catch (error) {
                return;
            }
        }

        if (!savedColor) return;

        const normalizedColor = savedColor.trim().toUpperCase();
        const primaryColor = normalizedColor === '#C7CCD8' ? '#667085' : savedColor;

        root.style.setProperty('--ph-theme-primary', primaryColor);
        root.style.setProperty('--ph-theme-surface-tint', savedColor);

        if (normalizedColor === '#C7CCD8') root.dataset.phThemeColor = 'cool-gray';
        else delete root.dataset.phThemeColor;
    }

    syncThemeColor();
    window.addEventListener('storage', function (event) {
        if (event.key === 'theme-color') syncThemeColor();
    });
})();
