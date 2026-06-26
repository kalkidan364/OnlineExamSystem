<script setup lang="ts">
defineProps<{
  title: string
  value: string | number
  subtitle: string
  icon: string
  colorClass: string
  bgClass: string
}>()
</script>

<template>
  <div class="bg-white rounded-2xl p-4 lg:p-5 border border-slate-100 shadow-sm flex items-center gap-3 lg:gap-4 transition-transform hover:-translate-y-1 hover:shadow-md overflow-hidden">
    
    <!-- Icon Container -->
    <div 
      class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
      :class="bgClass"
    >
      <svg 
        class="w-6 h-6" 
        :class="colorClass"
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
        v-html="icon"
      ></svg>
    </div>

    <!-- Content -->
    <div class="flex flex-col flex-1 min-w-0">
      <h3 class="text-[12px] font-bold text-slate-500 mb-0.5 leading-tight truncate">{{ title }}</h3>
      <span class="text-2xl font-extrabold text-slate-800 leading-none">{{ value }}</span>
      <span class="text-[11px] text-slate-400 mt-1 font-medium truncate">{{ subtitle }}</span>
    </div>

  </div>
</template>
