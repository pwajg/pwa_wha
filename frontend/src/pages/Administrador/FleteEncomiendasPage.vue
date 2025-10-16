<template>
  <AdminLayout :key="$route.path">
    <div class="flete-encomiendas-page">
      <!-- Header con título -->
      <div class="mb-8">
        <div class="flex items-center gap-4 mb-2">
          <button
            @click="$router.go(-1)"
            class="text-gray-600 hover:text-gray-800 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200"
            title="Volver"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
          </button>
          <h1 class="text-3xl font-bold text-gray-800">
            {{ destinoFlete }}
          </h1>
        </div>
        <p class="text-gray-600">Encomiendas del flete {{ codigoFlete }}</p>
      </div>

      <!-- Barra de búsqueda y botón agregar -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <!-- Buscador -->
        <div class="flex-1 max-w-md">
          <div class="relative">
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Buscar encomiendas..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              @input="filterEncomiendas"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Botón agregar -->
        <button
          @click="showAddModal = true"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors duration-200"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Agregar Encomienda
        </button>
      </div>

      <!-- Indicador de carga -->
      <div v-if="loading" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Cargando encomiendas...</p>
      </div>

      <!-- Tabla de encomiendas -->
      <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente Remitente</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente Destinatario</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="encomienda in filteredEncomiendas" :key="encomienda.id" class="hover:bg-gray-50">
                <!-- Código -->
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ encomienda.codigo }}
                </td>
                
                <!-- Cliente Remitente -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ encomienda.clienteRemitente?.nombre || 'N/A' }}
                </td>
                
                <!-- Cliente Destinatario -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ encomienda.clienteDestinatario?.nombre || 'N/A' }}
                </td>
                
                <!-- Descripción -->
                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                  {{ encomienda.descripcion }}
                </td>
                
                <!-- Estado -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getEstadoClass(encomienda.estado)"
                  >
                    {{ encomienda.estado }}
                  </span>
                </td>
                
                <!-- Costo -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  S/ {{ encomienda.costo?.toFixed(2) || '0.00' }}
                </td>
                
                <!-- Fecha -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(encomienda.created_at) }}
                </td>
                
                <!-- Acciones -->
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center gap-2">
                    <button
                      @click="verDetalles(encomienda)"
                      class="text-blue-600 hover:text-blue-900 p-1 rounded"
                      title="Ver detalles"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                    <button
                      @click="editEncomienda(encomienda)"
                      class="text-green-600 hover:text-green-900 p-1 rounded"
                      title="Editar encomienda"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button
                      @click="removeFromFlete(encomienda.id)"
                      class="text-red-600 hover:text-red-900 p-1 rounded"
                      title="Quitar del flete"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Estado vacío -->
        <div v-if="filteredEncomiendas.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No hay encomiendas</h3>
          <p class="mt-1 text-sm text-gray-500">No se encontraron encomiendas en este flete.</p>
        </div>
      </div>

      <!-- Modal para agregar encomienda -->
      <div v-if="showAddModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-900">Agregar Encomienda al Flete</h3>
              <button
                @click="showAddModal = false"
                class="text-gray-400 hover:text-gray-600"
              >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Encomiendas Disponibles</label>
              <div class="relative">
                <input
                  v-model="searchEncomiendasDisponibles"
                  type="text"
                  placeholder="Buscar por código o cliente..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  @input="filterEncomiendasDisponibles"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Lista de encomiendas disponibles -->
            <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg">
              <div v-if="encomiendasDisponibles.length === 0" class="p-4 text-center text-gray-500">
                No hay encomiendas disponibles
              </div>
              <div
                v-for="encomienda in encomiendasDisponibles"
                :key="encomienda.id"
                class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer"
                @click="agregarEncomienda(encomienda)"
              >
                <div class="flex justify-between items-start">
                  <div>
                    <h4 class="font-medium text-gray-900">{{ encomienda.codigo }}</h4>
                    <p class="text-sm text-gray-600">{{ encomienda.descripcion }}</p>
                    <p class="text-xs text-gray-500">
                      {{ encomienda.clienteRemitente?.nombre }} → {{ encomienda.clienteDestinatario?.nombre }}
                    </p>
                  </div>
                  <div class="text-right">
                    <span
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                      :class="getEstadoClass(encomienda.estado)"
                    >
                      {{ encomienda.estado }}
                    </span>
                    <p class="text-sm font-medium text-gray-900 mt-1">S/ {{ encomienda.costo?.toFixed(2) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal para ver detalles de encomienda -->
      <div v-if="showDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-900">Detalles de la Encomienda</h3>
              <button
                @click="showDetailsModal = false"
                class="text-gray-400 hover:text-gray-600"
              >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
            
            <div v-if="selectedEncomienda" class="space-y-6">
              <!-- Información general -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                  <h4 class="font-medium text-gray-800 mb-2">Información General</h4>
                  <div class="space-y-2 text-sm">
                    <div><span class="font-medium">Código:</span> {{ selectedEncomienda.codigo }}</div>
                    <div><span class="font-medium">Estado:</span> 
                      <span
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                        :class="getEstadoClass(selectedEncomienda.estado)"
                      >
                        {{ selectedEncomienda.estado }}
                      </span>
                    </div>
                    <div><span class="font-medium">Costo:</span> S/ {{ selectedEncomienda.costo?.toFixed(2) }}</div>
                    <div><span class="font-medium">Estado Pago:</span> {{ selectedEncomienda.estadoPago }}</div>
                  </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                  <h4 class="font-medium text-gray-800 mb-2">Clientes</h4>
                  <div class="space-y-2 text-sm">
                    <div><span class="font-medium">Remitente:</span> {{ selectedEncomienda.clienteRemitente?.nombre || 'N/A' }}</div>
                    <div><span class="font-medium">Destinatario:</span> {{ selectedEncomienda.clienteDestinatario?.nombre || 'N/A' }}</div>
                  </div>
                </div>
              </div>
              
              <!-- Descripción y observaciones -->
              <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="font-medium text-gray-800 mb-2">Descripción</h4>
                <p class="text-sm text-gray-700">{{ selectedEncomienda.descripcion }}</p>
                
                <h4 class="font-medium text-gray-800 mb-2 mt-4">Observaciones</h4>
                <p class="text-sm text-gray-700">{{ selectedEncomienda.observaciones || 'Sin observaciones' }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '../../components/AdminLayout.vue'
import axios from 'axios'

export default {
  name: 'FleteEncomiendasPage',
  components: {
    AdminLayout
  },
  data() {
    return {
      fleteId: null,
      codigoFlete: '',
      destinoFlete: '',
      encomiendas: [],
      encomiendasDisponibles: [],
      searchTerm: '',
      searchEncomiendasDisponibles: '',
      filteredEncomiendas: [],
      showAddModal: false,
      showDetailsModal: false,
      selectedEncomienda: null,
      loading: false
    }
  },
  async mounted() {
    this.fleteId = this.$route.params.fleteId
    this.codigoFlete = this.$route.query.codigo || 'N/A'
    this.destinoFlete = this.$route.query.destino || 'Sin destino'
    
    await this.loadEncomiendas()
    await this.loadEncomiendasDisponibles()
  },
  methods: {
    async loadEncomiendas() {
      try {
        this.loading = true
        // Simular datos de encomiendas del flete basados en los seeders
        this.encomiendas = [
          {
            id: 1,
            codigo: 'ENC-251008-001',
            descripcion: 'Paquete pequeño con documentos',
            costo: 25.50,
            estadoPago: 'Pagado',
            observaciones: 'Fragil - Manejar con cuidado',
            estado: 'En origen',
            clienteRemitente: { nombre: 'Juan Carlos Pérez García' },
            clienteDestinatario: { nombre: 'María Elena Rodríguez López' },
            created_at: '2025-10-08'
          },
          {
            id: 2,
            codigo: 'ENC-251008-004',
            descripcion: 'Paquete grande con ropa',
            costo: 35.75,
            estadoPago: 'Pagado',
            observaciones: 'Peso: 8 kg',
            estado: 'En origen',
            clienteRemitente: { nombre: 'Carlos Alberto Mendoza Silva' },
            clienteDestinatario: { nombre: 'Comercial Los Andes S.A.C.' },
            created_at: '2025-10-08'
          }
        ]
        this.filteredEncomiendas = [...this.encomiendas]
      } catch (error) {
        console.error('Error al cargar encomiendas:', error)
        this.$toast?.error('Error al cargar encomiendas')
      } finally {
        this.loading = false
      }
    },
    
    async loadEncomiendasDisponibles() {
      try {
        // Simular encomiendas disponibles que no están en este flete
        this.encomiendasDisponibles = [
          {
            id: 3,
            codigo: 'ENC-251008-002',
            descripcion: 'Caja mediana con productos electrónicos',
            costo: 45.00,
            estadoPago: 'Parcial',
            observaciones: 'Valor declarado: S/ 500',
            estado: 'En origen',
            clienteRemitente: { nombre: 'Comercial Los Andes S.A.C.' },
            clienteDestinatario: { nombre: 'Carlos Alberto Mendoza Silva' },
            created_at: '2025-10-08'
          },
          {
            id: 4,
            codigo: 'ENC-251008-003',
            descripcion: 'Sobre con documentos legales',
            costo: 15.00,
            estadoPago: 'Pendiente',
            observaciones: 'Urgente - Entregar en 24 horas',
            estado: 'En origen',
            clienteRemitente: { nombre: 'María Elena Rodríguez López' },
            clienteDestinatario: { nombre: 'Juan Carlos Pérez García' },
            created_at: '2025-10-08'
          }
        ]
      } catch (error) {
        console.error('Error al cargar encomiendas disponibles:', error)
      }
    },
    
    filterEncomiendas() {
      if (!this.searchTerm) {
        this.filteredEncomiendas = [...this.encomiendas]
        return
      }
      
      const term = this.searchTerm.toLowerCase()
      this.filteredEncomiendas = this.encomiendas.filter(encomienda => 
        encomienda.codigo.toLowerCase().includes(term) ||
        encomienda.clienteRemitente?.nombre.toLowerCase().includes(term) ||
        encomienda.clienteDestinatario?.nombre.toLowerCase().includes(term) ||
        encomienda.descripcion.toLowerCase().includes(term) ||
        encomienda.estado.toLowerCase().includes(term)
      )
    },
    
    filterEncomiendasDisponibles() {
      // Esta función se puede implementar si se necesita filtrar las encomiendas disponibles
      // Por ahora mantenemos todas las disponibles
    },
    
    agregarEncomienda(encomienda) {
      // Agregar la encomienda al flete
      this.encomiendas.push(encomienda)
      this.filteredEncomiendas.push(encomienda)
      
      // Remover de disponibles
      this.encomiendasDisponibles = this.encomiendasDisponibles.filter(e => e.id !== encomienda.id)
      
      this.showAddModal = false
      this.searchEncomiendasDisponibles = ''
      this.$toast?.success('Encomienda agregada al flete')
    },
    
    removeFromFlete(encomiendaId) {
      if (!confirm('¿Estás seguro de que deseas quitar esta encomienda del flete?')) {
        return
      }
      
      const encomienda = this.encomiendas.find(e => e.id === encomiendaId)
      if (encomienda) {
        // Remover del flete
        this.encomiendas = this.encomiendas.filter(e => e.id !== encomiendaId)
        this.filteredEncomiendas = this.filteredEncomiendas.filter(e => e.id !== encomiendaId)
        
        // Agregar a disponibles
        this.encomiendasDisponibles.push(encomienda)
        
        this.$toast?.success('Encomienda removida del flete')
      }
    },
    
    verDetalles(encomienda) {
      this.selectedEncomienda = encomienda
      this.showDetailsModal = true
    },
    
    editEncomienda(encomienda) {
      // Implementar edición de encomienda
      console.log('Editar encomienda:', encomienda)
      this.$toast?.info('Funcionalidad de edición pendiente')
    },
    
    getEstadoClass(estado) {
      const clases = {
        'Registrado': 'bg-gray-100 text-gray-800',
        'En origen': 'bg-yellow-100 text-yellow-800',
        'En tránsito': 'bg-blue-100 text-blue-800',
        'En destino': 'bg-green-100 text-green-800',
        'Entregada': 'bg-green-100 text-green-800',
        'Retenida': 'bg-red-100 text-red-800',
        'Devuelta': 'bg-gray-100 text-gray-800'
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
.flete-encomiendas-page {
  padding: 1.5rem;
}

@media (max-width: 640px) {
  .flete-encomiendas-page {
    padding: 1rem;
  }
  
  .overflow-x-auto {
    -webkit-overflow-scrolling: touch;
  }
  
  table {
    min-width: 800px;
  }
}
</style>
