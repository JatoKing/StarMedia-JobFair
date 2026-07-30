<script setup>
import { computed } from 'vue'

const colors = ['#B20A2C', '#6B041A', '#FFFBD5', '#E0A83D', '#FF4757']

function randomBetween(min, max) {
  return min + Math.random() * (max - min)
}

function makePiece(id, side) {
  const dir = side === 'left' ? 1 : -1 // cannon kiri tembak ke kanan, cannon kanan tembak ke kiri

  const spreadX = randomBetween(20, 55) * dir // jarak mendatar time puncak (vw)
  const riseY = randomBetween(-70, -40) // naik ke atas time puncak (vh, negatif = naik)
  const fallX = spreadX + randomBetween(-10, 10) * dir // terus hanyut sikit lepas puncak
  const fallY = randomBetween(30, 60) // turun lepas puncak (vh)

  return {
    id,
    xOrigin: side === 'left' ? '2%' : '98%',
    color: colors[id % colors.length],
    delay: randomBetween(0, 0.25),
    duration: randomBetween(1.6, 2.4),
    rotStart: randomBetween(0, 360),
    rotMid: randomBetween(180, 540) * dir,
    rotEnd: randomBetween(540, 900) * dir,
    dxMid: `${spreadX}vw`,
    dyMid: `${riseY}vh`,
    dxEnd: `${fallX}vw`,
    dyEnd: `${riseY + fallY}vh`
  }
}

const pieces = computed(() => {
  const left = Array.from({ length: 35 }, (_, i) => makePiece(i, 'left'))
  const right = Array.from({ length: 35 }, (_, i) => makePiece(i + 35, 'right'))
  return [...left, ...right]
})
</script>

<template>
  <div class="confetti">
    <span
      v-for="piece in pieces"
      :key="piece.id"
      class="confetti__piece"
      :style="{
        left: piece.xOrigin,
        background: piece.color,
        animationDelay: `${piece.delay}s`,
        animationDuration: `${piece.duration}s`,
        '--rot-start': `${piece.rotStart}deg`,
        '--rot-mid': `${piece.rotMid}deg`,
        '--rot-end': `${piece.rotEnd}deg`,
        '--dx-mid': piece.dxMid,
        '--dy-mid': piece.dyMid,
        '--dx-end': piece.dxEnd,
        '--dy-end': piece.dyEnd
      }"
    ></span>
  </div>
</template>

<style scoped>
.confetti {
  position: fixed;
  inset: 0;
  pointer-events: none;
  z-index: 1200;
  overflow: hidden;
}

.confetti__piece {
  position: absolute;
  bottom: -2%;
  width: 9px;
  height: 14px;
  opacity: 0;
  animation-name: confetti-cannon;
  animation-timing-function: cubic-bezier(0.25, 0.46, 0.45, 0.94);
  animation-fill-mode: forwards;
}

@keyframes confetti-cannon {
  0% {
    transform: translate(0, 0) rotate(var(--rot-start));
    opacity: 1;
  }
  45% {
    transform: translate(var(--dx-mid), var(--dy-mid)) rotate(var(--rot-mid));
    opacity: 1;
  }
  100% {
    transform: translate(var(--dx-end), var(--dy-end)) rotate(var(--rot-end));
    opacity: 0;
  }
}
</style>