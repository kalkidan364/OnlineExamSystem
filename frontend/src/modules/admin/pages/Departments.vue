<script setup lang="ts">
import { ref, computed } from 'vue'

const search = ref('')
const statusFilter = ref('all')
const currentPage = ref(1)
const perPage = 8
const showAddModal = ref(false)
const showDeleteModal = ref(false)
const selectedDept = ref<any>(null)

const allDepts = ref([
  { id: 1, name: 'Computer Science',    head: 'Dr. Abebe Kebede',   code: 'CS',   instructors: 14, students: 441, courses: 18, status: 'active',   established: '2010' },
  { id: 2, name: 'Information Systems', head: 'Prof. Dawit Solomon',code: 'IS',   instructors: 9,  students: 238, courses: 12, status: 'active',   established: '2012' },
  { id: 3, name: 'Mathematics',         head: 'Dr. Fikirte Girma',  code: 'MATH', instructors: 12, students: 185, courses: 15, status: 'active',   established: '2008' },
  { id: 4, name: 'Engineering',         head: 'Dr. Tigist Haile',   code: 'ENG',  instructors: 16, students: 312, courses: 24, status: 'active',   established: '2015' },
  { id: 5, name: 'Physics',             head: 'Dr. Tewodros Abay',  code: 'PHYS', instructors: 8,  students: 115, courses: 10, status: 'active',   established: '2009' },
  { id: 6, name: 'Software Engineering',head: 'Prof. Yonas Tadesse',code: 'SE',   instructors: 11, students: 280, courses: 16, status: 'active',   established: '2018' },
  { id: 7, name: 'Data Science',        head: 'Dr. Meron Bekele',   code: 'DS',   instructors: 5,  students: 85,  courses: 8,  status: 'inactive', established: '2023' },
])

const newDept = ref({ name: '', head: '', code: '', established: '' })

const filtered = computed(() =>
  allDepts.value.filter(d => {
    const matchSearch = d.name.toLowerCase().includes(search.value.toLowerCase()) ||
                        d.head.toLowerCase().includes(search.value.toLowerCase()) ||
                        d.code.toLowerCase().includes(search.value.toLowerCase())
    const matchStatus = statusFilter.value === 'all' || d.status === statusFilter.value
    return matchSearch && matchStatus
  })
)

const totalPages = computed(() => Math.ceil(filtered.value.length / perPage))
const paginated  = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const stats = computed(() => ({
  total:       allDepts.value.length,
  active:      allDepts.value.filter(d => d.status === 'active').length,
  instructors: allDepts.value.reduce((a, d) => a + d.instructors, 0),
  students:    allDepts.value.reduce((a, d) => a + d.students, 0),
}))

const deptColor = (id: number) => {
  const map = ['bg-indigo-50 text-indigo-700', 'bg-sky-50 text-sky-700', 'bg-amber-50 text-amber-700', 'bg-emerald-50 text-emerald-700', 'bg-rose-50 text-rose-700', 'bg-violet-50 text-violet-700', 'bg-orange-50 text-orange-700']
  return map[id % map.length]
}

const addDept = () => {
  if (!newDept.value.name || !newDept.value.code) return
  allDepts.value.push({ id: Date.now(), ...newDept.value, instructors: 0, students: 0, courses: 0, status: 'active' })
  showAddModal.value = false
}
const confirmDelete = (d: any) => { selectedDept.value = d; showDeleteModal.value = true }
const deleteDept    = () => { allDepts.value = allDepts.value.filter(d => d.id !== selectedDept.value.id); showDeleteModal.value = false }
const toggleStatus  = (d: any) => { d.status = d.status === 'active' ? 'inactive' : 'active' }
</script>

