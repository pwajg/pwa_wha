<template>
  <!-- Header completamente responsivo reutilizado de InicioPage -->
  <header class="header">
    <div class="header-content">
      <!-- Botón para mostrar sidebar en móviles -->
      <button
        v-if="showMenuToggle"
        @click="toggleSidebar"
        class="menu-toggle"
      >
        <i class="icon">☰</i>
      </button>

      <!-- Logo principal -->
      <span class="logo">WHAFREN</span>

      <!-- Botón de cambio de tema y perfil -->
      <div class="flex items-center gap-2">
        <ThemeToggle />

        <!-- Dropdown de perfil -->
        <div class="profile-dropdown">
          <button
            @click="toggleDropdown"
            class="profile-btn"
            :class="{ active: showDropdown }"
          >
            <div class="profile-avatar">
              {{ userInitial }}
            </div>
          </button>

          <!-- Menú desplegable -->
          <div
            v-if="showDropdown"
            ref="dropdownMenu"
            class="dropdown-menu"
            :style="{ display: 'block' }"
          >
            <div class="user-info">
              <div class="user-name">{{ userName }}</div>
              <div class="user-role">{{ userRole }}</div>
            </div>

            <div class="dropdown-divider"></div>

            <button
              @click="logout"
              class="dropdown-item logout-item"
            >
              <svg
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                />
              </svg>
              Cerrar Sesión
            </button>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>


<script>
import axios from 'axios'
import ThemeToggle from './ThemeToggle.vue'

export default {
  name: 'HeaderAdmin',
  components: {
    ThemeToggle
  },
  data() {
    return {
      showDropdown: false,
      showMenuToggle: false
    }
  },
  computed: {
    userName() {
      const usuario = localStorage.getItem('usuario');
      if (usuario) {
        const userData = JSON.parse(usuario);
        return userData.nombre || userData.email;
      }
      return 'Usuario';
    },
    userRole() {
      const usuario = localStorage.getItem('usuario');
      if (usuario) {
        const userData = JSON.parse(usuario);
        return userData.rol;
      }
      return 'Usuario';
    },
    userInitial() {
      return this.userName.charAt(0).toUpperCase();
    }
  },
  methods: {
    toggleDropdown() {
      console.log('Toggle dropdown clicked, current state:', this.showDropdown);
      this.showDropdown = !this.showDropdown;
      console.log('New state:', this.showDropdown);
      
      // Debug adicional
      this.$nextTick(() => {
        if (this.$refs.dropdownMenu) {
          console.log('Dropdown element found:', this.$refs.dropdownMenu);
          console.log('Dropdown styles:', window.getComputedStyle(this.$refs.dropdownMenu));
        } else {
          console.log('Dropdown element NOT found');
        }
      });
    },
    toggleSidebar() {
      this.$emit('toggle-sidebar');
    },
    async logout() {
      try {
        await axios.post('/logout');
      } catch (error) {
        console.log('Error en logout:', error);
      } finally {
        localStorage.removeItem('usuario');
        localStorage.removeItem('token');
        this.$router.push('/login');
      }
    }
  },
  mounted() {
    // Mostrar botón de menú en móviles
    this.showMenuToggle = window.innerWidth < 1024;
    
    // Manejar cambios de tamaño de ventana
    window.addEventListener('resize', () => {
      this.showMenuToggle = window.innerWidth < 1024;
    });
    
    // Cerrar dropdown al hacer click fuera
    this.clickOutsideHandler = (e) => {
      const profileDropdown = document.querySelector('.profile-dropdown');
      if (profileDropdown && !profileDropdown.contains(e.target)) {
        this.showDropdown = false;
      }
    };
    
    setTimeout(() => {
      document.addEventListener('click', this.clickOutsideHandler);
    }, 100);
  },
  
  beforeUnmount() {
    // Limpiar event listener
    if (this.clickOutsideHandler) {
      document.removeEventListener('click', this.clickOutsideHandler);
    }
  }
}
</script>

<style scoped>
/* Header completamente responsivo reutilizado de InicioPage */
.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  height: 4rem; /* Altura fija por defecto */
  background-color: var(--color-surface);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  box-sizing: border-box;
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.dark .header {
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1rem;
  max-width: 100%;
  height: 4rem; /* Altura fija para que el sidebar se alinee correctamente */
  box-sizing: border-box;
}

.logo {
  font-size: 1.25rem;
  font-weight: 800;
  color: #1d4ed8;
  letter-spacing: 0.1em;
  user-select: none;
  transition: color 0.3s ease;
}

.dark .logo {
  color: #60a5fa;
}

