<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 transition-all duration-200 hover:shadow-md overflow-hidden">
    <!-- Header del Widget -->
    <div class="flex items-center justify-between mb-4 min-w-0">
      <div class="flex items-center gap-3 min-w-0 flex-1">
        <!-- Ícono representativo -->
        <div v-if="icon" :class="iconBg" class="p-2 rounded-lg flex-shrink-0">
          <component :is="icon" :class="iconColor" class="w-5 h-5" />
        </div>
        <div class="min-w-0 flex-1">
          <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-white truncate">
            {{ title }}
          </h3>
          <p v-if="subtitle" class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1 break-words">
            {{ subtitle }}
          </p>
        </div>
      </div>
      
      <!-- Acciones del Widget -->
      <div class="flex items-center gap-2">
        <!-- Botón de filtro -->
        <button
          v-if="showFilter"
          @click="$emit('filter')"
          class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 active:scale-95"
          title="Filtrar"
          aria-label="Abrir filtros"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
          </svg>
        </button>
        
        <!-- Botón de exportación con dropdown -->
        <div v-if="showExport" class="relative">
          <button
            @click="toggleExportMenu"
            @blur="closeExportMenu"
            class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors flex items-center gap-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 active:scale-95"
            title="Exportar"
            aria-label="Exportar datos"
            aria-haspopup="true"
            :aria-expanded="exportMenuOpen"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
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
              aria-label="Exportar como PDF"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
              </svg>
              Exportar como PDF
            </button>
            <button
              @click="exportarExcel"
              class="w-full px-4 py-2 text-left text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2 transition-colors focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 active:bg-gray-200 dark:active:bg-gray-600"
              aria-label="Exportar como Excel"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Exportar como Excel
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Contenido del Widget -->
    <div class="widget-content overflow-hidden">
      <slot></slot>
    </div>
    
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-12" role="status" aria-label="Cargando datos">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 dark:border-blue-400"></div>
      <span class="sr-only">Cargando...</span>
    </div>
    
    <!-- Empty State -->
    <div v-else-if="empty" class="text-center py-12" role="status" aria-label="Sin datos">
      <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
      </svg>
      <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ emptyMessage || 'No hay datos disponibles' }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  icon: {
    type: [String, Object],
    default: null
  },
  iconBg: {
    type: String,
    default: 'bg-blue-100 dark:bg-blue-900'
  },
  iconColor: {
    type: String,
    default: 'text-blue-600 dark:text-blue-300'
  },
  showFilter: {
    type: Boolean,
    default: false
  },
  showExport: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  empty: {
    type: Boolean,
    default: false
  },
  emptyMessage: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['filter', 'export-pdf', 'export-excel'])

const exportMenuOpen = ref(false)

const toggleExportMenu = () => {
  exportMenuOpen.value = !exportMenuOpen.value
}

const closeExportMenu = () => {
  setTimeout(() => {
    exportMenuOpen.value = false
  }, 200)
}

const exportarPDF = () => {
  exportMenuOpen.value = false
  emit('export-pdf')
}

const exportarExcel = () => {
  exportMenuOpen.value = false
  emit('export-excel')
}
</script>

<style scoped>
.widget-content {
  min-height: 200px;
}
</style>
