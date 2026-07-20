import { defineStore } from 'pinia'
import { ref } from 'vue'

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

const MOCK_STATS: ResultStats = {
  total_exams: 8,
  completed_exams: 5,
  pending_manual_grading: 2,
  published_results: 3,
  average_score: 74,
  students_evaluated: 124,
  total_students: 125,
}

const MOCK_RESULTS: ResultExam[] = [
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
  },
  {
    id: 6,
    title: 'Database Systems Final Examination',
    subtitle: 'Final Semester Examination',
    type: 'Final Exam',
    scheduled_at: '2025-06-10T14:00:00',
    total_students: 125,
    submitted_count: 110,
    graded_count: 0,
    is_published: false,
    average_score: null,
    status: 'Not Started'
  },
  {
    id: 7,
    title: 'Database Systems Practical Exam',
    subtitle: 'Practical Examination',
    type: 'Practical',
    scheduled_at: '2025-06-05T09:00:00',
    total_students: 125,
    submitted_count: 0,
    graded_count: 0,
    is_published: false,
    average_score: null,
    status: 'Not Started'
  },
  {
    id: 8,
    title: 'Database Systems Old Final Exam',
    subtitle: 'Old Final (Reference)',
    type: 'Final Exam',
    scheduled_at: '2025-01-15T09:00:00',
    total_students: 125,
    submitted_count: 125,
    graded_count: 125,
    is_published: true,
    average_score: 71.4,
    status: 'Published'
  }
]

export const useInstructorResultStore = defineStore('instructorResult', () => {
  const stats = ref<ResultStats>({ ...MOCK_STATS })
  const results = ref<ResultExam[]>([...MOCK_RESULTS])
  const isLoading = ref(false)
  
  const fetchResults = async () => {
    // Forcing mock data to match the UI precisely
    stats.value = { ...MOCK_STATS }
    results.value = [...MOCK_RESULTS]
  }

  return {
    stats,
    results,
    isLoading,
    fetchResults,
  }
})
