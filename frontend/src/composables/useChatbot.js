import { ref } from 'vue'
import { API_BASE_URL } from '@/config/api'

// Composable untuk urus state perbualan chatbot
export function useChatbot() {
  const messages = ref([
    {
      role: 'assistant',
      content: 'Hai! 👋 Saya pembantu maya Job Fair 2026. Ada apa-apa yang boleh saya bantu?'
    }
  ])
  const isSending = ref(false)
  const error = ref('')

  async function sendMessage(userMessage) {
    if (!userMessage.trim()) return

    messages.value.push({ role: 'user', content: userMessage })
    isSending.value = true
    error.value = ''

    // Hantar history (tanpa mesej user terkini, sebab tu dihantar berasingan sebagai 'message')
    const history = messages.value.slice(0, -1).map((m) => ({
      role: m.role,
      content: m.content
    }))

    try {
      const response = await fetch(`${API_BASE_URL}/chatbot.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: userMessage, history })
      })

      const data = await response.json()

      if (!response.ok) {
        error.value = data.message || 'Ralat berlaku.'
        messages.value.push({
          role: 'assistant',
          content: 'Maaf, saya tidak dapat menjawab buat masa ini. Sila cuba lagi atau guna borang Hubungi Kami.'
        })
        return
      }

      messages.value.push({ role: 'assistant', content: data.reply })
    } catch {
      error.value = 'Tidak dapat menghubungi server.'
      messages.value.push({
        role: 'assistant',
        content: 'Maaf, sambungan terputus. Sila cuba lagi.'
      })
    } finally {
      isSending.value = false
    }
  }

  return { messages, isSending, error, sendMessage }
}