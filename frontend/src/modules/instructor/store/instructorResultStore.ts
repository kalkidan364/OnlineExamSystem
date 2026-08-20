import { defineStore } from 'pinia'
import { ref } from 'vue'
import apiClient from '../../../core/api/apiClient'

export interface CourseInfo {
  name: string
  code: string
  instructor_name: string
  status: string
  semester: string
  academic_year: string
  department: string
}

export interface ResultStats {
  total_exams: number
  completed_exams: number
  pending_manual_grading: number
  published_results: number
  average_score: number
  students_evaluated: number
  total_students: number
}

export interface ResultExam {
  id: number
  title: string
  subtitle: string
  type: string
  scheduled_at: string
  total_students: number
  submitted_count: number
  graded_count: number
  is_published: boolean
  average_score: number | null
  status: 'Pending Grading' | 'Published' | 'Draft' | 'Not Started'
}

export interface GradingProgress {
  percentage: number
  graded: number
  total: number
}


export const useInstructorResultStore = defineStore('instructorResult', () => {
  const course = ref<CourseInfo>({
    name: 'Database Systems',
    code: 'CS 304',
    instructor_name: 'Dr. Abebe Kebede',
    status: 'Active',
    semester: 'Semester I',
    academic_year: '2025 / 2026',
    department: 'Computer Science'
  })

  const stats = ref<ResultStats>({
    total_exams: 0,
    completed_exams: 0,
    pending_manual_grading: 0,
    published_results: 0,
    average_score: 0,
    students_evaluated: 0,
    total_students: 0,
  })

  const gradingProgress = ref<GradingProgress>({
    percentage: 0,
    graded: 0,
    total: 0
  })

  const results = ref<ResultExam[]>([])
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const fetchResults = async () => {
    isLoading.value = true
    error.value = null
    try {
      const response = await apiClient.get('/instructor/results')
      const data = response.data?.data || {}
      
      if (data.course) course.value = data.course
      if (data.stats) stats.value = data.stats
      if (data.results) {
        results.value = data.results
      } else {
        results.value = []
      }
      if (data.grading_progress) gradingProgress.value = data.grading_progress
    } catch (err: any) {
      console.warn('Backend /instructor/results call failed, using active state metrics:', err)
    } finally {
      isLoading.value = false
    }
  }

  return {
    course,
    stats,
    results,
    gradingProgress,
    isLoading,
    error,
    fetchResults,
  }
})
