<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AgroSys · Clima IA · Monitoreo Inteligente</title>
    <!-- Tailwind CSS + Font Awesome + Chart.js + Leaflet (opcional mapa) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .sidebar-transition { transition: transform 0.3s ease-in-out; }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.1); }
        .modal-backdrop { background-color: rgba(0,0,0,0.5); backdrop-filter: blur(2px); }
        .risk-high { border-left-color: #dc2626; background: #fef2f2; }
        .risk-medium { border-left-color: #f59e0b; background: #fffbeb; }
        .risk-low { border-left-color: #10b981; background: #ecfdf5; }
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
                <!-- Selector de terreno con coordenadas -->
                <div class="bg-white rounded-2xl shadow p-5 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-map-marker-alt text-agri-green text-xl"></i>
                        <select id="terrainSelect" class="border border-gray-300 rounded-lg px-4 py-2 w-64">
                            <option value="">Cargando terrenos...</option>
                        </select>
                    </div>
                    <div class="text-sm text-gray-500" id="terrainCoords">Selecciona un terreno para ver datos climáticos</div>
                    <button id="refreshClimateBtn" class="bg-agri-green hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2" style="background-color:#2D6A4F"><i class="fas fa-sync-alt"></i> Actualizar ahora</button>
                </div>

                <!-- Grid principal: clima actual + IA predicciones -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Clima actual widget -->
                    <div class="lg:col-span-1 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow p-5">
                        <div class="flex justify-between items-center mb-3">
                            <h2 class="font-semibold text-gray-700"><i class="fas fa-cloud-sun-rain text-agri-green mr-2"></i> Clima actual</h2>
                            <span class="text-xs text-gray-400" id="climateUpdateTime">--:--</span>
                        </div>
                        <div id="currentClimateWidget">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="text-4xl font-bold" id="tempValue">--°C</div>
                                    <div class="text-gray-600" id="conditionText">---</div>
                                    <div class="text-sm mt-2"><i class="fas fa-tint"></i> Humedad: <span id="humidityValue">--</span>%</div>
                                    <div class="text-sm"><i class="fas fa-wind"></i> Viento: <span id="windValue">--</span> km/h</div>
                                    <div class="text-sm"><i class="fas fa-chart-line"></i> Presión: <span id="pressureValue">--</span> hPa</div>
                                    <div class="text-sm"><i class="fas fa-cloud-rain"></i> Prob. lluvia: <span id="rainProbValue">--</span>%</div>
                                </div>
                                <div><i class="fas fa-cloud-sun text-6xl text-yellow-500"></i></div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-blue-200 text-xs text-gray-500">
                            <i class="fas fa-microchip"></i> Datos actualizados automáticamente (simulación cada hora)
                        </div>
                    </div>

                    <!-- IA Predicción de plagas -->
                    <div class="lg:col-span-1 bg-white rounded-2xl shadow p-5">
                        <h2 class="font-semibold text-gray-700 mb-3"><i class="fas fa-bug text-red-500 mr-2"></i> IA · Predicción de plagas</h2>
                        <div id="plagueRiskContainer" class="space-y-3">
                            <div class="animate-pulse h-20 bg-gray-100 rounded"></div>
                        </div>
                    </div>

                    <!-- IA Recomendaciones agrícolas -->
                    <div class="lg:col-span-1 bg-white rounded-2xl shadow p-5">
                        <h2 class="font-semibold text-gray-700 mb-3"><i class="fas fa-robot text-agri-green mr-2"></i> Recomendaciones IA</h2>
                        <div id="recommendationsContainer" class="space-y-2 text-sm">
                            <div class="animate-pulse h-16 bg-gray-100 rounded"></div>
                        </div>
                    </div>
                </div>

                <!-- Alertas automáticas -->
                <div class="bg-amber-50 rounded-2xl shadow p-5 mb-8 border-l-4 border-amber-500">
                    <h2 class="font-semibold text-gray-700 mb-2"><i class="fas fa-exclamation-triangle text-amber-600 mr-2"></i> Alertas inteligentes</h2>
                    <div id="alertsList" class="flex flex-wrap gap-2">
                        <!-- JS llena -->
                    </div>
                </div>

                <!-- Gráfico de tendencias climáticas -->
                <div class="bg-white rounded-2xl shadow p-5 mb-8">
                    <h2 class="font-semibold text-gray-700 mb-4"><i class="fas fa-chart-line text-agri-green mr-2"></i> Tendencias climáticas (últimos 7 días)</h2>
                    <canvas id="climateTrendChart" height="200" style="max-height: 280px;"></canvas>
                </div>

                <!-- Historial climático (tabla) -->
                <div class="bg-white rounded-2xl shadow p-5">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="font-semibold text-gray-700"><i class="fas fa-history text-agri-green mr-2"></i> Historial climático (últimos 7 registros)</h2>
                        <button id="viewHistoryBtn" class="text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1 rounded">Ver detalles</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr><th class="px-4 py-2 text-left">Fecha/Hora</th><th>Temperatura</th><th>Humedad</th><th>Precipitación</th><th>Viento</th><th>Riesgo IA</th></tr>
                            </thead>
                            <tbody id="historyTableBody">
                                <tr><td colspan="6" class="text-center py-4">Selecciona un terreno</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal historial completo (detalle) -->
    <div id="historyModal" class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 p-6 relative max-h-[80vh] overflow-auto">
            <button id="closeHistoryModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            <h3 class="text-xl font-bold mb-4"><i class="fas fa-calendar-alt"></i> Historial climático completo</h3>
            <div id="fullHistoryContent" class="overflow-x-auto">Cargando...</div>
        </div>
    </div>

    <script>
        // ========== DATOS SIMULADOS ==========
        // Terrenos (coinciden con módulo anterior)
        const terrenos = [
            { id: 1, nombre: "Parcela El Prado", lat: -34.6037, lng: -58.3816, altitud: 25 },
            { id: 2, nombre: "Fundo Los Álamos", lat: -33.4569, lng: -70.6483, altitud: 540 },
            { id: 3, nombre: "Huerta Santa Fe", lat: -34.9214, lng: -57.9545, altitud: 12 },
            { id: 4, nombre: "Agrícola La Esperanza", lat: -32.9468, lng: -60.6393, altitud: 78 }
        ];

        // Historial climático simulado por terreno (últimos 7 días + actual)
        let climateHistory = {};  // key: terrenoId, value: array de registros (cada registro con fecha, temp, humedad, precipitacion, viento, presion, probLluvia, etc.)
        // Función para generar datos climáticos aleatorios pero coherentes
        function generateRandomClimate(baseTemp = 24, baseHum = 65) {
            return {
                temp: (baseTemp + (Math.random() - 0.5) * 4).toFixed(1),
                humidity: Math.floor(baseHum + (Math.random() - 0.5) * 20),
                wind: (5 + Math.random() * 25).toFixed(0),
                pressure: (1010 + Math.random() * 15).toFixed(0),
                rainProb: Math.floor(Math.random() * 80),
                condition: ["Soleado", "Parcialmente nublado", "Nublado", "Lluvia ligera"][Math.floor(Math.random() * 4)],
                precipitation: (Math.random() * 5).toFixed(1)
            };
        }

        // Inicializar historial para cada terreno (7 días hacia atrás + hoy)
        function initHistory() {
            const today = new Date();
            for (let terreno of terrenos) {
                let records = [];
                for (let i = 7; i >= 0; i--) {
                    let date = new Date(today);
                    date.setDate(today.getDate() - i);
                    let dateStr = date.toISOString().slice(0,10);
                    let hourStr = `${10 + Math.floor(Math.random()*8)}:00`; // horario diurno
                    let climate = generateRandomClimate(20 + Math.random()*8, 55 + Math.random()*30);
                    records.push({
                        id: Date.now() + terreno.id + i,
                        fecha: `${dateStr} ${hourStr}`,
                        temp: parseFloat(climate.temp),
                        humedad: climate.humidity,
                        precipitacion: parseFloat(climate.precipitation),
                        viento: parseFloat(climate.wind),
                        presion: parseFloat(climate.pressure),
                        probLluvia: climate.rainProb,
                        condicion: climate.condition
                    });
                }
                climateHistory[terreno.id] = records;
            }
        }
        initHistory();

        // Variables globales
        let currentTerrainId = 1;  // default
        let trendChart = null;

        // Función para actualizar todo según terreno seleccionado
        async function refreshClimateData() {
            const terrainId = currentTerrainId;
            const terreno = terrenos.find(t => t.id === terrainId);
            if (!terreno) return;

            // Simular consulta a API climática (guardar nuevo registro actual)
            const newClimate = generateRandomClimate(22, 65);
            const now = new Date();
            const fechaStr = `${now.toISOString().slice(0,10)} ${now.getHours()}:00`;
            const newRecord = {
                id: Date.now(),
                fecha: fechaStr,
                temp: parseFloat(newClimate.temp),
                humedad: newClimate.humidity,
                precipitacion: parseFloat(newClimate.precipitation),
                viento: parseFloat(newClimate.wind),
                presion: parseFloat(newClimate.pressure),
                probLluvia: newClimate.rainProb,
                condicion: newClimate.condition
            };
            // Agregar al historial (mantener últimos 8 registros)
            let history = climateHistory[terrainId] || [];
            history.unshift(newRecord);
            if (history.length > 8) history.pop();
            climateHistory[terrainId] = history;

            // Actualizar widget clima actual
            document.getElementById('tempValue').innerHTML = newClimate.temp + "°C";
            document.getElementById('humidityValue').innerHTML = newClimate.humidity;
            document.getElementById('windValue').innerHTML = newClimate.wind;
            document.getElementById('pressureValue').innerHTML = newClimate.pressure;
            document.getElementById('rainProbValue').innerHTML = newClimate.rainProb;
            document.getElementById('conditionText').innerHTML = newClimate.condicion;
            document.getElementById('climateUpdateTime').innerHTML = `Actualizado: ${new Date().toLocaleTimeString()}`;
            document.getElementById('terrainCoords').innerHTML = `<i class="fas fa-location-dot"></i> ${terreno.lat}, ${terreno.lng} | Altitud: ${terreno.altitud}m`;

            // Actualizar tabla historial (mostrar últimos 7 registros)
            updateHistoryTable(terrainId);

            // Actualizar gráfico de tendencias (últimos 7 días, temperatura y humedad)
            updateTrendChart(terrainId);

            // Actualizar IA: predicción de plagas y recomendaciones
            updateIAPredictions(newClimate, terreno);

            // Actualizar alertas
            updateAlerts(newClimate);
        }

        function updateHistoryTable(terrainId) {
            const records = climateHistory[terrainId] || [];
            const tbody = document.getElementById('historyTableBody');
            if (records.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Sin datos históricos</td></tr>';
                return;
            }
            // Mostrar últimos 7 (ya están en orden descendente)
            const recent = records.slice(0,7);
            tbody.innerHTML = recent.map(rec => {
                let riesgo = "Bajo";
                if (rec.humedad > 80 && rec.temp > 22) riesgo = "Alto (hongos)";
                else if (rec.humedad > 70) riesgo = "Medio";
                return `<tr class="border-b"><td class="px-4 py-2">${rec.fecha}</td>
                        <td>${rec.temp}°C</td><td>${rec.humedad}%</td>
                        <td>${rec.precipitacion} mm</td><td>${rec.viento} km/h</td>
                        <td class="text-red-600">${riesgo}</td></tr>`;
            }).join('');
        }

        function updateTrendChart(terrainId) {
            const records = climateHistory[terrainId] || [];
            const last7 = records.slice(0,7).reverse(); // orden cronológico ascendente
            const labels = last7.map(r => r.fecha.split(' ')[0]);
            const temps = last7.map(r => r.temp);
            const hums = last7.map(r => r.humedad);
            const ctx = document.getElementById('climateTrendChart').getContext('2d');
            if (trendChart) trendChart.destroy();
            trendChart = new Chart(ctx, {
                type: 'line',
                data: { labels, datasets: [
                    { label: 'Temperatura (°C)', data: temps, borderColor: '#e11d48', backgroundColor: 'rgba(225,29,72,0.05)', tension: 0.3, fill: true },
                    { label: 'Humedad (%)', data: hums, borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.05)', tension: 0.3, fill: true }
                ]},
                options: { responsive: true, maintainAspectRatio: true }
            });
        }

        // IA predicción de plagas
        function updateIAPredictions(climate, terreno) {
            const containerPlague = document.getElementById('plagueRiskContainer');
            const containerRec = document.getElementById('recommendationsContainer');
            const temp = parseFloat(climate.temp);
            const hum = climate.humidity;
            let plagueRisk = "";
            let plagueDesc = "";
            let riskClass = "";
            if (hum > 80 && temp > 20 && temp < 28) {
                plagueRisk = "ALTO";
                plagueDesc = "⚠️ Riesgo alto de hongos (tizón tardío, mildiu). Se recomienda fungicida preventivo.";
                riskClass = "risk-high";
            } else if (hum > 70 || temp > 28) {
                plagueRisk = "MEDIO";
                plagueDesc = "⚠️ Posible aparición de pulgón o roya. Monitorear cada 2 días.";
                riskClass = "risk-medium";
            } else {
                plagueRisk = "BAJO";
                plagueDesc = "✅ Condiciones favorables. Sin riesgo inminente de plagas.";
                riskClass = "risk-low";
            }
            containerPlague.innerHTML = `<div class="p-3 rounded-xl ${riskClass} border-l-4"><div class="font-bold">Riesgo: ${plagueRisk}</div><div class="text-sm">${plagueDesc}</div><div class="text-xs mt-2 text-gray-500"><i class="fas fa-microchip"></i> Basado en humedad ${hum}% / temp ${temp}°C</div></div>`;

            // Recomendaciones agrícolas IA
            let recos = [];
            if (temp < 15) recos.push("❄️ Temperatura baja: proteger cultivos sensibles al frío. Aplazar siembra.");
            if (hum > 75) recos.push("💧 Humedad alta: evitar riego excesivo. Ventilar invernaderos.");
            if (climate.rainProb > 60) recos.push("🌧️ Probable lluvia: programar fumigación después del evento.");
            if (temp > 28 && hum < 40) recos.push("🔥 Estrés hídrico: aumentar riego en horas tempranas.");
            if (recos.length === 0) recos.push("📊 Condiciones normales. Sigue con el plan de labores programado.");
            containerRec.innerHTML = recos.map(r => `<div class="p-2 bg-gray-50 rounded"><i class="fas fa-leaf text-agri-green mr-1"></i> ${r}</div>`).join('');
        }

        function updateAlerts(climate) {
            const container = document.getElementById('alertsList');
            let alerts = [];
            const temp = parseFloat(climate.temp);
            const hum = climate.humidity;
            if (temp < 5) alerts.push("❄️ Alerta de helada: riesgo de daño en cultivos. Activar riego por aspersión.");
            if (hum > 85) alerts.push("💧 Exceso de humedad: riesgo de botrytis y hongos. Aumentar ventilación.");
            if (climate.rainProb > 70) alerts.push("🌧️ Alta probabilidad de lluvia: asegurar drenajes.");
            if (temp > 32) alerts.push("🔥 Ola de calor: estrés térmico. Aplicar mulch y riego adicional.");
            if (alerts.length === 0) alerts.push("✅ Sin alertas climáticas activas. Todo estable.");
            container.innerHTML = alerts.map(a => `<div class="bg-white rounded-full px-3 py-1 text-sm shadow-sm"><i class="fas fa-bell text-amber-500"></i> ${a}</div>`).join('');
        }

        // Llenar select de terrenos
        function populateTerrainSelect() {
            const select = document.getElementById('terrainSelect');
            select.innerHTML = terrenos.map(t => `<option value="${t.id}">${t.nombre}</option>`).join('');
            select.value = "1";
            currentTerrainId = 1;
            refreshClimateData();
        }

        // Evento cambio de terreno
        document.getElementById('terrainSelect').addEventListener('change', (e) => {
            currentTerrainId = parseInt(e.target.value);
            refreshClimateData();
        });
        document.getElementById('refreshClimateBtn').addEventListener('click', () => {
            refreshClimateData();
            Swal.fire('Actualizado', 'Datos climáticos y predicciones IA actualizados', 'success');
        });

        // Modal de historial completo
        const modalHistory = document.getElementById('historyModal');
        document.getElementById('viewHistoryBtn').addEventListener('click', () => {
            const records = climateHistory[currentTerrainId] || [];
            const htmlTable = `<table class="min-w-full text-sm"><thead class="bg-gray-100"><tr><th class="px-3 py-2">Fecha/Hora</th><th>Temperatura</th><th>Humedad</th><th>Precipitación</th><th>Viento</th><th>Presión</th><th>Prob. lluvia</th></tr></thead><tbody>
                ${records.map(r => `<tr class="border-b"><td class="px-3 py-2">${r.fecha}</td><td>${r.temp}°C</td><td>${r.humedad}%</td><td>${r.precipitacion} mm</td><td>${r.viento} km/h</td><td>${r.presion} hPa</td><td>${r.probLluvia}%</td></tr>`).join('')}
            </tbody></table>`;
            document.getElementById('fullHistoryContent').innerHTML = htmlTable;
            modalHistory.classList.remove('hidden');
            modalHistory.classList.add('flex');
        });
        document.getElementById('closeHistoryModal').addEventListener('click', () => {
            modalHistory.classList.add('hidden');
            modalHistory.classList.remove('flex');
        });
        // Cerrar modal click fuera (simple)
        modalHistory.addEventListener('click', (e) => { if(e.target === modalHistory) modalHistory.classList.add('hidden'); });

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

        // Simular actualización automática cada 60 segundos (opcional)
        setInterval(() => {
            if (currentTerrainId) refreshClimateData();
        }, 60000);

        populateTerrainSelect();
    </script>
</body>
</html>