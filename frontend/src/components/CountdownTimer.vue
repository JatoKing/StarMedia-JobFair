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

function pad(num) {
  return String(num).padStart(2, '0')
}

const bars = [
  ['end', 'top'],
  ['side', 'top', 'left'],
  ['side', 'top', 'right'],
  ['middle'],
  ['side', 'bottom', 'left'],
  ['side', 'bottom', 'right'],
  ['end', 'bottom']
]

const units = computed(() => {
  const list = [
    { label: t('hero.days'), value: remaining.value.days },
    { label: t('hero.hours'), value: remaining.value.hours },
    { label: t('hero.minutes'), value: remaining.value.minutes },
    { label: t('hero.seconds'), value: remaining.value.seconds }
  ]

  return list.map(u => ({
    label: u.label,
    digits: pad(u.value).split('')
  }))
})
</script>

<template>
  <div class="countdown-3d">
    <div class="countdown-3d__stage">
      <div class="countdown-3d__wrapper">
        <div class="countdown-3d__scene">
          <div class="countdown">
            <div v-for="unit in units" :key="unit.label" class="countdown__block">
              <div class="seven-segment">
                <!-- digit utama -->
                <div class="seven-segment__group seven-segment__group--main">
                  <div
                    v-for="(digit, di) in unit.digits"
                    :key="di"
                    class="digit"
                    :data-digit="digit"
                  >
                    <span v-for="(bar, bi) in bars" :key="bi" :class="bar"></span>
                  </div>
                </div>

                <!-- reflection 3D -->
                <div class="seven-segment__group seven-segment__group--shadow1">
                  <div
                    v-for="(digit, di) in unit.digits"
                    :key="'sh1-' + di"
                    class="digit"
                    :data-digit="digit"
                  >
                    <span v-for="(bar, bi) in bars" :key="bi" :class="bar"></span>
                  </div>
                </div>
                <div class="seven-segment__group seven-segment__group--shadow2">
                  <div
                    v-for="(digit, di) in unit.digits"
                    :key="'sh2-' + di"
                    class="digit"
                    :data-digit="digit"
                  >
                    <span v-for="(bar, bi) in bars" :key="bi" :class="bar"></span>
                  </div>
                </div>
              </div>

              <span class="countdown__label">{{ unit.label }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.countdown-3d__stage {
  perspective: 45rem;
  display: flex;
  justify-content: center;
}

.countdown-3d__wrapper {
  transform-style: preserve-3d;
}

.countdown-3d__scene {
  position: relative;
  transform-style: preserve-3d;
  animation: countdown-float 9s ease-in-out infinite;
  padding: 2.5em 1em 4em;
}

.countdown {
  display: flex;
  gap: var(--space-lg, 2rem);
  flex-wrap: wrap;
  justify-content: center;
  position: relative;
  z-index: 2;
}

.countdown__block {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
}

/* ---- 7-segment display ---- */
.seven-segment {
  --seg-color: var(--color-secondary, #ffffff);
  /* Kepekatan warna segmen AKTIF (0 - 1). Turunkan nilai ni untuk warna lebih pudar/lembut */
  --seg-intensity: 0.75;

  position: relative;
  display: flex;
  color: var(--seg-color);
  font-size: 1.6rem;
  transform-style: preserve-3d;
}

.seven-segment__group {
  display: flex;
  gap: 0.35em;
}

.seven-segment__group--main {
  position: relative;
  z-index: 2;
}

.seven-segment__group--shadow1,
.seven-segment__group--shadow2 {
  position: absolute;
  top: 100%;
  left: 0;
  pointer-events: none;
  transform-origin: top center;
  transform: rotateX(-90deg) translateZ(0);
}

.seven-segment__group--shadow1 {
  opacity: 0.4;
  filter: blur(0.1em);
  mask-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.7), transparent 65%);
}

.seven-segment__group--shadow2 {
  opacity: 0.2;
  filter: blur(0.3em);
  mask-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.5), transparent 50%);
}

.digit {
  position: relative;
  height: 3.4em;
  aspect-ratio: 1 / 2;
  filter: drop-shadow(0 0 0.15em currentColor) drop-shadow(0 0 0.4em currentColor);
}

.digit span {
  --act: 0;
  --signX: 1;
  --signY: 1;
  position: absolute;
  background-color: currentColor;
  opacity: calc((0.04 + 0.96 * var(--act)) * var(--seg-intensity));
  transition: opacity 0.3s cubic-bezier(0.17, 0.67, 0.5, 1.15);
  transform: scale(var(--signX), var(--signY));
}

.digit span.end {
  clip-path: polygon(15% 0%, 7.5% 20%, 25% 100%, 75% 100%, 92.5% 20%, 85% 0%);
  left: 0;
  width: 100%;
  height: 10%;
}
.digit span.end.top { top: 0; }
.digit span.end.bottom { top: initial; bottom: 0; --signY: -1; }

.digit span.side {
  clip-path: polygon(0% 15%, 20% 7.5%, 100% 22.5%, 100% 85%, 20% 95%, 0% 90%);
  height: 50%;
  width: 20%;
}
.digit span.side.left { top: 0; left: 0; }
.digit span.side.left.bottom { top: initial; bottom: 0; --signY: -1; }
.digit span.side.right { top: 0; left: initial; right: 0; --signX: -1; }
.digit span.side.right.bottom { top: initial; --signY: -1; bottom: 0; }

.digit span.middle {
  clip-path: polygon(22.5% 0%, 6.5% 50%, 22.5% 100%, 77.5% 100%, 93.5% 50%, 77.5% 0%);
  left: 0;
  top: 45%;
  height: 10%;
  width: 100%;
}

.digit[data-digit="0"] :not(.middle) { --act: 1; }
.digit[data-digit="1"] .right { --act: 1; }
.digit[data-digit="2"] :not(.top.left, .bottom.right) { --act: 1; }
.digit[data-digit="3"] :not(.left) { --act: 1; }
.digit[data-digit="4"] :not(.end, .bottom.left) { --act: 1; }
.digit[data-digit="5"] :not(.top.right, .bottom.left) { --act: 1; }
.digit[data-digit="6"] :not(.top.right) { --act: 1; }
.digit[data-digit="7"] .top,
.digit[data-digit="7"] .right { --act: 1; }
.digit[data-digit="8"] > * { --act: 1; }
.digit[data-digit="9"] :not(.bottom.left) { --act: 1; }

.countdown__label {
  font-size: var(--fs-sm, 0.85rem);
  color: var(--color-text-muted, rgba(255, 255, 255, 0.6));
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

@keyframes countdown-float {
  0%, 100% { transform: translate(0, 0); }
  25% { transform: translate(0.3em, -0.5em); }
  50% { transform: translate(-0.2em, -0.8em); }
  75% { transform: translate(-0.35em, -0.3em); }
}

@media (max-width: 480px) {
  .seven-segment {
    font-size: 1.05rem;
  }
  .countdown {
    gap: var(--space-md, 1.25rem);
  }
  .countdown-3d__stage {
    perspective: 30rem;
  }
}
</style>