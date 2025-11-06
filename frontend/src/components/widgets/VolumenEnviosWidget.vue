<template>
  <BaseWidget
    :title="'Volumen de Envíos'"
    :subtitle="`Periodo: ${periodoTexto}`"
    :show-filter="true"
    :show-export="true"
    :loading="loading"
    :empty="!loading && datos.length === 0"
    empty-message="No hay datos de envíos para el periodo seleccionado"
    :icon="ChartBarIcon"
    icon-bg="bg-blue-100 dark:bg-blue-900"
    icon-color="text-blue-600 dark:text-blue-300"
    @filter="mostrarFiltros = true"
    @export-pdf="exportarPDF"
    @export-excel="exportarExcel"
  >
    <!-- Contenido principal -->
    <template v-if="!loading && datos.length > 0">
      <div data-widget="volumen-envios">
        <!-- Totales destacados -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-6">
          <div
            v-for="(total, index) in totales"
            :key="index"
            class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg p-3 sm:p-4 min-w-0"
          >
            <p class="text-xs font-medium text-blue-600 dark:text-blue-300 uppercase tracking-wide truncate">
              {{ total.label }}
            </p>
            <p class="text-xl sm:text-2xl font-bold text-blue-900 dark:text-blue-100 mt-1 break-words">
              {{ total.valor.toLocaleString() }}
            </p>
          </div>
        </div>

        <!-- Selector de periodo -->
        <div class="flex flex-wrap gap-2 mb-4">
          <button
            v-for="p in periodos"
            :key="p.value"
            @click="cambiarPeriodo(p.value)"
            :class="[
              'px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors whitespace-nowrap',
              periodo === p.value
                ? 'bg-blue-600 text-white'
                : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
            ]"
          >
            {{ p.label }}
          </button>
        </div>

        <!-- Selector de estado -->
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Filtrar por estado:
          </label>
          <select
            v-model="estadoFiltro"
            @change="aplicarFiltroEstado"
            class="w-full md:w-auto px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Todos los estados</option>
            <option value="Registrado">Registrado</option>
            <option value="Enviado">Enviado</option>
            <option value="En tránsito">En tránsito</option>
            <option value="En destino">En destino</option>
            <option value="Entregado">Entregado</option>
            <option value="Devuelto">Devuelto</option>
          </select>
        </div>

        <!-- Gráfico de barras -->
        <div class="h-64 sm:h-80 overflow-hidden">
          <Bar
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
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import BaseWidget from './BaseWidget.vue'
import axios from 'axios'
import { exportarPDF as exportarPDFService, exportarExcel as exportarExcelService } from '../../services/exportService.js'
import ChartBarIcon from '../icons/ChartBarIcon.vue'

// Registrar componentes de Chart.js
ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
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
const periodo = ref('semana') // 'dia', 'semana', 'mes'
const estadoFiltro = ref('')
const mostrarFiltros = ref(false)
const fechaDesde = ref('')
const fechaHasta = ref('')

const periodos = [
  { label: 'Día', value: 'dia' },
  { label: 'Semana', value: 'semana' },
  { label: 'Mes', value: 'mes' }
]

const periodoTexto = computed(() => {
  const map = {
    dia: 'Último día',
    semana: 'Última semana',
    mes: 'Último mes'
  }
  return map[periodo.value] || 'Última semana'
})

const totales = computed(() => {
  if (datos.value.length === 0) return []
  
  const total = datos.value.reduce((sum, item) => sum + item.cantidad, 0)
  const porEstado = datos.value.reduce((acc, item) => {
    acc[item.estado] = (acc[item.estado] || 0) + item.cantidad
    return acc
  }, {})
  
  return [
    { label: 'Total Envíos', valor: total },
    { label: 'Pendientes', valor: porEstado['Registrado'] || 0 },
    { label: 'En Tránsito', valor: (porEstado['Enviado'] || 0) + (porEstado['En tránsito'] || 0) },
    { label: 'Entregados', valor: porEstado['Entregado'] || 0 }
  ]
})

