<script setup lang="ts">
import type { RecentResult } from '../types'

const props = defineProps<{
  results: RecentResult[]
}>()

const emit = defineEmits<{
  (e: 'view-result', result: RecentResult): void
  (e: 'download-transcript'): void
}>()
</script>

<template>
  <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm h-full flex flex-col">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-base font-bold text-slate-900">Latest Results</h3>
      <button 
        @click="emit('download-transcript')"
        class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-700 transition-colors"
      >
        View all
      </button>
    </div>

    <div class="space-y-4 flex-1 overflow-y-auto">
      <div
        v-for="result in results.slice(0, 4)"
        :key="result.id"
        class="flex items-center justify-between p-4 rounded-xl border border-slate-50 hover:border-slate-100 hover:bg-slate-50/50 transition-colors cursor-pointer"
        @click="emit('view-result', result)"
      >
        <div class="flex items-center gap-4">
          <div class="w-10 h-10 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          </div>
          <div>
            <h4 class="text-[13px] font-bold text-slate-900 truncate">{{ result.courseName }}</h4>
            <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ result.courseCode }} - {{ result.examTitle }}</p>
          </div>
        </div>
        
        <div class="text-right">
          <div class="flex items-baseline justify-end gap-1">
            <span class="text-[15px] font-black text-slate-900">{{ result.score }}</span>
            <span class="text-[10px] font-bold text-slate-400">/ {{ result.totalMarks }}</span>
          </div>
          <span 
            :class="[
              'text-[10px] font-bold px-2 py-0.5 rounded flex items-center gap-1 mt-1 justify-end',
              result.percentage >= 50 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50'
            ]"
          >
            {{ result.percentage >= 50 ? 'Passed' : 'Failed' }}
          </span>
        </div>
      </div>

      <div v-if="results.length === 0" class="text-center py-6">
        <p class="text-xs text-slate-500">No recent results found.</p>
      </div>
    </div>
  </div>
</template>
