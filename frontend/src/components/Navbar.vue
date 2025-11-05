<template>
  <nav class="navbar">
    <div class="navbar-brand">
      <h2>Sistema de Encomiendas</h2>
    </div>
    <div class="navbar-menu">
      <div class="navbar-items">
        <!-- Items comunes para ambos roles -->
        <router-link to="/home" class="navbar-item" :class="{ active: $route.path === '/home' }">
          <i class="icon">🏠</i>
          Inicio
        </router-link>
        
        <router-link to="/encomiendas" class="navbar-item" :class="{ active: $route.path === '/encomiendas' }">
          <i class="icon">📦</i>
          Encomiendas
        </router-link>
        
        <router-link to="/crear-encomienda" class="navbar-item" :class="{ active: $route.path === '/crear-encomienda' }">
          <i class="icon">➕</i>
          Crear Encomienda
        </router-link>
        
        <!-- Items solo para Administrador -->
        <template v-if="userRole === 'Administrador'">
          <router-link to="/usuarios" class="navbar-item" :class="{ active: $route.path === '/usuarios' }">
            <i class="icon">👥</i>
            Usuarios
          </router-link>
          
          <router-link to="/sucursales" class="navbar-item" :class="{ active: $route.path === '/sucursales' }">
            <i class="icon">🏢</i>
            Sucursales
          </router-link>
          
          <router-link to="/reportes" class="navbar-item" :class="{ active: $route.path === '/reportes' }">
            <i class="icon">📊</i>
            Reportes
          </router-link>
        </template>
      </div>
      
      <div class="navbar-user">
        <ThemeToggle />
        <div class="user-info">
          <span class="user-name">{{ userName }}</span>
          <span class="user-role">{{ userRole }}</span>
        </div>
        <button @click="logout" class="logout-btn">
          <i class="icon">🚪</i>
          Cerrar Sesión
        </button>
      </div>
    </div>
  </nav>
</template>

<script>
import axios from 'axios'
import ThemeToggle from './ThemeToggle.vue'

export default {
  name: 'Navbar',
  components: {
    ThemeToggle
  },
  computed: {
    userRole() {
      const usuario = localStorage.getItem('usuario');
      if (usuario) {
        const userData = JSON.parse(usuario);
        return userData.rol;
      }
      return null;
    },
    userName() {
      const usuario = localStorage.getItem('usuario');
      if (usuario) {
        const userData = JSON.parse(usuario);
        return userData.nombre || userData.email;
      }
      return 'Usuario';
    }
  },
  methods: {
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
  }
}
</script>

<style scoped>
.navbar {
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  color: #333;
  padding: 1rem 2rem;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: background 0.3s ease, color 0.3s ease;
}

.dark .navbar {
  background: linear-gradient(135deg, #111a2e 0%, #0b1220 100%);
  color: #f3f4f6;
  box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.navbar-brand h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
  transition: color 0.3s ease;
}

.dark .navbar-brand h2 {
  color: #f3f4f6;
}

.navbar-menu {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.navbar-items {
  display: flex;
  gap: 1rem;
}

.navbar-item {
  color: #333;
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 500;
}

.dark .navbar-item {
  color: #cbd5e1;
}

.navbar-item:hover {
  background-color: rgba(0, 0, 0, 0.05);
  transform: translateY(-2px);
}

.dark .navbar-item:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.navbar-item.active {
  background-color: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
  font-weight: 600;
}

.dark .navbar-item.active {
  background-color: rgba(96, 165, 250, 0.2);
  color: #60a5fa;
}

.navbar-user {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.user-name {
  font-weight: 600;
  font-size: 0.9rem;
  transition: color 0.3s ease;
}

.dark .user-name {
  color: #f3f4f6;
}

.user-role {
  font-size: 0.8rem;
  opacity: 0.8;
  transition: color 0.3s ease;
}

.dark .user-role {
  color: #cbd5e1;
}

.logout-btn {
  background: #f8f9fa;
  color: #333;
  border: 1px solid #dee2e6;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 500;
}

.dark .logout-btn {
  background: #1f2a44;
  color: #cbd5e1;
  border-color: #1f2a44;
}

.logout-btn:hover {
  background: #e9ecef;
  transform: translateY(-2px);
}

.dark .logout-btn:hover {
  background: #111a2e;
}

.icon {
  font-size: 1rem;
}

@media (max-width: 768px) {
  .navbar {
    flex-direction: column;
    gap: 1rem;
  }
  
  .navbar-menu {
    flex-direction: column;
    gap: 1rem;
  }
  
  .navbar-items {
    flex-wrap: wrap;
    justify-content: center;
  }
}
</style>
