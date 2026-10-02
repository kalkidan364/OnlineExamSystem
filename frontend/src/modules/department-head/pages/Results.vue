<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import apiClient from '../../../core/api/apiClient'

// ── State ──
const search = ref('')
const semesterFilter = ref('all')
const statusFilter = ref('all')
const currentPage = ref(1)
const perPage = 10
const isLoading = ref(false)

const departmentInfo = ref({
  name: 'Software Engineering',
  code: 'SE',
  head_name: 'Department Head',
  academic_year: '2025 / 2026',
  total_courses: 0,
  total_students: 0,
})

const statsData = ref({
  total_exams: 0,
  completed_exams: 0,
  total_attempts: 0,
  average_score: 0,
  pass_rate: 0,
})

const resultsList = ref<any[]>([])

// ── Detail Modal State ──
const showDetailModal = ref(false)
const selectedExamDetails = ref<any>(null)
const studentResults = ref<any[]>([])
const isLoadingStudentResults = ref(false)

// ── Fetch Results ──
const fetchResults = async () => {
  isLoading.value = true
  try {
    const res = await apiClient.get('/dept-head/results')
    if (res.data?.data) {
      if (res.data.data.department) {
        departmentInfo.value = res.data.data.department
      }
      if (res.data.data.stats) {
        statsData.value = res.data.data.stats
      }
      resultsList.value = res.data.data.results || []
    }
  } catch (err) {
    console.error('Failed to fetch department results:', err)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchResults()
})

// ── Open Exam Student Details ──
const openExamResults = async (exam: any) => {
  selectedExamDetails.value = exam
  showDetailModal.value = true
  isLoadingStudentResults.value = true
  studentResults.value = []
  try {
    const res = await apiClient.get(`/dept-head/results/${exam.id}`)
    if (res.data?.data?.students) {
      studentResults.value = res.data.data.students
    }
  } catch (err) {
    console.error('Failed to load student exam results:', err)
  } finally {
    isLoadingStudentResults.value = false
  }
}

// ── Filtered & Paginated ──
const filtered = computed(() => {
  return resultsList.value.filter(r => {
    const q = search.value.trim().toLowerCase()
    const matchSearch = !q ||
      (r.title && r.title.toLowerCase().includes(q)) ||
      (r.code && r.code.toLowerCase().includes(q)) ||
      (r.course_name && r.course_name.toLowerCase().includes(q)) ||
      (r.course_code && r.course_code.toLowerCase().includes(q)) ||
      (r.instructor_name && r.instructor_name.toLowerCase().includes(q))

    const matchSemester = semesterFilter.value === 'all' || r.semester === semesterFilter.value
    const matchStatus = statusFilter.value === 'all' || (r.status && r.status.toLowerCase() === statusFilter.value.toLowerCase())

    return matchSearch && matchSemester && matchStatus
  })
})

watch([search, semesterFilter, statusFilter], () => {
  currentPage.value = 1
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))
const paginated = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filtered.value.slice(start, start + perPage)
})

const displayPages = computed(() => {
  const tp = totalPages.value
  if (tp <= 7) return Array.from({ length: tp }, (_, i) => i + 1)
  if (currentPage.value <= 4) return [1, 2, 3, 4, 5, '...', tp]
  if (currentPage.value >= tp - 3) return [1, '...', tp - 4, tp - 3, tp - 2, tp - 1, tp]
  return [1, '...', currentPage.value - 1, currentPage.value, currentPage.value + 1, '...', tp]
})

// ── Badges ──
const statusBadge = (s: string) => {
  const status = (s || '').toLowerCase()
  if (status === 'published') return 'bg-emerald-50 text-emerald-600'
  if (status === 'graded') return 'bg-indigo-50 text-[#5138ed]'
  if (status === 'completed') return 'bg-sky-50 text-sky-600'
  if (status === 'pending') return 'bg-amber-50 text-amber-600'
  return 'bg-slate-100 text-slate-600'
}

const gradeBadge = (g: string) => {
  const grade = (g || '').toUpperCase()
  if (grade.startsWith('A')) return 'bg-emerald-50 text-emerald-600'
  if (grade.startsWith('B')) return 'bg-sky-50 text-sky-600'
  if (grade.startsWith('C')) return 'bg-amber-50 text-amber-600'
  if (grade.startsWith('D')) return 'bg-orange-50 text-orange-600'
  return 'bg-rose-50 text-rose-600'
}

