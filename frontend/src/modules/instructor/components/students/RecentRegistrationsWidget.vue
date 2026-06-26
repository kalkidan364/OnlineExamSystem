<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-[14px] font-bold text-slate-800">Recent Registrations</h2>
      <button class="text-[11px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">
        View All
      </button>
    </div>
    
    <div class="space-y-4">
      
      <!-- Item 1 -->
      <div class="flex items-center justify-between group cursor-pointer">
        <div class="flex items-center gap-3">
          <img src="https://i.pravatar.cc/150?u=a042581f4e29026701d" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-slate-200" />
          <div>
            <span class="block text-[12px] font-bold text-slate-800 group-hover:text-[#5138ed] transition-colors">Biruk Tamirat</span>
            <span class="block text-[10px] font-medium text-slate-500 mt-0.5">Computer Science</span>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-[10px] font-medium text-slate-400">May 23, 2025</span>
          <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-[#5138ed] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </div>
      </div>

      <!-- Item 2 -->
      <div class="flex items-center justify-between group cursor-pointer">
        <div class="flex items-center gap-3">
          <img src="https://i.pravatar.cc/150?u=a042581f4e29026702d" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-slate-200" />
          <div>
            <span class="block text-[12px] font-bold text-slate-800 group-hover:text-[#5138ed] transition-colors">Meseret Abebe</span>
            <span class="block text-[10px] font-medium text-slate-500 mt-0.5">Software Engineering</span>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-[10px] font-medium text-slate-400">May 22, 2025</span>
          <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-[#5138ed] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </div>
      </div>

      <!-- Item 3 -->
      <div class="flex items-center justify-between group cursor-pointer">
        <div class="flex items-center gap-3">
          <img src="https://i.pravatar.cc/150?u=a042581f4e29026703d" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-slate-200" />
          <div>
            <span class="block text-[12px] font-bold text-slate-800 group-hover:text-[#5138ed] transition-colors">Daniel Kahsay</span>
            <span class="block text-[10px] font-medium text-slate-500 mt-0.5">Information Technology</span>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-[10px] font-medium text-slate-400">May 21, 2025</span>
          <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-[#5138ed] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </div>
      </div>

    </div>
  </div>
</template>
