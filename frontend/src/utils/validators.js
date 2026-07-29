// Kumpulan fungsi validation ringkas untuk form (Contact & Exhibitor Registration)

export function isRequired(value) {
  return value !== null && value !== undefined && String(value).trim() !== ''
}

export function isValidEmail(value) {
  const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return pattern.test(String(value).trim())
}

export function isValidPhone(value) {
  // Terima format nombor Malaysia: 01X-XXXXXXX, +601X-XXXXXXX, atau tanpa dash
  const pattern = /^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/
  return pattern.test(String(value).replace(/\s/g, ''))
}

export function minLength(value, length) {
  return String(value).trim().length >= length
}
