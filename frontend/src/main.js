import { createApp } from 'vue'
import './style.css'
import 'tailwindcss/tailwind.css'
import App from './App.vue'
import router from './router'
import axios from 'axios'

// Configurar axios con la URL base del backend
axios.defaults.baseURL = 'http://localhost:8000/api'
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
