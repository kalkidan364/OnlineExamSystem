<script setup lang="ts">
import { computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'

import ExamStepper from '../components/create-exam/ExamStepper.vue'
import ExamInformationForm from '../components/create-exam/ExamInformationForm.vue'
import AddQuestionsSidebar from '../components/create-exam/AddQuestionsSidebar.vue'
import ExamTipsSidebar from '../components/create-exam/ExamTipsSidebar.vue'
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

const currentStep = computed({
  get: () => Number(route.query.step) || 1,
  set: (val) => router.push({ query: { ...route.query, step: val } })
})

const nextStep = () => {
  if (currentStep.value < 4) {
    currentStep.value++
  }
}
</script>

<template>
  <div class="max-w-[1400px] mx-auto">
    
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
            <button class="px-6 py-2.5 border border-slate-200 text-slate-600 font-bold text-[13px] rounded-xl hover:bg-slate-50 transition-colors">
              Cancel
            </button>
            
            <div class="flex items-center gap-3">
              <button class="px-6 py-2.5 border border-slate-200 text-[#5138ed] font-bold text-[13px] rounded-xl hover:border-indigo-200 hover:bg-indigo-50 transition-colors">
                Save as Draft
              </button>
              <button @click="nextStep" class="px-6 py-2.5 bg-[#5138ed] hover:bg-indigo-600 text-white font-bold text-[13px] rounded-xl shadow-sm transition-colors flex items-center gap-2">
                Next: Add Questions
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
              </button>
            </div>
          </div>
        </template>

        <!-- STEP 2: Add Questions -->
        <template v-else-if="currentStep === 2">
          <AddQuestionForm @cancel="currentStep = 1" @next="nextStep" />
        </template>

        <!-- STEP 3: Exam Settings -->
        <template v-else-if="currentStep === 3">
          <ExamSettingsForm @cancel="currentStep = 2" @next="nextStep" />
        </template>

        <!-- STEP 4: Review & Publish -->
        <template v-else-if="currentStep === 4">
          <ReviewPublishForm @cancel="currentStep = 3" @edit-step="(step) => currentStep = step" />
        </template>

      </div>

      <!-- Right Column (Sidebar Widgets) -->
      <div class="w-full xl:w-[320px] pt-4 xl:pt-[84px]">
        <template v-if="currentStep === 1">
          <AddQuestionsSidebar />
          <ExamTipsSidebar />
          <ExamHelpSidebar />
        </template>
        <template v-else-if="currentStep === 2">
          <QuestionTypesSidebar />
          <QuestionTipsSidebar />
          <QuickActionsSidebar />
        </template>
        <template v-else-if="currentStep === 3">
          <SettingsOverviewSidebar />
          <SettingsTipsSidebar />
          <SettingsHelpSidebar />
        </template>
        <template v-else-if="currentStep === 4">
          <ReviewSummarySidebar />
          <ReadyPublishSidebar />
          <WhatHappensNextSidebar />
          <ExamHelpSidebar class="mt-6" />
        </template>
      </div>

    </div>
  </div>
</template>
