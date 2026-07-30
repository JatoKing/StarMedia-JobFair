<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { getCategoryColor } from '@/data/exhibitors'

const { t } = useI18n()

const FACILITY_ICONS = {
  info: '<circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.5"/><line x1="12" y1="11" x2="12" y2="16" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="8" r="1" fill="currentColor"/>',
  toilet: '<line x1="12" y1="2" x2="12" y2="22" stroke="currentColor" stroke-width="1" stroke-dasharray="2 2" opacity="0.5"/><circle cx="6" cy="3.4" r="1.7" fill="currentColor"/><rect x="3" y="6" width="6" height="1.5" rx="0.7" fill="currentColor"/><rect x="5.1" y="6" width="1.8" height="7.2" fill="currentColor"/><path d="M4.3 13.2h3.4l1 7.8h-1.8L6 17l-0.9 4h-1.8z" fill="currentColor"/><circle cx="18" cy="3.4" r="1.7" fill="currentColor"/><rect x="15" y="6" width="6" height="1.4" rx="0.7" fill="currentColor"/><path d="M16.3 7.4h3.4l1.5 8.2h-6.4z" fill="currentColor"/><rect x="16.5" y="15.6" width="1.1" height="5.4" fill="currentColor"/><rect x="18.4" y="15.6" width="1.1" height="5.4" fill="currentColor"/>',
  cafe: '<path d="M4 8h13v6a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4V8z" fill="none" stroke="currentColor" stroke-width="1.5"/><path d="M17 9h2a2 2 0 0 1 0 4h-2" fill="none" stroke="currentColor" stroke-width="1.5"/><line x1="6" y1="3" x2="6" y2="5" stroke="currentColor" stroke-width="1.5"/><line x1="10" y1="3" x2="10" y2="5" stroke="currentColor" stroke-width="1.5"/>',
  door: '<rect x="4" y="2" width="14" height="20" rx="1" fill="none" stroke="currentColor" stroke-width="1.5"/><circle cx="14" cy="12" r="1" fill="currentColor"/>'
}

// Ikon tapak kaki (footstep) untuk animasi berjalan di lorong & pintu keluar
const FOOTSTEP_ICON = '<ellipse cx="12" cy="15.5" rx="3.6" ry="5.6" fill="currentColor"/><circle cx="8.6" cy="6.4" r="1.5" fill="currentColor"/><circle cx="11.5" cy="4.6" r="1.7" fill="currentColor"/><circle cx="14.5" cy="5" r="1.5" fill="currentColor"/><circle cx="16.6" cy="7.2" r="1.2" fill="currentColor"/>'

const props = defineProps({
  exhibitors: {
    type: Array,
    required: true
  },
  size: {
    type: String,
    default: 'preview' // 'preview' | 'full'
  },
  showEntrance: {
    type: Boolean,
    default: true
  },
  // Bilangan lajur gerai setiap zon. Kurangkan (cth: 2) bila FloorPlan
  // diletak dalam ruang sempit (contohnya sebelah senarai ExhibitorCard).
  columns: {
    type: Number,
    default: 4
  },
  // Kemudahan yang dipaparkan selepas zon tertentu.
  // 'afterZone' mesti sepadan dengan huruf pertama kod gerai (cth: 'A', 'B', 'C')
  facilities: {
    type: Array,
    default: () => ([
      { id: 'info', icon: 'info', label: 'Kaunter Info', afterZone: 'A' },
      { id: 'toilet', icon: 'toilet', label: 'Tandas', afterZone: 'B' },
      { id: 'cafe', icon: 'cafe', label: 'Kafe', afterZone: 'C' }
    ])
  },
  // Kategori filter yang aktif dari Directory ('all' = tiada filter).
  // Bila diisi, booth yang tak sepadan dipudarkan jadi kelabu.
  activeCategory: {
    type: String,
    default: 'all'
  }
})

const emit = defineEmits(['select-booth'])

const MUTED_COLOR = '#B9B4A8'

function boothColor(exhibitor) {
  const isMuted = props.activeCategory !== 'all' && exhibitor.category !== props.activeCategory
  return isMuted ? MUTED_COLOR : getCategoryColor(exhibitor.category)
}

