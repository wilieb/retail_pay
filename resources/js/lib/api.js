import api from './axios'

export const apiCall = async (method, endpoint, data = null) => {
  try {
    const response = await api[method](endpoint, data)
    return {
      success: true,
      data: response.data?.data || response.data,
      message: response.data?.message
    }
  } catch (error) {
    return {
      success: false,
      error: error.response?.data?.message || error.message,
      statusCode: error.response?.status
    }
  }
}

export const createResource = async (endpoint, data) => {
  return apiCall('post', endpoint, data)
}

export const updateResource = async (endpoint, data) => {
  return apiCall('put', endpoint, data)
}

export const deleteResource = async (endpoint) => {
  return apiCall('delete', endpoint)
}

export const getResources = async (endpoint) => {
  return apiCall('get', endpoint)
}

export const getResource = async (endpoint) => {
  return apiCall('get', endpoint)
}

export const paginateResources = async (endpoint, page = 1, perPage = 15) => {
  return apiCall('get', `${endpoint}?page=${page}&per_page=${perPage}`)
}

export const searchResources = async (endpoint, query) => {
  return apiCall('get', `${endpoint}?search=${query}`)
}

export const filterResources = async (endpoint, filters = {}) => {
  const queryString = new URLSearchParams(filters).toString()
  return apiCall('get', `${endpoint}?${queryString}`)
}

export const bulkUpdate = async (endpoint, ids, data) => {
  return apiCall('put', `${endpoint}/bulk`, { ids, ...data })
}

export const bulkDelete = async (endpoint, ids) => {
  return apiCall('delete', `${endpoint}/bulk`, { ids })
}

export const handleError = (error, defaultMessage = 'An error occurred') => {
  if (error.response?.data?.message) {
    return error.response.data.message
  }
  if (error.response?.data?.errors) {
    return Object.values(error.response.data.errors).flat().join(', ')
  }
  return error.message || defaultMessage
}
