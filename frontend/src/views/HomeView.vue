<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppNavbar from '@/components/AppNavbar.vue'
import HeroSection from '@/components/HeroSection.vue'
import DirectorySection from '@/components/DirectorySection.vue'
import ReservationSection from '@/components/ReservationSection.vue'
import ContactSection from '@/components/ContactSection.vue'
import ExhibitorRegistrationModal from '@/components/ExhibitorRegistrationModal.vue'
import ChatbotWidget from '@/components/ChatbotWidget.vue'

const route = useRoute()
const router = useRouter()

const isExhibitorModalOpen = ref(false)

function handleOpenExhibitorForm() {
  isExhibitorModalOpen.value = true
}

onMounted(() => {
  if (route.query.openExhibitor) {
    isExhibitorModalOpen.value = true
    router.replace({ path: '/' })
  }
})
</script>

<template>
  <div class="home">
    <AppNavbar @open-exhibitor-form="handleOpenExhibitorForm" />
    <HeroSection @open-exhibitor-form="handleOpenExhibitorForm" />
    <DirectorySection />
    <ReservationSection />
    <ContactSection />

    <ExhibitorRegistrationModal v-model="isExhibitorModalOpen" />
    <ChatbotWidget />
  </div>
</template>