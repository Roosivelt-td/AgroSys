package com.sigcpa.agrosys.ui

import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.view.LayoutInflater
import android.view.ViewGroup
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.CultivoEntity
import com.sigcpa.agrosys.database.entities.TerrenoEntity
import com.sigcpa.agrosys.databinding.ActivityDetalleTerrenoBinding
import com.sigcpa.agrosys.databinding.ItemCultivoBinding
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class DetalleTerrenoActivity : AppCompatActivity() {

    private lateinit var binding: ActivityDetalleTerrenoBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    private var currentTerrenoId: Int = -1
    private var currentTerreno: TerrenoEntity? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityDetalleTerrenoBinding.inflate(layoutInflater)
        setContentView(binding.root)

        currentTerrenoId = intent.getIntExtra("TERRENO_ID", -1)
        if (currentTerrenoId == -1) {
            finish()
            return
        }

        setupUI()
        
        // Forzar barra de estado verde y iconos blancos
        window.statusBarColor = android.graphics.Color.parseColor("#15803D")
        androidx.core.view.WindowInsetsControllerCompat(window, window.decorView).apply {
            isAppearanceLightStatusBars = false
            window.navigationBarColor = android.graphics.Color.WHITE
            isAppearanceLightNavigationBars = true
        }

        // SOLUCIÓN: Ajustar el menú inferior para que no lo tapen los botones del sistema
        val initialBottomPadding = binding.bottomNav.paddingBottom
        androidx.core.view.ViewCompat.setOnApplyWindowInsetsListener(binding.bottomNav) { v, insets ->
            val systemBars = insets.getInsets(androidx.core.view.WindowInsetsCompat.Type.systemBars())
            v.setPadding(v.paddingLeft, v.paddingTop, v.paddingRight, initialBottomPadding + systemBars.bottom)
            insets
        }

        // También ajustamos el Header para que respete la barra de estado superior
        androidx.core.view.ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(androidx.core.view.WindowInsetsCompat.Type.systemBars())
            binding.headerContainer.setPadding(
                binding.headerContainer.paddingLeft,
                systemBars.top,
                binding.headerContainer.paddingRight,
                binding.headerContainer.paddingBottom
            )
            insets
        }

        loadData()
    }

    private fun setupUI() {
        binding.btnBack.setOnClickListener { finish() }
        
        binding.rvCultivosTerreno.layoutManager = LinearLayoutManager(this)
        
        binding.btnNuevoCultivo.setOnClickListener {
            AlertDialog.Builder(this)
                .setMessage(getString(R.string.dialog_confirm_nuevo_cultivo))
                .setPositiveButton(getString(R.string.btn_continuar)) { _, _ ->
                    val intent = Intent(this, RegisterCultivoActivity::class.java)
                    intent.putExtra("TERRENO_ID", currentTerrenoId)
                    startActivity(intent)
                }
                .setNegativeButton(getString(R.string.btn_cancelar), null)
                .show()
        }

        binding.btnEliminarTerrenoIcon.setOnClickListener { mostrarDialogoEliminar() }
        binding.btnEliminarTerrenoIconOverlay.setOnClickListener { mostrarDialogoEliminar() }
        
        binding.btnEditarTerreno.setOnClickListener { abrirEditarTerreno() }
        binding.btnEditarTerrenoOverlay.setOnClickListener { abrirEditarTerreno() }
        
        binding.btnRenovarAlquiler.setOnClickListener {
            currentTerreno?.let { mostrarDialogoRenovar(it) }
        }

        // Navigation
        binding.navHome.setOnClickListener {
            val intent = Intent(this, DashboardActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP
            startActivity(intent)
        }
        binding.navCultivos.setOnClickListener {
            startActivity(Intent(this, CultivosListActivity::class.java))
            finish()
        }
    }

    private fun abrirEditarTerreno() {
        currentTerreno?.let { terreno ->
            AlertDialog.Builder(this)
                .setMessage(getString(R.string.dialog_confirm_edit_terreno))
                .setPositiveButton(getString(R.string.btn_continuar)) { _, _ ->
                    val intent = Intent(this, RegisterTerrenoActivity::class.java)
                    intent.putExtra("TERRENO_ID", terreno.id)
                    
                    // Calcular área ocupada para validación en edición
                    lifecycleScope.launch {
                        val cultivos = db.assetDao().getCultivosByTerreno(terreno.id)
                        val areaOcupada = cultivos.filter { it.estado != "cosechado" && it.estado != "perdido" }.sumOf { it.area_destinada ?: 0.0 }
                        intent.putExtra("AREA_OCUPADA", areaOcupada)
                        startActivity(intent)
                    }
                }
                .setNegativeButton(getString(R.string.btn_cancelar), null)
                .show()
        }
    }

    private fun loadData() {
        lifecycleScope.launch {
            val terreno = db.assetDao().getTerrenoById(currentTerrenoId)
            if (terreno == null) {
                finish()
                return@launch
            }
            currentTerreno = terreno
            val cultivos = db.assetDao().getCultivosByTerreno(currentTerrenoId)

            updateGeneralUI(terreno, cultivos)
            binding.rvCultivosTerreno.adapter = CultivosAdapter(cultivos, terreno.nombre)
        }
    }

    private fun formatArea(area: Double): String {
        return if (area % 1.0 == 0.0) {
            "${area.toInt()} ha"
        } else {
            "$area ha"
        }
    }

    private fun updateGeneralUI(terreno: TerrenoEntity, cultivos: List<CultivoEntity>) {
        binding.tvHeaderTitle.text = terreno.nombre
        binding.tvTerrenoNombreCard.text = terreno.nombre
        binding.tvTerrenoNombreCardOverlay.text = terreno.nombre
        binding.tvUbicacion.text = terreno.direccion_referencia ?: getString(R.string.label_no_location)
        binding.tvUbicacionOverlay.text = terreno.direccion_referencia ?: getString(R.string.label_no_location)
        
        val totalAreaFormatted = formatArea(terreno.area_hectareas)
        binding.tvAreaOverlay.text = totalAreaFormatted

        terreno.foto_path?.let { path ->
            binding.cardTerrenoPhoto.visibility = android.view.View.VISIBLE
            binding.layoutInfoNombreOriginal.visibility = android.view.View.GONE
            val file = java.io.File(path)
            val uri = if (file.exists()) Uri.fromFile(file) else Uri.parse(path)
            
            Glide.with(this)
                .load(uri)
                .placeholder(R.drawable.uploap)
                .error(R.drawable.uploap)
                .centerCrop()
                .into(binding.ivTerrenoPhoto)
        } ?: run {
            binding.cardTerrenoPhoto.visibility = android.view.View.GONE
            binding.layoutInfoNombreOriginal.visibility = android.view.View.VISIBLE
        }

        val totalArea = terreno.area_hectareas
        val areaOcupada = cultivos.filter { it.estado != "cosechado" && it.estado != "perdido" }.sumOf { it.area_destinada ?: 0.0 }
        val areaDisponible = totalArea - areaOcupada

        binding.tvAreaTotalInfo.text = totalAreaFormatted
        binding.tvAreaDisponibleInfo.text = "${formatArea(areaDisponible)} libres"

        // Lógica de Barra de Progreso (Uso de Terreno)
        val ratioOcupado = if (totalArea > 0) (areaOcupada / totalArea) else 0.0
        binding.progressUsoTerreno.progress = (ratioOcupado * 100).toInt()
        
        // Lógica de Colores basada en porcentaje de uso:
        // Verde: 0% - 70%
        // Naranja: 70.01% - 99.99%
        // Rojo: 100%
        val colorUso = when {
            ratioOcupado >= 1.0 -> "#ef4444" // Rojo (100% o más)
            ratioOcupado > 0.7  -> "#f97316" // Naranja (> 70%)
            else                -> "#22c55e" // Verde (0% a 70%)
        }
        binding.progressUsoTerreno.setIndicatorColor(android.graphics.Color.parseColor(colorUso))
        
        // Actualizar Badge de Estado (Propio / Alquilado)
        if (terreno.tipo_tenencia == "alquilado") {
            val labelAlquilado = getString(R.string.label_alquilado_badge)
            binding.tvCostoAlquilerBadge.text = labelAlquilado
            binding.tvCostoAlquilerBadgeOverlay.text = labelAlquilado
            
            binding.tvCostoAlquilerBadge.backgroundTintList = android.content.res.ColorStateList.valueOf(android.graphics.Color.parseColor("#FEFCE8"))
            binding.tvCostoAlquilerBadge.setTextColor(android.graphics.Color.parseColor("#A16207"))
            
            binding.tvCostoAlquilerBadgeOverlay.backgroundTintList = android.content.res.ColorStateList.valueOf(android.graphics.Color.WHITE)
            binding.tvCostoAlquilerBadgeOverlay.setTextColor(android.graphics.Color.parseColor("#A16207"))
            
            binding.layoutCostoInfo.visibility = android.view.View.VISIBLE
            binding.tvCostoValor.text = "S/ ${terreno.costo_alquiler_anual}/año"

            if (terreno.fecha_alquiler != null && terreno.fecha_vencimiento_alquiler != null) {
                binding.layoutFechasAlquiler.visibility = android.view.View.VISIBLE
                val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
                binding.tvFechaAlquilerInfo.text = getString(R.string.label_alquilado_el, sdf.format(Date(terreno.fecha_alquiler * 1000)))
                binding.tvFechaVencimientoInfo.text = getString(R.string.label_vence_el, sdf.format(Date(terreno.fecha_vencimiento_alquiler * 1000)))

                // Lógica de barra de progreso de tiempo de alquiler
                val hoy = System.currentTimeMillis() / 1000
                val totalTiempo = terreno.fecha_vencimiento_alquiler - terreno.fecha_alquiler
                val transcurrido = hoy - terreno.fecha_alquiler
                
                if (totalTiempo > 0) {
                    val porcentajeTiempo = (transcurrido.toDouble() / totalTiempo.toDouble() * 100).toInt()
                    binding.progressTiempoAlquiler.progress = porcentajeTiempo.coerceIn(0, 100)
                    
                    // Color de la barra de tiempo
                    val colorTiempo = when {
                        porcentajeTiempo >= 90 -> "#ef4444" // Rojo (Casi vence)
                        porcentajeTiempo >= 70 -> "#f97316" // Naranja
                        else                   -> "#15803d" // Verde
                    }
                    binding.progressTiempoAlquiler.setIndicatorColor(android.graphics.Color.parseColor(colorTiempo))
                }
            } else {
                binding.layoutFechasAlquiler.visibility = android.view.View.GONE
            }
        } else {
            val labelPropio = getString(R.string.label_propio)
            binding.tvCostoAlquilerBadge.text = labelPropio
            binding.tvCostoAlquilerBadgeOverlay.text = labelPropio

            binding.tvCostoAlquilerBadge.backgroundTintList = android.content.res.ColorStateList.valueOf(android.graphics.Color.parseColor("#DCFCE7"))
            binding.tvCostoAlquilerBadge.setTextColor(android.graphics.Color.parseColor("#15803D"))

            binding.tvCostoAlquilerBadgeOverlay.backgroundTintList = android.content.res.ColorStateList.valueOf(android.graphics.Color.WHITE)
            binding.tvCostoAlquilerBadgeOverlay.setTextColor(android.graphics.Color.parseColor("#15803D"))
            
            binding.layoutCostoInfo.visibility = android.view.View.GONE
        }
        
        binding.tvCalidadSuelo.text = terreno.calidad_suelo ?: getString(R.string.label_no_location)
        binding.tvFuenteAgua.text = terreno.fuente_agua ?: getString(R.string.label_no_location)
        
        // Mostrar resumen de cultivos activos
        val activosCount = cultivos.count { it.estado == "activo" }
        binding.tvTerrenoResumenCard.text = getString(R.string.label_activos_count, activosCount)
        
        binding.tvCultivosTitulo.text = getString(R.string.label_cultivos_count, cultivos.size)
        
        // Si no hay área disponible, deshabilitar botón de nuevo cultivo
        if (areaDisponible <= 0.01) {
            binding.btnNuevoCultivo.alpha = 0.5f
            binding.btnNuevoCultivo.isEnabled = false
        } else {
            binding.btnNuevoCultivo.alpha = 1.0f
            binding.btnNuevoCultivo.isEnabled = true
        }
    }

    private fun mostrarDialogoEliminar() {
        AlertDialog.Builder(this)
            .setTitle(getString(R.string.dialog_eliminar_terreno_title))
            .setMessage(getString(R.string.dialog_eliminar_terreno_msg))
            .setPositiveButton(getString(R.string.btn_eliminar)) { _, _ ->
                eliminarTerreno()
            }
            .setNegativeButton(getString(R.string.btn_cancelar), null)
            .show()
    }

    private fun mostrarDialogoRenovar(terreno: TerrenoEntity) {
        val dialogBinding = com.sigcpa.agrosys.databinding.DialogRenovarAlquilerBinding.inflate(layoutInflater)
        val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
        
        var tempFechaAlquiler = terreno.fecha_alquiler ?: (System.currentTimeMillis() / 1000)
        var tempFechaVencimiento = terreno.fecha_vencimiento_alquiler ?: ((System.currentTimeMillis() / 1000) + (180L * 24 * 60 * 60))

        dialogBinding.etFechaAlquiler.setText(sdf.format(Date(tempFechaAlquiler * 1000)))
        dialogBinding.etFechaVencimiento.setText(sdf.format(Date(tempFechaVencimiento * 1000)))

        dialogBinding.etFechaAlquiler.setOnClickListener {
            showDatePicker(tempFechaAlquiler) { selected ->
                tempFechaAlquiler = selected
                dialogBinding.etFechaAlquiler.setText(sdf.format(Date(selected * 1000)))
                
                // Sugerir 6 meses
                val cal = Calendar.getInstance()
                cal.timeInMillis = selected * 1000
                cal.add(Calendar.MONTH, 6)
                tempFechaVencimiento = cal.timeInMillis / 1000
                dialogBinding.etFechaVencimiento.setText(sdf.format(Date(tempFechaVencimiento * 1000)))
            }
        }

        dialogBinding.etFechaVencimiento.setOnClickListener {
            showDatePicker(tempFechaVencimiento, minDate = (tempFechaAlquiler * 1000) + 86400000) { selected ->
                tempFechaVencimiento = selected
                dialogBinding.etFechaVencimiento.setText(sdf.format(Date(selected * 1000)))
            }
        }

        AlertDialog.Builder(this)
            .setTitle(getString(R.string.title_renovar_alquiler))
            .setView(dialogBinding.root)
            .setPositiveButton(getString(R.string.btn_actualizar)) { _, _ ->
                actualizarAlquiler(terreno, tempFechaAlquiler, tempFechaVencimiento)
            }
            .setNegativeButton(getString(R.string.btn_cancelar), null)
            .show()
    }

    private fun showDatePicker(currentDate: Long, minDate: Long? = null, onDateSelected: (Long) -> Unit) {
        val calendar = Calendar.getInstance()
        calendar.timeInMillis = currentDate * 1000
        val dialog = android.app.DatePickerDialog(
            this,
            { _, year, month, dayOfMonth ->
                val selected = Calendar.getInstance()
                selected.set(year, month, dayOfMonth)
                onDateSelected(selected.timeInMillis / 1000)
            },
            calendar.get(Calendar.YEAR),
            calendar.get(Calendar.MONTH),
            calendar.get(Calendar.DAY_OF_MONTH)
        )
        if (minDate != null) dialog.datePicker.minDate = minDate
        dialog.show()
    }

    private fun actualizarAlquiler(terreno: TerrenoEntity, inicio: Long, fin: Long) {
        lifecycleScope.launch {
            val updated = terreno.copy(fecha_alquiler = inicio, fecha_vencimiento_alquiler = fin)
            db.assetDao().updateTerreno(updated)
            Toast.makeText(this@DetalleTerrenoActivity, getString(R.string.msg_alquiler_actualizado), Toast.LENGTH_SHORT).show()
            loadData()
        }
    }

    private fun eliminarTerreno() {
        val terreno = currentTerreno ?: return
        lifecycleScope.launch {
            db.assetDao().deleteTerreno(terreno)
            Toast.makeText(this@DetalleTerrenoActivity, getString(R.string.msg_terreno_eliminado), Toast.LENGTH_SHORT).show()
            finish()
        }
    }

    override fun onResume() {
        super.onResume()
        loadData()
    }

    inner class CultivosAdapter(private val lista: List<CultivoEntity>, private val terrenoNombre: String) : RecyclerView.Adapter<CultivosAdapter.ViewHolder>() {
        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
            val b = ItemCultivoBinding.inflate(LayoutInflater.from(parent.context), parent, false)
            return ViewHolder(b)
        }
        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val item = lista[position]
            holder.binding.tvNombreLote.text = item.nombre_lote ?: getString(R.string.label_cargando)
            holder.binding.tvTerreno.text = terrenoNombre
            holder.binding.tvArea.text = "${item.area_destinada ?: 0.0} ${getString(R.string.unit_ha)}"
            
            val sdf = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            
            val fechaSiembraStr = sdf.format(Date(item.fecha_siembra * 1000))
            holder.binding.tvFecha.text = "S: $fechaSiembraStr"

            if (item.fecha_planificada != null) {
                val fechaPlanificadaStr = sdf.format(Date(item.fecha_planificada * 1000))
                holder.binding.tvFechaPlanificada.text = "P: $fechaPlanificadaStr"
                holder.binding.tvFechaPlanificada.visibility = android.view.View.VISIBLE
            } else {
                holder.binding.tvFechaPlanificada.visibility = android.view.View.GONE
            }
            
            holder.binding.tvEstado.text = when(item.estado) {
                "activo" -> getString(R.string.label_activo)
                "planificado" -> getString(R.string.label_planificado)
                "cosechado" -> getString(R.string.filter_cosechado)
                "perdido" -> getString(R.string.filter_perdido)
                else -> item.estado
            }

            holder.binding.root.setOnClickListener {
                val intent = Intent(this@DetalleTerrenoActivity, DetalleCultivoActivity::class.java)
                intent.putExtra("CULTIVO_ID", item.id)
                startActivity(intent)
            }
        }
        override fun getItemCount() = lista.size
        inner class ViewHolder(val binding: ItemCultivoBinding) : RecyclerView.ViewHolder(binding.root)
    }
}
