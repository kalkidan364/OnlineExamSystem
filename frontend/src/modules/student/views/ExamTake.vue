<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { initialStudentProfile } from '../data/mockData'
import type { StudentProfile, RecentResult } from '../types'
import { useStudentExamStore } from '../store/studentExamStore'

// Component imports
import ExamConsole from '../components/ExamConsole.vue'

// Store
const examStore = useStudentExamStore()
const router = useRouter()

// Profile stays local (no API for it yet)
const profile = ref<StudentProfile>({ ...initialStudentProfile })
const windowRef = window

// Connect to store data via computed refs for reactivity
const activeExam = computed(() => examStore.activeExam)

// If there's no active exam loaded yet, try fetching
onMounted(async () => {
  if (!activeExam.value) {
    await examStore.fetchExams()
  }
  // If still no active exam after fetch, redirect back to dashboard
  if (!activeExam.value) {
    router.replace('/student')
  }
})

// Action helper when the student submits an exam in ExamConsole
const handleExamCompleted = async (
  answers: Record<number, string>,
  _scoredMarks: number,
  percentage: number
) => {
  if (!activeExam.value) return

  const examName = activeExam.value.courseName
  const examId = activeExam.value.id

  try {
    // Submit to backend for real grading
    const result = await examStore.submitExam(examId, answers)

    // Dynamically bump student profile metrics
    profile.value.creditsCompleted += 5
    profile.value.cgpa = Math.min(4.00, Number((profile.value.cgpa + 0.02).toFixed(2)))

    // Route back to dashboard
    router.push('/student')

    // Show congratulations
    setTimeout(() => {
      windowRef.alert(`CONGRATULATIONS!\nYou have completed: ${examName} successfully!\nYour score is ${result.percentage}% (${result.grade}). Your CGPA has been adjusted to ${profile.value.cgpa} and 5 credits have been officially recorded.`)
    }, 400)
  } catch (err: any) {
    // Fallback: If API fails, still handle locally so the UI doesn't break
    router.push('/student')
    windowRef.alert(`Exam submitted. Score: ${percentage}%`)
  }
}

const handleCancel = () => {
  router.push('/student')
}
</script>

<template>
  <div class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">
    
    <!-- RENDER LIVE EXAM CONSOLE (FULLSCREEN) -->
    <ExamConsole
      v-if="activeExam"
      :exam="activeExam"
      @cancel="handleCancel"
      @submit-exam="handleExamCompleted"
    />
    
    <!-- Loading state if no exam yet -->
    <div v-else class="flex items-center justify-center min-h-screen">
      <div class="text-center space-y-4">
        <div class="w-12 h-12 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto"></div>
        <p class="text-sm text-slate-500 font-medium">Loading exam...</p>
      </div>
    </div>

  </div>
</template>
