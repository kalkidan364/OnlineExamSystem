<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import apiClient from '../../../core/api/apiClient'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Doughnut } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend)

const search = ref('')
const yearFilter = ref('all')
const academicYearFilter = ref('all')
const semesterFilter = ref('all')
const statusFilter = ref('all')
const deptFilter = ref('all')
const sectionFilter = ref('all')
const sections = ['A', 'B', 'C']
const currentPage = ref(1)
const perPage = 10
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingStudent = ref<any>(null)
const editStudentForm = ref({
  fullName: '',
  email: '',
  phone: '',
  dob: '',
  gender: '',
  studentId: '',
  admissionNumber: '',
  department_id: '',
  year: '',
  semester: '',
  section: '',
  role: 'Student',
  accessLevel: 'Standard Student',
  status: 'Active',
  permissions: 'Default Access',
})
const showSuccessModal = ref(false)
const showDeleteModal = ref(false)
const showGetStudentModal = ref(false)
const getStudentQuery = ref({ id_no: '', year: 'Year 1', semester: '1st Semester' })

const viewingStudent = ref<any>(null)
const activeTab = ref('personal')

const viewingCourse = ref<any>(null)
const activeCourseTab = ref('overview')
const creatingAnnouncement = ref(false)

const selectedStudent = ref<any>(null)
const isLoading = ref(false)

const allStudents = ref<any[]>([])
const allDepartments = ref<any[]>([])

const newStudent = ref({ name: '', email: '', year_level: 'Year 1', academic_year: '2025/2026', semester: '1st Semester', department_id: '', id_no: '', password: '' })
const years = ['Year 1', 'Year 2', 'Year 3', 'Year 4']
const academicYears = ['2025/2026', '2026/2027', '2027/2028', '2028/2029']
const semesters = ['1st Semester', '2nd Semester', '3rd Semester']

const levelLabels = ['Level 1', 'Level 2', 'Level 3', 'Level 4']
const mockRegistered = ['May 12, 2025', 'Apr 20, 2025', 'Mar 15, 2025', 'Feb 10, 2025', 'Jan 05, 2025', 'Dec 28, 2024', 'Dec 15, 2024', 'Nov 30, 2024', 'Nov 10, 2024', 'Oct 22, 2024']
const mockLastLogin = ['Today, 10:20 AM', 'Yesterday, 04:15 PM', 'Today, 09:30 AM', '2 weeks ago', 'Today, 11:05 AM', 'Yesterday, 02:40 PM', 'Today, 08:45 AM', '3 weeks ago', 'Today, 12:10 PM', 'Yesterday, 05:30 PM']

const fetchStudents = async () => {
  try {
    const res = await apiClient.get('/admin/users?role=student')
    allStudents.value = (res.data.data || []).map((u: any, i: number) => ({
      ...u,
      avatar: u.name ? u.name.split(' ').map((w: string) => w[0]).join('').toUpperCase().slice(0, 2) : '??',
      status: i % 8 === 3 || i % 8 === 7 ? 'inactive' : 'active',
      enrolled: new Date(u.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
      departmentName: u.department?.name || '—',
      exams: 0,
      avg: 0,
      year: u.year_level || levelLabels[i % 4],
      level: levelLabels[i % 4],
      academic_year: u.academic_year || '—',
      semester: u.semester || '—',
      section: u.section || '—',
      id_no: u.id_no || `WU/${u.id.toString().padStart(4, '0')}/26`,
      registeredOn: mockRegistered[i % mockRegistered.length],
      lastLogin: mockLastLogin[i % mockLastLogin.length],
    }))
  } catch (err) { console.error('Failed to fetch students:', err) }
}

const fetchDepartments = async () => {
  try {
    const res = await apiClient.get('/admin/departments')
    allDepartments.value = res.data.data || []
  } catch (err) { console.error('Failed to fetch departments:', err) }
}

onMounted(async () => { await Promise.all([fetchStudents(), fetchDepartments()]) })

const filtered = computed(() =>
  allStudents.value.filter(s => {
    const matchSearch = s.name.toLowerCase().includes(search.value.toLowerCase()) ||
                        s.email.toLowerCase().includes(search.value.toLowerCase()) ||
                        (s.id_no && s.id_no.toLowerCase().includes(search.value.toLowerCase()))
    const matchYear   = yearFilter.value === 'all' || s.year === yearFilter.value
    const matchAcYear = academicYearFilter.value === 'all' || s.academic_year === academicYearFilter.value
    const matchSem    = semesterFilter.value === 'all' || s.semester === semesterFilter.value
    const matchStatus = statusFilter.value === 'all' || s.status === statusFilter.value
    const matchDept   = deptFilter.value === 'all' || s.departmentName === deptFilter.value
    const matchSection = sectionFilter.value === 'all' || s.section === sectionFilter.value
    return matchSearch && matchYear && matchAcYear && matchSem && matchStatus && matchDept && matchSection
  })
)

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))
const paginated  = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const stats = computed(() => ({
  total:     allStudents.value.length,
  active:    allStudents.value.filter(s => s.status === 'active').length,
  inactive:  allStudents.value.filter(s => s.status === 'inactive').length,
  avgScore:  allStudents.value.length ? Math.round(allStudents.value.reduce((a, s) => a + (s.avg || 0), 0) / allStudents.value.length) : 0,
  exams:     allStudents.value.reduce((a, s) => a + (s.exams || 0), 0),
}))

const avatarColor = (id: number) => {
  const c = ['bg-indigo-500','bg-sky-500','bg-emerald-500','bg-violet-500','bg-amber-500','bg-rose-500','bg-teal-500','bg-orange-500','bg-cyan-500','bg-purple-500']
  return c[id % c.length]
}

