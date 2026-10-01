<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Line, Doughnut } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  ArcElement
} from 'chart.js'
import apiClient from '../../../core/api/apiClient'
import { useAuthStore } from '../../auth/store/authStore'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  ArcElement
)

const router = useRouter()
const authStore = useAuthStore()

const isLoading = ref(true)
const dashboardData = ref<any>({
  department: null,
  stats: [],
  lineChart: { labels: [], students: [], courses: [] },
  doughnutChart: { totalExams: 0, data: [0, 0, 0, 0], stats: [] },
  performances: [],
  activities: [],
  announcements: []
})

// Fetch real dashboard stats from backend
const fetchDashboardStats = async () => {
  isLoading.value = true
  try {
    const res = await apiClient.get('/dept-head/dashboard-stats')
    if (res.data?.data) {
      dashboardData.value = res.data.data
    }
  } catch (err) {
    console.error('Failed to load department dashboard stats:', err)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchDashboardStats()
})

// KPIs
const stats = computed(() => {
  if (dashboardData.value.stats && dashboardData.value.stats.length > 0) {
    return dashboardData.value.stats
  }
  return [
    { label: 'Total Students',    value: '0', change: 'Enrolled in department', bg: 'bg-indigo-50',  ic: 'text-[#5138ed]',  icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', color: 'text-emerald-500' },
    { label: 'Total Instructors', value: '0', change: 'Active faculty',         bg: 'bg-emerald-50', ic: 'text-emerald-500',icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', color: 'text-emerald-500' },
    { label: 'Total Courses',     value: '0', change: 'Active curriculum',      bg: 'bg-sky-50',     ic: 'text-sky-500',    icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', color: 'text-emerald-500' },
    { label: 'Active Exams',      value: '0', change: 'Scheduled & active',     bg: 'bg-amber-50',   ic: 'text-amber-500',  icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', color: 'text-emerald-500' },
  ]
})

// Department Overview Line Chart
const lineChartData = computed(() => ({
  labels: dashboardData.value.lineChart?.labels?.length
    ? dashboardData.value.lineChart.labels
    : ['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May'],
  datasets: [
    {
      label: 'Students',
      backgroundColor: '#5138ed',
      borderColor: '#5138ed',
      data: dashboardData.value.lineChart?.students?.length
        ? dashboardData.value.lineChart.students
        : [0, 0, 0, 0, 0, 0, 0, 0, 0],
      tension: 0.4,
      pointRadius: 4,
      pointBackgroundColor: '#5138ed',
    },
    {
      label: 'Courses',
      backgroundColor: '#38bdf8',
      borderColor: '#38bdf8',
      data: dashboardData.value.lineChart?.courses?.length
        ? dashboardData.value.lineChart.courses
        : [0, 0, 0, 0, 0, 0, 0, 0, 0],
      tension: 0.4,
      pointRadius: 4,
      pointBackgroundColor: '#38bdf8',
    }
  ]
}))

const lineChartOptions = computed(() => {
  const students = dashboardData.value.lineChart?.students || [0]
  const courses = dashboardData.value.lineChart?.courses || [0]
  const maxVal = Math.max(...students, ...courses, 5)
  const step = Math.max(1, Math.ceil(maxVal / 4))

  return {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      y: {
        beginAtZero: true,
        suggestedMax: maxVal + step,
        ticks: { stepSize: step, color: '#94a3b8', font: { size: 10 } },
        border: { display: false },
        grid: { color: '#f8fafc' }
      },
      x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } }, border: { display: false } }
    }
  }
})

