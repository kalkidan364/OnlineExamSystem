<template>
  <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col">
    <!-- Top Section: Pending Manual Grading -->
    <div class="p-4 border-b border-slate-100">
      <h2 class="text-[12px] font-bold text-slate-800 mb-3">Pending Manual Grading</h2>
      <div class="space-y-3">
        
        <!-- Essay Questions -->
        <div class="flex items-start justify-between">
          <div class="flex items-start gap-2">
            <div class="w-7 h-7 rounded-lg bg-orange-50 flex items-center justify-center text-orange-500 mt-0.5 shrink-0">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <div class="flex flex-col">
              <span class="text-[11px] font-bold text-slate-800">Essay Questions</span>
              <span class="text-[10px] text-slate-500 mt-0.5">14 students</span>
              <span class="text-[9px] text-slate-400 mt-0.5 flex items-center gap-1">
                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Due: Tomorrow
              </span>
            </div>
          </div>
          <span class="px-1.5 py-0.5 text-[8px] font-bold bg-rose-50 text-rose-600 rounded">High Priority</span>
        </div>

        <!-- Short Answer -->
        <div class="flex items-start justify-between">
          <div class="flex items-start gap-2">
            <div class="w-7 h-7 rounded-lg bg-orange-50 flex items-center justify-center text-orange-500 mt-0.5 shrink-0">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
            </div>
            <div class="flex flex-col">
              <span class="text-[11px] font-bold text-slate-800">Short Answer</span>
              <span class="text-[10px] text-slate-500 mt-0.5">21 students</span>
              <span class="text-[9px] text-slate-400 mt-0.5 flex items-center gap-1">
                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Due: In 3 days
              </span>
            </div>
          </div>
          <span class="px-1.5 py-0.5 text-[8px] font-bold bg-slate-100 text-slate-600 rounded">Normal</span>
        </div>

      </div>
    </div>

    <!-- Bottom Section: Result Status Overview -->
    <div class="p-4">
      <h2 class="text-[12px] font-bold text-slate-800 mb-3">Result Status Overview</h2>
      
      <div class="space-y-3">
        <!-- Published -->
        <div>
          <div class="flex items-center justify-between mb-1">
            <div class="flex items-center gap-2">
              <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
              <span class="text-[10px] font-bold text-slate-700">Published</span>
            </div>
            <span class="text-[10px] font-bold text-slate-700">5 <span class="text-[9px] text-slate-400 font-medium">exams</span></span>
          </div>
          <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden flex">
            <div class="h-full bg-emerald-500 rounded-full" style="width: 62.5%"></div>
          </div>
        </div>
        
        <!-- Pending -->
        <div>
          <div class="flex items-center justify-between mb-1">
            <div class="flex items-center gap-2">
              <div class="w-2 h-2 rounded-full bg-orange-400"></div>
              <span class="text-[10px] font-bold text-slate-700">Pending</span>
            </div>
            <span class="text-[10px] font-bold text-slate-700">2 <span class="text-[9px] text-slate-400 font-medium">exams</span></span>
          </div>
          <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden flex">
            <div class="h-full bg-orange-400 rounded-full" style="width: 25%"></div>
          </div>
        </div>
        
        <!-- Draft -->
        <div>
          <div class="flex items-center justify-between mb-1">
            <div class="flex items-center gap-2">
              <div class="w-2 h-2 rounded-full bg-slate-300"></div>
              <span class="text-[10px] font-bold text-slate-700">Draft</span>
            </div>
            <span class="text-[10px] font-bold text-slate-700">1 <span class="text-[9px] text-slate-400 font-medium">exam</span></span>
          </div>
          <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden flex">
            <div class="h-full bg-slate-300 rounded-full" style="width: 12.5%"></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>
