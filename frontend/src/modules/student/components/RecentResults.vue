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
  <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs">
    <!-- Table Title and Actions -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
      <div>
        <h3 class="text-lg font-bold text-slate-900">Recent Term Results</h3>
        <p class="text-xs text-slate-500 font-medium">Verified evaluations by the Department of Software Engineering</p>
      </div>
      <button
        @click="emit('download-transcript')"
        class="inline-flex items-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-3.5 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100 transition-colors active:scale-[0.99]"
      >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
        <span>View Transcript Slip</span>
      </button>
    </div>

    <!-- Responsive Table Container -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-100 text-xs font-bold uppercase tracking-wider text-slate-400">
            <th class="py-3 px-4 pl-0">Course Code & Name</th>
            <th class="py-3 px-4">Evaluation Mode</th>
            <th class="py-3 px-4 text-center">Score Ratio</th>
            <th class="py-3 px-4 text-center">Grade</th>
            <th class="py-3 px-4 text-center">Status</th>
            <th class="py-3 px-4 text-right pr-0">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="result in results" :key="result.id" class="group hover:bg-slate-50/70 transition-colors">
            <!-- Course Code & Name -->
            <td class="py-4 px-4 pl-0 max-w-[280px]">
              <p class="text-xs font-mono font-bold text-indigo-600 uppercase tracking-wide">
                {{ result.courseCode }}
              </p>
              <p class="font-bold text-slate-800 text-sm truncate mt-0.5" :title="result.courseName">
                {{ result.courseName }}
              </p>
              <p class="text-[10px] text-slate-400 font-medium mt-0.5">Completed {{ result.completedDate }}</p>
            </td>

            <!-- Evaluation Mode -->
            <td class="py-4 px-4 text-xs font-semibold text-slate-600">
              {{ result.examTitle }}
            </td>

            <!-- Score Ratio -->
            <td class="py-4 px-4 text-center">
              <span class="font-mono text-sm font-bold text-slate-800">
                {{ result.score }}
              </span>
              <span class="font-mono text-xs text-slate-400">/{{ result.totalMarks }}</span>
              <span class="block text-[10px] font-semibold text-indigo-500 font-mono mt-0.5">
                {{ result.percentage }}%
              </span>
            </td>

            <!-- Grade Badge -->
            <td class="py-4 px-4 text-center">
              <span class="inline-block rounded-md bg-indigo-50 px-2.5 py-1 font-mono text-xs font-extrabold text-indigo-700 border border-indigo-100">
                {{ result.grade }}
              </span>
            </td>

            <!-- Status Badge -->
            <td class="py-4 px-4 text-center">
              <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-100">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                Passed
              </span>
            </td>

            <!-- Actions Trigger -->
            <td class="py-4 px-4 text-right pr-0">
              <button
                @click="emit('view-result', result)"
                class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-600 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900 transition-colors"
              >
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                <span>Review Paper</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
