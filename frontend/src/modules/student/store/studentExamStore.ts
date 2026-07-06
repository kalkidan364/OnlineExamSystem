import { defineStore } from 'pinia'
import { ref } from 'vue'
import apiClient from '../../../core/api/apiClient'
import { sampleActiveExam, sampleUpcomingExams, sampleRecentResults } from '../data/mockData'
import type { ActiveExam, UpcomingExam, RecentResult } from '../types'

export const useStudentExamStore = defineStore('studentExam', () => {
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const usingMockData = ref(false)

  // Dashboard stats
  const dashboardStats = ref({
    completed_exams: 0,
    upcoming_exams: 0,
    average_score: 0,
  })

  // Exam data
  const activeExam = ref<ActiveExam | null>(null)
  const upcomingExams = ref<UpcomingExam[]>([])
  const results = ref<RecentResult[]>([])

  /**
   * Fetch dashboard stats from API
   */
  const fetchDashboard = async () => {
    try {
      const response = await apiClient.get('/student/dashboard')
      dashboardStats.value = response.data.data
    } catch (err: any) {
      console.error('Failed to fetch student dashboard', err)
    }
  }

  /**
   * Fetch published exams (upcoming + active) from API.
   * Falls back to mock data if backend is offline.
   */
  const fetchExams = async () => {
    isLoading.value = true
    error.value = null
    usingMockData.value = false

    try {
      const response = await apiClient.get('/student/exams')
      const data = response.data.data

      // Map active exam from API to frontend ActiveExam shape
      if (data.active_exam) {
        const ae = data.active_exam
        activeExam.value = {
          id: ae.id,
          courseCode: ae.courseCode,
          courseName: ae.courseName,
          examTitle: ae.examTitle,
          instructor: ae.instructor,
          date: ae.date,
          time: ae.time,
          durationMinutes: ae.durationMinutes,
          totalQuestions: 0, // will be filled on start
          totalMarks: ae.totalMarks,
          questions: [], // loaded when exam is started
        }
      } else {
        activeExam.value = null
      }

      // Map upcoming exams
      upcomingExams.value = data.upcoming_exams.map((e: any) => ({
        id: e.id,
        courseCode: e.courseCode,
        courseName: e.courseName,
        instructor: e.instructor,
        examType: e.examType,
        scheduledDate: e.scheduledDate,
        startTime: e.startTime,
        durationMinutes: e.durationMinutes,
        totalQuestions: e.totalQuestions,
        totalMarks: e.totalMarks,
        status: e.status as 'Soon' | 'Pending' | 'Ready',
      }))
    } catch (err: any) {
      console.error('Failed to fetch student exams, using mock data', err)
      usingMockData.value = true
      activeExam.value = sampleActiveExam ? { ...sampleActiveExam } : null
      upcomingExams.value = [...sampleUpcomingExams]
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Start an exam attempt — returns questions without correct answers.
   */
  const startExam = async (examId: number) => {
    try {
      const response = await apiClient.post(`/student/exams/${examId}/start`)
      const data = response.data.data

      // Build ActiveExam with real questions
      const examData: ActiveExam = {
        id: examId,
        courseCode: data.course_code,
        courseName: data.course_name,
        examTitle: data.exam_title,
        instructor: '',
        date: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
        time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
        durationMinutes: data.duration_minutes,
        totalQuestions: data.questions.length,
        totalMarks: data.total_marks,
        questions: data.questions.map((q: any) => ({
          id: q.id,
          text: q.text,
          type: q.type as 'multiple-choice' | 'text',
          options: (q.options || []).map((o: any) => typeof o === 'string' ? o : (o.text || '')),
        })),
      }

      activeExam.value = examData
      return { attemptId: data.attempt_id, exam: examData }
    } catch (err: any) {
      const message = err.response?.data?.message || 'Failed to start exam'
      console.error(message, err)
      throw new Error(message)
    }
  }

  /**
   * Submit exam answers — returns graded result.
   */
  const submitExam = async (examId: number, answers: Record<number, string>) => {
    try {
      const response = await apiClient.post(`/student/exams/${examId}/submit`, { answers })
      const data = response.data.data

      // Build a RecentResult from the response
      const result: RecentResult = {
        id: data.attempt_id,
        courseCode: data.course_code,
        courseName: data.course_name,
        examTitle: data.exam_title,
        score: data.score,
        totalMarks: data.total_marks,
        percentage: data.percentage,
        grade: data.grade,
        status: data.percentage >= 50 ? 'Passed' : 'Failed',
        completedDate: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
        questionsReview: data.questionsReview.map((q: any) => ({
          questionText: q.questionText,
          studentAnswer: q.studentAnswer || 'Not answered',
          correctAnswer: q.correctAnswer || 'N/A',
          explanation: q.explanation || '',
          isCorrect: q.isCorrect,
        })),
      }

      // Prepend to results
      results.value.unshift(result)

      // Clear active exam
      activeExam.value = null

      // Remove from upcoming
      upcomingExams.value = upcomingExams.value.filter(e => e.id !== examId)

      return result
    } catch (err: any) {
      console.error('Failed to submit exam', err)
      throw err
    }
  }

  /**
   * Fetch past exam results from API.
   */
  const fetchResults = async () => {
    try {
      const response = await apiClient.get('/student/results')
      results.value = response.data.data.map((r: any) => ({
        id: r.id,
        courseCode: r.courseCode,
        courseName: r.courseName,
        examTitle: r.examTitle,
        score: r.score,
        totalMarks: r.totalMarks,
        percentage: r.percentage,
        grade: r.grade,
        status: r.status as 'Passed' | 'Failed',
        completedDate: r.completedDate,
        questionsReview: r.questionsReview || [],
      }))
    } catch (err: any) {
      console.error('Failed to fetch results, using mock data', err)
      if (usingMockData.value) {
        results.value = [...sampleRecentResults]
      }
    }
  }

  return {
    isLoading,
    error,
    usingMockData,
    dashboardStats,
    activeExam,
    upcomingExams,
    results,
    fetchDashboard,
    fetchExams,
    startExam,
    submitExam,
    fetchResults,
  }
})
