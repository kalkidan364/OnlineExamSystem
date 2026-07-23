<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import apiClient from '../../../core/api/apiClient'

// ── View State ──
import { useSettingsStore } from '../../../store/settingsStore'
const currentView = ref<'list' | 'add' | 'detail' | 'edit'>('list')
const settingsStore = useSettingsStore()

// ── State ──
const deptName = ref('Loading...')
const search = ref('')
const createdByFilter = ref('all')
const yearLevelFilter = ref('all')
const statusFilter = ref('all')
const currentPage = ref(1)
const perPage = 8
const showDeleteModal = ref(false)
const selectedCourse = ref<any>(null)
const isLoading = ref(false)

// ── Unauthorized Modal ──
const showUnauthorizedModal = ref(false)

const allCourses = ref<any[]>([])
const deptInstructors = ref<any[]>([])
const currentUser = ref<any>(null)

const newCourse = ref({
  code: '', title: '', department: '', level: '', semester: '', credits: '' as string | number,
  instructor_id: '', coInstructors: '', enrollmentStatus: '', visibility: '', startDate: '', endDate: ''
})

// ── Assign Instructor State ──
const showAssignModal = ref(false)
const courseToAssign = ref<any>(null)
const assignInstructorId = ref('')
const assignCoInstructorId = ref('')

const availableInstructors = computed(() => {
  return deptInstructors.value.filter(inst => inst.id !== assignCoInstructorId.value)
})

const availableCoInstructors = computed(() => {
  return deptInstructors.value.filter(inst => inst.id !== assignInstructorId.value)
})

// ── Edit Course State ──
const showEditModal = ref(false)

// ── Mock Data (for display until API is connected) ──
const mockCourses = [
  { id: 1, title: 'Database Systems', code: 'CS-301', section: 'Section A', instructor: { name: 'Dr. Solomon Abate', avatar: '' }, semester: 'Spring 2026', studentsEnrolled: 45, exams: 2, status: 'active', created_by: 1 },
  { id: 2, title: 'Software Engineering', code: 'SE-201', section: 'Section B', instructor: { name: 'Dr. Abebe Kebede', avatar: '' }, semester: 'Spring 2026', studentsEnrolled: 38, exams: 2, status: 'active', created_by: 1 },
  { id: 3, title: 'Data Structures', code: 'CS-202', section: 'Section A', instructor: { name: 'Dr. Yeshimebet Desta', avatar: '' }, semester: 'Spring 2026', studentsEnrolled: 32, exams: 1, status: 'active', created_by: 1 },
  { id: 4, title: 'Operating Systems', code: 'CS-401', section: 'Section C', instructor: { name: 'Mr. Getachew Tadesse', avatar: '' }, semester: 'Spring 2026', studentsEnrolled: 40, exams: 2, status: 'active', created_by: 1 },
  { id: 5, title: 'Web Development', code: 'SE-302', section: 'Section B', instructor: { name: 'Mrs. Melaku Alemu', avatar: '' }, semester: 'Spring 2026', studentsEnrolled: 28, exams: 1, status: 'active', created_by: 1 },
  { id: 6, title: 'Computer Networks', code: 'CS-303', section: 'Section A', instructor: { name: 'Mr. Fikret Abebe', avatar: '' }, semester: 'Fall 2025', studentsEnrolled: 36, exams: 2, status: 'completed', created_by: 1 },
  { id: 7, title: 'Information Systems', code: 'IS-201', section: 'Section A', instructor: { name: 'Ms. Hana Bokele', avatar: '' }, semester: 'Fall 2025', studentsEnrolled: 30, exams: 1, status: 'completed', created_by: 1 },
  { id: 8, title: 'Cyber Security', code: 'CS-402', section: 'Section C', instructor: { name: 'Mr. Tesfaye Alemu', avatar: '' }, semester: 'Fall 2025', studentsEnrolled: 27, exams: 1, status: 'draft', created_by: 1 },
]

// ── Fetch Department Info ──
const fetchDeptInfo = async () => {
  try {
    const res = await apiClient.get('/user')
    currentUser.value = res.data
    deptName.value = res.data?.department?.name || 'My Department'
  } catch { deptName.value = 'My Department' }
}

