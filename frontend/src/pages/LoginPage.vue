<template>
  <div class="login">
    <h1>Iniciar Sesión</h1>
    <form @submit.prevent="login">
      <input v-model="email" placeholder="Correo" type="email" required />
      <input v-model="password" type="password" placeholder="Contraseña" required />
      <button type="submit" :disabled="loading">
        {{ loading ? 'Iniciando sesión...' : 'Entrar' }}
      </button>
    </form>
    <div v-if="error" class="error-message">
      {{ error }}
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'Login',
  data() {
    return { 
      email: '', 
      password: '',
      loading: false,
      error: ''
    }
  },
  methods: {
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
  }
}
</script>

<style scoped>
.login {
  max-width: 400px;
  margin: 0 auto;
  padding: 2rem;
}

.login h1 {
  text-align: center;
  margin-bottom: 2rem;
}

.login form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.login input {
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}

.login button {
  padding: 0.75rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
}

.login button:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}

.error-message {
  margin-top: 1rem;
  padding: 0.75rem;
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
  border-radius: 4px;
  text-align: center;
}
</style> 