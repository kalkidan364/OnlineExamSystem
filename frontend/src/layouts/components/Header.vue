<script setup lang="ts">
import { useAuthStore } from '../../modules/auth/store/authStore'
import { useSettingsStore } from '../../store/settingsStore'
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import apiClient from '../../core/api/apiClient'

const authStore = useAuthStore()
const settingsStore = useSettingsStore()
const route = useRoute()

const instructorData = ref<any>(null)

onMounted(async () => {
  // Only fetch if the user is an instructor
  if (authStore.user?.role === 'instructor' || authStore.user?.role === 'dept_head') {
    try {
      const res = await apiClient.get('/instructor/me')
      instructorData.value = res.data.data
    } catch (e) {
      console.error('Failed to fetch instructor info', e)
    }
  }
})

const instructorRoleText = computed(() => {
  if (!instructorData.value) return 'Instructor'
  
  let deptName = instructorData.value.department || ''
  
  // Abbreviate department name (e.g. Computer Science -> cs, Information System -> is)
  if (deptName) {
    const knownAbbreviations: Record<string, string> = {
      'computer science': 'cs',
      'information system': 'is',
      'information systems': 'is',
      'software engineering': 'se',
      'information technology': 'it'
    }
    
    const lowerDept = deptName.toLowerCase().trim()
    if (knownAbbreviations[lowerDept]) {
      deptName = knownAbbreviations[lowerDept]
    } else {
      // Fallback: create acronym from words
      deptName = lowerDept.split(' ')
        .map((w: string) => w[0])
        .join('')
        .toLowerCase()
    }
  }

  const dept = deptName ? `${deptName}` : ''
  const year = instructorData.value.year_level ? `${dept ? ', ' : ''}${instructorData.value.year_level}` : ''
  const section = instructorData.value.section ? `${(dept || year) ? ', ' : ''}${instructorData.value.section}` : ''
  
  // Clean up formatting: "is, 3rd year, section A, instructor"
  let str = `${dept}${year}${section}${(dept || year || section) ? ', ' : ''}instructor`
  return str.toLowerCase()
})

const isCreateExam = computed(() => route.path === '/instructor/exams/create')
const createExamStep = computed(() => Number(route.query.step) || 1)


// Map routes to dynamic titles
const pageTitle = computed(() => {
  if (isCreateExam.value) {
    if (createExamStep.value === 4) return 'Review & Publish'
    if (createExamStep.value === 2) return 'Add Questions'
    return 'Create New Exam'
  }
  if (route.path.includes('/dashboard')) return 'Instructor Dashboard'
  if (route.path.includes('/question-banks')) return 'Question Banks'
  if (route.path.includes('/exams')) return 'Exams Management'
  if (route.path.includes('/students')) return 'Student Management'
  if (route.path.includes('/results')) return 'Results'
  if (route.path.includes('/reports')) return 'Reports'
  if (route.path.includes('/profile')) return 'My Profile'
  if (route.path.includes('/settings')) return 'Settings'
  return 'Dashboard'
})
</script>

<template>
  <header class="h-24 bg-white/80 backdrop-blur-md border-b border-slate-100 flex items-center justify-between px-8 sticky top-0 z-30">
    
    <!-- Left Side: Title & Menu Toggle (Mobile) -->
    <div class="flex items-center gap-4">
      <button class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 lg:hidden transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
      <div class="hidden lg:flex items-center justify-center w-10 h-10 rounded-xl bg-slate-50 text-slate-500 border border-slate-100 mr-2">
         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path></svg>
      </div>
      
      <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-800">{{ pageTitle }}</h1>
        <div v-if="isCreateExam" class="flex items-center gap-2 mt-0.5 text-[12px] font-medium text-slate-500">
          <router-link to="/instructor/exams" class="hover:text-[#5138ed] transition-colors">Exams</router-link>
          <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          <router-link to="/instructor/exams/create" class="hover:text-[#5138ed] transition-colors" :class="{'text-slate-700': createExamStep === 1 || createExamStep === 3 || createExamStep === 4}">Create New Exam</router-link>
          
          <template v-if="createExamStep === 2">
            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="text-slate-700">Add Questions</span>
          </template>
        </div>
        <div v-else-if="route.path.includes('/results')" class="mt-0.5 text-[12px] font-medium text-slate-500">
          View and analyze exam results and performance.
        </div>
        <div v-else-if="route.path.includes('/reports')" class="mt-0.5 text-[12px] font-medium text-slate-500">
          View and analyze exam, student and performance reports.
        </div>
        <div v-else-if="route.path.includes('/profile')" class="mt-0.5 text-[12px] font-medium text-slate-500">
          View and manage your account information and preferences.
        </div>
        <div v-else-if="route.path.includes('/settings')" class="mt-0.5 text-[12px] font-medium text-slate-500">
          Manage system settings and preferences.
        </div>
      </div>
    </div>

    <!-- Center: Semester/Year Badge -->
    <div class="absolute left-1/2 -translate-x-1/2 hidden md:flex items-center">
      <span class="text-[13px] font-bold text-[#5138ed] bg-indigo-50 px-5 py-1.5 rounded-full border border-indigo-100 shadow-sm whitespace-nowrap">
        {{ settingsStore.formattedAcademicTerm }}
      </span>
    </div>

    <!-- Right Side: User & Actions -->
    <div class="flex items-center gap-6">
      
      <!-- Notifications -->
      <button class="relative p-2 text-slate-400 hover:text-slate-600 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-[#5138ed] border-2 border-white rounded-full"></span>
      </button>

      <!-- User Profile Dropdown -->
      <div class="flex items-center gap-3 pl-6 border-l border-slate-200 cursor-pointer group">
        <div class="w-10 h-10 rounded-full bg-slate-200 overflow-hidden border-2 border-transparent group-hover:border-[#5138ed] transition-all">
          <!-- Placeholder avatar, matching the design style -->
          <img src="https://i.pravatar.cc/150?u=a042581f4e29026704d" alt="Profile" class="w-full h-full object-cover" />
        </div>
        <div class="hidden md:flex flex-col">
          <span class="text-sm font-bold text-slate-800">{{ instructorData?.name || authStore.user?.name || 'Loading...' }}</span>
          <span class="text-[11px] font-medium text-slate-500 leading-snug max-w-[200px] truncate" :title="instructorRoleText">
            {{ instructorRoleText }}
          </span>
        </div>
        <svg class="w-4 h-4 text-slate-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>

      </div>

    </div>

  </header>
</template>
