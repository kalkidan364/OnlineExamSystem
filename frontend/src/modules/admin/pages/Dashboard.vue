<script setup lang="ts">
import { ref, computed } from 'vue'

const selectedPeriod = ref('Last 30 Days')

const stats = [
  { label: 'Total Users',  value: '2,534', change: '+12.5%', sub: 'All registered users',  color: 'indigo', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
  { label: 'Instructors',  value: '186',   change: '+8.3%',  sub: 'Active instructors',     color: 'green',  icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
  { label: 'Students',     value: '2,248', change: '+14.7%', sub: 'Active students',         color: 'blue',   icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
  { label: 'Courses',      value: '124',   change: '+5.2%',  sub: 'Offered courses',         color: 'orange', icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' },
  { label: 'Exams',        value: '342',   change: '+10.1%', sub: 'Total exams created',     color: 'rose',   icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
]

const colorMap: Record<string, { bg: string, icon: string, text: string, badge: string }> = {
  indigo: { bg: 'bg-indigo-50', icon: 'text-indigo-500', text: 'text-indigo-600', badge: 'bg-indigo-100 text-indigo-600' },
  green:  { bg: 'bg-green-50',  icon: 'text-green-500',  text: 'text-green-600',  badge: 'bg-green-100 text-green-600'  },
  blue:   { bg: 'bg-blue-50',   icon: 'text-blue-500',   text: 'text-blue-600',   badge: 'bg-blue-100 text-blue-600'   },
  orange: { bg: 'bg-orange-50', icon: 'text-orange-500', text: 'text-orange-600', badge: 'bg-orange-100 text-orange-600'},
  rose:   { bg: 'bg-rose-50',   icon: 'text-rose-500',   text: 'text-rose-600',   badge: 'bg-rose-100 text-rose-600'   },
}

const systemMetrics = [
  { label: 'Exams Conducted', value: '156',    change: '+11.4%', color: 'text-indigo-500' },
  { label: 'Total Attempts',  value: '1,248',  change: '+16.8%', color: 'text-green-500'  },
  { label: 'Pass Rate',       value: '72.4%',  change: '+5.6%',  color: 'text-orange-500' },
  { label: 'Average Score',   value: '68.7%',  change: '+3.2%',  color: 'text-blue-500'   },
]

const systemStatus = [
  { name: 'System Server', icon: 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2', color: 'text-green-500', bg: 'bg-green-50' },
  { name: 'Database',      icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4',                      color: 'text-green-500', bg: 'bg-green-50' },
  { name: 'File Storage',  icon: 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12',                                           color: 'text-green-500', bg: 'bg-green-50' },
  { name: 'Email Service', icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',                          color: 'text-green-500', bg: 'bg-green-50' },
]

const recentExams = [
  { title: 'Database Systems Midterm',    course: 'Database Systems (SWE-301)',   date: 'May 27, 2025', status: 'Completed' },
  { title: 'Data Structures Quiz 3',      course: 'Data Structures (SWE-201)',    date: 'May 26, 2025', status: 'Completed' },
  { title: 'Software Engineering Final',  course: 'Software Eng. (SWE-401)',      date: 'May 25, 2025', status: 'Completed' },
  { title: 'Web Development Quiz 2',      course: 'Web Development (SWE-203)',    date: 'May 24, 2025', status: 'Completed' },
  { title: 'Operating Systems Midterm',   course: 'Operating Systems (SWE-302)',  date: 'May 23, 2025', status: 'Completed' },
]

const quickActions = [
  { label: 'Create New Exam',   icon: 'M12 4v16m8-8H4',                                                       path: '/admin/exams'        },
  { label: 'Add New User',      icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', path: '/admin/users'        },
  { label: 'Manage Instructors',icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', path: '/admin/instructors'  },
  { label: 'View Reports',      icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', path: '/admin/reports' },
]

const donutDistribution = [
  { label: 'Students',    count: 2248, pct: '88.7%', color: '#5138ed' },
  { label: 'Instructors', count: 186,  pct: '7.3%',  color: '#22c55e' },
  { label: 'Admins',      count: 52,   pct: '2.1%',  color: '#f97316' },
  { label: 'Others',      count: 48,   pct: '1.9%',  color: '#94a3b8' },
]

// SVG line chart data (simplified wave points)
const chartPoints = [
  [0,300],[30,260],[60,280],[90,220],[120,240],[150,200],[180,210],[210,180],[240,200],[270,160],[300,180],[330,150],[360,170],[390,140],[420,160],[450,130],[480,150],[510,170],[540,145],[570,160],[600,140]
]
const svgPath = computed(() => {
  return chartPoints.map((p, i) => `${i === 0 ? 'M' : 'L'}${p[0]},${p[1]}`).join(' ')
})
const svgFill = computed(() => {
  return chartPoints.map((p, i) => `${i === 0 ? 'M' : 'L'}${p[0]},${p[1]}`).join(' ') + ` L${chartPoints[chartPoints.length-1][0]},400 L0,400 Z`
})
</script>

<template>
  <div class="space-y-6">

    <!-- Page Title + Date -->
    <div class="flex items-start justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Super Admin Dashboard</h1>
        <p class="text-[13px] text-slate-500 mt-1">Welcome back, Super Admin! Here's an overview of your system.</p>
      </div>
      <div class="flex items-center gap-2 text-[13px] font-medium text-slate-600 border border-slate-200 rounded-xl px-4 py-2.5 bg-white shadow-sm cursor-pointer hover:border-indigo-300 transition-colors">
        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        May 27, 2025
        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
      </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-5 gap-4">
      <div v-for="stat in stats" :key="stat.label" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
        <div :class="[colorMap[stat.color].bg, 'w-11 h-11 rounded-xl flex items-center justify-center shrink-0']">
          <svg class="w-5 h-5" :class="colorMap[stat.color].icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon"></path>
          </svg>
        </div>
        <div class="min-w-0">
          <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider truncate">{{ stat.label }}</p>
          <div class="flex items-center gap-2 mt-0.5 flex-wrap">
            <span class="text-[20px] font-bold text-slate-800">{{ stat.value }}</span>
            <span :class="[colorMap[stat.color].badge, 'text-[10px] font-bold px-1.5 py-0.5 rounded-md']">{{ stat.change }}</span>
          </div>
          <p class="text-[11px] text-slate-400 font-medium mt-1 truncate">{{ stat.sub }}</p>
        </div>
      </div>
    </div>

    <!-- Row 2: System Overview + System Status -->
    <div class="grid grid-cols-3 gap-6">
      
      <!-- System Overview Chart -->
      <div class="col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
          <h3 class="text-[15px] font-bold text-slate-800">System Overview</h3>
          <div class="flex items-center gap-2 text-[12px] font-semibold text-slate-600 border border-slate-200 rounded-lg px-3 py-1.5 cursor-pointer hover:border-indigo-300 transition-colors">
            {{ selectedPeriod }}
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
        </div>
        <!-- Metrics Row -->
        <div class="grid grid-cols-4 gap-4 mb-5">
          <div v-for="m in systemMetrics" :key="m.label" class="flex items-center gap-2">
            <span :class="[m.color, 'w-2 h-2 rounded-full flex-shrink-0']" style="display:inline-block"></span>
            <div>
              <p class="text-[11px] font-medium text-slate-500 leading-none">{{ m.label }}</p>
              <p class="text-[13px] font-bold text-slate-800">{{ m.value }} <span class="text-[10px] font-semibold text-green-500">{{ m.change }}</span></p>
            </div>
          </div>
        </div>
        <!-- SVG Chart -->
        <div class="relative">
          <svg viewBox="0 0 600 200" class="w-full h-44" preserveAspectRatio="none">
            <!-- Fill -->
            <defs>
              <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#5138ed" stop-opacity="0.12"/>
                <stop offset="100%" stop-color="#5138ed" stop-opacity="0"/>
              </linearGradient>
            </defs>
            <path :d="svgFill" fill="url(#chartGrad)" />
            <path :d="svgPath" fill="none" stroke="#5138ed" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <!-- X axis labels -->
          <div class="flex justify-between mt-1 px-1">
            <span v-for="label in ['Apr 27', 'May 02', 'May 07', 'May 12', 'May 17', 'May 22', 'May 27']" :key="label" class="text-[10px] text-slate-400 font-medium">{{ label }}</span>
          </div>
        </div>
      </div>

      <!-- System Status -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-[15px] font-bold text-slate-800 mb-5">System Status</h3>
        <div class="space-y-4">
          <div v-for="item in systemStatus" :key="item.name" class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div :class="[item.bg, 'w-9 h-9 rounded-xl flex items-center justify-center']">
                <svg class="w-4.5 h-4.5" :class="item.color" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"></path>
                </svg>
              </div>
              <span class="text-[13px] font-semibold text-slate-700">{{ item.name }}</span>
            </div>
            <span class="text-[12px] font-bold text-green-500">Operational</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Row 3: User Distribution + Recent Exams + Quick Actions -->
    <div class="grid grid-cols-3 gap-6">

      <!-- User Distribution Donut -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-[15px] font-bold text-slate-800 mb-5">User Distribution</h3>
        <div class="flex flex-col items-center">
          <!-- Donut SVG -->
          <div class="relative mb-5">
            <svg viewBox="0 0 120 120" class="w-36 h-36">
              <!-- Background ring -->
              <circle cx="60" cy="60" r="44" fill="none" stroke="#f1f5f9" stroke-width="18"/>
              <!-- Segments (simplified) -->
              <circle cx="60" cy="60" r="44" fill="none" stroke="#5138ed" stroke-width="18"
                stroke-dasharray="246 32" stroke-dashoffset="0" stroke-linecap="butt" transform="rotate(-90 60 60)"/>
              <circle cx="60" cy="60" r="44" fill="none" stroke="#22c55e" stroke-width="18"
                stroke-dasharray="20 258" stroke-dashoffset="-246" stroke-linecap="butt" transform="rotate(-90 60 60)"/>
              <circle cx="60" cy="60" r="44" fill="none" stroke="#f97316" stroke-width="18"
                stroke-dasharray="6 272" stroke-dashoffset="-266" stroke-linecap="butt" transform="rotate(-90 60 60)"/>
              <circle cx="60" cy="60" r="44" fill="none" stroke="#94a3b8" stroke-width="18"
                stroke-dasharray="5 273" stroke-dashoffset="-272" stroke-linecap="butt" transform="rotate(-90 60 60)"/>
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
              <span class="text-[18px] font-bold text-slate-800">2,534</span>
              <span class="text-[10px] font-medium text-slate-400">Total Users</span>
            </div>
          </div>
          <!-- Legend -->
          <div class="w-full space-y-2">
            <div v-for="d in donutDistribution" :key="d.label" class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full flex-shrink-0" :style="{ backgroundColor: d.color }"></span>
                <span class="text-[12px] font-medium text-slate-600">{{ d.label }}</span>
              </div>
              <span class="text-[12px] font-semibold text-slate-700">{{ d.count.toLocaleString() }} ({{ d.pct }})</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Exams -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
          <h3 class="text-[15px] font-bold text-slate-800">Recent Exams</h3>
          <router-link to="/admin/exams" class="text-[12px] font-bold text-[#5138ed] hover:underline">View All</router-link>
        </div>
        <!-- Header -->
        <div class="grid grid-cols-[1fr_auto_auto] gap-2 pb-2 border-b border-slate-100 mb-2">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Exam Title</span>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Date</span>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Status</span>
        </div>
        <div class="space-y-3">
          <div v-for="exam in recentExams" :key="exam.title" class="grid grid-cols-[1fr_auto_auto] gap-2 items-center">
            <div>
              <p class="text-[12px] font-bold text-slate-800 leading-snug truncate">{{ exam.title }}</p>
              <p class="text-[10px] text-slate-400 font-medium truncate">{{ exam.course }}</p>
            </div>
            <span class="text-[11px] text-slate-500 font-medium text-right whitespace-nowrap">{{ exam.date }}</span>
            <span class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-md whitespace-nowrap">{{ exam.status }}</span>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-[15px] font-bold text-slate-800 mb-5">Quick Actions</h3>
        <div class="space-y-3">
          <router-link
            v-for="action in quickActions"
            :key="action.label"
            :to="action.path"
            class="flex items-center justify-between p-3.5 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/40 transition-all group cursor-pointer"
          >
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="action.icon"></path>
                </svg>
              </div>
              <span class="text-[13px] font-semibold text-slate-700 group-hover:text-[#5138ed] transition-colors">{{ action.label }}</span>
            </div>
            <svg class="w-4 h-4 text-slate-300 group-hover:text-[#5138ed] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </router-link>
        </div>
      </div>
    </div>

  </div>
</template>
