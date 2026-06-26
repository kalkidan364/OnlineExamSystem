<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <h2 class="text-[14px] font-bold text-slate-800 mb-6">Students by Course</h2>
    
    <div class="space-y-4 mb-6">
      
      <!-- Course 1 -->
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <span class="text-[11px] font-bold text-slate-700">Computer Science</span>
          <span class="text-[11px] font-bold text-slate-800">128</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
          <div class="bg-[#5138ed] h-1.5 rounded-full" style="width: 100%"></div>
        </div>
      </div>

      <!-- Course 2 -->
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <span class="text-[11px] font-bold text-slate-700">Software Engineering</span>
          <span class="text-[11px] font-bold text-slate-800">96</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
          <div class="bg-[#5138ed] h-1.5 rounded-full" style="width: 75%"></div>
        </div>
      </div>

      <!-- Course 3 -->
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <span class="text-[11px] font-bold text-slate-700">Information Technology</span>
          <span class="text-[11px] font-bold text-slate-800">64</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
          <div class="bg-[#5138ed] h-1.5 rounded-full" style="width: 50%"></div>
        </div>
      </div>

      <!-- Course 4 -->
      <div>
        <div class="flex items-center justify-between mb-1.5">
          <span class="text-[11px] font-bold text-slate-700">Electrical Engineering</span>
          <span class="text-[11px] font-bold text-slate-800">32</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
          <div class="bg-indigo-200 h-1.5 rounded-full" style="width: 25%"></div>
        </div>
      </div>

    </div>

    <button class="w-full py-2.5 border border-[#5138ed] text-[#5138ed] text-[12px] font-bold rounded-xl hover:bg-indigo-50 transition-colors">
      View Detailed Report
    </button>
  </div>
</template>
