<script setup lang="ts">
const stats = [
  {
    title: 'Total Exams',
    value: '18',
    subtitle: 'All exams created',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>',
    colorClass: 'text-[#5138ed]',
    bgClass: 'bg-indigo-50'
  },
  {
    title: 'Upcoming Exams',
    value: '6',
    subtitle: 'Scheduled exams',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>',
    colorClass: 'text-emerald-500',
    bgClass: 'bg-emerald-50'
  },
  {
    title: 'Published Exams',
    value: '8',
    subtitle: 'Active and visible',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>',
    colorClass: 'text-orange-500',
    bgClass: 'bg-orange-50'
  },
  {
    title: 'Completed Exams',
    value: '10',
    subtitle: 'Finished exams',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>',
    colorClass: 'text-rose-500',
    bgClass: 'bg-rose-50'
  }
]
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
    <div 
      v-for="(stat, index) in stats" 
      :key="index"
      class="bg-white rounded-2xl p-4 xl:p-5 border border-slate-100 shadow-sm flex flex-col items-start gap-3 overflow-hidden"
    >
      <div 
        class="w-10 h-10 xl:w-12 xl:h-12 rounded-xl flex items-center justify-center flex-shrink-0"
        :class="stat.bgClass"
      >
        <svg 
          class="w-5 h-5 xl:w-6 xl:h-6" 
          :class="stat.colorClass"
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
          v-html="stat.icon"
        ></svg>
      </div>

      <div class="flex flex-col w-full min-w-0">
        <h3 class="text-[12px] xl:text-[13px] font-bold text-slate-500 mb-1 leading-tight break-words whitespace-normal">{{ stat.title }}</h3>
        <span class="text-2xl xl:text-3xl font-extrabold text-slate-800 leading-none mb-1">{{ stat.value }}</span>
        <span class="text-[11px] xl:text-[12px] text-slate-400 font-medium break-words whitespace-normal">{{ stat.subtitle }}</span>
      </div>
    </div>
  </div>
</template>
