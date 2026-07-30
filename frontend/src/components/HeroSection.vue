<script setup>
import { useI18n } from 'vue-i18n'
import CountdownTimer from './CountdownTimer.vue'

const { t } = useI18n()
const emit = defineEmits(['open-exhibitor-form'])

function makeCluster(count, cx, cy, spread, color) {
  return Array.from({ length: count }, (_, i) => {
    const angle = (i * 137.508) % 360
    const radius = spread * Math.sqrt((i / count))
    const jitter = ((i * 9301 + 49297) % 233280) / 233280
    return {
      x: cx + Math.cos(angle) * radius + (jitter - 0.5) * 20,
      y: cy + Math.sin(angle) * radius + (jitter - 0.5) * 20,
      size: 2 + (jitter * 5),
      opacity: 0.3 + jitter * 0.6,
      color,
      duration: 1.5 + jitter * 2,
      delay: -jitter * 4
    }
  })
}

const particles = [
  ...makeCluster(140, 78, 30, 30, 'var(--color-primary)'),
  ...makeCluster(160, 92, 55, 26, 'var(--color-secondary)')
]
</script>

<template>
  <section id="home" class="hero">
    <div class="hero__blob hero__blob--1"></div>
    <div class="hero__blob hero__blob--2"></div>
    <div class="hero__particles">
      <span
        v-for="(p, i) in particles"
        :key="i"
        class="hero__particle"
        :style="{
          left: p.x + '%',
          top: p.y + '%',
          width: p.size + 'px',
          height: p.size + 'px',
          opacity: p.opacity,
          background: p.color,
          animationDuration: p.duration + 's',
          animationDelay: p.delay + 's'
        }"
      ></span>
    </div>

    <div class="hero__content">
      <h1 class="hero__title" v-reveal="100" v-html="t('hero.title')"></h1>
      <p class="hero__subtitle" v-reveal="200">{{ t('hero.subtitle') }}</p>

      <div class="hero__actions" v-reveal="300">
        <button
          class="btn cta-split"
          :aria-label="t('hero.ctaExhibitor')"
          @click="emit('open-exhibitor-form')"
        >
          <span class="cta-split__sizer" aria-hidden="true">{{ t('hero.ctaExhibitor') }}</span>
          <span class="cta-split__half cta-split__half--top" aria-hidden="true">
            <span>{{ t('hero.ctaExhibitor') }}</span>
          </span>
          <span class="cta-split__half cta-split__half--bottom" aria-hidden="true">
            <span>{{ t('hero.ctaExhibitor') }}</span>
          </span>
        </button>
        <a
          href="#directory"
          class="btn cta-split cta-split--outline"
          :aria-label="t('hero.ctaDirectory')"
        >
          <span class="cta-split__sizer" aria-hidden="true">{{ t('hero.ctaDirectory') }}</span>
          <span class="cta-split__half cta-split__half--top" aria-hidden="true">
            <span>{{ t('hero.ctaDirectory') }}</span>
          </span>
          <span class="cta-split__half cta-split__half--bottom" aria-hidden="true">
            <span>{{ t('hero.ctaDirectory') }}</span>
          </span>
        </a>
      </div>

      <div class="hero__countdown" v-reveal="400">
        <p class="hero__countdown-label">{{ t('hero.countdownLabel') }}</p>
        <CountdownTimer />
      </div>
    </div>
  </section>
</template>

<style scoped>
.hero {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  background: var(--color-bg);
  overflow: hidden;
  padding: var(--space-2xl) clamp(1.5rem, 8vw, 6rem) var(--space-xl);
}

.hero__content {
  position: relative;
  z-index: 2;
  max-width: 720px;
  width: 100%;
}

.hero__title {
  font-size: var(--fs-4xl);
  color: var(--color-text);
  margin-bottom: var(--space-sm);
}

.hero__title :deep(.hero__title-solid) {
  color: var(--color-primary);
}

.hero__subtitle {
  font-size: var(--fs-lg);
  color: var(--color-text-muted);
  max-width: 560px;
  margin-bottom: var(--space-lg);
}

.hero__actions {
  display: flex;
  gap: var(--space-sm);
  margin-bottom: var(--space-xl);
  flex-wrap: wrap;
}


/* ---- Split/tear hover effect: seluruh butang "Jadi Exhibitor" koyak ---- */
.cta-split {
  position: relative;
  background: none;
  box-shadow: none;
}

