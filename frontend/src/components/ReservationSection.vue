<script setup>
import { useI18n } from 'vue-i18n'
import { ref } from 'vue'
import { useSessions } from '@/composables/useSessions'
import ReservationModal from './ReservationModal.vue'

const { t, locale } = useI18n()
const { sessions, isLoading, loadError, fetchSessions } = useSessions()

const isModalOpen = ref(false)
const selectedSession = ref(null)

// Tiada foto sebenar per sesi, jadi setiap kad dapat identiti visual
// sendiri melalui gradient tema warna sedia ada.
const CARD_GRADIENTS = [
  'linear-gradient(135deg, var(--color-primary), var(--color-secondary))',
  'linear-gradient(135deg, var(--color-secondary), var(--color-bg-dark))',
  'linear-gradient(135deg, var(--color-warm), var(--color-primary))',
  'linear-gradient(135deg, var(--color-accent), var(--color-secondary))'
]
const CARD_ICONS = ['💼', '🎤', '🚀', '🤝']

function gradientFor(index) {
  return CARD_GRADIENTS[index % CARD_GRADIENTS.length]
}
function iconFor(index) {
  return CARD_ICONS[index % CARD_ICONS.length]
}

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

function openReservation(session) {
  if (session.seats_remaining <= 0) return
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

fetchSessions()
</script>

<template>
  <section id="reservation" class="reservation">
    <div class="container">
      <div class="reservation__heading">
        <p class="reservation__eyebrow">{{ t('reservation.eyebrow') }}</p>
        <h2 class="reservation__title">{{ t('reservation.title') }}</h2>
        <p class="reservation__subtitle">{{ t('reservation.subtitle') }}</p>
      </div>

      <p v-if="isLoading" class="reservation__status">{{ t('reservation.loading') }}</p>
      <p v-else-if="loadError" class="reservation__status reservation__status--error">
        {{ loadError }}
      </p>

      <main v-else class="masonry">
        <div
          v-for="(session, i) in sessions"
          :key="session.id"
          class="masonry__reveal"
        >
          <article
            class="session-card"
            :class="{ 'session-card--full': session.seats_remaining <= 0 }"
            @click="openReservation(session)"
          >
            <div class="session-card__visual" :style="{ background: gradientFor(i) }">
              <span class="session-card__icon">{{ iconFor(i) }}</span>
            </div>

            <p class="session-card__eyebrow">☆ {{ t('reservation.eyebrow') }}</p>
            <h3 class="session-card__title">{{ session.title }}</h3>
            <p class="session-card__meta">🎤 {{ session.speaker }}</p>
            <p class="session-card__meta">🕒 {{ formatDateTime(session.session_time) }}</p>

            <div class="session-card__seats">
              <div class="session-card__progress">
                <div
                  class="session-card__progress-bar"
                  :style="{ width: `${(session.seats_taken / session.capacity) * 100}%` }"
                ></div>
              </div>
              <span>
                {{ session.seats_remaining > 0
                  ? t('reservation.seatsRemaining', { n: session.seats_remaining })
                  : t('reservation.full') }}
              </span>
            </div>

            <button
              class="btn btn-primary session-card__btn"
              type="button"
              :disabled="session.seats_remaining <= 0"
              @click.stop="openReservation(session)"
            >
              {{ session.seats_remaining > 0 ? t('reservation.reserveSlot') : t('reservation.slotFull') }}
            </button>
          </article>
        </div>
      </main>
    </div>

    <ReservationModal
      v-model="isModalOpen"
      :session="selectedSession"
      @reserved="handleReserved"
    />
  </section>
</template>

<style scoped>
@import "https://unpkg.com/open-props" layer(reservation.design-system);

@keyframes reservation-slide-in {
  from {
    scale: 0.85;
    rotate: calc(var(--side, 1) * (5deg * var(--amp, 1)));
  }
}

.reservation {
  padding: var(--space-2xl) 0;
  background: var(--color-bg-alt);
}

.reservation__heading {
  text-align: center;
  max-width: 640px;
  margin: 0 auto var(--space-xl);
  transform-origin: 50% 100%;

  @media (prefers-reduced-motion: no-preference) {
    @supports (animation-timeline: view()) {
      animation: reservation-slide-in linear both;
      animation-timeline: view();
      animation-range: cover 0% contain 20%;
    }
  }
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
  color: var(--color-error, #ff6b6b);
}

/* ---- Masonry scroll-reveal grid ---- */
@layer reservation.design-system {
  .masonry {
    --cols: 1;
    display: grid;
    grid-template-columns: repeat(var(--cols), minmax(0, 1fr));
    gap: var(--space-lg);
    align-items: start;
  }

  @media (min-width: 560px) {
    .masonry { --cols: 2; }
  }
  @media (min-width: 900px) {
    .masonry { --cols: 3; }
  }
  @media (min-width: 1280px) {
    .masonry { --cols: 4; }
  }

  .masonry__reveal {
    display: grid;

    @media (prefers-reduced-motion: no-preference) {
      @supports (animation-timeline: view()) {
        animation: reservation-slide-in linear both;
        animation-timeline: view();
        animation-range: cover 0% contain 25%;
      }
    }
  }

  .masonry__reveal:nth-of-type(2n + 1) { transform-origin: 20vw 100%; --side: -1; }
  .masonry__reveal:nth-of-type(2n)     { transform-origin: -20vw 100%; --side: 1; }

  @media (min-width: 900px) {
    .masonry__reveal:nth-of-type(3n + 1) { transform-origin: 35vw 100%; --side: -1; --amp: 2; }
    .masonry__reveal:nth-of-type(3n + 2) { transform-origin: 0vw 100%; --side: -1; }
    .masonry__reveal:nth-of-type(3n)     { transform-origin: -35vw 100%; --side: 1; --amp: 2; }
  }

  @media (min-width: 1280px) {
    .masonry__reveal:nth-of-type(4n + 1) { transform-origin: 45vw 100%; --side: -1; --amp: 3; }
    .masonry__reveal:nth-of-type(4n + 2) { transform-origin: 15vw 100%; --side: -1; --amp: 1.5; }
    .masonry__reveal:nth-of-type(4n + 3) { transform-origin: -15vw 100%; --side: 1; --amp: 1.5; }
    .masonry__reveal:nth-of-type(4n)     { transform-origin: -45vw 100%; --side: 1; --amp: 3; }
  }
}

/* ---- Session card ---- */
.session-card {
  background: var(--color-bg, #fff);
  border: 1px solid var(--color-border, rgba(0, 0, 0, 0.08));
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
  padding: var(--space-md);
  cursor: pointer;
  transition: transform var(--duration-fast, 0.2s) var(--ease-bounce, ease),
              box-shadow var(--duration-fast, 0.2s) var(--ease-smooth, ease);
}

.session-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}

.session-card--full {
  opacity: 0.75;
}

.session-card__visual {
  height: 140px;
  border-radius: var(--radius-md, 12px);
  margin-bottom: var(--space-sm);
  display: flex;
  align-items: center;
  justify-content: center;
}

.session-card__icon {
  font-size: 2.5rem;
}

.session-card__eyebrow {
  font-size: var(--fs-xs);
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--color-secondary);
  margin: 0 0 0.4em;
}

.session-card__title {
  font-size: var(--fs-md, 1.1rem);
  color: var(--color-text);
  margin: 0 0 0.4em;
  line-height: 1.3;
}

.session-card__meta {
  font-size: var(--fs-sm);
  color: var(--color-text-muted);
  margin: 0 0 0.3em;
}

.session-card__seats {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  margin: var(--space-sm) 0;
}

.session-card__progress {
  height: 6px;
  background: var(--color-border);
  border-radius: var(--radius-full, 999px);
  overflow: hidden;
}

.session-card__progress-bar {
  height: 100%;
  background: var(--gradient-card, var(--color-primary));
  border-radius: var(--radius-full, 999px);
}

.session-card__seats span {
  font-size: var(--fs-xs);
  color: var(--color-text-muted);
}

.session-card__btn {
  width: 100%;
}

.session-card__btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none !important;
  background: var(--color-text-muted);
  box-shadow: none;
}
</style>