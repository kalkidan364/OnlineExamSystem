<template>
  <div class="space-y-6">
    <!-- Quick Actions Card -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
      <h2 class="text-[14px] font-bold text-slate-800 mb-6">Quick Actions</h2>
      
      <div class="space-y-3">
        
        <!-- Clear System Cache -->
        <button class="w-full flex items-start gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100 group text-left">
          <div class="w-9 h-9 rounded-lg bg-indigo-50 text-[#5138ed] flex items-center justify-center flex-shrink-0 group-hover:bg-[#5138ed] group-hover:text-white transition-colors">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
          </div>
          <div>
            <h3 class="text-[13px] font-bold text-slate-800">Clear System Cache</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Improve system performance</p>
          </div>
        </button>

        <!-- Backup Now -->
        <button class="w-full flex items-start gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100 group text-left">
          <div class="w-9 h-9 rounded-lg bg-indigo-50 text-[#5138ed] flex items-center justify-center flex-shrink-0 group-hover:bg-[#5138ed] group-hover:text-white transition-colors">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
          </div>
          <div>
            <h3 class="text-[13px] font-bold text-slate-800">Backup Now</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Create a system backup</p>
          </div>
        </button>

        <!-- View Audit Logs -->
        <button class="w-full flex items-start gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100 group text-left">
          <div class="w-9 h-9 rounded-lg bg-indigo-50 text-[#5138ed] flex items-center justify-center flex-shrink-0 group-hover:bg-[#5138ed] group-hover:text-white transition-colors">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          </div>
          <div>
            <h3 class="text-[13px] font-bold text-slate-800">View Audit Logs</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Check system activity logs</p>
          </div>
        </button>

        <!-- System Health -->
        <button class="w-full flex items-start gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100 group text-left">
          <div class="w-9 h-9 rounded-lg bg-indigo-50 text-[#5138ed] flex items-center justify-center flex-shrink-0 group-hover:bg-[#5138ed] group-hover:text-white transition-colors">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
          </div>
          <div>
            <h3 class="text-[13px] font-bold text-slate-800">System Health</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Check system health status</p>
          </div>
        </button>

      </div>
    </div>

    <!-- Need Help Card -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
      <h2 class="text-[14px] font-bold text-slate-800 mb-4">Need Help?</h2>
      <p class="text-[11px] text-slate-500 font-medium leading-relaxed mb-5">
        Check our documentation or contact support for assistance.
      </p>
      
      <div class="space-y-3">
        <button class="w-full px-4 py-2.5 bg-white border border-[#5138ed] text-[#5138ed] text-[12px] font-bold rounded-xl hover:bg-indigo-50 transition-colors flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
          View User Guide
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
        </button>
        
        <button class="w-full px-4 py-2.5 bg-white border border-[#5138ed] text-[#5138ed] text-[12px] font-bold rounded-xl hover:bg-indigo-50 transition-colors flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
          Contact Support
        </button>
      </div>
    </div>
  </div>
</template>
