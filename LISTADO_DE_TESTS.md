# 📋 Listado Completo de Pruebas Automatizadas de AgroSys V0.9

> **Estado General:** 51/51 Pruebas Pasadas al 100% (145 Aserciones de Control de Calidad)  
> **Comando de Ejecución:** `docker exec agrosys-app php artisan test` o `php artisan test`

---

## 📊 Resumen por Categoría de Pruebas

| Categoría de Prueba | Archivos / Suites | N° de Tests | Estado |
| :--- | :--- | :---: | :---: |
| 🔐 **Autenticación, Registro y Perfil** | `AuthenticationTest`, `RegistrationTest`, `ProfileTest`, etc. | **24** | PASS ✅ |
| 🚜 **Módulos Agrícolas y Comerciales** | `AgroSysFullWorkflowTest`, `LivewireManagersTest` | **8** | PASS ✅ |
| 👨‍💼 **Supervisión y Asignación de Tareas** | `SupervisionAndTasksTest`, `OrganizacionControllerTest` | **7** | PASS ✅ |
| 🤖 **Servicios de IA, Clima y Almacenamiento** | `ServicesTest`, `AgroSysServicesAndModulesTest` | **6** | PASS ✅ |
| 🛡️ **Auditoría Forense y Modelos** | `ModelsAndObserverTest` | **6** | PASS ✅ |
| **TOTAL GENERAL** | **16 Suites de Pruebas** | **51** | **100% PASS ✅** |

---

## 🛠️ Pruebas Unitarias (`tests/Unit/`)

### 1. `tests/Unit/ExampleTest.php` (1 Test)
* `test_that_true_is_true`: Sanidad básica de ejecución del motor de pruebas PHPUnit.

### 2. `tests/Unit/ModelsAndObserverTest.php` (2 Tests)
* `test_observer_records_historial_procesos`: Verifica que los eventos Eloquent (`created`, `updated`, `deleted`) en modelos registrados son capturados por `AgroAuditObserver` y guardados en `historial_procesos`.
* `test_eloquent_relationships_chain`: Verifica la integridad de la cadena de relaciones Eloquent: `User` &rarr; `Terreno` &rarr; `Cultivo` &rarr; `Labor` &rarr; `Cosecha` &rarr; `Venta`.

### 3. `tests/Unit/ServicesTest.php` (4 Tests)
* `test_agro_storage_service_user_file_upload`: Verifica la estructura jerárquica de almacenamiento de archivos de usuario (`users/{hash}/{tipo}/{categoria}`) en `AgroStorageService::storeUserFile()`.
* `test_agro_storage_service_chat_file_upload`: Verifica el almacenamiento de adjuntos en conversaciones de chat (`chats/private/{hash}/img`).
* `test_weather_service_fetches_weather_data`: Verifica la consulta, procesamiento y formateo de datos climáticos y pronósticos satelitales en `WeatherService::getWeatherByCoords()`.
* `test_agrobot_service_ai_coordination`: Verifica la generación de dictámenes agronómicos estratégicos en viñetas formateadas por `AgroBotService::coordinateStrategicPlan()`.

---

## 🚀 Pruebas de Funcionalidades y Flujos Integrados (`tests/Feature/`)

### 4. `tests/Feature/AgroSysFullWorkflowTest.php` (4 Tests)
* `test_can_create_and_manage_terreno`: Verifica la creación de terrenos con geolocalización, polígonos GPS y cálculo de hectáreas mediante `TerrenosManager`.
* `test_can_create_and_track_cultivo`: Verifica la siembra y seguimiento de lotes de cultivo con estimación de rendimiento en `CultivosManager`.
* `test_full_labor_harvest_and_sale_cycle`: Verifica el ciclo productivo y comercial completo: ejecución de labor, registro de cosecha obtenida y venta comercial a comprador con comprobante.
* `test_agrobot_ai_service_response`: Verifica las respuestas e instrucciones técnicas generadas por el motor de IA en tiempo real.

### 5. `tests/Feature/AgroSysServicesAndModulesTest.php` (4 Tests)
* `test_services_coverage`: Prueba de integración amplia para servicios climáticos, telemetría y respuestas multimodelo en Gemini 3.5 Flash.
* `test_audit_observer_coverage`: Prueba de auditoría forense para acciones `INSERT`, `UPDATE` y `DELETE` en la entidad `Organizacion`.
* `test_models_and_custom_attributes_coverage`: Prueba de atributos calculados personalizados como `area_ocupada`, `area_disponible`, `is_alquiler_vencido` y `descripcion_completa`.
* `test_livewire_managers_coverage`: Pruebas de renderizado y ejecución en los controladores Livewire (`TerrenosManager`, `CultivosManager`, `LaboresManager`, `CosechasManager`, `VentasManager`).

