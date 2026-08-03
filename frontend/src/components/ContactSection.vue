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
  { id: 'linkedin', label: 'LinkedIn', href: '#' },
  { id: 'x', label: 'X (Twitter)', href: '#' }
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
                <RouterLink to="/spinning-wheel">{{ t('nav.spinWheel').replace('🎡 ', '') }}</RouterLink>
              </li>
            </ul>
          </div>

          <div class="footer-nav__col">
            <p class="footer-nav__heading">{{ t('footer.followUs') }}</p>
            <ul class="footer-nav__socials">
              <li v-for="social in socialLinks" :key="social.id">
                <a :href="social.href" :aria-label="social.label" target="_blank" rel="noreferrer noopener">
                  <svg v-if="social.id === 'facebook'" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true">
                    <path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5.02 3.66 9.18 8.44 9.94v-7.03H7.9v-2.91h2.54V9.85c0-2.51 1.49-3.9 3.77-3.9 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.89h2.78l-.44 2.91h-2.34V22c4.78-.76 8.44-4.92 8.44-9.94Z"/>
                  </svg>
                  <svg v-else-if="social.id === 'instagram'" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true">
                    <path d="M12 2c2.72 0 3.06.01 4.12.06 1.06.05 1.79.22 2.43.47.66.26 1.22.6 1.77 1.15.55.55.89 1.11 1.15 1.77.25.64.42 1.37.47 2.43.05 1.06.06 1.4.06 4.12s-.01 3.06-.06 4.12c-.05 1.06-.22 1.79-.47 2.43a4.9 4.9 0 0 1-1.15 1.77 4.9 4.9 0 0 1-1.77 1.15c-.64.25-1.37.42-2.43.47-1.06.05-1.4.06-4.12.06s-3.06-.01-4.12-.06c-1.06-.05-1.79-.22-2.43-.47a4.9 4.9 0 0 1-1.77-1.15 4.9 4.9 0 0 1-1.15-1.77c-.25-.64-.42-1.37-.47-2.43C2.01 15.06 2 14.72 2 12s.01-3.06.06-4.12c.05-1.06.22-1.79.47-2.43.26-.66.6-1.22 1.15-1.77A4.9 4.9 0 0 1 5.45.53c.64-.25 1.37-.42 2.43-.47C8.94 2.01 9.28 2 12 2Zm0 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm0 8.25A3.25 3.25 0 1 1 12 8.75a3.25 3.25 0 0 1 0 6.5ZM17.5 6.5a1.2 1.2 0 1 0 0 2.4 1.2 1.2 0 0 0 0-2.4Z"/>
                  </svg>
                  <svg v-else-if="social.id === 'linkedin'" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true">
                    <path d="M20.45 2H3.55A1.55 1.55 0 0 0 2 3.55v16.9A1.55 1.55 0 0 0 3.55 22h16.9A1.55 1.55 0 0 0 22 20.45V3.55A1.55 1.55 0 0 0 20.45 2ZM8.34 18.34H5.67V9.75h2.67v8.59ZM7 8.6a1.55 1.55 0 1 1 0-3.1 1.55 1.55 0 0 1 0 3.1Zm11.34 9.74h-2.67v-4.18c0-1-.02-2.27-1.38-2.27-1.39 0-1.6 1.08-1.6 2.2v4.25H10.02V9.75h2.56v1.17h.04c.36-.68 1.23-1.4 2.53-1.4 2.7 0 3.2 1.78 3.2 4.1v4.72Z"/>
                  </svg>
                  <svg v-else-if="social.id === 'x'" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true">
                    <path d="M18.24 2.25h3.31l-7.23 8.26 8.5 11.24h-6.66l-5.22-6.83-5.97 6.83H1.66l7.73-8.84L1.25 2.25h6.83l4.72 6.24 5.44-6.24Zm-1.16 17.52h1.83L7.02 4.13H5.06l12.02 15.64Z"/>
                  </svg>
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
  position: relative;
  display: inline-block;
  color: rgba(255, 255, 255, 0.75);
  font-size: var(--fs-sm);
  transition: color var(--duration-fast) var(--ease-smooth),
              transform var(--duration-fast) var(--ease-smooth);
}

.footer-nav__list a::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -2px;
  width: 100%;
  height: 1px;
  background: var(--color-warm);
  transform: scaleX(0);
  transform-origin: left;
  transition: transform var(--duration-fast) var(--ease-smooth);
}

.footer-nav__list a:hover {
  color: #fff;
  transform: translateX(4px);
}

.footer-nav__list a:hover::after {
  transform: scaleX(1);
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
  transition: background var(--duration-fast) var(--ease-smooth),
              transform var(--duration-fast) var(--ease-bounce);
}

.footer-nav__socials a svg {
  transition: transform var(--duration-fast) var(--ease-bounce);
}

.footer-nav__socials a:hover svg {
  transform: scale(1.15) rotate(8deg);
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