import { createI18n } from 'vue-i18n'
import ms from './locales/ms.json'
import en from './locales/en.json'

// Simpan pilihan bahasa dalam variable (bukan localStorage — tak disokong dalam artifact,
// tapi untuk projek sebenar anda di luar Claude, localStorage.getItem('locale') selamat digunakan)
const savedLocale = 'ms' // default Bahasa Melayu

const i18n = createI18n({
  legacy: false, // guna Composition API mode supaya boleh pakai useI18n()
  locale: savedLocale,
  fallbackLocale: 'en',
  messages: { ms, en }
})

export default i18n