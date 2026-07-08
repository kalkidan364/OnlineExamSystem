<script setup lang="ts">
import { ref, computed } from 'vue'

const deptName = 'Computer Science'

const search = ref('')
const statusFilter = ref('all')
const currentPage = ref(1)
const perPage = 8

// Dummy Data
const allExams = ref([
  { id: 1, title: 'Database Systems Final',       course: 'CS-301', instructor: 'Dr. Abebe Kebede',    date: 'Oct 15, 2024', status: 'completed', duration: '120 min', participants: 140, passRate: 85 },
  { id: 2, title: 'Software Engineering Midterm', course: 'CS-401', instructor: 'Prof. Yonas Tadesse', date: 'Oct 16, 2024', status: 'completed', duration: '90 min',  participants: 165, passRate: 78 },
  { id: 3, title: 'Algorithms Quiz 1',            course: 'CS-202', instructor: 'Dr. Meron Bekele',    date: 'Oct 18, 2024', status: 'active',    duration: '45 min',  participants: 130, passRate: null },
  { id: 4, title: 'Operating Systems Final',      course: 'CS-302', instructor: 'Dr. Samuel Getachew', date: 'Oct 20, 2024', status: 'upcoming',  duration: '150 min', participants: 0,   passRate: null },
  { id: 5, title: 'AI Midterm',                   course: 'CS-405', instructor: 'Prof. Helen Assefa',  date: 'Oct 22, 2024', status: 'upcoming',  duration: '90 min',  participants: 0,   passRate: null },
])

const filtered = computed(() => {
  return allExams.value.filter(e => {
    const matchSearch = e.title.toLowerCase().includes(search.value.toLowerCase()) || e.course.toLowerCase().includes(search.value.toLowerCase())
    const matchStatus = statusFilter.value === 'all' || e.status === statusFilter.value
    return matchSearch && matchStatus
  })
})

const totalPages = computed(() => Math.ceil(filtered.value.length / perPage))
const paginated = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const stats = computed(() => ({
  total: allExams.value.length,
  completed: allExams.value.filter(e => e.status === 'completed').length,
  upcoming: allExams.value.filter(e => e.status === 'upcoming' || e.status === 'active').length,
  avgPassRate: Math.round(allExams.value.filter(e => e.passRate !== null).reduce((acc, curr) => acc + (curr.passRate || 0), 0) / (allExams.value.filter(e => e.passRate !== null).length || 1))
}))

const statusColor = (s: string) => {
  if (s === 'active') return 'bg-emerald-50 text-emerald-600 border border-emerald-200'
  if (s === 'upcoming') return 'bg-amber-50 text-amber-600 border border-amber-200'
  if (s === 'completed') return 'bg-slate-100 text-slate-600 border border-slate-200'
  return 'bg-slate-100 text-slate-500'
}

const passRateColor = (r: number | null) => {
  if (r === null) return 'bg-slate-100 text-slate-400'
  if (r >= 80) return 'bg-emerald-50 text-emerald-600'
  if (r >= 60) return 'bg-[#5138ed] text-white'
  return 'bg-rose-50 text-rose-600'
}
</script>

<template>
  <div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Department Exams</h1>
        <p class="text-[13px] text-slate-500 mt-1">Monitor all exams across {{ deptName }} courses.</p>
      </div>
      <button class="flex items-center gap-2 bg-white border border-slate-200 hover:border-[#5138ed] hover:text-[#5138ed] text-slate-600 text-[13px] font-bold px-5 py-2.5 rounded-xl shadow-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        Export Report
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-4 gap-4">
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center"><svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg></div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase">Total Exams</p><p class="text-[20px] font-bold text-slate-800">{{ stats.total }}</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center"><svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase">Completed</p><p class="text-[20px] font-bold text-slate-800">{{ stats.completed }}</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center"><svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase">Upcoming/Active</p><p class="text-[20px] font-bold text-slate-800">{{ stats.upcoming }}</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-sky-50 rounded-xl flex items-center justify-center"><svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase">Avg Pass Rate</p><p class="text-[20px] font-bold text-slate-800">{{ stats.avgPassRate }}%</p></div>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
      <!-- Filters -->
      <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-slate-100">
        <div class="relative flex-1 max-w-sm">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input v-model="search" type="text" placeholder="Search exams or courses..." class="w-full pl-9 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
        </div>
        <div class="flex items-center gap-3">
          <select v-model="statusFilter" class="text-[13px] border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="upcoming">Upcoming</option>
            <option value="completed">Completed</option>
          </select>
        </div>
      </div>

      <table class="w-full">
        <thead>
          <tr class="bg-slate-50/60 border-b border-slate-100">
            <th class="text-left px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Exam Title</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Date & Time</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pass Rate</th>
            <th class="text-right px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <tr v-for="exam in paginated" :key="exam.id" class="hover:bg-slate-50/40 transition-colors group">
            <td class="px-6 py-4">
              <p class="text-[13px] font-bold text-slate-800 leading-snug">{{ exam.title }}</p>
              <p class="text-[11px] text-slate-400 font-medium">By {{ exam.instructor }}</p>
            </td>
            <td class="px-4 py-4"><span class="text-[12px] font-bold text-slate-700 font-mono bg-slate-100 px-2 py-1 rounded">{{ exam.course }}</span></td>
            <td class="px-4 py-4">
              <p class="text-[12px] font-bold text-slate-700">{{ exam.date }}</p>
              <p class="text-[11px] text-slate-400">{{ exam.duration }}</p>
            </td>
            <td class="px-4 py-4">
              <span :class="[statusColor(exam.status), 'text-[11px] font-bold px-2 py-0.5 rounded-md capitalize']">{{ exam.status }}</span>
            </td>
            <td class="px-4 py-4">
              <div v-if="exam.passRate !== null" class="flex items-center gap-2">
                <span :class="[passRateColor(exam.passRate), 'px-2 py-1 rounded text-[11px] font-bold']">{{ exam.passRate }}%</span>
              </div>
              <span v-else class="text-[11px] font-medium text-slate-400">N/A</span>
            </td>
            <td class="px-6 py-4 text-right">
              <button class="text-[12px] font-bold text-[#5138ed] hover:underline opacity-0 group-hover:opacity-100 transition-opacity">View Results</button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/30">
        <p class="text-[12px] text-slate-500 font-medium">Showing {{ Math.min((currentPage - 1) * perPage + 1, filtered.length) }}–{{ Math.min(currentPage * perPage, filtered.length) }} of {{ filtered.length }}</p>
        <div class="flex items-center gap-1">
          <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 disabled:opacity-40 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
          <button v-for="p in totalPages" :key="p" @click="currentPage = p" :class="[currentPage === p ? 'bg-[#5138ed] text-white' : 'text-slate-500 hover:bg-slate-100', 'w-8 h-8 rounded-lg text-[12px] font-bold']">{{ p }}</button>
          <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 disabled:opacity-40 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
        </div>
      </div>
    </div>
  </div>
</template>
