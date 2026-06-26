<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <h2 class="text-[14px] font-bold text-slate-800 mb-5">Performance Summary</h2>
    
    <div class="space-y-4">
      
      <!-- Average Score -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-6 h-6 rounded-md bg-indigo-50 flex items-center justify-center text-[#5138ed]">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
          </div>
          <span class="text-[12px] font-bold text-slate-600">Average Score</span>
        </div>
        <span class="text-[13px] font-black text-slate-800">74.6%</span>
      </div>

      <!-- Highest Score -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-6 h-6 rounded-md bg-indigo-50 flex items-center justify-center text-[#5138ed]">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
          </div>
          <span class="text-[12px] font-bold text-slate-600">Highest Score</span>
        </div>
        <span class="text-[13px] font-black text-slate-800">96.0%</span>
      </div>

      <!-- Lowest Score -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-6 h-6 rounded-md bg-rose-50 flex items-center justify-center text-rose-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
          </div>
          <span class="text-[12px] font-bold text-slate-600">Lowest Score</span>
        </div>
        <span class="text-[13px] font-black text-slate-800">22.0%</span>
      </div>

      <!-- Standard Deviation -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-6 h-6 rounded-md bg-orange-50 flex items-center justify-center text-orange-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
          </div>
          <span class="text-[12px] font-bold text-slate-600">Standard Deviation</span>
        </div>
        <span class="text-[13px] font-black text-slate-800">12.8%</span>
      </div>

      <!-- Pass Rate -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-6 h-6 rounded-md bg-emerald-50 flex items-center justify-center text-emerald-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <span class="text-[12px] font-bold text-slate-600">Pass Rate</span>
        </div>
        <span class="text-[13px] font-black text-slate-800">68.4%</span>
      </div>

      <!-- Fail Rate -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-6 h-6 rounded-md bg-rose-50 flex items-center justify-center text-rose-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <span class="text-[12px] font-bold text-slate-600">Fail Rate</span>
        </div>
        <span class="text-[13px] font-black text-slate-800">31.6%</span>
      </div>

    </div>
  </div>
</template>
