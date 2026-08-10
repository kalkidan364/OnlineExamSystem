<script setup lang="ts">
import type { Announcement } from '../types'

const props = defineProps<{
  announcements: Announcement[]
}>()

const getCategoryStyles = (category: string) => {
  switch (category) {
    case 'Urgent':
      return { bg: 'bg-rose-50 border-rose-100', iconColor: 'text-rose-500' }
    case 'Schedule':
      return { bg: 'bg-indigo-50 border-indigo-100', iconColor: 'text-indigo-500' }
    default:
      return { bg: 'bg-amber-50 border-amber-100', iconColor: 'text-amber-500' }
  }
}
</script>

<template>
  <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm h-full flex flex-col">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-base font-bold text-slate-900">Official Notices</h3>
      <button class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
        View all
      </button>
    </div>

    <div class="space-y-4 flex-1 overflow-y-auto">
      <div
        v-for="ann in announcements.slice(0, 4)"
        :key="ann.id"
        class="flex items-start gap-4 p-4 rounded-xl border border-slate-50 hover:border-slate-100 hover:bg-slate-50/50 transition-colors cursor-pointer"
      >
        <div :class="['w-10 h-10 rounded-full flex items-center justify-center border flex-shrink-0', getCategoryStyles(ann.category).bg]">
          <svg class="w-5 h-5" :class="getCategoryStyles(ann.category).iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
        </div>
        
        <div class="flex-1 min-w-0 pr-2">
          <h4 class="text-[13px] font-bold text-slate-900 truncate">{{ ann.title }}</h4>
          <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5 leading-tight">{{ ann.content }}</p>
        </div>
        
        <div class="flex-shrink-0">
          <span v-if="ann.category === 'Urgent'" class="text-[10px] font-bold px-2 py-0.5 rounded text-rose-600 bg-rose-50 border border-rose-100 uppercase tracking-widest">
            New
          </span>
          <span v-else class="text-[10px] font-medium text-slate-400 block pt-0.5">
            {{ ann.date.split(' ')[0] }} {{ ann.date.split(' ')[1] }}
          </span>
        </div>
      </div>
      
      <div v-if="announcements.length === 0" class="text-center py-6">
        <p class="text-xs text-slate-500">No official notices.</p>
      </div>
    </div>
  </div>
</template>
