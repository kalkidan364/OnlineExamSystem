<script setup lang="ts">
import type { UpcomingExam } from '../types'

const props = defineProps<{
  exams: UpcomingExam[]
}>()

const emit = defineEmits<{
  (e: 'start-exam', examId: string): void
  (e: 'view-details', exam: UpcomingExam): void
}>()
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="text-lg font-bold text-slate-900">Upcoming Academic Schedule</h3>
        <p class="text-xs text-slate-500 font-medium">Examinations scheduled for your registration code</p>
      </div>
      <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 border border-slate-200">
        {{ exams.length }} Scheduled
      </span>
    </div>

    <div class="grid gap-4 md:grid-cols-1 lg:grid-cols-2">
      <div
        v-for="exam in exams"
        :key="exam.id"
        :class="[
          'group relative overflow-hidden rounded-xl border bg-white p-5 transition-all hover:shadow-md hover:border-slate-300',
          exam.status === 'Ready' ? 'ring-2 ring-emerald-500/10 border-emerald-200' : 'border-slate-200'
        ]"
      >
        <!-- Corner badge indicating status -->
        <div class="flex items-center justify-between mb-3">
          <span class="font-mono text-xs font-bold text-slate-500 uppercase tracking-wider">
            {{ exam.courseCode }}
          </span>
          <span
            :class="[
              'inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-bold',
              exam.status === 'Ready'
                ? 'bg-emerald-50 text-emerald-700 border border-emerald-100'
                : exam.status === 'Pending'
                ? 'bg-amber-50 text-amber-700 border border-amber-100'
                : 'bg-slate-50 text-slate-600 border border-slate-200'
            ]"
          >
            <span :class="['h-1.5 w-1.5 rounded-full', exam.status === 'Ready' ? 'bg-emerald-500' : exam.status === 'Pending' ? 'bg-amber-500' : 'bg-slate-400']"></span>
            {{ exam.status === 'Ready' ? 'Ready to Start' : exam.status === 'Pending' ? 'In Review' : 'Scheduled' }}
          </span>
        </div>

        <!-- Course Title and Type -->
        <h4 class="font-bold text-slate-800 text-sm group-hover:text-indigo-600 transition-colors">
          {{ exam.courseName }}
        </h4>
        <p class="text-xs text-slate-500 font-medium mt-0.5">{{ exam.examType }}</p>

        <!-- Schedule Parameters -->
        <div class="mt-4 grid grid-cols-2 gap-3 text-xs text-slate-600 border-t border-slate-50 pt-3">
          <div class="flex items-center gap-1.5">
            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span class="font-medium truncate">{{ exam.scheduledDate }}</span>
          </div>
          <div class="flex items-center gap-1.5">
            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-medium truncate">{{ exam.startTime }} ({{ exam.durationMinutes }}m)</span>
          </div>
          <div class="flex items-center gap-1.5 col-span-2">
            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="font-medium">Lecturer: {{ exam.instructor }}</span>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 flex gap-2">
          <button
            v-if="exam.status === 'Ready'"
            @click="emit('start-exam', exam.id)"
            class="flex-1 rounded-lg bg-emerald-600 py-2 text-xs font-bold text-white transition-colors hover:bg-emerald-700 active:scale-[0.99]"
          >
            Start Exam
          </button>
          <button
            v-else
            disabled
            class="flex-1 rounded-lg bg-slate-100 py-2 text-xs font-bold text-slate-400 cursor-not-allowed border border-slate-200"
          >
            Locked
          </button>
          <button
            @click="emit('view-details', exam)"
            class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 transition-colors hover:bg-slate-50 active:bg-slate-100"
            title="View Guidelines"
          >
            Details
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
