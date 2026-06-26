<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <h2 class="text-[14px] font-bold text-slate-800 mb-5">Quick Actions</h2>
    
    <div class="grid grid-cols-2 gap-3">
      
      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group">
        <svg class="w-6 h-6 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v4m-2-2h4"></path></svg>
        <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed]">Schedule New Exam</span>
      </button>

      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group">
        <svg class="w-6 h-6 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed]">Bulk Schedule</span>
      </button>

      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group">
        <svg class="w-6 h-6 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed]">Import Schedule</span>
      </button>

      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group">
        <svg class="w-6 h-6 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed]">Exam Calendar</span>
      </button>

    </div>
  </div>
</template>
