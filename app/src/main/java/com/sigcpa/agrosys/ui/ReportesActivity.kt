package com.sigcpa.agrosys.ui

import android.Manifest
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.graphics.Canvas
import android.graphics.Color
import android.graphics.Paint
import android.graphics.pdf.PdfDocument
import android.os.Build
import android.os.Bundle
import android.os.Environment
import android.view.LayoutInflater
import android.view.ViewGroup
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.core.graphics.toColorInt
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.github.mikephil.charting.components.XAxis
import com.github.mikephil.charting.data.BarData
import com.github.mikephil.charting.data.BarDataSet
import com.github.mikephil.charting.data.BarEntry
import com.github.mikephil.charting.formatter.ValueFormatter
import com.sigcpa.agrosys.database.AppDatabase
import com.sigcpa.agrosys.database.entities.CultivoEntity
import com.sigcpa.agrosys.databinding.ActivityReportesBinding
import com.sigcpa.agrosys.databinding.ItemReporteCultivoBinding
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.io.File
import java.io.FileOutputStream
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

class ReportesActivity : AppCompatActivity() {

    private lateinit var binding: ActivityReportesBinding
    private lateinit var db: AppDatabase
    private var agricultorId: Int = -1
    private var lastReportData: List<CultivoReportInfo> = emptyList()

