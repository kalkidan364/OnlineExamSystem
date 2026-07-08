<script setup lang="ts">
import { ref } from 'vue'

const selectedPeriod = ref('Last 30 Days')

// ── Summary KPIs ──
const kpis = [
  { label: 'Total Exams Conducted', value: '342',    change: '+10.1%', trend: 'up', bg:'bg-indigo-50', ic:'text-[#5138ed]', icon:'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
  { label: 'Total Attempts',        value: '1,248',  change: '+16.8%', trend: 'up', bg:'bg-sky-50',    ic:'text-sky-500',   icon:'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
  { label: 'Overall Pass Rate',     value: '72.4%',  change: '+5.6%',  trend: 'up', bg:'bg-emerald-50',ic:'text-emerald-500',icon:'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
  { label: 'Average Score',         value: '68.7%',  change: '+3.2%',  trend: 'up', bg:'bg-amber-50',  ic:'text-amber-500', icon:'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z' },
]

// ── Top Performing Courses ──
const topCourses = [
  { name: 'Software Engineering', code:'CS-401', passRate: 91, avgScore: 84, exams: 24 },
  { name: 'Database Systems',     code:'CS-301', passRate: 82, avgScore: 79, exams: 18 },
  { name: 'Algorithms',           code:'CS-202', passRate: 79, avgScore: 76, exams: 21 },
  { name: 'Web Development',      code:'IS-302', passRate: 74, avgScore: 71, exams: 19 },
  { name: 'Data Structures',      code:'IS-201', passRate: 78, avgScore: 73, exams: 15 },
]

// ── Department Breakdown ──
const deptStats = [
  { dept: 'Computer Science',    exams: 63, students: 441, passRate: 84, color: 'bg-[#5138ed]' },
  { dept: 'Information Systems', exams: 34, students: 238, passRate: 76, color: 'bg-sky-500'   },
  { dept: 'Mathematics',         exams: 23, students: 185, passRate: 71, color: 'bg-amber-500' },
  { dept: 'Engineering',         exams: 22, students: 173, passRate: 68, color: 'bg-emerald-500'},
  { dept: 'Physics',             exams: 7,  students: 65,  passRate: 62, color: 'bg-rose-400'  },
]

// ── Recent Results ──
const recentResults = [
  { student: 'Berhane Tesfaye', exam: 'Software Engineering Final', score: 88, status: 'passed'  },
  { student: 'Dawit Mengistu',  exam: 'Database Systems Midterm',   score: 74, status: 'passed'  },
  { student: 'Selam Bekele',    exam: 'Data Structures Quiz 3',     score: 56, status: 'failed'  },
  { student: 'Abel Habtamu',    exam: 'Algorithms Final Exam',      score: 91, status: 'passed'  },
  { student: 'Hana Wolde',      exam: 'Web Development Quiz 2',     score: 48, status: 'failed'  },
]

// SVG chart
const chartPoints = [[0,160],[50,130],[100,150],[150,110],[200,130],[250,100],[300,120],[350,90],[400,110],[450,80],[500,100],[550,75]]
const svgLine = chartPoints.map((p,i) => `${i===0?'M':'L'}${p[0]},${p[1]}`).join(' ')
const svgFill = svgLine + ` L${chartPoints[chartPoints.length-1][0]},200 L0,200 Z`

const scoreColor = (n: number) => n >= 80 ? 'text-emerald-600' : n >= 60 ? 'text-amber-600' : 'text-rose-600'
const scoreBadge = (s: string)  => s === 'passed' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-600'
</script>

<template>
  <div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">System Reports</h1>
        <p class="text-[13px] text-slate-500 mt-1">Comprehensive analytics across all exams, departments, and students.</p>
      </div>
      <div class="flex items-center gap-3">
        <select v-model="selectedPeriod" class="text-[13px] border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white shadow-sm">
          <option>Last 7 Days</option>
          <option>Last 30 Days</option>
          <option>Last 3 Months</option>
          <option>This Semester</option>
        </select>
        <button class="flex items-center gap-2 border border-slate-200 text-slate-600 text-[13px] font-bold px-4 py-2.5 rounded-xl hover:border-indigo-300 hover:text-[#5138ed] transition-all shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
          Export
        </button>
      </div>
    </div>

    <!-- KPI Cards -->
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

    <!-- Row 2: Trend Chart + Department Breakdown -->
    <div class="grid grid-cols-3 gap-6">

      <!-- Exam Trend Chart -->
      <div class="col-span-2 bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h3 class="text-[15px] font-bold text-slate-800">Exam Activity Trend</h3>
            <p class="text-[12px] text-slate-400 mt-0.5">Number of exams conducted over time</p>
          </div>
          <span class="text-[12px] font-bold text-[#5138ed] bg-indigo-50 px-3 py-1.5 rounded-lg">{{ selectedPeriod }}</span>
        </div>
        <div class="relative">
          <svg viewBox="0 0 550 200" class="w-full h-40" preserveAspectRatio="none">
            <defs>
              <linearGradient id="rGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#5138ed" stop-opacity="0.15"/>
                <stop offset="100%" stop-color="#5138ed" stop-opacity="0"/>
              </linearGradient>
            </defs>
            <path :d="svgFill" fill="url(#rGrad)"/>
            <path :d="svgLine" fill="none" stroke="#5138ed" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            <circle v-for="(p, i) in chartPoints" :key="i" :cx="p[0]" :cy="p[1]" r="3" fill="white" stroke="#5138ed" stroke-width="2"/>
          </svg>
          <div class="flex justify-between mt-1 px-1">
            <span v-for="l in ['Apr 27','May 02','May 07','May 12','May 17','May 22','May 27','Jun 01','Jun 05','Jun 10','Jun 15','Jun 20']" :key="l" class="text-[9px] text-slate-400 font-medium">{{ l }}</span>
          </div>
        </div>
      </div>

      <!-- Department Breakdown -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <h3 class="text-[15px] font-bold text-slate-800 mb-5">Department Breakdown</h3>
        <div class="space-y-4">
          <div v-for="d in deptStats" :key="d.dept">
            <div class="flex items-center justify-between mb-1.5">
              <span class="text-[12px] font-bold text-slate-700 truncate max-w-[130px]">{{ d.dept }}</span>
              <span class="text-[12px] font-bold text-slate-800">{{ d.passRate }}%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2">
              <div :class="[d.color, 'h-2 rounded-full transition-all']" :style="{ width: d.passRate + '%' }"></div>
            </div>
            <div class="flex items-center gap-3 mt-1">
              <span class="text-[10px] text-slate-400 font-medium">{{ d.exams }} exams</span>
              <span class="text-slate-200">·</span>
              <span class="text-[10px] text-slate-400 font-medium">{{ d.students }} students</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Row 3: Top Courses + Recent Results -->
    <div class="grid grid-cols-2 gap-6">

      <!-- Top Performing Courses -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <h3 class="text-[15px] font-bold text-slate-800 mb-5">Top Performing Courses</h3>
        <div class="space-y-3">
          <div v-for="(c, idx) in topCourses" :key="c.code" class="flex items-center gap-4 p-3.5 rounded-xl hover:bg-slate-50 transition-colors">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[11px] font-black" :class="idx === 0 ? 'bg-amber-50 text-amber-600' : idx === 1 ? 'bg-slate-100 text-slate-500' : 'bg-orange-50 text-orange-500'">{{ idx + 1 }}</div>
            <div class="flex-1 min-w-0">
              <p class="text-[13px] font-bold text-slate-800 truncate">{{ c.name }}</p>
              <p class="text-[10px] font-mono font-bold text-slate-400">{{ c.code }}</p>
            </div>
            <div class="text-right shrink-0">
              <p class="text-[13px] font-bold text-emerald-600">{{ c.passRate }}%</p>
              <p class="text-[10px] text-slate-400 font-medium">Pass rate</p>
            </div>
            <div class="text-right shrink-0">
              <p class="text-[13px] font-bold text-[#5138ed]">{{ c.avgScore }}%</p>
              <p class="text-[10px] text-slate-400 font-medium">Avg score</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Results -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <h3 class="text-[15px] font-bold text-slate-800 mb-5">Recent Student Results</h3>
        <div class="space-y-3">
          <div v-for="r in recentResults" :key="r.student + r.exam" class="flex items-center gap-3 p-3.5 rounded-xl hover:bg-slate-50 transition-colors">
            <div class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center text-[11px] font-bold text-[#5138ed] shrink-0">
              {{ r.student.split(' ').map(w => w[0]).join('') }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[12px] font-bold text-slate-800 truncate">{{ r.student }}</p>
              <p class="text-[11px] text-slate-400 font-medium truncate">{{ r.exam }}</p>
            </div>
            <div class="text-right shrink-0">
              <p class="text-[14px] font-bold" :class="scoreColor(r.score)">{{ r.score }}%</p>
              <span :class="[scoreBadge(r.status), 'text-[10px] font-bold px-1.5 py-0.5 rounded-md capitalize']">{{ r.status }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
