import { ref } from 'vue'

export const useFormValidation = () => {
  const errors = ref({})

  const validate = (formData, rules) => {
    errors.value = {}
    let isValid = true

    for (const field in rules) {
      const fieldRules = rules[field]
      const value = formData[field]

      for (const rule of fieldRules) {
        const { type, message, validate: customValidate } = rule

        let fieldValid = true

        if (type === 'required' && (!value || value.trim() === '')) {
          fieldValid = false
        }

        if (type === 'email' && value && !isValidEmail(value)) {
          fieldValid = false
        }

        if (type === 'minLength' && value && value.length < rule.length) {
          fieldValid = false
        }

        if (type === 'maxLength' && value && value.length > rule.length) {
          fieldValid = false
        }

        if (type === 'custom' && customValidate && !customValidate(value)) {
          fieldValid = false
        }

        if (!fieldValid) {
          if (!errors.value[field]) {
            errors.value[field] = message || `${field} is invalid`
          }
          isValid = false
          break
        }
      }
    }

    return isValid
  }

  const isValidEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return re.test(email)
  }

  const clearErrors = () => {
    errors.value = {}
  }

  return {
    errors,
    validate,
    clearErrors
  }
}
