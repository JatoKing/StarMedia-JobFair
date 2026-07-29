<script setup>
import { computed } from 'vue'

const props = defineProps({
  prizes: {
    type: Array,
    required: true
  },
  rotation: {
    type: Number,
    default: 0
  }
})

const size = 320
const center = size / 2
const radius = size / 2 - 4

// Palette warna cycle ikut index (Vibrant Playful theme)
const palette = ['#FF6B35', '#6C5CE7', '#00D9C0', '#FFC93C', '#FF4757', '#A29BFE']

function colorFor(prize, index) {
  if (prize.type === 'grand') return '#FFC93C' // gold highlight untuk grand prize
  return palette[index % palette.length]
}

// Convert sudut (0deg = atas/12 o'clock, ikut jam) ke koordinat SVG
function polarToCartesian(angleDeg) {
  const angleRad = (angleDeg * Math.PI) / 180
  return {
    x: center + radius * Math.sin(angleRad),
    y: center - radius * Math.cos(angleRad)
  }
}

const slices = computed(() => {
  const n = props.prizes.length
  if (n === 0) return []

  const seg = 360 / n

  return props.prizes.map((prize, i) => {
    const startAngle = -seg / 2
    const endAngle = seg / 2
    const p1 = polarToCartesian(startAngle)
    const p2 = polarToCartesian(endAngle)
    const largeArcFlag = seg > 180 ? 1 : 0

    const path = `M ${center},${center} L ${p1.x},${p1.y} A ${radius},${radius} 0 ${largeArcFlag},1 ${p2.x},${p2.y} Z`

    // Label diposisikan pada tengah slice (radius 65%), diputar ikut sudut tengah slice
    const labelRadius = radius * 0.65
    const labelAngle = 0 // relatif kepada base wedge (sebab base wedge sentiasa center di 0)

    return {
      path,
      color: colorFor(prize, i),
      groupRotation: i * seg,
      labelRadius,
      labelAngle,
      graphic: prize.graphic,
      name: prize.name
    }
  })
})
</script>

<template>
  <div class="spin-wheel">
    <svg
      :width="size"
      :height="size"
      :viewBox="`0 0 ${size} ${size}`"
      class="spin-wheel__svg"
      :style="{ transform: `rotate(${rotation}deg)` }"
    >
      <g v-for="(slice, i) in slices" :key="i" :transform="`rotate(${slice.groupRotation} ${center} ${center})`">
        <path :d="slice.path" :fill="slice.color" stroke="#fff" stroke-width="2" />
        <text
          :x="center"
          :y="center - slice.labelRadius"
          text-anchor="middle"
          class="spin-wheel__emoji"
        >
          {{ slice.graphic }}
        </text>
      </g>

      <circle :cx="center" :cy="center" r="28" fill="#fff" stroke="var(--color-secondary)" stroke-width="3" />
    </svg>

    <div class="spin-wheel__pointer">▼</div>
  </div>
</template>

<style scoped>
.spin-wheel {
  position: relative;
  display: inline-block;
}

.spin-wheel__svg {
  filter: drop-shadow(0 8px 24px rgba(26, 26, 46, 0.2));
}

.spin-wheel__emoji {
  font-size: 1.6rem;
}

.spin-wheel__pointer {
  position: absolute;
  top: -14px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 2rem;
  color: var(--color-primary);
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
  z-index: 2;
}
</style>