<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <h2 class="text-[14px] font-bold text-slate-800 mb-6">Overall Performance</h2>
    
    <div class="flex flex-col items-center justify-center">
      
      <!-- Custom SVG Donut Chart -->
      <div class="relative w-40 h-40 mb-6">
        <!-- Background ring -->
        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
          <circle cx="50" cy="50" r="40" fill="none" stroke="#f1f5f9" stroke-width="12"></circle>
          
          <!-- A segment (17.8%) Green -->
          <circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="12" 
                  stroke-dasharray="44.7 251.2" stroke-dashoffset="0"></circle>
          
          <!-- B segment (32.2%) Blue -->
          <circle cx="50" cy="50" r="40" fill="none" stroke="#3b82f6" stroke-width="12" 
                  stroke-dasharray="80.8 251.2" stroke-dashoffset="-44.7"></circle>
          
          <!-- C segment (23.1%) Orange -->
          <circle cx="50" cy="50" r="40" fill="none" stroke="#f59e0b" stroke-width="12" 
                  stroke-dasharray="58.0 251.2" stroke-dashoffset="-125.5"></circle>

          <!-- D segment (14.0%) Yellow/Lighter Orange -->
          <circle cx="50" cy="50" r="40" fill="none" stroke="#fb923c" stroke-width="12" 
                  stroke-dasharray="35.1 251.2" stroke-dashoffset="-183.5"></circle>
          
          <!-- F segment (12.9%) Red -->
          <circle cx="50" cy="50" r="40" fill="none" stroke="#ef4444" stroke-width="12" 
                  stroke-dasharray="32.4 251.2" stroke-dashoffset="-218.6"></circle>
        </svg>
        
        <!-- Center Text -->
        <div class="absolute inset-0 flex flex-col items-center justify-center">
          <span class="text-[24px] font-black text-slate-800 leading-none mb-1">856</span>
          <span class="text-[9px] font-bold text-slate-400">Total Students</span>
        </div>
      </div>

      <!-- Legend List -->
      <div class="w-full space-y-3">
        <!-- A -->
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
            <span class="text-[11px] font-bold text-slate-800">A (90-100%)</span>
          </div>
          <span class="text-[10px] text-slate-500 font-medium">152 (17.8%)</span>
        </div>
        <!-- B -->
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
            <span class="text-[11px] font-bold text-slate-800">B (80-89%)</span>
          </div>
          <span class="text-[10px] text-slate-500 font-medium">276 (32.2%)</span>
        </div>
        <!-- C -->
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-2.5 h-2.5 rounded-full bg-yellow-500"></div>
            <span class="text-[11px] font-bold text-slate-800">C (70-79%)</span>
          </div>
          <span class="text-[10px] text-slate-500 font-medium">198 (23.1%)</span>
        </div>
        <!-- D -->
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-2.5 h-2.5 rounded-full bg-orange-400"></div>
            <span class="text-[11px] font-bold text-slate-800">D (60-69%)</span>
          </div>
          <span class="text-[10px] text-slate-500 font-medium">120 (14.0%)</span>
        </div>
        <!-- F -->
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div>
            <span class="text-[11px] font-bold text-slate-800">F (Below 60%)</span>
          </div>
          <span class="text-[10px] text-slate-500 font-medium">110 (12.9%)</span>
        </div>
      </div>

    </div>
  </div>
</template>
