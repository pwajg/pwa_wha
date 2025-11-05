<template>
  <BaseWidget
    :title="'Tendencia de Crecimiento'"
    :subtitle="`Visualizando: ${vistaActual === 'envios' ? 'Envíos' : 'Ganancias'}`"
    :show-filter="true"
    :show-export="true"
    :loading="loading"
    :empty="!loading && datos.length === 0"
    empty-message="No hay datos históricos disponibles"
    :icon="ChartLineIcon"
    icon-bg="bg-green-100 dark:bg-green-900"
    icon-color="text-green-600 dark:text-green-300"
    @filter="mostrarFiltros = true"
    @export-pdf="exportarPDF"
    @export-excel="exportarExcel"
  >
    <template v-if="!loading && datos.length > 0">
      <div data-widget="tendencia-crecimiento">
      <!-- Selector de vista -->
      <div class="flex flex-wrap gap-2 mb-6">
        <button
          v-for="vista in vistas"
          :key="vista.value"
          @click="cambiarVista(vista.value)"
          :class="[
            'px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors flex items-center gap-2 whitespace-nowrap',
            vistaActual === vista.value
              ? 'bg-blue-600 text-white'
              : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
          ]"
        >
          <component :is="vista.icon" class="w-4 h-4 flex-shrink-0" />
          <span>{{ vista.label }}</span>
        </button>
      </div>

      <!-- Métricas destacadas -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
        <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg p-3 sm:p-4 min-w-0">
          <p class="text-xs font-medium text-green-600 dark:text-green-300 uppercase tracking-wide truncate">
            {{ vistaActual === 'envios' ? 'Total Envíos' : 'Total Ganancias' }}
          </p>
          <p class="text-xl sm:text-2xl font-bold text-green-900 dark:text-green-100 mt-1 break-words">
            {{ vistaActual === 'envios' 
              ? metricas.total.toLocaleString() 
              : `$${metricas.total.toLocaleString()}` }}
          </p>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg p-3 sm:p-4 min-w-0">
          <p class="text-xs font-medium text-blue-600 dark:text-blue-300 uppercase tracking-wide truncate">
            Promedio Mensual
          </p>
          <p class="text-xl sm:text-2xl font-bold text-blue-900 dark:text-blue-100 mt-1 break-words">
            {{ vistaActual === 'envios' 
              ? metricas.promedio.toLocaleString() 
              : `$${metricas.promedio.toLocaleString()}` }}
          </p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-lg p-3 sm:p-4 min-w-0">
          <p class="text-xs font-medium text-purple-600 dark:text-purple-300 uppercase tracking-wide truncate">
            Crecimiento
          </p>
          <p class="text-xl sm:text-2xl font-bold text-purple-900 dark:text-purple-100 mt-1 break-words">
            {{ metricas.crecimiento >= 0 ? '+' : '' }}{{ metricas.crecimiento.toFixed(1) }}%
          </p>
        </div>
      </div>

      <!-- Gráfico de líneas -->
      <div class="h-64 sm:h-96 overflow-hidden">
        <Line
          v-if="chartData"
          :data="chartData"
          :options="chartOptions"
        />
      </div>
      </div>
    </template>

    <!-- Modal de filtros -->
    <div
      v-if="mostrarFiltros"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click="mostrarFiltros = false"
    >
      <div
        class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4"
        @click.stop
      >
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
          Filtros de Fecha
        </h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Fecha Desde
            </label>
            <input
              v-model="fechaDesde"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Fecha Hasta
            </label>
            <input
              v-model="fechaHasta"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            />
          </div>
          <div class="flex gap-2 justify-end">
            <button
              @click="mostrarFiltros = false"
              class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            >
              Cancelar
            </button>
            <button
              @click="aplicarFiltros"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
              Aplicar
            </button>
          </div>
        </div>
      </div>
    </div>
  </BaseWidget>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'
import BaseWidget from './BaseWidget.vue'
import { exportarPDF as exportarPDFService, exportarExcel as exportarExcelService } from '../../services/exportService.js'
import ChartLineIcon from '../icons/ChartLineIcon.vue'

// Registrar componentes de Chart.js
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
)

