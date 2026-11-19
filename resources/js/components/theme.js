const STORAGE_KEY = 'theme';

/** localStorage can throw (private mode, blocked storage): never let that break the page. */
function readStoredTheme() {
    try {
        return window.localStorage.getItem(STORAGE_KEY);
    } catch {
        return null;
    }
}

function storeTheme(value) {
    try {
        window.localStorage.setItem(STORAGE_KEY, value);
    } catch {
        // Ignore: the choice simply won't be remembered.
    }
}

/**
 * Light/dark toggle. The initial theme is applied by an inline script in <head>
 * (before first paint) to avoid a flash of the wrong theme.
 */
export default () => ({
    dark: document.documentElement.classList.contains('dark'),

    init() {
        // Follow the OS setting until the visitor makes an explicit choice.
        window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', (event) => {
            if (readStoredTheme() === null) {
                this.apply(!event.matches);
            }
        });
    },

    toggle() {
        this.apply(!this.dark);
        storeTheme(this.dark ? 'dark' : 'light');
    },

    apply(dark) {
        this.dark = dark;
        document.documentElement.classList.toggle('dark', dark);
    },
});
