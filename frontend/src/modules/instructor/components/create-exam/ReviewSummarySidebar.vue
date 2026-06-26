<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm mb-6">
    <h2 class="text-[14px] font-bold text-slate-800 mb-6">Exam Summary</h2>
    
    <div class="space-y-5">
      <!-- Exam Title -->
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-[#5138ed]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[12px] font-bold text-slate-700">Exam Title</span>
          <span class="text-[11px] text-slate-500 font-medium mt-0.5 leading-snug">Database Systems Mid Exam</span>
        </div>
      </div>

      <!-- Course -->
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-[#5138ed]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[12px] font-bold text-slate-700">Course</span>
          <span class="text-[11px] text-slate-500 font-medium mt-0.5 leading-snug">Database Systems (CS 304)</span>
        </div>
      </div>

      <!-- Exam Type -->
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-[#5138ed]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[12px] font-bold text-slate-700">Exam Type</span>
          <span class="text-[11px] text-slate-500 font-medium mt-0.5">Mid Exam</span>
        </div>
      </div>

      <!-- Total Questions -->
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-[#5138ed]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[12px] font-bold text-slate-700">Total Questions</span>
          <span class="text-[11px] text-slate-500 font-medium mt-0.5">40 Questions</span>
        </div>
      </div>

      <!-- Total Marks -->
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-[#5138ed]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[12px] font-bold text-slate-700">Total Marks</span>
          <span class="text-[11px] text-slate-500 font-medium mt-0.5">100</span>
        </div>
      </div>

      <!-- Passing Marks -->
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-[#5138ed]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[12px] font-bold text-slate-700">Passing Marks</span>
          <span class="text-[11px] text-slate-500 font-medium mt-0.5">60 (60%)</span>
        </div>
      </div>

      <!-- Duration -->
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-[#5138ed]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[12px] font-bold text-slate-700">Duration</span>
          <span class="text-[11px] text-slate-500 font-medium mt-0.5">1h 30m</span>
        </div>
      </div>

      <!-- Status -->
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-[#5138ed]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[12px] font-bold text-slate-700 mb-1">Status</span>
          <span class="inline-block px-2 py-0.5 bg-orange-50 text-orange-600 text-[10px] font-bold rounded-md self-start">Draft</span>
        </div>
      </div>
    </div>
  </div>
</template>
