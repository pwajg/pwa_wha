<template>
  <!-- Sidebar posicionado debajo del header -->
  <aside class="sidebar" :class="{ 'open': isOpen, 'collapsed': isCollapsed }">
    <!-- Botón para colapsar/expandir -->
    <button @click="toggleCollapse" class="collapse-btn">
      <i class="icon">{{ isCollapsed ? '▶' : '◀' }}</i>
    </button>
    
    <!-- Contenido del sidebar -->
    <div class="sidebar-content">
      <div class="sidebar-header">
        <h2 v-if="!isCollapsed">Panel Admin</h2>
        <div v-else class="collapsed-header">
          <span class="collapsed-logo">W</span>
        </div>
      </div>
      
      <nav class="sidebar-nav">
        <ul class="nav-list">
          <li class="nav-item">
            <router-link to="/admin" class="nav-link" :class="{ 'collapsed': isCollapsed }" @click="closeSidebar">
              <i class="nav-icon">📊</i>
              <span class="nav-text" v-if="!isCollapsed">Dashboard</span>
              <span class="nav-tooltip" v-if="isCollapsed">Dashboard</span>
            </router-link>
          </li>
          
          <li class="nav-item">
            <router-link to="/admin/fletes" class="nav-link" :class="{ 'collapsed': isCollapsed }" @click="closeSidebar">
              <i class="nav-icon">🚚</i>
              <span class="nav-text" v-if="!isCollapsed">Fletes y Envíos</span>
              <span class="nav-tooltip" v-if="isCollapsed">Fletes</span>
            </router-link>
          </li>
          
          <li class="nav-item">
            <router-link to="/admin/usuarios" class="nav-link" :class="{ 'collapsed': isCollapsed }" @click="closeSidebar">
              <i class="nav-icon">👥</i>
              <span class="nav-text" v-if="!isCollapsed">Usuarios</span>
              <span class="nav-tooltip" v-if="isCollapsed">Usuarios</span>
            </router-link>
          </li>
          
          <li class="nav-item">
            <router-link to="/admin/sucursales" class="nav-link" :class="{ 'collapsed': isCollapsed }" @click="closeSidebar">
              <i class="nav-icon">🏢</i>
              <span class="nav-text" v-if="!isCollapsed">Sucursales</span>
              <span class="nav-tooltip" v-if="isCollapsed">Sucursales</span>
            </router-link>
          </li>
          
          <li class="nav-item">
            <router-link to="/admin/reportes" class="nav-link" :class="{ 'collapsed': isCollapsed }" @click="closeSidebar">
              <i class="nav-icon">📈</i>
              <span class="nav-text" v-if="!isCollapsed">Reportes y Estadísticas</span>
              <span class="nav-tooltip" v-if="isCollapsed">Reportes</span>
            </router-link>
          </li>
          
          <li class="nav-item">
            <router-link to="/admin/configuracion" class="nav-link" :class="{ 'collapsed': isCollapsed }" @click="closeSidebar">
              <i class="nav-icon">⚙️</i>
              <span class="nav-text" v-if="!isCollapsed">Configuración del Sistema</span>
              <span class="nav-tooltip" v-if="isCollapsed">Configuración</span>
            </router-link>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
</template>

<script>
export default {
  name: 'SidebarAdmin',
  data() {
    return {
      isOpen: false,
      isCollapsed: false
    }
  },
  methods: {
    toggleSidebar() {
      this.isOpen = !this.isOpen;
      this.emitToggle();
    },
    closeSidebar() {
      this.isOpen = false;
      this.emitToggle();
    },
    toggleCollapse() {
      this.isCollapsed = !this.isCollapsed;
      this.emitToggle();
    },
    emitToggle() {
      this.$emit('sidebar-toggle', {
        isOpen: this.isOpen,
        isCollapsed: this.isCollapsed
      });
    }
  },
  mounted() {
    // En móviles, el sidebar debe estar oculto por defecto
    if (window.innerWidth < 1024) {
      this.isCollapsed = true;
    }
    
    // Emitir estado inicial
    this.emitToggle();
    
    // Cerrar sidebar con tecla Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.closeSidebar();
      }
    });
    
    // Manejar cambios de tamaño de ventana
    window.addEventListener('resize', () => {
      if (window.innerWidth < 1024) {
        this.isCollapsed = true;
        this.emitToggle();
      }
    });
  }
}
</script>

<style scoped>
/* Sidebar posicionado debajo del header */
.sidebar {
  position: fixed;
  top: 4rem; /* Debajo del header */
  left: 0;
  width: 250px;
  height: calc(100vh - 4rem); /* Altura completa menos el header */
  background: white;
  border-right: 1px solid #e5e7eb;
  z-index: 999;
  transition: all 0.3s ease;
  overflow-y: auto;
}

