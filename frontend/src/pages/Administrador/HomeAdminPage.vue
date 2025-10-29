<template>
  <AdminLayout :key="$route.path">
    <div class="welcome-section">
      <h1 class="welcome-title">¡Bienvenido al Panel de Administración!</h1>
      <p class="welcome-subtitle">Has iniciado sesión como administrador</p>
    </div>
    
    <!-- Tarjetas de funciones principales -->
    <div class="functions-grid">
      <div class="function-card">
        <div class="card-icon">📊</div>
        <h3>Dashboard</h3>
        <p>Panel de control principal con métricas y estadísticas</p>
        <router-link to="/admin" class="card-link">Acceder</router-link>
      </div>
      
      <div class="function-card">
        <div class="card-icon">🚚</div>
        <h3>Fletes y Encomiendas</h3>
        <p>Gestión completa de servicios de transporte</p>
        <router-link to="/admin/fletes" class="card-link">Acceder</router-link>
      </div>
      
      <div class="function-card">
        <div class="card-icon">👥</div>
        <h3>Usuarios</h3>
        <p>Administración de usuarios del sistema</p>
        <router-link to="/admin/usuarios" class="card-link">Acceder</router-link>
      </div>
      
      <div class="function-card">
        <div class="card-icon">🏢</div>
        <h3>Sucursales</h3>
        <p>Gestión de sucursales y ubicaciones</p>
        <router-link to="/admin/sucursales" class="card-link">Acceder</router-link>
      </div>
      
      <div class="function-card">
        <div class="card-icon">📈</div>
        <h3>Reportes</h3>
        <p>Análisis y reportes del sistema</p>
        <router-link to="/admin/reportes" class="card-link">Acceder</router-link>
      </div>
      
      <div class="function-card">
        <div class="card-icon">⚙️</div>
        <h3>Configuración</h3>
        <p>Configuración general del sistema</p>
        <router-link to="/admin/configuracion" class="card-link">Acceder</router-link>
      </div>
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
}

.welcome-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.welcome-subtitle {
  font-size: 1.125rem;
  color: #6b7280;
  margin: 0;
}

/* Grid de funciones */
.functions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}

/* Tarjetas de función */
.function-card {
  background: white;
  border-radius: 1rem;
  padding: 2rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
  transition: all 0.3s ease;
  text-align: center;
}

.function-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  border-color: #3b82f6;
}

.card-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  display: block;
}

.function-card h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.75rem;
}

.function-card p {
  color: #6b7280;
  margin-bottom: 1.5rem;
  line-height: 1.5;
}

.card-link {
  display: inline-block;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  text-decoration: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: all 0.2s ease;
}

.card-link:hover {
  background: linear-gradient(135deg, #2563eb, #1e40af);
  transform: translateY(-1px);
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

  .functions-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .function-card {
    padding: 1.5rem;
  }
  
  .card-icon {
    font-size: 2.5rem;
  }
  
  .function-card h3 {
    font-size: 1.125rem;
  }
  
  .function-card p {
    font-size: 0.875rem;
  }
}

@media (min-width: 481px) and (max-width: 768px) {
  .welcome-title {
    font-size: 1.75rem;
  }
  
  .functions-grid {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.25rem;
  }
}

@media (min-width: 769px) and (max-width: 1023px) {
  .welcome-title {
    font-size: 2.25rem;
  }
  
  .functions-grid {
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.75rem;
  }
}

@media (min-width: 1024px) {
  .welcome-title {
    font-size: 2.5rem;
  }
  
  .functions-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
  }
}

/* Animaciones */
.function-card {
  animation: fadeInUp 0.6s ease-out;
}

.function-card:nth-child(1) { animation-delay: 0.1s; }
.function-card:nth-child(2) { animation-delay: 0.2s; }
.function-card:nth-child(3) { animation-delay: 0.3s; }
.function-card:nth-child(4) { animation-delay: 0.4s; }
.function-card:nth-child(5) { animation-delay: 0.5s; }
.function-card:nth-child(6) { animation-delay: 0.6s; }

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.welcome-section {
  animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
</style>