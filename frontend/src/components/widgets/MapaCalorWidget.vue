<template>
  <BaseWidget
    title="Mapa de Calor de Zonas"
    subtitle="Distritos y pueblos de Pataz y Trujillo (La Libertad, Perú)"
    :show-filter="true"
    :show-export="true"
    :loading="loading"
    :empty="!loading && zonas.length === 0"
    empty-message="No hay datos de zonas disponibles"
    :icon="MapIcon"
    icon-bg="bg-purple-100 dark:bg-purple-900"
    icon-color="text-purple-600 dark:text-purple-300"
    @filter="mostrarFiltros = true"
    @export-pdf="exportarPDF"
    @export-excel="exportarExcel"
  >
    <template v-if="!loading && zonas.length > 0">
      <div data-widget="mapa-calor">
      <!-- Leyenda de colores -->
      <div class="mb-4 flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
        <div class="flex items-center gap-2 flex-shrink-0">
          <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Intensidad:</span>
          <div class="flex gap-1">
            <div
              v-for="(color, index) in coloresLeyenda"
              :key="index"
              :class="color"
              class="w-6 sm:w-8 h-3 sm:h-4 rounded"
              :title="`${index * 25}% - ${(index + 1) * 25}%`"
            ></div>
          </div>
        </div>
        <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 break-words">
          Total: {{ totalEnvios.toLocaleString() }} envíos | 
          Ingresos: ${{ totalIngresos.toLocaleString() }}
        </div>
      </div>

      <!-- Mapa de zonas -->
      <div class="relative bg-gray-50 dark:bg-gray-900 rounded-lg p-2 sm:p-4 min-h-[300px] sm:min-h-[400px]">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-3">
          <div
            v-for="zona in zonas"
            :key="zona.id"
            @mouseenter="zonaHover = zona"
            @mouseleave="zonaHover = null"
            :class="[
              'p-2 sm:p-4 rounded-lg cursor-pointer transition-all duration-200 transform hover:scale-105 hover:shadow-lg min-w-0',
              getColorClass(zona.envios)
            ]"
            :style="{ backgroundColor: getColorIntensidad(zona.envios) }"
          >
            <h4 class="font-semibold text-xs sm:text-sm text-gray-900 dark:text-white mb-1 truncate" :title="zona.nombre">
              {{ zona.nombre }}
            </h4>
            <p class="text-xs text-gray-700 dark:text-gray-300 break-words">
              {{ zona.envios }} envíos
            </p>
            <p class="text-xs font-medium text-gray-900 dark:text-white mt-1 break-words">
              ${{ zona.ingresos.toLocaleString() }}
            </p>
          </div>
        </div>

        <!-- Tooltip flotante -->
        <div
          v-if="zonaHover"
          class="absolute bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 border border-gray-200 dark:border-gray-700 z-10"
          :style="{ left: tooltipPosition.x + 'px', top: tooltipPosition.y + 'px' }"
        >
          <h5 class="font-bold text-gray-900 dark:text-white mb-2">
            {{ zonaHover.nombre }}
          </h5>
          <div class="space-y-1 text-sm">
            <p class="text-gray-600 dark:text-gray-400">
              <span class="font-medium">Envíos:</span> {{ zonaHover.envios.toLocaleString() }}
            </p>
            <p class="text-gray-600 dark:text-gray-400">
              <span class="font-medium">Ingresos:</span> ${{ zonaHover.ingresos.toLocaleString() }}
            </p>
            <p class="text-gray-600 dark:text-gray-400">
              <span class="font-medium">Promedio:</span> ${{ Math.round(zonaHover.ingresos / zonaHover.envios).toLocaleString() }}
            </p>
            <p class="text-gray-600 dark:text-gray-400">
              <span class="font-medium">% del total:</span> {{ ((zonaHover.envios / totalEnvios) * 100).toFixed(1) }}%
            </p>
          </div>
        </div>
      </div>

      <!-- Lista de zonas ordenadas -->
      <div class="mt-6">
        <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-3">
          Zonas ordenadas por volumen
        </h4>
        <div class="space-y-2">
          <div
            v-for="(zona, index) in zonasOrdenadas"
            :key="zona.id"
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors gap-2 sm:gap-0"
          >
            <div class="flex items-center gap-3 min-w-0 flex-1">
              <span class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 w-6 flex-shrink-0">
                #{{ index + 1 }}
              </span>
              <span class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white truncate">
                {{ zona.nombre }}
              </span>
            </div>
            <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0">
              <span class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                {{ zona.envios }} envíos
              </span>
              <span class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                ${{ zona.ingresos.toLocaleString() }}
              </span>
            </div>
          </div>
        </div>
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
          Filtros de Zona
        </h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Provincia
            </label>
            <select
              v-model="filtroProvincia"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            >
              <option value="">Todas las provincias</option>
              <option value="Pataz">Pataz</option>
              <option value="Trujillo">Trujillo</option>
            </select>
          </div>
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
import { ref, computed, onMounted, watch } from 'vue'
import BaseWidget from './BaseWidget.vue'
import axios from 'axios'
import { exportarPDF as exportarPDFService, exportarExcel as exportarExcelService } from '../../services/exportService.js'
import MapIcon from '../icons/MapIcon.vue'

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

