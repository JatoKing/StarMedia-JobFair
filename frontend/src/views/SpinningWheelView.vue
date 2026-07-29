<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import AppNavbar from '@/components/AppNavbar.vue'
import SpinWheelSVG from '@/components/SpinWheelSVG.vue'
import BaseModal from '@/components/BaseModal.vue'
import ConfettiBurst from '@/components/ConfettiBurst.vue'
import SparkleBurst from '@/components/SparkleBurst.vue'
import { useSpinningWheel } from '@/composables/useSpinningWheel'

const { t } = useI18n()
const { prizes, isLoading, isSpinning, loadError, spinError, fetchPrizes, spin } = useSpinningWheel()

const rotation = ref(0)
const isResultOpen = ref(false)
const wonPrize = ref(null)
const showConfetti = ref(false)
const showSparkle = ref(false)

// Hanya prize dengan remaining > 0 yang ditunjuk sebagai slice di wheel
const availablePrizes = computed(() => prizes.value.filter((p) => p.remaining > 0))
const allSoldOut = computed(() => !isLoading.value && availablePrizes.value.length === 0)

function typeLabel(type) {
  return t(`spinningWheel.types.${type}`)
}

async function handleSpin() {
  if (isSpinning.value || availablePrizes.value.length === 0) return

  const result = await spin()
  if (!result.success) return

  const winningIndex = availablePrizes.value.findIndex((p) => p.id === result.prize.id)
  // Selepas API update 'remaining', prize yang baru habis akan hilang dari availablePrizes
  // pada render seterusnya — tapi kita kira index BERDASARKAN list SEBELUM update supaya
  // animation align dengan slice yang betul semasa spin berlaku
  const n = availablePrizes.value.length
  const seg = 360 / n
  const targetSliceAngle = winningIndex >= 0 ? winningIndex * seg : 0

  const desiredFinalMod = (360 - targetSliceAngle) % 360
  const currentMod = ((rotation.value % 360) + 360) % 360
  const diffToTarget = ((desiredFinalMod - currentMod) % 360 + 360) % 360
  const fullSpins = 6

  rotation.value += fullSpins * 360 + diffToTarget

  // Tunggu animation selesai (CSS transition 4s) baru papar modal result
  setTimeout(() => {
    wonPrize.value = result.prize
    isResultOpen.value = true

    if (result.prize.type === 'grand') {
      showConfetti.value = true
      setTimeout(() => (showConfetti.value = false), 3500)
    } else if (result.prize.type === 'second') {
      showSparkle.value = true
      setTimeout(() => (showSparkle.value = false), 1200)
    }
  }, 4000)
}

function closeResult() {
  isResultOpen.value = false
  wonPrize.value = null
}

onMounted(fetchPrizes)
</script>

