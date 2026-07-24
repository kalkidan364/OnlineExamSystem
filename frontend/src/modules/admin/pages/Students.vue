<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import apiClient from '../../../core/api/apiClient'
import { useSettingsStore } from '../../../store/settingsStore'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Doughnut } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend)

const settingsStore = useSettingsStore()

// ── State ──
const search = ref('')
const yearFilter = ref('all')
const semesterFilter = ref('all')
const statusFilter = ref('all')
const deptFilter = ref('all')
const sectionFilter = ref('all')
const sections = ['A', 'B', 'C', 'D']
const currentPage = ref(1)
const perPage = 10

const showAddModal = ref(false)
const showEditModal = ref(false)
const editingStudent = ref<any>(null)
const showSuccessModal = ref(false)
const showDeleteModal = ref(false)
const showGetStudentModal = ref(false)
const getStudentQuery = ref({ id_no: '', year: '1st Year', semester: '1st Semester' })

const viewingStudent = ref<any>(null)

const selectedStudent = ref<any>(null)
const isLoading = ref(false)

const showPassword = ref(false)
const showConfirmPassword = ref(false)

const allStudents = ref<any[]>([])
const allDepartments = ref<any[]>([])

const academicYearLevels = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year']

// ── Add Form State ──
const newStudentForm = ref({
  fullName: '',
  email: '',
  username: '',
  password: '',
  confirmPassword: '',
  phone: '',
  dob: '',
  gender: '',
  studentId: '',
  admissionNumber: '',
  department_id: '',
  year_level: '',
  section: '',
})

const resetAddForm = () => {
  newStudentForm.value = {
    fullName: '',
    email: '',
    username: '',
    password: '',
    confirmPassword: '',
    phone: '',
    dob: '',
    gender: '',
    studentId: '',
    admissionNumber: '',
    department_id: '',
    year_level: '',
    section: '',
  }
}

// ── Edit Form State ──
const editStudentForm = ref({
  fullName: '',
  email: '',
  phone: '',
  dob: '',
  gender: '',
  studentId: '',
  admissionNumber: '',
  department_id: '',
  year_level: '',
  semester: '',
  section: '',
  status: 'active',
})

// ── Fetch Data ──
const fetchStudents = async () => {
  try {
    const res = await apiClient.get('/admin/users?role=student')
    allStudents.value = (res.data.data || []).map((u: any) => ({
      ...u,
      id: u.id,
      name: u.name,
      email: u.email,
      username: u.username || '—',
      phone: u.phone || '—',
      gender: u.gender || '—',
      avatar: u.name ? u.name.split(' ').map((w: string) => w[0]).join('').toUpperCase().slice(0, 2) : 'ST',
      status: u.status || 'active',
      departmentName: u.department?.name || '—',
      department_id: u.department_id,
      year: u.year_level || '1st Year',
      year_level: u.year_level || '1st Year',
      academic_year: u.academic_year || '2025/2026',
      semester: u.semester || settingsStore.semester || '1st Semester',
      section: u.section || 'A',
      id_no: u.id_no || `WU/${u.id.toString().padStart(4, '0')}/26`,
      registeredOn: u.created_at ? new Date(u.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) : '—',
      lastLogin: u.updated_at ? new Date(u.updated_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '—',
    }))
  } catch (err) {
    console.error('Failed to fetch students:', err)
  }
}

const fetchDepartments = async () => {
  try {
    const res = await apiClient.get('/admin/departments')
    allDepartments.value = res.data.data || []
  } catch (err) {
    console.error('Failed to fetch departments:', err)
  }
}

onMounted(async () => {
  await Promise.all([fetchStudents(), fetchDepartments()])
})

// ── Computed Properties ──
const filtered = computed(() =>
  allStudents.value.filter(s => {
    const matchSearch = s.name.toLowerCase().includes(search.value.toLowerCase()) ||
                        s.email.toLowerCase().includes(search.value.toLowerCase()) ||
                        (s.username && s.username.toLowerCase().includes(search.value.toLowerCase())) ||
                        (s.id_no && s.id_no.toLowerCase().includes(search.value.toLowerCase()))
    const matchYear   = yearFilter.value === 'all' || s.year_level === yearFilter.value || s.year === yearFilter.value
    const matchSem    = semesterFilter.value === 'all' || s.semester === semesterFilter.value
    const matchStatus = statusFilter.value === 'all' || s.status === statusFilter.value
    const matchDept   = deptFilter.value === 'all' || s.departmentName === deptFilter.value
    const matchSection = sectionFilter.value === 'all' || s.section === sectionFilter.value
    return matchSearch && matchYear && matchSem && matchStatus && matchDept && matchSection
  })
)

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))
const paginated  = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const stats = computed(() => {
  const total = allStudents.value.length
  const active = allStudents.value.filter(s => s.status === 'active').length
  const inactive = allStudents.value.filter(s => s.status === 'inactive').length
  const newStudents = allStudents.value.filter(s => {
    if (!s.created_at) return false
    const d = new Date(s.created_at)
    const now = new Date()
    return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear()
  }).length
  return { total, active, inactive, newStudents }
})

