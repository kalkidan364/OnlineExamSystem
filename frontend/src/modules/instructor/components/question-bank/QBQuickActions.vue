<script setup lang="ts">
const emit = defineEmits(['create'])

const handleImport = () => {
  alert('To import questions, please open a specific Question Bank first and click the Import button.')
}

const handleCategories = () => {
  alert('Categories are managed within individual Question Banks. Open a bank to manage its categories.')
}

const handleTemplates = () => {
  alert('Question Templates feature is coming soon!')
}
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <h2 class="text-[14px] font-bold text-slate-800 mb-6">Quick Actions</h2>
    
    <div class="grid grid-cols-2 gap-3">
      <button @click="emit('create')" class="flex flex-col items-center justify-center py-4 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all group">
        <svg class="w-5 h-5 text-[#5138ed] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed]">Create Question Bank</span>
      </button>
      
      <button @click="handleImport" class="flex flex-col items-center justify-center py-4 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all group">
        <svg class="w-5 h-5 text-[#5138ed] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed]">Import Questions</span>
      </button>
      
      <button @click="handleCategories" class="flex flex-col items-center justify-center py-4 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all group">
        <svg class="w-5 h-5 text-[#5138ed] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
        <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed]">Question Categories</span>
      </button>
      
      <button @click="handleTemplates" class="flex flex-col items-center justify-center py-4 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:shadow-sm transition-all group">
        <svg class="w-5 h-5 text-[#5138ed] mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed]">Question Templates</span>
      </button>
    </div>
  </div>
</template>
