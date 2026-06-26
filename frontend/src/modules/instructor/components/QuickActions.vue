<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <h2 class="text-[16px] font-bold text-slate-800 mb-5">Quick Actions</h2>
    
    <div class="grid grid-cols-2 gap-3">
      <button class="flex flex-col items-center justify-center py-4 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all group">
        <svg class="w-6 h-6 text-[#5138ed] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        <span class="text-[12px] font-semibold text-slate-600 group-hover:text-[#5138ed]">Create Exam</span>
      </button>
      
      <button class="flex flex-col items-center justify-center py-4 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all group">
        <svg class="w-6 h-6 text-[#5138ed] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        <span class="text-[12px] font-semibold text-slate-600 group-hover:text-[#5138ed]">Add Questions</span>
      </button>
      
      <button class="flex flex-col items-center justify-center py-4 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all group">
        <svg class="w-6 h-6 text-[#5138ed] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        <span class="text-[12px] font-semibold text-slate-600 group-hover:text-[#5138ed]">Schedule Exam</span>
      </button>
      
      <button class="flex flex-col items-center justify-center py-4 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all group">
        <svg class="w-6 h-6 text-[#5138ed] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <span class="text-[12px] font-semibold text-slate-600 group-hover:text-[#5138ed]">View Reports</span>
      </button>
    </div>
  </div>
</template>
