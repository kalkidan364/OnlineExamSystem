<script setup lang="ts">
import { useAuthStore } from '../../modules/auth/store/authStore'
import { useSettingsStore } from '../../store/settingsStore'
import { computed, inject, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const authStore = useAuthStore()
const settingsStore = useSettingsStore()
const route = useRoute()

onMounted(() => {
  authStore.fetchCurrentUser()
})

const profilePhotoUrl = computed(() => {
  const pic = authStore.user?.profile_picture_url || authStore.user?.profile_picture
  if (!pic) return 'https://i.pravatar.cc/150?u=admin123'
  if (pic.startsWith('http://') || pic.startsWith('https://') || pic.startsWith('data:')) {
    return pic
  }
  return `http://localhost:8000/storage/${pic}`
})

const toggleSidebar = inject<() => void>('toggleSidebar', () => {})
const sidebarOpen = inject<{ value: boolean }>('sidebarOpen', { value: true })

// Map routes to dynamic titles and descriptions
const pageInfo = computed(() => {
  const path = route.path

  if (path.includes('/dashboard')) return { title: 'Dashboard', desc: 'System-wide overview and key metrics.' }
  if (path.includes('/instructors')) return { title: 'Instructors', desc: 'Manage system instructors and their assignments.' }
  if (path.includes('/students')) return { title: 'Students', desc: 'Manage enrolled students and their profiles.' }
  if (path.includes('/courses')) return { title: 'Courses', desc: 'Manage academic courses and materials.' }
  if (path.includes('/exams')) return { title: 'Exams', desc: 'Manage system-wide examinations and schedules.' }
  if (path.includes('/departments')) return { title: 'Departments', desc: 'Manage departments and organizational structure.' }
  if (path.includes('/reports')) return { title: 'Reports', desc: 'View and generate comprehensive reports about the system.' }
  if (path.includes('/calendar')) return { title: 'Academic Calendar', desc: 'Manage academic terms, semesters, and important dates.' }
  if (path.includes('/settings')) return { title: 'Settings', desc: 'Global application settings and configuration.' }
  if (path.includes('/activity-logs')) return { title: 'Activity Logs', desc: 'Track and review all system activities and events.' }
  if (path.includes('/question-banks')) return { title: 'Question Banks', desc: 'Manage centralized pools of examination questions.' }
  if (path.includes('/users')) return { title: 'User Management', desc: 'Manage system users, roles, and permissions.' }

  return { title: 'Super Admin Dashboard', desc: 'System administration and management.' }
})
</script>

<template>
  <header class="h-24 bg-white/80 backdrop-blur-md border-b border-slate-100 flex items-center justify-between px-8 sticky top-0 z-30">
    
    <!-- Left Side: Title & Menu Toggle -->
    <div class="flex items-center gap-4">
      <!-- Hamburger button — always visible, toggles sidebar -->
      <button
        @click="toggleSidebar"
        class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition-colors border border-slate-100"
        :title="sidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
      
      <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-800">{{ pageInfo.title }}</h1>
        <div class="mt-0.5 text-[12px] font-medium text-slate-500">
          {{ pageInfo.desc }}
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
      </button>

      <!-- User Profile Dropdown -->
      <div class="flex items-center gap-3 pl-6 border-l border-slate-200 cursor-pointer group">
        <div class="w-10 h-10 rounded-full bg-slate-200 overflow-hidden border-2 border-transparent group-hover:border-rose-500 transition-all flex items-center justify-center">
          <img :src="profilePhotoUrl" alt="Profile" class="w-full h-full object-cover" />
        </div>
        <div class="hidden md:flex flex-col">
          <span class="text-sm font-bold text-slate-800">{{ authStore.user?.name || 'Super Admin' }}</span>
          <span class="text-xs font-medium text-rose-500">Administrator</span>
        </div>
      </div>
    </div>
  </header>
</template>
