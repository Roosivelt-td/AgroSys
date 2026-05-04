package com.sigcpa.agrosys.ui

import android.content.Intent
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import android.widget.Toast
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.bottomsheet.BottomSheetDialog
import com.sigcpa.agrosys.database.entities.TerrenoEntity
import com.sigcpa.agrosys.databinding.DialogSelectTerrenoBinding
import com.sigcpa.agrosys.databinding.ItemTerrenoSelectorBinding
import com.bumptech.glide.Glide
import android.net.Uri
import java.io.File
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.databinding.ActivityCultivosListBinding
import kotlinx.coroutines.launch

class CultivosListActivity : AppCompatActivity() {

    private lateinit var binding: ActivityCultivosListBinding
    private lateinit var adapter: CultivosAdapter
    private val db by lazy { AppDatabase.getDatabase(this) }
    private var currentFilter: String = "todos"
    
    private var hasTerrenos = false
    private var hasCultivos = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityCultivosListBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Forzar barra de estado verde y iconos blancos
        window.statusBarColor = android.graphics.Color.parseColor("#15803D")
        WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        // SOLUCIÓN: Ajustar el menú inferior para que no lo tapen los botones del sistema
        val initialBottomPadding = binding.bottomNav.paddingBottom
        ViewCompat.setOnApplyWindowInsetsListener(binding.bottomNav) { v, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            v.setPadding(v.paddingLeft, v.paddingTop, v.paddingRight, initialBottomPadding + systemBars.bottom)
            insets
        }

