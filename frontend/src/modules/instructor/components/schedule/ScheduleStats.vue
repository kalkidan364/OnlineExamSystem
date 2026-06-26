<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    
    <!-- Total Scheduled -->
    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-[#5138ed] flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2 2 4-4"></path></svg>
      </div>
      <div>
        <span class="block text-[11px] font-bold text-slate-500 mb-0.5">Total Scheduled</span>
        <span class="block text-[22px] font-black text-slate-800 leading-none mb-1">12</span>
        <span class="text-[10px] text-slate-400 font-medium">All scheduled exams</span>
      </div>
    </div>

    <!-- Upcoming -->
    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v4m0 0l-2-2m2 2l2-2"></path></svg>
      </div>
      <div>
        <span class="block text-[11px] font-bold text-slate-500 mb-0.5">Upcoming</span>
        <span class="block text-[22px] font-black text-slate-800 leading-none mb-1">6</span>
        <span class="text-[10px] text-slate-400 font-medium">Exams to come</span>
      </div>
    </div>

    <!-- Ongoing -->
    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v4m0 4h.01"></path></svg>
      </div>
      <div>
        <span class="block text-[11px] font-bold text-slate-500 mb-0.5">Ongoing</span>
        <span class="block text-[22px] font-black text-slate-800 leading-none mb-1">2</span>
        <span class="text-[10px] text-slate-400 font-medium">Currently running</span>
      </div>
    </div>

    <!-- Completed -->
    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500 flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
      </div>
      <div>
        <span class="block text-[11px] font-bold text-slate-500 mb-0.5">Completed</span>
        <span class="block text-[22px] font-black text-slate-800 leading-none mb-1">4</span>
        <span class="text-[10px] text-slate-400 font-medium">Finished exams</span>
      </div>
    </div>

  </div>
</template>
