<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import apiClient from '../../../core/api/apiClient'
import { useSettingsStore } from '../../../store/settingsStore'
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Doughnut } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend)

const search = ref('')
const deptFilter = ref('all')
const statusFilter = ref('all')
const showAddPage = ref(false)
const showDetailsPage = ref(false)
const isEditing = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const showAssignModal = ref(false)
const selectedCourse = ref<any>(null)
const courseToAssign = ref<any>(null)
const assignSection = ref('')
const assignInstructorId = ref('')
const assignCoInstructorId = ref('')
const isLoading = ref(false)

const sectionOptions = ['All Sections', 'Section A', 'Section B', 'Section C', 'Section D', 'Section E']

const currentInstructorInfo = computed(() => {
  if (!courseToAssign.value?.instructor) return null
  const name = courseToAssign.value.instructor?.name || courseToAssign.value.instructor
  const section = courseToAssign.value.section || ''
  const title = courseToAssign.value.name || courseToAssign.value.title || ''
  if (!name || name === 'Unassigned') return null
  return `${name} is the current${section ? ' ' + section : ''} ${title} instructor`
})

const currentCoInstructorInfo = computed(() => {
  if (!courseToAssign.value?.co_instructor_id) return null
  const coInst = allInstructors.value.find(i => i.id === courseToAssign.value.co_instructor_id)
  if (!coInst) return null
  const name = coInst.name
  const section = courseToAssign.value.section || ''
  const title = courseToAssign.value.name || courseToAssign.value.title || ''
  return `${name} is the${section ? ' ' + section : ''} ${title} co-instructor`
})


const settingsStore = useSettingsStore()

const allCourses = ref<any[]>([])
const allDepartments = ref<any[]>([])
const allInstructors = ref<any[]>([])

const newCourseForm = ref({
  code: '',
  title: '',
  type: '',
  department_id: '',
  program: '',
  level: '',
  semester: '',
  credits: '',
  language: '',
  short_description: '',
  full_description: '',
  instructor_id: '',
  co_instructors: '',
  capacity: '',
  enrollment_status: 'Open for Enrollment',
  visibility: 'Visible to Students',
  start_date: '',
  end_date: '',
  status: 'active'
})

const currentPage = ref(1)

