package com.sigcpa.agrosys.ui

import android.content.Context
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.widget.ImageView
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.lifecycleScope
import com.canhub.cropper.CropImageContract
import com.canhub.cropper.CropImageContractOptions
import com.canhub.cropper.CropImageOptions
import com.canhub.cropper.CropImageView
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.*
import com.sigcpa.agrosys.database.dao.UsuarioWithRol
import com.sigcpa.agrosys.database.dao.RedSocialWithMetadata
import com.sigcpa.agrosys.databinding.ActivityProfileBinding
import com.sigcpa.agrosys.databinding.DialogAddSocialNetworkBinding
import com.sigcpa.agrosys.databinding.DialogChangePasswordBinding
import com.google.android.material.card.MaterialCardView
import com.google.android.material.dialog.MaterialAlertDialogBuilder
import com.google.android.material.switchmaterial.SwitchMaterial
import com.google.android.material.textfield.TextInputEditText
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.io.File
import java.io.FileOutputStream

class ProfileActivity : AppCompatActivity() {

    private lateinit var binding: ActivityProfileBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    private var currentUser: UsuarioEntity? = null
    private var userWithRol: com.sigcpa.agrosys.database.dao.UsuarioWithRol? = null
    private var isPickingProfile = true

    private val cropImage = registerForActivityResult(CropImageContract()) { result ->
        if (result.isSuccessful) {
            val uriContent = result.uriContent
            if (uriContent != null) {
                saveImageLocally(uriContent, isPickingProfile)
            }
        } else {
            val error = result.error
            error?.printStackTrace()
        }
    }

    private val availableSocialTypes = listOf(
        SocialTypeData("Facebook", R.drawable.red_social_facebook, "#ebfff4"),
        SocialTypeData("Instagram", R.drawable.red_social_instagram, "#ebfff4"),
        SocialTypeData("WhatsApp", R.drawable.red_social_whatsapp, "#ebfff4"),
        SocialTypeData("TikTok", R.drawable.red_social_tiktok, "#ebfff4"),
        SocialTypeData("Telegram", R.drawable.red_socila_telegram, "#ebfff4"),
        SocialTypeData("YouTube", R.drawable.ic_youtube, "#ebfff4"),
        SocialTypeData("Twitter", R.drawable.red_social_twitter_01, "#ebfff4"),
        SocialTypeData("Threads", R.drawable.red_social_hreads, "#ebfff4"), 
    )

    data class SocialTypeData(val name: String, val iconRes: Int, val color: String)

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityProfileBinding.inflate(layoutInflater)
        setContentView(binding.root)

