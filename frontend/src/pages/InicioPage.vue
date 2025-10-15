<template>
  <!-- Estructura principal completamente responsiva -->
  <div class="page-container">
    <!-- Header completamente responsivo -->
    <header class="header">
      <div class="header-content">
        <span class="logo">WAFREN</span>
        <button @click="$router.push('/login')" class="login-btn">
          Iniciar Sesión
        </button>
      </div>
    </header>

    <!-- Contenido principal completamente responsivo -->
    <main class="main-content">
      <div class="content-wrapper">
        <h1 class="title">
          Bienvenido(a) a Whafren S.A.C
        </h1>
        
        <!-- Buscador completamente responsivo -->
        <div class="search-container">
          <label for="codigo" class="search-label text-base sm:text-lg md:text-xl">
            Ingrese código de encomienda
          </label>
          <div class="search-form">
            <input
              type="text"
              placeholder="Ej. ENC-251008-001"
              class="search-input"
              v-model="codigo"
              @keyup.enter="buscarEncomienda"
            />
            <button 
              @click="buscarEncomienda" 
              class="search-btn"
              :disabled="buscando"
            >
              <span v-if="buscando">Buscando...</span>
              <span v-else>Buscar</span>
            </button>
          </div>
        </div>

        <!-- Tabla de estado de encomienda completamente responsiva -->
        <div v-if="showTable" class="table-container">
          <div class="table-wrapper">
            <table class="data-table">
              <thead class="table-header">
                <tr>
                  <th class="table-cell-header">Código de encomienda</th>
                  <th class="table-cell-header">Estado de encomienda</th>
                </tr>
              </thead>
              <tbody class="table-body">
                <tr v-if="encomiendaEncontrada && datosEncomienda" class="table-row">
                  <td class="table-cell">{{ datosEncomienda.codigo }}</td>
                  <td class="table-cell">
                    <div class="estado-info">
                      <span class="estado-actual">{{ datosEncomienda.estadoActual }}</span>
                      <small class="estado-fecha" v-if="datosEncomienda.fechaUltimoEstado">
                        {{ new Date(datosEncomienda.fechaUltimoEstado).toLocaleDateString('es-ES') }}
                      </small>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="error" class="table-row">
                  <td class="table-cell" colspan="2">
                    <div class="error-message">
                      <i class="icon">⚠️</i>
                      <span>{{ error }}</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="buscando" class="table-row">
                  <td class="table-cell" colspan="2">
                    <div class="loading-message">
                      <i class="icon">⏳</i>
                      <span>Buscando encomienda...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else class="table-row">
                  <td class="table-cell" colspan="2">
                    <div class="no-encomienda">
                      <i class="icon">❌</i>
                      <span>No se encontró la encomienda</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer completamente responsivo -->
    <footer class="footer">
      <div class="footer-content">
        <div class="footer-text">
          © {{ new Date().getFullYear() }} Whafren S.A.C. Todos los derechos reservados.
        </div>
      </div>
    </footer>
  </div>
</template>
  
<script>
import axios from 'axios'

  export default {
  name: 'Inicio',
  data() {
    return {
      codigo: '',
      showTable: false,
      encomiendaEncontrada: false,
      estadoEncomienda: '',
      buscando: false,
      datosEncomienda: null,
      error: null
    }
  },
  methods: {
    async buscarEncomienda() {
      if (!this.codigo.trim()) {
        this.showTable = false;
        return;
      }

      this.buscando = true;
      this.showTable = true;
      this.encomiendaEncontrada = false;
      this.error = null;
      this.datosEncomienda = null;

      try {
        // Llamada real a la API
        const response = await axios.get(`/encomiendas/buscar/${encodeURIComponent(this.codigo.trim())}`);
        
        if (response.data.encontrada) {
          this.encomiendaEncontrada = true;
          this.estadoEncomienda = response.data.data.estadoActual;
          this.datosEncomienda = response.data.data;
        } else {
          this.encomiendaEncontrada = false;
        }
      } catch (error) {
        console.error('Error al buscar encomienda:', error);
        
        if (error.response && error.response.status === 404) {
          // Encomienda no encontrada
          this.encomiendaEncontrada = false;
        } else {
          // Error del servidor
          this.error = 'Error al conectar con el servidor. Intente nuevamente.';
          this.encomiendaEncontrada = false;
        }
      } finally {
        this.buscando = false;
      }
    },

    // Método para limpiar búsqueda
    limpiarBusqueda() {
      this.codigo = '';
      this.showTable = false;
      this.encomiendaEncontrada = false;
      this.estadoEncomienda = '';
      this.datosEncomienda = null;
      this.error = null;
    }
  }
  }
