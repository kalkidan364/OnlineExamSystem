<script setup lang="ts">
import ResultsStats from '../components/results/ResultsStats.vue'
import ResultsTable from '../components/results/ResultsTable.vue'
import OverallPerformanceWidget from '../components/results/OverallPerformanceWidget.vue'
import PerformanceSummaryWidget from '../components/results/PerformanceSummaryWidget.vue'
import ResultsQuickActionsWidget from '../components/results/ResultsQuickActionsWidget.vue'
</script>

<template>
  <div class="max-w-[1500px] mx-auto">
    
    <!-- Main Content Grid -->
    <div class="flex flex-col xl:flex-row gap-6">
      
      <!-- Left Column (Stats + Table) -->
      <div class="flex-1 min-w-0">
        <ResultsStats />
        <ResultsTable />
      </div>

      <!-- Right Column (Sidebar Widgets) -->
      <div class="w-full xl:w-[320px] space-y-6">
        <OverallPerformanceWidget />
        <PerformanceSummaryWidget />
        <ResultsQuickActionsWidget />
      </div>

    </div>
  </div>
</template>
