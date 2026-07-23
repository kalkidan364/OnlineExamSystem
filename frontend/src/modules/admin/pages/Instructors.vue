<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import apiClient from '../../../core/api/apiClient'
import { useSettingsStore } from '../../../store/settingsStore'

const settingsStore = useSettingsStore()
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Doughnut } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend)

// ── State ──
const search = ref('')
const deptFilter = ref('all')
const statusFilter = ref('all')
const currentPage = ref(1)
const perPage = 10
const showAddPage = ref(false)
const showDeleteModal = ref(false)
const showEditModal = ref(false)
const editInstructorForm = ref<any>({})
const viewingInstructor = ref<any>(null)
const selectedInstructor = ref<any>(null)
const isLoading = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const allInstructors = ref<any[]>([])
const allDepartments = ref<any[]>([])

// ── New Instructor Form ──
const newInstructor = ref({
  name: '', email: '', phone: '', gender: '', department_id: '', semester: '', year: '',
  employeeId: '', username: '', password: '', confirmPassword: '',
  profilePicture: null as File | null,
})

const permissions = ref([
  { key: 'create_exams', label: 'Create Exams', desc: 'Create and manage exams', checked: true },
  { key: 'view_results', label: 'View & Results', desc: 'View student results and analytics', checked: true },
  { key: 'manage_results', label: 'Manage Results', desc: 'Create and manage courses', checked: true },
  { key: 'manage_questions', label: 'Manage Questions', desc: 'Add, edit and manage questions', checked: true },
  { key: 'grade_exams', label: 'Grade Exams', desc: 'Grade student submissions', checked: true },
  { key: 'generate_reports', label: 'Generate Reports', desc: 'Generate and export reports', checked: true },
])

// ── Fetch Instructors ──
const fetchInstructors = async () => {
  try {
    const res = await apiClient.get('/admin/users?role=instructor,dept_head')
    allInstructors.value = (res.data.data || []).map((u: any) => ({
      ...u,
      avatar: u.name ? u.name.split(' ').map((w: string) => w[0]).join('').toUpperCase().slice(0, 2) : '??',
      status: u.status || 'active',
      joined: new Date(u.created_at || Date.now()).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
      departmentName: u.department?.name || '—',
      employeeId: u.id_no || u.employee_id || `WU-INS-${(u.id || 1).toString().padStart(4, '0')}`,
      lastLogin: u.last_login_at || 'Never',
      phone: u.phone || 'N/A',
      gender: u.gender || 'N/A',
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
                        i.email.toLowerCase().includes(search.value.toLowerCase())
    const matchDept   = deptFilter.value === 'all' || i.departmentName === deptFilter.value
    const matchStatus = statusFilter.value === 'all' || i.status === statusFilter.value
    return matchSearch && matchDept && matchStatus
  })
})

const totalPages  = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))
const paginated   = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const stats = computed(() => {
  const total = allInstructors.value.length
  const active = allInstructors.value.filter(i => i.status === 'active').length
  const inactive = total - active
  const newInst = allInstructors.value.filter(i => {
    const created = new Date(i.created_at || Date.now())
    const now = new Date()
    return (now.getTime() - created.getTime()) < 30 * 24 * 60 * 60 * 1000
  }).length
  return { total, active, inactive, newInst }
})

// ── Donut Chart ──
const chartData = computed(() => ({
  labels: ['Active', 'Inactive', 'New This Month'],
  datasets: [{
    backgroundColor: ['#4338ca', '#F43F5E', '#06b6d4'],
    data: [stats.value.active, stats.value.inactive, stats.value.newInst],
    borderWidth: 0,
    hoverOffset: 4
  }]
}))
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '72%',
  plugins: { legend: { display: false }, tooltip: { enabled: true } }
}

// ── Top Departments ──
const topDepartments = computed(() => {
  const map: Record<string, number> = {}
  allInstructors.value.forEach(i => {
    if (i.departmentName && i.departmentName !== '—') {
      map[i.departmentName] = (map[i.departmentName] || 0) + 1
    }
  })
  return Object.entries(map)
    .map(([name, count]) => ({ name, count }))
    .sort((a, b) => b.count - a.count)
    .slice(0, 5)
})

const deptIcons = ['💻', '⚙️', '📊', '🗄️', '🌐']

// ── Recent Registrations ──
const recentRegistrations = computed(() => {
  return [...allInstructors.value]
    .sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
    .slice(0, 3)
})

// ── Avatar Color ──
const avatarBg = (id: number) => {
  const colors = ['bg-indigo-100 text-indigo-600','bg-emerald-100 text-emerald-600','bg-sky-100 text-sky-600','bg-amber-100 text-amber-600','bg-rose-100 text-rose-600','bg-violet-100 text-violet-600','bg-teal-100 text-teal-600','bg-orange-100 text-orange-600','bg-cyan-100 text-cyan-600','bg-purple-100 text-purple-600']
  return colors[(id || 0) % colors.length]
}

// ── Page Numbers ──
const visiblePages = computed(() => {
  const pages: (number | string)[] = []
  const total = totalPages.value
  const current = currentPage.value
  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i)
  } else {
    pages.push(1)
    if (current > 3) pages.push('...')
    const start = Math.max(2, current - 1)
    const end = Math.min(total - 1, current + 1)
    for (let i = start; i <= end; i++) pages.push(i)
    if (current < total - 2) pages.push('...')
    pages.push(total)
  }
  return pages
})

