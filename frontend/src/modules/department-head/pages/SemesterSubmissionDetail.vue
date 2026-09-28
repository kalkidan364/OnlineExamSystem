<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import apiClient from '../../../core/api/apiClient'

const route = useRoute()
const router = useRouter()

const submissionId = route.params.id as string

const isLoading = ref(true)
const isSubmittingAction = ref(false)
const searchQuery = ref('')
const selectedDepartment = ref('All Departments')
const selectedStatus = ref('All Statuses')
const selectedSemester = ref('2025/2026 — Second Semester')
const openMenuId = ref<number | null>(null)

// Toast notification state
const toast = ref<{ show: boolean; message: string; type: 'success' | 'error' | 'info' }>({
  show: false,
  message: '',
  type: 'success'
})

const showToast = (message: string, type: 'success' | 'error' | 'info' = 'success') => {
  toast.value = { show: true, message, type }
  setTimeout(() => {
    toast.value.show = false
  }, 4000)
}

// Modal States
const isGuidelinesModalOpen = ref(false)
const correctionModal = ref<{
  open: boolean
  instructor: any | null
  remarks: string
}>({
  open: false,
  instructor: null,
  remarks: ''
})

const rejectModal = ref<{
  open: boolean
  instructor: any | null
  remarks: string
}>({
  open: false,
  instructor: null,
  remarks: ''
})

// Summary Stats
const semesterInfo = ref({
  academicYear: '2025/2026',
  semester: 'Second Semester',
  department: 'Software Engineering',
  pendingReview: 0,
  approved: 0,
  correctionRequired: 0,
  total: 0,
})

// Real Instructors & Submissions
const instructors = ref<any[]>([])
const departmentsList = ref<string[]>([
  'All Departments',
  'Software Engineering',
  'Computer Science',
  'Mathematics',
  'Electrical Engineering'
])

const semestersList = ref<string[]>([
  '2025/2026 — Second Semester',
  '2025/2026 — First Semester',
  '2024/2025 — Second Semester',
  '2024/2025 — First Semester',
])

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(8)

// Fetch Data from Backend
const fetchSubmissions = async () => {
  isLoading.value = true
  try {
    const params: any = {}

    if (submissionId && submissionId !== 'all') {
      params.year_level = submissionId
    }

    if (selectedDepartment.value && selectedDepartment.value !== 'All Departments') {
      params.department = selectedDepartment.value
    } else {
      params.department = 'All Departments'
    }

    if (selectedStatus.value && selectedStatus.value !== 'All Statuses') {
      params.status = selectedStatus.value
    }

    if (selectedSemester.value) {
      const parts = selectedSemester.value.split('—').map(s => s.trim())
      if (parts[0]) params.academic_year = parts[0]
      if (parts[1]) params.semester = parts[1]
    }

    if (searchQuery.value) {
      params.search = searchQuery.value
    }

    const res = await apiClient.get('/dept-head/semester-submissions/details', { params })
    if (res.data) {
      instructors.value = res.data.instructors || []
      if (res.data.semester_info) {
        semesterInfo.value = res.data.semester_info
      }
      if (res.data.departments && res.data.departments.length > 0) {
        departmentsList.value = ['All Departments', ...res.data.departments]
      }
      if (res.data.semesters && res.data.semesters.length > 0) {
        semestersList.value = res.data.semesters
      }
    }
  } catch (error) {
    console.error('Failed to fetch semester submissions:', error)
    showToast('Failed to load semester submissions from server.', 'error')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchSubmissions()
  // Global click listener to close dropdowns
  window.addEventListener('click', closeAllMenus)
})

const closeAllMenus = () => {
  openMenuId.value = null
}

// Watch filters to refresh or filter
watch([selectedDepartment, selectedSemester], () => {
  currentPage.value = 1
  fetchSubmissions()
})

const filteredInstructors = computed(() => {
  return instructors.value.filter(inst => {
    const q = searchQuery.value.toLowerCase().trim()
    const matchesSearch = !q
      || inst.name?.toLowerCase().includes(q)
      || inst.email?.toLowerCase().includes(q)
      || inst.department?.toLowerCase().includes(q)
      || inst.course?.toLowerCase().includes(q)
      || inst.section?.toLowerCase().includes(q)

    const matchesDept = selectedDepartment.value === 'All Departments'
      || inst.department === selectedDepartment.value

    const matchesStatus = selectedStatus.value === 'All Statuses'
      || inst.status === selectedStatus.value

    return matchesSearch && matchesDept && matchesStatus
  })
})

