<script setup lang="ts">
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
)

const chartData = {
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
  datasets: [
    {
      label: 'Average Score',
      backgroundColor: (context: any) => {
        const ctx = context.chart.ctx;
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(81, 56, 237, 0.2)'); // Top color (lighter purple)
        gradient.addColorStop(1, 'rgba(81, 56, 237, 0)'); // Bottom color (transparent)
        return gradient;
      },
      borderColor: '#5138ed',
      pointBackgroundColor: '#ffffff',
      pointBorderColor: '#5138ed',
      pointBorderWidth: 2,
      pointRadius: 4,
      pointHoverRadius: 6,
      borderWidth: 2,
      tension: 0.4, // smooth curves
      fill: true,
      data: [30, 55, 43, 67, 49, 72.4, 55] // mock data matching shape
    }
  ]
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      backgroundColor: '#1e293b',
      padding: 12,
      titleFont: { size: 13 },
      bodyFont: { size: 14, weight: 'bold' as const },
      displayColors: false,
      callbacks: {
        label: function(context: any) {
          return context.parsed.y + '%';
        }
      }
    }
  },
  scales: {
    y: {
      min: 0,
      max: 100,
      ticks: {
        stepSize: 25,
        callback: function(value: any) {
          return value + '%';
        },
        color: '#94a3b8',
        font: { size: 11 }
      },
      border: { display: false },
      grid: {
        color: '#f1f5f9',
        drawTicks: false
      }
    },
    x: {
      grid: {
        display: false
      },
      ticks: {
        color: '#94a3b8',
        font: { size: 12 }
      },
      border: { display: false }
    }
  },
  interaction: {
    intersect: false,
    mode: 'index' as const,
  },
}
</script>

<template>
  <div class="h-[280px] w-full mt-4">
    <Line :data="chartData" :options="chartOptions" />
  </div>
</template>
