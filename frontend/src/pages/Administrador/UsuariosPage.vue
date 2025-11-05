<template>
  <AdminLayout :key="$route.path">
    <div class="usuarios-page">
      <!-- Header con título -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-700 dark:text-white mb-2">Gestión de Usuarios</h1>
        <p class="text-gray-600 dark:text-gray-300">Administra los usuarios del sistema</p>
      </div>

      <!-- Barra de búsqueda y botón agregar -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <!-- Buscador -->
        <div class="flex-1 max-w-md">
          <div class="relative">
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Buscar usuarios..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              @input="filterUsers"
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
          Agregar Usuario
        </button>
      </div>

      <!-- Indicador de carga -->
      <div v-if="loading" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-gray-600">Cargando usuarios...</p>
      </div>

      <!-- Tabla de usuarios -->
      <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sucursal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="usuario in filteredUsers" :key="usuario.id" class="hover:bg-gray-50">
                <!-- ID -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ usuario.id }}</td>
                
                <!-- Nombre -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <input
                    v-if="editingId === usuario.id"
                    v-model="editingUser.nombre"
                    type="text"
                    class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                  <span v-else>{{ usuario.nombre }}</span>
                </td>
                
                <!-- Email -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <input
                    v-if="editingId === usuario.id"
                    v-model="editingUser.email"
                    type="email"
                    class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                  <span v-else>{{ usuario.email }}</span>
                </td>
                
                <!-- Rol -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <select
                    v-if="editingId === usuario.id"
                    v-model="editingUser.rol"
                    class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="Administrador">Administrador</option>
                    <option value="Colaborador">Colaborador</option>
                  </select>
                  <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                        :class="usuario.rol === 'Administrador' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'">
                    {{ usuario.rol }}
                  </span>
                </td>
                
                <!-- Sucursal -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <select
                    v-if="editingId === usuario.id"
                    v-model="editingUser.idSucursal"
                    class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Sin sucursal</option>
                    <option v-for="sucursal in sucursales" :key="sucursal.id" :value="sucursal.id">
                      {{ sucursal.nombre }}
                    </option>
                  </select>
                  <span v-else>{{ usuario.sucursal ? usuario.sucursal.nombre : 'Sin sucursal' }}</span>
                </td>
                
                <!-- Fecha Creación -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(usuario.created_at) }}
                </td>
                
                <!-- Acciones -->
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div v-if="editingId === usuario.id" class="flex items-center gap-2">
                    <button
                      @click="saveUser(usuario.id)"
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
                      @click="editUser(usuario)"
                      class="text-blue-600 hover:text-blue-900 p-1 rounded"
                      title="Editar"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button
                      @click="deleteUser(usuario.id)"
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
        <div v-if="filteredUsers.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No hay usuarios</h3>
          <p class="mt-1 text-sm text-gray-500">Comienza agregando un nuevo usuario.</p>
        </div>
      </div>

      <!-- Modal para agregar usuario -->
      <div v-if="showAddModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Agregar Nuevo Usuario</h3>
            <form @submit.prevent="addUser">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                  <input
                    v-model="newUser.nombre"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input
                    v-model="newUser.email"
                    type="email"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                  <input
                    v-model="newUser.password"
                    type="password"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                  <select
                    v-model="newUser.rol"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar rol</option>
                    <option value="Administrador">Administrador</option>
                    <option value="Colaborador">Colaborador</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Sucursal</label>
                  <select
                    v-model="newUser.idSucursal"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar sucursal</option>
                    <option v-for="sucursal in sucursales" :key="sucursal.id" :value="sucursal.id">
                      {{ sucursal.nombre }}
                    </option>
                  </select>
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
  name: 'UsuariosPage',
  components: {
    AdminLayout
  },
  data() {
    return {
      usuarios: [],
      sucursales: [],
      searchTerm: '',
      filteredUsers: [],
      editingId: null,
      editingUser: {},
      showAddModal: false,
      newUser: {
        nombre: '',
        email: '',
        password: '',
        rol: '',
        idSucursal: ''
      },
      loading: false
    }
  },
  async mounted() {
    await this.loadUsuarios()
    await this.loadSucursales()
  },
  methods: {
    async loadUsuarios() {
      try {
        this.loading = true
        const response = await axios.get('/usuarios')
        this.usuarios = response.data
        this.filteredUsers = [...this.usuarios]
      } catch (error) {
        console.error('Error al cargar usuarios:', error)
        this.$toast?.error('Error al cargar usuarios')
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
        // Si falla la carga de sucursales, continuar sin ellas
        this.sucursales = []
      }
    },
    
    filterUsers() {
      if (!this.searchTerm) {
        this.filteredUsers = [...this.usuarios]
        return
      }
      
      const term = this.searchTerm.toLowerCase()
      this.filteredUsers = this.usuarios.filter(usuario => 
        usuario.nombre.toLowerCase().includes(term) ||
        usuario.email.toLowerCase().includes(term) ||
        usuario.rol.toLowerCase().includes(term) ||
        (usuario.sucursal && usuario.sucursal.nombre.toLowerCase().includes(term))
      )
    },
    
    editUser(usuario) {
      this.editingId = usuario.id
      this.editingUser = {
        nombre: usuario.nombre,
        email: usuario.email,
        rol: usuario.rol,
        idSucursal: usuario.idSucursal
      }
    },
    
    async saveUser(id) {
      try {
        const response = await axios.put(`/usuarios/${id}`, this.editingUser)
        const updatedUser = response.data
        
        // Actualizar el usuario en la lista
        const index = this.usuarios.findIndex(u => u.id === id)
        if (index !== -1) {
          this.usuarios[index] = updatedUser
        }
        
        // Actualizar también en filteredUsers
        const filteredIndex = this.filteredUsers.findIndex(u => u.id === id)
        if (filteredIndex !== -1) {
          this.filteredUsers[filteredIndex] = updatedUser
        }
        
        this.editingId = null
        this.editingUser = {}
        this.$toast?.success('Usuario actualizado correctamente')
      } catch (error) {
        console.error('Error al actualizar usuario:', error)
        this.$toast?.error('Error al actualizar usuario')
      }
    },
    
    cancelEdit() {
      this.editingId = null
      this.editingUser = {}
    },
    
    async deleteUser(id) {
      if (!confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
        return
      }
      
      try {
        await axios.delete(`/usuarios/${id}`)
        
        // Remover de la lista
        this.usuarios = this.usuarios.filter(u => u.id !== id)
        this.filteredUsers = this.filteredUsers.filter(u => u.id !== id)
        
        this.$toast?.success('Usuario eliminado correctamente')
      } catch (error) {
        console.error('Error al eliminar usuario:', error)
        this.$toast?.error('Error al eliminar usuario')
      }
    },
    
    async addUser() {
      try {
        const response = await axios.post('/usuarios', this.newUser)
        const newUsuario = response.data
        
        this.usuarios.push(newUsuario)
        this.filteredUsers.push(newUsuario)
        
        this.showAddModal = false
        this.newUser = {
          nombre: '',
          email: '',
          password: '',
          rol: '',
          idSucursal: ''
        }
        
        this.$toast?.success('Usuario agregado correctamente')
      } catch (error) {
        console.error('Error al agregar usuario:', error)
        this.$toast?.error('Error al agregar usuario')
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
.usuarios-page {
  padding: 1.5rem;
}

@media (max-width: 640px) {
  .usuarios-page {
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
