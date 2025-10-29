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
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 rounded-lg flex items-center gap-2 transition-colors duration-200 btn-md-height"
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

      <!-- Modal para agregar/editar encomienda -->
      <div v-if="showAddModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-start justify-center p-3 sm:p-4" style="padding-top: 6rem; padding-bottom: 2rem;" @click.self="cancelarAgregarEncomienda">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[calc(100vh-10rem)] overflow-y-auto">
          <div class="sticky top-0 bg-white border-b border-gray-200 px-3 sm:px-4 py-3 z-10">
            <div class="flex justify-between items-center">
              <h3 class="text-base sm:text-lg font-semibold text-gray-900">
                {{ isEditingEncomienda ? 'Editar Encomienda' : 'Agregar Encomienda' }}
              </h3>
              <button
                @click="cancelarAgregarEncomienda"
                class="text-gray-400 hover:text-gray-600 transition-colors p-1"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
            </div>
            
          <form @submit.prevent="isEditingEncomienda ? actualizarEncomienda() : crearEncomienda()" class="p-3 sm:p-4">
            <!-- Información básica -->
            <div class="mb-4">
              <h4 class="text-sm sm:text-base font-medium text-gray-800 mb-3 pb-2 border-b border-gray-200">Información Básica</h4>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                  <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Código</label>
                <input
                    v-model="newEncomiendaForm.codigo"
                  type="text"
                    :readonly="!isEditingEncomienda"
                    :class="[
                      'w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg',
                      isEditingEncomienda 
                        ? 'focus:ring-2 focus:ring-blue-500 focus:border-transparent' 
                        : 'bg-gray-50 text-gray-600 cursor-not-allowed'
                    ]"
                  />
                </div>
                
                <div class="sm:col-span-2">
                  <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Descripción *</label>
                  <textarea
                    v-model="newEncomiendaForm.descripcion"
                    rows="2"
                    required
                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Describe el contenido"
                  ></textarea>
                </div>
                
                <div>
                  <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Costo (S/) *</label>
                  <input
                    v-model="newEncomiendaForm.costo"
                    type="number"
                    step="0.01"
                    min="0"
                    required
                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="0.00"
                  />
                </div>
                
                <div>
                  <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Estado Pago *</label>
                  <select
                    v-model="newEncomiendaForm.estadoPago"
                    required
                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar</option>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Parcial">Parcial</option>
                    <option value="Pagado">Pagado</option>
                  </select>
                </div>
                
                <div class="sm:col-span-2">
                  <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                  <textarea
                    v-model="newEncomiendaForm.observaciones"
                    rows="2"
                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Observaciones (opcional)"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Cliente Remitente -->
            <div class="mb-4">
              <h4 class="text-sm sm:text-base font-medium text-gray-800 mb-3 pb-2 border-b border-gray-200">Cliente Remitente</h4>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Tipo Cliente *</label>
                  <select
                    v-model="newEncomiendaForm.clienteRemitente.tipoCliente"
                    @change="validarDNIRemitente"
                    required
                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar</option>
                    <option value="Persona Natural">Persona Natural</option>
                    <option value="Empresa">Empresa</option>
                    <option value="Extranjero">Extranjero</option>
                  </select>
                </div>
                
                <div>
                  <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Documento *</label>
                  <input
                    v-model="newEncomiendaForm.clienteRemitente.dni"
                    type="text"
                    :maxlength="getMaxLength(newEncomiendaForm.clienteRemitente.tipoCliente)"
                    @input="validarDNIRemitente"
                    @blur="buscarClienteRemitente"
                    required
                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Número de documento"
                  />
                  <small class="text-xs text-gray-500 mt-0.5 block">{{ getHelpText(newEncomiendaForm.clienteRemitente.tipoCliente) }}</small>
                </div>
                
                <div>
                  <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                  <input
                    v-model="newEncomiendaForm.clienteRemitente.nombre"
                    type="text"
                    required
                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Nombre o razón social"
                  />
                </div>
                
                <div>
                  <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                  <input
                    v-model="newEncomiendaForm.clienteRemitente.telefono"
                    type="tel"
                    required
                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Teléfono"
                  />
                </div>
              </div>
            </div>
            
            <!-- Cliente Destinatario -->
            <div class="mb-4">
              <h4 class="text-sm sm:text-base font-medium text-gray-800 mb-3 pb-2 border-b border-gray-200">Cliente Destinatario</h4>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Tipo Cliente *</label>
                  <select
                    v-model="newEncomiendaForm.clienteDestinatario.tipoCliente"
                    @change="validarDNIDestinatario"
                    required
                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar</option>
                    <option value="Persona Natural">Persona Natural</option>
                    <option value="Empresa">Empresa</option>
                    <option value="Extranjero">Extranjero</option>
                  </select>
                </div>
                
                  <div>
                  <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Documento *</label>
                  <input
                    v-model="newEncomiendaForm.clienteDestinatario.dni"
                    type="text"
                    :maxlength="getMaxLength(newEncomiendaForm.clienteDestinatario.tipoCliente)"
                    @input="validarDNIDestinatario"
                    @blur="buscarClienteDestinatario"
                    required
                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Número de documento"
                  />
                  <small class="text-xs text-gray-500 mt-0.5 block">{{ getHelpText(newEncomiendaForm.clienteDestinatario.tipoCliente) }}</small>
                  </div>
                
                <div>
                  <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                  <input
                    v-model="newEncomiendaForm.clienteDestinatario.nombre"
                    type="text"
                    required
                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Nombre o razón social"
                  />
                  </div>
                
                <div>
                  <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                  <input
                    v-model="newEncomiendaForm.clienteDestinatario.telefono"
                    type="tel"
                    required
                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Teléfono"
                  />
                </div>
              </div>
            </div>
            
            <!-- Botones -->
            <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 pt-3 border-t border-gray-200">
              <button
                type="button"
                @click="cancelarAgregarEncomienda"
                class="w-full sm:w-auto px-3 sm:px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200"
              >
                Cancelar
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="w-full sm:w-auto px-3 sm:px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="loading">{{ isEditingEncomienda ? 'Actualizando...' : 'Creando...' }}</span>
                <span v-else>{{ isEditingEncomienda ? 'Actualizar' : 'Crear' }}</span>
              </button>
          </div>
          </form>
        </div>
      </div>

      <!-- Modal para ver detalles de encomienda -->
      <div v-if="showDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4" @click.self="showDetailsModal = false">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
          <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 z-10">
            <div class="flex justify-between items-center">
              <h3 class="text-xl font-semibold text-gray-900">Detalles de la Encomienda</h3>
              <button
                @click="showDetailsModal = false"
                class="text-gray-400 hover:text-gray-600 transition-colors"
              >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
            </div>
            
          <div v-if="selectedEncomienda" class="p-6">
            <!-- Código destacado -->
            <div class="mb-6">
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-blue-800 mb-1">Código de Encomienda</p>
                    <p class="text-2xl font-bold text-blue-900">{{ selectedEncomienda.codigo }}</p>
                  </div>
                      <span
                    class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"
                        :class="getEstadoClass(selectedEncomienda.estado)"
                      >
                        {{ selectedEncomienda.estado }}
                      </span>
                    </div>
                  </div>
                </div>
                
            <!-- Información en grid responsivo -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <!-- Información general -->
              <div class="bg-gray-50 rounded-lg p-5">
                <h4 class="text-base font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-300">Información General</h4>
                <div class="space-y-3">
                  <div>
                    <p class="text-xs font-medium text-gray-500 uppercase mb-1">Costo</p>
                    <p class="text-lg font-semibold text-gray-900">S/ {{ selectedEncomienda.costo?.toFixed(2) || '0.00' }}</p>
                  </div>
                  <div>
                    <p class="text-xs font-medium text-gray-500 uppercase mb-1">Estado de Pago</p>
                    <span
                      class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full"
                      :class="{
                        'bg-yellow-100 text-yellow-800': selectedEncomienda.estadoPago === 'Pendiente',
                        'bg-blue-100 text-blue-800': selectedEncomienda.estadoPago === 'Parcial',
                        'bg-green-100 text-green-800': selectedEncomienda.estadoPago === 'Pagado'
                      }"
                    >
                      {{ selectedEncomienda.estadoPago }}
                    </span>
                  </div>
                  <div>
                    <p class="text-xs font-medium text-gray-500 uppercase mb-1">Fecha de Creación</p>
                    <p class="text-sm text-gray-700">{{ formatDate(selectedEncomienda.created_at) }}</p>
                  </div>
                </div>
              </div>
              
              <!-- Clientes -->
              <div class="bg-gray-50 rounded-lg p-5">
                <h4 class="text-base font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-300">Clientes</h4>
                <div class="space-y-4">
                  <div>
                    <p class="text-xs font-medium text-gray-500 uppercase mb-1">Remitente</p>
                    <p class="text-sm font-medium text-gray-900">{{ selectedEncomienda.clienteRemitente?.nombre || 'N/A' }}</p>
                  </div>
                  <div class="flex items-center justify-center text-gray-400 my-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-xs font-medium text-gray-500 uppercase mb-1">Destinatario</p>
                    <p class="text-sm font-medium text-gray-900">{{ selectedEncomienda.clienteDestinatario?.nombre || 'N/A' }}</p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Descripción -->
            <div class="bg-gray-50 rounded-lg p-5 mb-6">
              <h4 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-300">Descripción</h4>
              <p class="text-sm text-gray-700 leading-relaxed">{{ selectedEncomienda.descripcion || 'Sin descripción' }}</p>
            </div>
            
            <!-- Observaciones -->
            <div class="bg-gray-50 rounded-lg p-5">
              <h4 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-300">Observaciones</h4>
              <p class="text-sm text-gray-700 leading-relaxed">{{ selectedEncomienda.observaciones || 'Sin observaciones' }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal de confirmación para eliminar encomienda -->
      <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4" @click.self="showDeleteModal = false">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-auto">
          <div class="p-6">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
              <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
            </div>
            <div class="mt-3 text-center">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Eliminar Encomienda del Flete</h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  ¿Estás seguro de que deseas quitar esta encomienda del flete? Esta acción no se puede deshacer.
                </p>
              </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
              <button
                @click="showDeleteModal = false"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200"
              >
                Cancelar
              </button>
              <button
                @click="confirmDeleteEncomienda"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200"
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
      searchTerm: '',
      filteredEncomiendas: [],
      showAddModal: false,
      showDetailsModal: false,
      selectedEncomienda: null,
      loading: false,
      showDeleteModal: false,
      encomiendaToDelete: null,
      isEditingEncomienda: false,
      editingEncomiendaId: null,
      newEncomiendaForm: {
        codigo: '',
        descripcion: '',
        costo: '',
        estadoPago: '',
        observaciones: '',
        clienteRemitente: {
          tipoCliente: '',
          dni: '',
          nombre: '',
          telefono: ''
        },
        clienteDestinatario: {
          tipoCliente: '',
          dni: '',
          nombre: '',
          telefono: ''
        }
      }
    }
  },
  async mounted() {
    this.fleteId = this.$route.params.fleteId
    this.codigoFlete = this.$route.query.codigo || 'N/A'
    this.destinoFlete = this.$route.query.destino || 'Sin destino'
    
    await this.loadEncomiendas()
    this.generarCodigoEncomienda()
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
    
    removeFromFlete(encomiendaId) {
      this.encomiendaToDelete = encomiendaId
      this.showDeleteModal = true
    },
    
    async confirmDeleteEncomienda() {
      try {
        const encomienda = this.encomiendas.find(e => e.id === this.encomiendaToDelete)
      if (encomienda) {
          // Aquí se haría la llamada a la API para eliminar
        // Remover del flete
          this.encomiendas = this.encomiendas.filter(e => e.id !== this.encomiendaToDelete)
          this.filteredEncomiendas = this.filteredEncomiendas.filter(e => e.id !== this.encomiendaToDelete)
        
          this.showDeleteModal = false
          this.encomiendaToDelete = null
        this.$toast?.success('Encomienda removida del flete')
        }
      } catch (error) {
        console.error('Error al eliminar encomienda:', error)
        this.$toast?.error('Error al eliminar encomienda')
      }
    },
    
    verDetalles(encomienda) {
      this.selectedEncomienda = encomienda
      this.showDetailsModal = true
    },
    
    editEncomienda(encomienda) {
      this.isEditingEncomienda = true
      this.editingEncomiendaId = encomienda.id
      
      // Cargar datos de la encomienda en el formulario
      this.newEncomiendaForm = {
        codigo: encomienda.codigo || '',
        descripcion: encomienda.descripcion || '',
        costo: encomienda.costo?.toString() || '',
        estadoPago: encomienda.estadoPago || '',
        observaciones: encomienda.observaciones || '',
        clienteRemitente: {
          tipoCliente: encomienda.clienteRemitente?.tipoCliente || '',
          dni: encomienda.clienteRemitente?.dni || '',
          nombre: encomienda.clienteRemitente?.nombre || '',
          telefono: encomienda.clienteRemitente?.telefono || ''
        },
        clienteDestinatario: {
          tipoCliente: encomienda.clienteDestinatario?.tipoCliente || '',
          dni: encomienda.clienteDestinatario?.dni || '',
          nombre: encomienda.clienteDestinatario?.nombre || '',
          telefono: encomienda.clienteDestinatario?.telefono || ''
        }
      }
      
      this.showAddModal = true
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
    },
    
    generarCodigoEncomienda() {
      const now = new Date()
      const timestamp = now.getTime().toString().slice(-6)
      const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0')
      this.newEncomiendaForm.codigo = `ENC-${timestamp}-${random}`
    },
    
    getMaxLength(tipoCliente) {
      switch(tipoCliente) {
        case 'Persona Natural': return 8
        case 'Empresa': return 11
        case 'Extranjero': return 12
        default: return 12
      }
    },
    
    getHelpText(tipoCliente) {
      switch(tipoCliente) {
        case 'Persona Natural': return '8 dígitos'
        case 'Empresa': return '11 dígitos'
        case 'Extranjero': return '12 dígitos'
        default: return ''
      }
    },
    
    validarDNIRemitente() {
      const tipo = this.newEncomiendaForm.clienteRemitente.tipoCliente
      const dni = this.newEncomiendaForm.clienteRemitente.dni
      
      if (tipo && dni) {
        const maxLength = this.getMaxLength(tipo)
        if (dni.length !== maxLength && dni.length > 0) {
          console.warn(`El DNI debe tener ${maxLength} dígitos para ${tipo}`)
        }
      }
    },
    
    validarDNIDestinatario() {
      const tipo = this.newEncomiendaForm.clienteDestinatario.tipoCliente
      const dni = this.newEncomiendaForm.clienteDestinatario.dni
      
      if (tipo && dni) {
        const maxLength = this.getMaxLength(tipo)
        if (dni.length !== maxLength && dni.length > 0) {
          console.warn(`El DNI debe tener ${maxLength} dígitos para ${tipo}`)
        }
      }
    },
    
    async buscarClienteRemitente() {
      if (this.newEncomiendaForm.clienteRemitente.dni && this.newEncomiendaForm.clienteRemitente.dni.length >= 8) {
        try {
          const response = await axios.get(`/api/clientes/buscar/${this.newEncomiendaForm.clienteRemitente.dni}`)
          if (response.data) {
            this.newEncomiendaForm.clienteRemitente.nombre = response.data.nombre
            this.newEncomiendaForm.clienteRemitente.telefono = response.data.telefono
            this.$toast?.success('Cliente encontrado, datos cargados automáticamente')
          }
        } catch (error) {
          // Cliente no encontrado, continuar con formulario vacío
          console.log('Cliente no encontrado, se creará uno nuevo')
        }
      }
    },
    
    async buscarClienteDestinatario() {
      if (this.newEncomiendaForm.clienteDestinatario.dni && this.newEncomiendaForm.clienteDestinatario.dni.length >= 8) {
        try {
          const response = await axios.get(`/api/clientes/buscar/${this.newEncomiendaForm.clienteDestinatario.dni}`)
          if (response.data) {
            this.newEncomiendaForm.clienteDestinatario.nombre = response.data.nombre
            this.newEncomiendaForm.clienteDestinatario.telefono = response.data.telefono
            this.$toast?.success('Cliente encontrado, datos cargados automáticamente')
          }
        } catch (error) {
          // Cliente no encontrado, continuar con formulario vacío
          console.log('Cliente no encontrado, se creará uno nuevo')
        }
      }
    },
    
    async crearEncomienda() {
      this.loading = true
      try {
        // Preparar datos para crear encomienda
        const encomiendaData = {
          ...this.newEncomiendaForm,
          idFlete: this.fleteId
        }
        
        // Aquí se haría la llamada a la API
        console.log('Crear encomienda:', encomiendaData)
        
        // Simular creación exitosa
        const nuevaEncomienda = {
          id: Date.now(),
          codigo: this.newEncomiendaForm.codigo,
          descripcion: this.newEncomiendaForm.descripcion,
          costo: parseFloat(this.newEncomiendaForm.costo),
          estadoPago: this.newEncomiendaForm.estadoPago,
          observaciones: this.newEncomiendaForm.observaciones,
          estado: 'En origen',
          clienteRemitente: { 
            nombre: this.newEncomiendaForm.clienteRemitente.nombre,
            tipoCliente: this.newEncomiendaForm.clienteRemitente.tipoCliente,
            dni: this.newEncomiendaForm.clienteRemitente.dni,
            telefono: this.newEncomiendaForm.clienteRemitente.telefono
          },
          clienteDestinatario: { 
            nombre: this.newEncomiendaForm.clienteDestinatario.nombre,
            tipoCliente: this.newEncomiendaForm.clienteDestinatario.tipoCliente,
            dni: this.newEncomiendaForm.clienteDestinatario.dni,
            telefono: this.newEncomiendaForm.clienteDestinatario.telefono
          },
          created_at: new Date().toISOString().split('T')[0]
        }
        
        this.encomiendas.push(nuevaEncomienda)
        this.filteredEncomiendas.push(nuevaEncomienda)
        
        this.cancelarAgregarEncomienda()
        this.$toast?.success('Encomienda creada y agregada al flete')
      } catch (error) {
        console.error('Error al crear encomienda:', error)
        this.$toast?.error('Error al crear encomienda')
      } finally {
        this.loading = false
      }
    },
    
    async actualizarEncomienda() {
      this.loading = true
      try {
        // Preparar datos para actualizar encomienda
        const encomiendaData = {
          ...this.newEncomiendaForm,
          id: this.editingEncomiendaId
        }
        
        // Aquí se haría la llamada a la API
        console.log('Actualizar encomienda:', encomiendaData)
        
        // Actualizar encomienda en la lista
        const index = this.encomiendas.findIndex(e => e.id === this.editingEncomiendaId)
        if (index !== -1) {
          this.encomiendas[index] = {
            ...this.encomiendas[index],
            codigo: this.newEncomiendaForm.codigo,
            descripcion: this.newEncomiendaForm.descripcion,
            costo: parseFloat(this.newEncomiendaForm.costo),
            estadoPago: this.newEncomiendaForm.estadoPago,
            observaciones: this.newEncomiendaForm.observaciones,
            clienteRemitente: { 
              ...this.encomiendas[index].clienteRemitente,
              nombre: this.newEncomiendaForm.clienteRemitente.nombre,
              tipoCliente: this.newEncomiendaForm.clienteRemitente.tipoCliente,
              dni: this.newEncomiendaForm.clienteRemitente.dni,
              telefono: this.newEncomiendaForm.clienteRemitente.telefono
            },
            clienteDestinatario: { 
              ...this.encomiendas[index].clienteDestinatario,
              nombre: this.newEncomiendaForm.clienteDestinatario.nombre,
              tipoCliente: this.newEncomiendaForm.clienteDestinatario.tipoCliente,
              dni: this.newEncomiendaForm.clienteDestinatario.dni,
              telefono: this.newEncomiendaForm.clienteDestinatario.telefono
            }
          }
          
          // Actualizar también en filteredEncomiendas
          const filteredIndex = this.filteredEncomiendas.findIndex(e => e.id === this.editingEncomiendaId)
          if (filteredIndex !== -1) {
            this.filteredEncomiendas[filteredIndex] = this.encomiendas[index]
          }
        }
        
        this.cancelarAgregarEncomienda()
        this.$toast?.success('Encomienda actualizada correctamente')
      } catch (error) {
        console.error('Error al actualizar encomienda:', error)
        this.$toast?.error('Error al actualizar encomienda')
      } finally {
        this.loading = false
      }
    },
    
    cancelarAgregarEncomienda() {
      const wasEditing = this.isEditingEncomienda
      this.showAddModal = false
      this.isEditingEncomienda = false
      this.editingEncomiendaId = null
      this.newEncomiendaForm = {
        codigo: '',
        descripcion: '',
        costo: '',
        estadoPago: '',
        observaciones: '',
        clienteRemitente: {
          tipoCliente: '',
          dni: '',
          nombre: '',
          telefono: ''
        },
        clienteDestinatario: {
          tipoCliente: '',
          dni: '',
          nombre: '',
          telefono: ''
        }
      }
      if (!wasEditing) {
        this.generarCodigoEncomienda()
      }
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

/* Mejoras responsivas para modales */
@media (max-width: 640px) {
  .fixed.inset-0.flex {
    padding: 0.75rem !important;
    padding-top: 6rem !important;
    padding-bottom: 1rem !important;
    align-items: flex-start;
  }
  
  .max-w-2xl,
  .max-w-3xl,
  .max-w-md {
    max-width: calc(100% - 0.75rem);
    width: 100%;
    margin-top: 0 !important;
  }
}

/* Asegurar que el modal no quede detrás del header */
.fixed.inset-0 {
  z-index: 50;
}

/* Mejorar scroll en modales */
.overflow-y-auto {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 transparent;
}

.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 3px;
}
</style>
