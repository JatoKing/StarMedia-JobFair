import { ref } from 'vue'
import { API_BASE_URL } from '@/config/api'

// Composable generic untuk submit form ke backend PHP
// Mengurus state: loading, success, error, dan errors per-field dari server
export function useApiForm(endpoint) {
  const isSubmitting = ref(false)
  const isSuccess = ref(false)
  const serverError = ref('')
  const fieldErrors = ref({})

  async function submit(payload) {
    isSubmitting.value = true
    isSuccess.value = false
    serverError.value = ''
    fieldErrors.value = {}

    try {
      const response = await fetch(`${API_BASE_URL}/${endpoint}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
      })

      const data = await response.json()

      if (!response.ok) {
        // Backend PHP validation gagal — data.errors ialah object { fieldName: 'mesej' }
        if (data.errors) {
          fieldErrors.value = data.errors
        }
        serverError.value = data.message || 'Ralat berlaku semasa menghantar borang.'
        return { success: false }
      }

      isSuccess.value = true
      return { success: true, data }
    } catch {
      serverError.value = 'Tidak dapat menghubungi server. Sila cuba lagi.'
      return { success: false }
    } finally {
      isSubmitting.value = false
    }
  }

  function reset() {
    isSuccess.value = false
    serverError.value = ''
    fieldErrors.value = {}
  }

  return {
    isSubmitting,
    isSuccess,
    serverError,
    fieldErrors,
    submit,
    reset
  }
}