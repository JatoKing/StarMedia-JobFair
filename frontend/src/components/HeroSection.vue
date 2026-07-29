<script setup>
import { useI18n } from 'vue-i18n'
import CountdownTimer from './CountdownTimer.vue'

const { t } = useI18n()
const emit = defineEmits(['open-exhibitor-form'])
</script>

<template>
  <section id="home" class="hero">
    <div class="hero__blob hero__blob--1"></div>
    <div class="hero__blob hero__blob--2"></div>

    <div class="container hero__content">
      <p class="hero__eyebrow" v-reveal>{{ t('hero.eyebrow') }}</p>
      <h1 class="hero__title" v-reveal="100" v-html="t('hero.title')"></h1>
      <p class="hero__subtitle" v-reveal="200">{{ t('hero.subtitle') }}</p>

      <div class="hero__actions" v-reveal="300">
        <button class="btn btn-primary" @click="emit('open-exhibitor-form')">
          {{ t('hero.ctaExhibitor') }}
        </button>
        <a href="#directory" class="btn btn-outline">{{ t('hero.ctaDirectory') }}</a>
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
  background: var(--gradient-hero);
  overflow: hidden;
  padding: var(--space-2xl) 0 var(--space-xl);
}

.hero__content {
  position: relative;
  z-index: 2;
  max-width: 720px;
}

.hero__eyebrow {
  display: inline-block;
  color: #fff;
  background: rgba(255, 255, 255, 0.18);
  padding: 0.4rem 1rem;
  border-radius: var(--radius-full);
  font-size: var(--fs-sm);
  font-weight: 600;
  margin-bottom: var(--space-sm);
}

.hero__title {
  font-size: var(--fs-4xl);
  color: #fff;
  margin-bottom: var(--space-sm);
}

.hero__subtitle {
  font-size: var(--fs-lg);
  color: rgba(255, 255, 255, 0.9);
  max-width: 560px;
  margin-bottom: var(--space-lg);
}

.hero__actions {
  display: flex;
  gap: var(--space-sm);
  margin-bottom: var(--space-xl);
  flex-wrap: wrap;
}

.hero__countdown-label {
  color: rgba(255, 255, 255, 0.85);
  font-weight: 500;
  margin-bottom: var(--space-xs);
}

.hero__blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(60px);
  opacity: 0.5;
  animation: float 8s ease-in-out infinite;
}

.hero__blob--1 {
  width: 320px;
  height: 320px;
  background: var(--color-accent);
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

@keyframes float {
  0%, 100% { transform: translateY(0) scale(1); }
  50% { transform: translateY(-24px) scale(1.05); }
}

@media (max-width: 768px) {
  .hero__title {
    font-size: var(--fs-3xl);
  }
}
</style>