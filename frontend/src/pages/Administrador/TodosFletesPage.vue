<template>
  <AdminLayout :key="$route.path">
    <div class="fletes-page">
      <!-- Header con título -->
      <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-700 dark:text-white mb-2">
          Todos los Fletes
        </h1>
        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">Historial completo de fletes registrados en el sistema</p>
      </div>

      <!-- Filtros de fecha -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6">
        <h2 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Filtrar por Fecha</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Año</label>
            <select
              v-model="filtroAnio"
              @change="aplicarFiltroFecha"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
            >
              <option value="">Todos los años</option>
              <option v-for="anio in añosDisponibles" :key="anio" :value="anio">{{ anio }}</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Mes</label>
            <select
              v-model="filtroMes"
              @change="aplicarFiltroFecha"
              :disabled="!filtroAnio"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base disabled:bg-gray-100 disabled:cursor-not-allowed"
            >
              <option value="">Todos los meses</option>
              <option v-for="(mes, index) in meses" :key="index" :value="index + 1">
                {{ mes }}
              </option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Día</label>
            <select
              v-model="filtroDia"
              @change="aplicarFiltroFecha"
              :disabled="!filtroAnio || !filtroMes"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base disabled:bg-gray-100 disabled:cursor-not-allowed"
            >
              <option value="">Todos los días</option>
              <option v-for="dia in diasDisponibles" :key="dia" :value="dia">{{ dia }}</option>
            </select>
          </div>
        </div>
        
        <div class="mt-4 flex flex-col sm:flex-row flex-wrap gap-2 sm:gap-3">
          <button
            @click="limpiarFiltros"
            class="w-full sm:w-auto px-3 sm:px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200"
          >
            Limpiar Filtros
          </button>
          <button
            @click="vistaActual = vistaActual === 'box' ? 'tabla' : 'box'"
            class="w-full sm:w-auto px-3 sm:px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center justify-center sm:justify-start gap-2"
          >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="vistaActual === 'box'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0V4a1 1 0 011-1h16a1 1 0 011 1v16a1 1 0 01-1 1H4a1 1 0 01-1-1z"></path>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span>{{ vistaActual === 'box' ? 'Vista Tabla' : 'Vista Cajas' }}</span>
          </button>
        </div>
      </div>

      <!-- Barra de búsqueda -->
      <div class="mb-6">
        <div class="relative max-w-md">
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Buscar por código, origen o destino..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
            @input="filterFletes"
          />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- Indicador de carga -->
      <div v-if="loading" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Cargando fletes...</p>
      </div>

      <!-- Mensaje cuando no hay fletes con los filtros aplicados -->
      <div v-else-if="filteredFletes.length === 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 sm:p-12 text-center">
        <svg class="mx-auto h-12 w-12 sm:h-16 sm:w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        <h3 class="text-lg sm:text-xl font-medium text-gray-900 mb-2">
          {{ mensajeSinFletes.titulo }}
        </h3>
        <p class="text-sm sm:text-base text-gray-500">
          {{ mensajeSinFletes.mensaje }}
        </p>
      </div>

      <!-- Vista Box (Grid de cajas) -->
      <div v-else-if="vistaActual === 'box'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <div
          v-for="flete in filteredFletes"
          :key="flete.idFlete || flete.id"
          @click="verEncomiendas(flete)"
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 cursor-pointer hover:shadow-md transition-shadow duration-200"
        >
          <!-- Header del card -->
          <div class="flex justify-between items-start mb-4">
            <div>
              <h3 class="text-base sm:text-lg font-semibold text-gray-800">{{ flete.sucursalDestino?.nombre || 'Sin destino' }}</h3>
              <p class="text-xs sm:text-sm text-gray-500">{{ flete.codigo }}</p>
            </div>
            <span
              class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
              :class="getEstadoClass(flete.estado)"
            >
              {{ flete.estado }}
            </span>
          </div>

          <!-- Información del flete -->
          <div class="space-y-2 mb-4">
            <div class="flex items-center text-xs sm:text-sm text-gray-600">
              <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              <span class="truncate">{{ flete.sucursalOrigen?.nombre || 'Sin origen' }} → {{ flete.sucursalDestino?.nombre || 'Sin destino' }}</span>
            </div>
            
            <div class="flex items-center text-xs sm:text-sm text-gray-600">
              <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
              <span>{{ flete.totalEncomiendas }} encomiendas</span>
            </div>
            
            <div class="flex items-center text-xs sm:text-sm text-gray-600">
              <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <span>{{ formatDate(flete.created_at) }}</span>
            </div>
          </div>

          <!-- Botones de acción -->
          <div class="flex justify-between items-center pt-3 border-t border-gray-100">
            <div class="flex items-center gap-2">
              <button
                @click.stop="verEncomiendas(flete)"
                class="text-blue-600 hover:text-blue-800 p-1 rounded transition-colors"
                title="Ver encomiendas"
              >
                <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Vista Tabla -->
      <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origen</th>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destino</th>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Encomiendas</th>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="flete in filteredFletes" :key="flete.idFlete || flete.id" class="hover:bg-gray-50">
                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ flete.codigo }}
                </td>
                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ flete.sucursalOrigen?.nombre || 'N/A' }}
                </td>
                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ flete.sucursalDestino?.nombre || 'N/A' }}
                </td>
                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                  <span
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getEstadoClass(flete.estado)"
                  >
                    {{ flete.estado }}
                  </span>
                </td>
                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ flete.totalEncomiendas }} encomiendas
                  </span>
                </td>
                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(flete.created_at) }}
                </td>
                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button
                    @click="verEncomiendas(flete)"
                    class="text-blue-600 hover:text-blue-900 p-1 rounded transition-colors"
                    title="Ver encomiendas"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '../../components/AdminLayout.vue'
