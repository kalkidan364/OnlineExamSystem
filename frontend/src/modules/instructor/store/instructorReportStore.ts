import { defineStore } from 'pinia'
import { ref } from 'vue'
import apiClient from '../../../core/api/apiClient'

export interface ReportStats {
  total_exams: number
  total_students: number
  average_score: number
  pass_rate: number
  fail_rate: number
  top_score: number
}

export interface ExamStat {
  id: number
  title: string
  attempts: number
  average_score: number
}

const MOCK_STATS: ReportStats = {
  total_exams: 18,
  total_students: 856,
  average_score: 74.6,
  pass_rate: 68.4,
  fail_rate: 31.6,
  top_score: 96.0,
}

export const useInstructorReportStore = defineStore('instructorReport', () => {
  const stats = ref<ReportStats>({ ...MOCK_STATS })
  const examStats = ref<ExamStat[]>([])
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const usingMockData = ref(false)

  const fetchReports = async () => {
    isLoading.value = true
    error.value = null
    usingMockData.value = false

    try {
      const response = await apiClient.get('/instructor/reports')
      stats.value = response.data.data.stats
      examStats.value = response.data.data.exam_stats
    } catch (err: any) {
      if (err.code === 'ERR_NETWORK' || !err.response) {
        console.warn('[InstructorReportStore] Backend unreachable. Using mock data.')
        stats.value = { ...MOCK_STATS }
        usingMockData.value = true
      } else {
        error.value = err.response?.data?.message || 'Failed to load reports.'
      }
    } finally {
      isLoading.value = false
    }
  }

  return {
    stats,
    examStats,
    isLoading,
    error,
    usingMockData,
    fetchReports,
  }
})
