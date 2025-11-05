<template>
  <div class="login-page">
    <!-- Elementos de fondo borroso -->
    <div class="blur-background">
      <div class="blur-element blur-element-1"></div>
      <div class="blur-element blur-element-2"></div>
      <div class="blur-element blur-element-3"></div>
      <div class="blur-element blur-element-4"></div>
      <div class="blur-element blur-element-5"></div>
    </div>
    
    <div class="login-container">
      <div class="login-modal">
      <!-- Logo y toggle de tema -->
      <div class="flex justify-between items-start mb-4">
        <div class="logo-section flex-1">
          <span class="logo">WAFREN</span>
        </div>
        <ThemeToggle />
      </div>
      
      <!-- Título -->
      <h2 class="login-title">Ingrese sus datos</h2>
      
      <!-- Formulario -->
      <form @submit.prevent="login" class="login-form">
        <!-- Campo Email -->
        <div class="input-group">
          <input 
            v-model="email" 
            placeholder="Correo electrónico" 
            type="email" 
            required 
            class="form-input"
          />
        </div>
        
        <!-- Campo Contraseña -->
        <div class="input-group">
          <div class="password-container">
            <input 
              v-model="password" 
              :type="showPassword ? 'text' : 'password'" 
              placeholder="Contraseña" 
              required 
              class="form-input password-input"
            />
            <button 
              type="button" 
              @click="togglePassword" 
              class="password-toggle"
              :class="{ 'active': showPassword }"
            >
              <svg v-if="!showPassword" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Recordarme -->
        <div class="remember-section">
          <label class="remember-checkbox">
            <input 
              v-model="rememberMe" 
              type="checkbox" 
              class="checkbox-input"
            />
            <span class="checkbox-custom"></span>
            <span class="checkbox-label">Recordarme</span>
          </label>
        </div>
        
        <!-- Botón de envío -->
        <button type="submit" :disabled="loading" class="login-button">
          <span v-if="loading" class="loading-spinner"></span>
          {{ loading ? 'Iniciando sesión...' : 'Iniciar Sesión' }}
      </button>
    </form>
      
      <!-- Mensaje de error -->
    <div v-if="error" class="error-message">
      {{ error }}
      </div>
    </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import ThemeToggle from '../components/ThemeToggle.vue'

export default {
  name: 'Login',
  components: {
    ThemeToggle
  },
  data() {
    return { 
      email: '', 
      password: '',
      loading: false,
      error: '',
      showPassword: false,
      rememberMe: false
    }
  },
  methods: {
    togglePassword() {
      this.showPassword = !this.showPassword
    },
    
    async login() {
      this.loading = true
      this.error = ''
      
      try {
        const response = await axios.post('/login', {
          email: this.email,
          password: this.password
        })
        
        // Guardar datos del usuario en localStorage
        localStorage.setItem('usuario', JSON.stringify(response.data.usuario))
        localStorage.setItem('token', response.data.token)
        
        // Sincronizar tema del usuario después del login
        try {
          const { useTheme } = await import('../composables/useTheme')
          const { fetchUserTheme } = useTheme()
          await fetchUserTheme()
        } catch (error) {
          console.warn('No se pudo sincronizar el tema del usuario:', error)
        }
        
        // Si recordarme está marcado, guardar credenciales
        if (this.rememberMe) {
          localStorage.setItem('rememberedEmail', this.email)
        } else {
          localStorage.removeItem('rememberedEmail')
        }
        
        // Redirigir al home (el HomeWrapper decidirá qué vista mostrar según el rol)
        this.$router.push('/home')
        
      } catch (error) {
        console.error('Error en login:', error)
        
        if (error.response && error.response.status === 401) {
          this.error = 'Credenciales incorrectas'
        } else if (error.response && error.response.data && error.response.data.message) {
          this.error = error.response.data.message
        } else {
          this.error = 'Error de conexión. Verifica que el backend esté funcionando.'
        }
      } finally {
        this.loading = false
      }
    }
  },
  
  mounted() {
    // Cargar email recordado si existe
    const rememberedEmail = localStorage.getItem('rememberedEmail')
    if (rememberedEmail) {
      this.email = rememberedEmail
      this.rememberMe = true
    }
  }
}
</script>

<style scoped>
/* Página completa */
.login-page {
  min-height: 100vh;
  position: relative;
  overflow: hidden;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  transition: background 0.3s ease;
}

.dark .login-page {
  background: linear-gradient(135deg, #0b1220 0%, #111a2e 100%);
}

/* Elementos de fondo borroso */
.blur-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
}

.blur-element {
  position: absolute;
  border-radius: 50%;
  filter: blur(40px);
  opacity: 0.6;
  animation: float 6s ease-in-out infinite;
}

.blur-element-1 {
  width: 300px;
  height: 300px;
  background: #3b82f6;
  top: 10%;
  left: 10%;
  animation-delay: 0s;
}

.blur-element-2 {
  width: 200px;
  height: 200px;
  background: #1d4ed8;
  top: 60%;
  right: 15%;
  animation-delay: 2s;
}

.blur-element-3 {
  width: 250px;
  height: 250px;
  background: #6366f1;
  bottom: 20%;
  left: 20%;
  animation-delay: 4s;
}

.blur-element-4 {
  width: 180px;
  height: 180px;
  background: #64748b;
  top: 30%;
  right: 30%;
  animation-delay: 1s;
}

