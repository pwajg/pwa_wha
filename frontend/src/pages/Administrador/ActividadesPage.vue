<template>
  <AdminLayout :key="$route.path">
    <div class="actividades-page">
      <!-- Header con título y botón de exportación global -->
      <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="min-w-0 flex-1">
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-700 dark:text-white mb-2 break-words">
            Actividades de Usuarios
          </h1>
          <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 break-words">
            Registro de acciones realizadas por administradores y colaboradores
          </p>
        </div>
        
        <!-- Botón de exportación global unificado -->
        <div class="relative">
          <button
            @click="toggleExportMenu"
            @blur="closeExportMenu"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-lg flex items-center gap-2 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="exportando"
            aria-label="Exportar actividades"
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
              @click="exportarPDF"
              class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 active:bg-gray-200 dark:active:bg-gray-600"
              :disabled="exportando"
              aria-label="Exportar actividades como PDF"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
              </svg>
              Exportar como PDF
            </button>
            <button
              @click="exportarExcel"
              class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 active:bg-gray-200 dark:active:bg-gray-600"
              :disabled="exportando"
              aria-label="Exportar actividades como Excel"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Exportar como Excel
            </button>
          </div>
        </div>
      </div>

      <!-- Filtros y búsqueda -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
          <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-white">
            Filtros y Búsqueda
          </h3>
          <button
            @click="limpiarFiltros"
            class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 active:scale-95"
            aria-label="Limpiar filtros"
          >
            Limpiar Filtros
          </button>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-4">
          <!-- Búsqueda por texto -->
          <div class="sm:col-span-2 lg:col-span-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Búsqueda
            </label>
              <input
              v-model="filtros.busqueda"
              type="text"
              placeholder="Buscar por usuario, acción, módulo o descripción..."
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 transition-colors"
              @input="debounceBusqueda"
            />
          </div>
          
          <!-- Filtro por fecha desde -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Fecha Desde
            </label>
            <input
              v-model="filtros.fechaDesde"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 transition-colors"
              @change="aplicarFiltros"
            />
          </div>
          
          <!-- Filtro por fecha hasta -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Fecha Hasta
            </label>
            <input
              v-model="filtros.fechaHasta"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 transition-colors"
              @change="aplicarFiltros"
            />
          </div>
          
          <!-- Filtro por tipo de acción -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Tipo de Acción
            </label>
            <select
              v-model="filtros.tipoAccion"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 transition-colors"
              @change="aplicarFiltros"
            >
              <option value="">Todas las acciones</option>
              <option value="creacion">Creación</option>
              <option value="actualizacion">Actualización</option>
              <option value="eliminacion">Eliminación</option>
              <option value="login">Inicio de Sesión</option>
              <option value="logout">Cierre de Sesión</option>
              <option value="descarga">Descarga</option>
              <option value="exportacion">Exportación</option>
              <option value="visualizacion">Visualización</option>
            </select>
          </div>
          
          <!-- Filtro por usuario -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Usuario
            </label>
            <select
              v-model="filtros.usuarioId"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 transition-colors"
              @change="aplicarFiltros"
            >
              <option value="">Todos los usuarios</option>
              <option v-for="usuario in usuarios" :key="usuario.id" :value="usuario.id">
                {{ usuario.nombre }}
              </option>
            </select>
          </div>
          
          <!-- Filtro por rol -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Rol
            </label>
            <select
              v-model="filtros.rol"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 transition-colors"
              @change="aplicarFiltros"
            >
              <option value="">Todos los roles</option>
              <option value="Administrador">Administrador</option>
              <option value="Colaborador">Colaborador</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Tabla de actividades -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center py-12" role="status" aria-label="Cargando actividades">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 dark:border-blue-400"></div>
          <span class="sr-only">Cargando...</span>
        </div>
        
        <!-- Tabla -->
        <div v-else-if="actividadesFiltradas.length > 0" class="overflow-x-auto">
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-gray-50 dark:bg-gray-700/80 border-b-2 border-gray-200 dark:border-gray-600">
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Usuario
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Tipo de Acción
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Módulo
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Descripción
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                  Fecha y Hora
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr
                v-for="(actividad, index) in actividadesPaginas"
                :key="actividad.id"
                :class="[
                  index % 2 === 0 
                    ? 'bg-white dark:bg-gray-800' 
                    : 'bg-gray-50 dark:bg-gray-800/50',
                  'hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 ease-in-out cursor-pointer'
                ]"
              >
                <td class="px-4 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                      <span class="text-xs font-medium text-blue-600 dark:text-blue-300">
                        {{ actividad.usuario_nombre.charAt(0).toUpperCase() }}
                      </span>
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[150px]">
                        {{ actividad.usuario_nombre }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ actividad.usuario_rol }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <span
                    :class="getBadgeClass(actividad.tipo_accion)"
                    class="px-2 py-1 text-xs font-medium rounded-full"
                  >
                    {{ getTipoAccionLabel(actividad.tipo_accion) }}
                  </span>
                </td>
                <td class="px-4 py-4">
                  <div class="text-sm text-gray-900 dark:text-white truncate max-w-[120px]">
                    {{ actividad.modulo }}
                  </div>
                </td>
                <td class="px-4 py-4">
                  <div class="text-sm text-gray-900 dark:text-white truncate max-w-[300px]" :title="actividad.descripcion">
                    {{ actividad.descripcion }}
                  </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white">
                    {{ formatFecha(actividad.fecha_hora) }}
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ formatHora(actividad.fecha_hora) }}
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Empty State -->
        <div v-else class="text-center py-12" role="status" aria-label="Sin actividades">
          <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
          </svg>
          <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            {{ filtrosActivos ? 'No se encontraron actividades con los filtros aplicados' : 'No hay actividades registradas' }}
          </p>
        </div>
      </div>

      <!-- Paginación -->
      <div v-if="!loading && actividadesFiltradas.length > 0" class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="text-sm text-gray-700 dark:text-gray-300">
          Mostrando {{ ((paginaActual - 1) * porPagina) + 1 }} - {{ Math.min(paginaActual * porPagina, actividadesFiltradas.length) }} de {{ actividadesFiltradas.length }} actividades
        </div>
        <div class="flex gap-2">
          <button
            @click="paginaActual--"
            :disabled="paginaActual === 1"
            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
            aria-label="Página anterior"
          >
            Anterior
          </button>
          <button
            @click="paginaActual++"
            :disabled="paginaActual >= totalPaginas"
            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
            aria-label="Página siguiente"
          >
            Siguiente
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import AdminLayout from '../../components/AdminLayout.vue'
import {
  exportarPDF as exportarPDFService,
  exportarExcel as exportarExcelService
} from '../../services/exportService.js'
import axios from 'axios'

