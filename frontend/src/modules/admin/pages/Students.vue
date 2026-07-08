<script setup lang="ts">
import { ref, computed } from 'vue'

const search = ref('')
const yearFilter = ref('all')
const statusFilter = ref('all')
const currentPage = ref(1)
const perPage = 8
const showAddModal = ref(false)
const showDeleteModal = ref(false)
const selectedStudent = ref<any>(null)

const allStudents = ref([
  { id: 1,  name: 'Dawit Mengistu',   email: 'dawit@student.wou.et',    year: 'Year 3', dept: 'Computer Science',    id_no: 'WU/0123/14', exams: 14, avg: 81, status: 'active',    enrolled: 'Sep 01, 2022', avatar: 'DM' },
  { id: 2,  name: 'Selam Bekele',     email: 'selam@student.wou.et',    year: 'Year 2', dept: 'Information Systems', id_no: 'WU/0456/15', exams: 8,  avg: 76, status: 'active',    enrolled: 'Sep 01, 2023', avatar: 'SB' },
  { id: 3,  name: 'Berhane Tesfaye',  email: 'berhane@student.wou.et',  year: 'Year 4', dept: 'Software Engineering',id_no: 'WU/0789/13', exams: 21, avg: 88, status: 'active',    enrolled: 'Sep 01, 2021', avatar: 'BT' },
  { id: 4,  name: 'Hana Wolde',       email: 'hana@student.wou.et',     year: 'Year 1', dept: 'Computer Science',    id_no: 'WU/0321/16', exams: 3,  avg: 62, status: 'suspended', enrolled: 'Sep 01, 2024', avatar: 'HW' },
  { id: 5,  name: 'Mekdes Alemu',     email: 'mekdes@student.wou.et',   year: 'Year 2', dept: 'Mathematics',         id_no: 'WU/0654/15', exams: 9,  avg: 79, status: 'active',    enrolled: 'Sep 01, 2023', avatar: 'MA' },
  { id: 6,  name: 'Robel Girma',      email: 'robel@student.wou.et',    year: 'Year 3', dept: 'Physics',             id_no: 'WU/0987/14', exams: 13, avg: 73, status: 'active',    enrolled: 'Sep 01, 2022', avatar: 'RG' },
  { id: 7,  name: 'Kidist Hailu',     email: 'kidist@student.wou.et',   year: 'Year 2', dept: 'Computer Science',    id_no: 'WU/1234/15', exams: 7,  avg: 84, status: 'active',    enrolled: 'Sep 01, 2023', avatar: 'KH' },
  { id: 8,  name: 'Biruk Tadesse',    email: 'biruk@student.wou.et',    year: 'Year 4', dept: 'Information Systems', id_no: 'WU/1567/13', exams: 18, avg: 69, status: 'inactive',  enrolled: 'Sep 01, 2021', avatar: 'BT' },
  { id: 9,  name: 'Tigist Solomon',   email: 'tigist@student.wou.et',   year: 'Year 1', dept: 'Mathematics',         id_no: 'WU/1890/16', exams: 2,  avg: 55, status: 'active',    enrolled: 'Sep 01, 2024', avatar: 'TS' },
  { id: 10, name: 'Abel Habtamu',     email: 'abel@student.wou.et',     year: 'Year 3', dept: 'Software Engineering',id_no: 'WU/2123/14', exams: 16, avg: 91, status: 'active',    enrolled: 'Sep 01, 2022', avatar: 'AH' },
])

const newStudent = ref({ name: '', email: '', year: 'Year 1', dept: '', id_no: '', password: '' })
const years = ['Year 1', 'Year 2', 'Year 3', 'Year 4']
const depts = ['Computer Science', 'Mathematics', 'Physics', 'Software Engineering', 'Information Systems']

const filtered = computed(() =>
  allStudents.value.filter(s => {
    const matchSearch = s.name.toLowerCase().includes(search.value.toLowerCase()) ||
                        s.email.toLowerCase().includes(search.value.toLowerCase()) ||
                        s.id_no.toLowerCase().includes(search.value.toLowerCase())
    const matchYear   = yearFilter.value === 'all' || s.year === yearFilter.value
    const matchStatus = statusFilter.value === 'all' || s.status === statusFilter.value
    return matchSearch && matchYear && matchStatus
  })
)

