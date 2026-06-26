<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <h2 class="text-[14px] font-bold text-slate-800 mb-5">Quick Actions</h2>
    
    <div class="grid grid-cols-2 gap-3">
      
      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group text-center">
        <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <span class="text-[10px] font-bold text-slate-600 group-hover:text-[#5138ed] leading-tight">Generate Custom Report</span>
      </button>

      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group text-center">
        <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        <span class="text-[10px] font-bold text-slate-600 group-hover:text-[#5138ed] leading-tight">Export All Data</span>
      </button>

      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group text-center">
        <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="text-[10px] font-bold text-slate-600 group-hover:text-[#5138ed] leading-tight">Schedule Report</span>
      </button>

      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group text-center">
        <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
        <span class="text-[10px] font-bold text-slate-600 group-hover:text-[#5138ed] leading-tight">Share Report</span>
      </button>

    </div>
  </div>
</template>
