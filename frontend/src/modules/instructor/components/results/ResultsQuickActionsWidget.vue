<template>
  <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-4">
    <h2 class="text-[12px] font-bold text-slate-800 mb-3">Quick Actions</h2>
    
    <div class="space-y-3">
      
      <!-- Continue Grading -->
      <button class="w-full flex items-center justify-between group">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-lg bg-indigo-50 text-[#5138ed] flex items-center justify-center group-hover:bg-[#5138ed] group-hover:text-white transition-colors shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
          </div>
          <div class="flex flex-col items-start">
            <span class="text-[11px] font-bold text-slate-800 group-hover:text-[#5138ed] transition-colors">Continue Grading</span>
            <span class="text-[10px] text-slate-500">Pick up where you left off</span>
          </div>
        </div>
        <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-[#5138ed] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
      </button>

      <!-- View Pending Students -->
      <button class="w-full flex items-center justify-between group">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-colors shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
          </div>
          <div class="flex flex-col items-start">
            <span class="text-[11px] font-bold text-slate-800 group-hover:text-emerald-500 transition-colors">View Pending Students</span>
            <span class="text-[10px] text-slate-500">See who hasn't submitted</span>
          </div>
        </div>
        <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
      </button>

      <!-- Publish Results -->
      <button class="w-full flex items-center justify-between group">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-lg bg-orange-50 text-orange-500 flex items-center justify-center group-hover:bg-orange-500 group-hover:text-white transition-colors shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
          </div>
          <div class="flex flex-col items-start">
            <span class="text-[11px] font-bold text-slate-800 group-hover:text-orange-500 transition-colors">Publish Results</span>
            <span class="text-[10px] text-slate-500">Make grades visible to students</span>
          </div>
        </div>
        <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
      </button>

      <!-- Export Results -->
      <button class="w-full flex items-center justify-between group">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white transition-colors shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
          </div>
          <div class="flex flex-col items-start">
            <span class="text-[11px] font-bold text-slate-800 group-hover:text-blue-500 transition-colors">Export Results</span>
            <span class="text-[10px] text-slate-500">Download as CSV or Excel</span>
          </div>
        </div>
        <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
      </button>

      <!-- Print Result Summary -->
      <button class="w-full flex items-center justify-between group">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center group-hover:bg-rose-500 group-hover:text-white transition-colors shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
          </div>
          <div class="flex flex-col items-start">
            <span class="text-[11px] font-bold text-slate-800 group-hover:text-rose-500 transition-colors">Print Result Summary</span>
            <span class="text-[10px] text-slate-500">Generate a printable PDF report</span>
          </div>
        </div>
        <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-rose-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
      </button>

    </div>
  </div>
</template>
