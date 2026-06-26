<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
    <h2 class="text-[13px] font-bold text-slate-800 mb-4">Quick Actions</h2>
    
    <div class="grid grid-cols-2 gap-3">
      <button class="flex flex-col items-center justify-center py-4 px-2 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all group">
        <svg class="w-5 h-5 text-[#5138ed] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        <span class="text-[10px] xl:text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed] text-center">Create New Exam</span>
      </button>
      
      <button class="flex flex-col items-center justify-center py-4 px-2 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all group">
        <svg class="w-5 h-5 text-[#5138ed] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        <span class="text-[10px] xl:text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed] text-center">Import Questions</span>
      </button>
      
      <button class="flex flex-col items-center justify-center py-4 px-2 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all group">
        <svg class="w-5 h-5 text-[#5138ed] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        <span class="text-[10px] xl:text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed] text-center">Schedule Exam</span>
      </button>
      
      <button class="flex flex-col items-center justify-center py-4 px-2 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all group">
        <svg class="w-5 h-5 text-[#5138ed] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <span class="text-[10px] xl:text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed] text-center">Exam Reports</span>
      </button>
    </div>
  </div>
</template>
