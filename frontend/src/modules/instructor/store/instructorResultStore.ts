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

const FALLBACK_RESULTS: ResultExam[] = [
  {
    id: 1,
    title: 'Database Systems Mid Examination',
    subtitle: 'Mid Semester Examination',
    type: 'Mid Exam',
    scheduled_at: '2025-05-25T10:30:00',
    total_students: 125,
    submitted_count: 122,
    graded_count: 90,
    is_published: false,
    average_score: 68.4,
    status: 'Pending Grading'
  },
  {
    id: 2,
    title: 'Database Systems Quiz 2',
    subtitle: 'Unit 2 Quiz',
    type: 'Quiz',
    scheduled_at: '2025-05-18T09:45:00',
    total_students: 125,
    submitted_count: 125,
    graded_count: 125,
    is_published: true,
    average_score: 80.6,
    status: 'Published'
  },
  {
    id: 3,
    title: 'Database Systems Quiz 1',
    subtitle: 'Unit 1 Quiz',
    type: 'Quiz',
    scheduled_at: '2025-05-05T09:30:00',
    total_students: 125,
    submitted_count: 125,
    graded_count: 125,
    is_published: true,
    average_score: 82.2,
    status: 'Published'
  },
  {
    id: 4,
    title: 'Database Systems Assignment',
    subtitle: 'Short Assignment',
    type: 'Assignment',
    scheduled_at: '2025-04-28T11:00:00',
    total_students: 125,
    submitted_count: 119,
    graded_count: 119,
    is_published: true,
    average_score: 76.3,
    status: 'Published'
  },
  {
    id: 5,
    title: 'Database Systems Practice Test',
    subtitle: 'Practice Examination',
    type: 'Practice',
    scheduled_at: '2025-04-20T10:00:00',
    total_students: 125,
    submitted_count: 125,
    graded_count: 125,
    is_published: true,
    average_score: 77.8,
    status: 'Published'
  }
]

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
    total_exams: 8,
    completed_exams: 5,
    pending_manual_grading: 2,
    published_results: 3,
    average_score: 74,
    students_evaluated: 124,
    total_students: 125,
  })

  const gradingProgress = ref<GradingProgress>({
    percentage: 72,
    graded: 90,
    total: 125
  })

  const results = ref<ResultExam[]>([...FALLBACK_RESULTS])
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
      if (data.results && data.results.length > 0) {
        results.value = data.results
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
