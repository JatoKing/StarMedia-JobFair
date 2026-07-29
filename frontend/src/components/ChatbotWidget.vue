<script setup>
import { ref, nextTick, watch } from 'vue'
import { useChatbot } from '@/composables/useChatbot'

const isOpen = ref(false)
const inputText = ref('')
const messagesContainer = ref(null)

const { messages, isSending, sendMessage } = useChatbot()

async function handleSend() {
  const text = inputText.value.trim()
  if (!text || isSending.value) return

  inputText.value = ''
  await sendMessage(text)
}

function scrollToBottom() {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

watch(messages, scrollToBottom, { deep: true })
watch(isOpen, (open) => {
  if (open) scrollToBottom()
})
</script>

<template>
  <div class="chatbot">
    <!-- Floating toggle button -->
    <button class="chatbot__fab" :class="{ 'chatbot__fab--active': isOpen }" @click="isOpen = !isOpen">
      <Transition name="icon-flip" mode="out-in">
        <span v-if="!isOpen" key="chat">💬</span>
        <span v-else key="close">✕</span>
      </Transition>
    </button>

    <!-- Chat window -->
    <Transition name="chat-pop">
      <div v-if="isOpen" class="chatbot__window">
        <div class="chatbot__header">
          <div class="chatbot__header-info">
            <span class="chatbot__avatar">🤖</span>
            <div>
              <p class="chatbot__title">Job Fair Assistant</p>
              <p class="chatbot__status">
                <span class="chatbot__status-dot"></span> Online
              </p>
            </div>
          </div>
        </div>

        <div ref="messagesContainer" class="chatbot__messages">
          <TransitionGroup name="message-pop">
            <div
              v-for="(msg, index) in messages"
              :key="index"
              class="chatbot__message"
              :class="`chatbot__message--${msg.role}`"
            >
              <span v-if="msg.role === 'assistant'" class="chatbot__message-avatar">🤖</span>
              <p class="chatbot__bubble">{{ msg.content }}</p>
            </div>
          </TransitionGroup>

          <div v-if="isSending" class="chatbot__message chatbot__message--assistant">
            <span class="chatbot__message-avatar">🤖</span>
            <div class="chatbot__typing">
              <span></span><span></span><span></span>
            </div>
          </div>
        </div>

        <form class="chatbot__input-row" @submit.prevent="handleSend">
          <input
            v-model="inputText"
            type="text"
            placeholder="Taip soalan anda..."
            class="chatbot__input"
            :disabled="isSending"
          />
          <button type="submit" class="chatbot__send" :disabled="isSending || !inputText.trim()">
            ➤
          </button>
        </form>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.chatbot {
  position: fixed;
  bottom: var(--space-lg);
  right: var(--space-lg);
  z-index: 900;
}

.chatbot__fab {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: var(--gradient-hero);
  color: #fff;
  font-size: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: var(--shadow-lg);
  transition: transform var(--duration-fast) var(--ease-bounce);
}

.chatbot__fab:hover {
  transform: scale(1.08);
}

.chatbot__fab--active {
  background: var(--color-bg-dark);
}

.icon-flip-enter-active,
.icon-flip-leave-active {
  transition: all var(--duration-fast) var(--ease-bounce);
}
.icon-flip-enter-from,
.icon-flip-leave-to {
  opacity: 0;
  transform: rotate(90deg) scale(0.5);
}

.chatbot__window {
  position: absolute;
  bottom: 76px;
  right: 0;
  width: 360px;
  height: 500px;
  background: var(--color-bg);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-lg);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.chat-pop-enter-active {
  transition: all var(--duration-base) var(--ease-bounce);
  transform-origin: bottom right;
}
.chat-pop-leave-active {
  transition: all var(--duration-fast) var(--ease-smooth);
  transform-origin: bottom right;
}
.chat-pop-enter-from,
.chat-pop-leave-to {
  opacity: 0;
  transform: scale(0.85) translateY(12px);
}

.chatbot__header {
  background: var(--gradient-hero);
  padding: var(--space-sm) var(--space-md);
  flex-shrink: 0;
}

.chatbot__header-info {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
}

.chatbot__avatar {
  font-size: 1.5rem;
  background: rgba(255, 255, 255, 0.2);
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.chatbot__title {
  color: #fff;
  font-weight: 600;
  font-size: var(--fs-sm);
}

.chatbot__status {
  color: rgba(255, 255, 255, 0.85);
  font-size: var(--fs-xs);
  display: flex;
  align-items: center;
  gap: 0.3rem;
}

.chatbot__status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--color-accent);
  display: inline-block;
}

.chatbot__messages {
  flex: 1;
  overflow-y: auto;
  padding: var(--space-md);
  display: flex;
  flex-direction: column;
  gap: var(--space-sm);
  background: var(--color-bg-alt);
}

.chatbot__message {
  display: flex;
  align-items: flex-end;
  gap: 0.4rem;
  max-width: 85%;
}

.chatbot__message--user {
  align-self: flex-end;
  flex-direction: row-reverse;
}

.chatbot__message--assistant {
  align-self: flex-start;
}

.chatbot__message-avatar {
  font-size: 1.1rem;
  flex-shrink: 0;
}

.chatbot__bubble {
  padding: 0.6rem 0.9rem;
  border-radius: var(--radius-md);
  font-size: var(--fs-sm);
  line-height: 1.4;
}

.chatbot__message--assistant .chatbot__bubble {
  background: #fff;
  color: var(--color-text);
  border-bottom-left-radius: 4px;
  box-shadow: var(--shadow-sm);
}

.chatbot__message--user .chatbot__bubble {
  background: var(--color-primary);
  color: #fff;
  border-bottom-right-radius: 4px;
}

.message-pop-enter-active {
  transition: all var(--duration-base) var(--ease-bounce);
}
.message-pop-enter-from {
  opacity: 0;
  transform: translateY(8px) scale(0.95);
}

.chatbot__typing {
  background: #fff;
  padding: 0.7rem 1rem;
  border-radius: var(--radius-md);
  border-bottom-left-radius: 4px;
  display: flex;
  gap: 0.25rem;
  box-shadow: var(--shadow-sm);
}

.chatbot__typing span {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--color-text-muted);
  animation: typing-bounce 1.2s infinite ease-in-out;
}

.chatbot__typing span:nth-child(2) {
  animation-delay: 0.15s;
}
.chatbot__typing span:nth-child(3) {
  animation-delay: 0.3s;
}

@keyframes typing-bounce {
  0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
  30% { transform: translateY(-4px); opacity: 1; }
}

.chatbot__input-row {
  display: flex;
  gap: var(--space-xs);
  padding: var(--space-sm);
  background: var(--color-bg);
  border-top: 1px solid var(--color-border);
  flex-shrink: 0;
}

.chatbot__input {
  flex: 1;
  padding: 0.6rem 1rem;
  border: 1.5px solid var(--color-border);
  border-radius: var(--radius-full);
  font-size: var(--fs-sm);
  outline: none;
  transition: border-color var(--duration-fast) var(--ease-smooth);
}

.chatbot__input:focus {
  border-color: var(--color-secondary);
}

.chatbot__input:disabled {
  opacity: 0.6;
}

.chatbot__send {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--color-primary);
  color: #fff;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform var(--duration-fast) var(--ease-bounce),
              opacity var(--duration-fast) var(--ease-smooth);
}

.chatbot__send:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.chatbot__send:not(:disabled):hover {
  transform: scale(1.1);
}

@media (max-width: 480px) {
  .chatbot__window {
    width: calc(100vw - 2rem);
    height: 70vh;
    right: -0.5rem;
  }
}
</style>