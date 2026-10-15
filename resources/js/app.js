import React from 'react';
import { createRoot } from 'react-dom/client';
import { Chart, registerables } from 'chart.js';
import MapTerrenos from './react/MapTerrenos';
import BarChart from './react/BarChart';
import PieChart from './react/PieChart';
import LandLineChart from './react/LandLineChart';
import ClimateTrendChart from './react/ClimateTrendChart';
import AgroLogoPremium from './react/AgroLogoPremium';

// Registrar Chart.js globalmente
Chart.register(...registerables);
window.Chart = Chart;

const components = {
    'agro-map-terrenos': MapTerrenos,
    'agro-bar-chart': BarChart,
    'agro-pie-chart': PieChart,
    'agro-land-line-chart': LandLineChart,
    'agro-climate-trend-chart': ClimateTrendChart,
    'agro-logo-premium': AgroLogoPremium,
};

const mountedRoots = new WeakMap();
const mountedProps = new WeakMap();

function mountReactComponents() {
    const elements = document.querySelectorAll('[data-react-component]');

    elements.forEach(el => {
        const componentName = el.getAttribute('data-react-component');
        const Component = components[componentName];

        if (Component) {
            try {
                const propsRaw = el.getAttribute('data-props') || '{}';
                const lastProps = mountedProps.get(el);

                // Evitar re-renders infinitos si el elemento ya está montado con las mismas props
                if (lastProps === propsRaw && mountedRoots.has(el)) {
                    return;
                }

                const props = JSON.parse(propsRaw);
                const key = el.getAttribute('wire:key') || el.getAttribute('data-key') || 'default';

                let root = mountedRoots.get(el);
                if (!root) {
                    root = createRoot(el);
                    mountedRoots.set(el, root);
                }

                mountedProps.set(el, propsRaw);
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

// MutationObserver con debouncing seguro
let isMounting = false;
let timeoutId = null;

const observer = new MutationObserver(() => {
    if (isMounting) return;
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => {
        isMounting = true;
        mountReactComponents();
        isMounting = false;
    }, 50);
});

observer.observe(document.body, { childList: true, subtree: true });
