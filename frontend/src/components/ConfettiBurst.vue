<script setup>
import { computed } from 'vue'

const colors = ['#B20A2C', '#6B041A', '#FFFBD5', '#E0A83D', '#FF4757']

const pieces = computed(() =>
  Array.from({ length: 60 }, (_, i) => ({
    id: i,
    left: Math.random() * 100,
    delay: Math.random() * 0.5,
    duration: 2 + Math.random() * 1.5,
    color: colors[i % colors.length],
    rotation: Math.random() * 360
  }))
)
</script>

<template>
  <div class="confetti">
    <span
      v-for="piece in pieces"
      :key="piece.id"
      class="confetti__piece"
      :style="{
        left: `${piece.left}%`,
        animationDelay: `${piece.delay}s`,
        animationDuration: `${piece.duration}s`,
        background: piece.color,
        transform: `rotate(${piece.rotation}deg)`
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
  top: -20px;
  width: 10px;
  height: 16px;
  opacity: 0.9;
  animation: confetti-fall linear forwards;
}

@keyframes confetti-fall {
  0% {
    transform: translateY(0) rotate(0deg);
    opacity: 1;
  }
  100% {
    transform: translateY(110vh) rotate(720deg);
    opacity: 0.3;
  }
}
</style>