### 6. `tests/Feature/SupervisionAndTasksTest.php` (4 Tests)
* `test_admin_can_render_gestion_miembros_and_assign_supervisor`: Verifica la asignación directa de un agricultor a un supervisor por parte del Administrador de la Organización.
* `test_supervisor_can_send_suggestion_to_agricultor`: Verifica la creación y envío de sugerencias/órdenes de trabajo técnicas del supervisor al agricultor.
* `test_agricultor_can_view_and_complete_suggestion`: Verifica que el agricultor abre el modal de la sugerencia, ingresa comentarios y la marca como completada.
* `test_chat_manager_opens_direct_conversation_with_user`: Verifica la apertura directa del chat privado entre el supervisor y el agricultor (`/mensajeria?user={id}`).

### 7. `tests/Feature/LivewireManagersTest.php` (4 Tests)
* `test_notification_center_renders_and_marks_notifications_read`: Verifica el centro de notificaciones y el marcado de leídos.
* `test_cosechas_manager_renders`: Verifica el renderizado del gestor de cosechas.
* `test_labores_manager_renders`: Verifica el renderizado del gestor de labores agrícolas.
* `test_ventas_manager_renders`: Verifica el renderizado del gestor de comercialización y ventas.

### 8. `tests/Feature/OrganizacionControllerTest.php` (3 Tests)
* `test_registrar_y_aprobar_organizacion`: Verifica la solicitud formal de creación de empresa y su aprobación por el SuperAdmin.
* `test_invitar_usuario_y_aprobar_ingreso`: Verifica la invitación a un usuario por DNI y la aceptación de ingreso a la organización.
* `test_asignar_y_eliminar_supervisor`: Verifica el otorgamiento y revocación de cargos de supervisor en la empresa.

### 9. `tests/Feature/ProfileTest.php` (5 Tests)
* `test_profile_page_is_displayed`: Muestra de la vista del perfil de usuario.
* `test_profile_information_can_be_updated`: Actualización de datos personales con validaciones estricta de Nombres, DNI, Teléfono y Ubicación.
* `test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged`: Preservación del estado de verificación si el correo no cambia.
* `test_user_can_delete_their_account`: Eliminación voluntaria de la cuenta del usuario.
* `test_correct_password_must_be_provided_to_delete_account`: Verificación obligatoria de contraseña antes de eliminar la cuenta.

### 10. `tests/Feature/Auth/AuthenticationTest.php` (5 Tests)
* `test_login_screen_can_be_rendered`: Renderizado de la pantalla de inicio de sesión.
* `test_users_can_authenticate_using_the_login_screen`: Autenticación exitosa de usuarios registrados.
* `test_users_can_not_authenticate_with_invalid_password`: Bloqueo de acceso con contraseñas erróneas.
* `test_navigation_menu_can_be_rendered`: Renderizado del menú de navegación tras iniciar sesión.
* `test_users_can_logout`: Cierre de sesión seguro y destrucción de datos de sesión.

### 11. `tests/Feature/Auth/EmailVerificationTest.php` (3 Tests)
* `test_email_verification_screen_can_be_rendered`: Pantalla de aviso de verificación de correo.
* `test_email_can_be_verified`: Verificación del correo mediante enlace firmado.
* `test_email_is_not_verified_with_invalid_hash`: Rechazo de tokens o hashes de verificación inválidos.

### 12. `tests/Feature/Auth/PasswordConfirmationTest.php` (3 Tests)
* `test_confirm_password_screen_can_be_rendered`: Pantalla de confirmación de contraseña.
* `test_password_can_be_confirmed`: Confirmación exitosa de la clave.
* `test_password_is_not_confirmed_with_invalid_password`: Bloqueo cuando la clave es incorrecta.

### 13. `tests/Feature/Auth/PasswordResetTest.php` (4 Tests)
* `test_reset_password_link_screen_can_be_rendered`: Vista para solicitar enlace de recuperación.
* `test_reset_password_link_can_be_requested`: Envío exitoso del correo de recuperación.
* `test_reset_password_screen_can_be_rendered`: Pantalla de cambio de contraseña.
* `test_password_can_be_reset_with_valid_token`: Restablecimiento exitoso de la clave con token válido.

### 14. `tests/Feature/Auth/PasswordUpdateTest.php` (2 Tests)
* `test_password_can_be_updated`: Actualización exitosa de contraseña.
* `test_correct_password_must_be_provided_to_update_password`: Exigencia de contraseña previa.

### 15. `tests/Feature/Auth/RegistrationTest.php` (2 Tests)
* `test_registration_screen_can_be_rendered`: Formulario de registro de usuario.
* `test_new_users_can_register`: Registro exitoso con sanitización y validaciones de DNI.

### 16. `tests/Feature/ExampleTest.php` (1 Test)
* `test_the_application_returns_a_successful_response`: Verificación de respuesta exitosa en la ruta raíz del proyecto.
