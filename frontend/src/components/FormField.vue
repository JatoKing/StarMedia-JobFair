<script setup>
defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'text' // 'text' | 'email' | 'tel' | 'textarea' | 'select'
  },
  placeholder: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  options: {
    type: Array,
    default: () => [] // untuk type='select', array of string
  }
})

defineEmits(['update:modelValue'])
</script>

<template>
  <div class="form-field" :class="{ 'form-field--error': error }">
    <label class="form-field__label">{{ label }}</label>

    <textarea
      v-if="type === 'textarea'"
      :value="modelValue"
      :placeholder="placeholder"
      rows="4"
      class="form-field__input form-field__input--textarea"
      @input="$emit('update:modelValue', $event.target.value)"
    />

    <select
      v-else-if="type === 'select'"
      :value="modelValue"
      class="form-field__input form-field__input--select"
      @change="$emit('update:modelValue', $event.target.value)"
    >
      <option value="" disabled>Pilih {{ label.toLowerCase() }}</option>
      <option v-for="option in options" :key="option" :value="option">{{ option }}</option>
    </select>

    <input
      v-else
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      class="form-field__input"
      @input="$emit('update:modelValue', $event.target.value)"
    />

    <Transition name="error-fade">
      <p v-if="error" class="form-field__error">{{ error }}</p>
    </Transition>
  </div>
</template>

<style scoped>
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
  transition: border-color var(--duration-fast) var(--ease-smooth),
              box-shadow var(--duration-fast) var(--ease-smooth);
}

.form-field__input:focus {
  outline: none;
  border-color: var(--color-secondary);
  box-shadow: 0 0 0 3px rgba(107, 4, 26, 0.15);
}

.form-field__input--textarea {
  resize: vertical;
  min-height: 100px;
}

.form-field--error .form-field__input {
  border-color: var(--color-error);
}

.form-field--error .form-field__input:focus {
  box-shadow: 0 0 0 3px rgba(255, 71, 87, 0.15);
}

.form-field__error {
  font-size: var(--fs-xs);
  color: var(--color-error);
  font-weight: 500;
}

.error-fade-enter-active,
.error-fade-leave-active {
  transition: all var(--duration-fast) var(--ease-smooth);
}

.error-fade-enter-from,
.error-fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>