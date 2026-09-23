<script setup lang="ts">
import { ref, computed } from 'vue'

const dateRange = ref('May 27, 2025 - Jun 27, 2025')
const deptFilter = ref('All Departments')
const trendFilter = ref('Last 6 Months')

// â”€â”€ KPI Cards â”€â”€
const kpis = [
  { label: 'Total Students', value: '1,248', change: 'â†‘ 12%', sub: 'vs. last semester', bg: 'bg-indigo-50', ic: 'text-indigo-500', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
  { label: 'Total Instructors', value: '86', change: 'â†‘ 8%', sub: 'vs. last semester', bg: 'bg-emerald-50', ic: 'text-emerald-500', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
  { label: 'Total Courses', value: '142', change: 'â†‘ 6%', sub: 'vs. last semester', bg: 'bg-sky-50', ic: 'text-sky-500', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
  { label: 'Total Exams', value: '468', change: 'â†‘ 15%', sub: 'vs. last semester', bg: 'bg-amber-50', ic: 'text-amber-500', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
  { label: 'Total Results Processed', value: '3,542', change: 'â†‘ 20%', sub: 'vs. last semester', bg: 'bg-rose-50', ic: 'text-rose-500', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
]

// â”€â”€ Bar Chart: Student Performance Overview â”€â”€
const departments = ['Computer Science', 'Software Engineering', 'Information Systems', 'Electrical Engineering', 'Civil Engineering']
const gradeData = [
  [320, 280, 200, 120, 80],
  [250, 310, 180, 140, 70],
  [200, 250, 220, 160, 90],
  [180, 220, 190, 150, 100],
  [150, 200, 170, 130, 110],
]
const gradeColors = ['#6366f1', '#22c55e', '#f59e0b', '#fb923c', '#f43f5e']
const gradeLabels = ['Excellent (A)', 'Good (B)', 'Average (C)', 'Pass (D)', 'Fail (F)']

const barGroupWidth = 95
const barWidth = 12
const barGap = 2
const barPaddingLeft = 32
const barChartHeight = 200
const maxBarValue = 400
const barChartWidth = barPaddingLeft + departments.length * barGroupWidth + 10
const yLabels = [0, 100, 200, 300, 400]

function getBarHeight(val: number) { return (val / maxBarValue) * barChartHeight }
function getBarX(deptIdx: number, gradeIdx: number) { return barPaddingLeft + deptIdx * barGroupWidth + gradeIdx * (barWidth + barGap) }
function getBarY(val: number) { return barChartHeight - getBarHeight(val) }

// â”€â”€ Line Chart: Exam Results Trend â”€â”€
const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
const passedData = [250, 310, 280, 400, 420, 380]
const failedData = [180, 160, 200, 150, 170, 140]
const lineChartWidth = 480
const lineChartHeight = 200
const linePaddingLeft = 40
const linePaddingRight = 20
const maxLineValue = 800
const lineYLabels = [0, 200, 400, 600, 800]

function getLineX(idx: number) {
  const usable = lineChartWidth - linePaddingLeft - linePaddingRight
  return linePaddingLeft + (idx / (months.length - 1)) * usable
}
function getLineY(val: number) { return lineChartHeight - (val / maxLineValue) * lineChartHeight }
function buildLinePath(data: number[]) { return data.map((v, i) => `${i === 0 ? 'M' : 'L'}${getLineX(i)},${getLineY(v)}`).join(' ') }
function buildFillPath(data: number[]) {
  return buildLinePath(data) + ` L${getLineX(data.length - 1)},${lineChartHeight} L${getLineX(0)},${lineChartHeight} Z`
}

const passedPath = computed(() => buildLinePath(passedData))
const failedPath = computed(() => buildLinePath(failedData))
const passedFill = computed(() => buildFillPath(passedData))
const failedFill = computed(() => buildFillPath(failedData))

// â”€â”€ Recent Reports â”€â”€
const recentReports = [
  { id: 1, name: 'Student Performance Report', type: 'Academic',    generatedBy: 'Super Admin', date: 'Jun 27, 2025 10:26 AM' },
  { id: 2, name: 'Exam Results Report',         type: 'Examination', generatedBy: 'Super Admin', date: 'Jun 26, 2025 03:17 PM' },
  { id: 3, name: 'Course Enrollment Report',    type: 'Academic',    generatedBy: 'Super Admin', date: 'Jun 25, 2025 11:42 AM' },
  { id: 4, name: 'Department Summary Report',   type: 'Academic',    generatedBy: 'Super Admin', date: 'Jun 22, 2025 09:30 AM' },
  { id: 5, name: 'Final Grade Report',          type: 'Academic',    generatedBy: 'Super Admin', date: 'Jun 20, 2025 02:15 PM' },
]

// â”€â”€ Report Categories â”€â”€
const reportCategories = [
  { title: 'Academic Reports',     desc: 'Student performance, enrollment, course statistics', bg: 'bg-indigo-50', ic: 'text-indigo-500', icon: 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z' },
  { title: 'Examination Reports',  desc: 'Exam results, pass rates, failed students', bg: 'bg-rose-50', ic: 'text-rose-500', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' },
  { title: 'Student Reports',      desc: 'Student list, registration, profile information', bg: 'bg-emerald-50', ic: 'text-emerald-500', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
  { title: 'Instructor Reports',   desc: 'Instructor workload, course assignments', bg: 'bg-sky-50', ic: 'text-sky-500', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
  { title: 'Course Reports',       desc: 'Course statistics, enrollment numbers', bg: 'bg-amber-50', ic: 'text-amber-500', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
  { title: 'Department Reports',   desc: 'Department-wise analytics, summary reports', bg: 'bg-purple-50', ic: 'text-purple-500', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
]
</script>

<template>
  <div class="space-y-6">

    <!-- Page Actions -->
    <div class="flex items-center justify-end">
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 border border-slate-200 rounded-xl px-4 py-2.5 bg-white shadow-sm cursor-pointer hover:border-indigo-300 transition-colors">
          <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <span class="text-[13px] font-semibold text-slate-600">{{ dateRange }}</span>
          <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </div>
        <button class="flex items-center gap-2 bg-[#5138ed] hover:bg-indigo-700 text-white text-[13px] font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-indigo-200 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          Generate Report
        </button>
      </div>
    </div>

    <!-- KPI Cards (5 columns) -->
    <div class="grid grid-cols-5 gap-4">
      <div v-for="kpi in kpis" :key="kpi.label" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-4 flex items-start gap-3 hover:shadow-md transition-shadow">
        <div :class="[kpi.bg, 'w-10 h-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5']">
          <svg class="w-5 h-5" :class="kpi.ic" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="kpi.icon"/>
          </svg>
        </div>
        <div class="min-w-0">
          <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide leading-tight">{{ kpi.label }}</p>
          <p class="text-[20px] font-black text-slate-800 leading-tight mt-0.5">{{ kpi.value }}</p>
          <div class="flex items-center gap-1 mt-1 flex-wrap">
            <span class="text-[11px] font-bold text-emerald-600">{{ kpi.change }}</span>
            <span class="text-[10px] text-slate-400">{{ kpi.sub }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-2 gap-6">

      <!-- Student Performance Overview (Grouped Bar Chart) -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <h3 class="text-[14px] font-bold text-slate-800">Student Performance Overview</h3>
          </div>
          <div class="relative">
            <select v-model="deptFilter" class="text-[11px] border border-slate-200 rounded-lg px-3 py-1.5 bg-white focus:outline-none text-slate-600 pr-7 appearance-none cursor-pointer">
              <option>All Departments</option>
              <option>Computer Science</option>
              <option>Software Engineering</option>
              <option>Information Systems</option>
            </select>
            <svg class="w-3 h-3 absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </div>
        </div>

        <!-- Grade Legend -->
        <div class="flex items-center gap-3 flex-wrap mb-3">
          <div v-for="(label, i) in gradeLabels" :key="label" class="flex items-center gap-1">
            <div class="w-2.5 h-2.5 rounded-sm" :style="{ backgroundColor: gradeColors[i] }"></div>
            <span class="text-[10px] text-slate-500">{{ label }}</span>
          </div>
        </div>

        <!-- Bar SVG -->
        <div class="overflow-x-auto">
          <svg :viewBox="`0 0 ${barChartWidth} ${barChartHeight + 30}`" class="w-full" style="min-width:460px">
            <!-- Grid lines + Y labels -->
            <g v-for="y in yLabels" :key="y">
              <text :x="barPaddingLeft - 5" :y="barChartHeight - (y / maxBarValue) * barChartHeight + 4" font-size="8" fill="#94a3b8" text-anchor="end">{{ y }}</text>
              <line :x1="barPaddingLeft" :y1="barChartHeight - (y / maxBarValue) * barChartHeight" :x2="barChartWidth - 5" :y2="barChartHeight - (y / maxBarValue) * barChartHeight" stroke="#f1f5f9" stroke-width="1"/>
            </g>
            <!-- Bars -->
            <g v-for="(deptData, deptIdx) in gradeData" :key="deptIdx">
              <rect v-for="(val, gradeIdx) in deptData" :key="gradeIdx"
                :x="getBarX(deptIdx, gradeIdx)"
                :y="getBarY(val)"
                :width="barWidth"
                :height="getBarHeight(val)"
                :fill="gradeColors[gradeIdx]"
                rx="2"
              />
            </g>
            <!-- X-axis labels -->
            <text v-for="(dept, idx) in departments" :key="dept"
              :x="barPaddingLeft + idx * barGroupWidth + (5 * (barWidth + barGap)) / 2"
              :y="barChartHeight + 18"
              font-size="7" fill="#94a3b8" text-anchor="middle"
            >{{ dept.length > 16 ? dept.substring(0, 15) + 'â€¦' : dept }}</text>
          </svg>
        </div>
      </div>

      <!-- Exam Results Trend (Line Chart) -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            <h3 class="text-[14px] font-bold text-slate-800">Exam Results Trend</h3>
          </div>
          <div class="relative">
            <select v-model="trendFilter" class="text-[11px] border border-slate-200 rounded-lg px-3 py-1.5 bg-white focus:outline-none text-slate-600 pr-7 appearance-none cursor-pointer">
              <option>Last 6 Months</option>
              <option>Last 3 Months</option>
              <option>Last Year</option>
            </select>
            <svg class="w-3 h-3 absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </div>
        </div>

        <!-- Legend -->
        <div class="flex items-center gap-5 mb-3">
          <div class="flex items-center gap-1.5">
            <div class="w-4 h-1.5 bg-emerald-500 rounded-full"></div>
            <span class="text-[11px] text-slate-500">Passed</span>
          </div>
          <div class="flex items-center gap-1.5">
            <div class="w-4 h-1.5 bg-rose-500 rounded-full"></div>
            <span class="text-[11px] text-slate-500">Failed</span>
          </div>
        </div>

        <!-- Line SVG -->
        <svg :viewBox="`0 0 ${lineChartWidth} ${lineChartHeight + 25}`" class="w-full">
          <defs>
            <linearGradient id="passGrad" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#22c55e" stop-opacity="0.25"/>
              <stop offset="100%" stop-color="#22c55e" stop-opacity="0.02"/>
            </linearGradient>
            <linearGradient id="failGrad" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#f43f5e" stop-opacity="0.15"/>
              <stop offset="100%" stop-color="#f43f5e" stop-opacity="0.02"/>
            </linearGradient>
          </defs>
          <!-- Grid lines + Y labels -->
          <g v-for="y in lineYLabels" :key="y">
            <text :x="linePaddingLeft - 5" :y="getLineY(y) + 4" font-size="8" fill="#94a3b8" text-anchor="end">{{ y }}</text>
            <line :x1="linePaddingLeft" :y1="getLineY(y)" :x2="lineChartWidth - linePaddingRight" :y2="getLineY(y)" stroke="#f1f5f9" stroke-width="1"/>
          </g>
          <!-- Fill areas -->
          <path :d="passedFill" fill="url(#passGrad)"/>
          <path :d="failedFill" fill="url(#failGrad)"/>
          <!-- Lines -->
          <path :d="passedPath" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          <path :d="failedPath" fill="none" stroke="#f43f5e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          <!-- Data points -->
          <circle v-for="(v, i) in passedData" :key="`p${i}`" :cx="getLineX(i)" :cy="getLineY(v)" r="4" fill="white" stroke="#22c55e" stroke-width="2"/>
          <circle v-for="(v, i) in failedData" :key="`f${i}`" :cx="getLineX(i)" :cy="getLineY(v)" r="4" fill="white" stroke="#f43f5e" stroke-width="2"/>
          <!-- X-axis month labels -->
          <text v-for="(m, i) in months" :key="m" :x="getLineX(i)" :y="lineChartHeight + 18" font-size="9" fill="#94a3b8" text-anchor="middle">{{ m }}</text>
        </svg>
      </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-2 gap-6">

      <!-- Recent Reports Table -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-[14px] font-bold text-slate-800">Recent Reports</h3>
          </div>
          <a href="#" class="text-[12px] font-bold text-[#5138ed] hover:text-indigo-800 transition-colors">View All</a>
        </div>
        <table class="w-full">
          <thead>
            <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
              <th class="pb-2.5 text-left">#</th>
              <th class="pb-2.5 text-left">Report Name</th>
              <th class="pb-2.5 text-left">Type</th>
              <th class="pb-2.5 text-left">Generated By</th>
              <th class="pb-2.5 text-left">Date</th>
              <th class="pb-2.5 text-center">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr v-for="r in recentReports" :key="r.id" class="hover:bg-slate-50/60 transition-colors">
              <td class="py-2.5 text-[12px] text-slate-400 font-semibold">{{ r.id }}</td>
              <td class="py-2.5 text-[12px] font-semibold text-slate-700 max-w-[130px] truncate pr-2">{{ r.name }}</td>
              <td class="py-2.5">
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md whitespace-nowrap"
                  :class="r.type === 'Academic' ? 'bg-indigo-50 text-indigo-600' : 'bg-rose-50 text-rose-600'">
                  {{ r.type }}
                </span>
              </td>
              <td class="py-2.5 text-[11px] text-slate-500 whitespace-nowrap">{{ r.generatedBy }}</td>
              <td class="py-2.5 text-[10px] text-slate-400 whitespace-nowrap">{{ r.date }}</td>
              <td class="py-2.5 text-center">
                <button class="flex items-center gap-1 text-[10px] font-bold text-[#5138ed] bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1.5 rounded-lg transition-colors mx-auto whitespace-nowrap">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                  </svg>
                  Download
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Report Categories -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-2 mb-5">
          <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
          </svg>
          <h3 class="text-[14px] font-bold text-slate-800">Report Categories</h3>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div v-for="cat in reportCategories" :key="cat.title"
            class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/30 cursor-pointer transition-all group">
            <div :class="[cat.bg, 'w-9 h-9 rounded-xl flex items-center justify-center shrink-0']">
              <svg class="w-4 h-4" :class="cat.ic" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="cat.icon"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[12px] font-bold text-slate-800 leading-tight">{{ cat.title }}</p>
              <p class="text-[10px] text-slate-400 mt-0.5 leading-snug">{{ cat.desc }}</p>
            </div>
            <svg class="w-4 h-4 text-slate-300 group-hover:text-indigo-500 shrink-0 mt-1 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>
