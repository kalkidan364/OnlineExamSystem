<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  currentStep: number
}>()

const steps = computed(() => [
  { id: 1, name: 'Exam Information', active: props.currentStep === 1, completed: props.currentStep > 1 },
  { id: 2, name: 'Add Questions', active: props.currentStep === 2, completed: props.currentStep > 2 },
  { id: 3, name: 'Exam Settings', active: props.currentStep === 3, completed: props.currentStep > 3 },
  { id: 4, name: 'Review & Publish', active: props.currentStep === 4, completed: props.currentStep > 4 },
])
</script>

<template>
  <div class="flex items-center justify-between px-2 xl:px-10 py-6 mb-2">
    <template v-for="(step, index) in steps" :key="step.id">
      
      <!-- Step Node -->
      <div class="flex items-center gap-3 relative z-10 bg-[#f8fafc] xl:bg-transparent pr-4 xl:pr-0">
        <div 
          class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold transition-colors shadow-sm"
          :class="[
            step.active ? 'bg-[#5138ed] text-white shadow-indigo-200' : 
            step.completed ? 'bg-[#5138ed] text-white shadow-indigo-200' : 
            'bg-slate-200 text-slate-500'
          ]"
        >
          <span v-if="!step.completed">{{ step.id }}</span>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <span 
          class="text-[12px] font-bold hidden md:block"
          :class="[step.active ? 'text-[#5138ed]' : 'text-slate-500']"
        >
          {{ step.name }}
        </span>
      </div>

      <!-- Connector Line -->
      <div v-if="index < steps.length - 1" class="flex-1 h-[2px] mx-2 xl:mx-4" :class="[steps[index].completed ? 'bg-[#5138ed]' : 'bg-slate-200']"></div>
      
    </template>
  </div>
</template>