/* Botón de menú para móviles */
.menu-toggle {
  background: none;
  border: 1px solid #e5e7eb;
  border-radius: 0.375rem;
  padding: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.dark .menu-toggle {
  border-color: #4b5563;
}

.menu-toggle:hover {
  background: #f9fafb;
  border-color: #3b82f6;
}

.dark .menu-toggle:hover {
  background: #374151;
  border-color: #60a5fa;
}

.menu-toggle:focus,
.menu-toggle:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

.menu-toggle:active {
  transform: scale(0.95);
}

.menu-toggle .icon {
  font-size: 1.25rem;
  color: #6b7280;
  transition: color 0.3s ease;
}

.dark .menu-toggle .icon {
  color: #d1d5db;
}

/* Profile dropdown styles */
.profile-dropdown {
  position: relative;
  z-index: 1000;
}

.profile-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  transition: all 0.2s ease;
}

.profile-btn:hover {
  background-color: rgba(59, 130, 246, 0.1);
}

.profile-btn.active {
  background-color: rgba(59, 130, 246, 0.2);
}

.profile-avatar {
  width: 2.5rem;
  height: 2.5rem;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 1rem;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.dropdown-menu {
  position: absolute !important;
  top: calc(100% + 0.5rem) !important;
  right: 0 !important;
  background: white !important;
  border-radius: 0.5rem !important;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
  min-width: 200px !important;
  overflow: visible !important;
  z-index: 99999 !important;
  border: 1px solid #e5e7eb !important;
  opacity: 1 !important;
  visibility: visible !important;
  transform: translateY(0) !important;
  display: block !important;
  width: 200px !important;
  height: auto !important;
  transition: all 0.2s ease !important;
}

.dark .dropdown-menu {
  background: #1f2937 !important;
  border-color: #4b5563 !important;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3) !important;
}

@keyframes dropdownFadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.user-info {
  padding: 1rem;
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  transition: background 0.3s ease;
}

.dark .user-info {
  background: linear-gradient(135deg, #374151, #4b5563);
}

.user-name {
  font-weight: 600;
  color: #1f2937;
  font-size: 0.9rem;
  transition: color 0.3s ease;
}

.dark .user-name {
  color: #f9fafb;
}

.user-role {
  font-size: 0.8rem;
  color: #6b7280;
  margin-top: 0.25rem;
  transition: color 0.3s ease;
}

.dark .user-role {
  color: #d1d5db;
}

.dropdown-divider {
  height: 1px;
  background: #e5e7eb;
  transition: background-color 0.3s ease;
}

.dark .dropdown-divider {
  background: #4b5563;
}

.dropdown-item {
  width: 100%;
  padding: 0.75rem 1rem;
  background: none;
  border: none;
  text-align: left;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
  color: #374151;
  transition: background-color 0.2s ease, color 0.2s ease;
}

.dark .dropdown-item {
  color: #e5e7eb;
}

.dropdown-item:hover {
  background-color: #f9fafb;
}

.dark .dropdown-item:hover {
  background-color: #374151;
}

.dropdown-item:focus {
  outline: none;
  background-color: #f3f4f6;
}

.dark .dropdown-item:focus {
  background-color: #4b5563;
}

.dropdown-item:active {
  background-color: #e5e7eb;
}

.dark .dropdown-item:active {
  background-color: #1f2937;
}

.logout-item {
  color: #dc2626;
}

.dark .logout-item {
  color: #f87171;
}

.logout-item:hover {
  background-color: #fef2f2;
}

.dark .logout-item:hover {
  background-color: #7f1d1d;
}

.icon {
  font-size: 1rem;
}

.w-4 {
  width: 1rem;
}

.h-4 {
  height: 1rem;
}

/* Responsive adjustments */
@media (max-width: 480px) {
  .header {
    height: 3rem; /* Altura fija del header en móviles pequeños */
  }
  
  .header-content {
    padding: 0.5rem 0.75rem;
    height: 3rem; /* Altura fija para alineación */
  }
  
  .logo {
    font-size: 1rem;
  }
  
  .profile-avatar {
    width: 2rem;
    height: 2rem;
    font-size: 0.875rem;
  }
  
  .dropdown-menu {
    min-width: 180px;
  }
}

@media (min-width: 481px) and (max-width: 768px) {
  .header {
    height: 4rem; /* Altura fija del header */
  }
  
  .header-content {
    padding: 0.75rem 1rem;
    height: 4rem; /* Altura fija para alineación */
  }
  
  .logo {
    font-size: 1.125rem;
  }
}

@media (min-width: 769px) and (max-width: 1024px) {
  .header {
    height: 5rem; /* Altura fija del header en tablets grandes */
  }
  
  .header-content {
    padding: 1rem 1.5rem;
    height: 5rem; /* Altura fija para alineación */
  }
  
  .logo {
    font-size: 1.5rem;
  }
}

@media (min-width: 1025px) {
  .header {
    height: 4rem; /* Altura fija del header en desktop */
  }
  
  .header-content {
    padding: 1rem 2rem;
    height: 4rem; /* Altura fija para alineación */
  }
  
  .logo {
    font-size: 1.75rem;
  }
}
</style>
