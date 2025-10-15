<template>
  <AdminLayout :key="$route.path">
    <div class="fletes-page">
      <!-- Header con título -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Gestión de Fletes y Envíos</h1>
        <p class="text-gray-600">Administra los fletes y grupos de encomiendas</p>
      </div>

      <!-- Barra de búsqueda y botón agregar -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <!-- Buscador -->
        <div class="flex-1 max-w-md">
          <div class="relative">
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Buscar fletes..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              @input="filterFletes"
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
          Crear Flete
        </button>
      </div>

      <!-- Indicador de carga -->
      <div v-if="loading" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Cargando fletes...</p>
      </div>

      <!-- Tabla de fletes -->
      <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origen</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destino</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Encomiendas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="flete in filteredFletes" :key="flete.id" class="hover:bg-gray-50">
                <!-- ID -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ flete.id }}</td>
                
                <!-- Código -->
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  <input
                    v-if="editingId === flete.id"
                    v-model="editingFlete.codigo"
                    type="text"
                    class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                  <span v-else>{{ flete.codigo }}</span>
                </td>
                
                <!-- Origen -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <select
                    v-if="editingId === flete.id"
                    v-model="editingFlete.sucursalOrigen"
                    class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar origen</option>
                    <option v-for="sucursal in sucursales" :key="sucursal.id" :value="sucursal.id">
                      {{ sucursal.nombre }}
                    </option>
                  </select>
                  <span v-else>{{ flete.sucursalOrigen?.nombre || 'N/A' }}</span>
                </td>
                
                <!-- Destino -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <select
                    v-if="editingId === flete.id"
                    v-model="editingFlete.sucursalDestino"
                    class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar destino</option>
                    <option v-for="sucursal in sucursales" :key="sucursal.id" :value="sucursal.id">
                      {{ sucursal.nombre }}
                    </option>
                  </select>
                  <span v-else>{{ flete.sucursalDestino?.nombre || 'N/A' }}</span>
                </td>
                
                <!-- Encomiendas -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ flete.totalEncomiendas }} encomiendas
                  </span>
                </td>
                
                <!-- Estado -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <select
                    v-if="editingId === flete.id"
                    v-model="editingFlete.estado"
                    class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="Programado">Programado</option>
                    <option value="En camino">En camino</option>
                    <option value="En destino">En destino</option>
                    <option value="Completado">Completado</option>
                    <option value="Cancelado">Cancelado</option>
                  </select>
                  <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                        :class="getEstadoClass(flete.estado)">
                    {{ flete.estado }}
                  </span>
                </td>
                
                <!-- Fecha Creación -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(flete.created_at) }}
                </td>
                
                <!-- Acciones -->
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div v-if="editingId === flete.id" class="flex items-center gap-2">
                    <button
                      @click="saveFlete(flete.id)"
                      class="text-green-600 hover:text-green-900 p-1 rounded"
                      title="Guardar"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      </svg>
                    </button>
                    <button
                      @click="cancelEdit"
                      class="text-gray-600 hover:text-gray-900 p-1 rounded"
                      title="Cancelar"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                  <div v-else class="flex items-center gap-2">
                    <button
                      @click="verDetalles(flete)"
                      class="text-blue-600 hover:text-blue-900 p-1 rounded"
                      title="Ver detalles"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                    <button
                      @click="editFlete(flete)"
                      class="text-green-600 hover:text-green-900 p-1 rounded"
                      title="Editar"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button
                      @click="deleteFlete(flete.id)"
                      class="text-red-600 hover:text-red-900 p-1 rounded"
                      title="Eliminar"
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
        <div v-if="filteredFletes.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No hay fletes</h3>
          <p class="mt-1 text-sm text-gray-500">Comienza creando un nuevo flete.</p>
        </div>
      </div>

      <!-- Modal para crear flete -->
      <div v-if="showAddModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Crear Nuevo Flete</h3>
            <form @submit.prevent="addFlete">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Código del Flete</label>
                  <input
                    v-model="newFlete.codigo"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal Origen</label>
                  <select
                    v-model="newFlete.sucursalOrigen"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar origen</option>
                    <option v-for="sucursal in sucursales" :key="sucursal.id" :value="sucursal.id">
                      {{ sucursal.nombre }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal Destino</label>
                  <select
                    v-model="newFlete.sucursalDestino"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar destino</option>
                    <option v-for="sucursal in sucursales" :key="sucursal.id" :value="sucursal.id">
                      {{ sucursal.nombre }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                  <select
                    v-model="newFlete.estado"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="Programado">Programado</option>
                    <option value="En camino">En camino</option>
                    <option value="En destino">En destino</option>
                    <option value="Completado">Completado</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                  <textarea
                    v-model="newFlete.descripcion"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  ></textarea>
                </div>
              </div>
              <div class="flex justify-end gap-3 mt-6">
                <button
                  type="button"
                  @click="showAddModal = false"
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-200"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors duration-200"
                >
                  Crear Flete
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Modal para ver detalles del flete -->
      <div v-if="showDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-900">Detalles del Flete: {{ selectedFlete?.codigo }}</h3>
              <button
                @click="showDetailsModal = false"
                class="text-gray-400 hover:text-gray-600"
              >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
            
            <div v-if="selectedFlete" class="space-y-6">
              <!-- Información general -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                  <h4 class="font-medium text-gray-800 mb-2">Información General</h4>
                  <div class="space-y-2 text-sm">
                    <div><span class="font-medium">Código:</span> {{ selectedFlete.codigo }}</div>
                    <div><span class="font-medium">Estado:</span> 
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                            :class="getEstadoClass(selectedFlete.estado)">
                        {{ selectedFlete.estado }}
                      </span>
                    </div>
                    <div><span class="font-medium">Fecha Creación:</span> {{ formatDate(selectedFlete.created_at) }}</div>
                  </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                  <h4 class="font-medium text-gray-800 mb-2">Ruta</h4>
                  <div class="space-y-2 text-sm">
                    <div><span class="font-medium">Origen:</span> {{ selectedFlete.sucursalOrigen?.nombre || 'N/A' }}</div>
                    <div><span class="font-medium">Destino:</span> {{ selectedFlete.sucursalDestino?.nombre || 'N/A' }}</div>
                  </div>
                </div>
              </div>
              
              <!-- Encomiendas del flete -->
              <div>
                <h4 class="font-medium text-gray-800 mb-4">Encomiendas Incluidas</h4>
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                  <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="encomienda in selectedFlete.encomiendas || []" :key="encomienda.id" class="hover:bg-gray-50">
                          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ encomienda.codigo }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ encomienda.cliente?.nombre || 'N/A' }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                  :class="getEstadoClass(encomienda.estado)">
                              {{ encomienda.estado }}
                            </span>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ${{ encomienda.valor?.toLocaleString() || '0' }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  
                  <div v-if="!selectedFlete.encomiendas || selectedFlete.encomiendas.length === 0" class="text-center py-8">
                    <p class="text-gray-500">No hay encomiendas asignadas a este flete.</p>
                  </div>
                </div>
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
  name: 'FletesPage',
  components: {
    AdminLayout
  },
  data() {
    return {
      fletes: [],
      sucursales: [],
      searchTerm: '',
      filteredFletes: [],
      editingId: null,
      editingFlete: {},
      showAddModal: false,
      showDetailsModal: false,
      selectedFlete: null,
      newFlete: {
        codigo: '',
        sucursalOrigen: '',
        sucursalDestino: '',
        estado: 'Programado',
        descripcion: ''
      },
      loading: false
    }
  },
  async mounted() {
    await this.loadFletes()
    await this.loadSucursales()
  },
  methods: {
    async loadFletes() {
      try {
        this.loading = true
        // Simular datos de fletes (backend pendiente)
        this.fletes = [
          {
            id: 1,
            codigo: 'FLT-001',
            sucursalOrigen: { id: 1, nombre: 'Sucursal Principal' },
            sucursalDestino: { id: 2, nombre: 'Sucursal Norte' },
            estado: 'En camino',
            totalEncomiendas: 5,
            created_at: '2025-10-09',
            encomiendas: [
              { id: 1, codigo: 'ENC-001', cliente: { nombre: 'Juan Pérez' }, estado: 'En tránsito', valor: 15000 },
              { id: 2, codigo: 'ENC-002', cliente: { nombre: 'María García' }, estado: 'En tránsito', valor: 12000 }
            ]
          },
          {
            id: 2,
            codigo: 'FLT-002',
            sucursalOrigen: { id: 2, nombre: 'Sucursal Norte' },
            sucursalDestino: { id: 3, nombre: 'Sucursal Sur' },
            estado: 'Completado',
            totalEncomiendas: 3,
            created_at: '2025-10-08',
            encomiendas: [
              { id: 3, codigo: 'ENC-003', cliente: { nombre: 'Carlos López' }, estado: 'Entregada', valor: 18000 }
            ]
          }
        ]
        this.filteredFletes = [...this.fletes]
      } catch (error) {
        console.error('Error al cargar fletes:', error)
        this.$toast?.error('Error al cargar fletes')
      } finally {
        this.loading = false
      }
    },
    
    async loadSucursales() {
      try {
        const response = await axios.get('/sucursales')
        this.sucursales = response.data
      } catch (error) {
        console.error('Error al cargar sucursales:', error)
        // Datos de ejemplo
        this.sucursales = [
          { id: 1, nombre: 'Sucursal Principal' },
          { id: 2, nombre: 'Sucursal Norte' },
          { id: 3, nombre: 'Sucursal Sur' }
        ]
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
        flete.sucursalOrigen?.nombre.toLowerCase().includes(term) ||
        flete.sucursalDestino?.nombre.toLowerCase().includes(term) ||
        flete.estado.toLowerCase().includes(term)
      )
    },
    
    editFlete(flete) {
      this.editingId = flete.id
      this.editingFlete = {
        codigo: flete.codigo,
        sucursalOrigen: flete.sucursalOrigen?.id || '',
        sucursalDestino: flete.sucursalDestino?.id || '',
        estado: flete.estado
      }
    },
    
    async saveFlete(id) {
      try {
        console.log('Guardando flete:', id, this.editingFlete)
        // Aquí se haría la llamada a la API
        
        // Actualizar el flete en la lista
        const index = this.fletes.findIndex(f => f.id === id)
        if (index !== -1) {
          const sucursalOrigen = this.sucursales.find(s => s.id == this.editingFlete.sucursalOrigen)
          const sucursalDestino = this.sucursales.find(s => s.id == this.editingFlete.sucursalDestino)
          
          this.fletes[index] = {
            ...this.fletes[index],
            codigo: this.editingFlete.codigo,
            sucursalOrigen,
            sucursalDestino,
            estado: this.editingFlete.estado
          }
        }
        
        // Actualizar también en filteredFletes
        const filteredIndex = this.filteredFletes.findIndex(f => f.id === id)
        if (filteredIndex !== -1) {
          this.filteredFletes[filteredIndex] = this.fletes[index]
        }
        
        this.editingId = null
        this.editingFlete = {}
        this.$toast?.success('Flete actualizado correctamente')
      } catch (error) {
        console.error('Error al actualizar flete:', error)
        this.$toast?.error('Error al actualizar flete')
      }
    },
    
    cancelEdit() {
      this.editingId = null
      this.editingFlete = {}
    },
    
    async deleteFlete(id) {
      if (!confirm('¿Estás seguro de que deseas eliminar este flete?')) {
        return
      }
      
      try {
        console.log('Eliminando flete:', id)
        // Aquí se haría la llamada a la API
        
        // Remover de la lista
        this.fletes = this.fletes.filter(f => f.id !== id)
        this.filteredFletes = this.filteredFletes.filter(f => f.id !== id)
        
        this.$toast?.success('Flete eliminado correctamente')
      } catch (error) {
        console.error('Error al eliminar flete:', error)
        this.$toast?.error('Error al eliminar flete')
      }
    },
    
    async addFlete() {
      try {
        console.log('Creando flete:', this.newFlete)
        // Aquí se haría la llamada a la API
        
        const sucursalOrigen = this.sucursales.find(s => s.id == this.newFlete.sucursalOrigen)
        const sucursalDestino = this.sucursales.find(s => s.id == this.newFlete.sucursalDestino)
        
        const newFlete = {
          id: Date.now(), // ID temporal
          codigo: this.newFlete.codigo,
          sucursalOrigen,
          sucursalDestino,
          estado: this.newFlete.estado,
          totalEncomiendas: 0,
          created_at: new Date().toISOString().split('T')[0],
          encomiendas: []
        }
        
        this.fletes.push(newFlete)
        this.filteredFletes.push(newFlete)
        
        this.showAddModal = false
        this.newFlete = {
          codigo: '',
          sucursalOrigen: '',
          sucursalDestino: '',
          estado: 'Programado',
          descripcion: ''
        }
        
        this.$toast?.success('Flete creado correctamente')
      } catch (error) {
        console.error('Error al crear flete:', error)
        this.$toast?.error('Error al crear flete')
      }
    },
    
    verDetalles(flete) {
      this.selectedFlete = flete
      this.showDetailsModal = true
    },
    
    getEstadoClass(estado) {
      const clases = {
        'Programado': 'bg-gray-100 text-gray-800',
        'En camino': 'bg-blue-100 text-blue-800',
        'En destino': 'bg-yellow-100 text-yellow-800',
        'Completado': 'bg-green-100 text-green-800',
        'Cancelado': 'bg-red-100 text-red-800',
        'Entregada': 'bg-green-100 text-green-800',
        'En tránsito': 'bg-blue-100 text-blue-800',
        'En origen': 'bg-yellow-100 text-yellow-800',
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
.fletes-page {
  padding: 1.5rem;
}

@media (max-width: 640px) {
  .fletes-page {
    padding: 1rem;
  }
  
  .overflow-x-auto {
    -webkit-overflow-scrolling: touch;
  }
  
  table {
    min-width: 600px;
  }
}
</style>
