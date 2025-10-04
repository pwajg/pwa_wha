<script setup>
import HelloWorld from './components/HelloWorld.vue'
import Usuarios from './components/Usuarios.vue'

</script>

<template>
  <div>
    <h1 class="text-2xl font-bold">Gestión de Usuarios</h1>
    <Usuarios />
  </div>
  <HelloWorld msg="Vite + Vue" />
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
