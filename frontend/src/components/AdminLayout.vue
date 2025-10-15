<template>
  <div class="admin-layout">
    <!-- Header reutilizado de InicioPage con icono de perfil -->
    <HeaderAdmin @toggle-sidebar="handleHeaderToggle" />
    
    <!-- Menú lateral desplegable -->
    <SidebarAdmin ref="sidebar" @sidebar-toggle="handleSidebarToggle" />
    
    <!-- Contenido principal -->
    <main class="main-content" :class="{ 'sidebar-open': sidebarOpen, 'sidebar-collapsed': sidebarCollapsed }">
      <div class="content-wrapper" :key="$route.path">
        <slot></slot>
      </div>
    </main>
  </div>
</template>

<script>
import HeaderAdmin from './HeaderAdmin.vue'
import SidebarAdmin from './SidebarAdmin.vue'

export default {
  name: 'AdminLayout',
  components: {
    HeaderAdmin,
    SidebarAdmin
  },
  data() {
    return {
      sidebarOpen: false,
      sidebarCollapsed: false
    }
  },
  methods: {
    handleSidebarToggle(data) {
      this.sidebarOpen = data.isOpen;
      this.sidebarCollapsed = data.isCollapsed;
    },
    handleHeaderToggle() {
      // Toggle del sidebar desde el header
      if (this.$refs.sidebar) {
        this.$refs.sidebar.toggleSidebar();
      }
    }
  },
  mounted() {
    // El sidebar ya emite eventos directamente al componente padre
    // No necesitamos usar $on en Vue 3
  }
}
</script>

<style scoped>
/* Layout principal */
.admin-layout {
  min-height: 100vh;
  background-color: #f8fafc;
}

/* Contenido principal */
.main-content {
  margin-top: 4rem;
  padding: 2rem 1rem;
  transition: all 0.3s ease;
  margin-left: 0;
}

/* Desktop: sidebar expandido */
@media (min-width: 1024px) {
  .main-content {
    margin-top: 0;
    padding: 2rem;
    margin-left: 250px; /* Sidebar expandido */
  }
  
  .main-content.sidebar-collapsed {
    margin-left: 60px; /* Sidebar colapsado */
  }
}

/* Contenedor del contenido */
.content-wrapper {
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

/* Responsive adjustments */
@media (max-width: 480px) {
  .main-content {
    margin-top: 3.5rem;
    padding: 1rem 0.75rem;
  }
}

@media (min-width: 481px) and (max-width: 768px) {
  .main-content {
    margin-top: 4rem;
    padding: 1.5rem 1rem;
  }
}

@media (min-width: 769px) and (max-width: 1023px) {
  .main-content {
    margin-top: 5rem;
    padding: 2rem 1.5rem;
  }
}

@media (min-width: 1024px) {
  .main-content {
    margin-top: 0;
    padding: 2rem;
    margin-left: 250px;
  }
  
  .main-content.sidebar-collapsed {
    margin-left: 60px;
  }
}
</style>
