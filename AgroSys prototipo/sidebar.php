<aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-gray-200 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300 ease-in-out shadow-lg md:shadow-none flex flex-col">
    <div class="flex items-center justify-between p-5 border-b border-gray-100">
        <div class="flex items-center space-x-2">
            <i class="fas fa-leaf text-agri-green text-2xl"></i>
            <span class="font-bold text-xl text-gray-800">Agro<span class="text-agri-green">Sys</span></span>
        </div>
        <button id="closeSidebarBtn" class="md:hidden text-gray-500 hover:text-agri-green">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    
    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
        <a href="index.php" class="flex items-center px-4 py-3 text-agri-green bg-agri-light rounded-lg font-medium transition">
            <i class="fas fa-tachometer-alt w-6"></i>
            <span class="ml-3">Dashboard</span>
        </a>
        <a href="terreno.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-map-marker-alt w-6"></i>
            <span class="ml-3">Terrenos</span>
        </a>
        <a href="cultivo.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-seedling w-6"></i>
            <span class="ml-3">Cultivos</span>
        </a>
        <a href="labores.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-tasks w-6"></i>
            <span class="ml-3">Labores</span>
        </a>
        <a href="clima.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-cloud-sun-rain w-6"></i>
            <span class="ml-3">Clima IA</span>
        </a>
        <a href="alerta.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-robot w-6"></i>
            <span class="ml-3">Alertas IA</span>
        </a>
        <a href="reporte.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <i class="fas fa-chart-line w-6"></i>
            <span class="ml-3">Reportes</span>
        </a>
    </nav>
    <div class="p-4 border-t border-gray-100 text-xs text-gray-400">
        <i class="fas fa-microchip"></i> AgroSys v2.0 · IA Agtech
    </div>
</aside>