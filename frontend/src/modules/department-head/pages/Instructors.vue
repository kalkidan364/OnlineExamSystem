<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import apiClient from '../../../core/api/apiClient'

// ── State ──
const deptName = ref('Loading...')
const search = ref('')
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

// ── New Instructor Form ──
const newInstructor = ref({ name: '', email: '', phone: '', password: '', semester: '' })

// ── Fetch Department Info ──
const fetchDeptInfo = async () => {
  try {
    const res = await apiClient.get('/user')
    deptName.value = res.data?.department?.name || 'My Department'
  } catch { deptName.value = 'My Department' }
}

// ── Fetch Instructors ──
const fetchInstructors = async () => {
  try {
    const res = await apiClient.get('/dept-head/instructors')
    allInstructors.value = (res.data.data || []).map((i: any) => ({
      ...i,
      avatar: i.name ? i.name.split(' ').map((w: string) => w[0]).join('').toUpperCase().slice(0, 2) : '??',
      status: 'active',
      joined: new Date(i.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
    }))
  } catch (err) { console.error('Failed to fetch instructors:', err) }
}

onMounted(async () => { await fetchDeptInfo(); await fetchInstructors() })

// ── Computed ──
const filtered = computed(() => {
  return allInstructors.value.filter(i => {
    const matchSearch = i.name.toLowerCase().includes(search.value.toLowerCase()) ||
                        i.email.toLowerCase().includes(search.value.toLowerCase())
    const matchStatus = statusFilter.value === 'all' || i.status === statusFilter.value
    return matchSearch && matchStatus
  })
})
const totalPages  = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))
const paginated   = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))
const stats = computed(() => ({
  total:  allInstructors.value.length,
  active: allInstructors.value.filter(i => i.status === 'active').length,
}))

// ── Helpers ──
const avatarColor = (id: number) => {
  const colors = ['bg-indigo-500','bg-sky-500','bg-emerald-500','bg-violet-500','bg-amber-500','bg-rose-500','bg-teal-500','bg-orange-500','bg-cyan-500','bg-purple-500']
  return colors[id % colors.length]
}
const statusBadge = (s: string) => s === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'
const statusDot = (s: string) => s === 'active' ? 'bg-emerald-500' : 'bg-slate-400'

// ── Actions ──
const openView = (instructor: any) => { selectedInstructor.value = instructor; showViewModal.value = true }
const openAdd  = () => { newInstructor.value = { name:'', email:'', phone:'', password:'', semester:'' }; showAddModal.value = true }

const addInstructor = async () => {
  if (!newInstructor.value.name || !newInstructor.value.email || !newInstructor.value.password) return
  isLoading.value = true
  try {
    await apiClient.post('/dept-head/instructors', {
      name: newInstructor.value.name,
      email: newInstructor.value.email,
      password: newInstructor.value.password,
      semester: newInstructor.value.semester || null,
    })
    await fetchInstructors()
    showAddModal.value = false
  } catch (err: any) {
    let msg = err.response?.data?.message || 'Failed to create instructor.'
    if (err.response?.data?.errors) msg += '\n' + Object.values(err.response.data.errors).flat().join('\n')
    alert(msg)
  } finally { isLoading.value = false }
}

