import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useCreateExamStore = defineStore('createExam', () => {
  const title = ref('')
  const courseCode = ref('') // We can leave it blank or default to SWE-301
  const examType = ref('Mid Exam')
  const totalMarks = ref(100)
  const passingMarks = ref(60)
  const description = ref('')
  
  const durationMinutes = ref(90)
  const scheduledDate = ref('')
  const scheduledTime = ref('09:00 AM')

  // Array to hold questions created during the exam creation flow
  const questions = ref<any[]>([])

  // Exam Settings
  const shuffleQuestions = ref(true)
  const showReviewScreen = ref(true)
  const shuffleAnswers = ref(true)
  const allowBacktracking = ref(true)
  const showOneQuestionAtATime = ref(false)
  const autoSubmitOnTimeFinish = ref(true)

  // Security Settings
  const enableFullscreenMode = ref(true)
  const enableBrowserTabMonitoring = ref(true)
  const disableRightClick = ref(true)
  const allowCalculator = ref(false)
  const disableCopyPaste = ref(true)
  const webcamMonitoring = ref(false)

  const maxAttempts = ref('1')
  const timeZone = ref('(UTC+03:00) Addis Ababa, Nairobi')

  const getSettingsPayload = () => {
    return {
      shuffleQuestions: shuffleQuestions.value,
      showReviewScreen: showReviewScreen.value,
      shuffleAnswers: shuffleAnswers.value,
      allowBacktracking: allowBacktracking.value,
      showOneQuestionAtATime: showOneQuestionAtATime.value,
      autoSubmitOnTimeFinish: autoSubmitOnTimeFinish.value,
      enableFullscreenMode: enableFullscreenMode.value,
      enableBrowserTabMonitoring: enableBrowserTabMonitoring.value,
      disableRightClick: disableRightClick.value,
      allowCalculator: allowCalculator.value,
      disableCopyPaste: disableCopyPaste.value,
      webcamMonitoring: webcamMonitoring.value,
      maxAttempts: maxAttempts.value,
      timeZone: timeZone.value,
    }
  }

  // Helpers to get ISO string
  const getScheduledAt = () => {
    if (!scheduledDate.value) return null
    // A simplified conversion assuming date is mm/dd/yyyy and time is hh:mm AM/PM
    try {
      const d = new Date(`${scheduledDate.value} ${scheduledTime.value}`)
      return d.toISOString()
    } catch {
      return new Date().toISOString()
    }
  }

  const reset = () => {
    title.value = ''
    courseCode.value = ''
    examType.value = 'Mid Exam'
    totalMarks.value = 100
    passingMarks.value = 60
    description.value = ''
    durationMinutes.value = 90
    scheduledDate.value = ''
    scheduledTime.value = '09:00 AM'
    questions.value = []
    
    shuffleQuestions.value = true
    showReviewScreen.value = true
    shuffleAnswers.value = true
    allowBacktracking.value = true
    showOneQuestionAtATime.value = false
    autoSubmitOnTimeFinish.value = true
    enableFullscreenMode.value = true
    enableBrowserTabMonitoring.value = true
    disableRightClick.value = true
    allowCalculator.value = false
    disableCopyPaste.value = true
    webcamMonitoring.value = false
    maxAttempts.value = '1'
    timeZone.value = '(UTC+03:00) Addis Ababa, Nairobi'
  }

  return {
    title,
    courseCode,
    examType,
    totalMarks,
    passingMarks,
    description,
    durationMinutes,
    scheduledDate,
    scheduledTime,
    questions,
    shuffleQuestions,
    showReviewScreen,
    shuffleAnswers,
    allowBacktracking,
    showOneQuestionAtATime,
    autoSubmitOnTimeFinish,
    enableFullscreenMode,
    enableBrowserTabMonitoring,
    disableRightClick,
    allowCalculator,
    disableCopyPaste,
    webcamMonitoring,
    maxAttempts,
    timeZone,
    getSettingsPayload,
    getScheduledAt,
    reset
  }
})
