<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import type { ActiveExam, Question } from '../types'

const props = defineProps<{
  exam: ActiveExam
}>()

const emit = defineEmits<{
  (e: 'cancel'): void
  (e: 'submit-exam', answers: Record<number, string>, scores: number, percentage: number): void
}>()

const questions = ref<Question[]>(props.exam.questions)
const currentIndex = ref<number>(0)
const answers = ref<Record<number, string>>({})
const flagged = ref<Record<number, boolean>>({})
const secondsRemaining = ref<number>(props.exam.durationMinutes * 60)
const showConfirmSubmit = ref<boolean>(false)
const tabSwitches = ref<number>(0)

const activeQuestion = computed(() => questions.value[currentIndex.value])

let timer: number | null = null

// Live timer countdown
onMounted(() => {
  timer = window.setInterval(() => {
    if (secondsRemaining.value <= 1) {
      if (timer) clearInterval(timer)
      triggerAutoSubmit()
      secondsRemaining.value = 0
    } else {
      secondsRemaining.value--
    }
  }, 1000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})

// Monitor screen tab focus switches to enforce academic integrity rules
const handleVisibilityChange = () => {
  if (document.hidden) {
    tabSwitches.value++
    if (tabSwitches.value >= 3) {
      alert("SECURITY VIOLATION LOGGED:\nYou have switched tabs or lost focus 3 times. This attempt has been logged and sent to Dr. Abraham Getahun.")
    } else {
      alert(`ACADEMIC INTEGRITY WARNING:\nTab switches or window changes are strictly logged by Wollo University proctors.\nWarning count: ${tabSwitches.value}/3`)
    }
  }
}

onMounted(() => {
  document.addEventListener('visibilitychange', handleVisibilityChange)
})

onUnmounted(() => {
  document.removeEventListener('visibilitychange', handleVisibilityChange)
})

const formatTime = (secs: number) => {
  const hours = Math.floor(secs / 3600)
  const minutes = Math.floor((secs % 3600) / 60)
  const seconds = secs % 60
  return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
}

const handleSelectOption = (option: string) => {
  answers.value[activeQuestion.value.id] = option
}

const toggleFlag = () => {
  flagged.value[activeQuestion.value.id] = !flagged.value[activeQuestion.value.id]
}

const isQuestionAnswered = (qId: number) => {
  const ans = answers.value[qId]
  return ans !== undefined && ans.trim() !== ''
}

const triggerAutoSubmit = () => {
  alert("Time limit reached! Your academic response sheet is being compiled and submitted automatically.")
  processAndSubmit()
}

const processAndSubmit = () => {
  let correctCount = 0
  questions.value.forEach(q => {
    if (q.type === 'multiple-choice') {
      if (answers.value[q.id] === q.correctAnswer) {
        correctCount += 1
      }
    } else {
      // Free text: simulate generous grading if they input anything
      if (answers.value[q.id] && answers.value[q.id].length > 10) {
        correctCount += 1
      }
    }
  })

  // Score out of 50
  const scoredMarks = correctCount * 10
  const percentage = (scoredMarks / 50) * 100

  emit('submit-exam', answers.value, scoredMarks, percentage)
}

const confirmCancel = () => {
  if (confirm("Are you sure you want to exit the Exam Console? Your current draft options will be saved, but the countdown continues.")) {
    emit('cancel')
  }
}
</script>

<template>
  <div class="min-h-screen bg-slate-900 text-slate-100 flex flex-col font-sans selection:bg-indigo-500 selection:text-white pb-16">
    <!-- Top Console Bar -->
    <header class="border-b border-slate-800 bg-slate-950 px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white font-serif font-black text-sm">
          W
        </div>
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xs font-bold tracking-widest text-slate-400 uppercase">EXAM PORTAL</span>
            <span class="h-2 w-2 rounded-full bg-rose-500 animate-ping"></span>
          </div>
          <h2 class="text-sm font-bold text-white truncate max-w-xs sm:max-w-md">
            {{ exam.courseCode }}: {{ exam.courseName }}
          </h2>
        </div>
      </div>

      <!-- Live Timer and Submit Block -->
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-2 rounded-lg bg-slate-800 px-3.5 py-1.5 border border-slate-700">
          <svg class="h-4 w-4 text-rose-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          <span class="font-mono text-sm font-extrabold text-white">
            {{ formatTime(secondsRemaining) }}
          </span>
        </div>

        <button
          @click="showConfirmSubmit = true"
          class="rounded-lg bg-indigo-600 hover:bg-indigo-700 px-4 py-2 text-xs font-bold text-white transition-all shadow-md shadow-indigo-600/10 active:scale-[0.98]"
        >
          Submit Response Sheet
        </button>
      </div>
    </header>

    <!-- Main Console Arena -->
    <div class="flex-1 grid grid-cols-1 xl:grid-cols-12 gap-6 max-w-[1800px] w-full mx-auto p-6 overflow-hidden">
      
      <!-- Left Side: Question Navigation & Live Feed Mockup (3 Cols) -->
      <div class="xl:col-span-3 space-y-6 flex flex-col justify-start">
        
        <!-- Visual Proctoring System -->
        <div class="rounded-xl border border-slate-800 bg-slate-950 p-4 space-y-3">
          <div class="flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
              <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-ping"></span>
              Webcam Proctor Active
            </span>
            <span class="text-[9px] font-mono font-bold text-emerald-400 uppercase bg-emerald-950/50 px-1.5 py-0.5 rounded">
              AI MATCHED 100%
            </span>
          </div>

          <!-- Simulated Live Frame -->
          <div class="relative aspect-video rounded-lg overflow-hidden bg-slate-800 border border-slate-700 flex items-center justify-center">
            <!-- Overlay graphics to simulate biometric analysis -->
            <div class="absolute inset-0 border border-indigo-500/30 m-4 rounded pointer-events-none"></div>
            <div class="absolute top-2 left-2 text-[8px] font-mono text-slate-400 bg-black/40 px-1.5 py-0.5 rounded">
              SECURE_ID: Kalkidan M.
            </div>
            <div class="absolute bottom-2 right-2 flex items-center gap-1 text-[8px] font-mono text-slate-400 bg-black/40 px-1.5 py-0.5 rounded">
              <span class="h-1 w-1 rounded-full bg-red-500 animate-pulse"></span>
              REC 1080p
            </div>

            <!-- Vector Mock Face / Camera Silhouette -->
            <div class="flex flex-col items-center text-slate-400">
              <div class="h-16 w-16 rounded-full border-2 border-indigo-500/50 flex items-center justify-center bg-slate-900 relative">
                <div class="h-8 w-8 rounded-full bg-indigo-500/10 border border-indigo-500/30"></div>
                <!-- Facial recognition reticle lines -->
                <div class="absolute -top-1 -left-1 h-3 w-3 border-t-2 border-l-2 border-emerald-400"></div>
                <div class="absolute -top-1 -right-1 h-3 w-3 border-t-2 border-r-2 border-emerald-400"></div>
                <div class="absolute -bottom-1 -left-1 h-3 w-3 border-b-2 border-l-2 border-emerald-400"></div>
                <div class="absolute -bottom-1 -right-1 h-3 w-3 border-b-2 border-r-2 border-emerald-400"></div>
              </div>
              <span class="text-[9px] font-mono uppercase tracking-widest text-slate-400 mt-2">Biometrics Synchronized</span>
            </div>
          </div>
          
          <p class="text-[10px] text-slate-500 text-center leading-relaxed font-sans">
            Head positioning, ambient voice feeds, and multiple-monitors are audited continuously.
          </p>
        </div>

        <!-- Question Navigator Board -->
        <div class="rounded-xl border border-slate-800 bg-slate-950 p-5 space-y-4">
          <div>
            <h4 class="text-xs font-bold text-white uppercase tracking-wider">Question Navigation</h4>
            <p class="text-[11px] text-slate-500 mt-0.5">Jump directly to any section</p>
          </div>

          <div class="grid grid-cols-5 gap-2">
            <button
              v-for="(q, idx) in questions"
              :key="q.id"
              @click="currentIndex = idx"
              :class="[
                'relative flex h-10 w-full items-center justify-center rounded-lg border font-mono text-xs font-bold transition-all',
                idx === currentIndex
                  ? 'bg-indigo-600 border-indigo-500 text-white shadow-md shadow-indigo-600/20 scale-105'
                  : isQuestionAnswered(q.id)
                  ? 'bg-emerald-950/40 border-emerald-800/80 text-emerald-400'
                  : 'bg-slate-900 border-slate-800 text-slate-400 hover:bg-slate-800 hover:border-slate-700'
              ]"
            >
              {{ idx + 1 }}
              <!-- Small flag icon overlay -->
              <span v-if="flagged[q.id]" class="absolute -top-1 -right-1 flex h-3 w-3">
                <span class="relative inline-flex h-3 w-3 rounded-full bg-amber-500 text-[8px] items-center justify-center text-slate-950 font-bold">!</span>
              </span>
            </button>
          </div>

          <hr class="border-slate-800" />

          <!-- Navigator Legends -->
          <div class="space-y-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
            <div class="flex items-center gap-2">
              <span class="h-3 w-3 rounded bg-emerald-950/80 border border-emerald-800"></span>
              <span>Completed / Answered</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="h-3 w-3 rounded bg-slate-900 border border-slate-800"></span>
              <span>Unvisited / Empty</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="relative h-3 w-3 rounded bg-slate-900 border border-slate-800 flex items-center justify-center text-amber-500 font-black text-[9px]">!</span>
              <span>Flagged for review</span>
            </div>
          </div>
        </div>

      </div>

      <!-- Center: Main Active Question Board (6 Cols) -->
      <div class="xl:col-span-6 space-y-6 flex flex-col justify-between">
        
        <div class="rounded-xl border border-slate-800 bg-slate-950 p-6 md:p-8 space-y-6 min-h-[480px]">
          <!-- Question Header meta -->
          <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 pb-4">
            <div class="space-y-1">
              <span class="text-[10px] font-mono uppercase tracking-widest text-indigo-400 font-bold">
                Section A: Core Evaluation
              </span>
              <h3 class="text-base font-black text-white">
                Question {{ currentIndex + 1 }} of {{ questions.length }}
              </h3>
            </div>
            <div class="flex items-center gap-2">
              <span class="rounded bg-slate-800 px-2 py-1 text-[10px] font-mono text-slate-300 border border-slate-700">
                Value: 10 Marks
              </span>
              <button
                @click="toggleFlag"
                :class="[
                  'inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-bold transition-all border',
                  flagged[activeQuestion.id]
                    ? 'bg-amber-500/10 border-amber-500 text-amber-400'
                    : 'bg-slate-900 border-slate-800 text-slate-400 hover:text-slate-300'
                ]"
              >
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                <span>{{ flagged[activeQuestion.id] ? 'Flagged' : 'Flag Question' }}</span>
              </button>
            </div>
          </div>

          <!-- The Question Statement -->
          <div class="space-y-4">
            <p class="text-base font-medium leading-relaxed text-slate-100 font-sans">
              {{ activeQuestion.text }}
            </p>
          </div>

          <!-- Answer Controls: Pick Option vs free text -->
          <div class="mt-8 space-y-3">
            <template v-if="activeQuestion.type === 'multiple-choice' && activeQuestion.options">
              <button
                v-for="(option, index) in activeQuestion.options"
                :key="index"
                @click="handleSelectOption(option)"
                :class="[
                  'flex w-full items-start gap-4 rounded-xl border p-4 text-left transition-all text-xs font-medium font-sans',
                  answers[activeQuestion.id] === option
                    ? 'bg-indigo-600/15 border-indigo-500 text-indigo-200'
                    : 'bg-slate-900 border-slate-800 text-slate-300 hover:bg-slate-850 hover:border-slate-700'
                ]"
              >
                <span :class="[
                  'flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border text-xs font-bold font-mono',
                  answers[activeQuestion.id] === option
                    ? 'bg-indigo-600 border-indigo-400 text-white'
                    : 'bg-slate-950 border-slate-700 text-slate-400'
                ]">
                  {{ String.fromCharCode(65 + index) }}
                </span>
                <span class="leading-relaxed pt-0.5">{{ option }}</span>
              </button>
            </template>
            <template v-else>
              <div class="space-y-2">
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2">Write your analytical response below:</p>
                <textarea
                  rows="8"
                  v-model="answers[activeQuestion.id]"
                  placeholder="Provide a clear, detailed, and mathematically grounded response. Your input is saved automatically on each keystroke..."
                  class="w-full rounded-xl border border-slate-800 bg-slate-900 p-4 text-xs font-sans text-slate-100 placeholder-slate-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-hidden"
                ></textarea>
                <div class="flex items-center justify-between text-[10px] text-slate-500 font-semibold font-mono px-1">
                  <span>Markdown formatting is supported</span>
                  <span>Characters count: {{ (answers[activeQuestion.id] || '').length }}/1500 maximum</span>
                </div>
              </div>
            </template>
          </div>
        </div>

        <!-- Bottom Question Controls -->
        <div class="flex items-center justify-between">
          <button
            @click="currentIndex = Math.max(0, currentIndex - 1)"
            :disabled="currentIndex === 0"
            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-800 bg-slate-950 px-4 py-2 text-xs font-bold text-slate-400 hover:text-white hover:border-slate-700 transition-colors disabled:opacity-30 disabled:pointer-events-none"
          >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Back Question</span>
          </button>

          <span class="text-xs font-bold font-mono text-slate-500 hidden sm:inline-block">
            SECURE BUFFER: ALL INPUTS SAVED REDUNDANTLY
          </span>

          <button
            v-if="currentIndex < questions.length - 1"
            @click="currentIndex = Math.min(questions.length - 1, currentIndex + 1)"
            class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-xs font-bold text-white hover:bg-indigo-700 transition-colors"
          >
            <span>Next Question</span>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
          </button>
          <button
            v-else
            @click="showConfirmSubmit = true"
            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition-colors"
          >
            <span>Finish & Submit</span>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
          </button>
        </div>

      </div>

      <!-- Right Side: Proctors integrity log & Exam conditions (3 Cols) -->
      <div class="xl:col-span-3 space-y-6">
        
        <!-- Active Proctor Logs -->
        <div class="rounded-xl border border-slate-800 bg-slate-950 p-5 space-y-4">
          <div>
            <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
              <svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
              Auditor Live Logs
            </h4>
            <p class="text-[11px] text-slate-500 mt-0.5">Continuous integrity audit metrics</p>
          </div>

          <div class="space-y-3 text-[11px] font-mono text-slate-400">
            <div class="flex justify-between border-b border-slate-900 pb-2">
              <span>Lockdown Status:</span>
              <span class="text-emerald-400 font-bold">SECURE</span>
            </div>
            <div class="flex justify-between border-b border-slate-900 pb-2">
              <span>Tab focus limits:</span>
              <span :class="tabSwitches > 0 ? 'text-amber-400' : 'text-slate-400'">
                {{ tabSwitches }} of 3 switches
              </span>
            </div>
            <div class="flex justify-between border-b border-slate-900 pb-2">
              <span>Connected servers:</span>
              <span>Dessie-Primary-Node-01</span>
            </div>
            <div class="flex justify-between border-b border-slate-900 pb-2">
              <span>Integrity Index:</span>
              <span class="text-emerald-400 font-bold">99.8% Perfect</span>
            </div>
            <div class="flex justify-between pb-1">
              <span>IP Address logged:</span>
              <span>10.124.93.18</span>
            </div>
          </div>

          <div class="rounded-lg bg-rose-950/20 p-3 border border-rose-900/30 text-[10px] text-rose-400 flex items-start gap-2">
            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Warning: Do not open DevTools console or any terminal window. Doing so results in direct exam termination.</span>
          </div>
        </div>

        <!-- Official Course Guidelines -->
        <div class="rounded-xl border border-slate-800 bg-slate-950 p-5 space-y-3">
          <h4 class="text-xs font-bold text-white uppercase tracking-wider">Candidate Guidelines</h4>
          <ul class="text-xs text-slate-400 space-y-2 list-disc list-inside font-sans leading-relaxed">
            <li>Each question awards 10 marks equally. No negative marking is applied.</li>
            <li>Calculations can be done on a blank sheet of paper within webcam frame.</li>
            <li>Once submitted, you will receive an immediate score summary sheet.</li>
            <li>For assistance, hit the proctor chat icon (contact Dr. Abraham).</li>
          </ul>
        </div>

        <!-- Quick Exit trigger -->
        <button
          @click="confirmCancel"
          class="w-full rounded-xl border border-rose-950 bg-rose-950/10 text-rose-400 py-2.5 text-xs font-bold hover:bg-rose-950/30 transition-all border-dashed"
        >
          Suspend Session & Go Back
        </button>

      </div>
    </div>

    <!-- Submit Confirmation Dialog Drawer -->
    <div v-if="showConfirmSubmit" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 p-4 backdrop-blur-md">
      <div class="relative w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 p-6 md:p-8 shadow-2xl text-slate-100 animate-in fade-in zoom-in-95">
        <h3 class="text-xl font-extrabold text-white">Academic Response Submission</h3>
        <p class="mt-2 text-xs text-slate-400">
          You are about to submit your academic sheets for <strong class="text-white">{{ exam.courseCode }}</strong>.
        </p>

        <hr class="my-5 border-slate-800" />

        <div class="space-y-3 bg-slate-950/60 p-4 rounded-xl border border-slate-800 font-mono text-xs">
          <div class="flex justify-between text-slate-400">
            <span>Total Questions:</span>
            <span class="text-white font-bold">{{ questions.length }}</span>
          </div>
          <div class="flex justify-between text-slate-400">
            <span>Answered Questions:</span>
            <span class="text-emerald-400 font-bold">
              {{ questions.filter((q: any) => isQuestionAnswered(q.id)).length }} of {{ questions.length }}
            </span>
          </div>
          <div class="flex justify-between text-slate-400">
            <span>Flagged Questions:</span>
            <span class="text-amber-400 font-bold">
              {{ Object.values(flagged).filter(Boolean).length }} flagged
            </span>
          </div>
          <div class="flex justify-between text-slate-400">
            <span>Proctor Violations logged:</span>
            <span :class="tabSwitches > 0 ? 'text-rose-400' : 'text-slate-400'">
              {{ tabSwitches }} issues logged
            </span>
          </div>
        </div>

        <div class="rounded-lg bg-indigo-950/40 border border-indigo-900/30 p-3 text-[11px] text-indigo-400 font-sans mt-5">
          💡 Your responses are graded immediately on submission. Results are posted directly to your senate clearance profile.
        </div>

        <div class="mt-6 flex gap-3">
          <button
            @click="showConfirmSubmit = false"
            class="flex-1 rounded-xl border border-slate-800 bg-slate-950 py-3 text-xs font-bold text-slate-400 hover:text-white hover:bg-slate-900 transition-colors"
          >
            Resume Exam
          </button>
          <button
            @click="() => { showConfirmSubmit = false; processAndSubmit() }"
            class="flex-1 rounded-xl bg-emerald-600 py-3 text-xs font-bold text-white hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/10"
          >
            Confirm Final Submit
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