<template>
  <div class="spin-page">
    <AppNavbar />

    <section class="spin-hero">
      <div class="container spin-hero__inner">
        <p class="spin-hero__eyebrow">{{ t('spinningWheel.eyebrow') }}</p>
        <h1 class="spin-hero__title">{{ t('spinningWheel.title') }}</h1>
        <p class="spin-hero__subtitle">{{ t('spinningWheel.subtitle') }}</p>

        <p v-if="isLoading" class="spin-hero__status">{{ t('spinningWheel.loading') }}</p>
        <p v-else-if="loadError" class="spin-hero__status spin-hero__status--error">{{ loadError }}</p>

        <template v-else>
          <div class="spin-hero__wheel-area">
            <SpinWheelSVG :prizes="availablePrizes" :rotation="rotation" />
            <SparkleBurst v-if="showSparkle" />
          </div>

          <p v-if="spinError" class="spin-hero__status spin-hero__status--error">{{ spinError }}</p>

          <button
            class="btn btn-primary spin-hero__cta"
            :disabled="isSpinning || allSoldOut"
            @click="handleSpin"
          >
            {{ allSoldOut ? t('spinningWheel.soldOut') : (isSpinning ? t('spinningWheel.spinning') : t('spinningWheel.spinButton')) }}
          </button>

          <div class="spin-hero__legend">
            <p class="spin-hero__legend-title">{{ t('spinningWheel.prizesLabel') }}</p>
            <div class="spin-hero__legend-grid">
              <div v-for="prize in prizes" :key="prize.id" class="legend-item">
                <span class="legend-item__graphic">{{ prize.graphic }}</span>
                <div class="legend-item__info">
                  <p class="legend-item__name">{{ prize.name }}</p>
                  <p class="legend-item__badge" :class="`legend-item__badge--${prize.type}`">
                    {{ typeLabel(prize.type) }} · {{ t('spinningWheel.remaining', { n: prize.remaining }) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>
    </section>

    <BaseModal :model-value="isResultOpen" size="md" @update:model-value="closeResult">
      <template v-if="wonPrize">
        <div class="result-modal" :class="`result-modal--${wonPrize.type}`">
          <div class="result-modal__graphic">{{ wonPrize.graphic }}</div>
          <p class="result-modal__congrats">
            {{ wonPrize.type === 'grand' ? t('spinningWheel.congratsGrand') : t('spinningWheel.congrats') }}
          </p>
          <h3 class="result-modal__prize-name">{{ wonPrize.name }}</h3>
          <span class="result-modal__badge" :class="`result-modal__badge--${wonPrize.type}`">
            {{ typeLabel(wonPrize.type) }}
          </span>
          <button class="btn btn-primary result-modal__close" @click="closeResult">
            {{ t('spinningWheel.closeResult') }}
          </button>
        </div>
      </template>
    </BaseModal>

    <ConfettiBurst v-if="showConfetti" />
  </div>
</template>

<style scoped>
.spin-page {
  min-height: 100vh;
  background: var(--gradient-hero);
}

.spin-hero {
  padding: calc(var(--space-2xl) + 60px) 0 var(--space-2xl);
}

.spin-hero__inner {
  text-align: center;
  max-width: 640px;
  margin: 0 auto;
}

.spin-hero__eyebrow {
  color: rgba(255, 255, 255, 0.9);
  font-weight: 600;
  font-size: var(--fs-sm);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: var(--space-xs);
}

.spin-hero__title {
  font-size: var(--fs-3xl);
  color: #fff;
  margin-bottom: var(--space-xs);
}

.spin-hero__subtitle {
  color: rgba(255, 255, 255, 0.85);
  margin-bottom: var(--space-lg);
}

.spin-hero__status {
  color: #fff;
  padding: var(--space-md) 0;
}

.spin-hero__status--error {
  color: var(--color-warm);
  font-weight: 600;
}

.spin-hero__wheel-area {
  position: relative;
  display: flex;
  justify-content: center;
  margin-bottom: var(--space-lg);
}

.spin-wheel__svg {
  transition: transform 4s cubic-bezier(0.17, 0.67, 0.29, 0.99);
}

.spin-hero__cta {
  font-size: var(--fs-lg);
  padding: 1rem 3rem;
  margin-bottom: var(--space-xl);
}

.spin-hero__cta:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none !important;
}

.spin-hero__legend {
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
  border-radius: var(--radius-lg);
  padding: var(--space-md);
  text-align: left;
}

.spin-hero__legend-title {
  color: #fff;
  font-weight: 600;
  margin-bottom: var(--space-sm);
  text-align: center;
}

.spin-hero__legend-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: var(--space-sm);
}

.legend-item {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  background: rgba(255, 255, 255, 0.9);
  border-radius: var(--radius-sm);
  padding: 0.6rem 0.8rem;
}

.legend-item__graphic {
  font-size: 1.5rem;
}

.legend-item__name {
  font-weight: 600;
  font-size: var(--fs-sm);
  color: var(--color-text);
}

.legend-item__badge {
  font-size: var(--fs-xs);
  color: var(--color-text-muted);
  font-weight: 500;
}

.legend-item__badge--grand {
  color: var(--color-warm);
}

.legend-item__badge--second {
  color: var(--color-secondary);
}

.legend-item__badge--consolation {
  color: var(--color-accent);
}

/* Result Modal */
.result-modal {
  text-align: center;
  padding: var(--space-sm);
}

.result-modal__graphic {
  font-size: 4rem;
  margin-bottom: var(--space-sm);
}

.result-modal__congrats {
  font-weight: 600;
  color: var(--color-text-muted);
  margin-bottom: var(--space-xs);
}

.result-modal__prize-name {
  font-size: var(--fs-xl);
  color: var(--color-text);
  margin-bottom: var(--space-sm);
}

.result-modal__badge {
  display: inline-block;
  padding: 0.3rem 0.9rem;
  border-radius: var(--radius-full);
  font-size: var(--fs-xs);
  font-weight: 600;
  margin-bottom: var(--space-md);
}

.result-modal__badge--grand {
  background: rgba(255, 201, 60, 0.2);
  color: #b8860b;
}

.result-modal__badge--second {
  background: rgba(108, 92, 231, 0.15);
  color: var(--color-secondary);
}

.result-modal__badge--consolation {
  background: rgba(0, 217, 192, 0.15);
  color: var(--color-accent);
}

.result-modal__close {
  width: 100%;
}
</style>
