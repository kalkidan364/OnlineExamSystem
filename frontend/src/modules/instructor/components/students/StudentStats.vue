<script setup lang="ts">
import { useInstructorStudentStore } from '../../store/instructorStudentStore'

const studentStore = useInstructorStudentStore()
</script>

<template>
  <div class="mb-6">
    
    <!-- Welcome Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h2 class="text-[18px] font-bold text-slate-800 flex items-center gap-2">
          Welcome back, Dr. Abebe Kebede <span class="text-[20px]">👋</span>
        </h2>
        <p class="text-[13px] text-slate-500 mt-1">Here's an overview of your students and their performance.</p>
      </div>
      
      <div class="flex items-center gap-3">
        <button class="px-5 py-2.5 bg-[#5138ed] hover:bg-indigo-600 text-white font-bold text-[13px] rounded-xl shadow-sm transition-colors flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Add New Student
        </button>
        <button class="px-5 py-2.5 border border-slate-200 text-slate-700 font-bold text-[13px] rounded-xl hover:bg-slate-50 transition-colors flex items-center gap-2 bg-white">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
          Import Students
        </button>
      </div>
    </div>

    <!-- 5 Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
      
      <!-- Total Students -->
      <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-[#5138ed] flex-shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <div>
          <span class="block text-[10px] font-bold text-slate-500 mb-0.5">Total Students</span>
          <div v-if="studentStore.isLoading" class="h-5 w-10 bg-slate-100 animate-pulse rounded"></div>
          <span v-else class="block text-[20px] font-black text-slate-800 leading-none mb-1">{{ studentStore.stats.total_students }}</span>
          <span class="text-[9px] text-slate-400 font-medium">In your course</span>
        </div>
      </div>

      <!-- Active Students -->
      <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 flex-shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
        </div>
        <div>
          <span class="block text-[10px] font-bold text-slate-500 mb-0.5">Active Students</span>
          <div v-if="studentStore.isLoading" class="h-5 w-10 bg-slate-100 animate-pulse rounded"></div>
          <span v-else class="block text-[20px] font-black text-slate-800 leading-none mb-1">{{ studentStore.stats.active_students }}</span>
          <span class="text-[9px] text-slate-400 font-medium">{{ studentStore.stats.total_students > 0 ? Math.round((studentStore.stats.active_students / studentStore.stats.total_students) * 100) : 0 }}% active</span>
        </div>
      </div>

      <!-- New This Month -->
      <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 flex-shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <div>
          <span class="block text-[10px] font-bold text-slate-500 mb-0.5">New This Month</span>
          <span class="block text-[20px] font-black text-slate-800 leading-none mb-1">—</span>
          <span class="text-[9px] text-slate-400 font-medium">Joined this month</span>
        </div>
      </div>

      <!-- Average Score -->
      <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 flex-shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
        </div>
        <div>
          <span class="block text-[10px] font-bold text-slate-500 mb-0.5">Average Score</span>
          <div v-if="studentStore.isLoading" class="h-5 w-10 bg-slate-100 animate-pulse rounded"></div>
          <span v-else class="block text-[20px] font-black text-slate-800 leading-none mb-1">{{ studentStore.stats.average_score }}%</span>
          <span class="text-[9px] text-slate-400 font-medium">Overall average</span>
        </div>
      </div>

      <!-- Top Performers -->
      <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-500 flex-shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
        </div>
        <div>
          <span class="block text-[10px] font-bold text-slate-500 mb-0.5">Top Performers</span>
          <div v-if="studentStore.isLoading" class="h-5 w-10 bg-slate-100 animate-pulse rounded"></div>
          <span v-else class="block text-[20px] font-black text-slate-800 leading-none mb-1">{{ studentStore.stats.top_performers }}</span>
          <span class="text-[9px] text-slate-400 font-medium">Above 85% average</span>
        </div>
      </div>

    </div>
  </div>
</template>
