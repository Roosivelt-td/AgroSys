import React from 'react';
import { createRoot } from 'react-dom/client';
import MapTerrenos from './react/MapTerrenos';
import BarChart from './react/BarChart';
import PieChart from './react/PieChart';
import LandLineChart from './react/LandLineChart';
import ClimateTrendChart from './react/ClimateTrendChart';
import AgroLogoPremium from './react/AgroLogoPremium';

const components = {
    'agro-map-terrenos': MapTerrenos,
    'agro-bar-chart': BarChart,
    'agro-pie-chart': PieChart,
    'agro-land-line-chart': LandLineChart,
    'agro-climate-trend-chart': ClimateTrendChart,
    'agro-logo-premium': AgroLogoPremium,
};

const mountedRoots = new WeakMap();

function mountReactComponents() {
    const elements = document.querySelectorAll('[data-react-component]');

    elements.forEach(el => {
        const componentName = el.getAttribute('data-react-component');
        const Component = components[componentName];

        if (Component) {
            try {
                const props = JSON.parse(el.getAttribute('data-props') || '{}');
                const key = el.getAttribute('wire:key') || el.getAttribute('data-key') || 'default';

                let root = mountedRoots.get(el);
                if (!root) {
                    root = createRoot(el);
                    mountedRoots.set(el, root);
                }

                root.render(React.createElement(Component, { ...props, key }));
            } catch (e) {
                console.error(`[AgroReact] Error montando ${componentName}:`, e);
            }
        }
    });
}

// Escuchar eventos de Livewire y carga de página
document.addEventListener('livewire:navigated', () => mountReactComponents());
document.addEventListener('DOMContentLoaded', () => mountReactComponents());
window.mountAgroReact = () => mountReactComponents();

// Observer para cambios dinámicos (cuando se abren modales)
const observer = new MutationObserver(() => mountReactComponents());
observer.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['data-props'] });
