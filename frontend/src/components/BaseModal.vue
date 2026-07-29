<script setup>
import { onMounted, onUnmounted, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'md' // 'md' | 'lg'
  }
})

const emit = defineEmits(['update:modelValue'])

function close() {
  emit('update:modelValue', false)
}

function handleKeydown(e) {
  if (e.key === 'Escape' && props.modelValue) {
    close()
  }
}

// Lock body scroll bila modal terbuka
watch(
  () => props.modelValue,
  (isOpen) => {
    document.body.style.overflow = isOpen ? 'hidden' : ''
  }
)

onMounted(() => window.addEventListener('keydown', handleKeydown))
onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
  document.body.style.overflow = ''
})
</script>

<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div v-if="modelValue" class="modal-overlay" @click.self="close">
        <Transition name="modal-pop" appear>
          <div class="modal-box" :class="`modal-box--${size}`" role="dialog" aria-modal="true">
            <button class="modal-close" aria-label="Tutup" @click="close">✕</button>
            <div class="modal-content">
              <slot />
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 1000;
  background: rgba(26, 26, 46, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--space-md);
}

.modal-box {
  position: relative;
  background: var(--color-bg);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-lg);
  width: 100%;
  max-height: 85vh;
  overflow-y: auto;
  padding: var(--space-lg);
}

.modal-box--md {
  max-width: 520px;
}

.modal-box--lg {
  max-width: 900px;
}

.modal-close {
  position: absolute;
  top: var(--space-sm);
  right: var(--space-sm);
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--color-bg-alt);
  color: var(--color-text);
  font-size: var(--fs-base);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background var(--duration-fast) var(--ease-smooth),
              transform var(--duration-fast) var(--ease-bounce);
  z-index: 1;
}

.modal-close:hover {
  background: var(--color-error);
  color: #fff;
  transform: rotate(90deg);
}

/* Overlay fade */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity var(--duration-base) var(--ease-smooth);
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

/* Box pop */
.modal-pop-enter-active {
  transition: transform var(--duration-base) var(--ease-bounce),
              opacity var(--duration-base) var(--ease-smooth);
}
.modal-pop-leave-active {
  transition: transform var(--duration-fast) var(--ease-smooth),
              opacity var(--duration-fast) var(--ease-smooth);
}
.modal-pop-enter-from,
.modal-pop-leave-to {
  transform: scale(0.92) translateY(12px);
  opacity: 0;
}

@media (max-width: 640px) {
  .modal-box {
    padding: var(--space-md);
    max-height: 90vh;
  }
}
</style>