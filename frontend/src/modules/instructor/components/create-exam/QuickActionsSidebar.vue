<template>
  <div>
    <h2 class="text-[14px] font-bold text-slate-800 mb-4">Quick Actions</h2>
    
    <div class="space-y-3">
      <!-- Save as Draft -->
      <button class="w-full flex items-center gap-3 px-4 py-3 bg-white border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all group">
        <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
        <span class="text-[12px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors">Save as Draft</span>
      </button>

      <!-- Preview Question -->
      <button class="w-full flex items-center gap-3 px-4 py-3 bg-white border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all group">
        <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
        <span class="text-[12px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors">Preview Question</span>
      </button>

      <!-- Clear Form -->
      <button class="w-full flex items-center gap-3 px-4 py-3 bg-white border border-slate-100 rounded-xl hover:border-rose-300 hover:shadow-sm transition-all group">
        <svg class="w-4 h-4 text-[#5138ed] group-hover:text-rose-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
        <span class="text-[12px] font-bold text-slate-700 group-hover:text-rose-500 transition-colors">Clear Form</span>
      </button>
    </div>
  </div>
</template>
