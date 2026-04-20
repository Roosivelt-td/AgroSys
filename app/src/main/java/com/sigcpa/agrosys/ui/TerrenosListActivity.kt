package com.sigcpa.agrosys.ui

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.LinearLayout
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import com.sigcpa.agrosys.R
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.databinding.ActivityTerrenosListBinding
import kotlinx.coroutines.launch

class TerrenosListActivity : AppCompatActivity() {

    private lateinit var binding: ActivityTerrenosListBinding
    private lateinit var adapter: TerrenosAdapter
    private val db by lazy { AppDatabase.getDatabase(this) }
    
    private var hasTerrenos = false
    private var hasCultivos = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityTerrenosListBinding.inflate(layoutInflater)
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
        val initialHeaderTopPadding = binding.header.paddingTop
        ViewCompat.setOnApplyWindowInsetsListener(binding.mainLayout) { _, insets ->
            val systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars())
            binding.header.setPadding(binding.header.paddingLeft, initialHeaderTopPadding + systemBars.top, binding.header.paddingRight, binding.header.paddingBottom)
            insets
        }

        setupRecyclerView()
        setupListeners()
    }

    private fun setupRecyclerView() {
        adapter = TerrenosAdapter(emptyList()) { terreno ->
            val intent = Intent(this, DetalleTerrenoActivity::class.java)
            intent.putExtra("TERRENO_ID", terreno.id)
            startActivity(intent)
        }
        binding.rvTerrenos.layoutManager = LinearLayoutManager(this)
        binding.rvTerrenos.adapter = adapter
    }

    private fun setupListeners() {
        val goToRegister = {
            val intent = Intent(this, RegisterTerrenoActivity::class.java)
            startActivity(intent)
        }

        binding.btnAddHeader.setOnClickListener { goToRegister() }
        binding.btnEmptyAdd.setOnClickListener { goToRegister() }
        binding.fabAdd.setOnClickListener { goToRegister() }
        
        // Navigation
        binding.navHome.setOnClickListener {
            val intent = Intent(this, DashboardActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP
            startActivity(intent)
            finish()
        }

        binding.navCultivos.setOnClickListener {
            if (hasTerrenos) {
                val intent = Intent(this, CultivosListActivity::class.java)
                startActivity(intent)
                finish()
            } else {
                Toast.makeText(this, "⚠️ Registra un terreno primero", Toast.LENGTH_SHORT).show()
            }
        }

        binding.navLabores.setOnClickListener {
            if (hasCultivos) {
                val intent = Intent(this, LaboresListActivity::class.java)
                startActivity(intent)
                finish()
            } else {
                Toast.makeText(this, "⚠️ Registra un cultivo primero", Toast.LENGTH_SHORT).show()
            }
        }
        
        binding.navReportes.setOnClickListener {
            Toast.makeText(this, "Módulo de Reportes en desarrollo", Toast.LENGTH_SHORT).show()
        }
    }

    private fun loadTerrenos() {
        val sharedPref = getSharedPreferences("agrosys_prefs", MODE_PRIVATE)
        val userId = sharedPref.getInt("USER_ID", -1)

        lifecycleScope.launch {
            val agricultor = db.userDao().getAgricultorByUserId(userId)
            if (agricultor != null) {
                val terrenos = db.assetDao().getTerrenosByAgricultor(agricultor.id)
                val cultivos = db.assetDao().getCultivosByAgricultor(agricultor.id)
                
                hasTerrenos = terrenos.isNotEmpty()
                hasCultivos = cultivos.isNotEmpty()
                
                applyLocks()
                updateUI(terrenos)
            } else {
                updateUI(emptyList())
            }
        }
    }

    private fun applyLocks() {
        // Bloqueo visual de la barra inferior
        binding.navCultivos.alpha = if (hasTerrenos) 1.0f else 0.4f
        binding.navLabores.alpha = if (hasCultivos) 1.0f else 0.4f
        binding.navReportes.alpha = if (hasCultivos) 1.0f else 0.4f
    }

    private fun updateUI(terrenos: List<com.sigcpa.agrosys.database.entities.TerrenoEntity>) {
        if (terrenos.isEmpty()) {
            binding.emptyState.visibility = View.VISIBLE
            binding.rvTerrenos.visibility = View.GONE
            binding.fabAdd.visibility = View.GONE
        } else {
            binding.emptyState.visibility = View.GONE
            binding.rvTerrenos.visibility = View.VISIBLE
            binding.fabAdd.visibility = View.VISIBLE
            adapter.updateData(terrenos)
        }
    }

    override fun onResume() {
        super.onResume()
        loadTerrenos()
    }
}
