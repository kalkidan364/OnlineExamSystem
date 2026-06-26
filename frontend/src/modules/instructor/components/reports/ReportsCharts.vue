<template>
  <div class="flex flex-col lg:flex-row gap-6">
    
    <!-- Exam Performance Trend (Line Chart) -->
    <div class="flex-[2] min-w-0 bg-white border border-slate-100 rounded-2xl p-6 shadow-sm flex flex-col">
      <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        <h2 class="text-[14px] font-bold text-slate-800">Exam Performance Trend</h2>
        
        <div class="flex flex-wrap items-center gap-4">
          <!-- Legend -->
          <div class="flex items-center gap-3">
            <div class="flex items-center gap-1.5">
              <div class="w-2 h-2 rounded-full bg-[#5138ed]"></div>
              <span class="text-[10px] font-bold text-slate-500">Average Score (%)</span>
            </div>
            <div class="flex items-center gap-1.5">
              <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
              <span class="text-[10px] font-bold text-slate-500">Pass Rate (%)</span>
            </div>
            <div class="flex items-center gap-1.5">
              <div class="w-2 h-2 rounded-full bg-rose-500"></div>
              <span class="text-[10px] font-bold text-slate-500">Fail Rate (%)</span>
            </div>
          </div>
          
          <!-- Dropdown -->
          <div class="relative min-w-[80px]">
            <select class="w-full appearance-none pl-3 pr-8 py-1.5 text-[10px] border border-slate-200 rounded-lg focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] text-slate-700 bg-white cursor-pointer font-bold">
              <option>Daily</option>
            </select>
            <svg class="w-3.5 h-3.5 text-slate-400 absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
        </div>
      </div>

      <!-- Custom SVG Line Chart -->
      <div class="relative w-full h-[220px] mt-2 flex-1">
        
        <!-- Y-Axis Labels -->
        <div class="absolute left-0 top-0 bottom-6 flex flex-col justify-between text-[9px] font-medium text-slate-400">
          <span>100%</span>
          <span>75%</span>
          <span>50%</span>
          <span>25%</span>
          <span>0%</span>
        </div>

        <!-- Grid Lines & Chart Area -->
        <div class="absolute left-8 right-0 top-2 bottom-6">
          <!-- Horizontal Grid Lines -->
          <div class="absolute inset-0 flex flex-col justify-between">
            <div class="w-full border-b border-slate-100"></div>
            <div class="w-full border-b border-slate-100"></div>
            <div class="w-full border-b border-slate-100"></div>
            <div class="w-full border-b border-slate-100"></div>
            <div class="w-full border-b border-slate-200"></div>
          </div>

          <!-- SVG Lines -->
          <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
            <!-- Average Score Line (Blue) -->
            <path 
              fill="none" 
              stroke="#5138ed" 
              stroke-width="1.5" 
              d="M 0 25 L 10 20 L 20 28 L 30 18 L 40 25 L 50 15 L 60 20 L 70 25 L 80 18 L 90 22 L 100 20" 
              stroke-linejoin="round"
              vector-effect="non-scaling-stroke"
            />
            <!-- Dots for Blue Line -->
            <circle cx="0" cy="25" r="1.5" fill="#5138ed" />
            <circle cx="10" cy="20" r="1.5" fill="#5138ed" />
            <circle cx="20" cy="28" r="1.5" fill="#5138ed" />
            <circle cx="30" cy="18" r="1.5" fill="#5138ed" />
            <circle cx="40" cy="25" r="1.5" fill="#5138ed" />
            <circle cx="50" cy="15" r="1.5" fill="#5138ed" />
            <circle cx="60" cy="20" r="1.5" fill="#5138ed" />
            <circle cx="70" cy="25" r="1.5" fill="#5138ed" />
            <circle cx="80" cy="18" r="1.5" fill="#5138ed" />
            <circle cx="90" cy="22" r="1.5" fill="#5138ed" />
            <circle cx="100" cy="20" r="1.5" fill="#5138ed" />

            <!-- Pass Rate Line (Green) -->
            <path 
              fill="none" 
              stroke="#10b981" 
              stroke-width="1.5" 
              d="M 0 35 L 10 32 L 20 38 L 30 30 L 40 35 L 50 30 L 60 38 L 70 32 L 80 35 L 90 32 L 100 32" 
              stroke-linejoin="round"
              vector-effect="non-scaling-stroke"
            />
            <!-- Dots for Green Line -->
            <circle cx="0" cy="35" r="1.5" fill="#10b981" />
            <circle cx="10" cy="32" r="1.5" fill="#10b981" />
            <circle cx="20" cy="38" r="1.5" fill="#10b981" />
            <circle cx="30" cy="30" r="1.5" fill="#10b981" />
            <circle cx="40" cy="35" r="1.5" fill="#10b981" />
            <circle cx="50" cy="30" r="1.5" fill="#10b981" />
            <circle cx="60" cy="38" r="1.5" fill="#10b981" />
            <circle cx="70" cy="32" r="1.5" fill="#10b981" />
            <circle cx="80" cy="35" r="1.5" fill="#10b981" />
            <circle cx="90" cy="32" r="1.5" fill="#10b981" />
            <circle cx="100" cy="32" r="1.5" fill="#10b981" />

            <!-- Fail Rate Line (Red) -->
            <path 
              fill="none" 
              stroke="#ef4444" 
              stroke-width="1.5" 
              d="M 0 75 L 10 75 L 20 65 L 30 68 L 40 75 L 50 70 L 60 65 L 70 72 L 80 68 L 90 72 L 100 75" 
              stroke-linejoin="round"
              vector-effect="non-scaling-stroke"
            />
            <!-- Dots for Red Line -->
            <circle cx="0" cy="75" r="1.5" fill="#ef4444" />
            <circle cx="10" cy="75" r="1.5" fill="#ef4444" />
            <circle cx="20" cy="65" r="1.5" fill="#ef4444" />
            <circle cx="30" cy="68" r="1.5" fill="#ef4444" />
            <circle cx="40" cy="75" r="1.5" fill="#ef4444" />
            <circle cx="50" cy="70" r="1.5" fill="#ef4444" />
            <circle cx="60" cy="65" r="1.5" fill="#ef4444" />
            <circle cx="70" cy="72" r="1.5" fill="#ef4444" />
            <circle cx="80" cy="68" r="1.5" fill="#ef4444" />
            <circle cx="90" cy="72" r="1.5" fill="#ef4444" />
            <circle cx="100" cy="75" r="1.5" fill="#ef4444" />
          </svg>
        </div>

        <!-- X-Axis Labels -->
        <div class="absolute left-8 right-0 bottom-0 flex justify-between text-[9px] font-medium text-slate-400">
          <span>May 01</span>
          <span>May 05</span>
          <span>May 09</span>
          <span>May 13</span>
          <span>May 17</span>
          <span>May 21</span>
          <span>May 25</span>
        </div>

      </div>
    </div>

    <!-- Results Distribution (Donut Chart) -->
    <div class="flex-1 min-w-0 bg-white border border-slate-100 rounded-2xl p-4 lg:p-6 shadow-sm flex flex-col">
      <h2 class="text-[14px] font-bold text-slate-800 mb-4 lg:mb-6">Results Distribution</h2>
      
      <div class="flex flex-col 2xl:flex-row items-center gap-4 lg:gap-6 flex-1">
        <!-- SVG Donut Chart -->
        <div class="relative w-28 h-28 lg:w-32 lg:h-32 flex-shrink-0">
          <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="40" fill="none" stroke="#f1f5f9" stroke-width="12"></circle>
            
            <circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="12" 
                    stroke-dasharray="44.7 251.2" stroke-dashoffset="0"></circle>
            
            <circle cx="50" cy="50" r="40" fill="none" stroke="#3b82f6" stroke-width="12" 
                    stroke-dasharray="80.8 251.2" stroke-dashoffset="-44.7"></circle>
            
            <circle cx="50" cy="50" r="40" fill="none" stroke="#f59e0b" stroke-width="12" 
                    stroke-dasharray="58.0 251.2" stroke-dashoffset="-125.5"></circle>

            <circle cx="50" cy="50" r="40" fill="none" stroke="#fb923c" stroke-width="12" 
                    stroke-dasharray="35.1 251.2" stroke-dashoffset="-183.5"></circle>
            
            <circle cx="50" cy="50" r="40" fill="none" stroke="#ef4444" stroke-width="12" 
                    stroke-dasharray="32.4 251.2" stroke-dashoffset="-218.6"></circle>
          </svg>
          
          <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="text-[18px] font-black text-slate-800 leading-none mb-1">856</span>
            <span class="text-[9px] font-bold text-slate-400">Students</span>
          </div>
        </div>

        <!-- Legend -->
        <div class="flex-1 space-y-2.5">
          <div class="flex items-start gap-2">
            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-0.5"></div>
            <div>
              <span class="block text-[10px] font-bold text-slate-800 leading-tight">A (90-100%)</span>
              <span class="block text-[8px] text-slate-500 mt-0.5">152 (17.8%)</span>
            </div>
          </div>
          <div class="flex items-start gap-2">
            <div class="w-2.5 h-2.5 rounded-full bg-blue-500 mt-0.5"></div>
            <div>
              <span class="block text-[10px] font-bold text-slate-800 leading-tight">B (80-89%)</span>
              <span class="block text-[8px] text-slate-500 mt-0.5">276 (32.2%)</span>
            </div>
          </div>
          <div class="flex items-start gap-2">
            <div class="w-2.5 h-2.5 rounded-full bg-yellow-500 mt-0.5"></div>
            <div>
              <span class="block text-[10px] font-bold text-slate-800 leading-tight">C (70-79%)</span>
              <span class="block text-[8px] text-slate-500 mt-0.5">198 (23.1%)</span>
            </div>
          </div>
          <div class="flex items-start gap-2">
            <div class="w-2.5 h-2.5 rounded-full bg-orange-400 mt-0.5"></div>
            <div>
              <span class="block text-[10px] font-bold text-slate-800 leading-tight">D (60-69%)</span>
              <span class="block text-[8px] text-slate-500 mt-0.5">120 (14.0%)</span>
            </div>
          </div>
          <div class="flex items-start gap-2">
            <div class="w-2.5 h-2.5 rounded-full bg-rose-500 mt-0.5"></div>
            <div>
              <span class="block text-[10px] font-bold text-slate-800 leading-tight">F (Below 60%)</span>
              <span class="block text-[8px] text-slate-500 mt-0.5">110 (12.9%)</span>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</template>
