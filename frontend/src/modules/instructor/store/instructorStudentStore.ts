import { defineStore } from 'pinia'
import { ref } from 'vue'
import apiClient from '../../../core/api/apiClient'

export interface Student {
  id: number
  name: string
  email: string
  id_number: string
  course_code: string
  course_name: string
  status: string
  exams_taken: number
  average_score: number
  created_at: string
}

export interface StudentStats {
  total_students: number
  active_students: number
  average_score: number
  top_performers: number
}

const MOCK_STATS: StudentStats = {
  total_students: 320,
  active_students: 298,
  average_score: 72.4,
  top_performers: 46,
}

const MOCK_STUDENTS: Student[] = [
  { id: 1, name: 'Abebe Tesfaye', email: 'abebe.tesfaye@wollo.edu.et', id_number: 'WU/2021/CS/001', course_code: 'SWE-301', course_name: 'Software Engineering', status: 'Active', exams_taken: 12, average_score: 85.6, created_at: '2025-01-15T10:00:00.000Z' },
  { id: 2, name: 'Selamawit Getachew', email: 'selamawit.get@wollo.edu.et', id_number: 'WU/2021/SE/015', course_code: 'SWE-301', course_name: 'Software Engineering', status: 'Active', exams_taken: 10, average_score: 78.3, created_at: '2025-01-15T10:00:00.000Z' },
  { id: 3, name: 'Kebede Assefa', email: 'kebede.assefa@wollo.edu.et', id_number: 'WU/2022/CS/027', course_code: 'SWE-301', course_name: 'Software Engineering', status: 'Active', exams_taken: 8, average_score: 68.7, created_at: '2025-02-10T10:00:00.000Z' },
  { id: 4, name: 'Hanna Mengesha', email: 'hanna.mengesha@wollo.edu.et', id_number: 'WU/2022/IT/031', course_code: 'SWE-301', course_name: 'Software Engineering', status: 'Active', exams_taken: 9, average_score: 82.1, created_at: '2025-02-10T10:00:00.000Z' },
  { id: 5, name: 'Mekonnen Worku', email: 'mekonnen.worku@wollo.edu.et', id_number: 'WU/2021/CS/045', course_code: 'SWE-301', course_name: 'Software Engineering', status: 'Active', exams_taken: 6, average_score: 45.2, created_at: '2025-01-15T10:00:00.000Z' },
  { id: 6, name: 'Rahel Solomon', email: 'rahel.solomon@wollo.edu.et', id_number: 'WU/2023/SE/058', course_code: 'SWE-301', course_name: 'Software Engineering', status: 'Active', exams_taken: 4, average_score: 75.9, created_at: '2025-03-01T10:00:00.000Z' },
  { id: 7, name: 'Yonas Alemu', email: 'yonas.alemu@wollo.edu.et', id_number: 'WU/2023/CS/064', course_code: 'SWE-301', course_name: 'Software Engineering', status: 'Active', exams_taken: 4, average_score: 62.4, created_at: '2025-03-01T10:00:00.000Z' },
  { id: 8, name: 'Lidya Gebremedhin', email: 'lidya.gebremedhin@wollo.edu.et', id_number: 'WU/2023/IT/072', course_code: 'SWE-301', course_name: 'Software Engineering', status: 'Active', exams_taken: 5, average_score: 88.6, created_at: '2025-03-01T10:00:00.000Z' },
]

export const useInstructorStudentStore = defineStore('instructorStudent', () => {
  const students = ref<Student[]>([...MOCK_STUDENTS])
  const stats = ref<StudentStats>({ ...MOCK_STATS })
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const usingMockData = ref(false)

  const fetchStudents = async () => {
    isLoading.value = true
    error.value = null
    usingMockData.value = false

    try {
      const response = await apiClient.get('/instructor/students')
      students.value = response.data.data.students
      stats.value = response.data.data.stats
    } catch (err: any) {
      if (err.code === 'ERR_NETWORK' || !err.response) {
        console.warn('[InstructorStudentStore] Backend unreachable. Using mock data.')
        students.value = [...MOCK_STUDENTS]
        stats.value = { ...MOCK_STATS }
        usingMockData.value = true
      } else {
        error.value = err.response?.data?.message || 'Failed to load students.'
      }
    } finally {
      isLoading.value = false
    }
  }

  return {
    students,
    stats,
    isLoading,
    error,
    usingMockData,
    fetchStudents,
  }
})