const props = defineProps({
  fechaDesde: {
    type: String,
    default: ''
  },
  fechaHasta: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['filter-changed'])

const loading = ref(false)
const datos = ref([])
const vistaActual = ref('envios') // 'envios' o 'ganancias'
const mostrarFiltros = ref(false)
const fechaDesde = ref('')
const fechaHasta = ref('')

const vistas = [
  {
    label: 'Envíos',
    value: 'envios',
    icon: 'svg'
  },
  {
    label: 'Ganancias',
    value: 'ganancias',
    icon: 'svg'
  }
]

const metricas = computed(() => {
  if (datos.value.length === 0) {
    return { total: 0, promedio: 0, crecimiento: 0 }
  }
  
  const valores = datos.value.map(item => 
    vistaActual.value === 'envios' ? item.envios : item.ganancias
  )
  
  const total = valores.reduce((sum, val) => sum + val, 0)
  const promedio = total / valores.length
  
  // Calcular crecimiento (comparar último mes con penúltimo)
  let crecimiento = 0
  if (valores.length >= 2) {
    const ultimo = valores[valores.length - 1]
    const anterior = valores[valores.length - 2]
    crecimiento = anterior > 0 ? ((ultimo - anterior) / anterior) * 100 : 0
  }
  
  return { total, promedio, crecimiento }
})

const chartData = computed(() => {
  if (datos.value.length === 0) return null
  
  return {
    labels: datos.value.map(item => item.mes),
    datasets: [
      {
        label: vistaActual.value === 'envios' ? 'Envíos' : 'Ganancias ($)',
        data: datos.value.map(item => 
          vistaActual.value === 'envios' ? item.envios : item.ganancias
        ),
        borderColor: vistaActual.value === 'envios' 
          ? 'rgba(59, 130, 246, 1)'
          : 'rgba(16, 185, 129, 1)',
        backgroundColor: vistaActual.value === 'envios'
          ? 'rgba(59, 130, 246, 0.1)'
          : 'rgba(16, 185, 129, 0.1)',
        fill: true,
        tension: 0.4,
        pointRadius: 4,
        pointHoverRadius: 6,
        pointBackgroundColor: vistaActual.value === 'envios'
          ? 'rgba(59, 130, 246, 1)'
          : 'rgba(16, 185, 129, 1)',
        pointBorderColor: '#fff',
        pointBorderWidth: 2
      }
    ]
  }
})

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'top'
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: '#fff',
      bodyColor: '#fff',
      borderColor: 'rgba(255, 255, 255, 0.1)',
      borderWidth: 1,
      callbacks: {
        label: function(context) {
          const value = context.parsed.y
          if (vistaActual.value === 'ganancias') {
            return `$${value.toLocaleString()}`
          }
          return `${value.toLocaleString()} envíos`
        }
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: {
        color: 'rgba(0, 0, 0, 0.05)'
      },
      ticks: {
        callback: function(value) {
          if (vistaActual.value === 'ganancias') {
            return `$${value.toLocaleString()}`
          }
          return value.toLocaleString()
        }
      }
    },
    x: {
      grid: {
        display: false
      }
    }
  },
  interaction: {
    intersect: false,
    mode: 'index'
  }
}

const cargarDatos = async () => {
  loading.value = true
  try {
    // Simular datos por ahora - reemplazar con llamada real a la API
    await new Promise(resolve => setTimeout(resolve, 500))
    
    // Datos de ejemplo para los últimos 6 meses
    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio']
    datos.value = meses.map((mes, index) => ({
      mes,
      envios: 300 + (index * 50) + Math.floor(Math.random() * 100),
      ganancias: 1500000 + (index * 250000) + Math.floor(Math.random() * 500000)
    }))
    
    // TODO: Reemplazar con llamada real a la API
    // const params = { fechaDesde: fechaDesde.value, fechaHasta: fechaHasta.value }
    // const response = await axios.get('/api/reportes/tendencia-crecimiento', { params })
    // datos.value = response.data
  } catch (error) {
    console.error('Error al cargar datos de tendencia:', error)
  } finally {
    loading.value = false
  }
}

const cambiarVista = (vista) => {
  vistaActual.value = vista
}

const aplicarFiltros = () => {
  mostrarFiltros.value = false
  emit('filter-changed', {
    fechaDesde: fechaDesde.value,
    fechaHasta: fechaHasta.value
  })
  cargarDatos()
}

const exportarPDF = async () => {
  try {
    const elemento = document.querySelector('[data-widget="tendencia-crecimiento"]')
    if (elemento) {
      await exportarPDFService(elemento, `tendencia-crecimiento-${new Date().toISOString().split('T')[0]}`, 'Tendencia de Crecimiento')
    }
  } catch (error) {
    console.error('Error al exportar PDF:', error)
  }
}

const exportarExcel = () => {
  try {
    const datosParaExportar = datos.value.map(item => ({
      Mes: item.mes,
      Envíos: item.envios,
      Ganancias: item.ganancias
    }))
    
    exportarExcelService(datosParaExportar, `tendencia-crecimiento-${new Date().toISOString().split('T')[0]}`, 'Tendencia de Crecimiento')
  } catch (error) {
    console.error('Error al exportar Excel:', error)
  }
}

onMounted(() => {
  const hoy = new Date()
  const haceSeisMeses = new Date(hoy)
  haceSeisMeses.setMonth(haceSeisMeses.getMonth() - 6)
  
  fechaDesde.value = haceSeisMeses.toISOString().split('T')[0]
  fechaHasta.value = hoy.toISOString().split('T')[0]
  
  cargarDatos()
})

watch(() => props.fechaDesde, (nuevo) => {
  if (nuevo) fechaDesde.value = nuevo
})

watch(() => props.fechaHasta, (nuevo) => {
  if (nuevo) fechaHasta.value = nuevo
})
</script>