// ── Actions ──
const viewInstructor = (inst: any) => { viewingInstructor.value = inst }
const closeView = () => { viewingInstructor.value = null }
const openAdd = () => {
  newInstructor.value = { name:'', email:'', phone:'', gender:'', department_id:'', semester:'', year:'', employeeId:'', username:'', password:'', confirmPassword:'', profilePicture: null }
  showPassword.value = false
  showConfirmPassword.value = false
  permissions.value.forEach(p => p.checked = true)
  showAddPage.value = true
}
const closeAdd = () => { showAddPage.value = false }
const editPermissions = ref([
  { id: 'create_exams', key: 'createExams', label: 'Create Exams', desc: 'Create and manage exams', checked: true },
  { id: 'view_results', key: 'viewResults', label: 'View Results', desc: 'View student results and analytics', checked: true },
  { id: 'manage_results', key: 'manageResults', label: 'Manage Results', desc: 'Create and manage courses', checked: false },
  { id: 'manage_questions', key: 'manageQuestions', label: 'Manage Questions', desc: 'Add, edit and manage questions', checked: true },
  { id: 'grade_exams', key: 'gradeExams', label: 'Grade Exams', desc: 'Grade student submissions', checked: true },
  { id: 'generate_reports', key: 'generateReports', label: 'Generate Reports', desc: 'Generate and export reports', checked: true }
])
const fileInput = ref<HTMLInputElement | null>(null)
const triggerFileInput = () => { if (fileInput.value) fileInput.value.click() }
const editFileInput = ref<HTMLInputElement | null>(null)
const triggerEditFileInput = () => { if (editFileInput.value) editFileInput.value.click() }
const handleProfilePicture = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (file) newInstructor.value.profilePicture = file
}

const openEdit = (instructor: any) => {
  editInstructorForm.value = { 
    ...instructor,
    gender: instructor.gender || '',
    employeeId: instructor.employeeId || '',
    username: instructor.username || '',
    password: '',
    profilePicture: null
  }
  // Initialize editPermissions (in a real app, populate from instructor.permissions)
  editPermissions.value.forEach(p => p.checked = true)
  showEditModal.value = true
}

const saveEdit = () => {
  isLoading.value = true
  setTimeout(() => {
    const index = allInstructors.value.findIndex(i => i.id === editInstructorForm.value.id)
    if (index !== -1) {
      allInstructors.value[index] = { ...allInstructors.value[index], ...editInstructorForm.value }
      if (viewingInstructor.value?.id === editInstructorForm.value.id) {
        viewingInstructor.value = { ...allInstructors.value[index] }
      }
    }
    showEditModal.value = false
    isLoading.value = false
  }, 300)
}

const addInstructor = async () => {
  if (!newInstructor.value.name || !newInstructor.value.email || !newInstructor.value.password) return
  if (newInstructor.value.password !== newInstructor.value.confirmPassword) {
    alert('Passwords do not match.')
    return
  }
  isLoading.value = true
  try {
    await apiClient.post('/admin/users', {
      name: newInstructor.value.name,
      email: newInstructor.value.email,
      username: newInstructor.value.username,
      phone: newInstructor.value.phone,
      gender: newInstructor.value.gender,
      role: 'instructor',
      department_id: newInstructor.value.department_id || null,
      password: newInstructor.value.password,
      year_level: newInstructor.value.year || null,
      semester: settingsStore.semester || null,
      id_no: newInstructor.value.employeeId || null,
    })
    await fetchInstructors()
    showAddPage.value = false
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
    if (viewingInstructor.value?.id === selectedInstructor.value.id) {
      viewingInstructor.value = null
    }
    showDeleteModal.value = false
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete instructor.')
  } finally {
    isLoading.value = false
  }
}

const showExportDropdown = ref(false)

