<script setup lang="ts">
import QBStatCards from '../components/question-bank/QBStatCards.vue'
import QBTable from '../components/question-bank/QBTable.vue'
import QBOverviewChart from '../components/question-bank/QBOverviewChart.vue'
import QBTopCourses from '../components/question-bank/QBTopCourses.vue'
import QBQuickActions from '../components/question-bank/QBQuickActions.vue'
</script>

<template>
  <div class="max-w-[1400px] mx-auto flex flex-col xl:flex-row gap-6">
    
    <!-- Main Left Column -->
    <div class="flex-1 min-w-0 space-y-6">
      
      <!-- Page Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
        <div>
          <h1 class="text-2xl font-bold text-slate-800">Manage your question banks</h1>
          <p class="text-[14px] text-slate-500 mt-1">Create, organize and manage questions for your exams.</p>
        </div>
        <button class="flex items-center gap-2 bg-[#5138ed] hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition-colors w-fit">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Create Question Bank
        </button>
      </div>

      <!-- Stat Cards -->
      <QBStatCards />

      <!-- Main Table Area -->
      <QBTable />

    </div>

    <!-- Right Sidebar Column -->
    <div class="w-full xl:w-[320px] space-y-6">
      
      <!-- Overview Chart -->
      <QBOverviewChart />
      
      <!-- Top Courses -->
      <QBTopCourses />
      
      <!-- Quick Actions -->
      <QBQuickActions />

    </div>

  </div>
</template>
