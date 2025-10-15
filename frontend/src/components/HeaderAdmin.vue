<template>
  <!-- Header completamente responsivo reutilizado de InicioPage -->
  <header class="header">
    <div class="header-content">
      <!-- Botón para mostrar sidebar en móviles -->
      <button @click="toggleSidebar" class="menu-toggle" v-if="showMenuToggle">
        <i class="icon">☰</i>
      </button>
      
      <span class="logo">WAFREN</span>
      
      <!-- Icono de perfil con dropdown -->
      <div class="profile-dropdown">
        <button @click="toggleDropdown" class="profile-btn" :class="{ 'active': showDropdown }">
          <div class="profile-avatar">
            {{ userInitial }}
          </div>
        </button>
        
        <!-- Dropdown menu -->
        <div v-show="showDropdown" class="dropdown-menu">
          <div class="user-info">
            <div class="user-name">{{ userName }}</div>
            <div class="user-role">{{ userRole }}</div>
          </div>
          <div class="dropdown-divider"></div>
          <button @click="logout" class="dropdown-item logout-item">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            Cerrar Sesión
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import axios from 'axios'

export default {
  name: 'HeaderAdmin',
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
    setTimeout(() => {
      document.addEventListener('click', (e) => {
        if (!this.$el.contains(e.target)) {
          this.showDropdown = false;
        }
      });
    }, 100);
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
  background-color: #ffffff;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1rem;
  max-width: 100%;
  min-height: 4rem;
}

.logo {
  font-size: 1.25rem;
  font-weight: 800;
  color: #1d4ed8;
  letter-spacing: 0.1em;
  user-select: none;
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

.menu-toggle:hover {
  background: #f9fafb;
  border-color: #3b82f6;
}

.menu-toggle .icon {
  font-size: 1.25rem;
  color: #6b7280;
}

/* Profile dropdown styles */
.profile-dropdown {
  position: relative;
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
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 0.5rem;
  background: white;
  border-radius: 0.5rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  min-width: 200px;
  overflow: hidden;
  animation: dropdownFadeIn 0.2s ease-out;
  z-index: 1001;
  border: 1px solid #e5e7eb;
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
}

.user-name {
  font-weight: 600;
  color: #1f2937;
  font-size: 0.9rem;
}

.user-role {
  font-size: 0.8rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.dropdown-divider {
  height: 1px;
  background: #e5e7eb;
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
  transition: background-color 0.2s ease;
}

.dropdown-item:hover {
  background-color: #f9fafb;
}

.logout-item {
  color: #dc2626;
}

.logout-item:hover {
  background-color: #fef2f2;
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
  .header-content {
    padding: 0.5rem 0.75rem;
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
  .header-content {
    padding: 0.75rem 1rem;
  }
  
  .logo {
    font-size: 1.125rem;
  }
}

@media (min-width: 769px) and (max-width: 1024px) {
  .header-content {
    padding: 1rem 1.5rem;
  }
  
  .logo {
    font-size: 1.5rem;
  }
}

@media (min-width: 1025px) {
  .header-content {
    padding: 1rem 2rem;
  }
  
  .logo {
    font-size: 1.75rem;
  }
}
</style>
