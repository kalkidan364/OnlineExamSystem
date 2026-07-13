<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import apiClient from '../../../core/api/apiClient'

// ── State ──
const search = ref('')
const roleFilter = ref('all')
const statusFilter = ref('all')
const currentPage = ref(1)
const perPage = 8
const showAddModal = ref(false)
const showDeleteModal = ref(false)
const selectedUser = ref<any>(null)
const isLoading = ref(false)

const allUsers = ref<any[]>([])
const allDepartments = ref<any[]>([])

// ── New User Form ──
const newUser = ref({ name: '', email: '', role: 'student', department_id: '', password: '' })

// ── Fetch Users ──
const fetchUsers = async () => {
  try {
    const res = await apiClient.get('/admin/users')
    allUsers.value = (res.data.data || []).map((u: any) => ({
      ...u,
      avatar: u.name ? u.name.split(' ').map((w: string) => w[0]).join('').toUpperCase().slice(0, 2) : '??',
      status: 'active',
      joined: new Date(u.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
      departmentName: u.department?.name || '—',
    }))
  } catch (err) { console.error('Failed to fetch users:', err) }
}

// ── Fetch Departments ──
const fetchDepartments = async () => {
  try {
    const res = await apiClient.get('/admin/departments')
    allDepartments.value = res.data.data || []
  } catch (err) { console.error('Failed to fetch departments:', err) }
}

onMounted(async () => { await Promise.all([fetchUsers(), fetchDepartments()]) })

// ── Computed ──
const filtered = computed(() => {
  return allUsers.value.filter(u => {
    const matchSearch = u.name.toLowerCase().includes(search.value.toLowerCase()) ||
                        u.email.toLowerCase().includes(search.value.toLowerCase())
    const matchRole   = roleFilter.value === 'all' || u.role === roleFilter.value
    const matchStatus = statusFilter.value === 'all' || u.status === statusFilter.value
    return matchSearch && matchRole && matchStatus
  })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))
const paginated  = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const stats = computed(() => ({
  total:       allUsers.value.length,
  active:      allUsers.value.filter(u => u.status === 'active').length,
  instructors: allUsers.value.filter(u => u.role === 'instructor').length,
  students:    allUsers.value.filter(u => u.role === 'student').length,
}))

// ── Helpers ──
const roleColor = (role: string) => ({
  instructor: 'bg-indigo-50 text-indigo-700 border border-indigo-200',
  student:    'bg-sky-50 text-sky-700 border border-sky-200',
  admin:      'bg-rose-50 text-rose-700 border border-rose-200',
  dept_head:  'bg-violet-50 text-violet-700 border border-violet-200',
}[role] || 'bg-slate-100 text-slate-600')

const statusColor = (status: string) => ({
  active:    'bg-emerald-50 text-emerald-700',
  inactive:  'bg-slate-100 text-slate-500',
  suspended: 'bg-rose-50 text-rose-600',
}[status] || 'bg-slate-100 text-slate-500')

const statusDot = (status: string) => ({
  active:    'bg-emerald-500',
  inactive:  'bg-slate-400',
  suspended: 'bg-rose-500',
}[status] || 'bg-slate-400')

const avatarColor = (id: number) => {
  const colors = ['bg-indigo-500','bg-sky-500','bg-emerald-500','bg-violet-500','bg-amber-500','bg-rose-500','bg-teal-500','bg-orange-500']
  return colors[id % colors.length]
}

// ── Actions ──
const openAdd = () => { newUser.value = { name: '', email: '', role: 'student', department_id: '', password: '' }; showAddModal.value = true }

const addUser = async () => {
  if (!newUser.value.name || !newUser.value.email || !newUser.value.password) return
  isLoading.value = true
  try {
    await apiClient.post('/admin/users', {
      name: newUser.value.name,
      email: newUser.value.email,
      role: newUser.value.role,
      department_id: newUser.value.department_id || null,
      password: newUser.value.password,
    })
    await fetchUsers()
    showAddModal.value = false
  } catch (err: any) {
    let msg = 'Failed to create user.'
    if (err.response?.data) {
      msg = err.response.data.message || msg
      if (err.response.data.errors) {
        msg += '\n' + Object.values(err.response.data.errors).flat().join('\n')
      }
    }
    alert(msg)
  } finally { isLoading.value = false }
}

const confirmDelete = (user: any) => { selectedUser.value = user; showDeleteModal.value = true }
const deleteUser = async () => {
  if (!selectedUser.value) return
  isLoading.value = true
  try {
    await apiClient.delete(`/admin/users/${selectedUser.value.id}`)
    await fetchUsers()
    showDeleteModal.value = false
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete user.')
  } finally { isLoading.value = false }
}
const toggleStatus = (user: any) => {
  user.status = user.status === 'active' ? 'inactive' : 'active'
}
</script>

<template>
  <div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">User Management</h1>
        <p class="text-[13px] text-slate-500 mt-1">Manage all system users — instructors and students.</p>
      </div>
      <button @click="openAdd" class="flex items-center gap-2 bg-[#5138ed] hover:bg-indigo-700 text-white text-[13px] font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-indigo-200 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add New User
      </button>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-4 gap-4">
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Users</p><p class="text-[22px] font-bold text-slate-800">{{ stats.total }}</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Active</p><p class="text-[22px] font-bold text-slate-800">{{ stats.active }}</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-violet-50 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Instructors</p><p class="text-[22px] font-bold text-slate-800">{{ stats.instructors }}</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-sky-50 rounded-xl flex items-center justify-center shrink-0">
          <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Students</p><p class="text-[22px] font-bold text-slate-800">{{ stats.students }}</p></div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">

      <!-- Filters -->
      <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-slate-100">
        <!-- Search -->
        <div class="relative flex-1 max-w-sm">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input v-model="search" type="text" placeholder="Search by name or email..." class="w-full pl-9 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
        </div>
        <!-- Filters -->
        <div class="flex items-center gap-3">
          <select v-model="roleFilter" class="text-[13px] border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white">
            <option value="all">All Roles</option>
            <option value="instructor">Instructors</option>
            <option value="student">Students</option>
          </select>
          <select v-model="statusFilter" class="text-[13px] border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
          </select>
          <span class="text-[12px] font-semibold text-slate-400">{{ filtered.length }} users</span>
        </div>
      </div>

      <!-- Table -->
      <table class="w-full">
        <thead>
          <tr class="bg-slate-50/60 border-b border-slate-100">
            <th class="text-left px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">User</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Role</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Department</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Joined</th>
            <th class="text-right px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <tr v-for="user in paginated" :key="user.id" class="hover:bg-slate-50/40 transition-colors group">
            <!-- User -->
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div :class="[avatarColor(user.id), 'w-9 h-9 rounded-xl flex items-center justify-center text-[12px] font-bold text-white shrink-0']">{{ user.avatar }}</div>
                <div>
                  <p class="text-[13px] font-bold text-slate-800">{{ user.name }}</p>
                  <p class="text-[11px] text-slate-400 font-medium">{{ user.email }}</p>
                </div>
              </div>
            </td>
            <!-- Role -->
            <td class="px-4 py-4">
              <span :class="[roleColor(user.role), 'text-[11px] font-bold px-2.5 py-1 rounded-lg capitalize']">{{ user.role }}</span>
            </td>
            <!-- Department -->
            <td class="px-4 py-4">
              <p class="text-[13px] text-slate-600 font-medium">{{ user.departmentName }}</p>
            </td>
            <!-- Status -->
            <td class="px-4 py-4">
              <div class="flex items-center gap-2">
                <span :class="[statusDot(user.status), 'w-1.5 h-1.5 rounded-full shrink-0']"></span>
                <span :class="[statusColor(user.status), 'text-[11px] font-bold px-2 py-0.5 rounded-md capitalize']">{{ user.status }}</span>
              </div>
            </td>
            <!-- Joined -->
            <td class="px-4 py-4">
              <p class="text-[12px] text-slate-500 font-medium">{{ user.joined }}</p>
            </td>
            <!-- Actions -->
            <td class="px-6 py-4">
              <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="toggleStatus(user)" :title="user.status === 'active' ? 'Deactivate' : 'Activate'" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-amber-500 hover:bg-amber-50 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                </button>
                <button title="Edit" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
                <button @click="confirmDelete(user)" title="Delete" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </div>
            </td>
          </tr>
          <!-- Empty State -->
          <tr v-if="paginated.length === 0">
            <td colspan="6" class="py-16 text-center">
              <div class="flex flex-col items-center gap-3">
                <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center">
                  <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <p class="text-[14px] font-bold text-slate-600">No users found</p>
                <p class="text-[12px] text-slate-400">Try adjusting your search or filters.</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/30">
        <p class="text-[12px] text-slate-500 font-medium">
          Showing {{ Math.min((currentPage - 1) * perPage + 1, filtered.length) }}–{{ Math.min(currentPage * perPage, filtered.length) }} of {{ filtered.length }} users
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

    <!-- ── Add User Modal ── -->
    <Teleport to="body">
      <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
          <!-- Modal Header -->
          <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div>
              <h3 class="text-[16px] font-bold text-slate-800">Add New User</h3>
              <p class="text-[12px] text-slate-500 mt-0.5">Fill in the details to create a new account.</p>
            </div>
            <button @click="showAddModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <!-- Modal Body -->
          <div class="px-6 py-5 space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                <input v-model="newUser.name" type="text" placeholder="e.g. Dr. Abebe Kebede" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                <input v-model="newUser.email" type="email" placeholder="user@wou.edu.et" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Role <span class="text-rose-500">*</span></label>
                <select v-model="newUser.role" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] bg-white">
                  <option value="student">Student</option>
                  <option value="instructor">Instructor</option>
                </select>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Department</label>
                <select v-model="newUser.department_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] bg-white">
                  <option value="">Select Department</option>
                  <option v-for="dept in allDepartments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Password <span class="text-rose-500">*</span></label>
              <input v-model="newUser.password" type="password" placeholder="Min 8 characters" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              <p class="text-[11px] text-slate-400 mt-1.5 font-medium">The user will log in with this email and password.</p>
            </div>
          </div>
          <!-- Modal Footer -->
          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            <button @click="showAddModal = false" class="px-5 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">Cancel</button>
            <button @click="addUser" :disabled="isLoading" class="px-5 py-2.5 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-200 transition-all disabled:opacity-70 disabled:cursor-not-allowed">
              {{ isLoading ? 'Creating...' : 'Create User' }}
            </button>
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
            <h3 class="text-[16px] font-bold text-slate-800 mb-2">Delete User?</h3>
            <p class="text-[13px] text-slate-500 leading-relaxed">
              Are you sure you want to delete <span class="font-bold text-slate-700">{{ selectedUser?.name }}</span>? This action cannot be undone.
            </p>
          </div>
          <div class="flex items-center gap-3 px-6 pb-6">
            <button @click="showDeleteModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="deleteUser" :disabled="isLoading" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors disabled:opacity-70">
              {{ isLoading ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>
