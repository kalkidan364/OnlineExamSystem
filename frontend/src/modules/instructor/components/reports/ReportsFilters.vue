<template>
  <div class="flex flex-wrap items-center justify-between gap-4 mb-2">
    
    <div class="flex flex-wrap items-center gap-3">
      <!-- Date Range -->
      <div class="relative min-w-[200px]">
        <div class="w-full pl-3 pr-8 py-2 text-[11px] border border-slate-200 rounded-xl text-slate-700 bg-white cursor-pointer font-medium flex items-center justify-between">
          <span>May 01, 2025 - May 25, 2025</span>
          <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
      </div>

      <!-- Exams Dropdown -->
      <div class="relative min-w-[130px]">
        <select class="w-full appearance-none pl-3 pr-8 py-2 text-[11px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] text-slate-700 bg-white cursor-pointer font-medium">
          <option>All Exams</option>
        </select>
        <svg class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
      </div>

      <!-- Courses Dropdown -->
      <div class="relative min-w-[130px]">
        <select class="w-full appearance-none pl-3 pr-8 py-2 text-[11px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] text-slate-700 bg-white cursor-pointer font-medium">
          <option>All Courses</option>
        </select>
        <svg class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
      </div>

      <!-- Exam Types Dropdown -->
      <div class="relative min-w-[140px]">
        <select class="w-full appearance-none pl-3 pr-8 py-2 text-[11px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] text-slate-700 bg-white cursor-pointer font-medium">
          <option>All Exam Types</option>
        </select>
        <svg class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
      </div>

      <button class="px-4 py-2 border border-[#5138ed] text-[#5138ed] font-bold text-[11px] rounded-xl hover:bg-indigo-50 transition-colors flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
        Filter
      </button>
    </div>

    <!-- Data Refresh Text -->
    <div class="flex items-center gap-2 text-slate-400 cursor-pointer hover:text-[#5138ed] transition-colors">
      <span class="text-[10px] font-medium">Data as of May 25, 2025 10:30 AM</span>
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
    </div>

  </div>
</template>
