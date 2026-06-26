<script setup lang="ts">
const stats = [
  {
    title: 'Total Question Banks',
    value: '12',
    subtitle: 'All Question Banks',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>',
    colorClass: 'text-[#5138ed]',
    bgClass: 'bg-indigo-50'
  },
  {
    title: 'Total Questions',
    value: '856',
    subtitle: 'Across all banks',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    colorClass: 'text-emerald-500',
    bgClass: 'bg-emerald-50'
  },
  {
    title: 'MCQ Questions',
    value: '652',
    subtitle: '76.2% of total',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>',
    colorClass: 'text-blue-500',
    bgClass: 'bg-blue-50'
  },
  {
    title: 'Short Answer',
    value: '124',
    subtitle: '14.5% of total',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>',
    colorClass: 'text-orange-500',
    bgClass: 'bg-orange-50'
  },
  {
    title: 'Essay Questions',
    value: '80',
    subtitle: '9.3% of total',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>',
    colorClass: 'text-rose-500',
    bgClass: 'bg-rose-50'
  }
]
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-3 lg:gap-4">
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