const handleExport = async (format: string) => {
  showExportDropdown.value = false;
  isLoading.value = true;
  try {
    const res = await apiClient.get(`/admin/users-export?role=instructor&format=${format}`, { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([res.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `instructors_export_${new Date().toISOString().slice(0,10)}.${format === 'pdf' ? 'pdf' : 'csv'}`);
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (err: any) {
    if (err.response && err.response.data && err.response.data instanceof Blob) {
      const text = await err.response.data.text();
      try {
        const json = JSON.parse(text);
        alert(json.message || 'Failed to export instructors');
      } catch (e) {
        alert('Failed to export instructors');
      }
    } else {
      alert('Failed to export instructors');
    }
  } finally {
    isLoading.value = false;
  }
}

const importFileInput = ref<HTMLInputElement | null>(null)
const triggerImport = () => { if (importFileInput.value) importFileInput.value.click() }
const handleImport = async (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (file) {
    isLoading.value = true
    try {
      const formData = new FormData()
      formData.append('file', file)
      formData.append('role', 'instructor')
      await apiClient.post('/admin/users-import', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      alert(`Successfully imported ${file.name}`)
      await fetchInstructors()
    } catch (err: any) { 
        alert(err.response?.data?.message || 'Failed to import instructors') 
    }
    finally {
      isLoading.value = false
      if (importFileInput.value) importFileInput.value.value = ''
    }
  }
}
</script>

<template>
  <div class="w-full">

    <!-- ════════════════ LIST VIEW ════════════════ -->
    <div v-if="!viewingInstructor && !showAddPage" class="space-y-6 pb-12 min-w-0 w-full">

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <h1 class="text-[24px] font-bold text-slate-800">Instructors</h1>
          </div>
          <p class="text-[13px] text-slate-500 ml-[52px]">Manage all instructors and their information across the system.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
          <input type="file" ref="importFileInput" class="hidden" accept=".csv" @change="handleImport">
          <button @click="triggerImport" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-[#4338ca] font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Import Instructors
          </button>
          <div class="relative">
            <button @click="showExportDropdown = !showExportDropdown" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-[#4338ca] font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm whitespace-nowrap">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Export Instructors
            </button>
            <div v-if="showExportDropdown" class="absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-50">
              <button @click="handleExport('csv')" class="w-full text-left px-4 py-2 text-[13px] text-slate-600 hover:bg-slate-50 hover:text-[#4338ca] transition-colors">Export as CSV</button>
              <button @click="handleExport('pdf')" class="w-full text-left px-4 py-2 text-[13px] text-slate-600 hover:bg-slate-50 hover:text-[#4338ca] transition-colors">Export as PDF</button>
            </div>
          </div>
          <button @click="openAdd" class="flex items-center gap-2 px-4 py-2.5 bg-[#4338ca] text-white font-bold rounded-xl text-[13px] hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Add New Instructor
          </button>
        </div>
      </div>

      <!-- ── Stats Cards ── -->
      <div class="grid grid-cols-4 gap-4">
        <!-- Total -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
          <div class="flex items-start justify-between">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center">
              <svg class="w-5 h-5 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div class="text-right">
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Instructors</p>
              <p class="text-[24px] font-extrabold text-slate-800 leading-none">{{ stats.total }}</p>
            </div>
          </div>
          <div class="mt-3 flex items-center gap-2">
            <span class="flex items-center text-[11px] font-bold text-emerald-500">
              <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>8.3%
            </span>
            <span class="text-[11px] text-slate-400">All registered instructors</span>
          </div>
        </div>
        <!-- Active -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
          <div class="flex items-start justify-between">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center">
              <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <div class="text-right">
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Active Instructors</p>
              <p class="text-[24px] font-extrabold text-slate-800 leading-none">{{ stats.active }}</p>
            </div>
          </div>
          <div class="mt-3 flex items-center gap-2">
            <span class="flex items-center text-[11px] font-bold text-emerald-500">
              <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>9.6%
            </span>
            <span class="text-[11px] text-slate-400">Active instructors</span>
          </div>
        </div>
        <!-- Inactive -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
          <div class="flex items-start justify-between">
            <div class="w-11 h-11 rounded-xl bg-rose-50 flex items-center justify-center">
              <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path></svg>
            </div>
            <div class="text-right">
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Inactive Instructors</p>
              <p class="text-[24px] font-extrabold text-slate-800 leading-none">{{ stats.inactive }}</p>
            </div>
          </div>
          <div class="mt-3 flex items-center gap-2">
            <span class="flex items-center text-[11px] font-bold text-rose-500">
              <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>12.5%
            </span>
            <span class="text-[11px] text-slate-400">Inactive instructors</span>
          </div>
        </div>
        <!-- New -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
          <div class="flex items-start justify-between">
            <div class="w-11 h-11 rounded-xl bg-sky-50 flex items-center justify-center">
              <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
            </div>
            <div class="text-right">
              <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">New Instructors</p>
              <p class="text-[24px] font-extrabold text-slate-800 leading-none">{{ stats.newInst }}</p>
            </div>
          </div>
          <div class="mt-3 flex items-center gap-2">
            <span class="flex items-center text-[11px] font-bold text-emerald-500">
              <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>12.5%
            </span>
            <span class="text-[11px] text-slate-400">This month</span>
          </div>
        </div>
      </div>

      <!-- ── Main Content Grid ── -->
      <div class="flex flex-col gap-6 w-full">

        <!-- Top Column: Table -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm flex flex-col min-w-0 w-full">

          <!-- Table Filters -->
          <div class="p-4 border-b border-slate-100 flex items-center gap-3 overflow-x-auto">
            <div class="relative shrink-0">
              <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
              <input v-model="search" type="text" placeholder="Search Instructors..." class="w-52 border border-slate-200 rounded-lg pl-9 pr-4 py-2 text-[12px] focus:outline-none focus:border-[#4338ca] bg-white">
            </div>
            <select v-model="deptFilter" class="border border-slate-200 rounded-lg px-3 py-2 text-[12px] font-medium text-slate-600 focus:outline-none focus:border-[#4338ca] min-w-[130px] shrink-0 bg-white">
              <option value="all">All Departments</option>
              <option v-for="d in allDepartments" :key="d.id" :value="d.name">{{ d.name }}</option>
            </select>
            <select v-model="statusFilter" class="border border-slate-200 rounded-lg px-3 py-2 text-[12px] font-medium text-slate-600 focus:outline-none focus:border-[#4338ca] min-w-[110px] shrink-0 bg-white">
              <option value="all">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
            <div class="relative shrink-0">
              <input type="text" value="Join Date" readonly class="w-[110px] border border-slate-200 rounded-lg px-3 py-2 text-[12px] font-medium text-slate-600 bg-white cursor-pointer">
              <svg class="w-4 h-4 text-slate-400 absolute right-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto flex-1">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50/60 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                  <th class="px-5 py-3.5">Instructor</th>
                  <th class="px-4 py-3.5">Email</th>
                  <th class="px-4 py-3.5">Department</th>
                  <th class="px-4 py-3.5">Phone</th>
                  <th class="px-4 py-3.5">Status</th>
                  <th class="px-4 py-3.5">Join Date</th>
                  <th class="px-4 py-3.5">Last Login</th>
                  <th class="px-4 py-3.5 text-center">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50">
                <tr v-for="inst in paginated" :key="inst.id" class="hover:bg-slate-50/50 transition-colors">
                  <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                      <div :class="avatarBg(inst.id)" class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold shrink-0">
                        {{ inst.avatar }}
                      </div>
                      <span class="text-[12px] font-bold text-slate-800 whitespace-nowrap">{{ inst.name }}</span>
                    </div>
                  </td>
                  <td class="px-4 py-3 text-[12px] text-slate-500">{{ inst.email }}</td>
                  <td class="px-4 py-3 text-[12px] font-medium text-slate-600">{{ inst.departmentName }}</td>
                  <td class="px-4 py-3 text-[12px] text-slate-500 whitespace-nowrap">{{ inst.phone }}</td>
                  <td class="px-4 py-3">
                    <span :class="inst.status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-500'" class="px-2.5 py-1 text-[10px] font-bold rounded-md capitalize">
                      {{ inst.status }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-[12px] text-slate-500 whitespace-nowrap">{{ inst.joined }}</td>
                  <td class="px-4 py-3 text-[12px] text-slate-500 whitespace-nowrap">{{ inst.lastLogin }}</td>
                  <td class="px-4 py-3">
                    <div class="flex items-center justify-center gap-2 text-slate-400">
                      <button @click="viewInstructor(inst)" class="hover:text-[#4338ca] transition-colors" title="View"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                      <button @click="openEdit(inst)" class="hover:text-[#4338ca] transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                      <button @click="confirmDelete(inst)" class="hover:text-rose-500 transition-colors" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                  </td>
                </tr>
                <tr v-if="paginated.length === 0">
                  <td colspan="8" class="px-6 py-10 text-center text-slate-400 text-[13px]">No instructors found matching your criteria.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[12px] text-slate-500">Showing {{ filtered.length === 0 ? 0 : (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, filtered.length) }} of {{ filtered.length }} instructors</span>
            <div class="flex items-center gap-1.5">
              <button @click="currentPage--" :disabled="currentPage === 1" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 disabled:opacity-40 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
              </button>
              <template v-for="(page, idx) in visiblePages" :key="idx">
                <span v-if="page === '...'" class="w-8 h-8 flex items-center justify-center text-[12px] text-slate-400">…</span>
                <button v-else @click="currentPage = page as number" :class="currentPage === page ? 'bg-[#4338ca] text-white border-[#4338ca]' : 'border-slate-200 text-slate-600 hover:bg-slate-50'" class="w-8 h-8 flex items-center justify-center rounded-lg border text-[12px] font-bold transition-colors">
                  {{ page }}
                </button>
              </template>
              <button @click="currentPage++" :disabled="currentPage === totalPages" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 disabled:opacity-40 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
            </div>
          </div>
        </div>

        <!-- ── Bottom Grid (was Right Sidebar) ── -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

          <!-- Instructor Overview Chart -->
          <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <h3 class="text-[14px] font-bold text-slate-800 mb-5">Instructor Overview</h3>
            <div class="relative w-44 h-44 mx-auto mb-5">
              <Doughnut :data="chartData" :options="chartOptions" />
              <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-[24px] font-extrabold text-slate-800 leading-none">{{ stats.total }}</span>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-1">Total Instructors</span>
              </div>
            </div>
            <div class="space-y-2.5">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2"><div class="w-2.5 h-2.5 rounded-full bg-[#4338ca]"></div><span class="text-[12px] text-slate-600">Active</span></div>
                <div><span class="text-[12px] font-bold text-slate-800">{{ stats.active }}</span> <span class="text-[11px] text-slate-400">({{ stats.total ? ((stats.active / stats.total) * 100).toFixed(1) : 0 }}%)</span></div>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2"><div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div><span class="text-[12px] text-slate-600">Inactive</span></div>
                <div><span class="text-[12px] font-bold text-slate-800">{{ stats.inactive }}</span> <span class="text-[11px] text-slate-400">({{ stats.total ? ((stats.inactive / stats.total) * 100).toFixed(1) : 0 }}%)</span></div>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2"><div class="w-2.5 h-2.5 rounded-full bg-cyan-500"></div><span class="text-[12px] text-slate-600">New This Month</span></div>
                <div><span class="text-[12px] font-bold text-slate-800">{{ stats.newInst }}</span> <span class="text-[11px] text-slate-400">({{ stats.total ? ((stats.newInst / stats.total) * 100).toFixed(1) : 0 }}%)</span></div>
              </div>
            </div>
          </div>

          <!-- Top Departments -->
          <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <h3 class="text-[14px] font-bold text-slate-800 mb-4">Top Departments</h3>
            <div class="space-y-3">
              <div v-for="(dept, i) in topDepartments" :key="dept.name" class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <span class="text-[14px]">{{ deptIcons[i] || '📁' }}</span>
                  <span class="text-[12px] font-medium text-slate-700">{{ dept.name }}</span>
                </div>
                <span class="text-[12px] font-bold text-slate-800">{{ dept.count }}</span>
              </div>
              <div v-if="topDepartments.length === 0" class="text-[12px] text-slate-400 text-center py-2">No department data</div>
            </div>
            <button class="text-[12px] font-bold text-[#4338ca] hover:underline mt-3">View All</button>
          </div>

          <!-- Recent Registrations -->
          <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <h3 class="text-[14px] font-bold text-slate-800 mb-4">Recent Registrations</h3>
            <div class="space-y-4">
              <div v-for="inst in recentRegistrations" :key="inst.id" class="flex items-center gap-3">
                <div :class="avatarBg(inst.id)" class="w-9 h-9 rounded-full flex items-center justify-center text-[11px] font-bold shrink-0">
                  {{ inst.avatar }}
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-[12px] font-bold text-slate-800 truncate">{{ inst.name }}</p>
                  <p class="text-[11px] text-slate-400 truncate">{{ inst.departmentName }}</p>
                </div>
                <span class="text-[10px] text-slate-400 whitespace-nowrap shrink-0">{{ inst.joined }}</span>
              </div>
              <div v-if="recentRegistrations.length === 0" class="text-[12px] text-slate-400 text-center py-2">No recent registrations</div>
            </div>
            <button class="text-[12px] font-bold text-[#4338ca] hover:underline mt-3">View All</button>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
            <h3 class="text-[14px] font-bold text-slate-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
              <button @click="openAdd" class="flex items-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-[#4338ca] group transition-colors text-left">
                <svg class="w-4 h-4 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-[#4338ca]">Add Instructor</span>
              </button>
              <button @click="triggerImport" class="flex items-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-[#4338ca] group transition-colors text-left">
                <svg class="w-4 h-4 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-[#4338ca]">Import Instructors</span>
              </button>
              <button class="flex items-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-[#4338ca] group transition-colors text-left">
                <svg class="w-4 h-4 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-[#4338ca]">Export Instructors</span>
              </button>
              <button class="flex items-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-[#4338ca] group transition-colors text-left">
                <svg class="w-4 h-4 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span class="text-[11px] font-bold text-slate-700 group-hover:text-[#4338ca]">Manage Departments</span>
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- ════════════════ DETAIL VIEW ════════════════ -->
    <div v-else-if="viewingInstructor" class="space-y-6 pb-12 min-w-0 w-full">
      <!-- Detail Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
          <button @click="closeView" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center hover:bg-slate-50 transition-colors text-slate-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          </button>
          <div>
            <h1 class="text-[22px] font-bold text-slate-800">Instructor Details</h1>
            <div class="flex items-center gap-2 text-[13px] text-slate-500 mt-0.5">
              <span class="hover:text-[#4338ca] cursor-pointer" @click="closeView">Instructors</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              <span class="font-medium text-slate-700">{{ viewingInstructor.name }}</span>
            </div>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <button @click="openEdit(viewingInstructor)" class="flex items-center gap-2 px-4 py-2 bg-white border border-[#4338ca] text-[#4338ca] font-bold rounded-xl text-[13px] hover:bg-indigo-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg> Edit
          </button>
          <button @click="confirmDelete(viewingInstructor)" class="flex items-center gap-2 px-4 py-2 bg-white border border-rose-300 text-rose-500 font-bold rounded-xl text-[13px] hover:bg-rose-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Delete
          </button>
        </div>
      </div>

      <!-- Detail Grid -->
      <div class="grid grid-cols-[1fr_320px] gap-6">
        <!-- Left -->
        <div class="space-y-6">
          <!-- Personal Info -->
          <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm">
            <h3 class="text-[15px] font-bold text-slate-800 mb-6">Personal Information</h3>
            <div class="flex gap-8">
              <div class="flex flex-col items-center gap-3">
                <div :class="avatarBg(viewingInstructor.id)" class="w-28 h-28 rounded-2xl flex items-center justify-center text-[28px] font-bold">
                  {{ viewingInstructor.avatar }}
                </div>
                <span :class="viewingInstructor.status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-500'" class="px-3 py-1 text-[11px] font-bold rounded-full capitalize flex items-center gap-1.5">
                  <div :class="viewingInstructor.status === 'active' ? 'bg-emerald-500' : 'bg-rose-500'" class="w-1.5 h-1.5 rounded-full"></div> {{ viewingInstructor.status }}
                </span>
              </div>
              <div class="flex-1 grid grid-cols-2 gap-x-8 gap-y-5">
                <div><p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Full Name</p><p class="text-[14px] font-bold text-slate-800">{{ viewingInstructor.name }}</p></div>
                <div><p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Gender</p><p class="text-[14px] font-bold text-slate-800">{{ viewingInstructor.gender }}</p></div>
                <div><p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email</p><p class="text-[14px] font-bold text-slate-800">{{ viewingInstructor.email }}</p></div>
                <div><p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Phone</p><p class="text-[14px] font-bold text-slate-800">{{ viewingInstructor.phone }}</p></div>
                <div><p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Department</p><p class="text-[14px] font-bold text-slate-800">{{ viewingInstructor.departmentName }}</p></div>
                <div><p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Employee ID</p><p class="text-[14px] font-bold text-slate-800">{{ viewingInstructor.employeeId }}</p></div>
              </div>
            </div>
          </div>
          <!-- Account Info -->
          <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm">
            <h3 class="text-[15px] font-bold text-slate-800 mb-6">Account Information</h3>
            <div class="grid grid-cols-3 gap-6">
              <div><p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Username</p><p class="text-[14px] font-bold text-slate-800">{{ viewingInstructor.email }}</p></div>
              <div><p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Account Status</p><p :class="viewingInstructor.status === 'active' ? 'text-emerald-500' : 'text-rose-500'" class="text-[14px] font-bold capitalize">{{ viewingInstructor.status }}</p></div>
              <div><p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Role</p><p class="text-[14px] font-bold text-slate-800">Instructor</p></div>
              <div><p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Account Created</p><p class="text-[14px] font-bold text-slate-800">{{ viewingInstructor.joined }}</p></div>
              <div><p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Last Login</p><p class="text-[14px] font-bold text-slate-800">{{ viewingInstructor.lastLogin }}</p></div>
              <div><p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Updated By</p><p class="text-[14px] font-bold text-slate-800">Super Admin</p></div>
            </div>
          </div>
          <!-- Permissions -->
          <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm">
            <h3 class="text-[15px] font-bold text-slate-800 mb-6">Permissions & Access</h3>
            <div class="grid grid-cols-3 gap-5">
              <div v-for="perm in ['Create Exams','View Results','Manage Courses','Manage Questions','Grade Exams','Generate Reports']" :key="perm" class="flex items-start gap-2.5">
                <div class="w-5 h-5 rounded bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0 mt-0.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                <span class="text-[12px] font-bold text-slate-700">{{ perm }}</span>
              </div>
            </div>
          </div>
        </div>
        <!-- Right -->
        <div class="space-y-6">
          <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
            <h3 class="text-[14px] font-bold text-slate-800 mb-4">Summary</h3>
            <div class="space-y-3.5">
              <div class="flex items-center justify-between"><span class="text-[12px] text-slate-500">Status</span><span :class="viewingInstructor.status==='active'?'text-emerald-500':'text-rose-500'" class="text-[12px] font-bold capitalize">{{ viewingInstructor.status }}</span></div>
              <div class="flex items-center justify-between"><span class="text-[12px] text-slate-500">Department</span><span class="text-[12px] font-bold text-slate-800">{{ viewingInstructor.departmentName }}</span></div>
              <div class="flex items-center justify-between"><span class="text-[12px] text-slate-500">Employee ID</span><span class="text-[12px] font-bold text-slate-800">{{ viewingInstructor.employeeId }}</span></div>
              <div class="flex items-center justify-between"><span class="text-[12px] text-slate-500">Role</span><span class="text-[12px] font-bold text-slate-800">Instructor</span></div>
              <div class="flex items-center justify-between"><span class="text-[12px] text-slate-500">Permissions</span><span class="text-[12px] font-bold text-slate-800">6 Modules</span></div>
            </div>
          </div>
          <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
            <h3 class="text-[14px] font-bold text-slate-800 mb-4">Quick Actions</h3>
            <div class="space-y-2.5">
              <button @click="openEdit(viewingInstructor)" class="w-full flex items-center justify-between p-3 border border-slate-100 rounded-xl hover:border-[#4338ca] group transition-colors">
                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg><span class="text-[12px] font-bold text-slate-700 group-hover:text-[#4338ca]">Edit Instructor</span></div>
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
              <button class="w-full flex items-center justify-between p-3 border border-slate-100 rounded-xl hover:border-[#4338ca] group transition-colors">
                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg><span class="text-[12px] font-bold text-slate-700 group-hover:text-[#4338ca]">Reset Password</span></div>
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
              <button @click="confirmDelete(viewingInstructor)" class="w-full flex items-center justify-between p-3 border border-slate-100 rounded-xl hover:border-rose-500 group transition-colors">
                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg><span class="text-[12px] font-bold text-slate-700 group-hover:text-rose-500">Delete Instructor</span></div>
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ════════════════ ADD FORM VIEW ════════════════ -->
    <div v-else-if="showAddPage" class="space-y-8 pb-12 min-w-0 w-full">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-[22px] font-bold text-slate-800">Add New Instructor</h1>
          <div class="flex items-center gap-2 text-[13px] text-slate-500 mt-0.5">
            <span class="hover:text-[#4338ca] cursor-pointer" @click="closeAdd">Instructors</span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="font-medium text-slate-700">Add New Instructor</span>
          </div>
        </div>
        <button @click="closeAdd" class="flex items-center gap-2 px-4 py-2 border border-slate-200 text-slate-600 font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Instructors
        </button>
      </div>

      <div class="grid grid-cols-[1fr_320px] gap-6">
        <!-- Main Form -->
        <div class="space-y-6">
          
          <!-- Personal Information -->
          <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm space-y-6">
            <h3 class="text-[15px] font-bold text-slate-800">Personal Information</h3>
            <div class="grid grid-cols-3 gap-6">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Full Name <span class="text-rose-500">*</span></label>
                <input v-model="newInstructor.name" type="text" placeholder="Enter full name" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Email Address <span class="text-rose-500">*</span></label>
                <input v-model="newInstructor.email" type="email" placeholder="Enter email address" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Phone Number <span class="text-rose-500">*</span></label>
                <input v-model="newInstructor.phone" type="text" placeholder="Enter phone number" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]">
              </div>
            </div>
            <div class="grid grid-cols-2 gap-6">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Gender</label>
                <select v-model="newInstructor.gender" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca] bg-white">
                  <option value="">Select gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Profile Picture</label>
                <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 flex flex-col items-center justify-center hover:border-[#4338ca] hover:bg-slate-50 transition-colors cursor-pointer" @click="triggerFileInput">
                  <input type="file" ref="fileInput" class="hidden" accept="image/png, image/jpeg" @change="handleProfilePicture">
                  <svg class="w-6 h-6 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                  <span class="text-[13px] font-bold text-slate-700 mb-1">Upload Photo</span>
                  <span class="text-[11px] text-slate-400">PNG, JPG up to 2MB</span>
                  <div v-if="newInstructor.profilePicture" class="mt-3 text-[12px] text-emerald-600 font-medium">{{ newInstructor.profilePicture.name }} selected</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Professional Information -->
          <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm space-y-6">
            <h3 class="text-[15px] font-bold text-slate-800">Professional Information</h3>
            <div class="grid grid-cols-2 gap-6">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Department <span class="text-rose-500">*</span></label>
                <select v-model="newInstructor.department_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca] bg-white">
                  <option value="">Select department</option>
                  <option v-for="d in allDepartments" :key="d.id" :value="d.id">{{ d.name }}</option>
                </select>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Employee ID</label>
                <input v-model="newInstructor.employeeId" type="text" placeholder="Enter employee ID" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Semester</label>
                <input type="text" disabled :value="settingsStore.semester" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-500 bg-slate-50 cursor-not-allowed font-bold">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Academic Year Level</label>
                <select v-model="newInstructor.year" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca] bg-white">
                  <option value="">Select level</option>
                  <option value="1st Year">1st Year</option>
                  <option value="2nd Year">2nd Year</option>
                  <option value="3rd Year">3rd Year</option>
                  <option value="4th Year">4th Year</option>
                  <option value="5th Year">5th Year</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Account Information -->
          <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm space-y-6">
            <h3 class="text-[15px] font-bold text-slate-800">Account Information</h3>
            <div class="grid grid-cols-3 gap-6">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Username <span class="text-rose-500">*</span></label>
                <input v-model="newInstructor.username" type="text" placeholder="Enter username" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Password <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <input v-model="newInstructor.password" :type="showPassword ? 'text' : 'password'" placeholder="Enter password" class="w-full border border-slate-200 rounded-xl pl-4 pr-10 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]">
                  <button @click="showPassword = !showPassword" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                    <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                  </button>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Confirm Password <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <input v-model="newInstructor.confirmPassword" :type="showConfirmPassword ? 'text' : 'password'" placeholder="Confirm password" class="w-full border border-slate-200 rounded-xl pl-4 pr-10 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]">
                  <button @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                    <svg v-if="!showConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Permissions & Access -->
          <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm space-y-6">
            <h3 class="text-[15px] font-bold text-slate-800">Instructor Permissions</h3>
            <div class="grid grid-cols-3 gap-6">
              <label v-for="perm in permissions" :key="perm.key" class="flex items-start gap-3 cursor-pointer group">
                <div class="relative flex items-center justify-center shrink-0 mt-0.5">
                  <input type="checkbox" v-model="perm.checked" class="peer appearance-none w-5 h-5 border-2 border-slate-200 rounded text-[#4338ca] focus:ring-[#4338ca] checked:bg-[#4338ca] checked:border-[#4338ca] transition-colors cursor-pointer">
                  <svg class="absolute w-3 h-3 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                  <div class="text-[13px] font-bold text-slate-700 group-hover:text-[#4338ca] transition-colors">{{ perm.label }}</div>
                  <div class="text-[12px] text-slate-500 mt-0.5">{{ perm.desc }}</div>
                </div>
              </label>
            </div>
          </div>

          <div class="flex items-center justify-between pt-4">
            <button @click="closeAdd" class="px-6 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="addInstructor" :disabled="isLoading" class="flex items-center gap-2 px-6 py-2.5 text-[13px] font-bold text-white bg-[#4338ca] rounded-xl hover:bg-indigo-700 transition-colors shadow-sm disabled:opacity-50">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
              Create Instructor
            </button>
          </div>

        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          
          <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
            <h3 class="text-[14px] font-bold text-slate-800 mb-5">Account Summary</h3>
            <div class="flex items-center gap-4 mb-6">
              <div class="w-14 h-14 bg-indigo-50 text-[#4338ca] rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
              </div>
              <div>
                <p class="text-[14px] font-bold text-slate-800">New Instructor</p>
                <p class="text-[12px] text-slate-500">{{ newInstructor.name || 'Name not set' }}</p>
              </div>
            </div>
            <div class="space-y-4">
              <div class="flex justify-between items-center"><span class="text-[12px] text-slate-500">Department</span><span class="text-[12px] font-bold text-slate-800">{{ allDepartments.find(d => d.id === newInstructor.department_id)?.name || 'Not Selected' }}</span></div>
              <div class="flex justify-between items-center"><span class="text-[12px] text-slate-500">Status</span><span class="text-[11px] font-bold bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded">Active</span></div>
              <div class="flex justify-between items-center"><span class="text-[12px] text-slate-500">Permissions</span><span class="text-[12px] font-bold text-slate-800">{{ permissions.filter(p=>p.checked).length }} Modules</span></div>
            </div>
          </div>

          <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
            <h3 class="text-[14px] font-bold text-slate-800 mb-4">Password Requirements</h3>
            <ul class="space-y-2.5">
              <li class="flex items-center gap-2.5 text-[12px] font-medium text-emerald-600"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> At least 8 characters long</li>
              <li class="flex items-center gap-2.5 text-[12px] font-medium text-emerald-600"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Include uppercase letter</li>
              <li class="flex items-center gap-2.5 text-[12px] font-medium text-emerald-600"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Include lowercase letter</li>
              <li class="flex items-center gap-2.5 text-[12px] font-medium text-emerald-600"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Include number</li>
              <li class="flex items-center gap-2.5 text-[12px] font-medium text-emerald-600"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Include special character</li>
            </ul>
          </div>

          <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
            <h3 class="text-[14px] font-bold text-slate-800 mb-4">Quick Actions</h3>
            <div class="space-y-3">
              <button class="w-full flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-[#4338ca] group transition-colors text-left">
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                  <span class="text-[11px] font-bold text-slate-700 group-hover:text-[#4338ca]">Import Instructors</span>
                </div>
                <svg class="w-3 h-3 text-slate-400 group-hover:text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
              <button class="w-full flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-[#4338ca] group transition-colors text-left">
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                  <span class="text-[11px] font-bold text-slate-700 group-hover:text-[#4338ca]">Manage Departments</span>
                </div>
                <svg class="w-3 h-3 text-slate-400 group-hover:text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
              <button class="w-full flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-[#4338ca] group transition-colors text-left">
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                  <span class="text-[11px] font-bold text-slate-700 group-hover:text-[#4338ca]">Manage Roles</span>
                </div>
                <svg class="w-3 h-3 text-slate-400 group-hover:text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
              <button class="w-full flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-[#4338ca] group transition-colors text-left">
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  <span class="text-[11px] font-bold text-slate-700 group-hover:text-[#4338ca]">View Instructor Guide</span>
                </div>
                <svg class="w-3 h-3 text-slate-400 group-hover:text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- ════════════════ MODALS ════════════════ -->
    <Teleport to="body">
      <!-- Delete -->
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
          <div class="p-6 text-center">
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
            </div>
            <h3 class="text-[16px] font-bold text-slate-800 mb-2">Remove Instructor?</h3>
            <p class="text-[13px] text-slate-500 leading-relaxed">Are you sure you want to remove <span class="font-bold text-slate-700">{{ selectedInstructor?.name }}</span>? This action cannot be undone.</p>
          </div>
          <div class="flex items-center gap-3 px-6 pb-6">
            <button @click="showDeleteModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="deleteInstructor" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors">Remove</button>
          </div>
        </div>
      </div>

      <!-- Edit -->
      <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
          <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 shrink-0">
            <h3 class="text-[16px] font-bold text-slate-800">Edit Instructor</h3>
            <button @click="showEditModal = false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-500 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
          </div>
          <div class="p-6 overflow-y-auto space-y-6">
            <div class="grid grid-cols-2 gap-5">
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Full Name <span class="text-rose-500">*</span></label><input v-model="editInstructorForm.name" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]"></div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Email Address <span class="text-rose-500">*</span></label><input v-model="editInstructorForm.email" type="email" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]"></div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Phone Number <span class="text-rose-500">*</span></label><input v-model="editInstructorForm.phone" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]"></div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Gender</label>
                <select v-model="editInstructorForm.gender" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca] bg-white">
                  <option value="">Select gender</option><option value="Male">Male</option><option value="Female">Female</option>
                </select>
              </div>
              <div class="col-span-2">
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Profile Picture</label>
                <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 flex flex-col items-center justify-center hover:border-[#4338ca] hover:bg-slate-50 transition-colors cursor-pointer" @click="triggerEditFileInput">
                  <input type="file" ref="editFileInput" class="hidden" accept="image/png, image/jpeg" @change="(e) => { const f = (e.target as HTMLInputElement).files?.[0]; if(f) editInstructorForm.profilePicture = f }">
                  <span class="text-[12px] font-bold text-slate-700">Upload Photo</span>
                  <div v-if="editInstructorForm.profilePicture" class="mt-1 text-[11px] text-emerald-600">{{ editInstructorForm.profilePicture.name }} selected</div>
                </div>
              </div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Department <span class="text-rose-500">*</span></label>
                <select v-model="editInstructorForm.department_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca] bg-white">
                  <option value="">Select Department</option><option v-for="d in allDepartments" :key="d.id" :value="d.id">{{ d.name }}</option>
                </select>
              </div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Employee ID</label><input v-model="editInstructorForm.employeeId" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]"></div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Username <span class="text-rose-500">*</span></label><input v-model="editInstructorForm.username" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]"></div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Password <span class="text-rose-500">*</span></label><input v-model="editInstructorForm.password" type="password" placeholder="Leave blank to keep current" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]"></div>
            </div>

            <div>
              <h4 class="text-[13px] font-bold text-slate-800 mb-3">Instructor Permissions</h4>
              <div class="grid grid-cols-2 gap-4">
                <label v-for="perm in editPermissions" :key="perm.key" class="flex items-start gap-2.5 cursor-pointer group">
                  <div class="relative flex items-center justify-center shrink-0 mt-0.5">
                    <input type="checkbox" v-model="perm.checked" class="peer appearance-none w-4 h-4 border-2 border-slate-200 rounded text-[#4338ca] focus:ring-[#4338ca] checked:bg-[#4338ca] checked:border-[#4338ca] transition-colors cursor-pointer">
                    <svg class="absolute w-2.5 h-2.5 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                  </div>
                  <div>
                    <div class="text-[12px] font-bold text-slate-700 group-hover:text-[#4338ca] transition-colors">{{ perm.label }}</div>
                  </div>
                </label>
              </div>
            </div>
          </div>
          <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 bg-slate-50/50 shrink-0">
            <button @click="showEditModal = false" class="px-5 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="saveEdit" :disabled="isLoading" class="px-5 py-2.5 text-[13px] font-bold text-white bg-[#4338ca] rounded-xl hover:bg-indigo-700 transition-colors shadow-sm disabled:opacity-50">Save Changes</button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>