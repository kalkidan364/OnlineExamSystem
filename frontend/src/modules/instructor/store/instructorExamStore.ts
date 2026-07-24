import { defineStore } from 'pinia'
import { ref } from 'vue'
import apiClient from '../../../core/api/apiClient'
import type { Exam } from './instructorStore' // Reuse interface

export interface ExamStats {
  total: number
  upcoming: number
  active: number
  completed: number
  draft: number
  archived: number
}

// ─────────────────────────────────────────────
// Fallback mock data
// ─────────────────────────────────────────────
const MOCK_STATS: ExamStats = {
  total: 18,
  upcoming: 3,
  active: 2,
  completed: 9,
  draft: 3,
  archived: 1
}

const MOCK_EXAMS: Exam[] = [
  {
    id: 1,
    title: 'Database Systems Mid Exam',
    course_name: 'Midterm Examination',
    course_code: 'DBS-MID-2026',
    status: 'scheduled',
    duration_minutes: 90,
    total_marks: 100,
    students_count: 125,
    questions_count: 50,
    scheduled_at: new Date('2025-05-27T09:00:00').toISOString(),
    created_at: new Date().toISOString(),
  },
  {
    id: 2,
    title: 'Database Systems Quiz 3',
    course_name: 'Unit 3 Quiz',
    course_code: 'DBS-QUIZ3-2026',
    status: 'scheduled',
    duration_minutes: 60,
    total_marks: 50,
    students_count: 118,
    questions_count: 30,
    scheduled_at: new Date('2025-05-30T11:00:00').toISOString(),
    created_at: new Date().toISOString(),
  },
  {
    id: 3,
    title: 'Database Systems Practical Test',
    course_name: 'Practical Examination',
    course_code: 'DBS-PRAC-2026',
    status: 'published',
    duration_minutes: 120,
    total_marks: 100,
    students_count: 120,
    questions_count: undefined,
    scheduled_at: new Date('2025-06-03T14:00:00').toISOString(),
    created_at: new Date().toISOString(),
  },
  {
    id: 4,
    title: 'Database Systems Final Exam',
    course_name: 'Final Examination',
    course_code: 'DBS-FINAL-2026',
    status: 'scheduled',
    duration_minutes: 180,
    total_marks: 100,
    students_count: 124,
    questions_count: 60,
    scheduled_at: new Date('2025-06-10T09:00:00').toISOString(),
    created_at: new Date().toISOString(),
  },
  {
    id: 5,
    title: 'Database Systems Quiz 2',
    course_name: 'Unit 2 Quiz',
    course_code: 'DBS-QUIZ2-2026',
    status: 'completed',
    duration_minutes: 45,
    total_marks: 25,
    students_count: 122,
    questions_count: 20,
    scheduled_at: new Date('2025-05-15T11:00:00').toISOString(),
    created_at: new Date().toISOString(),
  },
  {
    id: 6,
    title: 'Database Systems Quiz 1',
    course_name: 'Unit 1 Quiz',
    course_code: 'DBS-QUIZ1-2026',
    status: 'completed',
    duration_minutes: 45,
    total_marks: 25,
    students_count: 123,
    questions_count: 20,
    scheduled_at: new Date('2025-05-01T10:00:00').toISOString(),
    created_at: new Date().toISOString(),
  },
  {
    id: 7,
    title: 'Database Systems Assignment Test',
    course_name: 'Short Assignment Test',
    course_code: 'DBS-ASG-2026',
    status: 'completed',
    duration_minutes: 60,
    total_marks: 20,
    students_count: 119,
    questions_count: 10,
    scheduled_at: new Date('2025-04-20T09:00:00').toISOString(),
    created_at: new Date().toISOString(),
  },
  {
    id: 8,
    title: 'Database Systems Mock Exam',
    course_name: 'Practice Exam',
    course_code: 'DBS-MOCK-2026',
    status: 'draft',
    duration_minutes: 90,
    total_marks: 100,
    students_count: 120,
    questions_count: 50,
    scheduled_at: new Date('2025-04-10T14:00:00').toISOString(),
    created_at: new Date().toISOString(),
  },
  {
    id: 9,
    title: 'Database Systems Old Final Exam',
    course_name: 'Semester II, 2024/2025',
    course_code: 'DBS-FINAL-2025',
    status: 'completed',
    duration_minutes: 180,
    total_marks: 100,
    students_count: undefined,
    questions_count: 60,
    scheduled_at: new Date('2025-01-15T09:00:00').toISOString(),
    created_at: new Date().toISOString(),
  },
]

export const useInstructorExamStore = defineStore('instructorExam', () => {
  const exams = ref<Exam[]>([...MOCK_EXAMS])
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
      // Force mock data as requested
      exams.value = [...MOCK_EXAMS]
      stats.value = { ...MOCK_STATS }
      usingMockData.value = true
    } catch (err: any) {
      error.value = 'Failed to load exams.'
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
