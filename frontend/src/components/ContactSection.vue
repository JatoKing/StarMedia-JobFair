<script setup>
import { reactive, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import FormField from './FormField.vue'
import { useApiForm } from '@/composables/useApiForm'
import { isRequired, isValidEmail, isValidPhone } from '@/utils/validators'

const { t } = useI18n()

const footerNavLinks = [
  { key: 'nav.home', target: '#home' },
  { key: 'nav.directory', target: '#directory' },
  { key: 'nav.exhibitor', target: '#exhibitor' },
  { key: 'nav.contact', target: '#contact' }
]

const socialLinks = [
  { id: 'facebook', label: 'Facebook', href: '#' },
  { id: 'instagram', label: 'Instagram', href: '#' },
  { id: 'linkedin', label: 'LinkedIn', href: '#' }
]

function scrollToSection(target) {
  const el = document.querySelector(target)
  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

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

        <div class="contact__footer-nav">
          <div class="footer-nav__col">
            <p class="footer-nav__heading">{{ t('footer.quickLinks') }}</p>
            <ul class="footer-nav__list">
              <li v-for="link in footerNavLinks" :key="link.target">
                <a href="#" @click.prevent="scrollToSection(link.target)">{{ t(link.key) }}</a>
              </li>
              <li>
                <RouterLink to="/spinning-wheel">{{ t('nav.spinWheel') }}</RouterLink>
              </li>
            </ul>
          </div>

          <div class="footer-nav__col">
            <p class="footer-nav__heading">{{ t('footer.followUs') }}</p>
            <ul class="footer-nav__socials">
              <li v-for="social in socialLinks" :key="social.id">
                <a :href="social.href" :aria-label="social.label" target="_blank" rel="noreferrer noopener">
                  {{ social.label.charAt(0) }}
                </a>
              </li>
            </ul>
          </div>
        </div>
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

    <div class="container contact__bottom">
      <p class="contact__copyright">{{ t('footer.copyright') }}</p>
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

/* Scroll-driven gradient reveal: tajuk "shimmer" masuk bila section
   discroll ke dalam viewport. Fallback warna putih pekat untuk browser
   yang tak sokong animation-timeline (mis. Safari/Firefox lama). */
@supports (animation-timeline: view()) {
  .contact__title {
    background: radial-gradient(60% 120% at 30% 100%, #fff 0%, var(--color-warm) 55%, transparent 80%);
    background-size: 160% 220%;
    background-position: 0% 0%;
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    opacity: 0;
    animation: contact-title-move linear both, contact-title-fade linear both;
    animation-timeline: view();
    animation-range: entry 0% cover 35%, entry 0% cover 20%;
  }
}

@keyframes contact-title-move {
  to { background-position: 100% 100%; }
}

@keyframes contact-title-fade {
  to { opacity: 1; }
}

.contact__subtitle {
  color: rgba(255, 255, 255, 0.75);
}

.contact__footer-nav {
  display: flex;
  gap: var(--space-2xl);
  margin-top: var(--space-lg);
  padding-top: var(--space-lg);
  border-top: 1px solid rgba(255, 255, 255, 0.12);
}

.footer-nav__heading {
  color: var(--color-warm);
  font-weight: 600;
  font-size: var(--fs-sm);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: var(--space-sm);
}

.footer-nav__list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: var(--space-xs);
}

.footer-nav__list a {
  color: rgba(255, 255, 255, 0.75);
  font-size: var(--fs-sm);
  transition: color var(--duration-fast) var(--ease-smooth);
}

.footer-nav__list a:hover {
  color: #fff;
}

.footer-nav__socials {
  list-style: none;
  display: flex;
  gap: var(--space-xs);
}

.footer-nav__socials a {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2.25rem;
  height: 2.25rem;
  border-radius: var(--radius-full);
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  font-weight: 700;
  font-size: var(--fs-sm);
  transition: background var(--duration-fast) var(--ease-smooth),
              transform var(--duration-fast) var(--ease-bounce);
}

.footer-nav__socials a:hover {
  background: var(--color-primary);
  transform: translateY(-2px);
}

.contact__bottom {
  margin-top: var(--space-2xl);
  padding-top: var(--space-md);
  border-top: 1px solid rgba(255, 255, 255, 0.12);
}

.contact__copyright {
  color: rgba(255, 255, 255, 0.5);
  font-size: var(--fs-xs);
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

  .contact__footer-nav {
    gap: var(--space-lg);
    flex-wrap: wrap;
  }
}
</style>