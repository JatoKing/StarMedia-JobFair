// Custom directive: v-reveal
// Tambah class 'is-visible' bila element masuk viewport (guna native IntersectionObserver, tiada library)

const observerOptions = {
  root: null,
  rootMargin: '0px 0px -80px 0px',
  threshold: 0.1
}

const observerMap = new WeakMap()

export const vReveal = {
  mounted(el, binding) {
    el.setAttribute('data-reveal', '')

    // optional delay: v-reveal="200" untuk stagger effect
    const delay = typeof binding.value === 'number' ? binding.value : 0
    if (delay) {
      el.style.transitionDelay = `${delay}ms`
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          el.classList.add('is-visible')
          observer.unobserve(el)
        }
      })
    }, observerOptions)

    observer.observe(el)
    observerMap.set(el, observer)
  },
  unmounted(el) {
    const observer = observerMap.get(el)
    if (observer) {
      observer.disconnect()
      observerMap.delete(el)
    }
  }
}