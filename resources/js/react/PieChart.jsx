import React, { useEffect, useRef } from 'react';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const PieChart = ({ data }) => {
    const chartRef = useRef(null);
    const chartInstance = useRef(null);

    const draw = () => {
        if (!chartRef.current) return;
        const ctx = chartRef.current.getContext('2d');

        if (chartInstance.current) {
            chartInstance.current.destroy();
            chartInstance.current = null;
        }

        const values = Array.isArray(data?.values) ? data.values : [];
        if (values.length === 0 || values.reduce((a, b) => a + b, 0) === 0) return;

        chartInstance.current = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.labels || [],
                datasets: [{
                    data: values,
                    backgroundColor: data.colors || ['#3b82f6', '#f59e0b', '#8b5cf6'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1000, animateRotate: true },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 15,
                            font: { size: 10, weight: 'bold' }
                        }
                    },
                    tooltip: { backgroundColor: '#003a38', cornerRadius: 8 }
                }
            }
        });
    };

    useEffect(() => {
        // Ejecutar dibujo inmediatamente y con un par de reintentos por si el modal está animando
        draw();
        const timer1 = setTimeout(draw, 500);
        const timer2 = setTimeout(draw, 1000);

        const observer = new ResizeObserver(() => {
            if (chartRef.current?.parentElement?.clientWidth > 0) {
                draw();
            }
        });

        if (chartRef.current?.parentElement) observer.observe(chartRef.current.parentElement);

        return () => {
            clearTimeout(timer1);
            clearTimeout(timer2);
            observer.disconnect();
            if (chartInstance.current) chartInstance.current.destroy();
        };
    }, [JSON.stringify(data)]);

    return (
        <div className="w-full h-full relative flex items-center justify-center min-h-[250px] overflow-visible">
            <canvas ref={chartRef}></canvas>
        </div>
    );
};

export default PieChart;
