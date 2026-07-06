<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCreateExamStore } from '../../store/createExamStore'
import { useInstructorExamStore } from '../../store/instructorExamStore'

const emit = defineEmits(['cancel', 'edit-step'])
const router = useRouter()
const formStore = useCreateExamStore()
const examStore = useInstructorExamStore()

const isSubmitting = ref(false)
const showQuestionsFullscreen = ref(false)

const handlePublish = async () => {
  isSubmitting.value = true
  try {
    await examStore.createExam({
      title: formStore.title || 'Untitled Exam',
      course_code: formStore.courseCode || 'SWE-301',
      course_name: formStore.examType || 'Software Engineering',
      duration_minutes: formStore.durationMinutes,
      total_marks: formStore.totalMarks,
      status: 'published',
      scheduled_at: formStore.getScheduledAt(),
      questions: formStore.questions,
      settings: formStore.getSettingsPayload()
    })
    formStore.reset()
    router.push('/instructor/exams')
  } catch (err) {
    console.error('Failed to create exam:', err)
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="mb-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h2 class="text-[18px] font-bold text-slate-800">Review & Publish</h2>
        <p class="text-[13px] text-slate-500 mt-1">Review all exam details before publishing. You can go back to previous steps to make changes.</p>
      </div>
      <button @click="emit('edit-step', 1)" class="px-5 py-2 border border-[#5138ed] text-[#5138ed] text-[12px] font-bold rounded-xl hover:bg-indigo-50 transition-colors flex items-center gap-2">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
        Edit Sections
      </button>
    </div>

    <div class="space-y-6 mb-6">
      
      <!-- 1. Exam Information -->
      <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-[14px] font-bold text-slate-800">1. Exam Information</h3>
          </div>
          <button @click="emit('edit-step', 1)" class="flex items-center gap-1.5 text-[12px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            Edit
          </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
          <div>
            <span class="block text-[11px] font-bold text-slate-500 mb-1">Exam Title</span>
            <span class="text-[13px] font-medium text-slate-800">{{ formStore.title || 'Untitled' }}</span>
          </div>
          <div>
            <span class="block text-[11px] font-bold text-slate-500 mb-1">Course</span>
            <span class="text-[13px] font-medium text-slate-800">{{ formStore.courseCode || 'SWE-301' }}</span>
          </div>
          <div>
            <span class="block text-[11px] font-bold text-slate-500 mb-1">Exam Type</span>
            <span class="inline-block px-3 py-1 bg-indigo-50 text-[#5138ed] text-[11px] font-bold rounded-lg">{{ formStore.examType }}</span>
          </div>
          <div>
            <span class="block text-[11px] font-bold text-slate-500 mb-1">Total Marks</span>
            <span class="text-[13px] font-medium text-slate-800">{{ formStore.totalMarks }}</span>
          </div>
          <div>
            <span class="block text-[11px] font-bold text-slate-500 mb-1">Passing Marks</span>
            <span class="text-[13px] font-medium text-slate-800">{{ formStore.passingMarks }} ({{ Math.round((formStore.passingMarks / formStore.totalMarks) * 100) || 0 }}%)</span>
          </div>
        </div>
        <div>
          <span class="block text-[11px] font-bold text-slate-500 mb-1">Description</span>
          <p class="text-[12px] text-slate-600 font-medium leading-relaxed">{{ formStore.description || 'No description provided.' }}</p>
        </div>
      </div>

      <!-- 2. Questions -->
      <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
            </div>
            <h3 class="text-[14px] font-bold text-slate-800">2. Questions</h3>
          </div>
          <div class="flex items-center gap-3">
            <!-- Fullscreen Icon -->
            <button @click="showQuestionsFullscreen = true" title="View all questions" class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
            </button>
            <button @click="emit('edit-step', 2)" class="flex items-center gap-1.5 text-[12px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
              Edit
            </button>
          </div>
        </div>

        <div class="flex flex-wrap gap-4 mb-6">
          <div class="flex-1 min-w-[120px] p-4 border border-slate-100 rounded-xl bg-slate-50 flex flex-col items-center justify-center">
            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-[#5138ed] mb-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <span class="text-[11px] font-bold text-slate-500 mb-0.5">Total Questions</span>
            <span class="text-[16px] font-black text-slate-800">{{ formStore.questions.length }}</span>
          </div>

          <div class="flex-1 min-w-[120px] p-4 border border-slate-100 rounded-xl bg-white flex flex-col justify-center">
            <template v-if="formStore.questions.length === 0">
              <span class="text-slate-400 text-center">No questions added yet.</span>
            </template>
            <template v-else>
              <ul class="text-[12px] text-slate-600 space-y-1">
                <li v-for="(q, idx) in formStore.questions.slice(0, 3)" :key="idx" class="truncate">
                  {{ idx + 1 }}. {{ q.text }}
                </li>
                <li v-if="formStore.questions.length > 3" class="text-indigo-500 font-bold">
                  + {{ formStore.questions.length - 3 }} more
                </li>
              </ul>
            </template>
          </div>
        </div>

        <div class="flex items-center text-[12px] font-bold text-slate-500">
          Total Marks: <span class="text-slate-800 ml-1">{{ formStore.totalMarks }}</span>
        </div>
      </div>

      <!-- 3. Exam Settings -->
      <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <h3 class="text-[14px] font-bold text-slate-800">3. Exam Settings</h3>
          </div>
          <button @click="emit('edit-step', 3)" class="flex items-center gap-1.5 text-[12px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            Edit
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="space-y-4">
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-[#5138ed] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Duration</span>
                <span class="text-[11px] font-medium text-slate-500">{{ formStore.durationMinutes }} minutes</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-[#5138ed] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Passing Marks</span>
                <span class="text-[11px] font-medium text-slate-500">{{ Math.round((formStore.passingMarks / formStore.totalMarks) * 100) || 0 }}%</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-[#5138ed] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Maximum Attempts</span>
                <span class="text-[11px] font-medium text-slate-500">1</span>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-[#5138ed] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Scheduled Time</span>
                <span class="text-[11px] font-medium text-slate-500">{{ formStore.scheduledDate }} {{ formStore.scheduledTime }}</span>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <div class="flex items-start gap-3">
              <svg v-if="formStore.shuffleQuestions" class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <svg v-else class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Shuffle Questions</span>
                <span class="text-[11px] font-medium text-slate-500">{{ formStore.shuffleQuestions ? 'Enabled' : 'Disabled' }}</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg v-if="formStore.shuffleAnswers" class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <svg v-else class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Shuffle Answer Options</span>
                <span class="text-[11px] font-medium text-slate-500">{{ formStore.shuffleAnswers ? 'Enabled' : 'Disabled' }}</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg v-if="formStore.showReviewScreen" class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <svg v-else class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Show Review Screen</span>
                <span class="text-[11px] font-medium text-slate-500">{{ formStore.showReviewScreen ? 'Enabled' : 'Disabled' }}</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg v-if="formStore.autoSubmitOnTimeFinish" class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <svg v-else class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Auto Submit on Time Finish</span>
                <span class="text-[11px] font-medium text-slate-500">{{ formStore.autoSubmitOnTimeFinish ? 'Enabled' : 'Disabled' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 4. Security Settings -->
      <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <h3 class="text-[14px] font-bold text-slate-800">4. Security Settings</h3>
          </div>
          <button @click="emit('edit-step', 3)" class="flex items-center gap-1.5 text-[12px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            Edit
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="space-y-4">
            <div class="flex items-start gap-3">
              <svg v-if="formStore.enableFullscreenMode" class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <svg v-else class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Fullscreen Mode</span>
                <span class="text-[11px] font-medium text-slate-500">{{ formStore.enableFullscreenMode ? 'Enabled' : 'Disabled' }}</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg v-if="!formStore.disableRightClick" class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <svg v-else class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Right Click</span>
                <span class="text-[11px] font-medium text-slate-500">{{ formStore.disableRightClick ? 'Disabled' : 'Enabled' }}</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg v-if="!formStore.disableCopyPaste" class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <svg v-else class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Copy & Paste</span>
                <span class="text-[11px] font-medium text-slate-500">{{ formStore.disableCopyPaste ? 'Disabled' : 'Enabled' }}</span>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <div class="flex items-start gap-3">
              <svg v-if="formStore.enableBrowserTabMonitoring" class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <svg v-else class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Browser Tab Monitoring</span>
                <span class="text-[11px] font-medium text-slate-500">{{ formStore.enableBrowserTabMonitoring ? 'Enabled' : 'Disabled' }}</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg v-if="formStore.allowCalculator" class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <svg v-else class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Calculator</span>
                <span class="text-[11px] font-medium text-slate-500">{{ formStore.allowCalculator ? 'Enabled' : 'Disabled' }}</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg v-if="formStore.webcamMonitoring" class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <svg v-else class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Webcam Monitoring</span>
                <span class="text-[11px] font-medium text-slate-500">{{ formStore.webcamMonitoring ? 'Enabled' : 'Disabled' }}</span>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Allow Backtracking</span>
                <span class="text-[11px] font-medium text-slate-500">Enabled</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">One Question at a Time</span>
                <span class="text-[11px] font-medium text-slate-500">Disabled</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between pt-2 pb-10">
      <button @click="emit('cancel')" class="px-6 py-2.5 border border-slate-200 text-slate-600 font-bold text-[13px] rounded-xl hover:bg-slate-50 transition-colors">
        Cancel
      </button>
      
      <div class="flex items-center gap-3">
        <button class="px-6 py-2.5 border border-slate-200 text-[#5138ed] font-bold text-[13px] rounded-xl hover:border-indigo-200 hover:bg-indigo-50 transition-colors">
          Save as Draft
        </button>
        <button @click="handlePublish" :disabled="isSubmitting" class="px-8 py-2.5 bg-[#5138ed] hover:bg-indigo-600 text-white font-bold text-[13px] rounded-xl shadow-sm transition-colors flex items-center gap-2 disabled:opacity-50">
          <span v-if="isSubmitting">Publishing...</span>
          <span v-else class="flex items-center gap-2">
            Publish Exam
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
          </span>
        </button>
      </div>
    </div>
  </div>

  <!-- Questions Fullscreen Modal -->
  <Teleport to="body">
    <Transition name="modal-fade">
      <div v-if="showQuestionsFullscreen" class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4" @keydown.escape="showQuestionsFullscreen = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl h-[90vh] flex flex-col overflow-hidden">

          <!-- Modal Header -->
          <div class="flex items-center justify-between px-8 py-5 border-b border-slate-100 shrink-0 bg-gradient-to-r from-indigo-50/60 to-white">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-[#5138ed] flex items-center justify-center text-white shadow-lg shadow-indigo-200/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
              </div>
              <div>
                <h2 class="text-[16px] font-bold text-slate-800">All Questions</h2>
                <p class="text-[12px] text-slate-500 mt-0.5">{{ formStore.questions.length }} question{{ formStore.questions.length !== 1 ? 's' : '' }} · {{ formStore.totalMarks }} total marks</p>
              </div>
            </div>
            <button @click="showQuestionsFullscreen = false" class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>

          <!-- Questions List -->
          <div class="overflow-y-auto flex-1 px-8 py-6 space-y-5">
            <div v-if="formStore.questions.length === 0" class="flex flex-col items-center justify-center h-full py-20 text-slate-400">
              <svg class="w-12 h-12 mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
              <p class="text-[14px] font-medium">No questions added yet.</p>
            </div>

            <div v-for="(q, idx) in formStore.questions" :key="idx" class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-indigo-100 transition-all">
              <!-- Question Header -->
              <div class="flex items-start gap-4 mb-4">
                <div class="w-7 h-7 rounded-lg bg-[#5138ed] text-white text-[11px] font-black flex items-center justify-center shrink-0 mt-0.5">{{ idx + 1 }}</div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <!-- Type badge -->
                    <span v-if="q.type === 'mcq' || q.type === 'multiple_choice'" class="px-2 py-0.5 rounded-md bg-indigo-50 text-[#5138ed] text-[10px] font-bold uppercase tracking-wide">MCQ</span>
                    <span v-else-if="q.type === 'true_false' || q.type === 'true/false'" class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-600 text-[10px] font-bold uppercase tracking-wide">True / False</span>
                    <span v-else-if="q.type === 'short_answer'" class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-wide">Short Answer</span>
                    <span v-else-if="q.type === 'essay'" class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-500 text-[10px] font-bold uppercase tracking-wide">Essay</span>
                    <span v-else class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 text-[10px] font-bold uppercase tracking-wide">{{ q.type }}</span>
                    <!-- Marks badge -->
                    <span class="px-2 py-0.5 rounded-md bg-slate-50 text-slate-500 text-[10px] font-bold border border-slate-100">{{ q.marks || q.points || 1 }} mark{{ (q.marks || q.points || 1) > 1 ? 's' : '' }}</span>
                  </div>
                  <p class="text-[14px] font-semibold text-slate-800 leading-snug">{{ q.text || q.question }}</p>
                </div>
              </div>

              <!-- MCQ Options -->
              <div v-if="(q.type === 'mcq' || q.type === 'multiple_choice') && (q.options || q.choices)" class="ml-11 space-y-2">
                <div v-for="(opt, oIdx) in (q.options || q.choices)" :key="oIdx"
                  :class="[
                    'flex items-center gap-3 px-4 py-2.5 rounded-xl border text-[13px] font-medium transition-colors',
                    (opt === q.correctAnswer || opt === q.correct_answer || oIdx === q.correctAnswerIndex || oIdx === q.correct_answer_index)
                      ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                      : 'bg-slate-50 border-slate-100 text-slate-600'
                  ]"
                >
                  <div :class="[
                    'w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0',
                    (opt === q.correctAnswer || opt === q.correct_answer || oIdx === q.correctAnswerIndex || oIdx === q.correct_answer_index)
                      ? 'border-emerald-500 bg-emerald-500'
                      : 'border-slate-300 bg-white'
                  ]">
                    <svg v-if="opt === q.correctAnswer || opt === q.correct_answer || oIdx === q.correctAnswerIndex || oIdx === q.correct_answer_index" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                  </div>
                  <span>{{ typeof opt === 'object' ? (opt.text || opt.label || opt.value) : opt }}</span>
                  <span v-if="opt === q.correctAnswer || opt === q.correct_answer || oIdx === q.correctAnswerIndex || oIdx === q.correct_answer_index" class="ml-auto text-[10px] font-bold text-emerald-600 bg-emerald-100 px-1.5 py-0.5 rounded">Correct</span>
                </div>
              </div>

              <!-- True / False Options -->
              <div v-else-if="q.type === 'true_false' || q.type === 'true/false'" class="ml-11 flex gap-3">
                <div :class="[
                  'flex items-center gap-2 px-4 py-2.5 rounded-xl border text-[13px] font-medium flex-1 justify-center',
                  (q.correctAnswer === true || q.correctAnswer === 'true' || q.correct_answer === true || q.correct_answer === 'true')
                    ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                    : 'bg-slate-50 border-slate-100 text-slate-600'
                ]">
                  <div :class="[
                    'w-5 h-5 rounded-full border-2 flex items-center justify-center',
                    (q.correctAnswer === true || q.correctAnswer === 'true' || q.correct_answer === true || q.correct_answer === 'true')
                      ? 'border-emerald-500 bg-emerald-500'
                      : 'border-slate-300 bg-white'
                  ]">
                    <svg v-if="q.correctAnswer === true || q.correctAnswer === 'true' || q.correct_answer === true || q.correct_answer === 'true'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                  </div>
                  True
                </div>
                <div :class="[
                  'flex items-center gap-2 px-4 py-2.5 rounded-xl border text-[13px] font-medium flex-1 justify-center',
                  (q.correctAnswer === false || q.correctAnswer === 'false' || q.correct_answer === false || q.correct_answer === 'false')
                    ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                    : 'bg-slate-50 border-slate-100 text-slate-600'
                ]">
                  <div :class="[
                    'w-5 h-5 rounded-full border-2 flex items-center justify-center',
                    (q.correctAnswer === false || q.correctAnswer === 'false' || q.correct_answer === false || q.correct_answer === 'false')
                      ? 'border-emerald-500 bg-emerald-500'
                      : 'border-slate-300 bg-white'
                  ]">
                    <svg v-if="q.correctAnswer === false || q.correctAnswer === 'false' || q.correct_answer === false || q.correct_answer === 'false'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                  </div>
                  False
                </div>
              </div>

              <!-- Short Answer / Essay hint -->
              <div v-else-if="q.type === 'short_answer' || q.type === 'essay'" class="ml-11">
                <div class="px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-[12px] text-slate-400 italic">
                  {{ q.type === 'essay' ? 'Open-ended essay response' : 'Short written answer' }}
                  <span v-if="q.sampleAnswer || q.sample_answer" class="block mt-1 not-italic text-slate-600 font-medium">Sample: {{ q.sampleAnswer || q.sample_answer }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="px-8 py-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0">
            <span class="text-[12px] text-slate-500 font-medium">{{ formStore.questions.length }} question{{ formStore.questions.length !== 1 ? 's' : '' }} · {{ formStore.totalMarks }} total marks</span>
            <button @click="showQuestionsFullscreen = false" class="px-5 py-2 bg-[#5138ed] text-white text-[13px] font-bold rounded-xl hover:bg-indigo-600 transition-colors shadow-sm">Close</button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

