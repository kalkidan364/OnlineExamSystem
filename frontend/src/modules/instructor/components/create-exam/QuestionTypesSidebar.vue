<script setup lang="ts">
const types = [
  { id: 'MCQ', name: 'Multiple Choice Question', abbr: 'MCQ', color: 'bg-indigo-50 text-[#5138ed]' },
  { id: 'SA', name: 'Short Answer Question', abbr: 'SA', color: 'bg-amber-50 text-amber-500' },
  { id: 'Essay', name: 'Essay Question', abbr: 'Essay', color: 'bg-rose-50 text-rose-500', isIcon: true },
  { id: 'TF', name: 'True/False Question', abbr: 'TF', color: 'bg-emerald-50 text-emerald-500' },
  { id: 'MTF', name: 'Multiple True/False', abbr: 'MTF', color: 'bg-emerald-50 text-emerald-500' },
  { id: 'FIB', name: 'Fill in the Blank', abbr: 'FIB', color: 'bg-cyan-50 text-cyan-500' },
  { id: 'MS', name: 'Matching Question', abbr: 'MS', color: 'bg-pink-50 text-pink-500' },
]
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm mb-6">
    <h2 class="text-[14px] font-bold text-slate-800 mb-4">Question Types</h2>
    
    <div class="space-y-4">
      <div v-for="type in types" :key="type.id" class="flex items-center gap-3 group cursor-pointer">
        <div 
          class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-[10px] font-bold transition-transform group-hover:scale-105"
          :class="type.color"
        >
          <template v-if="type.isIcon">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          </template>
          <template v-else>
            {{ type.abbr }}
          </template>
        </div>
        <div class="flex flex-col">
          <span class="text-[13px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors">{{ type.abbr }}</span>
          <span class="text-[11px] text-slate-400 font-medium">{{ type.name }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
