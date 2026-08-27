import React, { useEffect, useRef } from 'react';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const BarChart = ({ data }) => {
    const chartRef = useRef(null);
    const chartInstance = useRef(null);

    useEffect(() => {
        const chartElement = chartRef.current;
        if (!chartElement) return;

        // Limpieza absoluta inicial
        if (chartInstance.current) {
            chartInstance.current.destroy();
            chartInstance.current = null;
        }

        if (!data || !data.labels || data.labels.length === 0) return;

        const ctx = chartElement.getContext('2d');

        const createChart = () => {
            // Destruir instancia previa si existe antes de crear la nueva
            if (chartInstance.current) {
                chartInstance.current.destroy();
            }

            chartInstance.current = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: data.unit || 'S/',
                        data: data.values,
                        backgroundColor: data.colors || '#00ba2e',
                        borderRadius: 10,
                        borderSkipped: false,
                        barThickness: 35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 500 },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#003a38',
                            titleFont: { size: 11, weight: 'bold' },
                            bodyFont: { size: 10 },
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false },
                            ticks: { font: { size: 9, weight: '700' }, color: '#94a3b8' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 9, weight: '700' }, color: '#94a3b8' }
                        }
                    }
                }
            });
        };

        // Usamos un observador de redimensionamiento para detectar cuándo el canvas es visible y tiene tamaño real
        const resizeObserver = new ResizeObserver((entries) => {
            if (entries[0].contentRect.width > 0 && entries[0].contentRect.height > 0) {
                createChart();
            }
        });

        resizeObserver.observe(chartElement.parentElement);

        return () => {
            resizeObserver.disconnect();
            if (chartInstance.current) {
                chartInstance.current.destroy();
                chartInstance.current = null;
            }
        };
    }, [data]);

    return (
        <div className="w-full h-full relative p-4 flex flex-col items-center justify-center">
            {(!data || !data.labels || data.labels.length === 0) && (
                <div className="absolute inset-0 flex flex-col items-center justify-center text-slate-400">
                    <i className="fa-solid fa-chart-pie text-4xl mb-2 opacity-20"></i>
                    <span className="text-[10px] font-black uppercase tracking-widest italic">No hay datos para esta selección</span>
                </div>
            )}
            <canvas ref={chartRef} className="w-full h-full"></canvas>
        </div>
    );
};

export default BarChart;
