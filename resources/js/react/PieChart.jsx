import React, { useEffect, useRef } from 'react';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const PieChart = ({ data }) => {
    const chartRef = useRef(null);
    const chartInstance = useRef(null);

    const draw = () => {
        if (!chartRef.current) return;
        const ctx = chartRef.current.getContext('2d');

        if (chartInstance.current) chartInstance.current.destroy();

        const values = data?.values || [];
        const hasValues = values.length > 0 && values.reduce((a,b) => a+b, 0) > 0;

        if (!hasValues) return;

        chartInstance.current = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels || [],
                datasets: [{
                    data: values,
                    backgroundColor: data.colors || ['#3b82f6', '#f59e0b', '#8b5cf6'],
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: 'bold' } } }
                }
            }
        });
    };

    useEffect(() => {
        // Intentar dibujar con un pequeño retraso para asegurar que el modal terminó su animación
        const timer = setTimeout(draw, 300);
        return () => {
            clearTimeout(timer);
            if (chartInstance.current) chartInstance.current.destroy();
        };
    }, [JSON.stringify(data)]);

    return (
        <div className="w-full h-full relative min-h-[250px] flex items-center justify-center">
            <canvas ref={chartRef}></canvas>
            <div className="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pb-8">
                <span className="text-[10px] font-black text-slate-400 uppercase">Total</span>
                <span className="text-sm font-black text-slate-800 dark:text-white">
                    S/ {data?.values?.reduce((a,b) => a+b, 0).toLocaleString()}
                </span>
            </div>
        </div>
    );
};

export default PieChart;
