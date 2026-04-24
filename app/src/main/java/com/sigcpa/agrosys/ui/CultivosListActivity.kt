package com.sigcpa.agrosys.ui

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
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
                startActivity(Intent(this, RegisterCultivoActivity::class.java))
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
                // Ir a reportes (en desarrollo)
                Toast.makeText(this, getString(R.string.msg_reportes_dev), Toast.LENGTH_SHORT).show()
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
        
        selectedView.backgroundTintList = getColorStateList(com.sigcpa.agrosys.R.color.black)
        selectedView.setTextColor(getColor(android.R.color.white))
    }

    private fun loadCultivos() {
        val sharedPref = getSharedPreferences("agrosys_prefs", MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)

        lifecycleScope.launch {
            val user = db.userDao().getUsuarioById(userId)
            if (user != null) {
                val terrenos = db.assetDao().getTerrenosByAgricultor(user.id)
                var cultivos = db.assetDao().getCultivosByAgricultor(user.id)
                
                hasTerrenos = terrenos.isNotEmpty()
                hasCultivos = cultivos.isNotEmpty()
                
                applyLocks()

                if (currentFilter != "todos") {
                    cultivos = cultivos.filter { it.estado == currentFilter }
                }

                val cultivosConTerreno = cultivos.map { cultivo ->
                    val terreno = terrenos.find { it.id == cultivo.terreno_id }
                    cultivo to (terreno?.nombre ?: "N/A")
                }
                
                updateUI(cultivosConTerreno)
            } else {
                updateUI(emptyList())
            }
        }
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

    private fun updateUI(data: List<Pair<com.sigcpa.agrosys.database.entities.CultivoEntity, String>>) {
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
