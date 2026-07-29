<script setup>
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

defineProps({
  session: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['reserve'])

function formatDateTime(datetime) {
  const date = new Date(datetime.replace(' ', 'T'))
  const localeCode = locale.value === 'ms' ? 'ms-MY' : 'en-MY'
  return date.toLocaleString(localeCode, {
    weekday: 'short',
    day: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<template>
  <div class="session-card" :class="{ 'session-card--full': session.seats_remaining <= 0 }">
    <div class="session-card__header">
      <h3 class="session-card__title">{{ session.title }}</h3>
      <span
        class="session-card__badge"
        :class="{ 'session-card__badge--full': session.seats_remaining <= 0 }"
      >
        {{ session.seats_remaining > 0 ? t('reservation.seatsRemaining', { n: session.seats_remaining }) : t('reservation.full') }}
      </span>
    </div>

    <p class="session-card__speaker">🎤 {{ session.speaker }}</p>
    <p class="session-card__time">🕒 {{ formatDateTime(session.session_time) }}</p>

    <div class="session-card__progress">
      <div
        class="session-card__progress-bar"
        :style="{ width: `${(session.seats_taken / session.capacity) * 100}%` }"
      ></div>
    </div>

    <button
      class="btn btn-primary session-card__cta"
      :disabled="session.seats_remaining <= 0"
      @click="emit('reserve', session)"
    >
      {{ session.seats_remaining > 0 ? t('reservation.reserveSlot') : t('reservation.slotFull') }}
    </button>
  </div>
</template>

<style scoped>
.session-card {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  padding: var(--space-md);
  display: flex;
  flex-direction: column;
  gap: var(--space-xs);
  transition: transform var(--duration-fast) var(--ease-bounce),
              box-shadow var(--duration-fast) var(--ease-smooth);
}

.session-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
}

.session-card--full {
  opacity: 0.7;
}

.session-card__header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: var(--space-xs);
}

.session-card__title {
  font-size: var(--fs-base);
  color: var(--color-text);
}

.session-card__badge {
  flex-shrink: 0;
  font-size: var(--fs-xs);
  font-weight: 600;
  padding: 0.25rem 0.7rem;
  border-radius: var(--radius-full);
  background: rgba(0, 217, 192, 0.15);
  color: var(--color-accent);
  white-space: nowrap;
}

.session-card__badge--full {
  background: rgba(255, 71, 87, 0.12);
  color: var(--color-error);
}

.session-card__speaker,
.session-card__time {
  font-size: var(--fs-sm);
  color: var(--color-text-muted);
}

.session-card__progress {
  height: 6px;
  background: var(--color-bg-alt);
  border-radius: var(--radius-full);
  overflow: hidden;
  margin: 0.3rem 0;
}

.session-card__progress-bar {
  height: 100%;
  background: var(--gradient-card);
  border-radius: var(--radius-full);
  transition: width var(--duration-slow) var(--ease-smooth);
}

.session-card__cta {
  width: 100%;
  margin-top: var(--space-xs);
}

.session-card__cta:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none !important;
  background: var(--color-text-muted);
  box-shadow: none;
}
</style>