<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

interface UpcomingExam {
  id: number
  courseCode: string
  courseName: string
  examType: string
  instructor?: string
  scheduledAt?: string | null   // ISO string from backend
  scheduledDate?: string | null  // fallback (also ISO now)
  startTime?: string
  durationMinutes: number
  totalMarks?: number
}

const props = defineProps<{
  exam: UpcomingExam
}>()

const emit = defineEmits<{
  (e: 'start-exam', id: number): void
}>()

const currentTime = ref(new Date())
let timerInterval: number | null = null

onMounted(() => {
  timerInterval = window.setInterval(() => {
    currentTime.value = new Date()
  }, 1000)
})

onUnmounted(() => {
  if (timerInterval !== null) {
    clearInterval(timerInterval)
    timerInterval = null
  }
})

// Parse the exam start as a proper Date from the ISO string the backend sends
const startDateTime = computed((): Date => {
  // Try scheduledAt first (new field), then scheduledDate (legacy but now also ISO)
  const raw = props.exam.scheduledAt || props.exam.scheduledDate
  if (!raw) return new Date(0) // epoch sentinel for "no date"
  const d = new Date(raw)
  return isNaN(d.getTime()) ? new Date(0) : d
})

const endDateTime = computed((): Date => {
  const start = startDateTime.value
  if (!start.getTime()) return new Date(0)
  return new Date(start.getTime() + props.exam.durationMinutes * 60 * 1000)
})

const now = computed(() => currentTime.value.getTime())
const startMs = computed(() => startDateTime.value.getTime())
const endMs = computed(() => endDateTime.value.getTime())

// 10 minutes in ms
const TEN_MIN = 10 * 60 * 1000

/** Status rules */
const isUpcoming = computed(() => now.value < startMs.value - TEN_MIN)
const isReady    = computed(() => now.value >= startMs.value - TEN_MIN && now.value < startMs.value)
const isOngoing  = computed(() => now.value >= startMs.value && now.value < endMs.value)
const isFinished = computed(() => now.value >= endMs.value)

/** Whether the card should be shown at all */
const isVisible = computed(() => isReady.value || isOngoing.value)

/** Countdown until exam starts (only meaningful during READY phase) */
const countdownMs = computed(() => Math.max(0, startMs.value - now.value))

const formattedCountdown = computed(() => {
  const ms = countdownMs.value
  if (ms <= 0) return '00:00'
  const mins = Math.floor(ms / (1000 * 60))
  const secs = Math.floor((ms % (1000 * 60)) / 1000)
  return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`
})

/** Formatters */
const formatDate = (d: Date): string => {
  if (!d.getTime()) return 'TBD'
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}
const formatTime = (d: Date): string => {
  if (!d.getTime()) return 'TBD'
  return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
}
const getMonthShort = (d: Date): string => {
  if (!d.getTime()) return '---'
  return d.toLocaleDateString('en-US', { month: 'short' }).toUpperCase()
}
const getDayNum = (d: Date): string => {
  if (!d.getTime()) return '--'
  return String(d.getDate()).padStart(2, '0')
}
</script>

<template>
  <!-- Only render when within the 10-min window or during the exam itself -->
  <div v-if="isVisible" class="bg-white rounded-2xl border-2 border-red-400 p-5 flex flex-col md:flex-row md:items-center justify-between gap-5 relative overflow-hidden shadow-md hover:shadow-lg transition-shadow">

    <!-- Left red accent bar -->
    <div class="absolute top-0 left-0 w-1 h-full bg-red-500 rounded-l-2xl"></div>

    <!-- Live "Ongoing" pulse indicator -->
    <div v-if="isOngoing" class="absolute top-3 right-3 flex h-3 w-3">
      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
      <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
    </div>

    <!-- Date Box + Exam Info -->
    <div class="flex items-center gap-4 flex-1 pl-3">
      <!-- Date Box -->
      <div class="flex flex-col items-center justify-center w-14 h-14 bg-red-50 rounded-xl border border-red-100 flex-shrink-0">
        <span class="text-[10px] font-black text-red-600 uppercase tracking-widest">{{ getMonthShort(startDateTime) }}</span>
        <span class="text-xl font-black text-slate-900 leading-none mt-0.5">{{ getDayNum(startDateTime) }}</span>
      </div>

      <!-- Info -->
      <div class="min-w-0">
        <div class="flex items-center gap-2 mb-0.5">
          <span v-if="isOngoing" class="px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-green-100">Ongoing</span>
          <span v-else class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-red-100">Starting Soon</span>
        </div>
        <h4 class="text-base font-extrabold text-slate-900 truncate pr-4">{{ exam.examType }}</h4>
        <p class="text-xs font-semibold text-slate-500 truncate mt-0.5 mb-1.5">{{ exam.courseCode }} — {{ exam.courseName }}</p>
        <div class="flex items-center gap-3 text-[11px] font-medium text-slate-500">
          <span class="flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ formatTime(startDateTime) }} – {{ formatTime(endDateTime) }} ({{ exam.durationMinutes }}m)
          </span>
        </div>
      </div>
    </div>

    <!-- Action Area -->
    <div class="flex items-center gap-3 shrink-0">

      <!-- READY: show countdown only, no start button -->
      <div v-if="isReady" class="flex flex-col items-center">
        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Starts In</span>
        <div class="bg-slate-900 text-white font-mono font-black py-2 px-5 rounded-xl text-lg shadow-inner tracking-widest">
          {{ formattedCountdown }}
        </div>
        <span class="text-[10px] text-slate-400 mt-1">mm:ss</span>
      </div>

      <!-- ONGOING: show Start Exam button -->
      <div v-else-if="isOngoing">
        <button
          @click="emit('start-exam', exam.id)"
          class="bg-green-50 text-green-700 border-2 border-green-500 hover:bg-green-100 font-bold py-2.5 px-6 rounded-xl text-sm transition-colors flex items-center gap-2 shadow-sm"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          Start Exam
        </button>
      </div>

    </div>
  </div>
</template>
