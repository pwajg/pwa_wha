<template>
  <button
    @click="toggleTheme"
    :disabled="isSyncing"
    class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 flex items-center justify-center"
    :title="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
    aria-label="Cambiar tema"
  >
    <!-- Icono de sol (modo claro) -->
    <svg
      v-if="!isDark"
      class="w-5 h-5 transition-all duration-300"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
      />
    </svg>
    <!-- Icono de luna (modo oscuro) -->
    <svg
      v-else
      class="w-5 h-5 transition-all duration-300"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
      />
    </svg>
  </button>
</template>

<script setup>
import { useTheme } from '../composables/useTheme'

const { isDark, toggleTheme, isSyncing } = useTheme()
</script>