/* Sizer: invisible, wujud dalam flow semata-mata untuk tetapkan saiz butang
   (dua "half" di bawah position:absolute, jadi keluar dari flow) */
.cta-split__sizer {
  visibility: hidden;
}

.cta-split__half {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--color-primary);
  color: var(--color-text-light);
  border-radius: var(--radius-full);
  transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.25s,
              background 0.3s ease;
}

.cta-split__half--top {
  clip-path: polygon(0% 66%, 0% 0%, 100% 0%, 100% 40%);
}

.cta-split__half--top::after {
  content: '';
  position: absolute;
  top: 36%;
  left: 0;
  width: 100%;
  height: 8%;
  background: var(--color-cream);
  transform: rotateZ(-2.2deg) scaleX(0%);
  transform-origin: right top;
  transition: transform 0.2s ease 0.22s;
}

.cta-split__half--bottom {
  clip-path: polygon(0% 65%, 100% 40%, 100% 110%, 0% 110%);
}

.cta-split__half--bottom::after {
  content: '';
  position: absolute;
  top: 40%;
  left: 0;
  width: 100%;
  height: 8%;
  background: var(--color-cream);
  transform: rotateZ(-2deg) scaleX(0%);
  transform-origin: right top;
  transition: transform 0.2s ease 0.22s;
}

.cta-split:hover .cta-split__half {
  background: var(--color-primary-dark);
}

.cta-split:hover .cta-split__half--top {
  transform: translateY(-0.5em) rotateZ(-3deg);
  transition: transform 0.5s cubic-bezier(0.12, 0.8, 0.57, 1) 0.42s,
              background 0.3s ease;
}

.cta-split:hover .cta-split__half--bottom {
  transform: translateY(0.15em) rotateZ(1.5deg);
  transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.25s,
              background 0.3s ease;
}

.cta-split:hover .cta-split__half--top::after {
  top: 62%;
  transform-origin: left top;
  transform: rotateZ(-2.2deg) scaleX(100%);
}

.cta-split:hover .cta-split__half--bottom::after {
  top: 65%;
  transform-origin: left top;
  transform: rotateZ(-2.1deg) scaleX(100%);
}

/* ---- Variant outline: butang "Lihat Direktori" ---- */
.cta-split--outline .cta-split__half {
  background: transparent;
  border: 2px solid var(--color-border);
  color: var(--color-text);
}

.cta-split--outline .cta-split__half--top::after,
.cta-split--outline .cta-split__half--bottom::after {
  background: var(--color-secondary);
}

.cta-split--outline:hover .cta-split__half {
  background: var(--color-bg-alt);
  border-color: var(--color-secondary);
}

/* ---- Countdown block: susun label & timer sebagai satu unit visual ---- */
.hero__countdown {
  display: grid;
  width: fit-content;
}

.hero__countdown-label {
  color: var(--color-secondary);
  font-weight: 600;
  font-size: var(--fs-sm);
  text-transform: uppercase;
  letter-spacing: 0.15em;
  margin-bottom: var(--space-sm);
  text-align: center;
  text-shadow: 0 0 0.6em color-mix(in srgb, var(--color-secondary) 45%, transparent);
}

.hero__blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.15;
  animation: float 8s ease-in-out infinite;
}

.hero__blob--1 {
  width: 320px;
  height: 320px;
  background: var(--color-cream);
  top: -80px;
  right: -60px;
}

.hero__blob--2 {
  width: 260px;
  height: 260px;
  background: var(--color-warm);
  bottom: -60px;
  right: 15%;
  animation-delay: -3s;
}

.hero__particles {
  position: absolute;
  inset: 0;
  z-index: 1;
  pointer-events: none;
}

.hero__particle {
  position: absolute;
  border-radius: 50%;
  background: #fff;
  animation-name: dotFloat;
  animation-timing-function: ease-in-out;
  animation-iteration-count: infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0) scale(1); }
  50% { transform: translateY(-24px) scale(1.05); }
}

@keyframes dotFloat {
  0%, 100% { transform: translate(-50%, -50%) translateY(0); }
  50% { transform: translate(-50%, -50%) translateY(-10px); }
}

@media (max-width: 768px) {
  .hero__title {
    font-size: var(--fs-3xl);
  }
}
</style>