const fetchCourses = async () => {
  try {
    const res = await apiClient.get('/admin/courses')
    allCourses.value = (res.data.data || []).map((c: any) => ({
      ...c,
      name: c.title,
      dept: c.department?.name || '—',
      departmentName: c.department?.name || '—',
      instructor: c.instructor?.name || 'Unassigned',
      instructorsCount: c.instructor ? 1 : 0, 
      students: 0, 
      exams: 0,
      status: c.status || 'active',
      semester: c.semester,
      credits: c.credits || '—',
      level: c.level,
      created_by: c.creator?.role === 'dept_head' || c.creator?.role === 'department_head' ? 'Dept. Head' : (c.creator?.role === 'admin' ? 'Admin' : 'Unknown'),
      created_on: new Date(c.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
    }))
  } catch (err) { console.error('Failed to fetch courses:', err) }
}

const fetchDepartments = async () => {
  try {
    const res = await apiClient.get('/admin/departments')
    allDepartments.value = res.data.data || []
  } catch (err) { console.error('Failed to fetch departments:', err) }
}

const fetchInstructors = async () => {
  try {
    const res = await apiClient.get('/admin/instructors')
    allInstructors.value = res.data.data || []
  } catch (err) { console.error('Failed to fetch instructors:', err) }
}

onMounted(async () => {
  await Promise.all([fetchCourses(), fetchDepartments(), fetchInstructors()])
})

const formAvailableInstructors = computed(() => {
  if (!newCourseForm.value.department_id) return []
  return allInstructors.value.filter(i => i.department_id === newCourseForm.value.department_id)
})

const instructorsForAssign = computed(() => {
  if (!courseToAssign.value?.department_id) return []
  return allInstructors.value.filter(i => i.department_id === courseToAssign.value.department_id)
})

const availableInstructors = computed(() => {
  return instructorsForAssign.value.filter(inst => inst.id !== assignCoInstructorId.value)
})

const availableCoInstructors = computed(() => {
  return instructorsForAssign.value.filter(inst => inst.id !== assignInstructorId.value)
})

const levelFilter = ref('all')

const filtered = computed(() =>
  allCourses.value.filter(c => {
    const matchSearch = (c.name && c.name.toLowerCase().includes(search.value.toLowerCase())) ||
                        (c.code && c.code.toLowerCase().includes(search.value.toLowerCase())) ||
                        (c.instructor && c.instructor.toLowerCase().includes(search.value.toLowerCase()))
    const matchDept = deptFilter.value === 'all' || c.departmentName === deptFilter.value
    const matchStatus = statusFilter.value === 'all' || c.status === statusFilter.value
    const matchLevel = levelFilter.value === 'all' || c.level === levelFilter.value
    return matchSearch && matchDept && matchStatus && matchLevel
  })
)

const paginated = computed(() => {
  const start = (currentPage.value - 1) * 10
  return filtered.value.slice(start, start + 10)
})

const totalPages = computed(() => Math.ceil(filtered.value.length / 10))

const stats = computed(() => {
  const total = allCourses.value.length || 0
  const active = allCourses.value.filter(c => c.status === 'active').length || 0
  return {
    total,
    active,
    inactive: total - active,
    newCourses: allCourses.value.filter(c => {
      const createdDate = new Date(c.created_at || c.created_on)
      const now = new Date()
      return (now.getTime() - createdDate.getTime()) < 30 * 24 * 60 * 60 * 1000
    }).length,
    published: active 
  }
})


const openAddPage = () => {
  isEditing.value = false
  newCourseForm.value = {
    code: '', title: '', type: '', department_id: '', program: '', level: '', semester: settingsStore.semester,
    credits: '', language: '', short_description: '', full_description: '',
    instructor_id: '', co_instructors: '', capacity: '', enrollment_status: 'Open for Enrollment',
    visibility: 'Visible to Students', start_date: '', end_date: '', status: 'active'
  }
  showAddPage.value = true
}

const openEditPage = (c: any) => {
  isEditing.value = true
  selectedCourse.value = c
  newCourseForm.value = {
    ...newCourseForm.value,
    code: c.code || '', title: c.name || '', department_id: c.department_id || '',
    semester: c.semester || settingsStore.semester, level: c.level || '', credits: c.credits || '', instructor_id: c.instructor_id || '',
    enrollment_status: c.enrollment_status || 'Open for Enrollment', visibility: c.visibility || 'Visible to Students',
    start_date: c.start_date || '', end_date: c.end_date || '', status: c.status || 'active'
  }
  showEditModal.value = true
}

const openDetailsPage = (c: any) => {
  selectedCourse.value = c
  showDetailsPage.value = true
}

const saveCourse = async () => {
  if (!newCourseForm.value.title || !newCourseForm.value.code || !newCourseForm.value.department_id) return
  isLoading.value = true
  try {
    if (isEditing.value && selectedCourse.value) {
      await apiClient.put(`/admin/courses/${selectedCourse.value.id}`, {
        title: newCourseForm.value.title,
        code: newCourseForm.value.code,
        credits: newCourseForm.value.credits || 3,
        department_id: newCourseForm.value.department_id,
        instructor_id: newCourseForm.value.instructor_id || null,
        semester: newCourseForm.value.semester,
        level: newCourseForm.value.level,
        start_date: newCourseForm.value.start_date,
        end_date: newCourseForm.value.end_date,
        visibility: newCourseForm.value.visibility,
        enrollment_status: newCourseForm.value.enrollment_status,
        status: newCourseForm.value.status
      })
    } else {
      await apiClient.post('/admin/courses', {
        title: newCourseForm.value.title,
        code: newCourseForm.value.code,
        credits: newCourseForm.value.credits || 3,
        department_id: newCourseForm.value.department_id,
        instructor_id: newCourseForm.value.instructor_id || null,
        semester: newCourseForm.value.semester,
        level: newCourseForm.value.level,
        start_date: newCourseForm.value.start_date,
        end_date: newCourseForm.value.end_date,
        visibility: newCourseForm.value.visibility,
        enrollment_status: newCourseForm.value.enrollment_status,
        status: newCourseForm.value.status || 'active'
      })
    }
    await fetchCourses()
    showAddPage.value = false
    showEditModal.value = false
  } catch (err: any) {
    let msg = isEditing.value ? 'Failed to update course.' : 'Failed to create course.'
    if (err.response?.data?.message) {
      msg = err.response.data.message
      if (err.response.data.errors) {
        msg += '\n' + Object.values(err.response.data.errors).flat().join('\n')
      }
    }
    alert(msg)
  } finally {
    isLoading.value = false
  }
}

const confirmDelete = (c: any) => { selectedCourse.value = c; showDeleteModal.value = true }
const deleteCourse  = async () => {
  if (!selectedCourse.value) return
  isLoading.value = true
  try {
    await apiClient.delete(`/admin/courses/${selectedCourse.value.id}`)
    await fetchCourses()
    showDeleteModal.value = false
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete course.')
  } finally {
    isLoading.value = false
  }
}


const openAssign = (course: any) => {
  courseToAssign.value = course
  assignSection.value = course.section || ''
  assignInstructorId.value = course.instructor_id || ''
  assignCoInstructorId.value = course.co_instructor_id || ''
  showAssignModal.value = true
}

const assignInstructor = async () => {
  if (!courseToAssign.value) return
  isLoading.value = true
  try {
    await apiClient.put(`/admin/courses/${courseToAssign.value.id}`, {
      instructor_id: assignInstructorId.value || null,
      co_instructor_id: assignCoInstructorId.value || null,
      section: assignSection.value === 'All Sections' ? null : assignSection.value || null
    })
    await fetchCourses()
    showAssignModal.value = false
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to assign instructor.')
  } finally {
    isLoading.value = false
  }
}

const chartData = {
  labels: ['Computer Science', 'Software Engineering', 'Information Systems', 'ICT', 'Others'],
  datasets: [{
    backgroundColor: ['#4338ca', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444'],
    data: [38, 28, 20, 18, 20],
    borderWidth: 0,
    hoverOffset: 4
  }]
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '75%',
  plugins: {
    legend: {
      display: false // Hide legend to prevent it from being cut off in narrow sidebar
    },
    tooltip: { enabled: true }
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
      await apiClient.post('/admin/courses-import', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      alert(`Successfully imported ${file.name}`)
      await fetchCourses()
    } catch (err: any) { 
        alert(err.response?.data?.message || 'Failed to import courses') 
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
    const res = await apiClient.get(`/admin/courses-export?format=${format}`, { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([res.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `courses_export_${new Date().toISOString().slice(0,10)}.${format === 'pdf' ? 'pdf' : 'csv'}`);
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (err: any) {
    if (err.response && err.response.data && err.response.data instanceof Blob) {
      const text = await err.response.data.text();
      try {
        const json = JSON.parse(text);
        alert(json.message || 'Failed to export courses');
      } catch (e) {
        alert('Failed to export courses');
      }
    } else {
      alert('Failed to export courses');
    }
  } finally {
    isLoading.value = false;
  }
}
</script>

<template>
  <div class="w-full">
    <!-- List View -->
    <div v-if="!showAddPage && !showDetailsPage" class="space-y-6 min-w-0 w-full">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <h1 class="text-[24px] font-bold text-slate-800">Courses</h1>
          </div>
          <p class="text-[13px] text-slate-500 ml-[52px]">Manage all courses and their information across the system.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
          <input type="file" ref="importFileInput" @change="handleImport" accept=".csv" class="hidden">
          <button @click="triggerImport" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-[#4338ca] font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Import Courses
          </button>
          
          <div class="relative">
            <button @click="showExportDropdown = !showExportDropdown" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-[#4338ca] font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm whitespace-nowrap">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg> Export Courses
            </button>
            <div v-if="showExportDropdown" class="absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-50">
              <button @click="handleExport('csv')" class="w-full text-left px-4 py-2 text-[13px] text-slate-600 hover:bg-slate-50 hover:text-[#4338ca] transition-colors">Export as CSV</button>
              <button @click="handleExport('pdf')" class="w-full text-left px-4 py-2 text-[13px] text-slate-600 hover:bg-slate-50 hover:text-[#4338ca] transition-colors">Export as PDF</button>
            </div>
          </div>
          <button @click="openAddPage" class="flex items-center gap-2 px-4 py-2.5 bg-[#4338ca] text-white font-bold rounded-xl text-[13px] hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Add New Course
          </button>
        </div>
      </div>

    <!-- 5 Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
      <div v-for="(item, i) in [
        { label:'Total Courses', val: stats.total,      icon:'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z', bg:'bg-indigo-50', ic:'text-[#4338ca]', trend:'↑ 5.2%', tc:'text-emerald-500', sub:'All courses' },
        { label:'Active Courses',val: stats.active,     icon:'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', bg:'bg-emerald-50',ic:'text-emerald-500', trend:'↑ 6.7%', tc:'text-emerald-500', sub:'Active courses'},
        { label:'Inactive Courses',val: stats.inactive, icon:'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', bg:'bg-rose-50', ic:'text-rose-500', trend:'↓ 7.7%', tc:'text-rose-500', sub:'Inactive courses'},
        { label:'New Courses',   val: stats.newCourses, icon:'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z', bg:'bg-sky-50', ic:'text-sky-500', trend:'↑ 14.3%', tc:'text-emerald-500', sub:'This month'},
        { label:'Published Courses',val: stats.published, icon:'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', bg:'bg-amber-50', ic:'text-amber-500', trend:'↑ 4.3%', tc:'text-emerald-500', sub:'Published courses'},
      ]" :key="i" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex flex-col justify-between">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-[11px] font-semibold text-slate-500 tracking-wide">{{ item.label }}</p>
            <p class="text-[24px] font-bold text-slate-800 mt-1">{{ item.val }}</p>
          </div>
          <div :class="[item.bg, 'w-10 h-10 rounded-xl flex items-center justify-center shrink-0']">
            <svg class="w-5 h-5" :class="item.ic" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"></path></svg>
          </div>
        </div>
        <div class="flex items-center gap-2 mt-4 text-[11px]">
          <span :class="item.tc" class="font-bold">{{ item.trend }}</span>
          <span class="text-slate-400">{{ item.sub }}</span>
        </div>
      </div>
    </div>

    <!-- Main Layout: Table Full Width + Bottom Cards -->
    <div class="flex flex-col gap-6 w-full">
      
      <!-- Top: Table Area -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm flex flex-col min-w-0 overflow-hidden w-full">
        <!-- Table Toolbar -->
        <div class="flex flex-wrap items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
          <div class="relative flex-1 max-w-[200px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input v-model="search" placeholder="Search Courses..." class="w-full pl-9 pr-4 py-2 text-[12px] border border-slate-200 rounded-lg focus:outline-none focus:border-[#4338ca] bg-white">
          </div>
          <select v-model="deptFilter" class="text-[12px] border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:border-[#4338ca] text-slate-600 bg-white">
            <option value="all">All Departments</option>
            <option v-for="d in allDepartments" :key="d.id" :value="d.name">{{ d.name }}</option>
          </select>
          <select v-model="statusFilter" class="text-[12px] border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:border-[#4338ca] text-slate-600 bg-white">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <select v-model="levelFilter" class="text-[12px] border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:border-[#4338ca] text-slate-600 bg-white">
            <option value="all">All Levels</option>
            <option value="1st Year">1st Year</option>
            <option value="2nd Year">2nd Year</option>
            <option value="3rd Year">3rd Year</option>
            <option value="4th Year">4th Year</option>
            <option value="5th Year">5th Year</option>
          </select>
          <input type="text" disabled :value="settingsStore.semester" class="w-32 text-[12px] border border-slate-200 rounded-lg px-3 py-2 bg-slate-50 text-slate-500 cursor-not-allowed text-center font-bold">
          <button class="flex items-center gap-1.5 px-3 py-2 text-[12px] font-bold text-[#4338ca] border border-indigo-100 bg-white rounded-lg hover:bg-indigo-50 ml-auto">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Filter
          </button>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto flex-1 min-w-0">
          <table class="w-full text-left whitespace-nowrap min-w-max">
            <thead>
              <tr class="border-b border-slate-100 bg-white">
                <th class="px-5 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Course Code</th>
                <th class="px-5 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Course Title</th>
                <th class="px-5 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Department</th>
                <th class="px-5 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Level</th>
                <th class="px-5 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Instructors</th>
                <th class="px-5 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Credits</th>
                <th class="px-5 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Status</th>
                <th class="px-5 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Created By</th>
                <th class="px-5 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="course in paginated" :key="course.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors group">
                <td class="px-5 py-4">
                  <span class="text-[11px] font-bold text-[#4338ca] bg-indigo-50 px-2.5 py-1 rounded-md tracking-wide">{{ course.code }}</span>
                </td>
                <td class="px-5 py-4">
                  <p class="text-[13px] font-bold text-slate-800">{{ course.name }}</p>
                </td>
                <td class="px-5 py-4">
                  <p class="text-[12px] text-slate-600">{{ course.departmentName }}</p>
                </td>
                <td class="px-5 py-4">
                  <p class="text-[12px] text-slate-600">{{ course.level || '—' }}</p>
                </td>
                <td class="px-5 py-4 text-center">
                  <p class="text-[12px] font-semibold text-slate-700">{{ course.instructorsCount }}</p>
                </td>
                <td class="px-5 py-4 text-center">
                  <p class="text-[12px] font-semibold text-slate-700">{{ course.credits }}</p>
                </td>
                <td class="px-5 py-4 text-center">
                  <span :class="course.status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-500'" class="text-[10px] font-bold px-2.5 py-1 rounded-lg capitalize">{{ course.status }}</span>
                </td>
                <td class="px-5 py-4 text-center">
                  <span class="text-[11px] font-bold text-sky-600 bg-sky-50 px-2.5 py-1 rounded-md">{{ course.created_by }}</span>
                </td>
                <td class="px-5 py-4">
                  <div class="flex items-center justify-center gap-1.5 transition-opacity">
                    <button @click="openAssign(course)" class="w-7 h-7 rounded bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-100 transition-colors" title="Assign Instructor"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg></button>
                    <button @click="openDetailsPage(course)" class="w-7 h-7 rounded bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 transition-colors" title="View Details"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                    <button @click="openEditPage(course)" class="w-7 h-7 rounded bg-indigo-50 text-[#4338ca] flex items-center justify-center hover:bg-indigo-100 transition-colors" title="Edit"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                    <button @click="confirmDelete(course)" class="w-7 h-7 rounded bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-100 transition-colors" title="Delete"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                  </div>
                </td>
              </tr>
              <tr v-if="filtered.length === 0">
                <td colspan="9" class="px-5 py-16 text-center text-slate-500 text-[13px]">No courses found matching your criteria.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
          <p class="text-[12px] text-slate-500">Showing <span class="font-bold text-slate-700">{{ (currentPage-1)*10 + 1 }}</span> to <span class="font-bold text-slate-700">{{ Math.min(currentPage*10, filtered.length) }}</span> of <span class="font-bold text-slate-700">{{ filtered.length }}</span> courses</p>
          <div class="flex items-center gap-1.5">
            <button @click="currentPage--" :disabled="currentPage===1" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button v-for="p in totalPages" :key="p" @click="currentPage = p" :class="[currentPage===p ? 'bg-[#4338ca] text-white border-transparent' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50', 'w-8 h-8 flex items-center justify-center rounded-lg border text-[12px] font-bold transition-colors']">{{ p }}</button>
            <button @click="currentPage++" :disabled="currentPage===totalPages || totalPages===0" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Bottom: Dashboard Cards (was Sidebar) -->
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 w-full">
        <!-- Course Overview -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
          <h3 class="text-[14px] font-bold text-slate-800 mb-6">Course Overview</h3>
          <div class="relative h-48 w-full flex items-center justify-center">
            <Doughnut :data="chartData" :options="chartOptions" />
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-2">
              <span class="text-[24px] font-black text-slate-800 leading-none">124</span>
              <span class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-wide">Total Courses</span>
            </div>
          </div>
          <!-- HTML Legend -->
          <div class="grid grid-cols-2 gap-2 mt-6">
            <div v-for="(label, i) in chartData.labels" :key="i" class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full shrink-0" :style="{ backgroundColor: chartData.datasets[0].backgroundColor[i] }"></span>
              <span class="text-[10px] text-slate-600 truncate" :title="label">{{ label }}</span>
            </div>
          </div>
        </div>

        <!-- Top Departments -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
          <h3 class="text-[14px] font-bold text-slate-800 mb-4">Top Departments</h3>
          <div class="space-y-3">
            <div v-for="(dept, i) in [
              { name: 'Computer Science', count: 38 },
              { name: 'Software Engineering', count: 28 },
              { name: 'Information Systems', count: 20 },
              { name: 'ICT', count: 18 },
              { name: 'Database Systems', count: 12 },
            ]" :key="i" class="flex items-center justify-between text-[12px]">
              <div class="flex items-center gap-2 text-slate-600">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                {{ dept.name }}
              </div>
              <span class="font-bold text-slate-800 bg-slate-50 px-2 py-0.5 rounded">{{ dept.count }}</span>
            </div>
          </div>
          <button class="w-full text-center text-[11px] font-bold text-[#4338ca] hover:text-indigo-800 mt-4 transition-colors">View All</button>
        </div>

        <!-- Recent Courses -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
          <h3 class="text-[14px] font-bold text-slate-800 mb-4">Recent Courses</h3>
          <div class="space-y-4">
            <div v-for="(course, i) in [
              { name: 'Machine Learning', date: 'May 27, 2025' },
              { name: 'Mobile App Development', date: 'May 26, 2025' },
              { name: 'Cyber Security', date: 'May 25, 2025' },
            ]" :key="i" class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-[12px] font-bold text-slate-800 truncate">{{ course.name }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">{{ course.date }}</p>
              </div>
            </div>
          </div>
          <button class="w-full text-center text-[11px] font-bold text-[#4338ca] hover:text-indigo-800 mt-4 transition-colors">View All</button>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
          <h3 class="text-[14px] font-bold text-slate-800 mb-4">Quick Actions</h3>
          <div class="grid grid-cols-2 gap-3">
            <button @click="openAddPage" class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50 transition-colors text-[#4338ca]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
              <span class="text-[10px] font-bold text-center">Add New<br>Course</span>
            </button>
            <button class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50 transition-colors text-slate-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
              <span class="text-[10px] font-bold text-center">Import<br>Courses</span>
            </button>
            <button class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50 transition-colors text-slate-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
              <span class="text-[10px] font-bold text-center">Export<br>Courses</span>
            </button>
            <button class="flex flex-col items-center justify-center gap-2 p-3 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50 transition-colors text-slate-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
              <span class="text-[10px] font-bold text-center">Manage<br>Departments</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    </div> <!-- Close List View -->

    <!-- Add Course Form (Full Page) -->
    <div v-if="showAddPage" class="space-y-6 pb-12 w-full min-w-0">
      <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
          </div>
          <div>
            <h1 class="text-[24px] font-bold text-slate-800 leading-tight">{{ isEditing ? 'Edit Course' : 'Add New Course' }}</h1>
            <p class="text-[13px] text-slate-500 mt-0.5">{{ isEditing ? 'Update the course information.' : 'Create a new course and set all the necessary information.' }}</p>
            <div class="flex items-center gap-2 text-[11px] font-bold text-slate-400 mt-1">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
              <span>Courses</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              <span class="text-slate-600">{{ isEditing ? 'Edit Course' : 'Add New Course' }}</span>
            </div>
          </div>
        </div>
        <button @click="showAddPage = false" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Courses
        </button>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-[1fr_320px] gap-6 items-start">
        <!-- Main Form Area -->
        <div class="space-y-6">
          
          <!-- Course Information -->
          <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
            <h2 class="text-[15px] font-bold text-slate-800 mb-6">Course Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Course Code <span class="text-rose-500">*</span></label>
                <input v-model="newCourseForm.code" type="text" placeholder="Enter course code (e.g., CS-301)" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Course Title <span class="text-rose-500">*</span></label>
                <input v-model="newCourseForm.title" type="text" placeholder="Enter course title" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Department <span class="text-rose-500">*</span></label>
                <select v-model="newCourseForm.department_id" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] bg-white appearance-none">
                  <option value="">Select department</option>
                  <option v-for="d in allDepartments" :key="d.id" :value="d.id">{{ d.name }}</option>
                </select>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Academic Year Level <span class="text-rose-500">*</span></label>
                <select v-model="newCourseForm.level" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] bg-white appearance-none">
                  <option value="">Select Academic Year</option>
                  <option value="1st Year">1st Year</option>
                  <option value="2nd Year">2nd Year</option>
                  <option value="3rd Year">3rd Year</option>
                  <option value="4th Year">4th Year</option>
                  <option value="5th Year">5th Year</option>
                </select>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Semester <span class="text-rose-500">*</span></label>
                <input v-model="newCourseForm.semester" type="text" disabled class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-500 bg-slate-50 cursor-not-allowed">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Credits</label>
                <input v-model="newCourseForm.credits" type="number" placeholder="Enter credit hours" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]">
              </div>
            </div>
          </div>


          <!-- Course Settings -->
          <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
            <h2 class="text-[15px] font-bold text-slate-800 mb-6">Course Settings</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Enrollment Status</label>
                <select v-model="newCourseForm.enrollment_status" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] bg-white appearance-none">
                  <option value="Open for Enrollment">Open for Enrollment</option>
                  <option value="Closed">Closed</option>
                </select>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Visibility</label>
                <select v-model="newCourseForm.visibility" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] bg-white appearance-none">
                  <option value="Visible to Students">Visible to Students</option>
                  <option value="Hidden">Hidden</option>
                </select>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Start Date (Optional)</label>
                <input v-model="newCourseForm.start_date" type="date" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] bg-white appearance-none">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">End Date (Optional)</label>
                <input v-model="newCourseForm.end_date" type="date" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] bg-white appearance-none">
              </div>
            </div>
            
            <div class="mt-8 flex items-center justify-between border-t border-slate-100 pt-6">
              <button @click="showAddPage = false" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm">Cancel</button>
              <button @click="saveCourse" class="flex items-center gap-2 px-8 py-3 bg-[#4338ca] text-white font-bold rounded-xl text-[13px] hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                {{ isEditing ? 'Save Changes' : 'Create Course' }}
              </button>
            </div>
          </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
          <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-[14px] font-bold text-slate-800 mb-5">Course Summary</h3>
            <div class="flex items-start gap-4 mb-6 pb-6 border-b border-slate-50">
              <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
              </div>
              <div>
                <h4 class="text-[13px] font-bold text-slate-800">New Course</h4>
                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Course will be created with the details provided</p>
              </div>
            </div>
            <div class="space-y-4">
              <div class="flex items-center justify-between text-[12px]"><div class="flex items-center gap-2 text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>Course Code</div><span class="font-bold text-slate-800">{{ newCourseForm.code || 'Not Set' }}</span></div>
              <div class="flex items-center justify-between text-[12px]"><div class="flex items-center gap-2 text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>Course Title</div><span class="font-bold text-slate-800 truncate max-w-[120px]">{{ newCourseForm.title || 'Not Set' }}</span></div>
              <div class="flex items-center justify-between text-[12px]"><div class="flex items-center gap-2 text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>Department</div><span class="font-bold text-slate-800">{{ allDepartments.find(d => d.id === newCourseForm.department_id)?.name || 'Not Set' }}</span></div>
              <div class="flex items-center justify-between text-[12px]"><div class="flex items-center gap-2 text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Credits</div><span class="font-bold text-slate-800">{{ newCourseForm.credits || '0' }}</span></div>
              <div class="flex items-center justify-between text-[12px]"><div class="flex items-center gap-2 text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Status</div><span class="font-bold text-amber-500 bg-amber-50 px-2 py-0.5 rounded">Draft</span></div>
            </div>
          </div>
          
          <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-[14px] font-bold text-slate-800 mb-5">Course Features</h3>
            <div class="space-y-3">
              <div class="flex items-center gap-3 text-[12px] text-slate-600"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Online Exams</div>
              <div class="flex items-center gap-3 text-[12px] text-slate-600"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Question Banks</div>
              <div class="flex items-center gap-3 text-[12px] text-slate-600"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Grade Management</div>
              <div class="flex items-center gap-3 text-[12px] text-slate-600"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Reports & Analytics</div>
              <div class="flex items-center gap-3 text-[12px] text-slate-600"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Student Progress Tracking</div>
            </div>
          </div>
          
          <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-[14px] font-bold text-slate-800 mb-5">Quick Actions</h3>
            <div class="space-y-2">
              <button class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-slate-100 hover:bg-slate-50 text-[12px] text-slate-700 transition-colors">
                <div class="flex items-center gap-2 text-[#4338ca]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg><span class="text-slate-700 font-semibold">Import Course Data</span></div>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
              <button class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-slate-100 hover:bg-slate-50 text-[12px] text-slate-700 transition-colors">
                <div class="flex items-center gap-2 text-[#4338ca]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg><span class="text-slate-700 font-semibold">Manage Departments</span></div>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
              <button @click="openAssign(selectedCourse)" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-slate-100 hover:bg-slate-50 text-[12px] text-slate-700 transition-colors">
                <div class="flex items-center gap-2 text-[#4338ca]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg><span class="text-slate-700 font-semibold">Manage Instructors</span></div>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
              <button class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-slate-100 hover:bg-slate-50 text-[12px] text-slate-700 transition-colors">
                <div class="flex items-center gap-2 text-[#4338ca]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg><span class="text-slate-700 font-semibold">Course Templates</span></div>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div> <!-- Close Add Page -->

    <!-- Course Details (Full Page) -->
    <div v-if="showDetailsPage" class="space-y-6 pb-12 w-full min-w-0">
      <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
          </div>
          <div>
            <h1 class="text-[24px] font-bold text-slate-800 leading-tight">Course Details</h1>
            <p class="text-[13px] text-slate-500 mt-0.5">View complete information about this course.</p>
            <div class="flex items-center gap-2 text-[11px] font-bold text-slate-400 mt-1">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
              <span>Courses</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              <span class="text-slate-600">Course List</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              <span class="text-slate-600">Course Details</span>
            </div>
          </div>
        </div>
        <button @click="showDetailsPage = false" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl text-[13px] hover:bg-slate-50 transition-colors shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Course List
        </button>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-[1fr_320px] gap-6 items-start">
        <!-- Main Details Area -->
        <div class="space-y-6">
          
          <!-- Course Information -->
          <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
            <h2 class="text-[15px] font-bold text-slate-800 mb-6">Course Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-y-8 gap-x-6">
              <div>
                <p class="text-[12px] font-bold text-slate-500 mb-1">Course Code</p>
                <p class="text-[14px] font-bold text-[#4338ca]">{{ selectedCourse?.code || 'CS-301' }}</p>
              </div>
              <div>
                <p class="text-[12px] font-bold text-slate-500 mb-1">Course Title</p>
                <p class="text-[14px] font-bold text-[#4338ca]">{{ selectedCourse?.name || 'Data Structures and Algorithms' }}</p>
              </div>
              <div>
                <p class="text-[12px] font-bold text-slate-500 mb-1">Department</p>
                <p class="text-[14px] font-bold text-[#4338ca]">{{ selectedCourse?.departmentName || 'Computer Science' }}</p>
              </div>
              <div>
                <p class="text-[12px] font-bold text-slate-500 mb-1">Semester</p>
                <p class="text-[13px] font-bold text-slate-800">{{ selectedCourse?.semester || '1st Semester (2025/2026)' }}</p>
              </div>
              <div>
                <p class="text-[12px] font-bold text-slate-500 mb-1">Credits</p>
                <p class="text-[13px] font-bold text-slate-800">{{ selectedCourse?.credits || '4' }}</p>
              </div>
            </div>
          </div>

          <!-- Course Settings -->
          <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
            <h2 class="text-[15px] font-bold text-slate-800 mb-6">Course Settings</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-y-8 gap-x-6">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-200 overflow-hidden shrink-0">
                  <img src="https://i.pravatar.cc/150?u=a042581f4e29026704d" alt="Instructor" class="w-full h-full object-cover">
                </div>
                <div>
                  <p class="text-[11px] font-bold text-slate-500">Course Instructor</p>
                  <p class="text-[13px] font-bold text-slate-800">{{ selectedCourse?.instructor || 'Dr. Abebe Kebede' }}</p>
                  <p class="text-[11px] text-slate-500">abebe.kebede@wu.edu.et</p>
                </div>
              </div>
              <div>
                <p class="text-[12px] font-bold text-slate-500 mb-1">Co-Instructors</p>
                <p class="text-[13px] font-bold text-[#4338ca]">2 Co-Instructor(s)</p>
              </div>
              <div>
                <p class="text-[12px] font-bold text-slate-500 mb-1">Enrollment Status</p>
                <span class="inline-flex text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">Open for Enrollment</span>
              </div>
              <div>
                <p class="text-[12px] font-bold text-slate-500 mb-1">Visibility</p>
                <p class="text-[13px] font-bold text-slate-800">Visible to Students</p>
              </div>
              <div>
                <p class="text-[12px] font-bold text-slate-500 mb-1">Start Date</p>
                <p class="text-[13px] font-bold text-slate-800">May 10, 2025</p>
              </div>
              <div>
                <p class="text-[12px] font-bold text-slate-500 mb-1">End Date</p>
                <p class="text-[13px] font-bold text-slate-800">Aug 20, 2025</p>
              </div>
            </div>
          </div>

          <!-- Course Description -->
          <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
            <h2 class="text-[15px] font-bold text-slate-800 mb-4">Course Description</h2>
            <p class="text-[13px] text-slate-600 leading-relaxed">
              This course introduces fundamental data structures and algorithms used in computer science. Topics include arrays, linked lists, stacks, queues, trees, graphs, sorting, searching, and algorithm analysis.
            </p>
          </div>

          <!-- Created & Updated Information -->
          <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
            <h2 class="text-[15px] font-bold text-slate-800 mb-6">Created & Updated Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
              <div>
                <p class="text-[12px] font-bold text-slate-500 mb-1">Created By</p>
                <p class="text-[13px] font-bold text-slate-800">Super Admin</p>
              </div>
              <div>
                <p class="text-[12px] font-bold text-slate-500 mb-1">Created Date</p>
                <p class="text-[13px] font-bold text-slate-800">May 1, 2025 10:30 AM</p>
              </div>
              <div>
                <p class="text-[12px] font-bold text-slate-500 mb-1">Last Updated By</p>
                <p class="text-[13px] font-bold text-slate-800">Super Admin</p>
              </div>
              <div>
                <p class="text-[12px] font-bold text-slate-500 mb-1">Last Updated</p>
                <p class="text-[13px] font-bold text-slate-800">May 5, 2025 09:15 AM</p>
              </div>
            </div>
          </div>

        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
          <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-[14px] font-bold text-slate-800 mb-5">Course Summary</h3>
            <div class="flex items-start gap-4 mb-6 pb-6 border-b border-slate-50">
              <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-[#4338ca]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
              </div>
              <div>
                <h4 class="text-[13px] font-bold text-slate-800">{{ selectedCourse?.name || 'Data Structures and Algorithms' }}</h4>
                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">{{ selectedCourse?.code || 'CS-301' }}</p>
              </div>
            </div>
            <div class="space-y-4">
              <div class="flex items-center justify-between text-[12px]"><div class="flex items-center gap-2 text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>Department</div><span class="font-bold text-slate-800">{{ selectedCourse?.departmentName || 'Computer Science' }}</span></div>
              <div class="flex items-center justify-between text-[12px]"><div class="flex items-center gap-2 text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>Credits</div><span class="font-bold text-slate-800">{{ selectedCourse?.credits || '4' }}</span></div>
              <div class="flex items-center justify-between text-[12px]"><div class="flex items-center gap-2 text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>Semester</div><span class="font-bold text-slate-800">{{ selectedCourse?.semester || '1st Semester (2025/2026)' }}</span></div>
              <div class="flex items-center justify-between text-[12px]"><div class="flex items-center gap-2 text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Status</div><span class="font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Open for Enrollment</span></div>
              <div class="flex items-center justify-between text-[12px]"><div class="flex items-center gap-2 text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>Visibility</div><span class="font-bold text-slate-800">Visible to Students</span></div>
            </div>
          </div>
          
          <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-[14px] font-bold text-slate-800 mb-5">Course Features</h3>
            <div class="space-y-3">
              <div class="flex items-center gap-3 text-[12px] text-slate-600"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Online Exams</div>
              <div class="flex items-center gap-3 text-[12px] text-slate-600"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Question Banks</div>
              <div class="flex items-center gap-3 text-[12px] text-slate-600"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Grade Management</div>
              <div class="flex items-center gap-3 text-[12px] text-slate-600"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Reports & Analytics</div>
              <div class="flex items-center gap-3 text-[12px] text-slate-600"><svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Student Progress Tracking</div>
            </div>
          </div>
          
          <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="text-[14px] font-bold text-slate-800 mb-5">Quick Actions</h3>
            <div class="space-y-2">
              <button @click="openEditPage(selectedCourse); showDetailsPage = false" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-slate-100 hover:bg-slate-50 text-[12px] text-slate-700 transition-colors">
                <div class="flex items-center gap-2 text-[#4338ca]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg><span class="text-slate-700 font-semibold">Edit Course</span></div>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
              <button @click="openAssign(selectedCourse)" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-slate-100 hover:bg-slate-50 text-[12px] text-slate-700 transition-colors">
                <div class="flex items-center gap-2 text-[#4338ca]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg><span class="text-slate-700 font-semibold">Manage Instructors</span></div>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
              <button class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-slate-100 hover:bg-slate-50 text-[12px] text-slate-700 transition-colors">
                <div class="flex items-center gap-2 text-[#4338ca]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg><span class="text-slate-700 font-semibold">Manage Enrollments</span></div>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
              <button class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-slate-100 hover:bg-slate-50 text-[12px] text-slate-700 transition-colors">
                <div class="flex items-center gap-2 text-[#4338ca]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg><span class="text-slate-700 font-semibold">Duplicate Course</span></div>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div> <!-- Close Details Page -->

    <!-- Delete Modal -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
          <div class="p-6 text-center">
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg></div>
            <h3 class="text-[16px] font-bold text-slate-800 mb-2">Delete Course?</h3>
            <p class="text-[13px] text-slate-500">Delete <span class="font-bold text-slate-700">{{ selectedCourse?.name }}</span>? This cannot be undone.</p>
          </div>
          <div class="flex items-center gap-3 px-6 pb-6">
            <button @click="showDeleteModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="deleteCourse" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors">Delete</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Assign Modal -->
    <Teleport to="body">
      <div v-if="showAssignModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
          <div class="p-6">
            <h3 class="text-[16px] font-bold text-slate-800 mb-2">Assign Instructors</h3>
            <p class="text-[13px] text-slate-500 mb-4">Select instructors for <span class="font-bold text-slate-700">{{ courseToAssign?.name }}</span>.</p>
            <div class="space-y-4">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Section</label>
                <div class="relative">
                  <select v-model="assignSection" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] bg-white appearance-none">
                    <option value="">Select section</option>
                    <option v-for="sec in sectionOptions" :key="sec" :value="sec">{{ sec }}</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Course Instructor <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select v-model="assignInstructorId" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] bg-white appearance-none">
                    <option value="">Unassigned</option>
                    <option v-for="inst in availableInstructors" :key="inst.id" :value="inst.id">{{ inst.name }}</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
                <p v-if="currentInstructorInfo" class="mt-1.5 text-[11px] text-slate-400 italic">{{ currentInstructorInfo }}</p>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Co-Instructor</label>
                <div class="relative">
                  <select v-model="assignCoInstructorId" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] bg-white appearance-none">
                    <option value="">Select co-instructor (optional)</option>
                    <option v-for="inst in availableCoInstructors" :key="'co'+inst.id" :value="inst.id">{{ inst.name }}</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
                <p v-if="currentCoInstructorInfo" class="mt-1.5 text-[11px] text-slate-400 italic">{{ currentCoInstructorInfo }}</p>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-3 px-6 pb-6">
            <button @click="showAssignModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="assignInstructor" :disabled="isLoading" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-[#4338ca] hover:bg-indigo-600 rounded-xl transition-colors disabled:opacity-70">Save</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Edit Course Modal -->
    <Teleport to="body">
      <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden my-8">
          <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div>
              <h3 class="text-[18px] font-bold text-slate-800">Edit Course: {{ selectedCourse?.name }}</h3>
              <p class="text-[13px] text-slate-500 mt-0.5">Update course details and settings.</p>
            </div>
            <button @click="showEditModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <div class="p-6 overflow-y-auto max-h-[65vh] space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Course Code <span class="text-rose-500">*</span></label>
                <input v-model="newCourseForm.code" type="text" placeholder="e.g., CS-301" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Course Title <span class="text-rose-500">*</span></label>
                <input v-model="newCourseForm.title" type="text" placeholder="Enter course title" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Department <span class="text-rose-500">*</span></label>
                <select v-model="newCourseForm.department_id" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] bg-white appearance-none">
                  <option value="">Select department</option>
                  <option v-for="d in allDepartments" :key="d.id" :value="d.id">{{ d.name }}</option>
                </select>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Academic Year Level <span class="text-rose-500">*</span></label>
                <select v-model="newCourseForm.level" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] bg-white appearance-none">
                  <option value="">Select Academic Year</option>
                  <option value="1st Year">1st Year</option>
                  <option value="2nd Year">2nd Year</option>
                  <option value="3rd Year">3rd Year</option>
                  <option value="4th Year">4th Year</option>
                  <option value="5th Year">5th Year</option>
                </select>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Semester <span class="text-rose-500">*</span></label>
                <input v-model="newCourseForm.semester" type="text" disabled class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-500 bg-slate-50 cursor-not-allowed font-bold">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Credits</label>
                <input v-model="newCourseForm.credits" type="number" placeholder="Enter credit hours" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca]">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Start Date (Optional)</label>
                <input v-model="newCourseForm.start_date" type="date" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] bg-white appearance-none">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">End Date (Optional)</label>
                <input v-model="newCourseForm.end_date" type="date" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] bg-white appearance-none">
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Visibility</label>
                <select v-model="newCourseForm.visibility" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] bg-white appearance-none">
                  <option value="Visible to Students">Visible to Students</option>
                  <option value="Hidden">Hidden</option>
                </select>
              </div>
              <div>
                <label class="block text-[12px] font-bold text-slate-700 mb-2">Enrollment Status</label>
                <select v-model="newCourseForm.enrollment_status" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#4338ca] focus:ring-1 focus:ring-[#4338ca] bg-white appearance-none">
                  <option value="Open for Enrollment">Open for Enrollment</option>
                  <option value="Closed">Closed</option>
                </select>
              </div>
            </div>

            <!-- Status Toggle -->
            <div class="flex items-center justify-between p-4 border border-slate-200 rounded-xl bg-slate-50 mt-4">
              <div>
                <label class="block text-[13px] font-bold text-slate-700">Course Status</label>
                <p class="text-[11px] text-slate-500 mt-0.5">Toggle to set course as active or inactive.</p>
              </div>
              <button 
                @click="newCourseForm.status = newCourseForm.status === 'active' ? 'inactive' : 'active'" 
                :class="[
                  newCourseForm.status === 'active' ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-slate-300 hover:bg-slate-400',
                  'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none'
                ]" 
                role="switch" 
                :aria-checked="newCourseForm.status === 'active'"
              >
                <span 
                  aria-hidden="true" 
                  :class="[
                    newCourseForm.status === 'active' ? 'translate-x-5' : 'translate-x-0',
                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                  ]"
                ></span>
              </button>
            </div>
          </div>
          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            <button @click="showEditModal = false" class="px-5 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">Cancel</button>
            <button @click="saveCourse" :disabled="isLoading" class="px-5 py-2.5 text-[13px] font-bold text-white bg-[#4338ca] hover:bg-indigo-700 rounded-xl shadow-sm transition-all disabled:opacity-70 disabled:cursor-not-allowed">
              {{ isLoading ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>
