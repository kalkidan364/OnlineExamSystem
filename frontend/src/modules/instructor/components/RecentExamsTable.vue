<script setup lang="ts">
import type { Exam } from '../store/instructorStore'

const props = defineProps<{
  exams: Exam[]
  isLoading: boolean
}>()

const statusConfig: Record<string, { label: string; class: string }> = {
  draft:     { label: 'Draft',     class: 'bg-slate-100 text-slate-500' },
  published: { label: 'Published', class: 'bg-blue-50 text-blue-600' },
  scheduled: { label: 'Scheduled', class: 'bg-indigo-50 text-[#5138ed]' },
  completed: { label: 'Completed', class: 'bg-emerald-50 text-emerald-600' },
}

const formatDate = (iso: string | null) => {
  if (!iso) return '—'
  return new Date(iso).toLocaleDateString('en-US', {
    month: 'short', day: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h2 class="text-[16px] font-bold text-slate-800">Recent Exams</h2>
      </div>
      <button class="text-sm font-semibold text-[#5138ed] bg-indigo-50 px-4 py-1.5 rounded-lg hover:bg-indigo-100 transition-colors">
        View All
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="space-y-4">
      <div v-for="i in 4" :key="i" class="flex gap-4 animate-pulse">
        <div class="h-4 bg-slate-100 rounded flex-1"></div>
        <div class="h-4 bg-slate-100 rounded w-32"></div>
        <div class="h-4 bg-slate-100 rounded w-28"></div>
        <div class="h-4 bg-slate-100 rounded w-20"></div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="exams.length === 0" class="text-center py-12 text-slate-400">
      <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <p class="font-medium">No exams created yet.</p>
    </div>

    <!-- Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-100 text-[13px] font-bold text-slate-400 uppercase tracking-wide">
            <th class="pb-4 pr-4 font-semibold">Exam Title</th>
            <th class="pb-4 px-4 font-semibold">Course</th>
            <th class="pb-4 px-4 font-semibold">Scheduled</th>
            <th class="pb-4 px-4 font-semibold">Students</th>
            <th class="pb-4 px-4 font-semibold">Status</th>
            <th class="pb-4 pl-4 text-center font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="exam in exams"
            :key="exam.id"
            class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors last:border-0 group"
          >
            <td class="py-4 pr-4 text-[14px] font-semibold text-slate-700">{{ exam.title }}</td>
            <td class="py-4 px-4 text-[14px] text-slate-500 font-medium">{{ exam.course_code }}</td>
            <td class="py-4 px-4 text-[14px] text-slate-500 font-medium">{{ formatDate(exam.scheduled_at) }}</td>
            <td class="py-4 px-4 text-[14px] text-slate-600 font-semibold">{{ exam.students_count ?? 0 }}</td>
            <td class="py-4 px-4">
              <span
                class="px-3 py-1 text-[12px] font-bold rounded-full"
                :class="statusConfig[exam.status]?.class || 'bg-slate-100 text-slate-500'"
              >
                {{ statusConfig[exam.status]?.label || exam.status }}
              </span>
            </td>
            <td class="py-4 pl-4 text-center">
              <button class="text-slate-400 hover:text-[#5138ed] transition-colors p-1 rounded hover:bg-indigo-50">
                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
