import React, { useEffect, useRef, useState } from 'react';

const MapTerrenos = ({ terrenos = [], selectionMode = false, drawMode = false, initialPoints = [], center = null, editingId = null, tenure = 'propio' }) => {
    const mapRef = useRef(null);
    const mapInstance = useRef(null);
    const markersLayerGroup = useRef(null);
    const drawingLayerGroup = useRef(null);
    const polygonLayer = useRef(null);
    const editingCenterMarker = useRef(null);

    const vertexMarkersRef = useRef([]);
    const midpointMarkersRef = useRef([]);

    const [points, setPoints] = useState([]);
    const [isInitialized, setIsInitialized] = useState(false);
    const [isGeocoding, setIsGeocoding] = useState(false);

    const drawModeRef = useRef(drawMode);
    const terrenosRef = useRef(terrenos);
    const pointsRef = useRef([]);
    const editingIdRef = useRef(editingId);

    useEffect(() => {
        drawModeRef.current = drawMode;
        terrenosRef.current = terrenos;
        editingIdRef.current = editingId;
    }, [drawMode, terrenos, editingId]);

    // Color activo basado en el estado (Propio = Verde, Alquilado = Azul)
    const activeColor = tenure === 'alquilado' ? '#3b82f6' : '#10b981';

    // SINCRONIZACIÓN DE PUNTOS (Carga inicial y Reset)
    useEffect(() => {
        const pts = Array.isArray(initialPoints) ? initialPoints.map(p => ({ lat: parseFloat(p.lat), lng: parseFloat(p.lng) })) : [];

        if (pts.length > 0) {
            setPoints(pts);
            pointsRef.current = pts;
        } else if (!editingId) {
            setPoints([]);
            pointsRef.current = [];
            if (polygonLayer.current) polygonLayer.current.remove();
            polygonLayer.current = null;
            if (editingCenterMarker.current) editingCenterMarker.current.remove();
            editingCenterMarker.current = null;
            drawingLayerGroup.current?.clearLayers();
            vertexMarkersRef.current = [];
            midpointMarkersRef.current = [];
        }
    }, [JSON.stringify(initialPoints), editingId]);

    const calculateArea = (coords) => {
        if (coords.length < 3) return 0;
        let area = 0;
        const factorHectareas = (111319 * 111319) / 10000;
        for (let i = 0; i < coords.length; i++) {
            let j = (i + 1) % coords.length;
            area += coords[i].lat * coords[j].lng;
            area -= coords[j].lat * coords[i].lng;
        }
        return Math.abs(area / 2) * factorHectareas * Math.cos(coords[0].lat * Math.PI / 180);
    };

    const isPointInPolygon = (point, vs) => {
        let x = point.lat, y = point.lng;
        let inside = false;
        for (let i = 0, j = vs.length - 1; i < vs.length; j = i++) {
            let xi = vs[i].lat, yi = vs[i].lng;
            let xj = vs[j].lat, yj = vs[j].lng;
            let intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) inside = !inside;
        }
        return inside;
    };

    const checkCollision = (latlng) => {
        const others = terrenosRef.current.filter(t =>
            !t.es_mio &&
            t.type !== 'marker' &&
            t.poligono &&
            t.id != editingIdRef.current
        );
        for (const t of others) {
            if (isPointInPolygon(latlng, t.poligono)) return true;
        }
        return false;
    };

    const updatePolygonData = async (updatedPoints, fetchAddress = false) => {
        let address = null;
        const firstPoint = updatedPoints[0];

        if (fetchAddress && firstPoint) {
            setIsGeocoding(true);
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${firstPoint.lat}&lon=${firstPoint.lng}&zoom=18`);
                const data = await res.json();
                address = data.display_name;
            } catch (err) { console.error("Error geocoding:", err); }
            setIsGeocoding(false);
        }

        window.dispatchEvent(new CustomEvent('map-polygon-updated', {
            detail: {
                points: updatedPoints.map(p => ({ lat: p.lat, lng: p.lng })),
                area: calculateArea(updatedPoints).toFixed(2),
                center: firstPoint || null,
                address: address
            }
        }));
    };

    // CTRL+Z
    useEffect(() => {
        const handleKeyDown = (e) => {
            if (drawModeRef.current && (e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') {
                e.preventDefault();
                const newPoints = pointsRef.current.slice(0, -1);
                pointsRef.current = newPoints;
                setPoints(newPoints);
                updatePolygonData(newPoints);
            }
        };
        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, []);

    // INICIALIZACIÓN DEL MAPA
    useEffect(() => {
        if (!mapRef.current || mapInstance.current) return;
        const L = window.L;
        if (!L) return;

        mapInstance.current = L.map(mapRef.current, {
            center: [-13.1606, -74.2257],
            zoom: 15,
            zoomControl: true,
            fadeAnimation: false,
            layers: [L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                maxZoom: 22, subdomains:['mt0','mt1','mt2','mt3'], attribution: '&copy; Google'
            })]
        });

        const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19, attribution: '&copy; OSM'
        });

        L.control.layers({ "Satélite": mapInstance.current.options.layers[0], "Calles": osm }, null, { position: 'bottomright' }).addTo(mapInstance.current);

        markersLayerGroup.current = L.layerGroup().addTo(mapInstance.current);
        drawingLayerGroup.current = L.layerGroup().addTo(mapInstance.current);

        mapInstance.current.on('click', (e) => {
            if (drawModeRef.current) {
                const { lat, lng } = e.latlng;
                if (checkCollision({ lat, lng })) return;

                const updated = [...pointsRef.current, { lat, lng }];
                const isFirstPoint = updated.length === 1;
                pointsRef.current = updated;
                setPoints(updated);
                updatePolygonData(updated, isFirstPoint);
            }
        });

        const flyHandler = (e) => {
            const data = Array.isArray(e.detail) ? e.detail[0] : e.detail;
            if (data?.lat && mapInstance.current) {
                // Zoom 16: Una vista clara pero no tan asfixiante como el 18
                mapInstance.current.flyTo([data.lat, data.lng], 17, {
                    animate: true,
                    duration: 1.5
                });
            }
        };
        window.addEventListener('map-fly-to', flyHandler);

        setIsInitialized(true);
        return () => window.removeEventListener('map-fly-to', flyHandler);
    }, []);

    // FIX DISTORSIÓN MODAL
    useEffect(() => {
        if (!isInitialized || !mapInstance.current) return;
        const resizer = new ResizeObserver(() => mapInstance.current?.invalidateSize());
        resizer.observe(mapRef.current);
        const timers = [100, 300, 800, 1500].map(ms => setTimeout(() => mapInstance.current?.invalidateSize(), ms));
        return () => { resizer.disconnect(); timers.forEach(t => clearTimeout(t)); };
    }, [isInitialized]);

    // RENDERIZAR POLÍGONO Y HANDLES
    useEffect(() => {
        if (!isInitialized) return;
        const L = window.L;

        // 1. Dibujar Polígono
        if (polygonLayer.current) {
            polygonLayer.current.setLatLngs(points.map(p => [p.lat, p.lng]));
            polygonLayer.current.setStyle({ color: activeColor, fillColor: activeColor, fillOpacity: 0.5 });
        } else if (points.length >= 2) {
            polygonLayer.current = L.polygon(points, {
                color: activeColor, fillColor: activeColor, weight: 4, fillOpacity: 0.5, interactive: false
            }).addTo(mapInstance.current);
        }

        // Marcador central para el terreno en edición
        if (polygonLayer.current && points.length >= 3) {
            const centerPos = polygonLayer.current.getBounds().getCenter();
            const centerIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color: white; border: 2px solid ${activeColor}; width: 22px; height: 22px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.4);">
                        <div style="width: 7px; height: 7px; background: ${activeColor}; border-radius: 50%; transform: rotate(45deg);"></div>
                       </div>`,
                iconSize: [22, 22], iconAnchor: [11, 22]
            });

            if (editingCenterMarker.current) {
                editingCenterMarker.current.setLatLng(centerPos);
                editingCenterMarker.current.setIcon(centerIcon);
            } else {
                editingCenterMarker.current = L.marker(centerPos, { icon: centerIcon, interactive: false }).addTo(mapInstance.current);
            }
        } else if (editingCenterMarker.current) {
            editingCenterMarker.current.remove();
            editingCenterMarker.current = null;
        }

        if (!drawMode) {
            drawingLayerGroup.current.clearLayers();
            vertexMarkersRef.current = [];
            midpointMarkersRef.current = [];
            return;
        }

        const vertexIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background-color: ${activeColor}; border: 3px solid white; width: 24px; height: 24px; border-radius: 50%; box-shadow: 0 0 15px rgba(0,0,0,0.6); cursor: grab; display: flex; align-items: center; justify-content: center;">
                    <div style="width: 8px; height: 8px; background: white; border-radius: 50%;"></div>
                   </div>`,
            iconSize: [24, 24], iconAnchor: [12, 12]
        });

        const midpointIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background-color: white; border: 2px solid ${activeColor}; width: 14px; height: 14px; border-radius: 50%; opacity: 0.9; box-shadow: 0 0 8px rgba(0,0,0,0.4); cursor: pointer;"></div>`,
            iconSize: [14, 14], iconAnchor: [7, 7]
        });

        if (vertexMarkersRef.current.length !== points.length) {
            drawingLayerGroup.current.clearLayers();
            vertexMarkersRef.current = [];
            midpointMarkersRef.current = [];

            points.forEach((p, index) => {
                const marker = L.marker([p.lat, p.lng], { icon: vertexIcon, draggable: true }).addTo(drawingLayerGroup.current);
                vertexMarkersRef.current[index] = marker;

                marker.on('drag', (ev) => {
                    const pos = ev.target.getLatLng();
                    if (checkCollision({ lat: pos.lat, lng: pos.lng })) {
                        marker.setLatLng([pointsRef.current[index].lat, pointsRef.current[index].lng]);
                        return;
                    }
                    const updated = [...pointsRef.current];
                    updated[index] = { lat: pos.lat, lng: pos.lng };
                    pointsRef.current = updated;
                    if (polygonLayer.current) polygonLayer.current.setLatLngs(updated.map(pts => [pts.lat, pts.lng]));
                    updateMidpoints(updated);
                });

                marker.on('dragend', () => {
                    const updated = [...pointsRef.current];
                    setPoints(updated);
                    updatePolygonData(updated, false);
                });
            });
            updateMidpoints(points);
        } else {
            points.forEach((p, index) => vertexMarkersRef.current[index]?.setLatLng([p.lat, p.lng]));
            updateMidpoints(points);
        }

        function updateMidpoints(currentPoints) {
            midpointMarkersRef.current.forEach(m => m.remove());
            midpointMarkersRef.current = [];
            if (currentPoints.length < 2) return;

            currentPoints.forEach((p, index) => {
                const nextIndex = (index + 1) % currentPoints.length;
                if (nextIndex === 0 && currentPoints.length < 3) return;
                const p2 = currentPoints[nextIndex];
                const midLat = (p.lat + p2.lat) / 2;
                const midLng = (p.lng + p2.lng) / 2;
                const midMarker = L.marker([midLat, midLng], { icon: midpointIcon }).addTo(drawingLayerGroup.current);
                midpointMarkersRef.current.push(midMarker);
                midMarker.on('click', (e) => {
                    L.DomEvent.stopPropagation(e);
                    const updated = [...currentPoints];
                    updated.splice(index + 1, 0, { lat: midLat, lng: midLng });
                    pointsRef.current = updated;
                    setPoints(updated);
                    updatePolygonData(updated);
                });
            });
        }
    }, [points.length, drawMode, isInitialized, activeColor]);

    // POSICIONAR MAPA INICIAL
    useEffect(() => {
        if (!isInitialized) return;
        const targetCenter = center || (points.length > 0 ? points[0] : null);
        if (targetCenter && mapInstance.current) {
            setTimeout(() => mapInstance.current?.setView([targetCenter.lat, targetCenter.lng], 18), 500);
        }
    }, [isInitialized, !!center, drawMode]);

    // RENDERIZAR TERRENOS EXISTENTES
    useEffect(() => {
        if (!isInitialized || !markersLayerGroup.current) return;
        markersLayerGroup.current.clearLayers();
        const L = window.L;
        terrenos.forEach(t => {
            if (drawMode && editingId && t.id == editingId) return;

            let color = '#ef4444'; // Rojo por defecto
            if (t.color === 'green') color = '#10b981';
            else if (t.color === 'blue') color = '#3b82f6';
            else if (t.color === 'purple') color = '#a855f7';

            if (t.type === 'marker') {
                const locationIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style="background-color: #a855f7; border: 2px solid white; width: 24px; height: 24px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"><div style="width: 8px; height: 8px; background: white; border-radius: 50%; transform: rotate(45deg);"></div></div>`,
                    iconSize: [24, 24], iconAnchor: [12, 24]
                });
                L.marker([t.lat, t.lng], { icon: locationIcon }).addTo(markersLayerGroup.current).bindPopup(`<b>${t.label}</b>`);
            } else if (t.poligono && Array.isArray(t.poligono)) {
                const poly = L.polygon(t.poligono, {
                    color,
                    fillOpacity: 0.15,
                    weight: t.es_mio ? 3 : 1,
                    interactive: true
                }).addTo(markersLayerGroup.current);

                poly.bindTooltip(t.label, {
                    sticky: true,
                    className: 'agro-tooltip'
                });

                // ICONO DE UBICACIÓN EN EL CENTRO DEL TERRENO
                const centerPos = poly.getBounds().getCenter();
                const centerIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style="background-color: white; border: 2px solid ${color}; width: 22px; height: 22px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.4);">
                            <div style="width: 7px; height: 7px; background: ${color}; border-radius: 50%; transform: rotate(45deg);"></div>
                           </div>`,
                    iconSize: [22, 22], iconAnchor: [11, 22]
                });
                L.marker(centerPos, { icon: centerIcon, interactive: false }).addTo(markersLayerGroup.current);
            }
        });
    }, [terrenos, isInitialized, drawMode, editingId]);

    return (
        <div className="w-full h-full relative bg-slate-100 overflow-hidden" style={{ minHeight: '400px', height: '100%' }}>
            <style>{`.agro-tooltip { background: rgba(0,0,0,0.8) !important; color: white !important; font-weight: 800; font-size: 10px; text-transform: uppercase; border-radius: 8px; padding: 4px 8px; }`}</style>
            <div ref={mapRef} className="w-full h-full absolute inset-0" style={{ zIndex: 1 }} />
            {isGeocoding && (
                <div className="absolute top-4 left-1/2 -translate-x-1/2 z-[1001] bg-agri-green text-white px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest shadow-2xl animate-pulse">
                    <i className="fa-solid fa-magnifying-glass-location mr-2"></i> Obteniendo Ubicación...
                </div>
            )}
            {!isInitialized && <div className="absolute inset-0 flex items-center justify-center bg-white/50 z-[1000]"><i className="fa-solid fa-satellite-dish fa-spin text-agri-green text-3xl"></i></div>}
        </div>
    );
};

export default MapTerrenos;
