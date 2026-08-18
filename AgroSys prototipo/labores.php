<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AgroSys · Gestión de Labores Agrícolas</title>
    <!-- Tailwind CSS + Font Awesome + Chart.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .sidebar-transition { transition: transform 0.3s ease-in-out; }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.1); }
        .modal-backdrop { background-color: rgba(0,0,0,0.5); backdrop-filter: blur(2px); }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
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
                <!-- Barra de filtros y botón agregar -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                    <div class="flex flex-wrap gap-3">
                        <select id="filterLaborType" class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm">
                            <option value="">Todos los tipos</option>
                            <option value="Riego">Riego</option>
                            <option value="Fumigación">Fumigación</option>
                            <option value="Fertilización">Fertilización</option>
                            <option value="Cosecha">Cosecha</option>
                            <option value="Mano de obra">Mano de obra</option>
                        </select>
                        <select id="filterCrop" class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm">
                            <option value="">Todos los cultivos</option>
                        </select>
                        <select id="filterStatus" class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm">
                            <option value="">Todos los estados</option>
                            <option value="Completada">Completada</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="En progreso">En progreso</option>
                        </select>
                        <input type="date" id="filterDateFrom" class="border rounded-lg px-3 py-2 text-sm" placeholder="Desde">
                        <input type="date" id="filterDateTo" class="border rounded-lg px-3 py-2 text-sm" placeholder="Hasta">
                        <button id="resetFiltersBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm transition"><i class="fas fa-undo-alt mr-1"></i> Reset</button>
                    </div>
                    <button id="openModalBtn" class="bg-agri-green hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow transition flex items-center gap-2" style="background-color:#2D6A4F"><i class="fas fa-plus"></i> Nueva Labor</button>
                </div>

                <!-- Tarjetas de resumen -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between border-l-4 border-agri-green">
                        <div><p class="text-gray-500 text-sm">Total Labores</p><p id="totalLabors" class="text-3xl font-bold">0</p></div>
                        <i class="fas fa-clipboard-list text-4xl text-agri-green opacity-70"></i>
                    </div>
                    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between border-l-4 border-blue-500">
                        <div><p class="text-gray-500 text-sm">Costo Total</p><p id="totalCost" class="text-3xl font-bold">$0</p></div>
                        <i class="fas fa-dollar-sign text-4xl text-blue-500 opacity-70"></i>
                    </div>
                    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between border-l-4 border-yellow-500">
                        <div><p class="text-gray-500 text-sm">Labores Pendientes</p><p id="pendingLabors" class="text-3xl font-bold">0</p></div>
                        <i class="fas fa-hourglass-half text-4xl text-yellow-500 opacity-70"></i>
                    </div>
                    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between border-l-4 border-purple-500">
                        <div><p class="text-gray-500 text-sm">Costo Promedio</p><p id="avgCost" class="text-3xl font-bold">$0</p></div>
                        <i class="fas fa-chart-line text-4xl text-purple-500 opacity-70"></i>
                    </div>
                </div>

                <!-- Gráficos: costos por tipo y tendencia mensual -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow p-5">
                        <h2 class="font-semibold text-gray-700 mb-4"><i class="fas fa-chart-pie text-agri-green mr-2"></i> Costos por tipo de labor</h2>
                        <canvas id="costByTypeChart" height="200" style="max-height: 250px;"></canvas>
                    </div>
                    <div class="bg-white rounded-2xl shadow p-5">
                        <h2 class="font-semibold text-gray-700 mb-4"><i class="fas fa-chart-line text-agri-green mr-2"></i> Tendencia mensual (costos)</h2>
                        <canvas id="monthlyTrendChart" height="200" style="max-height: 250px;"></canvas>
                    </div>
                </div>

                <!-- Alertas IA (próximas labores recomendadas) -->
                <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-2xl shadow p-5 mb-8 border-l-4 border-amber-500">
                    <h2 class="font-semibold text-gray-700 mb-2"><i class="fas fa-robot text-amber-600 mr-2"></i> Alertas Inteligentes · IA AgroSys</h2>
                    <div id="iaAlertsContainer" class="space-y-2 text-sm">
                        <!-- JS dinámico -->
                    </div>
                </div>

                <!-- LISTADO DE LABORES (tarjetas) -->
                <h2 class="text-lg font-semibold text-gray-700 mb-4"><i class="fas fa-list text-agri-green mr-2"></i> Registro de labores</h2>
                <div id="laborsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <div class="col-span-full flex justify-center py-10"><i class="fas fa-spinner fa-spin text-3xl text-agri-green"></i></div>
                </div>
            </main>
        </div>
    </div>

    <!-- MODAL PARA AGREGAR/EDITAR LABOR -->
    <div id="laborModal" class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop transition-all">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6 relative max-h-[90vh] overflow-y-auto">
            <button id="closeModalBtn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            <h3 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-tasks"></i> Nueva Labor</h3>
            <form id="laborForm">
                <div class="mb-3"><label class="block text-sm font-medium">Tipo de labor *</label>
                    <select id="laborType" required class="w-full border rounded-lg px-3 py-2">
                        <option value="Riego">Riego</option><option value="Fumigación">Fumigación</option>
                        <option value="Fertilización">Fertilización</option><option value="Cosecha">Cosecha</option>
                        <option value="Mano de obra">Mano de obra</option>
                    </select>
                </div>
                <div class="mb-3"><label class="block text-sm font-medium">Cultivo asociado *</label>
                    <select id="laborCropId" required class="w-full border rounded-lg px-3 py-2"></select>
                </div>
                <div class="mb-3"><label class="block text-sm font-medium">Fecha de realización *</label>
                    <input type="date" id="laborDate" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="mb-3"><label class="block text-sm font-medium">Costo (USD)</label>
                    <input type="number" step="0.01" id="laborCost" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="mb-3"><label class="block text-sm font-medium">Estado</label>
                    <select id="laborStatus" class="w-full border rounded-lg px-3 py-2">
                        <option>Completada</option><option>Pendiente</option><option>En progreso</option>
                    </select>
                </div>
                <div class="mb-3"><label class="block text-sm font-medium">Notas / detalles</label>
                    <textarea id="laborNotes" rows="2" class="w-full border rounded-lg px-3 py-2"></textarea>
                </div>
                <div class="flex justify-end space-x-3 mt-5">
                    <button type="button" id="cancelModalBtn" class="px-4 py-2 border rounded-lg">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-agri-green text-white rounded-lg" style="background-color:#2D6A4F">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ========== DATOS SIMULADOS ==========
        // Cultivos (de módulo anterior)
        const cultivosDisponibles = [
            { id: 1, nombre: "Maíz", terrenoId: 1, fechaSiembra: "2025-02-10", estado: "En crecimiento" },
            { id: 2, nombre: "Tomate", terrenoId: 2, fechaSiembra: "2025-01-20", estado: "En crecimiento" },
            { id: 3, nombre: "Papa", terrenoId: 3, fechaSiembra: "2025-03-01", estado: "Planificado" },
            { id: 4, nombre: "Palta", terrenoId: 4, fechaSiembra: "2024-11-05", estado: "En crecimiento" },
            { id: 5, nombre: "Fresa", terrenoId: 1, fechaSiembra: "2025-03-15", estado: "Planificado" }
        ];

        // Labores iniciales
        let labores = [
            { id: 1, tipo: "Riego", cultivoId: 1, fecha: "2025-03-10", costo: 150.00, estado: "Completada", notas: "Riego por goteo, 2 horas" },
            { id: 2, tipo: "Fumigación", cultivoId: 2, fecha: "2025-03-12", costo: 320.50, estado: "Completada", notas: "Contra pulgón" },
            { id: 3, tipo: "Fertilización", cultivoId: 1, fecha: "2025-03-15", costo: 210.00, estado: "Completada", notas: "Nitrógeno" },
            { id: 4, tipo: "Riego", cultivoId: 4, fecha: "2025-03-18", costo: 200.00, estado: "Pendiente", notas: "Programado para mañana" },
            { id: 5, tipo: "Mano de obra", cultivoId: 3, fecha: "2025-03-20", costo: 450.00, estado: "Pendiente", notas: "Deshierbe" },
            { id: 6, tipo: "Cosecha", cultivoId: 2, fecha: "2025-03-25", costo: 800.00, estado: "En progreso", notas: "Inicio cosecha temprana" }
        ];

        // Función para obtener nombre de cultivo
        function getCropName(cropId) {
            const crop = cultivosDisponibles.find(c => c.id === cropId);
            return crop ? crop.nombre : "Desconocido";
        }

        // Actualizar estadísticas y gráficos
        function updateStats() {
            const total = labores.length;
            const totalCost = labores.reduce((sum, l) => sum + (l.costo || 0), 0);
            const pending = labores.filter(l => l.estado === "Pendiente" || l.estado === "En progreso").length;
            const avgCost = total > 0 ? totalCost / total : 0;
            document.getElementById('totalLabors').innerText = total;
            document.getElementById('totalCost').innerText = `$${totalCost.toFixed(2)}`;
            document.getElementById('pendingLabors').innerText = pending;
            document.getElementById('avgCost').innerText = `$${avgCost.toFixed(2)}`;
            updateCharts();
        }

        // Gráficos: costos por tipo y tendencia mensual
        let costByTypeChart, monthlyTrendChart;
        function updateCharts() {
            // Costos por tipo
            const typeMap = new Map();
            labores.forEach(l => { if (l.costo) typeMap.set(l.tipo, (typeMap.get(l.tipo) || 0) + l.costo); });
            const types = Array.from(typeMap.keys());
            const costs = Array.from(typeMap.values());
            if (costByTypeChart) costByTypeChart.destroy();
            const ctx1 = document.getElementById('costByTypeChart').getContext('2d');
            costByTypeChart = new Chart(ctx1, {
                type: 'bar', data: { labels: types, datasets: [{ label: 'Costo total (USD)', data: costs, backgroundColor: '#2D6A4F', borderRadius: 6 }] },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } } }
            });
            // Tendencia mensual (agrupar por mes)
            const monthly = new Map();
            labores.forEach(l => {
                if (l.costo && l.fecha) {
                    const month = l.fecha.substring(0,7); // YYYY-MM
                    monthly.set(month, (monthly.get(month) || 0) + l.costo);
                }
            });
            const months = Array.from(monthly.keys()).sort();
            const monthlyCosts = months.map(m => monthly.get(m));
            if (monthlyTrendChart) monthlyTrendChart.destroy();
            const ctx2 = document.getElementById('monthlyTrendChart').getContext('2d');
            monthlyTrendChart = new Chart(ctx2, {
                type: 'line', data: { labels: months, datasets: [{ label: 'Costo mensual (USD)', data: monthlyCosts, borderColor: '#2D6A4F', fill: true, tension: 0.3 }] },
                options: { responsive: true, maintainAspectRatio: true }
            });
        }

        // Alertas IA: recomendar labores próximas según estado de cultivos
        function generateAlertsIA() {
            const container = document.getElementById('iaAlertsContainer');
            const today = new Date().toISOString().split('T')[0];
            const recomendaciones = [];
            // Buscar cultivos en crecimiento sin labores recientes (simulado)
            cultivosDisponibles.forEach(crop => {
                const laboresCultivo = labores.filter(l => l.cultivoId === crop.id && l.fecha >= today);
                if (crop.estado === "En crecimiento" && laboresCultivo.length === 0) {
                    recomendaciones.push(`🔔 <strong>${crop.nombre}</strong>: No hay labores registradas en los últimos días. IA recomienda programar riego o monitoreo.`);
                }
            });
            // Próximas labores pendientes
            const pendingSoon = labores.filter(l => l.estado === "Pendiente" && l.fecha >= today).sort((a,b)=>a.fecha.localeCompare(b.fecha)).slice(0,3);
            pendingSoon.forEach(l => {
                recomendaciones.push(`⏰ <strong>${l.tipo}</strong> en ${getCropName(l.cultivoId)} programada para ${l.fecha} · Costo estimado $${l.costo?.toFixed(2) || 'N/A'}`);
            });
            if (recomendaciones.length === 0) {
                container.innerHTML = '<p class="text-gray-600"><i class="fas fa-check-circle text-green-500"></i> No hay alertas activas. Todas las labores están al día.</p>';
            } else {
                container.innerHTML = recomendaciones.map(r => `<div class="bg-white/70 rounded-lg p-2"><i class="fas fa-robot text-amber-600 mr-2"></i> ${r}</div>`).join('');
            }
        }

        // Renderizar labores con filtros
        function renderLabors() {
            const typeFilter = document.getElementById('filterLaborType').value;
            const cropFilter = document.getElementById('filterCrop').value;
            const statusFilter = document.getElementById('filterStatus').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;

            let filtered = [...labores];
            if (typeFilter) filtered = filtered.filter(l => l.tipo === typeFilter);
            if (cropFilter) filtered = filtered.filter(l => l.cultivoId === parseInt(cropFilter));
            if (statusFilter) filtered = filtered.filter(l => l.estado === statusFilter);
            if (dateFrom) filtered = filtered.filter(l => l.fecha >= dateFrom);
            if (dateTo) filtered = filtered.filter(l => l.fecha <= dateTo);

            const grid = document.getElementById('laborsGrid');
            if (filtered.length === 0) {
                grid.innerHTML = `<div class="col-span-full text-center py-12 text-gray-400"><i class="fas fa-clipboard-list text-4xl mb-2"></i><p>No hay labores con esos filtros</p></div>`;
                return;
            }

            grid.innerHTML = filtered.map(labor => {
                const cropName = getCropName(labor.cultivoId);
                const statusColor = {
                    "Completada": "bg-green-100 text-green-800",
                    "Pendiente": "bg-yellow-100 text-yellow-800",
                    "En progreso": "bg-blue-100 text-blue-800"
                }[labor.estado] || "bg-gray-100";
                // Alerta inteligente individual (si está pendiente y fecha es hoy o pasada)
                let alertBadge = "";
                if (labor.estado === "Pendiente" && labor.fecha < new Date().toISOString().split('T')[0]) {
                    alertBadge = `<span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full ml-2"><i class="fas fa-exclamation-circle"></i> Atrasada</span>`;
                } else if (labor.estado === "Pendiente" && labor.fecha === new Date().toISOString().split('T')[0]) {
                    alertBadge = `<span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full ml-2"><i class="fas fa-bell"></i> Hoy</span>`;
                }
                return `
                <div class="bg-white rounded-2xl shadow-md overflow-hidden card-hover transition border border-gray-100">
                    <div class="p-5">
                        <div class="flex justify-between items-start">
                            <div><h3 class="font-bold text-lg text-gray-800"><i class="fas fa-tractor text-agri-green mr-1"></i> ${labor.tipo}</h3></div>
                            <span class="status-badge ${statusColor}">${labor.estado} ${alertBadge}</span>
                        </div>
                        <p class="text-gray-500 text-sm mt-1"><i class="fas fa-seedling mr-1"></i> Cultivo: ${cropName}</p>
                        <div class="grid grid-cols-2 gap-2 mt-3 text-sm">
                            <div><i class="fas fa-calendar-day"></i> Fecha: ${labor.fecha}</div>
                            <div><i class="fas fa-dollar-sign"></i> Costo: $${(labor.costo || 0).toFixed(2)}</div>
                        </div>
                        ${labor.notas ? `<div class="mt-2 text-xs text-gray-500"><i class="fas fa-pen"></i> ${labor.notas}</div>` : ''}
                        <div class="flex justify-end space-x-3 mt-3 pt-2 border-t border-gray-100">
                            <button onclick="editarLabor(${labor.id})" class="text-blue-600 hover:text-blue-800 text-sm"><i class="fas fa-edit"></i> Editar</button>
                            <button onclick="eliminarLabor(${labor.id})" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash-alt"></i> Eliminar</button>
                        </div>
                    </div>
                </div>`;
            }).join('');
        }

        // CRUD Modal
        let editandoLaborId = null;
        const modal = document.getElementById('laborModal');
        function openModal(labor = null) {
            const cropSelect = document.getElementById('laborCropId');
            cropSelect.innerHTML = cultivosDisponibles.map(c => `<option value="${c.id}">${c.nombre}</option>`).join('');
            if (labor) {
                editandoLaborId = labor.id;
                document.getElementById('laborType').value = labor.tipo;
                document.getElementById('laborCropId').value = labor.cultivoId;
                document.getElementById('laborDate').value = labor.fecha;
                document.getElementById('laborCost').value = labor.costo;
                document.getElementById('laborStatus').value = labor.estado;
                document.getElementById('laborNotes').value = labor.notas || '';
                document.querySelector('#laborModal h3').innerHTML = '<i class="fas fa-edit"></i> Editar Labor';
            } else {
                editandoLaborId = null;
                document.getElementById('laborForm').reset();
                document.getElementById('laborDate').value = new Date().toISOString().split('T')[0];
                document.querySelector('#laborModal h3').innerHTML = '<i class="fas fa-tasks"></i> Nueva Labor';
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

        document.getElementById('openModalBtn').addEventListener('click', () => openModal());
        document.getElementById('closeModalBtn').addEventListener('click', closeModal);
        document.getElementById('cancelModalBtn').addEventListener('click', closeModal);

        document.getElementById('laborForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const nuevaLabor = {
                id: editandoLaborId || Date.now(),
                tipo: document.getElementById('laborType').value,
                cultivoId: parseInt(document.getElementById('laborCropId').value),
                fecha: document.getElementById('laborDate').value,
                costo: parseFloat(document.getElementById('laborCost').value) || 0,
                estado: document.getElementById('laborStatus').value,
                notas: document.getElementById('laborNotes').value
            };
            if (editandoLaborId) {
                const index = labores.findIndex(l => l.id === editandoLaborId);
                if (index !== -1) labores[index] = nuevaLabor;
                Swal.fire('Actualizado', 'Labor modificada correctamente', 'success');
            } else {
                labores.push(nuevaLabor);
                Swal.fire('Agregado', 'Labor registrada correctamente', 'success');
            }
            closeModal();
            renderLabors();
            updateStats();
            generateAlertsIA();
        });

        window.editarLabor = (id) => {
            const labor = labores.find(l => l.id === id);
            if (labor) openModal(labor);
        };
        window.eliminarLabor = async (id) => {
            const result = await Swal.fire({ title: '¿Eliminar labor?', text: "Esta acción no se puede revertir", icon: 'warning', showCancelButton: true });
            if (result.isConfirmed) {
                labores = labores.filter(l => l.id !== id);
                renderLabors();
                updateStats();
                generateAlertsIA();
                Swal.fire('Eliminado', '', 'success');
            }
        };

        // Llenar filtro de cultivos
        function populateCropFilter() {
            const select = document.getElementById('filterCrop');
            select.innerHTML = '<option value="">Todos los cultivos</option>' + cultivosDisponibles.map(c => `<option value="${c.id}">${c.nombre}</option>`).join('');
        }

        // Eventos filtros
        document.getElementById('filterLaborType').addEventListener('change', renderLabors);
        document.getElementById('filterCrop').addEventListener('change', renderLabors);
        document.getElementById('filterStatus').addEventListener('change', renderLabors);
        document.getElementById('filterDateFrom').addEventListener('change', renderLabors);
        document.getElementById('filterDateTo').addEventListener('change', renderLabors);
        document.getElementById('resetFiltersBtn').addEventListener('click', () => {
            document.getElementById('filterLaborType').value = '';
            document.getElementById('filterCrop').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterDateFrom').value = '';
            document.getElementById('filterDateTo').value = '';
            renderLabors();
        });

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

        // Inicializar
        function init() {
            populateCropFilter();
            renderLabors();
            updateStats();
            generateAlertsIA();
        }
        init();
    </script>
</body>
</html>