import React, { useEffect, useRef } from 'react';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const BarChart = ({ data }) => {
    const chartRef = useRef(null);
    const chartInstance = useRef(null);

    const initChart = () => {
        if (!chartRef.current) return;
        const ctx = chartRef.current.getContext('2d');

        if (chartInstance.current) {
            chartInstance.current.destroy();
        }

        const values = data?.values || [];
        const labels = data?.labels || [];

        if (values.length === 0) return;

        chartInstance.current = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: data.title || 'Inversión',
                    data: values,
                    backgroundColor: data.colors || ['#3b82f6', '#f59e0b', '#8b5cf6'],
                    borderRadius: 8,
                    borderWidth: 0,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' } } },
                    x: { grid: { display: false }, ticks: { font: { size: 9, weight: 'bold' } } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#003a38', cornerRadius: 8 }
                }
            }
        });
    };

    useEffect(() => {
        // Delay crítico para esperar al modal
        const timer = setTimeout(initChart, 500);

        const observer = new ResizeObserver(() => {
            if (chartRef.current?.parentElement?.clientWidth > 0) {
                initChart();
            }
        });

        if (chartRef.current?.parentElement) {
            observer.observe(chartRef.current.parentElement);
        }

        return () => {
            clearTimeout(timer);
            observer.disconnect();
            if (chartInstance.current) chartInstance.current.destroy();
        };
    }, [JSON.stringify(data)]);

    return (
        <div className="w-full h-full min-h-[200px] flex items-center justify-center p-4">
            <canvas ref={chartRef}></canvas>
        </div>
    );
};

export default BarChart;
