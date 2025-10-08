<template>
  <HomeAdminPage v-if="userRole === 'Administrador'" />
  <HomeColaboradorPage v-else-if="userRole === 'Colaborador'" />
  <div v-else class="loading-container">
    <div class="spinner"></div>
    <p>Cargando...</p>
  </div>
</template>

<script>
import HomeAdminPage from '../pages/Administrador/HomeAdminPage.vue'
import HomeColaboradorPage from '../pages/Colaborador/HomePage.vue'

export default {
  name: 'HomeWrapper',
  components: {
    HomeAdminPage,
    HomeColaboradorPage
  },
  computed: {
    userRole() {
      const usuario = localStorage.getItem('usuario');
      if (usuario) {
        const userData = JSON.parse(usuario);
        return userData.rol;
      }
      return null;
    }
  }
}
</script>

<style scoped>
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh;
  background-color: #f5f5f5;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

p {
  color: #666;
  font-size: 16px;
}
</style>
