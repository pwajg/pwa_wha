<template>
  <AdminLayout :key="$route.path">
    <div class="configuracion-page">
      <!-- Header con título -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-700 dark:text-white mb-2">Configuración del Sistema</h1>
        <p class="text-gray-600 dark:text-gray-300">Gestiona la configuración general del sistema</p>
      </div>

      <!-- Configuraciones principales -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Configuración de empresa -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            Información de la Empresa
          </h3>
          <form @submit.prevent="guardarConfiguracionEmpresa">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Empresa</label>
                <input
                  v-model="configuracion.empresa.nombre"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">RUC</label>
                <input
                  v-model="configuracion.empresa.ruc"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                <textarea
                  v-model="configuracion.empresa.direccion"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                ></textarea>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input
                  v-model="configuracion.empresa.telefono"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input
                  v-model="configuracion.empresa.email"
                  type="email"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
            </div>
            <div class="mt-6">
              <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors duration-200"
              >
                Guardar Cambios
              </button>
            </div>
          </form>
        </div>

        <!-- Configuración de sistema -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Configuración del Sistema
          </h3>
          <form @submit.prevent="guardarConfiguracionSistema">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Código de Encomienda (Prefijo)</label>
                <input
                  v-model="configuracion.sistema.prefijoCodigo"
                  type="text"
                  placeholder="ENC"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tiempo de Retención (días)</label>
                <input
                  v-model="configuracion.sistema.tiempoRetencion"
                  type="number"
                  min="1"
                  max="365"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor Mínimo de Encomienda</label>
                <input
                  v-model="configuracion.sistema.valorMinimo"
                  type="number"
                  min="0"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor Máximo de Encomienda</label>
                <input
                  v-model="configuracion.sistema.valorMaximo"
                  type="number"
                  min="0"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div class="flex items-center">
                <input
                  v-model="configuracion.sistema.notificacionesEmail"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label class="ml-2 block text-sm text-gray-700">
                  Habilitar notificaciones por email
                </label>
              </div>
              <div class="flex items-center">
                <input
                  v-model="configuracion.sistema.backupAutomatico"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label class="ml-2 block text-sm text-gray-700">
                  Backup automático diario
                </label>
              </div>
            </div>
            <div class="mt-6">
              <button
                type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors duration-200"
              >
                Guardar Cambios
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Configuración de seguridad -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
          <svg class="h-5 w-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
          Configuración de Seguridad
        </h3>
        <form @submit.prevent="guardarConfiguracionSeguridad">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tiempo de Sesión (minutos)</label>
                <input
                  v-model="configuracion.seguridad.tiempoSesion"
                  type="number"
                  min="5"
                  max="480"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Intentos de Login Máximos</label>
                <input
                  v-model="configuracion.seguridad.intentosMaximos"
                  type="number"
                  min="3"
                  max="10"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tiempo de Bloqueo (minutos)</label>
                <input
                  v-model="configuracion.seguridad.tiempoBloqueo"
                  type="number"
                  min="5"
                  max="60"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
            </div>
            <div class="space-y-4">
              <div class="flex items-center">
                <input
                  v-model="configuracion.seguridad.requerirVerificacionEmail"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label class="ml-2 block text-sm text-gray-700">
                  Requerir verificación de email
                </label>
              </div>
              <div class="flex items-center">
                <input
                  v-model="configuracion.seguridad.logAuditoria"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label class="ml-2 block text-sm text-gray-700">
                  Habilitar log de auditoría
                </label>
              </div>
              <div class="flex items-center">
                <input
                  v-model="configuracion.seguridad.cifradoDatos"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label class="ml-2 block text-sm text-gray-700">
                  Cifrado de datos sensibles
                </label>
              </div>
            </div>
          </div>
          <div class="mt-6">
            <button
              type="submit"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition-colors duration-200"
            >
              Guardar Cambios
            </button>
          </div>
        </form>
      </div>

      <!-- Configuración de notificaciones -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
          <svg class="h-5 w-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12 7l-1.586-1.586a2 2 0 00-2.828 0L4.828 7z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
          </svg>
          Configuración de Notificaciones
        </h3>
        <form @submit.prevent="guardarConfiguracionNotificaciones">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-4">
              <h4 class="font-medium text-gray-800">Email</h4>
              <div class="space-y-3">
                <div class="flex items-center">
                  <input
                    v-model="configuracion.notificaciones.email.nuevaEncomienda"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <label class="ml-2 block text-sm text-gray-700">
                    Nueva encomienda
                  </label>
                </div>
                <div class="flex items-center">
                  <input
                    v-model="configuracion.notificaciones.email.cambioEstado"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <label class="ml-2 block text-sm text-gray-700">
                    Cambio de estado
                  </label>
                </div>
                <div class="flex items-center">
                  <input
                    v-model="configuracion.notificaciones.email.entregaExitosa"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <label class="ml-2 block text-sm text-gray-700">
                    Entrega exitosa
                  </label>
                </div>
              </div>
            </div>
            <div class="space-y-4">
              <h4 class="font-medium text-gray-800">SMS</h4>
              <div class="space-y-3">
                <div class="flex items-center">
                  <input
                    v-model="configuracion.notificaciones.sms.nuevaEncomienda"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <label class="ml-2 block text-sm text-gray-700">
                    Nueva encomienda
                  </label>
                </div>
                <div class="flex items-center">
                  <input
                    v-model="configuracion.notificaciones.sms.cambioEstado"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <label class="ml-2 block text-sm text-gray-700">
                    Cambio de estado
                  </label>
                </div>
                <div class="flex items-center">
                  <input
                    v-model="configuracion.notificaciones.sms.entregaExitosa"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <label class="ml-2 block text-sm text-gray-700">
                    Entrega exitosa
                  </label>
                </div>
              </div>
            </div>
            <div class="space-y-4">
              <h4 class="font-medium text-gray-800">Push</h4>
              <div class="space-y-3">
                <div class="flex items-center">
                  <input
                    v-model="configuracion.notificaciones.push.nuevaEncomienda"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <label class="ml-2 block text-sm text-gray-700">
                    Nueva encomienda
                  </label>
                </div>
                <div class="flex items-center">
                  <input
                    v-model="configuracion.notificaciones.push.cambioEstado"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <label class="ml-2 block text-sm text-gray-700">
                    Cambio de estado
                  </label>
                </div>
                <div class="flex items-center">
                  <input
                    v-model="configuracion.notificaciones.push.entregaExitosa"
                    type="checkbox"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <label class="ml-2 block text-sm text-gray-700">
                    Entrega exitosa
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="mt-6">
            <button
              type="submit"
              class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md transition-colors duration-200"
            >
              Guardar Cambios
            </button>
          </div>
        </form>
      </div>

      <!-- Acciones del sistema -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
          <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Acciones del Sistema
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <button
            @click="crearBackup"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-md transition-colors duration-200 flex items-center justify-center"
          >
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
            </svg>
            Crear Backup
          </button>
          <button
            @click="limpiarCache"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-md transition-colors duration-200 flex items-center justify-center"
          >
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Limpiar Cache
          </button>
          <button
            @click="optimizarBaseDatos"
            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-3 rounded-md transition-colors duration-200 flex items-center justify-center"
          >
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Optimizar BD
          </button>
          <button
            @click="reiniciarSistema"
            class="bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-md transition-colors duration-200 flex items-center justify-center"
          >
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Reiniciar Sistema
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '../../components/AdminLayout.vue'

