import { defineStore } from 'pinia'
import { ref } from 'vue'
import apiClient from '../../../core/api/apiClient'
import type { Exam } from './instructorStore' // Reuse interface

export interface ExamStats {
  total: number
  published: number
  draft: number
  completed: number
}

// ─────────────────────────────────────────────
// Fallback mock data
// ─────────────────────────────────────────────
const MOCK_STATS: ExamStats = {
  total: 5,
  published: 1,
  draft: 1,
  completed: 2,
}

const MOCK_EXAMS: Exam[] = [
  {
    id: 1,
    title: 'Midterm — Software Engineering',
    course_code: 'SWE-301',
    course_name: 'Software Engineering',
    status: 'completed',
    duration_minutes: 120,
    total_marks: 100,
    students_count: 45,
    scheduled_at: new Date(Date.now() - 10 * 86400000).toISOString(),
    created_at: new Date(Date.now() - 15 * 86400000).toISOString(),
  },
  {
    id: 2,
    title: 'Final Exam — Software Engineering',
    course_code: 'SWE-301',
    course_name: 'Software Engineering',
    status: 'scheduled',
    duration_minutes: 180,
    total_marks: 100,
    students_count: 60,
    scheduled_at: new Date(Date.now() + 7 * 86400000).toISOString(),
    created_at: new Date(Date.now() - 2 * 86400000).toISOString(),
  },
  {
    id: 3,
    title: 'Quiz 1 — Software Engineering',
    course_code: 'SWE-301',
    course_name: 'Software Engineering',
    status: 'scheduled',
    duration_minutes: 60,
    total_marks: 50,
    students_count: 55,
    scheduled_at: new Date(Date.now() + 3 * 86400000).toISOString(),
    created_at: new Date(Date.now() - 86400000).toISOString(),
  },
  {
    id: 4,
    title: 'Assignment Test — Software Engineering',
    course_code: 'SWE-301',
    course_name: 'Software Engineering',
    status: 'draft',
    duration_minutes: 90,
    total_marks: 75,
    students_count: 0,
    scheduled_at: null,
    created_at: new Date().toISOString(),
  },
  {
    id: 5,
    title: 'Semester End — Software Engineering',
    course_code: 'SWE-301',
    course_name: 'Software Engineering',
    status: 'published',
    duration_minutes: 180,
    total_marks: 100,
    students_count: 72,
    scheduled_at: new Date(Date.now() + 14 * 86400000).toISOString(),
    created_at: new Date().toISOString(),
  },
]

export const useInstructorExamStore = defineStore('instructorExam', () => {
  const exams = ref<Exam[]>([])
  const stats = ref<ExamStats>({ ...MOCK_STATS })
  const isLoading = ref(false)
  const isSaving = ref(false)
  const error = ref<string | null>(null)
  const usingMockData = ref(false)

  const fetchExams = async () => {
    isLoading.value = true
    error.value = null
    usingMockData.value = false

    try {
      const response = await apiClient.get('/instructor/exams')
      exams.value = response.data.data.exams
      stats.value = response.data.data.stats
    } catch (err: any) {
      if (err.code === 'ERR_NETWORK' || !err.response) {
        console.warn('[InstructorExamStore] Backend unreachable. Using mock data.')
        exams.value = [...MOCK_EXAMS]
        stats.value = { ...MOCK_STATS }
        usingMockData.value = true
      } else {
        error.value = err.response?.data?.message || 'Failed to load exams.'
      }
    } finally {
      isLoading.value = false
    }
  }

  const createExam = async (examData: Partial<Exam>) => {
    isSaving.value = true
    error.value = null

    try {
      if (usingMockData.value) {
        // Mock save
        const newExam: Exam = {
          id: Date.now(),
          title: examData.title || 'Untitled',
          course_code: 'SWE-301',
          course_name: 'Software Engineering',
          duration_minutes: examData.duration_minutes || 60,
          total_marks: examData.total_marks || 100,
          status: examData.status || 'draft',
          students_count: 0,
          scheduled_at: examData.scheduled_at || null,
          created_at: new Date().toISOString(),
        }
        exams.value.unshift(newExam)
        return newExam
      }

      const response = await apiClient.post('/instructor/exams', examData)
      exams.value.unshift(response.data.data) // Prepend new exam
      return response.data.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create exam.'
      throw err
    } finally {
      isSaving.value = false
    }
  }

  const deleteExam = async (id: number) => {
    try {
      if (usingMockData.value) {
        exams.value = exams.value.filter(e => e.id !== id)
        return
      }
      
      await apiClient.delete(`/instructor/exams/${id}`)
      exams.value = exams.value.filter(e => e.id !== id)
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete exam.'
      throw err
    }
  }

  return {
    exams,
    stats,
    isLoading,
    isSaving,
    error,
    usingMockData,
    fetchExams,
    createExam,
    deleteExam,
  }
})