// Quick Actions with functional routing
const quickActions = [
  { label: 'Manage Instructors', desc: 'View and manage department instructors', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', color: 'text-[#5138ed]', bg: 'bg-indigo-50', route: '/dept-head/instructors' },
  { label: 'Manage Students', desc: 'View and manage department students', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', color: 'text-emerald-500', bg: 'bg-emerald-50', route: '/dept-head/students' },
  { label: 'Manage Courses', desc: 'View and manage department courses', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', color: 'text-sky-500', bg: 'bg-sky-50', route: '/dept-head/courses' },
  { label: 'Review Exam Results', desc: 'Review and approve exam schedules & results', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', color: 'text-amber-500', bg: 'bg-amber-50', route: '/dept-head/exams' },
]

// Recent Announcements
const announcements = computed(() => dashboardData.value.announcements || [])

// Recent Activities
const activities = computed(() => dashboardData.value.activities || [])

// Doughnut Chart Data
const doughnutChartData = computed(() => ({
  labels: ['Upcoming', 'Ongoing', 'Completed', 'Cancelled'],
  datasets: [{
    data: dashboardData.value.doughnutChart?.data || [0, 0, 0, 0],
    backgroundColor: ['#5138ed', '#38bdf8', '#10b981', '#ef4444'],
    borderWidth: 0,
    cutout: '75%'
  }]
}))

const doughnutChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false }, tooltip: { enabled: true } },
}

const totalExamsCount = computed(() => dashboardData.value.doughnutChart?.totalExams ?? 0)

const examStats = computed(() => {
  if (dashboardData.value.doughnutChart?.stats?.length) {
    return dashboardData.value.doughnutChart.stats
  }
  return [
    { label: 'Upcoming', val: 0, color: 'bg-[#5138ed]' },
    { label: 'Ongoing', val: 0, color: 'bg-sky-400' },
    { label: 'Completed', val: 0, color: 'bg-emerald-500' },
    { label: 'Cancelled', val: 0, color: 'bg-rose-500' }
  ]
})

// Department Performance
const performances = computed(() => {
  if (dashboardData.value.performances?.length) {
    return dashboardData.value.performances
  }
  return [
    { label: 'Attendance Rate', val: '0%', pct: 0, color: 'bg-emerald-500' },
    { label: 'Pass Rate', val: '0%', pct: 0, color: 'bg-[#5138ed]' },
    { label: 'Average Grade', val: '0.00 / 4.00', pct: 0, color: 'bg-sky-400' },
    { label: 'Course Completion', val: '0%', pct: 0, color: 'bg-amber-500' },
  ]
})

// Determine Current Date Display
const currentDate = new Date().toLocaleDateString('en-US', {
  month: 'short',
  day: 'numeric',
  year: 'numeric'
})
const currentDay = new Date().toLocaleDateString('en-US', {
  weekday: 'long'
})
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-start justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Dashboard</h1>
        <p class="text-[13px] text-slate-500 mt-1">
          Welcome back, <span class="font-semibold text-slate-700">{{ authStore.user?.name || 'Department Head' }}</span>!
          <span v-if="dashboardData.department?.name" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-[#5138ed] border border-indigo-100">
            {{ dashboardData.department.name }}
          </span>
        </p>
      </div>
      <div class="flex flex-col items-end">
        <div class="flex items-center gap-2 text-[13px] font-semibold text-slate-700">
          <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          {{ currentDate }}
        </div>
        <p class="text-[12px] text-slate-500 mt-0.5">{{ currentDay }}</p>
      </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-4 gap-6">
      <div v-for="s in stats" :key="s.label" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 flex items-center gap-5 hover:shadow-md transition-shadow">
        <div :class="[s.bg, 'w-14 h-14 rounded-2xl flex items-center justify-center shrink-0']">
          <svg class="w-6 h-6" :class="s.ic" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="s.icon"></path></svg>
        </div>
        <div>
          <p class="text-[12px] font-semibold text-slate-500">{{ s.label }}</p>
          <p class="text-[24px] font-bold text-slate-800 leading-tight mt-0.5">{{ s.value }}</p>
          <p :class="[s.color, 'text-[11px] font-bold mt-1']">{{ s.change }}</p>
        </div>
      </div>
    </div>

    <!-- Top Grid: Dept Overview | Quick Actions | Announcements -->
    <div class="grid grid-cols-4 gap-6">
      <!-- Department Overview (Span 2) -->
      <div class="col-span-2 bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-[15px] font-bold text-slate-800">Department Overview</h3>
          <div class="relative">
            <select class="pl-3 pr-8 py-1.5 text-[12px] font-medium border border-slate-200 rounded-lg text-slate-600 bg-white appearance-none focus:outline-none">
              <option>This Academic Year</option>
            </select>
            <svg class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
        </div>
        
        <div class="flex items-center gap-6 mb-4">
          <div class="flex items-center gap-2">
            <div class="w-4 h-1 bg-[#5138ed] rounded-full"></div>
            <span class="text-[12px] font-semibold text-slate-600">Students</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-4 h-1 bg-sky-400 rounded-full"></div>
            <span class="text-[12px] font-semibold text-slate-600">Courses</span>
          </div>
        </div>
        
        <div class="h-[220px] w-full">
          <Line :data="lineChartData" :options="lineChartOptions" />
        </div>
      </div>

      <!-- Quick Actions (Span 1) -->
      <div class="col-span-1 bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <h3 class="text-[15px] font-bold text-slate-800 mb-5">Quick Actions</h3>
        <div class="space-y-4">
          <div
            v-for="act in quickActions"
            :key="act.label"
            @click="router.push(act.route)"
            class="flex items-center justify-between p-3 border border-slate-100 rounded-xl hover:border-indigo-200 hover:bg-slate-50 transition-colors cursor-pointer group"
          >
            <div class="flex items-center gap-3">
              <div :class="[act.bg, act.color, 'w-10 h-10 rounded-lg flex items-center justify-center shrink-0']">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="act.icon"></path></svg>
              </div>
              <div>
                <p class="text-[13px] font-bold text-slate-800 group-hover:text-[#5138ed] transition-colors leading-tight">{{ act.label }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ act.desc }}</p>
              </div>
            </div>
            <svg class="w-4 h-4 text-slate-300 group-hover:text-[#5138ed] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </div>
        </div>
      </div>

      <!-- Recent Announcements (Span 1) -->
      <div class="col-span-1 bg-white border border-slate-100 rounded-2xl shadow-sm p-6 flex flex-col">
        <div class="flex items-center justify-between mb-5">
          <h3 class="text-[15px] font-bold text-slate-800">Recent Announcements</h3>
          <button @click="router.push('/dept-head/schedule')" class="text-[12px] font-bold text-[#5138ed] hover:underline">View All</button>
        </div>
        <div class="space-y-5 flex-1">
          <div v-if="announcements.length === 0" class="h-full flex flex-col items-center justify-center py-6 text-center text-slate-400">
            <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <p class="text-[12px] font-semibold text-slate-600">No Announcements</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Academic notices and events will appear here.</p>
          </div>
          <div v-else v-for="(ann, i) in announcements" :key="i" class="flex items-start gap-4 pb-4 border-b border-slate-100 last:border-b-0 last:pb-0">
            <div :class="[ann.bg, ann.color, 'w-10 h-10 rounded-xl flex items-center justify-center shrink-0']">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="ann.icon"></path></svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between mb-1 gap-2">
                <h4 class="text-[12px] font-bold text-slate-800 leading-tight truncate">{{ ann.title }}</h4>
                <span class="text-[10px] font-medium text-slate-400 shrink-0 whitespace-nowrap">{{ ann.date }}</span>
              </div>
              <p class="text-[11px] text-slate-500 leading-snug line-clamp-2">{{ ann.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Grid: Activities | Exams Overview | Dept Performance -->
    <div class="grid grid-cols-3 gap-6">
      
      <!-- Recent Activities -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-[15px] font-bold text-slate-800">Recent Activities</h3>
          <button @click="router.push('/dept-head/activity-logs')" class="text-[12px] font-bold text-[#5138ed] hover:underline">View All</button>
        </div>
        <div class="space-y-5">
          <div v-if="activities.length === 0" class="flex flex-col items-center justify-center py-8 text-center text-slate-400">
            <p class="text-[12px] font-medium text-slate-500">No recent activities found for this department.</p>
          </div>
          <div v-else v-for="(act, i) in activities" :key="i" class="flex items-start gap-4">
            <div :class="[act.bg, act.color, 'w-10 h-10 rounded-xl flex items-center justify-center shrink-0']">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="act.icon"></path></svg>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between">
                <p class="text-[13px] font-bold text-slate-800 truncate">{{ act.title }}</p>
                <span class="text-[11px] font-medium text-slate-400 shrink-0 ml-2">{{ act.time }}</span>
              </div>
              <p class="text-[12px] text-slate-500 mt-0.5 line-clamp-2">{{ act.desc }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Exams Overview -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 flex flex-col">
        <h3 class="text-[15px] font-bold text-slate-800 mb-6">Exams Overview</h3>
        <div class="flex-1 flex items-center justify-center gap-8 px-2">
          <!-- Chart -->
          <div class="relative w-[150px] h-[150px] shrink-0 flex items-center justify-center">
            <Doughnut
              v-if="doughnutChartData.datasets[0].data.some((v: number) => v > 0)"
              :data="doughnutChartData"
              :options="doughnutChartOptions"
            />
            <div
              v-else
              class="w-full h-full rounded-full border-[10px] border-slate-100 flex items-center justify-center"
            ></div>
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
              <span class="text-[28px] font-bold text-slate-800 leading-none">{{ totalExamsCount }}</span>
              <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide mt-1">Total Exams</span>
            </div>
          </div>
          <!-- Legend -->
          <div class="space-y-4">
            <div v-for="stat in examStats" :key="stat.label" class="flex items-center gap-3">
              <div :class="[stat.color, 'w-3 h-3 rounded-full shrink-0']"></div>
              <span class="text-[12px] text-slate-500 font-medium w-16">{{ stat.label }}</span>
              <span class="text-[14px] font-bold text-slate-800">{{ stat.val }}</span>
            </div>
          </div>
        </div>
        <button
          @click="router.push('/dept-head/exams')"
          class="w-full mt-6 py-2.5 bg-indigo-50 text-[#5138ed] text-[13px] font-bold rounded-xl hover:bg-indigo-100 transition-colors flex items-center justify-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
          View All Exams
        </button>
      </div>

      <!-- Department Performance -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 flex flex-col">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-[15px] font-bold text-slate-800">Department Performance</h3>
          <div class="relative">
            <select class="pl-2 pr-6 py-1 text-[11px] font-medium text-slate-500 bg-transparent appearance-none focus:outline-none border border-slate-200 rounded-lg">
              <option>This Semester</option>
            </select>
            <svg class="w-3 h-3 absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
        </div>
        
        <div class="flex-1 space-y-7">
          <div v-for="perf in performances" :key="perf.label">
            <div class="flex items-center justify-between mb-2">
              <span class="text-[13px] font-semibold text-slate-600">{{ perf.label }}</span>
              <span class="text-[13px] font-bold text-slate-800">{{ perf.val }}</span>
            </div>
            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
              <div :class="[perf.color, 'h-full rounded-full transition-all duration-500']" :style="{ width: perf.pct + '%' }"></div>
            </div>
          </div>
        </div>
        
        <button
          @click="router.push('/dept-head/reports')"
          class="w-full mt-6 py-2.5 text-[#5138ed] text-[13px] font-bold hover:underline transition-colors flex items-center justify-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
          View Detailed Reports
        </button>
      </div>
      
    </div>

  </div>
</template>
