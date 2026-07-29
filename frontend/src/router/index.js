import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import SpinningWheelView from '@/views/SpinningWheelView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/spinning-wheel',
      name: 'spinning-wheel',
      component: SpinningWheelView
    }
  ]
})

export default router