// ── Fetch Courses ──
const fetchCourses = async () => {
  try {
    const res = await apiClient.get('/dept-head/courses')
    const apiCourses = res.data.data || []
    allCourses.value = apiCourses.map((c: any) => ({
      ...c,
      studentsEnrolled: c.students_count || 0,
      exams: c.exams_count || 0,
      createdByRole: c.creator?.role || 'admin',
      createdByName: c.creator?.role === 'dept_head' || c.creator?.role === 'department_head' ? 'Dept. Head' : 'Admin',
      co_instructor: c.co_instructor || null,
    }))
  } catch (err) {
    console.error('Failed to fetch courses:', err)
    allCourses.value = []
  }
}

// ── Fetch Instructors (for dropdown) ──
const fetchInstructors = async () => {
  try {
    const res = await apiClient.get('/dept-head/instructors')
    deptInstructors.value = res.data.data || []
  } catch (err) { console.error('Failed to fetch instructors:', err) }
}

onMounted(async () => {
  await fetchDeptInfo()
  await Promise.all([fetchCourses(), fetchInstructors()])
})

// ── Filtering ──
const filtered = computed(() => {
  return allCourses.value.filter(c => {
    const t = c.title?.toLowerCase() || ''
    const cd = c.code?.toLowerCase() || ''
    const s = search.value.toLowerCase()
    const matchSearch = t.includes(s) || cd.includes(s)
    const matchStatus = statusFilter.value === 'all' || c.status === statusFilter.value
    const matchCreatedBy = createdByFilter.value === 'all' || c.createdByRole === createdByFilter.value || 
      (createdByFilter.value === 'dept_head' && (c.createdByRole === 'dept_head' || c.createdByRole === 'department_head')) ||
      (createdByFilter.value === 'admin' && c.createdByRole === 'admin')
    const matchYearLevel = yearLevelFilter.value === 'all' || (c.level && c.level === yearLevelFilter.value)
    return matchSearch && matchStatus && matchCreatedBy && matchYearLevel
  })
})

// ── Pagination ──
const totalPages = computed(() => Math.ceil(filtered.value.length / perPage))
const paginatedCourses = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filtered.value.slice(start, start + perPage)
})
const paginationStart = computed(() => filtered.value.length === 0 ? 0 : (currentPage.value - 1) * perPage + 1)
const paginationEnd = computed(() => Math.min(currentPage.value * perPage, filtered.value.length))
const visiblePages = computed(() => {
  const pages: number[] = []
  const total = totalPages.value
  const current = currentPage.value
  let start = Math.max(1, current - 2)
  let end = Math.min(total, start + 4)
  if (end - start < 4) start = Math.max(1, end - 4)
  for (let i = start; i <= end; i++) pages.push(i)
  return pages
})

// ── Stats ──
const stats = computed(() => ({
  total: allCourses.value.length,
  active: allCourses.value.filter(c => c.status === 'active').length,
  totalStudents: allCourses.value.reduce((acc, c) => acc + (c.studentsEnrolled || 0), 0),
  totalExams: allCourses.value.reduce((acc, c) => acc + (c.exams || c.exams_count || 0), 0),
}))

// ── Actions ──
const openAddPage = () => {
  newCourse.value = {
    code: '', title: '', department: '', program: '', semester: '', credits: '',
    instructor_id: '', coInstructors: '', enrollmentStatus: '', visibility: '', startDate: '', endDate: ''
  }
  currentView.value = 'add'
}
const openDetail = (course: any) => {
  selectedCourse.value = course
  currentView.value = 'detail'
}
const openEdit = (course: any) => {
  // Block edit if created by admin
  if (course.createdByRole === 'admin') {
    showUnauthorizedModal.value = true
    return
  }
  selectedCourse.value = course
  newCourse.value = {
    code: course.code || '',
    title: course.title || '',
    department: course.department || '',
    level: course.level || course.program || '',
    semester: course.semester || '',
    credits: course.credits || '',
    instructor_id: course.instructor_id || course.instructor?.id || '',
    coInstructors: course.coInstructors || '',
    enrollmentStatus: course.enrollmentStatus || '',
    visibility: course.visibility || '',
    startDate: course.startDate || '',
    endDate: course.endDate || '',
    status: course.status || 'active'
  }
  showEditModal.value = true
}
const backToList = () => { currentView.value = 'list' }

