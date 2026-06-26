<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm mb-6">
    <h2 class="text-[14px] font-bold text-slate-800 mb-6">Exam Settings Overview</h2>
    
    <div class="space-y-5">
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

      <!-- Passing Marks -->
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-[#5138ed]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[12px] font-bold text-slate-700">Passing Marks</span>
          <span class="text-[11px] text-slate-500 font-medium mt-0.5">60%</span>
        </div>
      </div>

      <!-- Availability -->
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-[#5138ed]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[12px] font-bold text-slate-700">Availability</span>
          <span class="text-[11px] text-slate-500 font-medium mt-0.5 leading-snug">May 25, 2025 09:00 AM<br>to<br>May 25, 2025 10:30 AM</span>
        </div>
      </div>

      <!-- Attempts Allowed -->
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-[#5138ed]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[12px] font-bold text-slate-700">Attempts Allowed</span>
          <span class="text-[11px] text-slate-500 font-medium mt-0.5">1</span>
        </div>
      </div>

      <!-- Time Zone -->
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-full bg-[#5138ed]/10 flex items-center justify-center flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[12px] font-bold text-slate-700">Time Zone</span>
          <span class="text-[11px] text-slate-500 font-medium mt-0.5">(UTC+03:00) Addis Ababa</span>
        </div>
      </div>
    </div>
  </div>
</template>
