<script setup lang="ts">
import { ref, computed } from 'vue'

// ── State ──
const search = ref('')
const deptFilter = ref('all')
const statusFilter = ref('all')
const viewMode = ref<'table' | 'grid'>('table')
const currentPage = ref(1)
const perPage = 8
const showAddModal = ref(false)
const showDeleteModal = ref(false)
const showViewModal = ref(false)
const selectedInstructor = ref<any>(null)

// ── Dummy Data ──
const allInstructors = ref([
  { id: 1,  name: 'Dr. Abebe Kebede',    email: 'abebe@wou.edu.et',      department: 'Computer Science',    course: 'Database Systems',        courseCode: 'CS-301', exams: 18, students: 142, status: 'active',   joined: 'Jan 12, 2024', avatar: 'AK', phone: '+251 91 234 5678' },
  { id: 2,  name: 'Dr. Fikirte Girma',   email: 'fikirte@wou.edu.et',    department: 'Mathematics',         course: 'Calculus & Analysis',     courseCode: 'MT-201', exams: 12, students: 98,  status: 'active',   joined: 'Feb 03, 2024', avatar: 'FG', phone: '+251 92 345 6789' },
  { id: 3,  name: 'Prof. Yonas Tadesse', email: 'yonas@wou.edu.et',      department: 'Computer Science',    course: 'Software Engineering',    courseCode: 'CS-401', exams: 24, students: 167, status: 'active',   joined: 'Mar 15, 2024', avatar: 'YT', phone: '+251 93 456 7890' },
  { id: 4,  name: 'Dr. Tigist Haile',    email: 'tigist@wou.edu.et',     department: 'Engineering',         course: 'Computer Networks',       courseCode: 'EN-302', exams: 9,  students: 78,  status: 'inactive', joined: 'Apr 20, 2024', avatar: 'TH', phone: '+251 94 567 8901' },
  { id: 5,  name: 'Prof. Dawit Solomon', email: 'dawit@wou.edu.et',      department: 'Information Systems', course: 'Data Structures',         courseCode: 'IS-201', exams: 15, students: 110, status: 'active',   joined: 'May 05, 2024', avatar: 'DS', phone: '+251 95 678 9012' },
  { id: 6,  name: 'Dr. Meron Bekele',    email: 'meron@wou.edu.et',      department: 'Computer Science',    course: 'Algorithms',              courseCode: 'CS-202', exams: 21, students: 134, status: 'active',   joined: 'Jun 10, 2024', avatar: 'MB', phone: '+251 96 789 0123' },
  { id: 7,  name: 'Dr. Tewodros Abay',   email: 'tewodros@wou.edu.et',   department: 'Physics',             course: 'Applied Physics',         courseCode: 'PH-101', exams: 7,  students: 65,  status: 'active',   joined: 'Jul 18, 2024', avatar: 'TA', phone: '+251 97 890 1234' },
  { id: 8,  name: 'Prof. Sara Teferra',  email: 'sara@wou.edu.et',       department: 'Mathematics',         course: 'Linear Algebra',          courseCode: 'MT-301', exams: 11, students: 87,  status: 'active',   joined: 'Aug 22, 2024', avatar: 'ST', phone: '+251 98 901 2345' },
  { id: 9,  name: 'Dr. Berhane Alemu',   email: 'berhane@wou.edu.et',    department: 'Engineering',         course: 'Digital Electronics',     courseCode: 'EN-201', exams: 13, students: 95,  status: 'inactive', joined: 'Sep 03, 2024', avatar: 'BA', phone: '+251 91 012 3456' },
  { id: 10, name: 'Prof. Rahel Tesfaye', email: 'rahel@wou.edu.et',      department: 'Information Systems', course: 'Web Development',         courseCode: 'IS-302', exams: 19, students: 152, status: 'active',   joined: 'Oct 14, 2024', avatar: 'RT', phone: '+251 92 123 4567' },
])

// ── New Instructor Form ──
const newInstructor = ref({ name: '', email: '', department: '', course: '', courseCode: '', phone: '', password: '' })

