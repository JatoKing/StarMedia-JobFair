import { ref } from 'vue'
import { API_BASE_URL } from '@/config/api'

// Composable untuk fetch senarai career talk sessions dari backend
export function useSessions() {
  const sessions = ref([])
  const isLoading = ref(false)
  const loadError = ref('')

  async function fetchSessions() {
    isLoading.value = true
    loadError.value = ''

    try {
      const response = await fetch(`${API_BASE_URL}/sessions.php`)
      const data = await response.json()

      if (!response.ok) {
        loadError.value = data.message || 'Gagal memuatkan sesi.'
        return
      }

      sessions.value = data.sessions
    } catch {
      loadError.value = 'Tidak dapat menghubungi server. Sila cuba lagi.'
    } finally {
      isLoading.value = false
    }
  }

  return { sessions, isLoading, loadError, fetchSessions }
}