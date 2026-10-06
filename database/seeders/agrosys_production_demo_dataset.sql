-- ==============================================================================
-- AGROSYS SAAS - SCRIPT DE DATOS DEMO Y PRODUCCIÓN (1 AÑO DE ACTIVIDAD HISTÓRICA)
-- Generado con coherencia relacional absoluta entre Terrenos, Cultivos, Labores,
-- Cosechas, Ventas, Historial de Auditoría y Usuarios.
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- 1. CATÁLOGOS BASE Y ROLES GLOBAL
-- ------------------------------------------------------------------------------
INSERT INTO `rol` (`id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'Administrador Maestro Global de la Plataforma SaaS AgroSys', NOW(), NOW()),
(2, 'Agricultor', 'Usuario Operativo, Productor y Propietario de Parcelas Agrícolas', NOW(), NOW())
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

INSERT INTO `roles_organizacion` (`id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'Líder administrativo de la empresa o cooperativa agrícola', NOW(), NOW()),
(2, 'Supervisor', 'Ingeniero agrónomo o técnico encargado del monitoreo de campo', NOW(), NOW()),
(3, 'Agricultor', 'Productor y encargado directo de las labores en parcela', NOW(), NOW())
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- Catálogo de Cultivos
INSERT INTO `catalogo_cultivos` (`id`, `nombre`, `nombre_cientifico`, `tipo_ciclo`, `vida_util_estimada_meses`, `dias_a_cosecha_promedio`, `instrucciones_base_riego`, `instrucciones_base_plagas`, `es_personalizado`, `created_at`, `updated_at`) VALUES
(1, 'Maíz Morada', 'Zea mays L.', 'ciclo_corto', 5, 120, 'Riego por goteo o gravedad cada 7 a 10 días.', 'Control preventivo de cogollero.', 0, NOW(), NOW()),
(2, 'Cebolla Arequipeña', 'Allium cepa L.', 'ciclo_corto', 6, 140, 'Riego frecuente ligero por goteo.', 'Monitoreo de trips y mildeu.', 0, NOW(), NOW()),
(3, 'Papa Amarilla Tumbay', 'Solanum tuberosum', 'ciclo_corto', 5, 130, 'Riego moderado en floración.', 'Prevención de rancha (Phytophthora).', 0, NOW(), NOW()),
(4, 'Palta Hass', 'Persea americana', 'perenne', 240, 300, 'Riego tecnificado por goteo continuo.', 'Control de queresa y oidium.', 0, NOW(), NOW()),
(5, 'Quinua Blanca', 'Chenopodium quinoa', 'ciclo_corto', 6, 150, 'Resistente a sequía, riego suplementario.', 'Vigilancia de polilla de la quinua.', 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- Catálogo de Labores
INSERT INTO `catalogo_labores` (`id`, `nombre`, `categoria`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Preparar Terreno', 'preparacion', 'Mecanización, arado y surcado de suelo', NOW(), NOW()),
(2, 'Siembra Directa', 'siembra', 'Instalación de semilla en surco o cinta de goteo', NOW(), NOW()),
(3, 'Riego por Goteo', 'mantenimiento', 'Suministro de agua tecnificada y fertirriego', NOW(), NOW()),
(4, 'Fumigación Fitosanitaria', 'mantenimiento', 'Aplicación de fungicidas e insecticidas', NOW(), NOW()),
(5, 'Abonado Orgánico', 'mantenimiento', 'Aplicación de compost, humus y fertirriego foliar', NOW(), NOW()),
(6, 'Cosecha Manual', 'cosecha', 'Recolección, selección y ensacado en campo', NOW(), NOW())
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- Catálogo de Insumos
INSERT INTO `catalogo_insumos` (`id`, `nombre`, `unidad_medida`, `created_at`, `updated_at`) VALUES
(1, 'Urea Granulada 46% N', 'kg', NOW(), NOW()),
(2, 'NPK 15-15-15', 'kg', NOW(), NOW()),
(3, 'Fungicida Ridomil Gold', 'litros', NOW(), NOW()),
(4, 'Insecticida Karate Zeon', 'litros', NOW(), NOW()),
(5, 'Compost Orgánico Certificado', 'sacos', NOW(), NOW())
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);


-- ------------------------------------------------------------------------------
-- 2. 20 USUARIOS EN PRODUCCIÓN CON CREDENCIALES
-- (Contraseña para todos: 'password' -> $2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O)
-- ------------------------------------------------------------------------------
INSERT INTO `usuarios` (`id`, `rol_id`, `nombres`, `apellidos`, `email`, `email_verified_at`, `password`, `telefono`, `dni`, `estado`, `experiencia_anios`, `nivel_educativo`, `ubicacion`, `descripcion`, `is_activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Super', 'Admin', 'admin@agrosys.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '999888777', '00000000', 1, 10, 'Universitario', 'Lima, Perú', 'Administrador Maestro del Sistema AgroSys', 1, '2025-10-01 08:00:00', NOW()),
(2, 1, 'Roosivelt', 'Tupla Dipaz', 'roosivelt.td@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '912345678', '71234567', 1, 8, 'Ingeniería', 'Arequipa, Perú', 'Propietario e Investigador AgroSys', 1, '2025-10-02 09:30:00', NOW()),
(3, 2, 'Carlos', 'Mendoza Véliz', 'carlos.mendoza@agrosys.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '984512367', '41526374', 1, 12, 'Ingeniero Agrónomo', 'Majes, Arequipa', 'Supervisor Agrónomo Senior de Campo', 1, '2025-10-05 10:15:00', NOW()),
(4, 2, 'María Elena', 'Quispe Huamán', 'maria.quispe@agricolavalle.pe', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '956321478', '42637485', 1, 6, 'Licenciada en Administración', 'Arequipa, Perú', 'Gerente General Cooperativa Valle del Sur', 1, '2025-10-10 11:00:00', NOW()),
(5, 2, 'José Luis', 'Huamán Castro', 'jose.huaman@agricolavalle.pe', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '974123568', '43748596', 1, 9, 'Técnico Agrícola', 'La Joya, Arequipa', 'Supervisor Fitosanitario de Valle del Sur', 1, '2025-10-12 14:20:00', NOW()),
(6, 2, 'Ana Sofía', 'Flores Delgado', 'ana.flores@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '981234567', '44859607', 1, 5, 'Técnico', 'Majes, Arequipa', 'Productora de Cebolla Morada', 1, '2025-10-15 08:45:00', NOW()),
(7, 2, 'Pedro', 'Castillo Rivas', 'pedro.castillo@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '923456789', '45960718', 1, 7, 'Secundaria Completa', 'La Joya, Arequipa', 'Agricultor especialista en Maíz', 1, '2025-10-20 09:00:00', NOW()),
(8, 2, 'Gabriel', 'Ramos Pinto', 'gabriel.ramos@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '934567890', '46071829', 1, 4, 'Técnico', 'Pedregal, Arequipa', 'Productor de Papa Tumbay', 1, '2025-11-01 10:30:00', NOW()),
(9, 2, 'Carmen Rosa', 'Vargas Lazo', 'carmen.vargas@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '945678901', '47182930', 1, 10, 'Universitario', 'Ica, Perú', 'Propietaria Fundo San José', 1, '2025-11-05 11:15:00', NOW()),
(10, 2, 'Diego', 'Silva Cornejo', 'diego.silva@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '956789012', '48293041', 1, 3, 'Secundaria', 'Ica, Perú', 'Agricultor de Espárrago y Palta', 1, '2025-11-10 12:00:00', NOW()),
(11, 2, 'Lucía', 'Morales Benítez', 'lucia.morales@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '967890123', '49304152', 1, 6, 'Técnico Agrícola', 'Valle Ica, Perú', 'Especialista en Palta Hass', 1, '2025-11-15 15:40:00', NOW()),
(12, 2, 'Fernando', 'Delgado Paredes', 'fernando.delgado@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '978901234', '50415263', 1, 11, 'Secundaria', 'Trujillo, La Libertad', 'Administrador Agroindustrial Norte Verde', 1, '2025-12-01 08:30:00', NOW()),
(13, 2, 'Sofía', 'Gutiérrez Ríos', 'sofia.gutierrez@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '989012345', '51526374', 1, 5, 'Universitario', 'Chao, La Libertad', 'Supervisor Fitosanitario Norte Verde', 1, '2025-12-05 09:10:00', NOW()),
(14, 2, 'Mateo', 'Salazar Cruz', 'mateo.salazar@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '990123456', '52637485', 1, 4, 'Técnico', 'Virú, La Libertad', 'Productor de Pimiento Páprika', 1, '2025-12-10 10:00:00', NOW()),
(15, 2, 'Natalia', 'Ríos Salazar', 'natalia.rios@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '901234567', '53748596', 1, 7, 'Secundaria', 'Virú, La Libertad', 'Agricultora de Horticultura', 1, '2025-12-15 11:20:00', NOW()),
(16, 2, 'Hugo', 'Guerrero Bravo', 'hugo.guerrero@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '912345670', '54859607', 1, 8, 'Técnico', 'Majes, Arequipa', 'Agricultor de Quinua', 1, '2026-01-05 08:15:00', NOW()),
(17, 2, 'Elena', 'Paredes Vaca', 'elena.paredes@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '923456781', '55960718', 1, 3, 'Secundaria', 'La Joya, Arequipa', 'Agricultora Valle del Sur', 1, '2026-01-10 09:30:00', NOW()),
(18, 2, 'Javier', 'Benítez Soto', 'javier.benitez@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '934567892', '56071829', 1, 9, 'Técnico', 'Cañete, Lima', 'Especialista en Riego Tecnicado', 1, '2026-01-15 10:45:00', NOW()),
(19, 2, 'Rosa', 'Campos Tello', 'rosa.campos@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '945678903', '57182930', 1, 5, 'Universitario', 'Ica, Perú', 'Productora Fundo San José', 1, '2026-02-01 08:00:00', NOW()),
(20, 2, 'Miguel Ángel', 'Torres Vega', 'miguel.torres@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '956789014', '58293041', 1, 12, 'Ingeniero Agrónomo', 'Majes, Arequipa', 'Consultor Fitosanitario Valle del Sur', 1, '2026-02-05 09:00:00', NOW())
ON DUPLICATE KEY UPDATE `nombres` = VALUES(`nombres`), `is_activo` = 1;


-- ------------------------------------------------------------------------------
-- 3. ORGANIZACIONES, MEMBRESÍAS Y ASIGNACIÓN DE SUPERVISORES
-- ------------------------------------------------------------------------------
INSERT INTO `organizaciones` (`id`, `nombre`, `descripcion`, `ruc`, `telefono`, `email`, `direccion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Cooperativa Agrícola Valle del Sur', 'Cooperativa Agrícola de Productores de Cebolla, Maíz y Quinua de Majes y La Joya', '20601234567', '054456789', 'contacto@agricolavalle.pe', 'Av. Mariscal Castilla 405, Majes, Arequipa', 1, '2025-10-10 09:00:00', NOW()),
(2, 'Agrogremial Fundo San José S.A.C.', 'Empresa Agroexportadora de Palta Hass y Espárragos en el Valle de Ica', '20609876543', '056234567', 'ventas@agrofundo.pe', 'Panamericana Sur Km 300, Subtanjalla, Ica', 1, '2025-11-05 10:00:00', NOW()),
(3, 'Empresa Agroindustrial Norte Verde S.R.L.', 'Consorcio Agrícola de Pimiento Páprika y Hortalizas de La Libertad', '20605554433', '044321654', 'informes@norteverde.pe', 'Calle Los Laureles 120, Chao, Virú, La Libertad', 1, '2025-12-01 08:30:00', NOW())
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- Membresías en Organizaciones
INSERT INTO `miembros_organizacion` (`id`, `usuario_id`, `organizacion_id`, `es_propietario`, `estado`, `fecha_ingreso`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 1, 1, '2025-10-10', NOW(), NOW()), -- Roosivelt (Propietario / Admin Org 1)
(2, 5, 1, 0, 1, '2025-10-12', NOW(), NOW()), -- José Luis (Supervisor Org 1)
(3, 6, 1, 0, 1, '2025-10-15', NOW(), NOW()), -- Ana Flores (Agricultor Org 1)
(4, 7, 1, 0, 1, '2025-10-20', NOW(), NOW()), -- Pedro Castillo (Agricultor Org 1)
(5, 8, 1, 0, 1, '2025-11-01', NOW(), NOW()), -- Gabriel Ramos (Agricultor Org 1)
(6, 9, 2, 1, 1, '2025-11-05', NOW(), NOW()), -- Carmen Vargas (Admin Org 2)
(7, 10, 2, 0, 1, '2025-11-10', NOW(), NOW()),-- Diego Silva (Agricultor Org 2)
(8, 11, 2, 0, 1, '2025-11-15', NOW(), NOW()),-- Lucía Morales (Supervisor Org 2)
(9, 12, 3, 1, 1, '2025-12-01', NOW(), NOW()),-- Fernando Delgado (Admin Org 3)
(10, 13, 3, 0, 1, '2025-12-05', NOW(), NOW()),-- Sofía Gutiérrez (Supervisor Org 3)
(11, 14, 3, 0, 1, '2025-12-10', NOW(), NOW()),-- Mateo Salazar (Agricultor Org 3)
(12, 15, 3, 0, 1, '2025-12-15', NOW(), NOW()) -- Natalia Ríos (Agricultor Org 3)
ON DUPLICATE KEY UPDATE `estado` = 1;

-- Roles Internos de Empresa
INSERT INTO `miembro_roles` (`id`, `miembro_id`, `rol_id`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, NOW(), NOW()), -- Roosivelt -> Admin
(2, 2, 2, 1, NOW(), NOW()), -- José Luis -> Supervisor
(3, 3, 3, 1, NOW(), NOW()), -- Ana -> Agricultor
(4, 4, 3, 1, NOW(), NOW()), -- Pedro -> Agricultor
(5, 5, 3, 1, NOW(), NOW()), -- Gabriel -> Agricultor
(6, 6, 1, 1, NOW(), NOW()), -- Carmen -> Admin
(7, 8, 2, 1, NOW(), NOW()), -- Lucía -> Supervisor
(8, 7, 3, 1, NOW(), NOW()), -- Diego -> Agricultor
(9, 9, 1, 1, NOW(), NOW()), -- Fernando -> Admin
(10, 10, 2, 1, NOW(), NOW()),-- Sofía -> Supervisor
(11, 11, 3, 1, NOW(), NOW()),-- Mateo -> Agricultor
(12, 12, 3, 1, NOW(), NOW()) -- Natalia -> Agricultor
ON DUPLICATE KEY UPDATE `estado` = 1;

-- Asignación de Supervisores a Agricultores
INSERT INTO `asignaciones_supervisor` (`id`, `organizacion_id`, `supervisor_miembro_id`, `agricultor_usuario_id`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 2, NOW(), NOW()), -- Supervisor José Luis -> Roosivelt
(2, 1, 2, 6, NOW(), NOW()), -- Supervisor José Luis -> Ana Flores
(3, 1, 2, 7, NOW(), NOW()), -- Supervisor José Luis -> Pedro Castillo
(4, 2, 8, 10, NOW(), NOW()),-- Supervisor Lucía -> Diego Silva
(5, 3, 10, 14, NOW(), NOW()),-- Supervisor Sofía -> Mateo Salazar
(6, 3, 10, 15, NOW(), NOW()) -- Supervisor Sofía -> Natalia Ríos
ON DUPLICATE KEY UPDATE `updated_at` = NOW();


-- ------------------------------------------------------------------------------
-- 4. TERRENOS Y PARCELAS AGRÍCOLAS (PERTENECIENTES A ROOSIVELT Y ORG 1)
-- ------------------------------------------------------------------------------
INSERT INTO `terrenos` (`id`, `organizacion_id`, `usuario_id`, `nombre`, `ubicacion`, `direccion_referencia`, `latitud`, `longitud`, `poligono`, `hectareas`, `tipo_tenencia`, `costo_alquiler_anual`, `alquiler_modalidad`, `alquiler_periodo`, `fecha_alquiler`, `fecha_vencimiento_alquiler`, `calidad_suelo`, `fuente_agua`, `estado_terreno`, `foto_path`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'INIA Majes Parcela 12', 'Majes, Arequipa', 'Sección A, Lote 12, Canal Lateral 4', -16.32145600, -72.21543200, '[{"lat":-16.321,"lng":-72.215},{"lat":-16.322,"lng":-72.215},{"lat":-16.322,"lng":-72.216},{"lat":-16.321,"lng":-72.216}]', 5.50, 'propio', 0.00, 'global', 'fecha', NULL, NULL, 'franco', 'Riego por goteo', 'activo', 'users/e2cff8313c92d9b117b2b0834461fb46/img/terreno/inia_terreno.jpg', 1, '2025-10-15 09:00:00', NOW()),
(2, 1, 2, 'Fundo Los Uinuapata', 'La Joya, Arequipa', 'Km 988 Panamericana Sur', -16.54123000, -71.85412000, '[{"lat":-16.541,"lng":-71.854},{"lat":-16.542,"lng":-71.854},{"lat":-16.542,"lng":-71.855},{"lat":-16.541,"lng":-71.855}]', 8.20, 'propio', 0.00, 'global', 'fecha', NULL, NULL, 'franco', 'Riego por goteo', 'activo', NULL, 1, '2025-10-18 10:30:00', NOW()),
(3, 1, 2, 'Fundo Quispe - Parcela Norte', 'Majes, Arequipa', 'Sección B, Lote 45', -16.33541000, -72.20412000, NULL, 6.00, 'alquilado', 1800.00, 'por_campana', 'fecha', '2025-10-20', '2026-02-20', 'arenoso', 'Riego por goteo', 'activo', NULL, 1, '2025-10-20 11:00:00', NOW()),
(4, 1, 2, 'Fundo San José Subtanjalla', 'Ica, Perú', 'Panamericana Sur Km 302', -14.02154000, -75.73214000, NULL, 15.00, 'propio', 0.00, 'global', 'fecha', NULL, NULL, 'limoso', 'Pozo tubular', 'activo', NULL, 1, '2025-11-05 12:00:00', NOW()),
(5, 1, 2, 'Parcela Las Dunas', 'Ica, Perú', 'Valle Los Molinos Lote 8', -14.05123000, -75.71234000, NULL, 4.50, 'alquilado', 2200.00, 'global', 'anual', '2025-11-10', '2026-11-10', 'arenoso', 'Riego por goteo', 'activo', NULL, 1, '2025-11-10 14:00:00', NOW()),
(6, 1, 2, 'Fundo Norte Verde Chao', 'Chao, La Libertad', 'Sector Huancaco Lote 10', -8.54123000, -78.68412000, NULL, 12.00, 'propio', 0.00, 'global', 'fecha', NULL, NULL, 'franco', 'Canal Madre Chavimochic', 'activo', NULL, 1, '2025-12-01 09:00:00', NOW()),
(7, 1, 2, 'Parcela Virú Agrícola', 'Virú, La Libertad', 'Sector Tomabal Lote 3', -8.42154000, -78.75123000, NULL, 5.00, 'propio', 0.00, 'global', 'fecha', NULL, NULL, 'franco', 'Canal Chavimochic', 'activo', NULL, 1, '2025-12-10 11:30:00', NOW())
ON DUPLICATE KEY UPDATE `usuario_id` = VALUES(`usuario_id`), `nombre` = VALUES(`nombre`);


-- ------------------------------------------------------------------------------
-- 5. CULTIVOS Y CAMPAÑAS AGRÍCOLAS DE ROOSIVELT
-- ------------------------------------------------------------------------------
INSERT INTO `cultivos` (`id`, `terreno_id`, `catalogo_cultivo_id`, `nombre_lote`, `variedad`, `fecha_planificada`, `fecha_siembra`, `fecha_cosecha_estimada`, `fecha_cosecha_finalizada`, `estado`, `area_destinada`, `plantas_estimadas`, `rendimiento_esperado_tn_ha`, `observaciones`, `foto_path`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'LOTE Y5PA', 'Morada Arequipeña', '2025-10-25', '2025-10-28', '2026-02-28', '2026-03-02', 'Cosechado', 2.50, 45000, 8.50, 'Excelente rendimiento de pigmento antocianina.', 'users/e2cff8313c92d9b117b2b0834461fb46/img/cultivo/maiz_morado.jpg', '2025-10-25 10:00:00', NOW()),
(2, 2, 2, 'LOTE PTFI', 'Arequipeña Roja', '2025-11-01', '2025-11-05', '2026-03-15', '2026-03-20', 'Cosechado', 3.00, 90000, 18.00, 'Cebolla de alta firmeza para exportación.', NULL, '2025-11-01 11:30:00', NOW()),
(3, 3, 1, 'LOTE 41TA', 'Morada Selección', '2025-11-10', '2025-11-15', '2026-03-15', '2026-03-18', 'Cosechado', 4.00, 72000, 9.00, 'Alquiler por campaña concluido exitosamente en Fundo Quispe.', NULL, '2025-11-10 09:15:00', NOW()),
(4, 4, 4, 'LOTE PALTA HASS 01', 'Hass Exportación', '2025-11-20', '2025-11-25', '2026-09-30', NULL, 'En crecimiento', 10.00, 4000, 15.00, 'Desarrollo permanente con riego por goteo continuo.', NULL, '2025-11-20 14:00:00', NOW()),
(5, 5, 4, 'LOTE PALTA HASS 02', 'Hass Orgánica', '2025-12-01', '2025-12-05', '2026-10-15', NULL, 'En crecimiento', 4.00, 1600, 14.00, 'Parcela en alquiler vigente con manejo orgánico.', NULL, '2025-12-01 10:20:00', NOW()),
(6, 6, 5, 'LOTE QUINUA CHAO', 'Blanca de Juli', '2026-01-05', '2026-01-10', '2026-05-20', '2026-05-25', 'Cosechado', 5.00, 120000, 3.20, 'Cosecha de grano limpio certificado.', NULL, '2026-01-05 08:30:00', NOW()),
(7, 7, 3, 'LOTE PAPA VIRÚ', 'Tumbay Selección', '2026-02-01', '2026-02-05', '2026-06-15', '2026-06-18', 'Cosechado', 3.50, 60000, 22.00, 'Excelente tubérculo sin rancha.', NULL, '2026-02-01 09:00:00', NOW()),
(8, 1, 2, 'LOTE NUEVA CEBOLLA', 'Sintética Arequipeña', '2026-06-01', '2026-06-05', '2026-10-20', NULL, 'En crecimiento', 2.50, 75000, 20.00, 'Segunda campaña del año en parcela INIA 12.', NULL, '2026-06-01 10:00:00', NOW())
ON DUPLICATE KEY UPDATE `estado` = VALUES(`estado`);


-- ------------------------------------------------------------------------------
-- 6. SECUENCIA CRONOLÓGICA COMPLETA DE LABORES DE CAMPO
-- (Cada cultivo cosechado tiene Preparación -> Siembra -> Fertirriego -> Fumigación -> Cosecha)
-- ------------------------------------------------------------------------------
INSERT INTO `labores` (`id`, `cultivo_id`, `catalogo_labor_id`, `fecha_realizacion`, `costo_mano_obra_total`, `costo_maquinaria_total`, `costo_total`, `estado`, `observaciones`, `foto_path`, `created_at`, `updated_at`) VALUES
-- Labores para Cultivo 1 (Maíz Morada LOTE Y5PA)
(1, 1, 1, '2025-10-26', 300.00, 250.00, 550.00, 'Completada', 'Arado profundo y mecanización de terreno.', NULL, '2025-10-26 10:00:00', NOW()),
(2, 1, 2, '2025-10-28', 400.00, 0.00, 650.00, 'Completada', 'Siembra manual a golpe con 2 semillas por punto.', NULL, '2025-10-28 11:30:00', NOW()),
(3, 1, 3, '2025-11-15', 150.00, 0.00, 450.00, 'Completada', 'Primer fertirriego con Urea y compost.', NULL, '2025-11-15 09:00:00', NOW()),
(4, 1, 4, '2025-12-10', 200.00, 100.00, 500.00, 'Completada', 'Fumigación preventiva contra gusano cogollero.', NULL, '2025-12-10 08:20:00', NOW()),
(5, 1, 5, '2026-01-15', 250.00, 0.00, 600.00, 'Completada', 'Abonado orgánico de nutrición foliar.', NULL, '2026-01-15 10:00:00', NOW()),
(6, 1, 6, '2026-03-02', 800.00, 150.00, 950.00, 'Completada', 'Cosecha y deshojado manual de coronta morada.', NULL, '2026-03-02 16:00:00', NOW()),

-- Labores para Cultivo 2 (Cebolla LOTE PTFI)
(7, 2, 1, '2025-11-02', 400.00, 300.00, 700.00, 'Completada', 'Nivelación y surcado para riego por goteo.', NULL, '2025-11-02 09:00:00', NOW()),
(8, 2, 2, '2025-11-05', 600.00, 0.00, 900.00, 'Completada', 'Trasplante de plántulas de cebolla roja.', NULL, '2025-11-05 10:15:00', NOW()),
(9, 2, 3, '2025-12-01', 200.00, 0.00, 500.00, 'Completada', 'Riego por goteo con NPK solubilizado.', NULL, '2025-12-01 08:30:00', NOW()),
(10, 2, 4, '2026-01-10', 250.00, 150.00, 550.00, 'Completada', 'Aplicación de Ridomil Gold contra mildeu.', NULL, '2026-01-10 09:00:00', NOW()),
(11, 2, 6, '2026-03-20', 1200.00, 200.00, 1400.00, 'Completada', 'Cosecha, secado y curado de cabeza de cebolla.', NULL, '2026-03-20 17:00:00', NOW()),

-- Labores para Cultivo 3 (Maíz LOTE 41TA - Terreno Alquilado Fundo Quispe)
(12, 3, 1, '2025-11-12', 350.00, 250.00, 600.00, 'Completada', 'Preparación de terreno alquilado Fundo Quispe.', NULL, '2025-11-12 08:00:00', NOW()),
(13, 3, 2, '2025-11-15', 500.00, 0.00, 750.00, 'Completada', 'Siembra directa de Maíz Selección.', NULL, '2025-11-15 10:00:00', NOW()),
(14, 3, 3, '2025-12-20', 180.00, 0.00, 480.00, 'Completada', 'Fertirriego con nitrógeno y microelementos.', NULL, '2025-12-20 09:30:00', NOW()),
(15, 3, 6, '2026-03-18', 900.00, 150.00, 1050.00, 'Completada', 'Cosecha manual de lote 41TA.', NULL, '2026-03-18 16:30:00', NOW()),

-- Labores para Cultivo 4 (Palta Hass LOTE 01)
(16, 4, 1, '2025-11-22', 800.00, 600.00, 1400.00, 'Completada', 'Hoyado y preparación de suelo con materia orgánica.', NULL, '2025-11-22 09:00:00', NOW()),
(17, 4, 2, '2025-11-25', 1200.00, 0.00, 2200.00, 'Completada', 'Instalación de plántulas injertadas de Palta Hass.', NULL, '2025-11-25 11:00:00', NOW()),
(18, 4, 3, '2026-02-10', 300.00, 0.00, 800.00, 'Completada', 'Mantenimiento de cintas de goteo y nutrición.', NULL, '2026-02-10 10:00:00', NOW()),

-- Labores para Cultivo 5 (Palta Hass LOTE 02)
(19, 5, 1, '2025-12-02', 600.00, 400.00, 1000.00, 'Completada', 'Preparación de hoyos y aplicación de compost.', NULL, '2025-12-02 09:00:00', NOW()),
(20, 5, 2, '2025-12-05', 900.00, 0.00, 1600.00, 'Completada', 'Siembra e instalación de palta Hass orgánica.', NULL, '2025-12-05 11:00:00', NOW()),

-- Labores para Cultivo 6 (Quinua LOTE QUINUA CHAO)
(21, 6, 1, '2026-01-07', 400.00, 300.00, 700.00, 'Completada', 'Barbecho y nivelación de suelo.', NULL, '2026-01-07 08:30:00', NOW()),
(22, 6, 2, '2026-01-10', 450.00, 0.00, 650.00, 'Completada', 'Siembra en surco de Quinua Blanca.', NULL, '2026-01-10 10:00:00', NOW()),
(23, 6, 6, '2026-05-25', 1100.00, 200.00, 1300.00, 'Completada', 'Segado, trillado y venteado de quinua.', NULL, '2026-05-25 17:00:00', NOW()),

-- Labores para Cultivo 7 (Papa LOTE PAPA VIRÚ)
(24, 7, 1, '2026-02-02', 500.00, 400.00, 900.00, 'Completada', 'Preparación de camas y surcado de papa.', NULL, '2026-02-02 08:00:00', NOW()),
(25, 7, 2, '2026-02-05', 700.00, 0.00, 1500.00, 'Completada', 'Siembra de tubérculo semilla Tumbay.', NULL, '2026-02-05 10:00:00', NOW()),
(26, 7, 6, '2026-06-18', 1500.00, 300.00, 1800.00, 'Completada', 'Cosecha, desenterrado y clasificación de papa.', NULL, '2026-06-18 16:00:00', NOW()),

-- Labores para Cultivo 8 (NUEVA CEBOLLA)
(27, 8, 1, '2026-06-02', 350.00, 250.00, 600.00, 'Completada', 'Preparación de cama de siembra para segunda campaña.', NULL, '2026-06-02 09:00:00', NOW()),
(28, 8, 2, '2026-06-05', 550.00, 0.00, 850.00, 'Completada', 'Trasplante de plántulas de cebolla sintética.', NULL, '2026-06-05 11:00:00', NOW())
ON DUPLICATE KEY UPDATE `estado` = 'Completada';

-- Insumos Usados
INSERT INTO `insumos_usados` (`id`, `labor_id`, `catalogo_insumo_id`, `cantidad`, `costo_unitario`, `costo_flete`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 100.00, 2.50, 50.00, NOW(), NOW()), -- Urea en Maíz Y5PA
(2, 3, 5, 20.00, 5.00, 0.00, NOW(), NOW()),  -- Compost
(3, 4, 4, 2.00, 150.00, 0.00, NOW(), NOW()), -- Karate Insecticida
(4, 9, 2, 150.00, 3.00, 50.00, NOW(), NOW()),-- NPK en Cebolla PTFI
(5, 10, 3, 2.50, 120.00, 0.00, NOW(), NOW()),-- Ridomil en Cebolla
(6, 14, 1, 120.00, 2.50, 50.00, NOW(), NOW())-- Urea en Maíz 41TA
ON DUPLICATE KEY UPDATE `cantidad` = VALUES(`cantidad`);


-- ------------------------------------------------------------------------------
-- 7. COSECHAS REALES (VINCULADAS STRICTAMENTE A SUS LABORES DE COSECHA)
-- ------------------------------------------------------------------------------
INSERT INTO `cosechas` (`id`, `labor_id`, `fecha_cosecha`, `cantidad_kg`, `unidad_medida`, `calidad`, `lote_codigo`, `costo_operativo_cosecha`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 6, '2026-03-02', 12500.00, 'sacos', 'Primera', 'Y5PA', 950.00, 'Maíz morado de excelente coronta y pigmentación.', '2026-03-02 17:00:00', NOW()), -- Cosecha de Cultivo 1 (Lote Y5PA)
(2, 11, '2026-03-20', 45000.00, 'kg', 'Primera', 'PTFI', 1400.00, 'Cebolla roja seleccionada para exportación.', '2026-03-20 18:00:00', NOW()),-- Cosecha de Cultivo 2 (Lote PTFI)
(3, 15, '2026-03-18', 18000.00, 'sacos', 'Primera', '41TA', 1050.00, 'Cosecha de terreno alquilado Fundo Quispe.', '2026-03-18 17:30:00', NOW()),-- Cosecha de Cultivo 3 (Lote 41TA)
(4, 23, '2026-05-25', 16000.00, 'kg', 'Primera', 'QUINUA CHAO', 1300.00, 'Quinua Blanca de primera calidad exportable.', '2026-05-25 18:00:00', NOW()),-- Cosecha de Cultivo 6 (Lote QUINUA CHAO)
(5, 26, '2026-06-18', 77000.00, 'kg', 'Primera', 'PAPA VIRÚ', 1800.00, 'Papa Amarilla Tumbay de tamaño primera.', '2026-06-18 17:00:00', NOW()) -- Cosecha de Cultivo 7 (Lote PAPA VIRÚ)
ON DUPLICATE KEY UPDATE `cantidad_kg` = VALUES(`cantidad_kg`);


-- ------------------------------------------------------------------------------
-- 8. VENTAS COMERCIALIZADAS (VINCULADAS STRICTAMENTE A SUS COSECHAS)
-- ------------------------------------------------------------------------------
INSERT INTO `compradores` (`id`, `nombre`, `ruc_dni`, `telefono`, `email`, `direccion`, `created_at`, `updated_at`) VALUES
(1, 'COMERCIALIZADORA AGROSUR S.A.C.', '20551234567', '054231245', 'compras@agrosur.pe', 'Mercado Mayorista El Palomar Puesto 45, Arequipa', '2025-11-01', NOW()),
(2, 'EXPORTADORA Y FRUTAS DEL PERÚ S.A.', '20449876543', '014567890', 'exportaciones@frutasperu.com', 'Av. Argentina 2400, Callao, Lima', '2025-11-15', NOW()),
(3, 'MERCADO CENTRAL Y ACOPIO MAJES', '20338877665', '054582123', 'acopio@majes.pe', 'Av. Arequipa S/N, Pedregal, Majes', '2025-12-01', NOW()),
(4, 'AGROINDUSTRIAS DEL NORTE E.I.R.L.', '20608877661', '044556677', 'ventas@agronorte.pe', 'Av. Mansiche 890, Trujillo', '2026-01-10', NOW())
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- Ventas vinculadas a Cosechas existentes (Cosechas 1, 2, 3, 4, 5)
INSERT INTO `ventas` (`id`, `cosecha_id`, `comprador_id`, `fecha_venta`, `cantidad_vendida_kg`, `precio_por_kg`, `costo_flete`, `impuestos`, `comprobante_tipo`, `comprobante_numero`, `foto_path`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-03-05', 8000.00, 3.20, 200.00, 0.00, 'factura', 'F001-000124', NULL, '2026-03-05 10:00:00', NOW()),
(2, 1, 3, '2026-03-10', 4500.00, 3.00, 100.00, 0.00, 'boleta', 'B001-000456', NULL, '2026-03-10 11:30:00', NOW()),
(3, 2, 2, '2026-03-25', 30000.00, 1.80, 500.00, 0.00, 'factura', 'F001-000125', NULL, '2026-03-25 15:00:00', NOW()),
(4, 3, 1, '2026-03-22', 18000.00, 3.10, 300.00, 0.00, 'factura', 'F001-000128', NULL, '2026-03-22 14:00:00', NOW()),
(5, 4, 4, '2026-05-30', 16000.00, 6.50, 400.00, 0.00, 'factura', 'F001-000140', NULL, '2026-05-30 11:00:00', NOW()),
(6, 5, 2, '2026-06-22', 77000.00, 2.20, 800.00, 0.00, 'factura', 'F001-000155', NULL, '2026-06-22 16:30:00', NOW())
ON DUPLICATE KEY UPDATE `cantidad_vendida_kg` = VALUES(`cantidad_vendida_kg`);


-- ------------------------------------------------------------------------------
-- 9. CONVERSACIONES Y MENSAJES DE CHAT REALES ENTRE COMPAÑEROS
-- ------------------------------------------------------------------------------
INSERT INTO `conversaciones` (`id`, `organizacion_id`, `tipo_conversacion`, `nombre_grupo`, `created_at`, `updated_at`) VALUES
(1, 1, 'grupal', 'Chat General - Cooperativa Valle del Sur', '2025-10-12 09:00:00', NOW()),
(2, 1, 'privada', 'Coordinación Fitosanitaria (Carlos & José Luis)', '2025-10-15 10:00:00', NOW()),
(3, 2, 'grupal', 'Coordinación de Riego y Exportación - Fundo San José', '2025-11-06 11:00:00', NOW())
ON DUPLICATE KEY UPDATE `nombre_grupo` = VALUES(`nombre_grupo`);

-- Participantes de Chat
INSERT INTO `conversaciones_participantes` (`id`, `conversacion_id`, `usuario_id`, `created_at`, `updated_at`) VALUES
(1, 1, 4, NOW(), NOW()), -- María Elena
(2, 1, 5, NOW(), NOW()), -- José Luis
(3, 1, 6, NOW(), NOW()), -- Ana Flores
(4, 1, 7, NOW(), NOW()), -- Pedro Castillo
(5, 2, 3, NOW(), NOW()), -- Carlos Mendoza
(6, 2, 5, NOW(), NOW())  -- José Luis
ON DUPLICATE KEY UPDATE `created_at` = NOW();

-- Mensajes de Chat
INSERT INTO `mensajes_chat` (`id`, `conversacion_id`, `remitente_usuario_id`, `es_ia`, `mensaje`, `leido`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 0, 'Bienvenidos a la plataforma AgroSys de la Cooperativa Valle del Sur. Por favor mantengamos los cuadernos de campo al día.', 1, '2025-10-12 09:05:00', NOW()),
(2, 1, 5, 0, 'Entendido Sra. María Elena. He programado el calendario de visitas técnicas para los lotes de Cebolla y Maíz Morado.', 1, '2025-10-12 09:15:00', NOW()),
(3, 1, 6, 0, 'Excelente. En el Fundo Los Uinuapata iniciaremos la siembra de Cebolla Arequipeña la próxima semana.', 1, '2025-10-15 10:20:00', NOW()),
(4, 2, 3, 0, 'Hola José Luis, estuve revisando la humedad en el lote Y5PA. El canal de riego por goteo presenta buena presión.', 1, '2025-10-15 10:30:00', NOW()),
(5, 2, 5, 0, 'Muchas gracias Ing. Carlos. Aplicaremos la Urea Granulada programada para el fertirriego este viernes.', 1, '2025-10-15 10:35:00', NOW())
ON DUPLICATE KEY UPDATE `mensaje` = VALUES(`mensaje`);


-- ------------------------------------------------------------------------------
-- 10. HISTORIAL DE PROCESOS / FORENSE / AUDITORÍA EN TIEMPO REAL
-- ------------------------------------------------------------------------------
INSERT INTO `historial_procesos` (`id`, `usuario_id`, `organizacion_id`, `tabla_afectada`, `registro_id`, `accion`, `descripcion`, `detalles_previos`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'usuarios', 1, 'INICIO DE SESIÓN', 'El usuario admin@agrosys.com inició sesión desde Google Chrome en Windows 10/11 (Escritorio)', '{"ip":"192.168.1.100","dispositivo":"Google Chrome en Windows 10/11 (Escritorio)","ubicacion_ip":"Red Local / Servidor AgroSys"}', '2025-10-01 08:00:00', NOW()),
(2, 2, NULL, 'usuarios', 2, 'INICIO DE SESIÓN', 'El usuario roosivelt.td@gmail.com inició sesión desde Google Chrome en Linux (Escritorio)', '{"ip":"192.168.1.105","dispositivo":"Google Chrome en Linux (Escritorio)","ubicacion_ip":"Red Local / Servidor AgroSys"}', '2025-10-02 09:30:00', NOW()),
(3, 2, 1, 'terrenos', 1, 'REGISTRO TERRENO', 'Se registró un terreno: INIA Majes Parcela 12 (5.50 Ha)', '{"nombre":"INIA Majes Parcela 12","hectareas":"5.50","tipo_tenencia":"propio"}', '2025-10-15 09:00:00', NOW()),
(4, 2, 1, 'cultivos', 1, 'REGISTRO CULTIVO', 'Se registró un cultivo: Maíz Morada (Lote Y5PA)', '{"nombre_lote":"Y5PA","area_destinada":"2.50","estado":"Planificado"}', '2025-10-25 10:00:00', NOW()),
(5, 2, 1, 'labores', 1, 'EJECUCIÓN LABOR', 'Labor de Preparar Terreno realizada en el cultivo Maíz Morada (Lote Y5PA) del terreno INIA Majes Parcela 12', '{"costo_total":"550.00","categoria":"Preparación"}', '2025-10-26 10:00:00', NOW()),
(6, 2, 1, 'cosechas', 1, 'REGISTRO COSECHA', 'Cosecha registrada: 12500 kg (Calidad Primera) - Lote Y5PA', '{"cantidad_kg":12500,"calidad":"Primera"}', '2026-03-02 17:00:00', NOW()),
(7, 2, 1, 'ventas', 1, 'REGISTRO VENTA', 'Venta registrada a COMERCIALIZADORA AGROSUR S.A.C. por S/ 25,600.00', '{"cantidad_vendida_kg":8000,"precio_por_kg":3.2}', '2026-03-05 10:00:00', NOW())
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`);

SET FOREIGN_KEY_CHECKS = 1;