const levelCounts = computed(() => {
  const counts: Record<string, number> = {
    '1st Year': 0,
    '2nd Year': 0,
    '3rd Year': 0,
    '4th Year': 0,
    '5th Year': 0,
  }
  allStudents.value.forEach(s => {
    const y = s.year_level || s.year || '1st Year'
    if (counts[y] !== undefined) counts[y]++
    else counts['1st Year']++
  })
  return counts
})

const chartData = computed(() => ({
  labels: ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'],
  datasets: [{
    data: [
      levelCounts.value['1st Year'],
      levelCounts.value['2nd Year'],
      levelCounts.value['3rd Year'],
      levelCounts.value['4th Year'],
      levelCounts.value['5th Year'],
    ],
    backgroundColor: ['#c084fc', '#10b981', '#f97316', '#fbbf24', '#3b82f6'],
    borderWidth: 0,
  }]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false }, tooltip: { enabled: true } },
  cutout: '72%',
}

const topDepartments = computed(() => {
  const deptCounts: Record<string, number> = {}
  allStudents.value.forEach(s => {
    const dName = s.departmentName !== '—' ? s.departmentName : 'Unassigned'
    deptCounts[dName] = (deptCounts[dName] || 0) + 1
  })
  return Object.entries(deptCounts)
    .map(([name, count]) => ({ name, count }))
    .sort((a, b) => b.count - a.count)
    .slice(0, 5)
})

const recentRegistrations = computed(() => {
  return [...allStudents.value]
    .sort((a, b) => new Date(b.created_at || 0).getTime() - new Date(a.created_at || 0).getTime())
    .slice(0, 3)
})

const avatarColor = (id: number) => {
  const c = ['bg-indigo-500','bg-sky-500','bg-emerald-500','bg-violet-500','bg-amber-500','bg-rose-500','bg-teal-500','bg-orange-500','bg-cyan-500','bg-purple-500']
  return c[id % c.length]
}

const statusBadge = (s: string) => ({
  active: 'bg-emerald-50 text-emerald-700',
  inactive: 'bg-rose-50 text-rose-500',
  suspended: 'bg-rose-50 text-rose-600'
}[s] || 'bg-slate-100 text-slate-600')


// ── Actions ──
const addStudent = async () => {
  if (!newStudentForm.value.fullName || !newStudentForm.value.email || !newStudentForm.value.username || !newStudentForm.value.password) {
    alert('Please fill in all required fields (Full Name, Email Address, Username, Password).')
    return
  }
  if (newStudentForm.value.password !== newStudentForm.value.confirmPassword) {
    alert('Passwords do not match.')
    return
  }
  isLoading.value = true
  try {
    await apiClient.post('/admin/users', {
      name: newStudentForm.value.fullName,
      email: newStudentForm.value.email,
      username: newStudentForm.value.username,
      password: newStudentForm.value.password,
      phone: newStudentForm.value.phone || null,
      gender: newStudentForm.value.gender || null,
      role: 'student',
      department_id: newStudentForm.value.department_id || null,
      id_no: newStudentForm.value.studentId || null,
      year_level: newStudentForm.value.year_level || '1st Year',
      academic_year: '2025/2026',
      semester: settingsStore.semester || '1st Semester',
      section: newStudentForm.value.section || null,
    })
    await fetchStudents()
    showAddModal.value = false
    showSuccessModal.value = true
    setTimeout(() => { showSuccessModal.value = false }, 3000)
    resetAddForm()
  } catch (err: any) {
    let msg = 'Failed to create student.'
    if (err.response?.data) {
      msg = err.response.data.message || msg
      if (err.response.data.errors) {
        msg += '\n' + Object.values(err.response.data.errors).flat().join('\n')
      }
    }
    alert(msg)
  } finally {
    isLoading.value = false
  }
}

const confirmDelete = (s: any) => { selectedStudent.value = s; showDeleteModal.value = true }
const deleteStudent = async () => {
  if (!selectedStudent.value) return
  isLoading.value = true
  try {
    await apiClient.delete(`/admin/users/${selectedStudent.value.id}`)
    await fetchStudents()
    showDeleteModal.value = false
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete student.')
  } finally {
    isLoading.value = false
  }
}

const handleGetStudent = () => {
  search.value = getStudentQuery.value.id_no
  yearFilter.value = getStudentQuery.value.year || 'all'
  semesterFilter.value = getStudentQuery.value.semester || 'all'
  showGetStudentModal.value = false
}

