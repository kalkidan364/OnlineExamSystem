<script setup lang="ts">
import { onMounted } from 'vue'
import { useInstructorExamStore } from '../store/instructorExamStore'

import ExamStatCards from '../components/exam/ExamStatCards.vue'
import ExamTabs from '../components/exam/ExamTabs.vue'
import ExamTable from '../components/exam/ExamTable.vue'
import ExamCalendar from '../components/exam/ExamCalendar.vue'
import ExamOverviewChart from '../components/exam/ExamOverviewChart.vue'
import ExamQuickActions from '../components/exam/ExamQuickActions.vue'

const examStore = useInstructorExamStore()

onMounted(() => {
  examStore.fetchExams()
})
</script>

<template>
  <div class="max-w-[1400px] mx-auto flex flex-col xl:flex-row gap-6">
    
    <!-- Main Left Column -->
    <div class="flex-1 min-w-0 space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
        <div>
          <h1 class="text-2xl font-bold text-slate-800">Exams</h1>
          <p class="text-[14px] text-slate-500 mt-1">Create, manage and schedule exams</p>
        </div>
        
        <!-- Global Search Placeholder from image -->
        <div class="relative hidden md:block w-72">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input 
            type="text" 
            class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors"
            placeholder="Search exams, courses..."
          >
        </div>
      </div>

      <!-- Dev Banner: Mock Data Active -->
      <div
        v-if="examStore.usingMockData"
        class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-sm font-medium flex items-center gap-2"
      >
        <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>
          <strong>Dev Mode:</strong> Backend is offline — showing mock data.
        </span>
      </div>

      <!-- Error Banner: Real API Error -->
      <div v-if="examStore.error" class="bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3 text-sm font-medium flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        {{ examStore.error }}
      </div>

      <!-- Stat Cards -->
      <ExamStatCards />

      <!-- Tabs & Table Wrapper -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 pt-2">
          <ExamTabs />
        </div>
        <!-- Main Table Area (We remove the inner border/shadow from ExamTable since it's wrapped) -->
        <div class="p-0">
          <ExamTable class="border-0 shadow-none rounded-none" />
        </div>
      </div>

    </div>

    <!-- Right Sidebar Column -->
    <div class="w-full xl:w-[320px] space-y-6">
      
      <!-- Top Action Buttons -->
      <div class="flex items-center gap-3">
        <router-link to="/instructor/exams/create" class="flex-1 flex items-center justify-center gap-2 bg-[#5138ed] hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold text-[13px] shadow-sm transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Create New Exam
        </router-link>
        <button class="w-11 h-11 flex items-center justify-center border border-slate-200 text-slate-500 hover:text-[#5138ed] hover:border-indigo-200 hover:bg-indigo-50 rounded-xl transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        </button>
      </div>
      
      <!-- Exam Calendar -->
      <ExamCalendar />

      <!-- Overview Chart -->
      <ExamOverviewChart />
      
      <!-- Quick Actions -->
      <ExamQuickActions />

    </div>

  </div>
</template>
