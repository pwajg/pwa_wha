<template>
  <div class="home-colaborador">
    <Navbar />
    <div class="content">
      <h1>Panel del Colaborador</h1>
      <div class="welcome-message">
        <p>¡Bienvenido al sistema!</p>
        <p>Has iniciado sesión como colaborador.</p>
      </div>
    </div>
  </div>
</template>

<script>
import Navbar from '../../components/Navbar.vue'

export default {
  name: 'HomeColaborador',
  components: {
    Navbar
  }
}
</script>

<style scoped>
.home-colaborador {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
  text-align: center;
}

.home-colaborador h1 {
  color: #333;
  margin-bottom: 2rem;
}

.welcome-message {
  background-color: #f8f9fa;
  padding: 2rem;
  border-radius: 8px;
  margin-bottom: 2rem;
}

.welcome-message p {
  margin: 0.5rem 0;
  font-size: 1.1rem;
  color: #666;
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
