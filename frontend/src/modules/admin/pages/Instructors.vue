<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import apiClient from '../../../core/api/apiClient'

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
const isLoading = ref(false)

const allInstructors = ref<any[]>([])
const allDepartments = ref<any[]>([])

// ── New Instructor Form ──
const newInstructor = ref({ name: '', email: '', department_id: '', phone: '', password: '' })

// ── Fetch Instructors ──
const fetchInstructors = async () => {
  try {
    const res = await apiClient.get('/admin/users?role=instructor')
    allInstructors.value = (res.data.data || []).map((u: any) => ({
      ...u,
      avatar: u.name ? u.name.split(' ').map((w: string) => w[0]).join('').toUpperCase().slice(0, 2) : '??',
      status: 'active',
      joined: new Date(u.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
      departmentName: u.department?.name || '—',
      exams: 0, // Mock exams count
      students: 0 // Mock students count
    }))
  } catch (err) { console.error('Failed to fetch instructors:', err) }
}

// ── Fetch Departments ──
const fetchDepartments = async () => {
  try {
    const res = await apiClient.get('/admin/departments')
    allDepartments.value = res.data.data || []
  } catch (err) { console.error('Failed to fetch departments:', err) }
}

onMounted(async () => { await Promise.all([fetchInstructors(), fetchDepartments()]) })

// ── Computed ──
const filtered = computed(() => {
  return allInstructors.value.filter(i => {
    const matchSearch = i.name.toLowerCase().includes(search.value.toLowerCase()) ||
                        i.email.toLowerCase().includes(search.value.toLowerCase()) ||
                        (i.course_name && i.course_name.toLowerCase().includes(search.value.toLowerCase()))
    const matchDept   = deptFilter.value === 'all' || i.departmentName === deptFilter.value
    const matchStatus = statusFilter.value === 'all' || i.status === statusFilter.value
    return matchSearch && matchDept && matchStatus
  })
})

const totalPages  = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))
const paginated   = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const stats = computed(() => ({
  total:      allInstructors.value.length,
  active:     allInstructors.value.filter(i => i.status === 'active').length,
  depts:      [...new Set(allInstructors.value.map(i => i.departmentName))].length,
  totalExams: allInstructors.value.reduce((s, i) => s + (i.exams || 0), 0),
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
const openAdd  = () => { newInstructor.value = { name:'', email:'', department_id:'', phone:'', password:'' }; showAddModal.value = true }

const addInstructor = async () => {
  if (!newInstructor.value.name || !newInstructor.value.email || !newInstructor.value.password) return
  isLoading.value = true
  try {
    await apiClient.post('/admin/users', {
      name: newInstructor.value.name,
      email: newInstructor.value.email,
      role: 'instructor',
      department_id: newInstructor.value.department_id || null,
      password: newInstructor.value.password,
    })
    await fetchInstructors()
    showAddModal.value = false
  } catch (err: any) {
    let msg = 'Failed to create instructor.'
    if (err.response?.data) {
      msg = err.response.data.message || msg
      if (err.response.data.errors) {
        msg += '\n' + Object.values(err.response.data.errors).flat().join('\n')
      }
    }
    alert(msg)
  } finally { isLoading.value = false }
}

const confirmDelete = (instructor: any) => { selectedInstructor.value = instructor; showDeleteModal.value = true }
const deleteInstructor = async () => {
  if (!selectedInstructor.value) return
  isLoading.value = true
  try {
    await apiClient.delete(`/admin/users/${selectedInstructor.value.id}`)
    await fetchInstructors()
    showDeleteModal.value = false
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete instructor.')
  } finally { isLoading.value = false }
}
const toggleStatus = (instructor: any) => {
  instructor.status = instructor.status === 'active' ? 'inactive' : 'active'
}


// Supress TS errors for unused vars in the mockup
console.log(viewMode, totalPages, paginated, stats, statusDot, openView, confirmDelete, toggleStatus, avatarColor, statusBadge, deptColor)
</script>

<template>
  <div class="space-y-6 pb-12">
    <!-- List View -->
    <div v-if="!showAddModal" class="space-y-6">
      
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-start gap-4">
        <div class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2m4-10a4 4 0 118 0 4 4 0 01-8 0z"></path></svg>
        </div>
        <div>
          <h1 class="text-[24px] font-bold text-slate-800">Instructors</h1>
          <p class="text-[13px] text-slate-500 mt-0.5">Manage all instructors and their information across the system.</p>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <div class="relative">
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input type="text" placeholder="Search Instructors..." class="w-64 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-[13px] font-medium text-slate-600 focus:outline-none focus:border-[#5138ed] bg-white">
        </div>
        <button class="flex items-center gap-2 px-4 py-2 bg-white border border-[#5138ed] text-[#5138ed] rounded-xl text-[13px] font-bold hover:bg-[#5138ed]/5 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg> Filter
        </button>
        <button class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-[#5138ed] font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Export Instructors
        </button>
        <button @click="openAdd" class="flex items-center gap-2 px-5 py-2 bg-[#5138ed] text-white rounded-xl text-[13px] font-bold hover:bg-indigo-700 shadow-sm transition-all">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Add New Instructor
        </button>
      </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-5 gap-5">
      <!-- Total -->
      <div class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col justify-between shadow-sm">
        <div class="flex items-start justify-between">
          <div class="w-10 h-10 rounded-xl bg-[#5138ed]/10 text-[#5138ed] flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          </div>
          <div class="text-right">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Instructors</p>
            <p class="text-[22px] font-bold text-slate-800">186</p>
          </div>
        </div>
        <div class="mt-4 flex items-center gap-2">
          <div class="flex items-center text-[10px] font-bold text-emerald-500"><svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg> 8.3%</div>
          <span class="text-[11px] text-slate-400 font-medium">All registered instructors</span>
        </div>
      </div>
      <!-- Active -->
      <div class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col justify-between shadow-sm">
        <div class="flex items-start justify-between">
          <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
          </div>
          <div class="text-right">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Active Instructors</p>
            <p class="text-[22px] font-bold text-slate-800">172</p>
          </div>
        </div>
        <div class="mt-4 flex items-center gap-2">
          <div class="flex items-center text-[10px] font-bold text-emerald-500"><svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg> 9.6%</div>
          <span class="text-[11px] text-slate-400 font-medium">Active instructors</span>
        </div>
      </div>
      <!-- Inactive -->
      <div class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col justify-between shadow-sm">
        <div class="flex items-start justify-between">
          <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path></svg>
          </div>
          <div class="text-right">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Inactive Instructors</p>
            <p class="text-[22px] font-bold text-slate-800">14</p>
          </div>
        </div>
        <div class="mt-4 flex items-center gap-2">
          <div class="flex items-center text-[10px] font-bold text-rose-500"><svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg> 12.5%</div>
          <span class="text-[11px] text-slate-400 font-medium">Inactive instructors</span>
        </div>
      </div>
      <!-- New -->
      <div class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col justify-between shadow-sm">
        <div class="flex items-start justify-between">
          <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-500 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
          </div>
          <div class="text-right">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">New Instructors</p>
            <p class="text-[22px] font-bold text-slate-800">9</p>
          </div>
        </div>
        <div class="mt-4 flex items-center gap-2">
          <div class="flex items-center text-[10px] font-bold text-emerald-500"><svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg> 12.5%</div>
          <span class="text-[11px] text-slate-400 font-medium">This month</span>
        </div>
      </div>
      <!-- Verified -->
      <div class="bg-white border border-slate-100 rounded-2xl p-5 flex flex-col justify-between shadow-sm">
        <div class="flex items-start justify-between">
          <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
          </div>
          <div class="text-right">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Verified Accounts</p>
            <p class="text-[22px] font-bold text-slate-800">178</p>
          </div>
        </div>
        <div class="mt-4 flex items-center gap-2">
          <div class="flex items-center text-[10px] font-bold text-emerald-500"><svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg> 7.1%</div>
          <span class="text-[11px] text-slate-400 font-medium">Verified accounts</span>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-[1fr_320px] gap-6">
      
      <!-- Left Column: Table & Filters -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm flex flex-col min-w-0">
        <!-- Table Filters -->
        <div class="p-5 border-b border-slate-100 flex items-center gap-3 overflow-x-auto">
          <div class="relative w-64 shrink-0">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" placeholder="Search Instructors..." class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-[12px] focus:outline-none focus:border-[#5138ed]">
          </div>
          <select class="border border-slate-200 rounded-xl px-4 py-2 text-[12px] font-medium text-slate-600 focus:outline-none focus:border-[#5138ed] min-w-[140px] shrink-0 bg-white">
            <option>All Departments</option>
          </select>
          <select class="border border-slate-200 rounded-xl px-4 py-2 text-[12px] font-medium text-slate-600 focus:outline-none focus:border-[#5138ed] min-w-[120px] shrink-0 bg-white">
            <option>All Status</option>
          </select>
          <select class="border border-slate-200 rounded-xl px-4 py-2 text-[12px] font-medium text-slate-600 focus:outline-none focus:border-[#5138ed] min-w-[130px] shrink-0 bg-white">
            <option>Qualification</option>
          </select>
          <div class="relative shrink-0">
            <input type="text" value="Join Date" class="w-[120px] border border-slate-200 rounded-xl px-4 py-2 text-[12px] font-medium text-slate-600 focus:outline-none focus:border-[#5138ed] bg-white">
            <svg class="w-4 h-4 text-slate-400 absolute right-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          </div>
          <button class="flex items-center gap-2 px-4 py-2 border border-[#5138ed] text-[#5138ed] rounded-xl text-[12px] font-bold hover:bg-[#5138ed]/5 transition-colors shrink-0 ml-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg> Filter
          </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[1000px]">
            <thead>
              <tr class="bg-slate-50/50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="px-6 py-4">Instructor</th>
                <th class="px-4 py-4">Email</th>
                <th class="px-4 py-4">Department</th>
                <th class="px-4 py-4">Qualification</th>
                <th class="px-4 py-4">Phone</th>
                <th class="px-4 py-4">Status</th>
                <th class="px-4 py-4">Join Date</th>
                <th class="px-4 py-4">Last Login</th>
                <th class="px-6 py-4 text-center">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=Abebe+Kebede&background=e0e7ff&color=4f46e5" class="w-8 h-8 rounded-full" alt="">
                    <span class="text-[12px] font-bold text-slate-800">Abebe Kebede</span>
                  </div>
                </td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">abebe@wu.edu.et</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-600">Computer Science</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">MSc. Computer Science</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">+251 91 234 5678</td>
                <td class="px-4 py-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-500 text-[10px] font-bold rounded-md">Active</span></td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">May 12, 2025</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">Today, 10:30 AM</td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2.5 text-slate-400">
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                    <button class="hover:text-rose-500 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    <button class="hover:text-slate-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button>
                  </div>
                </td>
              </tr>
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=Michael+Tadesse&background=dcfce7&color=16a34a" class="w-8 h-8 rounded-full" alt="">
                    <span class="text-[12px] font-bold text-slate-800">Michael Tadesse</span>
                  </div>
                </td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">michael@wu.edu.et</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-600">Database Systems</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">MSc. Data Science</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">+251 91 678 9012</td>
                <td class="px-4 py-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-500 text-[10px] font-bold rounded-md">Active</span></td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">Jan 05, 2025</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">Today, 11:05 AM</td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2.5 text-slate-400">
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                    <button class="hover:text-rose-500 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    <button class="hover:text-slate-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button>
                  </div>
                </td>
              </tr>
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=Yared+Bekele&background=fef3c7&color=d97706" class="w-8 h-8 rounded-full" alt="">
                    <span class="text-[12px] font-bold text-slate-800">Yared Bekele</span>
                  </div>
                </td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">yared@wu.edu.et</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-600">Information Systems</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">MSc. Information Sys.</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">+251 91 890 1234</td>
                <td class="px-4 py-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-500 text-[10px] font-bold rounded-md">Active</span></td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">Dec 15, 2024</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">Today, 08:45 AM</td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2.5 text-slate-400">
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                    <button class="hover:text-rose-500 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    <button class="hover:text-slate-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button>
                  </div>
                </td>
              </tr>
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=Bethelhem+Desta&background=e0f2fe&color=0284c7" class="w-8 h-8 rounded-full" alt="">
                    <span class="text-[12px] font-bold text-slate-800">Bethelhem Desta</span>
                  </div>
                </td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">bethelhem@wu.edu.et</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-600">ICT</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">MSc. Information Tech.</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">+251 91 123 4567</td>
                <td class="px-4 py-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-500 text-[10px] font-bold rounded-md">Active</span></td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">Oct 22, 2024</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">Yesterday, 05:30 PM</td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2.5 text-slate-400">
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                    <button class="hover:text-rose-500 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    <button class="hover:text-slate-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button>
                  </div>
                </td>
              </tr>
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=Biruk+Mengesha&background=ffe4e6&color=e11d48" class="w-8 h-8 rounded-full" alt="">
                    <span class="text-[12px] font-bold text-slate-800">Biruk Mengesha</span>
                  </div>
                </td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">biruk@wu.edu.et</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-600">Software Engineering</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">MSc. Software Eng.</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">+251 91 345 6789</td>
                <td class="px-4 py-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-500 text-[10px] font-bold rounded-md">Active</span></td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">May 27, 2025</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">Today, 09:15 AM</td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2.5 text-slate-400">
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                    <button class="hover:text-rose-500 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    <button class="hover:text-slate-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button>
                  </div>
                </td>
              </tr>
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=Liya+Mekonnen&background=f3e8ff&color=9333ea" class="w-8 h-8 rounded-full" alt="">
                    <span class="text-[12px] font-bold text-slate-800">Liya Mekonnen</span>
                  </div>
                </td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">liya@wu.edu.et</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-600">Software Engineering</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">MSc. Software Eng.</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">+251 91 901 2345</td>
                <td class="px-4 py-4"><span class="px-2.5 py-1 bg-rose-50 text-rose-500 text-[10px] font-bold rounded-md">Inactive</span></td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">Nov 30, 2024</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">3 weeks ago</td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2.5 text-slate-400">
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                    <button class="hover:text-rose-500 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    <button class="hover:text-slate-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button>
                  </div>
                </td>
              </tr>
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=Hailu+Asfaw&background=d1fae5&color=059669" class="w-8 h-8 rounded-full" alt="">
                    <span class="text-[12px] font-bold text-slate-800">Hailu Asfaw</span>
                  </div>
                </td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">hailu@wu.edu.et</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-600">Computer Science</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">MSc. Computer Science</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">+251 91 456 7890</td>
                <td class="px-4 py-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-500 text-[10px] font-bold rounded-md">Active</span></td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">Apr 18, 2025</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">Yesterday, 02:10 PM</td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2.5 text-slate-400">
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                    <button class="hover:text-rose-500 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    <button class="hover:text-slate-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button>
                  </div>
                </td>
              </tr>
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=Samuel+Getachew&background=e0e7ff&color=4f46e5" class="w-8 h-8 rounded-full" alt="">
                    <span class="text-[12px] font-bold text-slate-800">Samuel Getachew</span>
                  </div>
                </td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">samuel@wu.edu.et</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-600">Information Systems</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">MSc. Information Sys.</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">+251 91 567 8901</td>
                <td class="px-4 py-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-500 text-[10px] font-bold rounded-md">Active</span></td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">May 26, 2025</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">Today, 09:20 AM</td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2.5 text-slate-400">
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                    <button class="hover:text-rose-500 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    <button class="hover:text-slate-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button>
                  </div>
                </td>
              </tr>
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=Tsion+Abera&background=fef3c7&color=d97706" class="w-8 h-8 rounded-full" alt="">
                    <span class="text-[12px] font-bold text-slate-800">Tsion Abera</span>
                  </div>
                </td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">tsion@wu.edu.et</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-600">Database Systems</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">MSc. Data Science</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">+251 91 234 8901</td>
                <td class="px-4 py-4"><span class="px-2.5 py-1 bg-rose-50 text-rose-500 text-[10px] font-bold rounded-md">Inactive</span></td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">Feb 10, 2025</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">2 weeks ago</td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2.5 text-slate-400">
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                    <button class="hover:text-rose-500 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    <button class="hover:text-slate-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button>
                  </div>
                </td>
              </tr>
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=Dagmawi+Kassaye&background=e0f2fe&color=0284c7" class="w-8 h-8 rounded-full" alt="">
                    <span class="text-[12px] font-bold text-slate-800">Dagmawi Kassaye</span>
                  </div>
                </td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">dagmawi@wu.edu.et</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-600">ICT</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">MSc. Information Tech.</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">+251 91 789 0123</td>
                <td class="px-4 py-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-500 text-[10px] font-bold rounded-md">Active</span></td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">Mar 05, 2025</td>
                <td class="px-4 py-4 text-[12px] font-medium text-slate-500">Today, 12:00 PM</td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2.5 text-slate-400">
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                    <button class="hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                    <button class="hover:text-rose-500 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    <button class="hover:text-slate-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button>
                  </div>
                </td>
              </tr>

            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="p-5 border-t border-slate-100 flex items-center justify-between">
          <p class="text-[12px] text-slate-500 font-medium">Showing 1 to 10 of 186 instructors</p>
          <div class="flex items-center gap-1.5">
            <button class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-50 transition-colors border border-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
            <button class="w-8 h-8 rounded-xl flex items-center justify-center bg-[#5138ed] text-white text-[12px] font-bold">1</button>
            <button class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-600 text-[12px] font-bold hover:bg-slate-50 transition-colors">2</button>
            <button class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-600 text-[12px] font-bold hover:bg-slate-50 transition-colors">3</button>
            <span class="text-slate-400 text-[12px] mx-1">...</span>
            <button class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-600 text-[12px] font-bold hover:bg-slate-50 transition-colors">19</button>
            <button class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-50 transition-colors border border-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
          </div>
        </div>
      </div>

      <!-- Right Column: Sidebar -->
      <div class="space-y-6">
        
        <!-- Instructor Overview -->
        <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between mb-6">
            <h4 class="text-[13px] font-bold text-slate-800 flex items-center gap-2">
              <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
              Instructor Overview
            </h4>
            <button class="text-slate-400 hover:text-[#5138ed]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg></button>
          </div>
          <div class="flex items-center justify-center relative mb-8">
            <div class="relative w-36 h-36">
              <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                <circle class="text-slate-50" stroke-width="4" stroke="currentColor" fill="none" r="15.9155" cx="18" cy="18" />
                <circle class="text-emerald-500" stroke-width="4" stroke-dasharray="92.5, 100" stroke="currentColor" fill="none" stroke-linecap="round" r="15.9155" cx="18" cy="18" />
                <circle class="text-rose-500 transform -rotate-[33deg] origin-center" stroke-width="4" stroke-dasharray="7.5, 100" stroke="currentColor" fill="none" stroke-linecap="round" r="15.9155" cx="18" cy="18" />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-[24px] font-bold text-slate-800 leading-tight">186</span>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total Instructors</span>
              </div>
            </div>
          </div>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2 text-[12px] font-bold text-slate-700"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Active</div>
              <span class="text-[11px] text-slate-500 font-medium">172 (92.5%)</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2 text-[12px] font-bold text-slate-700"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Inactive</div>
              <span class="text-[11px] text-slate-500 font-medium">14 (7.5%)</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2 text-[12px] font-bold text-slate-700"><span class="w-2 h-2 rounded-full bg-[#5138ed]"></span> New This Month</div>
              <span class="text-[11px] text-slate-500 font-medium">9 (4.8%)</span>
            </div>
          </div>
        </div>

        <!-- Top Departments -->
        <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
          <h4 class="text-[13px] font-bold text-slate-800 mb-5">Top Departments</h4>
          <div class="space-y-4">
            <div class="flex items-center justify-between group cursor-pointer">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-[#5138ed]/10 group-hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>
                <span class="text-[12px] font-bold text-slate-600 group-hover:text-[#5138ed] transition-colors">Computer Science</span>
              </div>
              <span class="text-[12px] font-bold text-slate-800">56</span>
            </div>
            <div class="flex items-center justify-between group cursor-pointer">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-[#5138ed]/10 group-hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg></div>
                <span class="text-[12px] font-bold text-slate-600 group-hover:text-[#5138ed] transition-colors">Software Engineering</span>
              </div>
              <span class="text-[12px] font-bold text-slate-800">42</span>
            </div>
            <div class="flex items-center justify-between group cursor-pointer">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-[#5138ed]/10 group-hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg></div>
                <span class="text-[12px] font-bold text-slate-600 group-hover:text-[#5138ed] transition-colors">Information Systems</span>
              </div>
              <span class="text-[12px] font-bold text-slate-800">28</span>
            </div>
            <div class="flex items-center justify-between group cursor-pointer">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-[#5138ed]/10 group-hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg></div>
                <span class="text-[12px] font-bold text-slate-600 group-hover:text-[#5138ed] transition-colors">Database Systems</span>
              </div>
              <span class="text-[12px] font-bold text-slate-800">22</span>
            </div>
            <div class="flex items-center justify-between group cursor-pointer">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-[#5138ed]/10 group-hover:text-[#5138ed] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></div>
                <span class="text-[12px] font-bold text-slate-600 group-hover:text-[#5138ed] transition-colors">ICT</span>
              </div>
              <span class="text-[12px] font-bold text-slate-800">18</span>
            </div>
          </div>
          <button class="w-full mt-5 text-[12px] font-bold text-[#5138ed] text-center hover:text-indigo-700 transition-colors">View All</button>
        </div>

        <!-- Recent Registrations -->
        <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
          <h4 class="text-[13px] font-bold text-slate-800 mb-5">Recent Registrations</h4>
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name=Hailu+Asfaw&background=e0e7ff&color=4f46e5" class="w-8 h-8 rounded-full" alt="">
                <div>
                  <p class="text-[12px] font-bold text-slate-700">Hailu Asfaw</p>
                  <p class="text-[10px] text-slate-400 font-medium">Computer Science</p>
                </div>
              </div>
              <div class="text-[10px] text-slate-400 text-right">
                <p>May 27, 2025</p>
                <p>09:15 AM</p>
              </div>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name=Abebe+Kebede&background=e0e7ff&color=4f46e5" class="w-8 h-8 rounded-full" alt="">
                <div>
                  <p class="text-[12px] font-bold text-slate-700">Abebe Kebede</p>
                  <p class="text-[10px] text-slate-400 font-medium">Computer Science</p>
                </div>
              </div>
              <div class="text-[10px] text-slate-400 text-right">
                <p>May 27, 2025</p>
                <p>08:42 AM</p>
              </div>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name=Samuel+Getachew&background=e0e7ff&color=4f46e5" class="w-8 h-8 rounded-full" alt="">
                <div>
                  <p class="text-[12px] font-bold text-slate-700">Samuel Getachew</p>
                  <p class="text-[10px] text-slate-400 font-medium">Information Systems</p>
                </div>
              </div>
              <div class="text-[10px] text-slate-400 text-right">
                <p>May 26, 2025</p>
                <p>03:18 PM</p>
              </div>
            </div>
          </div>
          <button class="w-full mt-5 text-[12px] font-bold text-[#5138ed] text-center hover:text-indigo-700 transition-colors">View All</button>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
          <h4 class="text-[13px] font-bold text-slate-800 mb-4">Quick Actions</h4>
          <div class="grid grid-cols-2 gap-3">
            <button class="flex items-center gap-2 p-3 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:text-[#5138ed] transition-colors group text-left">
              <div class="text-[#5138ed] shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg></div>
              <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed] transition-colors leading-tight">Add Instructor</span>
            </button>
            <button class="flex items-center gap-2 p-3 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:text-[#5138ed] transition-colors group text-left">
              <div class="text-[#5138ed] shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg></div>
              <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed] transition-colors leading-tight">Import Instructors</span>
            </button>
            <button class="flex items-center gap-2 p-3 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:text-[#5138ed] transition-colors group text-left">
              <div class="text-[#5138ed] shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg></div>
              <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed] transition-colors leading-tight">Export Instructors</span>
            </button>
            <button class="flex items-center gap-2 p-3 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:text-[#5138ed] transition-colors group text-left">
              <div class="text-[#5138ed] shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>
              <span class="text-[11px] font-bold text-slate-600 group-hover:text-[#5138ed] transition-colors leading-tight">Manage Departments</span>
            </button>
          </div>
        </div>

      </div>
    </div>


  
    </div>

    <!-- Add Form View -->
    <div v-else class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-start gap-4">
          <div class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
          </div>
          <div>
            <h1 class="text-[24px] font-bold text-slate-800">Add New Instructor</h1>
            <p class="text-[13px] text-slate-500 mt-0.5">Create a new instructor account and set their permissions.</p>
            <div class="flex items-center gap-2 mt-2 text-[12px] font-medium text-slate-400">
              <button @click="showAddModal = false" class="hover:text-[#5138ed] flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Instructors
              </button>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              <span class="text-slate-700">Add New Instructor</span>
            </div>
          </div>
        </div>
        <button @click="showAddModal = false" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Instructors
        </button>
      </div>

      <!-- Main Form Area -->
      <div class="grid grid-cols-[1fr_340px] gap-8">
        
        <!-- Left Column: Form -->
        <div class="space-y-8">
          
          <!-- Personal Information -->
          <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm">
            <h3 class="text-[15px] font-bold text-slate-800 mb-6">Personal Information</h3>
            <div class="grid grid-cols-3 gap-6 mb-6">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Full Name <span class="text-rose-500">*</span></label>
                <input v-model="newInstructor.name" type="text" placeholder="Enter full name" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#5138ed]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Email Address <span class="text-rose-500">*</span></label>
                <input v-model="newInstructor.email" type="email" placeholder="Enter email address" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#5138ed]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Phone Number <span class="text-rose-500">*</span></label>
                <input v-model="newInstructor.phone" type="text" placeholder="Enter phone number" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#5138ed]">
              </div>
            </div>
            <div class="grid grid-cols-3 gap-6">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Date of Birth</label>
                <div class="relative">
                  <input type="text" placeholder="Select date of birth" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#5138ed]">
                  <svg class="w-4 h-4 text-slate-400 absolute right-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Gender</label>
                <div class="relative">
                  <select class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#5138ed] appearance-none bg-white text-slate-500">
                    <option>Select gender</option>
                  </select>
                  <svg class="w-4 h-4 text-slate-400 absolute right-4 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Profile Picture</label>
                <div class="border-2 border-dashed border-slate-200 rounded-xl py-4 flex flex-col items-center justify-center text-center hover:border-[#5138ed]/40 transition-colors cursor-pointer">
                  <svg class="w-5 h-5 text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                  <span class="text-[12px] font-bold text-slate-700">Upload Photo</span>
                  <span class="text-[10px] text-slate-400 mt-0.5">PNG, JPG up to 2MB</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Professional Information -->
          <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm">
            <h3 class="text-[15px] font-bold text-slate-800 mb-6">Professional Information</h3>
            <div class="grid grid-cols-3 gap-6 mb-6">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Department <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select v-model="newInstructor.department_id" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#5138ed] appearance-none bg-white text-slate-500">
                    <option value="">Select department</option>
                  </select>
                  <svg class="w-4 h-4 text-slate-400 absolute right-4 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Qualification <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#5138ed] appearance-none bg-white text-slate-500">
                    <option>Select qualification</option>
                  </select>
                  <svg class="w-4 h-4 text-slate-400 absolute right-4 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Specialization</label>
                <input type="text" placeholder="Enter specialization" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#5138ed]">
              </div>
            </div>
            <div class="grid grid-cols-3 gap-6">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Employee ID</label>
                <input type="text" placeholder="Enter employee ID" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#5138ed]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Designation</label>
                <input type="text" placeholder="Enter designation" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#5138ed]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Years of Experience</label>
                <input type="text" placeholder="Enter years of experience" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#5138ed]">
              </div>
            </div>
          </div>

          <!-- Account Information -->
          <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm">
            <h3 class="text-[15px] font-bold text-slate-800 mb-6">Account Information</h3>
            <div class="grid grid-cols-3 gap-6">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Username <span class="text-rose-500">*</span></label>
                <input type="text" placeholder="Enter username" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#5138ed]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Password <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <input v-model="newInstructor.password" type="password" placeholder="Enter password" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#5138ed]">
                  <svg class="w-4 h-4 text-slate-400 absolute right-4 top-3.5 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Confirm Password <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <input type="password" placeholder="Confirm password" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#5138ed]">
                  <svg class="w-4 h-4 text-slate-400 absolute right-4 top-3.5 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Permissions & Access -->
          <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm">
            <h3 class="text-[15px] font-bold text-slate-800 mb-6">Permissions & Access</h3>
            <div class="grid grid-cols-2 gap-6 mb-8">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Role <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] font-bold text-slate-700 focus:outline-none focus:border-[#5138ed] appearance-none bg-white">
                    <option>Instructor</option>
                  </select>
                  <svg class="w-4 h-4 text-slate-400 absolute right-4 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Access Level <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] font-bold text-slate-700 focus:outline-none focus:border-[#5138ed] appearance-none bg-white">
                    <option>Standard Instructor</option>
                  </select>
                  <svg class="w-4 h-4 text-slate-400 absolute right-4 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
            </div>

            <h4 class="text-[13px] font-bold text-slate-800 mb-4">Instructor Permissions</h4>
            <div class="grid grid-cols-3 gap-y-6 gap-x-4">
              <!-- Checkboxes -->
              <div class="flex items-start gap-3">
                <input type="checkbox" checked class="w-4 h-4 rounded border-slate-300 text-[#5138ed] focus:ring-[#5138ed] mt-0.5">
                <div>
                  <p class="text-[12px] font-bold text-slate-700">Create Exams</p>
                  <p class="text-[11px] text-slate-400 mt-0.5">Create and manage exams</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <input type="checkbox" checked class="w-4 h-4 rounded border-slate-300 text-[#5138ed] focus:ring-[#5138ed] mt-0.5">
                <div>
                  <p class="text-[12px] font-bold text-slate-700">View Results</p>
                  <p class="text-[11px] text-slate-400 mt-0.5">View student scores and analytics</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <input type="checkbox" checked class="w-4 h-4 rounded border-slate-300 text-[#5138ed] focus:ring-[#5138ed] mt-0.5">
                <div>
                  <p class="text-[12px] font-bold text-slate-700">Manage Results</p>
                  <p class="text-[11px] text-slate-400 mt-0.5">Create and manage courses</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <input type="checkbox" checked class="w-4 h-4 rounded border-slate-300 text-[#5138ed] focus:ring-[#5138ed] mt-0.5">
                <div>
                  <p class="text-[12px] font-bold text-slate-700">Manage Questions</p>
                  <p class="text-[11px] text-slate-400 mt-0.5">Add, edit and manage questions</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <input type="checkbox" checked class="w-4 h-4 rounded border-slate-300 text-[#5138ed] focus:ring-[#5138ed] mt-0.5">
                <div>
                  <p class="text-[12px] font-bold text-slate-700">Grade Exams</p>
                  <p class="text-[11px] text-slate-400 mt-0.5">Grade student submissions</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <input type="checkbox" checked class="w-4 h-4 rounded border-slate-300 text-[#5138ed] focus:ring-[#5138ed] mt-0.5">
                <div>
                  <p class="text-[12px] font-bold text-slate-700">Generate Reports</p>
                  <p class="text-[11px] text-slate-400 mt-0.5">Generate and export reports</p>
                </div>
              </div>
            </div>
            
            <div class="flex items-center justify-between pt-8 mt-8 border-t border-slate-100">
              <button @click="showAddModal = false" class="px-6 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
              <button @click="addInstructor" class="flex items-center gap-2 px-6 py-2.5 bg-[#5138ed] text-white rounded-xl text-[13px] font-bold hover:bg-[#5138ed]/90 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg> Create Instructor
              </button>
            </div>
          </div>

        </div>

        <!-- Right Column: Sidebar -->
        <div class="space-y-6">
          
          <!-- Account Summary -->
          <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
            <h4 class="text-[13px] font-bold text-slate-800 mb-6">Account Summary</h4>
            <div class="flex items-center gap-4 mb-6">
              <div class="w-12 h-12 rounded-xl bg-[#5138ed]/10 text-[#5138ed] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
              </div>
              <span class="text-[13px] font-bold text-slate-800">New Instructor</span>
            </div>
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-slate-500">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                  <span class="text-[12px]">Role</span>
                </div>
                <span class="text-[12px] font-bold text-slate-700">Instructor</span>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-slate-500">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                  <span class="text-[12px]">Access Level</span>
                </div>
                <span class="text-[12px] font-bold text-slate-700">Standard Instructor</span>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-slate-500">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                  <span class="text-[12px]">Department</span>
                </div>
                <span class="text-[12px] font-bold text-slate-700">Not Selected</span>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-slate-500">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  <span class="text-[12px]">Status</span>
                </div>
                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-500 text-[10px] font-bold rounded-md">Active</span>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-slate-500">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                  <span class="text-[12px]">Permissions</span>
                </div>
                <span class="text-[12px] font-bold text-slate-700">6 Modules</span>
              </div>
            </div>
          </div>

          <!-- Password Requirements -->
          <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
            <h4 class="text-[13px] font-bold text-slate-800 mb-4">Password Requirements</h4>
            <div class="space-y-3">
              <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-[12px] text-slate-600">At least 8 characters long</span>
              </div>
              <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-[12px] text-slate-600">Include uppercase letter</span>
              </div>
              <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-[12px] text-slate-600">Include lowercase letter</span>
              </div>
              <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-[12px] text-slate-600">Include number</span>
              </div>
              <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-[12px] text-slate-600">Include special character</span>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
            <h4 class="text-[13px] font-bold text-slate-800 mb-4">Quick Actions</h4>
            <div class="space-y-3">
              <button class="w-full flex items-center justify-between p-3 border border-slate-100 rounded-xl hover:border-[#5138ed] group transition-colors">
                <div class="flex items-center gap-2">
                  <div class="text-[#5138ed]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg></div>
                  <span class="text-[12px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors">Import Instructors</span>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
              <button class="w-full flex items-center justify-between p-3 border border-slate-100 rounded-xl hover:border-[#5138ed] group transition-colors">
                <div class="flex items-center gap-2">
                  <div class="text-[#5138ed]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>
                  <span class="text-[12px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors">Manage Departments</span>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
              <button class="w-full flex items-center justify-between p-3 border border-slate-100 rounded-xl hover:border-[#5138ed] group transition-colors">
                <div class="flex items-center gap-2">
                  <div class="text-[#5138ed]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div>
                  <span class="text-[12px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors">Manage Roles</span>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
              <button class="w-full flex items-center justify-between p-3 border border-slate-100 rounded-xl hover:border-[#5138ed] group transition-colors">
                <div class="flex items-center gap-2">
                  <div class="text-[#5138ed]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                  <span class="text-[12px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors">View Instructor Guide</span>
                </div>
                <svg class="w-4 h-4 text-slate-400 group-hover:text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>
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
</template>