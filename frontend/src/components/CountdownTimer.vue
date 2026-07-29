<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  targetDate: {
    type: String,
    default: '2026-09-15T09:00:00'
  }
})

const now = ref(Date.now())
let intervalId = null

onMounted(() => {
  intervalId = setInterval(() => {
    now.value = Date.now()
  }, 1000)
})

onUnmounted(() => clearInterval(intervalId))

const remaining = computed(() => {
  const diff = Math.max(0, new Date(props.targetDate).getTime() - now.value)

  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  const hours = Math.floor((diff / (1000 * 60 * 60)) % 24)
  const minutes = Math.floor((diff / (1000 * 60)) % 60)
  const seconds = Math.floor((diff / 1000) % 60)

  return { days, hours, minutes, seconds }
})

const units = computed(() => [
  { label: t('hero.days'), value: remaining.value.days },
  { label: t('hero.hours'), value: remaining.value.hours },
  { label: t('hero.minutes'), value: remaining.value.minutes },
  { label: t('hero.seconds'), value: remaining.value.seconds }
])

function pad(num) {
  return String(num).padStart(2, '0')
}
</script>

<template>
  <div class="countdown">
    <div v-for="unit in units" :key="unit.label" class="countdown__block">
      <div class="countdown__digit-wrapper">
        <Transition name="flip" mode="out-in">
          <span :key="unit.value" class="countdown__digit">{{ pad(unit.value) }}</span>
        </Transition>
      </div>
      <span class="countdown__label">{{ unit.label }}</span>
    </div>
  </div>
</template>

<style scoped>
.countdown {
  display: flex;
  gap: var(--space-sm);
}

.countdown__block {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.35rem;
}

.countdown__digit-wrapper {
  width: 4.5rem;
  height: 4.5rem;
  border-radius: var(--radius-md);
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(6px);
  border: 1px solid rgba(255, 255, 255, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.countdown__digit {
  font-family: var(--font-heading);
  font-size: var(--fs-3xl);
  font-weight: 700;
  color: #fff;
}

.countdown__label {
  font-size: var(--fs-sm);
  color: rgba(255, 255, 255, 0.85);
  font-weight: 500;
}

.flip-enter-active,
.flip-leave-active {
  transition: transform var(--duration-base) var(--ease-bounce),
              opacity var(--duration-fast) var(--ease-smooth);
}

.flip-enter-from {
  transform: translateY(100%) rotateX(-90deg);
  opacity: 0;
}

.flip-leave-to {
  transform: translateY(-100%) rotateX(90deg);
  opacity: 0;
}

@media (max-width: 480px) {
  .countdown {
    gap: var(--space-xs);
  }
  .countdown__digit-wrapper {
    width: 3.5rem;
    height: 3.5rem;
  }
  .countdown__digit {
    font-size: var(--fs-xl);
  }
}
</style>