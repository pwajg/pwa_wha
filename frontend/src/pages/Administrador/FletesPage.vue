<template>
  <AdminLayout :key="$route.path">
    <div class="fletes-page">
      <!-- Header con título dinámico -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
          Fletes de {{ fechaActual }}
        </h1>
        <p class="text-gray-600">Administra los fletes y grupos de encomiendas</p>
      </div>

      <!-- Controles de vista -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <!-- Botones de alternancia de vista -->
        <div class="flex items-center gap-2">
          <button
            @click="vistaActual = 'box'"
            :class="[
              'p-2 rounded-lg transition-colors duration-200',
              vistaActual === 'box' 
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
            ]"
            title="Vista de cajas"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </button>
          <button
            @click="vistaActual = 'tabla'"
            :class="[
              'p-2 rounded-lg transition-colors duration-200',
              vistaActual === 'tabla' 
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
            ]"
            title="Vista de tabla"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0V4a1 1 0 011-1h16a1 1 0 011 1v16a1 1 0 01-1 1H4a1 1 0 01-1-1z"></path>
            </svg>
          </button>
        </div>

        <!-- Barra de búsqueda y filtros -->
        <div class="flex flex-col sm:flex-row gap-4 flex-1 max-w-md">
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
          
          <!-- Filtro por estado -->
          <select
            v-model="filtroEstado"
            @change="filterFletes"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">Todos los estados</option>
            <option value="En origen">En origen</option>
            <option value="En tránsito">En tránsito</option>
            <option value="En destino">En destino</option>
            <option value="De vuelta">De vuelta</option>
          </select>
        </div>
      </div>

      <!-- Indicador de carga -->
      <div v-if="loading" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Cargando fletes...</p>
      </div>

      <!-- Vista Box (Grid de cajas) -->
      <div v-else-if="vistaActual === 'box'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="flete in filteredFletes"
          :key="flete.id"
          @click="verEncomiendas(flete)"
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 cursor-pointer hover:shadow-md transition-shadow duration-200"
        >
          <!-- Header del card -->
          <div class="flex justify-between items-start mb-4">
            <div>
              <h3 class="text-lg font-semibold text-gray-800">{{ flete.sucursalDestino?.nombre || 'Sin destino' }}</h3>
              <p class="text-sm text-gray-500">{{ flete.codigo }}</p>
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
            <div class="flex items-center text-sm text-gray-600">
              <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              <span>{{ flete.sucursalOrigen?.nombre || 'Sin origen' }} → {{ flete.sucursalDestino?.nombre || 'Sin destino' }}</span>
            </div>
            
            <div class="flex items-center text-sm text-gray-600">
              <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
              <span>{{ flete.totalEncomiendas }} encomiendas</span>
            </div>
          </div>

          <!-- Botones de acción -->
          <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
              <button
                @click.stop="verEncomiendas(flete)"
                class="text-blue-600 hover:text-blue-800 p-1 rounded"
                title="Ver encomiendas"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
              </button>
              <button
                @click.stop="editFlete(flete)"
                class="text-green-600 hover:text-green-800 p-1 rounded"
                title="Editar flete"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              <button
                @click.stop="deleteFlete(flete.id)"
                class="text-red-600 hover:text-red-800 p-1 rounded"
                title="Eliminar flete"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
            <span class="text-xs text-gray-400">{{ formatDate(flete.created_at) }}</span>
          </div>
        </div>

        <!-- Estado vacío para vista box -->
        <div v-if="filteredFletes.length === 0" class="col-span-full text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No hay fletes</h3>
          <p class="mt-1 text-sm text-gray-500">No se encontraron fletes con los criterios de búsqueda.</p>
        </div>
      </div>

      <!-- Vista Tabla -->
      <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origen</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destino</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Encomiendas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="flete in filteredFletes" :key="flete.id" class="hover:bg-gray-50">
                <!-- Código -->
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ flete.codigo }}
                </td>
                
                <!-- Origen -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ flete.sucursalOrigen?.nombre || 'N/A' }}
                </td>
                
                <!-- Destino -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ flete.sucursalDestino?.nombre || 'N/A' }}
                </td>
                
                <!-- Estado -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getEstadoClass(flete.estado)"
                  >
                    {{ flete.estado }}
                  </span>
                </td>
                
                <!-- Encomiendas -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ flete.totalEncomiendas }} encomiendas
                  </span>
                </td>
                
                <!-- Fecha -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(flete.created_at) }}
                </td>
                
                <!-- Acciones -->
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center gap-2">
                    <button
                      @click="verEncomiendas(flete)"
                      class="text-blue-600 hover:text-blue-900 p-1 rounded"
                      title="Ver encomiendas"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                    <button
                      @click="editFlete(flete)"
                      class="text-green-600 hover:text-green-900 p-1 rounded"
                      title="Editar flete"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button
                      @click="deleteFlete(flete.id)"
                      class="text-red-600 hover:text-red-900 p-1 rounded"
                      title="Eliminar flete"
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
        
        <!-- Estado vacío para vista tabla -->
        <div v-if="filteredFletes.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No hay fletes</h3>
          <p class="mt-1 text-sm text-gray-500">No se encontraron fletes con los criterios de búsqueda.</p>
        </div>
      </div>

      <!-- Modal para editar flete -->
      <div v-if="showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Editar Flete</h3>
            <form @submit.prevent="saveFlete">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Código del Flete</label>
                  <input
                    v-model="editingFlete.codigo"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal Destino</label>
                  <select
                    v-model="editingFlete.sucursalDestino"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar destino</option>
                    <option v-for="sucursal in sucursalesDisponibles" :key="sucursal.id" :value="sucursal.id">
                      {{ sucursal.nombre }}
                    </option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                  <select
                    v-model="editingFlete.estado"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="En origen">En origen</option>
                    <option value="En tránsito">En tránsito</option>
                    <option value="En destino">En destino</option>
                    <option value="De vuelta">De vuelta</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                  <textarea
                    v-model="editingFlete.observaciones"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  ></textarea>
                </div>
              </div>
              <div class="flex justify-end gap-3 mt-6">
                <button
                  type="button"
                  @click="showEditModal = false"
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-200"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors duration-200"
                >
                  Guardar Cambios
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Modal de confirmación para eliminar -->
      <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
              <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
            </div>
            <div class="mt-3 text-center">
              <h3 class="text-lg font-medium text-gray-900">Eliminar Flete</h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  ¿Estás seguro de que deseas eliminar este flete? Esta acción no se puede deshacer.
                </p>
              </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
              <button
                @click="showDeleteModal = false"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-200"
              >
                Cancelar
              </button>
              <button
                @click="confirmDelete"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors duration-200"
              >
                Eliminar
              </button>
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
      filtroEstado: '',
      filteredFletes: [],
      vistaActual: 'box', // 'box' o 'tabla'
      showEditModal: false,
      showDeleteModal: false,
      editingFlete: {},
      fleteToDelete: null,
      loading: false,
      // Simular usuario en sesión (sucursal origen)
      usuarioSesion: {
        idSucursal: 1, // Sucursal Principal
        nombre: 'Administrador'
      }
    }
  },
  computed: {
    fechaActual() {
      const hoy = new Date()
      return hoy.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      })
    },
    sucursalesDisponibles() {
      // Excluir la sucursal del usuario en sesión
      return this.sucursales.filter(sucursal => sucursal.id !== this.usuarioSesion.idSucursal)
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
        // Simular datos de fletes basados en los seeders
        this.fletes = [
          {
            id: 1,
            codigo: 'FLT-251015-001',
            sucursalOrigen: { id: 1, nombre: 'Sucursal Principal' },
            sucursalDestino: { id: 2, nombre: 'Lima Centro' },
            estado: 'En origen',
            totalEncomiendas: 2,
            observaciones: 'Flete programado para Lima',
            created_at: '2025-10-15',
            encomiendas: [
              { id: 1, codigo: 'ENC-251008-001', cliente: { nombre: 'Juan Carlos Pérez García' }, estado: 'En origen', valor: 25.50 },
              { id: 2, codigo: 'ENC-251008-004', cliente: { nombre: 'Carlos Alberto Mendoza Silva' }, estado: 'En origen', valor: 35.75 }
            ]
          },
          {
            id: 2,
            codigo: 'FLT-251015-002',
            sucursalOrigen: { id: 1, nombre: 'Sucursal Principal' },
            sucursalDestino: { id: 3, nombre: 'Arequipa Centro' },
            estado: 'En tránsito',
            totalEncomiendas: 1,
            observaciones: 'Flete en camino a Arequipa',
            created_at: '2025-10-15',
            encomiendas: [
              { id: 3, codigo: 'ENC-251008-002', cliente: { nombre: 'Comercial Los Andes S.A.C.' }, estado: 'En tránsito', valor: 45.00 }
            ]
          },
          {
            id: 3,
            codigo: 'FLT-251015-003',
            sucursalOrigen: { id: 1, nombre: 'Sucursal Principal' },
            sucursalDestino: { id: 4, nombre: 'Cusco Centro' },
            estado: 'En destino',
            totalEncomiendas: 1,
            observaciones: 'Flete llegó a Cusco',
            created_at: '2025-10-15',
            encomiendas: [
              { id: 4, codigo: 'ENC-251008-003', cliente: { nombre: 'María Elena Rodríguez López' }, estado: 'En destino', valor: 15.00 }
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
        // Datos de ejemplo basados en los seeders
        this.sucursales = [
          { id: 1, nombre: 'Sucursal Principal' },
          { id: 2, nombre: 'Lima Centro' },
          { id: 3, nombre: 'Arequipa Centro' },
          { id: 4, nombre: 'Cusco Centro' }
        ]
      }
    },
    
    filterFletes() {
      let filtered = [...this.fletes]
      
      // Filtro por término de búsqueda
      if (this.searchTerm) {
      const term = this.searchTerm.toLowerCase()
        filtered = filtered.filter(flete => 
        flete.codigo.toLowerCase().includes(term) ||
        flete.sucursalOrigen?.nombre.toLowerCase().includes(term) ||
        flete.sucursalDestino?.nombre.toLowerCase().includes(term) ||
        flete.estado.toLowerCase().includes(term)
      )
      }
      
      // Filtro por estado
      if (this.filtroEstado) {
        filtered = filtered.filter(flete => flete.estado === this.filtroEstado)
      }
      
      this.filteredFletes = filtered
    },
    
    verEncomiendas(flete) {
      // Navegar a la vista de encomiendas del flete
      this.$router.push({
        name: 'FleteEncomiendas',
        params: { fleteId: flete.id },
        query: { 
          codigo: flete.codigo,
          destino: flete.sucursalDestino?.nombre || 'Sin destino'
        }
      })
    },
    
    editFlete(flete) {
      this.editingFlete = {
        id: flete.id,
        codigo: flete.codigo,
        sucursalDestino: flete.sucursalDestino?.id || '',
        estado: flete.estado,
        observaciones: flete.observaciones || ''
      }
      this.showEditModal = true
    },
    
    async saveFlete() {
      try {
        console.log('Guardando flete:', this.editingFlete)
        // Aquí se haría la llamada a la API
        
        // Actualizar el flete en la lista
        const index = this.fletes.findIndex(f => f.id === this.editingFlete.id)
        if (index !== -1) {
          const sucursalDestino = this.sucursales.find(s => s.id == this.editingFlete.sucursalDestino)
          
          this.fletes[index] = {
            ...this.fletes[index],
            codigo: this.editingFlete.codigo,
            sucursalDestino,
            estado: this.editingFlete.estado,
            observaciones: this.editingFlete.observaciones
          }
        }
        
        // Actualizar también en filteredFletes
        const filteredIndex = this.filteredFletes.findIndex(f => f.id === this.editingFlete.id)
        if (filteredIndex !== -1) {
          this.filteredFletes[filteredIndex] = this.fletes[index]
        }
        
        this.showEditModal = false
        this.editingFlete = {}
        this.$toast?.success('Flete actualizado correctamente')
      } catch (error) {
        console.error('Error al actualizar flete:', error)
        this.$toast?.error('Error al actualizar flete')
      }
    },
    
    deleteFlete(id) {
      this.fleteToDelete = id
      this.showDeleteModal = true
    },
    
    async confirmDelete() {
      try {
        console.log('Eliminando flete:', this.fleteToDelete)
        // Aquí se haría la llamada a la API
        
        // Remover de la lista
        this.fletes = this.fletes.filter(f => f.id !== this.fleteToDelete)
        this.filteredFletes = this.filteredFletes.filter(f => f.id !== this.fleteToDelete)
        
        this.showDeleteModal = false
        this.fleteToDelete = null
        this.$toast?.success('Flete eliminado correctamente')
      } catch (error) {
        console.error('Error al eliminar flete:', error)
        this.$toast?.error('Error al eliminar flete')
      }
    },
    
    getEstadoClass(estado) {
      const clases = {
        'En origen': 'bg-yellow-100 text-yellow-800',
        'En tránsito': 'bg-blue-100 text-blue-800',
        'En destino': 'bg-green-100 text-green-800',
        'De vuelta': 'bg-purple-100 text-purple-800'
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

/* Animaciones para las cards */
.grid > div {
  transition: transform 0.2s ease-in-out;
}

.grid > div:hover {
  transform: translateY(-2px);
}
</style>