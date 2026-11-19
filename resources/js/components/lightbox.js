/**
 * Accessible image gallery lightbox: keyboard navigation (arrows, Escape),
 * focus trap while open and focus restored to the thumbnail on close.
 *
 * @param {{src: string, alt: string, caption: ?string}[]} images
 */
export default (images = []) => ({
    images,
    index: null,
    trigger: null,

    get isOpen() {
        return this.index !== null;
    },

    get current() {
        return this.index === null ? null : this.images[this.index];
    },

    show(index) {
        this.trigger = document.activeElement;
        this.index = index;
    },

    close() {
        this.index = null;
        this.$nextTick(() => this.trigger?.focus());
    },

    next() {
        this.index = (this.index + 1) % this.images.length;
    },

    previous() {
        this.index = (this.index - 1 + this.images.length) % this.images.length;
    },
});
