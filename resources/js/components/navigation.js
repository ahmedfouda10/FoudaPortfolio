/**
 * Sticky navbar: highlights the section currently in view and drives the
 * accessible mobile menu (focus trap, Escape to close, scroll lock).
 *
 * @param {string[]} sectionIds
 */
export default (sectionIds = []) => ({
    open: false,
    active: null,

    init() {
        const sections = sectionIds.map((id) => document.getElementById(id)).filter(Boolean);
        let scheduled = false;

        // The active section is the last one whose top has crossed 40% of the viewport.
        const update = () => {
            const threshold = window.innerHeight * 0.4;

            this.active = sections.reduce(
                (current, section) => (section.getBoundingClientRect().top <= threshold ? section.id : current),
                null,
            );
            scheduled = false;
        };

        window.addEventListener(
            'scroll',
            () => {
                if (!scheduled) {
                    scheduled = true;
                    window.requestAnimationFrame(update);
                }
            },
            { passive: true },
        );

        update();

        window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
            if (event.matches) {
                this.close();
            }
        });
    },

    isActive(id) {
        return this.active === id;
    },

    toggle() {
        this.open = !this.open;
    },

    close() {
        this.open = false;
    },
});
