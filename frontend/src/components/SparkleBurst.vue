<script setup>
import { computed } from 'vue'

const sparkles = computed(() =>
  Array.from({ length: 16 }, (_, i) => {
    const angle = (i / 16) * 360
    return {
      id: i,
      angle,
      delay: Math.random() * 0.2
    }
  })
)
</script>

<template>
  <div class="sparkle-burst">
    <span
      v-for="s in sparkles"
      :key="s.id"
      class="sparkle-burst__star"
      :style="{
        transform: `rotate(${s.angle}deg) translateX(80px)`,
        animationDelay: `${s.delay}s`
      }"
    >✨</span>
  </div>
</template>

<style scoped>
.sparkle-burst {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  pointer-events: none;
  z-index: 5;
}

.sparkle-burst__star {
  position: absolute;
  font-size: 1.4rem;
  opacity: 0;
  animation: sparkle-pop 1s ease-out forwards;
  transform-origin: 0 0;
}

@keyframes sparkle-pop {
  0% {
    opacity: 0;
    scale: 0;
  }
  40% {
    opacity: 1;
    scale: 1;
  }
  100% {
    opacity: 0;
    scale: 1.2;
  }
}
</style>