const statusBadge  = (s: string) => ({ active:'bg-emerald-50 text-emerald-700', inactive:'bg-rose-50 text-rose-500', suspended:'bg-rose-50 text-rose-600' }[s] || 'bg-slate-100 text-slate-600')
const statusDot    = (s: string) => ({ active:'bg-emerald-500', inactive:'bg-slate-400', suspended:'bg-rose-500' }[s] || 'bg-slate-400')
const scoreColor   = (n: number) => n >= 80 ? 'text-emerald-600' : n >= 60 ? 'text-amber-600' : 'text-rose-600'
const scoreBg      = (n: number) => n >= 80 ? 'bg-emerald-500' : n >= 60 ? 'bg-amber-500' : 'bg-rose-500'

const levelColor = (level: string) => {
  const map: Record<string, string> = {
    'Level 1': 'bg-amber-50 text-amber-600',
    'Level 2': 'bg-orange-50 text-orange-600',
    'Level 3': 'bg-emerald-50 text-emerald-600',
    'Level 4': 'bg-purple-50 text-purple-600',
    'Year 1': 'bg-amber-50 text-amber-600',
    'Year 2': 'bg-orange-50 text-orange-600',
    'Year 3': 'bg-emerald-50 text-emerald-600',
    'Year 4': 'bg-purple-50 text-purple-600',
  }
  return map[level] || 'bg-slate-50 text-slate-600'
}

const importFileInput = ref<HTMLInputElement | null>(null)
const triggerImport = () => { if (importFileInput.value) importFileInput.value.click() }
const handleImport = async (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (file) {
    isLoading.value = true
    try {
      await new Promise(resolve => setTimeout(resolve, 1000))
      alert(`Successfully imported ${file.name}`)
      await fetchStudents()
    } catch (err) {
      console.error('Import failed', err)
      alert('Failed to import students')
    } finally {
      isLoading.value = false
      if (importFileInput.value) importFileInput.value.value = ''
    }
  }
}

// Form state
const newStudentForm = ref({
  fullName: '',
  email: '',
  phone: '',
  dob: '',
  gender: '',
  studentId: '',
  admissionNumber: '',
  department_id: '',
  year: '',
  semester: '',
  section: '',
  role: 'Student',
  accessLevel: 'Standard Student',
  status: 'Active',
  permissions: 'Default Access',
})

const addStudent = async () => {
  if (!newStudentForm.value.fullName || !newStudentForm.value.email) return
  isLoading.value = true
  try {
    await apiClient.post('/admin/users', {
      name: newStudentForm.value.fullName,
      email: newStudentForm.value.email,
      role: 'student',
      department_id: newStudentForm.value.department_id || null,
      id_no: newStudentForm.value.studentId,
      password: 'DefaultPassword123!',
      year_level: newStudentForm.value.year || 'Year 1',
      academic_year: '2025/2026',
      semester: newStudentForm.value.semester || '1st Semester',
      section: newStudentForm.value.section || null,
    })
    await fetchStudents()
    showAddModal.value = false
    showSuccessModal.value = true
    setTimeout(() => { showSuccessModal.value = false }, 3000)
    newStudentForm.value = { fullName: '', email: '', phone: '', dob: '', gender: '', studentId: '', admissionNumber: '', department_id: '', year: '', semester: '', section: '', role: 'Student', accessLevel: 'Standard Student', status: 'Active', permissions: 'Default Access' }
  } catch (err: any) {
    let msg = 'Failed to create student.'
    if (err.response?.data) {
      msg = err.response.data.message || msg
      if (err.response.data.errors) {
        msg += '\n' + Object.values(err.response.data.errors).flat().join('\n')
      }
    }
    alert(msg)
  } finally { isLoading.value = false }
}

const confirmDelete  = (s: any) => { selectedStudent.value = s; showDeleteModal.value = true }
const deleteStudent  = async () => {
  if (!selectedStudent.value) return
  isLoading.value = true
  try {
    await apiClient.delete(`/admin/users/${selectedStudent.value.id}`)
    await fetchStudents()
    showDeleteModal.value = false
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete student.')
  } finally { isLoading.value = false }
}
const handleGetStudent = () => {
  search.value = getStudentQuery.value.id_no
  yearFilter.value = getStudentQuery.value.year || 'all'
  semesterFilter.value = getStudentQuery.value.semester || 'all'
  showGetStudentModal.value = false
}

const toggleStatus   = (s: any) => { s.status = s.status === 'active' ? 'inactive' : 'active' }
const viewStudent = (name: string, email: string, dept: string, level: string, phone: string, status: string, registered: string, lastLogin: string) => {
  viewingStudent.value = { name, email, dept, level, phone, status, registered, lastLogin }
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
    year: student.year_level || student.year || '',
    semester: student.semester || '',
    section: student.section || '',
    role: 'Student',
    accessLevel: 'Standard Student',
    status: student.status || 'active',
    permissions: 'Default Access',
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
      year_level: editStudentForm.value.year || 'Year 1',
      academic_year: '2025/2026',
      semester: editStudentForm.value.semester || '1st Semester',
      section: editStudentForm.value.section || null,
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
  } finally { isLoading.value = false }
}

// Chart.js donut chart data
const chartData = {
  labels: ['Level 4', 'Level 3', 'Level 2', 'Level 1'],
  datasets: [{
    backgroundColor: ['#7C3AED', '#10B981', '#F97316', '#F59E0B'],
    data: [37.5, 27.2, 23.3, 12.0],
    borderWidth: 0,
    hoverOffset: 4
  }]
}
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '72%',
  plugins: {
    legend: { display: false },
    tooltip: { enabled: true }
  }
}

console.log(newStudentForm, editStudentForm, totalPages, paginated, stats, avatarColor, statusBadge, statusDot, scoreColor, scoreBg, addStudent, confirmDelete, deleteStudent, handleGetStudent, toggleStatus, activeTab, viewingCourse, activeCourseTab, creatingAnnouncement, years, academicYears, semesters, newStudent, viewStudent, levelColor, openEditStudent, saveEditStudent, showEditModal, editingStudent)
</script>

