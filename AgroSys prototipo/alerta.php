<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AgroSys · Alertas Inteligentes IA</title>
    <!-- Tailwind CSS + Font Awesome + Chart.js (para mini gráfico opcional) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .sidebar-transition { transition: transform 0.3s ease-in-out; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1); }
        .modal-backdrop { background-color: rgba(0,0,0,0.5); backdrop-filter: blur(2px); }
        .alert-critical { border-left-color: #dc2626; background: #fef2f2; }
        .alert-high { border-left-color: #f97316; background: #fff7ed; }
        .alert-medium { border-left-color: #eab308; background: #fefce8; }
        .alert-low { border-left-color: #22c55e; background: #f0fdf4; }
        .alert-info { border-left-color: #3b82f6; background: #eff6ff; }
        .badge-critical { background: #dc2626; color: white; }
        .badge-high { background: #f97316; color: white; }
        .badge-medium { background: #eab308; color: white; }
        .badge-low { background: #22c55e; color: white; }
        .badge-info { background: #3b82f6; color: white; }
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
                include "header.php"
            ?>

            <main class="p-6 md:p-8">
                <!-- Tarjetas de resumen -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between border-l-4 border-agri-green">
                        <div><p class="text-gray-500 text-sm">Total Alertas</p><p id="totalAlerts" class="text-3xl font-bold">0</p></div>
                        <i class="fas fa-bell text-4xl text-agri-green opacity-70"></i>
                    </div>
                    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between border-l-4 border-red-500">
                        <div><p class="text-gray-500 text-sm">No leídas</p><p id="unreadCount" class="text-3xl font-bold">0</p></div>
                        <i class="fas fa-envelope text-4xl text-red-500 opacity-70"></i>
                    </div>
                    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between border-l-4 border-orange-500">
                        <div><p class="text-gray-500 text-sm">Críticas</p><p id="criticalCount" class="text-3xl font-bold">0</p></div>
                        <i class="fas fa-exclamation-triangle text-4xl text-orange-500 opacity-70"></i>
                    </div>
                    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between border-l-4 border-green-500">
                        <div><p class="text-gray-500 text-sm">Resueltas</p><p id="resolvedCount" class="text-3xl font-bold">0</p></div>
                        <i class="fas fa-check-circle text-4xl text-green-500 opacity-70"></i>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                    <div class="flex flex-wrap gap-3">
                        <select id="filterLevel" class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm">
                            <option value="">Todos los niveles</option>
                            <option value="critical">Crítica</option>
                            <option value="high">Alta</option>
                            <option value="medium">Media</option>
                            <option value="low">Baja</option>
                            <option value="info">Informativa</option>
                        </select>
                        <select id="filterType" class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm">
                            <option value="">Todos los tipos</option>
                            <option value="clima">Clima</option>
                            <option value="plagas">Plagas</option>
                            <option value="labores">Labores</option>
                            <option value="cosecha">Cosecha</option>
                            <option value="sistema">Sistema</option>
                        </select>
                        <button id="resetFiltersBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm transition"><i class="fas fa-undo-alt mr-1"></i> Reset</button>
                    </div>
                    <button id="markAllReadBtn" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg text-sm transition"><i class="fas fa-check-double"></i> Marcar todas como leídas</button>
                </div>

                <!-- Listado de alertas -->
                <div id="alertsContainer" class="space-y-4">
                    <!-- JS dinámico -->
                    <div class="text-center py-10"><i class="fas fa-spinner fa-spin text-3xl text-agri-green"></i> Cargando alertas...</div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal de detalles de alerta -->
    <div id="alertModal" class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop transition-all">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 relative">
            <button id="closeAlertModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            <div class="flex items-center gap-3 mb-3">
                <i id="modalIcon" class="fas fa-bell text-2xl"></i>
                <h3 id="modalTitle" class="text-xl font-bold text-gray-800">Título</h3>
            </div>
            <p id="modalDescription" class="text-gray-600 text-sm mb-3"></p>
            <div class="bg-gray-50 p-3 rounded-lg mb-3 text-sm">
                <p><strong>Fecha:</strong> <span id="modalDate"></span></p>
                <p><strong>Tipo:</strong> <span id="modalType"></span></p>
                <p><strong>Nivel:</strong> <span id="modalLevel"></span></p>
                <p><strong>Fuente:</strong> <span id="modalSource"></span></p>
            </div>
            <div class="border-t pt-3 mt-2">
                <p class="text-sm font-semibold text-gray-700">Acción sugerida por IA:</p>
                <p id="modalAction" class="text-sm text-gray-600 mt-1"></p>
            </div>
            <div class="flex justify-end mt-5 space-x-3">
                <button id="resolveFromModalBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Marcar como resuelta</button>
                <button id="closeModalBtn" class="px-4 py-2 border rounded-lg">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        // ========== DATOS SIMULADOS DE ALERTAS ==========
        // Terrenos y cultivos de referencia (para generar alertas realistas)
        const terrenos = [
            { id: 1, nombre: "Parcela El Prado", ubicacion: "Valle Central" },
            { id: 2, nombre: "Fundo Los Álamos", ubicacion: "Zona Norte" },
            { id: 3, nombre: "Huerta Santa Fe", ubicacion: "Costa Central" }
        ];
        const cultivos = [
            { id: 1, nombre: "Maíz", terrenoId: 1, estado: "En crecimiento" },
            { id: 2, nombre: "Tomate", terrenoId: 2, estado: "En crecimiento" },
            { id: 3, nombre: "Papa", terrenoId: 3, estado: "Planificado" }
        ];

        // Alertas iniciales (simuladas)
        let alertas = [
            { id: 1, titulo: "Riesgo alto de tizón tardío", descripcion: "Condiciones de humedad >85% y temperatura 22°C en los últimos 3 días. Aplicar fungicida preventivo.", nivel: "critical", tipo: "plagas", fuente: "IA · Predicción climática", fecha: "2025-03-18 08:30", leida: false, resuelta: false, accion: "Aplicar mancozeb 2kg/ha antes de 24h" },
            { id: 2, titulo: "Alerta de helada para esta noche", descripcion: "Pronóstico de temperatura bajo cero en el sector norte. Proteger cultivos sensibles.", nivel: "high", tipo: "clima", fuente: "API Climática + IA", fecha: "2025-03-18 10:15", leida: false, resuelta: false, accion: "Activar riego por aspersión nocturno o usar mallas antigranizo" },
            { id: 3, titulo: "Próxima cosecha de tomate", descripcion: "El cultivo de tomate en Fundo Los Álamos alcanzó madurez óptima. Programar cosecha en los próximos 5 días.", nivel: "medium", tipo: "cosecha", fuente: "IA · Análisis de madurez", fecha: "2025-03-17 14:20", leida: true, resuelta: false, accion: "Organizar cuadrilla de cosecha y revisar demanda del mercado" },
            { id: 4, titulo: "Fertilización recomendada para maíz", descripcion: "Fase de crecimiento vegetativo. IA sugiere aplicación de nitrógeno.", nivel: "low", tipo: "labores", fuente: "Recomendación IA", fecha: "2025-03-16 09:00", leida: true, resuelta: true, accion: "Aplicar 150 kg/ha de urea" },
            { id: 5, titulo: "Detección temprana de pulgón", descripcion: "Imágenes satelitales indican focos iniciales de pulgón en parcela norte. Monitorear.", nivel: "high", tipo: "plagas", fuente: "Visión IA", fecha: "2025-03-15 11:45", leida: false, resuelta: false, accion: "Inspeccionar 10 plantas por hectárea. Aplicar jabón potásico si se confirma." }
        ];

        // Función para generar nueva alerta aleatoria (simula IA en tiempo real)
        function generateRandomAlert() {
            const tipos = ["clima", "plagas", "labores", "cosecha", "sistema"];
            const niveles = ["critical", "high", "medium", "low", "info"];
            const titulosPorTipo = {
                clima: ["Tormenta eléctrica inminente", "Alerta de granizo", "Vientos fuertes >50 km/h", "Sequía prolongada"],
                plagas: ["Posible brote de roya", "Aviso de mosca blanca", "Riesgo de gusano cogollero", "Ácaros detectados"],
                labores: ["Riego programado para mañana", "Fumigación recomendada", "Mantenimiento de maquinaria", "Fertilización de fondo"],
                cosecha: ["Punto óptimo de cosecha alcanzado", "Rendimiento estimado superior al promedio", "Pérdida por maduración avanzada"],
                sistema: ["Actualización de IA disponible", "Sincronización de datos offline completada", "Nuevo informe generado"]
            };
            const tipo = tipos[Math.floor(Math.random() * tipos.length)];
            const nivel = niveles[Math.floor(Math.random() * niveles.length)];
            const titulo = titulosPorTipo[tipo][Math.floor(Math.random() * titulosPorTipo[tipo].length)];
            const descripcionBase = `Generado por IA basado en análisis de datos recientes. Se recomienda acción inmediata según nivel de alerta.`;
            const accionBase = "Revisar panel de control para más detalles y ejecutar plan de contingencia.";
            const now = new Date();
            const fechaStr = `${now.toISOString().slice(0,10)} ${now.getHours()}:${now.getMinutes()}`;
            return {
                id: Date.now(),
                titulo: titulo,
                descripcion: descripcionBase,
                nivel: nivel,
                tipo: tipo,
                fuente: "Motor IA · Alertas Automáticas",
                fecha: fechaStr,
                leida: false,
                resuelta: false,
                accion: accionBase
            };
        }

        // Intervalo para generar alerta aleatoria cada 45 segundos (simula IA)
        setInterval(() => {
            const newAlert = generateRandomAlert();
            alertas.unshift(newAlert);
            renderAlerts();
            updateStats();
            // Notificación visual (si está permitido)
            if (Notification.permission === "granted") {
                new Notification("Nueva alerta IA: " + newAlert.titulo);
            }
            Swal.fire({
                title: "Nueva alerta inteligente",
                text: newAlert.titulo,
                icon: "info",
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 4000
            });
        }, 45000);

        // Solicitar permiso para notificaciones al cargar
        if (Notification.permission !== "denied") {
            Notification.requestPermission();
        }

        // Función para actualizar estadísticas
        function updateStats() {
            const total = alertas.length;
            const unread = alertas.filter(a => !a.leida && !a.resuelta).length;
            const critical = alertas.filter(a => a.nivel === "critical" && !a.resuelta).length;
            const resolved = alertas.filter(a => a.resuelta).length;
            document.getElementById('totalAlerts').innerText = total;
            document.getElementById('unreadCount').innerText = unread;
            document.getElementById('criticalCount').innerText = critical;
            document.getElementById('resolvedCount').innerText = resolved;
            document.getElementById('unreadBadge').innerText = unread > 99 ? "99+" : unread;
            if (unread === 0) document.getElementById('unreadBadge').classList.add('hidden');
            else document.getElementById('unreadBadge').classList.remove('hidden');
        }

        // Renderizar alertas con filtros
        function renderAlerts() {
            const levelFilter = document.getElementById('filterLevel').value;
            const typeFilter = document.getElementById('filterType').value;
            let filtered = alertas.filter(a => !a.resuelta); // por defecto mostramos no resueltas
            // Si queremos mostrar también resueltas? mejo mostrar no resueltas primero, pero podemos incluir opción. Por simplicidad mostramos todas pero resaltamos resueltas
            filtered = alertas; // mostrar todas, pero las resueltas con estilo diferente

            if (levelFilter) filtered = filtered.filter(a => a.nivel === levelFilter);
            if (typeFilter) filtered = filtered.filter(a => a.tipo === typeFilter);

            const container = document.getElementById('alertsContainer');
            if (filtered.length === 0) {
                container.innerHTML = `<div class="bg-white rounded-2xl shadow p-8 text-center text-gray-400"><i class="fas fa-check-circle text-4xl mb-2"></i><p>No hay alertas con estos filtros</p></div>`;
                return;
            }

            container.innerHTML = filtered.map(alert => {
                let borderClass = "";
                let badgeClass = "";
                switch(alert.nivel) {
                    case 'critical': borderClass = 'alert-critical'; badgeClass = 'badge-critical'; break;
                    case 'high': borderClass = 'alert-high'; badgeClass = 'badge-high'; break;
                    case 'medium': borderClass = 'alert-medium'; badgeClass = 'badge-medium'; break;
                    case 'low': borderClass = 'alert-low'; badgeClass = 'badge-low'; break;
                    default: borderClass = 'alert-info'; badgeClass = 'badge-info';
                }
                const resolvedClass = alert.resuelta ? 'opacity-60 bg-gray-100' : '';
                const leidoIcon = alert.leida ? '<i class="fas fa-envelope-open text-gray-400"></i>' : '<i class="fas fa-envelope text-blue-500"></i>';
                return `
                <div class="bg-white rounded-2xl shadow-sm border-l-4 p-5 transition ${borderClass} ${resolvedClass} card-hover">
                    <div class="flex justify-between items-start">
                        <div class="flex items-start gap-3">
                            <div class="mt-1">${leidoIcon}</div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-gray-800">${alert.titulo}</h3>
                                    <span class="text-xs px-2 py-0.5 rounded-full ${badgeClass}">${alert.nivel.toUpperCase()}</span>
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">${alert.tipo}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">${alert.descripcion.substring(0, 120)}${alert.descripcion.length > 120 ? '...' : ''}</p>
                                <div class="flex gap-4 mt-2 text-xs text-gray-400">
                                    <span><i class="far fa-calendar-alt"></i> ${alert.fecha}</span>
                                    <span><i class="fas fa-microchip"></i> ${alert.fuente}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="verDetalle(${alert.id})" class="text-agri-green hover:text-green-700 text-sm"><i class="fas fa-eye"></i></button>
                            ${!alert.resuelta ? `<button onclick="marcarResuelta(${alert.id})" class="text-green-600 hover:text-green-800 text-sm"><i class="fas fa-check-circle"></i></button>` : ''}
                            ${!alert.leida && !alert.resuelta ? `<button onclick="marcarLeida(${alert.id})" class="text-blue-600 hover:text-blue-800 text-sm"><i class="fas fa-check-double"></i></button>` : ''}
                        </div>
                    </div>
                </div>`;
            }).join('');
        }

        // Marcar alerta como leída
        window.marcarLeida = (id) => {
            const alert = alertas.find(a => a.id === id);
            if (alert && !alert.leida) {
                alert.leida = true;
                renderAlerts();
                updateStats();
                Swal.fire('Marcada como leída', '', 'success');
            }
        };

        // Marcar como resuelta
        window.marcarResuelta = (id) => {
            const alert = alertas.find(a => a.id === id);
            if (alert && !alert.resuelta) {
                alert.resuelta = true;
                alert.leida = true;
                renderAlerts();
                updateStats();
                Swal.fire('Alerta resuelta', 'La alerta se ha marcado como resuelta', 'success');
            }
        };

        // Ver detalle en modal
        let currentModalAlertId = null;
        window.verDetalle = (id) => {
            const alert = alertas.find(a => a.id === id);
            if (!alert) return;
            currentModalAlertId = id;
            document.getElementById('modalTitle').innerText = alert.titulo;
            document.getElementById('modalDescription').innerText = alert.descripcion;
            document.getElementById('modalDate').innerText = alert.fecha;
            document.getElementById('modalType').innerText = alert.tipo;
            document.getElementById('modalLevel').innerText = alert.nivel.toUpperCase();
            document.getElementById('modalSource').innerText = alert.fuente;
            document.getElementById('modalAction').innerText = alert.accion;
            let iconClass = "fas fa-bell";
            if (alert.tipo === 'plagas') iconClass = "fas fa-bug";
            else if (alert.tipo === 'clima') iconClass = "fas fa-cloud-sun-rain";
            else if (alert.tipo === 'labores') iconClass = "fas fa-tasks";
            else if (alert.tipo === 'cosecha') iconClass = "fas fa-seedling";
            document.getElementById('modalIcon').className = iconClass + " text-2xl text-agri-green";
            const modal = document.getElementById('alertModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // Si no estaba leída, marcarla como leída al abrir
            if (!alert.leida && !alert.resuelta) {
                alert.leida = true;
                renderAlerts();
                updateStats();
            }
        };

        // Cerrar modal
        document.getElementById('closeAlertModal').addEventListener('click', () => {
            document.getElementById('alertModal').classList.add('hidden');
        });
        document.getElementById('closeModalBtn').addEventListener('click', () => {
            document.getElementById('alertModal').classList.add('hidden');
        });
        document.getElementById('resolveFromModalBtn').addEventListener('click', () => {
            if (currentModalAlertId) {
                marcarResuelta(currentModalAlertId);
                document.getElementById('alertModal').classList.add('hidden');
            }
        });

        // Marcar todas como leídas (no resueltas)
        document.getElementById('markAllReadBtn').addEventListener('click', () => {
            let count = 0;
            alertas.forEach(a => {
                if (!a.leida && !a.resuelta) {
                    a.leida = true;
                    count++;
                }
            });
            renderAlerts();
            updateStats();
            Swal.fire(`Se marcaron ${count} alertas como leídas`, '', 'info');
        });

        // Filtros
        document.getElementById('filterLevel').addEventListener('change', renderAlerts);
        document.getElementById('filterType').addEventListener('change', renderAlerts);
        document.getElementById('resetFiltersBtn').addEventListener('click', () => {
            document.getElementById('filterLevel').value = '';
            document.getElementById('filterType').value = '';
            renderAlerts();
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

        // Inicialización
        function init() {
            renderAlerts();
            updateStats();
        }
        init();
    </script>
</body>
</html>