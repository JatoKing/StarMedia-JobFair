<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useSessions } from '@/composables/useSessions'
import SessionCard from './SessionCard.vue'
import ReservationModal from './ReservationModal.vue'

const { t } = useI18n()
const { sessions, isLoading, loadError, fetchSessions } = useSessions()

const isModalOpen = ref(false)
const selectedSession = ref(null)

function openReservation(session) {
  selectedSession.value = session
  isModalOpen.value = true
}

function handleReserved(sessionId) {
  const session = sessions.value.find((s) => s.id === sessionId)
  if (session) {
    session.seats_taken += 1
    session.seats_remaining -= 1
  }
}

onMounted(fetchSessions)
</script>

<template>
  <section id="reservation" class="reservation">
    <div class="container">
      <div class="reservation__heading" v-reveal>
        <p class="reservation__eyebrow">{{ t('reservation.eyebrow') }}</p>
        <h2 class="reservation__title">{{ t('reservation.title') }}</h2>
        <p class="reservation__subtitle">{{ t('reservation.subtitle') }}</p>
      </div>

      <p v-if="isLoading" class="reservation__status">{{ t('reservation.loading') }}</p>
      <p v-else-if="loadError" class="reservation__status reservation__status--error">
        {{ loadError }}
      </p>

      <div v-else class="reservation__grid" v-reveal="150">
        <SessionCard
          v-for="session in sessions"
          :key="session.id"
          :session="session"
          @reserve="openReservation"
        />
      </div>
    </div>

    <ReservationModal
      v-model="isModalOpen"
      :session="selectedSession"
      @reserved="handleReserved"
    />
  </section>
</template>

<style scoped>
.reservation {
  padding: var(--space-2xl) 0;
  background: var(--color-bg-alt);
}

.reservation__heading {
  text-align: center;
  max-width: 640px;
  margin: 0 auto var(--space-lg);
}

.reservation__eyebrow {
  color: var(--color-secondary);
  font-weight: 600;
  font-size: var(--fs-sm);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: var(--space-xs);
}

.reservation__title {
  font-size: var(--fs-2xl);
  color: var(--color-text);
  margin-bottom: var(--space-xs);
}

.reservation__subtitle {
  color: var(--color-text-muted);
}

.reservation__status {
  text-align: center;
  color: var(--color-text-muted);
  padding: var(--space-lg) 0;
}

.reservation__status--error {
  color: var(--color-error);
}

.reservation__grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: var(--space-md);
}
</style>