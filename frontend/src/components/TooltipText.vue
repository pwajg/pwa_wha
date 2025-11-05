/**
 * Utilidad para aplicar tooltips a textos truncados automáticamente
 * Uso: <TooltipText :text="textoLargo" />
 */
<template>
  <div
    ref="containerRef"
    class="tooltip-text-wrapper"
    @mouseenter="checkTruncation"
    @focus="checkTruncation"
    @mouseleave="showTooltip = false"
  >
    <span
      ref="textRef"
      :class="[
        'tooltip-text-content',
        truncateClasses
      ]"
      :style="textStyle"
    >
      <slot>{{ text }}</slot>
    </span>
    
    <div
      v-if="showTooltip && isTruncated"
      :class="[
        'tooltip-popup',
        `tooltip-${position}`
      ]"
      role="tooltip"
      :id="`tooltip-${tooltipId}`"
    >
      {{ tooltipText || text }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  text: {
    type: String,
    default: ''
  },
  tooltipText: {
    type: String,
    default: ''
  },
  position: {
    type: String,
    default: 'top',
    validator: (value) => ['top', 'bottom', 'left', 'right'].includes(value)
  },
  truncate: {
    type: Boolean,
    default: true
  },
  maxLines: {
    type: Number,
    default: null
  },
  maxWidth: {
    type: String,
    default: ''
  }
})

const containerRef = ref(null)
const textRef = ref(null)
const isTruncated = ref(false)
const showTooltip = ref(false)
const tooltipId = ref(Math.random().toString(36).substr(2, 9))

const truncateClasses = computed(() => {
  if (!props.truncate) return ''
  
  if (props.maxLines) {
    return `line-clamp-${props.maxLines}`
  }
  
  return 'truncate'
})

const textStyle = computed(() => {
  const style = {}
  if (props.maxWidth) {
    style.maxWidth = props.maxWidth
  }
  return style
})

const checkTruncation = () => {
  if (!textRef.value || !props.truncate) {
    isTruncated.value = false
    showTooltip.value = false
    return
  }
  
  const element = textRef.value
  
  if (props.maxLines) {
    const lineHeight = parseInt(window.getComputedStyle(element).lineHeight)
    const maxHeight = lineHeight * props.maxLines
    isTruncated.value = element.scrollHeight > maxHeight
  } else {
    isTruncated.value = element.scrollWidth > element.clientWidth
  }
  
  showTooltip.value = isTruncated.value
}

watch(() => props.text, () => {
  checkTruncation()
})

onMounted(() => {
  checkTruncation()
  window.addEventListener('resize', checkTruncation)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkTruncation)
})
</script>

<style scoped>
.tooltip-text-wrapper {
  position: relative;
  display: inline-block;
  width: 100%;
}

.tooltip-text-content {
  display: inline-block;
  width: 100%;
}

.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.line-clamp-1 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 1;
}

.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
}

.line-clamp-3 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
}

.tooltip-popup {
  position: absolute;
  z-index: 1000;
  padding: 0.5rem 0.75rem;
  background-color: #111827;
  color: #ffffff;
  font-size: 0.875rem;
  border-radius: 0.375rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  white-space: normal;
  pointer-events: none;
  max-width: 300px;
  word-wrap: break-word;
  opacity: 0;
  transition: opacity 0.2s ease;
}

.tooltip-popup {
  opacity: 1;
}

.dark .tooltip-popup {
  background-color: #374151;
  color: #f9fafb;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
}

.tooltip-top {
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  margin-bottom: 0.5rem;
}

.tooltip-top::after {
  content: '';
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  border: 4px solid transparent;
  border-top-color: #111827;
}

.dark .tooltip-top::after {
  border-top-color: #374151;
}

.tooltip-bottom {
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  margin-top: 0.5rem;
}

.tooltip-bottom::after {
  content: '';
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  border: 4px solid transparent;
  border-bottom-color: #111827;
}

.dark .tooltip-bottom::after {
  border-bottom-color: #374151;
}

.tooltip-left {
  right: 100%;
  top: 50%;
  transform: translateY(-50%);
  margin-right: 0.5rem;
}

.tooltip-left::after {
  content: '';
  position: absolute;
  left: 100%;
  top: 50%;
  transform: translateY(-50%);
  border: 4px solid transparent;
  border-left-color: #111827;
}

.dark .tooltip-left::after {
  border-left-color: #374151;
}

.tooltip-right {
  left: 100%;
  top: 50%;
  transform: translateY(-50%);
  margin-left: 0.5rem;
}

.tooltip-right::after {
  content: '';
  position: absolute;
  right: 100%;
  top: 50%;
  transform: translateY(-50%);
  border: 4px solid transparent;
  border-right-color: #111827;
}

.dark .tooltip-right::after {
  border-right-color: #374151;
}
</style>
