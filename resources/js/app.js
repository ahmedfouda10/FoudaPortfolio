/*
 * Livewire ships its own copy of Alpine (with the focus, intersect and collapse
 * plugins). Bundling it here gives every public page Alpine, with or without
 * Livewire components, from a single script.
 */
import { Alpine, Livewire } from '../../vendor/livewire/livewire/dist/livewire.esm';
import lightbox from './components/lightbox';
import navigation from './components/navigation';
import projectFilter from './components/project-filter';
import theme from './components/theme';

Alpine.data('theme', theme);
Alpine.data('navigation', navigation);
Alpine.data('projectFilter', projectFilter);
Alpine.data('lightbox', lightbox);

// After a failed submit, move keyboard focus to the first invalid field of forms that opt in.
Livewire.hook('commit', ({ component, succeed }) => {
    succeed(() => {
        window.requestAnimationFrame(() => {
            if (!component.el.hasAttribute('data-focus-first-error')) {
                return;
            }

            component.el.querySelector('[aria-invalid="true"]')?.focus();
        });
    });
});

Livewire.start();
