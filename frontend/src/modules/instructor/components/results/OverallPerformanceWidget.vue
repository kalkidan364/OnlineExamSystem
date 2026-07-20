<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
    <h2 class="text-[12px] font-bold text-slate-800 mb-4">Grading Progress</h2>
    
    <div class="flex flex-col items-center justify-center">
      
      <!-- Custom SVG Donut Chart -->
      <div class="relative w-32 h-32 mb-2">
        <!-- Background ring -->
        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
          <circle cx="50" cy="50" r="40" fill="none" stroke="#f1f5f9" stroke-width="12"></circle>
          
          <!-- Progress segment (72%) Indigo -->
          <!-- 2 * Math.PI * 40 = 251.3 -->
          <!-- 72% of 251.3 = 180.9 -->
          <circle cx="50" cy="50" r="40" fill="none" stroke="#5138ed" stroke-width="12" stroke-linecap="round"
                  stroke-dasharray="180.9 251.3" stroke-dashoffset="0"></circle>
        </svg>
        
        <!-- Center Text -->
        <div class="absolute inset-0 flex flex-col items-center justify-center mt-1">
          <span class="text-[22px] font-black text-slate-800 leading-none">72%</span>
        </div>
      </div>

      <span class="text-[10px] font-bold text-slate-500 mb-2">90 / 125 Graded</span>

    </div>
  </div>
</template>
