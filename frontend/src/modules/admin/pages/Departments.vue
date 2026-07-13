<script setup lang="ts">
import { ref, computed } from 'vue'

import apiClient from '../../../core/api/apiClient'

const search = ref('')
const statusFilter = ref('all')
const facultyFilter = ref('all')
const sortBy = ref('name')
const currentPage = ref(1)
const perPage = 8
const showAddForm = ref(false)
const showDeleteModal = ref(false)
const showEditModal = ref(false)
const editData = ref({ id: null as number|null, name: '', code: '', established: '' })
const selectedDept = ref<any>(null)
const isLoading = ref(false)

// Detail view state
const showDetailView = ref(false)
const viewingDept = ref<any>(null)
const detailActiveTab = ref('info')
const showAssignHeadModal = ref(false)
const headSearchQuery = ref('')

const viewDeptDetail = (dept: any) => {
  viewingDept.value = dept
  detailActiveTab.value = 'info'
  showDetailView.value = true
}

const backToDepartments = () => {
  showDetailView.value = false
  viewingDept.value = null
}

const detailTabs = [
  { key: 'info', label: 'Department Information', icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
  { key: 'head', label: 'Department Head', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
  { key: 'instructors', label: 'Instructors', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
  { key: 'courses', label: 'Courses', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
  { key: 'students', label: 'Students', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
  { key: 'settings', label: 'Settings', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' },
  { key: 'activity', label: 'Activity Log', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
]

const mockInstructors = [
  { name: 'Dr. Abebe Kebede', email: 'abebe.kebede@wu.edu.et', current: true },
  { name: 'Mr. Tesfaye Alemu', email: 'tesfaye.alemu@wu.edu.et', current: false },
  { name: 'Ms. Yeshimebet D.', email: 'yeshimebet.d@wu.edu.et', current: false },
  { name: 'Mr. Haile Getachew', email: 'haile.getachew@wu.edu.et', current: false },
]

const allDepts = ref<any[]>([])

// Add Department form data
const newDeptForm = ref({
  name: '',
  code: '',
  faculty: '',
  college: '',
  programLevel: '',
  departmentType: '',
  headOfDepartment: '',
  email: '',
  phone: '',
  officeLocation: '',
  description: '',
  establishedDate: '',
  status: 'active',
  displayOrder: 0,
  vision: '',
  mission: '',
})

const descriptionCharCount = computed(() => newDeptForm.value.description.length)
const visionCharCount = computed(() => newDeptForm.value.vision.length)
const missionCharCount = computed(() => newDeptForm.value.mission.length)

const resetAddForm = () => {
  newDeptForm.value = {
    name: '',
    code: '',
    faculty: '',
    college: '',
    programLevel: '',
    departmentType: '',
    headOfDepartment: '',
    email: '',
    phone: '',
    officeLocation: '',
    description: '',
    establishedDate: '',
    status: 'active',
    displayOrder: 0,
    vision: '',
    mission: '',
  }
}

// Mock data for display (will be replaced by API data when available)
const mockDepts = [
  { id: 1, name: 'Computer Science', subLabel: 'Computing & Informatics', code: 'CS', faculty: 'College of Computing and Informatics', head: 'Dr. Solomon Abate', headEmail: 'solomon.abate@wu.edu.et', students: 632, instructors: 28, courses: 65, status: 'active', established: '2005' },
  { id: 2, name: 'Software Engineering', subLabel: 'Computing & Informatics', code: 'SWE', faculty: 'College of Computing and Informatics', head: 'Dr. Abebe Kebede', headEmail: 'abebe.kebede@wu.edu.et', students: 524, instructors: 18, courses: 36, status: 'active', established: '2010' },
  { id: 3, name: 'Information Systems', subLabel: 'Computing & Informatics', code: 'IS', faculty: 'College of Computing and Informatics', head: 'Dr. Yeshimebet D.', headEmail: 'yeshimebet.d@wu.edu.et', students: 312, instructors: 15, courses: 28, status: 'active', established: '2008' },
  { id: 4, name: 'Electrical Engineering', subLabel: 'Engineering', code: 'EE', faculty: 'College of Engineering and Technology', head: 'Dr. Getachew T.', headEmail: 'getachew.t@wu.edu.et', students: 298, instructors: 20, courses: 42, status: 'active', established: '2003' },
  { id: 5, name: 'Mechanical Engineering', subLabel: 'Engineering', code: 'ME', faculty: 'College of Engineering and Technology', head: 'Dr. Melaku A.', headEmail: 'melaku.a@wu.edu.et', students: 276, instructors: 17, courses: 40, status: 'active', established: '2004' },
  { id: 6, name: 'Civil Engineering', subLabel: 'Engineering', code: 'CE', faculty: 'College of Engineering and Technology', head: 'Dr. Fikret A.', headEmail: 'fikret.a@wu.edu.et', students: 210, instructors: 14, courses: 32, status: 'active', established: '2002' },
  { id: 7, name: 'Mathematics', subLabel: 'Natural Sciences', code: 'MATH', faculty: 'College of Natural Sciences', head: 'Dr. Tesfaye A.', headEmail: 'tesfaye.a@wu.edu.et', students: 186, instructors: 12, courses: 24, status: 'active', established: '2001' },
  { id: 8, name: 'Physics', subLabel: 'Natural Sciences', code: 'PHYS', faculty: 'College of Natural Sciences', head: 'Dr. Alemu T.', headEmail: 'alemu.t@wu.edu.et', students: 142, instructors: 10, courses: 20, status: 'active', established: '2001' },
  { id: 9, name: 'Chemistry', subLabel: 'Natural Sciences', code: 'CHEM', faculty: 'College of Natural Sciences', head: 'Dr. Marta G.', headEmail: 'marta.g@wu.edu.et', students: 120, instructors: 9, courses: 18, status: 'active', established: '2001' },
  { id: 10, name: 'Biology', subLabel: 'Natural Sciences', code: 'BIO', faculty: 'College of Natural Sciences', head: 'Dr. Hanna B.', headEmail: 'hanna.b@wu.edu.et', students: 98, instructors: 7, courses: 15, status: 'inactive', established: '2006' },
]

// Fetch departments from backend
const fetchDepartments = async () => {
  try {
    const res = await apiClient.get('/admin/departments')
    allDepts.value = res.data.data.map((d: any) => ({
      id: d.id,
      name: d.name,
      subLabel: d.faculty || 'General',
      head: d.head?.name || 'N/A',
      headEmail: d.head?.email || '',
      code: d.code,
      faculty: d.faculty || 'N/A',
      instructors: d.instructors_count || 0,
      students: d.students_count || 0,
      courses: d.courses_count || 0,
      status: d.status,
      established: d.established || 'N/A'
    }))
    // If API returns no data, use mock data for display
    if (allDepts.value.length === 0) {
      allDepts.value = mockDepts
    }
  } catch (err) {
    console.error('Failed to fetch departments:', err)
    // Fall back to mock data on error
    allDepts.value = mockDepts
  }
}
fetchDepartments()

const faculties = computed(() => {
  const set = new Set(allDepts.value.map(d => d.faculty))
  return Array.from(set)
})

const filtered = computed(() =>
  allDepts.value.filter(d => {
    const matchSearch = d.name.toLowerCase().includes(search.value.toLowerCase()) ||
                        d.head.toLowerCase().includes(search.value.toLowerCase()) ||
                        d.code.toLowerCase().includes(search.value.toLowerCase())
    const matchStatus = statusFilter.value === 'all' || d.status === statusFilter.value
    const matchFaculty = facultyFilter.value === 'all' || d.faculty === facultyFilter.value
    return matchSearch && matchStatus && matchFaculty
  }).sort((a: any, b: any) => {
    if (sortBy.value === 'name') return a.name.localeCompare(b.name)
    if (sortBy.value === 'students') return b.students - a.students
    if (sortBy.value === 'code') return a.code.localeCompare(b.code)
    return 0
  })
)

const totalPages = computed(() => Math.ceil(filtered.value.length / perPage))
const paginated  = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const stats = computed(() => ({
  total:       allDepts.value.length,
  active:      allDepts.value.filter(d => d.status === 'active').length,
  students:    allDepts.value.reduce((a, d) => a + d.students, 0),
  instructors: allDepts.value.reduce((a, d) => a + d.instructors, 0),
  courses:     allDepts.value.reduce((a, d) => a + d.courses, 0),
}))

const getAvatarUrl = (name: string) => {
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=e0e7ff&color=4338ca&size=80`
}

const saveDepartment = async () => {
  if (!newDeptForm.value.name || !newDeptForm.value.code) return
  isLoading.value = true
  try {
    await apiClient.post('/admin/departments', {
      name: newDeptForm.value.name,
      code: newDeptForm.value.code,
      faculty: newDeptForm.value.faculty,
      college: newDeptForm.value.college,
      head_name: newDeptForm.value.headOfDepartment,
      head_email: newDeptForm.value.email,
      head_password: 'password123',
      established: newDeptForm.value.establishedDate,
      status: newDeptForm.value.status,
      description: newDeptForm.value.description,
      vision: newDeptForm.value.vision,
      mission: newDeptForm.value.mission,
    })
    await fetchDepartments()
    showAddForm.value = false
    resetAddForm()
  } catch (err: any) {
    console.error('Error creating department:', err)
    let errorMessage = err.response?.data?.message || 'Failed to create department.'
    if (err.response?.data?.errors) {
      errorMessage += '\n' + Object.values(err.response.data.errors).flat().join('\n')
    }
    alert(errorMessage)
  } finally {
    isLoading.value = false
  }
}

const openEditModal = (d: any) => {
  editData.value = { id: d.id, name: d.name, code: d.code, established: d.established === 'N/A' ? '' : d.established }
  showEditModal.value = true
}

const updateDept = async () => {
  if (!editData.value.name || !editData.value.code) return
  isLoading.value = true
  try {
    await apiClient.put(`/admin/departments/${editData.value.id}`, {
      name: editData.value.name,
      code: editData.value.code,
      established: editData.value.established
    })
    await fetchDepartments()
    showEditModal.value = false
  } catch (err: any) {
    console.error('Error updating department:', err)
    let errorMessage = err.response?.data?.message || 'Failed to update department.'
    if (err.response?.data?.errors) {
      errorMessage += '\n' + Object.values(err.response.data.errors).flat().join('\n')
    }
    alert(errorMessage)
  } finally {
    isLoading.value = false
  }
}

const confirmDelete = (d: any) => { selectedDept.value = d; showDeleteModal.value = true }
const deleteDept = async () => { 
  if (!selectedDept.value) return
  isLoading.value = true
  try {
    await apiClient.delete(`/admin/departments/${selectedDept.value.id}`)
    await fetchDepartments()
    showDeleteModal.value = false 
  } catch (err) {
    console.error('Error deleting department:', err)
  } finally {
    isLoading.value = false
  }
}


// File upload drag & drop
const isDragging = ref(false)
const logoFile = ref<File | null>(null)
const logoPreview = ref('')

const handleDragOver = (e: DragEvent) => {
  e.preventDefault()
  isDragging.value = true
}
const handleDragLeave = () => {
  isDragging.value = false
}
const handleDrop = (e: DragEvent) => {
  e.preventDefault()
  isDragging.value = false
  const files = e.dataTransfer?.files
  if (files && files.length > 0) {
    processFile(files[0])
  }
}
const handleFileSelect = (e: Event) => {
  const input = e.target as HTMLInputElement
  if (input.files && input.files.length > 0) {
    processFile(input.files[0])
  }
}
const processFile = (file: File) => {
  if (file.size > 2 * 1024 * 1024) {
    alert('File size must be less than 2MB')
    return
  }
  logoFile.value = file
  const reader = new FileReader()
  reader.onload = (e) => {
    logoPreview.value = e.target?.result as string
  }
  reader.readAsDataURL(file)
}
</script>

<template>
  <div class="space-y-6">

    <!-- ==================== DEPARTMENT DETAIL VIEW ==================== -->
    <template v-if="showDetailView && viewingDept">
      
      <!-- Breadcrumbs -->
      <div class="flex items-center gap-2 text-[13px]">
        <button @click="backToDepartments" class="text-slate-500 hover:text-[#4338ca] transition-colors">Departments</button>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span class="text-slate-700 font-semibold">Department Details</span>
      </div>

      <!-- Header -->
      <div class="flex items-start justify-between">
        <div>
          <h1 class="text-[22px] font-bold text-slate-800">Department Details</h1>
          <p class="text-[13px] text-slate-500 mt-1">View and manage department information and settings</p>
        </div>
        <button @click="backToDepartments" class="flex items-center gap-2 px-4 py-2.5 border border-slate-200 rounded-lg text-[13px] font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          Back to Departments
        </button>
      </div>

      <!-- Top Profile Card -->
      <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-8">
        <div class="flex items-start gap-8">
          <!-- Avatar & Info -->
          <div class="flex items-start gap-6 flex-1">
            <div class="w-[100px] h-[100px] rounded-2xl bg-indigo-50 flex items-center justify-center shrink-0 border-2 border-indigo-100">
              <svg class="w-12 h-12 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div class="space-y-2">
              <div class="flex items-center gap-3">
                <h2 class="text-[20px] font-bold text-slate-800">{{ viewingDept.name }}</h2>
                <span :class="[viewingDept.status === 'active' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200', 'text-[11px] font-bold px-2.5 py-0.5 rounded-full border capitalize']">{{ viewingDept.status }}</span>
              </div>
              <div class="space-y-1.5">
                <p class="text-[13px] text-slate-600">Department Code: <span class="font-bold text-[#4338ca]">{{ viewingDept.code }}</span></p>
                <p class="text-[13px] text-slate-600">Faculty: <span class="font-medium text-slate-700">{{ viewingDept.faculty }}</span></p>
                <p class="text-[13px] text-slate-500 max-w-[500px] leading-relaxed">Description: The {{ viewingDept.name }} department focuses on developing skilled software professionals through quality education and practical training.</p>
              </div>
            </div>
          </div>

          <!-- Stats -->
          <div class="flex items-center gap-4 shrink-0">
            <div v-for="(stat, si) in [
              { label: 'Students', val: viewingDept.students, sub: 'Active Students', bg: 'bg-indigo-50', ic: 'text-[#4338ca]', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
              { label: 'Instructors', val: viewingDept.instructors, sub: 'Total Instructors', bg: 'bg-blue-50', ic: 'text-blue-500', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
              { label: 'Courses', val: viewingDept.courses, sub: 'Total Courses', bg: 'bg-amber-50', ic: 'text-amber-500', icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
              { label: 'Exams', val: 85, sub: 'Total Exams', bg: 'bg-purple-50', ic: 'text-purple-500', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
            ]" :key="si" class="border border-slate-100 rounded-xl p-4 min-w-[110px] text-center">
              <div :class="[stat.bg, 'w-8 h-8 rounded-lg flex items-center justify-center mx-auto mb-2']">
                <svg class="w-4 h-4" :class="stat.ic" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon"></path></svg>
              </div>
              <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">{{ stat.label }}</p>
              <p class="text-[22px] font-bold text-slate-800">{{ stat.val }}</p>
              <p class="text-[10px] text-slate-400">{{ stat.sub }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Content Area with Sidebar Tabs -->
      <div class="grid grid-cols-[220px_1fr] gap-6">
        
        <!-- Sidebar Navigation -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-3 self-start">
          <nav class="space-y-1">
            <button v-for="tab in detailTabs" :key="tab.key" @click="detailActiveTab = tab.key"
              :class="[detailActiveTab === tab.key ? 'bg-indigo-50 text-[#4338ca] font-bold' : 'text-slate-600 hover:bg-slate-50', 'flex items-center gap-3 w-full px-4 py-3 rounded-lg text-[13px] transition-colors text-left']">
              <svg class="w-4.5 h-4.5 shrink-0" :class="detailActiveTab === tab.key ? 'text-[#4338ca]' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="tab.icon"></path></svg>
              {{ tab.label }}
            </button>
          </nav>
        </div>

        <!-- Main Content -->
        <div class="space-y-6">

          <!-- Department Information Tab -->
          <template v-if="detailActiveTab === 'info'">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-8">
              <h3 class="text-[16px] font-bold text-slate-800 mb-6">Department Information</h3>
              <div class="grid grid-cols-2 gap-x-16 gap-y-5">
                <div class="grid grid-cols-[140px_1fr] gap-2 items-start">
                  <span class="text-[13px] text-slate-500">Department Code</span>
                  <span class="text-[13px] font-semibold text-slate-800">{{ viewingDept.code }}</span>
                </div>
                <div class="grid grid-cols-[140px_1fr] gap-2 items-start">
                  <span class="text-[13px] text-slate-500">Head Since</span>
                  <span class="text-[13px] font-semibold text-slate-800">March 15, 2024</span>
                </div>
                <div class="grid grid-cols-[140px_1fr] gap-2 items-start">
                  <span class="text-[13px] text-slate-500">Faculty</span>
                  <span class="text-[13px] font-semibold text-slate-800">{{ viewingDept.faculty }}</span>
                </div>
                <div class="grid grid-cols-[140px_1fr] gap-2 items-start">
                  <span class="text-[13px] text-slate-500">Email</span>
                  <span class="text-[13px] font-semibold text-slate-800">{{ viewingDept.code.toLowerCase() }}@wu.edu.et</span>
                </div>
                <div class="grid grid-cols-[140px_1fr] gap-2 items-start">
                  <span class="text-[13px] text-slate-500">Established Date</span>
                  <span class="text-[13px] font-semibold text-slate-800">September 10, {{ viewingDept.established }}</span>
                </div>
                <div class="grid grid-cols-[140px_1fr] gap-2 items-start">
                  <span class="text-[13px] text-slate-500">Phone</span>
                  <span class="text-[13px] font-semibold text-slate-800">+251 11 123 4567</span>
                </div>
                <div class="grid grid-cols-[140px_1fr] gap-2 items-start">
                  <span class="text-[13px] text-slate-500">Office Location</span>
                  <span class="text-[13px] font-semibold text-slate-800">Block C, Room 204</span>
                </div>
                <div class="grid grid-cols-[140px_1fr] gap-2 items-start">
                  <span class="text-[13px] text-slate-500">Status</span>
                  <span><span :class="[viewingDept.status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500', 'text-[11px] font-bold px-2.5 py-1 rounded-full capitalize']">{{ viewingDept.status }}</span></span>
                </div>
              </div>
            </div>

            <!-- Description, Vision, Mission -->
            <div class="grid grid-cols-3 gap-6">
              <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                <h4 class="text-[14px] font-bold text-slate-800 mb-3">Description</h4>
                <p class="text-[13px] text-slate-500 leading-relaxed">The {{ viewingDept.name }} department focuses on developing skilled professionals through quality education and practical training.</p>
              </div>
              <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                <h4 class="text-[14px] font-bold text-slate-800 mb-3">Vision</h4>
                <p class="text-[13px] text-slate-500 leading-relaxed">To become a leading center of excellence in {{ viewingDept.name.toLowerCase() }} education and research in East Africa.</p>
              </div>
              <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                <h4 class="text-[14px] font-bold text-slate-800 mb-3">Mission</h4>
                <p class="text-[13px] text-slate-500 leading-relaxed">To produce competent and ethical professionals who can contribute to the nation's technological advancement.</p>
              </div>
            </div>
          </template>

          <!-- Department Head Tab -->
          <template v-if="detailActiveTab === 'head'">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-8">
              <div class="flex items-start justify-between mb-6">
                <div>
                  <h3 class="text-[16px] font-bold text-slate-800">Department Head</h3>
                  <p class="text-[13px] text-slate-500 mt-1">Assign an instructor as the department head will additional privileges to manage the department.</p>
                </div>
                <button @click="showAssignHeadModal = true" class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white text-[13px] font-bold px-4 py-2.5 rounded-lg shadow-sm transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                  Change Department Head
                </button>
              </div>

              <!-- Current Head Card -->
              <div class="border border-slate-200 rounded-xl p-6 bg-slate-50/50">
                <h4 class="text-[14px] font-bold text-slate-800 mb-4">Current Department Head</h4>
                <div class="flex items-center gap-4">
                  <img :src="getAvatarUrl(viewingDept.head)" :alt="viewingDept.head" class="w-16 h-16 rounded-full border-3 border-white shadow-sm" />
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                      <h5 class="text-[15px] font-bold text-slate-800">{{ viewingDept.head }}</h5>
                      <span class="text-[10px] font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Current Head</span>
                    </div>
                    <p class="text-[12px] text-slate-500">{{ viewingDept.headEmail }}</p>
                    <p class="text-[12px] text-slate-500">+251 91 123 4567</p>
                    <p class="text-[12px] text-slate-400 mt-1">Since: March 15, 2024</p>
                  </div>
                </div>
              </div>
            </div>
          </template>

          <!-- Instructors Tab -->
          <template v-if="detailActiveTab === 'instructors'">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-8">
              <div class="flex items-center justify-between mb-6">
                <h3 class="text-[16px] font-bold text-slate-800">Instructors <span class="text-slate-400 font-normal">({{ viewingDept.instructors }})</span></h3>
                <button class="flex items-center gap-2 bg-[#4338ca] hover:bg-indigo-700 text-white text-[13px] font-bold px-4 py-2.5 rounded-lg shadow-sm transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                  Assign Instructor
                </button>
              </div>
              <table class="w-full">
                <thead>
                  <tr class="border-b border-slate-100">
                    <th class="text-left py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Instructor</th>
                    <th class="text-left py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email</th>
                    <th class="text-left py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Phone</th>
                    <th class="text-center py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Courses</th>
                    <th class="text-center py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="inst in mockInstructors" :key="inst.email" class="border-b border-slate-50 hover:bg-slate-50/60 transition-colors">
                    <td class="py-3.5 px-4">
                      <div class="flex items-center gap-3">
                        <img :src="getAvatarUrl(inst.name)" :alt="inst.name" class="w-8 h-8 rounded-full border border-slate-100" />
                        <span class="text-[13px] font-semibold text-slate-700">{{ inst.name }}</span>
                      </div>
                    </td>
                    <td class="py-3.5 px-4 text-[12px] text-slate-500">{{ inst.email }}</td>
                    <td class="py-3.5 px-4 text-[12px] text-slate-500">+251 91 XXX XXXX</td>
                    <td class="py-3.5 px-4 text-center text-[13px] font-bold text-slate-700">3</td>
                    <td class="py-3.5 px-4 text-center"><span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600">Active</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </template>

          <!-- Courses Tab -->
          <template v-if="detailActiveTab === 'courses'">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-8">
              <div class="flex items-center justify-between mb-6">
                <h3 class="text-[16px] font-bold text-slate-800">Courses <span class="text-slate-400 font-normal">({{ viewingDept.courses }})</span></h3>
                <button class="flex items-center gap-2 bg-[#4338ca] hover:bg-indigo-700 text-white text-[13px] font-bold px-4 py-2.5 rounded-lg shadow-sm transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                  Add Course
                </button>
              </div>
              <table class="w-full">
                <thead>
                  <tr class="border-b border-slate-100">
                    <th class="text-left py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Course Code</th>
                    <th class="text-left py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Course Title</th>
                    <th class="text-center py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Credits</th>
                    <th class="text-left py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Instructor</th>
                    <th class="text-center py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Type</th>
                    <th class="text-center py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(course, ci) in [
                    { code: viewingDept.code + ' 301', title: 'Introduction to ' + viewingDept.name, credits: 4, instructor: viewingDept.head, type: 'Core' },
                    { code: viewingDept.code + ' 302', title: 'Advanced ' + viewingDept.name, credits: 3, instructor: 'Mr. Tesfaye A.', type: 'Core' },
                    { code: viewingDept.code + ' 303', title: viewingDept.name + ' Lab', credits: 2, instructor: 'Ms. Yeshimebet D.', type: 'Elective' },
                  ]" :key="ci" class="border-b border-slate-50 hover:bg-slate-50/60 transition-colors">
                    <td class="py-3.5 px-4 text-[13px] font-semibold font-mono text-slate-700">{{ course.code }}</td>
                    <td class="py-3.5 px-4 text-[13px] text-slate-600">{{ course.title }}</td>
                    <td class="py-3.5 px-4 text-center text-[13px] font-bold text-slate-700">{{ course.credits }}</td>
                    <td class="py-3.5 px-4 text-[12px] text-slate-500">{{ course.instructor }}</td>
                    <td class="py-3.5 px-4 text-center"><span :class="[course.type === 'Core' ? 'bg-indigo-50 text-indigo-600' : 'bg-blue-50 text-blue-600', 'text-[11px] font-bold px-2.5 py-1 rounded-full']">{{ course.type }}</span></td>
                    <td class="py-3.5 px-4 text-center"><span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600">Active</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </template>

          <!-- Students Tab -->
          <template v-if="detailActiveTab === 'students'">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-8">
              <div class="flex items-center justify-between mb-6">
                <h3 class="text-[16px] font-bold text-slate-800">Students <span class="text-slate-400 font-normal">({{ viewingDept.students }})</span></h3>
              </div>
              <table class="w-full">
                <thead>
                  <tr class="border-b border-slate-100">
                    <th class="text-left py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Student</th>
                    <th class="text-left py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Student ID</th>
                    <th class="text-left py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Year</th>
                    <th class="text-center py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Courses</th>
                    <th class="text-center py-3 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(student, si) in [
                    { name: 'Kalkidan Mengistu', id: 'WU/00119/25', year: '3rd Year', courses: 6 },
                    { name: 'Abebe Tesfaye', id: 'WU/00120/25', year: '3rd Year', courses: 5 },
                    { name: 'Meron Tadesse', id: 'WU/00145/25', year: '2nd Year', courses: 6 },
                    { name: 'Solomon Bekele', id: 'WU/00167/25', year: '1st Year', courses: 7 },
                  ]" :key="si" class="border-b border-slate-50 hover:bg-slate-50/60 transition-colors">
                    <td class="py-3.5 px-4">
                      <div class="flex items-center gap-3">
                        <img :src="getAvatarUrl(student.name)" :alt="student.name" class="w-8 h-8 rounded-full border border-slate-100" />
                        <span class="text-[13px] font-semibold text-slate-700">{{ student.name }}</span>
                      </div>
                    </td>
                    <td class="py-3.5 px-4 text-[12px] font-mono text-slate-500">{{ student.id }}</td>
                    <td class="py-3.5 px-4 text-[12px] text-slate-600">{{ student.year }}</td>
                    <td class="py-3.5 px-4 text-center text-[13px] font-bold text-slate-700">{{ student.courses }}</td>
                    <td class="py-3.5 px-4 text-center"><span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600">Active</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </template>

          <!-- Settings Tab -->
          <template v-if="detailActiveTab === 'settings'">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-8">
              <h3 class="text-[16px] font-bold text-slate-800 mb-6">Department Settings</h3>
              <div class="space-y-6">
                <div class="flex items-center justify-between p-4 border border-slate-100 rounded-lg">
                  <div>
                    <p class="text-[13px] font-bold text-slate-700">Department Status</p>
                    <p class="text-[12px] text-slate-500 mt-0.5">Enable or disable this department</p>
                  </div>
                  <span :class="[viewingDept.status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500', 'text-[11px] font-bold px-3 py-1.5 rounded-full capitalize']">{{ viewingDept.status }}</span>
                </div>
                <div class="flex items-center justify-between p-4 border border-slate-100 rounded-lg">
                  <div>
                    <p class="text-[13px] font-bold text-slate-700">Accept New Students</p>
                    <p class="text-[12px] text-slate-500 mt-0.5">Allow new student enrollments</p>
                  </div>
                  <span class="text-[11px] font-bold px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-600">Enabled</span>
                </div>
                <div class="flex items-center justify-between p-4 border border-rose-100 rounded-lg bg-rose-50/30">
                  <div>
                    <p class="text-[13px] font-bold text-rose-700">Danger Zone</p>
                    <p class="text-[12px] text-rose-500 mt-0.5">Permanently delete this department and all associated data</p>
                  </div>
                  <button class="px-4 py-2 text-[12px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-lg transition-colors">Delete Department</button>
                </div>
              </div>
            </div>
          </template>

          <!-- Activity Log Tab -->
          <template v-if="detailActiveTab === 'activity'">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-8">
              <h3 class="text-[16px] font-bold text-slate-800 mb-6">Activity Log</h3>
              <div class="space-y-4">
                <div v-for="(activity, ai) in [
                  { action: 'Department head changed', user: 'Admin', time: '2 hours ago', icon: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', color: 'bg-blue-50 text-blue-500' },
                  { action: 'New course added: ' + viewingDept.code + ' 305', user: viewingDept.head, time: '1 day ago', icon: 'M12 4v16m8-8H4', color: 'bg-emerald-50 text-emerald-500' },
                  { action: 'Instructor assigned', user: 'Admin', time: '3 days ago', icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', color: 'bg-purple-50 text-purple-500' },
                  { action: 'Department status changed to Active', user: 'Admin', time: '1 week ago', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', color: 'bg-amber-50 text-amber-500' },
                  { action: 'Department created', user: 'Super Admin', time: 'September 10, ' + viewingDept.established, icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5', color: 'bg-indigo-50 text-[#4338ca]' },
                ]" :key="ai" class="flex items-start gap-4 p-4 border border-slate-100 rounded-lg hover:bg-slate-50/50 transition-colors">
                  <div :class="[activity.color, 'w-9 h-9 rounded-lg flex items-center justify-center shrink-0']">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="activity.icon"></path></svg>
                  </div>
                  <div class="flex-1">
                    <p class="text-[13px] font-semibold text-slate-700">{{ activity.action }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">by {{ activity.user }} · {{ activity.time }}</p>
                  </div>
                </div>
              </div>
            </div>
          </template>

        </div>
      </div>

      <!-- Assign Head Modal -->
      <Teleport to="body">
        <div v-if="showAssignHeadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
          <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
              <h3 class="text-[16px] font-bold text-slate-800">Assign Department Head</h3>
              <button @click="showAssignHeadModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="px-6 py-5 space-y-5">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Department</label>
                <input type="text" :value="viewingDept.name + ' (' + viewingDept.code + ')'" disabled class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-500 bg-slate-50" />
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Select Instructor</label>
                <div class="relative mb-3">
                  <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                  <input v-model="headSearchQuery" type="text" placeholder="Search instructor..." class="w-full pl-9 pr-4 py-2.5 border border-slate-200 rounded-lg text-[13px] focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400" />
                  <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
                <div class="border border-slate-200 rounded-lg overflow-hidden max-h-[200px] overflow-y-auto">
                  <div v-for="inst in mockInstructors" :key="inst.email" class="flex items-center gap-3 px-4 py-3 hover:bg-indigo-50 cursor-pointer transition-colors border-b border-slate-50 last:border-b-0" :class="inst.current ? 'bg-indigo-50' : ''">
                    <img :src="getAvatarUrl(inst.name)" :alt="inst.name" class="w-9 h-9 rounded-full border-2 border-slate-100" />
                    <div class="flex-1">
                      <p class="text-[13px] font-semibold text-slate-700">{{ inst.name }}</p>
                      <p class="text-[11px] text-slate-400">{{ inst.email }}</p>
                    </div>
                    <span v-if="inst.current" class="text-[10px] font-bold text-[#4338ca] bg-indigo-100 px-2 py-0.5 rounded-full">Current</span>
                  </div>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Department Head Since</label>
                <div class="relative">
                  <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  <input type="date" value="2025-05-27" class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-lg text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]" />
                </div>
              </div>
            </div>
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
              <button @click="showAssignHeadModal = false" class="px-5 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">Cancel</button>
              <button @click="showAssignHeadModal = false" class="px-5 py-2.5 text-[13px] font-bold text-white bg-emerald-500 hover:bg-emerald-600 rounded-xl shadow-sm transition-all">Assign Head</button>
            </div>
          </div>
        </div>
      </Teleport>

    </template>

    <!-- ==================== ADD DEPARTMENT FORM VIEW ==================== -->
    <template v-if="showAddForm && !showDetailView">
      
      <!-- Breadcrumbs -->
      <div class="flex items-center gap-2 text-[13px]">
        <button @click="showAddForm = false; resetAddForm()" class="text-slate-500 hover:text-[#4338ca] transition-colors">Departments</button>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span class="text-slate-700 font-semibold">Add Department</span>
      </div>

      <!-- Header -->
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Add Department</h1>
        <p class="text-[13px] text-slate-500 mt-1">Create a new academic department in the university.</p>
      </div>

      <!-- Department Information Section -->
      <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-8">
        <div class="flex items-center gap-3 mb-8">
          <div class="w-9 h-9 rounded-lg bg-indigo-50 text-[#4338ca] flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
          </div>
          <h2 class="text-[16px] font-bold text-slate-800">Department Information</h2>
        </div>

        <div class="space-y-6">
          <!-- Row 1: Name, Code, Faculty -->
          <div class="grid grid-cols-3 gap-6">
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Department Name <span class="text-rose-500">*</span></label>
              <input v-model="newDeptForm.name" type="text" placeholder="e.g., Computer Science" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400 transition-shadow" />
            </div>
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Department Code <span class="text-rose-500">*</span></label>
              <input v-model="newDeptForm.code" type="text" placeholder="e.g., CS" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 font-mono focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400 transition-shadow" />
            </div>
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Faculty <span class="text-rose-500">*</span></label>
              <div class="relative">
                <select v-model="newDeptForm.faculty" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] transition-shadow">
                  <option value="" disabled>Select faculty</option>
                  <option value="Computing & Informatics">Computing & Informatics</option>
                  <option value="Engineering">Engineering</option>
                  <option value="Natural Sciences">Natural Sciences</option>
                  <option value="Social Sciences">Social Sciences</option>
                  <option value="Business & Economics">Business & Economics</option>
                  <option value="Health Sciences">Health Sciences</option>
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
          </div>

          <!-- Row 2: College/School, Program Level, Department Type -->
          <div class="grid grid-cols-3 gap-6">
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">College/School <span class="text-rose-500">*</span></label>
              <div class="relative">
                <select v-model="newDeptForm.college" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] transition-shadow">
                  <option value="" disabled>Select college or school</option>
                  <option value="College of Computing and Informatics">College of Computing and Informatics</option>
                  <option value="College of Engineering and Technology">College of Engineering and Technology</option>
                  <option value="College of Natural Sciences">College of Natural Sciences</option>
                  <option value="College of Social Sciences">College of Social Sciences</option>
                  <option value="College of Business and Economics">College of Business and Economics</option>
                  <option value="College of Health Sciences">College of Health Sciences</option>
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Program Level</label>
              <input v-model="newDeptForm.programLevel" type="text" placeholder="Select program level" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400 transition-shadow" />
            </div>
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Department Type</label>
              <div class="relative">
                <select v-model="newDeptForm.departmentType" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] transition-shadow">
                  <option value="" disabled>Select department type</option>
                  <option value="Academic">Academic</option>
                  <option value="Research">Research</option>
                  <option value="Service">Service</option>
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
          </div>

          <!-- Row 3: Head, Email, Phone -->
          <div class="grid grid-cols-3 gap-6">
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Head of Department</label>
              <div class="relative">
                <select v-model="newDeptForm.headOfDepartment" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] transition-shadow">
                  <option value="" disabled>Select head of department</option>
                  <option value="Dr. Solomon Abate">Dr. Solomon Abate</option>
                  <option value="Dr. Abebe Kebede">Dr. Abebe Kebede</option>
                  <option value="Dr. Yeshimebet D.">Dr. Yeshimebet D.</option>
                  <option value="Dr. Getachew T.">Dr. Getachew T.</option>
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Email</label>
              <input v-model="newDeptForm.email" type="email" placeholder="e.g., cs@wu.edu.et" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400 transition-shadow" />
            </div>
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Phone</label>
              <input v-model="newDeptForm.phone" type="text" placeholder="e.g., +251 11 123 4567" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400 transition-shadow" />
            </div>
          </div>

          <!-- Row 4: Office Location, Description -->
          <div class="grid grid-cols-[1fr_2fr] gap-6">
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Office Location</label>
              <input v-model="newDeptForm.officeLocation" type="text" placeholder="e.g., Block C, Room 204" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400 transition-shadow" />
            </div>
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Description</label>
              <div class="relative">
                <textarea v-model="newDeptForm.description" maxlength="500" rows="3" placeholder="Enter department description..." class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400 transition-shadow resize-y"></textarea>
                <span class="absolute bottom-2 right-3 text-[11px] text-slate-400">{{ descriptionCharCount }} / 500</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Information Section -->
      <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-8">
        <div class="flex items-center gap-3 mb-8">
          <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
          </div>
          <h2 class="text-[16px] font-bold text-slate-800">Additional Information</h2>
        </div>

        <div class="space-y-6">
          <!-- Row 1: Established Date, Status, Display Order -->
          <div class="grid grid-cols-3 gap-6">
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Established Date</label>
              <div class="relative">
                <input v-model="newDeptForm.establishedDate" type="date" placeholder="Select date" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400 transition-shadow" />
              </div>
            </div>
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Status <span class="text-rose-500">*</span></label>
              <div class="relative">
                <select v-model="newDeptForm.status" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] transition-shadow">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Display Order</label>
              <input v-model.number="newDeptForm.displayOrder" type="number" min="0" placeholder="0" class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400 transition-shadow" />
              <p class="text-[11px] text-slate-400 mt-1 italic">Lower numbers appear first</p>
            </div>
          </div>

          <!-- Row 2: Logo Upload, Vision, Mission -->
          <div class="grid grid-cols-3 gap-6">
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Department Logo (Optional)</label>
              <div
                @dragover="handleDragOver"
                @dragleave="handleDragLeave"
                @drop="handleDrop"
                :class="[isDragging ? 'border-[#4338ca] bg-indigo-50' : 'border-slate-200 bg-white', 'border-2 border-dashed rounded-lg p-6 text-center cursor-pointer hover:border-[#4338ca] hover:bg-indigo-50/30 transition-all']"
                @click="($refs.fileInput as HTMLInputElement)?.click()"
              >
                <input ref="fileInput" type="file" accept=".png,.jpg,.jpeg,.svg" class="hidden" @change="handleFileSelect" />
                <div v-if="logoPreview" class="flex flex-col items-center gap-2">
                  <img :src="logoPreview" alt="Logo preview" class="w-16 h-16 object-contain rounded-lg" />
                  <p class="text-[11px] text-slate-500">{{ logoFile?.name }}</p>
                </div>
                <div v-else class="flex flex-col items-center gap-2">
                  <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                  </div>
                  <div>
                    <p class="text-[12px] text-slate-600"><span class="font-bold text-slate-700">Click to upload</span> or drag and drop</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">PNG, JPG or SVG (max. 2MB)</p>
                  </div>
                </div>
              </div>
            </div>
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Vision (Optional)</label>
              <div class="relative">
                <textarea v-model="newDeptForm.vision" maxlength="300" rows="4" placeholder="Enter department vision..." class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400 transition-shadow resize-y"></textarea>
                <span class="absolute bottom-2 right-3 text-[11px] text-slate-400">{{ visionCharCount }} / 300</span>
              </div>
            </div>
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Mission (Optional)</label>
              <div class="relative">
                <textarea v-model="newDeptForm.mission" maxlength="300" rows="4" placeholder="Enter department mission..." class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400 transition-shadow resize-y"></textarea>
                <span class="absolute bottom-2 right-3 text-[11px] text-slate-400">{{ missionCharCount }} / 300</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Actions -->
      <div class="flex items-center justify-end gap-3 pt-2 pb-4">
        <button @click="showAddForm = false; resetAddForm()" class="px-6 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
          Cancel
        </button>
        <button @click="saveDepartment" :disabled="isLoading" class="flex items-center gap-2 px-6 py-2.5 text-[13px] font-bold text-white bg-[#4338ca] hover:bg-indigo-700 rounded-lg shadow-sm shadow-indigo-200 transition-all disabled:opacity-70 disabled:cursor-not-allowed">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
          {{ isLoading ? 'Saving...' : 'Save Department' }}
        </button>
      </div>

    </template>

    <!-- ==================== DEPARTMENTS LIST VIEW ==================== -->
    <template v-if="!showAddForm && !showDetailView">

      <!-- Header -->
      <div class="flex items-start justify-between">
        <div>
          <h1 class="text-[22px] font-bold text-slate-800">Departments</h1>
          <p class="text-[13px] text-slate-500 mt-1">View and manage all academic departments in the university.</p>
        </div>
        <button @click="showAddForm = true; resetAddForm()" class="flex items-center gap-2 bg-[#4338ca] hover:bg-indigo-700 text-white text-[13px] font-bold px-5 py-2.5 rounded-lg shadow-sm shadow-indigo-200 transition-all">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Add Department
        </button>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-5 gap-4">
        <div v-for="(item, i) in [
          { label:'TOTAL DEPARTMENTS', val: stats.total,       bg:'bg-indigo-50', ic:'text-[#4338ca]',  icon:'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', borderColor:'border-indigo-100' },
          { label:'ACTIVE DEPARTMENTS', val: stats.active,      bg:'bg-emerald-50',ic:'text-emerald-500', icon:'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', borderColor:'border-emerald-100' },
          { label:'TOTAL STUDENTS',     val: stats.students.toLocaleString(),    bg:'bg-green-50',  ic:'text-green-500',   icon:'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', borderColor:'border-green-100' },
          { label:'TOTAL INSTRUCTORS',  val: stats.instructors, bg:'bg-blue-50',   ic:'text-blue-500',    icon:'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', borderColor:'border-blue-100' },
          { label:'TOTAL COURSES',      val: stats.courses,     bg:'bg-purple-50', ic:'text-purple-500',  icon:'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', borderColor:'border-purple-100' },
        ]" :key="i" :class="[item.borderColor, 'bg-white border rounded-xl shadow-sm p-5 flex items-center gap-4']">
          <div :class="[item.bg, 'w-11 h-11 rounded-xl flex items-center justify-center shrink-0']">
            <svg class="w-5 h-5" :class="item.ic" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"></path></svg>
          </div>
          <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ item.label }}</p>
            <p class="text-[22px] font-bold text-slate-800 mt-0.5">{{ item.val }}</p>
          </div>
        </div>
      </div>

      <!-- Table Card -->
      <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        
        <!-- Search & Filters -->
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100">
          <div class="relative flex-1 max-w-[420px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input v-model="search" placeholder="Search departments by name or code..." class="w-full pl-9 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-lg focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] placeholder:text-slate-400">
          </div>
          
          <div class="relative">
            <select v-model="facultyFilter" class="pl-4 pr-8 py-2.5 text-[13px] border border-slate-200 rounded-lg text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] w-[180px]">
              <option value="all">All Faculties</option>
              <option v-for="f in faculties" :key="f" :value="f">{{ f }}</option>
            </select>
            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
          
          <div class="relative">
            <select v-model="statusFilter" class="pl-4 pr-8 py-2.5 text-[13px] border border-slate-200 rounded-lg text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] w-[140px]">
              <option value="all">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
          
          <div class="relative">
            <select v-model="sortBy" class="pl-4 pr-8 py-2.5 text-[13px] border border-slate-200 rounded-lg text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] w-[150px]">
              <option value="name">Sort by Name</option>
              <option value="students">Sort by Students</option>
              <option value="code">Sort by Code</option>
            </select>
            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
          
          <button class="flex items-center gap-2 px-4 py-2.5 border border-slate-200 rounded-lg text-[13px] text-slate-600 hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Filter
          </button>
        </div>

        <!-- Table -->
        <table class="w-full">
          <thead>
            <tr class="border-b border-slate-100">
              <th class="text-left px-6 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Department</th>
              <th class="text-left px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Department Code</th>
              <th class="text-left px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Faculty</th>
              <th class="text-left px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Head of Department</th>
              <th class="text-center px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Students</th>
              <th class="text-center px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Instructors</th>
              <th class="text-center px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Courses</th>
              <th class="text-center px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
              <th class="text-center px-4 py-3.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="dept in paginated" :key="dept.id" class="border-b border-slate-50 hover:bg-slate-50/60 transition-colors group">
              <!-- Department Name -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-indigo-50 text-[#4338ca] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                  </div>
                  <div>
                    <p class="text-[13px] font-bold text-slate-800 leading-snug">{{ dept.name }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ dept.subLabel }}</p>
                  </div>
                </div>
              </td>
              
              <!-- Department Code -->
              <td class="px-4 py-4">
                <span class="text-[13px] font-semibold font-mono text-slate-700">{{ dept.code }}</span>
              </td>
              
              <!-- Faculty -->
              <td class="px-4 py-4">
                <p class="text-[12px] text-slate-600 leading-snug max-w-[160px]">{{ dept.faculty }}</p>
              </td>
              
              <!-- Head of Department -->
              <td class="px-4 py-4">
                <div class="flex items-center gap-3">
                  <img :src="getAvatarUrl(dept.head)" :alt="dept.head" class="w-9 h-9 rounded-full object-cover border-2 border-slate-100" />
                  <div>
                    <p class="text-[12px] font-semibold text-slate-700">{{ dept.head }}</p>
                    <p class="text-[11px] text-slate-400">{{ dept.headEmail }}</p>
                  </div>
                </div>
              </td>
              
              <!-- Students -->
              <td class="px-4 py-4 text-center"><span class="text-[13px] font-bold text-slate-800">{{ dept.students }}</span></td>
              
              <!-- Instructors -->
              <td class="px-4 py-4 text-center"><span class="text-[13px] font-bold text-slate-800">{{ dept.instructors }}</span></td>
              
              <!-- Courses -->
              <td class="px-4 py-4 text-center"><span class="text-[13px] font-bold text-slate-800">{{ dept.courses }}</span></td>
              
              <!-- Status -->
              <td class="px-4 py-4 text-center">
                <span :class="[dept.status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500', 'text-[11px] font-bold px-2.5 py-1 rounded-full capitalize']">
                  {{ dept.status }}
                </span>
              </td>
              
              <!-- Actions -->
              <td class="px-4 py-4">
                <div class="flex items-center justify-center gap-1">
                  <button @click="viewDeptDetail(dept)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-emerald-500 hover:bg-emerald-50 transition-colors" title="View Details">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                  </button>
                  <button @click="openEditModal(dept)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-[#4338ca] hover:bg-indigo-50 transition-colors" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                  </button>
                  <button @click="confirmDelete(dept)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors" title="More">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                  </button>
                </div>
              </td>
            </tr>
            
            <!-- Empty State -->
            <tr v-if="paginated.length === 0">
              <td colspan="9" class="py-16 text-center">
                <div class="flex flex-col items-center gap-3">
                  <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center"><svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>
                  <p class="text-[14px] font-bold text-slate-600">No departments found</p>
                  <p class="text-[12px] text-slate-400">Try adjusting your search or filter criteria.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
          <p class="text-[13px] text-slate-500">Showing {{ Math.min((currentPage-1)*perPage+1, filtered.length) }} to {{ Math.min(currentPage*perPage, filtered.length) }} of {{ filtered.length }} departments</p>
          <div class="flex items-center gap-1">
            <button @click="currentPage = Math.max(1, currentPage-1)" :disabled="currentPage===1" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-500 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button v-for="p in totalPages" :key="p" @click="currentPage = p" :class="[currentPage===p?'bg-[#4338ca] text-white shadow-sm':'text-slate-600 border border-slate-200 hover:bg-slate-50','w-8 h-8 rounded-lg flex items-center justify-center text-[13px] font-semibold transition-colors']">{{ p }}</button>
            <button @click="currentPage = Math.min(totalPages, currentPage+1)" :disabled="currentPage===totalPages || totalPages===0" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-500 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
          </div>
        </div>
      </div>

    </template>

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
            <button @click="deleteDept" :disabled="isLoading" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors disabled:opacity-70 disabled:cursor-not-allowed">
              {{ isLoading ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Edit Modal -->
    <Teleport to="body">
      <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
          <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div><h3 class="text-[16px] font-bold text-slate-800">Edit Department</h3><p class="text-[12px] text-slate-500 mt-0.5">Update department details.</p></div>
            <button @click="showEditModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
          </div>
          <div class="px-6 py-5 space-y-4">
            <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Department Name <span class="text-rose-500">*</span></label><input v-model="editData.name" type="text" placeholder="e.g. Computer Science" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]"></div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Dept Code <span class="text-rose-500">*</span></label><input v-model="editData.code" type="text" placeholder="e.g. CS" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] font-mono focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]"></div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Established Year</label><input v-model="editData.established" type="number" placeholder="e.g. 2010" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]"></div>
            </div>
          </div>
          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            <button @click="showEditModal = false" class="px-5 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">Cancel</button>
            <button @click="updateDept" :disabled="isLoading" class="px-5 py-2.5 text-[13px] font-bold text-white bg-[#4338ca] hover:bg-indigo-700 rounded-xl shadow-sm transition-all disabled:opacity-70 disabled:cursor-not-allowed">
              {{ isLoading ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>
