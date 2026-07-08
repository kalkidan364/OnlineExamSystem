<script setup lang="ts">
import { ref, computed } from 'vue'

const deptName = 'Computer Science' // Simulated locked department

const search = ref('')
const yearFilter = ref('all')
const currentPage = ref(1)
const perPage = 10

// Dummy Data (Scoped to Computer Science)
const allStudents = ref([
  { id: 'STU-001', name: 'Abebe Bikila',    email: 'abebe.b@wou.edu.et', year: 'Year 4', courses: 6,  examsTaken: 24, avgScore: 82.5, status: 'active',   joined: 'Sep 2021', avatar: 'AB' },
  { id: 'STU-002', name: 'Chala Debele',    email: 'chala.d@wou.edu.et', year: 'Year 3', courses: 5,  examsTaken: 18, avgScore: 76.2, status: 'active',   joined: 'Sep 2022', avatar: 'CD' },
  { id: 'STU-003', name: 'Makeda Tesfaye',  email: 'makeda.t@wou.edu.et',year: 'Year 2', courses: 6,  examsTaken: 12, avgScore: 88.9, status: 'active',   joined: 'Sep 2023', avatar: 'MT' },
  { id: 'STU-004', name: 'Tewodros Kasshun',email: 'tedi.k@wou.edu.et',  year: 'Year 4', courses: 4,  examsTaken: 22, avgScore: 91.4, status: 'active',   joined: 'Sep 2021', avatar: 'TK' },
  { id: 'STU-005', name: 'Senait Bekele',   email: 'senait.b@wou.edu.et',year: 'Year 1', courses: 5,  examsTaken: 4,  avgScore: 65.0, status: 'inactive', joined: 'Sep 2024', avatar: 'SB' },
  { id: 'STU-006', name: 'Dawit Mengistu',  email: 'dawit.m@wou.edu.et', year: 'Year 3', courses: 6,  examsTaken: 16, avgScore: 79.8, status: 'active',   joined: 'Sep 2022', avatar: 'DM' },
  { id: 'STU-007', name: 'Lidya Girma',     email: 'lidya.g@wou.edu.et', year: 'Year 2', courses: 5,  examsTaken: 10, avgScore: 84.1, status: 'active',   joined: 'Sep 2023', avatar: 'LG' },
])

const filtered = computed(() => {
  return allStudents.value.filter(s => {
    const matchSearch = s.name.toLowerCase().includes(search.value.toLowerCase()) ||
                        s.id.toLowerCase().includes(search.value.toLowerCase()) ||
                        s.email.toLowerCase().includes(search.value.toLowerCase())
    const matchYear   = yearFilter.value === 'all' || s.year === yearFilter.value
    return matchSearch && matchYear
  })
})

const totalPages = computed(() => Math.ceil(filtered.value.length / perPage))
const paginated  = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const avatarColor = (name: string) => {
  const colors = ['bg-indigo-500','bg-sky-500','bg-emerald-500','bg-violet-500','bg-amber-500','bg-rose-500']
  const charCode = name.charCodeAt(0)
  return colors[charCode % colors.length]
}

const getScoreColor = (score: number) => {
  if (score >= 85) return 'text-emerald-600'
  if (score >= 70) return 'text-[#5138ed]'
  if (score >= 50) return 'text-amber-500'
  return 'text-rose-500'
}
</script>

<template>
  <div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Department Students</h1>
        <p class="text-[13px] text-slate-500 mt-1">View students enrolled in the {{ deptName }} department.</p>
      </div>
      <!-- For Students, Dept Heads typically don't "create" them manually in bulk, but they can view them -->
      <button class="flex items-center gap-2 bg-white border border-slate-200 hover:border-[#5138ed] hover:text-[#5138ed] text-slate-600 text-[13px] font-bold px-5 py-2.5 rounded-xl shadow-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        Export List
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-4 gap-4">
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center"><svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase">Total Students</p><p class="text-[20px] font-bold text-slate-800">{{ allStudents.length }}</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center"><svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase">Active</p><p class="text-[20px] font-bold text-slate-800">{{ allStudents.filter(s => s.status==='active').length }}</p></div>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
      <!-- Filters -->
      <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-slate-100">
        <div class="relative flex-1 max-w-sm">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input v-model="search" type="text" placeholder="Search by name, ID, or email..." class="w-full pl-9 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
        </div>
        <div class="flex items-center gap-3">
          <select v-model="yearFilter" class="text-[13px] border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white">
            <option value="all">All Years</option>
            <option value="Year 1">Year 1</option>
            <option value="Year 2">Year 2</option>
            <option value="Year 3">Year 3</option>
            <option value="Year 4">Year 4</option>
          </select>
        </div>
      </div>

      <table class="w-full">
        <thead>
          <tr class="bg-slate-50/60 border-b border-slate-100">
            <th class="text-left px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Student Details</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Year</th>
            <th class="text-center px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Courses</th>
            <th class="text-center px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Exams Taken</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Avg Score</th>
            <th class="text-right px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <tr v-for="stu in paginated" :key="stu.id" class="hover:bg-slate-50/40 transition-colors group">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div :class="[avatarColor(stu.name), 'w-10 h-10 rounded-xl flex items-center justify-center text-[12px] font-bold text-white shrink-0']">{{ stu.avatar }}</div>
                <div>
                  <div class="flex items-center gap-2">
                    <p class="text-[13px] font-bold text-slate-800">{{ stu.name }}</p>
                    <span v-if="stu.status === 'inactive'" class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-500">Inactive</span>
                  </div>
                  <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-[10px] font-mono font-bold text-slate-400 bg-slate-50 px-1 rounded">{{ stu.id }}</span>
                    <span class="text-[11px] text-slate-400 font-medium">{{ stu.email }}</span>
                  </div>
                </div>
              </div>
            </td>
            <td class="px-4 py-4"><span class="text-[12px] font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded-lg">{{ stu.year }}</span></td>
            <td class="px-4 py-4 text-center"><span class="text-[13px] font-bold text-slate-800">{{ stu.courses }}</span></td>
            <td class="px-4 py-4 text-center"><span class="text-[13px] font-bold text-slate-800">{{ stu.examsTaken }}</span></td>
            <td class="px-4 py-4">
              <div class="flex items-center gap-2">
                <span class="text-[14px] font-bold" :class="getScoreColor(stu.avgScore)">{{ stu.avgScore }}%</span>
              </div>
            </td>
            <td class="px-6 py-4 text-right">
              <button class="text-[12px] font-bold text-[#5138ed] hover:underline opacity-0 group-hover:opacity-100 transition-opacity">View Profile</button>
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