const loading = ref(false)
const exportando = ref(false)
const exportMenuOpen = ref(false)
const actividades = ref([])
const usuarios = ref([])
const paginaActual = ref(1)
const porPagina = ref(50)

const filtros = ref({
  busqueda: '',
  fechaDesde: '',
  fechaHasta: '',
  tipoAccion: '',
  usuarioId: '',
  rol: ''
})

const actividadesFiltradas = computed(() => {
  // Los filtros ya se aplican en el backend, solo devolvemos las actividades
  return actividades.value
})

const filtrosActivos = computed(() => {
  return !!(
    filtros.value.busqueda ||
    filtros.value.fechaDesde ||
    filtros.value.fechaHasta ||
    filtros.value.tipoAccion ||
    filtros.value.usuarioId ||
    filtros.value.rol
  )
})

const totalPaginas = computed(() => {
  return Math.ceil(actividadesFiltradas.value.length / porPagina.value)
})

const actividadesPaginas = computed(() => {
  const inicio = (paginaActual.value - 1) * porPagina.value
  const fin = inicio + porPagina.value
  return actividadesFiltradas.value.slice(inicio, fin)
})

const cargarActividades = async () => {
  loading.value = true
  try {
    const params = {}
    
    // Solo enviar parámetros que tienen valor
    if (filtros.value.busqueda) params.busqueda = filtros.value.busqueda
    if (filtros.value.fechaDesde) params.fechaDesde = filtros.value.fechaDesde
    if (filtros.value.fechaHasta) params.fechaHasta = filtros.value.fechaHasta
    if (filtros.value.tipoAccion) params.tipoAccion = filtros.value.tipoAccion
    if (filtros.value.usuarioId) params.idUsuario = filtros.value.usuarioId
    if (filtros.value.rol) params.rol = filtros.value.rol
    
    const response = await axios.get('/actividades', { params })
    
    // El backend devuelve { data: [...], total: ... }
    actividades.value = response.data.data || response.data.actividades || []
  } catch (error) {
    console.error('Error al cargar actividades:', error)
    // En caso de error, mostrar array vacío en lugar de datos de ejemplo
    actividades.value = []
  } finally {
    loading.value = false
  }
}

const cargarUsuarios = async () => {
  try {
    const response = await axios.get('/usuarios')
    usuarios.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Error al cargar usuarios:', error)
    // Datos de ejemplo para desarrollo
    usuarios.value = [
      { id: 1, nombre: 'Juan Pérez', rol: 'Administrador' },
      { id: 2, nombre: 'María García', rol: 'Colaborador' },
      { id: 3, nombre: 'Carlos López', rol: 'Administrador' }
    ]
  }
}