const chartData = computed(() => {
  if (datos.value.length === 0) return null
  
  let datosFiltrados = datos.value
  
  if (estadoFiltro.value) {
    datosFiltrados = datos.value.filter(item => item.estado === estadoFiltro.value)
  }
  
  return {
    labels: datosFiltrados.map(item => item.label),
    datasets: [
      {
        label: 'Cantidad de Envíos',
        data: datosFiltrados.map(item => item.cantidad),
        backgroundColor: [
          'rgba(59, 130, 246, 0.8)',
          'rgba(16, 185, 129, 0.8)',
          'rgba(251, 191, 36, 0.8)',
          'rgba(239, 68, 68, 0.8)',
          'rgba(139, 92, 246, 0.8)',
          'rgba(236, 72, 153, 0.8)'
        ],
        borderColor: [
          'rgba(59, 130, 246, 1)',
          'rgba(16, 185, 129, 1)',
          'rgba(251, 191, 36, 1)',
          'rgba(239, 68, 68, 1)',
          'rgba(139, 92, 246, 1)',
          'rgba(236, 72, 153, 1)'
        ],
        borderWidth: 1
      }
    ]
  }
})

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: '#fff',
      bodyColor: '#fff',
      borderColor: 'rgba(255, 255, 255, 0.1)',
      borderWidth: 1
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        precision: 0
      },
      grid: {
        color: 'rgba(0, 0, 0, 0.05)'
      }
    },
    x: {
      grid: {
        display: false
      }
    }
  }
}

const cargarDatos = async () => {
  loading.value = true
  try {
    const params = {
      periodo: periodo.value
    }
    
    if (fechaDesde.value) params.fechaDesde = fechaDesde.value
    if (fechaHasta.value) params.fechaHasta = fechaHasta.value
    
    const response = await axios.get('/reportes/volumen-envios', { params })
    datos.value = response.data
    
    // Si no hay datos, usar array vacío
    if (!datos.value || datos.value.length === 0) {
      datos.value = []
    }
  } catch (error) {
    console.error('Error al cargar datos de volumen de envíos:', error)
    datos.value = []
  } finally {
    loading.value = false
  }
}

const cambiarPeriodo = (nuevoPeriodo) => {
  periodo.value = nuevoPeriodo
  cargarDatos()
}

const aplicarFiltroEstado = () => {
  // El filtro se aplica automáticamente en el computed chartData
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
    const elemento = document.querySelector('[data-widget="volumen-envios"]')
    if (elemento) {
      await exportarPDFService(elemento, `volumen-envios-${new Date().toISOString().split('T')[0]}`, 'Volumen de Envíos')
    }
  } catch (error) {
    console.error('Error al exportar PDF:', error)
  }
}

const exportarExcel = () => {
  try {
    const datosParaExportar = datos.value.map(item => ({
      Periodo: item.label,
      Cantidad: item.cantidad,
      Estado: item.estado
    }))
    
    exportarExcelService(datosParaExportar, `volumen-envios-${new Date().toISOString().split('T')[0]}`, 'Volumen de Envíos')
  } catch (error) {
    console.error('Error al exportar Excel:', error)
  }
}

onMounted(() => {
  const hoy = new Date()
  const haceUnMes = new Date(hoy)
  haceUnMes.setMonth(haceUnMes.getMonth() - 1)
  
  fechaDesde.value = haceUnMes.toISOString().split('T')[0]
  fechaHasta.value = hoy.toISOString().split('T')[0]
  
  cargarDatos()
})

watch(() => props.fechaDesde, (nuevo) => {
  if (nuevo) fechaDesde.value = nuevo
  cargarDatos()
})

watch(() => props.fechaHasta, (nuevo) => {
  if (nuevo) fechaHasta.value = nuevo
  cargarDatos()
})

watch([() => props.fechaDesde, () => props.fechaHasta], () => {
  if (props.fechaDesde || props.fechaHasta) {
    cargarDatos()
  }
})
</script>