    private val requestPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestPermission()
    ) { isGranted: Boolean ->
        if (isGranted) {
            exportToPDF()
        } else {
            Toast.makeText(this, "Permiso denegado para guardar el PDF", Toast.LENGTH_SHORT).show()
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityReportesBinding.inflate(layoutInflater)
        setContentView(binding.root)

        // Forzar barra de estado verde y iconos blancos
        window.statusBarColor = android.graphics.Color.parseColor("#15803D")
        androidx.core.view.WindowInsetsControllerCompat(window, window.decorView).isAppearanceLightStatusBars = false

        // Ajustar el Header para que respete la barra de estado superior
        androidx.core.view.ViewCompat.setOnApplyWindowInsetsListener(binding.headerContainer) { v, insets ->
            val systemBars = insets.getInsets(androidx.core.view.WindowInsetsCompat.Type.systemBars())
            v.setPadding(
                v.paddingLeft,
                systemBars.top,
                v.paddingRight,
                v.paddingBottom
            )
            insets
        }

        db = AppDatabase.getDatabase(this)
        
        // Obtener ID del agricultor de SharedPreferences
        val sharedPref = getSharedPreferences("agrosys_prefs", android.content.Context.MODE_PRIVATE)
        agricultorId = sharedPref.getInt("USER_ID", -1)

        setupRecyclerView()
        loadReportData()
        setupNavigation()

        binding.btnBack.setOnClickListener { finish() }

        binding.btnExportar.setOnClickListener {
            if (lastReportData.isNotEmpty()) {
                exportToPDF()
            } else {
                Toast.makeText(this, "No hay datos para exportar", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun setupNavigation() {
        binding.btnNavHome.setOnClickListener {
            val intent = Intent(this, DashboardActivity::class.java)
            intent.flags = Intent.FLAG_ACTIVITY_CLEAR_TOP
            startActivity(intent)
            finish()
        }
        binding.btnNavTerrenos.setOnClickListener {
            startActivity(Intent(this, TerrenosListActivity::class.java))
            finish()
        }
        binding.btnNavCultivos.setOnClickListener {
            startActivity(Intent(this, CultivosListActivity::class.java))
            finish()
        }
        binding.btnNavLabores.setOnClickListener {
            startActivity(Intent(this, LaboresListActivity::class.java))
            finish()
        }
    }

    override fun onResume() {
        super.onResume()
        loadReportData()
    }

    private fun setupRecyclerView() {
        binding.rvDetalleCultivos.layoutManager = LinearLayoutManager(this)
    }

    private fun loadReportData() {
        lifecycleScope.launch {
            val cultivos = db.assetDao().getCultivosByAgricultor(agricultorId)
            val reportData = mutableListOf<CultivoReportInfo>()
            
            var totalCostosGlobal = 0.0
            var totalIngresosGlobal = 0.0

            for (cultivo in cultivos) {
                val info = calculateCultivoReport(cultivo)
                reportData.add(info)
                totalCostosGlobal += info.totalCostos
                totalIngresosGlobal += info.totalIngresos
            }

            updateUI(reportData, totalCostosGlobal, totalIngresosGlobal)
        }
    }

    private suspend fun calculateCultivoReport(cultivo: CultivoEntity): CultivoReportInfo {
        val labores = db.assetDao().getLaboresByCultivo(cultivo.id)
        val insumos = db.assetDao().getInsumosByCultivo(cultivo.id)
        val ventas = db.assetDao().getVentasByCultivo(cultivo.id)
        val terreno = db.assetDao().getTerrenoById(cultivo.terreno_id)

        var prep = 0.0
        var siembra = 0.0
        var mant = 0.0
        var cosechaCost = 0.0

        for (labor in labores) {
            val laborCost = (labor.costo_mano_obra_total ?: 0.0) + (labor.costo_maquinaria_total ?: 0.0)
            
            // Categorización simple basada en el ID o nombre del catálogo
            when (labor.catalogo_labor_id) {
                1 -> prep += laborCost
                2 -> siembra += laborCost
                3, 4, 5, 6, 7, 8 -> mant += laborCost // 8 es poda según AppDatabase
                9 -> cosechaCost += laborCost // 9 es Cosecha según AppDatabase
                else -> mant += laborCost
            }
        }

        for (insumo in insumos) {
            val insumoCost = (insumo.cantidad ?: 0.0) * (insumo.costo_unitario ?: 0.0)
            mant += insumoCost
        }

        var ingresos = 0.0
        for (venta in ventas) {
            ingresos += (venta.cantidad_vendida_kg ?: 0.0) * (venta.precio_por_kg ?: 0.0)
        }

        val totalCostos = prep + siembra + mant + cosechaCost

        return CultivoReportInfo(
            id = cultivo.id,
            nombre = cultivo.nombre_lote ?: "Cultivo #${cultivo.id}",
            terrenoNombre = terreno?.nombre ?: "Sin terreno",
            costoPrep = prep,
            costoSiembra = siembra,
            costoMant = mant,
            costoCosecha = cosechaCost,
            totalCostos = totalCostos,
            totalIngresos = ingresos,
            ganancia = ingresos - totalCostos
        )
    }

    private fun updateUI(data: List<CultivoReportInfo>, totalCostos: Double, totalIngresos: Double) {
        lastReportData = data
        val gananciaGlobal = totalIngresos - totalCostos
        
        binding.tvGananciaGlobal.text = String.format(Locale.getDefault(), "S/ %.2f", gananciaGlobal)
        binding.tvTotalCostos.text = String.format(Locale.getDefault(), "S/ %.0f", totalCostos)
        binding.tvTotalIngresos.text = String.format(Locale.getDefault(), "S/ %.0f", totalIngresos)
        binding.tvTotalCultivos.text = data.size.toString()

        setupBarChart(data)
        binding.rvDetalleCultivos.adapter = ReportAdapter(data)
    }

    private fun setupBarChart(data: List<CultivoReportInfo>) {
        if (data.isEmpty()) return

        val entries = data.mapIndexed { index, info ->
            BarEntry(index.toFloat(), info.ganancia.toFloat())
        }

        val dataSet = BarDataSet(entries, "Ganancia")
        dataSet.colors = data.map { if (it.ganancia >= 0) "#2D6A4F".toColorInt() else "#DC2626".toColorInt() }
        dataSet.valueTextColor = Color.BLACK
        dataSet.valueTextSize = 10f

        val barData = BarData(dataSet)
        barData.barWidth = 0.7f

        binding.barChart.apply {
            this.data = barData
            description.isEnabled = false
            legend.isEnabled = false
            
            xAxis.apply {
                position = XAxis.XAxisPosition.BOTTOM
                setDrawGridLines(false)
                granularity = 1f
                valueFormatter = object : ValueFormatter() {
                    override fun getFormattedValue(value: Float): String {
                        val index = value.toInt()
                        return if (index >= 0 && index < data.size) {
                            val name = data[index].nombre
                            if (name.length > 10) name.substring(0, 8) + ".." else name
                        } else ""
                    }
                }
            }

            axisLeft.apply {
                setDrawGridLines(true)
                axisMinimum = if (data.any { it.ganancia < 0 }) data.minOf { it.ganancia }.toFloat() - 100f else 0f
            }
            axisRight.isEnabled = false
            animateY(1000)
            invalidate()
        }
    }

    private fun exportToPDF() {
        if (Build.VERSION.SDK_INT <= Build.VERSION_CODES.P &&
            ContextCompat.checkSelfPermission(this, Manifest.permission.WRITE_EXTERNAL_STORAGE) != PackageManager.PERMISSION_GRANTED
        ) {
            requestPermissionLauncher.launch(Manifest.permission.WRITE_EXTERNAL_STORAGE)
            return
        }

        lifecycleScope.launch {
            val success = withContext(Dispatchers.IO) {
                generatePDFFile()
            }
            if (success) {
                Toast.makeText(this@ReportesActivity, "Reporte exportado a Descargas", Toast.LENGTH_LONG).show()
            } else {
                Toast.makeText(this@ReportesActivity, "Error al generar PDF", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun generatePDFFile(): Boolean {
        val pdfDocument = PdfDocument()
        val paint = Paint()
        val titlePaint = Paint()

        // Configuración de página (A4 aprox 595 x 842)
        val pageInfo = PdfDocument.PageInfo.Builder(595, 842, 1).create()
        val page = pdfDocument.startPage(pageInfo)
        val canvas: Canvas = page.canvas

        // Cabecera
        titlePaint.color = "#15803D".toColorInt()
        titlePaint.textSize = 20f
        titlePaint.isFakeBoldText = true
        canvas.drawText("REPORTE DE RENTABILIDAD - AGROSYS", 40f, 60f, titlePaint)

        paint.textSize = 12f
        val sdf = SimpleDateFormat("dd/MM/yyyy HH:mm", Locale.getDefault())
        canvas.drawText("Fecha de generación: ${sdf.format(Date())}", 40f, 85f, paint)

        // Resumen General
        paint.isFakeBoldText = true
        canvas.drawText("RESUMEN GENERAL", 40f, 130f, paint)
        paint.isFakeBoldText = false
        
        var totalCostos = 0.0
        var totalIngresos = 0.0
        lastReportData.forEach {
            totalCostos += it.totalCostos
            totalIngresos += it.totalIngresos
        }
        
        canvas.drawText("Cultivos Activos: ${lastReportData.size}", 40f, 155f, paint)
        canvas.drawText("Inversión Total: S/ ${String.format(Locale.getDefault(), "%.2f", totalCostos)}", 40f, 175f, paint)
        canvas.drawText("Ingresos Totales: S/ ${String.format(Locale.getDefault(), "%.2f", totalIngresos)}", 40f, 195f, paint)
        
        paint.isFakeBoldText = true
        val ganancia = totalIngresos - totalCostos
        paint.color = if (ganancia >= 0) "#15803D".toColorInt() else Color.RED
        canvas.drawText("GANANCIA NETA: S/ ${String.format(Locale.getDefault(), "%.2f", ganancia)}", 40f, 220f, paint)

        // Tabla de Detalle
        paint.color = Color.BLACK
        paint.isFakeBoldText = true
        canvas.drawText("DETALLE POR CULTIVO", 40f, 270f, paint)
        
        // Cabeceras de tabla
        var y = 300f
        paint.textSize = 10f
        canvas.drawText("CULTIVO", 40f, y, paint)
        canvas.drawText("COSTOS", 250f, y, paint)
        canvas.drawText("INGRESOS", 350f, y, paint)
        canvas.drawText("GANANCIA", 460f, y, paint)
        
        canvas.drawLine(40f, y + 5, 550f, y + 5, paint)
        
        paint.isFakeBoldText = false
        y += 25f

        for (item in lastReportData) {
            if (y > 780) break // Evitar desborde simple (en una sola página por ahora)
            
            canvas.drawText(if (item.nombre.length > 25) item.nombre.take(22) + "..." else item.nombre, 40f, y, paint)
            canvas.drawText("S/ ${String.format(Locale.getDefault(), "%.0f", item.totalCostos)}", 250f, y, paint)
            canvas.drawText("S/ ${String.format(Locale.getDefault(), "%.0f", item.totalIngresos)}", 350f, y, paint)
            
            val originalColor = paint.color
            paint.color = if (item.ganancia >= 0) "#15803D".toColorInt() else Color.RED
            canvas.drawText("S/ ${String.format(Locale.getDefault(), "%.2f", item.ganancia)}", 460f, y, paint)
            paint.color = originalColor
            
            y += 20f
        }

        pdfDocument.finishPage(page)

        // Guardar archivo
        val directory = Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_DOWNLOADS)
        val fileName = "Reporte_AgroSys_${System.currentTimeMillis()}.pdf"
        val file = File(directory, fileName)

        return try {
            pdfDocument.writeTo(FileOutputStream(file))
            pdfDocument.close()
            true
        } catch (e: Exception) {
            e.printStackTrace()
            pdfDocument.close()
            false
        }
    }

    data class CultivoReportInfo(
        val id: Int,
        val nombre: String,
        val terrenoNombre: String,
        val costoPrep: Double,
        val costoSiembra: Double,
        val costoMant: Double,
        val costoCosecha: Double,
        val totalCostos: Double,
        val totalIngresos: Double,
        val ganancia: Double
    )

    inner class ReportAdapter(private val items: List<CultivoReportInfo>) : RecyclerView.Adapter<ReportAdapter.ViewHolder>() {
        inner class ViewHolder(val itemBinding: ItemReporteCultivoBinding) : RecyclerView.ViewHolder(itemBinding.root)

        override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
            val b = ItemReporteCultivoBinding.inflate(LayoutInflater.from(parent.context), parent, false)
            return ViewHolder(b)
        }

        override fun onBindViewHolder(holder: ViewHolder, position: Int) {
            val item = items[position]
            holder.itemBinding.apply {
                tvCultivoNombre.text = item.nombre
                tvTerrenoNombre.text = item.terrenoNombre
                tvGananciaCultivo.text = String.format(Locale.getDefault(), "S/ %.2f", item.ganancia)
                tvGananciaCultivo.setTextColor(if (item.ganancia >= 0) "#15803D".toColorInt() else Color.RED)
                
                tvCostoPrep.text = String.format(Locale.getDefault(), "S/ %.2f", item.costoPrep)
                tvCostoSiembra.text = String.format(Locale.getDefault(), "S/ %.2f", item.costoSiembra)
                tvCostoMant.text = String.format(Locale.getDefault(), "S/ %.2f", item.costoMant)
                tvCostoCosecha.text = String.format(Locale.getDefault(), "S/ %.2f", item.costoCosecha)
                
                tvCostosTotales.text = String.format(Locale.getDefault(), "S/ %.2f", item.totalCostos)
                tvIngresosTotales.text = String.format(Locale.getDefault(), "S/ %.2f", item.totalIngresos)
            }
        }

        override fun getItemCount() = items.size
    }
}
