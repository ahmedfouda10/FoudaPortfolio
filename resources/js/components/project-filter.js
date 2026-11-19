/**
 * Client-side filter for the project cards. Every project is rendered by the
 * server, so the list works without JavaScript and filtering is instant.
 *
 * @param {string[][]} projects The integrations of each project, in card order.
 */
export default (projects = []) => ({
    selected: null,
    projects,

    get visibleCount() {
        return this.projects.filter((integrations) => this.matches(integrations)).length;
    },

    select(integration) {
        this.selected = integration;
    },

    isSelected(integration) {
        return this.selected === integration;
    },

    /** @param {string[]} integrations */
    matches(integrations) {
        return this.selected === null || integrations.includes(this.selected);
    },
});
