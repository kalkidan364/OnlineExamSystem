import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../core/api/apiClient'

export const useSettingsStore = defineStore('settings', () => {
  const academicYear = ref('2025')
  const semester = ref('1st Semester')
  const isLoading = ref(false)

  const formattedAcademicTerm = computed(() => {
    return `${academicYear.value} ${semester.value}`
  })

  async function fetchSettings() {
    try {
      const response = await api.get('/settings')
      if (response.data) {
        if (response.data.academicYear) academicYear.value = response.data.academicYear
        if (response.data.semester) semester.value = response.data.semester
      }
    } catch (error) {
      console.error('Failed to fetch settings', error)
    }
  }

  async function updateTerm(year: string, sem: string) {
    isLoading.value = true
    try {
      await api.post('/admin/settings', {
        academicYear: year,
        semester: sem
      })
      academicYear.value = year
      semester.value = sem
    } catch (error) {
      console.error('Failed to update settings', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  return {
    academicYear,
    semester,
    isLoading,
    formattedAcademicTerm,
    fetchSettings,
    updateTerm
  }
})