        // Ajustar el Header para que respete la barra de estado superior
        ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            binding.headerContainer.setPadding(
                binding.headerContainer.paddingLeft,
                systemBars.top,
                binding.headerContainer.paddingRight,
                binding.headerContainer.paddingBottom
            )
            insets
        }

        setupRecyclerView()
        setupListeners()
        setupFilters()
    }

    private fun setupRecyclerView() {
        adapter = CultivosAdapter(emptyList()) { cultivo ->
            val intent = Intent(this, DetalleCultivoActivity::class.java)
            intent.putExtra("CULTIVO_ID", cultivo.id)
            startActivity(intent)
        }
        binding.rvCultivos.layoutManager = LinearLayoutManager(this)
        binding.rvCultivos.adapter = adapter
    }

    private fun setupListeners() {
        // Botón Nuevo Cultivo con validación
        val goToRegister = {
            if (hasTerrenos) {
                mostrarSelectorTerreno()
            } else {
                Toast.makeText(this, getString(R.string.error_no_terrenos), Toast.LENGTH_LONG).show()
            }
        }

        binding.btnBack.setOnClickListener { finish() }
        binding.btnNuevoCultivo.setOnClickListener { goToRegister() }
        binding.btnEmptyAdd.setOnClickListener { goToRegister() }
        binding.fabAdd.setOnClickListener { goToRegister() }
        
        binding.navHome.setOnClickListener {
            val intent = Intent(this, DashboardActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP
            startActivity(intent)
            finish()
        }
        
        binding.navTerrenos.setOnClickListener {
            startActivity(Intent(this, TerrenosListActivity::class.java))
            finish()
        }

        binding.navLabores.setOnClickListener {
            if (hasCultivos) {
                startActivity(Intent(this, LaboresListActivity::class.java))
                finish()
            } else {
                Toast.makeText(this, getString(R.string.error_require_siembra), Toast.LENGTH_SHORT).show()
            }
        }

        binding.navReportes.setOnClickListener {
            if (hasCultivos) {
                startActivity(Intent(this, ReportesActivity::class.java))
                finish()
            } else {
                Toast.makeText(this, getString(R.string.error_require_siembra), Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun setupFilters() {
        val filterViews = mapOf(
            "todos" to binding.filterTodos,
            "activo" to binding.filterActivo,
            "planificado" to binding.filterPlanificado,
            "cosechado" to binding.filterCosechado,
            "perdido" to binding.filterPerdido
        )

        filterViews.forEach { (filter, view) ->
            view.setOnClickListener {
                updateFilterUI(filter, filterViews.values)
                currentFilter = filter
                loadCultivos()
            }
        }
    }

    private fun updateFilterUI(selectedFilter: String, allViews: Collection<TextView>) {
        allViews.forEach { view ->
            view.backgroundTintList = getColorStateList(android.R.color.transparent)
            view.setBackgroundResource(com.sigcpa.agrosys.R.drawable.rounded_background)
            view.backgroundTintList = getColorStateList(android.R.color.darker_gray).withAlpha(30)
            view.setTextColor(getColor(android.R.color.tab_indicator_text))
        }
        
        val selectedView = when(selectedFilter) {
            "todos" -> binding.filterTodos
            "activo" -> binding.filterActivo
            "planificado" -> binding.filterPlanificado
            "cosechado" -> binding.filterCosechado
            "perdido" -> binding.filterPerdido
            else -> binding.filterTodos
        }
        
        selectedView.backgroundTintList = getColorStateList(android.R.color.transparent)
        selectedView.setBackgroundResource(com.sigcpa.agrosys.R.drawable.rounded_background)
        selectedView.backgroundTintList = getColorStateList(com.sigcpa.agrosys.R.color.black)
        selectedView.setTextColor(getColor(android.R.color.white))
    }

    private fun loadCultivos() {
        val sharedPref = getSharedPreferences("agrosys_prefs", MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)

        lifecycleScope.launch {
            val user = db.userDao().getUsuarioById(userId)
            if (user != null) {
                val agricultorId = user.usuario.id
                val terrenos = db.assetDao().getTerrenosByAgricultor(agricultorId)
                var cultivos = db.assetDao().getCultivosByAgricultor(agricultorId)
                
                hasTerrenos = terrenos.isNotEmpty()
                hasCultivos = cultivos.isNotEmpty()
                
                applyLocks()

                if (currentFilter != "todos") {
                    cultivos = cultivos.filter { it.estado == currentFilter }
                }

                val cultivosConTerreno = cultivos.map { cultivo ->
                    val terreno = terrenos.find { it.id == cultivo.terreno_id }
                    Triple(cultivo, terreno?.nombre ?: "N/A", terreno?.tipo_tenencia ?: "propio")
                }
                
                updateUI(cultivosConTerreno)
            } else {
                updateUI(emptyList())
            }
        }
    }

    private fun mostrarSelectorTerreno() {
        val dialog = BottomSheetDialog(this)
        val dialogBinding = DialogSelectTerrenoBinding.inflate(layoutInflater)
        dialog.setContentView(dialogBinding.root)

        var selectedTerrenoId: Int = -1

        lifecycleScope.launch {
            val sharedPref = getSharedPreferences("agrosys_prefs", MODE_PRIVATE)
            val userId = sharedPref.getInt("USER_ID", -1)
            val user = db.userDao().getUsuarioById(userId)
            if (user != null) {
                val terrenos = db.assetDao().getTerrenosByAgricultor(user.usuario.id)
                val terrenosConArea = terrenos.map { terreno ->
                    val areaOcupada = db.assetDao().getAreaOcupadaByTerreno(terreno.id) ?: 0.0
                    Pair(terreno, areaOcupada)
                }
                
                dialogBinding.rvTerrenosSelector.layoutManager = LinearLayoutManager(this@CultivosListActivity)
                dialogBinding.rvTerrenosSelector.adapter = TerrenosSelectorAdapter(terrenosConArea) { id ->
                    selectedTerrenoId = id
                    dialogBinding.btnConfirmarSeleccion.isEnabled = true
                }
            }
        }

        dialogBinding.btnCloseDialog.setOnClickListener { dialog.dismiss() }
        
        dialogBinding.btnConfirmarSeleccion.setOnClickListener {
            dialog.dismiss()
            val intent = Intent(this, RegisterCultivoActivity::class.java)
            intent.putExtra("TERRENO_ID", selectedTerrenoId)
            startActivity(intent)
        }

        dialog.show()
    }

    inner class TerrenosSelectorAdapter(
        private val lista: List<Pair<TerrenoEntity, Double>>,
        private val onSelected: (Int) -> Unit
    ) : RecyclerView.Adapter<TerrenosSelectorAdapter.ViewHolder>() {
        
        private var selectedPos = -1

        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
            val b = ItemTerrenoSelectorBinding.inflate(LayoutInflater.from(parent.context), parent, false)
            return ViewHolder(b)
        }

        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val (item, areaOcupada) = lista[position]
            val areaTotal = item.area_hectareas
            val areaDisponible = maxOf(0.0, areaTotal - areaOcupada)
            val estaLleno = areaDisponible <= 0.0
            val porcentajeOcupado = if (areaTotal > 0) (areaOcupada / areaTotal).toFloat() else 0f

            holder.binding.tvNombreTerreno.text = item.nombre
            holder.binding.tvUbicacionTerreno.text = item.direccion_referencia ?: "Sin ubicación"
            
            val df = java.text.DecimalFormat("#.##")
            val areaTexto = "${df.format(areaDisponible)} ha libres de ${df.format(areaTotal)} ha"
            holder.binding.tvAreaTerreno.text = areaTexto

            holder.binding.tvStatusOverlay.visibility = View.VISIBLE
            if (estaLleno) {
                holder.binding.tvStatusOverlay.text = holder.itemView.context.getString(R.string.label_terreno_lleno)
                holder.binding.tvStatusOverlay.background = androidx.appcompat.content.res.AppCompatResources.getDrawable(holder.itemView.context, R.drawable.bg_status_badge)
                (holder.binding.tvStatusOverlay.background as android.graphics.drawable.GradientDrawable).setColor(android.graphics.Color.parseColor("#FEE2E2"))
                holder.binding.tvStatusOverlay.setTextColor(android.graphics.Color.parseColor("#DC2626"))
                
                holder.binding.tvAreaTerreno.setTextColor(android.graphics.Color.parseColor("#DC2626"))
                holder.binding.cardTerrenoSelector.setStrokeColor(android.graphics.Color.parseColor("#FEE2E2"))
                holder.binding.cardTerrenoSelector.setCardBackgroundColor(android.graphics.Color.parseColor("#FFF1F2"))
                holder.itemView.alpha = 1.0f
            } else {
                holder.binding.tvStatusOverlay.text = holder.itemView.context.getString(R.string.label_terreno_disponible)
                holder.binding.tvStatusOverlay.background = androidx.appcompat.content.res.AppCompatResources.getDrawable(holder.itemView.context, R.drawable.bg_status_badge)
                (holder.binding.tvStatusOverlay.background as android.graphics.drawable.GradientDrawable).setColor(android.graphics.Color.parseColor("#DCFCE7"))
                holder.binding.tvStatusOverlay.setTextColor(android.graphics.Color.parseColor("#15803D"))
                
                holder.binding.tvAreaTerreno.setTextColor(android.graphics.Color.parseColor("#15803D"))
                holder.binding.cardTerrenoSelector.setStrokeColor(android.graphics.Color.parseColor("#F1F5F9"))
                holder.binding.cardTerrenoSelector.setCardBackgroundColor(android.graphics.Color.WHITE)
                holder.itemView.alpha = 1.0f
            }

            // Cargar imagen del terreno
            if (!item.foto_path.isNullOrEmpty()) {
                val file = File(item.foto_path)
                val uri = if (file.exists()) Uri.fromFile(file) else Uri.parse(item.foto_path)
                
                holder.binding.ivTerrenoPhotoSelector.imageTintList = null
                Glide.with(holder.itemView.context)
                    .load(uri)
                    .centerCrop()
                    .placeholder(R.drawable.ic_ubicacion_terreno)
                    .into(holder.binding.ivTerrenoPhotoSelector)
            } else {
                holder.binding.ivTerrenoPhotoSelector.setImageResource(R.drawable.ic_ubicacion_terreno)
                holder.binding.ivTerrenoPhotoSelector.imageTintList = android.content.res.ColorStateList.valueOf(
                    android.graphics.Color.parseColor("#15803d")
                )
            }

            val isSelected = selectedPos == position
            holder.binding.rbSelected.isChecked = isSelected
            holder.binding.rbSelected.isEnabled = !estaLleno
            
            // Estilo visual llamativo para la selección
            if (isSelected) {
                holder.binding.cardTerrenoSelector.strokeColor = android.graphics.Color.parseColor("#15803d")
                holder.binding.cardTerrenoSelector.strokeWidth = 4
                holder.binding.cardTerrenoSelector.setCardBackgroundColor(android.graphics.Color.parseColor("#F0FDF4"))
            }

            holder.itemView.setOnClickListener {
                if (!estaLleno) {
                    val oldPos = selectedPos
                    selectedPos = holder.adapterPosition
                    notifyItemChanged(oldPos)
                    notifyItemChanged(selectedPos)
                    onSelected(item.id)
                }
            }
        }

        override fun getItemCount() = lista.size

        inner class ViewHolder(val binding: ItemTerrenoSelectorBinding) : RecyclerView.ViewHolder(binding.root)
    }

    private fun applyLocks() {
        // Bloqueo de botones de creación
        binding.btnNuevoCultivo.alpha = if (hasTerrenos) 1.0f else 0.4f
        binding.btnEmptyAdd.alpha = if (hasTerrenos) 1.0f else 0.4f
        binding.fabAdd.alpha = if (hasTerrenos) 1.0f else 0.4f
        binding.fabAdd.isEnabled = hasTerrenos

        // Bloqueo de barra inferior
        binding.navLabores.alpha = if (hasCultivos) 1.0f else 0.4f
        binding.navReportes.alpha = if (hasCultivos) 1.0f else 0.4f
    }

    private fun updateUI(data: List<Triple<com.sigcpa.agrosys.database.entities.CultivoEntity, String, String>>) {
        if (data.isEmpty()) {
            binding.emptyState.visibility = View.VISIBLE
            binding.rvCultivos.visibility = View.GONE
        } else {
            binding.emptyState.visibility = View.GONE
            binding.rvCultivos.visibility = View.VISIBLE
            adapter.updateData(data)
        }
    }

    override fun onResume() {
        super.onResume()
        loadCultivos()
    }
}
