<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { exhibitors, exhibitorCategoryKeys, getCategoryColor } from '@/data/exhibitors'
import ExhibitorCard from './ExhibitorCard.vue'
import FloorPlanGrid from './FloorPlanGrid.vue'
import BaseModal from './BaseModal.vue'

const { t } = useI18n()

const searchQuery = ref('')
const activeCategory = ref('all')
const selectedExhibitor = ref(null)
const selectedId = ref(null)
const isFullMapOpen = ref(false)

// 'all' + semua category key asal, label displayed diterjemah masa render
const categoryFilters = ['all', ...exhibitorCategoryKeys]

const filteredExhibitors = computed(() => {
  return exhibitors.filter((ex) => {
    const matchCategory = activeCategory.value === 'all' || ex.category === activeCategory.value
    const matchSearch =
      searchQuery.value.trim() === '' ||
      ex.name.toLowerCase().includes(searchQuery.value.trim().toLowerCase()) ||
      ex.positions.some((p) => p.toLowerCase().includes(searchQuery.value.trim().toLowerCase()))
    return matchCategory && matchSearch
  })
})

const hasActiveFilters = computed(() => searchQuery.value.trim() !== '' || activeCategory.value !== 'all')

function categoryLabel(key) {
  return key === 'all' ? t('directory.categories.all') : t(`directory.categories.${key}`)
}

function openExhibitorDetail(exhibitor) {
  selectedExhibitor.value = exhibitor
  selectedId.value = exhibitor.id
}

function closeExhibitorDetail() {
  selectedExhibitor.value = null
}

function clearFilters() {
  searchQuery.value = ''
  activeCategory.value = 'all'
}

function openFullMap() {
  isFullMapOpen.value = true
}

function handleFullMapSelect(exhibitor) {
  isFullMapOpen.value = false
  selectedExhibitor.value = exhibitor
}
</script>

<template>
  <section id="directory" class="directory">
    <div class="container">
      <div class="directory__heading" v-reveal>
        <p class="directory__eyebrow">{{ t('directory.eyebrow') }}</p>
        <h2 class="directory__title">{{ t('directory.title') }}</h2>
        <p class="directory__subtitle">{{ t('directory.subtitle') }}</p>
      </div>

      <div class="directory__toolbar" v-reveal="100">
        <div class="directory__search">
          <span class="directory__search-icon">🔍</span>
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="t('directory.searchPlaceholder')"
            class="directory__search-input"
          />
        </div>

        <button class="btn btn-floorplan" :disabled="!hasActiveFilters" @click="clearFilters">
          {{ t('directory.clearFilter') }}
        </button>
      </div>

      <div class="directory__filters" v-reveal="150">
        <button
          v-for="category in categoryFilters"
          :key="category"
          class="filter-chip"
          :class="{ 'filter-chip--active': activeCategory === category }"
          @click="activeCategory = category"
        >
          {{ categoryLabel(category) }}
        </button>
      </div>

      <div class="directory__map-toggle-row" v-reveal="180">
        <button class="directory__map-toggle" @click="openFullMap">
          <span>{{ t('directory.toggleFloorPlan') }}</span>
          <svg viewBox="0 0 24 24" width="18" height="18">
            <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>
      </div>

      <div class="directory__layout" v-reveal="200">
        <div class="directory__list">
          <TransitionGroup
            name="card-list"
            tag="div"
            class="directory__grid"
          >
            <ExhibitorCard
              v-for="exhibitor in filteredExhibitors"
              :key="exhibitor.id"
              :exhibitor="exhibitor"
              :style="selectedId === exhibitor.id ? { borderColor: 'transparent', boxShadow: 'var(--shadow-lg)' } : null"
              @select="openExhibitorDetail"
            />
          </TransitionGroup>

          <p v-if="filteredExhibitors.length === 0" class="directory__empty">
            {{ t('directory.empty') }}
          </p>
        </div>
      </div>
    </div>

    <BaseModal :model-value="!!selectedExhibitor" size="md" @update:model-value="closeExhibitorDetail">
      <template v-if="selectedExhibitor">
        <div
          class="detail-avatar"
          :style="{ background: getCategoryColor(selectedExhibitor.category) }"
        >
          {{ selectedExhibitor.name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase() }}
        </div>
        <h3 class="modal-title">{{ selectedExhibitor.name }}</h3>
        <p class="detail-meta">
          {{ t('directory.booth') }} {{ selectedExhibitor.booth }} &middot; {{ categoryLabel(selectedExhibitor.category) }}
        </p>
        <p class="detail-desc">{{ selectedExhibitor.description }}</p>

        <div class="detail-positions">
          <p class="detail-positions__label">{{ t('directory.positionsLabel') }}</p>
          <ul class="detail-positions__list">
            <li v-for="position in selectedExhibitor.positions" :key="position">{{ position }}</li>
          </ul>
        </div>
      </template>
    </BaseModal>

    <BaseModal v-model="isFullMapOpen" size="lg">
      <h3 class="modal-title">{{ t('directory.fullMapTitle') }}</h3>
      <FloorPlanGrid
        v-if="isFullMapOpen"
        :exhibitors="exhibitors"
        :active-category="activeCategory"
        size="full"
        @select-booth="handleFullMapSelect"
      />
    </BaseModal>
  </section>