const totalPages = computed(() => Math.ceil(filtered.value.length / perPage))
const paginated  = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const stats = computed(() => ({
  total:     allStudents.value.length,
  active:    allStudents.value.filter(s => s.status === 'active').length,
  avgScore:  Math.round(allStudents.value.reduce((a, s) => a + s.avg, 0) / allStudents.value.length),
  exams:     allStudents.value.reduce((a, s) => a + s.exams, 0),
}))

const avatarColor = (id: number) => {
  const c = ['bg-indigo-500','bg-sky-500','bg-emerald-500','bg-violet-500','bg-amber-500','bg-rose-500','bg-teal-500','bg-orange-500','bg-cyan-500','bg-purple-500']
  return c[id % c.length]
}

const statusBadge  = (s: string) => ({ active:'bg-emerald-50 text-emerald-700', inactive:'bg-slate-100 text-slate-500', suspended:'bg-rose-50 text-rose-600' }[s] || 'bg-slate-100 text-slate-600')
const statusDot    = (s: string) => ({ active:'bg-emerald-500', inactive:'bg-slate-400', suspended:'bg-rose-500' }[s] || 'bg-slate-400')
const scoreColor   = (n: number) => n >= 80 ? 'text-emerald-600' : n >= 60 ? 'text-amber-600' : 'text-rose-600'
const scoreBg      = (n: number) => n >= 80 ? 'bg-emerald-500' : n >= 60 ? 'bg-amber-500' : 'bg-rose-500'

const addStudent = () => {
  if (!newStudent.value.name || !newStudent.value.email) return
  allStudents.value.push({
    id: Date.now(), ...newStudent.value, exams: 0, avg: 0, status: 'active',
    enrolled: new Date().toLocaleDateString('en-US', { month:'short', day:'2-digit', year:'numeric' }),
    avatar: newStudent.value.name.split(' ').map((w: string) => w[0]).join('').toUpperCase().slice(0,2)
  })
  showAddModal.value = false
}
const confirmDelete  = (s: any) => { selectedStudent.value = s; showDeleteModal.value = true }
const deleteStudent  = () => { allStudents.value = allStudents.value.filter(s => s.id !== selectedStudent.value.id); showDeleteModal.value = false }
const toggleStatus   = (s: any) => { s.status = s.status === 'active' ? 'inactive' : 'active' }
</script>

