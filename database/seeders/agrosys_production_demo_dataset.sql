-- ==============================================================================
-- AGROSYS SAAS - CATÁLOGOS BASE (CULTIVOS Y LABORES)
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. ROLES GLOBAL Y DE ORGANIZACIÓN
INSERT IGNORE INTO `rol` (`id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'Administrador Maestro Global de la Plataforma SaaS AgroSys', NOW(), NOW()),
(2, 'Agricultor', 'Usuario Operativo, Productor y Propietario de Parcelas Agrícolas', NOW(), NOW());

INSERT IGNORE INTO `roles_organizacion` (`id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'Líder administrativo de la empresa o cooperativa agrícola', NOW(), NOW()),
(2, 'Supervisor', 'Ingeniero agrónomo o técnico encargado del monitoreo de campo', NOW(), NOW()),
(3, 'Agricultor', 'Productor y encargado directo de las labores en parcela', NOW(), NOW());

-- 2. CATÁLOGO MAESTRO DE CULTIVOS (5 CULTIVOS BASE CON RANGOS DE RENDIMIENTO)
INSERT IGNORE INTO `catalogo_cultivos` (`id`, `nombre`, `nombre_cientifico`, `tipo_ciclo`, `vida_util_estimada_meses`, `dias_a_cosecha_promedio`, `rendimiento_min_tn_ha`, `rendimiento_max_tn_ha`, `instrucciones_base_riego`, `instrucciones_base_plagas`, `es_personalizado`, `created_at`, `updated_at`) VALUES
(1, 'Maíz', 'Zea mays L.', 'ciclo_corto', 5, 120, 5.00, 6.00, 'Riego por goteo o gravedad cada 7 a 10 días.', 'Control preventivo de cogollero.', 0, NOW(), NOW()),
(2, 'Cebolla', 'Allium cepa L.', 'ciclo_corto', 6, 140, 15.00, 20.00, 'Riego frecuente ligero por goteo.', 'Monitoreo de trips y mildeu.', 0, NOW(), NOW()),
(3, 'Papa', 'Solanum tuberosum', 'ciclo_corto', 5, 130, 18.00, 22.00, 'Riego moderado en floración.', 'Prevención de rancha (Phytophthora).', 0, NOW(), NOW()),
(4, 'Palta', 'Persea americana', 'perenne', 240, 300, 12.00, 15.00, 'Riego tecnificado por goteo continuo.', 'Control de queresa y oidium.', 0, NOW(), NOW()),
(5, 'Quinua', 'Chenopodium quinoa', 'ciclo_corto', 6, 150, 3.00, 4.00, 'Resistente a sequía, riego suplementario.', 'Vigilancia de polilla de la quinua.', 0, NOW(), NOW());

-- 3. CATÁLOGO MAESTRO DE LABORES (LAS 9 LABORES AGRONÓMICAS)
INSERT IGNORE INTO `catalogo_labores` (`id`, `nombre`, `categoria`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'Preparar Terreno', 'preparacion', 'Mecanización, arado y surcado de suelo', NOW(), NOW()),
(2, 'Siembra', 'siembra', 'Instalación de semilla en surco o cinta de goteo', NOW(), NOW()),
(3, 'Riego', 'mantenimiento', 'Suministro de agua tecnificada y fertirriego', NOW(), NOW()),
(4, 'Fumigación', 'mantenimiento', 'Aplicación de fungicidas e insecticidas', NOW(), NOW()),
(5, 'Abonado Orgánico', 'mantenimiento', 'Aplicación de compost, humus y fertirriego foliar', NOW(), NOW()),
(6, 'Cosecha', 'cosecha', 'Recolección, selección y ensacado en campo', NOW(), NOW()),
(7, 'Aporque', 'mantenimiento', 'Aporque de tierra y levantamiento de surcos', NOW(), NOW()),
(8, 'Deshierbe', 'mantenimiento', 'Desyerbado y control manual de maleza', NOW(), NOW()),
(9, 'Otros Trabajos', 'mantenimiento', 'Labores generales y mantenimiento complementario', NOW(), NOW());

-- 4. CATÁLOGO MAESTRO DE INSUMOS
INSERT IGNORE INTO `catalogo_insumos` (`id`, `nombre`, `unidad_medida`, `created_at`, `updated_at`) VALUES
(1, 'Urea Granulada 46% N', 'kg', NOW(), NOW()),
(2, 'NPK 15-15-15', 'kg', NOW(), NOW()),
(3, 'Fungicida Ridomil Gold', 'litros', NOW(), NOW()),
(4, 'Insecticida Karate Zeon', 'litros', NOW(), NOW()),
(5, 'Compost Orgánico Certificado', 'sacos', NOW(), NOW());

-- 4.1. CATÁLOGO BASE DE TIPOS DE MANO DE OBRA
INSERT IGNORE INTO `mano_obra_tipo` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Jornalero', NOW(), NOW()),
(2, 'Operador de Maquinaria', NOW(), NOW()),
(3, 'Especialista Técnico', NOW(), NOW());

-- 5. USUARIOS ADMINISTRADORES BASE DE LA PLATAFORMA
INSERT IGNORE INTO `usuarios` (`id`, `rol_id`, `nombres`, `apellidos`, `email`, `email_verified_at`, `password`, `telefono`, `dni`, `estado`, `experiencia_anios`, `nivel_educativo`, `ubicacion`, `descripcion`, `is_activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Super', 'Admin', 'admin@agrosys.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '999888777', '00000000', 1, 10, 'Universitario', 'Lima, Perú', 'Administrador Maestro del Sistema AgroSys', 1, '2025-10-01 08:00:00', NOW()),
(2, 1, 'Roosivelt', 'Tupla Dipaz', 'roosivelt.td@gmail.com', NOW(), '$2y$12$lGaRilee20.wf4a1GqO1Be7EexOZOV0NDFIinwwCQEb4YdThJqH0O', '912345678', '71234567', 1, 8, 'Ingeniería', 'Arequipa, Perú', 'Propietario e Investigador AgroSys', 1, '2025-10-02 09:30:00', NOW());

SET FOREIGN_KEY_CHECKS = 1;
