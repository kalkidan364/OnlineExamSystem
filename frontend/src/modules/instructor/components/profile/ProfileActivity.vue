<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-[14px] font-bold text-slate-800">Account Activity</h2>
      <a href="#" class="text-[11px] font-bold text-[#5138ed] hover:underline">View All</a>
    </div>

    <div class="space-y-6">
      
      <!-- Activity Item 1 -->
      <div class="flex items-start gap-4">
        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500 flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
        </div>
        <div class="flex-1">
          <div class="flex flex-wrap items-start justify-between gap-2">
            <div>
              <h3 class="text-[12px] font-bold text-slate-800">Logged in successfully</h3>
              <p class="text-[11px] text-slate-500 mt-0.5">May 25, 2025 10:30 AM</p>
            </div>
            <span class="text-[10px] font-bold text-slate-400">Today</span>
          </div>
        </div>
      </div>

      <!-- Activity Item 2 -->
      <div class="flex items-start gap-4">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed] flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <div class="flex-1">
          <div class="flex flex-wrap items-start justify-between gap-2">
            <div>
              <h3 class="text-[12px] font-bold text-slate-800">Created a new exam</h3>
              <p class="text-[11px] text-slate-500 mt-0.5">Database Systems Mid Exam</p>
            </div>
            <span class="text-[10px] font-bold text-slate-400">Yesterday</span>
          </div>
        </div>
      </div>

      <!-- Activity Item 3 -->
      <div class="flex items-start gap-4">
        <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-orange-500 flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        </div>
        <div class="flex-1">
          <div class="flex flex-wrap items-start justify-between gap-2">
            <div>
              <h3 class="text-[12px] font-bold text-slate-800">Viewed exam results</h3>
              <p class="text-[11px] text-slate-500 mt-0.5">Database Systems Mid Exam</p>
            </div>
            <span class="text-[10px] font-bold text-slate-400">May 23, 2025</span>
          </div>
        </div>
      </div>

      <!-- Activity Item 4 -->
      <div class="flex items-start gap-4">
        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <div class="flex-1">
          <div class="flex flex-wrap items-start justify-between gap-2">
            <div>
              <h3 class="text-[12px] font-bold text-slate-800">Updated profile information</h3>
            </div>
            <span class="text-[10px] font-bold text-slate-400">May 20, 2025</span>
          </div>
        </div>
      </div>

      <!-- Activity Item 5 -->
      <div class="flex items-start gap-4">
        <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-500 flex-shrink-0 mt-0.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>
        <div class="flex-1">
          <div class="flex flex-wrap items-start justify-between gap-2">
            <div>
              <h3 class="text-[12px] font-bold text-slate-800">Changed password</h3>
            </div>
            <span class="text-[10px] font-bold text-slate-400">May 18, 2025</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>
