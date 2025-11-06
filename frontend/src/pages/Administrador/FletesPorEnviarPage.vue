<template>
  <AdminLayout :key="$route.path">
    <div class="fletes-page">
      <!-- Header con título -->
      <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-700 dark:text-white mb-2">
          Fletes por Enviar (Hoy)
        </h1>
        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300">Fletes registrados que pueden enviarse hoy</p>
      </div>

      <!-- Indicador de carga -->
      <div v-if="loading" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Cargando fletes...</p>
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
              <h3 class="text-base sm:text-lg font-semibold text-gray-800">{{ flete.sucursalDestino?.nombre || flete.SucursalDestino?.nombre || 'Sin destino' }}</h3>
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
              <span class="truncate">{{ flete.sucursalOrigen?.nombre || flete.SucursalOrigen?.nombre || 'Sin origen' }} → {{ flete.sucursalDestino?.nombre || 'Sin destino' }}</span>
            </div>
            
            <div class="flex items-center text-xs sm:text-sm text-gray-600">
              <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
              <span>{{ flete.totalEncomiendas }} encomiendas</span>
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
            <span class="text-xs text-gray-400">{{ formatDate(flete.created_at) }}</span>
          </div>
        </div>

        <!-- Estado vacío -->
        <div v-if="filteredFletes.length === 0" class="col-span-full text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-700 dark:text-white">No hay fletes por enviar</h3>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">No se encontraron fletes registrados para enviar hoy.</p>
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
                  {{ flete.sucursalOrigen?.nombre || flete.SucursalOrigen?.nombre || 'N/A' }}
                </td>
                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ flete.sucursalDestino?.nombre || flete.SucursalDestino?.nombre || 'N/A' }}
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
        
        <!-- Estado vacío para vista tabla -->
        <div v-if="filteredFletes.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-700 dark:text-white">No hay fletes por enviar</h3>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">No se encontraron fletes registrados para enviar hoy.</p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '../../components/AdminLayout.vue'
import axios from 'axios'

export default {
  name: 'FletesPorEnviarPage',
  components: {
    AdminLayout
  },
  data() {
    return {
      fletes: [],
      searchTerm: '',
      filteredFletes: [],
      vistaActual: 'box',
      loading: false
    }
  },
  async mounted() {
    await this.loadFletes()
  },
  methods: {
    async loadFletes() {
      try {
        this.loading = true
        // Obtener fletes con filtros: estado "Registrado" y fecha de hoy
        const response = await axios.get('/fletes/por-enviar')
        if (response.data && response.data.data) {
          this.fletes = response.data.data
          this.filteredFletes = [...this.fletes]
        } else {
          this.fletes = []
          this.filteredFletes = []
        }
      } catch (error) {
        console.error('Error al cargar fletes:', error)
        if (error.response && error.response.data && error.response.data.message) {
          this.$toast?.error(error.response.data.message)
        } else {
          this.$toast?.error('Error al cargar fletes por enviar')
        }
        this.fletes = []
        this.filteredFletes = []
      } finally {
        this.loading = false
      }
    },
    
    filterFletes() {
      if (!this.searchTerm) {
        this.filteredFletes = [...this.fletes]
        return
      }
      
      const term = this.searchTerm.toLowerCase()
      this.filteredFletes = this.fletes.filter(flete => 
        flete.codigo.toLowerCase().includes(term) ||
        (flete.sucursalOrigen?.nombre || flete.SucursalOrigen?.nombre || '').toLowerCase().includes(term) ||
        (flete.sucursalDestino?.nombre || flete.sucursalDestino?.nombre || '').toLowerCase().includes(term)
      )
    },
    
    verEncomiendas(flete) {
      this.$router.push({
        name: 'FleteEncomiendas',
        params: { fleteId: flete.idFlete || flete.id },
        query: { 
          codigo: flete.codigo,
          destino: flete.sucursalDestino?.nombre || flete.SucursalDestino?.nombre ||'Sin destino'
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
</style>

