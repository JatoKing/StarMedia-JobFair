import { ref } from 'vue'
import { API_BASE_URL } from '@/config/api'

export function useSpinningWheel() {
  const prizes = ref([])
  const isLoading = ref(false)
  const isSpinning = ref(false)
  const loadError = ref('')
  const spinError = ref('')

  async function fetchPrizes() {
    isLoading.value = true
    loadError.value = ''

    try {
      const response = await fetch(`${API_BASE_URL}/spinning-wheel/prizes.php`)
      const data = await response.json()

      if (!response.ok) {
        loadError.value = data.message || 'Gagal memuatkan hadiah.'
        return
      }

      prizes.value = data.prizes
    } catch {
      loadError.value = 'Tidak dapat menghubungi server.'
    } finally {
      isLoading.value = false
    }
  }

  async function spin() {
    isSpinning.value = true
    spinError.value = ''

    try {
      const response = await fetch(`${API_BASE_URL}/spinning-wheel/spin.php`, {
        method: 'POST'
      })
      const data = await response.json()

      if (!response.ok) {
        spinError.value = data.message || 'Ralat berlaku semasa memutar roda.'
        return { success: false }
      }

      // Update baki tempatan supaya wheel & legend terus reflect perubahan
      const target = prizes.value.find((p) => p.id === data.prize.id)
      if (target) {
        target.remaining = data.prize.remaining
      }

      return { success: true, prize: data.prize }
    } catch {
      spinError.value = 'Tidak dapat menghubungi server.'
      return { success: false }
    } finally {
      isSpinning.value = false
    }
  }

  return { prizes, isLoading, isSpinning, loadError, spinError, fetchPrizes, spin }
}