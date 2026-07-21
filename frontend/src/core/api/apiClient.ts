import axios from 'axios'

const apiClient = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json'
  }
})

// Request Interceptor: Attach token if it exists
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response Interceptor: Handle 401 Unauthorized globally
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      // Only redirect to login if we are NOT already on the login page
      // This prevents an infinite reload loop when unauthenticated requests
      // are made (e.g., fetching settings before login)
      if (window.location.pathname !== '/') {
        localStorage.removeItem('auth_token')
        window.location.href = '/'
      }
    }
    return Promise.reject(error)
  }
)

export default apiClient