const loading = ref(false)
const zonas = ref([])
const zonaHover = ref(null)
const tooltipPosition = ref({ x: 0, y: 0 })
const mostrarFiltros = ref(false)
const filtroProvincia = ref('')
const fechaDesde = ref('')
const fechaHasta = ref('')

// Zonas de Pataz y Trujillo (La Libertad, Perú)
const zonasDefault = [
  // Pataz
  { id: 1, nombre: 'Tayabamba', provincia: 'Pataz', envios: 0, ingresos: 0 },
  { id: 2, nombre: 'Chilia', provincia: 'Pataz', envios: 0, ingresos: 0 },
  { id: 3, nombre: 'Huancaspata', provincia: 'Pataz', envios: 0, ingresos: 0 },
  { id: 4, nombre: 'Huaylillas', provincia: 'Pataz', envios: 0, ingresos: 0 },
  { id: 5, nombre: 'Huayo', provincia: 'Pataz', envios: 0, ingresos: 0 },
  { id: 6, nombre: 'Ongón', provincia: 'Pataz', envios: 0, ingresos: 0 },
  { id: 7, nombre: 'Parcoy', provincia: 'Pataz', envios: 0, ingresos: 0 },
  { id: 8, nombre: 'Pataz', provincia: 'Pataz', envios: 0, ingresos: 0 },
  { id: 9, nombre: 'Pías', provincia: 'Pataz', envios: 0, ingresos: 0 },
  { id: 10, nombre: 'Santiago de Challas', provincia: 'Pataz', envios: 0, ingresos: 0 },
  { id: 11, nombre: 'Taurija', provincia: 'Pataz', envios: 0, ingresos: 0 },
  { id: 12, nombre: 'Urpay', provincia: 'Pataz', envios: 0, ingresos: 0 },
  
  // Trujillo
  { id: 13, nombre: 'Trujillo', provincia: 'Trujillo', envios: 0, ingresos: 0 },
  { id: 14, nombre: 'El Porvenir', provincia: 'Trujillo', envios: 0, ingresos: 0 },
  { id: 15, nombre: 'La Esperanza', provincia: 'Trujillo', envios: 0, ingresos: 0 },
  { id: 16, nombre: 'Huanchaco', provincia: 'Trujillo', envios: 0, ingresos: 0 },
  { id: 17, nombre: 'Moche', provincia: 'Trujillo', envios: 0, ingresos: 0 },
  { id: 18, nombre: 'Salaverry', provincia: 'Trujillo', envios: 0, ingresos: 0 }
]

const coloresLeyenda = [
  'bg-yellow-200',
  'bg-yellow-300',
  'bg-orange-300',
  'bg-orange-400',
  'bg-red-400'
]

const totalEnvios = computed(() => {
  return zonas.value.reduce((sum, zona) => sum + zona.envios, 0)
})

const totalIngresos = computed(() => {
  return zonas.value.reduce((sum, zona) => sum + zona.ingresos, 0)
})