<template>
  <div class="w-full">
    <div v-if="!viewingStudent" class="space-y-6 pb-12 min-w-0 w-full">

    <!-- List View -->
    <div v-if="!showAddModal && !showEditModal" class="space-y-6 min-w-0 w-full">

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h1 class="text-[24px] font-bold text-slate-800">Students</h1>
          </div>
          <p class="text-[13px] text-slate-500 ml-[52px]">Manage all students and their information across the system.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
          <input type="file" ref="importFileInput" class="hidden" accept=".csv,.xlsx,.xls" @change="handleImport">
          <button @click="triggerImport" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-[#4338ca] font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Import Students
          </button>
          <button class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-[#4338ca] font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Export Students
          </button>
          <button @click="showAddModal = true" class="flex items-center gap-2 px-4 py-2.5 bg-[#4338ca] text-white font-bold rounded-xl text-[13px] hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Add New Student
          </button>
        </div>
      </div>

      <!-- Stats Cards -->
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
              <span>14.7%</span><span class="text-slate-400 font-medium ml-1">All registered students</span>
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
              <span>13.3%</span><span class="text-slate-400 font-medium ml-1">Active students</span>
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
              <span>8.6%</span><span class="text-slate-400 font-medium ml-1">Inactive students</span>
            </div>
          </div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 bg-sky-50 text-sky-500 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path></svg>
          </div>
          <div>
            <p class="text-[12px] font-bold text-slate-500 mb-0.5">New Students</p>
            <h3 class="text-[22px] font-black text-slate-800 leading-none">{{ Math.round(stats.total * 0.1) }}</h3>
            <div class="flex items-center gap-1 mt-1 text-[11px] font-bold text-emerald-500">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
              <span>16.2%</span><span class="text-slate-400 font-medium ml-1">This month</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Layout: Table on Left, Sidebar on Right -->
      <div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] xl:grid-cols-[1fr_320px] gap-6 items-start">

        <!-- Left Column: Table & Filters -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col min-w-0">
          <!-- Filters Row -->
          <div class="flex items-center gap-2 p-4 border-b border-slate-100 overflow-x-auto">
            <div class="relative flex-1 min-w-[160px]">
              <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
              <input v-model="search" type="text" placeholder="Search Students..." class="w-full border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-[12px] text-slate-600 focus:outline-none focus:border-[#4338ca] bg-white">
            </div>
            <select v-model="deptFilter" class="appearance-none pl-3 pr-8 py-2 border border-slate-200 rounded-xl text-[12px] text-slate-600 focus:outline-none focus:border-[#4338ca] bg-white">
              <option value="all">All Departments</option>
              <option v-for="d in allDepartments" :key="d.id" :value="d.name">{{ d.name }}</option>
            </select>
            <select v-model="statusFilter" class="appearance-none pl-3 pr-8 py-2 border border-slate-200 rounded-xl text-[12px] text-slate-600 focus:outline-none focus:border-[#4338ca] bg-white">
              <option value="all">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
            <select v-model="semesterFilter" class="appearance-none pl-3 pr-8 py-2 border border-slate-200 rounded-xl text-[12px] text-slate-600 focus:outline-none focus:border-[#4338ca] bg-white">
              <option value="all">All Semesters</option>
              <option value="semester_1">Semester 1</option>
              <option value="semester_2">Semester 2</option>
              <option value="summer">Summer</option>
            </select>
            <select v-model="yearFilter" class="appearance-none pl-3 pr-8 py-2 border border-slate-200 rounded-xl text-[12px] text-slate-600 focus:outline-none focus:border-[#4338ca] bg-white">
              <option value="all">All Years</option>
              <option value="year_1">Year 1</option>
              <option value="year_2">Year 2</option>
              <option value="year_3">Year 3</option>
              <option value="year_4">Year 4</option>
              <option value="year_5">Year 5</option>
            </select>
            <select v-model="sectionFilter" class="appearance-none pl-3 pr-8 py-2 border border-slate-200 rounded-xl text-[12px] text-slate-600 focus:outline-none focus:border-[#4338ca] bg-white">
              <option value="all">All Sections</option>
              <option v-for="s in sections" :key="s" :value="s">{{ s }}</option>
            </select>
          </div>

          <!-- Table -->
          <div class="flex-1 overflow-x-auto min-w-0">
            <table class="w-full text-left whitespace-nowrap min-w-max">
              <thead>
                <tr class="border-b border-slate-100">
                  <th class="py-4 px-5 text-[10px] font-black text-slate-400 uppercase tracking-wider">Student</th>
                  <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Student ID</th>
                  <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Email</th>
                  <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Department</th>
                  <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Semester</th>
                  <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Phone</th>
                  <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider text-center">Status</th>
                  <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Year</th>
                  <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Last Login</th>
                  <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(student, index) in paginated" :key="student.id || index" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors group">
                  <td class="py-3.5 px-5">
                    <div class="flex items-center gap-3">
                      <div :class="[avatarColor(student.id || index), 'w-9 h-9 rounded-full flex items-center justify-center text-[11px] font-bold text-white shrink-0']">{{ student.avatar }}</div>
                      <span class="text-[13px] font-bold text-slate-800">{{ student.name }}</span>
                    </div>
                  </td>
                  <td class="py-3.5 px-4 text-[12px] text-slate-800 font-bold">{{ student.id_no }}</td>
                  <td class="py-3.5 px-4 text-[12px] text-slate-500 font-medium">{{ student.email }}</td>
                  <td class="py-3.5 px-4 text-[12px] text-slate-600 font-medium">{{ student.departmentName }}</td>
                  <td class="py-3.5 px-4">
                    <span class="bg-indigo-50 text-indigo-600 text-[10px] font-bold px-2.5 py-1 rounded-lg capitalize">{{ student.semester?.replace('_', ' ') || 'Semester 1' }}</span>
                  </td>
                  <td class="py-3.5 px-4 text-[12px] text-slate-500 font-medium">{{ student.phone || '+251 91 123 4567' }}</td>
                  <td class="py-3.5 px-4 text-center">
                    <span :class="[statusBadge(student.status), 'text-[10px] font-bold px-2.5 py-1 rounded-lg capitalize']">{{ student.status }}</span>
                  </td>
                  <td class="py-3.5 px-4 text-[12px] text-slate-500">{{ student.year?.replace('_', ' ') || 'Year 1' }}</td>
                  <td class="py-3.5 px-4 text-[12px] text-slate-500">{{ student.lastLogin }}</td>
                  <td class="py-3.5 px-4">
                    <div class="flex items-center justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                      <button @click="viewStudent(student.name, student.email, student.departmentName, student.year, student.phone || '+251 91 123 4567', student.status, student.registeredOn, student.lastLogin)" class="w-7 h-7 rounded bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                      <button @click="openEditStudent(student)" class="w-7 h-7 rounded bg-indigo-50 text-[#4338ca] flex items-center justify-center hover:bg-indigo-100 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                      <button @click="confirmDelete(student)" class="w-7 h-7 rounded bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-100 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
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
            <p class="text-[12px] font-medium text-slate-500">Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, filtered.length) }} of {{ filtered.length }} students</p>
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

        <!-- Right Sidebar -->
        <div class="space-y-6 min-w-0">

          <!-- Student Overview (Chart.js Donut) -->
          <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-[15px] font-bold text-slate-800 mb-5">Student Overview</h3>
            <div class="relative h-44 w-full">
              <Doughnut :data="chartData" :options="chartOptions" />
              <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <h4 class="text-[22px] font-black text-slate-800 leading-none">{{ stats.total }}</h4>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Students</p>
              </div>
            </div>
            <!-- HTML Legend -->
            <div class="space-y-2.5 mt-5">
              <div v-for="(item, i) in [
                { label: 'Level 4', count: 842, pct: '37.5%', color: 'bg-purple-600' },
                { label: 'Level 3', count: 612, pct: '27.2%', color: 'bg-emerald-500' },
                { label: 'Level 2', count: 524, pct: '23.3%', color: 'bg-orange-500' },
                { label: 'Level 1', count: 270, pct: '12.0%', color: 'bg-amber-400' },
              ]" :key="i" class="flex items-center justify-between text-[12px]">
                <div class="flex items-center gap-2"><div :class="[item.color, 'w-2 h-2 rounded-full']"></div><span class="font-bold text-slate-700">{{ item.label }}</span></div>
                <span class="text-slate-500"><span class="font-bold text-slate-700">{{ item.count }}</span> ({{ item.pct }})</span>
              </div>
            </div>
          </div>

          <!-- Top Departments -->
          <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-[14px] font-bold text-slate-800 mb-4">Top Departments</h3>
            <div class="space-y-3">
              <div v-for="(dept, i) in [
                { name: 'Computer Science', count: 486 },
                { name: 'Software Engineering', count: 622 },
                { name: 'Information Systems', count: 418 },
                { name: 'ICT', count: 352 },
                { name: 'Database Systems', count: 370 },
              ]" :key="i" class="flex items-center justify-between text-[12px]">
                <div class="flex items-center gap-2 text-slate-600">
                  <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                  <span>{{ dept.name }}</span>
                </div>
                <span class="font-bold text-slate-800 bg-slate-50 px-2 py-0.5 rounded">{{ dept.count }}</span>
              </div>
            </div>
            <button class="w-full text-center text-[11px] font-bold text-[#4338ca] hover:text-indigo-800 mt-4 transition-colors">View All</button>
          </div>

          <!-- Recent Registrations -->
          <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-[14px] font-bold text-slate-800 mb-4">Recent Registrations</h3>
            <div class="space-y-4">
              <div v-for="(s, i) in (allStudents.slice(0, 3))" :key="i" class="flex items-center gap-3">
                <div :class="[avatarColor(s.id || i), 'w-9 h-9 rounded-full flex items-center justify-center text-[11px] font-bold text-white shrink-0']">{{ s.avatar }}</div>
                <div class="flex-1 min-w-0">
                  <p class="text-[12px] font-bold text-slate-800 truncate">{{ s.name }}</p>
                  <p class="text-[10px] text-slate-400 mt-0.5">{{ s.departmentName }} · {{ s.registeredOn }}</p>
                </div>
              </div>
              <div v-if="allStudents.length === 0" class="text-[12px] text-slate-400 text-center py-2">No students yet.</div>
            </div>
            <button class="w-full text-center text-[11px] font-bold text-[#4338ca] hover:text-indigo-800 mt-4 transition-colors">View All</button>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-[14px] font-bold text-slate-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
              <button @click="showAddModal = true" class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50 transition-colors text-[#4338ca]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span class="text-[10px] font-bold text-center">Add New<br>Student</span>
              </button>
              <button @click="triggerImport" class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50 transition-colors text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span class="text-[10px] font-bold text-center">Import<br>Students</span>
              </button>
              <button class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50 transition-colors text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                <span class="text-[10px] font-bold text-center">Export<br>Students</span>
              </button>
              <button class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50 transition-colors text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                <span class="text-[10px] font-bold text-center">Manage<br>Levels</span>
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Add Form View -->
    <template v-if="showAddModal && !viewingStudent">
      <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            </div>
            <div>
              <h1 class="text-[22px] font-bold text-slate-800">Add New Student</h1>
              <p class="text-[13px] text-slate-500 mt-0.5">Create a new student account and set their information.</p>
              <div class="flex items-center gap-2 mt-2 text-[12px] text-slate-400">
                <span class="hover:text-[#4338ca] cursor-pointer transition-colors" @click="showAddModal = false">Students</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="font-semibold text-slate-600">Add New Student</span>
              </div>
            </div>
          </div>
          <button @click="showAddModal = false" class="flex items-center gap-2 px-4 py-2 border border-slate-200 text-slate-600 font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm bg-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Students
          </button>
        </div>

        <div class="grid grid-cols-[1fr_320px] gap-6 items-start">
          <!-- Left Column (Form) -->
          <div class="space-y-6">
            
            <!-- Personal Information -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
              <h3 class="text-[15px] font-bold text-slate-800 mb-6">Personal Information</h3>
              <div class="grid grid-cols-3 gap-6">
                <!-- Row 1 -->
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Full Name <span class="text-rose-500">*</span></label>
                  <input type="text" v-model="newStudentForm.fullName" placeholder="Enter full name" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400">
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Email Address <span class="text-rose-500">*</span></label>
                  <input type="email" v-model="newStudentForm.email" placeholder="Enter email address" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400">
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Phone Number <span class="text-rose-500">*</span></label>
                  <input type="text" v-model="newStudentForm.phone" placeholder="Enter phone number" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400">
                </div>
                
                <!-- Row 2 -->
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Date of Birth <span class="text-rose-500">*</span></label>
                  <div class="relative">
                    <input v-model="newStudentForm.dob" type="date" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400 bg-white">
                  </div>
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Gender <span class="text-rose-500">*</span></label>
                  <div class="relative">
                    <select v-model="newStudentForm.gender" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]">
                      <option value="" disabled selected>Select gender</option>
                      <option value="male">Male</option>
                      <option value="female">Female</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                  </div>
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Profile Picture</label>
                  <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center hover:border-[#4338ca] hover:bg-indigo-50/50 cursor-pointer transition-colors h-[72px] flex flex-col justify-center items-center">
                    <div class="flex items-center gap-2 text-slate-600 mb-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                      <span class="text-[12px] font-bold">Upload Photo</span>
                    </div>
                    <p class="text-[10px] text-slate-400">PNG, JPG up to 2MB</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Academic Information -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
              <h3 class="text-[15px] font-bold text-slate-800 mb-6">Academic Information</h3>
              <div class="grid grid-cols-3 gap-6">
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Student ID <span class="text-rose-500">*</span></label>
                  <input type="text" v-model="newStudentForm.studentId" placeholder="Enter student ID" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400">
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Admission Number <span class="text-rose-500">*</span></label>
                  <input type="text" v-model="newStudentForm.admissionNumber" placeholder="Enter admission no." class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400">
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Department <span class="text-rose-500">*</span></label>
                  <div class="relative">
                    <select v-model="newStudentForm.department_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]">
                      <option value="" disabled selected>Select department</option>
                      <option v-for="d in allDepartments" :key="d.id" :value="d.id">{{ d.name }}</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                  </div>
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Year <span class="text-rose-500">*</span></label>
                  <div class="relative">
                    <select v-model="newStudentForm.year" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]">
                      <option value="" disabled selected>Select year</option>
                      <option value="year_1">Year 1</option>
                      <option value="year_2">Year 2</option>
                      <option value="year_3">Year 3</option>
                      <option value="year_4">Year 4</option>
                      <option value="year_5">Year 5</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                  </div>
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Semester <span class="text-rose-500">*</span></label>
                  <div class="relative">
                    <select v-model="newStudentForm.semester" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]">
                      <option value="" disabled selected>Select semester</option>
                      <option value="semester_1">Semester 1</option>
                      <option value="semester_2">Semester 2</option>
                      <option value="summer">Summer</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                  </div>
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Section <span class="text-rose-500">*</span></label>
                  <input type="text" v-model="newStudentForm.section" placeholder="Enter section" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400">
                </div>
              </div>
            </div>

            <!-- Save Action Button -->
            <div class="flex items-center justify-end">
              <button @click="addStudent" :disabled="isLoading" class="px-6 py-2.5 text-[13px] font-bold text-white bg-[#4338ca] disabled:opacity-70 disabled:cursor-not-allowed hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-200 transition-colors">
                Save Student Record
              </button>
            </div>
            
          </div>

          <!-- Right Column (Sidebars) -->
          <div class="space-y-6">
            
            <!-- Account Summary -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
              <h3 class="text-[14px] font-bold text-slate-800 mb-6">Account Summary</h3>
              
              <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-indigo-50 text-[#4338ca] rounded-xl flex items-center justify-center shrink-0">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <p class="text-[14px] font-bold text-slate-800">New Student</p>
              </div>

              <div class="space-y-4">
                <div class="flex items-center justify-between text-[13px]">
                  <div class="flex items-center gap-2 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Role</span>
                  </div>
                  <span class="font-semibold text-slate-700">Student</span>
                </div>
                
                <div class="flex items-center justify-between text-[13px]">
                  <div class="flex items-center gap-2 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>Access Level</span>
                  </div>
                  <span class="font-bold text-[#4338ca]">Standard Student</span>
                </div>
                
                <div class="flex items-center justify-between text-[13px]">
                  <div class="flex items-center gap-2 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span>Department</span>
                  </div>
                  <span class="font-semibold text-slate-400">Not Selected</span>
                </div>
                
                <div class="flex items-center justify-between text-[13px]">
                  <div class="flex items-center gap-2 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Status</span>
                  </div>
                  <span class="font-bold text-emerald-600">Active</span>
                </div>
                
                <div class="flex items-center justify-between text-[13px]">
                  <div class="flex items-center gap-2 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>Permissions</span>
                  </div>
                  <span class="font-semibold text-slate-700">Default Access</span>
                </div>
              </div>
            </div>

            <!-- Password Requirements -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
              <h3 class="text-[14px] font-bold text-slate-800 mb-4">Password Requirements</h3>
              <div class="space-y-3">
                <div class="flex items-center gap-3 text-[12px] font-bold text-emerald-600">
                  <div class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                  </div>
                  <span>At least 8 characters long</span>
                </div>
                <div class="flex items-center gap-3 text-[12px] font-bold text-emerald-600">
                  <div class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                  </div>
                  <span>Include uppercase letter</span>
                </div>
                <div class="flex items-center gap-3 text-[12px] font-bold text-emerald-600">
                  <div class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                  </div>
                  <span>Include a number</span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </template>

    <!-- Edit Form View -->
    <template v-if="showEditModal && !viewingStudent">
      <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </div>
            <div>
              <h1 class="text-[22px] font-bold text-slate-800">Edit Student</h1>
              <p class="text-[13px] text-slate-500 mt-0.5">Update the student's information and academic details.</p>
              <div class="flex items-center gap-2 mt-2 text-[12px] text-slate-400">
                <span class="hover:text-[#4338ca] cursor-pointer transition-colors" @click="showEditModal = false; editingStudent = null">Students</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="font-semibold text-slate-600">Edit Student</span>
              </div>
            </div>
          </div>
          <button @click="showEditModal = false; editingStudent = null" class="flex items-center gap-2 px-4 py-2 border border-slate-200 text-slate-600 font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm bg-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Students
          </button>
        </div>

        <div class="grid grid-cols-[1fr_320px] gap-6 items-start">
          <!-- Left Column (Form) -->
          <div class="space-y-6">

            <!-- Personal Information -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
              <h3 class="text-[15px] font-bold text-slate-800 mb-6">Personal Information</h3>
              <div class="grid grid-cols-3 gap-6">
                <!-- Row 1 -->
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Full Name <span class="text-rose-500">*</span></label>
                  <input type="text" v-model="editStudentForm.fullName" placeholder="Enter full name" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400">
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Email Address <span class="text-rose-500">*</span></label>
                  <input type="email" v-model="editStudentForm.email" placeholder="Enter email address" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400">
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Phone Number <span class="text-rose-500">*</span></label>
                  <input type="text" v-model="editStudentForm.phone" placeholder="Enter phone number" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400">
                </div>

                <!-- Row 2 -->
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Date of Birth <span class="text-rose-500">*</span></label>
                  <div class="relative">
                    <input v-model="editStudentForm.dob" type="date" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400 bg-white">
                  </div>
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Gender <span class="text-rose-500">*</span></label>
                  <div class="relative">
                    <select v-model="editStudentForm.gender" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]">
                      <option value="" disabled>Select gender</option>
                      <option value="male">Male</option>
                      <option value="female">Female</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                  </div>
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Profile Picture</label>
                  <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center hover:border-[#4338ca] hover:bg-indigo-50/50 cursor-pointer transition-colors h-[72px] flex flex-col justify-center items-center">
                    <div class="flex items-center gap-2 text-slate-600 mb-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                      <span class="text-[12px] font-bold">Upload Photo</span>
                    </div>
                    <p class="text-[10px] text-slate-400">PNG, JPG up to 2MB</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Academic Information -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
              <h3 class="text-[15px] font-bold text-slate-800 mb-6">Academic Information</h3>
              <div class="grid grid-cols-3 gap-6">
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Student ID <span class="text-rose-500">*</span></label>
                  <input type="text" v-model="editStudentForm.studentId" placeholder="Enter student ID" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400">
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Admission Number <span class="text-rose-500">*</span></label>
                  <input type="text" v-model="editStudentForm.admissionNumber" placeholder="Enter admission no." class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400">
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Department <span class="text-rose-500">*</span></label>
                  <div class="relative">
                    <select v-model="editStudentForm.department_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]">
                      <option value="" disabled>Select department</option>
                      <option v-for="d in allDepartments" :key="d.id" :value="d.id">{{ d.name }}</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                  </div>
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Year <span class="text-rose-500">*</span></label>
                  <div class="relative">
                    <select v-model="editStudentForm.year" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]">
                      <option value="" disabled>Select year</option>
                      <option value="year_1">Year 1</option>
                      <option value="year_2">Year 2</option>
                      <option value="year_3">Year 3</option>
                      <option value="year_4">Year 4</option>
                      <option value="year_5">Year 5</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                  </div>
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Semester <span class="text-rose-500">*</span></label>
                  <div class="relative">
                    <select v-model="editStudentForm.semester" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]">
                      <option value="" disabled>Select semester</option>
                      <option value="semester_1">Semester 1</option>
                      <option value="semester_2">Semester 2</option>
                      <option value="summer">Summer</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                  </div>
                </div>
                <div>
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Section <span class="text-rose-500">*</span></label>
                  <input type="text" v-model="editStudentForm.section" placeholder="Enter section" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400">
                </div>
              </div>
            </div>

            <!-- Update Action Buttons -->
            <div class="flex items-center justify-end gap-3">
              <button @click="showEditModal = false; editingStudent = null" class="px-6 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                Cancel
              </button>
              <button @click="saveEditStudent" :disabled="isLoading" class="px-6 py-2.5 text-[13px] font-bold text-white bg-[#4338ca] disabled:opacity-70 disabled:cursor-not-allowed hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-200 transition-colors">
                Update Student Record
              </button>
            </div>

          </div>

          <!-- Right Column (Sidebars) -->
          <div class="space-y-6">

            <!-- Account Summary -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
              <h3 class="text-[14px] font-bold text-slate-800 mb-6">Account Summary</h3>

              <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-indigo-50 text-[#4338ca] rounded-xl flex items-center justify-center shrink-0">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <p class="text-[14px] font-bold text-slate-800">{{ editStudentForm.fullName || 'Student' }}</p>
              </div>

              <div class="space-y-4">
                <div class="flex items-center justify-between text-[13px]">
                  <div class="flex items-center gap-2 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Role</span>
                  </div>
                  <span class="font-semibold text-slate-700">Student</span>
                </div>

                <div class="flex items-center justify-between text-[13px]">
                  <div class="flex items-center gap-2 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>Access Level</span>
                  </div>
                  <span class="font-bold text-[#4338ca]">Standard Student</span>
                </div>

                <div class="flex items-center justify-between text-[13px]">
                  <div class="flex items-center gap-2 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Status</span>
                  </div>
                  <span :class="editStudentForm.status === 'active' || editStudentForm.status === 'Active' ? 'font-bold text-emerald-600' : 'font-bold text-rose-500'" class="capitalize">{{ editStudentForm.status }}</span>
                </div>

                <div class="flex items-center justify-between text-[13px]">
                  <div class="flex items-center gap-2 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>Permissions</span>
                  </div>
                  <span class="font-semibold text-slate-700">Default Access</span>
                </div>
              </div>
            </div>

            <!-- Important Notice -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 shadow-sm">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div>
                  <h4 class="text-[13px] font-bold text-amber-800 mb-1">Important Notice</h4>
                  <p class="text-[12px] text-amber-700 leading-relaxed">Changes to student information will be reflected immediately across the system. Please verify all details before saving.</p>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </template>
    </div>

  <!-- Student Detail View -->
  <div v-else class="space-y-6 pb-12">
    <!-- Breadcrumbs & Header -->
    <div class="flex flex-col gap-4 mb-6">
      <div class="flex items-center gap-2 text-[13px] font-medium text-slate-500">
        <button @click="viewingStudent = null" class="hover:text-indigo-600 transition-colors">Students</button>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <button @click="viewingStudent = null" class="hover:text-indigo-600 transition-colors">Student List</button>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span class="text-slate-700 font-semibold cursor-default">Student Details</span>
      </div>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-[24px] font-bold text-slate-800">Student Details</h1>
          <p class="text-[14px] text-slate-500 mt-1">View complete information about the student.</p>
        </div>
        <button @click="viewingStudent = null" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 font-semibold rounded-lg text-[13px] hover:bg-slate-50 transition-colors shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Student List
        </button>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-[1fr_320px] gap-6 items-start">
      
      <!-- Left Column -->
      <div class="space-y-6 min-w-0">
        
        <!-- Top Profile Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-8 shadow-sm flex flex-col lg:flex-row items-center lg:items-start gap-8">
          <!-- Profile Info (Left side of card) -->
          <div class="flex flex-col items-center gap-4 shrink-0">
            <img src="https://ui-avatars.com/api/?name=Kalkidan+Mengistu&background=e0e7ff&color=4338ca&size=120" alt="Avatar" class="w-32 h-32 rounded-full object-cover border-4 border-slate-50 shadow-sm" />
          </div>
          <div class="flex-1 space-y-4 text-center lg:text-left">
            <h2 class="text-[22px] font-bold text-slate-800">{{ viewingStudent?.name || 'Kalkidan Mengistu' }}</h2>
            <div class="flex items-center justify-center lg:justify-start gap-3">
              <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100">Active</span>
              <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-indigo-50 text-[#4338ca] border border-indigo-100">Student ID: WU/00119/25</span>
            </div>
            <div class="space-y-2 mt-4 text-[13px] text-slate-600 inline-block text-left">
              <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <span>{{ viewingStudent?.email || 'kalkidan.mengistu@wu.edu.et' }}</span>
              </div>
              <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                <span>{{ viewingStudent?.phone || '+251 91 234 5678' }}</span>
              </div>
              <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Wollo University, Dessie, Ethiopia</span>
              </div>
            </div>
          </div>

          <!-- Right Side Data Columns -->
          <div class="hidden lg:flex gap-12 border-l border-slate-100 pl-8 w-max shrink-0">
            <div class="space-y-4 w-[160px]">
              <div>
                <p class="text-[12px] font-medium text-slate-400 mb-1">Role</p>
                <p class="text-[13px] font-semibold text-slate-800">Student</p>
              </div>
              <div>
                <p class="text-[12px] font-medium text-slate-400 mb-1">Access Level</p>
                <p class="text-[13px] font-bold text-[#4338ca]">Standard Student</p>
              </div>
              <div>
                <p class="text-[12px] font-medium text-slate-400 mb-1">Department</p>
                <p class="text-[13px] font-semibold text-slate-800">Not Assigned</p>
              </div>
              <div>
                <p class="text-[12px] font-medium text-slate-400 mb-1">Section</p>
                <p class="text-[13px] font-semibold text-slate-800">Section A</p>
              </div>
              <div>
                <p class="text-[12px] font-medium text-slate-400 mb-1">Academic Year</p>
                <p class="text-[13px] font-semibold text-slate-800">{{ viewingStudent?.level || '3rd Year' }}</p>
              </div>
            </div>
            <div class="space-y-4 w-[140px]">
              <div>
                <p class="text-[12px] font-medium text-slate-400 mb-1">Admission Number</p>
                <p class="text-[13px] font-bold text-[#4338ca]">ADM/2023/5678</p>
              </div>
              <div>
                <p class="text-[12px] font-medium text-slate-400 mb-1">Admission Date</p>
                <p class="text-[13px] font-semibold text-slate-800">Sep 15, 2023</p>
              </div>
              <div>
                <p class="text-[12px] font-medium text-slate-400 mb-1">Status</p>
                <span class="px-2.5 py-0.5 inline-block text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-600">Active</span>
              </div>
              <div>
                <p class="text-[12px] font-medium text-slate-400 mb-1">Registration Date</p>
                <p class="text-[13px] font-semibold text-slate-800">Oct 01, 2023</p>
              </div>
              <div>
                <p class="text-[12px] font-medium text-slate-400 mb-1">Last Updated</p>
                <p class="text-[13px] font-semibold text-slate-800">May 10, 2025</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Personal Information Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm relative">
          <h3 class="text-[15px] font-bold text-slate-800 mb-6">Personal Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <p class="text-[12px] font-medium text-slate-400 mb-1">Full Name</p>
              <p class="text-[13px] font-semibold text-slate-800">{{ viewingStudent?.name || 'Kalkidan Mengistu' }}</p>
            </div>
            <div>
              <p class="text-[12px] font-medium text-slate-400 mb-1">Email Address</p>
              <p class="text-[13px] font-semibold text-[#4338ca]">{{ viewingStudent?.email || 'kalkidan.mengistu@wu.edu.et' }}</p>
            </div>
            <div>
              <p class="text-[12px] font-medium text-slate-400 mb-1">Phone Number</p>
              <p class="text-[13px] font-semibold text-slate-800">{{ viewingStudent?.phone || '+251 91 234 5678' }}</p>
            </div>
            <div>
              <p class="text-[12px] font-medium text-slate-400 mb-1">Date of Birth</p>
              <p class="text-[13px] font-semibold text-slate-800">May 10, 2003</p>
            </div>
            <div>
              <p class="text-[12px] font-medium text-slate-400 mb-1">Gender</p>
              <p class="text-[13px] font-semibold text-slate-800">Female</p>
            </div>
          </div>
          <!-- Profile Picture Thumbnail -->
          <div class="absolute bottom-6 right-6 hidden md:flex flex-col items-center">
            <p class="text-[12px] font-medium text-slate-400 mb-2">Profile Picture</p>
            <img src="https://ui-avatars.com/api/?name=Kalkidan+Mengistu&background=e0e7ff&color=4338ca&size=64" alt="Avatar Thumbnail" class="w-16 h-16 rounded-xl object-cover shadow-sm" />
          </div>
        </div>

        <!-- Academic Information Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-[15px] font-bold text-slate-800 mb-6">Academic Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <p class="text-[12px] font-medium text-slate-400 mb-1">Student ID</p>
              <p class="text-[13px] font-semibold text-slate-800">WU/00119/25</p>
            </div>
            <div>
              <p class="text-[12px] font-medium text-slate-400 mb-1">Admission Number</p>
              <p class="text-[13px] font-semibold text-slate-800">ADM/2023/5678</p>
            </div>
            <div>
              <p class="text-[12px] font-medium text-slate-400 mb-1">Enrollment Date</p>
              <p class="text-[13px] font-semibold text-slate-800">May 10, 2025</p>
            </div>
            <div>
              <p class="text-[12px] font-medium text-slate-400 mb-1">Academic Year</p>
              <p class="text-[13px] font-semibold text-slate-800">{{ viewingStudent?.level || '3rd Year' }}</p>
            </div>
            <div>
              <p class="text-[12px] font-medium text-slate-400 mb-1">Semester</p>
              <p class="text-[13px] font-semibold text-slate-800">1st Semester (2025/2026)</p>
            </div>
            <div>
              <p class="text-[12px] font-medium text-slate-400 mb-1">Section</p>
              <p class="text-[13px] font-semibold text-slate-800">Section A</p>
            </div>
            <div>
              <p class="text-[12px] font-medium text-slate-400 mb-1">Department</p>
              <p class="text-[13px] font-semibold text-slate-800">Not Assigned</p>
            </div>
            <div>
              <p class="text-[12px] font-medium text-slate-400 mb-1">Credit Hours Earned</p>
              <p class="text-[13px] font-semibold text-slate-800">12</p>
            </div>
            <div>
              <p class="text-[12px] font-medium text-slate-400 mb-1">CGPA</p>
              <p class="text-[13px] font-semibold text-slate-800">3.25 / 4.00</p>
            </div>
          </div>
        </div>
        
      </div>

      <!-- Right Sidebar -->
      <div class="space-y-6">
        
        <!-- Account Summary -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-[15px] font-bold text-slate-800 mb-5">Account Summary</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span class="text-[13px] text-slate-500">Role</span>
              </div>
              <span class="text-[13px] font-semibold text-slate-800">Student</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <span class="text-[13px] text-slate-500">Access Level</span>
              </div>
              <span class="text-[13px] font-bold text-[#4338ca]">Standard Student</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-[13px] text-slate-500">Status</span>
              </div>
              <span class="text-[13px] font-bold text-emerald-600">Active</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span class="text-[13px] text-slate-500">Permissions</span>
              </div>
              <span class="text-[13px] font-semibold text-slate-800">Default Access</span>
            </div>
          </div>
        </div>

        <!-- Academic Summary -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-[15px] font-bold text-slate-800 mb-5">Academic Summary</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="text-[13px] text-slate-500">Total Enrolled Courses</span>
              </div>
              <span class="text-[13px] font-bold text-slate-800">6</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-[13px] text-slate-500">Completed Courses</span>
              </div>
              <span class="text-[13px] font-bold text-slate-800">0</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-[13px] text-slate-500">Current GPA</span>
              </div>
              <span class="text-[13px] font-bold text-slate-800">3.25 / 4.00</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-[13px] text-slate-500">Attendance Rate</span>
              </div>
              <span class="text-[13px] font-bold text-slate-800">92%</span>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-[15px] font-bold text-slate-800 mb-5">Quick Actions</h3>
          <div class="space-y-3">
            <button class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-white border border-slate-200 rounded-lg text-[13px] font-semibold text-[#4338ca] hover:bg-indigo-50 hover:border-indigo-100 transition-colors shadow-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg> Edit Student
            </button>
            <button class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-white border border-slate-200 rounded-lg text-[13px] font-semibold text-[#4338ca] hover:bg-indigo-50 hover:border-indigo-100 transition-colors shadow-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> Reset Password
            </button>
            <button class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-white border border-slate-200 rounded-lg text-[13px] font-semibold text-[#4338ca] hover:bg-indigo-50 hover:border-indigo-100 transition-colors shadow-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg> Deactivate Student
            </button>
            <button class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-white border border-slate-200 rounded-lg text-[13px] font-semibold text-[#4338ca] hover:bg-indigo-50 hover:border-indigo-100 transition-colors shadow-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Download Profile
            </button>
          </div>
        </div>
        
      </div>
    </div>
  </div>
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
            <div><h3 class="text-[16px] font-bold text-slate-800">Get Student</h3><p class="text-[12px] text-slate-500 mt-0.5">Find a specific student.</p></div>
            <button @click="showGetStudentModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
          </div>
          <div class="px-6 py-5 space-y-4">
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Student ID</label>
              <input v-model="getStudentQuery.id_no" @keyup.enter="handleGetStudent" type="text" placeholder="WU/XXXX/YY" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Year Level</label>
              <select v-model="getStudentQuery.year" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] bg-white">
                <option value="all">Any Year</option>
                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
              </select>
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Semester</label>
              <select v-model="getStudentQuery.semester" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] bg-white">
                <option value="all">Any Semester</option>
                <option v-for="s in semesters" :key="s" :value="s">{{ s }}</option>
              </select>
            </div>
          </div>
          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            <button @click="showGetStudentModal = false" class="px-5 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">Cancel</button>
            <button @click="handleGetStudent" class="px-5 py-2.5 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-200 transition-all">Filter</button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>