function initials(name) {
  return name
    .split(' ')
    .map((word) => word[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
}

const zones = computed(() => {
  const groups = {}

  for (const exhibitor of props.exhibitors) {
    const zoneKey = (exhibitor.booth || '').trim().charAt(0).toUpperCase() || '?'
    if (!groups[zoneKey]) groups[zoneKey] = []
    groups[zoneKey].push(exhibitor)
  }

  return Object.keys(groups)
    .sort()
    .map((key, index) => ({
      key,
      label: `Zon ${key}`,
      lorong: index + 1,
      exhibitors: [...groups[key]].sort((a, b) => a.booth.localeCompare(b.booth)),
      facility: props.facilities.find((f) => f.afterZone === key) || null
    }))
})

// Bilangan tapak kaki yang dijana untuk setiap lorong (corak berjalan kiri-kanan)
const FOOTSTEP_COUNT = 6
const footsteps = Array.from({ length: FOOTSTEP_COUNT }, (_, i) => i)

// Kedudukan (%) & orientasi setiap tapak kaki supaya ia duduk terus di atas garis lorong,
// beralun naik-turun merentasi garis (macam melangkah masuk ke booth & keluar semula),
// dan menghadap mengikut arah jalan: separuh kiri menghadap kanan, separuh kanan menghadap kiri
function footstepStyle(index) {
  const spread = 82 / (FOOTSTEP_COUNT - 1) // jarak sama rata dari 6% hingga 88%
  const left = 6 + index * spread
  const isUpStep = index % 2 === 0
  const verticalOffset = isUpStep ? -9 : 9 // naik ke zon atas / turun ke zon bawah
  const facingRight = index < FOOTSTEP_COUNT / 2
  const rotate = facingRight ? 90 : -90

  return {
    left: `${left}%`,
    transform: `translateY(calc(-50% + ${verticalOffset}px)) rotate(${rotate}deg)`,
    '--step-delay': `${index * 0.7}s`
  }
}
</script>

<template>
  <div
    class="floor-plan"
    :class="`floor-plan--${size}`"
    :style="{ '--floor-plan-columns': columns }"
  >
    <div v-if="showEntrance" class="floor-plan__gate">
      <svg viewBox="0 0 24 24" class="floor-plan__gate-icon" v-html="FACILITY_ICONS.door"></svg>
      <span>Pintu Masuk</span>
    </div>

    <template v-for="(zone, zIndex) in zones" :key="zone.key">
      <div class="floor-plan__zone">
        <div class="floor-plan__zone-label">{{ zone.label }}</div>

        <div class="floor-plan__grid">
          <div
            v-for="exhibitor in zone.exhibitors"
            :key="exhibitor.id"
            :data-exhibitor-id="exhibitor.id"
            class="floor-plan__booth"
            :style="{ '--booth-color': boothColor(exhibitor) }"
            @click="emit('select-booth', exhibitor)"
          >
            <span class="floor-plan__booth-no">{{ exhibitor.booth }}</span>
            <div class="floor-plan__booth-avatar" :style="{ background: boothColor(exhibitor) }">
              {{ initials(exhibitor.name) }}
            </div>
            <span class="floor-plan__booth-name">{{ exhibitor.name }}</span>
            <span
              class="floor-plan__booth-badge"
              :style="{ background: `${boothColor(exhibitor)}1A`, color: boothColor(exhibitor) }"
            >
              {{ t(`directory.categories.${exhibitor.category}`) }}
            </span>
            <p class="floor-plan__booth-desc">{{ exhibitor.description }}</p>
          </div>

          <div
            v-if="zone.facility"
            class="floor-plan__facility"
            :class="`floor-plan__facility--${zone.facility.icon}`"
          >
            <svg
              viewBox="0 0 24 24"
              class="floor-plan__facility-icon"
              :class="`floor-plan__facility-icon--${zone.facility.icon}`"
              v-html="FACILITY_ICONS[zone.facility.icon]"
            ></svg>
            <span class="floor-plan__facility-label">{{ zone.facility.label }}</span>
          </div>
        </div>
      </div>

      <div v-if="zIndex < zones.length - 1" class="floor-plan__aisle">
        <span class="floor-plan__aisle-label">Lorong {{ zone.lorong }}</span>
        <span class="floor-plan__footsteps" aria-hidden="true">
          <svg
            v-for="n in footsteps"
            :key="n"
            viewBox="0 0 24 24"
            class="floor-plan__footstep"
            :style="footstepStyle(n)"
            v-html="FOOTSTEP_ICON"
          ></svg>
        </span>
      </div>
    </template>

    <div v-if="showEntrance" class="floor-plan__gate floor-plan__gate--exit">
      <svg viewBox="0 0 24 24" class="floor-plan__gate-icon" v-html="FACILITY_ICONS.door"></svg>
      <span>Pintu Keluar</span>
    </div>
  </div>
</template>

<style scoped>
.floor-plan {
  display: flex;
  flex-direction: column;
  gap: var(--space-md);
  padding: var(--space-md);
  background: var(--color-bg-alt);
  border-radius: var(--radius-lg);
}

.floor-plan__gate {
  align-self: center;
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  padding: var(--space-xs) var(--space-md);
  border: 1px dashed var(--color-border);
  border-radius: var(--radius-sm);
  font-size: var(--fs-xs);
  font-weight: 600;
  color: var(--color-text-muted);
}

.floor-plan__gate--exit {
  order: 999;
  position: relative;
  border-color: rgba(34, 197, 94, 0.5);
  animation: gate-blink-green 2.8s ease-in-out infinite;
}

@keyframes gate-blink-green {
  0%, 100% {
    border-color: rgba(34, 197, 94, 0.35);
    color: var(--color-text-muted);
    background: transparent;
    box-shadow: none;
  }
  50% {
    border-color: #22c55e;
    color: #16a34a;
    background: rgba(34, 197, 94, 0.14);
    box-shadow: 0 0 10px rgba(34, 197, 94, 0.45);
  }
}

.floor-plan__gate-icon {
  width: 16px;
  height: 16px;
}

.floor-plan__zone-label {
  font-family: var(--font-heading);
  font-weight: 700;
  font-size: var(--fs-sm);
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: var(--color-text-muted);
  margin-bottom: var(--space-xs);
}

.floor-plan__grid {
  display: grid;
  grid-template-columns: repeat(var(--floor-plan-columns, 4), 1fr);
  gap: var(--space-sm);
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

.floor-plan__booth-avatar {
  display: none;
  width: 40px;
  height: 40px;
  border-radius: var(--radius-sm);
  align-items: center;
  justify-content: center;
  color: #fff;
  font-family: var(--font-heading);
  font-weight: 700;
  font-size: var(--fs-sm);
  margin: 0.3rem 0;
}

.floor-plan__booth-name {
  font-size: var(--fs-xs);
  color: var(--color-text-muted);
  margin-top: 0.15rem;
  line-height: 1.2;
  font-weight: 600;
}

.floor-plan__booth-badge {
  display: none;
  font-size: var(--fs-xs);
  font-weight: 600;
  padding: 0.15rem 0.55rem;
  border-radius: var(--radius-full);
  margin-top: 0.35rem;
  white-space: nowrap;
}

.floor-plan__booth-desc {
  display: none;
  font-size: var(--fs-xs);
  color: var(--color-text-muted);
  margin-top: 0.35rem;
  line-height: 1.3;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.floor-plan__facility {
  aspect-ratio: 1;
  border-radius: var(--radius-sm);
  border: 2px dashed var(--color-border);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.25rem;
  color: var(--color-text-muted);
  padding: var(--space-xs);
}

/* Kotak Kaunter Info berkelip biru sekali dengan ikon di dalamnya */
.floor-plan__facility--info {
  animation: facility-border-blink-blue 2.8s ease-in-out infinite;
}

@keyframes facility-border-blink-blue {
  0%, 100% {
    border-color: var(--color-border);
  }
  50% {
    border-color: #3b82f6;
  }
}

.floor-plan__facility-icon {
  width: 22px;
  height: 22px;
}

/* Kaunter Info berkelip biru untuk tarik perhatian */
.floor-plan__facility-icon--info {
  color: #3b82f6;
  animation: facility-blink-blue 2.8s ease-in-out infinite;
}

@keyframes facility-blink-blue {
  0%, 100% {
    color: var(--color-text-muted);
    filter: drop-shadow(0 0 0 rgba(59, 130, 246, 0));
  }
  50% {
    color: #3b82f6;
    filter: drop-shadow(0 0 4px rgba(59, 130, 246, 0.6));
  }
}

.floor-plan__facility-label {
  font-size: var(--fs-xs);
  text-align: center;
  line-height: 1.2;
}

.floor-plan__aisle {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 48px;
  padding: var(--space-xs) 0;
  border-top: 2px dashed var(--color-border);
  border-bottom: 2px dashed var(--color-border);
}

.floor-plan__aisle-label {
  position: relative;
  z-index: 2;
  background: var(--color-bg-alt);
  padding: 0.2rem 0.7rem;
  border-radius: var(--radius-full);
  font-size: var(--fs-xs);
  font-weight: 600;
  letter-spacing: 0.03em;
  color: var(--color-text-muted);
}

/* Trek footstep di dalam lorong (antara dua garis dashed) — setiap tapak kaki
   "muncul" satu demi satu, beralun naik-turun dalam lorong seperti melangkah
   masuk ke booth di sebelah menyebelah, lalu kembali semula ke laluan */
.floor-plan__footsteps {
  position: absolute;
  inset: 0;
  z-index: 1;
}

.floor-plan__footstep {
  position: absolute;
  top: 50%;
  width: 13px;
  height: 13px;
  color: var(--color-primary, #6366f1);
  opacity: 0;
  animation: footstep-reveal 4.2s ease-in-out infinite;
  animation-delay: var(--step-delay, 0s);
}

@keyframes footstep-reveal {
  0%, 92%, 100% {
    opacity: 0;
  }
  8%, 78% {
    opacity: 0.85;
  }
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
  gap: var(--space-lg);
}

.floor-plan--full .floor-plan__grid {
  gap: var(--space-md);
}

.floor-plan--full .floor-plan__booth-no {
  font-size: var(--fs-xl);
}

.floor-plan--full .floor-plan__booth {
  aspect-ratio: auto;
  padding: var(--space-sm);
}

.floor-plan--full .floor-plan__booth-avatar,
.floor-plan--full .floor-plan__booth-badge,
.floor-plan--full .floor-plan__booth-desc {
  display: flex;
}

.floor-plan--full .floor-plan__booth-desc {
  display: -webkit-box;
}

@media (max-width: 640px) {
  .floor-plan__grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (prefers-reduced-motion: reduce) {
  .floor-plan__gate--exit {
    animation: none;
    border-color: #22c55e;
  }

  .floor-plan__footstep {
    animation: none;
    display: none;
  }

  .floor-plan__facility-icon--info {
    animation: none;
    color: #3b82f6;
  }

  .floor-plan__facility--info {
    animation: none;
    border-color: #3b82f6;
  }
}
</style>