<script setup lang="ts">
import { ref, watch } from 'vue'
import type { CalendarEvent } from '../types'

const props = defineProps<{
  events: CalendarEvent[]
}>()

const selectedEvent = ref<CalendarEvent | null>(props.events[0] || null)

// Update selected if events change
watch(() => props.events, (newEvents) => {
  if (newEvents && newEvents.length > 0 && !selectedEvent.value) {
    selectedEvent.value = newEvents[0]
  }
})

const daysInJune = Array.from({ length: 30 }, (_, i) => i + 1)

const getEventForDay = (day: number) => {
  const formattedDate = `2026-06-${day.toString().padStart(2, '0')}`
  return props.events.find(e => e.date === formattedDate)
}

const getDayBadgeClass = (event: CalendarEvent | undefined) => {
  if (!event) return ''
  switch (event.type) {
    case 'Exam':
      return 'bg-indigo-600 text-white font-bold ring-2 ring-indigo-500/20'
    case 'Deadline':
      return 'bg-rose-500 text-white font-bold ring-2 ring-rose-500/20'
    case 'Holiday':
      return 'bg-slate-400 text-white font-bold ring-2 ring-slate-300/20'
    default:
      return 'bg-indigo-500 text-white'
  }
}
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs">
    <div class="flex items-center justify-between mb-5">
      <div>
        <h3 class="text-lg font-bold text-slate-900">Academic Calendar</h3>
        <p class="text-xs text-slate-500 font-medium">Critical dates and deadlines for this semester</p>
      </div>
      <div class="flex items-center gap-1">
        <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
      </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
      <!-- Visual Monthly Calendar Grid -->
      <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
        <div class="flex items-center justify-between mb-4">
          <span class="text-xs font-extrabold uppercase tracking-widest text-indigo-600 font-sans">
            June 2026
          </span>
          <div class="flex items-center gap-1.5 text-slate-400">
            <svg class="h-4 w-4 cursor-pointer hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <svg class="h-4 w-4 cursor-pointer hover:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </div>
        </div>

        <!-- Days of Week -->
        <div class="grid grid-cols-7 text-center gap-y-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">
          <span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span>
        </div>

        <!-- June 2026 starts on Mon, so no leading blank days! -->
        <div class="grid grid-cols-7 text-center gap-1 text-xs">
          <button
            v-for="day in daysInJune"
            :key="day"
            @click="() => { const ev = getEventForDay(day); if (ev) selectedEvent = ev }"
            :class="[
              'flex h-8 w-full items-center justify-center rounded-lg transition-all focus:outline-none',
              getEventForDay(day)
                ? `${getDayBadgeClass(getEventForDay(day))} cursor-pointer scale-105 shadow-xs`
                : 'text-slate-600 hover:bg-slate-100',
              selectedEvent && selectedEvent.date === `2026-06-${day.toString().padStart(2, '0')}` ? 'ring-2 ring-indigo-500 ring-offset-2' : '',
              day === 26 ? 'font-black underline' : ''
            ]"
            :title="getEventForDay(day) ? `${getEventForDay(day)?.title} (${getEventForDay(day)?.type})` : `June ${day}`"
          >
            {{ day }}
          </button>
        </div>

        <!-- Legend -->
        <div class="mt-4 flex flex-wrap justify-center gap-3 text-[10px] font-semibold text-slate-500 border-t border-slate-100 pt-3">
          <div class="flex items-center gap-1">
            <span class="h-2 w-2 rounded-full bg-indigo-600"></span>
            <span>Exam</span>
          </div>
          <div class="flex items-center gap-1">
            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
            <span>Deadline</span>
          </div>
          <div class="flex items-center gap-1">
            <span class="h-2 w-2 rounded-full bg-slate-400"></span>
            <span>Holiday</span>
          </div>
        </div>
      </div>

      <!-- Selected Date Information details -->
      <div class="flex flex-col justify-between p-2">
        <div v-if="selectedEvent" class="space-y-4">
          <div class="inline-flex items-center gap-2 rounded-md bg-indigo-50 px-2 py-1 text-[10px] font-bold text-indigo-700 border border-indigo-100 uppercase tracking-widest">
            {{ selectedEvent.type }} Schedule
          </div>
          <div>
            <h4 class="text-base font-bold text-slate-900">{{ selectedEvent.title }}</h4>
            <p class="text-xs font-medium text-indigo-500 mt-1">Date: {{ selectedEvent.date }}</p>
          </div>
          <p class="text-xs text-slate-600 leading-relaxed font-sans bg-slate-50 p-3 rounded-lg border border-slate-100">
            {{ selectedEvent.description }}
          </p>
        </div>
        <div v-else class="flex h-full flex-col items-center justify-center text-center p-6 text-slate-400 border border-dashed border-slate-200 rounded-xl">
          <svg class="h-8 w-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          <p class="text-xs font-medium">Select a colored calendar date to view the active event details.</p>
        </div>

        <!-- Prompt warning -->
        <div class="rounded-lg bg-amber-50 p-3 border border-amber-100 text-[11px] text-amber-800 font-medium mt-4">
          Verify all deadlines with your academic coordinator. Schedule is subject to change based on university senate updates.
        </div>
      </div>
    </div>
  </div>
</template>
