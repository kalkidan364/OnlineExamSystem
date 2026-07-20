<script setup lang="ts">
import { ref, computed } from 'vue'
import { useInstructorExamStore } from '../../store/instructorExamStore'

const examStore = useInstructorExamStore()

const searchQuery = ref('')
const selectedStatus = ref('All Status')
const selectedSemester = ref('Semester I, 2025/2026')
const sortBy = ref('Exam Date')

const resetFilters = () => {
  searchQuery.value = ''
  selectedStatus.value = 'All Status'
  selectedSemester.value = 'Semester I, 2025/2026'
  sortBy.value = 'Exam Date'
}

// Format ISO date strings
const formatDate = (iso: string | null) => {
  if (!iso) return '—'
  return new Date(iso).toLocaleDateString('en-US', {
    month: 'short', day: '2-digit', year: 'numeric'
  })
}
const formatTime = (iso: string | null) => {
  if (!iso) return '—'
  return new Date(iso).toLocaleTimeString('en-US', {
    hour: '2-digit', minute: '2-digit'
  })
}

// Compute filtered exams
const filteredExams = computed(() => {
  let result = [...examStore.exams]

  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(e => e.title.toLowerCase().includes(q) || (e.course_name && e.course_name.toLowerCase().includes(q)) || (e.course_code && e.course_code.toLowerCase().includes(q)))
  }

  if (selectedStatus.value !== 'All Status') {
    result = result.filter(e => e.status.toLowerCase() === selectedStatus.value.toLowerCase())
  }

  // Sort By
  if (sortBy.value === 'Exam Date') {
    result.sort((a, b) => {
      const dateA = a.scheduled_at ? new Date(a.scheduled_at).getTime() : 0
      const dateB = b.scheduled_at ? new Date(b.scheduled_at).getTime() : 0
      return dateB - dateA
    })
  } else if (sortBy.value === 'Name (A-Z)') {
    result.sort((a, b) => a.title.localeCompare(b.title))
  }

  return result
})

const getStatusColor = (status: string) => {
  switch(status.toLowerCase()) {
    case 'upcoming': 
    case 'scheduled': 
      return 'bg-indigo-50 text-[#5138ed]'
    case 'active': 
    case 'published': 
    case 'completed': 
      return 'bg-emerald-50 text-emerald-600'
    case 'draft': 
      return 'bg-amber-50 text-amber-600'
    case 'archived':
      return 'bg-slate-100 text-slate-600'
    default: 
      return 'bg-emerald-50 text-emerald-600'
  }
}

