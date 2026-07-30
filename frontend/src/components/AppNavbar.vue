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

      <menu class="navbar__links">
        <li v-for="link in navLinks" :key="link.target">
          <a
            href="#"
            class="navbar__link"
            @click.prevent="scrollToSection(link.target)"
          >
            {{ t(link.key) }}
          </a>
        </li>
        <li>
          <RouterLink to="/spinning-wheel" class="navbar__link cta">
            {{ t('nav.spinWheel') }}
          </RouterLink>
        </li>
      </menu>

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
      <menu v-if="isMobileOpen" class="navbar__mobile">
        <li v-for="link in navLinks" :key="link.target">
          <a
            href="#"
            class="navbar__mobile-link"
            @click.prevent="scrollToSection(link.target)"
          >
            {{ t(link.key) }}
          </a>
        </li>
        <li>
          <RouterLink
            to="/spinning-wheel"
            class="navbar__mobile-link cta"
            @click="isMobileOpen = false"
          >
            {{ t('nav.spinWheel') }}
          </RouterLink>
        </li>
      </menu>
    </Transition>
  </header>
</template>

<style scoped>
@import url('https://fonts.bunny.net/css?family=jura:300,500');

.navbar {
  --gap: 1rem;
  --link-color: var(--color-primary);
  --cta-color: rgb(246 51 154);

  position: fixed;
  top: 0;
  left: 50%;
  right: auto;
  transform: translateX(-50%);
  width: 100%;
  z-index: 100;
  border: 2px solid transparent;
  border-radius: 0;
  will-change: top, width, border-radius;
  transition: top var(--duration-base) var(--ease-smooth),
              width var(--duration-base) var(--ease-smooth),
              border-radius var(--duration-base) var(--ease-smooth),
              border-color var(--duration-base) var(--ease-smooth),
              background var(--duration-base) var(--ease-smooth),
              box-shadow var(--duration-base) var(--ease-smooth);
}

.navbar--scrolled {
  top: var(--space-sm);
  width: min(calc(100% - 2rem), 1100px);
  border-radius: var(--radius-full);
  border-color: rgba(255, 255, 255, 0.5);
  background: rgba(255, 255, 255, 0.55);
  backdrop-filter: blur(20px) saturate(180%);
  -webkit-backdrop-filter: blur(20px) saturate(180%);
  box-shadow: var(--shadow-sm), inset 0 1px 0 rgba(255, 255, 255, 0.6);
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
  transition: color var(--duration-base) var(--ease-smooth);
}

.navbar--scrolled .navbar__logo {
  color: var(--color-secondary);
}

.navbar__logo span {
  color: var(--color-primary);
}

/* --- Menu / links (echo-shadow hover effect) --- */
.navbar__links {
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
  gap: var(--gap);
  list-style: none;
  margin: 0;
  padding: 0;
  font-family: "Jura", sans-serif;
}

.navbar__links li {
  display: flex;
  align-items: center;
  color: var(--link-color);
  --shadow-color: var(--link-color);
}

.navbar__links li + li::before {
  content: '\00B7';
  margin-right: var(--gap);
  color: var(--link-color);
}

.navbar__link {
  font-weight: 500;
  font-size: var(--fs-sm);
  color: inherit;
  text-decoration: none;
  transition: text-shadow 300ms ease-in;
}

.navbar__link.cta,
.navbar__mobile-link.cta {
  --shadow-color: var(--cta-color);
  color: var(--cta-color);
  font-weight: 600;
}

.navbar__link:where(:hover, :focus-visible),
.navbar__mobile-link:where(:hover, :focus-visible) {
  outline: none;
  text-shadow:
    0  2ex color-mix(in srgb, var(--shadow-color) 35%, transparent),
    0 -2ex color-mix(in srgb, var(--shadow-color) 35%, transparent),
    0  4ex color-mix(in srgb, var(--shadow-color) 15%, transparent),
    0 -4ex color-mix(in srgb, var(--shadow-color) 15%, transparent),
    0  6ex color-mix(in srgb, var(--shadow-color) 7.5%, transparent),
    0 -6ex color-mix(in srgb, var(--shadow-color) 7.5%, transparent);
}

/* --- Actions / toggle --- */
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

/* --- Mobile menu --- */
.navbar__mobile {
  display: flex;
  flex-direction: column;
  gap: var(--space-sm);
  padding: var(--space-md);
  margin: 0;
  list-style: none;
  background: var(--color-bg);
  box-shadow: var(--shadow-md);
  font-family: "Jura", sans-serif;
}

.navbar__mobile-link {
  font-weight: 500;
  padding: var(--space-xs) 0;
  border-bottom: 1px solid var(--color-border);
  color: var(--link-color);
  text-decoration: none;
  display: block;
  transition: text-shadow 300ms ease-in;
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