const confirmDelete = (instructor: any) => { selectedInstructor.value = instructor; showDeleteModal.value = true }
const deleteInstructor = async () => {
  if (!selectedInstructor.value) return
  isLoading.value = true
  try {
    await apiClient.delete(`/dept-head/instructors/${selectedInstructor.value.id}`)
    await fetchInstructors()
    showDeleteModal.value = false
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete instructor.')
  } finally { isLoading.value = false }
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
        <h1 class="text-[22px] font-bold text-slate-800">Department Instructors</h1>
        <p class="text-[13px] text-slate-500 mt-1">Manage instructors for the {{ deptName }} department.</p>
      </div>
      <button @click="openAdd" class="flex items-center gap-2 bg-[#5138ed] hover:bg-indigo-700 text-white text-[13px] font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-indigo-200 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add Instructor
      </button>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 gap-4">
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Instructors</p><p class="text-[22px] font-bold text-slate-800">{{ stats.total }}</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Active</p><p class="text-[22px] font-bold text-slate-800">{{ stats.active }}</p></div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
      <!-- Filters -->
      <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-slate-100">
        <div class="relative flex-1 max-w-sm">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input v-model="search" type="text" placeholder="Search instructors..." class="w-full pl-9 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
        </div>
        <div class="flex items-center gap-3">
          <select v-model="statusFilter" class="text-[13px] border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <div class="flex items-center gap-1 border border-slate-200 rounded-xl p-1">
            <button @click="viewMode = 'table'" :class="[viewMode === 'table' ? 'bg-[#5138ed] text-white' : 'text-slate-400 hover:text-slate-600', 'w-8 h-7 rounded-lg flex items-center justify-center transition-colors']">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            </button>
            <button @click="viewMode = 'grid'" :class="[viewMode === 'grid' ? 'bg-[#5138ed] text-white' : 'text-slate-400 hover:text-slate-600', 'w-8 h-7 rounded-lg flex items-center justify-center transition-colors']">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- TABLE VIEW -->
      <template v-if="viewMode === 'table'">
        <table class="w-full">
          <thead>
            <tr class="bg-slate-50/60 border-b border-slate-100">
              <th class="text-left px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Instructor</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Department</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Semester</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Joined</th>
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
              <td class="px-4 py-4"><span class="text-[12px] font-semibold text-slate-600">{{ deptName }}</span></td>
              <td class="px-4 py-4"><span class="text-[12px] font-semibold text-slate-600">{{ inst.semester || '—' }}</span></td>
              <td class="px-4 py-4">
                <div class="flex items-center gap-1.5">
                  <span :class="[statusDot(inst.status), 'w-1.5 h-1.5 rounded-full shrink-0']"></span>
                  <span :class="[statusBadge(inst.status), 'text-[11px] font-bold px-2 py-0.5 rounded-md capitalize']">{{ inst.status }}</span>
                </div>
              </td>
              <td class="px-4 py-4"><span class="text-[12px] text-slate-500">{{ inst.joined }}</span></td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button @click="openView(inst)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                  <button @click="toggleStatus(inst)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-amber-500 hover:bg-amber-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg></button>
                  <button @click="confirmDelete(inst)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                </div>
              </td>
            </tr>
            <tr v-if="paginated.length === 0">
              <td colspan="6" class="px-6 py-12 text-center text-[13px] text-slate-400">No instructors found. Click "Add Instructor" to create one.</td>
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
              <span :class="[statusDot(inst.status), 'w-2 h-2 rounded-full']"></span>
            </div>
            <div class="mb-3 text-[12px] text-slate-500"><span class="font-semibold text-slate-600">Semester:</span> {{ inst.semester || '—' }}</div>
            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <button @click="openView(inst)" class="flex-1 py-2 text-[11px] font-bold text-[#5138ed] bg-indigo-50 rounded-lg">View</button>
              <button @click="toggleStatus(inst)" class="flex-1 py-2 text-[11px] font-bold text-amber-600 bg-amber-50 rounded-lg">{{ inst.status === 'active' ? 'Deactivate' : 'Activate' }}</button>
              <button @click="confirmDelete(inst)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
            </div>
          </div>
          <div v-if="paginated.length === 0" class="col-span-full text-center py-12 text-[13px] text-slate-400">No instructors found.</div>
        </div>
      </template>

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

    <!-- ── Add Instructor Modal ── -->
    <Teleport to="body">
      <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
          <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div>
              <h3 class="text-[16px] font-bold text-slate-800">Add New Instructor</h3>
              <p class="text-[12px] text-slate-500 mt-0.5">Create an instructor account in {{ deptName }}.</p>
            </div>
            <button @click="showAddModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
          </div>
          <div class="px-6 py-5 space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Full Name <span class="text-rose-500">*</span></label><input v-model="newInstructor.name" type="text" placeholder="e.g. Dr. Abebe" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Email <span class="text-rose-500">*</span></label><input v-model="newInstructor.email" type="email" placeholder="name@wou.edu.et" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Department</label><input type="text" disabled :value="deptName" class="w-full border border-slate-200 bg-slate-50 text-slate-500 rounded-xl px-4 py-2.5 text-[13px]"></div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Semester</label>
                <select v-model="newInstructor.semester" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] bg-white">
                  <option value="">Select Semester</option>
                  <option value="Semester 1">Semester 1</option>
                  <option value="Semester 2">Semester 2</option>
                  <option value="Summer">Summer</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Password <span class="text-rose-500">*</span></label>
              <input v-model="newInstructor.password" type="password" placeholder="Min 8 characters" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
            </div>
          </div>
          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            <button @click="showAddModal = false" class="px-5 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">Cancel</button>
            <button @click="addInstructor" :disabled="isLoading" class="px-5 py-2.5 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl shadow-sm transition-all disabled:opacity-70 disabled:cursor-not-allowed">
              {{ isLoading ? 'Creating...' : 'Create Instructor' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- ── View Modal ── -->
    <Teleport to="body">
      <div v-if="showViewModal && selectedInstructor" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
          <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <h3 class="text-[16px] font-bold text-slate-800">Instructor Details</h3>
            <button @click="showViewModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
          </div>
          <div class="px-6 py-5 space-y-4">
            <div class="flex items-center gap-4">
              <div :class="[avatarColor(selectedInstructor.id), 'w-14 h-14 rounded-xl flex items-center justify-center text-[18px] font-bold text-white']">{{ selectedInstructor.avatar }}</div>
              <div><p class="text-[15px] font-bold text-slate-800">{{ selectedInstructor.name }}</p><p class="text-[12px] text-slate-500">{{ selectedInstructor.email }}</p></div>
            </div>
            <div class="grid grid-cols-2 gap-4 pt-2">
              <div class="bg-slate-50 rounded-xl p-3"><p class="text-[10px] font-semibold text-slate-400 uppercase mb-1">Department</p><p class="text-[13px] font-bold text-slate-700">{{ deptName }}</p></div>
              <div class="bg-slate-50 rounded-xl p-3"><p class="text-[10px] font-semibold text-slate-400 uppercase mb-1">Semester</p><p class="text-[13px] font-bold text-slate-700">{{ selectedInstructor.semester || '—' }}</p></div>
              <div class="bg-slate-50 rounded-xl p-3"><p class="text-[10px] font-semibold text-slate-400 uppercase mb-1">Status</p><p class="text-[13px] font-bold capitalize" :class="selectedInstructor.status === 'active' ? 'text-emerald-600' : 'text-slate-500'">{{ selectedInstructor.status }}</p></div>
              <div class="bg-slate-50 rounded-xl p-3"><p class="text-[10px] font-semibold text-slate-400 uppercase mb-1">Joined</p><p class="text-[13px] font-bold text-slate-700">{{ selectedInstructor.joined }}</p></div>
            </div>
          </div>
          <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            <button @click="showViewModal = false" class="w-full py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">Close</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- ── Delete Modal ── -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6">
          <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg></div>
          <h3 class="text-[16px] font-bold text-slate-800 mb-2">Remove Instructor?</h3>
          <p class="text-[13px] text-slate-500 mb-6">Are you sure you want to remove <span class="font-bold text-slate-700">{{ selectedInstructor?.name }}</span>?</p>
          <div class="flex gap-3">
            <button @click="showDeleteModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="deleteInstructor" :disabled="isLoading" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors disabled:opacity-70">
              {{ isLoading ? 'Removing...' : 'Remove' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>

</template>
