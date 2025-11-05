<template>
  <AdminLayout :key="$route.path">
    <div class="welcome-section">
      <h1 class="welcome-title">¡Bienvenido al Panel de Administración!</h1>
      <p class="welcome-subtitle">Has iniciado sesión como administrador</p>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '../../components/AdminLayout.vue'

export default {
  name: 'HomeAdmin',
  components: {
    AdminLayout
  }
}
</script>

<style scoped>
/* Sección de bienvenida */
.welcome-section {
  text-align: center;
  margin-bottom: 3rem;
  padding: 2rem;
  background: linear-gradient(135deg, #ffffff, #f8fafc);
  border-radius: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}

.dark .welcome-section {
  background: linear-gradient(135deg, #111827, #1f2937);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

.welcome-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.5rem;
  transition: color 0.3s ease;
}

.dark .welcome-title {
  color: #f9fafb;
}

.welcome-subtitle {
  font-size: 1.125rem;
  color: #6b7280;
  margin: 0;
  transition: color 0.3s ease;
}

.dark .welcome-subtitle {
  color: #d1d5db;
}

/* Responsive adjustments */
@media (max-width: 480px) {
  .welcome-section {
    padding: 1.5rem 1rem;
    margin-bottom: 2rem;
  }
  
  .welcome-title {
    font-size: 1.5rem;
  }
  
  .welcome-subtitle {
    font-size: 1rem;
  }
}

@media (min-width: 481px) and (max-width: 768px) {
  .welcome-title {
    font-size: 1.75rem;
  }
}

@media (min-width: 769px) and (max-width: 1023px) {
  .welcome-title {
    font-size: 2.25rem;
  }
}

@media (min-width: 1024px) {
  .welcome-title {
    font-size: 2.5rem;
  }
}

/* Animaciones */
.welcome-section {
  animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>