const departments = ['Computer Science', 'Mathematics', 'Physics', 'Engineering', 'Information Systems']

// ── Computed ──
const filtered = computed(() => {
  return allInstructors.value.filter(i => {
    const matchSearch = i.name.toLowerCase().includes(search.value.toLowerCase()) ||
                        i.email.toLowerCase().includes(search.value.toLowerCase()) ||
                        i.course.toLowerCase().includes(search.value.toLowerCase())
    const matchDept   = deptFilter.value === 'all' || i.department === deptFilter.value
    const matchStatus = statusFilter.value === 'all' || i.status === statusFilter.value
    return matchSearch && matchDept && matchStatus
  })
})

const totalPages  = computed(() => Math.ceil(filtered.value.length / perPage))
const paginated   = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const stats = computed(() => ({
  total:      allInstructors.value.length,
  active:     allInstructors.value.filter(i => i.status === 'active').length,
  depts:      [...new Set(allInstructors.value.map(i => i.department))].length,
  totalExams: allInstructors.value.reduce((s, i) => s + i.exams, 0),
}))

// ── Helpers ──
const avatarColor = (id: number) => {
  const colors = ['bg-indigo-500','bg-sky-500','bg-emerald-500','bg-violet-500','bg-amber-500','bg-rose-500','bg-teal-500','bg-orange-500','bg-cyan-500','bg-purple-500']
  return colors[id % colors.length]
}

const statusBadge = (s: string) => s === 'active'
  ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
  : 'bg-slate-100 text-slate-500 border border-slate-200'

const statusDot = (s: string) => s === 'active' ? 'bg-emerald-500' : 'bg-slate-400'

const deptColor = (dept: string) => {
  const map: Record<string, string> = {
    'Computer Science':    'bg-indigo-50 text-indigo-700',
    'Mathematics':         'bg-sky-50 text-sky-700',
    'Physics':             'bg-amber-50 text-amber-700',
    'Engineering':         'bg-emerald-50 text-emerald-700',
    'Information Systems': 'bg-violet-50 text-violet-700',
  }
  return map[dept] || 'bg-slate-100 text-slate-600'
}

// ── Actions ──
const openView = (instructor: any) => { selectedInstructor.value = instructor; showViewModal.value = true }
const openAdd  = () => { newInstructor.value = { name:'', email:'', department:'', course:'', courseCode:'', phone:'', password:'' }; showAddModal.value = true }
const addInstructor = () => {
  if (!newInstructor.value.name || !newInstructor.value.email) return
  allInstructors.value.push({
    id: Date.now(), ...newInstructor.value,
    exams: 0, students: 0, status: 'active',
    joined: new Date().toLocaleDateString('en-US', { month:'short', day:'2-digit', year:'numeric' }),
    avatar: newInstructor.value.name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0,2)
  })
  showAddModal.value = false
}
const confirmDelete = (instructor: any) => { selectedInstructor.value = instructor; showDeleteModal.value = true }
const deleteInstructor = () => {
  allInstructors.value = allInstructors.value.filter(i => i.id !== selectedInstructor.value.id)
  showDeleteModal.value = false
}
const toggleStatus = (instructor: any) => {
  instructor.status = instructor.status === 'active' ? 'inactive' : 'active'
}
</script>