        window.statusBarColor = android.graphics.Color.parseColor("#00582a")
        WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        androidx.core.view.ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(androidx.core.view.WindowInsetsCompat.Type.systemBars())
            binding.appBarLayout.setPadding(0, systemBars.top, 0, 0)
            insets
        }

        setupStats()
        setupMenu()
        setupListeners()
        loadUserData()
    }

    private fun setupStats() {
        binding.statCrops.apply {
            findViewById<ImageView>(R.id.ivStatIcon).setImageResource(R.drawable.agro_planta_hoja)
            findViewById<TextView>(R.id.tvStatValue).text = "0"
            findViewById<TextView>(R.id.tvStatLabel).text = "CULTIVOS"
        }
        binding.statBalance.apply {
            findViewById<ImageView>(R.id.ivStatIcon).setImageResource(R.drawable.ic_money_01)
            findViewById<TextView>(R.id.tvStatValue).text = "S/ 0.0"
            findViewById<TextView>(R.id.tvStatLabel).text = "BALANCE"
        }
        binding.statHarvests.apply {
            findViewById<ImageView>(R.id.ivStatIcon).setImageResource(R.drawable.agro_maquinaria_tractor)
            findViewById<TextView>(R.id.tvStatValue).text = "0"
            findViewById<TextView>(R.id.tvStatLabel).text = "COSECHAS"
        }
    }

    private fun setupMenu() {
        binding.menuPersonalInfo.apply {
            ivMenuIcon.setImageResource(R.drawable.ic_user_modern)
            tvMenuTitle.text = "Información Personal"
            root.setOnClickListener { showEditProfileModal() }
        }

        binding.menuSecurity.apply {
            ivMenuIcon.setImageResource(R.drawable.ic_admin)
            tvMenuTitle.text = "Cambio de Contraseña"
            root.setOnClickListener { showChangePasswordDialog() }
        }

        binding.menuLogout.apply {
            ivMenuIcon.setImageResource(R.drawable.ic_cerrar_sesion)
            ivMenuIcon.setColorFilter(android.graphics.Color.parseColor("#DC2626"))
            tvMenuTitle.text = "Salir"
            tvMenuTitle.setTextColor(android.graphics.Color.parseColor("#DC2626"))
            root.setOnClickListener { showLogoutConfirmationDialog() }
        }
    }

    private fun showLogoutConfirmationDialog() {
        MaterialAlertDialogBuilder(this)
            .setTitle("Cerrar Sesión")
            .setMessage("¿Estás seguro de que deseas salir de AgroSys?")
            .setNegativeButton("Cancelar", null)
            .setPositiveButton("Salir") { _, _ ->
                val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
                sharedPref.edit().remove("USER_ID").apply()
                finish()
            }
            .show()
    }

    private fun setupListeners() {
        binding.btnChangeAvatar.setOnClickListener {
            isPickingProfile = true
            startImagePicker(true)
        }
        binding.btnEditProfileHeader.setOnClickListener {
            isPickingProfile = false
            startImagePicker(false)
        }
        binding.btnAddSocial.setOnClickListener {
            showAddSocialDialog(null)
        }

        binding.btnRequestSupervisor.setOnClickListener { showRequestSupervisorDialog() }
    }

    private fun loadUserData() {
        val sharedPref = getSharedPreferences("agrosys_prefs", Context.MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)

        lifecycleScope.launch {
            userWithRol = db.userDao().getUsuarioById(userId)
            currentUser = userWithRol?.usuario
            updateUIWithUserData()
            loadUserOrganizaciones()
        }
    }

    private fun updateUIWithUserData() {
        userWithRol?.let { wrap ->
            val user = wrap.usuario
            binding.tvProfileName.text = "${user.nombre} ${user.apellidos}"
            binding.tvProfileLocation.text = user.ubicacion ?: "Ubicación no definida"
            binding.tvProfileRole.text = wrap.nombre_rol.replaceFirstChar { it.uppercase() }
            
            // Lógica de botones de organización e información de rol
            lifecycleScope.launch {
                val orgAdmin = db.userDao().getOrganizacionByAdmin(user.id)
                val userOrgs = db.userDao().getOrganizacionesByUsuario(user.id)
                val solicitudPendiente = db.userDao().getSolicitudPendienteByUser(user.id)

                // Mostrar rol como Admin si es creador de alguna org, aunque su rol global sea usuario
                if (orgAdmin != null && wrap.nombre_rol == "usuario") {
                    binding.tvProfileRole.text = "Admin de Organización"
                } else {
                    binding.tvProfileRole.text = wrap.nombre_rol.replaceFirstChar { it.uppercase() }
                }

                // Botón Crear: Si ya tiene org, cambia texto según img
                if (orgAdmin != null) {
                    binding.btnCreateOrgText.text = orgAdmin.nombre
                    binding.ivEditOrgIcon.visibility = View.VISIBLE
                    binding.ivEditOrgIcon.setOnClickListener { showEditOrgDialog(orgAdmin) }
                    binding.btnCreateOrgCard.setOnClickListener { showOrganizationDetailsDialog(orgAdmin) }
                } else {
                    binding.btnCreateOrgText.text = "CREAR ORGANIZACIÓN"
                    binding.ivEditOrgIcon.visibility = View.GONE
                    binding.btnCreateOrgCard.setOnClickListener { showCreateOrgDialog() }
                }
                
                // Botón Unirse: Siempre visible
                binding.btnJoinOrgCard.setOnClickListener { showJoinOrgDialog() }
                binding.btnJoinOrgCard.visibility = View.VISIBLE
                
                // Botón Solicitar Supervisor: Solo para quienes se unieron (no creadores/admins)
                val esCreadorAdmin = orgAdmin != null
                val yaEsSupervisorOAdminGlobal = wrap.nombre_rol != "usuario"
                val tieneSolicitudSupervisor = solicitudPendiente?.tipo_solicitud == "ascenso_supervisor"

                if (userOrgs.isNotEmpty() && !esCreadorAdmin && !yaEsSupervisorOAdminGlobal) {
                    binding.btnRequestSupervisor.visibility = View.VISIBLE
                    if (tieneSolicitudSupervisor) {
                        binding.btnRequestSupervisor.isEnabled = false
                        binding.btnRequestSupervisor.text = "SOLICITUD ENVIADA"
                    } else {
                        binding.btnRequestSupervisor.isEnabled = true
                        binding.btnRequestSupervisor.text = "SOLICITAR SER SUPERVISOR"
                    }
                } else {
                    binding.btnRequestSupervisor.visibility = View.GONE
                }
            }
            
            user.foto_perfil_url?.let { path ->
                val file = File(path)
                if (file.exists()) {
                    com.bumptech.glide.Glide.with(this)
                        .load(file)
                        .circleCrop()
                        .diskCacheStrategy(com.bumptech.glide.load.engine.DiskCacheStrategy.NONE)
                        .skipMemoryCache(true)
                        .into(binding.ivProfileAvatar)
                    binding.ivProfileAvatar.imageTintList = null
                    binding.ivProfileAvatar.setPadding(0, 0, 0, 0)
                }
            }

            user.foto_portada_url?.let { path ->
                val file = File(path)
                if (file.exists()) {
                    com.bumptech.glide.Glide.with(this)
                        .load(file)
                        .centerCrop()
                        .diskCacheStrategy(com.bumptech.glide.load.engine.DiskCacheStrategy.NONE)
                        .skipMemoryCache(true)
                        .into(binding.ivProfilePortada)
                }
            }

            val infoDescription = StringBuilder()
            
            // Línea 1: 🎓 Nivel Educativo | 📘 DNI | 📱 Teléfono
            val linea1Items = mutableListOf<String>()
            user.nivel_educativo?.let { linea1Items.add("🎓 $it") }
            user.dni?.let { linea1Items.add("📘 DNI: $it") }
            user.telefono?.let { linea1Items.add("📱 $it") }
            val linea1 = linea1Items.joinToString(" | ")
            
            // Línea 2: Experiencia
            val linea2 = "🌾 Sostenibilidad | 🚜 ${user.experiencia_anios} años de experiencia | 📸 AgroSys"
            
            infoDescription.append(linea1).append("\n").append(linea2)
            binding.tvProfileDescription.text = infoDescription.toString()
            
            loadUserSocialNetworks(user.id)
        }
    }

    private fun loadUserOrganizaciones() {
        val user = currentUser ?: return
        lifecycleScope.launch {
            val orgs = db.userDao().getOrganizacionesByUsuario(user.id)
            binding.llOrgsContainer.removeAllViews()
            
            if (orgs.isNotEmpty()) {
                val header = TextView(this@ProfileActivity).apply {
                    text = "MIS ORGANIZACIONES"
                    textSize = 12f
                    setTextColor(android.graphics.Color.parseColor("#15803D"))
                    setTypeface(null, android.graphics.Typeface.BOLD)
                    setPadding(0, 48, 0, 16)
                    letterSpacing = 0.1f
                }
                binding.llOrgsContainer.addView(header)
            }

            orgs.forEach { org ->
                val orgView = LayoutInflater.from(this@ProfileActivity).inflate(R.layout.item_profile_menu, binding.llOrgsContainer, false)
                val title = orgView.findViewById<TextView>(R.id.tvMenuTitle)
                val subtitle = orgView.findViewById<TextView>(R.id.tvMenuSubtitle)
                val icon = orgView.findViewById<ImageView>(R.id.ivMenuIcon)
                
                title.text = org.nombre
                subtitle.text = if (org.administrador_id == user.id) "Administrador" else "Miembro"
                subtitle.visibility = View.VISIBLE
                icon.setImageResource(R.drawable.ic_user_modern)
                
                orgView.setOnClickListener { showOrganizationDetailsDialog(org) }
                
                binding.llOrgsContainer.addView(orgView)
            }
        }
    }

    private fun showOrganizationDetailsDialog(org: OrganizacionEntity) {
        val user = currentUser ?: return
        lifecycleScope.launch {
            val miembros = db.userDao().getMiembrosByOrganizacion(org.id)
            val admin = db.userDao().getUsuarioById(org.administrador_id)
            val isCurrentUserAdmin = org.administrador_id == user.id
            
            val message = StringBuilder()
            message.append("📍 Ubicación: ${org.direccion ?: "No definida"}\n")
            message.append("👑 Admin: ${admin?.usuario?.nombre ?: "N/A"}\n\n")
            message.append("👥 Miembros Activos (${miembros.size}):\n")
            miembros.forEach { m ->
                message.append("• ${m.nombre} ${m.apellidos}\n")
            }

            val builder = MaterialAlertDialogBuilder(this@ProfileActivity)
                .setTitle(org.nombre)
                .setMessage(message.toString())
                .setPositiveButton("Cerrar", null)

            if (isCurrentUserAdmin) {
                builder.setNeutralButton("Editar") { _, _ ->
                    showEditOrgDialog(org)
                }
                builder.setNegativeButton("Eliminar") { _, _ ->
                    confirmDeleteOrg(org, miembros.size)
                }
            }

            builder.show()
        }
    }

    private fun showEditOrgDialog(org: OrganizacionEntity) {
        val dialogView = layoutInflater.inflate(R.layout.dialog_edit_org, null)
        val etName = dialogView.findViewById<com.google.android.material.textfield.TextInputEditText>(R.id.etOrgName)
        val etRuc = dialogView.findViewById<com.google.android.material.textfield.TextInputEditText>(R.id.etOrgRuc)
        val etType = dialogView.findViewById<com.google.android.material.textfield.TextInputEditText>(R.id.etOrgType)
        val etRubro = dialogView.findViewById<com.google.android.material.textfield.TextInputEditText>(R.id.etOrgRubro)
        val etAddress = dialogView.findViewById<com.google.android.material.textfield.TextInputEditText>(R.id.etOrgAddress)
        val etPhone = dialogView.findViewById<com.google.android.material.textfield.TextInputEditText>(R.id.etOrgPhone)
        val etDesc = dialogView.findViewById<com.google.android.material.textfield.TextInputEditText>(R.id.etOrgDescription)

        etName.setText(org.nombre)
        etRuc.setText(org.ruc)
        etType.setText(org.tipo)
        etRubro.setText(org.rubro)
        etAddress.setText(org.direccion)
        etPhone.setText(org.telefono_contacto)
        etDesc.setText(org.descripcion)

        MaterialAlertDialogBuilder(this)
            .setTitle("Editar Organización")
            .setView(dialogView)
            .setPositiveButton("Guardar") { _, _ ->
                val newName = etName.text.toString()
                if (newName.isNotBlank()) {
                    lifecycleScope.launch {
                        val updatedOrg = org.copy(
                            nombre = newName,
                            ruc = etRuc.text.toString(),
                            tipo = etType.text.toString(),
                            rubro = etRubro.text.toString(),
                            direccion = etAddress.text.toString(),
                            telefono_contacto = etPhone.text.toString(),
                            descripcion = etDesc.text.toString(),
                            updated_at = System.currentTimeMillis() / 1000
                        )
                        db.userDao().updateOrganizacion(updatedOrg)
                        Toast.makeText(this@ProfileActivity, "Organización actualizada", Toast.LENGTH_SHORT).show()
                        loadUserOrganizaciones()
                        updateUIWithUserData()
                    }
                }
            }
            .setNegativeButton("Cancelar", null)
            .show()
    }

    private fun confirmDeleteOrg(org: OrganizacionEntity, miembrosCount: Int) {
        val canDeleteDirectly = miembrosCount <= 3
        val title = if (canDeleteDirectly) "Eliminar Organización" else "Solicitar Eliminación (70% Votos)"
        val message = if (canDeleteDirectly) 
            "¿Estás seguro de eliminar '${org.nombre}'? Esta acción es irreversible." 
            else "La organización tiene más de 3 miembros. Se requiere una votación con el 70% de aprobación para eliminarla. ¿Deseas iniciar la votación?"

        MaterialAlertDialogBuilder(this)
            .setTitle(title)
            .setMessage(message)
            .setPositiveButton(if (canDeleteDirectly) "Eliminar" else "Iniciar Voto") { _, _ ->
                lifecycleScope.launch {
                    if (canDeleteDirectly) {
                        db.userDao().deleteOrganizacion(org)
                        Toast.makeText(this@ProfileActivity, "Organización eliminada", Toast.LENGTH_SHORT).show()
                        loadUserOrganizaciones()
                        updateUIWithUserData()
                    } else {
                        // Aquí iría la lógica de votación real. Por ahora simulamos el inicio.
                        Toast.makeText(this@ProfileActivity, "Votación de eliminación iniciada (Req. 70%)", Toast.LENGTH_LONG).show()
                    }
                }
            }
            .setNegativeButton("Cancelar", null)
            .show()
    }

    private fun showCreateOrgDialog() {
        val dialogView = layoutInflater.inflate(R.layout.dialog_edit_org, null)
        val etName = dialogView.findViewById<com.google.android.material.textfield.TextInputEditText>(R.id.etOrgName)
        val etRuc = dialogView.findViewById<com.google.android.material.textfield.TextInputEditText>(R.id.etOrgRuc)
        val etType = dialogView.findViewById<com.google.android.material.textfield.TextInputEditText>(R.id.etOrgType)
        val etRubro = dialogView.findViewById<com.google.android.material.textfield.TextInputEditText>(R.id.etOrgRubro)
        val etAddress = dialogView.findViewById<com.google.android.material.textfield.TextInputEditText>(R.id.etOrgAddress)
        val etPhone = dialogView.findViewById<com.google.android.material.textfield.TextInputEditText>(R.id.etOrgPhone)
        val etDesc = dialogView.findViewById<com.google.android.material.textfield.TextInputEditText>(R.id.etOrgDescription)

        MaterialAlertDialogBuilder(this)
            .setTitle("Nueva Organización")
            .setView(dialogView)
            .setPositiveButton("Crear") { _, _ ->
                val orgName = etName.text.toString()
                if (orgName.isNotBlank()) {
                    lifecycleScope.launch {
                        currentUser?.let { user ->
                            val newOrg = OrganizacionEntity(
                                nombre = orgName,
                                administrador_id = user.id,
                                ruc = etRuc.text.toString(),
                                tipo = etType.text.toString(),
                                rubro = etRubro.text.toString(),
                                direccion = etAddress.text.toString(),
                                telefono_contacto = etPhone.text.toString(),
                                descripcion = etDesc.text.toString()
                            )
                            val orgId = db.userDao().insertOrganizacion(newOrg)
                            
                            // Agregar al creador como miembro activo (admin)
                            val miembro = MiembroOrganizacionEntity(
                                usuario_id = user.id,
                                organizacion_id = orgId.toInt(),
                                estado = "activo"
                            )
                            db.userDao().insertMiembroOrganizacion(miembro)
                            
                            Toast.makeText(this@ProfileActivity, "Organización creada con éxito", Toast.LENGTH_SHORT).show()
                            loadUserOrganizaciones()
                            updateUIWithUserData()
                        }
                    }
                }
            }
            .setNegativeButton("Cancelar", null)
            .show()
    }

    private fun showJoinOrgDialog() {
        lifecycleScope.launch {
            val user = currentUser ?: return@launch
            val userOrgsIds = db.userDao().getOrganizacionesByUsuario(user.id).map { it.id }.toSet()
            val filtradas = db.userDao().getAllOrganizaciones().filter { it.id !in userOrgsIds }
            val nombres = filtradas.map { it.nombre }.toTypedArray()

            if (filtradas.isEmpty()) {
                Toast.makeText(this@ProfileActivity, "No hay nuevas organizaciones para unirse", Toast.LENGTH_SHORT).show()
                return@launch
            }
            
            com.google.android.material.dialog.MaterialAlertDialogBuilder(this@ProfileActivity)
                .setTitle("Unirse a Organización")
                .setItems(nombres) { _, which ->
                    val orgSeleccionada = filtradas[which]
                    lifecycleScope.launch {
                        val solicitud = SolicitudUsuarioEntity(
                            usuario_id = user.id,
                            organizacion_id = orgSeleccionada.id,
                            tipo_solicitud = "unirse",
                            estado = "pendiente"
                        )
                        db.userDao().insertSolicitud(solicitud)
                        Toast.makeText(this@ProfileActivity, "Solicitud enviada a ${orgSeleccionada.nombre}", Toast.LENGTH_SHORT).show()
                        updateUIWithUserData()
                    }
                }
                .setNegativeButton("Cancelar", null)
                .show()
        }
    }

    private fun showRequestSupervisorDialog() {
        val user = currentUser ?: return
        lifecycleScope.launch {
            val userOrgs = db.userDao().getOrganizacionesByUsuario(user.id)
            if (userOrgs.isEmpty()) {
                Toast.makeText(this@ProfileActivity, "Debes pertenecer a una organización para solicitar ser supervisor", Toast.LENGTH_SHORT).show()
                return@launch
            }
            
            val orgId = userOrgs.first().id // Se solicita para la primera organización encontrada

            com.google.android.material.dialog.MaterialAlertDialogBuilder(this@ProfileActivity)
                .setTitle("Solicitar Cargo de Supervisor")
                .setMessage("¿Deseas enviar una solicitud para ser ascendido a Supervisor en ${userOrgs.first().nombre}?")
                .setPositiveButton("Enviar") { _, _ ->
                    lifecycleScope.launch {
                        val solicitud = SolicitudUsuarioEntity(
                            usuario_id = user.id,
                            organizacion_id = orgId,
                            tipo_solicitud = "ascenso_supervisor",
                            estado = "pendiente"
                        )
                        db.userDao().insertSolicitud(solicitud)
                        Toast.makeText(this@ProfileActivity, "Solicitud de ascenso enviada", Toast.LENGTH_SHORT).show()
                        binding.btnRequestSupervisor.isEnabled = false
                        binding.btnRequestSupervisor.text = "SOLICITUD ENVIADA"
                    }
                }
                .setNegativeButton("Cancelar", null)
                .show()
        }
    }

    private fun startImagePicker(isProfile: Boolean) {
        val options = CropImageOptions(
            imageSourceIncludeGallery = true,
            imageSourceIncludeCamera = true,
            guidelines = CropImageView.Guidelines.ON,
            cropShape = if (isProfile) CropImageView.CropShape.OVAL else CropImageView.CropShape.RECTANGLE,
            fixAspectRatio = true,
            aspectRatioX = if (isProfile) 1 else 16,
            aspectRatioY = if (isProfile) 1 else 9,
            activityBackgroundColor = android.graphics.Color.parseColor("#77e0b4"),
            toolbarColor = android.graphics.Color.parseColor("#15803D"),
            toolbarTintColor = android.graphics.Color.WHITE,
            activityMenuIconColor = android.graphics.Color.WHITE,
            progressBarColor = android.graphics.Color.WHITE,
            cropMenuCropButtonTitle = "GUARDAR",
            activityTitle = if (isProfile) "Recortar Perfil" else "Recortar Portada"
        )

        cropImage.launch(CropImageContractOptions(uri = null, cropImageOptions = options))
    }

    private fun saveImageLocally(uri: android.net.Uri, isProfile: Boolean) {
        lifecycleScope.launch(Dispatchers.IO) {
            try {
                val user = currentUser ?: return@launch
                val inputStream = contentResolver.openInputStream(uri)
                val bytes = inputStream?.readBytes() ?: return@launch
                
                val folderName = "perfil/${user.id}"
                val directory = File(filesDir, folderName)
                if (!directory.exists()) directory.mkdirs()

                val fileName = if (isProfile) "profile.jpg" else "cover.jpg"
                val file = File(directory, fileName)
                
                FileOutputStream(file).use { it.write(bytes) }

                val updatedUser = if (isProfile) user.copy(foto_perfil_url = file.absolutePath) else user.copy(foto_portada_url = file.absolutePath)
                
                db.userDao().updateUsuario(updatedUser)
                currentUser = updatedUser
                userWithRol = userWithRol?.copy(usuario = updatedUser)
                
                withContext(Dispatchers.Main) {
                    updateUIWithUserData()
                    Toast.makeText(this@ProfileActivity, "Imagen actualizada", Toast.LENGTH_SHORT).show()
                }
            } catch (e: Exception) {
                withContext(Dispatchers.Main) {
                    Toast.makeText(this@ProfileActivity, "Error al guardar imagen", Toast.LENGTH_SHORT).show()
                }
            }
        }
    }

    private fun loadUserSocialNetworks(userId: Int) {
        lifecycleScope.launch {
            val redes = db.userDao().getRedesSocialesByUsuario(userId)
            binding.llSocialContainer.removeAllViews()

            redes.forEach { red ->
                val view = LayoutInflater.from(this@ProfileActivity).inflate(R.layout.item_social_icon, binding.llSocialContainer, false)
                val card = view.findViewById<MaterialCardView>(R.id.cardSocialIcon)
                val icon = view.findViewById<ImageView>(R.id.ivSocialIcon)
                
                icon.setImageResource(availableSocialTypes.find { it.name == red.tipo_nombre }?.iconRes ?: R.drawable.ic_edit_01)
                card.setCardBackgroundColor(android.graphics.Color.parseColor(red.color_hex ?: "#F3F4F6"))

                view.setOnClickListener { openSocialUrl(red.redSocial.link) }
                view.setOnLongClickListener {
                    showSocialOptionsDialog(red)
                    true
                }
                binding.llSocialContainer.addView(view)
            }
            binding.llSocialContainer.addView(binding.btnAddSocial)
        }
    }

    private fun openSocialUrl(url: String) {
        var finalUrl = url.trim()
        if (!finalUrl.startsWith("http")) finalUrl = "https://$finalUrl"
        try {
            startActivity(android.content.Intent(android.content.Intent.ACTION_VIEW, android.net.Uri.parse(finalUrl)))
        } catch (e: Exception) {
            Toast.makeText(this, "No se pudo abrir el enlace", Toast.LENGTH_SHORT).show()
        }
    }

    private fun showSocialOptionsDialog(red: RedSocialWithMetadata) {
        MaterialAlertDialogBuilder(this)
            .setTitle(red.tipo_nombre)
            .setItems(arrayOf("Editar", "Eliminar")) { _, which ->
                if (which == 0) showAddSocialDialog(red)
                else deleteSocialNetwork(red.redSocial)
            }
            .show()
    }

    private fun deleteSocialNetwork(red: RedSocialEntity) {
        lifecycleScope.launch {
            db.userDao().deleteRedSocial(red)
            loadUserSocialNetworks(red.usuario_id)
        }
    }

    private fun showAddSocialDialog(redParaEditar: RedSocialWithMetadata?) {
        val dialogBinding = DialogAddSocialNetworkBinding.inflate(layoutInflater)
        var tipoSeleccionado: SocialTypeData? = null

        if (redParaEditar != null) {
            dialogBinding.tvDialogTitle.text = "Editar Red Social"
            dialogBinding.llSocialIconsSelector.visibility = View.GONE
            dialogBinding.etSocialUrl.setText(redParaEditar.redSocial.link)
            dialogBinding.etSocialDesc.setText(redParaEditar.redSocial.descripcion)
            tipoSeleccionado = availableSocialTypes.find { it.name == redParaEditar.tipo_nombre }
        } else {
            availableSocialTypes.forEach { type ->
                val iconView = LayoutInflater.from(this).inflate(R.layout.item_social_icon, dialogBinding.llSocialIconsSelector, false)
                iconView.findViewById<ImageView>(R.id.ivSocialIcon).setImageResource(type.iconRes)
                iconView.setOnClickListener {
                    tipoSeleccionado = type
                    dialogBinding.tvSelectedSocialName.text = "Seleccionado: ${type.name}"
                }
                dialogBinding.llSocialIconsSelector.addView(iconView)
            }
        }

        val dialog = MaterialAlertDialogBuilder(this).setView(dialogBinding.root).create()
        dialogBinding.btnSaveSocial.setOnClickListener {
            val url = dialogBinding.etSocialUrl.text.toString().trim()
            if (tipoSeleccionado == null || url.isEmpty()) return@setOnClickListener

            lifecycleScope.launch {
                val userId = currentUser?.id ?: 0
                val tiposDB = db.userDao().getAllTiposRedSocial()
                val tipoDB = tiposDB.find { it.nombre == tipoSeleccionado!!.name } ?: return@launch

                if (redParaEditar != null) {
                    db.userDao().updateRedSocial(redParaEditar.redSocial.copy(link = url, descripcion = dialogBinding.etSocialDesc.text.toString()))
                } else {
                    db.userDao().insertRedSocial(RedSocialEntity(usuario_id = userId, tipo_red_id = tipoDB.id, link = url, usuario_social = currentUser?.nombre ?: "", descripcion = dialogBinding.etSocialDesc.text.toString()))
                }
                loadUserSocialNetworks(userId)
                dialog.dismiss()
            }
        }
        dialog.show()
    }

    private fun showEditProfileModal() {
        val user = currentUser ?: return
        val dialogView = layoutInflater.inflate(R.layout.dialog_edit_profile, null)
        
        val etNombre = dialogView.findViewById<TextInputEditText>(R.id.etEditNombre)
        val etApellidos = dialogView.findViewById<TextInputEditText>(R.id.etEditApellidos)
        val etDni = dialogView.findViewById<TextInputEditText>(R.id.etEditDni)
        val etTelefono = dialogView.findViewById<TextInputEditText>(R.id.etEditTelefono)
        val etUbicacion = dialogView.findViewById<TextInputEditText>(R.id.etEditUbicacion)
        val etNivelEducativo = dialogView.findViewById<TextInputEditText>(R.id.etEditNivelEducativo)
        val etExperiencia = dialogView.findViewById<TextInputEditText>(R.id.etEditExperiencia)

        etNombre.setText(user.nombre)
        etApellidos.setText(user.apellidos)
        etDni.setText(user.dni)
        etTelefono.setText(user.telefono)
        etUbicacion.setText(user.ubicacion)
        etNivelEducativo.setText(user.nivel_educativo)
        etExperiencia.setText(user.experiencia_anios.toString())

        val dialog = MaterialAlertDialogBuilder(this).setView(dialogView).create()

        dialogView.findViewById<com.google.android.material.button.MaterialButton>(R.id.btnSaveProfile).setOnClickListener {
            val updatedUser = user.copy(
                nombre = etNombre.text.toString(),
                apellidos = etApellidos.text.toString(),
                dni = etDni.text.toString(),
                telefono = etTelefono.text.toString(),
                ubicacion = etUbicacion.text.toString(),
                nivel_educativo = etNivelEducativo.text.toString(),
                experiencia_anios = etExperiencia.text.toString().toIntOrNull() ?: 0,
                updated_at = System.currentTimeMillis() / 1000
            )
            lifecycleScope.launch {
                db.userDao().updateUsuario(updatedUser)
                currentUser = updatedUser
                updateUIWithUserData()
                dialog.dismiss()
            }
        }
        dialog.show()
    }

    private fun showChangePasswordDialog() {
        val dialogBinding = DialogChangePasswordBinding.inflate(layoutInflater)
        val dialog = MaterialAlertDialogBuilder(this).setView(dialogBinding.root).create()
        dialogBinding.btnConfirmChange.setOnClickListener {
            val current = dialogBinding.etCurrentPassword.text.toString()
            val new = dialogBinding.etNewPassword.text.toString()
            if (currentUser?.password == current) {
                lifecycleScope.launch {
                    db.userDao().updateUsuario(currentUser!!.copy(password = new))
                    dialog.dismiss()
                }
            } else Toast.makeText(this, "Contraseña incorrecta", Toast.LENGTH_SHORT).show()
        }
        dialog.show()
    }
}
