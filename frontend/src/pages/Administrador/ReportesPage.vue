<template>
  <AdminLayout :key="$route.path">
    <div class="reportes-page">
      <!-- Header con título -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-700 dark:text-white mb-2">Reportes y Estadísticas</h1>
        <p class="text-gray-600 dark:text-white">Análisis de datos y métricas del negocio</p>
      </div>

      <!-- Filtros de fecha -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Filtros de Fecha</h3>
        <div class="flex flex-col sm:flex-row gap-4">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
            <input
              v-model="filtros.fechaDesde"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
            <input
              v-model="filtros.fechaHasta"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          <div class="flex items-end">
            <button
              @click="aplicarFiltros"
              class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition-colors duration-200"
            >
              Aplicar Filtros
            </button>
          </div>
        </div>
      </div>

      <!-- Métricas principales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-full">
              <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Encomiendas</p>
              <p class="text-2xl font-bold text-gray-900">{{ metricas.totalEncomiendas }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-full">
              <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Ingresos Totales</p>
              <p class="text-2xl font-bold text-gray-900">${{ metricas.ingresosTotales.toLocaleString() }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-full">
              <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Clientes Activos</p>
              <p class="text-2xl font-bold text-gray-900">{{ metricas.clientesActivos }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-full">
              <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Sucursales</p>
              <p class="text-2xl font-bold text-gray-900">{{ metricas.totalSucursales }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Gráficos principales -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Gráfico de encomiendas por estado -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Encomiendas por Estado</h3>
          <div class="h-64 flex items-center justify-center">
            <div class="text-center">
              <div class="w-32 h-32 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <span class="text-gray-500">📊</span>
              </div>
              <p class="text-gray-600">Gráfico de estados de encomiendas</p>
              <p class="text-sm text-gray-500">(Integración con Chart.js pendiente)</p>
            </div>
          </div>
        </div>

        <!-- Gráfico de ingresos mensuales -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Ingresos Mensuales</h3>
          <div class="h-64 flex items-center justify-center">
            <div class="text-center">
              <div class="w-32 h-32 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <span class="text-gray-500">📈</span>
              </div>
              <p class="text-gray-600">Evolución de ingresos</p>
              <p class="text-sm text-gray-500">(Integración con Chart.js pendiente)</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de encomiendas recientes -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-800">Encomiendas Recientes</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origen</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destino</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="encomienda in encomiendasRecientes" :key="encomienda.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ encomienda.codigo }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ encomienda.cliente?.nombre || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ encomienda.sucursalOrigen?.nombre || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ encomienda.sucursalDestino?.nombre || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                        :class="getEstadoClass(encomienda.estado)">
                    {{ encomienda.estado }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(encomienda.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  ${{ encomienda.valor?.toLocaleString() || '0' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Estadísticas por sucursal -->
      <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-800">Estadísticas por Sucursal</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sucursal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Encomiendas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eficiencia</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="sucursal in estadisticasSucursales" :key="sucursal.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ sucursal.nombre }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ sucursal.totalEncomiendas }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  ${{ sucursal.ingresos.toLocaleString() }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  ${{ sucursal.promedioEncomienda.toLocaleString() }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                      <div class="bg-green-500 h-2 rounded-full" :style="{ width: sucursal.eficiencia + '%' }"></div>
                    </div>
                    <span class="text-sm text-gray-600">{{ sucursal.eficiencia }}%</span>
                  </div>
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
  name: 'ReportesPage',
  components: {
    AdminLayout
  },
  data() {
    return {
      filtros: {
        fechaDesde: '',
        fechaHasta: ''
      },
      metricas: {
        totalEncomiendas: 0,
        ingresosTotales: 0,
        clientesActivos: 0,
        totalSucursales: 0
      },
      encomiendasRecientes: [],
      estadisticasSucursales: []
    }
  },
  async mounted() {
    await this.cargarDatos()
    this.establecerFechasPorDefecto()
  },
  methods: {
    async cargarDatos() {
      try {
        await Promise.all([
          this.cargarMetricas(),
          this.cargarEncomiendasRecientes(),
          this.cargarEstadisticasSucursales()
        ])
      } catch (error) {
        console.error('Error al cargar datos:', error)
      }
    },
    
    async cargarMetricas() {
      try {
        // Simular datos de métricas
        this.metricas = {
          totalEncomiendas: 1247,
          ingresosTotales: 45678900,
          clientesActivos: 89,
          totalSucursales: 3
        }
      } catch (error) {
        console.error('Error al cargar métricas:', error)
      }
    },
    
    async cargarEncomiendasRecientes() {
      try {
        const response = await axios.get('/encomiendas')
        this.encomiendasRecientes = response.data.slice(0, 10) // Últimas 10
      } catch (error) {
        console.error('Error al cargar encomiendas recientes:', error)
        // Datos de ejemplo
        this.encomiendasRecientes = [
          {
            id: 1,
            codigo: 'ENC-001',
            cliente: { nombre: 'Juan Pérez' },
            sucursalOrigen: { nombre: 'Sucursal Central' },
            sucursalDestino: { nombre: 'Sucursal Norte' },
            estado: 'Entregada',
            created_at: '2025-10-09',
            valor: 15000
          }
        ]
      }
    },
    
    async cargarEstadisticasSucursales() {
      try {
        // Simular estadísticas por sucursal
        this.estadisticasSucursales = [
          {
            id: 1,
            nombre: 'Sucursal Principal',
            totalEncomiendas: 456,
            ingresos: 15678900,
            promedioEncomienda: 34400,
            eficiencia: 92
          },
          {
            id: 2,
            nombre: 'Sucursal Norte',
            totalEncomiendas: 389,
            ingresos: 12345600,
            promedioEncomienda: 31700,
            eficiencia: 88
          },
          {
            id: 3,
            nombre: 'Sucursal Sur',
            totalEncomiendas: 402,
            ingresos: 17654400,
            promedioEncomienda: 43900,
            eficiencia: 95
          }
        ]
      } catch (error) {
        console.error('Error al cargar estadísticas de sucursales:', error)
      }
    },
    
    establecerFechasPorDefecto() {
      const hoy = new Date()
      const haceUnMes = new Date(hoy.getFullYear(), hoy.getMonth() - 1, hoy.getDate())
      
      this.filtros.fechaDesde = haceUnMes.toISOString().split('T')[0]
      this.filtros.fechaHasta = hoy.toISOString().split('T')[0]
    },
    
    aplicarFiltros() {
      console.log('Aplicando filtros:', this.filtros)
      this.cargarDatos()
    },
    
    getEstadoClass(estado) {
      const clases = {
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
.reportes-page {
  padding: 1.5rem;
}

@media (max-width: 640px) {
  .reportes-page {
    padding: 1rem;
  }
}
</style>
