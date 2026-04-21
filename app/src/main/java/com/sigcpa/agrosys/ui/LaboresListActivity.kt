package com.sigcpa.agrosys.ui

import android.content.Intent
import android.content.res.ColorStateList
import android.graphics.Color
import android.os.Bundle
import android.transition.AutoTransition
import android.transition.TransitionManager
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.LaborRealizadaEntity
import com.sigcpa.agrosys.database.entities.CultivoEntity
import com.sigcpa.agrosys.databinding.ActivityLaboresListBinding
import com.sigcpa.agrosys.databinding.ItemLaborGroupBinding
import com.sigcpa.agrosys.databinding.ItemLaborMiniBinding
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class LaboresListActivity : AppCompatActivity() {

    private lateinit var binding: ActivityLaboresListBinding
    private val db by lazy { AppDatabase.getDatabase(this) }
    
    private var hasTerrenos = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityLaboresListBinding.inflate(layoutInflater)
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
        loadTerrenosActivos()
    }

    private fun setupRecyclerView() {
        binding.rvLabores.layoutManager = LinearLayoutManager(this)
    }

    private fun setupListeners() {
        val goToRegister = {
            if (hasTerrenos) {
                // Ahora la lógica es seleccionar desde el terreno
                Toast.makeText(this, getString(R.string.msg_select_terreno_labor), Toast.LENGTH_SHORT).show()
            } else {
                Toast.makeText(this, getString(R.string.error_no_terrenos), Toast.LENGTH_SHORT).show()
            }
        }

        binding.btnBack.setOnClickListener { finish() }
        binding.btnNuevaLabor.setOnClickListener { goToRegister() }
        binding.btnEmptyAdd.setOnClickListener { goToRegister() }

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
        
        binding.navCultivos.setOnClickListener {
            startActivity(Intent(this, CultivosListActivity::class.java))
            finish()
        }

        binding.navReportes.setOnClickListener {
            Toast.makeText(this, getString(R.string.msg_reportes_dev), Toast.LENGTH_SHORT).show()
        }
    }

    private fun loadTerrenosActivos() {
        val sharedPref = getSharedPreferences("agrosys_prefs", MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)

        lifecycleScope.launch {
            val agricultor = db.userDao().getAgricultorByUserId(userId)
            if (agricultor != null) {
                val terrenos = db.assetDao().getTerrenosActivosYPlanificados(agricultor.id)
                hasTerrenos = terrenos.isNotEmpty()
                
                // Aplicar bloqueos visuales
                binding.btnNuevaLabor.alpha = if (hasTerrenos) 1.0f else 0.4f
                binding.navReportes.alpha = if (hasTerrenos) 1.0f else 0.4f

                updateUI(terrenos)
            } else {
                updateUI(emptyList())
            }
        }
    }

    private fun updateUI(terrenos: List<com.sigcpa.agrosys.database.entities.TerrenoEntity>) {
        if (terrenos.isEmpty()) {
            binding.emptyState.visibility = View.VISIBLE
            binding.rvLabores.visibility = View.GONE
        } else {
            binding.emptyState.visibility = View.GONE
            binding.rvLabores.visibility = View.VISIBLE
            binding.rvLabores.adapter = TerrenosLaboresAdapter(terrenos)
        }
    }

    override fun onResume() {
        super.onResume()
        loadTerrenosActivos()
    }

    inner class TerrenosLaboresAdapter(private val terrenos: List<com.sigcpa.agrosys.database.entities.TerrenoEntity>) 
        : RecyclerView.Adapter<TerrenosLaboresAdapter.ViewHolder>() {

        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int) = 
            ViewHolder(com.sigcpa.agrosys.databinding.ItemTerrenoLaboresBinding.inflate(LayoutInflater.from(parent.context), parent, false))

        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val terreno = terrenos[position]
            
            holder.binding.tvNombreTerreno.text = terreno.nombre
            holder.binding.tvUbicacion.text = terreno.direccion_referencia ?: getString(R.string.label_sin_ubicacion)
            holder.binding.tvAreaTotal.text = "${terreno.area_hectareas} ${getString(R.string.unit_ha)}"
            
            holder.binding.chipEstado.text = terreno.estado.uppercase()
            holder.binding.chipEstado.chipBackgroundColor = ColorStateList.valueOf(
                if (terreno.estado == "activo") Color.parseColor("#166534") else Color.parseColor("#1d4ed8")
            )

            lifecycleScope.launch {
                val cultivos = db.assetDao().getCultivosActivosByTerreno(terreno.id)
                val areaOcupada = cultivos.sumOf { it.area_destinada ?: 0.0 }
                val areaDisponible = terreno.area_hectareas - areaOcupada
                
                holder.binding.tvCantCultivos.text = cultivos.size.toString()
                holder.binding.tvAreaDisponible.text = "${String.format("%.2f", areaDisponible)} ${getString(R.string.unit_ha)}"
                
                holder.itemView.setOnClickListener {
                    showCultivosModal(terreno, cultivos, areaDisponible)
                }
            }
        }

        private fun showCultivosModal(terreno: com.sigcpa.agrosys.database.entities.TerrenoEntity, cultivos: List<CultivoEntity>, disponible: Double) {
            val dialog = com.google.android.material.bottomsheet.BottomSheetDialog(this@LaboresListActivity)
            val modalBinding = com.sigcpa.agrosys.databinding.DialogTerrenoCultivosBinding.inflate(layoutInflater)
            dialog.setContentView(modalBinding.root)

            modalBinding.tvTerrenoNombre.text = terreno.nombre
            modalBinding.tvTerrenoDetalles.text = "${terreno.area_hectareas} ${getString(R.string.unit_ha)} ${getString(R.string.label_totales)} | ${String.format("%.2f", disponible)} ${getString(R.string.unit_ha)} ${getString(R.string.label_disponibles)}"

            if (cultivos.isEmpty()) {
                modalBinding.tvNoCultivos.text = getString(R.string.msg_no_cultivos_terreno)
                modalBinding.tvNoCultivos.visibility = View.VISIBLE
                modalBinding.rvCultivos.visibility = View.GONE
            } else {
                modalBinding.tvNoCultivos.visibility = View.GONE
                modalBinding.rvCultivos.visibility = View.VISIBLE
                modalBinding.rvCultivos.layoutManager = LinearLayoutManager(this@LaboresListActivity)
                modalBinding.rvCultivos.adapter = CultivosModalAdapter(cultivos) { cultivo ->
                    dialog.dismiss()
                    val intent = Intent(this@LaboresListActivity, RegisterLaborActivity::class.java)
                    intent.putExtra("CULTIVO_ID", cultivo.id)
                    intent.putExtra("TERRENO_NOMBRE", terreno.nombre)
                    intent.putExtra("CULTIVO_DETALLE", "${cultivo.nombre_lote} (${cultivo.variedad})")
                    startActivity(intent)
                }
            }

            modalBinding.btnCerrar.text = getString(R.string.btn_cerrar)
            modalBinding.btnCerrar.setOnClickListener { dialog.dismiss() }
            dialog.show()
        }

        override fun getItemCount() = terrenos.size
        inner class ViewHolder(val binding: com.sigcpa.agrosys.databinding.ItemTerrenoLaboresBinding) : RecyclerView.ViewHolder(binding.root)
    }

    inner class CultivosModalAdapter(private val cultivos: List<CultivoEntity>, private val onClick: (CultivoEntity) -> Unit) 
        : RecyclerView.Adapter<CultivosModalAdapter.ViewHolder>() {

        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int) = 
            ViewHolder(com.sigcpa.agrosys.databinding.ItemCultivoModalBinding.inflate(LayoutInflater.from(parent.context), parent, false))

        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val cultivo = cultivos[position]
            holder.binding.tvNombreCultivo.text = cultivo.nombre_lote ?: "${getString(R.string.label_cultivo)} #${cultivo.id}"
            holder.binding.tvDetalleCultivo.text = "${cultivo.variedad} | ${cultivo.area_destinada} ${getString(R.string.unit_ha)}"
            
            holder.itemView.setOnClickListener { onClick(cultivo) }
        }

        override fun getItemCount() = cultivos.size
        inner class ViewHolder(val binding: com.sigcpa.agrosys.databinding.ItemCultivoModalBinding) : RecyclerView.ViewHolder(binding.root)
    }
}
