import { createApp } from 'vue'
import './style.css'
import './assets/css/whafren-colors.css'
import './assets/css/theme-variables.css'
import 'tailwindcss/tailwind.css'
import App from './App.vue'
import router from './router'
import axios from 'axios'

// Inicializar tema básico ANTES de crear la app para evitar flash
// Esto establece el tema inicial basado en localStorage o preferencia del sistema
const savedTheme = localStorage.getItem('theme')
const systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
    document.documentElement.classList.add('dark')
} else {
    document.documentElement.classList.remove('dark')
}


// Configurar axios con la URL base del backend
axios.defaults.baseURL = '/api'
axios.defaults.headers.common['Content-Type'] = 'application/json'
axios.defaults.headers.common['Accept'] = 'application/json'

// Interceptor para agregar el token a las peticiones
axios.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('token')
        if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
    },
    (error) => {
        return Promise.reject(error)
    }
)

// Interceptor para manejar respuestas de error 401
axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
        // Token expirado o inválido
            localStorage.removeItem('token')
            localStorage.removeItem('usuario')
        // Redirigir al login si no estamos ya ahí
            if (window.location.pathname !== '/login') {
                window.location.href = '/login'
            }
        }
        return Promise.reject(error)
    }
)

createApp(App)
    .use(router)
    .provide('$axios', axios)
    .mount('#app')