<template>
  <div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Student Management</h1>
        <p class="text-[13px] text-slate-500 mt-1">View and manage all enrolled students across departments.</p>
      </div>
      <button @click="showAddModal = true; newStudent = {name:'',email:'',year:'Year 1',dept:'',id_no:'',password:''}"
        class="flex items-center gap-2 bg-[#5138ed] hover:bg-indigo-700 text-white text-[13px] font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-indigo-200 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add Student
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-4 gap-4">
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-sky-50 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Students</p><p class="text-[22px] font-bold text-slate-800">{{ stats.total }}</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Active</p><p class="text-[22px] font-bold text-slate-800">{{ stats.active }}</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Avg. Score</p><p class="text-[22px] font-bold text-slate-800">{{ stats.avgScore }}%</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Exams Taken</p><p class="text-[22px] font-bold text-slate-800">{{ stats.exams }}</p></div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
      <!-- Filters -->
      <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-slate-100">
        <div class="relative flex-1 max-w-sm">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input v-model="search" type="text" placeholder="Search by name, email or ID..." class="w-full pl-9 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
        </div>
        <div class="flex items-center gap-3">
          <select v-model="yearFilter" class="text-[13px] border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white">
            <option value="all">All Years</option>
            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
          </select>
          <select v-model="statusFilter" class="text-[13px] border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
          </select>
          <span class="text-[12px] font-semibold text-slate-400">{{ filtered.length }} students</span>
        </div>
      </div>

      <table class="w-full">
        <thead>
          <tr class="bg-slate-50/60 border-b border-slate-100">
            <th class="text-left px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Student</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Student ID</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Department</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Year</th>
            <th class="text-center px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Exams</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Avg Score</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
            <th class="text-right px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <tr v-for="student in paginated" :key="student.id" class="hover:bg-slate-50/40 transition-colors group">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div :class="[avatarColor(student.id), 'w-9 h-9 rounded-xl flex items-center justify-center text-[11px] font-bold text-white shrink-0']">{{ student.avatar }}</div>
                <div>
                  <p class="text-[13px] font-bold text-slate-800">{{ student.name }}</p>
                  <p class="text-[11px] text-slate-400 font-medium">{{ student.email }}</p>
                </div>
              </div>
            </td>
            <td class="px-4 py-4"><span class="text-[12px] font-mono font-bold text-slate-600">{{ student.id_no }}</span></td>
            <td class="px-4 py-4"><p class="text-[12px] font-semibold text-slate-700">{{ student.dept }}</p></td>
            <td class="px-4 py-4"><span class="text-[11px] font-bold bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-lg">{{ student.year }}</span></td>
            <td class="px-4 py-4 text-center"><span class="text-[14px] font-bold text-slate-800">{{ student.exams }}</span></td>
            <td class="px-4 py-4">
              <div class="flex items-center gap-2">
                <div class="flex-1 bg-slate-100 rounded-full h-1.5 max-w-[60px]">
                  <div :class="[scoreBg(student.avg), 'h-1.5 rounded-full']" :style="{ width: student.avg + '%' }"></div>
                </div>
                <span :class="[scoreColor(student.avg), 'text-[12px] font-bold']">{{ student.avg }}%</span>
              </div>
            </td>
            <td class="px-4 py-4">
              <div class="flex items-center gap-1.5">
                <span :class="[statusDot(student.status), 'w-1.5 h-1.5 rounded-full']"></span>
                <span :class="[statusBadge(student.status), 'text-[11px] font-bold px-2 py-0.5 rounded-md capitalize']">{{ student.status }}</span>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="toggleStatus(student)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-amber-500 hover:bg-amber-50 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                </button>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
                <button @click="confirmDelete(student)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="paginated.length === 0">
            <td colspan="8" class="py-16 text-center">
              <div class="flex flex-col items-center gap-3">
                <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center"><svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg></div>
                <p class="text-[14px] font-bold text-slate-600">No students found</p>
                <p class="text-[12px] text-slate-400">Try adjusting your search or filters.</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/30">
        <p class="text-[12px] text-slate-500 font-medium">Showing {{ Math.min((currentPage-1)*perPage+1, filtered.length) }}–{{ Math.min(currentPage*perPage, filtered.length) }} of {{ filtered.length }} students</p>
        <div class="flex items-center gap-1">
          <button @click="currentPage = Math.max(1, currentPage-1)" :disabled="currentPage===1" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
          </button>
          <button v-for="p in totalPages" :key="p" @click="currentPage = p" :class="[currentPage===p ? 'bg-[#5138ed] text-white' : 'text-slate-500 hover:bg-slate-100','w-8 h-8 rounded-lg flex items-center justify-center text-[12px] font-bold transition-colors']">{{ p }}</button>
          <button @click="currentPage = Math.min(totalPages, currentPage+1)" :disabled="currentPage===totalPages" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Add Student Modal -->
    <Teleport to="body">
      <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
          <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div><h3 class="text-[16px] font-bold text-slate-800">Add New Student</h3><p class="text-[12px] text-slate-500 mt-0.5">Create a student account.</p></div>
            <button @click="showAddModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
          </div>
          <div class="px-6 py-5 space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Full Name <span class="text-rose-500">*</span></label><input v-model="newStudent.name" type="text" placeholder="Full name" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Email <span class="text-rose-500">*</span></label><input v-model="newStudent.email" type="email" placeholder="student@wou.et" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Department</label><select v-model="newStudent.dept" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] bg-white"><option value="" disabled>Select dept.</option><option v-for="d in depts" :key="d" :value="d">{{ d }}</option></select></div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Year</label><select v-model="newStudent.year" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] bg-white"><option v-for="y in years" :key="y" :value="y">{{ y }}</option></select></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Student ID</label><input v-model="newStudent.id_no" type="text" placeholder="WU/XXXX/YY" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] font-mono focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Password <span class="text-rose-500">*</span></label><input v-model="newStudent.password" type="password" placeholder="Temporary password" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
            </div>
          </div>
          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            <button @click="showAddModal = false" class="px-5 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">Cancel</button>
            <button @click="addStudent" class="px-5 py-2.5 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-200 transition-all">Create Student</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Delete Modal -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
          <div class="p-6 text-center">
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg></div>
            <h3 class="text-[16px] font-bold text-slate-800 mb-2">Remove Student?</h3>
            <p class="text-[13px] text-slate-500">Remove <span class="font-bold text-slate-700">{{ selectedStudent?.name }}</span>? Their exam records will be retained.</p>
          </div>
          <div class="flex items-center gap-3 px-6 pb-6">
            <button @click="showDeleteModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="deleteStudent" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors">Remove</button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>