const addCourse = async () => {
  if (!newCourse.value.title || !newCourse.value.code) return
  isLoading.value = true
  try {
    await apiClient.post('/dept-head/courses', {
      title: newCourse.value.title,
      code: newCourse.value.code,
      credits: newCourse.value.credits || 3,
      level: newCourse.value.level || null,
      semester: settingsStore.formattedAcademicTerm || null,
      instructor_id: newCourse.value.instructor_id || null,
    })
    await fetchCourses()
    currentView.value = 'list'
  } catch (err: any) {
    let msg = err.response?.data?.message || 'Failed to create course.'
    if (err.response?.data?.errors) msg += '\n' + Object.values(err.response.data.errors).flat().join('\n')
    alert(msg)
  } finally { isLoading.value = false }
}

const updateCourse = async () => {
  if (!newCourse.value.title || !newCourse.value.code) return
  isLoading.value = true
  try {
    await apiClient.put(`/dept-head/courses/${selectedCourse.value.id}`, {
      title: newCourse.value.title,
      code: newCourse.value.code,
      credits: newCourse.value.credits || 3,
      level: newCourse.value.level || null,
      semester: settingsStore.formattedAcademicTerm || null,
      instructor_id: newCourse.value.instructor_id || null,
      status: newCourse.value.status || 'active',
    })
    await fetchCourses()
    showEditModal.value = false
  } catch (err: any) {
    let msg = err.response?.data?.message || 'Failed to update course.'
    if (err.response?.data?.errors) msg += '\n' + Object.values(err.response.data.errors).flat().join('\n')
    alert(msg)
  } finally { isLoading.value = false }
}

const confirmDelete = (course: any) => { selectedCourse.value = course; showDeleteModal.value = true }
const deleteCourse = async () => {
  if (!selectedCourse.value) return
  isLoading.value = true
  try {
    await apiClient.delete(`/dept-head/courses/${selectedCourse.value.id}`)
    await fetchCourses()
    showDeleteModal.value = false
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete course.')
  } finally { isLoading.value = false }
}

const openAssign = (course: any) => {
  courseToAssign.value = course
  assignInstructorId.value = course.instructor_id || course.instructor?.id || ''
  showAssignModal.value = true
}

const assignInstructor = async () => {
  if (!courseToAssign.value) return
  isLoading.value = true
  try {
    await apiClient.put(`/dept-head/courses/${courseToAssign.value.id}`, {
      instructor_id: assignInstructorId.value || null,
      co_instructor_id: assignCoInstructorId.value || null
    })
    await fetchCourses()
    showAssignModal.value = false
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to assign instructor.')
  } finally { isLoading.value = false }
}

const getStatusClass = (status: string) => {
  switch (status) {
    case 'active': return 'text-emerald-500 font-semibold'
    case 'completed': return 'text-[#5138ed] font-semibold'
    case 'draft': return 'text-amber-500 font-semibold'
    default: return 'text-slate-400 font-semibold'
  }
}

const getStatusLabel = (status: string) => {
  switch (status) {
    case 'active': return 'Active'
    case 'completed': return 'Completed'
    case 'draft': return 'Draft'
    case 'inactive': return 'Inactive'
    default: return status
  }
}

const getInitials = (name: string) => {
  if (!name) return '?'
  return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
}

const getAvatarColor = (id: number) => {
  const colors = ['bg-indigo-100 text-indigo-600', 'bg-emerald-100 text-emerald-600', 'bg-amber-100 text-amber-600', 'bg-rose-100 text-rose-600', 'bg-sky-100 text-sky-600', 'bg-violet-100 text-violet-600', 'bg-teal-100 text-teal-600', 'bg-orange-100 text-orange-600']
  return colors[id % colors.length]
}

