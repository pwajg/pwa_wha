<template>
  <AdminLayout :key="$route.path">
    <div class="dashboard-page">
      <!-- Sección de bienvenida -->
    <div class="welcome-section">
      <h1 class="welcome-title">¡Bienvenido al Panel de Administración!</h1>
      <p class="welcome-subtitle">Has iniciado sesión como administrador</p>
      </div>

      <!-- Contenido de Reportes y Estadísticas -->
      <div class="reportes-section">

        <!-- Métricas resumidas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
          <div
            v-for="(metrica, index) in metricasResumen"
            :key="index"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6"
          >
            <div class="flex items-center">
              <div :class="metrica.iconBg" class="p-3 rounded-full flex-shrink-0">
                <svg :class="metrica.iconColor" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              <div class="ml-4 min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400 truncate">
                  {{ metrica.label }}
                </p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white break-words">
                  {{ metrica.valor }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Filtros globales -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6">
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-white">
              Filtros Globales
            </h3>
            
            <!-- Botón de exportación global unificado -->
            <div class="relative">
              <button
                @click="toggleExportMenu"
                @blur="closeExportMenu"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-lg flex items-center gap-2 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="exportando"
                aria-label="Exportar reportes"
                aria-haspopup="true"
                :aria-expanded="exportMenuOpen"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline">Exportar</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
              
              <!-- Menú desplegable -->
              <div
                v-if="exportMenuOpen"
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 py-1"
                @click.stop
              >
                <button
                  @click="exportarTodoPDF"
                  class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 active:bg-gray-200 dark:active:bg-gray-600"
                  :disabled="exportando"
                  aria-label="Exportar todos los reportes como PDF"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                  </svg>
                  Exportar como PDF
                </button>
                <button
                  @click="exportarTodoExcel"
                  class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 active:bg-gray-200 dark:active:bg-gray-600"
                  :disabled="exportando"
                  aria-label="Exportar todos los reportes como Excel"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  Exportar como Excel
                </button>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Fecha Desde
              </label>
              <input
                v-model="filtrosGlobales.fechaDesde"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500"
                @change="aplicarFiltrosGlobales"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Fecha Hasta
              </label>
              <input
                v-model="filtrosGlobales.fechaHasta"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500"
                @change="aplicarFiltrosGlobales"
              />
            </div>
            <div class="flex items-end">
              <button
                @click="limpiarFiltros"
                class="w-full px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 active:scale-95"
                aria-label="Limpiar filtros aplicados"
              >
                Limpiar Filtros
              </button>
            </div>
          </div>
        </div>

        <!-- Grid de widgets responsivo - Una sola columna para mejor legibilidad -->
        <div class="grid grid-cols-1 gap-6 mb-6">
          <!-- Widget: Volumen de Envíos -->
          <div ref="volumenEnviosRef">
            <VolumenEnviosWidget
              :key="`volumen-${refreshKey}`"
              :fecha-desde="filtrosGlobales.fechaDesde"
              :fecha-hasta="filtrosGlobales.fechaHasta"
              @filter-changed="handleFilterChanged"
            />
          </div>

          <!-- Widget: Tendencia de Crecimiento -->
          <div ref="tendenciaRef">
            <TendenciaCrecimientoWidget
              :key="`tendencia-${refreshKey}`"
              :fecha-desde="filtrosGlobales.fechaDesde"
              :fecha-hasta="filtrosGlobales.fechaHasta"
              @filter-changed="handleFilterChanged"
            />
          </div>
        </div>

        <!-- Widget: Mapa de Calor (ancho completo) -->
        <div class="mb-6" ref="mapaCalorRef">
          <MapaCalorWidget
            :key="`mapa-${refreshKey}`"
            :fecha-desde="filtrosGlobales.fechaDesde"
            :fecha-hasta="filtrosGlobales.fechaHasta"
          />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import AdminLayout from '../../components/AdminLayout.vue'
import VolumenEnviosWidget from '../../components/widgets/VolumenEnviosWidget.vue'
import TendenciaCrecimientoWidget from '../../components/widgets/TendenciaCrecimientoWidget.vue'
import MapaCalorWidget from '../../components/widgets/MapaCalorWidget.vue'
import {
  exportarMultiplesWidgetsPDF,
  exportarExcelEstructurado
} from '../../services/exportService.js'
import axios from 'axios'

const filtrosGlobales = ref({
  fechaDesde: '',
  fechaHasta: ''
})

const exportando = ref(false)
const exportMenuOpen = ref(false)
const volumenEnviosRef = ref(null)
const tendenciaRef = ref(null)
const mapaCalorRef = ref(null)
const cargandoMetricas = ref(false)
const refreshKey = ref(0) // Clave para forzar actualización de widgets
let intervaloActualizacion = null

const metricasResumen = ref([
  {
    label: 'Total Encomiendas',
    valor: '0',
    iconBg: 'bg-blue-100 dark:bg-blue-900',
    iconColor: 'text-blue-600 dark:text-blue-300'
  },
  {
    label: 'Ingresos Totales',
    valor: '$0',
    iconBg: 'bg-green-100 dark:bg-green-900',
    iconColor: 'text-green-600 dark:text-green-300'
  },
  {
    label: 'Clientes Activos',
    valor: '0',
    iconBg: 'bg-yellow-100 dark:bg-yellow-900',
    iconColor: 'text-yellow-600 dark:text-yellow-300'
  },
  {
    label: 'Sucursales',
    valor: '0',
    iconBg: 'bg-purple-100 dark:bg-purple-900',
    iconColor: 'text-purple-600 dark:text-purple-300'
  }
])

const establecerFechasPorDefecto = () => {
  const hoy = new Date()
  const haceUnMes = new Date(hoy)
  haceUnMes.setMonth(haceUnMes.getMonth() - 1)
  
  filtrosGlobales.value.fechaDesde = haceUnMes.toISOString().split('T')[0]
  filtrosGlobales.value.fechaHasta = hoy.toISOString().split('T')[0]
}

const aplicarFiltrosGlobales = () => {
  // Los filtros se aplican automáticamente a través de las props
  console.log('Aplicando filtros globales:', filtrosGlobales.value)
  // Recargar métricas cuando cambian los filtros
  cargarMetricasResumen()
}

const limpiarFiltros = () => {
  establecerFechasPorDefecto()
  aplicarFiltrosGlobales()
}

const handleFilterChanged = (filtros) => {
  filtrosGlobales.value.fechaDesde = filtros.fechaDesde
  filtrosGlobales.value.fechaHasta = filtros.fechaHasta
  aplicarFiltrosGlobales()
}

const toggleExportMenu = () => {
  exportMenuOpen.value = !exportMenuOpen.value
}

const closeExportMenu = () => {
  setTimeout(() => {
    exportMenuOpen.value = false
  }, 200)
}

const exportarTodoPDF = async () => {
  exportMenuOpen.value = false
  exportando.value = true
  try {
    const elementos = [
      volumenEnviosRef.value?.$el || volumenEnviosRef.value,
      tendenciaRef.value?.$el || tendenciaRef.value,
      mapaCalorRef.value?.$el || mapaCalorRef.value
    ].filter(el => el !== null)
    
    if (elementos.length === 0) {
      alert('No hay widgets disponibles para exportar')
      return
    }
    
    await exportarMultiplesWidgetsPDF(
      elementos,
      `reporte-completo-${new Date().toISOString().split('T')[0]}`,
      'Reporte Completo - Whafren'
    )
  } catch (error) {
    console.error('Error al exportar PDF:', error)
    alert('Error al exportar el reporte. Por favor, intente nuevamente.')
  } finally {
    exportando.value = false
  }
}

const exportarTodoExcel = async () => {
  exportMenuOpen.value = false
  exportando.value = true
  try {
    // Recopilar datos de todos los widgets
    const datosParaExportar = [
      {
        nombre: 'Volumen_Envios',
        titulo: 'Volumen de Envíos',
        datos: [
          // Estos datos deberían venir de los widgets
          { Periodo: 'Semana 1', Cantidad: 320, Estado: 'Enviado' },
          { Periodo: 'Semana 2', Cantidad: 345, Estado: 'En tránsito' }
        ]
      },
      {
        nombre: 'Tendencia_Crecimiento',
        titulo: 'Tendencia de Crecimiento',
        datos: [
          { Mes: 'Enero', Envios: 300, Ganancias: 1500000 },
          { Mes: 'Febrero', Envios: 350, Ganancias: 1750000 }
        ]
      },
      {
        nombre: 'Mapa_Calor',
        titulo: 'Mapa de Calor de Zonas',
        datos: [
          { Zona: 'Trujillo', Envios: 800, Ingresos: 2000000 },
          { Zona: 'El Porvenir', Envios: 600, Ingresos: 1500000 }
        ]
      }
    ]
    
    exportarExcelEstructurado(
      datosParaExportar,
      `reporte-completo-${new Date().toISOString().split('T')[0]}`
    )
  } catch (error) {
    console.error('Error al exportar Excel:', error)
    alert('Error al exportar el reporte. Por favor, intente nuevamente.')
  } finally {
    exportando.value = false
  }
}

const formatearNumero = (numero) => {
  return numero.toLocaleString('es-PE')
}

const formatearMoneda = (monto) => {
  return `$${monto.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
}

const cargarMetricasResumen = async () => {
  try {
    cargandoMetricas.value = true
    const response = await axios.get('/reportes/metricas-resumen', {
      params: filtrosGlobales.value
    })
    
    const datos = response.data
    
    // Actualizar métricas con datos reales
    metricasResumen.value[0].valor = formatearNumero(datos.totalEncomiendas)
    metricasResumen.value[1].valor = formatearMoneda(datos.ingresosTotales)
    metricasResumen.value[2].valor = formatearNumero(datos.clientesActivos)
    metricasResumen.value[3].valor = formatearNumero(datos.totalSucursales)
  } catch (error) {
    console.error('Error al cargar métricas resumen:', error)
    // Mantener valores por defecto en caso de error
  } finally {
    cargandoMetricas.value = false
  }
}

const actualizarTodosLosDatos = () => {
  // Actualizar métricas resumen
  cargarMetricasResumen()
  
  // Incrementar refreshKey para forzar actualización de widgets
  refreshKey.value++
}

onMounted(() => {
  establecerFechasPorDefecto()
  cargarMetricasResumen()
  
  // Actualizar datos cada 30 segundos para tiempo real
  intervaloActualizacion = setInterval(() => {
    actualizarTodosLosDatos()
  }, 30000) // 30 segundos
})

onUnmounted(() => {
  // Limpiar intervalo cuando el componente se desmonte
  if (intervaloActualizacion) {
    clearInterval(intervaloActualizacion)
  }
})
</script>

<style scoped>
/* Sección de bienvenida */
.welcome-section {
  text-align: center;
  margin-bottom: 2rem;
  padding: 1.25rem 1.5rem;
  background: linear-gradient(135deg, #ffffff, #f8fafc);
  border-radius: 0.75rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.dark .welcome-section {
  background: linear-gradient(135deg, #111827, #1f2937);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.welcome-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.25rem;
  transition: color 0.3s ease;
}

.dark .welcome-title {
  color: #f9fafb;
}

.welcome-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
  transition: color 0.3s ease;
}

.dark .welcome-subtitle {
  color: #d1d5db;
}

/* Sección de reportes */
.dashboard-page {
  padding: 1.5rem;
}

.reportes-section {
  width: 100%;
}

@media (max-width: 640px) {
  .dashboard-page {
    padding: 1rem;
  }
  
  .welcome-section {
    padding: 1rem;
    margin-bottom: 1.5rem;
  }
  
  .welcome-title {
    font-size: 1.25rem;
  }
  
  .welcome-subtitle {
    font-size: 0.8125rem;
  }
}

/* Animaciones */
.welcome-section {
  animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>
