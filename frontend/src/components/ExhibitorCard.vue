<script setup>
import { useI18n } from 'vue-i18n'
import { getCategoryColor } from '@/data/exhibitors'

const { t } = useI18n()

defineProps({
  exhibitor: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['select'])

function initials(name) {
  return name
    .split(' ')
    .map((word) => word[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
}
</script>

<template>
  <div class="ex-card" @click="emit('select', exhibitor)">
    <div class="ex-card__avatar" :style="{ background: getCategoryColor(exhibitor.category) }">
      {{ initials(exhibitor.name) }}
    </div>

    <div class="ex-card__body">
      <div class="ex-card__header">
        <h3 class="ex-card__name">{{ exhibitor.name }}</h3>
        <span class="ex-card__booth">{{ t('directory.booth') }} {{ exhibitor.booth }}</span>
      </div>

      <span
        class="ex-card__badge"
        :style="{ background: `${getCategoryColor(exhibitor.category)}1A`, color: getCategoryColor(exhibitor.category) }"
      >
        {{ t(`directory.categories.${exhibitor.category}`) }}
      </span>

      <p class="ex-card__desc">{{ exhibitor.description }}</p>
    </div>
  </div>
</template>

<style scoped>
.ex-card {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  padding: var(--space-md);
  cursor: pointer;
  display: flex;
  gap: var(--space-sm);
  transition: transform var(--duration-fast) var(--ease-bounce),
              box-shadow var(--duration-fast) var(--ease-smooth),
              border-color var(--duration-fast) var(--ease-smooth);
}

.ex-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-lg);
  border-color: transparent;
}

.ex-card__avatar {
  flex-shrink: 0;
  width: 52px;
  height: 52px;
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-family: var(--font-heading);
  font-weight: 700;
  font-size: var(--fs-base);
}

.ex-card__body {
  flex: 1;
  min-width: 0;
}

.ex-card__header {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  gap: var(--space-xs);
}

.ex-card__name {
  font-size: var(--fs-base);
  font-weight: 600;
  color: var(--color-text);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.ex-card__booth {
  font-size: var(--fs-xs);
  color: var(--color-text-muted);
  flex-shrink: 0;
}

.ex-card__badge {
  display: inline-block;
  font-size: var(--fs-xs);
  font-weight: 600;
  padding: 0.2rem 0.6rem;
  border-radius: var(--radius-full);
  margin: 0.4rem 0;
}

.ex-card__desc {
  font-size: var(--fs-sm);
  color: var(--color-text-muted);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>