<template>
  <div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Department Management</h1>
        <p class="text-[13px] text-slate-500 mt-1">Manage academic departments, heads, and view overall statistics.</p>
      </div>
      <button @click="showAddModal = true; newDept = {name:'',head:'',code:'',established:''}" class="flex items-center gap-2 bg-[#5138ed] hover:bg-indigo-700 text-white text-[13px] font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-indigo-200 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add Department
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-4 gap-4">
      <div v-for="(item, i) in [
        { label:'Total Departments', val: stats.total,       bg:'bg-indigo-50', ic:'text-[#5138ed]',  icon:'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
        { label:'Active',            val: stats.active,      bg:'bg-emerald-50',ic:'text-emerald-500', icon:'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
        { label:'Total Instructors', val: stats.instructors, bg:'bg-amber-50',  ic:'text-amber-500',   icon:'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
        { label:'Total Students',    val: stats.students,    bg:'bg-sky-50',    ic:'text-sky-500',     icon:'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
      ]" :key="i" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div :class="[item.bg, 'w-11 h-11 rounded-xl flex items-center justify-center shrink-0']">
          <svg class="w-5 h-5" :class="item.ic" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">{{ item.label }}</p><p class="text-[22px] font-bold text-slate-800">{{ item.val }}</p></div>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
      <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-slate-100">
        <div class="relative flex-1 max-w-sm">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input v-model="search" placeholder="Search departments or heads..." class="w-full pl-9 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
        </div>
        <div class="flex items-center gap-3">
          <select v-model="statusFilter" class="text-[13px] border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <span class="text-[12px] font-semibold text-slate-400">{{ filtered.length }} departments</span>
        </div>
      </div>

      <table class="w-full">
        <thead>
          <tr class="bg-slate-50/60 border-b border-slate-100">
            <th class="text-left px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Department</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Department Head</th>
            <th class="text-center px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Instructors</th>
            <th class="text-center px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Students</th>
            <th class="text-center px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Courses</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
            <th class="text-right px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <tr v-for="dept in paginated" :key="dept.id" class="hover:bg-slate-50/40 transition-colors group">
            <td class="px-6 py-4">
              <div>
                <p class="text-[13px] font-bold text-slate-800 leading-snug">{{ dept.name }}</p>
                <div class="flex items-center gap-2 mt-0.5">
                  <span :class="[deptColor(dept.id), 'text-[10px] font-bold font-mono px-1.5 py-0.5 rounded']">{{ dept.code }}</span>
                  <span class="text-[11px] text-slate-400 font-medium">Est. {{ dept.established }}</span>
                </div>
              </div>
            </td>
            <td class="px-4 py-4"><p class="text-[12px] font-semibold text-slate-700">{{ dept.head }}</p></td>
            <td class="px-4 py-4 text-center"><span class="text-[13px] font-bold text-slate-800">{{ dept.instructors }}</span></td>
            <td class="px-4 py-4 text-center"><span class="text-[13px] font-bold text-slate-800">{{ dept.students }}</span></td>
            <td class="px-4 py-4 text-center"><span class="text-[13px] font-bold text-slate-800">{{ dept.courses }}</span></td>
            <td class="px-4 py-4">
              <span :class="[dept.status === 'active' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200', 'text-[11px] font-bold px-2 py-0.5 rounded-md capitalize']">
                {{ dept.status }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="toggleStatus(dept)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-amber-500 hover:bg-amber-50 transition-colors" title="Toggle Status">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                </button>
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 transition-colors" title="Edit">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
                <button @click="confirmDelete(dept)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors" title="Delete">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="paginated.length === 0">
            <td colspan="7" class="py-16 text-center">
              <div class="flex flex-col items-center gap-3">
                <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center"><svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>
                <p class="text-[14px] font-bold text-slate-600">No departments found</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/30">
        <p class="text-[12px] text-slate-500 font-medium">Showing {{ Math.min((currentPage-1)*perPage+1, filtered.length) }}–{{ Math.min(currentPage*perPage, filtered.length) }} of {{ filtered.length }} departments</p>
        <div class="flex items-center gap-1">
          <button @click="currentPage = Math.max(1, currentPage-1)" :disabled="currentPage===1" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
          <button v-for="p in totalPages" :key="p" @click="currentPage = p" :class="[currentPage===p?'bg-[#5138ed] text-white':'text-slate-500 hover:bg-slate-100','w-8 h-8 rounded-lg flex items-center justify-center text-[12px] font-bold transition-colors']">{{ p }}</button>
          <button @click="currentPage = Math.min(totalPages, currentPage+1)" :disabled="currentPage===totalPages" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
        </div>
      </div>
    </div>

    <!-- Add Modal -->
    <Teleport to="body">
      <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
          <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div><h3 class="text-[16px] font-bold text-slate-800">Add New Department</h3><p class="text-[12px] text-slate-500 mt-0.5">Register a new academic department.</p></div>
            <button @click="showAddModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
          </div>
          <div class="px-6 py-5 space-y-4">
            <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Department Name <span class="text-rose-500">*</span></label><input v-model="newDept.name" type="text" placeholder="e.g. Computer Science" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Dept Code <span class="text-rose-500">*</span></label><input v-model="newDept.code" type="text" placeholder="e.g. CS" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] font-mono focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Established Year</label><input v-model="newDept.established" type="number" placeholder="e.g. 2010" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
            </div>
            <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Department Head</label><input v-model="newDept.head" type="text" placeholder="e.g. Dr. Abebe Kebede" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
          </div>
          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            <button @click="showAddModal = false" class="px-5 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">Cancel</button>
            <button @click="addDept" class="px-5 py-2.5 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl shadow-sm transition-all">Add Department</button>
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
            <h3 class="text-[16px] font-bold text-slate-800 mb-2">Delete Department?</h3>
            <p class="text-[13px] text-slate-500">Delete <span class="font-bold text-slate-700">{{ selectedDept?.name }}</span>? Ensure no active courses or instructors are assigned first.</p>
          </div>
          <div class="flex items-center gap-3 px-6 pb-6">
            <button @click="showDeleteModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="deleteDept" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors">Delete</button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>
