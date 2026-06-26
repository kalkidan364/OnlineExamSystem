<script setup lang="ts">
import ReportsTabs from '../components/reports/ReportsTabs.vue'
import ReportsFilters from '../components/reports/ReportsFilters.vue'
import ReportsStats from '../components/reports/ReportsStats.vue'
import ReportsCharts from '../components/reports/ReportsCharts.vue'
import ReportsExamSummary from '../components/reports/ReportsExamSummary.vue'
import TopPerformingExamsWidget from '../components/reports/TopPerformingExamsWidget.vue'
import LowestPerformingExamsWidget from '../components/reports/LowestPerformingExamsWidget.vue'
import ReportsQuickActionsWidget from '../components/reports/ReportsQuickActionsWidget.vue'
</script>

<template>
  <div class="max-w-[1500px] mx-auto pb-8">
    
    <!-- Top Navigation Tabs (Full bleed) -->
    <div class="-mx-8 -mt-8 mb-6">
      <ReportsTabs />
    </div>
    
    <div class="space-y-6">
      <!-- Full Width Top Section -->
      <ReportsFilters />
      <ReportsStats />

      <!-- Main Content Grid -->
      <div class="flex flex-col lg:flex-row gap-6">
        
        <!-- Left Column -->
        <div class="flex-1 min-w-0 space-y-6">
          <ReportsCharts />
          <ReportsExamSummary />
        </div>

        <!-- Right Column -->
        <div class="w-full lg:w-[280px] xl:w-[320px] space-y-6">
          <TopPerformingExamsWidget />
          <LowestPerformingExamsWidget />
          <ReportsQuickActionsWidget />
        </div>

      </div>
    </div>
  </div>
</template>
