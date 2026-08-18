<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AgroSys · Reportes y Analítica Avanzada</title>
    <!-- Tailwind CSS + Font Awesome + Chart.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .sidebar-transition { transition: transform 0.3s ease-in-out; }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.1); }
        .modal-backdrop { background-color: rgba(0,0,0,0.5); backdrop-filter: blur(2px); }
        .kpi-card { transition: all 0.2s; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        <!-- =============== SIDEBAR (IDÉNTICA) =============== -->
        <?php
            include "sidebar.php"
        ?>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <!-- Header -->
            <?php
                include "header.php";
            ?>

            <main class="p-6 md:p-8">
                <!-- Filtros y acciones -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                    <div class="flex flex-wrap gap-3">
                        <select id="reportCropFilter" class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm">
                            <option value="todos">Todos los cultivos</option>
                            <option value="Maíz">Maíz</option>
                            <option value="Tomate">Tomate</option>
                            <option value="Papa">Papa</option>
                            <option value="Palta">Palta</option>
                            <option value="Fresa">Fresa</option>
                        </select>
                        <input type="date" id="dateFrom" class="border rounded-lg px-3 py-2 text-sm">
                        <input type="date" id="dateTo" class="border rounded-lg px-3 py-2 text-sm">
                        <button id="applyFiltersBtn" class="bg-agri-green text-white px-4 py-2 rounded-lg text-sm transition" style="background-color:#2D6A4F"><i class="fas fa-filter"></i> Aplicar</button>
                        <button id="resetReportFiltersBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm"><i class="fas fa-undo-alt"></i> Reset</button>
                    </div>
                    <div class="flex gap-2">
                        <button id="exportPDFBtn" class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg text-sm transition"><i class="fas fa-file-pdf"></i> Exportar PDF</button>
                        <button id="exportExcelBtn" class="bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg text-sm transition"><i class="fas fa-file-excel"></i> Exportar Excel</button>
                    </div>
                </div>

                <!-- KPIs principales -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between border-l-4 border-agri-green kpi-card">
                        <div><p class="text-gray-500 text-sm">Producción total</p><p id="totalProduction" class="text-3xl font-bold">0</p><span class="text-xs text-gray-400">toneladas</span></div>
                        <i class="fas fa-weight-hanging text-4xl text-agri-green opacity-70"></i>
                    </div>
                    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between border-l-4 border-blue-500 kpi-card">
                        <div><p class="text-gray-500 text-sm">Ingresos totales</p><p id="totalIncome" class="text-3xl font-bold">$0</p></div>
                        <i class="fas fa-dollar-sign text-4xl text-blue-500 opacity-70"></i>
                    </div>
                    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between border-l-4 border-orange-500 kpi-card">
                        <div><p class="text-gray-500 text-sm">Costos totales</p><p id="totalCosts" class="text-3xl font-bold">$0</p></div>
                        <i class="fas fa-truck text-4xl text-orange-500 opacity-70"></i>
                    </div>
                    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between border-l-4 border-green-500 kpi-card">
                        <div><p class="text-gray-500 text-sm">Rentabilidad neta</p><p id="netProfit" class="text-3xl font-bold">$0</p><span id="profitMargin" class="text-xs"></span></div>
                        <i class="fas fa-chart-line text-4xl text-green-500 opacity-70"></i>
                    </div>
                </div>

                <!-- Gráficos principales -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow p-5">
                        <h2 class="font-semibold text-gray-700 mb-3"><i class="fas fa-chart-bar text-agri-green mr-2"></i> Producción por cultivo (toneladas)</h2>
                        <canvas id="productionByCropChart" height="250"></canvas>
                    </div>
                    <div class="bg-white rounded-2xl shadow p-5">
                        <h2 class="font-semibold text-gray-700 mb-3"><i class="fas fa-chart-line text-agri-green mr-2"></i> Evolución mensual (ingresos vs costos)</h2>
                        <canvas id="monthlyTrendChart" height="250"></canvas>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow p-5">
                        <h2 class="font-semibold text-gray-700 mb-3"><i class="fas fa-chart-pie text-agri-green mr-2"></i> Distribución de costos por tipo de labor</h2>
                        <canvas id="costDistributionChart" height="230"></canvas>
                    </div>
                    <div class="bg-white rounded-2xl shadow p-5">
                        <h2 class="font-semibold text-gray-700 mb-3"><i class="fas fa-chart-line text-agri-green mr-2"></i> Pronóstico IA de producción (próximos meses)</h2>
                        <canvas id="forecastChart" height="230"></canvas>
                        <div class="text-xs text-gray-400 mt-2 text-center"><i class="fas fa-microchip"></i> Basado en regresión lineal histórica</div>
                    </div>
                </div>

                <!-- Tabla comparativa de campañas -->
                <div class="bg-white rounded-2xl shadow p-5 mb-8">
                    <h2 class="font-semibold text-gray-700 mb-4"><i class="fas fa-calendar-alt mr-2 text-agri-green"></i> Comparativa de campañas (año actual vs anterior)</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr><th class="px-4 py-2 text-left">Cultivo</th><th>Rendimiento (kg/ha) 2024</th><th>Rendimiento (kg/ha) 2025</th><th>Variación</th><th>Ingresos 2025</th><th>Costos 2025</th><th>Rentabilidad</th></tr>
                            </thead>
                            <tbody id="comparisonTableBody">
                                <tr><td colspan="7" class="text-center py-4">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Resumen ejecutivo IA -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl shadow p-5 border border-gray-200">
                    <h2 class="font-semibold text-gray-700 mb-2"><i class="fas fa-robot text-agri-green mr-2"></i> Resumen ejecutivo generado por IA</h2>
                    <div id="executiveSummary" class="text-sm text-gray-600 leading-relaxed">
                        Analizando datos...
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // ========== DATOS SIMULADOS AVANZADOS ==========
        // Producción histórica por cultivo y por mes (últimos 12 meses)
        const produccionMensual = {
            "Maíz": [120, 135, 140, 0, 0, 0, 0, 0, 150, 160, 170, 180],  // kg/ha * área? Simplificado a toneladas
            "Tomate": [80, 85, 90, 0, 0, 0, 100, 110, 115, 120, 125, 130],
            "Papa": [50, 55, 60, 0, 0, 0, 65, 70, 75, 80, 85, 90],
            "Palta": [30, 32, 35, 38, 40, 42, 45, 48, 50, 52, 55, 58],
            "Fresa": [15, 18, 20, 22, 25, 28, 30, 32, 35, 38, 40, 42]
        };
        // Ingresos mensuales (miles de USD) y costos mensuales (miles de USD)
        const ingresosMensuales = [15, 18, 22, 20, 25, 30, 35, 40, 45, 50, 55, 60];
        const costosMensuales = [10, 12, 14, 13, 16, 18, 22, 25, 28, 30, 33, 35];

        // Costos por tipo de labor (acumulado año)
        const costosPorLabor = {
            "Riego": 12500,
            "Fumigación": 8700,
            "Fertilización": 11200,
            "Mano de obra": 18400,
            "Cosecha": 9500
        };

        // Datos de campañas comparativas
        const campañas = [
            { cultivo: "Maíz", rend2024: 7800, rend2025: 8200, ingresos2025: 24500, costos2025: 18200 },
            { cultivo: "Tomate", rend2024: 38500, rend2025: 41200, ingresos2025: 67800, costos2025: 42300 },
            { cultivo: "Papa", rend2024: 26500, rend2025: 28100, ingresos2025: 35200, costos2025: 24100 },
            { cultivo: "Palta", rend2024: 11200, rend2025: 11800, ingresos2025: 89400, costos2025: 51200 },
            { cultivo: "Fresa", rend2024: 23800, rend2025: 25200, ingresos2025: 76300, costos2025: 48400 }
        ];

        // Variables globales para gráficos
        let chartProdCrop, chartMonthlyTrend, chartCostDist, chartForecast;

        // Función para actualizar KPIs según filtros (simulamos filtros por cultivo y fechas)
        function updateKPIs(cropFilter, dateFrom, dateTo) {
            // Simulación: para este demo, los KPIs son totales anuales sin filtro complejo
            let totalProd = 0;
            for (let crop in produccionMensual) {
                if (cropFilter === "todos" || crop === cropFilter) {
                    totalProd += produccionMensual[crop].reduce((a,b)=>a+b,0);
                }
            }
            const totalIncome = ingresosMensuales.reduce((a,b)=>a+b,0) * 1000; // en USD
            const totalCost = costosMensuales.reduce((a,b)=>a+b,0) * 1000;
            const netProfit = totalIncome - totalCost;
            const margin = (netProfit / totalIncome * 100).toFixed(1);
            document.getElementById('totalProduction').innerText = totalProd.toFixed(1);
            document.getElementById('totalIncome').innerText = `$${(totalIncome/1000).toFixed(0)}k`;
            document.getElementById('totalCosts').innerText = `$${(totalCost/1000).toFixed(0)}k`;
            document.getElementById('netProfit').innerText = `$${(netProfit/1000).toFixed(0)}k`;
            document.getElementById('profitMargin').innerHTML = `Margen ${margin}%`;
        }

        // Gráfico producción por cultivo (barra)
        function renderProductionByCrop(cropFilter) {
            const ctx = document.getElementById('productionByCropChart').getContext('2d');
            const labels = [];
            const data = [];
            for (let crop in produccionMensual) {
                if (cropFilter === "todos" || crop === cropFilter) {
                    labels.push(crop);
                    data.push(produccionMensual[crop].reduce((a,b)=>a+b,0));
                }
            }
            if (chartProdCrop) chartProdCrop.destroy();
            chartProdCrop = new Chart(ctx, {
                type: 'bar',
                data: { labels, datasets: [{ label: 'Producción total (toneladas)', data, backgroundColor: '#2D6A4F', borderRadius: 6 }] },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } } }
            });
        }

        // Gráfico tendencia mensual ingresos vs costos
        function renderMonthlyTrend() {
            const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
            const months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
            if (chartMonthlyTrend) chartMonthlyTrend.destroy();
            chartMonthlyTrend = new Chart(ctx, {
                type: 'line',
                data: { labels: months, datasets: [
                    { label: 'Ingresos (miles USD)', data: ingresosMensuales, borderColor: '#22c55e', tension: 0.3, fill: false },
                    { label: 'Costos (miles USD)', data: costosMensuales, borderColor: '#f97316', tension: 0.3, fill: false }
                ]},
                options: { responsive: true, maintainAspectRatio: true }
            });
        }

        // Gráfico distribución de costos (dona)
        function renderCostDistribution() {
            const ctx = document.getElementById('costDistributionChart').getContext('2d');
            const labels = Object.keys(costosPorLabor);
            const data = Object.values(costosPorLabor);
            if (chartCostDist) chartCostDist.destroy();
            chartCostDist = new Chart(ctx, {
                type: 'doughnut',
                data: { labels, datasets: [{ data, backgroundColor: ['#2D6A4F','#40916C','#52B788','#74C69D','#95D5B2'] }] },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
            });
        }

        // Pronóstico IA (regresión lineal simple sobre producción total mensual)
        function renderForecast() {
            // Calcular producción total mensual histórica (suma de todos los cultivos)
            const totalMensual = [];
            for (let i = 0; i < 12; i++) {
                let sum = 0;
                for (let crop in produccionMensual) {
                    sum += produccionMensual[crop][i];
                }
                totalMensual.push(sum);
            }
            // Simular regresión lineal para predecir próximos 3 meses
            const x = [1,2,3,4,5,6,7,8,9,10,11,12];
            const y = totalMensual;
            const n = x.length;
            let sumX = 0, sumY = 0, sumXY = 0, sumX2 = 0;
            for (let i = 0; i < n; i++) {
                sumX += x[i];
                sumY += y[i];
                sumXY += x[i] * y[i];
                sumX2 += x[i] * x[i];
            }
            const slope = (n * sumXY - sumX * sumY) / (n * sumX2 - sumX * sumX);
            const intercept = (sumY - slope * sumX) / n;
            const forecast = [];
            for (let i = 13; i <= 15; i++) {
                forecast.push(Math.max(0, slope * i + intercept));
            }
            const months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic','Ene(próx)','Feb(próx)','Mar(próx)'];
            const allData = [...totalMensual, ...forecast];
            const ctx = document.getElementById('forecastChart').getContext('2d');
            if (chartForecast) chartForecast.destroy();
            chartForecast = new Chart(ctx, {
                type: 'line',
                data: { labels: months, datasets: [{ label: 'Producción total (toneladas)', data: allData, borderColor: '#3b82f6', fill: true, backgroundColor: 'rgba(59,130,246,0.1)', tension: 0.3 }] },
                options: { responsive: true, maintainAspectRatio: true, plugins: { tooltip: { callbacks: { label: (ctx) => `${ctx.raw.toFixed(1)} t` } } } }
            });
        }

        // Tabla comparativa de campañas
        function renderComparisonTable() {
            const tbody = document.getElementById('comparisonTableBody');
            tbody.innerHTML = campañas.map(c => {
                const variation = ((c.rend2025 - c.rend2024) / c.rend2024 * 100).toFixed(1);
                const profit = c.ingresos2025 - c.costos2025;
                const color = variation >= 0 ? 'text-green-600' : 'text-red-600';
                return `<tr class="border-b">
                    <td class="px-4 py-2 font-medium">${c.cultivo}</td>
                    <td>${c.rend2024.toLocaleString()}</td>
                    <td>${c.rend2025.toLocaleString()}</td>
                    <td class="${color}">${variation}%</td>
                    <td>$${(c.ingresos2025/1000).toFixed(0)}k</td>
                    <td>$${(c.costos2025/1000).toFixed(0)}k</td>
                    <td class="font-semibold">$${(profit/1000).toFixed(0)}k</td>
                </tr>`;
            }).join('');
        }

        // Resumen ejecutivo IA (texto dinámico)
        function updateExecutiveSummary(cropFilter, dateFrom, dateTo) {
            const summaryDiv = document.getElementById('executiveSummary');
            const totalProd = parseFloat(document.getElementById('totalProduction').innerText);
            const profit = document.getElementById('netProfit').innerText;
            const mejorCultivo = campañas.reduce((best, c) => c.rend2025 > best.rend2025 ? c : best, campañas[0]);
            const alerta = (totalProd < 500) ? "Se recomienda revisar prácticas de cultivo para aumentar rendimiento." : "Los indicadores muestran una tendencia positiva.";
            summaryDiv.innerHTML = `
                <p><i class="fas fa-chart-line text-agri-green mr-1"></i> <strong>Análisis general:</strong> La producción total alcanzó <strong>${totalProd} toneladas</strong> en el período seleccionado, con una rentabilidad neta de ${profit}. </p>
                <p class="mt-2"><i class="fas fa-trophy text-yellow-500 mr-1"></i> <strong>Cultivo destacado:</strong> ${mejorCultivo.cultivo} con rendimiento de ${mejorCultivo.rend2025.toLocaleString()} kg/ha, superando en ${((mejorCultivo.rend2025-mejorCultivo.rend2024)/mejorCultivo.rend2024*100).toFixed(1)}% al año anterior.</p>
                <p class="mt-2"><i class="fas fa-robot text-agri-green mr-1"></i> <strong>Recomendación IA:</strong> ${alerta} Considere optimizar la distribución de costos (principalmente mano de obra y fertilización) para mejorar margen.</p>
            `;
        }

        // Aplicar filtros (simulación, en un caso real se recargarían datos del backend)
        function applyFilters() {
            const cropFilter = document.getElementById('reportCropFilter').value;
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            updateKPIs(cropFilter, dateFrom, dateTo);
            renderProductionByCrop(cropFilter);
            // Los demás gráficos no dependen del filtro de cultivo en este demo, pero podrían
            updateExecutiveSummary(cropFilter, dateFrom, dateTo);
            Swal.fire('Filtros aplicados', 'Los datos se han actualizado según los criterios seleccionados.', 'success');
        }

        // Reset filtros
        function resetFilters() {
            document.getElementById('reportCropFilter').value = 'todos';
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            applyFilters();
        }

        // Exportar PDF (simulado)
        function exportPDF() {
            Swal.fire('Exportando PDF', 'El reporte se está generando... (simulación)', 'info');
            setTimeout(() => {
                Swal.fire('Exportación completada', 'El archivo PDF ha sido descargado.', 'success');
            }, 1500);
        }

        // Exportar Excel (simulado con descarga CSV)
        function exportExcel() {
            // Generar datos CSV simples
            let csv = "Cultivo,Rendimiento2024,Rendimiento2025,Ingresos2025,Costos2025\n";
            campañas.forEach(c => {
                csv += `${c.cultivo},${c.rend2024},${c.rend2025},${c.ingresos2025},${c.costos2025}\n`;
            });
            const blob = new Blob([csv], { type: 'text/csv' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'reporte_agrosys.csv';
            link.click();
            Swal.fire('Exportación CSV', 'Se ha descargado el archivo CSV con los datos principales.', 'success');
        }

        // Sidebar y dark mode
        const sidebar = document.getElementById('sidebar'), openBtn = document.getElementById('openSidebarBtn'), closeBtn = document.getElementById('closeSidebarBtn');
        openBtn?.addEventListener('click', () => sidebar.classList.remove('-translate-x-full'));
        closeBtn?.addEventListener('click', () => sidebar.classList.add('-translate-x-full'));
        document.addEventListener('click', (e) => { if (window.innerWidth < 768 && !sidebar.contains(e.target) && !openBtn.contains(e.target)) sidebar.classList.add('-translate-x-full'); });
        const darkToggle = document.getElementById('darkModeToggle');
        darkToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            if (document.documentElement.classList.contains('dark')) {
                darkToggle.innerHTML = '<i class="fas fa-sun"></i>';
                document.body.classList.add('bg-gray-900', 'text-gray-200');
            } else {
                darkToggle.innerHTML = '<i class="fas fa-moon"></i>';
                document.body.classList.remove('bg-gray-900', 'text-gray-200');
            }
        });

        // Event listeners de botones
        document.getElementById('applyFiltersBtn').addEventListener('click', applyFilters);
        document.getElementById('resetReportFiltersBtn').addEventListener('click', resetFilters);
        document.getElementById('exportPDFBtn').addEventListener('click', exportPDF);
        document.getElementById('exportExcelBtn').addEventListener('click', exportExcel);

        // Inicializar todo
        function init() {
            updateKPIs('todos', '', '');
            renderProductionByCrop('todos');
            renderMonthlyTrend();
            renderCostDistribution();
            renderForecast();
            renderComparisonTable();
            updateExecutiveSummary('todos', '', '');
        }
        init();
    </script>
</body>
</html>