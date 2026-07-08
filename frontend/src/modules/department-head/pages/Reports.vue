<script setup lang="ts">
import { ref } from 'vue'

const deptName = 'Computer Science'
const selectedPeriod = ref('This Semester')

const kpis = [
  { label: 'Total Exams Conducted', value: '87',     change: '+12%', trend: 'up', bg:'bg-indigo-50', ic:'text-[#5138ed]', icon:'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
  { label: 'Total Student Attempts',value: '3,412',  change: '+8%',  trend: 'up', bg:'bg-sky-50',    ic:'text-sky-500',   icon:'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
  { label: 'Department Pass Rate',  value: '84.2%',  change: '+3.1%',trend: 'up', bg:'bg-emerald-50',ic:'text-emerald-500',icon:'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
  { label: 'Average Score',         value: '76.8%',  change: '+1.5%',trend: 'up', bg:'bg-amber-50',  ic:'text-amber-500', icon:'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z' },
]

const coursePerformance = [
  { name: 'Database Systems',     code: 'CS-301', instructor: 'Dr. Abebe Kebede', passRate: 92, avgScore: 84 },
  { name: 'Software Engineering', code: 'CS-401', instructor: 'Prof. Yonas Tadesse', passRate: 88, avgScore: 81 },
  { name: 'Algorithms',           code: 'CS-202', instructor: 'Dr. Meron Bekele', passRate: 76, avgScore: 71 },
  { name: 'Operating Systems',    code: 'CS-302', instructor: 'Dr. Samuel Getachew', passRate: 68, avgScore: 65 },
]

// SVG chart
const chartPoints = [[0,160],[50,140],[100,150],[150,120],[200,130],[250,90],[300,100],[350,80],[400,90],[450,70],[500,85],[550,60]]
const svgLine = chartPoints.map((p,i) => `${i===0?'M':'L'}${p[0]},${p[1]}`).join(' ')
const svgFill = svgLine + ` L${chartPoints[chartPoints.length-1][0]},200 L0,200 Z`

</script>

<template>
  <div class="space-y-6">

    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Department Analytics</h1>
        <p class="text-[13px] text-slate-500 mt-1">Detailed performance metrics for {{ deptName }}.</p>
      </div>
      <div class="flex items-center gap-3">
        <select v-model="selectedPeriod" class="text-[13px] border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white">
          <option>Last 30 Days</option>
          <option>This Semester</option>
          <option>Last Semester</option>
          <option>This Year</option>
        </select>
        <button class="flex items-center gap-2 bg-[#5138ed] hover:bg-indigo-700 text-white text-[13px] font-bold px-4 py-2.5 rounded-xl transition-all shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
          Export
        </button>
      </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-4 gap-4">
      <div v-for="kpi in kpis" :key="kpi.label" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div :class="[kpi.bg, 'w-11 h-11 rounded-xl flex items-center justify-center shrink-0']">
          <svg class="w-5 h-5" :class="kpi.ic" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="kpi.icon"></path></svg>
        </div>
        <div>
          <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">{{ kpi.label }}</p>
          <div class="flex items-center gap-2 mt-0.5">
            <span class="text-[20px] font-bold text-slate-800">{{ kpi.value }}</span>
            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md">{{ kpi.change }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
      <!-- Trend Chart -->
      <div class="col-span-2 bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h3 class="text-[15px] font-bold text-slate-800">Exam Performance Trend</h3>
            <p class="text-[12px] text-slate-400 mt-0.5">Average scores across all {{ deptName }} courses</p>
          </div>
        </div>
        <div class="relative">
          <svg viewBox="0 0 550 200" class="w-full h-48" preserveAspectRatio="none">
            <defs>
              <linearGradient id="rGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#5138ed" stop-opacity="0.15"/>
                <stop offset="100%" stop-color="#5138ed" stop-opacity="0"/>
              </linearGradient>
            </defs>
            <path :d="svgFill" fill="url(#rGrad)"/>
            <path :d="svgLine" fill="none" stroke="#5138ed" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            <circle v-for="(p, i) in chartPoints" :key="i" :cx="p[0]" :cy="p[1]" r="3" fill="white" stroke="#5138ed" stroke-width="2"/>
          </svg>
          <div class="flex justify-between mt-2 px-2">
            <span v-for="l in ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']" :key="l" class="text-[10px] text-slate-400 font-medium">{{ l }}</span>
          </div>
        </div>
      </div>

      <!-- Course Breakdown -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <h3 class="text-[15px] font-bold text-slate-800 mb-5">Course Performance</h3>
        <div class="space-y-4">
          <div v-for="c in coursePerformance" :key="c.code">
            <div class="flex items-center justify-between mb-1.5">
              <span class="text-[12px] font-bold text-slate-700 truncate max-w-[140px]" :title="c.name">{{ c.name }}</span>
              <span class="text-[12px] font-bold text-slate-800">{{ c.passRate }}%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2">
              <div :class="[c.passRate >= 80 ? 'bg-emerald-500' : c.passRate >= 70 ? 'bg-[#5138ed]' : 'bg-amber-500', 'h-2 rounded-full transition-all']" :style="{ width: c.passRate + '%' }"></div>
            </div>
            <div class="flex items-center justify-between mt-1">
              <span class="text-[10px] font-mono font-bold text-slate-400">{{ c.code }}</span>
              <span class="text-[10px] text-slate-500">Instructor: <span class="font-bold">{{ c.instructor }}</span></span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
