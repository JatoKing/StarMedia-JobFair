<script setup>
import { reactive, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import FormField from './FormField.vue'
import { useApiForm } from '@/composables/useApiForm'
import { isRequired, isValidEmail, isValidPhone } from '@/utils/validators'

const { t } = useI18n()

const form = reactive({
  name: '',
  email: '',
  phone: '',
  message: ''
})

const clientErrors = ref({})
const { isSubmitting, isSuccess, serverError, fieldErrors, submit, reset } = useApiForm('contact.php')

function validate() {
  const errors = {}

  if (!isRequired(form.name)) errors.name = t('contact.errors.name')
  if (!isRequired(form.email)) {
    errors.email = t('contact.errors.email')
  } else if (!isValidEmail(form.email)) {
    errors.email = t('contact.errors.emailInvalid')
  }
  if (!isRequired(form.phone)) {
    errors.phone = t('contact.errors.phone')
  } else if (!isValidPhone(form.phone)) {
    errors.phone = t('contact.errors.phoneInvalid')
  }
  if (!isRequired(form.message)) errors.message = t('contact.errors.message')

  clientErrors.value = errors
  return Object.keys(errors).length === 0
}

async function handleSubmit() {
  if (!validate()) return

  const result = await submit({ ...form })

  if (result.success) {
    form.name = ''
    form.email = ''
    form.phone = ''
    form.message = ''
    clientErrors.value = {}

    setTimeout(() => reset(), 4000)
  }
}

function fieldError(field) {
  return clientErrors.value[field] || fieldErrors.value[field] || ''
}
</script>

<template>
  <section id="contact" class="contact">
    <div class="container contact__inner">
      <div class="contact__info" v-reveal>
        <p class="contact__eyebrow">{{ t('contact.eyebrow') }}</p>
        <h2 class="contact__title" v-html="t('contact.title')"></h2>
        <p class="contact__subtitle">{{ t('contact.subtitle') }}</p>
      </div>

      <form class="contact__form" v-reveal="150" @submit.prevent="handleSubmit">
        <FormField
          v-model="form.name"
          :label="t('contact.nameLabel')"
          :placeholder="t('contact.namePlaceholder')"
          :error="fieldError('name')"
        />
        <FormField
          v-model="form.email"
          type="email"
          :label="t('contact.emailLabel')"
          :placeholder="t('contact.emailPlaceholder')"
          :error="fieldError('email')"
        />
        <FormField
          v-model="form.phone"
          type="tel"
          :label="t('contact.phoneLabel')"
          :placeholder="t('contact.phonePlaceholder')"
          :error="fieldError('phone')"
        />
        <FormField
          v-model="form.message"
          type="textarea"
          :label="t('contact.messageLabel')"
          :placeholder="t('contact.messagePlaceholder')"
          :error="fieldError('message')"
        />

        <p v-if="serverError" class="contact__server-error">{{ serverError }}</p>

        <Transition name="success-pop">
          <p v-if="isSuccess" class="contact__success">{{ t('contact.success') }}</p>
        </Transition>

        <button type="submit" class="btn btn-primary contact__submit" :disabled="isSubmitting">
          {{ isSubmitting ? t('contact.submitting') : t('contact.submit') }}
        </button>
      </form>
    </div>
  </section>
</template>

<style scoped>
.contact {
  padding: var(--space-2xl) 0;
  background: var(--color-bg-dark);
}

.contact__inner {
  display: grid;
  grid-template-columns: 1fr 1.2fr;
  gap: var(--space-2xl);
  align-items: start;
}

.contact__eyebrow {
  color: var(--color-warm);
  font-weight: 600;
  font-size: var(--fs-sm);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: var(--space-xs);
}

.contact__title {
  font-size: var(--fs-2xl);
  color: #fff;
  margin-bottom: var(--space-sm);
}

.contact__subtitle {
  color: rgba(255, 255, 255, 0.75);
}

.contact__form {
  background: #fff;
  border-radius: var(--radius-lg);
  padding: var(--space-lg);
  display: flex;
  flex-direction: column;
  gap: var(--space-sm);
  box-shadow: var(--shadow-lg);
}

.contact__server-error {
  color: var(--color-error);
  font-size: var(--fs-sm);
  font-weight: 500;
}

.contact__success {
  color: var(--color-success);
  font-size: var(--fs-sm);
  font-weight: 600;
  background: rgba(0, 196, 140, 0.1);
  padding: 0.75rem 1rem;
  border-radius: var(--radius-sm);
}

.contact__submit {
  margin-top: var(--space-xs);
}

.contact__submit:disabled {
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

@media (max-width: 860px) {
  .contact__inner {
    grid-template-columns: 1fr;
  }
}
</style>