<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AgroSys · Gestión de Terrenos</title>
    <!-- Tailwind CSS + Font Awesome + Leaflet (mapa) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- SweetAlert2 opcional para notificaciones -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .sidebar-transition { transition: transform 0.3s ease-in-out; }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.1); }
        .modal-backdrop { background-color: rgba(0,0,0,0.5); backdrop-filter: blur(2px); }
        .map-container { height: 300px; border-radius: 1rem; z-index: 0; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        <!-- =============== SIDEBAR (IDÉNTICA AL DASHBOARD) =============== -->
        <?php
            include "sidebar.php"
        ?>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <!-- Header (mismo estilo) -->
            <?php
                include "header.php";
            ?>

            <main class="p-6 md:p-8">
                <!-- Barra de filtros y botón agregar -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                    <div class="flex flex-wrap gap-3">
                        <select id="filterCrop" class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-agri-green focus:border-agri-green">
                            <option value="">Todos los cultivos</option>
                            <option value="Maíz">Maíz</option>
                            <option value="Tomate">Tomate</option>
                            <option value="Papa">Papa</option>
                            <option value="Palta">Palta</option>
                        </select>
                        <select id="filterArea" class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm">
                            <option value="">Cualquier área</option>
                            <option value="0-5">Menos de 5 ha</option>
                            <option value="5-10">5 - 10 ha</option>
                            <option value="10+">Más de 10 ha</option>
                        </select>
                        <button id="resetFiltersBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm transition"><i class="fas fa-undo-alt mr-1"></i> Reset</button>
                    </div>
                    <button id="openModalBtn" class="bg-agri-green hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow transition flex items-center gap-2" style="background-color:#2D6A4F"><i class="fas fa-plus"></i> Nuevo Terreno</button>
                </div>

                <!-- Mapa + Listado de terrenos en grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Mapa Leaflet (columna 2/3) -->
                    <div class="lg:col-span-2 bg-white rounded-2xl shadow p-3">
                        <div id="map" class="map-container w-full rounded-xl"></div>
                        <p class="text-xs text-gray-400 mt-2 text-center"><i class="fas fa-map-marker-alt text-red-500"></i> Haz clic en un marcador para ver detalles</p>
                    </div>
                    <!-- Estadística rápida de terrenos -->
                    <div class="bg-gradient-to-r from-agri-green to-green-600 rounded-2xl shadow p-5 text-white flex flex-col justify-between" style="background: linear-gradient(135deg, #2D6A4F, #1B4332);">
                        <div><i class="fas fa-chart-simple text-3xl opacity-70"></i><p class="text-lg font-light mt-2">Total de terrenos</p><p id="totalLandsCount" class="text-4xl font-bold">0</p></div>
                        <div class="mt-4 text-sm"><i class="fas fa-leaf"></i> Superficie total: <span id="totalAreaHa">0</span> ha</div>
                    </div>
                </div>

                <!-- LISTADO DE TERRENOS (tarjetas) -->
                <h2 class="text-lg font-semibold text-gray-700 mb-4"><i class="fas fa-list text-agri-green mr-2"></i> Mis terrenos</h2>
                <div id="landsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <!-- JS dinámico -->
                    <div class="col-span-full flex justify-center py-10"><i class="fas fa-spinner fa-spin text-3xl text-agri-green"></i></div>
                </div>
            </main>
        </div>
    </div>

    <!-- MODAL PARA AGREGAR/EDITAR TERRENO (oculto por defecto) -->
    <div id="terrainModal" class="fixed inset-0 z-50 hidden items-center justify-center modal-backdrop transition-all">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 relative">
            <button id="closeModalBtn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            <h3 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-map"></i> Nuevo Terreno</h3>
            <form id="terrainForm">
                <div class="mb-3"><label class="block text-sm font-medium">Nombre del terreno *</label><input type="text" id="landName" required class="w-full border rounded-lg px-3 py-2 mt-1"></div>
                <div class="mb-3"><label class="block text-sm font-medium">Ubicación / Dirección</label><input type="text" id="landLocation" class="w-full border rounded-lg px-3 py-2 mt-1"></div>
                <div class="grid grid-cols-2 gap-3 mb-3"><div><label>Latitud *</label><input type="number" step="any" id="landLat" required class="w-full border rounded-lg px-3 py-2"></div><div><label>Longitud *</label><input type="number" step="any" id="landLng" required class="w-full border rounded-lg px-3 py-2"></div></div>
                <div class="grid grid-cols-2 gap-3 mb-3"><div><label>Área (ha) *</label><input type="number" step="0.1" id="landArea" required class="w-full border rounded-lg px-3 py-2"></div><div><label>Tipo de suelo</label><select id="landSoil" class="w-full border rounded-lg px-3 py-2"><option>Franco</option><option>Arenoso</option><option>Arcilloso</option><option>Limoso</option></select></div></div>
                <div class="mb-3"><label>Fuente de agua</label><select id="landWater" class="w-full border rounded-lg px-3 py-2"><option>Riego por goteo</option><option>Pozo</option><option>Canal</option><option>Lluvia</option></select></div>
                <div class="mb-3"><label>Cultivo actual</label><input type="text" id="landCrop" placeholder="Ej: Maíz" class="w-full border rounded-lg px-3 py-2"></div>
                <div class="flex justify-end space-x-3 mt-5"><button type="button" id="cancelModalBtn" class="px-4 py-2 border rounded-lg">Cancelar</button><button type="submit" class="px-4 py-2 bg-agri-green text-white rounded-lg" style="background-color:#2D6A4F">Guardar</button></div>
            </form>
        </div>
    </div>

    <script>
        // ---------- DATOS SIMULADOS DE TERRENOS ----------
        let terrenos = [
            {
                id: 1,
                nombre: "Parcela Chupas",
                ubicacion: "Distrito de Chupas, Huamanga",
                lat: -13.1587,
                lng: -74.2254,
                area: 8.5,
                suelo: "Franco",
                agua: "Riego por goteo",
                cultivo: "Maíz",
                climaTemp: 18,
                climaHumedad: 65
            },
            {
                id: 2,
                nombre: "Fundo Quinua",
                ubicacion: "Distrito de Quinua, Huamanga",
                lat: -13.0369,
                lng: -74.1386,
                area: 12.3,
                suelo: "Arenoso",
                agua: "Pozo",
                cultivo: "Papa",
                climaTemp: 16,
                climaHumedad: 70
            },
            {
                id: 3,
                nombre: "Huerta Acocro",
                ubicacion: "Distrito de Acocro, Huamanga",
                lat: -13.2214,
                lng: -74.0403,
                area: 3.2,
                suelo: "Arcilloso",
                agua: "Canal",
                cultivo: "Habas",
                climaTemp: 19,
                climaHumedad: 58
            },
            {
                id: 4,
                nombre: "Agrícola Tambo",
                ubicacion: "Distrito de Tambo, Huamanga",
                lat: -13.2309,
                lng: -74.1400,
                area: 20.0,
                suelo: "Franco",
                agua: "Riego por goteo",
                cultivo: "Quinua",
                climaTemp: 17,
                climaHumedad: 72
            }
        ];

        let map; // referencia al mapa Leaflet
        let markersLayer = {}; // guardar marcadores por id

        // Helper: actualizar contadores
        function updateStats() {
            document.getElementById('totalLandsCount').innerText = terrenos.length;
            let totalHa = terrenos.reduce((sum, t) => sum + t.area, 0);
            document.getElementById('totalAreaHa').innerText = totalHa.toFixed(1);
        }

        // Renderizar tarjetas de terrenos con filtros
        function renderTarjetas() {
            const cropFilter = document.getElementById('filterCrop').value;
            const areaFilter = document.getElementById('filterArea').value;
            let filtered = [...terrenos];
            if (cropFilter) filtered = filtered.filter(t => t.cultivo === cropFilter);
            if (areaFilter) {
                if (areaFilter === '0-5') filtered = filtered.filter(t => t.area < 5);
                else if (areaFilter === '5-10') filtered = filtered.filter(t => t.area >= 5 && t.area <= 10);
                else if (areaFilter === '10+') filtered = filtered.filter(t => t.area > 10);
            }

            const grid = document.getElementById('landsGrid');
            if (filtered.length === 0) {
                grid.innerHTML = `<div class="col-span-full text-center py-12 text-gray-400"><i class="fas fa-draw-polygon text-4xl mb-2"></i><p>No hay terrenos con esos filtros</p></div>`;
                return;
            }
            grid.innerHTML = filtered.map(terreno => `
                <div class="bg-white rounded-2xl shadow-md overflow-hidden card-hover transition border border-gray-100">
                    <div class="p-5">
                        <div class="flex justify-between items-start"><h3 class="font-bold text-lg text-gray-800">${terreno.nombre}</h3><span class="bg-agri-light text-agri-green text-xs px-2 py-1 rounded-full" style="background:#D8F3DC; color:#2D6A4F">${terreno.area} ha</span></div>
                        <p class="text-gray-500 text-sm mt-1"><i class="fas fa-map-pin mr-1"></i> ${terreno.ubicacion}</p>
                        <div class="grid grid-cols-2 gap-2 mt-3 text-sm">
                            <div><i class="fas fa-seedling text-agri-green"></i> ${terreno.cultivo || "Sin cultivo"}</div>
                            <div><i class="fas fa-tint"></i> ${terreno.agua}</div>
                            <div><i class="fas fa-mountain"></i> Suelo: ${terreno.suelo}</div>
                            <div><i class="fas fa-cloud-sun"></i> ${terreno.climaTemp}°C / ${terreno.climaHumedad}% HR</div>
                        </div>
                        <div class="flex justify-between mt-4 pt-2 border-t border-gray-100">
                            <button onclick="editarTerreno(${terreno.id})" class="text-blue-600 hover:text-blue-800 text-sm"><i class="fas fa-edit"></i> Editar</button>
                            <button onclick="eliminarTerreno(${terreno.id})" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash-alt"></i> Eliminar</button>
                            <button onclick="centrarMapa(${terreno.lat}, ${terreno.lng})" class="text-gray-500 text-sm"><i class="fas fa-location-dot"></i> Ver mapa</button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // ACTUALIZAR MARCADORES EN MAPA
        function updateMapMarkers() {
            if (!map) return;
            // Limpiar marcadores existentes
            Object.values(markersLayer).forEach(marker => map.removeLayer(marker));
            markersLayer = {};
            terrenos.forEach(terreno => {
                const marker = L.marker([terreno.lat, terreno.lng]).bindPopup(`
                    <b>${terreno.nombre}</b><br>
                    ${terreno.area} ha · Cultivo: ${terreno.cultivo}<br>
                    <button onclick="centrarMapa(${terreno.lat}, ${terreno.lng})" style="background:#2D6A4F; color:white; border:none; padding:2px 8px; border-radius:12px; margin-top:5px;">Ver</button>
                `).addTo(map);
                markersLayer[terreno.id] = marker;
            });
            if (terrenos.length > 0) {
                const bounds = L.latLngBounds(terrenos.map(t => [t.lat, t.lng]));
                map.fitBounds(bounds);
            } else {
                map.setView([-34.6037, -58.3816], 12);
            }
        }

        function centrarMapa(lat, lng) {
            map.setView([lat, lng], 15);
        }

        // Inicializar mapa Leaflet
        function initMap() {
            map = L.map('map').setView([-34.6037, -58.3816], 12);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> & CartoDB'
            }).addTo(map);
            updateMapMarkers();
        }

        // CRUD: Agregar / Editar
        let editandoId = null;
        const modal = document.getElementById('terrainModal');
        function openModal(terreno = null) {
            if (terreno) {
                editandoId = terreno.id;
                document.getElementById('landName').value = terreno.nombre;
                document.getElementById('landLocation').value = terreno.ubicacion;
                document.getElementById('landLat').value = terreno.lat;
                document.getElementById('landLng').value = terreno.lng;
                document.getElementById('landArea').value = terreno.area;
                document.getElementById('landSoil').value = terreno.suelo;
                document.getElementById('landWater').value = terreno.agua;
                document.getElementById('landCrop').value = terreno.cultivo || '';
                document.querySelector('#terrainModal h3').innerHTML = '<i class="fas fa-edit"></i> Editar Terreno';
            } else {
                editandoId = null;
                document.getElementById('terrainForm').reset();
                document.querySelector('#terrainModal h3').innerHTML = '<i class="fas fa-map"></i> Nuevo Terreno';
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

        document.getElementById('openModalBtn').addEventListener('click', () => openModal());
        document.getElementById('closeModalBtn').addEventListener('click', closeModal);
        document.getElementById('cancelModalBtn').addEventListener('click', closeModal);

        document.getElementById('terrainForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const newTerreno = {
                id: editandoId || Date.now(),
                nombre: document.getElementById('landName').value,
                ubicacion: document.getElementById('landLocation').value,
                lat: parseFloat(document.getElementById('landLat').value),
                lng: parseFloat(document.getElementById('landLng').value),
                area: parseFloat(document.getElementById('landArea').value),
                suelo: document.getElementById('landSoil').value,
                agua: document.getElementById('landWater').value,
                cultivo: document.getElementById('landCrop').value,
                climaTemp: Math.floor(18 + Math.random() * 12),
                climaHumedad: Math.floor(50 + Math.random() * 35)
            };
            if (editandoId) {
                const index = terrenos.findIndex(t => t.id === editandoId);
                if (index !== -1) terrenos[index] = newTerreno;
                Swal.fire('Actualizado', 'Terreno modificado correctamente', 'success');
            } else {
                terrenos.push(newTerreno);
                Swal.fire('Agregado', 'Nuevo terreno registrado', 'success');
            }
            closeModal();
            renderTarjetas();
            updateStats();
            updateMapMarkers();
        });

        window.editarTerreno = (id) => {
            const terreno = terrenos.find(t => t.id === id);
            if (terreno) openModal(terreno);
        };
        window.eliminarTerreno = async (id) => {
            const result = await Swal.fire({ title: '¿Eliminar terreno?', text: "No podrás revertirlo", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33' });
            if (result.isConfirmed) {
                terrenos = terrenos.filter(t => t.id !== id);
                renderTarjetas();
                updateStats();
                updateMapMarkers();
                Swal.fire('Eliminado', '', 'success');
            }
        };

        // Filtros y reset
        document.getElementById('filterCrop').addEventListener('change', () => { renderTarjetas(); updateMapMarkers(); });
        document.getElementById('filterArea').addEventListener('change', () => { renderTarjetas(); updateMapMarkers(); });
        document.getElementById('resetFiltersBtn').addEventListener('click', () => {
            document.getElementById('filterCrop').value = '';
            document.getElementById('filterArea').value = '';
            renderTarjetas();
            updateMapMarkers();
        });

        // Sidebar móvil y dark mode (mismo comportamiento)
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

        // Inicializar todo
        function init() {
            initMap();
            renderTarjetas();
            updateStats();
        }
        init();
    </script>
</body>
</html>