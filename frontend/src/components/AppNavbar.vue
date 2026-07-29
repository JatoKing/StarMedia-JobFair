<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import LanguageSwitcher from './LanguageSwitcher.vue'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()

const isScrolled = ref(false)
const isMobileOpen = ref(false)

const navLinks = [
  { key: 'nav.home', target: '#home' },
  { key: 'nav.directory', target: '#directory' },
  { key: 'nav.exhibitor', target: '#exhibitor' },
  { key: 'nav.contact', target: '#contact' }
]

function handleScroll() {
  isScrolled.value = window.scrollY > 40
}

function doScroll(target) {
  const el = document.querySelector(target)
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

function scrollToSection(target) {
  isMobileOpen.value = false

  if (route.path !== '/') {
    router.push('/').then(() => {
      nextTick(() => setTimeout(() => doScroll(target), 100))
    })
  } else {
    doScroll(target)
  }
}

onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))
</script>

<template>
  <header class="navbar" :class="{ 'navbar--scrolled': isScrolled }">
    <div class="navbar__inner container">
      <a href="#home" class="navbar__logo" @click.prevent="scrollToSection('#home')">
        Job<span>Fair</span>
      </a>
    
      <nav class="navbar__links">
        <a
          v-for="link in navLinks"
          :key="link.target"
          href="#"
          class="navbar__link"
          @click.prevent="scrollToSection(link.target)"
        >
          {{ t(link.key) }}
        </a>
        <RouterLink to="/spinning-wheel" class="navbar__link navbar__link--highlight">
          {{ t('nav.spinWheel') }}
        </RouterLink>
      </nav>

      <div class="navbar__actions">
        <LanguageSwitcher class="navbar__lang-desktop" />

        <button
          class="navbar__toggle"
          :aria-expanded="isMobileOpen"
          aria-label="Toggle menu"
          @click="isMobileOpen = !isMobileOpen"
        >
          <span :class="{ 'is-open': isMobileOpen }"></span>
        </button>
      </div>
    </div>

    <Transition name="mobile-menu">
      <nav v-if="isMobileOpen" class="navbar__mobile">
        <a
          v-for="link in navLinks"
          :key="link.target"
          href="#"
          class="navbar__mobile-link"
          @click.prevent="scrollToSection(link.target)"
        >
          {{ t(link.key) }}
        </a>
        <RouterLink to="/spinning-wheel" class="navbar__mobile-link navbar__link--highlight" @click="isMobileOpen = false">
          {{ t('nav.spinWheel') }}
        </RouterLink>
        <LanguageSwitcher class="navbar__lang-mobile" />
      </nav>
    </Transition>
  </header>
</template>

<style scoped>
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 100;
  transition: background var(--duration-base) var(--ease-smooth),
              box-shadow var(--duration-base) var(--ease-smooth);
}

.navbar--scrolled {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(12px);
  box-shadow: var(--shadow-sm);
}

.navbar__inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: var(--space-sm);
  padding-bottom: var(--space-sm);
}

.navbar__logo {
  font-family: var(--font-heading);
  font-size: var(--fs-xl);
  font-weight: 700;
  color: var(--color-text);
}

.navbar--scrolled .navbar__logo {
  color: var(--color-secondary);
}

.navbar__logo span {
  color: var(--color-primary);
}

.navbar__links {
  display: flex;
  align-items: center;
  gap: var(--space-lg);
}

.navbar__link {
  font-weight: 500;
  font-size: var(--fs-sm);
  color: var(--color-text);
  position: relative;
  padding: 0.25rem 0;
  transition: color var(--duration-fast) var(--ease-smooth);
}

.navbar__link::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -2px;
  width: 0;
  height: 2px;
  background: var(--color-primary);
  transition: width var(--duration-base) var(--ease-smooth);
}

.navbar__link:hover {
  color: var(--color-primary);
}

.navbar__link:hover::after {
  width: 100%;
}

.navbar__link--highlight {
  background: var(--gradient-hero);
  color: #fff !important;
  padding: 0.4rem 1rem;
  border-radius: var(--radius-full);
}

.navbar__link--highlight::after {
  display: none;
}

.navbar__link--highlight:hover {
  transform: translateY(-2px);
  color: #fff;
}

.navbar__actions {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
}

.navbar__lang-mobile {
  display: none;
}

.navbar__toggle {
  display: none;
  width: 32px;
  height: 24px;
  position: relative;
}

.navbar__toggle span,
.navbar__toggle span::before,
.navbar__toggle span::after {
  content: '';
  position: absolute;
  height: 2px;
  width: 100%;
  background: var(--color-text);
  border-radius: 2px;
  transition: transform var(--duration-fast) var(--ease-smooth), opacity var(--duration-fast);
}

.navbar__toggle span {
  top: 50%;
  transform: translateY(-50%);
}

.navbar__toggle span::before {
  top: -8px;
}

.navbar__toggle span::after {
  top: 8px;
}

.navbar__toggle span.is-open {
  background: transparent;
}

.navbar__toggle span.is-open::before {
  top: 0;
  transform: rotate(45deg);
}

.navbar__toggle span.is-open::after {
  top: 0;
  transform: rotate(-45deg);
}

.navbar__mobile {
  display: flex;
  flex-direction: column;
  gap: var(--space-sm);
  padding: var(--space-md);
  background: var(--color-bg);
  box-shadow: var(--shadow-md);
}

.navbar__mobile-link {
  font-weight: 500;
  padding: var(--space-xs) 0;
  border-bottom: 1px solid var(--color-border);
}

.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition: all var(--duration-base) var(--ease-smooth);
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
  opacity: 0;
  transform: translateY(-12px);
}

@media (max-width: 768px) {
  .navbar__links {
    display: none;
  }
  .navbar__toggle {
    display: block;
  }
  .navbar__lang-desktop {
    display: none;
  }
  .navbar__lang-mobile {
    display: flex;
    margin-top: var(--space-xs);
  }
}
</style>