/**
 * Componente base para inputs
 * Garantiza coherencia visual y adaptación al tema
 */
<template>
  <div class="input-wrapper">
    <label 
      v-if="label" 
      :for="inputId"
      :class="[
        'input-label',
        { 'required': required }
      ]"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1" aria-label="requerido">*</span>
    </label>
    
    <div class="input-container" :class="{ 'has-error': error, 'has-icon': icon }">
      <span v-if="icon" class="input-icon-left" aria-hidden="true">
        <component :is="icon" class="w-5 h-5" />
      </span>
      
      <input
        :id="inputId"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :readonly="readonly"
        :class="[
          'base-input',
          {
            'has-error': error,
            'has-icon-left': icon,
            'has-icon-right': showClearButton
          }
        ]"
        :aria-invalid="error ? 'true' : 'false'"
        :aria-describedby="error ? `${inputId}-error` : undefined"
        @input="handleInput"
        @blur="handleBlur"
        @focus="handleFocus"
      />
      
      <button
        v-if="showClearButton && modelValue"
        type="button"
        class="input-icon-right clear-button"
        @click="clear"
        aria-label="Limpiar"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    
    <p v-if="hint && !error" :id="`${inputId}-hint`" class="input-hint">
      {{ hint }}
    </p>
    
    <p v-if="error" :id="`${inputId}-error`" class="input-error" role="alert">
      {{ error }}
    </p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text',
    validator: (value) => ['text', 'email', 'password', 'number', 'tel', 'url', 'date', 'time', 'datetime-local', 'search'].includes(value)
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  hint: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  },
  clearable: {
    type: Boolean,
    default: false
  },
  icon: {
    type: [String, Object],
    default: null
  },
  id: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue', 'blur', 'focus', 'clear'])

const inputId = computed(() => props.id || `input-${Math.random().toString(36).substr(2, 9)}`)
const showClearButton = computed(() => props.clearable && props.modelValue && !props.disabled && !props.readonly)

const handleInput = (event) => {
  emit('update:modelValue', event.target.value)
}

const handleBlur = (event) => {
  emit('blur', event)
}

const handleFocus = (event) => {
  emit('focus', event)
}

const clear = () => {
  emit('update:modelValue', '')
  emit('clear')
}
</script>

<style scoped>
.input-wrapper {
  width: 100%;
}

.input-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
  transition: color 0.3s ease;
}

.dark .input-label {
  color: #d1d5db;
}

.input-label.required::after {
  content: '*';
  color: #ef4444;
  margin-left: 0.25rem;
}

.input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.input-container.has-error .base-input {
  border-color: #ef4444;
}

.input-container.has-error .base-input:focus {
  border-color: #ef4444;
  ring-color: #ef4444;
}

.base-input {
  width: 100%;
  padding: 0.75rem 1rem;
  font-size: 1rem;
  line-height: 1.5;
  color: #111827;
  background-color: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  transition: all 0.2s ease;
  outline: none;
}

.dark .base-input {
  color: #f9fafb;
  background-color: #1f2937;
  border-color: #4b5563;
}

.base-input::placeholder {
  color: #9ca3af;
}

.dark .base-input::placeholder {
  color: #6b7280;
}

.base-input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.dark .base-input:focus {
  border-color: #60a5fa;
  box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2);
}

.base-input:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  background-color: #f3f4f6;
}

.dark .base-input:disabled {
  background-color: #374151;
}

.base-input:readonly {
  background-color: #f9fafb;
  cursor: default;
}

.dark .base-input:readonly {
  background-color: #111827;
}

.base-input.has-icon-left {
  padding-left: 2.5rem;
}

.base-input.has-icon-right {
  padding-right: 2.5rem;
}

.input-icon-left {
  position: absolute;
  left: 0.75rem;
  color: #6b7280;
  pointer-events: none;
  z-index: 1;
  transition: color 0.3s ease;
}

.dark .input-icon-left {
  color: #9ca3af;
}

.input-icon-right {
  position: absolute;
  right: 0.75rem;
  color: #6b7280;
  pointer-events: none;
  z-index: 1;
  transition: color 0.3s ease;
}

.dark .input-icon-right {
  color: #9ca3af;
}

.clear-button {
  position: absolute;
  right: 0.5rem;
  background: none;
  border: none;
  cursor: pointer;
  color: #6b7280;
  padding: 0.25rem;
  border-radius: 0.25rem;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.dark .clear-button {
  color: #9ca3af;
}

.clear-button:hover {
  color: #374151;
  background-color: #f3f4f6;
}

.dark .clear-button:hover {
  color: #d1d5db;
  background-color: #374151;
}

.input-hint {
  margin-top: 0.5rem;
  font-size: 0.875rem;
  color: #6b7280;
  transition: color 0.3s ease;
}

.dark .input-hint {
  color: #9ca3af;
}

.input-error {
  margin-top: 0.5rem;
  font-size: 0.875rem;
  color: #ef4444;
  font-weight: 500;
}

.dark .input-error {
  color: #f87171;
}
</style>

