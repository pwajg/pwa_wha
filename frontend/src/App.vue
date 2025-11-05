<script setup>
import { onMounted } from 'vue'
import { useTheme } from './composables/useTheme'
import { useRouter } from 'vue-router'

const router = useRouter()
const { initTheme, fetchUserTheme } = useTheme()

// Inicializar tema al montar la app
onMounted(async () => {
  // Inicializar tema básico
  await initTheme()
  
  // Si hay usuario autenticado, sincronizar con backend
  const token = localStorage.getItem('token')
  if (token) {
    await fetchUserTheme()
  }
})
</script>

<template>
  <router-view />
</template>

<style scoped>
.logo {
  height: 6em;
  padding: 1.5em;
  will-change: filter;
  transition: filter 300ms;
}
.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}
.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}
</style>
