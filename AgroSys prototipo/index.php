<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AgroSys · Dashboard Inteligente</title>
    <!-- Tailwind CSS + Font Awesome + Chart.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Configuración adicional de Tailwind para personalización -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        agri: {
                            green: '#2D6A4F',
                            light: '#D8F3DC',
                            dark: '#1B4332',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* scroll suave y transiciones */
        body {
            transition: background-color 0.3s, color 0.2s;
        }
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.1);
        }
        .alert-enter {
            animation: fadeSlideDown 0.4s ease-out;
        }
        @keyframes fadeSlideDown {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <!-- ================= ENVOLTORIO PRINCIPAL ================= -->
    <div class="flex h-screen overflow-hidden">

        <!-- =============== SIDEBAR (RESPONSIVE) =============== -->
        <?php
            include "sidebar.php"
        ?>

        <!-- =============== CONTENIDO PRINCIPAL =============== -->
        <div class="flex-1 flex flex-col overflow-y-auto">

            <!-- Header con hamburguesa y acciones -->
            <header class="bg-white shadow-sm sticky top-0 z-20 px-6 py-3 flex items-center justify-between border-b border-gray-100">
                <div class="flex items-center space-x-4">
                    <button id="openSidebarBtn" class="md:hidden text-gray-600 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">Panel de Control</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button id="darkModeToggle" class="text-gray-500 hover:text-agri-green transition">
                        <i class="fas fa-moon"></i>
                    </button>
                    <div class="relative">
                        <i class="fas fa-bell text-gray-500 hover:text-agri-green cursor-pointer"></i>
                        <span class="absolute -top-1 -right-2 bg-red-500 text-white text-[10px] rounded-full px-1">3</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full bg-agri-green flex items-center justify-center text-white font-bold">JG</div>
                        <span class="hidden sm:inline text-sm font-medium">Juan Gómez</span>
                    </div>
                </div>
            </header>

            <!-- MAIN: dashboard avanzado -->
            <main class="p-6 md:p-8">
                <!-- Tarjetas de métricas principales -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center justify-between border-l-4 border-agri-green card-hover transition">
                        <div>
                            <p class="text-gray-500 text-sm">Terrenos activos</p>
                            <p class="text-3xl font-bold" id="totalLand">12.4</p>
                            <span class="text-xs text-green-600"><i class="fas fa-arrow-up"></i> +2.3 ha</span>
                        </div>
                        <i class="fas fa-map text-4xl text-agri-green opacity-70"></i>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center justify-between border-l-4 border-blue-500 card-hover transition">
                        <div>
                            <p class="text-gray-500 text-sm">Cultivos activos</p>
                            <p class="text-3xl font-bold" id="activeCrops">8</p>
                            <span class="text-xs text-gray-400">3 en cosecha</span>
                        </div>
                        <i class="fas fa-seedling text-4xl text-blue-500 opacity-70"></i>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center justify-between border-l-4 border-yellow-500 card-hover transition">
                        <div>
                            <p class="text-gray-500 text-sm">Labores pendientes</p>
                            <p class="text-3xl font-bold" id="pendingTasks">5</p>
                            <span class="text-xs text-yellow-600">2 de fumigación</span>
                        </div>
                        <i class="fas fa-clipboard-list text-4xl text-yellow-500 opacity-70"></i>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center justify-between border-l-4 border-red-400 card-hover transition">
                        <div>
                            <p class="text-gray-500 text-sm">Riesgo IA</p>
                            <p class="text-3xl font-bold" id="riskScore">Medio</p>
                            <span class="text-xs text-red-500"><i class="fas fa-exclamation-triangle"></i> Plagas potenciales</span>
                        </div>
                        <i class="fas fa-robot text-4xl text-red-400 opacity-70"></i>
                    </div>
                </div>

                <!-- Sección de dos columnas: clima + alertas IA -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Widget clima en tiempo real (simulado) -->
                    <div class="lg:col-span-1 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow p-5">
                        <div class="flex justify-between items-center mb-3">
                            <h2 class="font-semibold text-gray-700"><i class="fas fa-cloud-sun-rain text-agri-green mr-2"></i> Clima actual</h2>
                            <span class="text-xs text-gray-400" id="climateUpdateTime">Actualizado ahora</span>
                        </div>
                        <div id="climateWidget">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-4xl font-bold" id="tempValue">--°C</div>
                                    <div class="text-gray-600" id="conditionText">Cargando...</div>
                                    <div class="text-sm mt-2"><i class="fas fa-tint"></i> Humedad: <span id="humidityValue">--</span>%</div>
                                    <div class="text-sm"><i class="fas fa-wind"></i> Viento: <span id="windValue">--</span> km/h</div>
                                </div>
                                <div><i class="fas fa-cloud-sun text-6xl text-yellow-500"></i></div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-blue-200 text-xs text-gray-500">
                                <i class="fas fa-map-pin"></i> Parcela "El Prado" · 12°34' S
                            </div>
                        </div>
                        <button id="refreshClimateBtn" class="mt-3 w-full bg-white/60 hover:bg-white text-agri-green text-sm py-1 rounded-lg transition"><i class="fas fa-sync-alt mr-1"></i> Actualizar datos</button>
                    </div>

                    <!-- Alertas inteligentes IA -->
                    <div class="lg:col-span-2 bg-white rounded-2xl shadow p-5">
                        <div class="flex justify-between items-center border-b pb-2 mb-3">
                            <h2 class="font-semibold text-gray-700"><i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i> Alertas IA · Predicción de riesgos</h2>
                            <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Actualización automática</span>
                        </div>
                        <div id="alertsContainer" class="space-y-3">
                            <!-- JS llenará dinámicamente -->
                            <div class="animate-pulse flex space-x-3"><div class="h-12 bg-gray-200 rounded w-full"></div></div>
                        </div>
                        <div class="mt-3 text-right">
                            <span class="text-xs text-gray-400"><i class="fas fa-microchip"></i> Modelo ML: riesgo climático + plagas</span>
                        </div>
                    </div>
                </div>

                <!-- Gráfico avanzado y tabla de labores recientes -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Gráfico de tendencias climáticas (temperatura + humedad) -->
                    <div class="lg:col-span-2 bg-white rounded-2xl shadow p-5">
                        <h2 class="font-semibold text-gray-700 mb-4"><i class="fas fa-chart-line text-agri-green mr-2"></i> Tendencias climáticas (últimos 7 días)</h2>
                        <canvas id="climateChart" height="200" width="400" style="max-height: 260px; width: 100%;"></canvas>
                        <div class="flex justify-center space-x-6 mt-3 text-xs text-gray-500">
                            <span><i class="fas fa-thermometer-half text-red-500"></i> Temperatura media</span>
                            <span><i class="fas fa-tint text-blue-500"></i> Humedad relativa</span>
                        </div>
                    </div>

                    <!-- Tabla de labores agrícolas recientes -->
                    <div class="bg-white rounded-2xl shadow p-5 overflow-auto">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="font-semibold text-gray-700"><i class="fas fa-tasks text-agri-green mr-2"></i> Últimas labores</h2>
                            <a href="#" class="text-xs text-agri-green hover:underline">Ver todas</a>
                        </div>
                        <div class="space-y-3" id="recentTasksList">
                            <!-- JS carga tareas -->
                            <div class="animate-pulse space-y-2"><div class="h-10 bg-gray-100 rounded"></div><div class="h-10 bg-gray-100 rounded"></div></div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // ---------- 1. SIMULACIÓN DE DATOS AVANZADOS (API FAKE) ----------
        // Datos climáticos simulados (cambiar con refresh)
        let currentClimate = {
            temp: 24.3,
            humidity: 68,
            wind: 12,
            condition: "Parcialmente nublado",
            lastUpdate: new Date().toLocaleTimeString()
        };

        // Alertas IA
        const alertExamples = [
            { level: "alto", title: "Riesgo de tizón tardío", description: "Humedad >85% + temperaturas templadas en próximas 48h.", icon: "fa-biohazard", bg: "bg-red-50", text: "text-red-700" },
            { level: "medio", title: "Posible ataque de pulgón", description: "Detección temprana por imágenes satelitales en parcela norte.", icon: "fa-bug", bg: "bg-yellow-50", text: "text-yellow-700" },
            { level: "bajo", title: "Recomendación: fertilización nitrogenada", description: "IA sugiere aplicar urea en maíz dentro de 3 días.", icon: "fa-leaf", bg: "bg-green-50", text: "text-green-700" },
            { level: "alto", title: "Alerta helada temprana", description: "Pronóstico nocturno: -2°C. Activar riego por aspersión.", icon: "fa-snowflake", bg: "bg-blue-50", text: "text-blue-800" }
        ];

        // Labores recientes
        const labores = [
            { type: "Riego", crop: "Maíz", date: "2025-03-18", status: "completado" },
            { type: "Fumigación", crop: "Tomate", date: "2025-03-17", status: "completado" },
            { type: "Fertilización", crop: "Papa", date: "2025-03-16", status: "completado" },
            { type: "Monitoreo IA", crop: "Palta", date: "2025-03-15", status: "programado" }
        ];

        // Datos históricos para el gráfico (últimos 7 días)
        const last7Days = ["18/03", "17/03", "16/03", "15/03", "14/03", "13/03", "12/03"];
        const tempData = [23.2, 24.1, 22.8, 21.5, 20.9, 22.0, 23.4];
        const humData = [72, 68, 74, 77, 80, 75, 70];

        let chart;

        // Inicializar Chart.js
        function initChart() {
            const ctx = document.getElementById('climateChart').getContext('2d');
            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: last7Days,
                    datasets: [
                        {
                            label: 'Temperatura (°C)',
                            data: tempData,
                            borderColor: '#e11d48',
                            backgroundColor: 'rgba(225, 29, 72, 0.05)',
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#e11d48'
                        },
                        {
                            label: 'Humedad (%)',
                            data: humData,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59,130,246,0.05)',
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#3b82f6'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } }
                    },
                    scales: { y: { beginAtZero: false, grid: { color: '#e5e7eb' } } }
                }
            });
        }

        // Actualizar widget climático en DOM
        function updateClimateWidget() {
            document.getElementById('tempValue').innerHTML = currentClimate.temp + "°C";
            document.getElementById('humidityValue').innerHTML = currentClimate.humidity;
            document.getElementById('windValue').innerHTML = currentClimate.wind;
            document.getElementById('conditionText').innerHTML = currentClimate.condition;
            document.getElementById('climateUpdateTime').innerHTML = `Actualizado: ${currentClimate.lastUpdate}`;
        }

        // Simular llamada a API climática (podría ser OpenWeather)
        async function fetchClimateSimulation() {
            // Simula latencia y nuevos valores aleatorios pero coherentes
            return new Promise((resolve) => {
                setTimeout(() => {
                    const newTemp = (20 + Math.random() * 8).toFixed(1);
                    const newHum = Math.floor(55 + Math.random() * 30);
                    const conditions = ["Soleado", "Parcialmente nublado", "Nublado", "Lluvia ligera"];
                    resolve({
                        temp: parseFloat(newTemp),
                        humidity: newHum,
                        wind: (5 + Math.random() * 20).toFixed(0),
                        condition: conditions[Math.floor(Math.random() * conditions.length)],
                        lastUpdate: new Date().toLocaleTimeString()
                    });
                }, 600);
            });
        }

        // Refrescar clima y re-evaluar riesgos IA (simulación)
        async function refreshClimateAndAlerts() {
            // Mostrar loading visual
            const climateWidget = document.getElementById('climateWidget');
            climateWidget.style.opacity = '0.6';
            const newClimate = await fetchClimateSimulation();
            currentClimate = newClimate;
            updateClimateWidget();
            climateWidget.style.opacity = '1';
            // También actualizamos alertas basadas en el nuevo clima (simular lógica IA)
            generateDynamicAlerts(currentClimate);
            // Actualizar métricas de riesgo (tarjeta riesgo)
            updateRiskScore(currentClimate);
        }

        // Actualizar alertas en contenedor
        function generateDynamicAlerts(climate) {
            const container = document.getElementById('alertsContainer');
            let alertsToShow = [...alertExamples];
            // Lógica dinámica: si humedad > 75% añadir alerta extra de hongos
            if (climate.humidity > 75) {
                alertsToShow.unshift({
                    level: "alto",
                    title: "⚠️ Alerta crítica: Exceso de humedad",
                    description: `Humedad actual ${climate.humidity}% · Riesgo alto de mildiu y botrytis.`,
                    icon: "fa-droplet",
                    bg: "bg-red-100",
                    text: "text-red-800"
                });
            }
            if (climate.temp > 28) {
                alertsToShow.push({
                    level: "medio",
                    title: "Estrés térmico en cultivos",
                    description: "Temperatura elevada. Recomendación: riego adicional en horas tempranas.",
                    icon: "fa-temperature-high",
                    bg: "bg-orange-50",
                    text: "text-orange-700"
                });
            }
            // Render
            container.innerHTML = "";
            alertsToShow.slice(0, 4).forEach(alert => {
                const alertDiv = document.createElement('div');
                alertDiv.className = `p-3 rounded-xl ${alert.bg} border-l-4 border-${alert.level === 'alto' ? 'red' : (alert.level === 'medio' ? 'yellow' : 'green')}-500 flex items-start space-x-3 alert-enter`;
                alertDiv.innerHTML = `
                    <i class="fas ${alert.icon} ${alert.text} text-lg mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-semibold text-sm ${alert.text}">${alert.title}</p>
                        <p class="text-xs text-gray-600">${alert.description}</p>
                    </div>
                    <i class="fas fa-bell text-gray-400 text-xs cursor-pointer"></i>
                `;
                container.appendChild(alertDiv);
            });
        }

        function updateRiskScore(climate) {
            let risk = "Bajo";
            let extraClass = "text-green-600";
            if (climate.humidity > 80 || climate.temp > 30) {
                risk = "Alto";
                extraClass = "text-red-600";
            } else if (climate.humidity > 65 || climate.temp > 26) {
                risk = "Medio";
                extraClass = "text-yellow-600";
            }
            const riskElement = document.getElementById('riskScore');
            riskElement.innerHTML = risk;
            riskElement.className = `text-3xl font-bold ${extraClass}`;
        }

        // Mostrar labores recientes
        function renderRecentTasks() {
            const container = document.getElementById('recentTasksList');
            container.innerHTML = "";
            labores.forEach(labor => {
                const taskDiv = document.createElement('div');
                taskDiv.className = "flex justify-between items-center border-b border-gray-100 pb-2 mb-2";
                taskDiv.innerHTML = `
                    <div>
                        <p class="font-medium text-gray-800 text-sm">${labor.type}</p>
                        <p class="text-xs text-gray-500">${labor.crop} · ${labor.date}</p>
                    </div>
                    <span class="text-xs ${labor.status === 'completado' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'} px-2 py-0.5 rounded-full">${labor.status}</span>
                `;
                container.appendChild(taskDiv);
            });
            // extra info
            const pendingInfo = document.createElement('div');
            pendingInfo.className = "mt-3 text-xs text-center text-gray-400";
            pendingInfo.innerHTML = '<i class="fas fa-stopwatch"></i> Próxima labor: Riego nocturno (20/03)';
            container.appendChild(pendingInfo);
        }

        // Métricas estáticas (simular actualización)
        function updateMetrics() {
            document.getElementById('totalLand').innerText = "12.4";
            document.getElementById('activeCrops').innerText = "8";
            document.getElementById('pendingTasks').innerText = "5";
        }

        // =============== SIDEBAR MOBILE & DARK MODE ===============
        const sidebar = document.getElementById('sidebar');
        const openBtn = document.getElementById('openSidebarBtn');
        const closeBtn = document.getElementById('closeSidebarBtn');

        function openSidebar() { sidebar.classList.remove('-translate-x-full'); }
        function closeSidebar() { sidebar.classList.add('-translate-x-full'); }
        if (openBtn) openBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        // Cerrar al hacer click fuera en móvil (opcional)
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 768 && !sidebar.contains(e.target) && !openBtn.contains(e.target)) {
                closeSidebar();
            }
        });

        // Dark mode toggle (avanzado con clase dark)
        const darkToggle = document.getElementById('darkModeToggle');
        darkToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            if (document.documentElement.classList.contains('dark')) {
                darkToggle.innerHTML = '<i class="fas fa-sun"></i>';
                document.body.classList.add('bg-gray-900', 'text-gray-200');
                document.body.classList.remove('bg-gray-50');
            } else {
                darkToggle.innerHTML = '<i class="fas fa-moon"></i>';
                document.body.classList.remove('bg-gray-900', 'text-gray-200');
                document.body.classList.add('bg-gray-50');
            }
        });

        // Inicialización de la vista
        async function initDashboard() {
            initChart();
            updateMetrics();
            renderRecentTasks();
            await refreshClimateAndAlerts();  // carga clima y alertas dinámicas
            // Simular actualización periódica cada 60 segundos (clima + alertas)
            setInterval(async () => {
                await refreshClimateAndAlerts();
                // Podríamos actualizar también el gráfico con nuevos datos simulados
                // (opcional para demostrar dinamismo)
            }, 60000);
        }

        // Refresh manual botón
        document.getElementById('refreshClimateBtn').addEventListener('click', async () => {
            await refreshClimateAndAlerts();
        });

        initDashboard();
    </script>
</body>
</html>