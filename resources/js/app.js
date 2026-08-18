import React from 'react';
import { createRoot } from 'react-dom/client';
import MapTerrenos from './react/MapTerrenos';

const components = {
    'agro-map-terrenos': MapTerrenos,
};

const mountedRoots = new Map();

function mountReactComponents(container = document) {
    const elements = container.querySelectorAll('[data-react-component]');
    elements.forEach(el => {
        const componentName = el.getAttribute('data-react-component');
        const Component = components[componentName];

        if (Component && !mountedRoots.has(el)) {
            try {
                const props = JSON.parse(el.getAttribute('data-props') || '{}');
                const root = createRoot(el);
                root.render(React.createElement(Component, props));
                mountedRoots.set(el, root);
                console.log(`[AgroReact] Montado: ${componentName}`);
            } catch (e) {
                console.error(`[AgroReact] Error en ${componentName}:`, e);
            }
        }
    });
}

// Observador robusto para capturar modales inyectados por Livewire/Alpine
const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
            if (node.nodeType === 1) {
                mountReactComponents(node);
            }
        });
    });
});

observer.observe(document.body, { childList: true, subtree: true });

// Eventos de ciclo de vida de la página
document.addEventListener('livewire:navigated', () => mountReactComponents());
document.addEventListener('DOMContentLoaded', () => mountReactComponents());

// Función de respaldo global
window.mountAgroReact = () => mountReactComponents();
