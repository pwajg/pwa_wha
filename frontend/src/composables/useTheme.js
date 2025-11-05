/**
 * Composable para manejar el tema claro/oscuro
 * Usa VueUse para reactividad y sincronización con el backend
 */

import { useDark, useToggle } from '@vueuse/core'
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'

// Estado reactivo del tema usando VueUse
const isDark = useDark({
  selector: 'html',
  attribute: 'class',
  valueDark: 'dark',
  valueLight: ''
})

const toggleDark = useToggle(isDark)

// Estado para sincronización con backend
const isSyncing = ref(false)
const userThemePreference = ref(null)

/**
 * Obtener preferencia de tema del usuario desde el backend
 */
const fetchUserTheme = async () => {
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      // Si no hay token, usar preferencia del sistema o localStorage
      const localTheme = localStorage.getItem('theme')
      if (localTheme) {
        isDark.value = localTheme === 'dark'
      }
      return
    }

    const response = await axios.get('/api/user/theme')
    if (response.data && response.data.theme_preference) {
      userThemePreference.value = response.data.theme_preference
      isDark.value = response.data.theme_preference === 'dark'
      localStorage.setItem('theme', response.data.theme_preference)
    }
  } catch (error) {
    console.warn('No se pudo obtener la preferencia de tema del servidor:', error)
    // Fallback a localStorage
    const localTheme = localStorage.getItem('theme')
    if (localTheme) {
      isDark.value = localTheme === 'dark'
    }
  }
}

/**
 * Actualizar preferencia de tema en el backend
 */
const updateUserTheme = async (theme) => {
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      // Si no hay token, solo guardar en localStorage
      localStorage.setItem('theme', theme)
      return
    }

    isSyncing.value = true
    await axios.put('/api/user/theme', {
      theme_preference: theme
    })
    userThemePreference.value = theme
    localStorage.setItem('theme', theme)
  } catch (error) {
    console.error('Error al actualizar la preferencia de tema:', error)
    // Fallback: guardar en localStorage
    localStorage.setItem('theme', theme)
  } finally {
    isSyncing.value = false
  }
}

/**
 * Cambiar tema con animación suave
 */
const toggleTheme = async () => {
  toggleDark()
  const newTheme = isDark.value ? 'dark' : 'light'
  
  // Guardar en localStorage inmediatamente para persistencia local
  localStorage.setItem('theme', newTheme)
  
  // Sincronizar con backend (sin bloquear la UI)
  updateUserTheme(newTheme)
}

/**
 * Inicializar tema al cargar la aplicación
 */
const initTheme = async () => {
  // Primero intentar obtener del backend si hay usuario autenticado
  await fetchUserTheme()
  
  // Si no hay preferencia del usuario, usar preferencia del sistema
  if (!userThemePreference.value && !localStorage.getItem('theme')) {
    const systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
    if (systemPrefersDark) {
      isDark.value = true
      localStorage.setItem('theme', 'dark')
    } else {
      isDark.value = false
      localStorage.setItem('theme', 'light')
    }
  }
}

// Observar cambios en el tema para sincronizar con backend
watch(isDark, async (newValue) => {
  const theme = newValue ? 'dark' : 'light'
  localStorage.setItem('theme', theme)
  
  // Sincronizar con backend solo si hay usuario autenticado
  const token = localStorage.getItem('token')
  if (token && userThemePreference.value !== theme) {
    updateUserTheme(theme)
  }
})

// Escuchar cambios en la preferencia del sistema
if (window.matchMedia) {
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
  const handleSystemThemeChange = (e) => {
    // Solo aplicar si no hay preferencia guardada del usuario
    if (!userThemePreference.value && !localStorage.getItem('theme')) {
      isDark.value = e.matches
    }
  }
  mediaQuery.addEventListener('change', handleSystemThemeChange)
}

export function useTheme() {
  return {
    isDark,
    toggleTheme,
    initTheme,
    fetchUserTheme,
    isSyncing,
    userThemePreference
  }
}

