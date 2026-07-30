<script setup>
import { reactive, ref, watch } from 'vue'
import BaseModal from './BaseModal.vue'
import FormField from './FormField.vue'
import { useApiForm } from '@/composables/useApiForm'
import { isRequired, isValidEmail, isValidPhone } from '@/utils/validators'
import { exhibitorCategoryKeys } from '@/data/exhibitors'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])

const categoryOptions = exhibitorCategoryKeys

const form = reactive({
  companyName: '',
  contactPerson: '',
  email: '',
  phone: '',
  category: '',
  message: ''
})

const clientErrors = ref({})
const { isSubmitting, isSuccess, serverError, fieldErrors, submit, reset } = useApiForm('exhibitor.php')

function validate() {
  const errors = {}

  if (!isRequired(form.companyName)) errors.companyName = 'Nama syarikat diperlukan.'
  if (!isRequired(form.contactPerson)) errors.contactPerson = 'Nama pegawai dihubungi diperlukan.'
  if (!isRequired(form.email)) {
    errors.email = 'Email diperlukan.'
  } else if (!isValidEmail(form.email)) {
    errors.email = 'Format email tidak sah.'
  }
  if (!isRequired(form.phone)) {
    errors.phone = 'Nombor telefon diperlukan.'
  } else if (!isValidPhone(form.phone)) {
    errors.phone = 'Format nombor telefon tidak sah.'
  }
  if (!isRequired(form.category)) errors.category = 'Sila pilih kategori industri.'

  clientErrors.value = errors
  return Object.keys(errors).length === 0
}

async function handleSubmit() {
  if (!validate()) return

  const result = await submit({ ...form })

  if (result.success) {
    resetForm()
  }
}

function resetForm() {
  form.companyName = ''
  form.contactPerson = ''
  form.email = ''
  form.phone = ''
  form.category = ''
  form.message = ''
  clientErrors.value = {}
}

function fieldError(field) {
  return clientErrors.value[field] || fieldErrors.value[field] || ''
}

function close() {
  emit('update:modelValue', false)
}

// Reset state penuh setiap kali modal dibuka semula
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
    <h3 class="ex-modal__title">Jadi Exhibitor Job Fair 2026</h3>
    <p class="ex-modal__subtitle">
      Isi maklumat syarikat anda, pasukan kami akan hubungi untuk pengesahan booth.
    </p>

    <form class="ex-modal__form" @submit.prevent="handleSubmit">
      <FormField
        v-model="form.companyName"
        label="Nama Syarikat"
        placeholder="Nexora Technologies Sdn Bhd"
        :error="fieldError('companyName')"
      />
      <FormField
        v-model="form.contactPerson"
        label="Nama Pegawai Dihubungi"
        placeholder="Nurul Aina"
        :error="fieldError('contactPerson')"
      />
      <FormField
        v-model="form.email"
        type="email"
        label="Email"
        placeholder="hr@syarikat.com"
        :error="fieldError('email')"
      />
      <FormField
        v-model="form.phone"
        type="tel"
        label="Nombor Telefon"
        placeholder="012-3456789"
        :error="fieldError('phone')"
      />
      <FormField
        v-model="form.category"
        type="select"
        label="Kategori Industri"
        :options="categoryOptions"
        :error="fieldError('category')"
      />
      <FormField
        v-model="form.message"
        type="textarea"
        label="Nota Tambahan (Pilihan)"
        placeholder="Jawatan yang ditawarkan, keperluan booth, dll."
        :error="fieldError('message')"
      />

      <p v-if="serverError" class="ex-modal__server-error">{{ serverError }}</p>

      <Transition name="success-pop">
        <p v-if="isSuccess" class="ex-modal__success">
          ✅ Pendaftaran berjaya! Kami akan hubungi anda dalam masa 2 hari bekerja.
        </p>
      </Transition>

      <button type="submit" class="btn btn-primary ex-modal__submit" :disabled="isSubmitting">
        {{ isSubmitting ? 'Menghantar...' : 'Hantar Pendaftaran' }}
      </button>
    </form>
  </BaseModal>
</template>

<style scoped>
.ex-modal__title {
  font-size: var(--fs-xl);
  color: var(--color-text);
  margin-bottom: var(--space-xs);
}

.ex-modal__subtitle {
  color: var(--color-text-muted);
  font-size: var(--fs-sm);
  margin-bottom: var(--space-md);
}

.ex-modal__form {
  display: flex;
  flex-direction: column;
  gap: var(--space-sm);
}

.ex-modal__server-error {
  color: var(--color-error);
  font-size: var(--fs-sm);
  font-weight: 500;
}

.ex-modal__success {
  color: var(--color-success);
  font-size: var(--fs-sm);
  font-weight: 600;
  background: rgba(0, 196, 140, 0.1);
  padding: 0.75rem 1rem;
  border-radius: var(--radius-sm);
}

.ex-modal__submit:disabled {
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