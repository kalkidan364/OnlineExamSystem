<script setup lang="ts">
import { Doughnut } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const chartData = {
  labels: ['Upcoming', 'Published', 'Completed', 'Drafts'],
  datasets: [
    {
      backgroundColor: ['#5138ed', '#10b981', '#f97316', '#94a3b8'],
      data: [6, 8, 10, 4],
      borderWidth: 0,
      hoverOffset: 4
    }
  ]
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '75%',
  plugins: {
    legend: {
      display: false
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
  <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
    <div class="flex items-center gap-2 mb-6">
      <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
      <h2 class="text-[13px] font-bold text-slate-800">Exam Overview</h2>
    </div>
    
    <div class="flex flex-col items-center">
      <!-- Chart Container with Absolute Center Text -->
      <div class="relative w-40 h-40 mb-6">
        <Doughnut :data="chartData" :options="chartOptions" />
        
        <!-- Center Text -->
        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
          <span class="text-2xl font-extrabold text-slate-800">18</span>
          <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Total Exams</span>
        </div>
      </div>

      <!-- Custom Legend -->
      <div class="w-full space-y-3 pl-2">
        <div class="flex items-start gap-3">
          <div class="w-2.5 h-2.5 rounded-full bg-[#5138ed] mt-1 flex-shrink-0"></div>
          <div class="flex flex-col">
            <span class="text-[12px] font-bold text-slate-700">Upcoming</span>
            <span class="text-[11px] text-slate-500">6 (33.3%)</span>
          </div>
        </div>
        
        <div class="flex items-start gap-3">
          <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-1 flex-shrink-0"></div>
          <div class="flex flex-col">
            <span class="text-[12px] font-bold text-slate-700">Published</span>
            <span class="text-[11px] text-slate-500">8 (44.4%)</span>
          </div>
        </div>

        <div class="flex items-start gap-3">
          <div class="w-2.5 h-2.5 rounded-full bg-orange-500 mt-1 flex-shrink-0"></div>
          <div class="flex flex-col">
            <span class="text-[12px] font-bold text-slate-700">Completed</span>
            <span class="text-[11px] text-slate-500">10 (55.6%)</span>
          </div>
        </div>
        
        <div class="flex items-start gap-3">
          <div class="w-2.5 h-2.5 rounded-full bg-slate-400 mt-1 flex-shrink-0"></div>
          <div class="flex flex-col">
            <span class="text-[12px] font-bold text-slate-700">Drafts</span>
            <span class="text-[11px] text-slate-500">4 (22.2%)</span>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</template>
