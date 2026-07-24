<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import apiClient from '../../../core/api/apiClient'
import { useAuthStore } from '../../../modules/auth/store/authStore'
import { useSettingsStore } from '../../../store/settingsStore'

const authStore = useAuthStore()
const settingsStore = useSettingsStore()

const currentPage = ref(1)
const perPage = 8
const currentView = ref<'list' | 'schedule' | 'review'>('list')
const showAddCourseModal = ref(false)

const stats = ref([
  { label: 'Total Exams',     value: '0', change: '↑ 0 this semester', color: 'text-emerald-500', bg: 'bg-indigo-50',  iconColor: 'text-[#5138ed]',    icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
  { label: 'Scheduled Exams', value: '0', change: '↑ 0 this semester', color: 'text-emerald-500', bg: 'bg-emerald-50', iconColor: 'text-emerald-500', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
  { label: 'Upcoming Exams',  value: '0', change: '↑ 0 this week',     color: 'text-emerald-500', bg: 'bg-amber-50',   iconColor: 'text-amber-500',   icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
  { label: 'Conflicts',       value: '0', change: '↓ 0 this semester', color: 'text-rose-500',    bg: 'bg-rose-50',    iconColor: 'text-rose-500',    icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
])

const exams = ref<any[]>([])

// ── Real computed values from stores ──────────────────────────────
// Academic Year: e.g. "2025" → formatted as "2025/2026"
const academicYear = computed(() => {
  const yr = settingsStore.academicYear
  if (!yr) return ''
  // If super admin stored just "2025", display as "2025/2026"
  if (/^\d{4}$/.test(yr)) return `${yr}/${parseInt(yr) + 1}`
  return yr // already formatted like "2025/2026"
})

// Semester from settings store (e.g. "1st Semester" / "2nd Semester")
const currentSemester = computed(() => settingsStore.semester)

// Department name from logged-in dept head's department relation
const departmentName = computed(() => {
  const dept = authStore.user?.department
  if (!dept) return ''
  return dept.name || dept.code || ''
})

// Faculty is always "College of Informatics" (static per requirements, matches IS dept)
const facultyName = 'College of Informatics'

const fetchExams = async () => {
  try {
    const response = await apiClient.get('/dept-head/exams')
    exams.value = response.data.data
    
    // Update basic stats
    const total = exams.value.length
    const scheduled = exams.value.filter((e: any) => e.status === 'Scheduled').length
    stats.value[0].value = total.toString()
    stats.value[1].value = scheduled.toString()
  } catch (error) {
    console.error('Failed to fetch exams:', error)
  }
}

const fetchCourses = async () => {
  try {
    const response = await apiClient.get('/dept-head/courses')
    availableCourses.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch courses:', error)
  }
}

onMounted(async () => {
  fetchExams()
  fetchCourses()
  // Ensure settings are loaded (they may already be from login)
  if (!settingsStore.academicYear || settingsStore.academicYear === '2025') {
    await settingsStore.fetchSettings()
  }
  // Sync addForm fields from stores after data is ready
  syncFormFromStores()
})

const addForm = ref({
  title: 'Semester I Mid Examination Schedule',
  academic_year: '',
  semester: '',
  exam_type: 'Mid Examination',
  department: '',
  faculty: facultyName,
  start_date: '2026-06-02',
  end_date: '2026-06-10',
  description: '',
  year_level: '1st Year',
  courses: [] as any[]
})

// Sync addForm from store values whenever they change
function syncFormFromStores() {
  addForm.value.academic_year = academicYear.value
  addForm.value.semester = currentSemester.value
  addForm.value.department = departmentName.value
  addForm.value.faculty = facultyName
}

// Watch for reactive updates (in case stores update async after mount)
watch([academicYear, currentSemester, departmentName], () => {
  syncFormFromStores()
}, { immediate: true })

const newCourse = ref({
  name: '',
  code: '',
  date: '',
  time: '',
  room: '',
  inv: '',
  notes: ''
})

const availableCourses = ref<any[]>([])

const filteredCourses = computed(() => {
  return availableCourses.value.filter(c => c.level === addForm.value.year_level)
})

const selectedCourseId = ref('')

watch(selectedCourseId, (newId) => {
  const course = availableCourses.value.find(c => c.id === newId)
  if (course) {
    newCourse.value.name = course.title
    newCourse.value.code = course.code
  } else {
    newCourse.value.name = ''
    newCourse.value.code = ''
  }
})

const addCourse = () => {
  if (!newCourse.value.name || !newCourse.value.date) return
  addForm.value.courses.push({ ...newCourse.value })
  newCourse.value = { name: '', code: '', date: '', time: '', room: '', inv: '', notes: '' }
  selectedCourseId.value = ''
  showAddCourseModal.value = false
}

const isSubmitting = ref(false)
const submitError = ref('')

const confirmChecks = ref({
  dates: false,
  rooms: false,
  invigilators: false,
  notify: false,
})

const allConfirmed = computed(() =>
  confirmChecks.value.dates &&
  confirmChecks.value.rooms &&
  confirmChecks.value.invigilators &&
  confirmChecks.value.notify
)

const submitSchedule = async () => {
  if (!allConfirmed.value) {
    submitError.value = 'Please confirm all checkboxes before publishing.'
    return
  }
  isSubmitting.value = true
  submitError.value = ''
  try {
    await apiClient.post('/dept-head/exams', addForm.value)
    await fetchExams()
    // Reset form for next use
    addForm.value.courses = []
    addForm.value.title = 'Semester I Mid Examination Schedule'
    addForm.value.description = ''
    confirmChecks.value = { dates: false, rooms: false, invigilators: false, notify: false }
    currentView.value = 'list'
  } catch (error: any) {
    submitError.value = error?.response?.data?.message || 'Failed to publish schedule. Please try again.'
    console.error('Failed to create schedule:', error)
  } finally {
    isSubmitting.value = false
  }
}

const totalItems = computed(() => exams.value.length)
const totalPages = computed(() => Math.ceil(totalItems.value / perPage))

const statusBadge = (status: string) => {
  if (status === 'Scheduled') return 'text-emerald-600 bg-emerald-50'
  if (status === 'Conflict')  return 'text-rose-600 bg-rose-50'
  return 'text-slate-600 bg-slate-50'
}

const calIcon  = 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
const bellIcon = 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'
</script>

<template>
  <div>

    <!-- ══════════════════════════════════════════════════
         LIST VIEW
    ══════════════════════════════════════════════════ -->
    <div v-if="currentView === 'list'" class="space-y-6">

      <!-- Page Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-[22px] font-bold text-slate-800">Exam Schedule</h1>
          <p class="text-[13px] text-slate-500 mt-1">Manage and monitor exam schedules for your department.</p>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-4 gap-6">
        <div v-for="stat in stats" :key="stat.label" class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center gap-4">
            <div :class="[stat.bg, stat.iconColor, 'w-12 h-12 rounded-xl flex items-center justify-center shrink-0']">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon"></path></svg>
            </div>
            <div>
              <p class="text-[12px] font-bold text-slate-500">{{ stat.label }}</p>
              <h3 class="text-[24px] font-bold text-slate-800 leading-tight mt-1">{{ stat.value }}</h3>
              <p :class="[stat.color, 'text-[11px] font-bold mt-1']">{{ stat.change }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Table Card -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        <!-- Toolbar -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-4">
          <div class="flex items-center gap-4 flex-1">
            <!-- Search -->
            <div class="relative w-80 shrink-0">
              <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
              <input type="text" placeholder="Search exams by title, course or code..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-slate-600 placeholder:text-slate-400" />
            </div>
            <!-- Dropdowns -->
            <div class="relative w-40 shrink-0">
              <select class="w-full appearance-none px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-[13px] font-medium text-slate-700 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer">
                <option>All Semesters</option>
              </select>
              <svg class="w-4 h-4 absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            <div class="relative w-40 shrink-0">
              <select class="w-full appearance-none px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-[13px] font-medium text-slate-700 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer">
                <option>All Courses</option>
              </select>
              <svg class="w-4 h-4 absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            <div class="relative w-36 shrink-0">
              <select class="w-full appearance-none px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-[13px] font-medium text-slate-700 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer">
                <option>All Status</option>
              </select>
              <svg class="w-4 h-4 absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            <!-- Filter btn -->
            <button class="flex items-center gap-2 px-4 py-2.5 border border-[#5138ed] text-[#5138ed] rounded-xl text-[13px] font-bold hover:bg-indigo-50 transition-colors shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
              Filter
            </button>
          </div>
          <!-- Schedule btn -->
          <button @click="currentView = 'schedule'" class="flex items-center gap-2 bg-[#5138ed] text-white px-5 py-2.5 rounded-xl text-[13px] font-bold hover:bg-indigo-600 transition-colors shadow-sm shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Schedule Exam
          </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full min-w-[1000px]">
            <thead class="bg-slate-50 border-b border-slate-100">
              <tr>
                <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Exam Title</th>
                <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course Code</th>
                <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course</th>
                <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Date &amp; Time</th>
                <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Duration</th>
                <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Room</th>
                <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Students</th>
                <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                <th class="text-right px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
              <tr v-for="exam in exams" :key="exam.id" class="hover:bg-slate-50/80 transition-colors">
                <td class="px-6 py-4">
                  <span class="text-[13px] font-bold text-slate-700">{{ exam.title }}</span>
                </td>
                <td class="px-6 py-4 text-[13px] font-bold text-slate-600">{{ exam.code }}</td>
                <td class="px-6 py-4 text-[13px] text-slate-600 font-medium">{{ exam.course }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-1.5 mb-0.5">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="calIcon"></path></svg>
                    <span class="text-[13px] font-bold text-slate-700">{{ exam.date }}</span>
                  </div>
                  <div class="text-[12px] font-medium text-[#5138ed]">{{ exam.time }}</div>
                </td>
                <td class="px-6 py-4 text-[13px] text-slate-600 font-medium">{{ exam.duration }}</td>
                <td class="px-6 py-4 text-[13px] text-slate-600 font-medium">{{ exam.room }}</td>
                <td class="px-6 py-4 text-[13px] font-bold text-slate-600">{{ exam.students }}</td>
                <td class="px-6 py-4">
                  <span :class="[statusBadge(exam.status), 'text-[11px] font-bold px-2.5 py-1 rounded-md']">{{ exam.status }}</span>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-end gap-2">
                    <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                    <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-amber-500 hover:bg-amber-50 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </button>
                    <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
          <p class="text-[13px] font-medium text-slate-500">Showing 1 to {{ perPage }} of {{ totalItems }} exams</p>
          <div class="flex items-center gap-1">
            <button :disabled="currentPage === 1" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-white hover:text-slate-600 hover:shadow-sm transition-all border border-transparent hover:border-slate-200 disabled:opacity-40">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button
              v-for="page in totalPages" :key="page"
              @click="currentPage = page"
              :class="['w-8 h-8 flex items-center justify-center rounded-lg text-[13px] font-bold transition-all', currentPage === page ? 'bg-[#5138ed] text-white shadow-sm' : 'text-slate-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-slate-200']"
            >{{ page }}</button>
            <span class="w-8 h-8 flex items-center justify-center text-[13px] font-bold text-slate-400">...</span>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-600 text-[13px] font-bold hover:bg-white hover:shadow-sm border border-transparent hover:border-slate-200 transition-all">9</button>
            <button :disabled="currentPage === totalPages" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-white hover:text-slate-600 hover:shadow-sm transition-all border border-transparent hover:border-slate-200 disabled:opacity-40">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
          </div>
        </div>

      </div><!-- end Table Card -->

    </div><!-- end LIST VIEW -->


    <!-- ══════════════════════════════════════════════════
         SCHEDULE EXAM VIEW
    ══════════════════════════════════════════════════ -->
    <div v-else-if="currentView === 'schedule'" class="space-y-6">

      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-[22px] font-bold text-slate-800">Schedule New Examination</h1>
          <p class="text-[13px] text-slate-500 mt-1">Create a new examination schedule for your department.</p>
        </div>
        <button @click="currentView = 'list'" class="flex items-center gap-2 px-4 py-2 text-[12px] font-bold text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          Back to Schedule
        </button>
      </div>

      <!-- 2-Step Stepper -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-8 py-5 flex items-center">
        <!-- Step 1 active -->
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-[#5138ed] text-white flex items-center justify-center font-bold text-[13px] shadow-sm">1</div>
          <span class="text-[#5138ed] font-bold text-[14px]">Exam Information</span>
        </div>
        <!-- Connector line -->
        <div class="flex-1 mx-6 h-px bg-slate-200"></div>
        <!-- Step 2 inactive -->
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full border-2 border-slate-200 text-slate-400 flex items-center justify-center font-bold text-[13px]">2</div>
          <span class="text-slate-400 font-bold text-[14px]">Review &amp; Confirm</span>
        </div>
      </div>

      <!-- Two-column body -->
      <div class="flex gap-6 items-start">

        <!-- ── Left: Form ── -->
        <div class="flex-1 min-w-0 space-y-6">

          <!-- Exam Information card -->
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex gap-4 mb-6">
              <div class="w-10 h-10 rounded-xl bg-indigo-50 text-[#5138ed] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
              </div>
              <div>
                <h3 class="text-[15px] font-bold text-slate-800">Exam Information</h3>
                <p class="text-[12px] text-slate-500 mt-0.5">Configure the details for this examination session.</p>
              </div>
            </div>

            <!-- Row 1: Academic Year / Semester / Exam Type / Year Level -->
            <div class="grid grid-cols-4 gap-4 mb-4">
              <div class="space-y-1.5">
                <label class="text-[12px] font-bold text-slate-700">Academic Year <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <input type="text" v-model="addForm.academic_year" disabled class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-[13px] text-slate-500 focus:outline-none cursor-not-allowed font-medium" />
                </div>
              </div>
              <div class="space-y-1.5">
                <label class="text-[12px] font-bold text-slate-700">Semester <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <input type="text" v-model="addForm.semester" disabled class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-[13px] text-slate-500 focus:outline-none cursor-not-allowed font-medium" />
                </div>
              </div>
              <div class="space-y-1.5">
                <label class="text-[12px] font-bold text-slate-700">Exam Type <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select v-model="addForm.exam_type" class="w-full appearance-none px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors cursor-pointer">
                    <option>Mid Examination</option>
                    <option>Final Examination</option>
                    <option>Quiz</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div class="space-y-1.5">
                <label class="text-[12px] font-bold text-slate-700">Year Level <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select v-model="addForm.year_level" class="w-full appearance-none px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors cursor-pointer">
                    <option>1st Year</option>
                    <option>2nd Year</option>
                    <option>3rd Year</option>
                    <option>4th Year</option>
                    <option>5th Year</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
            </div>

            <!-- Row 2: Department / Faculty -->
            <div class="grid grid-cols-2 gap-4 mb-4">
              <div class="space-y-1.5">
                <label class="text-[12px] font-bold text-slate-700">Department</label>
                <input type="text" v-model="addForm.department" disabled class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-[13px] text-slate-500 focus:outline-none cursor-not-allowed font-medium" />
              </div>
              <div class="space-y-1.5">
                <label class="text-[12px] font-bold text-slate-700">Faculty</label>
                <input type="text" v-model="addForm.faculty" disabled class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-[13px] text-slate-500 focus:outline-none cursor-not-allowed font-medium" />
              </div>
            </div>

            <!-- Row 3: Schedule Title -->
            <div class="space-y-1.5 mb-4">
              <label class="text-[12px] font-bold text-slate-700">Schedule Title <span class="text-rose-500">*</span></label>
              <input type="text" v-model="addForm.title" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors" />
            </div>

            <!-- Row 4: Start Date / End Date -->
            <div class="grid grid-cols-2 gap-4 mb-4">
              <div class="space-y-1.5">
                <label class="text-[12px] font-bold text-slate-700">Start Date <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <input type="date" v-model="addForm.start_date" class="w-full pl-3 pr-10 py-2.5 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors" />
                </div>
              </div>
              <div class="space-y-1.5">
                <label class="text-[12px] font-bold text-slate-700">End Date <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <input type="date" v-model="addForm.end_date" class="w-full pl-3 pr-10 py-2.5 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors" />
                </div>
              </div>
            </div>

            <!-- Row 5: Description -->
            <div class="space-y-1.5">
              <label class="text-[12px] font-bold text-slate-700">Description <span class="text-slate-400 font-normal">(optional)</span></label>
              <textarea rows="3" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors resize-none">Semester I Mid Examination Schedule for all undergraduate programs in the Department of Computer Science.</textarea>
            </div>
          </div>

          <!-- Scheduled Courses card -->
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <!-- Card header -->
            <div class="flex items-center justify-between mb-5">
              <div class="flex gap-4 items-center">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-[#5138ed] flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <div>
                  <h3 class="text-[15px] font-bold text-slate-800">Scheduled Courses</h3>
                  <p class="text-[12px] text-slate-500 mt-0.5">Add all courses included in this examination session.</p>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <span class="px-3 py-1 bg-indigo-600 text-white text-[11px] font-bold rounded-full">● MID EXAM</span>
                <button @click="showAddCourseModal = true" class="flex items-center gap-1.5 bg-[#5138ed] text-white px-4 py-2 rounded-lg text-[12px] font-bold hover:bg-indigo-600 transition-colors shadow-sm">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                  Add Course
                </button>
              </div>
            </div>

            <!-- Courses table -->
            <table class="w-full">
              <thead>
                <tr class="border-b border-slate-100">
                  <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course Title</th>
                  <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course Code</th>
                  <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Exam Date</th>
                  <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Time</th>
                  <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Invigilator</th>
                  <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Room</th>
                  <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50">
                <tr v-for="(course, index) in addForm.courses" :key="index" class="hover:bg-slate-50/60 transition-colors">
                  <td class="py-3 text-[13px] font-semibold text-slate-700">{{ course.name }}</td>
                  <td class="py-3 text-[13px] text-slate-600">{{ course.code }}</td>
                  <td class="py-3">
                    <div class="flex items-center gap-1.5 text-[12px] text-slate-600">
                      <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="calIcon"></path></svg>
                      {{ course.date }}
                    </div>
                  </td>
                  <td class="py-3">
                    <div class="flex items-center gap-1.5 text-[12px] text-slate-600">
                      <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                      {{ course.time }}
                    </div>
                  </td>
                  <td class="py-3 text-[12px] text-slate-600">{{ course.inv ?? 'TBD' }}</td>
                  <td class="py-3 text-[12px] text-slate-600">{{ course.room }}</td>
                  <td class="py-3">
                    <button class="w-7 h-7 flex items-center justify-center rounded-lg text-rose-400 hover:bg-rose-50 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>

            <!-- Add more link -->
            <button @click="showAddCourseModal = true" class="mt-3 flex items-center gap-1.5 text-[#5138ed] text-[12px] font-bold hover:underline">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
              Add Course to schedule more
            </button>

            <!-- No conflicts bar -->
            <div class="mt-4 flex items-center gap-3 bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-3">
              <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <span class="text-[13px] font-bold text-emerald-700">No scheduling conflicts detected. All good!</span>
            </div>
          </div>

          <!-- Bottom action bar -->
          <div class="flex items-center justify-between pb-6">
            <button @click="currentView = 'list'" class="px-6 py-2.5 border border-slate-200 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 transition-colors">
              Cancel
            </button>
            <div class="flex items-center gap-3">
              <button class="flex items-center gap-2 px-5 py-2.5 border border-slate-300 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Save Draft
              </button>
              <button @click="currentView = 'review'" class="flex items-center gap-2 px-6 py-2.5 bg-[#5138ed] text-white rounded-xl text-[13px] font-bold hover:bg-indigo-600 transition-colors shadow-sm">
                Next
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
              </button>
            </div>
          </div>

        </div><!-- end Left form column -->

        <!-- ── Right: Schedule Summary sidebar ── -->
        <div class="w-64 shrink-0 space-y-4">
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <h3 class="text-[14px] font-bold text-slate-800 mb-4">Schedule Summary</h3>
            <div class="space-y-4">
              <!-- Exam Type -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                  </div>
                  <span class="text-[12px] text-slate-500 font-medium">Exam Type</span>
                </div>
                <span class="text-[11px] font-bold text-[#5138ed] bg-indigo-50 px-2.5 py-1 rounded-lg">MID EXAM</span>
              </div>
              <!-- Semester -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  </div>
                  <span class="text-[12px] text-slate-500 font-medium">Semester</span>
                </div>
                <span class="text-[12px] font-bold text-slate-700">{{ addForm.semester }}</span>
              </div>
              <!-- Academic Year -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                  </div>
                  <span class="text-[12px] text-slate-500 font-medium">Academic Year</span>
                </div>
                <span class="text-[12px] font-bold text-slate-700">{{ addForm.academic_year }}</span>
              </div>
              <!-- Courses Added -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                  </div>
                  <span class="text-[12px] text-slate-500 font-medium">Courses Added</span>
                </div>
                <span class="text-[12px] font-bold text-slate-700">{{ addForm.courses.length }}</span>
              </div>
              <!-- Divider -->
              <div class="border-t border-slate-100"></div>
              <!-- Schedule Status -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  </div>
                  <span class="text-[12px] text-slate-500 font-medium">Schedule Status</span>
                </div>
                <span class="text-[11px] font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg">DRAFT</span>
              </div>
              <!-- Conflicts -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                  </div>
                  <span class="text-[12px] text-slate-500 font-medium">Conflicts</span>
                </div>
                <div class="flex items-center gap-1.5 text-emerald-600">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                  <span class="text-[12px] font-bold">None</span>
                </div>
              </div>
            </div>
          </div>
        </div><!-- end Right sidebar -->

      </div><!-- end two-column body -->

    </div><!-- end SCHEDULE EXAM VIEW -->

    <!-- ══════════════════════════════════════════════════
         REVIEW & CONFIRM VIEW
    ══════════════════════════════════════════════════ -->
    <div v-else-if="currentView === 'review'" class="space-y-6">
      
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-[22px] font-bold text-slate-800">Review &amp; Confirm Schedule</h1>
          <p class="text-[13px] text-slate-500 mt-1">Review every examination before publishing the schedule to students.</p>
        </div>
        <button @click="currentView = 'schedule'" class="flex items-center gap-2 px-4 py-2 text-[12px] font-bold text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          Back to Schedule
        </button>
      </div>

      <!-- 2-Step Stepper -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-8 py-5 flex items-center">
        <!-- Step 1 completed -->
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-indigo-50 text-[#5138ed] flex items-center justify-center font-bold text-[13px] shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
          </div>
          <div class="flex flex-col">
            <span class="text-slate-800 font-bold text-[14px] leading-none">Exam Information</span>
            <span class="text-slate-500 text-[11px] mt-1.5 leading-none">Completed</span>
          </div>
        </div>
        <!-- Connector line -->
        <div class="flex-1 mx-8 h-[2px] bg-[#5138ed]"></div>
        <!-- Step 2 active -->
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-full bg-[#5138ed] text-white flex items-center justify-center font-bold text-[13px] shadow-sm">2</div>
          <div class="flex flex-col">
            <span class="text-[#5138ed] font-bold text-[14px] leading-none">Review &amp; Confirm</span>
            <span class="text-slate-500 text-[11px] mt-1.5 leading-none">Current Step</span>
          </div>
        </div>
      </div>

      <!-- Two-column body -->
      <div class="flex gap-6 items-start">
        
        <!-- ── Left: Review Forms ── -->
        <div class="flex-1 min-w-0 space-y-6">
          
          <!-- Schedule Summary -->
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex gap-3 items-center mb-6">
              <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
              <h3 class="text-[15px] font-bold text-slate-800">Schedule Summary</h3>
            </div>
            
            <div class="grid grid-cols-4 gap-6 gap-y-7">
              <div class="space-y-1">
                <p class="text-[12px] font-bold text-slate-500">Academic Year</p>
                <p class="text-[13px] font-bold text-slate-800">{{ addForm.academic_year }}</p>
              </div>
              <div class="space-y-1">
                <p class="text-[12px] font-bold text-slate-500">Semester</p>
                <p class="text-[13px] font-bold text-slate-800">{{ addForm.semester }}</p>
              </div>
              <div class="space-y-1">
                <p class="text-[12px] font-bold text-slate-500">Exam Type</p>
                <p class="text-[11px] font-bold text-[#5138ed] bg-indigo-50 px-2.5 py-1 rounded-lg inline-block">{{ addForm.exam_type.toUpperCase() }}</p>
              </div>
              <div class="space-y-1">
                <p class="text-[12px] font-bold text-slate-500">Year Level</p>
                <p class="text-[13px] font-bold text-slate-800">{{ addForm.year_level }}</p>
              </div>
              <div class="space-y-1">
                <p class="text-[12px] font-bold text-slate-500">Department</p>
                <p class="text-[13px] font-bold text-slate-800">{{ addForm.department }}</p>
              </div>
              
              <div class="space-y-1">
                <p class="text-[12px] font-bold text-slate-500">Faculty</p>
                <p class="text-[13px] font-bold text-slate-800">{{ addForm.faculty }}</p>
              </div>
              <div class="space-y-1 col-span-2">
                <p class="text-[12px] font-bold text-slate-500">Schedule Title</p>
                <p class="text-[13px] font-bold text-slate-800">{{ addForm.title }}</p>
              </div>
              <div class="space-y-1">
                <p class="text-[12px] font-bold text-slate-500">Total Courses</p>
                <p class="text-[13px] font-bold text-slate-800">{{ addForm.courses.length }}</p>
              </div>
              <div class="space-y-1">
                <p class="text-[12px] font-bold text-slate-500">Total Students</p>
                <p class="text-[13px] font-bold text-slate-800">TBD</p>
              </div>
              
              <div class="space-y-1 col-span-2 mt-2">
                <p class="text-[12px] font-bold text-slate-500">Schedule Period</p>
                <div class="flex items-center gap-1.5 mt-0.5 text-slate-800">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="calIcon"></path></svg>
                  <p class="text-[13px] font-bold">Jun 02, 2026 – Jun 10, 2026</p>
                </div>
              </div>
              <div class="space-y-1 col-span-2 mt-2">
                <p class="text-[12px] font-bold text-slate-500">Status</p>
                <div class="flex items-center gap-2 mt-0.5">
                  <p class="text-[11px] font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg inline-block">DRAFT</p>
                  <span class="text-[12px] font-medium text-slate-500">(Not Published)</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Course Schedule Review -->
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 overflow-hidden">
            <div class="flex gap-3 items-center mb-5">
              <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
              <h3 class="text-[15px] font-bold text-slate-800">Course Schedule Review</h3>
            </div>
            
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="border-b border-slate-100">
                    <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">#</th>
                    <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course Title</th>
                    <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course Code</th>
                    <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Exam Date</th>
                    <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Time</th>
                    <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Duration</th>
                    <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Room</th>
                    <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Invigilator</th>
                    <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Students</th>
                    <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                    <th class="text-left pb-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Action</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr v-for="(row, i) in addForm.courses" :key="i" class="hover:bg-slate-50/60 transition-colors">
                    <td class="py-3 pr-2 text-[12px] font-bold text-slate-500">{{ i+1 }}</td>
                    <td class="py-3 pr-2 text-[12px] font-bold text-slate-700 whitespace-nowrap">{{ row.name }}</td>
                    <td class="py-3 pr-2 text-[12px] font-bold text-slate-600">{{ row.code }}</td>
                    <td class="py-3 pr-2 text-[12px] text-slate-600 whitespace-nowrap">{{ row.date }}</td>
                    <td class="py-3 pr-2 text-[12px] text-[#5138ed] font-medium whitespace-nowrap">{{ row.time }}</td>
                    <td class="py-3 pr-2 text-[12px] text-slate-600 whitespace-nowrap">2 hrs</td>
                    <td class="py-3 pr-2 text-[12px] text-slate-600 whitespace-nowrap">{{ row.room }}</td>
                    <td class="py-3 pr-2 text-[12px] text-slate-600 whitespace-nowrap">{{ row.inv ?? 'TBD' }}</td>
                    <td class="py-3 pr-2 text-[12px] font-bold text-slate-600">TBD</td>
                    <td class="py-3 pr-2">
                      <span class="text-[10px] font-bold text-emerald-600 border border-emerald-100 bg-emerald-50 px-2.5 py-1 rounded-lg inline-block">Ready</span>
                    </td>
                    <td class="py-3">
                      <button class="w-7 h-7 flex items-center justify-center rounded-lg text-[#5138ed] hover:bg-indigo-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Conflict Validation -->
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex gap-3 items-center mb-6">
              <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
              <h3 class="text-[15px] font-bold text-slate-800">Conflict Validation</h3>
            </div>
            
            <div class="grid grid-cols-5 gap-4 mb-6">
              <div class="flex flex-col items-center justify-center text-center gap-2 p-2">
                <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-[11px] font-medium text-slate-600 leading-tight">No classroom<br/>conflicts</p>
              </div>
              <div class="flex flex-col items-center justify-center text-center gap-2 p-2 border-l border-slate-100">
                <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-[11px] font-medium text-slate-600 leading-tight">No instructor<br/>conflicts</p>
              </div>
              <div class="flex flex-col items-center justify-center text-center gap-2 p-2 border-l border-slate-100">
                <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-[11px] font-medium text-slate-600 leading-tight">No overlapping<br/>exams</p>
              </div>
              <div class="flex flex-col items-center justify-center text-center gap-2 p-2 border-l border-slate-100">
                <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-[11px] font-medium text-slate-600 leading-tight">All rooms<br/>available</p>
              </div>
              <div class="flex flex-col items-center justify-center text-center gap-2 p-2 border-l border-slate-100">
                <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-[11px] font-medium text-slate-600 leading-tight">All invigilators<br/>assigned</p>
              </div>
            </div>

            <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-100 px-4 py-3 rounded-xl">
              <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <span class="text-[13px] font-bold text-emerald-700">Great! No conflicts found. Your schedule is ready to publish.</span>
            </div>
          </div>

          <!-- Bottom Row: Notification Preview & Final Confirmation -->
          <div class="flex gap-6">
            
            <!-- Notification Preview -->
            <div class="flex-1 bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
              <div class="flex gap-3 items-center mb-5">
                <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <h3 class="text-[15px] font-bold text-slate-800">Notification Preview</h3>
              </div>

              <div class="flex items-start gap-10">
                <div class="flex-1 space-y-4">
                  <div class="space-y-1">
                    <p class="text-[11px] font-bold text-slate-800">Title</p>
                    <p class="text-[12px] text-[#5138ed] font-bold">{{ addForm.title }}</p>
                  </div>
                  <div class="space-y-1">
                    <p class="text-[11px] font-bold text-slate-800">Message</p>
                    <p class="text-[12px] text-slate-600 leading-relaxed pr-4">Your examination schedule has been published.<br/>Please review your exam dates, rooms and times carefully.</p>
                  </div>
                </div>

                <div class="space-y-4">
                  <div class="space-y-1.5">
                    <p class="text-[11px] font-bold text-slate-800">Recipients</p>
                    <div class="flex items-center gap-1.5 text-[12px] font-bold text-slate-600">
                      <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                      TBD Students
                    </div>
                    <div class="flex items-center gap-1.5 text-[12px] font-bold text-slate-600">
                      <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                      18 Instructors
                    </div>
                  </div>
                  <div class="space-y-1.5">
                    <p class="text-[11px] font-bold text-slate-800">Delivery</p>
                    <div class="flex items-center gap-1.5 text-[12px] font-bold text-slate-600">
                      <svg class="w-3.5 h-3.5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                      Email
                    </div>
                    <div class="flex items-center gap-1.5 text-[12px] font-bold text-slate-600">
                      <svg class="w-3.5 h-3.5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                      Portal Notification
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Final Confirmation -->
            <div class="flex-1 bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
              <div class="flex gap-3 items-center mb-5">
                <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-[15px] font-bold text-slate-800">Final Confirmation</h3>
              </div>

              <div class="space-y-4 mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                  <input type="checkbox" v-model="confirmChecks.dates" class="w-4 h-4 rounded border-slate-300 text-[#5138ed] focus:ring-[#5138ed]" />
                  <span class="text-[12px] text-slate-700">I confirm all examination dates are correct.</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                  <input type="checkbox" v-model="confirmChecks.rooms" class="w-4 h-4 rounded border-slate-300 text-[#5138ed] focus:ring-[#5138ed]" />
                  <span class="text-[12px] text-slate-700">I confirm rooms have been assigned.</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                  <input type="checkbox" v-model="confirmChecks.invigilators" class="w-4 h-4 rounded border-slate-300 text-[#5138ed] focus:ring-[#5138ed]" />
                  <span class="text-[12px] text-slate-700">I confirm invigilators have been assigned.</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                  <input type="checkbox" v-model="confirmChecks.notify" class="w-4 h-4 rounded border-slate-300 text-[#5138ed] focus:ring-[#5138ed]" />
                  <span class="text-[12px] text-slate-700">I understand publishing will notify all students and instructors.</span>
                </label>
              </div>

              <!-- Error message -->
              <p v-if="submitError" class="text-[12px] text-rose-600 font-medium mb-3 bg-rose-50 border border-rose-100 px-3 py-2 rounded-lg">{{ submitError }}</p>

              <button
                @click="submitSchedule"
                :disabled="!allConfirmed || isSubmitting"
                :class="[
                  'w-full py-3 rounded-xl font-bold text-[13px] transition-all',
                  allConfirmed && !isSubmitting
                    ? 'bg-[#5138ed] text-white hover:bg-indigo-600 shadow-sm cursor-pointer'
                    : 'bg-indigo-50 text-indigo-300 cursor-not-allowed'
                ]"
              >
                <span v-if="isSubmitting" class="flex items-center justify-center gap-2">
                  <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                  Publishing...
                </span>
                <span v-else>Publish Schedule</span>
              </button>
            </div>

          </div>

          <!-- Bottom Action Bar -->
          <div class="flex items-center justify-end gap-3 pb-6 mt-6 border-t border-slate-100 pt-6">
            <button @click="currentView = 'schedule'" class="flex items-center gap-2 px-6 py-2.5 border border-slate-200 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 transition-colors mr-auto">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
              Back
            </button>
            <button class="flex items-center gap-2 px-5 py-2.5 border border-slate-300 text-slate-700 rounded-xl text-[13px] font-bold hover:bg-slate-50 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
              Save Draft
            </button>
            <button
              @click="submitSchedule"
              :disabled="!allConfirmed || isSubmitting"
              :class="[
                'flex items-center gap-2 px-6 py-2.5 rounded-xl text-[13px] font-bold transition-all',
                allConfirmed && !isSubmitting
                  ? 'bg-[#5138ed] text-white hover:bg-indigo-600 shadow-sm cursor-pointer'
                  : 'bg-indigo-100 text-indigo-300 cursor-not-allowed'
              ]"
            >
              <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
              {{ isSubmitting ? 'Publishing...' : 'Publish Schedule' }}
            </button>
          </div>

        </div><!-- end Left: Review Forms -->
        
        <!-- ── Right: Schedule Overview sidebar ── -->
        <div class="w-64 shrink-0 space-y-4">
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <h3 class="text-[14px] font-bold text-slate-800 mb-6">Schedule Overview</h3>
            <div class="space-y-5">
              <!-- Exam Type -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                  <span class="text-[12px] text-slate-600 font-medium">Exam Type</span>
                </div>
                <span class="text-[11px] font-bold text-[#5138ed] bg-indigo-50 px-2 py-0.5 rounded-lg">{{ addForm.exam_type.toUpperCase() }}</span>
              </div>
              <!-- Year Level -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                  <span class="text-[12px] text-slate-600 font-medium">Year Level</span>
                </div>
                <span class="text-[12px] font-bold text-slate-700">{{ addForm.year_level }}</span>
              </div>
              <!-- Academic Year -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                  <span class="text-[12px] text-slate-600 font-medium">Academic Year</span>
                </div>
                <span class="text-[12px] font-bold text-slate-700">{{ addForm.academic_year }}</span>
              </div>
              <!-- Semester -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  <span class="text-[12px] text-slate-600 font-medium">Semester</span>
                </div>
                <span class="text-[12px] font-bold text-slate-700">{{ addForm.semester }}</span>
              </div>
              
              <!-- Divider -->
              <div class="border-t border-slate-100 pt-2"></div>
              
              <!-- Courses -->
              <div class="flex items-center justify-between mt-2">
                <div class="flex items-center gap-2.5">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                  <span class="text-[12px] text-slate-600 font-medium">Courses</span>
                </div>
                <span class="text-[12px] font-bold text-slate-700">{{ addForm.courses.length }}</span>
              </div>
              <!-- Students -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                  <span class="text-[12px] text-slate-600 font-medium">Students</span>
                </div>
                <span class="text-[12px] font-bold text-slate-700">TBD</span>
              </div>
              <!-- Invigilators -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                  <span class="text-[12px] text-slate-600 font-medium">Invigilators</span>
                </div>
                <span class="text-[12px] font-bold text-slate-700">18</span>
              </div>
              <!-- Rooms -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                  <span class="text-[12px] text-slate-600 font-medium">Rooms</span>
                </div>
                <span class="text-[12px] font-bold text-slate-700">{{ addForm.courses.length }}</span>
              </div>
              
              <!-- Divider -->
              <div class="border-t border-slate-100 pt-2"></div>
              
              <!-- Schedule Duration -->
              <div class="space-y-2 mt-2">
                <div class="flex items-center gap-2.5">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="calIcon"></path></svg>
                  <span class="text-[12px] text-slate-600 font-medium">Schedule Duration</span>
                </div>
                <div class="pl-6.5 text-[12px] font-bold text-slate-700">
                  Jun 02 - Jun 10, 2026<br/>
                  <span class="text-[11px] font-medium text-slate-500 font-normal">(9 Days)</span>
                </div>
              </div>
              
              <!-- Divider -->
              <div class="border-t border-slate-100 pt-2"></div>

              <!-- Conflict Status -->
              <div class="flex items-center justify-between mt-2">
                <div class="flex items-center gap-2.5">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                  <span class="text-[12px] text-slate-600 font-medium">Conflict Status</span>
                </div>
                <div class="flex items-center gap-1.5 text-emerald-600">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                  <span class="text-[12px] font-bold">None</span>
                </div>
              </div>
              
              <!-- Publish Status -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  <span class="text-[12px] text-slate-600 font-medium">Publish Status</span>
                </div>
                <span class="text-[10px] font-bold text-amber-600">DRAFT</span>
              </div>
            </div>
          </div>
        </div><!-- end Right sidebar -->

      </div><!-- end two-column body -->

    </div><!-- end REVIEW & CONFIRM VIEW -->

    <!-- ══════════════════════════════════════════════════
         ADD COURSE MODAL
    ══════════════════════════════════════════════════ -->
    <div v-if="showAddCourseModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showAddCourseModal = false"></div>
      
      <!-- Modal Content -->
      <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-[520px] overflow-hidden flex flex-col max-h-[90vh]">
        
        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-start justify-between">
          <div>
            <h3 class="text-[18px] font-bold text-slate-800">Add Course to Schedule</h3>
            <p class="text-[13px] text-slate-500 mt-0.5">Search and add course details to the examination schedule.</p>
          </div>
          <button @click="showAddCourseModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto space-y-5">
          <!-- Course -->
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
              <label class="text-[12px] font-bold text-slate-700">Course Name <span class="text-rose-500">*</span></label>
              <div class="relative">
                <select v-model="selectedCourseId" class="w-full appearance-none px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors cursor-pointer">
                  <option value="" disabled>Select a course</option>
                  <option v-for="course in filteredCourses" :key="course.id" :value="course.id">
                    {{ course.title }}
                  </option>
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
            <div class="space-y-1.5">
              <label class="text-[12px] font-bold text-slate-700">Course Code <span class="text-rose-500">*</span></label>
              <input type="text" v-model="newCourse.code" placeholder="e.g. CS401" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors placeholder:text-slate-400" />
            </div>
          </div>

          <!-- Exam Date & Time -->
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
              <label class="text-[12px] font-bold text-slate-700">Exam Date <span class="text-rose-500">*</span></label>
              <div class="relative">
                <input type="date" v-model="newCourse.date" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors placeholder:text-slate-400" />
              </div>
            </div>
            <div class="space-y-1.5">
              <label class="text-[12px] font-bold text-slate-700">Time <span class="text-rose-500">*</span></label>
              <div class="relative">
                <input type="text" v-model="newCourse.time" placeholder="e.g. 09:00 AM - 11:00 AM" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors placeholder:text-slate-400" />
              </div>
            </div>
          </div>

          <!-- Invigilator & Room -->
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
              <label class="text-[12px] font-bold text-slate-700">Invigilator <span class="text-rose-500">*</span></label>
              <div class="relative">
                <input type="text" v-model="newCourse.inv" placeholder="Search or select invigilator..." class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors placeholder:text-slate-400" />
              </div>
            </div>
            <div class="space-y-1.5">
              <label class="text-[12px] font-bold text-slate-700">Room <span class="text-rose-500">*</span></label>
              <div class="relative">
                <input type="text" v-model="newCourse.room" placeholder="Search or select room..." class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors placeholder:text-slate-400" />
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div class="space-y-1.5">
            <label class="text-[12px] font-bold text-slate-700">Notes <span class="text-slate-400 font-normal">(optional)</span></label>
            <div class="relative">
              <textarea v-model="newCourse.notes" rows="3" placeholder="Add any additional notes..." class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-700 focus:outline-none focus:border-indigo-500 transition-colors placeholder:text-slate-400 resize-none"></textarea>
              <span class="absolute bottom-2 right-3 text-[10px] font-medium text-slate-400">{{ newCourse.notes.length }} / 200</span>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-5 border-t border-slate-100 flex items-center justify-between bg-white">
          <button @click="showAddCourseModal = false" class="px-10 py-2.5 bg-white border border-slate-200 text-slate-700 font-bold text-[13px] rounded-xl hover:bg-slate-50 transition-colors">
            Cancel
          </button>
          <button @click="addCourse" class="px-10 py-2.5 bg-[#5138ed] text-white font-bold text-[13px] rounded-xl hover:bg-indigo-600 transition-colors shadow-sm w-36 text-center flex justify-center">
            Add Course
          </button>
        </div>

      </div>
    </div>

  </div>
</template>