const iconStyles = [
  { bg: 'bg-indigo-50', text: 'text-indigo-500', svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>' },
  { bg: 'bg-emerald-50', text: 'text-emerald-500', svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>' },
  { bg: 'bg-indigo-50', text: 'text-[#5138ed]', svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>' },
  { bg: 'bg-amber-50', text: 'text-amber-500', svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>' },
  { bg: 'bg-emerald-50', text: 'text-emerald-500', svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>' },
  { bg: 'bg-sky-50', text: 'text-sky-500', svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' },
  { bg: 'bg-rose-50', text: 'text-rose-500', svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>' },
  { bg: 'bg-amber-50', text: 'text-amber-500', svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>' },
  { bg: 'bg-slate-100', text: 'text-slate-500', svg: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>' }
]
</script>

<template>
  <div class="p-6">
    
    <!-- Filter Bar -->
    <div class="flex items-end gap-4 mb-6 w-full overflow-x-auto pb-2">
      
      <!-- Search -->
      <div class="relative flex-1 min-w-[200px]">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <input 
          v-model="searchQuery"
          type="text" 
          class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors"
          placeholder="Search exams..."
        >
      </div>

      <!-- Semester -->
      <div class="flex flex-col gap-1.5 w-full sm:w-auto">
        <label class="text-[11px] font-bold text-slate-700 capitalize tracking-wide">Semester</label>
        <div class="relative">
          <select v-model="selectedSemester" class="appearance-none border border-slate-200 rounded-xl text-sm pl-3 pr-10 py-2 text-slate-600 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors w-full sm:w-[180px]">
            <option>Semester I, 2025/2026</option>
            <option>Semester II, 2025/2026</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
        </div>
      </div>

      <!-- Status -->
      <div class="flex flex-col gap-1.5 w-full sm:w-auto">
        <label class="text-[11px] font-bold text-slate-700 capitalize tracking-wide">Status</label>
        <div class="relative">
          <select v-model="selectedStatus" class="appearance-none border border-slate-200 rounded-xl text-sm pl-3 pr-10 py-2 text-slate-600 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors w-full sm:w-[140px]">
            <option>All Status</option>
            <option value="Scheduled">Scheduled</option>
            <option value="Published">Published</option>
            <option value="Completed">Completed</option>
            <option value="Draft">Draft</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
        </div>
      </div>

      <!-- Sort By -->
      <div class="flex flex-col gap-1.5 w-full sm:w-auto">
        <label class="text-[11px] font-bold text-slate-700 capitalize tracking-wide">Sort By</label>
        <div class="relative">
          <select v-model="sortBy" class="appearance-none border border-slate-200 rounded-xl text-sm pl-3 pr-10 py-2 text-slate-600 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors w-full sm:w-[140px]">
            <option>Exam Date</option>
            <option>Name (A-Z)</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
        </div>
      </div>

    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse whitespace-nowrap">
        <thead>
          <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wide">
            <th class="pb-4 pr-4 font-semibold">Exam Title</th>
            <th class="pb-4 px-4 font-semibold">Exam Code</th>
            <th class="pb-4 px-4 font-semibold">Date & Time</th>
            <th class="pb-4 px-4 font-semibold text-center">Duration</th>
            <th class="pb-4 px-4 font-semibold text-center">Marks</th>
            <th class="pb-4 px-4 font-semibold text-center">Questions</th>
            <th class="pb-4 px-4 font-semibold text-center">Registered Students</th>
            <th class="pb-4 px-4 font-semibold text-center">Status</th>
            <th class="pb-4 pl-4 text-center font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <!-- Loading state -->
          <tr v-if="examStore.isLoading">
            <td colspan="7" class="py-8 text-center text-slate-500">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 inline text-[#5138ed]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Loading exams...
            </td>
          </tr>
          
          <!-- Empty State -->
          <tr v-else-if="filteredExams.length === 0">
             <td colspan="7" class="py-8 text-center text-slate-500 font-medium">No exams found.</td>
          </tr>

          <!-- Data rows -->
          <tr v-else v-for="(exam, index) in filteredExams" :key="exam.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors last:border-0 group">
            <td class="py-4 pr-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" :class="[iconStyles[index % iconStyles.length].bg, iconStyles[index % iconStyles.length].text]">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="iconStyles[index % iconStyles.length].svg"></svg>
                </div>
                <div class="flex flex-col">
                  <span class="text-[13px] font-bold text-slate-700">{{ exam.title }}</span>
                  <span class="text-[11px] text-slate-400">{{ exam.course_name || 'Midterm Examination' }}</span>
                </div>
              </div>
            </td>
            <td class="py-4 px-4">
              <div class="flex flex-col">
                <span class="text-[13px] font-medium text-slate-600">{{ exam.course_code || 'DBS-MID-2026' }}</span>
              </div>
            </td>
            <td class="py-4 px-4">
              <div class="flex flex-col">
                <span class="text-[13px] font-medium text-slate-600">{{ formatDate(exam.scheduled_at) }}</span>
                <span class="text-[11px] text-slate-400">{{ formatTime(exam.scheduled_at) }}</span>
              </div>
            </td>
            <td class="py-4 px-4 text-[13px] text-slate-600 font-medium text-center">
              <div class="flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ exam.duration_minutes }} min
              </div>
            </td>
            <td class="py-4 px-4 text-[13px] text-slate-600 font-bold text-center">{{ exam.total_marks }}</td>
            <td class="py-4 px-4 text-[13px] text-slate-600 font-bold text-center">{{ exam.questions_count !== undefined ? exam.questions_count : '-' }}</td>
            <td class="py-4 px-4 text-[13px] text-slate-600 font-bold text-center">
              <div class="flex items-center justify-center gap-1.5" v-if="exam.students_count !== undefined">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                {{ exam.students_count }}
              </div>
              <div v-else class="text-center">-</div>
            </td>
            <td class="py-4 px-4 text-center">
              <span class="px-2.5 py-1 text-[10px] font-bold rounded-md capitalize" :class="getStatusColor(exam.status)">
                {{ exam.status }}
              </span>
            </td>
            <td class="py-4 pl-4 text-center">
              <div class="flex items-center justify-center gap-1 text-slate-400">
                <button class="p-1.5 hover:text-[#5138ed] hover:bg-indigo-50 rounded-lg transition-colors" title="View">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>
                <button class="p-1.5 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
                <!-- CREATE ICON -->
                <router-link to="/instructor/exams/create" class="p-1.5 hover:text-emerald-500 hover:bg-emerald-50 rounded-lg transition-colors" title="Create Exam">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </router-link>
                <button @click="examStore.deleteExam(exam.id)" :disabled="examStore.isSaving" class="p-1.5 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors disabled:opacity-50" title="Options">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-100">
      <span class="text-[13px] text-slate-500 font-medium">Showing 1 to 9 of 18 exams</span>
      <div class="flex items-center gap-2">
        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#5138ed] text-white font-bold text-sm shadow-sm shadow-indigo-200">1</button>
        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm transition-colors">2</button>
        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
        
        <div class="relative ml-2">
          <select class="appearance-none border border-slate-200 rounded-xl text-sm pl-3 pr-8 py-1.5 text-slate-600 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors font-medium">
            <option>10 / page</option>
            <option>20 / page</option>
            <option>50 / page</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