</template>

<style scoped>
.directory {
  padding: var(--space-2xl) 0;
  background: var(--color-bg);
}

.directory__heading {
  text-align: center;
  max-width: 640px;
  margin: 0 auto var(--space-lg);
}

.directory__eyebrow {
  color: var(--color-primary);
  font-weight: 600;
  font-size: var(--fs-sm);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: var(--space-xs);
}

.directory__title {
  font-size: var(--fs-2xl);
  color: var(--color-text);
  margin-bottom: var(--space-xs);
}

.directory__subtitle {
  color: var(--color-text-muted);
}

.directory__toolbar {
  display: flex;
  gap: var(--space-sm);
  margin-bottom: var(--space-md);
  flex-wrap: wrap;
}

.directory__search {
  flex: 1;
  min-width: 240px;
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  background: var(--color-bg-alt);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-full);
  padding: 0.75rem 1.25rem;
  transition: border-color var(--duration-fast) var(--ease-smooth);
}

.directory__search:focus-within {
  border-color: var(--color-primary);
}

.directory__search-input {
  flex: 1;
  background: none;
  border: none;
  outline: none;
  font-size: var(--fs-sm);
  color: var(--color-text);
}

.btn-floorplan {
  background: var(--color-secondary);
  color: #fff;
  border-radius: var(--radius-full);
  padding: 0.75rem 1.5rem;
  font-weight: 600;
  font-size: var(--fs-sm);
  white-space: nowrap;
  box-shadow: var(--shadow-sm);
  transition: transform var(--duration-fast) var(--ease-bounce),
              box-shadow var(--duration-fast) var(--ease-smooth);
}

.btn-floorplan:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: var(--shadow-lg);
}

.btn-floorplan:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  box-shadow: none;
}

.directory__filters {
  display: flex;
  gap: var(--space-xs);
  flex-wrap: wrap;
  margin-bottom: var(--space-lg);
}

.filter-chip {
  padding: 0.5rem 1.1rem;
  border-radius: var(--radius-full);
  background: var(--color-bg-alt);
  color: var(--color-text-muted);
  font-size: var(--fs-sm);
  font-weight: 500;
  border: 1px solid transparent;
  transition: all var(--duration-fast) var(--ease-smooth);
}

.filter-chip:hover {
  color: var(--color-secondary);
  border-color: var(--color-secondary-light);
}

.filter-chip--active {
  background: var(--color-secondary);
  color: #fff;
}

.directory__map-toggle-row {
  display: flex;
  justify-content: center;
  margin-bottom: var(--space-sm);
}

.directory__map-toggle {
  height: 36px;
  padding: 0 1.1rem;
  border-radius: var(--radius-full);
  display: flex;
  align-items: center;
  gap: 0.4rem;
  background: var(--color-primary);
  color: #fff;
  font-size: var(--fs-sm);
  font-weight: 600;
  border: 1px solid transparent;
  box-shadow: var(--shadow-sm);
  transition: background var(--duration-fast) var(--ease-smooth),
              color var(--duration-fast) var(--ease-smooth),
              border-color var(--duration-fast) var(--ease-smooth);
}

.directory__map-toggle:hover {
  background: var(--color-bg-alt);
  color: var(--color-text);
  border-color: var(--color-primary);
}

.directory__layout {
  display: flex;
  align-items: flex-start;
  gap: var(--space-lg);
}

.directory__list {
  flex: 1;
  min-width: 0;
}

.directory__grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: var(--space-md);
  position: relative;
}

.directory__empty {
  text-align: center;
  color: var(--color-text-muted);
  padding: var(--space-lg) 0;
}

.card-list-move,
.card-list-enter-active,
.card-list-leave-active {
  transition: all var(--duration-base) var(--ease-smooth);
}

.card-list-enter-from,
.card-list-leave-to {
  opacity: 0;
  transform: scale(0.9) translateY(12px);
}

.card-list-leave-active {
  position: absolute;
}

.modal-title {
  font-size: var(--fs-xl);
  color: var(--color-text);
  margin-bottom: var(--space-xs);
}

.modal-subtitle {
  color: var(--color-text-muted);
  font-size: var(--fs-sm);
  margin-bottom: var(--space-md);
}

.detail-avatar {
  width: 64px;
  height: 64px;
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-family: var(--font-heading);
  font-weight: 700;
  font-size: var(--fs-xl);
  margin-bottom: var(--space-sm);
}

.detail-meta {
  color: var(--color-text-muted);
  font-size: var(--fs-sm);
  margin-bottom: var(--space-sm);
}

.detail-desc {
  color: var(--color-text);
  margin-bottom: var(--space-md);
}

.detail-positions__label {
  font-weight: 600;
  font-size: var(--fs-sm);
  margin-bottom: var(--space-xs);
}

.detail-positions__list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.detail-positions__list li {
  background: var(--color-bg-alt);
  padding: 0.5rem 0.9rem;
  border-radius: var(--radius-sm);
  font-size: var(--fs-sm);
}

@media (max-width: 640px) {
  .directory__toolbar {
    flex-direction: column;
  }
}
</style>