<script setup lang="ts">
import { onMounted } from 'vue'
import { useInstructorStudentStore } from '../store/instructorStudentStore'

import StudentStats from '../components/students/StudentStats.vue'
import StudentDirectoryTable from '../components/students/StudentDirectoryTable.vue'
import PerformanceChartWidget from '../components/students/PerformanceChartWidget.vue'
import CourseDistributionWidget from '../components/students/CourseDistributionWidget.vue'
import StudentQuickActionsWidget from '../components/students/StudentQuickActionsWidget.vue'
import RecentRegistrationsWidget from '../components/students/RecentRegistrationsWidget.vue'

const studentStore = useInstructorStudentStore()

onMounted(() => {
  studentStore.fetchStudents()
})
</script>

<template>
  <div class="max-w-[1500px] mx-auto">

    <!-- Dev Banner -->
    <div
      v-if="studentStore.usingMockData"
      class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-sm font-medium flex items-center gap-2 mb-6"
    >
      <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span><strong>Dev Mode:</strong> Backend is offline — showing mock data.</span>
    </div>

    <!-- Error Banner -->
    <div v-if="studentStore.error" class="bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3 text-sm font-medium flex items-center gap-2 mb-6">
      <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
      {{ studentStore.error }}
    </div>
    
    <!-- Main Content Grid -->
    <div class="flex flex-col xl:flex-row gap-6">
      
      <!-- Left Column (Stats + Table) -->
      <div class="flex-1 min-w-0">
        <StudentStats />
        <StudentDirectoryTable />
      </div>

      <!-- Right Column (Sidebar Widgets) -->
      <div class="w-full xl:w-[320px] space-y-6 mt-[72px]">
        <PerformanceChartWidget />
        <CourseDistributionWidget />
        <StudentQuickActionsWidget />
        <RecentRegistrationsWidget />
      </div>

    </div>
  </div>
</template>