const zonasOrdenadas = computed(() => {
  return [...zonas.value].sort((a, b) => b.envios - a.envios)
})

const getColorIntensidad = (envios) => {
  if (totalEnvios.value === 0) return 'rgba(254, 240, 138, 0.5)' // amarillo claro
  
  const porcentaje = (envios / totalEnvios.value) * 100
  
  if (porcentaje >= 80) return 'rgba(239, 68, 68, 0.8)' // rojo
  if (porcentaje >= 60) return 'rgba(251, 146, 60, 0.8)' // naranja oscuro
  if (porcentaje >= 40) return 'rgba(251, 191, 36, 0.8)' // naranja
  if (porcentaje >= 20) return 'rgba(253, 224, 71, 0.8)' // amarillo
  return 'rgba(254, 240, 138, 0.6)' // amarillo claro
}

const getColorClass = (envios) => {
  if (totalEnvios.value === 0) return 'bg-yellow-100'
  
  const porcentaje = (envios / totalEnvios.value) * 100
  
  if (porcentaje >= 80) return 'bg-red-500 text-white'
  if (porcentaje >= 60) return 'bg-orange-500 text-white'
  if (porcentaje >= 40) return 'bg-yellow-400 text-gray-900'
  if (porcentaje >= 20) return 'bg-yellow-300 text-gray-900'
  return 'bg-yellow-200 text-gray-900'
}

const cargarDatos = async () => {
  loading.value = true
  try {
    const params = {}
    
    if (fechaDesde.value) params.fechaDesde = fechaDesde.value
    if (fechaHasta.value) params.fechaHasta = fechaHasta.value
    if (filtroProvincia.value) params.provincia = filtroProvincia.value
    
    const response = await axios.get('/reportes/mapa-calor-zonas', { params })
    const datosApi = response.data
    
    // Mapear datos de la API a las zonas predefinidas
    // Si una zona viene en la respuesta, usar sus datos, sino usar 0
    zonas.value = zonasDefault.map(zonaDefault => {
      const zonaApi = datosApi.find(z => z.nombre === zonaDefault.nombre)
      return {
        ...zonaDefault,
        envios: zonaApi ? zonaApi.envios : 0,
        ingresos: zonaApi ? zonaApi.ingresos : 0
      }
    })
    
    // Si no hay datos, usar array vacío
    if (!zonas.value || zonas.value.length === 0) {
      zonas.value = zonasDefault.map(z => ({ ...z, envios: 0, ingresos: 0 }))
    }
  } catch (error) {
    console.error('Error al cargar datos del mapa de calor:', error)
    zonas.value = zonasDefault.map(z => ({ ...z, envios: 0, ingresos: 0 }))
  } finally {
    loading.value = false
  }
}

const aplicarFiltros = () => {
  mostrarFiltros.value = false
  cargarDatos()
}

const exportarPDF = async () => {
  try {
    const elemento = document.querySelector('[data-widget="mapa-calor"]')
    if (elemento) {
      await exportarPDFService(elemento, `mapa-calor-zonas-${new Date().toISOString().split('T')[0]}`, 'Mapa de Calor de Zonas')
    }
  } catch (error) {
    console.error('Error al exportar PDF:', error)
  }
}

const exportarExcel = () => {
  try {
    const datosParaExportar = zonas.value.map(zona => ({
      Zona: zona.nombre,
      Provincia: zona.provincia,
      Envíos: zona.envios,
      Ingresos: zona.ingresos
    }))
    
    exportarExcelService(datosParaExportar, `mapa-calor-zonas-${new Date().toISOString().split('T')[0]}`, 'Mapa de Calor de Zonas')
  } catch (error) {
    console.error('Error al exportar Excel:', error)
  }
}

onMounted(() => {
  const hoy = new Date()
  const haceUnMes = new Date(hoy)
  haceUnMes.setMonth(haceUnMes.getMonth() - 1)
  
  // Si no hay props, usar valores por defecto
  fechaDesde.value = props.fechaDesde || haceUnMes.toISOString().split('T')[0]
  fechaHasta.value = props.fechaHasta || hoy.toISOString().split('T')[0]
  
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


