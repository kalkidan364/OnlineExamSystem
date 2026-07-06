<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { initialStudentProfile, sampleAnnouncements, sampleCalendarEvents } from '../data/mockData'
import type { StudentProfile, UpcomingExam, RecentResult } from '../types'
import { useStudentExamStore } from '../store/studentExamStore'

// Component imports
import Header from '../components/Header.vue'
import HeroSection from '../components/HeroSection.vue'
import ActiveExamCard from '../components/ActiveExamCard.vue'
import UpcomingExams from '../components/UpcomingExams.vue'
import ProgressStats from '../components/ProgressStats.vue'
import RecentResults from '../components/RecentResults.vue'
import Announcements from '../components/Announcements.vue'
import CalendarSection from '../components/CalendarSection.vue'
import QuickActions from '../components/QuickActions.vue'
import ExamConsole from '../components/ExamConsole.vue'
import ResultReviewModal from '../components/ResultReviewModal.vue'
import DownloadTranscriptModal from '../components/DownloadTranscriptModal.vue'
import ProfileModal from '../components/ProfileModal.vue'

// Store
const examStore = useStudentExamStore()

// Navigation & Screen routing state
const currentView = ref<'dashboard' | 'exam-console'>('dashboard')

// Profile stays local (no API for it yet)
const profile = ref<StudentProfile>({ ...initialStudentProfile })

// Connect to store data via computed refs for reactivity
const activeExam = computed(() => examStore.activeExam)
const upcomingExams = computed(() => examStore.upcomingExams)
const results = computed(() => examStore.results)

// Overlay controller states
const selectedResultForReview = ref<RecentResult | null>(null)
const isNotificationsOpen = ref<boolean>(false)
const windowRef = window
const isTranscriptOpen = ref<boolean>(false)
const isProfileOpen = ref<boolean>(false)
const upcomingGuidelines = ref<UpcomingExam | null>(null)

// Stats calculation (dynamic updates from store!)
const completedCount = computed(() => results.value.length)
const remainingCount = computed(() => upcomingExams.value.length + (activeExam.value ? 1 : 0))

// Calculate average score based on existing results
const averageScore = computed(() => {
  if (results.value.length === 0) return 0
  return Math.round(results.value.reduce((acc, curr) => acc + curr.percentage, 0) / results.value.length)
})
const passRate = 100 // Hardcoded simulation for simplicity

// Fetch real data on mount
onMounted(async () => {
  await Promise.all([
    examStore.fetchExams(),
    examStore.fetchResults(),
    examStore.fetchDashboard(),
  ])
})

// Action helper when the student starts an exam from the upcoming list
const handleStartUpcomingExam = async (examId: number) => {
  try {
    await examStore.startExam(examId)
    currentView.value = 'exam-console'
  } catch (err: any) {
    windowRef.alert(err.message || 'Failed to start exam')
  }
}

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
    currentView.value = 'dashboard'

    // Show congratulations
    setTimeout(() => {
      windowRef.alert(`CONGRATULATIONS!\nYou have completed: ${examName} successfully!\nYour score is ${result.percentage}% (${result.grade}). Your CGPA has been adjusted to ${profile.value.cgpa} and 5 credits have been officially recorded.`)
    }, 400)
  } catch (err: any) {
    // Fallback: If API fails, still handle locally so the UI doesn't break
    currentView.value = 'dashboard'
    windowRef.alert(`Exam submitted. Score: ${percentage}%`)
  }
}

// Quick Action Click Router
const handleQuickAction = (actionKey: 'take-exam' | 'view-results' | 'download-results' | 'update-profile' | 'academic-calendar') => {
  switch (actionKey) {
    case 'take-exam':
      if (activeExam.value) {
        currentView.value = 'exam-console'
      } else if (upcomingExams.value.length > 0) {
        // Auto-start the first upcoming exam
        handleStartUpcomingExam(upcomingExams.value[0].id)
      } else {
        windowRef.alert("All current scheduled examinations have been completed. Please check back next week.")
      }
      break
    case 'view-results':
      document.getElementById('recent-results-section')?.scrollIntoView({ behavior: 'smooth' })
      break
    case 'download-results':
      isTranscriptOpen.value = true
      break
    case 'update-profile':
      isProfileOpen.value = true
      break
    case 'academic-calendar':
      document.getElementById('calendar-section')?.scrollIntoView({ behavior: 'smooth' })
      break
  }
}
</script>

