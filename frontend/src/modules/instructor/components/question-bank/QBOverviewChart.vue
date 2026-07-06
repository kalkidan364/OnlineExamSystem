<script setup lang="ts">
import { computed } from 'vue'
import { Doughnut } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const props = defineProps<{
  stats: any
}>()

const chartData = computed(() => {
  return {
    labels: ['MCQ Questions', 'Short Answer', 'Essay Questions'],
    datasets: [
      {
        backgroundColor: ['#5138ed', '#10b981', '#f97316'],
        data: [props.stats.mcq_questions, props.stats.sa_questions, props.stats.essay_questions],
        borderWidth: 0,
        hoverOffset: 4
      }
    ]
  }
})

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '75%', // Creates the doughnut hole
  plugins: {
    legend: {
      display: false // We will build a custom HTML legend instead
    },
    tooltip: {
      backgroundColor: '#1e293b',
      padding: 12,
      bodyFont: { size: 13, weight: 'bold' as const },
      displayColors: false,
      callbacks: {
        label: function(context: any) {
          return `${context.label}: ${context.raw}`;
        }
      }
    }
  }
}
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <h2 class="text-[14px] font-bold text-slate-800 mb-6">Question Bank Overview</h2>
    
    <div class="flex flex-col items-center">
      <!-- Chart Container with Absolute Center Text -->
      <div class="relative w-48 h-48 mb-6">
        <Doughnut :data="chartData" :options="chartOptions" />
        
        <!-- Center Text -->
        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
          <span class="text-3xl font-extrabold text-slate-800">{{ stats.total_questions }}</span>
          <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wide">Total Questions</span>
        </div>
      </div>

      <!-- Custom Legend -->
      <div class="w-full space-y-4">
        <div class="flex items-start gap-3">
          <div class="w-2.5 h-2.5 rounded-full bg-[#5138ed] mt-1 flex-shrink-0"></div>
          <div class="flex flex-col">
            <span class="text-[13px] font-bold text-slate-700">MCQ Questions</span>
            <span class="text-[12px] text-slate-500">{{ stats.mcq_questions }} ({{ stats.total_questions ? Math.round((stats.mcq_questions / stats.total_questions) * 100) : 0 }}%)</span>
          </div>
        </div>
        
        <div class="flex items-start gap-3">
          <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-1 flex-shrink-0"></div>
          <div class="flex flex-col">
            <span class="text-[13px] font-bold text-slate-700">Short Answer</span>
            <span class="text-[12px] text-slate-500">{{ stats.sa_questions }} ({{ stats.total_questions ? Math.round((stats.sa_questions / stats.total_questions) * 100) : 0 }}%)</span>
          </div>
        </div>

        <div class="flex items-start gap-3">
          <div class="w-2.5 h-2.5 rounded-full bg-orange-500 mt-1 flex-shrink-0"></div>
          <div class="flex flex-col">
            <span class="text-[13px] font-bold text-slate-700">Essay Questions</span>
            <span class="text-[12px] text-slate-500">{{ stats.essay_questions }} ({{ stats.total_questions ? Math.round((stats.essay_questions / stats.total_questions) * 100) : 0 }}%)</span>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</template>
