<script setup>
import { reactive, ref, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import BaseModal from './BaseModal.vue'
import FormField from './FormField.vue'
import { useApiForm } from '@/composables/useApiForm'
import { isRequired, isValidEmail, isValidPhone } from '@/utils/validators'
import { exhibitorCategoryKeys } from '@/data/exhibitors'

const { t } = useI18n()

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])

const categoryOptions = computed(() =>
  exhibitorCategoryKeys.map((key) => ({
    value: key,
    label: t(`directory.categories.${key}`)
  }))
)

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

  if (!isRequired(form.companyName)) errors.companyName = t('exhibitorForm.errors.companyName')
  if (!isRequired(form.contactPerson)) errors.contactPerson = t('exhibitorForm.errors.contactPerson')
  if (!isRequired(form.email)) {
    errors.email = t('exhibitorForm.errors.email')
  } else if (!isValidEmail(form.email)) {
    errors.email = t('exhibitorForm.errors.emailInvalid')
  }
  if (!isRequired(form.phone)) {
    errors.phone = t('exhibitorForm.errors.phone')
  } else if (!isValidPhone(form.phone)) {
    errors.phone = t('exhibitorForm.errors.phoneInvalid')
  }
  if (!isRequired(form.category)) errors.category = t('exhibitorForm.errors.category')

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
    <h3 class="ex-modal__title">{{ t('exhibitorForm.title') }}</h3>
    <p class="ex-modal__subtitle">{{ t('exhibitorForm.subtitle') }}</p>

    <form class="ex-modal__form" @submit.prevent="handleSubmit">
      <FormField
        v-model="form.companyName"
        :label="t('exhibitorForm.companyLabel')"
        :placeholder="t('exhibitorForm.companyPlaceholder')"
        :error="fieldError('companyName')"
      />
      <FormField
        v-model="form.contactPerson"
        :label="t('exhibitorForm.contactPersonLabel')"
        :placeholder="t('exhibitorForm.contactPersonPlaceholder')"
        :error="fieldError('contactPerson')"
      />
      <FormField
        v-model="form.email"
        type="email"
        :label="t('exhibitorForm.emailLabel')"
        :placeholder="t('exhibitorForm.emailPlaceholder')"
        :error="fieldError('email')"
      />
      <FormField
        v-model="form.phone"
        type="tel"
        :label="t('exhibitorForm.phoneLabel')"
        :placeholder="t('exhibitorForm.phonePlaceholder')"
        :error="fieldError('phone')"
      />

      <div class="form-field" :class="{ 'form-field--error': fieldError('category') }">
        <label class="form-field__label">{{ t('exhibitorForm.categoryLabel') }}</label>
        <select v-model="form.category" class="form-field__input">
          <option value="" disabled>{{ t('exhibitorForm.categoryLabel') }}</option>
          <option v-for="opt in categoryOptions" :key="opt.value" :value="opt.value">
            {{ opt.label }}
          </option>
        </select>
        <p v-if="fieldError('category')" class="form-field__error">{{ fieldError('category') }}</p>
      </div>

      <FormField
        v-model="form.message"
        type="textarea"
        :label="t('exhibitorForm.messageLabel')"
        :placeholder="t('exhibitorForm.messagePlaceholder')"
        :error="fieldError('message')"
      />

      <p v-if="serverError" class="ex-modal__server-error">{{ serverError }}</p>

      <Transition name="success-pop">
        <p v-if="isSuccess" class="ex-modal__success">{{ t('exhibitorForm.success') }}</p>
      </Transition>

      <button type="submit" class="btn btn-primary ex-modal__submit" :disabled="isSubmitting">
        {{ isSubmitting ? t('exhibitorForm.submitting') : t('exhibitorForm.submit') }}
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

.form-field {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.form-field__label {
  font-size: var(--fs-sm);
  font-weight: 600;
  color: var(--color-text);
}

.form-field__input {
  padding: 0.75rem 1rem;
  border: 1.5px solid var(--color-border);
  border-radius: var(--radius-sm);
  font-size: var(--fs-sm);
  font-family: var(--font-body);
  color: var(--color-text);
  background: var(--color-bg);
}

.form-field--error .form-field__input {
  border-color: var(--color-error);
}

.form-field__error {
  font-size: var(--fs-xs);
  color: var(--color-error);
  font-weight: 500;
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