.blur-element-5 {
  width: 220px;
  height: 220px;
  background: #475569;
  bottom: 40%;
  right: 10%;
  animation-delay: 3s;
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px) rotate(0deg);
  }
  33% {
    transform: translateY(-20px) rotate(120deg);
  }
  66% {
    transform: translateY(10px) rotate(240deg);
  }
}

/* Contenedor principal */
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  position: relative;
  z-index: 2;
}

/* Modal central */
.login-modal {
  background: #ffffff;
  border-radius: 1rem;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  padding: 2.5rem;
  width: 100%;
  max-width: 420px;
  position: relative;
  backdrop-filter: blur(10px);
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.dark .login-modal {
  background: #111a2e;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

/* Logo */
.logo-section {
  text-align: center;
  margin-bottom: 2rem;
}

.logo {
  font-size: 2.5rem;
  font-weight: 800;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: 2px;
}

/* Título */
.login-title {
  text-align: center;
  color: #1f2937;
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 2rem;
  margin-top: 0;
  transition: color 0.3s ease;
}

.dark .login-title {
  color: #f3f4f6;
}

/* Formulario */
.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* Grupos de input */
.input-group {
  position: relative;
}

/* Inputs */
.form-input {
  width: 100%;
  padding: 1rem 1.25rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: all 0.2s ease;
  background: #f9fafb;
  box-sizing: border-box;
  color: #1f2937;
}

.dark .form-input {
  background: #1f2a44;
  border-color: #1f2a44;
  color: #f3f4f6;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.dark .form-input:focus {
  background: #111a2e;
  box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2);
}

.form-input::placeholder {
  color: #6b7280;
}

.dark .form-input::placeholder {
  color: #9ca3af;
}

/* Contenedor de contraseña */
.password-container {
  position: relative;
}

.password-input {
  padding-right: 3.5rem;
}

/* Botón mostrar/ocultar contraseña */
.password-toggle {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #6b7280;
  transition: color 0.2s ease;
  padding: 0.25rem;
  border-radius: 0.25rem;
}

.password-toggle:hover {
  color: #3b82f6;
}

.password-toggle.active {
  color: #3b82f6;
}

/* Sección recordarme */
.remember-section {
  display: flex;
  align-items: center;
  margin: -0.5rem 0;
}

.remember-checkbox {
  display: flex;
  align-items: center;
  cursor: pointer;
  font-size: 0.9rem;
  color: #6b7280;
}

.checkbox-input {
  display: none;
}

.checkbox-custom {
  width: 20px;
  height: 20px;
  border: 2px solid #e5e7eb;
  border-radius: 0.25rem;
  margin-right: 0.75rem;
  position: relative;
  transition: all 0.2s ease;
  background: #ffffff;
}

.checkbox-input:checked + .checkbox-custom {
  background: #3b82f6;
  border-color: #3b82f6;
}

.checkbox-input:checked + .checkbox-custom::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #ffffff;
  font-size: 12px;
  font-weight: bold;
}

.checkbox-label {
  user-select: none;
}

/* Botón de login */
.login-button {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: #ffffff;
  border: none;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
  overflow: hidden;
}

.login-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
}

.login-button:active:not(:disabled) {
  transform: translateY(0);
}

.login-button:disabled {
  background: #6b7280;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* Spinner de carga */
.loading-spinner {
  display: inline-block;
  width: 16px;
  height: 16px;
  border: 2px solid transparent;
  border-top: 2px solid #ffffff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 0.5rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Mensaje de error */
.error-message {
  margin-top: 1rem;
  padding: 1rem;
  background: rgba(239, 68, 68, 0.1);
  color: #dc2626;
  border: 1px solid rgba(239, 68, 68, 0.3);
  border-radius: 0.5rem;
  text-align: center;
  font-size: 0.9rem;
  animation: shake 0.5s ease-in-out;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  75% { transform: translateX(5px); }
}

/* Responsive */
@media (max-width: 480px) {
  .login-container {
    padding: 0.5rem;
  }
  
  .login-modal {
    padding: 2rem 1.5rem;
    border-radius: 0.75rem;
  }
  
  .logo {
    font-size: 2rem;
  }
  
  .login-title {
    font-size: 1.25rem;
  }
  
  .form-input {
    padding: 0.875rem 1rem;
  }
  
  .login-button {
    padding: 0.875rem;
  }
}

@media (max-width: 360px) {
  .login-modal {
    padding: 1.5rem 1rem;
  }
  
  .logo {
    font-size: 1.75rem;
  }
}

/* Responsive para elementos borrosos */
@media (max-width: 768px) {
  .blur-element-1 {
    width: 200px;
    height: 200px;
    top: 5%;
    left: 5%;
  }
  
  .blur-element-2 {
    width: 150px;
    height: 150px;
    top: 70%;
    right: 10%;
  }
  
  .blur-element-3 {
    width: 180px;
    height: 180px;
    bottom: 10%;
    left: 10%;
  }
  
  .blur-element-4 {
    width: 120px;
    height: 120px;
    top: 20%;
    right: 20%;
  }
  
  .blur-element-5 {
    width: 160px;
    height: 160px;
    bottom: 30%;
    right: 5%;
  }
}

@media (max-width: 480px) {
  .blur-element {
    filter: blur(30px);
    opacity: 0.4;
  }
  
  .blur-element-1 {
    width: 150px;
    height: 150px;
  }
  
  .blur-element-2 {
    width: 120px;
    height: 120px;
  }
  
  .blur-element-3 {
    width: 140px;
    height: 140px;
  }
  
  .blur-element-4 {
    width: 100px;
    height: 100px;
  }
  
  .blur-element-5 {
    width: 130px;
    height: 130px;
  }
}
</style> 