<script setup>
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()

const languages = [
  { code: 'ms', label: 'BM' },
  { code: 'en', label: 'EN' }
]

function setLocale(code) {
  locale.value = code
}
</script>

<template>
  <div class="lang-switcher">
    <button
      v-for="lang in languages"
      :key="lang.code"
      class="lang-switcher__btn"
      :class="{ 'lang-switcher__btn--active': locale === lang.code }"
      @click="setLocale(lang.code)"
    >
      {{ lang.label }}
    </button>
  </div>
</template>

<style scoped>
.lang-switcher {
  display: flex;
  gap: 0.25rem;
  background: var(--color-bg-alt);
  border-radius: var(--radius-full);
  padding: 0.25rem;
}

.lang-switcher__btn {
  padding: 0.35rem 0.75rem;
  border-radius: var(--radius-full);
  font-size: var(--fs-xs);
  font-weight: 600;
  color: var(--color-text-muted);
  transition: all var(--duration-fast) var(--ease-smooth);
}

.lang-switcher__btn--active {
  background: var(--color-secondary);
  color: #fff;
}

.lang-switcher__btn:hover:not(.lang-switcher__btn--active) {
  color: var(--color-secondary);
}
</style>