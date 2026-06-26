<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm mb-6">
    <h2 class="text-[14px] font-bold text-slate-800 mb-4">Add Questions From</h2>
    
    <div class="space-y-3">
      <!-- Option 1: Question Banks -->
      <div class="flex items-start gap-3 p-3 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all cursor-pointer group">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0 group-hover:bg-[#5138ed] transition-colors">
          <svg class="w-4 h-4 text-[#5138ed] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[12px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors">Question Banks</span>
          <span class="text-[10px] text-slate-400 font-medium leading-relaxed mt-0.5">Add questions from existing question banks</span>
        </div>
      </div>

      <!-- Option 2: Upload Questions -->
      <div class="flex items-start gap-3 p-3 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all cursor-pointer group">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0 group-hover:bg-[#5138ed] transition-colors">
          <svg class="w-4 h-4 text-[#5138ed] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[12px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors">Upload Questions</span>
          <span class="text-[10px] text-slate-400 font-medium leading-relaxed mt-0.5">Import questions from Excel or CSV file</span>
        </div>
      </div>

      <!-- Option 3: Create New Questions -->
      <div class="flex items-start gap-3 p-3 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all cursor-pointer group">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0 group-hover:bg-[#5138ed] transition-colors">
          <svg class="w-4 h-4 text-[#5138ed] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[12px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors">Create New Questions</span>
          <span class="text-[10px] text-slate-400 font-medium leading-relaxed mt-0.5">Create questions manually for this exam</span>
        </div>
      </div>
    </div>
  </div>
</template>
