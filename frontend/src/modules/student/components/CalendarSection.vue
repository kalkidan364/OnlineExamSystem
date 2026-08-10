<script setup lang="ts">
import { ref } from 'vue'
import type { CalendarEvent } from '../types'

const props = defineProps<{
  events: CalendarEvent[]
}>()

const daysInJune = Array.from({ length: 30 }, (_, i) => i + 1)
const weekDays = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']

const getEventForDay = (day: number) => {
  const formattedDate = `2026-06-${day.toString().padStart(2, '0')}`
  return props.events.find(e => e.date === formattedDate)
}

const getDayBadgeClass = (event: CalendarEvent | undefined) => {
  if (!event) return 'text-slate-700 hover:bg-slate-100'
  switch (event.type) {
    case 'Exam':
      return 'bg-purple-600 text-white font-bold ring-2 ring-purple-200'
    case 'Deadline':
      return 'bg-rose-500 text-white font-bold ring-2 ring-rose-200'
    case 'Holiday':
      return 'bg-emerald-500 text-white font-bold ring-2 ring-emerald-200'
    default:
      return 'bg-blue-500 text-white font-bold ring-2 ring-blue-200'
  }
}
</script>

<template>
  <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm h-full flex flex-col">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-base font-bold text-slate-900">Academic Calendar</h3>
      <div class="flex items-center gap-4">
        <button class="text-slate-400 hover:text-slate-800 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <span class="text-[13px] font-bold text-slate-800">June 2026</span>
        <button class="text-slate-400 hover:text-slate-800 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
      </div>
    </div>

    <!-- Calendar Grid -->
    <div class="mb-auto">
      <div class="grid grid-cols-7 gap-1 mb-2">
        <div v-for="day in weekDays" :key="day" class="text-center text-[10px] font-bold text-slate-400 py-1">
          {{ day }}
        </div>
      </div>
      
      <!-- Placeholder offsets for June 1st (assuming starts on Mon for visual) -->
      <div class="grid grid-cols-7 gap-y-2 gap-x-1">
        <div class="text-center"></div> <!-- Sun offset -->
        <div v-for="day in daysInJune" :key="day" class="flex justify-center">
          <button 
            :title="getEventForDay(day)?.title"
            :class="['w-7 h-7 flex items-center justify-center rounded-full text-[11px] transition-colors', getDayBadgeClass(getEventForDay(day))]"
          >
            {{ day }}
          </button>
        </div>
      </div>
    </div>

    <!-- Legend -->
    <div class="mt-8 flex flex-wrap items-center gap-x-4 gap-y-2 text-[10px] font-bold text-slate-500 justify-center">
      <div class="flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-purple-600"></span> Exam
      </div>
      <div class="flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Deadline
      </div>
      <div class="flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Holiday
      </div>
      <div class="flex items-center gap-1.5">
        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Event
      </div>
    </div>

  </div>
</template>
