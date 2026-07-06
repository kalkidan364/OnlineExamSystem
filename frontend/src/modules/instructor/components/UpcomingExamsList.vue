<script setup lang="ts">
import type { Exam } from '../store/instructorStore'

const props = defineProps<{
  exams: Exam[]
  isLoading: boolean
}>()

const getDay = (iso: string | null) => {
  if (!iso) return '—'
  return new Date(iso).toLocaleDateString('en-US', { day: '2-digit' })
}
const getMonth = (iso: string | null) => {
  if (!iso) return ''
  return new Date(iso).toLocaleDateString('en-US', { month: 'short' }).toUpperCase()
}
const getTime = (iso: string | null) => {
  if (!iso) return '—'
  return new Date(iso).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
}
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <h2 class="text-[16px] font-bold text-slate-800">Upcoming Exams</h2>
      </div>
      <button class="text-sm font-semibold text-[#5138ed] hover:text-indigo-700 transition-colors">
        View All
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="space-y-5">
      <div v-for="i in 3" :key="i" class="flex gap-4 animate-pulse">
        <div class="w-12 h-14 bg-slate-100 rounded-xl flex-shrink-0"></div>
        <div class="flex-1 space-y-2 pt-1">
          <div class="h-4 bg-slate-100 rounded w-3/4"></div>
          <div class="h-3 bg-slate-100 rounded w-1/2"></div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="exams.length === 0" class="text-center py-8 text-slate-400">
      <svg class="w-10 h-10 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      <p class="text-sm font-medium">No upcoming exams scheduled.</p>
    </div>

    <!-- List -->
    <div v-else class="space-y-5">
      <div
        v-for="exam in exams"
        :key="exam.id"
        class="flex gap-4 items-start group cursor-pointer"
      >
        <!-- Date Badge -->
        <div class="flex flex-col items-center justify-center w-12 h-14 rounded-xl border border-indigo-100 bg-white group-hover:bg-[#5138ed] group-hover:border-[#5138ed] transition-colors flex-shrink-0">
          <span class="text-lg font-extrabold text-[#5138ed] group-hover:text-white leading-none">{{ getDay(exam.scheduled_at) }}</span>
          <span class="text-[10px] font-bold text-slate-400 group-hover:text-indigo-100 mt-1 uppercase">{{ getMonth(exam.scheduled_at) }}</span>
        </div>

        <!-- Exam Info -->
        <div class="flex flex-col flex-1 pt-0.5">
          <h4 class="text-[14px] font-bold text-slate-800 group-hover:text-[#5138ed] transition-colors line-clamp-1">{{ exam.title }}</h4>
          <div class="flex items-center gap-1.5 mt-1 text-slate-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-[12px] font-medium">{{ getTime(exam.scheduled_at) }} • {{ exam.duration_minutes }}min</span>
          </div>
          <div class="flex items-center gap-1.5 mt-1 text-slate-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="text-[12px] font-medium">{{ exam.students_count ?? 0 }} students • {{ exam.course_code }}</span>
          </div>
        </div>
      </div>
    </div>

    <button class="w-full mt-6 py-2.5 rounded-xl border border-indigo-100 text-[14px] font-bold text-[#5138ed] hover:bg-[#5138ed] hover:text-white transition-colors">
      View Full Schedule
    </button>
  </div>
</template>
