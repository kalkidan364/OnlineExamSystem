<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <h2 class="text-[14px] font-bold text-slate-800 mb-5">Quick Actions</h2>
    
    <div class="grid grid-cols-2 gap-3">
      
      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group">
        <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed]">Add New Student</span>
      </button>

      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group">
        <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed]">Import Students</span>
      </button>

      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group">
        <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
        <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed]">Student Progress</span>
      </button>

      <button class="flex flex-col items-center justify-center gap-2 p-4 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-100 transition-colors group">
        <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed]">Generate Report</span>
      </button>

    </div>
  </div>
</template>