import axios from 'axios'

export default {
  name: 'TodosFletesPage',
  components: {
    AdminLayout
  },
  data() {
    return {
      fletes: [],
      searchTerm: '',
      filteredFletes: [],
      vistaActual: 'box',
      loading: false,
      filtroAnio: '',
      filtroMes: '',
      filtroDia: '',
      meses: [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
      ]
    }
  },
  computed: {
    añosDisponibles() {
      const años = new Set()
      // Incluir años de los fletes existentes
      this.fletes.forEach(flete => {
        if (flete.created_at) {
          const año = new Date(flete.created_at).getFullYear()
          años.add(año)
        }
      })
      // Incluir año actual si no está en la lista
      const añoActual = new Date().getFullYear()
      años.add(añoActual)
      // Incluir algunos años próximos y pasados para facilitar la navegación
      for (let i = añoActual - 2; i <= añoActual + 1; i++) {
        años.add(i)
      }
      return Array.from(años).sort((a, b) => b - a) // De más reciente a más antiguo
    },
    
    diasDisponibles() {
      if (!this.filtroAnio || !this.filtroMes) return []
      
      const año = parseInt(this.filtroAnio)
      const mes = parseInt(this.filtroMes)
      const diasEnMes = new Date(año, mes, 0).getDate()
      return Array.from({ length: diasEnMes }, (_, i) => i + 1)
    },
    
    mensajeSinFletes() {
      if (this.filtroAnio || this.filtroMes || this.filtroDia) {
        let fecha = ''
        if (this.filtroDia && this.filtroMes && this.filtroAnio) {
          const mesNombre = this.meses[parseInt(this.filtroMes) - 1]
          fecha = `${this.filtroDia} de ${mesNombre} de ${this.filtroAnio}`
        } else if (this.filtroMes && this.filtroAnio) {
          const mesNombre = this.meses[parseInt(this.filtroMes) - 1]
          fecha = `${mesNombre} de ${this.filtroAnio}`
        } else if (this.filtroAnio) {
          fecha = `año ${this.filtroAnio}`
        }
        
        return {
          titulo: 'No hay fletes registrados',
          mensaje: `No se encontraron fletes registrados para la fecha seleccionada: ${fecha}. Intenta con otra fecha o limpia los filtros para ver todos los fletes.`
        }
      }
      
      return {
        titulo: 'No hay fletes registrados',
        mensaje: 'No se encontraron fletes en el sistema. Los fletes se crearán automáticamente o pueden ser creados manualmente.'
      }
    }
  },
  async mounted() {
    await this.loadFletes()
  },
  methods: {
    async loadFletes() {
      try {
        this.loading = true
        await this.cargarFletesConFiltros()
      } catch (error) {
        console.error('Error al cargar fletes:', error)
        if (error.response && error.response.data && error.response.data.message) {
          this.$toast?.error(error.response.data.message)
        } else {
          this.$toast?.error('Error al cargar fletes')
        }
        this.fletes = []
        this.filteredFletes = []
      } finally {
        this.loading = false
      }
    },
    
    async cargarFletesConFiltros() {
      try {
        // Construir parámetros solo si hay filtros aplicados
        const params = {}
        if (this.filtroAnio) params.anio = this.filtroAnio
        if (this.filtroMes) params.mes = this.filtroMes
        if (this.filtroDia) params.dia = this.filtroDia
        
        // Si no hay filtros, cargar todos los fletes sin parámetros
        const response = await axios.get('/fletes/mi-sucursal', Object.keys(params).length > 0 ? { params } : {})
        
        console.log('Respuesta de la API:', response.data)
        
        if (response.data && response.data.data) {
          this.fletes = response.data.data
          console.log('Fletes cargados:', this.fletes.length)
          
          // Aplicar filtro de búsqueda en el frontend
          if (this.searchTerm) {
            const term = this.searchTerm.toLowerCase()
            this.filteredFletes = this.fletes.filter(flete => 
              flete.codigo.toLowerCase().includes(term) ||
              (flete.sucursalOrigen?.nombre || flete.SucursalOrigen?.nombre || '').toLowerCase().includes(term) ||
              (flete.sucursalDestino?.nombre || flete.SucursalDestino?.nombre || '').toLowerCase().includes(term) ||
              (flete.estado || '').toLowerCase().includes(term)
            )
          } else {
            this.filteredFletes = [...this.fletes]
          }
        } else {
          console.warn('No se recibieron datos en la respuesta')
          this.fletes = []
          this.filteredFletes = []
        }
      } catch (error) {
        console.error('Error en cargarFletesConFiltros:', error)
        if (error.response) {
          console.error('Respuesta de error:', error.response.data)
          console.error('Status:', error.response.status)
          console.error('Headers:', error.response.headers)
        } else if (error.request) {
          console.error('No se recibió respuesta del servidor:', error.request)
        } else {
          console.error('Error configurando la petición:', error.message)
        }
        throw error
      }
    },
    
    aplicarFiltroFecha() {
      this.loadFletes()
    },
    
    filterFletes() {
      // Solo filtrar por búsqueda en el frontend si ya hay fletes cargados
      if (this.fletes.length > 0) {
        if (this.searchTerm) {
          const term = this.searchTerm.toLowerCase()
          this.filteredFletes = this.fletes.filter(flete => 
            flete.codigo.toLowerCase().includes(term) ||
            (flete.sucursalOrigen?.nombre || flete.SucursalOrigen?.nombre || '').toLowerCase().includes(term) ||
            (flete.sucursalDestino?.nombre || flete.SucursalDestino?.nombre || '').toLowerCase().includes(term) ||
            (flete.estado || '').toLowerCase().includes(term)
          )
        } else {
          this.filteredFletes = [...this.fletes]
        }
      }
    },
    
    limpiarFiltros() {
      this.filtroAnio = ''
      this.filtroMes = ''
      this.filtroDia = ''
      this.searchTerm = ''
      this.loadFletes()
    },
    
    verEncomiendas(flete) {
      this.$router.push({
        name: 'FleteEncomiendas',
        params: { fleteId: flete.idFlete || flete.id },
        query: { 
          codigo: flete.codigo,
          destino: flete.sucursalDestino?.nombre || flete.SucursalDestino?.nombre || 'Sin destino'
        }
      })
    },
    
    getEstadoClass(estado) {
      const clases = {
        'Registrado': 'bg-gray-100 text-gray-800',
        'Enviado': 'bg-indigo-100 text-indigo-800',
        'En origen': 'bg-yellow-100 text-yellow-800',
        'En tránsito': 'bg-blue-100 text-blue-800',
        'En destino': 'bg-green-100 text-green-800',
        'De vuelta': 'bg-purple-100 text-purple-800',
        'Sin estado': 'bg-gray-100 text-gray-500'
      }
      return clases[estado] || 'bg-gray-100 text-gray-800'
    },
    
    formatDate(dateString) {
      if (!dateString) return '-'
      const date = new Date(dateString)
      return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      })
    }
  }
}


</script>

<style scoped>
.fletes-page {
  padding: 1rem 1.5rem;
}

@media (max-width: 640px) {
  .fletes-page {
    padding: 0.75rem 1rem;
  }
  
  .overflow-x-auto {
    -webkit-overflow-scrolling: touch;
  }
  
  table {
    min-width: 700px;
  }
}

.grid > div {
  transition: transform 0.2s ease-in-out;
}

.grid > div:hover {
  transform: translateY(-2px);
}

.fletes-page {

padding: 1rem 1.5rem;

}



@media (max-width: 640px) {

.fletes-page {

  padding: 0.75rem 1rem;

}



.overflow-x-auto {

  -webkit-overflow-scrolling: touch;

}



table {

  min-width: 700px;

}

}



.grid > div {

transition: transform 0.2s ease-in-out;

}



.grid > div:hover {

transform: translateY(-2px);

}

</style>





