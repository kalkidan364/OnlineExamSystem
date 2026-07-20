<script setup lang="ts">
import { useInstructorResultStore } from '../../store/instructorResultStore'

const resultStore = useInstructorResultStore()
</script>

<template>
  <div class="mb-6">
    <!-- 4 Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
      
      <!-- Completed Exams -->
      <div class="bg-white border border-slate-100 rounded-2xl p-3 shadow-sm flex items-center gap-2.5 min-w-[150px]">
        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 flex-shrink-0">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="min-w-0">
          <span class="block text-[9px] font-bold text-slate-500 mb-0.5 truncate">Completed Exams</span>
          <div v-if="resultStore.isLoading" class="h-5 w-10 bg-slate-100 animate-pulse rounded"></div>
          <span v-else class="block text-[18px] font-black text-slate-800 leading-none mb-0.5">{{ resultStore.stats.completed_exams }}</span>
          <span class="text-[8px] text-slate-400 font-medium truncate block">Exams completed</span>
        </div>
      </div>

      <!-- Pending Manual Grading -->
      <div class="bg-white border border-slate-100 rounded-2xl p-3 shadow-sm flex items-center gap-2.5 min-w-[150px]">
        <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 flex-shrink-0">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        </div>
        <div class="min-w-0">
          <span class="block text-[9px] font-bold text-slate-500 mb-0.5 truncate">Pending Grading</span>
          <div v-if="resultStore.isLoading" class="h-5 w-10 bg-slate-100 animate-pulse rounded"></div>
          <span v-else class="block text-[18px] font-black text-slate-800 leading-none mb-0.5">{{ resultStore.stats.pending_manual_grading }}</span>
          <span class="text-[8px] text-slate-400 font-medium truncate block">Students to grade</span>
        </div>
      </div>

      <!-- Published Results -->
      <div class="bg-white border border-slate-100 rounded-2xl p-3 shadow-sm flex items-center gap-2.5 min-w-[150px]">
        <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500 flex-shrink-0">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
        </div>
        <div class="min-w-0">
          <span class="block text-[9px] font-bold text-slate-500 mb-0.5 truncate">Results Published</span>
          <div v-if="resultStore.isLoading" class="h-5 w-10 bg-slate-100 animate-pulse rounded"></div>
          <span v-else class="block text-[18px] font-black text-slate-800 leading-none mb-0.5">{{ resultStore.stats.published_results }}</span>
          <span class="text-[8px] text-slate-400 font-medium truncate block">Results published</span>
        </div>
      </div>

      <!-- Average Score -->
      <div class="bg-white border border-slate-100 rounded-2xl p-3 shadow-sm flex items-center gap-2.5 min-w-[150px]">
        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 flex-shrink-0">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
        </div>
        <div class="min-w-0">
          <span class="block text-[9px] font-bold text-slate-500 mb-0.5 truncate">Average Score</span>
          <div v-if="resultStore.isLoading" class="h-5 w-10 bg-slate-100 animate-pulse rounded"></div>
          <span v-else class="block text-[18px] font-black text-slate-800 leading-none mb-0.5">{{ resultStore.stats.average_score }}%</span>
          <span class="text-[8px] text-slate-400 font-medium truncate block">Across all exams</span>
        </div>
      </div>

    </div>
  </div>
</template>
