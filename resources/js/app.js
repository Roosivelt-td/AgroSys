import React from 'react';
import { createRoot } from 'react-dom/client';
import MapTerrenos from './react/MapTerrenos';
import BarChart from './react/BarChart';
import LandLineChart from './react/LandLineChart';
import ClimateTrendChart from './react/ClimateTrendChart';
import AgroLogoPremium from './react/AgroLogoPremium';

const components = {
    'agro-map-terrenos': MapTerrenos,
    'agro-bar-chart': BarChart,
    'agro-land-line-chart': LandLineChart,
    'agro-climate-trend-chart': ClimateTrendChart,
    'agro-logo-premium': AgroLogoPremium,
};

const mountedRoots = new WeakMap();
const componentKeys = new WeakMap();

function mountReactComponents(container = document) {
    const elements = container.querySelectorAll
        ? container.querySelectorAll('[data-react-component]')
        : [];

    // Si el contenedor mismo es un componente
    const list = [...elements];
    if (container.getAttribute && container.getAttribute('data-react-component')) {
        list.push(container);
    }

    list.forEach(el => {
        const componentName = el.getAttribute('data-react-component');
        const Component = components[componentName];

        if (Component) {
            try {
                const props = JSON.parse(el.getAttribute('data-props') || '{}');
                const key = el.getAttribute('wire:key') || el.getAttribute('data-key') || 'default';

                const prevKey = componentKeys.get(el);
                let root = mountedRoots.get(el);

                if (prevKey && prevKey !== key && root) {
                    console.log(`[AgroReact] Key cambiado: ${prevKey} → ${key}, recreando ${componentName}`);
                    root.unmount();
                    mountedRoots.delete(el);
                    componentKeys.delete(el);
                    root = null;
                }

                if (!root) {
                    root = createRoot(el);
                    mountedRoots.set(el, root);
                    componentKeys.set(el, key);
                    console.log(`[AgroReact] Montado: ${componentName} (key: ${key})`);
                }

                root.render(React.createElement(Component, { ...props, key }));
            } catch (e) {
                console.error(`[AgroReact] Error en ${componentName}:`, e);
            }
        }
    });
}

// Integración con Livewire 3
document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', ({ el }) => {
        if (el?.getAttribute && (el.getAttribute('data-react-component') || el.querySelector('[data-react-component]'))) {
            mountReactComponents(el.parentElement || document);
        }
    });

    Livewire.hook('navigate', () => {
        setTimeout(() => mountReactComponents(), 50);
    });
});

// Observer para capturar cambios dinámicos
const observer = new MutationObserver((mutations) => {
    const toUpdate = new Set();

    mutations.forEach((mutation) => {
        if (mutation.type === 'childList') {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    if (node.getAttribute && node.getAttribute('data-react-component')) toUpdate.add(node);
                    const children = node.querySelectorAll ? node.querySelectorAll('[data-react-component]') : [];
                    children.forEach(c => toUpdate.add(c));
                }
            });
        } else if (mutation.type === 'attributes') {
            const el = mutation.target;
            if (el.getAttribute && (
                mutation.attributeName === 'data-props' ||
                mutation.attributeName === 'wire:key' ||
                mutation.attributeName === 'data-key'
            )) {
                toUpdate.add(el);
            }
        }
    });

    toUpdate.forEach(el => mountReactComponents(el.parentElement || document));
});

observer.observe(document.body, {
    childList: true,
    subtree: true,
    attributes: true,
    attributeFilter: ['data-props', 'wire:key', 'data-key']
});

document.addEventListener('livewire:navigated', () => mountReactComponents());
document.addEventListener('DOMContentLoaded', () => mountReactComponents());

window.mountAgroReact = () => mountReactComponents();
