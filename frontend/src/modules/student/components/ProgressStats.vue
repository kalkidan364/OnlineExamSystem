<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  completedCount: number
  remainingCount: number
  averageScore: number
  passRate: number
}>()

const radius = 30
const circumference = 2 * Math.PI * radius

const averageScoreDashoffset = computed(() => {
  return circumference - (props.averageScore / 100) * circumference
})

const passRateDashoffset = computed(() => {
  return circumference - (props.passRate / 100) * circumference
})
</script>

<template>
  <div class="space-y-4">
    <div>
      <h3 class="text-lg font-bold text-slate-900">Academic Progress Overview</h3>
      <p class="text-xs text-slate-500 font-medium">Real-time status of your term performance</p>
    </div>

    <div class="grid gap-4 grid-cols-2 md:grid-cols-4">
      <!-- Card 1: Completed Exams -->
      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Completed</span>
          <div class="rounded-lg bg-indigo-50 p-1.5 text-indigo-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
        </div>
        <div class="mt-3 flex items-baseline gap-1.5">
          <span class="text-2xl font-black text-slate-900">{{ completedCount }}</span>
          <span class="text-xs font-medium text-slate-400">Courses done</span>
        </div>
        <div class="mt-2 text-[10px] font-semibold text-emerald-600">100% submission rating</div>
      </div>

      <!-- Card 2: Remaining Exams -->
      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Remaining</span>
          <div class="rounded-lg bg-amber-50 p-1.5 text-amber-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v4M12 15h2"></path></svg>
          </div>
        </div>
        <div class="mt-3 flex items-baseline gap-1.5">
          <span class="text-2xl font-black text-slate-900">{{ remainingCount }}</span>
          <span class="text-xs font-medium text-slate-400">Scheduled papers</span>
        </div>
        <div class="mt-2 text-[10px] font-semibold text-indigo-600">Active preparation stage</div>
      </div>

      <!-- Card 3: Average Score -->
      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs hover:shadow-md transition-shadow flex items-center justify-between gap-2">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Grade Point</span>
          <div class="mt-2 flex items-baseline gap-1.5">
            <span class="text-2xl font-black text-slate-900">{{ averageScore }}%</span>
          </div>
          <span class="text-[10px] font-semibold text-emerald-600 block mt-1">Excellent (A Grade Average)</span>
        </div>
        <!-- Circular Indicator for Average Score -->
        <div class="relative flex h-16 w-16 items-center justify-center">
          <svg class="absolute top-0 left-0 h-16 w-16 -rotate-90">
            <circle
              cx="32"
              cy="32"
              :r="radius"
              class="text-slate-100 stroke-current"
              stroke-width="5"
              fill="transparent"
            />
            <circle
              cx="32"
              cy="32"
              :r="radius"
              class="text-indigo-600 stroke-current transition-all duration-500"
              stroke-width="5"
              :stroke-dasharray="circumference"
              :stroke-dashoffset="averageScoreDashoffset"
              stroke-linecap="round"
              fill="transparent"
            />
          </svg>
          <span class="font-mono text-xs font-bold text-slate-800">{{ averageScore }}%</span>
        </div>
      </div>

      <!-- Card 4: Pass Rate -->
      <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs hover:shadow-md transition-shadow flex items-center justify-between gap-2">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Pass Rate</span>
          <div class="mt-2 flex items-baseline gap-1.5">
            <span class="text-2xl font-black text-slate-900">{{ passRate }}%</span>
          </div>
          <span class="text-[10px] font-semibold text-emerald-600 block mt-1">Safe Zone (No failures)</span>
        </div>
        <!-- Circular Indicator for Pass Rate -->
        <div class="relative flex h-16 w-16 items-center justify-center">
          <svg class="absolute top-0 left-0 h-16 w-16 -rotate-90">
            <circle
              cx="32"
              cy="32"
              :r="radius"
              class="text-slate-100 stroke-current"
              stroke-width="5"
              fill="transparent"
            />
            <circle
              cx="32"
              cy="32"
              :r="radius"
              class="text-emerald-500 stroke-current transition-all duration-500"
              stroke-width="5"
              :stroke-dasharray="circumference"
              :stroke-dashoffset="passRateDashoffset"
              stroke-linecap="round"
              fill="transparent"
            />
          </svg>
          <span class="font-mono text-xs font-bold text-slate-800">{{ passRate }}%</span>
        </div>
      </div>
    </div>
  </div>
</template>
