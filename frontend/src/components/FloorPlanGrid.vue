<script setup>
import { getCategoryColor } from '@/data/exhibitors'

defineProps({
  exhibitors: {
    type: Array,
    required: true
  },
  size: {
    type: String,
    default: 'preview' // 'preview' | 'full'
  }
})

const emit = defineEmits(['select-booth'])
</script>

<template>
  <div class="floor-plan" :class="`floor-plan--${size}`">
    <div
      v-for="exhibitor in exhibitors"
      :key="exhibitor.id"
      class="floor-plan__booth"
      :style="{ '--booth-color': getCategoryColor(exhibitor.category) }"
      @click="emit('select-booth', exhibitor)"
    >
      <span class="floor-plan__booth-no">{{ exhibitor.booth }}</span>
      <span class="floor-plan__booth-name">{{ exhibitor.name }}</span>
    </div>
  </div>
</template>

<style scoped>
.floor-plan {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--space-sm);
  padding: var(--space-md);
  background: var(--color-bg-alt);
  border-radius: var(--radius-lg);
}

.floor-plan__booth {
  aspect-ratio: 1;
  border-radius: var(--radius-sm);
  background: color-mix(in srgb, var(--booth-color) 12%, white);
  border: 2px solid var(--booth-color);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: var(--space-xs);
  cursor: pointer;
  transition: transform var(--duration-fast) var(--ease-bounce),
              box-shadow var(--duration-fast) var(--ease-smooth);
}

.floor-plan__booth:hover {
  transform: translateY(-4px) scale(1.04);
  box-shadow: var(--shadow-md);
  z-index: 1;
}

.floor-plan__booth-no {
  font-family: var(--font-heading);
  font-weight: 700;
  font-size: var(--fs-lg);
  color: var(--booth-color);
}

.floor-plan__booth-name {
  font-size: var(--fs-xs);
  color: var(--color-text-muted);
  margin-top: 0.15rem;
  line-height: 1.2;
}

/* Preview: nama disorok, saiz kecil */
.floor-plan--preview .floor-plan__booth-name {
  display: none;
}

.floor-plan--preview .floor-plan__booth {
  border-radius: var(--radius-sm);
}

/* Full: dalam modal, lebih besar + nama nampak */
.floor-plan--full {
  gap: var(--space-md);
  padding: var(--space-lg);
}

.floor-plan--full .floor-plan__booth-no {
  font-size: var(--fs-xl);
}

@media (max-width: 640px) {
  .floor-plan {
    grid-template-columns: repeat(3, 1fr);
  }
}
</style>