const viewStudent = (student: any) => {
  viewingStudent.value = student
}

const openEditStudent = (student: any) => {
  editingStudent.value = student
  editStudentForm.value = {
    fullName: student.name || '',
    email: student.email || '',
    phone: student.phone || '',
    dob: student.dob || '',
    gender: student.gender || '',
    studentId: student.id_no || '',
    admissionNumber: student.admission_number || '',
    department_id: student.department_id || '',
    year_level: student.year_level || student.year || '1st Year',
    semester: student.semester || settingsStore.semester,
    section: student.section || '',
    status: student.status || 'active',
  }
  showEditModal.value = true
}

const saveEditStudent = async () => {
  if (!editStudentForm.value.fullName || !editStudentForm.value.email || !editingStudent.value) return
  isLoading.value = true
  try {
    await apiClient.put(`/admin/users/${editingStudent.value.id}`, {
      name: editStudentForm.value.fullName,
      email: editStudentForm.value.email,
      role: 'student',
      department_id: editStudentForm.value.department_id || null,
      id_no: editStudentForm.value.studentId,
      year_level: editStudentForm.value.year_level || '1st Year',
      academic_year: '2025/2026',
      semester: editStudentForm.value.semester || settingsStore.semester,
      section: editStudentForm.value.section || null,
      status: editStudentForm.value.status,
    })
    await fetchStudents()
    showEditModal.value = false
    editingStudent.value = null
  } catch (err: any) {
    let msg = 'Failed to update student.'
    if (err.response?.data) {
      msg = err.response.data.message || msg
      if (err.response.data.errors) {
        msg += '\n' + Object.values(err.response.data.errors).flat().join('\n')
      }
    }
    alert(msg)
  } finally {
    isLoading.value = false
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
      formData.append('role', 'student')
      await apiClient.post('/admin/users-import', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      alert(`Successfully imported ${file.name}`)
      await fetchStudents()
    } catch (err: any) { 
        alert(err.response?.data?.message || 'Failed to import students') 
    }
    finally {
      isLoading.value = false
      if (importFileInput.value) importFileInput.value.value = ''
    }
  }
}