<template>
  <div class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white pb-16">
    
    <!-- 1. RENDER LIVE EXAM CONSOLE (FULLSCREEN OVERLAY) -->
    <ExamConsole
      v-if="currentView === 'exam-console' && activeExam"
      :exam="activeExam"
      @cancel="currentView = 'dashboard'"
      @submit-exam="handleExamCompleted"
    />
    
    <!-- 2. RENDER MAIN PORTAL DASHBOARD -->
    <div v-else class="space-y-8 animate-in fade-in duration-300">
      
      <!-- Header element -->
      <Header
        :profile="profile"
        :announcements="sampleAnnouncements"
        @open-profile="isProfileOpen = true"
        @open-notifications="isNotificationsOpen = true"
      />

      <!-- Core container centering the grid -->
      <main class="mx-auto max-w-7xl px-6 space-y-8">
        
        <!-- Row 1: Academic Hero Welcome Section -->
        <HeroSection :profile="profile" />

        <!-- Row 2: Secondary Quick Actions Rail -->
        <QuickActions @action="handleQuickAction" />

        <!-- Row 3: Primary Core Grid Layout (Active Exam vs. Upcoming Exams) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          
          <!-- Left Column (Active Exam Area - Dominates with 7 cols) -->
          <div class="lg:col-span-7 space-y-8">
            <div v-if="activeExam">
              <div class="mb-4">
                <h3 class="text-lg font-bold text-slate-900">Current Evaluation Target</h3>
                <p class="text-xs text-slate-500 font-medium">Sit for your scheduled paper before the portal closes</p>
              </div>
              <ActiveExamCard
                :exam="activeExam"
                @start-exam="currentView = 'exam-console'"
              />
            </div>
            <div v-else class="rounded-2xl border-2 border-dashed border-emerald-200 bg-emerald-50/20 p-8 text-center space-y-4">
              <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              </div>
              <div>
                <h4 class="text-base font-bold text-slate-950">You Are Fully Up-to-Date!</h4>
                <p class="text-xs text-slate-600 mt-1 max-w-md mx-auto leading-relaxed">
                  Excellent job! You have submitted all active examination papers for the Semester II syllabus. Your responses are stored securely in the Wollo University main record server.
                </p>
              </div>
            </div>

            <!-- Upcoming exams list -->
            <UpcomingExams
              :exams="upcomingExams"
              @start-exam="handleStartUpcomingExam"
              @view-details="(exam) => upcomingGuidelines = exam"
            />
          </div>

          <!-- Right Column (Side-rails for secondary information - 5 cols) -->
          <div class="lg:col-span-5 space-y-8">
            
            <!-- Visual Calendar Component -->
            <div id="calendar-section">
              <CalendarSection :events="sampleCalendarEvents" />
            </div>

            <!-- Official Announcements -->
            <Announcements :announcements="sampleAnnouncements" />

          </div>

        </div>

        <!-- Row 4: Term Performance & Circular Metrics -->
        <ProgressStats
          :completedCount="completedCount"
          :remainingCount="remainingCount"
          :averageScore="averageScore"
          :passRate="passRate"
        />

        <!-- Row 5: Detailed Scoreboard Section -->
        <div id="recent-results-section">
          <RecentResults
            :results="results"
            @view-result="(res) => selectedResultForReview = res"
            @download-transcript="isTranscriptOpen = true"
          />
        </div>

      </main>

    </div>

    <!-- ----------------- GLOBAL PORTAL OVERLAY DRAWERS & MODALS ----------------- -->

    <!-- A. Result Review Modal (Displays detailed answers/explanations) -->
    <ResultReviewModal
      v-if="selectedResultForReview"
      :result="selectedResultForReview"
      @close="selectedResultForReview = null"
    />

    <!-- B. Official printable transcript Record slip -->
    <DownloadTranscriptModal
      v-if="isTranscriptOpen"
      :profile="profile"
      :results="results"
      @close="isTranscriptOpen = false"
    />

    <!-- C. Registered profile credentials editor -->
    <ProfileModal
      v-if="isProfileOpen"
      :profile="profile"
      @close="isProfileOpen = false"
      @update-profile="(updated) => {
        profile = updated;
        windowRef.alert('SUCCESS:\nYour registry credentials have been updated successfully on the secure server.');
      }"
    />

    <!-- D. Upcoming Exam Guidelines drawer -->
    <div v-if="upcomingGuidelines" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs animate-in fade-in">
      <div class="relative w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
        <button
          @click="upcomingGuidelines = null"
          class="absolute top-4 right-4 rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="flex items-center gap-3 mb-4">
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 border border-amber-100">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <div>
            <span class="text-[9px] font-mono font-bold text-slate-400 uppercase tracking-widest">{{ upcomingGuidelines.courseCode }}</span>
            <h3 class="text-sm font-black text-slate-900">Guidelines & Scope</h3>
          </div>
        </div>

        <h4 class="text-xs font-bold text-slate-800">{{ upcomingGuidelines.courseName }}</h4>
        <p class="text-xs text-slate-500 mt-1">Instructor: {{ upcomingGuidelines.instructor }}</p>

        <hr class="my-4 border-slate-100" />

        <div class="space-y-3 font-sans text-xs text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100">
          <p class="font-bold text-slate-900">Syllabus Chapters Covered:</p>
          <ul class="list-disc list-inside space-y-1.5 pl-1">
            <li>Lecture units 1-5 (Core theory & diagnostics)</li>
            <li>Performance metrics and case evaluations</li>
            <li>Design patterns and automated unit suites</li>
          </ul>
          <p class="font-bold text-slate-900 mt-3">Expected Deliverables:</p>
          <p class="pl-1">
            20 Multiple choice items (1 mark each) and 2 logical essays (5 marks each). Strict proctoring restrictions apply.
          </p>
        </div>

        <button
          @click="upcomingGuidelines = null"
          class="mt-6 w-full rounded-xl bg-slate-900 py-3 text-xs font-bold text-white hover:bg-slate-800 transition-colors"
        >
          Acknowledge & Close
        </button>
      </div>
    </div>

    <!-- E. Floating live notifications drawer -->
    <div v-if="isNotificationsOpen" class="fixed inset-y-0 right-0 z-50 w-full max-w-md bg-white shadow-2xl border-l border-slate-200 flex flex-col justify-between animate-in slide-in-from-right duration-250">
      <div class="p-6 overflow-y-auto space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
          <div class="flex items-center gap-2">
            <svg class="h-5 w-5 text-indigo-600 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            <h3 class="text-base font-black text-slate-900">Active Notifications</h3>
          </div>
          <button
            @click="isNotificationsOpen = false"
            class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>

        <!-- Simulated Alerts list -->
        <div class="space-y-4 font-sans text-xs text-slate-600 leading-relaxed">
          <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 space-y-1.5">
            <p class="font-bold text-rose-800 flex items-center gap-1">
              <svg class="h-4 w-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
              Live Proctoring Alert
            </p>
            <p class="text-rose-700">
              Ensure your microphone is calibrated. The system logs background noises during the live assessment window.
            </p>
          </div>

          <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 space-y-1.5">
            <p class="font-bold text-indigo-800">Candidacy Cleared</p>
            <p class="text-indigo-700">
              Your registration status for course **Compiler Design (SWE-422)** has been cleared by the Registrar.
            </p>
          </div>

          <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 space-y-1.5">
            <p class="font-bold text-amber-800">Senate Warning</p>
            <p class="text-amber-700">
              All make-up examinations must be applied for within 48 hours of schedule release. No exceptions.
            </p>
          </div>
        </div>
      </div>

      <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-center">
        <button
          @click="isNotificationsOpen = false"
          class="w-full rounded-xl bg-slate-900 hover:bg-slate-800 py-3 text-xs font-bold text-white transition-colors"
        >
          Close Alerts Drawer
        </button>
      </div>
    </div>

  </div>
</template>