<template>
  <div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Instructor Management</h1>
        <p class="text-[13px] text-slate-500 mt-1">Create and manage instructor accounts and course assignments.</p>
      </div>
      <button @click="openAdd" class="flex items-center gap-2 bg-[#5138ed] hover:bg-indigo-700 text-white text-[13px] font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-indigo-200 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add Instructor
      </button>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-4 gap-4">
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total</p><p class="text-[22px] font-bold text-slate-800">{{ stats.total }}</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Active</p><p class="text-[22px] font-bold text-slate-800">{{ stats.active }}</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-sky-50 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Departments</p><p class="text-[22px] font-bold text-slate-800">{{ stats.depts }}</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Exams</p><p class="text-[22px] font-bold text-slate-800">{{ stats.totalExams }}</p></div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">

      <!-- Filters -->
      <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-slate-100">
        <div class="relative flex-1 max-w-sm">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input v-model="search" type="text" placeholder="Search instructors or courses..." class="w-full pl-9 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
        </div>
        <div class="flex items-center gap-3">
          <select v-model="deptFilter" class="text-[13px] border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white">
            <option value="all">All Departments</option>
            <option v-for="d in departments" :key="d" :value="d">{{ d }}</option>
          </select>
          <select v-model="statusFilter" class="text-[13px] border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <!-- View Toggle -->
          <div class="flex items-center gap-1 border border-slate-200 rounded-xl p-1">
            <button @click="viewMode = 'table'" :class="[viewMode === 'table' ? 'bg-[#5138ed] text-white' : 'text-slate-400 hover:text-slate-600', 'w-8 h-7 rounded-lg flex items-center justify-center transition-colors']">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            </button>
            <button @click="viewMode = 'grid'" :class="[viewMode === 'grid' ? 'bg-[#5138ed] text-white' : 'text-slate-400 hover:text-slate-600', 'w-8 h-7 rounded-lg flex items-center justify-center transition-colors']">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            </button>
          </div>
          <span class="text-[12px] font-semibold text-slate-400">{{ filtered.length }} found</span>
        </div>
      </div>

      <!-- TABLE VIEW -->
      <template v-if="viewMode === 'table'">
        <table class="w-full">
          <thead>
            <tr class="bg-slate-50/60 border-b border-slate-100">
              <th class="text-left px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Instructor</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Department</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course</th>
              <th class="text-center px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Exams</th>
              <th class="text-center px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Students</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
              <th class="text-right px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr v-for="inst in paginated" :key="inst.id" class="hover:bg-slate-50/40 transition-colors group">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div :class="[avatarColor(inst.id), 'w-10 h-10 rounded-xl flex items-center justify-center text-[12px] font-bold text-white shrink-0']">{{ inst.avatar }}</div>
                  <div>
                    <p class="text-[13px] font-bold text-slate-800">{{ inst.name }}</p>
                    <p class="text-[11px] text-slate-400 font-medium">{{ inst.email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span :class="[deptColor(inst.department), 'text-[11px] font-bold px-2.5 py-1 rounded-lg']">{{ inst.department }}</span>
              </td>
              <td class="px-4 py-4">
                <div>
                  <p class="text-[12px] font-bold text-slate-700">{{ inst.course }}</p>
                  <p class="text-[10px] text-slate-400 font-medium font-mono">{{ inst.courseCode }}</p>
                </div>
              </td>
              <td class="px-4 py-4 text-center">
                <span class="text-[14px] font-bold text-slate-800">{{ inst.exams }}</span>
              </td>
              <td class="px-4 py-4 text-center">
                <span class="text-[14px] font-bold text-slate-800">{{ inst.students }}</span>
              </td>
              <td class="px-4 py-4">
                <div class="flex items-center gap-1.5">
                  <span :class="[statusDot(inst.status), 'w-1.5 h-1.5 rounded-full shrink-0']"></span>
                  <span :class="[statusBadge(inst.status), 'text-[11px] font-bold px-2 py-0.5 rounded-md capitalize']">{{ inst.status }}</span>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button @click="openView(inst)" title="View Profile" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                  </button>
                  <button @click="toggleStatus(inst)" title="Toggle Status" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-amber-500 hover:bg-amber-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                  </button>
                  <button title="Edit" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-sky-500 hover:bg-sky-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                  </button>
                  <button @click="confirmDelete(inst)" title="Delete" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="paginated.length === 0">
              <td colspan="7" class="py-16 text-center">
                <div class="flex flex-col items-center gap-3">
                  <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                  </div>
                  <p class="text-[14px] font-bold text-slate-600">No instructors found</p>
                  <p class="text-[12px] text-slate-400">Try adjusting your search or filters.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </template>

      <!-- GRID VIEW -->
      <template v-else>
        <div class="p-6 grid grid-cols-2 xl:grid-cols-3 gap-4">
          <div v-for="inst in paginated" :key="inst.id" class="border border-slate-100 rounded-2xl p-5 hover:border-indigo-200 hover:shadow-md transition-all group bg-white">
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center gap-3">
                <div :class="[avatarColor(inst.id), 'w-12 h-12 rounded-xl flex items-center justify-center text-[14px] font-bold text-white shrink-0']">{{ inst.avatar }}</div>
                <div>
                  <p class="text-[13px] font-bold text-slate-800 leading-snug">{{ inst.name }}</p>
                  <p class="text-[11px] text-slate-400 font-medium truncate max-w-[140px]">{{ inst.email }}</p>
                </div>
              </div>
              <div class="flex items-center gap-1">
                <span :class="[statusDot(inst.status), 'w-2 h-2 rounded-full']"></span>
              </div>
            </div>
            <div class="space-y-2 mb-4">
              <div class="flex items-center gap-2 text-[12px] text-slate-600">
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span class="font-semibold truncate">{{ inst.department }}</span>
              </div>
              <div class="flex items-center gap-2 text-[12px] text-slate-600">
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="font-semibold truncate">{{ inst.course }}</span>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-2 mb-4">
              <div class="bg-indigo-50 rounded-xl px-3 py-2 text-center">
                <p class="text-[16px] font-bold text-[#5138ed]">{{ inst.exams }}</p>
                <p class="text-[10px] font-semibold text-indigo-400">Exams</p>
              </div>
              <div class="bg-sky-50 rounded-xl px-3 py-2 text-center">
                <p class="text-[16px] font-bold text-sky-600">{{ inst.students }}</p>
                <p class="text-[10px] font-semibold text-sky-400">Students</p>
              </div>
            </div>
            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <button @click="openView(inst)" class="flex-1 py-2 text-[11px] font-bold text-[#5138ed] bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">View</button>
              <button @click="toggleStatus(inst)" class="flex-1 py-2 text-[11px] font-bold text-amber-600 bg-amber-50 rounded-lg hover:bg-amber-100 transition-colors">{{ inst.status === 'active' ? 'Deactivate' : 'Activate' }}</button>
              <button @click="confirmDelete(inst)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </div>
          </div>
          <div v-if="paginated.length === 0" class="col-span-3 py-16 text-center">
            <p class="text-[14px] font-bold text-slate-600">No instructors found</p>
          </div>
        </div>
      </template>

      <!-- Pagination -->
      <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/30">
        <p class="text-[12px] text-slate-500 font-medium">
          Showing {{ Math.min((currentPage - 1) * perPage + 1, filtered.length) }}–{{ Math.min(currentPage * perPage, filtered.length) }} of {{ filtered.length }} instructors
        </p>
        <div class="flex items-center gap-1">
          <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
          </button>
          <button v-for="p in totalPages" :key="p" @click="currentPage = p"
            :class="[currentPage === p ? 'bg-[#5138ed] text-white' : 'text-slate-500 hover:bg-slate-100', 'w-8 h-8 rounded-lg flex items-center justify-center text-[12px] font-bold transition-colors']">
            {{ p }}
          </button>
          <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- ── View Profile Modal ── -->
    <Teleport to="body">
      <div v-if="showViewModal && selectedInstructor" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
          <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <h3 class="text-[16px] font-bold text-slate-800">Instructor Profile</h3>
            <button @click="showViewModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <div class="p-6">
            <!-- Profile Header -->
            <div class="flex items-center gap-4 mb-6 p-4 bg-slate-50 rounded-2xl">
              <div :class="[avatarColor(selectedInstructor.id), 'w-16 h-16 rounded-2xl flex items-center justify-center text-[20px] font-bold text-white shrink-0']">{{ selectedInstructor.avatar }}</div>
              <div>
                <p class="text-[16px] font-bold text-slate-800">{{ selectedInstructor.name }}</p>
                <p class="text-[13px] text-slate-500">{{ selectedInstructor.email }}</p>
                <div class="flex items-center gap-2 mt-1.5">
                  <span :class="[statusBadge(selectedInstructor.status), 'text-[11px] font-bold px-2 py-0.5 rounded-md capitalize']">{{ selectedInstructor.status }}</span>
                  <span :class="[deptColor(selectedInstructor.department), 'text-[11px] font-bold px-2 py-0.5 rounded-md']">{{ selectedInstructor.department }}</span>
                </div>
              </div>
            </div>
            <!-- Details Grid -->
            <div class="grid grid-cols-2 gap-3 mb-6">
              <div class="bg-slate-50 rounded-xl p-3"><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Course</p><p class="text-[12px] font-bold text-slate-700">{{ selectedInstructor.course }}</p></div>
              <div class="bg-slate-50 rounded-xl p-3"><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Course Code</p><p class="text-[12px] font-bold text-slate-700 font-mono">{{ selectedInstructor.courseCode }}</p></div>
              <div class="bg-indigo-50 rounded-xl p-3"><p class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider mb-1">Exams Created</p><p class="text-[20px] font-bold text-[#5138ed]">{{ selectedInstructor.exams }}</p></div>
              <div class="bg-sky-50 rounded-xl p-3"><p class="text-[10px] font-bold text-sky-400 uppercase tracking-wider mb-1">Students</p><p class="text-[20px] font-bold text-sky-600">{{ selectedInstructor.students }}</p></div>
              <div class="bg-slate-50 rounded-xl p-3"><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Phone</p><p class="text-[12px] font-bold text-slate-700">{{ selectedInstructor.phone }}</p></div>
              <div class="bg-slate-50 rounded-xl p-3"><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Joined</p><p class="text-[12px] font-bold text-slate-700">{{ selectedInstructor.joined }}</p></div>
            </div>
          </div>
          <div class="flex items-center gap-3 px-6 pb-6">
            <button @click="showViewModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Close</button>
            <button class="flex-1 py-2.5 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl transition-colors">Edit Profile</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- ── Add Instructor Modal ── -->
    <Teleport to="body">
      <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
          <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div>
              <h3 class="text-[16px] font-bold text-slate-800">Add New Instructor</h3>
              <p class="text-[12px] text-slate-500 mt-0.5">Create an instructor account and assign a course.</p>
            </div>
            <button @click="showAddModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <div class="px-6 py-5 space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                <input v-model="newInstructor.name" type="text" placeholder="e.g. Dr. Abebe Kebede" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Email <span class="text-rose-500">*</span></label>
                <input v-model="newInstructor.email" type="email" placeholder="name@wou.edu.et" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Department <span class="text-rose-500">*</span></label>
                <select v-model="newInstructor.department" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] bg-white">
                  <option value="" disabled>Select department</option>
                  <option v-for="d in departments" :key="d" :value="d">{{ d }}</option>
                </select>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Phone</label>
                <input v-model="newInstructor.phone" type="text" placeholder="+251 9X XXX XXXX" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Course Name <span class="text-rose-500">*</span></label>
                <input v-model="newInstructor.course" type="text" placeholder="e.g. Database Systems" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Course Code</label>
                <input v-model="newInstructor.courseCode" type="text" placeholder="e.g. CS-301" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] font-mono focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              </div>
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Temporary Password <span class="text-rose-500">*</span></label>
              <input v-model="newInstructor.password" type="password" placeholder="Set a temporary password" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              <p class="text-[11px] text-slate-400 mt-1.5 font-medium">The instructor will be prompted to change this on first login.</p>
            </div>
          </div>
          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            <button @click="showAddModal = false" class="px-5 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">Cancel</button>
            <button @click="addInstructor" class="px-5 py-2.5 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-200 transition-all">Create Instructor</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- ── Delete Confirm Modal ── -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
          <div class="p-6 text-center">
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
            </div>
            <h3 class="text-[16px] font-bold text-slate-800 mb-2">Remove Instructor?</h3>
            <p class="text-[13px] text-slate-500 leading-relaxed">
              Are you sure you want to remove <span class="font-bold text-slate-700">{{ selectedInstructor?.name }}</span>? All their exam data will be retained but their account will be deleted.
            </p>
          </div>
          <div class="flex items-center gap-3 px-6 pb-6">
            <button @click="showDeleteModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="deleteInstructor" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors">Remove</button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>
