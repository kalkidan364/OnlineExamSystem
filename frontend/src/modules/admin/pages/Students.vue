<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import apiClient from '../../../core/api/apiClient'

const search = ref('')
const yearFilter = ref('all')
const academicYearFilter = ref('all')
const semesterFilter = ref('all')
const statusFilter = ref('all')
const currentPage = ref(1)
const perPage = 8
const showAddModal = ref(false)
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

const fetchStudents = async () => {
  try {
    const res = await apiClient.get('/admin/users?role=student')
    allStudents.value = (res.data.data || []).map((u: any) => ({
      ...u,
      avatar: u.name ? u.name.split(' ').map((w: string) => w[0]).join('').toUpperCase().slice(0, 2) : '??',
      status: 'active',
      enrolled: new Date(u.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
      departmentName: u.department?.name || '—',
      exams: 0,
      avg: 0,
      year: u.year_level || 'Year 1',
      academic_year: u.academic_year || '—',
      semester: u.semester || '—',
      id_no: u.id_no || `WU/${u.id.toString().padStart(4, '0')}/26`
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
    return matchSearch && matchYear && matchAcYear && matchSem && matchStatus
  })
)

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))
const paginated  = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const stats = computed(() => ({
  total:     allStudents.value.length,
  active:    allStudents.value.filter(s => s.status === 'active').length,
  avgScore:  allStudents.value.length ? Math.round(allStudents.value.reduce((a, s) => a + (s.avg || 0), 0) / allStudents.value.length) : 0,
  exams:     allStudents.value.reduce((a, s) => a + (s.exams || 0), 0),
}))

const avatarColor = (id: number) => {
  const c = ['bg-indigo-500','bg-sky-500','bg-emerald-500','bg-violet-500','bg-amber-500','bg-rose-500','bg-teal-500','bg-orange-500','bg-cyan-500','bg-purple-500']
  return c[id % c.length]
}

const statusBadge  = (s: string) => ({ active:'bg-emerald-50 text-emerald-700', inactive:'bg-slate-100 text-slate-500', suspended:'bg-rose-50 text-rose-600' }[s] || 'bg-slate-100 text-slate-600')
const statusDot    = (s: string) => ({ active:'bg-emerald-500', inactive:'bg-slate-400', suspended:'bg-rose-500' }[s] || 'bg-slate-400')
const scoreColor   = (n: number) => n >= 80 ? 'text-emerald-600' : n >= 60 ? 'text-amber-600' : 'text-rose-600'
const scoreBg      = (n: number) => n >= 80 ? 'bg-emerald-500' : n >= 60 ? 'bg-amber-500' : 'bg-rose-500'


const importFileInput = ref<HTMLInputElement | null>(null)
const triggerImport = () => { if (importFileInput.value) importFileInput.value.click() }
const handleImport = async (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (file) {
    isLoading.value = true
    try {
      // Mock import delay
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

// Form state matching image 2
const newStudentForm = ref({
  fullName: '',
  email: '',
  phone: '',
  dob: '',
  gender: '',
  studentId: '',
  admissionNumber: '',
  program: '',
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
      department_id: newStudentForm.value.program || null,
      id_no: newStudentForm.value.studentId,
      password: 'DefaultPassword123!',
      year_level: 'Year 1',
      academic_year: '2025/2026',
      semester: '1st Semester'
    })
    await fetchStudents()
    showAddModal.value = false
    showSuccessModal.value = true
    setTimeout(() => { showSuccessModal.value = false }, 3000)
    // Clear form
    newStudentForm.value = { fullName: '', email: '', phone: '', dob: '', gender: '', studentId: '', admissionNumber: '', program: '', role: 'Student', accessLevel: 'Standard Student', status: 'Active', permissions: 'Default Access' }
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
console.log(newStudentForm, totalPages, paginated, stats, avatarColor, statusBadge, statusDot, scoreColor, scoreBg, addStudent, confirmDelete, deleteStudent, handleGetStudent, toggleStatus, activeTab, viewingCourse, activeCourseTab, creatingAnnouncement, years, academicYears, semesters, newStudent, viewStudent)
</script>

<template>
  <div v-if="!viewingStudent" class="space-y-6 pb-12">
        <!-- List View -->
    <div v-if="!showAddModal" class="space-y-6">
      
      <!-- Page Header -->
      <div class="flex items-center justify-between">
        <div class="flex items-start gap-4">
          <div class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
          </div>
          <div>
            <h1 class="text-[24px] font-bold text-slate-800">Students</h1>
            <p class="text-[13px] text-slate-500 mt-0.5">Manage all students and their information across the system.</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <div class="relative">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input v-model="search" type="text" placeholder="Search Students..." class="w-56 border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-[13px] font-medium text-slate-600 focus:outline-none focus:border-[#4338ca] bg-white">
          </div>
          <button class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-[#4338ca] font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg> Filter
          </button>
          
          <input type="file" ref="importFileInput" class="hidden" accept=".csv,.xlsx,.xls" @change="handleImport">
          
          <button @click="triggerImport" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-[#4338ca] font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Export Students
          </button>
          <button @click="showAddModal = true" class="flex items-center gap-2 px-4 py-2.5 bg-[#4338ca] text-white font-bold rounded-xl text-[13px] hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Add New Student
          </button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-5 gap-5">
        <!-- Total -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 bg-indigo-50 text-[#4338ca] rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
          </div>
          <div>
            <p class="text-[12px] font-bold text-slate-500 mb-0.5">Total Students</p>
            <div class="flex items-end gap-2">
              <h3 class="text-[22px] font-black text-slate-800 leading-none">{{ stats.total }}</h3>
            </div>
            <div class="flex items-center gap-1 mt-1.5 text-[11px] font-bold text-emerald-500">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
              <span>14.7%</span>
              <span class="text-slate-400 font-medium ml-1">All registered students</span>
            </div>
          </div>
        </div>
        
        <!-- Active -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
          </div>
          <div>
            <p class="text-[12px] font-bold text-slate-500 mb-0.5">Active Students</p>
            <div class="flex items-end gap-2">
              <h3 class="text-[22px] font-black text-slate-800 leading-none">{{ stats.active }}</h3>
            </div>
            <div class="flex items-center gap-1 mt-1.5 text-[11px] font-bold text-emerald-500">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
              <span>13.3%</span>
              <span class="text-slate-400 font-medium ml-1">Active students</span>
            </div>
          </div>
        </div>

        <!-- Inactive -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path></svg>
          </div>
          <div>
            <p class="text-[12px] font-bold text-slate-500 mb-0.5">Inactive Students</p>
            <div class="flex items-end gap-2">
              <h3 class="text-[22px] font-black text-slate-800 leading-none">{{ stats.total - stats.active }}</h3>
            </div>
            <div class="flex items-center gap-1 mt-1.5 text-[11px] font-bold text-rose-500">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
              <span>8.6%</span>
              <span class="text-slate-400 font-medium ml-1">Inactive students</span>
            </div>
          </div>
        </div>

        <!-- New -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 bg-sky-50 text-sky-500 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
          </div>
          <div>
            <p class="text-[12px] font-bold text-slate-500 mb-0.5">New Students</p>
            <div class="flex items-end gap-2">
              <h3 class="text-[22px] font-black text-slate-800 leading-none">{{ Math.round(stats.total * 0.1) }}</h3>
            </div>
            <div class="flex items-center gap-1 mt-1.5 text-[11px] font-bold text-emerald-500">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
              <span>16.2%</span>
              <span class="text-slate-400 font-medium ml-1">This month</span>
            </div>
          </div>
        </div>
        
        <!-- Verified -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <div>
            <p class="text-[12px] font-bold text-slate-500 mb-0.5">Verified Students</p>
            <div class="flex items-end gap-2">
              <h3 class="text-[22px] font-black text-slate-800 leading-none">{{ stats.active }}</h3>
            </div>
            <div class="flex items-center gap-1 mt-1.5 text-[11px] font-bold text-emerald-500">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
              <span>15.0%</span>
              <span class="text-slate-400 font-medium ml-1">Verified accounts</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Layout: Table on Left, Overview on Right -->
      <div class="grid grid-cols-[1fr_320px] gap-6">
        
        <!-- Left Column: Table & Filters -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
          
          <!-- Filters Row -->
          <div class="flex items-center gap-3 p-4 border-b border-slate-100">
            <button class="w-10 h-10 border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-[#4338ca] hover:border-[#4338ca] transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
            <div class="relative">
              <select class="appearance-none pl-4 pr-10 py-2 border border-slate-200 rounded-xl text-[13px] text-slate-600 font-medium focus:outline-none focus:border-[#4338ca] w-[180px]">
                <option value="all">All Departments</option>
              </select>
              <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            <div class="relative">
              <select class="appearance-none pl-4 pr-10 py-2 border border-slate-200 rounded-xl text-[13px] text-slate-600 font-medium focus:outline-none focus:border-[#4338ca] w-[140px]">
                <option value="all">All Status</option>
              </select>
              <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            <div class="relative">
              <select class="appearance-none pl-4 pr-10 py-2 border border-slate-200 rounded-xl text-[13px] text-slate-600 font-medium focus:outline-none focus:border-[#4338ca] w-[140px]">
                <option value="all">All Levels</option>
              </select>
              <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            <div class="relative">
              <input type="text" placeholder="Registration Date" class="pl-4 pr-10 py-2 border border-slate-200 rounded-xl text-[13px] text-slate-600 font-medium focus:outline-none focus:border-[#4338ca] w-[160px]">
              <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <button class="flex items-center gap-2 px-4 py-2 border border-slate-200 text-[#4338ca] font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm ml-auto">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg> Filter
            </button>
          </div>

          <!-- Table -->
          <div class="flex-1 overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-slate-100 text-left">
                  <th class="py-4 px-6 text-[10px] font-black text-slate-400 uppercase tracking-wider">Student</th>
                  <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Email</th>
                  <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Department</th>
                  <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Level</th>
                  <th class="py-4 px-4 text-[10px] font-black text-slate-400 uppercase tracking-wider">Phone</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(student, index) in paginated" :key="student.id || index" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors cursor-pointer" @click="viewStudent(student.name, student.email, student.departmentName, student.year, student.phone || '+251 91 123 4567', 'Active', '10/01/2023', 'Today, 10:30 AM')">
                  <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                      <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-[11px] font-bold text-slate-600">{{ student.avatar }}</div>
                      <span class="text-[13px] font-bold text-slate-800">{{ student.name }}</span>
                    </div>
                  </td>
                  <td class="py-4 px-4 text-[13px] text-slate-500 font-medium">{{ student.email }}</td>
                  <td class="py-4 px-4 text-[13px] text-slate-600 font-medium">{{ student.departmentName }}</td>
                  <td class="py-4 px-4 text-[13px] font-bold text-[#4338ca]">{{ student.year }}</td>
                  <td class="py-4 px-4 text-[13px] text-slate-500 font-medium">{{ student.phone || '+251 91 123 4567' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div class="flex items-center justify-between p-4 border-t border-slate-100 bg-slate-50/30">
            <p class="text-[12px] font-medium text-slate-500">Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, filtered.length) }} of {{ filtered.length }} entries</p>
            <div class="flex gap-1">
              <button class="w-8 h-8 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-600 bg-white"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
              <button class="w-8 h-8 rounded border border-[#4338ca] flex items-center justify-center text-white bg-[#4338ca] text-[13px] font-bold">1</button>
              <button class="w-8 h-8 rounded border border-slate-200 flex items-center justify-center text-slate-600 bg-white text-[13px] font-bold">2</button>
              <button class="w-8 h-8 rounded border border-slate-200 flex items-center justify-center text-slate-600 bg-white text-[13px] font-bold">3</button>
              <button class="w-8 h-8 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-600 bg-white"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
            </div>
          </div>
        </div>

        <!-- Right Column: Student Overview (Pie Chart) -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 flex flex-col">
          <h3 class="text-[15px] font-bold text-slate-800 mb-6">Student Overview</h3>
          
          <div class="relative flex-1 flex flex-col items-center justify-center min-h-[220px]">
            <!-- Fake SVG Donut Chart matching the screenshot colors -->
            <svg viewBox="0 0 36 36" class="w-48 h-48 drop-shadow-sm">
              <path class="text-orange-500" stroke-dasharray="23.3, 100" stroke-width="6" stroke="currentColor" fill="none" stroke-linecap="round" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
              <path class="text-emerald-500" stroke-dasharray="27.2, 100" stroke-dashoffset="-23.3" stroke-width="6" stroke="currentColor" fill="none" stroke-linecap="round" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
              <path class="text-purple-600" stroke-dasharray="37.5, 100" stroke-dashoffset="-50.5" stroke-width="6" stroke="currentColor" fill="none" stroke-linecap="round" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
              <path class="text-amber-400" stroke-dasharray="12.0, 100" stroke-dashoffset="-88" stroke-width="6" stroke="currentColor" fill="none" stroke-linecap="round" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
              <h4 class="text-2xl font-black text-slate-800 leading-none">{{ stats.total }}</h4>
              <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Students</p>
            </div>
          </div>
          
          <div class="space-y-4 mt-6">
            <div class="flex items-center justify-between text-[12px]">
              <div class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-purple-600"></div><span class="font-bold text-slate-700">Level 4</span></div>
              <span class="text-slate-500 font-medium"><span class="font-bold text-slate-700">842</span> (37.5%)</span>
            </div>
            <div class="flex items-center justify-between text-[12px]">
              <div class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-emerald-500"></div><span class="font-bold text-slate-700">Level 3</span></div>
              <span class="text-slate-500 font-medium"><span class="font-bold text-slate-700">612</span> (27.2%)</span>
            </div>
            <div class="flex items-center justify-between text-[12px]">
              <div class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-orange-500"></div><span class="font-bold text-slate-700">Level 2</span></div>
              <span class="text-slate-500 font-medium"><span class="font-bold text-slate-700">524</span> (23.3%)</span>
            </div>
            <div class="flex items-center justify-between text-[12px]">
              <div class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-amber-400"></div><span class="font-bold text-slate-700">Level 1</span></div>
              <span class="text-slate-500 font-medium"><span class="font-bold text-slate-700">270</span> (12.0%)</span>
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
                  <label class="block text-[12px] font-bold text-slate-700 mb-2">Program / Major <span class="text-rose-500">*</span></label>
                  <div class="relative">
                    <select v-model="newStudentForm.gender" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]">
                      <option value="" disabled selected>Select program</option>
                      <option value="cs">Computer Science</option>
                      <option value="swe">Software Engineering</option>
                      <option value="is">Information Systems</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                  </div>
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
</template>


  </div>
<!-- Student Detail View -->
  <div v-else class="space-y-6 pb-12">
    <!-- Breadcrumbs & Header -->
    <div class="flex flex-col gap-4 mb-6">
      <div class="flex items-center gap-2 text-[13px] font-medium text-slate-500">
        <button @click="viewingStudent = null" class="hover:text-blue-600 transition-colors">Students</button>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <button @click="activeTab = 'personal'" :class="activeTab === 'personal' ? 'text-slate-700 font-semibold cursor-default' : 'hover:text-blue-600 transition-colors'">Student Details</button>
        <template v-if="activeTab !== 'personal'">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          <span class="text-slate-700 font-semibold">{{ activeTab === 'academic' ? 'Academic Information' : activeTab === 'enrolled' ? 'Enrolled Courses' : activeTab }}</span>
        </template>
      </div>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-[24px] font-bold text-slate-800">{{ activeTab === 'academic' ? 'Academic Information' : activeTab === 'enrolled' ? 'Enrolled Courses' : 'Student Details' }}</h1>
          <p v-if="activeTab === 'academic'" class="text-[14px] text-slate-500 mt-1">Detailed academic profile and progress information</p>
          <p v-if="activeTab === 'enrolled'" class="text-[14px] text-slate-500 mt-1">Courses currently enrolled by the student</p>
        </div>
        <div class="flex items-center gap-3">
          <button v-if="activeTab === 'personal'" @click="viewingStudent = null" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 font-semibold rounded-lg text-[13px] hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Students
          </button>
          <button v-else @click="activeTab = 'personal'" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 font-semibold rounded-lg text-[13px] hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Student Details
          </button>
          <button v-if="activeTab === 'personal'" class="flex items-center gap-2 px-4 py-2 bg-[#4338ca] text-white font-semibold rounded-lg text-[13px] hover:bg-indigo-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg> Edit Student
          </button>
        </div>
      </div>
    </div>

    <!-- Top Profile Card -->
    <!-- Personal View Header -->
    <div v-if="activeTab === 'personal'" class="bg-white border border-slate-200 rounded-xl p-8 shadow-sm flex items-start gap-8">
      <div class="flex flex-col items-center gap-4 shrink-0">
        <img src="https://ui-avatars.com/api/?name=Kalkidan+Mengistu&background=e0e7ff&color=4338ca&size=120" alt="Avatar" class="w-32 h-32 rounded-full object-cover border-4 border-slate-50 shadow-sm" />
        <span class="px-3 py-1 text-[12px] font-bold rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100">Active Student</span>
      </div>
      
      <div class="flex-1 grid grid-cols-3 gap-8">
        <div class="space-y-4">
          <div>
            <h2 class="text-[22px] font-bold text-slate-800">{{ viewingStudent?.name || 'Kalkidan Mengistu' }}</h2>
            <div class="flex items-center gap-2 mt-1">
              <span class="text-[13px] text-slate-500">Student ID: <span class="font-medium text-slate-700">WU/00119/25</span></span>
              <button class="text-slate-400 hover:text-blue-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg></button>
            </div>
          </div>
          <div class="space-y-2">
            <div class="flex items-center gap-3 text-[13px] text-slate-600">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
              <span>{{ viewingStudent?.email || 'kalkidan.mengistu@wu.edu.et' }}</span>
            </div>
            <div class="flex items-center gap-3 text-[13px] text-slate-600">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
              <span>{{ viewingStudent?.phone || '+251 98 042 6395' }}</span>
            </div>
            <div class="flex items-center gap-3 text-[13px] text-slate-600">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
              <span>Wollo University, Dessie, Ethiopia</span>
            </div>
          </div>
        </div>
        
        <div class="space-y-5 border-l border-slate-100 pl-8">
          <div>
            <p class="text-[12px] font-medium text-slate-400 mb-1">Department</p>
            <p class="text-[13px] font-semibold text-slate-800">{{ viewingStudent?.dept || 'Software Engineering' }}</p>
          </div>
          <div>
            <p class="text-[12px] font-medium text-slate-400 mb-1">Program</p>
            <p class="text-[13px] font-semibold text-slate-800">BSc in Software Engineering</p>
          </div>
          <div>
            <p class="text-[12px] font-medium text-slate-400 mb-1">Academic Year</p>
            <p class="text-[13px] font-semibold text-slate-800">{{ viewingStudent?.level || '3rd Year' }}</p>
          </div>
        </div>

        <div class="space-y-5 border-l border-slate-100 pl-8">
          <div>
            <p class="text-[12px] font-medium text-slate-400 mb-1">Date of Birth</p>
            <p class="text-[13px] font-semibold text-slate-800">June 16, 2004</p>
          </div>
          <div>
            <p class="text-[12px] font-medium text-slate-400 mb-1">Gender</p>
            <p class="text-[13px] font-semibold text-slate-800">Female</p>
          </div>
          <div>
            <p class="text-[12px] font-medium text-slate-400 mb-1">Nationality</p>
            <p class="text-[13px] font-semibold text-slate-800">Ethiopian</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Academic View Header -->
    <div v-if="activeTab === 'academic'" class="bg-white border border-slate-200 rounded-xl p-8 shadow-sm flex items-start gap-8">
      <div class="flex-1 grid grid-cols-[1fr_260px_260px] gap-8">
        
        <div class="flex gap-6">
          <img src="https://ui-avatars.com/api/?name=Kalkidan+Mengistu&background=e0e7ff&color=4338ca&size=120" alt="Avatar" class="w-24 h-24 rounded-full object-cover border-4 border-slate-50 shadow-sm" />
          <div class="space-y-2">
            <h2 class="text-[20px] font-bold text-slate-800">{{ viewingStudent?.name || 'Kalkidan Mengistu' }}</h2>
            <div class="flex items-center gap-2">
              <span class="text-[13px] text-slate-500">Student ID: <span class="font-medium text-slate-700">WU/00119/25</span></span>
              <button class="text-slate-400 hover:text-blue-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg></button>
            </div>
            
            <div class="pt-2 grid grid-cols-2 gap-y-2 gap-x-6">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span class="text-[12px] text-slate-500">Department:</span>
                <span class="text-[12px] font-medium text-slate-700">{{ viewingStudent?.dept || 'Software Engineering' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="text-[12px] text-slate-500">Program:</span>
                <span class="text-[12px] font-medium text-slate-700">BSc in Software Engineering</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-[12px] text-slate-500">Academic Level:</span>
                <span class="text-[12px] font-medium text-slate-700">Year 3</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                <span class="text-[12px] text-slate-500">Status:</span>
                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-600">Active</span>
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-4 border-l border-slate-100 pl-8 pt-1">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
              <p class="text-[12px] text-slate-500 mb-0.5">Academic Year</p>
              <p class="text-[13px] font-semibold text-slate-800">{{ viewingStudent?.level || '3rd Year' }}</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div>
              <p class="text-[12px] text-slate-500 mb-0.5">Academic Program</p>
              <p class="text-[13px] font-semibold text-slate-800">BSc in Software Engineering</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div>
              <p class="text-[12px] text-slate-500 mb-0.5">Department</p>
              <p class="text-[13px] font-semibold text-slate-800">{{ viewingStudent?.dept || 'Software Engineering' }}</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
              <p class="text-[12px] text-slate-500 mb-0.5">Admission Date</p>
              <p class="text-[13px] font-semibold text-slate-800">September 20, 2022</p>
            </div>
          </div>
        </div>

        <div class="space-y-4 border-l border-slate-100 pl-8 pt-1">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
              <p class="text-[12px] text-slate-500 mb-0.5">Current Semester</p>
              <p class="text-[13px] font-semibold text-slate-800">1st Semester (2025/2026)</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <div>
              <p class="text-[12px] text-slate-500 mb-0.5">Student Type</p>
              <p class="text-[13px] font-semibold text-slate-800">Regular</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </div>
            <div>
              <p class="text-[12px] text-slate-500 mb-0.5">Section</p>
              <p class="text-[13px] font-semibold text-slate-800">Section A</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
              <p class="text-[12px] text-slate-500 mb-0.5">Expected Graduation</p>
              <p class="text-[13px] font-semibold text-slate-800">June, 2026</p>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Enrolled View Header -->
    <div v-if="activeTab === 'enrolled'" class="bg-white border border-slate-200 rounded-xl p-8 shadow-sm flex items-start gap-8">
      <div class="flex-1 grid grid-cols-[1fr_500px] gap-8">
        
        <div class="flex gap-6">
          <img src="https://ui-avatars.com/api/?name=Kalkidan+Mengistu&background=e0e7ff&color=4338ca&size=120" alt="Avatar" class="w-24 h-24 rounded-full object-cover border-4 border-slate-50 shadow-sm" />
          <div class="space-y-2">
            <h2 class="text-[20px] font-bold text-slate-800">{{ viewingStudent?.name || 'Kalkidan Mengistu' }}</h2>
            <div class="flex items-center gap-2">
              <span class="text-[13px] text-slate-500">Student ID: <span class="font-medium text-slate-700">WU/00119/25</span></span>
              <button class="text-slate-400 hover:text-blue-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg></button>
            </div>
            
            <div class="pt-2 grid grid-cols-2 gap-y-2 gap-x-6">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span class="text-[12px] text-slate-500">Department:</span>
                <span class="text-[12px] font-medium text-slate-700">{{ viewingStudent?.dept || 'Software Engineering' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="text-[12px] text-slate-500">Program:</span>
                <span class="text-[12px] font-medium text-slate-700">BSc in Software Engineering</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="text-[12px] text-slate-500">Academic Year:</span>
                <span class="text-[12px] font-medium text-slate-700">3rd Year</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-[12px] text-slate-500">Academic Level:</span>
                <span class="text-[12px] font-medium text-slate-700">Year 3</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                <span class="text-[12px] text-slate-500">Status:</span>
                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-600">Active</span>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-3 gap-6 border-l border-slate-100 pl-8">
           <!-- Column 1 -->
           <div class="space-y-6 pt-1">
             <div class="flex items-start gap-3">
               <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
               </div>
               <div>
                 <p class="text-[12px] text-slate-500 mb-0.5">Academic Year</p>
                 <p class="text-[13px] font-semibold text-slate-800">3rd Year</p>
               </div>
             </div>
             <div class="flex items-start gap-3">
               <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
               </div>
               <div>
                 <p class="text-[12px] text-slate-500 mb-0.5">Total Enrolled Courses</p>
                 <p class="text-[13px] font-semibold text-slate-800">6</p>
               </div>
             </div>
           </div>
           <!-- Column 2 -->
           <div class="space-y-6 pt-1">
             <div class="flex items-start gap-3">
               <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
               </div>
               <div>
                 <p class="text-[12px] text-slate-500 mb-0.5">Current Semester</p>
                 <p class="text-[13px] font-semibold text-slate-800">1st Semester (2025/2026)</p>
               </div>
             </div>
             <div class="flex items-start gap-3">
               <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
               </div>
               <div>
                 <p class="text-[12px] text-slate-500 mb-0.5">Completed Credits</p>
                 <p class="text-[13px] font-semibold text-slate-800">0</p>
               </div>
             </div>
           </div>
           <!-- Column 3 -->
           <div class="space-y-6 pt-1">
             <div class="flex items-start gap-3">
               <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
               </div>
               <div>
                 <p class="text-[12px] text-slate-500 mb-0.5">Total Registered Credits</p>
                 <p class="text-[13px] font-semibold text-slate-800">20</p>
               </div>
             </div>
             <div class="flex items-start gap-3">
               <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
               </div>
               <div>
                 <p class="text-[12px] text-slate-500 mb-0.5">Remaining Credits</p>
                 <p class="text-[13px] font-semibold text-slate-800">20</p>
               </div>
             </div>
           </div>
        </div>

      </div>
    </div>

    <!-- Tabs -->
    <div class="flex items-center gap-6 border-b border-slate-200">
      <button @click="activeTab = 'personal'" :class="activeTab === 'personal' ? 'border-[#4338ca] text-[#4338ca] font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="flex items-center gap-2 pb-3 border-b-2 text-[14px] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg> Personal Information
      </button>
      <button @click="activeTab = 'academic'" :class="activeTab === 'academic' ? 'border-[#4338ca] text-[#4338ca] font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="flex items-center gap-2 pb-3 border-b-2 text-[14px] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg> Academic Information
      </button>
      <button @click="activeTab = 'enrolled'" :class="activeTab === 'enrolled' ? 'border-[#4338ca] text-[#4338ca] font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="flex items-center gap-2 pb-3 border-b-2 text-[14px] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg> Enrolled Courses
      </button>
      <button @click="activeTab = 'exams'" :class="activeTab === 'exams' ? 'border-[#4338ca] text-[#4338ca] font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="flex items-center gap-2 pb-3 border-b-2 text-[14px] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Exams & Results
      </button>
      <button @click="activeTab = 'attendance'" :class="activeTab === 'attendance' ? 'border-[#4338ca] text-[#4338ca] font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="flex items-center gap-2 pb-3 border-b-2 text-[14px] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> Attendance
      </button>
      <button @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'border-[#4338ca] text-[#4338ca] font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="flex items-center gap-2 pb-3 border-b-2 text-[14px] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Documents
      </button>
      <button @click="activeTab = 'activity'" :class="activeTab === 'activity' ? 'border-[#4338ca] text-[#4338ca] font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="flex items-center gap-2 pb-3 border-b-2 text-[14px] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Activity Log
      </button>
    </div>

    <!-- Main Content Grid -->
    <div v-if="activeTab === 'personal'" class="grid grid-cols-[1fr_360px] gap-6">
      
      <!-- Left Column (Main Content) -->
      <div class="space-y-6">
        
        <!-- Personal Information Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-[16px] font-bold text-slate-800 mb-6">Personal Information</h3>
          
          <div class="grid grid-cols-2 gap-x-8 gap-y-4">
            <!-- Row 1 -->
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Full Name</span>
              <span class="text-[13px] font-medium text-slate-800">{{ viewingStudent?.name || 'Kalkidan Mengistu' }}</span>
            </div>
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Department</span>
              <span class="text-[13px] font-medium text-slate-800">{{ viewingStudent?.dept || 'Software Engineering' }}</span>
            </div>
            
            <!-- Row 2 -->
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Student ID</span>
              <span class="text-[13px] font-medium text-slate-800">WU/00119/25</span>
            </div>
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Program</span>
              <span class="text-[13px] font-medium text-slate-800">BSc in Software Engineering</span>
            </div>

            <!-- Row 3 -->
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Email Address</span>
              <span class="text-[13px] font-medium text-slate-800">{{ viewingStudent?.email || 'kalkidan.mengistu@wu.edu.et' }}</span>
            </div>
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Academic Year</span>
              <span class="text-[13px] font-medium text-slate-800">{{ viewingStudent?.level || '3rd Year' }}</span>
            </div>

            <!-- Row 4 -->
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Phone Number</span>
              <span class="text-[13px] font-medium text-slate-800">{{ viewingStudent?.phone || '+251 98 042 6395' }}</span>
            </div>
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Academic Level</span>
              <span class="text-[13px] font-medium text-slate-800">Year 3</span>
            </div>

            <!-- Row 5 -->
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Date of Birth</span>
              <span class="text-[13px] font-medium text-slate-800">June 16, 2004</span>
            </div>
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Admission Date</span>
              <span class="text-[13px] font-medium text-slate-800">September 20, 2022</span>
            </div>

            <!-- Row 6 -->
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Gender</span>
              <span class="text-[13px] font-medium text-slate-800">Female</span>
            </div>
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Student Status</span>
              <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span class="text-[13px] font-bold text-emerald-600">Active</span>
              </div>
            </div>

            <!-- Row 7 -->
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Nationality</span>
              <span class="text-[13px] font-medium text-slate-800">Ethiopian</span>
            </div>
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Registration No.</span>
              <span class="text-[13px] font-medium text-slate-800">REG/WU/22/0911</span>
            </div>

            <!-- Row 8 -->
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Address</span>
              <span class="text-[13px] font-medium text-slate-800">Wollo University, Dessie, Ethiopia</span>
            </div>
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Blood Group</span>
              <span class="text-[13px] font-medium text-slate-800">O+</span>
            </div>

            <!-- Row 9 -->
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Emergency Contact</span>
              <span class="text-[13px] font-medium text-slate-800">Mengistu Tadesse (Father)</span>
            </div>
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Religion</span>
              <span class="text-[13px] font-medium text-slate-800">Orthodox</span>
            </div>

            <!-- Row 10 -->
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Emergency Phone</span>
              <span class="text-[13px] font-medium text-slate-800">+251 91 123 4567</span>
            </div>
            <div class="grid grid-cols-[130px_1fr] gap-2 items-center">
              <span class="text-[12px] text-slate-500">Marital Status</span>
              <span class="text-[13px] font-medium text-slate-800">Single</span>
            </div>
          </div>
        </div>

        <!-- Enrolled Courses Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
          <div class="flex items-center gap-2 mb-6">
            <h3 class="text-[16px] font-bold text-slate-800">Enrolled Courses</h3>
            <span class="text-[14px] text-slate-500">(Current Semester)</span>
          </div>

          <table class="w-full">
            <thead>
              <tr class="border-b border-slate-100">
                <th class="text-left py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Course Code</th>
                <th class="text-left py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Course Title</th>
                <th class="text-left py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Instructor</th>
                <th class="text-center py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Credits</th>
                <th class="text-center py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="text-center py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Grade</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
              <tr>
                <td class="py-4 text-[13px] font-semibold text-slate-700">SENG 301</td>
                <td class="py-4 text-[13px] text-slate-600">Database Systems</td>
                <td class="py-4 text-[13px] text-slate-600">Dr. Abebe Kebede</td>
                <td class="py-4 text-[13px] font-medium text-slate-700 text-center">4</td>
                <td class="py-4 text-center"><span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-600">Active</span></td>
                <td class="py-4 text-[13px] font-bold text-slate-700 text-center">A-</td>
              </tr>
              <tr>
                <td class="py-4 text-[13px] font-semibold text-slate-700">SENG 303</td>
                <td class="py-4 text-[13px] text-slate-600">Web Engineering</td>
                <td class="py-4 text-[13px] text-slate-600">Mr. Tesfaye Alemu</td>
                <td class="py-4 text-[13px] font-medium text-slate-700 text-center">4</td>
                <td class="py-4 text-center"><span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-600">Active</span></td>
                <td class="py-4 text-[13px] font-bold text-slate-700 text-center">B+</td>
              </tr>
              <tr>
                <td class="py-4 text-[13px] font-semibold text-slate-700">SENG 305</td>
                <td class="py-4 text-[13px] text-slate-600">Software Project Management</td>
                <td class="py-4 text-[13px] text-slate-600">Dr. Yeshimebet D.</td>
                <td class="py-4 text-[13px] font-medium text-slate-700 text-center">3</td>
                <td class="py-4 text-center"><span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-600">Active</span></td>
                <td class="py-4 text-[13px] font-bold text-slate-700 text-center">A</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Right Column (Sidebar) -->
      <div class="space-y-6">
        
        <!-- Academic Summary Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
          <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-[#4338ca]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"></path><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
              </div>
              <h3 class="text-[15px] font-bold text-slate-800">Academic Summary</h3>
            </div>
            <button class="text-[12px] font-medium text-blue-600 hover:text-blue-700">View Full Report</button>
          </div>
          
          <div class="flex items-end justify-between px-1">
            <div class="flex flex-col items-center">
              <span class="text-[11px] font-medium text-slate-500 mb-1">CGPA</span>
              <span class="text-[26px] font-bold text-[#4338ca] leading-none">3.56</span>
            </div>
            <div class="flex flex-col items-center">
              <span class="text-[11px] font-medium text-slate-500 mb-1">Total Credits</span>
              <span class="text-[20px] font-bold text-slate-800 leading-none">102</span>
            </div>
            <div class="flex flex-col items-center">
              <span class="text-[11px] font-medium text-slate-500 mb-1">Completed Credits</span>
              <span class="text-[20px] font-bold text-slate-800 leading-none">66</span>
            </div>
            <div class="flex flex-col items-center">
              <span class="text-[11px] font-medium text-slate-500 mb-1">Remaining Credits</span>
              <span class="text-[20px] font-bold text-slate-800 leading-none">36</span>
            </div>
            <div class="flex flex-col items-center">
              <span class="text-[11px] font-medium text-slate-500 mb-1">Class Rank</span>
              <span class="text-[18px] font-bold text-slate-800 leading-none">12<span class="text-[14px] text-slate-400">/120</span></span>
            </div>
          </div>
        </div>

        <!-- Examination Summary Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
          <div class="flex items-center gap-2 mb-5">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-[#4338ca]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-[15px] font-bold text-slate-800">Examination Summary</h3>
          </div>
          
          <div class="flex items-end justify-between px-2">
            <div class="flex flex-col items-center">
              <span class="text-[12px] font-medium text-slate-500 mb-1">Exams Taken</span>
              <span class="text-[22px] font-bold text-[#4338ca] leading-none">18</span>
            </div>
            <div class="flex flex-col items-center">
              <span class="text-[12px] font-medium text-slate-500 mb-1">Passed</span>
              <span class="text-[22px] font-bold text-emerald-500 leading-none">16</span>
            </div>
            <div class="flex flex-col items-center">
              <span class="text-[12px] font-medium text-slate-500 mb-1">Failed</span>
              <span class="text-[22px] font-bold text-rose-500 leading-none">2</span>
            </div>
            <div class="flex flex-col items-center">
              <span class="text-[12px] font-medium text-slate-500 mb-1">Pass Rate</span>
              <span class="text-[22px] font-bold text-slate-800 leading-none">88.9%</span>
            </div>
          </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
          <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-[#4338ca]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h3 class="text-[15px] font-bold text-slate-800">Quick Actions</h3>
          </div>
          
          <div class="grid grid-cols-2 gap-3">
            <button class="flex items-center justify-center gap-2 px-3 py-2.5 border border-slate-200 rounded-lg text-[13px] font-medium text-slate-700 hover:border-[#4338ca] hover:text-[#4338ca] transition-colors">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> Edit Student
            </button>
            <button class="flex items-center justify-center gap-2 px-3 py-2.5 border border-slate-200 rounded-lg text-[13px] font-medium text-slate-700 hover:border-[#4338ca] hover:text-[#4338ca] transition-colors">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> Reset Password
            </button>
            <button class="flex items-center justify-center gap-2 px-3 py-2.5 border border-slate-200 rounded-lg text-[13px] font-medium text-slate-700 hover:border-[#4338ca] hover:text-[#4338ca] transition-colors">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg> View Results
            </button>
            <button class="flex items-center justify-center gap-2 px-3 py-2.5 border border-slate-200 rounded-lg text-[13px] font-medium text-slate-700 hover:border-[#4338ca] hover:text-[#4338ca] transition-colors">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> View Attendance
            </button>
          </div>
        </div>

        <!-- Recent Activity Card -->
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
          <div class="flex items-center justify-between mb-5">
            <h3 class="text-[15px] font-bold text-slate-800">Recent Activity</h3>
            <button class="text-[12px] font-medium text-blue-600 hover:text-blue-700">View All</button>
          </div>
          
          <div class="space-y-4 relative before:absolute before:inset-0 before:ml-4 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 before:to-transparent">
            
            <div class="relative flex items-start gap-4">
              <div class="w-8 h-8 bg-indigo-50 text-[#4338ca] rounded-full flex items-center justify-center shrink-0 border-2 border-white relative z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-[13px] font-medium text-slate-800 truncate">Completed exam: Database Systems Midterm</p>
                <p class="text-[11px] text-slate-500 mt-0.5">May 27, 2025 • 10:30 AM</p>
              </div>
            </div>

            <div class="relative flex items-start gap-4">
              <div class="w-8 h-8 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center shrink-0 border-2 border-white relative z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-[13px] font-medium text-slate-800 truncate">Submitted assignment: Web Project Phase 2</p>
                <p class="text-[11px] text-slate-500 mt-0.5">May 24, 2025 • 02:15 PM</p>
              </div>
            </div>

            <div class="relative flex items-start gap-4">
              <div class="w-8 h-8 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center shrink-0 border-2 border-white relative z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-[13px] font-medium text-slate-800 truncate">Logged in to the system</p>
                <p class="text-[11px] text-slate-500 mt-0.5">May 27, 2025 • 08:45 AM</p>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
    
    <!-- Academic Content Grid -->
    <div v-if="activeTab === 'academic'" class="space-y-6">
      
      <!-- Top Section: Academic Progress & Academic History -->
      <div class="grid grid-cols-[340px_1fr] gap-6">
        
        <!-- Academic Progress -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-[15px] font-bold text-slate-800 mb-6">Academic Progress</h3>
          
          <div class="flex items-center gap-6">
             <div class="relative w-24 h-24 flex items-center justify-center rounded-full border-[8px] border-[#4338ca]">
                <div class="absolute -right-2 bottom-4 w-[8px] h-4 bg-emerald-500 rotate-45 rounded-sm"></div>
                <div class="absolute -left-2 top-2 w-[8px] h-8 bg-slate-100 -rotate-12"></div>
                <div class="flex flex-col items-center">
                  <span class="text-[20px] font-bold text-slate-800">66%</span>
                  <span class="text-[10px] font-semibold text-emerald-600">Complete</span>
                </div>
             </div>
             
             <div class="flex-1 space-y-3">
               <div class="flex items-center justify-between text-[12px]">
                  <span class="text-slate-500">Total Credits Required</span>
                  <span class="font-bold text-slate-800">162</span>
               </div>
               <div class="flex items-center justify-between text-[12px]">
                  <span class="text-slate-500">Total Credits Earned</span>
                  <span class="font-bold text-slate-800">106</span>
               </div>
               <div class="flex items-center justify-between text-[12px]">
                  <span class="text-slate-500">Remaining Credits</span>
                  <span class="font-bold text-slate-800">56</span>
               </div>
               <div class="flex items-center justify-between text-[12px]">
                  <span class="text-slate-500">CGPA</span>
                  <span class="font-bold text-slate-800">3.56 / 4.00</span>
               </div>
               <div class="flex items-center justify-between text-[12px]">
                  <span class="text-slate-500">Academic Standing</span>
                  <span class="font-bold text-emerald-600">Good Standing</span>
               </div>
             </div>
          </div>
        </div>
        
        <!-- Academic History -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-[15px] font-bold text-slate-800 mb-6">Academic History</h3>
          
          <table class="w-full text-left">
            <thead>
              <tr class="text-[10px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100">
                <th class="pb-3">Academic Year</th>
                <th class="pb-3">Level</th>
                <th class="pb-3">Semester</th>
                <th class="pb-3 text-center">Status</th>
                <th class="pb-3 text-center">CGPA</th>
                <th class="pb-3 text-center">Credits Earned</th>
              </tr>
            </thead>
            <tbody class="text-[12px]">
              <tr class="border-b border-slate-50 last:border-0">
                <td class="py-3.5 font-medium text-slate-800">2025/2026</td>
                <td class="py-3.5 text-slate-600">Year 3</td>
                <td class="py-3.5 text-slate-600">1st Semester</td>
                <td class="py-3.5 text-center"><span class="px-2 py-1 text-[10px] font-bold rounded-full bg-blue-50 text-blue-600">In Progress</span></td>
                <td class="py-3.5 font-bold text-slate-800 text-center">-</td>
                <td class="py-3.5 font-medium text-slate-700 text-center">18</td>
              </tr>
              <tr class="border-b border-slate-50 last:border-0">
                <td class="py-3.5 font-medium text-slate-800">2024/2025</td>
                <td class="py-3.5 text-slate-600">Year 2</td>
                <td class="py-3.5 text-slate-600">2nd Semester</td>
                <td class="py-3.5 text-center"><span class="px-2 py-1 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-600">Completed</span></td>
                <td class="py-3.5 font-bold text-slate-800 text-center">3.60</td>
                <td class="py-3.5 font-medium text-slate-700 text-center">20</td>
              </tr>
              <tr class="border-b border-slate-50 last:border-0">
                <td class="py-3.5 font-medium text-slate-800">2024/2025</td>
                <td class="py-3.5 text-slate-600">Year 2</td>
                <td class="py-3.5 text-slate-600">1st Semester</td>
                <td class="py-3.5 text-center"><span class="px-2 py-1 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-600">Completed</span></td>
                <td class="py-3.5 font-bold text-slate-800 text-center">3.45</td>
                <td class="py-3.5 font-medium text-slate-700 text-center">20</td>
              </tr>
              <tr class="border-b border-slate-50 last:border-0">
                <td class="py-3.5 font-medium text-slate-800">2023/2024</td>
                <td class="py-3.5 text-slate-600">Year 1</td>
                <td class="py-3.5 text-slate-600">2nd Semester</td>
                <td class="py-3.5 text-center"><span class="px-2 py-1 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-600">Completed</span></td>
                <td class="py-3.5 font-bold text-slate-800 text-center">3.12</td>
                <td class="py-3.5 font-medium text-slate-700 text-center">18</td>
              </tr>
              <tr class="border-b border-slate-50 last:border-0">
                <td class="py-3.5 font-medium text-slate-800">2023/2024</td>
                <td class="py-3.5 text-slate-600">Year 1</td>
                <td class="py-3.5 text-slate-600">1st Semester</td>
                <td class="py-3.5 text-center"><span class="px-2 py-1 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-600">Completed</span></td>
                <td class="py-3.5 font-bold text-slate-800 text-center">2.80</td>
                <td class="py-3.5 font-medium text-slate-700 text-center">16</td>
              </tr>
            </tbody>
          </table>
        </div>
        
      </div>
      
      <!-- Bottom Section: 4 Columns -->
      <div class="grid grid-cols-4 gap-6">
        
        <!-- Program Information -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-[14px] font-bold text-slate-800 mb-4">Program Information</h3>
          <div class="space-y-3">
             <div class="grid grid-cols-[110px_1fr] gap-2 items-start text-[12px]">
               <span class="text-slate-500">Program Name</span>
               <span class="font-bold text-slate-800">BSc in Software Engineering</span>
             </div>
             <div class="grid grid-cols-[110px_1fr] gap-2 items-start text-[12px]">
               <span class="text-slate-500">Department</span>
               <span class="font-bold text-slate-800">Software Engineering</span>
             </div>
             <div class="grid grid-cols-[110px_1fr] gap-2 items-start text-[12px]">
               <span class="text-slate-500">Faculty</span>
               <span class="font-bold text-slate-800">College of Computing and Informatics</span>
             </div>
             <div class="grid grid-cols-[110px_1fr] gap-2 items-start text-[12px]">
               <span class="text-slate-500">Program Type</span>
               <span class="font-bold text-slate-800">Undergraduate</span>
             </div>
             <div class="grid grid-cols-[110px_1fr] gap-2 items-start text-[12px]">
               <span class="text-slate-500">Duration</span>
               <span class="font-bold text-slate-800">4 Years</span>
             </div>
             <div class="grid grid-cols-[110px_1fr] gap-2 items-start text-[12px]">
               <span class="text-slate-500">Total Credit Hours</span>
               <span class="font-bold text-slate-800">162</span>
             </div>
          </div>
        </div>

        <!-- Academic Level Information -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-[14px] font-bold text-slate-800 mb-4">Academic Level Information</h3>
          <div class="space-y-4">
             <div class="flex items-center justify-between text-[12px]">
               <span class="text-slate-500">Current Level</span>
               <span class="font-bold text-slate-800">Year 3</span>
             </div>
             <div class="flex items-center justify-between text-[12px]">
               <span class="text-slate-500">Total Levels</span>
               <span class="font-bold text-slate-800">4</span>
             </div>
             <div class="flex items-center justify-between text-[12px]">
               <span class="text-slate-500">Current Semester</span>
               <span class="font-bold text-slate-800">1st Semester</span>
             </div>
             <div class="flex items-center justify-between text-[12px]">
               <span class="text-slate-500">Total Semesters</span>
               <span class="font-bold text-slate-800">8</span>
             </div>
             <div class="flex items-center justify-between text-[12px]">
               <span class="text-slate-500">Credit Hours Completed</span>
               <span class="font-bold text-slate-800">106</span>
             </div>
             <div class="flex items-center justify-between text-[12px]">
               <span class="text-slate-500">Credit Hours Remaining</span>
               <span class="font-bold text-slate-800">56</span>
             </div>
          </div>
        </div>

        <!-- Important Dates -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-[14px] font-bold text-slate-800 mb-4">Important Dates</h3>
          <div class="space-y-4">
             <div class="grid grid-cols-[100px_1fr] gap-2 items-center text-[12px]">
               <span class="text-slate-500 flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> Admission Date</span>
               <span class="font-bold text-slate-800 text-right">September 20, 2022</span>
             </div>
             <div class="grid grid-cols-[100px_1fr] gap-2 items-center text-[12px]">
               <span class="text-slate-500 flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> Registration Date</span>
               <span class="font-bold text-slate-800 text-right">August 25, 2025</span>
             </div>
             <div class="grid grid-cols-[100px_1fr] gap-2 items-center text-[12px]">
               <span class="text-slate-500 flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> Semester Start Date</span>
               <span class="font-bold text-slate-800 text-right">September 15, 2025</span>
             </div>
             <div class="grid grid-cols-[100px_1fr] gap-2 items-center text-[12px]">
               <span class="text-slate-500 flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> Semester End Date</span>
               <span class="font-bold text-slate-800 text-right">January 30, 2026</span>
             </div>
             <div class="grid grid-cols-[100px_1fr] gap-2 items-center text-[12px]">
               <span class="text-slate-500 flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> Expected Graduation</span>
               <span class="font-bold text-slate-800 text-right">June, 2026</span>
             </div>
          </div>
        </div>

        <!-- Academic Advisor -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
          <h3 class="text-[14px] font-bold text-slate-800 mb-4">Academic Advisor</h3>
          
          <div class="flex items-center gap-3 mb-4">
            <img src="https://ui-avatars.com/api/?name=Abebe+Kebede&background=f8fafc&color=334155&size=80" alt="Advisor" class="w-10 h-10 rounded-full border border-slate-200" />
            <div>
               <p class="text-[13px] font-bold text-slate-800">Dr. Abebe Kebede</p>
               <p class="text-[11px] text-slate-500">Associate Professor</p>
            </div>
          </div>
          
          <div class="space-y-3">
             <p class="text-[12px] font-medium text-slate-700">Department of Software Engineering</p>
             <div class="flex items-center gap-2 text-[12px] text-slate-600">
               <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
               <span>abebe.kebede@wu.edu.et</span>
             </div>
             <div class="flex items-center gap-2 text-[12px] text-slate-600">
               <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
               <span>+251 91 123 4567</span>
             </div>
          </div>
        </div>

      </div>
    </div>

  </div>
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
</template>