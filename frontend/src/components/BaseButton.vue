/**
 * Componente base para botones
 * Garantiza coherencia visual, accesibilidad y estados hover/focus/active
 */
<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'base-button',
      sizeClasses,
      variantClasses,
      {
        'w-full': fullWidth,
        'opacity-50 cursor-not-allowed': disabled || loading
      }
    ]"
    :aria-label="ariaLabel"
    :aria-busy="loading"
    @click="handleClick"
  >
    <span v-if="loading" class="button-spinner" aria-hidden="true"></span>
    <span v-if="icon && iconPosition === 'left'" class="button-icon-left" aria-hidden="true">
      <component :is="icon" class="w-4 h-4 sm:w-5 sm:h-5" />
    </span>
    <span class="button-text">
      <slot>{{ label }}</slot>
    </span>
    <span v-if="icon && iconPosition === 'right'" class="button-icon-right" aria-hidden="true">
      <component :is="icon" class="w-4 h-4 sm:w-5 sm:h-5" />
    </span>
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'button',
    validator: (value) => ['button', 'submit', 'reset'].includes(value)
  },
  variant: {
    type: String,
    default: 'primary', // 'primary', 'secondary', 'danger', 'success', 'warning', 'ghost'
    validator: (value) => ['primary', 'secondary', 'danger', 'success', 'warning', 'ghost'].includes(value)
  },
  size: {
    type: String,
    default: 'md', // 'sm', 'md', 'lg'
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  fullWidth: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  icon: {
    type: [String, Object],
    default: null
  },
  iconPosition: {
    type: String,
    default: 'left', // 'left', 'right'
    validator: (value) => ['left', 'right'].includes(value)
  },
  label: {
    type: String,
    default: ''
  },
  ariaLabel: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['click'])

const sizeClasses = computed(() => {
  const sizes = {
    sm: 'px-3 py-1.5 text-sm',
    md: 'px-4 py-2 text-base',
    lg: 'px-6 py-3 text-lg'
  }
  return sizes[props.size] || sizes.md
})

const variantClasses = computed(() => {
  const variants = {
    primary: 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:active:bg-blue-800 dark:focus:ring-blue-400',
    secondary: 'bg-gray-200 hover:bg-gray-300 active:bg-gray-400 text-gray-800 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-700 dark:hover:bg-gray-600 dark:active:bg-gray-500 dark:text-gray-200',
    danger: 'bg-red-600 hover:bg-red-700 active:bg-red-800 text-white focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:bg-red-600 dark:hover:bg-red-700 dark:active:bg-red-800',
    success: 'bg-green-600 hover:bg-green-700 active:bg-green-800 text-white focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:bg-green-600 dark:hover:bg-green-700 dark:active:bg-green-800',
    warning: 'bg-yellow-600 hover:bg-yellow-700 active:bg-yellow-800 text-white focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:active:bg-yellow-800',
    ghost: 'bg-transparent hover:bg-gray-100 active:bg-gray-200 text-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:hover:bg-gray-700 dark:active:bg-gray-600 dark:text-gray-300'
  }
  return variants[props.variant] || variants.primary
})

const handleClick = (event) => {
  if (!props.disabled && !props.loading) {
    emit('click', event)
  }
}
</script>

<style scoped>
.base-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  font-weight: 500;
  border-radius: 0.5rem;
  transition: all 0.2s ease;
  cursor: pointer;
  border: none;
  outline: none;
}

.base-button:focus-visible {
  outline: 2px solid transparent;
  outline-offset: 2px;
}

.base-button:active:not(:disabled):not(.opacity-50) {
  transform: scale(0.98);
}

.button-spinner {
  display: inline-block;
  width: 1rem;
  height: 1rem;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.button-icon-left,
.button-icon-right {
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

.button-text {
  flex: 1;
  text-align: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>

