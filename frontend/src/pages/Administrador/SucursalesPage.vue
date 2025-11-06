<template>
  <AdminLayout :key="$route.path">
    <div class="sucursales-page">
      <!-- Header con título -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-700 dark:text-white mb-2">Gestión de Sucursales</h1>
        <p class="text-gray-600 dark:text-gray-300">Administra las sucursales del sistema</p>
      </div>

      <!-- Barra de búsqueda y botón agregar -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <!-- Buscador -->
        <div class="flex-1 max-w-md">
          <div class="relative">
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Buscar sucursales..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              @input="filterSucursales"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
          Agregar Sucursal
        </button>
      </div>

      <!-- Indicador de carga -->
      <div v-if="loading" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 dark:border-blue-400 mx-auto"></div>
        <p class="mt-2 text-gray-600 dark:text-gray-300">Cargando sucursales...</p>
      </div>

      <!-- Tabla de sucursales -->
      <div v-else class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 border-collapse">
            <thead class="bg-gray-50 dark:bg-gray-700/80 border-b-2 border-gray-200 dark:border-gray-600">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Dirección</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ciudad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Teléfono</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha Creación</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr 
                v-for="(sucursal, index) in filteredSucursales" 
                :key="sucursal.id" 
                :class="[
                  index % 2 === 0 
                    ? 'bg-white dark:bg-gray-800' 
                    : 'bg-gray-50 dark:bg-gray-800/50',
                  'hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 ease-in-out'
                ]"
              >
                <!-- ID -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ sucursal.id }}</td>
                
                <!-- Nombre -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  <input
                    v-if="editingId === sucursal.id"
                    v-model="editingSucursal.nombre"
                    type="text"
                    class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                  <span v-else>{{ sucursal.nombre }}</span>
                </td>
                
                <!-- Dirección -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  <input
                    v-if="editingId === sucursal.id"
                    v-model="editingSucursal.direccion"
                    type="text"
                    class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                  <span v-else>{{ sucursal.direccion }}</span>
                </td>
                
                <!-- Ciudad -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  <input
                    v-if="editingId === sucursal.id"
                    v-model="editingSucursal.ciudad"
                    type="text"
                    class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                  <span v-else>{{ sucursal.ciudad }}</span>
                </td>
                
                <!-- Teléfono -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  <input
                    v-if="editingId === sucursal.id"
                    v-model="editingSucursal.telefono"
                    type="text"
                    class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                  <span v-else>{{ sucursal.telefono }}</span>
                </td>
                
                <!-- Fecha Creación -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ formatDate(sucursal.created_at) }}
                </td>
                
                <!-- Acciones -->
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div v-if="editingId === sucursal.id" class="flex items-center gap-2">
                    <button
                      @click="saveSucursal(sucursal.id)"
                      class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 p-1 rounded transition-colors duration-150"
                      title="Guardar"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      </svg>
                    </button>
                    <button
                      @click="cancelEdit"
                      class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 p-1 rounded transition-colors duration-150"
                      title="Cancelar"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                  <div v-else class="flex items-center gap-2">
                    <button
                      @click="editSucursal(sucursal)"
                      class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 p-1 rounded transition-colors duration-150"
                      title="Editar"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button
                      @click="deleteSucursal(sucursal.id)"
                      class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 p-1 rounded transition-colors duration-150"
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
        <div v-if="filteredSucursales.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay sucursales</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza agregando una nueva sucursal.</p>
        </div>
      </div>

      <!-- Modal para agregar sucursal -->
      <div v-if="showAddModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Agregar Nueva Sucursal</h3>
            <form @submit.prevent="addSucursal">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                  <input
                    v-model="newSucursal.nombre"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                  <input
                    v-model="newSucursal.direccion"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                  <input
                    v-model="newSucursal.ciudad"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                  <input
                    v-model="newSucursal.telefono"
                    type="text"
                    required
                    maxlength="9"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
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
                  Agregar
                </button>
              </div>
            </form>
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
  name: 'SucursalesPage',
  components: {
    AdminLayout
  },
  data() {
    return {
      sucursales: [],
      searchTerm: '',
      filteredSucursales: [],
      editingId: null,
      editingSucursal: {},
      showAddModal: false,
      newSucursal: {
        nombre: '',
        direccion: '',
        ciudad: '',
        telefono: ''
      },
      loading: false
    }
  },
  async mounted() {
    await this.loadSucursales()
  },
  methods: {
    async loadSucursales() {
      try {
        this.loading = true
        const response = await axios.get('/sucursales')
        this.sucursales = response.data
        this.filteredSucursales = [...this.sucursales]
      } catch (error) {
        console.error('Error al cargar sucursales:', error)
        this.$toast?.error('Error al cargar sucursales')
      } finally {
        this.loading = false
      }
    },
    
    filterSucursales() {
      if (!this.searchTerm) {
        this.filteredSucursales = [...this.sucursales]
        return
      }
      
      const term = this.searchTerm.toLowerCase()
      this.filteredSucursales = this.sucursales.filter(sucursal => 
        sucursal.nombre.toLowerCase().includes(term) ||
        sucursal.direccion.toLowerCase().includes(term) ||
        sucursal.ciudad.toLowerCase().includes(term) ||
        sucursal.telefono.includes(term)
      )
    },
    
    editSucursal(sucursal) {
      this.editingId = sucursal.id
      this.editingSucursal = {
        nombre: sucursal.nombre,
        direccion: sucursal.direccion,
        ciudad: sucursal.ciudad,
        telefono: sucursal.telefono
      }
    },
    
    async saveSucursal(id) {
      try {
        const response = await axios.put(`/sucursales/${id}`, this.editingSucursal)
        const updatedSucursal = response.data
        
        // Actualizar la sucursal en la lista
        const index = this.sucursales.findIndex(s => s.id === id)
        if (index !== -1) {
          this.sucursales[index] = updatedSucursal
        }
        
        // Actualizar también en filteredSucursales
        const filteredIndex = this.filteredSucursales.findIndex(s => s.id === id)
        if (filteredIndex !== -1) {
          this.filteredSucursales[filteredIndex] = updatedSucursal
        }
        
        this.editingId = null
        this.editingSucursal = {}
        this.$toast?.success('Sucursal actualizada correctamente')
      } catch (error) {
        console.error('Error al actualizar sucursal:', error)
        this.$toast?.error('Error al actualizar sucursal')
      }
    },
    
    cancelEdit() {
      this.editingId = null
      this.editingSucursal = {}
    },
    
    async deleteSucursal(id) {
      if (!confirm('¿Estás seguro de que deseas eliminar esta sucursal?')) {
        return
      }
      
      try {
        await axios.delete(`/sucursales/${id}`)
        
        // Remover de la lista
        this.sucursales = this.sucursales.filter(s => s.id !== id)
        this.filteredSucursales = this.filteredSucursales.filter(s => s.id !== id)
        
        this.$toast?.success('Sucursal eliminada correctamente')
      } catch (error) {
        console.error('Error al eliminar sucursal:', error)
        this.$toast?.error('Error al eliminar sucursal')
      }
    },
    
    async addSucursal() {
      try {
        const response = await axios.post('/sucursales', this.newSucursal)
        const newSucursal = response.data
        
        this.sucursales.push(newSucursal)
        this.filteredSucursales.push(newSucursal)
        
        this.showAddModal = false
        this.newSucursal = {
          nombre: '',
          direccion: '',
          ciudad: '',
          telefono: ''
        }
        
        this.$toast?.success('Sucursal agregada correctamente')
      } catch (error) {
        console.error('Error al agregar sucursal:', error)
        this.$toast?.error('Error al agregar sucursal')
      }
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
.sucursales-page {
  padding: 1.5rem;
}

@media (max-width: 640px) {
  .sucursales-page {
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
