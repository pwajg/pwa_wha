/**
 * Componente base para tarjetas/cards
 * Garantiza coherencia visual y adaptación al tema
 */
<template>
  <div
    :class="[
      'base-card',
      {
        'hover-lift': hoverable,
        'has-shadow': shadow
      }
    ]"
    :style="cardStyle"
  >
    <div v-if="title || $slots.header" class="card-header">
      <slot name="header">
        <h3 v-if="title" class="card-title" :title="titleTruncated ? title : undefined">
          {{ title }}
        </h3>
        <p v-if="subtitle" class="card-subtitle" :title="subtitleTruncated ? subtitle : undefined">
          {{ subtitle }}
        </p>
      </slot>
    </div>
    
    <div class="card-content">
      <slot></slot>
    </div>
    
    <div v-if="$slots.footer" class="card-footer">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  title: {
    type: String,
    default: ''
  },
  subtitle: {
    type: String,
    default: ''
  },
  hoverable: {
    type: Boolean,
    default: false
  },
  shadow: {
    type: Boolean,
    default: true
  },
  padding: {
    type: String,
    default: 'normal', // 'none', 'sm', 'normal', 'lg'
    validator: (value) => ['none', 'sm', 'normal', 'lg'].includes(value)
  }
})

const titleTruncated = ref(false)
const subtitleTruncated = ref(false)

const cardStyle = computed(() => {
  const paddings = {
    none: '0',
    sm: '0.75rem',
    normal: '1rem',
    lg: '1.5rem'
  }
  return {
    padding: paddings[props.padding] || paddings.normal
  }
})
</script>

<style scoped>
.base-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  transition: all 0.3s ease;
}

.dark .base-card {
  background: #111827;
  border-color: #374151;
}

.base-card.has-shadow {
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.dark .base-card.has-shadow {
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3), 0 1px 2px 0 rgba(0, 0, 0, 0.2);
}

.base-card.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.dark .base-card.hover-lift:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
}

.card-header {
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #e5e7eb;
}

.dark .card-header {
  border-bottom-color: #374151;
}

.card-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.25rem 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  transition: color 0.3s ease;
}

.dark .card-title {
  color: #f9fafb;
}

.card-title[title] {
  cursor: help;
}

.card-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  transition: color 0.3s ease;
}

.dark .card-subtitle {
  color: #d1d5db;
}

.card-subtitle[title] {
  cursor: help;
}

.card-content {
  color: #374151;
  transition: color 0.3s ease;
}

.dark .card-content {
  color: #e5e7eb;
}

.card-footer {
  margin-top: 1rem;
  padding-top: 0.75rem;
  border-top: 1px solid #e5e7eb;
}

.dark .card-footer {
  border-top-color: #374151;
}
</style>

