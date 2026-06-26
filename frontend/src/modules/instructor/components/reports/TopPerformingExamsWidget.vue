<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-[14px] font-bold text-slate-800">Top Performing Exams</h2>
      <button class="text-[11px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">
        View All
      </button>
    </div>
    
    <!-- Table Header -->
    <div class="flex items-center justify-between mb-3 border-b border-slate-100 pb-2">
      <span class="text-[10px] font-bold text-slate-800 w-1/2">Exam Title</span>
      <span class="text-[10px] font-bold text-slate-800 w-1/4 text-center">Average Score</span>
      <span class="text-[10px] font-bold text-slate-800 w-1/4 text-center">Pass Rate</span>
    </div>

    <!-- Data Rows -->
    <div class="space-y-4">
      
      <!-- Row 1 -->
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-slate-600 w-1/2 truncate pr-2">Database Systems Mid Exam</span>
        <span class="text-[11px] font-medium text-slate-800 w-1/4 text-center">82.6%</span>
        <span class="text-[11px] font-medium text-slate-800 w-1/4 text-center">78.3%</span>
      </div>

      <!-- Row 2 -->
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-slate-600 w-1/2 truncate pr-2">Web Programming Quiz 1</span>
        <span class="text-[11px] font-medium text-slate-800 w-1/4 text-center">81.4%</span>
        <span class="text-[11px] font-medium text-slate-800 w-1/4 text-center">76.5%</span>
      </div>

      <!-- Row 3 -->
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-slate-600 w-1/2 truncate pr-2">Operating Systems Mid Exam</span>
        <span class="text-[11px] font-medium text-slate-800 w-1/4 text-center">79.8%</span>
        <span class="text-[11px] font-medium text-slate-800 w-1/4 text-center">74.2%</span>
      </div>

      <!-- Row 4 -->
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-slate-600 w-1/2 truncate pr-2">Software Engineering Quiz 1</span>
        <span class="text-[11px] font-medium text-slate-800 w-1/4 text-center">76.3%</span>
        <span class="text-[11px] font-medium text-slate-800 w-1/4 text-center">72.1%</span>
      </div>

      <!-- Row 5 -->
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-slate-600 w-1/2 truncate pr-2">Data Structures Final Exam</span>
        <span class="text-[11px] font-medium text-slate-800 w-1/4 text-center">75.6%</span>
        <span class="text-[11px] font-medium text-slate-800 w-1/4 text-center">70.4%</span>
      </div>

    </div>
  </div>
</template>