const totalPages = computed(() => {
  return Math.ceil(filteredInstructors.value.length / itemsPerPage.value) || 1
})

const paginatedInstructors = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredInstructors.value.slice(start, start + itemsPerPage.value)
})

const goToPage = (p: number) => {
  if (p >= 1 && p <= totalPages.value) {
    currentPage.value = p
  }
}

// Styling Helpers
const getStatusBadge = (status: string) => {
  if (status === 'Approved') return 'bg-emerald-50 text-emerald-600 border-emerald-100'
  if (status === 'Pending') return 'bg-amber-50 text-amber-600 border-amber-100'
  if (status === 'Under Review') return 'bg-blue-50 text-blue-600 border-blue-100'
  if (status === 'Correction Required') return 'bg-orange-50 text-orange-600 border-orange-100'
  if (status === 'Rejected') return 'bg-rose-50 text-rose-600 border-rose-100'
  return 'bg-slate-50 text-slate-500 border-slate-100'
}

const toggleMenu = (id: number) => {
  openMenuId.value = openMenuId.value === id ? null : id
}

// Functional Actions (Persisting to backend)
const approveSubmission = async (inst: any) => {
  openMenuId.value = null
  isSubmittingAction.value = true
  try {
    const targetId = inst.submission_id || inst.id
    const res = await apiClient.put(`/dept-head/semester-submissions/${targetId}/status`, {
      status: 'approved',
      academic_year: semesterInfo.value.academicYear,
      semester: semesterInfo.value.semester,
    })

    inst.status = 'Approved'
    inst.raw_status = 'approved'
    if (res.data?.submission?.approved_at) {
      inst.submitted = res.data.submission.approved_at.replace(' ', '\n')
    }

    // Refresh summary counts
    semesterInfo.value.approved++
    if (semesterInfo.value.pendingReview > 0) {
      semesterInfo.value.pendingReview--
    }

    showToast(`Submission for ${inst.name} approved successfully!`, 'success')
  } catch (error) {
    console.error('Failed to approve submission:', error)
    showToast('Failed to approve submission. Please try again.', 'error')
  } finally {
    isSubmittingAction.value = false
  }
}

const openCorrectionModal = (inst: any) => {
  openMenuId.value = null
  correctionModal.value = {
    open: true,
    instructor: inst,
    remarks: inst.remarks || ''
  }
}

const submitCorrection = async () => {
  if (!correctionModal.value.instructor) return
  const inst = correctionModal.value.instructor
  isSubmittingAction.value = true
  try {
    const targetId = inst.submission_id || inst.id
    await apiClient.put(`/dept-head/semester-submissions/${targetId}/status`, {
      status: 'correction_required',
      remarks: correctionModal.value.remarks || 'Please check and revise semester records.',
      academic_year: semesterInfo.value.academicYear,
      semester: semesterInfo.value.semester,
    })

    const prevStatus = inst.status
    inst.status = 'Correction Required'
    inst.raw_status = 'correction_required'
    inst.remarks = correctionModal.value.remarks

    // Refresh summary counts
    semesterInfo.value.correctionRequired++
    if (prevStatus === 'Pending' && semesterInfo.value.pendingReview > 0) {
      semesterInfo.value.pendingReview--
    } else if (prevStatus === 'Approved' && semesterInfo.value.approved > 0) {
      semesterInfo.value.approved--
    }

    correctionModal.value.open = false
    showToast(`Correction requested for ${inst.name}.`, 'info')
  } catch (error) {
    console.error('Failed to request correction:', error)
    showToast('Failed to request correction.', 'error')
  } finally {
    isSubmittingAction.value = false
  }
}

const openRejectModal = (inst: any) => {
  openMenuId.value = null
  rejectModal.value = {
    open: true,
    instructor: inst,
    remarks: inst.remarks || ''
  }
}

