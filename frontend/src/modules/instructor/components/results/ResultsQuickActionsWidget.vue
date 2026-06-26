<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <h2 class="text-[14px] font-bold text-slate-800 mb-5">Quick Actions</h2>
    
    <div class="grid grid-cols-2 gap-3">
      
      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group text-center">
        <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        <span class="text-[10px] font-bold text-slate-600 group-hover:text-[#5138ed] leading-tight">View Detailed Reports</span>
      </button>

      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group text-center">
        <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        <span class="text-[10px] font-bold text-slate-600 group-hover:text-[#5138ed] leading-tight">Export Results</span>
      </button>

      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group text-center">
        <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
        <span class="text-[10px] font-bold text-slate-600 group-hover:text-[#5138ed] leading-tight">Grade Analysis</span>
      </button>

      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group text-center">
        <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <span class="text-[10px] font-bold text-slate-600 group-hover:text-[#5138ed] leading-tight">Performance Report</span>
      </button>

    </div>
  </div>
</template>
