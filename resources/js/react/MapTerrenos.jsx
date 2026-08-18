import React, { useEffect, useRef, useState } from 'react';

const MapTerrenos = ({ terrenos = [], fullView = false, selectionMode = false }) => {
    const mapRef = useRef(null);
    const mapInstance = useRef(null);
    const markersLayerGroup = useRef(null);
    const [isInitialized, setIsInitialized] = useState(false);

    useEffect(() => {
        if (!mapRef.current || mapInstance.current) return;

        const L = window.L;
        if (!L) return;

        // Motor Satelital Google Hybrid
        const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 22,
            maxNativeZoom: 19,
            subdomains:['mt0','mt1','mt2','mt3'],
            attribution: '&copy; Google Maps'
        });

        const streets = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 20,
            attribution: '&copy; CartoDB'
        });

        mapInstance.current = L.map(mapRef.current, {
            center: [-13.1606, -74.2257],
            zoom: 14,
            layers: [googleHybrid]
        });

        L.control.layers({ "Satélite": googleHybrid, "Calles": streets }).addTo(mapInstance.current);
        markersLayerGroup.current = L.layerGroup().addTo(mapInstance.current);

        // ESCUCHADOR DE LOCALIZACIÓN (Para el botón de mira en la tarjeta)
        window.addEventListener('map-center-to', (e) => {
            if (mapInstance.current) {
                const { lat, lng } = e.detail;
                mapInstance.current.setView([lat, lng], 18, { animate: true, duration: 1.5 });
            }
        });

        // Asegurar visibilidad
        const resizeObserver = new ResizeObserver(() => {
            if (mapInstance.current) mapInstance.current.invalidateSize();
        });
        resizeObserver.observe(mapRef.current);

        if (selectionMode) {
            let tempMarker = null;
            mapInstance.current.on('click', async (e) => {
                const { lat, lng } = e.latlng;
                if (tempMarker) tempMarker.remove();
                tempMarker = L.marker([lat, lng]).addTo(mapInstance.current);
                try {
                    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18`);
                    const data = await res.json();
                    window.dispatchEvent(new CustomEvent('map-location-selected', {
                        detail: { lat, lng, address: data.display_name || "Ubicación detectada" }
                    }));
                } catch (err) {
                    window.dispatchEvent(new CustomEvent('map-location-selected', { detail: { lat, lng, address: "Punto marcado" } }));
                }
            });
        }

        setIsInitialized(true);

        return () => {
            resizeObserver.disconnect();
            if (mapInstance.current) {
                mapInstance.current.remove();
                mapInstance.current = null;
            }
        };
    }, []);

    useEffect(() => {
        if (!mapInstance.current || !markersLayerGroup.current || selectionMode) return;
        markersLayerGroup.current.clearLayers();
        terrenos.forEach(t => {
            if (t.lat && t.lng) {
                const m = L.marker([t.lat, t.lng]).addTo(markersLayerGroup.current);
                m.bindPopup(`
                    <div class="p-1">
                        <b class="uppercase text-[10px] block mb-1">${t.nombre}</b>
                        <span class="bg-agri-green text-white px-2 py-0.5 rounded text-[9px] font-black italic shadow-sm">${t.area} ha</span>
                    </div>
                `);
            }
        });
    }, [terrenos]);

    return (
        <div ref={mapRef} className="w-full h-full bg-slate-100 flex items-center justify-center relative overflow-hidden"
             style={{ minHeight: fullView ? '580px' : '350px', zIndex: 1, borderRadius: '1.5rem' }}>
            {!isInitialized && <i className="fa-solid fa-satellite-dish fa-spin text-agri-green text-3xl"></i>}
        </div>
    );
};

export default MapTerrenos;