</script>

<style scoped> 
/* Reset y configuración base */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

/* Contenedor principal con CSS Grid */
.page-container {
  min-height: 100vh;
  display: grid;
  grid-template-rows: auto 1fr auto;
  background-color: #ffffff;
  font-family: system-ui, -apple-system, sans-serif;
}

/* HEADER COMPLETAMENTE RESPONSIVO */
.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  background-color: #ffffff;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 1rem;
  max-width: 100%;
}

.logo {
  font-size: 1.25rem;
  font-weight: 800;
  color: #1d4ed8;
  letter-spacing: 0.1em;
  user-select: none;
}

.login-btn {
  background-color: #3b82f6;
  color: white;
  border: none;
  border-radius: 0.375rem;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.login-btn:hover {
  background-color: #2563eb;
  transform: translateY(-1px);
}

/* MAIN CONTENT COMPLETAMENTE RESPONSIVO */
.main-content {
  margin-top: 4rem;
  margin-bottom: 3rem;
  padding: 1rem;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 7rem);
}

.content-wrapper {
  width: 100%;
  max-width: 1200px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2rem;
}

.title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  text-align: center;
  line-height: 1.2;
}

/* SEARCH CONTAINER COMPLETAMENTE RESPONSIVO */
.search-container {
  width: 100%;
  max-width: 500px;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.search-label {
  font-size: 1rem;
  font-weight: 600;
  color: #374151;
  text-align: center;
  display: block;
  margin-bottom: 0;
}

.search-form {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.search-input {
  width: 100%;
  padding: 0.875rem;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: all 0.2s ease;
}

.search-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-btn {
  background-color: #3b82f6;
  color: white;
  border: none;
  border-radius: 0.5rem;
  padding: 0.875rem 1.5rem;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.search-btn:hover {
  background-color: #2563eb;
  transform: translateY(-1px);
}

.search-btn:disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
  transform: none;
}

.search-btn:disabled:hover {
  background-color: #9ca3af;
  transform: none;
}

/* TABLE CONTAINER COMPLETAMENTE RESPONSIVO */
.table-container {
  width: 100%;
  max-width: 800px;
}

.table-wrapper {
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.table-header {
  background-color: #f9fafb;
}

.table-cell-header {
  padding: 1rem;
  text-align: left;
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.table-body {
  background-color: white;
}

.table-row {
  border-bottom: 1px solid #e5e7eb;
}

.table-row:last-child {
  border-bottom: none;
}

.table-cell {
  padding: 1rem;
  font-size: 0.875rem;
  color: #6b7280;
}

.no-encomienda {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  color: #dc2626;
  font-weight: 500;
}

.no-encomienda .icon {
  font-size: 1.25rem;
}

.estado-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.estado-actual {
  font-weight: 600;
  color: #059669;
}

.estado-fecha {
  color: #6b7280;
  font-size: 0.75rem;
}

.error-message {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  color: #dc2626;
  font-weight: 500;
}

.error-message .icon {
  font-size: 1.25rem;
}

.loading-message {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  color: #3b82f6;
  font-weight: 500;
}

.loading-message .icon {
  font-size: 1.25rem;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* FOOTER COMPLETAMENTE RESPONSIVO */
.footer {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  width: 100%;
  background-color: #f9fafb;
  border-top: 1px solid #e5e7eb;
  z-index: 1000;
}

.footer-content {
  padding: 0.75rem 1rem;
  text-align: center;
}

.footer-text {
  font-size: 0.75rem;
  color: #6b7280;
}

/* BREAKPOINTS RESPONSIVOS */

/* Móvil pequeño (320px - 480px) */
@media (max-width: 480px) {
  .header-content {
    padding: 0.5rem 0.75rem;
  }
  
  .logo {
    font-size: 1rem;
  }
  
  .login-btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
  }
  
  .main-content {
    margin-top: 3.5rem;
    margin-bottom: 2.5rem;
    padding: 0.75rem;
  }
  
  .title {
    font-size: 1.25rem;
  }
  
  .content-wrapper {
    gap: 1.5rem;
  }
  
  .search-container {
    gap: 1rem;
  }
  
  .search-label {
    font-size: 0.875rem;
  }
  
  .search-input {
    padding: 0.75rem;
    font-size: 0.875rem;
  }
  
  .search-btn {
    padding: 0.75rem 1.25rem;
    font-size: 0.875rem;
  }
  
  .table-cell-header,
  .table-cell {
    padding: 0.75rem 0.5rem;
    font-size: 0.75rem;
  }
  
  .no-encomienda {
    font-size: 0.75rem;
  }
  
  .no-encomienda .icon {
    font-size: 1rem;
  }
  
  .estado-info {
    gap: 0.125rem;
  }
  
  .estado-fecha {
    font-size: 0.625rem;
  }
  
  .error-message .icon {
    font-size: 1rem;
  }
  
  .footer-content {
    padding: 0.5rem 0.75rem;
  }
  
  .footer-text {
    font-size: 0.625rem;
  }
}

/* Móvil (481px - 768px) */
@media (min-width: 481px) and (max-width: 768px) {
  .header-content {
    padding: 0.75rem 1rem;
  }
  
  .logo {
    font-size: 1.125rem;
  }
  
  .login-btn {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
  }
  
  .main-content {
    margin-top: 4rem;
    margin-bottom: 3rem;
    padding: 1rem;
  }
  
  .title {
    font-size: 1.375rem;
  }
  
  .search-container {
    gap: 1.25rem;
  }
  
  .search-label {
    font-size: 1.125rem;
  }
  
  .search-form {
    flex-direction: row;
    gap: 0.5rem;
  }
  
  .search-btn {
    flex-shrink: 0;
    white-space: nowrap;
  }
}

/* Tablet (769px - 1024px) */
@media (min-width: 769px) and (max-width: 1024px) {
  .header-content {
    padding: 1rem 1.5rem;
  }
  
  .logo {
    font-size: 1.5rem;
  }
  
  .login-btn {
    padding: 0.625rem 1.25rem;
    font-size: 1rem;
  }
  
  .main-content {
    margin-top: 5rem;
    margin-bottom: 4rem;
    padding: 1.5rem;
  }
  
  .title {
    font-size: 1.75rem;
  }
  
  .content-wrapper {
    gap: 2.5rem;
  }
  
  .search-container {
    gap: 1.75rem;
  }
  
  .search-label {
    font-size: 1.25rem;
  }
  
  .search-form {
    flex-direction: row;
    gap: 0.75rem;
  }
  
  .table-cell-header,
  .table-cell {
    padding: 1.25rem;
    font-size: 1rem;
  }
}

/* Desktop (1025px+) */
@media (min-width: 1025px) {
  .header-content {
    padding: 1rem 2rem;
  }
  
  .logo {
    font-size: 1.75rem;
  }
  
  .login-btn {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
  }
  
  .main-content {
    margin-top: 5rem;
    margin-bottom: 4rem;
    padding: 2rem;
  }
  
  .title {
    font-size: 2rem;
  }
  
  .content-wrapper {
    gap: 3rem;
  }
  
  .search-container {
    gap: 2rem;
  }
  
  .search-label {
    font-size: 1.5rem;
  }
  
  .search-form {
    flex-direction: row;
    gap: 1rem;
  }
  
  .search-input {
    padding: 1rem;
    font-size: 1.125rem;
  }
  
  .search-btn {
    padding: 1rem 2rem;
    font-size: 1.125rem;
  }
  
  .table-cell-header,
  .table-cell {
    padding: 1.5rem;
    font-size: 1.125rem;
  }
}

/* Pantallas muy grandes (1440px+) */
@media (min-width: 1440px) {
  .content-wrapper {
    max-width: 1400px;
  }
  
  .title {
    font-size: 2.25rem;
  }
  
  .search-container {
    max-width: 600px;
    gap: 2.25rem;
  }
  
  .search-label {
    font-size: 1.75rem;
  }
  
  .table-container {
    max-width: 1000px;
  }
}

/* Orientación landscape en móviles */
@media (max-width: 768px) and (orientation: landscape) {
  .main-content {
    margin-top: 3rem;
    margin-bottom: 2.5rem;
    padding: 0.75rem;
  }
  
  .content-wrapper {
    gap: 1rem;
  }
  
  .title {
    font-size: 1.25rem;
  }
}
</style>