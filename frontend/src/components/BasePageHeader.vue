/**
 * Componente base para títulos de página
 * Garantiza coherencia visual y accesibilidad
 */
<template>
  <div class="page-header" :class="{ 'has-tooltip': truncated && showTooltip }">
    <h1 
      ref="titleRef"
      :class="[
        'page-title',
        sizeClasses,
        weightClasses,
        textColorClasses
      ]"
      :title="truncated && showTooltip ? title : undefined"
    >
      {{ title }}
    </h1>
    <p 
      v-if="subtitle"
      :class="[
        'page-subtitle',
        subtitleSizeClasses,
        subtitleColorClasses
      ]"
      :title="subtitleTruncated && showTooltip ? subtitle : undefined"
    >
      {{ subtitle }}
    </p>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'lg', // 'sm', 'md', 'lg', 'xl'
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  },
  weight: {
    type: String,
    default: 'bold', // 'normal', 'semibold', 'bold'
    validator: (value) => ['normal', 'semibold', 'bold'].includes(value)
  },
  truncate: {
    type: Boolean,
    default: true
  },
  showTooltip: {
    type: Boolean,
    default: true
  }
})

const titleRef = ref(null)
const truncated = ref(false)
const subtitleTruncated = ref(false)

const sizeClasses = computed(() => {
  const sizes = {
    sm: 'text-xl sm:text-2xl',
    md: 'text-2xl sm:text-3xl',
    lg: 'text-2xl sm:text-3xl',
    xl: 'text-3xl sm:text-4xl'
  }
  return sizes[props.size] || sizes.lg
})

const weightClasses = computed(() => {
  const weights = {
    normal: 'font-normal',
    semibold: 'font-semibold',
    bold: 'font-bold'
  }
  return weights[props.weight] || weights.bold
})

const textColorClasses = computed(() => {
  return 'text-gray-700 dark:text-white'
})

const subtitleSizeClasses = computed(() => {
  return 'text-sm sm:text-base'
})

const subtitleColorClasses = computed(() => {
  return 'text-gray-600 dark:text-gray-300'
})

const checkTruncation = () => {
  if (!titleRef.value) return
  const element = titleRef.value
  truncated.value = element.scrollWidth > element.clientWidth
}

onMounted(() => {
  checkTruncation()
  window.addEventListener('resize', checkTruncation)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkTruncation)
})
</script>

<style scoped>
.page-header {
  margin-bottom: 2rem;
}

.page-title {
  margin-bottom: 0.5rem;
  word-break: break-word;
  transition: color 0.3s ease;
}

.page-title[title] {
  cursor: help;
}

.page-subtitle {
  transition: color 0.3s ease;
}

.page-subtitle[title] {
  cursor: help;
}

@media (max-width: 640px) {
  .page-header {
    margin-bottom: 1.5rem;
  }
}
</style>

