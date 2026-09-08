<script setup lang="ts">
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useCreateExamStore } from '../store/createExamStore'
import { useInstructorExamStore } from '../store/instructorExamStore'

import ExamStepper from '../components/create-exam/ExamStepper.vue'
import ExamInformationForm from '../components/create-exam/ExamInformationForm.vue'
import ExamHelpSidebar from '../components/create-exam/ExamHelpSidebar.vue'

// Step 2 Components
import AddQuestionForm from '../components/create-exam/AddQuestionForm.vue'
import QuestionTypesSidebar from '../components/create-exam/QuestionTypesSidebar.vue'
import QuestionTipsSidebar from '../components/create-exam/QuestionTipsSidebar.vue'
import QuickActionsSidebar from '../components/create-exam/QuickActionsSidebar.vue'

// Step 3 Components
import ExamSettingsForm from '../components/create-exam/ExamSettingsForm.vue'
import SettingsOverviewSidebar from '../components/create-exam/SettingsOverviewSidebar.vue'
import SettingsTipsSidebar from '../components/create-exam/SettingsTipsSidebar.vue'
import SettingsHelpSidebar from '../components/create-exam/SettingsHelpSidebar.vue'

// Step 4 Components
import ReviewPublishForm from '../components/create-exam/ReviewPublishForm.vue'
import ReviewSummarySidebar from '../components/create-exam/ReviewSummarySidebar.vue'
import ReadyPublishSidebar from '../components/create-exam/ReadyPublishSidebar.vue'
import WhatHappensNextSidebar from '../components/create-exam/WhatHappensNextSidebar.vue'

const router = useRouter()
const route = useRoute()
const formStore = useCreateExamStore()
const examStore = useInstructorExamStore()

const currentStep = computed({
  get: () => Number(route.query.step) || 1,
  set: (val) => router.push({ query: { ...route.query, step: val } })
})

// Reset the store every time we enter the Create Exam page
// so leftover data from a previously created exam never leaks into a new session
onMounted(() => {
  formStore.reset()
  // Also make sure step resets to 1
  if (route.query.step && Number(route.query.step) !== 1) {
    router.replace({ query: { step: 1 } })
  }
})

// Also reset when navigating away so the store stays clean
onBeforeUnmount(() => {
  formStore.reset()
})

const nextStep = () => {
  if (currentStep.value === 1) {
    if (!formStore.title || !formStore.examType || !formStore.totalMarks) {
      // Optional: replace with a toast/notification system if available
      alert('Please fill in all required fields: Exam Title, Exam Type, and Total Marks.')
      return
    }
  }
  if (currentStep.value < 4) {
    currentStep.value++
  }
}

const isSavingDraft = ref(false)
const saveAsDraft = async () => {
  isSavingDraft.value = true
  try {
    await examStore.createExam({
      title: formStore.title || 'Untitled Exam',
      course_code: formStore.courseCode,
      course_name: formStore.examType,
      section: formStore.section,
      duration_minutes: formStore.durationMinutes,
      total_marks: formStore.totalMarks,
      status: 'draft',
      scheduled_at: formStore.getScheduledAt(),
      settings: formStore.getSettingsPayload()
    })
    formStore.reset()
    router.push('/instructor/exams')
  } catch (err) {
    console.error('Failed to save draft:', err)
  } finally {
    isSavingDraft.value = false
  }
}
</script>

<template>
  <div class="max-w-[1400px] mx-auto">
    
    <div class="flex justify-end mb-4">
      <router-link to="/instructor/exams" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-bold text-[13px] rounded-xl hover:bg-slate-50 transition-colors shadow-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Exams
      </router-link>
    </div>

    <!-- Main Content Area -->
    <div class="flex flex-col xl:flex-row gap-6">
      
      <!-- Left Column (Form) -->
      <div class="flex-1 min-w-0">
        <ExamStepper :currentStep="currentStep" />
        
        <!-- STEP 1: Exam Information -->
        <template v-if="currentStep === 1">
          <ExamInformationForm />

          <!-- Action Buttons -->
          <div class="flex items-center justify-between pt-2 pb-10">
            <button @click="router.push('/instructor/exams')" class="px-6 py-2.5 border border-slate-200 text-slate-600 font-bold text-[13px] rounded-xl hover:bg-slate-50 transition-colors">
              Cancel
            </button>
            
            <div class="flex items-center gap-3">
              <button @click="nextStep" class="px-6 py-2.5 bg-[#5138ed] hover:bg-indigo-600 text-white font-bold text-[13px] rounded-xl shadow-sm transition-colors flex items-center gap-2">
                Next: Add Questions
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
              </button>
            </div>
          </div>
        </template>

        <!-- STEP 2: Add Questions -->
        <template v-else-if="currentStep === 2">
          <AddQuestionForm @cancel="router.push('/instructor/exams')" @next="nextStep" @prev="currentStep = 1" @save-draft="saveAsDraft" :isSaving="isSavingDraft" />
        </template>

        <!-- STEP 3: Exam Settings -->
        <template v-else-if="currentStep === 3">
          <ExamSettingsForm @cancel="router.push('/instructor/exams')" @next="nextStep" @prev="currentStep = 2" @save-draft="saveAsDraft" :isSaving="isSavingDraft" />
        </template>

        <!-- STEP 4: Review & Publish -->
        <template v-else-if="currentStep === 4">
          <ReviewPublishForm @edit-step="(step) => currentStep = step" @cancel="router.push('/instructor/exams')" @save-draft="saveAsDraft" :isSaving="isSavingDraft" />
        </template>

      </div>

      <!-- Right Column (Sidebar Widgets) -->
      <div v-if="currentStep === 3" class="w-full xl:w-[320px] pt-4 xl:pt-[84px]">
        <SettingsOverviewSidebar />
        <SettingsTipsSidebar />
        <SettingsHelpSidebar />
      </div>

    </div>
  </div>
</template>
