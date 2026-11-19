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

Livewire.start();