/* Estado colapsado */
.sidebar.collapsed {
  width: 60px;
}

/* Botón para colapsar/expandir */
.collapse-btn {
  position: absolute;
  top: 1rem;
  right: -15px;
  width: 30px;
  height: 30px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease;
  z-index: 1000;
}

.collapse-btn:hover {
  background: #f9fafb;
  transform: scale(1.1);
}

.icon {
  font-size: 0.875rem;
  color: #6b7280;
}

/* Contenido del sidebar */
.sidebar-content {
  height: 100%;
  display: flex;
  flex-direction: column;
}

/* Sidebar header */
.sidebar-header {
  padding: 1.5rem 1rem;
  border-bottom: 1px solid #e5e7eb;
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  display: flex;
  align-items: center;
  justify-content: center;
}

.sidebar-header h2 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
}

.collapsed-header {
  display: flex;
  align-items: center;
  justify-content: center;
}

.collapsed-logo {
  font-size: 1.5rem;
  font-weight: 800;
  color: #1d4ed8;
  background: white;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(29, 78, 216, 0.3);
}

/* Navigation */
.sidebar-nav {
  flex: 1;
  padding: 1rem 0;
}

.nav-list {
  list-style: none;
  margin: 0;
  padding: 0;
}

.nav-item {
  margin: 0;
}

.nav-link {
  display: flex;
  align-items: center;
  padding: 0.875rem 1.5rem;
  color: #374151;
  text-decoration: none;
  transition: all 0.2s ease;
  border-left: 3px solid transparent;
  position: relative;
}

.nav-link.collapsed {
  padding: 0.875rem 0.75rem;
  justify-content: center;
}

.nav-link:hover {
  background: #f9fafb;
  color: #1f2937;
}

.nav-link.router-link-active {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
  border-left-color: #3b82f6;
  font-weight: 500;
}

.nav-icon {
  font-size: 1.25rem;
  margin-right: 0.75rem;
  width: 1.5rem;
  text-align: center;
  flex-shrink: 0;
}

.nav-link.collapsed .nav-icon {
  margin-right: 0;
}

.nav-text {
  font-size: 0.9rem;
  font-weight: 500;
}

.nav-tooltip {
  position: absolute;
  left: 100%;
  top: 50%;
  transform: translateY(-50%);
  background: #1f2937;
  color: white;
  padding: 0.5rem 0.75rem;
  border-radius: 0.375rem;
  font-size: 0.8rem;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.2s ease;
  z-index: 1000;
  margin-left: 0.5rem;
}

.nav-tooltip::before {
  content: '';
  position: absolute;
  right: 100%;
  top: 50%;
  transform: translateY(-50%);
  border: 5px solid transparent;
  border-right-color: #1f2937;
}

.nav-link.collapsed:hover .nav-tooltip {
  opacity: 1;
}

/* Responsive adjustments */
@media (max-width: 1023px) {
  .sidebar {
    top: 3.5rem;
    height: calc(100vh - 3.5rem);
    transform: translateX(-100%);
    width: 280px;
  }
  
  .sidebar.open {
    transform: translateX(0);
  }
  
  .sidebar.collapsed {
    width: 60px;
  }
  
  .collapse-btn {
    display: none; /* Ocultar botón de colapsar en móviles */
  }
}

@media (max-width: 480px) {
  .sidebar {
    top: 3rem;
    height: calc(100vh - 3rem);
    width: 260px;
  }
  
  .sidebar-header {
    padding: 1rem 0.75rem;
  }
  
  .sidebar-header h2 {
    font-size: 1.125rem;
  }
  
  .nav-link {
    padding: 0.75rem 1rem;
  }
  
  .nav-text {
    font-size: 0.875rem;
  }
}

@media (min-width: 481px) and (max-width: 768px) {
  .sidebar {
    top: 4rem;
    height: calc(100vh - 4rem);
    width: 300px;
  }
}

@media (min-width: 769px) and (max-width: 1023px) {
  .sidebar {
    top: 5rem;
    height: calc(100vh - 5rem);
    width: 320px;
  }
}

@media (min-width: 1024px) {
  .sidebar {
    top: 4rem;
    height: calc(100vh - 4rem);
    width: 250px;
    transform: translateX(0);
  }
  
  .sidebar.collapsed {
    width: 60px;
  }
}

/* Animaciones */
.nav-link {
  animation: slideInLeft 0.3s ease-out;
}

@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Scrollbar personalizado */
.sidebar::-webkit-scrollbar {
  width: 6px;
}

.sidebar::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.sidebar::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
