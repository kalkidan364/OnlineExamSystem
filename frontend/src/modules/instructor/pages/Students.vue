<script setup lang="ts">
import StudentStats from '../components/students/StudentStats.vue'
import StudentDirectoryTable from '../components/students/StudentDirectoryTable.vue'
import PerformanceChartWidget from '../components/students/PerformanceChartWidget.vue'
import CourseDistributionWidget from '../components/students/CourseDistributionWidget.vue'
import StudentQuickActionsWidget from '../components/students/StudentQuickActionsWidget.vue'
import RecentRegistrationsWidget from '../components/students/RecentRegistrationsWidget.vue'
</script>

<template>
  <div class="max-w-[1500px] mx-auto">
    
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