const printSheet = () => {
  window.print()
}
</script>

<template>
  <div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Results Management</h1>
        <p class="text-[13px] text-slate-500 mt-1">Review student performance and grades across department exams.</p>
      </div>
      <div class="flex items-center gap-3">
        <button @click="printSheet" class="flex items-center gap-2 border border-slate-200 hover:bg-slate-50 text-slate-700 text-[13px] font-bold px-4 py-2.5 rounded-xl transition-all shadow-sm bg-white">
          <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
          Print Sheet
        </button>
      </div>
    </div>

    <!-- Department Info Card -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm flex flex-wrap items-center justify-between gap-6">
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-[#5138ed] shrink-0">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <div>
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Department Overview</span>
          <h2 class="text-[18px] font-bold text-slate-800">{{ departmentInfo.name }} ({{ departmentInfo.code }})</h2>
          <p class="text-[12px] text-slate-500 font-medium mt-0.5">Department Head: {{ departmentInfo.head_name }}</p>
        </div>
      </div>
      <div class="flex items-center gap-8 pr-4">
        <div>
          <span class="text-[11px] font-semibold text-slate-400 block">Academic Year</span>
          <span class="text-[14px] font-bold text-slate-800">{{ departmentInfo.academic_year }}</span>
        </div>
        <div>
          <span class="text-[11px] font-semibold text-slate-400 block">Total Courses</span>
          <span class="text-[14px] font-bold text-slate-800">{{ departmentInfo.total_courses }}</span>
        </div>
        <div>
          <span class="text-[11px] font-semibold text-slate-400 block">Total Students</span>
          <span class="text-[14px] font-bold text-slate-800">{{ departmentInfo.total_students }}</span>
        </div>
      </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-4 gap-6">
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 flex items-center gap-5 hover:shadow-md transition-shadow">
        <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-[#5138ed] shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        </div>
        <div>
          <p class="text-[12px] font-semibold text-slate-500">Total Exams</p>
          <p class="text-[24px] font-bold text-slate-800 leading-tight mt-0.5">{{ statsData.total_exams }}</p>
          <p class="text-[11px] font-bold text-emerald-500 mt-1">{{ statsData.completed_exams }} completed</p>
        </div>
      </div>

      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 flex items-center gap-5 hover:shadow-md transition-shadow">
        <div class="w-14 h-14 rounded-2xl bg-sky-50 flex items-center justify-center text-sky-500 shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </div>
        <div>
          <p class="text-[12px] font-semibold text-slate-500">Student Attempts</p>
          <p class="text-[24px] font-bold text-slate-800 leading-tight mt-0.5">{{ statsData.total_attempts }}</p>
          <p class="text-[11px] font-bold text-emerald-500 mt-1">Graded Submissions</p>
        </div>
      </div>

      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 flex items-center gap-5 hover:shadow-md transition-shadow">
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-500 shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
          <p class="text-[12px] font-semibold text-slate-500">Overall Pass Rate</p>
          <p class="text-[24px] font-bold text-slate-800 leading-tight mt-0.5">{{ statsData.pass_rate }}%</p>
          <p class="text-[11px] font-bold text-emerald-500 mt-1">≥ 50% benchmark</p>
        </div>
      </div>

      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 flex items-center gap-5 hover:shadow-md transition-shadow">
        <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-500 shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path></svg>
        </div>
        <div>
          <p class="text-[12px] font-semibold text-slate-500">Average Score</p>
          <p class="text-[24px] font-bold text-slate-800 leading-tight mt-0.5">{{ statsData.average_score }}%</p>
          <p class="text-[11px] font-bold text-emerald-500 mt-1">Across all courses</p>
        </div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
      
      <!-- Filters -->
      <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100">
        <div class="relative flex-1 max-w-sm">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input v-model="search" type="text" placeholder="Search results by exam, course or instructor..." class="w-full pl-9 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] placeholder:text-slate-400">
        </div>

        <div class="relative">
          <select v-model="semesterFilter" class="pl-4 pr-8 py-2.5 text-[13px] font-medium border border-slate-200 rounded-xl text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed]">
            <option value="all">All Semesters</option>
            <option value="Semester 1">Semester 1</option>
            <option value="Semester 2">Semester 2</option>
          </select>
          <svg class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>

        <div class="relative">
          <select v-model="statusFilter" class="pl-4 pr-8 py-2.5 text-[13px] font-medium border border-slate-200 rounded-xl text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed]">
            <option value="all">All Status</option>
            <option value="graded">Graded</option>
            <option value="published">Published</option>
            <option value="completed">Completed</option>
            <option value="pending">Pending</option>
          </select>
          <svg class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>

        <button @click="fetchResults" class="flex items-center gap-2 text-[13px] font-bold text-[#5138ed] border border-indigo-200 hover:bg-indigo-50 px-4 py-2.5 rounded-xl transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          Refresh
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="p-12 text-center text-slate-500">
        <div class="inline-block animate-spin w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full mb-3"></div>
        <p class="text-[14px] font-medium">Loading department results...</p>
      </div>

      <!-- Table -->
      <table v-else class="w-full">
        <thead>
          <tr class="border-b border-slate-100">
            <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Exam Title</th>
            <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course</th>
            <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Instructor</th>
            <th class="text-center px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Submissions</th>
            <th class="text-center px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Avg Score</th>
            <th class="text-center px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pass Rate</th>
            <th class="text-center px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
            <th class="text-center px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <tr v-if="filtered.length === 0">
            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
              <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
              <p class="text-[14px] font-semibold text-slate-600">No examination results found</p>
              <p class="text-[12px] text-slate-400 mt-1">Try adjusting your filters or search keywords.</p>
            </td>
          </tr>
          <tr v-for="r in paginated" :key="r.id" class="hover:bg-slate-50/40 transition-colors">
            <td class="px-6 py-4">
              <span class="block text-[13px] font-bold text-slate-800">{{ r.title }}</span>
              <span class="block text-[11px] font-medium text-slate-400 mt-0.5">{{ r.code }}</span>
            </td>
            <td class="px-4 py-4">
              <span class="block text-[13px] font-semibold text-slate-700">{{ r.course_name }}</span>
              <span class="block text-[11px] font-medium text-slate-400 mt-0.5">{{ r.course_code }}</span>
            </td>
            <td class="px-4 py-4">
              <span class="text-[13px] font-medium text-slate-700">{{ r.instructor_name }}</span>
            </td>
            <td class="px-4 py-4 text-center">
              <span class="text-[13px] font-bold text-slate-800">{{ r.submitted_count }} / {{ r.total_students }}</span>
            </td>
            <td class="px-4 py-4 text-center">
              <span v-if="r.average_pct !== null" class="text-[13px] font-bold text-slate-800">{{ r.average_pct }}%</span>
              <span v-else class="text-[12px] text-slate-400">N/A</span>
            </td>
            <td class="px-4 py-4 text-center">
              <span v-if="r.pass_rate !== null" class="text-[13px] font-bold text-emerald-600">{{ r.pass_rate }}%</span>
              <span v-else class="text-[12px] text-slate-400">N/A</span>
            </td>
            <td class="px-4 py-4 text-center">
              <span :class="[statusBadge(r.status), 'text-[11px] font-bold px-2.5 py-1 rounded-md capitalize']">{{ r.status }}</span>
            </td>
            <td class="px-6 py-4 text-center">
              <button @click="openExamResults(r)" class="text-[12px] font-bold text-[#5138ed] hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                View Scores
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="flex items-center justify-between px-6 py-5 border-t border-slate-100 bg-white">
        <p class="text-[13px] text-slate-500 font-medium">
          Showing {{ filtered.length === 0 ? 0 : (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, filtered.length) }} of {{ filtered.length }} results
        </p>
        <div class="flex items-center gap-2">
          <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-slate-50 disabled:opacity-40 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
          </button>
          <template v-for="p in displayPages" :key="p">
            <span v-if="p === '...'" class="w-8 h-8 flex items-center justify-center text-slate-400 text-[13px]">...</span>
            <button v-else @click="currentPage = (p as number)" :class="[currentPage === p ? 'bg-[#5138ed] text-white border border-[#5138ed]' : 'text-slate-500 border border-slate-200 hover:bg-slate-50', 'w-8 h-8 rounded-lg text-[13px] font-bold transition-colors']">{{ p }}</button>
          </template>
          <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-slate-50 disabled:opacity-40 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </button>
        </div>
      </div>

    </div>

    <!-- ══════════════════════════ STUDENT RESULTS MODAL ══════════════════════════ -->
    <div v-if="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4 overflow-y-auto">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl overflow-hidden my-8">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 bg-slate-50/50">
          <div>
            <h3 class="text-[17px] font-bold text-slate-800">{{ selectedExamDetails?.title }}</h3>
            <p class="text-[12px] text-slate-500 mt-0.5">
              {{ selectedExamDetails?.course_name }} ({{ selectedExamDetails?.course_code }}) | Instructor: {{ selectedExamDetails?.instructor_name }}
            </p>
          </div>
          <button @click="showDetailModal = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
          <div class="flex items-center justify-between">
            <p class="text-[13px] font-bold text-slate-700">Enrolled Students & Performance</p>
            <span class="text-[12px] font-semibold text-slate-500">Total Marks: {{ selectedExamDetails?.total_marks }}</span>
          </div>

          <div v-if="isLoadingStudentResults" class="py-12 text-center text-slate-500">
            <div class="inline-block animate-spin w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full mb-3"></div>
            <p class="text-[13px] font-medium">Loading student grades...</p>
          </div>

          <div v-else class="border border-slate-100 rounded-xl overflow-hidden">
            <table class="w-full">
              <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                  <th class="text-left px-4 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">#</th>
                  <th class="text-left px-4 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Student Name</th>
                  <th class="text-left px-4 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">ID</th>
                  <th class="text-center px-4 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Score</th>
                  <th class="text-center px-4 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Percentage</th>
                  <th class="text-center px-4 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Grade</th>
                  <th class="text-center px-4 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                  <th class="text-left px-4 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Submitted On</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50">
                <tr v-if="studentResults.length === 0">
                  <td colspan="8" class="px-4 py-8 text-center text-slate-400 text-[13px]">
                    No student submissions found for this exam.
                  </td>
                </tr>
                <tr v-for="(stu, idx) in studentResults" :key="stu.id" class="hover:bg-slate-50/50 transition-colors">
                  <td class="px-4 py-3 text-[12px] font-semibold text-slate-500">{{ idx + 1 }}</td>
                  <td class="px-4 py-3">
                    <span class="block text-[13px] font-bold text-slate-800">{{ stu.name }}</span>
                    <span class="block text-[11px] text-slate-400">{{ stu.email }}</span>
                  </td>
                  <td class="px-4 py-3 text-[12px] font-medium text-slate-600">{{ stu.student_id }}</td>
                  <td class="px-4 py-3 text-center text-[13px] font-bold text-slate-800">
                    {{ stu.score !== null ? `${stu.score} / ${stu.total_marks}` : '-' }}
                  </td>
                  <td class="px-4 py-3 text-center text-[13px] font-bold text-slate-800">
                    {{ stu.percentage !== null ? `${stu.percentage}%` : '-' }}
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span :class="[gradeBadge(stu.grade), 'text-[11px] font-bold px-2 py-0.5 rounded']">{{ stu.grade }}</span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span :class="[statusBadge(stu.status), 'text-[11px] font-bold px-2 py-0.5 rounded capitalize']">{{ stu.status }}</span>
                  </td>
                  <td class="px-4 py-3 text-[12px] text-slate-500 font-medium">{{ stu.submitted_at }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/50">
          <span class="text-[12px] text-slate-500 font-medium">
            Total Students: {{ studentResults.length }}
          </span>
          <div class="flex items-center gap-3">
            <button @click="printSheet" class="flex items-center gap-2 border border-slate-200 hover:bg-slate-100 text-slate-700 text-[12px] font-bold px-4 py-2 rounded-xl transition-colors bg-white">
              <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
              Print Result Sheet
            </button>
            <button @click="showDetailModal = false" class="px-4 py-2 text-[12px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl transition-colors">
              Close
            </button>
          </div>
        </div>

      </div>
    </div>

  </div>
</template>
