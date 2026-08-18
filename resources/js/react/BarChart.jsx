import React, { useEffect, useRef } from 'react';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const BarChart = ({ data }) => {
    const chartRef = useRef(null);
    const chartInstance = useRef(null);

    useEffect(() => {
        if (!data || !data.labels || data.labels.length === 0) {
            if (chartInstance.current) chartInstance.current.destroy();
            return;
        }

        if (chartInstance.current) {
            chartInstance.current.destroy();
        }

        const ctx = chartRef.current.getContext('2d');
        chartInstance.current = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: data.unit || 'Valor',
                    data: data.values,
                    backgroundColor: '#00ba2e',
                    borderRadius: 12,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#003a38',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 10 },
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
                    }
                }
            }
        });

        return () => {
            if (chartInstance.current) chartInstance.current.destroy();
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
