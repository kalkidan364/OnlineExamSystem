<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <h2 class="text-[14px] font-bold text-slate-800 mb-6">Performance Overview</h2>
    
    <div class="flex items-center gap-6">
      
      <!-- Custom SVG Donut Chart -->
      <div class="relative w-32 h-32 flex-shrink-0">
        <!-- Background ring -->
        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
          <circle cx="50" cy="50" r="40" fill="none" stroke="#f1f5f9" stroke-width="12"></circle>
          
          <!-- Green segment (Above 85%, 14.4%) -->
          <circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="12" 
                  stroke-dasharray="36.19 251.2" stroke-dashoffset="0"></circle>
          
          <!-- Blue segment (70-85%, 35%) -->
          <circle cx="50" cy="50" r="40" fill="none" stroke="#3b82f6" stroke-width="12" 
                  stroke-dasharray="87.92 251.2" stroke-dashoffset="-38.19"></circle>
          
          <!-- Orange segment (50-70%, 34.4%) -->
          <circle cx="50" cy="50" r="40" fill="none" stroke="#f59e0b" stroke-width="12" 
                  stroke-dasharray="86.41 251.2" stroke-dashoffset="-128.11"></circle>

          <!-- Red segment (Below 50%, 16.2%) -->
          <circle cx="50" cy="50" r="40" fill="none" stroke="#ef4444" stroke-width="12" 
                  stroke-dasharray="38.68 251.2" stroke-dashoffset="-216.52"></circle>
        </svg>
        
        <!-- Center Text -->
        <div class="absolute inset-0 flex flex-col items-center justify-center">
          <span class="text-[22px] font-black text-slate-800 leading-none mb-1">72.4%</span>
          <span class="text-[9px] font-bold text-slate-400">Average Score</span>
        </div>
      </div>

      <!-- Legend -->
      <div class="flex-1 space-y-3">
        <div class="flex items-start gap-2">
          <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-0.5"></div>
          <div>
            <span class="block text-[11px] font-bold text-slate-800 leading-tight">Above 85%</span>
            <span class="block text-[9px] text-slate-500 mt-0.5">46 Students (14.4%)</span>
          </div>
        </div>
        <div class="flex items-start gap-2">
          <div class="w-2.5 h-2.5 rounded-full bg-blue-500 mt-0.5"></div>
          <div>
            <span class="block text-[11px] font-bold text-slate-800 leading-tight">70% - 85%</span>
            <span class="block text-[9px] text-slate-500 mt-0.5">112 Students (35.0%)</span>
          </div>
        </div>
        <div class="flex items-start gap-2">
          <div class="w-2.5 h-2.5 rounded-full bg-orange-500 mt-0.5"></div>
          <div>
            <span class="block text-[11px] font-bold text-slate-800 leading-tight">50% - 70%</span>
            <span class="block text-[9px] text-slate-500 mt-0.5">110 Students (34.4%)</span>
          </div>
        </div>
        <div class="flex items-start gap-2">
          <div class="w-2.5 h-2.5 rounded-full bg-rose-500 mt-0.5"></div>
          <div>
            <span class="block text-[11px] font-bold text-slate-800 leading-tight">Below 50%</span>
            <span class="block text-[9px] text-slate-500 mt-0.5">52 Students (16.2%)</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>
