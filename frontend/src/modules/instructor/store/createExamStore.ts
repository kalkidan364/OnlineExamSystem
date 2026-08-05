import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useCreateExamStore = defineStore('createExam', () => {
  const title = ref('')
  const courseCode = ref('')
  const examType = ref('Mid Exam')
  const totalMarks = ref(100)
  const passingMarks = ref(60)
  const description = ref('')
  
  const durationMinutes = ref(90)
  const scheduledDate = ref('')
  const scheduledTime = ref('09:00 AM')

  // Editing an existing exam
  const editingExamId = ref<number | null>(null)

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

  // Load an existing exam into the form store for editing
  const loadExamForEditing = (exam: any) => {
    editingExamId.value = exam.id
    title.value = exam.title || ''
    courseCode.value = exam.course_code || ''
    examType.value = exam.course_name || 'Mid Exam'
    totalMarks.value = exam.total_marks || 100
    durationMinutes.value = exam.duration_minutes || 90

    // Parse scheduled_at back to date/time fields
    if (exam.scheduled_at) {
      const d = new Date(exam.scheduled_at)
      const month = String(d.getMonth() + 1).padStart(2, '0')
      const day = String(d.getDate()).padStart(2, '0')
      const year = d.getFullYear()
      scheduledDate.value = `${month}/${day}/${year}`
      let hours = d.getHours()
      const minutes = String(d.getMinutes()).padStart(2, '0')
      const ampm = hours >= 12 ? 'PM' : 'AM'
      hours = hours % 12 || 12
      scheduledTime.value = `${String(hours).padStart(2, '0')}:${minutes} ${ampm}`
    }

    // Load questions
    questions.value = (exam.questions || []).map((q: any) => ({
      ...q,
      options: Array.isArray(q.options)
        ? q.options.map((o: any) => (typeof o === 'string' ? o : o.text || o))
        : [],
    }))

    // Load settings
    const s = exam.settings || {}
    shuffleQuestions.value = s.shuffleQuestions ?? s.shuffle_questions ?? true
    showReviewScreen.value = s.showReviewScreen ?? s.show_review_screen ?? true
    shuffleAnswers.value = s.shuffleAnswers ?? s.shuffle_answers ?? true
    allowBacktracking.value = s.allowBacktracking ?? s.allow_backtracking ?? true
    showOneQuestionAtATime.value = s.showOneQuestionAtATime ?? s.show_one_question_at_a_time ?? false
    autoSubmitOnTimeFinish.value = s.autoSubmitOnTimeFinish ?? s.auto_submit_on_time_finish ?? true
    enableFullscreenMode.value = s.enableFullscreenMode ?? s.enable_fullscreen_mode ?? true
    enableBrowserTabMonitoring.value = s.enableBrowserTabMonitoring ?? s.enable_browser_tab_monitoring ?? true
    disableRightClick.value = s.disableRightClick ?? s.disable_right_click ?? true
    allowCalculator.value = s.allowCalculator ?? s.allow_calculator ?? false
    disableCopyPaste.value = s.disableCopyPaste ?? s.disable_copy_paste ?? true
    webcamMonitoring.value = s.webcamMonitoring ?? s.webcam_monitoring ?? false
    maxAttempts.value = String(s.maxAttempts ?? s.max_attempts ?? '1')
    timeZone.value = s.timeZone ?? s.time_zone ?? '(UTC+03:00) Addis Ababa, Nairobi'
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
    editingExamId.value = null
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
    editingExamId,
    loadExamForEditing,
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
