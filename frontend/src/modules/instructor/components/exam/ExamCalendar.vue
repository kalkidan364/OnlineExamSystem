<script setup lang="ts">
const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
// A simple static layout for May 2025 as shown in the design
const calendarDays = [
  { date: 27, currentMonth: false }, { date: 28, currentMonth: false }, { date: 29, currentMonth: false }, { date: 30, currentMonth: false }, { date: 1, currentMonth: true }, { date: 2, currentMonth: true }, { date: 3, currentMonth: true },
  { date: 4, currentMonth: true }, { date: 5, currentMonth: true }, { date: 6, currentMonth: true }, { date: 7, currentMonth: true }, { date: 8, currentMonth: true }, { date: 9, currentMonth: true }, { date: 10, currentMonth: true },
  { date: 11, currentMonth: true, hasEvent: true }, { date: 12, currentMonth: true }, { date: 13, currentMonth: true }, { date: 14, currentMonth: true }, { date: 15, currentMonth: true }, { date: 16, currentMonth: true }, { date: 17, currentMonth: true },
  { date: 18, currentMonth: true, hasEvent: true }, { date: 19, currentMonth: true }, { date: 20, currentMonth: true }, { date: 21, currentMonth: true }, { date: 22, currentMonth: true }, { date: 23, currentMonth: true }, { date: 24, currentMonth: true },
  { date: 25, currentMonth: true, selected: true }, { date: 26, currentMonth: true }, { date: 27, currentMonth: true }, { date: 28, currentMonth: true }, { date: 29, currentMonth: true }, { date: 30, currentMonth: true }, { date: 31, currentMonth: true }
]
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
    <div class="flex items-center gap-2 mb-4">
      <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
      <h2 class="text-[13px] font-bold text-slate-800">Exam Calendar</h2>
    </div>

    <!-- Header Month Navigation -->
    <div class="flex items-center justify-between mb-4">
      <button class="p-1 hover:bg-slate-50 rounded-lg text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
      </button>
      <span class="text-[13px] font-bold text-slate-800">May 2025</span>
      <button class="p-1 hover:bg-slate-50 rounded-lg text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
      </button>
    </div>

    <!-- Days of Week -->
    <div class="grid grid-cols-7 gap-1 mb-2">
      <div v-for="day in daysOfWeek" :key="day" class="text-center text-[10px] font-bold text-slate-400 py-1">
        {{ day }}
      </div>
    </div>

    <!-- Calendar Grid -->
    <div class="grid grid-cols-7 gap-1">
      <div 
        v-for="(day, index) in calendarDays" 
        :key="index"
        class="flex flex-col items-center justify-center h-8 rounded-lg text-[11px] font-medium relative cursor-pointer transition-colors"
        :class="[
          !day.currentMonth ? 'text-slate-300' : 'text-slate-600',
          day.selected ? 'bg-[#5138ed] text-white shadow-sm shadow-indigo-200' : 'hover:bg-slate-50'
        ]"
      >
        <span>{{ day.date }}</span>
        <div v-if="day.hasEvent" class="absolute bottom-1 w-1 h-1 rounded-full bg-[#5138ed]"></div>
      </div>
    </div>
  </div>
</template>
