export const useFormatting = () => {
  const formatNumber = (num, decimals = 0) => {
    if (!num && num !== 0) return '0'
    return new Intl.NumberFormat('en-US', {
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals
    }).format(num)
  }

  const formatCurrency = (num, currency = 'KES', locale = 'en-US') => {
    if (!num && num !== 0) return `${currency} 0`
    return new Intl.NumberFormat(locale, {
      style: 'currency',
      currency: currency,
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }).format(num)
  }

  const formatDate = (date, locale = 'en-US') => {
    if (!date) return 'N/A'
    const d = new Date(date)
    return d.toLocaleDateString(locale, {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  }

  const formatDateTime = (date, locale = 'en-US') => {
    if (!date) return 'N/A'
    const d = new Date(date)
    return d.toLocaleString(locale, {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  const formatTime = (date, locale = 'en-US') => {
    if (!date) return 'N/A'
    const d = new Date(date)
    return d.toLocaleTimeString(locale, {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    })
  }

  const formatPercentage = (num, decimals = 1) => {
    return `${formatNumber(num * 100, decimals)}%`
  }

  const truncate = (str, length = 50) => {
    if (!str) return ''
    return str.length > length ? str.substring(0, length) + '...' : str
  }

  const capitalize = (str) => {
    if (!str) return ''
    return str.charAt(0).toUpperCase() + str.slice(1)
  }

  const slugify = (str) => {
    if (!str) return ''
    return str.toLowerCase().replace(/\s+/g, '-')
  }

  return {
    formatNumber,
    formatCurrency,
    formatDate,
    formatDateTime,
    formatTime,
    formatPercentage,
    truncate,
    capitalize,
    slugify
  }
}
