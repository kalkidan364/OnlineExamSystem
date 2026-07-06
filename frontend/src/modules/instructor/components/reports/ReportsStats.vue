<script setup lang="ts">
import { useInstructorReportStore } from '../../store/instructorReportStore'

const reportStore = useInstructorReportStore()
</script>

<template>
  <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
    
    <!-- Total Exams -->
    <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-[#5138ed] flex-shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
      </div>
      <div>
        <span class="block text-[10px] font-bold text-slate-500 mb-0.5">Total Exams</span>
        <div v-if="reportStore.isLoading" class="h-5 w-10 bg-slate-100 animate-pulse rounded"></div>
        <span v-else class="block text-[20px] font-black text-slate-800 leading-none mb-1">{{ reportStore.stats.total_exams }}</span>
        <div class="flex items-center gap-1">
          <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
          <span class="text-[9px] font-bold text-emerald-500">12.5%</span>
          <span class="text-[9px] text-slate-400 font-medium whitespace-nowrap">vs Apr 01 - Apr 30</span>
        </div>
      </div>
    </div>

    <!-- Total Students -->
    <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 flex-shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
      </div>
      <div>
        <span class="block text-[10px] font-bold text-slate-500 mb-0.5">Total Students</span>
        <div v-if="reportStore.isLoading" class="h-5 w-10 bg-slate-100 animate-pulse rounded"></div>
        <span v-else class="block text-[20px] font-black text-slate-800 leading-none mb-1">{{ reportStore.stats.total_students }}</span>
        <div class="flex items-center gap-1">
          <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
          <span class="text-[9px] font-bold text-emerald-500">8.3%</span>
          <span class="text-[9px] text-slate-400 font-medium whitespace-nowrap">vs Apr 01 - Apr 30</span>
        </div>
      </div>
    </div>

    <!-- Average Score -->
    <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 flex-shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>
      <div>
        <span class="block text-[10px] font-bold text-slate-500 mb-0.5">Average Score</span>
        <div v-if="reportStore.isLoading" class="h-5 w-10 bg-slate-100 animate-pulse rounded"></div>
        <span v-else class="block text-[20px] font-black text-slate-800 leading-none mb-1">{{ reportStore.stats.average_score }}%</span>
        <div class="flex items-center gap-1">
          <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
          <span class="text-[9px] font-bold text-emerald-500">5.7%</span>
          <span class="text-[9px] text-slate-400 font-medium whitespace-nowrap">vs Apr 01 - Apr 30</span>
        </div>
      </div>
    </div>

    <!-- Pass Rate -->
    <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 flex-shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
      </div>
      <div>
        <span class="block text-[10px] font-bold text-slate-500 mb-0.5">Pass Rate</span>
        <div v-if="reportStore.isLoading" class="h-5 w-10 bg-slate-100 animate-pulse rounded"></div>
        <span v-else class="block text-[20px] font-black text-slate-800 leading-none mb-1">{{ reportStore.stats.pass_rate }}%</span>
        <div class="flex items-center gap-1">
          <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
          <span class="text-[9px] font-bold text-emerald-500">6.4%</span>
          <span class="text-[9px] text-slate-400 font-medium whitespace-nowrap">vs Apr 01 - Apr 30</span>
        </div>
      </div>
    </div>

    <!-- Fail Rate -->
    <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500 flex-shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>
      <div>
        <span class="block text-[10px] font-bold text-slate-500 mb-0.5">Fail Rate</span>
        <div v-if="reportStore.isLoading" class="h-5 w-10 bg-slate-100 animate-pulse rounded"></div>
        <span v-else class="block text-[20px] font-black text-slate-800 leading-none mb-1">{{ reportStore.stats.fail_rate }}%</span>
        <div class="flex items-center gap-1">
          <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
          <span class="text-[9px] font-bold text-rose-500">-6.4%</span>
          <span class="text-[9px] text-slate-400 font-medium">vs Apr 01 - Apr 30</span>
        </div>
      </div>
    </div>

  </div>
</template>