const showExportDropdown = ref(false)
const handleExport = async (format: string) => {
  showExportDropdown.value = false;
  isLoading.value = true;
  try {
    const res = await apiClient.get(`/admin/users-export?role=student&format=${format}`, { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([res.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `students_export_${new Date().toISOString().slice(0,10)}.${format === 'pdf' ? 'pdf' : 'csv'}`);
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (err: any) {
    if (err.response && err.response.data && err.response.data instanceof Blob) {
      const text = await err.response.data.text();
      try {
        const json = JSON.parse(text);
        alert(json.message || 'Failed to export students');
      } catch (e) {
        alert('Failed to export students');
      }
    } else {
      alert('Failed to export students');
    }
  } finally {
    isLoading.value = false;
  }
}
</script>

<template>
  <div class="space-y-6">

    <input type="file" ref="importFileInput" @change="handleImport" accept=".csv" class="hidden">

    <!-- ==================== MAIN STUDENT LIST VIEW ==================== -->
    <template v-if="!showAddModal && !viewingStudent">
      
      <!-- Top Title & Action Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-[22px] font-bold text-slate-800">Students</h1>
          <p class="text-[13px] text-slate-500 mt-0.5">Manage all students and their information across the system.</p>
        </div>
        <div class="flex items-center gap-3">
          <button @click="triggerImport" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-[13px] font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Import Students
          </button>
          
          <div class="relative">
            <button @click="showExportDropdown = !showExportDropdown" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-[13px] font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
              <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
              Export Students
            </button>
            <div v-if="showExportDropdown" class="absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-50">
              <button @click="handleExport('csv')" class="w-full text-left px-4 py-2 text-[13px] text-slate-600 hover:bg-slate-50 hover:text-[#4338ca] transition-colors">Export as CSV</button>
              <button @click="handleExport('pdf')" class="w-full text-left px-4 py-2 text-[13px] text-slate-600 hover:bg-slate-50 hover:text-[#4338ca] transition-colors">Export as PDF</button>
            </div>
          </div>

          <button @click="showAddModal = true; resetAddForm()" class="flex items-center gap-2 px-5 py-2.5 bg-[#4338ca] hover:bg-indigo-700 text-white rounded-xl text-[13px] font-bold transition-colors shadow-sm shadow-indigo-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Student
          </button>
        </div>
      </div>

      <!-- Stats Cards Row -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 bg-indigo-50 text-[#4338ca] rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
          </div>
          <div>
            <p class="text-[12px] font-bold text-slate-500 mb-0.5">Total Students</p>
            <h3 class="text-[22px] font-black text-slate-800 leading-none">{{ stats.total }}</h3>
            <div class="flex items-center gap-1 mt-1 text-[11px] font-bold text-emerald-500">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
              <span>{{ stats.total ? 100 : 0 }}%</span><span class="text-slate-400 font-medium ml-1">Registered</span>
            </div>
          </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
          </div>
          <div>
            <p class="text-[12px] font-bold text-slate-500 mb-0.5">Active Students</p>
            <h3 class="text-[22px] font-black text-slate-800 leading-none">{{ stats.active }}</h3>
            <div class="flex items-center gap-1 mt-1 text-[11px] font-bold text-emerald-500">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
              <span>{{ stats.total ? ((stats.active / stats.total) * 100).toFixed(1) : 0 }}%</span><span class="text-slate-400 font-medium ml-1">Active</span>
            </div>
          </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path></svg>
          </div>
          <div>
            <p class="text-[12px] font-bold text-slate-500 mb-0.5">Inactive Students</p>
            <h3 class="text-[22px] font-black text-slate-800 leading-none">{{ stats.inactive }}</h3>
            <div class="flex items-center gap-1 mt-1 text-[11px] font-bold text-rose-500">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
              <span>{{ stats.total ? ((stats.inactive / stats.total) * 100).toFixed(1) : 0 }}%</span><span class="text-slate-400 font-medium ml-1">Inactive</span>
            </div>
          </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 bg-sky-50 text-sky-500 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path></svg>
          </div>
          <div>
            <p class="text-[12px] font-bold text-slate-500 mb-0.5">New Students</p>
            <h3 class="text-[22px] font-black text-slate-800 leading-none">{{ stats.newStudents }}</h3>
            <div class="flex items-center gap-1 mt-1 text-[11px] font-bold text-emerald-500">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
              <span>This Month</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Table Section (Full Width) -->
      <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col min-w-0 mb-6">
        <!-- Filters Row -->
        <div class="flex items-center gap-2 p-4 border-b border-slate-100 flex-wrap lg:flex-nowrap">
          <div class="relative flex-1 min-w-[200px]">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input v-model="search" type="text" placeholder="Search Students by name, email, ID..." class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-[12px] text-slate-600 focus:outline-none focus:border-[#4338ca] bg-white">
          </div>
          
          <select v-model="deptFilter" class="pl-3 pr-8 py-2 border border-slate-200 rounded-xl text-[12px] text-slate-600 focus:outline-none focus:border-[#4338ca] bg-white">
            <option value="all">All Departments</option>
            <option v-for="d in allDepartments" :key="d.id" :value="d.name">{{ d.name }}</option>
          </select>

          <select v-model="statusFilter" class="pl-3 pr-8 py-2 border border-slate-200 rounded-xl text-[12px] text-slate-600 focus:outline-none focus:border-[#4338ca] bg-white">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>

          <input type="text" disabled :value="settingsStore.semester" class="px-4 py-2 border border-slate-200 rounded-xl text-[12px] text-slate-500 bg-slate-50 font-bold cursor-not-allowed" />

          <select v-model="yearFilter" class="pl-3 pr-8 py-2 border border-slate-200 rounded-xl text-[12px] text-slate-600 focus:outline-none focus:border-[#4338ca] bg-white">
            <option value="all">All Academic Year Levels</option>
            <option v-for="lvl in academicYearLevels" :key="lvl" :value="lvl">{{ lvl }}</option>
          </select>

          <select v-model="sectionFilter" class="pl-3 pr-8 py-2 border border-slate-200 rounded-xl text-[12px] text-slate-600 focus:outline-none focus:border-[#4338ca] bg-white">
            <option value="all">All Sections</option>
            <option v-for="s in sections" :key="s" :value="s">{{ s }}</option>
          </select>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto w-full">
          <table class="w-full text-left">
            <thead>
              <tr class="border-b border-slate-100 bg-slate-50/50">
                <th class="py-4 px-5 text-[10px] font-black text-slate-400 uppercase tracking-wider">Student</th>
                <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Student ID</th>
                <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Email</th>
                <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Department</th>
                <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Section</th>
                <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Phone</th>
                <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider text-center">Status</th>
                <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Academic Year Level</th>
                <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(student, index) in paginated" :key="student.id || index" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                <td class="py-3.5 px-5">
                  <div class="flex items-center gap-3">
                    <div :class="[avatarColor(student.id || index), 'w-9 h-9 rounded-full flex items-center justify-center text-[11px] font-bold text-white shrink-0']">{{ student.avatar }}</div>
                    <div>
                      <span class="text-[13px] font-bold text-slate-800 block">{{ student.name }}</span>
                      <span v-if="student.username && student.username !== '—'" class="text-[11px] text-slate-400">@{{ student.username }}</span>
                    </div>
                  </div>
                </td>
                <td class="py-3.5 px-4 text-[12px] text-slate-800 font-bold">{{ student.id_no }}</td>
                <td class="py-3.5 px-4 text-[12px] text-slate-500 font-medium">{{ student.email }}</td>
                <td class="py-3.5 px-4 text-[12px] text-slate-600 font-medium">{{ student.departmentName }}</td>
                <td class="py-3.5 px-4">
                  <span class="bg-indigo-50 text-indigo-600 text-[10px] font-bold px-2.5 py-1 rounded-lg capitalize">{{ student.section }}</span>
                </td>
                <td class="py-3.5 px-4 text-[12px] text-slate-500 font-medium">{{ student.phone }}</td>
                <td class="py-3.5 px-4 text-center">
                  <span :class="[statusBadge(student.status), 'text-[10px] font-bold px-2.5 py-1 rounded-lg capitalize']">{{ student.status }}</span>
                </td>
                <td class="py-3.5 px-4 text-[12px] font-semibold text-slate-700">{{ student.year_level || student.year }}</td>
                <td class="py-3.5 px-4">
                  <!-- Static action icons (always visible) -->
                  <div class="flex items-center justify-center gap-1.5">
                    <button @click="viewStudent(student)" title="View Details" class="w-7 h-7 rounded bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 transition-colors">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                    <button @click="openEditStudent(student)" title="Edit Student" class="w-7 h-7 rounded bg-indigo-50 text-[#4338ca] flex items-center justify-center hover:bg-indigo-100 transition-colors">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </button>
                    <button @click="confirmDelete(student)" title="Delete Student" class="w-7 h-7 rounded bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-100 transition-colors">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="paginated.length === 0">
                <td colspan="9" class="py-16 text-center text-slate-500 text-[13px]">No students found.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between p-4 border-t border-slate-100 bg-slate-50/30">
          <p class="text-[12px] font-medium text-slate-500">Showing {{ filtered.length ? (currentPage - 1) * perPage + 1 : 0 }} to {{ Math.min(currentPage * perPage, filtered.length) }} of {{ filtered.length }} students</p>
          <div class="flex gap-1">
            <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="w-8 h-8 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-600 bg-white disabled:opacity-40">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button v-for="p in Math.min(totalPages, 5)" :key="p" @click="currentPage = p" :class="[currentPage === p ? 'bg-[#4338ca] text-white border-[#4338ca]' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50', 'w-8 h-8 rounded border flex items-center justify-center text-[13px] font-bold transition-colors']">{{ p }}</button>
            <span v-if="totalPages > 5" class="w-8 h-8 flex items-center justify-center text-slate-400 text-[13px]">...</span>
            <button v-if="totalPages > 5" @click="currentPage = totalPages" class="w-8 h-8 rounded border border-slate-200 flex items-center justify-center text-slate-600 bg-white text-[13px] font-bold hover:bg-slate-50">{{ totalPages }}</button>
            <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="w-8 h-8 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-600 bg-white disabled:opacity-40">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Bottom Widgets Grid (4 Columns) -->
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        <!-- Student Overview (Chart.js Donut) -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
          <h3 class="text-[14px] font-bold text-slate-800 mb-5">Student Overview</h3>
          <div class="relative w-44 h-44 mx-auto mb-5">
            <Doughnut :data="chartData" :options="chartOptions" />
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
              <span class="text-[24px] font-extrabold text-slate-800 leading-none">{{ stats.total }}</span>
              <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-1">Total Students</span>
            </div>
          </div>
          <div class="space-y-2">
            <div v-for="(lvl, i) in academicYearLevels" :key="lvl" class="flex items-center justify-between text-[12px]">
              <div class="flex items-center gap-2">
                <div :class="['w-2.5 h-2.5 rounded-full', ['bg-purple-500', 'bg-emerald-500', 'bg-orange-500', 'bg-amber-400', 'bg-blue-500'][i]]"></div>
                <span class="text-slate-600 font-medium">{{ lvl }}</span>
              </div>
              <span class="font-bold text-slate-800">{{ levelCounts[lvl] }}</span>
            </div>
          </div>
        </div>

        <!-- Top Departments -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
          <h3 class="text-[14px] font-bold text-slate-800 mb-4">Top Departments</h3>
          <div class="space-y-3">
            <div v-for="dept in topDepartments" :key="dept.name" class="flex items-center justify-between text-[12px]">
              <div class="flex items-center gap-2 text-slate-600">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span class="truncate max-w-[140px]">{{ dept.name }}</span>
              </div>
              <span class="font-bold text-slate-800 bg-slate-50 px-2 py-0.5 rounded">{{ dept.count }}</span>
            </div>
            <div v-if="topDepartments.length === 0" class="text-[12px] text-slate-400 text-center py-4">No department data available.</div>
          </div>
        </div>

        <!-- Recent Registrations -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
          <h3 class="text-[14px] font-bold text-slate-800 mb-4">Recent Registrations</h3>
          <div class="space-y-4">
            <div v-for="(s, i) in recentRegistrations" :key="s.id || i" class="flex items-center gap-3">
              <div :class="[avatarColor(s.id || i), 'w-9 h-9 rounded-full flex items-center justify-center text-[11px] font-bold text-white shrink-0']">{{ s.avatar }}</div>
              <div class="flex-1 min-w-0">
                <p class="text-[12px] font-bold text-slate-800 truncate">{{ s.name }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5 truncate">{{ s.departmentName }} · {{ s.registeredOn }}</p>
              </div>
            </div>
            <div v-if="recentRegistrations.length === 0" class="text-[12px] text-slate-400 text-center py-4">No recent registrations.</div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
          <h3 class="text-[14px] font-bold text-slate-800 mb-4">Quick Actions</h3>
          <div class="grid grid-cols-2 gap-3">
            <button @click="showAddModal = true; resetAddForm()" class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-[#4338ca] hover:bg-indigo-50/50 transition-colors text-[#4338ca]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
              <span class="text-[10px] font-bold text-center">Add New<br>Student</span>
            </button>
            <button @click="triggerImport" class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-slate-300 hover:bg-slate-50 transition-colors text-slate-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
              <span class="text-[10px] font-bold text-center">Import<br>Students</span>
            </button>
            <button class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-slate-300 hover:bg-slate-50 transition-colors text-slate-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
              <span class="text-[10px] font-bold text-center">Export<br>Students</span>
            </button>
            <button @click="showGetStudentModal = true" class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-slate-300 hover:bg-slate-50 transition-colors text-slate-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
              <span class="text-[10px] font-bold text-center">Find<br>Student</span>
            </button>
          </div>
        </div>

      </div>

    </template>

    <!-- ==================== ADD STUDENT FORM VIEW ==================== -->
    <template v-if="showAddModal && !viewingStudent">
      <div class="space-y-6">
        
        <!-- Header with Back Button on the Right -->
        <div class="flex items-center justify-between">
          <div>
            <div class="flex items-center gap-2 text-[12px] text-slate-400 mb-1">
              <span class="hover:text-[#4338ca] cursor-pointer transition-colors" @click="showAddModal = false">Students</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              <span class="font-semibold text-slate-600">Add New Student</span>
            </div>
            <h1 class="text-[22px] font-bold text-slate-800">Add New Student</h1>
            <p class="text-[13px] text-slate-500 mt-0.5">Create a new student account and set their information.</p>
          </div>
          
          <button @click="showAddModal = false" class="flex items-center gap-2 px-4 py-2.5 border border-slate-200 text-slate-600 font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm bg-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Students
          </button>
        </div>

        <div class="space-y-6 max-w-5xl">
          
          <!-- Personal Information -->
          <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm space-y-6">
            <h3 class="text-[15px] font-bold text-slate-800">Personal Information</h3>
            <div class="grid grid-cols-3 gap-6">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Full Name <span class="text-rose-500">*</span></label>
                <input type="text" v-model="newStudentForm.fullName" placeholder="Enter full name" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Email Address <span class="text-rose-500">*</span></label>
                <input type="email" v-model="newStudentForm.email" placeholder="Enter email address" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Phone Number</label>
                <input type="text" v-model="newStudentForm.phone" placeholder="Enter phone number" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca]">
              </div>
              
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Date of Birth</label>
                <input v-model="newStudentForm.dob" type="date" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] bg-white">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Gender</label>
                <select v-model="newStudentForm.gender" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 bg-white focus:outline-none focus:border-[#4338ca]">
                  <option value="">Select gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Academic Information -->
          <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm space-y-6">
            <h3 class="text-[15px] font-bold text-slate-800">Academic Information</h3>
            <div class="grid grid-cols-3 gap-6">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Student ID</label>
                <input type="text" v-model="newStudentForm.studentId" placeholder="e.g. WU/0019/26" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Admission Number</label>
                <input type="text" v-model="newStudentForm.admissionNumber" placeholder="Enter admission no." class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Department <span class="text-rose-500">*</span></label>
                <select v-model="newStudentForm.department_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 bg-white focus:outline-none focus:border-[#4338ca]">
                  <option value="">Select department</option>
                  <option v-for="d in allDepartments" :key="d.id" :value="d.id">{{ d.name }}</option>
                </select>
              </div>
              
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Semester</label>
                <input type="text" disabled :value="settingsStore.semester" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-500 bg-slate-50 cursor-not-allowed font-bold">
              </div>

              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Academic Year Level <span class="text-rose-500">*</span></label>
                <select v-model="newStudentForm.year_level" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 bg-white focus:outline-none focus:border-[#4338ca]">
                  <option value="">Select level</option>
                  <option v-for="lvl in academicYearLevels" :key="lvl" :value="lvl">{{ lvl }}</option>
                </select>
              </div>

              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Section</label>
                <select v-model="newStudentForm.section" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 bg-white focus:outline-none focus:border-[#4338ca]">
                  <option value="">Select section</option>
                  <option v-for="s in sections" :key="s" :value="s">{{ s }}</option>
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
                <input v-model="newStudentForm.username" type="text" placeholder="Enter username" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Password <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <input v-model="newStudentForm.password" :type="showPassword ? 'text' : 'password'" placeholder="Enter password" class="w-full border border-slate-200 rounded-xl pl-4 pr-10 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]">
                  <button @click="showPassword = !showPassword" type="button" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                    <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                  </button>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Confirm Password <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <input v-model="newStudentForm.confirmPassword" :type="showConfirmPassword ? 'text' : 'password'" placeholder="Confirm password" class="w-full border border-slate-200 rounded-xl pl-4 pr-10 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]">
                  <button @click="showConfirmPassword = !showConfirmPassword" type="button" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                    <svg v-if="!showConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Save Action Button -->
          <div class="flex items-center justify-end gap-3">
            <button @click="showAddModal = false" class="px-6 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 hover:bg-slate-50 rounded-xl transition-colors">
              Cancel
            </button>
            <button @click="addStudent" :disabled="isLoading" class="px-8 py-2.5 text-[13px] font-bold text-white bg-[#4338ca] disabled:opacity-70 disabled:cursor-not-allowed hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-200 transition-colors">
              Save Student Record
            </button>
          </div>
          
        </div>
      </div>
    </template>

    <!-- ==================== VIEW STUDENT DETAIL ==================== -->
    <template v-if="viewingStudent">
      <div class="space-y-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <button @click="viewingStudent = null" class="p-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </button>
            <div>
              <h1 class="text-[20px] font-bold text-slate-800">{{ viewingStudent.name }}</h1>
              <p class="text-[12px] text-slate-500">{{ viewingStudent.email }} · {{ viewingStudent.id_no }}</p>
            </div>
          </div>
          <button @click="openEditStudent(viewingStudent); viewingStudent = null" class="px-4 py-2 bg-[#4338ca] text-white text-[13px] font-bold rounded-xl hover:bg-indigo-700 transition-colors">
            Edit Student
          </button>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
          <h3 class="text-[15px] font-bold text-slate-800">Student Overview</h3>
          <div class="grid grid-cols-3 gap-6 text-[13px]">
            <div><span class="text-slate-400 block text-[11px] font-bold uppercase">Full Name</span><span class="font-semibold text-slate-800">{{ viewingStudent.name }}</span></div>
            <div><span class="text-slate-400 block text-[11px] font-bold uppercase">Student ID</span><span class="font-semibold text-slate-800">{{ viewingStudent.id_no }}</span></div>
            <div><span class="text-slate-400 block text-[11px] font-bold uppercase">Email</span><span class="font-semibold text-slate-800">{{ viewingStudent.email }}</span></div>
            <div><span class="text-slate-400 block text-[11px] font-bold uppercase">Username</span><span class="font-semibold text-slate-800">{{ viewingStudent.username || '—' }}</span></div>
            <div><span class="text-slate-400 block text-[11px] font-bold uppercase">Password</span><span class="font-semibold text-slate-800">********</span></div>
            <div><span class="text-slate-400 block text-[11px] font-bold uppercase">Department</span><span class="font-semibold text-slate-800">{{ viewingStudent.departmentName }}</span></div>
            <div><span class="text-slate-400 block text-[11px] font-bold uppercase">Academic Year Level</span><span class="font-semibold text-slate-800">{{ viewingStudent.year_level || viewingStudent.year }}</span></div>
            <div><span class="text-slate-400 block text-[11px] font-bold uppercase">Semester</span><span class="font-semibold text-slate-800">{{ viewingStudent.semester }}</span></div>
            <div><span class="text-slate-400 block text-[11px] font-bold uppercase">Section</span><span class="font-semibold text-slate-800">{{ viewingStudent.section || '—' }}</span></div>
            <div><span class="text-slate-400 block text-[11px] font-bold uppercase">Phone</span><span class="font-semibold text-slate-800">{{ viewingStudent.phone || '—' }}</span></div>
            <div><span class="text-slate-400 block text-[11px] font-bold uppercase">Gender</span><span class="font-semibold text-slate-800">{{ viewingStudent.gender || '—' }}</span></div>
            <div><span class="text-slate-400 block text-[11px] font-bold uppercase">Status</span><span class="font-semibold capitalize text-emerald-600">{{ viewingStudent.status }}</span></div>
            <div><span class="text-slate-400 block text-[11px] font-bold uppercase">Registered Date</span><span class="font-semibold text-slate-800">{{ viewingStudent.registeredOn }}</span></div>
          </div>
        </div>
      </div>
    </template>

    <!-- Success Modal -->
    <Teleport to="body">
      <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl flex flex-col items-center transform transition-all animate-in fade-in zoom-in-95 duration-200">
          <div class="w-20 h-20 bg-emerald-100 text-emerald-500 rounded-full flex items-center justify-center mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h2 class="text-[22px] font-black text-slate-800 mb-2">Student Created!</h2>
          <p class="text-[14px] text-slate-500 font-medium leading-relaxed">
            The new student has been successfully added to the system database.
          </p>
        </div>
      </div>
    </Teleport>

    <!-- Edit Student Modal -->
    <Teleport to="body">
      <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden">
          <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <h3 class="text-[16px] font-bold text-slate-800">Edit Student</h3>
            <button @click="showEditModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>

          <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Full Name</label>
                <input v-model="editStudentForm.fullName" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-[13px] focus:outline-none focus:border-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Email Address</label>
                <input v-model="editStudentForm.email" type="email" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-[13px] focus:outline-none focus:border-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Student ID</label>
                <input v-model="editStudentForm.studentId" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-[13px] focus:outline-none focus:border-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Department</label>
                <select v-model="editStudentForm.department_id" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-[13px] focus:outline-none focus:border-[#4338ca] bg-white">
                  <option value="">Select department</option>
                  <option v-for="d in allDepartments" :key="d.id" :value="d.id">{{ d.name }}</option>
                </select>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Academic Year Level</label>
                <select v-model="editStudentForm.year_level" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-[13px] focus:outline-none focus:border-[#4338ca] bg-white">
                  <option v-for="lvl in academicYearLevels" :key="lvl" :value="lvl">{{ lvl }}</option>
                </select>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Semester</label>
                <input type="text" disabled :value="settingsStore.semester" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-[13px] text-slate-500 bg-slate-50 font-bold cursor-not-allowed">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Section</label>
                <select v-model="editStudentForm.section" class="w-full border border-slate-200 rounded-xl px-4 py-2 text-[13px] focus:outline-none focus:border-[#4338ca] bg-white">
                  <option value="">Select section</option>
                  <option v-for="s in sections" :key="s" :value="s">{{ s }}</option>
                </select>
              </div>
            </div>
            
            <div class="flex items-center justify-between mt-6 p-4 border border-slate-200 rounded-xl">
              <div>
                <label class="block text-[13px] font-bold text-slate-700">Student Status</label>
                <p class="text-[11px] text-slate-500 mt-0.5">Toggle to set student as active or inactive.</p>
              </div>
              <button 
                @click="editStudentForm.status = editStudentForm.status === 'active' ? 'inactive' : 'active'" 
                :class="[
                  editStudentForm.status === 'active' ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-slate-300 hover:bg-slate-400',
                  'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none'
                ]" 
                role="switch" 
                :aria-checked="editStudentForm.status === 'active'"
              >
                <span 
                  aria-hidden="true" 
                  :class="[
                    editStudentForm.status === 'active' ? 'translate-x-5' : 'translate-x-0',
                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                  ]"
                ></span>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            <button @click="showEditModal = false" class="px-5 py-2 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">Cancel</button>
            <button @click="saveEditStudent" :disabled="isLoading" class="px-6 py-2 text-[13px] font-bold text-white bg-[#4338ca] hover:bg-indigo-700 rounded-xl transition-all disabled:opacity-50">Save Changes</button>
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

    <!-- Get Student Modal -->
    <Teleport to="body">
      <div v-if="showGetStudentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
          <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div><h3 class="text-[16px] font-bold text-slate-800">Find Student</h3><p class="text-[12px] text-slate-500 mt-0.5">Find a specific student.</p></div>
            <button @click="showGetStudentModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
          </div>
          <div class="px-6 py-5 space-y-4">
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Student ID / Search</label>
              <input v-model="getStudentQuery.id_no" @keyup.enter="handleGetStudent" type="text" placeholder="Enter ID or Name..." class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca]">
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Academic Year Level</label>
              <select v-model="getStudentQuery.year" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca] bg-white">
                <option value="all">Any Level</option>
                <option v-for="lvl in academicYearLevels" :key="lvl" :value="lvl">{{ lvl }}</option>
              </select>
            </div>
          </div>
          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            <button @click="showGetStudentModal = false" class="px-5 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">Cancel</button>
            <button @click="handleGetStudent" class="px-5 py-2.5 text-[13px] font-bold text-white bg-[#4338ca] hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-200 transition-all">Filter</button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>