<script setup>
import { ref } from 'vue'
import AppNavbar from '@/components/AppNavbar.vue'
import HeroSection from '@/components/HeroSection.vue'
import DirectorySection from '@/components/DirectorySection.vue'
import ReservationSection from '@/components/ReservationSection.vue'
import ContactSection from '@/components/ContactSection.vue'
import ExhibitorRegistrationModal from '@/components/ExhibitorRegistrationModal.vue'
import ChatbotWidget from '@/components/ChatbotWidget.vue'

const isExhibitorModalOpen = ref(false)

function handleOpenExhibitorForm() {
  isExhibitorModalOpen.value = true
}
</script>

<template>
  <div class="home">
    <AppNavbar />
    <HeroSection @open-exhibitor-form="handleOpenExhibitorForm" />
    <DirectorySection />
    <ReservationSection />
    <ContactSection />

    <ExhibitorRegistrationModal v-model="isExhibitorModalOpen" />
    <ChatbotWidget />
  </div>
</template>