// ── Today's date ──
const today = computed(() => {
  const d = new Date()
  const options: Intl.DateTimeFormatOptions = { year: 'numeric', month: 'long', day: 'numeric' }
  const dayName = d.toLocaleDateString('en-US', { weekday: 'long' })
  return { date: d.toLocaleDateString('en-US', options), day: dayName }
})
</script>

<template>
  <div class="space-y-6">

    <!-- ══════════════════════════ ADD COURSE VIEW ══════════════════════════ -->
    <template v-if="currentView === 'add'">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-3 mb-1">
            <h1 class="text-[22px] font-bold text-slate-800">Add New Course</h1>
          </div>
          <div class="flex items-center gap-1.5 text-[12px] text-slate-400 mt-2">
            <button @click="backToList" class="text-[#5138ed] hover:text-indigo-700 font-medium transition-colors">Courses</button>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="text-slate-600 font-medium">Add New Course</span>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <button @click="backToList" class="flex items-center gap-2 px-5 py-2.5 text-[13px] font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Courses
          </button>
        </div>
      </div>

      <!-- Form Sections -->
      <div class="space-y-6">

        <!-- ── Course Information ── -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-8">
          <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <h2 class="text-[16px] font-bold text-slate-800">Course Information</h2>
          </div>
          <div class="space-y-6">
            <!-- Row 1: Course Code, Course Title, Department -->
            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[13px] font-bold text-slate-700 mb-2">Course Code <span class="text-rose-500">*</span></label>
                <input v-model="newCourse.code" type="text" placeholder="Enter course code" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
              </div>
              <div>
                <label class="block text-[13px] font-bold text-slate-700 mb-2">Course Title <span class="text-rose-500">*</span></label>
                <input v-model="newCourse.title" type="text" placeholder="Enter course title" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
              </div>
              <div>
                <label class="block text-[13px] font-bold text-slate-700 mb-2">Department</label>
                <input type="text" :value="deptName" readonly class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-500 bg-slate-50 cursor-not-allowed" />
              </div>
            </div>
            <!-- Row 2: Program, Semester, Credits -->
            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[13px] font-bold text-slate-700 mb-2">Academic Year Level <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select v-model="newCourse.level" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-shadow">
                    <option value="">Select year level</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[13px] font-bold text-slate-700 mb-2">Semester</label>
                <input type="text" :value="settingsStore.formattedAcademicTerm" readonly class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-500 bg-slate-50 cursor-not-allowed" />
              </div>
              <div>
                <label class="block text-[13px] font-bold text-slate-700 mb-2">Credits <span class="text-rose-500">*</span></label>
                <input v-model="newCourse.credits" type="text" placeholder="Enter credits" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
              </div>
            </div>
          </div>
        </div>



        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4 pb-4">
          <button @click="backToList" class="flex items-center gap-2 px-6 py-3 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            Cancel
          </button>
          <button @click="addCourse()" :disabled="isLoading" class="flex items-center gap-2 px-6 py-3 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-200 transition-all disabled:opacity-70 disabled:cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            {{ isLoading ? 'Saving...' : 'Save Course' }}
          </button>
        </div>
      </div>
    </template>

    <!-- ══════════════════════════ COURSE DETAILS VIEW ══════════════════════════ -->
    <template v-else-if="currentView === 'detail'">
      <!-- Header -->
      <div class="flex items-center justify-between mb-2">
        <div>
          <div class="flex items-center gap-3 mb-1">
            <h1 class="text-[22px] font-bold text-slate-800">Course Details</h1>
          </div>
          <div class="flex items-center gap-1.5 text-[12px] text-slate-400 mt-2">
            <button @click="backToList" class="text-[#5138ed] hover:text-indigo-700 font-medium transition-colors">Courses</button>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="text-slate-600 font-medium">Course Details</span>
          </div>
        </div>
        <div class="flex items-center gap-4">
          <!-- Back to Courses button -->
          <button @click="backToList" class="flex items-center gap-2 px-4 py-2.5 text-[13px] font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Courses
          </button>
        </div>
      </div>
      <p class="text-[13px] text-slate-500 mb-6">View complete information about the course.</p>

      <div class="space-y-6">
        <!-- Course Information -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-8">
          <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <h2 class="text-[16px] font-bold text-slate-800">Course Information</h2>
          </div>
          <div class="grid grid-cols-3 gap-y-8 gap-x-6">
            <div>
              <p class="text-[11px] font-bold text-slate-800 mb-1">Course Code</p>
              <p class="text-[13px] text-slate-600">{{ selectedCourse?.code || '—' }}</p>
            </div>
            <div>
              <p class="text-[11px] font-bold text-slate-800 mb-1">Course Title</p>
              <p class="text-[13px] text-slate-600">{{ selectedCourse?.title || '—' }}</p>
            </div>
            <div>
              <p class="text-[11px] font-bold text-slate-800 mb-1">Department</p>
              <p class="text-[13px] text-slate-600">{{ selectedCourse?.department || 'Computer Science' }}</p>
            </div>
            <div>
              <p class="text-[11px] font-bold text-slate-800 mb-1">Academic Year Level</p>
              <p class="text-[13px] text-slate-600">{{ selectedCourse?.level || '—' }}</p>
            </div>
            <div>
              <p class="text-[11px] font-bold text-slate-800 mb-1">Semester</p>
              <p class="text-[13px] text-slate-600">{{ selectedCourse?.semester || '—' }}</p>
            </div>
            <div>
              <p class="text-[11px] font-bold text-slate-800 mb-1">Credits</p>
              <p class="text-[13px] text-slate-600">{{ selectedCourse?.credits || '—' }}</p>
            </div>
          </div>
        </div>

        <!-- Course Settings -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-8">
          <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <h2 class="text-[16px] font-bold text-slate-800">Course Settings</h2>
          </div>
          <div class="grid grid-cols-3 gap-y-8 gap-x-6">
            <div>
              <p class="text-[11px] font-bold text-slate-800 mb-2">Course Instructor</p>
              <p class="text-[13px] text-slate-600">{{ selectedCourse?.instructor?.name || '—' }}</p>
            </div>
            <div>
              <p class="text-[11px] font-bold text-slate-800 mb-2">Co-Instructors</p>
              <p v-if="selectedCourse?.co_instructor?.name" class="text-[13px] text-slate-600">{{ selectedCourse.co_instructor.name }}</p>
              <p v-else class="text-[13px] text-slate-400">—</p>
            </div>
            <div>
              <p class="text-[11px] font-bold text-slate-800 mb-2">Enrollment Status</p>
              <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-600">Active</span>
            </div>
            <div>
              <p class="text-[11px] font-bold text-slate-800 mb-2">Visibility</p>
              <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-50 text-sky-600">Visible</span>
            </div>
            <div>
              <p class="text-[11px] font-bold text-slate-800 mb-2">Start Date</p>
              <p class="text-[13px] text-slate-600">{{ selectedCourse?.startDate || '—' }}</p>
            </div>
            <div>
              <p class="text-[11px] font-bold text-slate-800 mb-2">End Date</p>
              <p class="text-[13px] text-slate-600">{{ selectedCourse?.endDate || '—' }}</p>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- ══════════════════════════ LIST VIEW ══════════════════════════ -->
    <template v-else>

      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-[22px] font-bold text-slate-800">Courses</h1>
          <p class="text-[13px] text-slate-500 mt-1">Manage and monitor department courses.</p>
        </div>
        <div class="flex items-center gap-2">
          <button @click="openAddPage" class="flex items-center gap-2 bg-[#5138ed] hover:bg-indigo-700 text-white text-[13px] font-bold px-5 py-2.5 rounded-xl shadow-sm transition-all whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Course
          </button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-4 gap-5">
        <!-- Total Courses -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
          <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
          </div>
          <div>
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Total Courses</p>
            <p class="text-[24px] font-bold text-slate-800 leading-tight">{{ stats.total }}</p>
            <p class="text-[11px] text-[#5138ed] font-medium mt-0.5">↑ 4 this semester</p>
          </div>
        </div>
        <!-- Active Courses -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
          <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <div>
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Active Courses</p>
            <p class="text-[24px] font-bold text-slate-800 leading-tight">{{ stats.active }}</p>
            <p class="text-[11px] text-emerald-500 font-medium mt-0.5">↑ 3 this semester</p>
          </div>
        </div>
        <!-- Total Students Enrolled -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
          <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          </div>
          <div>
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Total Students Enrolled</p>
            <p class="text-[24px] font-bold text-slate-800 leading-tight">{{ stats.totalStudents.toLocaleString() }}</p>
            <p class="text-[11px] text-amber-500 font-medium mt-0.5">↑ 156 this semester</p>
          </div>
        </div>
        <!-- Total Exams -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
          <div class="w-12 h-12 bg-violet-50 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
          </div>
          <div>
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Total Exams</p>
            <p class="text-[24px] font-bold text-slate-800 leading-tight">{{ stats.totalExams }}</p>
            <p class="text-[11px] text-violet-500 font-medium mt-0.5">↑ 5 this semester</p>
          </div>
        </div>
      </div>

      <!-- Filters & Search -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5">
        <div class="flex items-center gap-4">
          <!-- Search -->
          <div class="relative flex-1 max-w-md">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input v-model="search" type="text" placeholder="Search courses by name or code..." class="w-full pl-10 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-xl bg-white focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" />
          </div>
          <!-- Created By Filter -->
          <div class="relative">
            <select v-model="createdByFilter" class="text-[13px] border border-slate-200 rounded-xl pl-4 pr-8 py-2.5 bg-white focus:outline-none focus:border-[#5138ed] text-slate-600 appearance-none">
              <option value="all">Created By</option>
              <option value="admin">Admin</option>
              <option value="dept_head">Dept. Head</option>
            </select>
            <svg class="w-4 h-4 absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
          <!-- Academic Year Level Filter -->
          <div class="relative">
            <select v-model="yearLevelFilter" class="text-[13px] border border-slate-200 rounded-xl pl-4 pr-8 py-2.5 bg-white focus:outline-none focus:border-[#5138ed] text-slate-600 appearance-none">
              <option value="all">All Year Levels</option>
              <option value="1st Year">1st Year</option>
              <option value="2nd Year">2nd Year</option>
              <option value="3rd Year">3rd Year</option>
              <option value="4th Year">4th Year</option>
              <option value="5th Year">5th Year</option>
            </select>
            <svg class="w-4 h-4 absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
          <!-- Status Filter -->
          <div class="relative">
            <select v-model="statusFilter" class="text-[13px] border border-slate-200 rounded-xl pl-4 pr-8 py-2.5 bg-white focus:outline-none focus:border-[#5138ed] text-slate-600 appearance-none">
              <option value="all">All Status</option>
              <option value="active">Active</option>
              <option value="completed">Completed</option>
              <option value="draft">Draft</option>
              <option value="inactive">Inactive</option>
            </select>
            <svg class="w-4 h-4 absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
          <!-- Filter Button -->
          <button class="flex items-center gap-2 px-4 py-2.5 text-[13px] font-semibold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Filter
          </button>
        </div>
      </div>

      <!-- Course Table -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-slate-100">
                <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course Name</th>
                <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course Code</th>
                <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Year Level</th>
                <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Instructor</th>
                <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Credits</th>
                <th class="text-center px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Created By</th>
                <th class="text-center px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Exams</th>
                <th class="text-center px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                <th class="text-center px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="course in paginatedCourses" :key="course.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                <!-- Course Name -->
                <td class="px-6 py-4">
                  <span class="text-[13px] font-semibold text-slate-800">{{ course.title }}</span>
                </td>
                <!-- Course Code -->
                <td class="px-4 py-4">
                  <span class="text-[13px] font-mono font-semibold text-slate-500">{{ course.code }}</span>
                </td>
                <!-- Year Level -->
                <td class="px-4 py-4">
                  <span class="text-[13px] text-slate-600">{{ course.level || course.program || '—' }}</span>
                </td>
                <!-- Instructor -->
                <td class="px-4 py-4">
                  <div class="flex items-center gap-3">
                    <div :class="[getAvatarColor(course.id), 'w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold shrink-0']">
                      {{ getInitials(course.instructor?.name || '') }}
                    </div>
                    <span class="text-[13px] text-slate-700 font-medium">{{ course.instructor?.name || 'Unassigned' }}</span>
                  </div>
                </td>
                <!-- Credits -->
                <td class="px-4 py-4">
                  <span class="text-[13px] text-slate-600">{{ course.credits || '—' }}</span>
                </td>
                <!-- Created By -->
                <td class="px-4 py-4 text-center">
                  <span :class="course.createdByRole === 'admin' ? 'bg-violet-50 text-violet-600' : 'bg-sky-50 text-sky-600'" class="text-[11px] font-bold px-2.5 py-1 rounded-full">
                    {{ course.createdByName || 'Admin' }}
                  </span>
                </td>
                <!-- Exams -->
                <td class="px-4 py-4 text-center">
                  <span class="text-[13px] font-semibold text-slate-700">{{ course.exams || course.exams_count || 0 }}</span>
                </td>
                <!-- Status -->
                <td class="px-4 py-4 text-center">
                  <span :class="getStatusClass(course.status)" class="text-[12px] capitalize">{{ getStatusLabel(course.status) }}</span>
                </td>
                <!-- Actions -->
                <td class="px-4 py-4">
                  <div class="flex items-center justify-center gap-1">
                    <!-- Assign Instructor -->
                    <button @click="openAssign(course)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-emerald-500 hover:bg-emerald-50 transition-all" title="Assign Instructor">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </button>
                    <!-- View -->
                    <button @click="openDetail(course)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 transition-all" title="View Details">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                    <!-- Edit -->
                    <button @click="openEdit(course)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-amber-500 hover:bg-amber-50 transition-all" title="Edit Course">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </button>
                    <!-- Delete -->
                    <button @click="confirmDelete(course)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-all" title="Delete Course">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                  </div>
                </td>
              </tr>
              <!-- Empty State -->
              <tr v-if="paginatedCourses.length === 0">
                <td colspan="9" class="text-center py-16">
                  <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                  </div>
                  <p class="text-[14px] font-semibold text-slate-600 mb-1">No courses found</p>
                  <p class="text-[13px] text-slate-400">Try adjusting your search or filters, or click "Add Course" to create one.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="filtered.length > 0" class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
          <p class="text-[13px] text-slate-500">Showing {{ paginationStart }} to {{ paginationEnd }} of {{ filtered.length }} courses</p>
          <div class="flex items-center gap-1">
            <button @click="currentPage > 1 && currentPage--" :disabled="currentPage === 1" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button v-for="page in visiblePages" :key="page" @click="currentPage = page"
              :class="[currentPage === page ? 'bg-[#5138ed] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-50', 'w-8 h-8 rounded-lg flex items-center justify-center text-[13px] font-semibold transition-all']">
              {{ page }}
            </button>
            <button @click="currentPage < totalPages && currentPage++" :disabled="currentPage === totalPages" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
          </div>
        </div>
      </div>

    </template>
    <!-- ── Unauthorized Modal ── -->
    <Teleport to="body">
      <div v-if="showUnauthorizedModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6">
          <div class="w-14 h-14 bg-violet-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg></div>
          <h3 class="text-[16px] font-bold text-slate-800 mb-2">Unauthorized</h3>
          <p class="text-[13px] text-slate-500 mb-6">You can only edit courses that were created by the Department Head. This course was created by an Admin.</p>
          <div class="flex gap-3">
            <button @click="showUnauthorizedModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-violet-500 hover:bg-violet-600 rounded-xl transition-colors">Understood</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- ── Delete Modal ── -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6">
          <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg></div>
          <h3 class="text-[16px] font-bold text-slate-800 mb-2">Delete Course?</h3>
          <p class="text-[13px] text-slate-500 mb-6">Are you sure you want to delete <span class="font-bold text-slate-700">{{ selectedCourse?.title }}</span>?</p>
          <div class="flex gap-3">
            <button @click="showDeleteModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="deleteCourse" :disabled="isLoading" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors disabled:opacity-70">
              {{ isLoading ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- ── Assign Modal ── -->
    <Teleport to="body">
      <div v-if="showAssignModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            </div>
            <div>
              <h3 class="text-[16px] font-bold text-slate-800">Assign Instructor</h3>
              <p class="text-[12px] text-slate-500 line-clamp-1">{{ courseToAssign?.title }}</p>
            </div>
          </div>
          <div class="mb-6">
            <label class="block text-[13px] font-bold text-slate-700 mb-2">Select Instructor</label>
            <div class="relative mb-4">
              <select v-model="assignInstructorId" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 bg-white appearance-none focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-shadow">
                <option value="">No Instructor</option>
                <option v-for="inst in availableInstructors" :key="inst.id" :value="inst.id">{{ inst.name }}</option>
              </select>
              <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            
            <label class="block text-[13px] font-bold text-slate-700 mb-2">Select Co-Instructor</label>
            <div class="relative">
              <select v-model="assignCoInstructorId" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 bg-white appearance-none focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-shadow">
                <option value="">No Co-Instructor</option>
                <option v-for="inst in availableCoInstructors" :key="'co'+inst.id" :value="inst.id">{{ inst.name }}</option>
              </select>
              <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
          </div>
          <div class="flex gap-3">
            <button @click="showAssignModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="assignInstructor" :disabled="isLoading" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-emerald-500 hover:bg-emerald-600 rounded-xl transition-colors disabled:opacity-70">
              {{ isLoading ? 'Assigning...' : 'Assign' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- ── Edit Course Modal ── -->
    <Teleport to="body">
      <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden p-8 my-8">
          <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <div>
              <h3 class="text-[18px] font-bold text-slate-800">Edit Course</h3>
              <p class="text-[13px] text-slate-500">{{ selectedCourse?.title }}</p>
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-5 mb-6">
            <div>
              <label class="block text-[13px] font-bold text-slate-700 mb-2">Course Code <span class="text-rose-500">*</span></label>
              <input v-model="newCourse.code" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" />
            </div>
            <div>
              <label class="block text-[13px] font-bold text-slate-700 mb-2">Course Title <span class="text-rose-500">*</span></label>
              <input v-model="newCourse.title" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" />
            </div>
            <div>
              <label class="block text-[13px] font-bold text-slate-700 mb-2">Department</label>
              <input type="text" :value="deptName" readonly class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-500 bg-slate-50 cursor-not-allowed" />
            </div>
            <div>
              <label class="block text-[13px] font-bold text-slate-700 mb-2">Academic Year Level <span class="text-rose-500">*</span></label>
              <div class="relative">
                <select v-model="newCourse.level" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
                  <option value="">Select year level</option>
                  <option value="1st Year">1st Year</option>
                  <option value="2nd Year">2nd Year</option>
                  <option value="3rd Year">3rd Year</option>
                  <option value="4th Year">4th Year</option>
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
            <div>
              <label class="block text-[13px] font-bold text-slate-700 mb-2">Semester</label>
              <input type="text" :value="settingsStore.formattedAcademicTerm" readonly class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-500 bg-slate-50 cursor-not-allowed" />
            </div>
            <div>
              <label class="block text-[13px] font-bold text-slate-700 mb-2">Credits <span class="text-rose-500">*</span></label>
              <input v-model="newCourse.credits" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" />
            </div>
            
            <div class="col-span-2">
              <div class="flex items-center justify-between bg-slate-50 border border-slate-100 rounded-xl p-5">
                <div>
                  <h4 class="text-[14px] font-bold text-slate-800">Course Status</h4>
                  <p class="text-[12px] text-slate-500 mt-1">Toggle to set course as active or inactive.</p>
                </div>
                <button 
                  @click="newCourse.status = newCourse.status === 'active' ? 'inactive' : 'active'" 
                  :class="newCourse.status === 'active' ? 'bg-emerald-500' : 'bg-slate-300'" 
                  class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                  type="button"
                >
                  <span :class="newCourse.status === 'active' ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                </button>
              </div>
            </div>
          </div>
          
          <div class="flex items-center justify-end gap-3 mt-8">
            <button @click="showEditModal = false" class="px-5 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="updateCourse" :disabled="isLoading" class="px-5 py-2.5 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl transition-colors disabled:opacity-70 flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              {{ isLoading ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>
