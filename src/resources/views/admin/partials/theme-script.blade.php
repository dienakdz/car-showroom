<script data-navigate-once>
(function () {
    function getStoredTheme() {
        try {
            const urlParams = new URLSearchParams(window.location.search);
            const paramTheme = urlParams.get('theme');
            if (paramTheme === 'dark' || paramTheme === 'light') {
                return paramTheme;
            }
            const match = document.cookie.match(/(^|;)\s*admin_theme=([^;]+)/);
            if (match && (match[2] === 'dark' || match[2] === 'light')) {
                return match[2];
            }
            return localStorage.getItem('admin_theme') || 'light';
        } catch (e) {
            return 'light';
        }
    }

    window.setAdminTheme = function (theme) {
        document.documentElement.setAttribute('data-theme', theme);
        try {
            localStorage.setItem('admin_theme', theme);
            document.cookie = "admin_theme=" + theme + ";path=/;max-age=31536000;SameSite=Lax";
        } catch (e) {}

        document.querySelectorAll('#adminThemeToggle, .c1-theme-toggle').forEach(function (btn) {
            const isDark = theme === 'dark';
            const title = isDark ? 'Chuyển sang chế độ sáng' : 'Chuyển sang chế độ tối';
            btn.setAttribute('title', title);
            btn.setAttribute('aria-label', title);
        });
    };

    window.toggleAdminTheme = function (e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        const currentTheme = document.documentElement.getAttribute('data-theme') || getStoredTheme();
        const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
        window.setAdminTheme(nextTheme);
    };

    // Global event delegation on document - indestructible across wire:navigate
    document.addEventListener('click', function (e) {
        const btn = e.target.closest && e.target.closest('#adminThemeToggle, .c1-theme-toggle');
        if (btn) {
            e.preventDefault();
            e.stopPropagation();
            window.toggleAdminTheme(e);
        }
    }, true);

    function syncTheme() {
        const current = getStoredTheme();
        window.setAdminTheme(current);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', syncTheme);
    } else {
        syncTheme();
    }

    // Keep theme and button attributes in sync across Livewire 3 wire:navigate transitions
    document.addEventListener('livewire:navigated', syncTheme);
})();
</script>