const submitReject = async () => {
  if (!rejectModal.value.instructor) return
  const inst = rejectModal.value.instructor
  isSubmittingAction.value = true
  try {
    const targetId = inst.submission_id || inst.id
    await apiClient.put(`/dept-head/semester-submissions/${targetId}/status`, {
      status: 'rejected',
      remarks: rejectModal.value.remarks || 'Semester submission rejected by department head.',
      academic_year: semesterInfo.value.academicYear,
      semester: semesterInfo.value.semester,
    })

    const prevStatus = inst.status
    inst.status = 'Rejected'
    inst.raw_status = 'rejected'
    inst.remarks = rejectModal.value.remarks

    // Refresh summary counts
    if (prevStatus === 'Pending' && semesterInfo.value.pendingReview > 0) {
      semesterInfo.value.pendingReview--
    } else if (prevStatus === 'Approved' && semesterInfo.value.approved > 0) {
      semesterInfo.value.approved--
    }

    rejectModal.value.open = false
    showToast(`Submission for ${inst.name} has been rejected.`, 'info')
  } catch (error) {
    console.error('Failed to reject submission:', error)
    showToast('Failed to reject submission.', 'error')
  } finally {
    isSubmittingAction.value = false
  }
}

// Export CSV Functionality
const exportReport = () => {
  if (instructors.value.length === 0) {
    showToast('No data to export.', 'info')
    return
  }

  const headers = ['#', 'Instructor Name', 'Email', 'Department', 'Course', 'Section', 'Credit', 'Submitted Date', 'Status', 'Remarks']
  const rows = filteredInstructors.value.map((inst, idx) => [
    idx + 1,
    `"${inst.name}"`,
    `"${inst.email}"`,
    `"${inst.department}"`,
    `"${inst.course}"`,
    `"${inst.section}"`,
    inst.credit,
    `"${inst.submitted?.replace('\n', ' ') || ''}"`,
    `"${inst.status}"`,
    `"${inst.remarks || ''}"`
  ])

  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n')
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement('a')
  link.setAttribute('href', encodedUri)
  link.setAttribute('download', `Semester_Submissions_${semesterInfo.value.academicYear.replace('/', '_')}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  showToast('Submission report exported successfully!', 'success')
}
</script>

<template>
  <div class="space-y-6">

    <!-- Toast Notification -->
    <transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="toast.show"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-xl border bg-white"
        :class="toast.type === 'success' ? 'border-emerald-200 text-emerald-800' : (toast.type === 'error' ? 'border-rose-200 text-rose-800' : 'border-indigo-200 text-indigo-800')"
      >
        <div class="w-2.5 h-2.5 rounded-full" :class="toast.type === 'success' ? 'bg-emerald-500' : (toast.type === 'error' ? 'bg-rose-500' : 'bg-indigo-500')"></div>
        <span class="text-[13px] font-semibold">{{ toast.message }}</span>
      </div>
    </transition>

    <!-- Page Header -->
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-3">
        <button
          @click="router.push({ name: 'DeptHeadSemesterSubmissionsOverview' })"
          class="w-8 h-8 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-500 hover:text-[#5138ed] hover:border-[#5138ed] hover:bg-indigo-50 transition-all shadow-sm"
          title="Back to Semester Submissions list"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <div class="w-10 h-10 bg-indigo-50 text-[#5138ed] rounded-xl flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
        </div>
        <div>
          <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Semester Submissions</h2>
          <p class="text-[13px] text-slate-500 font-medium">Review and approve instructor semester records for the current academic year and semester.</p>
        </div>
      </div>

      <button
        @click="fetchSubmissions"
        :disabled="isLoading"
        class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-[12px] font-bold hover:bg-slate-50 hover:text-slate-800 transition-colors shadow-sm disabled:opacity-50"
      >
        <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': isLoading }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        Sync Real Data
      </button>
    </div>

    <!-- Info Banner -->
    <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl px-4 py-3 flex items-center gap-3">
      <div class="w-5 h-5 bg-[#5138ed] text-white rounded-full flex items-center justify-center flex-shrink-0">
        <span class="text-[10px] font-black">i</span>
      </div>
      <p class="text-[12px] font-medium text-indigo-700">
        Once approved, the semester records will be locked and cannot be modified by the instructor.
      </p>
    </div>

    <!-- Top Row: Summary Cards + Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-stretch">

      <!-- Pending Review -->
      <div 
        @click="selectedStatus = selectedStatus === 'Pending' ? 'All Statuses' : 'Pending'"
        class="bg-white rounded-xl border border-slate-200 p-4 flex items-start justify-between shadow-sm hover:shadow-md transition-shadow cursor-pointer group"
        :class="{ 'ring-2 ring-amber-400': selectedStatus === 'Pending' }"
      >
        <div>
          <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 bg-amber-50 text-amber-500 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="text-[12px] font-bold text-slate-500 uppercase tracking-wide">Pending Review</span>
          </div>
          <div class="text-3xl font-black text-slate-800">
            <span v-if="isLoading" class="inline-block w-8 h-8 bg-slate-100 rounded animate-pulse"></span>
            <span v-else>{{ semesterInfo.pendingReview }}</span>
          </div>
          <span class="text-[11px] font-medium text-slate-500 mt-1">Awaiting your review</span>
        </div>
        <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-400 transition-colors mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
      </div>

      <!-- Approved -->
      <div 
        @click="selectedStatus = selectedStatus === 'Approved' ? 'All Statuses' : 'Approved'"
        class="bg-white rounded-xl border border-slate-200 p-4 flex items-start justify-between shadow-sm hover:shadow-md transition-shadow cursor-pointer group"
        :class="{ 'ring-2 ring-emerald-400': selectedStatus === 'Approved' }"
      >
        <div>
          <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="text-[12px] font-bold text-slate-500 uppercase tracking-wide">Approved</span>
          </div>
          <div class="text-3xl font-black text-slate-800">
            <span v-if="isLoading" class="inline-block w-8 h-8 bg-slate-100 rounded animate-pulse"></span>
            <span v-else>{{ semesterInfo.approved }}</span>
          </div>
          <span class="text-[11px] font-medium text-slate-500 mt-1">This semester</span>
        </div>
        <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-400 transition-colors mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
      </div>

      <!-- Correction Required -->
      <div 
        @click="selectedStatus = selectedStatus === 'Correction Required' ? 'All Statuses' : 'Correction Required'"
        class="bg-white rounded-xl border border-slate-200 p-4 flex items-start justify-between shadow-sm hover:shadow-md transition-shadow cursor-pointer group"
        :class="{ 'ring-2 ring-orange-400': selectedStatus === 'Correction Required' }"
      >
        <div>
          <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 bg-orange-50 text-orange-500 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <span class="text-[12px] font-bold text-slate-500 uppercase tracking-wide">Correction Required</span>
          </div>
          <div class="text-3xl font-black text-slate-800">
            <span v-if="isLoading" class="inline-block w-8 h-8 bg-slate-100 rounded animate-pulse"></span>
            <span v-else>{{ semesterInfo.correctionRequired }}</span>
          </div>
          <span class="text-[11px] font-medium text-slate-500 mt-1">Needs attention</span>
        </div>
        <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-400 transition-colors mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
      </div>

      <!-- Total Submissions -->
      <div 
        @click="selectedStatus = 'All Statuses'"
        class="bg-white rounded-xl border border-slate-200 p-4 flex items-start justify-between shadow-sm hover:shadow-md transition-shadow cursor-pointer group"
      >
        <div>
          <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 bg-indigo-50 text-[#5138ed] rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path></svg>
            </div>
            <span class="text-[12px] font-bold text-slate-500 uppercase tracking-wide">Total Submissions</span>
          </div>
          <div class="text-3xl font-black text-slate-800">
            <span v-if="isLoading" class="inline-block w-8 h-8 bg-slate-100 rounded animate-pulse"></span>
            <span v-else>{{ semesterInfo.total }}</span>
          </div>
          <span class="text-[11px] font-medium text-slate-500 mt-1">For this semester</span>
        </div>
        <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-400 transition-colors mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex flex-col justify-between">
        <div>
          <div class="flex items-center gap-2 mb-3">
            <div class="w-7 h-7 bg-indigo-50 text-[#5138ed] rounded-lg flex items-center justify-center">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <span class="text-[12px] font-bold text-slate-700">Quick Actions</span>
          </div>
          <div class="space-y-2">
            <button 
              @click="exportReport"
              class="w-full py-2 bg-[#5138ed] text-white text-[11px] font-bold rounded-xl hover:bg-[#4530d1] transition-colors flex items-center justify-center gap-2 shadow-sm"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
              Export Report
            </button>
            <button 
              @click="isGuidelinesModalOpen = true"
              class="w-full py-2 bg-white border border-slate-200 text-slate-600 text-[11px] font-semibold rounded-xl hover:bg-slate-50 transition-colors flex items-center justify-center gap-2"
            >
              <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              View Submission Guidelines
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main List Container -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm flex flex-col">

      <!-- Filter Bar -->
      <div class="p-4 border-b border-slate-100 flex flex-wrap items-center gap-3 bg-slate-50/50">
        <!-- Search Input -->
        <div class="relative flex-1 min-w-[260px] max-w-md">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by instructor, department, or section..."
            class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors shadow-sm"
          />
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>

        <!-- Department Dropdown -->
        <div class="relative">
          <select 
            v-model="selectedDepartment" 
            class="appearance-none pl-4 pr-9 py-2 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-600 font-medium focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] cursor-pointer min-w-[160px] shadow-sm"
          >
            <option v-for="dept in departmentsList" :key="dept" :value="dept">{{ dept }}</option>
          </select>
          <svg class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>

        <!-- Status Dropdown -->
        <div class="relative">
          <select 
            v-model="selectedStatus" 
            class="appearance-none pl-4 pr-9 py-2 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-600 font-medium focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] cursor-pointer min-w-[140px] shadow-sm"
          >
            <option>All Statuses</option>
            <option>Pending</option>
            <option>Approved</option>
            <option>Correction Required</option>
            <option>Rejected</option>
          </select>
          <svg class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>

        <!-- Academic Term Dropdown -->
        <div class="relative ml-auto">
          <select 
            v-model="selectedSemester" 
            class="appearance-none pl-4 pr-9 py-2 bg-white border border-slate-200 rounded-lg text-[13px] text-slate-600 font-medium focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] cursor-pointer min-w-[220px] shadow-sm"
          >
            <option v-for="sem in semestersList" :key="sem" :value="sem">{{ sem }}</option>
          </select>
          <svg class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>
      </div>

      <!-- Table Container -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-white">
              <th class="py-3.5 px-4 font-semibold w-10">#</th>
              <th class="py-3.5 px-4 font-semibold">Instructor</th>
              <th class="py-3.5 px-4 font-semibold">Course</th>
              <th class="py-3.5 px-4 font-semibold">Section</th>
              <th class="py-3.5 px-4 font-semibold text-center">Credit</th>
              <th class="py-3.5 px-4 font-semibold">Submitted</th>
              <th class="py-3.5 px-4 font-semibold">Status</th>
              <th class="py-3.5 px-4 font-semibold text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">

            <!-- Loading State -->
            <tr v-if="isLoading">
              <td colspan="8" class="py-14 text-center">
                <div class="flex flex-col items-center gap-3">
                  <div class="animate-spin w-8 h-8 border-2 border-[#5138ed] border-t-transparent rounded-full"></div>
                  <span class="text-[13px] font-semibold text-slate-500">Loading semester submissions...</span>
                </div>
              </td>
            </tr>

            <!-- Empty State -->
            <tr v-else-if="filteredInstructors.length === 0">
              <td colspan="8" class="py-14 text-center">
                <div class="flex flex-col items-center gap-2 text-slate-400">
                  <svg class="w-10 h-10 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                  <span class="text-[13px] font-semibold text-slate-600">No submissions found matching filters.</span>
                  <span class="text-[11px] text-slate-400">Try adjusting your search criteria or selecting "All Departments".</span>
                </div>
              </td>
            </tr>

            <!-- Real Data Rows -->
            <tr
              v-else
              v-for="(inst, index) in paginatedInstructors"
              :key="inst.id"
              class="hover:bg-slate-50/60 transition-colors group"
            >
              <!-- 1: Row Number -->
              <td class="py-3.5 px-4 text-[12px] font-bold text-slate-400">
                {{ (currentPage - 1) * itemsPerPage + index + 1 }}
              </td>

              <!-- 2: Instructor (Avatar, Name, Email) -->
              <td class="py-3.5 px-4">
                <div class="flex items-center gap-3">
                  <div :class="['w-9 h-9 rounded-full flex items-center justify-center text-[11px] font-extrabold flex-shrink-0 shadow-sm', inst.color]">
                    {{ inst.initials }}
                  </div>
                  <div class="flex flex-col min-w-0">
                    <span class="text-[13px] font-bold text-slate-800 truncate">{{ inst.name }}</span>
                    <span class="text-[11px] text-slate-400 font-medium truncate">{{ inst.email }}</span>
                  </div>
                </div>
              </td>

              <!-- 3: Course Title & Department -->
              <td class="py-3.5 px-4">
                <div class="flex flex-col">
                  <span class="text-[13px] font-bold text-slate-800 truncate">{{ inst.course || inst.department }}</span>
                  <span v-if="inst.course_code" class="text-[11px] text-slate-400 font-medium">{{ inst.course_code }} • {{ inst.department }}</span>
                  <span v-else class="text-[11px] text-slate-400 font-medium">{{ inst.department }}</span>
                </div>
              </td>

              <!-- 4: Section Badge -->
              <td class="py-3.5 px-4">
                <span
                  class="px-2.5 py-1 text-[11px] font-bold rounded-md whitespace-nowrap"
                  :class="inst.section?.toLowerCase().includes('b') ? 'bg-sky-50 text-sky-600' : 'bg-indigo-50 text-[#5138ed]'"
                >
                  {{ inst.section || 'Section A' }}
                </span>
              </td>

              <!-- 5: Credit Hours -->
              <td class="py-3.5 px-4 text-[13px] font-bold text-slate-700 text-center">
                {{ inst.credit || inst.courses || 4 }}
              </td>

              <!-- 6: Submitted Date and Time -->
              <td class="py-3.5 px-4">
                <div class="flex flex-col">
                  <span class="text-[12px] font-medium text-slate-700">
                    {{ inst.submitted_date || (inst.submitted?.includes('\n') ? inst.submitted.split('\n')[0] : inst.submitted) }}
                  </span>
                  <span class="text-[11px] text-slate-400">
                    {{ inst.submitted_time || (inst.submitted?.includes('\n') ? inst.submitted.split('\n')[1] : '') }}
                  </span>
                </div>
              </td>

              <!-- 7: Status Badge -->
              <td class="py-3.5 px-4">
                <span :class="['inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border whitespace-nowrap', getStatusBadge(inst.status)]">
                  <svg v-if="inst.status === 'Approved'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                  <svg v-else-if="inst.status === 'Pending'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  <svg v-else-if="inst.status === 'Correction Required'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                  <svg v-else-if="inst.status === 'Rejected'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                  {{ inst.status }}
                </span>
              </td>

              <!-- 8: Actions Dropdown Menu -->
              <td class="py-3.5 px-4 text-center">
                <div class="relative inline-block text-left">
                  <button
                    @click.stop="toggleMenu(inst.id)"
                    class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
                  >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                      <circle cx="12" cy="5" r="1.5"/>
                      <circle cx="12" cy="12" r="1.5"/>
                      <circle cx="12" cy="19" r="1.5"/>
                    </svg>
                  </button>

                  <!-- 3-Dot Dropdown -->
                  <div
                    v-if="openMenuId === inst.id"
                    @click.stop
                    class="absolute right-0 top-8 z-50 w-48 bg-white border border-slate-200 rounded-xl shadow-xl py-1 text-left animate-in fade-in zoom-in-95 duration-100"
                  >
                    <button
                      @click="approveSubmission(inst)"
                      class="w-full px-4 py-2.5 text-left text-[12px] font-semibold text-emerald-600 hover:bg-emerald-50 transition-colors flex items-center gap-2.5"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                      Approve
                    </button>
                    <button
                      @click="openCorrectionModal(inst)"
                      class="w-full px-4 py-2.5 text-left text-[12px] font-semibold text-orange-600 hover:bg-orange-50 transition-colors flex items-center gap-2.5"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                      Request Correction
                    </button>
                    <div class="border-t border-slate-100 my-1"></div>
                    <button
                      @click="openRejectModal(inst)"
                      class="w-full px-4 py-2.5 text-left text-[12px] font-semibold text-rose-600 hover:bg-rose-50 transition-colors flex items-center gap-2.5"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                      Reject
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Table Footer / Pagination -->
      <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between bg-white">
        <span class="text-[12px] font-medium text-slate-500">
          Showing {{ filteredInstructors.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0 }} to 
          {{ Math.min(currentPage * itemsPerPage, filteredInstructors.length) }} of 
          {{ filteredInstructors.length }} submissions
        </span>

        <div v-if="totalPages > 1" class="flex items-center gap-1.5">
          <button
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 text-slate-400 bg-white hover:bg-slate-50 hover:text-slate-600 disabled:opacity-40 transition-colors"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
          </button>
          
          <button
            v-for="p in totalPages"
            :key="p"
            @click="goToPage(p)"
            :class="[
              'w-7 h-7 flex items-center justify-center rounded text-[12px] font-bold transition-colors',
              p === currentPage 
                ? 'bg-[#5138ed] text-white shadow-sm' 
                : 'border border-slate-200 text-slate-600 bg-white hover:bg-slate-50'
            ]"
          >
            {{ p }}
          </button>

          <button
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 text-slate-400 bg-white hover:bg-slate-50 hover:text-slate-600 disabled:opacity-40 transition-colors"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Request Correction Modal -->
    <div
      v-if="correctionModal.open"
      class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4"
    >
      <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-800">Request Correction</h3>
            <p class="text-[12px] text-slate-500">Instructor: {{ correctionModal.instructor?.name }}</p>
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Feedback / Correction Remarks</label>
          <textarea
            v-model="correctionModal.remarks"
            rows="3"
            placeholder="Specify what needs correction (e.g. invalid grade entries, missing continuous assessment scores)..."
            class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:bg-white transition-colors"
          ></textarea>
        </div>

        <div class="flex items-center justify-end gap-2.5">
          <button
            @click="correctionModal.open = false"
            class="px-4 py-2 rounded-xl text-[12px] font-semibold text-slate-600 hover:bg-slate-100 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="submitCorrection"
            :disabled="isSubmittingAction"
            class="px-5 py-2 rounded-xl text-[12px] font-bold bg-orange-500 text-white hover:bg-orange-600 transition-colors shadow-sm disabled:opacity-50"
          >
            Submit Request
          </button>
        </div>
      </div>
    </div>

    <!-- Reject Submission Modal -->
    <div
      v-if="rejectModal.open"
      class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4"
    >
      <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-800">Reject Submission</h3>
            <p class="text-[12px] text-slate-500">Instructor: {{ rejectModal.instructor?.name }}</p>
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Reason for Rejection</label>
          <textarea
            v-model="rejectModal.remarks"
            rows="3"
            placeholder="Explain why this submission is rejected..."
            class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-[13px] text-slate-700 focus:outline-none focus:border-rose-500 focus:bg-white transition-colors"
          ></textarea>
        </div>

        <div class="flex items-center justify-end gap-2.5">
          <button
            @click="rejectModal.open = false"
            class="px-4 py-2 rounded-xl text-[12px] font-semibold text-slate-600 hover:bg-slate-100 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="submitReject"
            :disabled="isSubmittingAction"
            class="px-5 py-2 rounded-xl text-[12px] font-bold bg-rose-600 text-white hover:bg-rose-700 transition-colors shadow-sm disabled:opacity-50"
          >
            Confirm Rejection
          </button>
        </div>
      </div>
    </div>

    <!-- Submission Guidelines Modal -->
    <div
      v-if="isGuidelinesModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4"
    >
      <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-[#5138ed] flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Semester Submission Guidelines</h3>
          </div>
          <button @click="isGuidelinesModalOpen = false" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>

        <div class="space-y-4 text-[13px] text-slate-600 leading-relaxed">
          <div class="p-3 bg-indigo-50/60 rounded-xl border border-indigo-100">
            <span class="font-bold text-indigo-900 block mb-1">1. Completeness of Continuous Assessment</span>
            <p>Instructors must ensure all quizzes, assignments, midterm exams, and practical labs have been properly scored and published before finalizing semester submissions.</p>
          </div>

          <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
            <span class="font-bold text-slate-800 block mb-1">2. Grade Verification Protocol</span>
            <p>Verify that total scores conform to university grading scales (A+, A, B, C, D, F, NG) and that student attendances satisfy the minimum requirement.</p>
          </div>

          <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
            <span class="font-bold text-slate-800 block mb-1">3. Approval & Locking Mechanism</span>
            <p>Once a submission is marked as <strong>Approved</strong>, all course grades for that section will be locked from further edits by the instructor. To reopen editing, the department head must issue a <strong>Correction Request</strong>.</p>
          </div>

          <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
            <span class="font-bold text-slate-800 block mb-1">4. Rejection Policy</span>
            <p>Submissions should only be <strong>Rejected</strong> if there is a fundamental breach or major discrepancies in exam records. Always supply clear remarks so instructors understand necessary revisions.</p>
          </div>
        </div>

        <div class="mt-6 flex justify-end">
          <button
            @click="isGuidelinesModalOpen = false"
            class="px-5 py-2.5 bg-[#5138ed] text-white rounded-xl text-[12px] font-bold hover:bg-[#4530d1] transition-colors shadow-sm"
          >
            I Understand
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<style scoped>
/* Scoped styles */
</style>