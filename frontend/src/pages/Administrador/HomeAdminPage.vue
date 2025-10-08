<template>
  <div class="home-admin">
    <Navbar />
    <div class="content">
      <h1>Panel del Administrador</h1>
      <div class="welcome-message">
        <p>¡Bienvenido al sistema!</p>
        <p>Has iniciado sesión como administrador.</p>
      </div>
      <div class="admin-features">
        <h2>Funciones disponibles:</h2>
        <ul>
          <li>Gestión de usuarios</li>
          <li>Gestión de sucursales</li>
          <li>Reportes y estadísticas</li>
          <li>Configuración del sistema</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import Navbar from '../../components/Navbar.vue'

export default {
  name: 'HomeAdmin',
  components: {
    Navbar
  }
}
</script>

<style scoped>
.home-admin {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
  text-align: center;
}

.home-admin h1 {
  color: #333;
  margin-bottom: 2rem;
}

.welcome-message {
  background-color: #d4edda;
  padding: 2rem;
  border-radius: 8px;
  margin-bottom: 2rem;
  border: 1px solid #c3e6cb;
}

.welcome-message p {
  margin: 0.5rem 0;
  font-size: 1.1rem;
  color: #155724;
}

.admin-features {
  background-color: #f8f9fa;
  padding: 2rem;
  border-radius: 8px;
  margin-bottom: 2rem;
  text-align: left;
}

.admin-features h2 {
  color: #333;
  margin-bottom: 1rem;
  text-align: center;
}

.admin-features ul {
  list-style-type: none;
  padding: 0;
}

.admin-features li {
  padding: 0.5rem 0;
  border-bottom: 1px solid #dee2e6;
  color: #666;
}

.admin-features li:last-child {
  border-bottom: none;
}

.actions {
  margin-top: 2rem;
}

.logout-btn {
  background-color: #dc3545;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
}

.logout-btn:hover {
  background-color: #c82333;
}
</style>