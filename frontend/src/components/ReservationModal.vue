<script setup>
import { reactive, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import BaseModal from './BaseModal.vue'
import FormField from './FormField.vue'
import { useApiForm } from '@/composables/useApiForm'
import { isRequired, isValidEmail, isValidPhone } from '@/utils/validators'

const { t } = useI18n()

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  session: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['update:modelValue', 'reserved'])

const form = reactive({
  name: '',
  email: '',
  phone: ''
})

const clientErrors = ref({})
const { isSubmitting, isSuccess, serverError, fieldErrors, submit, reset } = useApiForm('reservation.php')

function validate() {
  const errors = {}

  if (!isRequired(form.name)) errors.name = t('reservation.errors.name')
  if (!isRequired(form.email)) {
    errors.email = t('reservation.errors.email')
  } else if (!isValidEmail(form.email)) {
    errors.email = t('reservation.errors.emailInvalid')
  }
  if (!isRequired(form.phone)) {
    errors.phone = t('reservation.errors.phone')
  } else if (!isValidPhone(form.phone)) {
    errors.phone = t('reservation.errors.phoneInvalid')
  }

  clientErrors.value = errors
  return Object.keys(errors).length === 0
}

async function handleSubmit() {
  if (!validate() || !props.session) return

  const result = await submit({
    sessionId: props.session.id,
    ...form
  })

  if (result.success) {
    emit('reserved', props.session.id)
    resetForm()
  }
}

function resetForm() {
  form.name = ''
  form.email = ''
  form.phone = ''
  clientErrors.value = {}
}

function fieldError(field) {
  return clientErrors.value[field] || fieldErrors.value[field] || ''
}

function close() {
  emit('update:modelValue', false)
}

watch(
  () => props.modelValue,
  (isOpen) => {
    if (isOpen) {
      reset()
      resetForm()
    }
  }
)
</script>

<template>
  <BaseModal :model-value="modelValue" size="md" @update:model-value="close">
    <template v-if="session">
      <h3 class="reservation-modal__title">{{ t('reservation.modalTitle') }}</h3>
      <p class="reservation-modal__session-title">{{ session.title }}</p>
      <p class="reservation-modal__session-meta">🎤 {{ session.speaker }}</p>

      <form class="reservation-modal__form" @submit.prevent="handleSubmit">
        <FormField
          v-model="form.name"
          :label="t('reservation.nameLabel')"
          :placeholder="t('reservation.namePlaceholder')"
          :error="fieldError('name')"
        />
        <FormField
          v-model="form.email"
          type="email"
          :label="t('reservation.emailLabel')"
          :placeholder="t('reservation.emailPlaceholder')"
          :error="fieldError('email')"
        />
        <FormField
          v-model="form.phone"
          type="tel"
          :label="t('reservation.phoneLabel')"
          :placeholder="t('reservation.phonePlaceholder')"
          :error="fieldError('phone')"
        />

        <p v-if="serverError" class="reservation-modal__error">{{ serverError }}</p>

        <Transition name="success-pop">
          <p v-if="isSuccess" class="reservation-modal__success">{{ t('reservation.success') }}</p>
        </Transition>

        <button type="submit" class="btn btn-primary reservation-modal__submit" :disabled="isSubmitting">
          {{ isSubmitting ? t('reservation.submitting') : t('reservation.submit') }}
        </button>
      </form>
    </template>
  </BaseModal>
</template>

<style scoped>
.reservation-modal__title {
  font-size: var(--fs-xl);
  color: var(--color-text);
  margin-bottom: var(--space-xs);
}

.reservation-modal__session-title {
  font-weight: 600;
  color: var(--color-secondary);
}

.reservation-modal__session-meta {
  font-size: var(--fs-sm);
  color: var(--color-text-muted);
  margin-bottom: var(--space-md);
}

.reservation-modal__form {
  display: flex;
  flex-direction: column;
  gap: var(--space-sm);
}

.reservation-modal__error {
  color: var(--color-error);
  font-size: var(--fs-sm);
  font-weight: 500;
}

.reservation-modal__success {
  color: var(--color-success);
  font-size: var(--fs-sm);
  font-weight: 600;
  background: rgba(0, 196, 140, 0.1);
  padding: 0.75rem 1rem;
  border-radius: var(--radius-sm);
}

.reservation-modal__submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

.success-pop-enter-active {
  transition: all var(--duration-base) var(--ease-bounce);
}
.success-pop-enter-from {
  opacity: 0;
  transform: scale(0.9);
}
</style>