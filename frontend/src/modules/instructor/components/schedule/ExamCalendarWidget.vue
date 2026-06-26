<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <h2 class="text-[14px] font-bold text-slate-800 mb-5">Exam Calendar</h2>
    
    <!-- Calendar Header -->
    <div class="flex items-center justify-between mb-4">
      <button class="w-7 h-7 rounded-lg hover:bg-slate-50 flex items-center justify-center text-slate-400 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
      </button>
      <span class="text-[13px] font-bold text-slate-800">May 2025</span>
      <button class="w-7 h-7 rounded-lg hover:bg-slate-50 flex items-center justify-center text-slate-400 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
      </button>
    </div>

    <!-- Calendar Grid -->
    <div class="grid grid-cols-7 gap-1 text-center mb-2">
      <div class="text-[10px] font-bold text-slate-400 py-1">Sun</div>
      <div class="text-[10px] font-bold text-slate-400 py-1">Mon</div>
      <div class="text-[10px] font-bold text-slate-400 py-1">Tue</div>
      <div class="text-[10px] font-bold text-slate-400 py-1">Wed</div>
      <div class="text-[10px] font-bold text-slate-400 py-1">Thu</div>
      <div class="text-[10px] font-bold text-slate-400 py-1">Fri</div>
      <div class="text-[10px] font-bold text-slate-400 py-1">Sat</div>
    </div>

    <div class="grid grid-cols-7 gap-y-2 gap-x-1 text-center">
      <!-- Previous Month -->
      <div class="text-[12px] font-medium text-slate-300 py-1.5 flex items-center justify-center">27</div>
      <div class="text-[12px] font-medium text-slate-300 py-1.5 flex items-center justify-center">28</div>
      <div class="text-[12px] font-medium text-slate-300 py-1.5 flex items-center justify-center">29</div>
      <div class="text-[12px] font-medium text-slate-300 py-1.5 flex items-center justify-center">30</div>
      
      <!-- Current Month -->
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">1</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">2</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">3</div>
      
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">4</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">5</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">6</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">7</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">8</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">9</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">10</div>

      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">11</div>
      <!-- May 12: Completed highlight -->
      <div class="w-6 h-6 mx-auto rounded-full bg-emerald-500 text-white font-bold text-[12px] flex items-center justify-center shadow-sm">12</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">13</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">14</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">15</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">16</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">17</div>

      <!-- May 18: Ongoing highlight -->
      <div class="w-6 h-6 mx-auto rounded-full bg-orange-500 text-white font-bold text-[12px] flex items-center justify-center shadow-sm">18</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">19</div>
      <!-- May 20: Dot highlight -->
      <div class="relative text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">
        20
        <span class="absolute bottom-0 w-1 h-1 rounded-full bg-[#5138ed]"></span>
      </div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">21</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">22</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">23</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">24</div>

      <!-- May 25: Upcoming highlight -->
      <div class="w-6 h-6 mx-auto rounded-full bg-[#5138ed] text-white font-bold text-[12px] flex items-center justify-center shadow-sm">25</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">26</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">27</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">28</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">29</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">30</div>
      <div class="text-[12px] font-medium text-slate-600 hover:bg-slate-50 rounded-lg cursor-pointer py-1.5 flex items-center justify-center">31</div>
    </div>
  </div>
</template>
