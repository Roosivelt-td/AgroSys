<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AgroSys · Gestión de Cultivos</title>
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
                        <select id="filterCropType" class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-agri-green">
                            <option value="">Todos los cultivos</option>
                            <option value="Maíz">Maíz</option>
                            <option value="Tomate">Tomate</option>
                            <option value="Papa">Papa</option>
                            <option value="Palta">Palta</option>
                            <option value="Fresa">Fresa</option>
                        </select>
                        <select id="filterStatus" class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm">
                            <option value="">Todos los estados</option>
                            <option value="Planificado">Planificado</option>
                            <option value="En crecimiento">En crecimiento</option>
                            <option value="Cosechado">Cosechado</option>
                            <option value="Perdido">Perdido</option>
                        </select>
                        <input type="date" id="filterDateFrom" class="border rounded-lg px-3 py-2 text-sm" placeholder="Desde">
                        <input type="date" id="filterDateTo" class="border rounded-lg px-3 py-2 text-sm" placeholder="Hasta">
                        <button id="resetFiltersBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm transition"><i class="fas fa-undo-alt mr-1"></i> Reset</button>
                    </div>
                    <button id="openModalBtn" class="bg-agri-green hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow transition flex items-center gap-2" style="background-color:#2D6A4F"><i class="fas fa-plus"></i> Nuevo Cultivo</button>
                </div>

                <!-- Tarjetas de resumen + gráfico -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between border-l-4 border-agri-green">
                        <div><p class="text-gray-500 text-sm">Total Cultivos</p><p id="totalCropsCount" class="text-3xl font-bold">0</p></div>
                        <i class="fas fa-seedling text-4xl text-agri-green opacity-70"></i>
                    </div>
                    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between border-l-4 border-blue-500">
                        <div><p class="text-gray-500 text-sm">Área Sembrada (ha)</p><p id="totalAreaSown" class="text-3xl font-bold">0</p></div>
                        <i class="fas fa-chart-line text-4xl text-blue-500 opacity-70"></i>
                    </div>
                    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between border-l-4 border-yellow-500">
                        <div><p class="text-gray-500 text-sm">Rendimiento Promedio (kg/ha)</p><p id="avgYield" class="text-3xl font-bold">0</p></div>
                        <i class="fas fa-trophy text-4xl text-yellow-500 opacity-70"></i>
                    </div>
                </div>

                <!-- Gráfico de rendimiento histórico -->
                <div class="bg-white rounded-2xl shadow p-5 mb-8">
                    <h2 class="font-semibold text-gray-700 mb-4"><i class="fas fa-chart-simple text-agri-green mr-2"></i> Rendimiento histórico por cultivo (kg/ha)</h2>
                    <canvas id="yieldChart" height="180" style="max-height: 280px; width: 100%;"></canvas>
                </div>

                <!-- LISTADO DE CULTIVOS (tarjetas) -->
                <h2 class="text-lg font-semibold text-gray-700 mb-4"><i class="fas fa-list text-agri-green mr-2"></i> Mis cultivos</h2>
                <div id="cropsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <div class="col-span-full flex justify-center py-10"><i class="fas fa-spinner fa-spin text-3xl text-agri-green"></i></div>
                </div>
            </main>
        </div>
    </div>

    <!-- MODAL PARA AGREGAR/EDITAR CULTIVO -->
    <div id="cropModal" class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop transition-all">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6 relative max-h-[90vh] overflow-y-auto">
            <button id="closeModalBtn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            <h3 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-seedling"></i> Nuevo Cultivo</h3>
            <form id="cropForm">
                <div class="mb-3"><label class="block text-sm font-medium">Nombre/Tipo de cultivo *</label><input type="text" id="cropName" required class="w-full border rounded-lg px-3 py-2 mt-1"></div>
                <div class="mb-3"><label class="block text-sm font-medium">Terreno asociado *</label><select id="cropLandId" required class="w-full border rounded-lg px-3 py-2"></select></div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div><label>Fecha de siembra *</label><input type="date" id="cropPlantingDate" required class="w-full border rounded-lg px-3 py-2"></div>
                    <div><label>Fecha estimada cosecha</label><input type="date" id="cropHarvestDate" class="w-full border rounded-lg px-3 py-2"></div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div><label>Área sembrada (ha) *</label><input type="number" step="0.1" id="cropArea" required class="w-full border rounded-lg px-3 py-2"></div>
                    <div><label>Rendimiento esperado (kg/ha)</label><input type="number" step="100" id="cropYield" class="w-full border rounded-lg px-3 py-2"></div>
                </div>
                <div class="mb-3"><label>Estado del cultivo *</label><select id="cropStatus" class="w-full border rounded-lg px-3 py-2"><option>Planificado</option><option>En crecimiento</option><option>Cosechado</option><option>Perdido</option></select></div>
                <div class="mb-3"><label>Observaciones / Notas</label><textarea id="cropNotes" rows="2" class="w-full border rounded-lg px-3 py-2"></textarea></div>
                <div class="flex justify-end space-x-3 mt-5"><button type="button" id="cancelModalBtn" class="px-4 py-2 border rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-agri-green text-white rounded-lg" style="background-color:#2D6A4F">Guardar</button></div>
            </form>
        </div>
    </div>

    <script>
        // ========== DATOS SIMULADOS ==========
        // Terrenos disponibles (similares a los del módulo anterior)
        const terrenosDisponibles = [
            { id: 1, nombre: "Parcela El Prado", ubicacion: "Valle Central", area: 8.5 },
            { id: 2, nombre: "Fundo Los Álamos", ubicacion: "Zona Norte", area: 12.3 },
            { id: 3, nombre: "Huerta Santa Fe", ubicacion: "Costa Central", area: 3.2 },
            { id: 4, nombre: "Agrícola La Esperanza", ubicacion: "Valle Verde", area: 20.0 }
        ];

        // Cultivos iniciales
        let cultivos = [
            { id: 1, nombre: "Maíz", terrenoId: 1, fechaSiembra: "2025-02-10", fechaCosechaEstimada: "2025-06-15", area: 5.2, rendimientoEsperado: 8500, estado: "En crecimiento", notas: "Variedad DK-390", rendimientoReal: null },
            { id: 2, nombre: "Tomate", terrenoId: 2, fechaSiembra: "2025-01-20", fechaCosechaEstimada: "2025-04-25", area: 3.0, rendimientoEsperado: 42000, estado: "En crecimiento", notas: "Riego por goteo", rendimientoReal: null },
            { id: 3, nombre: "Papa", terrenoId: 3, fechaSiembra: "2025-03-01", fechaCosechaEstimada: "2025-07-10", area: 2.5, rendimientoEsperado: 28000, estado: "Planificado", notas: "Variedad única", rendimientoReal: null },
            { id: 4, nombre: "Palta", terrenoId: 4, fechaSiembra: "2024-11-05", fechaCosechaEstimada: "2025-09-20", area: 15.0, rendimientoEsperado: 12000, estado: "En crecimiento", notas: "Hass", rendimientoReal: null },
            { id: 5, nombre: "Fresa", terrenoId: 1, fechaSiembra: "2025-03-15", fechaCosechaEstimada: "2025-05-30", area: 1.2, rendimientoEsperado: 25000, estado: "Planificado", notas: "Bajo invernadero", rendimientoReal: null }
        ];

        // Datos para gráfico de rendimiento histórico (simulado)
        const rendimientoHistorico = {
            "Maíz": [8200, 8400, 8600, 8500, 8700],
            "Tomate": [41000, 43000, 42000, 44000, 42500],
            "Papa": [27000, 28500, 28000, 29000, 27500],
            "Palta": [11500, 11800, 12000, 12200, 11900]
        };
        let yieldChart;

        // Helper: actualizar estadísticas y gráfico
        function updateStats() {
            const total = cultivos.length;
            const areaTotal = cultivos.reduce((sum, c) => sum + c.area, 0);
            const rendimientoPromedio = cultivos.reduce((sum, c) => sum + (c.rendimientoEsperado || 0), 0) / (total || 1);
            document.getElementById('totalCropsCount').innerText = total;
            document.getElementById('totalAreaSown').innerText = areaTotal.toFixed(1);
            document.getElementById('avgYield').innerText = Math.round(rendimientoPromedio).toLocaleString();
        }

        // Renderizar tarjetas de cultivos con filtros
        function renderCrops() {
            const tipoFilter = document.getElementById('filterCropType').value;
            const statusFilter = document.getElementById('filterStatus').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;

            let filtered = [...cultivos];
            if (tipoFilter) filtered = filtered.filter(c => c.nombre === tipoFilter);
            if (statusFilter) filtered = filtered.filter(c => c.estado === statusFilter);
            if (dateFrom) filtered = filtered.filter(c => c.fechaSiembra >= dateFrom);
            if (dateTo) filtered = filtered.filter(c => c.fechaSiembra <= dateTo);

            const grid = document.getElementById('cropsGrid');
            if (filtered.length === 0) {
                grid.innerHTML = `<div class="col-span-full text-center py-12 text-gray-400"><i class="fas fa-leaf text-4xl mb-2"></i><p>No hay cultivos con esos filtros</p></div>`;
                return;
            }

            grid.innerHTML = filtered.map(cultivo => {
                const terreno = terrenosDisponibles.find(t => t.id === cultivo.terrenoId);
                const nombreTerreno = terreno ? terreno.nombre : "Sin terreno";
                // Calcular días restantes para cosecha (simulado)
                let alertaIA = "";
                if (cultivo.estado === "En crecimiento") {
                    const hoy = new Date();
                    const cosecha = new Date(cultivo.fechaCosechaEstimada);
                    const diffDays = Math.ceil((cosecha - hoy) / (1000 * 60 * 60 * 24));
                    if (diffDays <= 15 && diffDays > 0) alertaIA = `<span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full"><i class="fas fa-clock"></i> Cosecha próxima (${diffDays} días)</span>`;
                    else if (diffDays < 0) alertaIA = `<span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full"><i class="fas fa-exclamation-triangle"></i> Cosecha atrasada</span>`;
                    else alertaIA = `<span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full"><i class="fas fa-chart-line"></i> IA: monitoreo óptimo</span>`;
                } else if (cultivo.estado === "Planificado") {
                    alertaIA = `<span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full"><i class="fas fa-robot"></i> Recomendación: preparar suelo</span>`;
                } else {
                    alertaIA = `<span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">Sin alertas</span>`;
                }

                const statusColor = {
                    "Planificado": "bg-gray-100 text-gray-800",
                    "En crecimiento": "bg-green-100 text-green-800",
                    "Cosechado": "bg-blue-100 text-blue-800",
                    "Perdido": "bg-red-100 text-red-800"
                }[cultivo.estado] || "bg-gray-100";

                return `
                <div class="bg-white rounded-2xl shadow-md overflow-hidden card-hover transition border border-gray-100">
                    <div class="p-5">
                        <div class="flex justify-between items-start">
                            <h3 class="font-bold text-lg text-gray-800"><i class="fas fa-seedling text-agri-green mr-1"></i> ${cultivo.nombre}</h3>
                            <span class="status-badge ${statusColor}">${cultivo.estado}</span>
                        </div>
                        <p class="text-gray-500 text-sm mt-1"><i class="fas fa-map-pin mr-1"></i> ${nombreTerreno} (${terreno?.area || 0} ha)</p>
                        <div class="grid grid-cols-2 gap-2 mt-3 text-sm">
                            <div><i class="fas fa-calendar-alt"></i> Siembra: ${cultivo.fechaSiembra}</div>
                            <div><i class="fas fa-calendar-check"></i> Cosecha est.: ${cultivo.fechaCosechaEstimada}</div>
                            <div><i class="fas fa-chart-simple"></i> Área: ${cultivo.area} ha</div>
                            <div><i class="fas fa-weight-hanging"></i> Rend. esp.: ${(cultivo.rendimientoEsperado || 0).toLocaleString()} kg/ha</div>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2 items-center justify-between">
                            ${alertaIA}
                            <div class="flex space-x-2">
                                <button onclick="editarCultivo(${cultivo.id})" class="text-blue-600 hover:text-blue-800 text-sm"><i class="fas fa-edit"></i></button>
                                <button onclick="eliminarCultivo(${cultivo.id})" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash-alt"></i></button>
                                <button onclick="verDetalle(${cultivo.id})" class="text-gray-500 text-sm"><i class="fas fa-info-circle"></i></button>
                            </div>
                        </div>
                    </div>
                </div>`;
            }).join('');
        }

        // Actualizar gráfico con los rendimientos esperados de los cultivos existentes (agrupados)
        function updateChart() {
            const ctx = document.getElementById('yieldChart').getContext('2d');
            const cultivosUnicos = [...new Map(cultivos.map(c => [c.nombre, c])).values()];
            const labels = cultivosUnicos.map(c => c.nombre);
            const data = cultivosUnicos.map(c => c.rendimientoEsperado || 0);
            if (yieldChart) yieldChart.destroy();
            yieldChart = new Chart(ctx, {
                type: 'bar',
                data: { labels, datasets: [{ label: 'Rendimiento esperado (kg/ha)', data, backgroundColor: '#2D6A4F', borderRadius: 8 }] },
                options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } } }
            });
        }

        // CRUD Modal
        let editandoId = null;
        const modal = document.getElementById('cropModal');
        function openModal(cultivo = null) {
            // Llenar select de terrenos
            const landSelect = document.getElementById('cropLandId');
            landSelect.innerHTML = terrenosDisponibles.map(t => `<option value="${t.id}">${t.nombre} (${t.area} ha)</option>`).join('');
            if (cultivo) {
                editandoId = cultivo.id;
                document.getElementById('cropName').value = cultivo.nombre;
                document.getElementById('cropLandId').value = cultivo.terrenoId;
                document.getElementById('cropPlantingDate').value = cultivo.fechaSiembra;
                document.getElementById('cropHarvestDate').value = cultivo.fechaCosechaEstimada;
                document.getElementById('cropArea').value = cultivo.area;
                document.getElementById('cropYield').value = cultivo.rendimientoEsperado;
                document.getElementById('cropStatus').value = cultivo.estado;
                document.getElementById('cropNotes').value = cultivo.notas || '';
                document.querySelector('#cropModal h3').innerHTML = '<i class="fas fa-edit"></i> Editar Cultivo';
            } else {
                editandoId = null;
                document.getElementById('cropForm').reset();
                document.querySelector('#cropModal h3').innerHTML = '<i class="fas fa-seedling"></i> Nuevo Cultivo';
                // Sugerir fecha de cosecha por defecto +3 meses
                const today = new Date();
                const threeMonths = new Date(today.setMonth(today.getMonth() + 3)).toISOString().split('T')[0];
                document.getElementById('cropHarvestDate').value = threeMonths;
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

        document.getElementById('openModalBtn').addEventListener('click', () => openModal());
        document.getElementById('closeModalBtn').addEventListener('click', closeModal);
        document.getElementById('cancelModalBtn').addEventListener('click', closeModal);

        document.getElementById('cropForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const nuevoCultivo = {
                id: editandoId || Date.now(),
                nombre: document.getElementById('cropName').value,
                terrenoId: parseInt(document.getElementById('cropLandId').value),
                fechaSiembra: document.getElementById('cropPlantingDate').value,
                fechaCosechaEstimada: document.getElementById('cropHarvestDate').value,
                area: parseFloat(document.getElementById('cropArea').value),
                rendimientoEsperado: parseInt(document.getElementById('cropYield').value) || 0,
                estado: document.getElementById('cropStatus').value,
                notas: document.getElementById('cropNotes').value,
                rendimientoReal: null
            };
            if (editandoId) {
                const index = cultivos.findIndex(c => c.id === editandoId);
                if (index !== -1) cultivos[index] = nuevoCultivo;
                Swal.fire('Actualizado', 'Cultivo modificado correctamente', 'success');
            } else {
                cultivos.push(nuevoCultivo);
                Swal.fire('Agregado', 'Nuevo cultivo registrado', 'success');
            }
            closeModal();
            renderCrops();
            updateStats();
            updateChart();
        });

        window.editarCultivo = (id) => {
            const cultivo = cultivos.find(c => c.id === id);
            if (cultivo) openModal(cultivo);
        };
        window.eliminarCultivo = async (id) => {
            const result = await Swal.fire({ title: '¿Eliminar cultivo?', text: "Esta acción no se puede revertir", icon: 'warning', showCancelButton: true });
            if (result.isConfirmed) {
                cultivos = cultivos.filter(c => c.id !== id);
                renderCrops();
                updateStats();
                updateChart();
                Swal.fire('Eliminado', '', 'success');
            }
        };
        window.verDetalle = (id) => {
            const cultivo = cultivos.find(c => c.id === id);
            Swal.fire({ title: cultivo.nombre, html: `<strong>Terreno:</strong> ${terrenosDisponibles.find(t=>t.id===cultivo.terrenoId)?.nombre}<br><strong>Siembra:</strong> ${cultivo.fechaSiembra}<br><strong>Cosecha estimada:</strong> ${cultivo.fechaCosechaEstimada}<br><strong>Área:</strong> ${cultivo.area} ha<br><strong>Rendimiento esperado:</strong> ${cultivo.rendimientoEsperado?.toLocaleString()} kg/ha<br><strong>Notas:</strong> ${cultivo.notas || 'Ninguna'}`, icon: 'info' });
        };

        // Filtros
        document.getElementById('filterCropType').addEventListener('change', renderCrops);
        document.getElementById('filterStatus').addEventListener('change', renderCrops);
        document.getElementById('filterDateFrom').addEventListener('change', renderCrops);
        document.getElementById('filterDateTo').addEventListener('change', renderCrops);
        document.getElementById('resetFiltersBtn').addEventListener('click', () => {
            document.getElementById('filterCropType').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterDateFrom').value = '';
            document.getElementById('filterDateTo').value = '';
            renderCrops();
        });

        // Sidebar y dark mode (igual que antes)
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
            renderCrops();
            updateStats();
            updateChart();
        }
        init();
    </script>
</body>
</html>