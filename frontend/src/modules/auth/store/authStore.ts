import { defineStore } from 'pinia'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '../../../core/api/apiClient'
import { useSettingsStore } from '../../../store/settingsStore'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<any>(null)
  const token = ref(localStorage.getItem('auth_token') || null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const router = useRouter()

  const login = async (credentials: any) => {
    isLoading.value = true
    error.value = null
    const settingsStore = useSettingsStore()

    try {
      try {
        const response = await apiClient.post('/login', credentials)
        const { user: userData, token: authToken } = response.data.data
        
        user.value = userData
        token.value = authToken
        localStorage.setItem('auth_token', authToken)
      } catch (apiErr: any) {
        // Fallback to mock login if backend is unreachable (ERR_NETWORK)
        if (apiErr.code === 'ERR_NETWORK' || !apiErr.response) {
          console.warn('Backend unreachable. Using mock login data for development.')
          await new Promise(resolve => setTimeout(resolve, 600))
          
          user.value = {
            id: 1,
            name: 'Dr. Abebe Kebede',
            email: credentials.login || 'instructor@wollo.edu.et',
            role: credentials.role === 'staff' ? 'admin' : (credentials.role || 'instructor')
          }
          token.value = 'mock_jwt_token_for_frontend_dev'
          localStorage.setItem('auth_token', token.value)
        } else {
          throw apiErr
        }
      }
      
      // Redirect based on user role
      if (user.value.role === 'dept_head') {
        router.push('/dept-head/dashboard')
      } else if (user.value.role === 'admin') {
        router.push('/admin/dashboard')
      } else if (user.value.role === 'student') {
        router.push('/student')
      } else {
        router.push('/instructor/dashboard')
      }
      
      // Fetch settings so the academic term badge appears immediately
      settingsStore.fetchSettings()
      
    } catch (err: any) {
      if (err.response && err.response.data && err.response.data.message) {
        error.value = err.response.data.message
      } else {
        error.value = 'An unexpected error occurred or invalid credentials.'
      }
    } finally {
      isLoading.value = false
    }
  }

  const logout = async () => {
    try {
      await apiClient.post('/logout')
    } catch (err) {
      console.warn('Backend unreachable. Proceeding with local logout.')
    } finally {
      user.value = null
      token.value = null
      localStorage.removeItem('auth_token')
      router.push('/')
    }
  }

  return {
    user,
    token,
    isLoading,
    error,
    login,
    logout
  }
})