export default {
  name: 'ConfiguracionPage',
  components: {
    AdminLayout
  },
  data() {
    return {
      configuracion: {
        empresa: {
          nombre: 'WAFREN SAC',
          ruc: '20123456789',
          direccion: 'Av. Villareal 123, Trujillo, Perú',
          telefono: '+51 1 234 5678',
          email: 'info@wafren.com'
        },
        sistema: {
          prefijoCodigo: 'ENC',
          tiempoRetencion: 30,
          valorMinimo: 1000,
          valorMaximo: 50000,
          notificacionesEmail: true,
          backupAutomatico: true
        },
        seguridad: {
          tiempoSesion: 60,
          intentosMaximos: 5,
          tiempoBloqueo: 15,
          requerirVerificacionEmail: true,
          logAuditoria: true,
          cifradoDatos: true
        },
        notificaciones: {
          email: {
            nuevaEncomienda: true,
            cambioEstado: true,
            entregaExitosa: true
          },
          sms: {
            nuevaEncomienda: false,
            cambioEstado: true,
            entregaExitosa: true
          },
          push: {
            nuevaEncomienda: true,
            cambioEstado: true,
            entregaExitosa: true
          }
        }
      }
    }
  },
  methods: {
    async guardarConfiguracionEmpresa() {
      try {
        console.log('Guardando configuración de empresa:', this.configuracion.empresa)
        // Aquí se haría la llamada a la API
        this.$toast?.success('Configuración de empresa guardada correctamente')
      } catch (error) {
        console.error('Error al guardar configuración de empresa:', error)
        this.$toast?.error('Error al guardar configuración')
      }
    },
    
    async guardarConfiguracionSistema() {
      try {
        console.log('Guardando configuración del sistema:', this.configuracion.sistema)
        // Aquí se haría la llamada a la API
        this.$toast?.success('Configuración del sistema guardada correctamente')
      } catch (error) {
        console.error('Error al guardar configuración del sistema:', error)
        this.$toast?.error('Error al guardar configuración')
      }
    },
    
    async guardarConfiguracionSeguridad() {
      try {
        console.log('Guardando configuración de seguridad:', this.configuracion.seguridad)
        // Aquí se haría la llamada a la API
        this.$toast?.success('Configuración de seguridad guardada correctamente')
      } catch (error) {
        console.error('Error al guardar configuración de seguridad:', error)
        this.$toast?.error('Error al guardar configuración')
      }
    },
    
    async guardarConfiguracionNotificaciones() {
      try {
        console.log('Guardando configuración de notificaciones:', this.configuracion.notificaciones)
        // Aquí se haría la llamada a la API
        this.$toast?.success('Configuración de notificaciones guardada correctamente')
      } catch (error) {
        console.error('Error al guardar configuración de notificaciones:', error)
        this.$toast?.error('Error al guardar configuración')
      }
    },
    
    async crearBackup() {
      if (confirm('¿Estás seguro de que deseas crear un backup del sistema?')) {
        try {
          console.log('Creando backup del sistema...')
          // Aquí se haría la llamada a la API
          this.$toast?.success('Backup creado correctamente')
        } catch (error) {
          console.error('Error al crear backup:', error)
          this.$toast?.error('Error al crear backup')
        }
      }
    },
    
    async limpiarCache() {
      if (confirm('¿Estás seguro de que deseas limpiar la caché del sistema?')) {
        try {
          console.log('Limpiando caché del sistema...')
          // Aquí se haría la llamada a la API
          this.$toast?.success('Caché limpiada correctamente')
        } catch (error) {
          console.error('Error al limpiar caché:', error)
          this.$toast?.error('Error al limpiar caché')
        }
      }
    },
    
    async optimizarBaseDatos() {
      if (confirm('¿Estás seguro de que deseas optimizar la base de datos?')) {
        try {
          console.log('Optimizando base de datos...')
          // Aquí se haría la llamada a la API
          this.$toast?.success('Base de datos optimizada correctamente')
        } catch (error) {
          console.error('Error al optimizar base de datos:', error)
          this.$toast?.error('Error al optimizar base de datos')
        }
      }
    },
    
    async reiniciarSistema() {
      if (confirm('¿Estás seguro de que deseas reiniciar el sistema? Esto puede causar interrupciones.')) {
        try {
          console.log('Reiniciando sistema...')
          // Aquí se haría la llamada a la API
          this.$toast?.success('Sistema reiniciado correctamente')
        } catch (error) {
          console.error('Error al reiniciar sistema:', error)
          this.$toast?.error('Error al reiniciar sistema')
        }
      }
    }
  }
}
</script>

<style scoped>
.configuracion-page {
  padding: 1.5rem;
}

@media (max-width: 640px) {
  .configuracion-page {
    padding: 1rem;
  }
}
</style>