const aplicarFiltros = () => {
  paginaActual.value = 1
  // Recargar actividades desde el backend con los nuevos filtros
  cargarActividades()
}

// Debounce para la búsqueda de texto
let timeoutBusqueda = null
const debounceBusqueda = () => {
  clearTimeout(timeoutBusqueda)
  timeoutBusqueda = setTimeout(() => {
    aplicarFiltros()
  }, 500) // Esperar 500ms después de que el usuario deje de escribir
}

const limpiarFiltros = () => {
  // Restaurar fechas por defecto (último mes)
  const hoy = new Date()
  const haceUnMes = new Date(hoy)
  haceUnMes.setMonth(haceUnMes.getMonth() - 1)
  
  filtros.value = {
    busqueda: '',
    fechaDesde: haceUnMes.toISOString().split('T')[0],
    fechaHasta: hoy.toISOString().split('T')[0],
    tipoAccion: '',
    usuarioId: '',
    rol: ''
  }
  paginaActual.value = 1
  // Recargar actividades sin filtros adicionales (solo con fechas por defecto)
  cargarActividades()
}

const toggleExportMenu = () => {
  exportMenuOpen.value = !exportMenuOpen.value
}

const closeExportMenu = () => {
  setTimeout(() => {
    exportMenuOpen.value = false
  }, 200)
}

const exportarPDF = async () => {
  exportMenuOpen.value = false
  exportando.value = true
  try {
    const tabla = document.querySelector('.actividades-page table')
    if (tabla) {
      await exportarPDFService(
        tabla,
        `actividades-usuarios-${new Date().toISOString().split('T')[0]}`,
        'Actividades de Usuarios'
      )
    }
  } catch (error) {
    console.error('Error al exportar PDF:', error)
    alert('Error al exportar el reporte. Por favor, intente nuevamente.')
  } finally {
    exportando.value = false
  }
}

const exportarExcel = () => {
  exportMenuOpen.value = false
  exportando.value = true
  try {
    const datosParaExportar = actividadesFiltradas.value.map(actividad => ({
      Usuario: actividad.usuario_nombre,
      Rol: actividad.usuario_rol,
      'Tipo de Acción': getTipoAccionLabel(actividad.tipo_accion),
      Módulo: actividad.modulo,
      Descripción: actividad.descripcion,
      Fecha: formatFecha(actividad.fecha_hora),
      Hora: formatHora(actividad.fecha_hora)
    }))
    
    exportarExcelService(
      datosParaExportar,
      `actividades-usuarios-${new Date().toISOString().split('T')[0]}`,
      'Actividades de Usuarios'
    )
  } catch (error) {
    console.error('Error al exportar Excel:', error)
    alert('Error al exportar el reporte. Por favor, intente nuevamente.')
  } finally {
    exportando.value = false
  }
}

const getTipoAccionLabel = (tipo) => {
  const labels = {
    creacion: 'Creación',
    actualizacion: 'Actualización',
    eliminacion: 'Eliminación',
    login: 'Inicio de Sesión',
    logout: 'Cierre de Sesión',
    descarga: 'Descarga',
    exportacion: 'Exportación',
    visualizacion: 'Visualización',
    otra: 'Otra'
  }
  return labels[tipo] || tipo
}

const getBadgeClass = (tipo) => {
  const classes = {
    creacion: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    actualizacion: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    eliminacion: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    login: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    logout: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
    descarga: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    exportacion: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
    visualizacion: 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
    otra: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
  }
  return classes[tipo] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
}

const formatFecha = (fecha) => {
  return new Date(fecha).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  })
}

const formatHora = (fecha) => {
  return new Date(fecha).toLocaleTimeString('es-ES', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

watch(paginaActual, () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
})

onMounted(() => {
  // Establecer fechas por defecto (último mes) antes de cargar
  const hoy = new Date()
  const haceUnMes = new Date(hoy)
  haceUnMes.setMonth(haceUnMes.getMonth() - 1)
  
  filtros.value.fechaDesde = haceUnMes.toISOString().split('T')[0]
  filtros.value.fechaHasta = hoy.toISOString().split('T')[0]
  
  // Cargar datos
  cargarUsuarios()
  cargarActividades()
})
</script>

<style scoped>
.actividades-page {
  padding: 1.5rem;
}

@media (max-width: 640px) {
  .actividades-page {
    padding: 1rem;
  }
}
</style>

