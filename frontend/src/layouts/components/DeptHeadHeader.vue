<script setup lang="ts">
import { useAuthStore } from '../../modules/auth/store/authStore'
import { useSettingsStore } from '../../store/settingsStore'
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const authStore = useAuthStore()
const settingsStore = useSettingsStore()
const route = useRoute()
const router = useRouter()

// Map routes to dynamic titles
const pageTitle = computed(() => {
  if (route.path.includes('/instructors')) return 'Department Instructors'
  if (route.path.includes('/students')) return 'Department Students'
  if (route.path.includes('/courses')) return 'Courses Management'
  if (route.path.includes('/exams')) return 'Exams Overview'
  if (route.path.includes('/reports')) return 'Analytics & Reports'
  if (route.path.includes('/settings')) return 'Account Settings'
  return 'Department Dashboard'
})

const logout = () => {
  router.push('/')
}
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
        <div v-if="route.path.includes('/dashboard')" class="mt-0.5 text-[12px] font-medium text-slate-500">
          Department-wide overview and key metrics.
        </div>
        <div v-else-if="route.path.includes('/instructors')" class="mt-0.5 text-[12px] font-medium text-slate-500">
          Manage instructors within your department.
        </div>
        <div v-else-if="route.path.includes('/students')" class="mt-0.5 text-[12px] font-medium text-slate-500">
          View all students enrolled in your department.
        </div>
        <div v-else-if="route.path.includes('/courses')" class="mt-0.5 text-[12px] font-medium text-slate-500">
          Manage courses and assign instructors.
        </div>
        <div v-else-if="route.path.includes('/exams')" class="mt-0.5 text-[12px] font-medium text-slate-500">
          Monitor all department exams and pass rates.
        </div>
        <div v-else-if="route.path.includes('/reports')" class="mt-0.5 text-[12px] font-medium text-slate-500">
          Detailed analytics on department performance.
        </div>
        <div v-else-if="route.path.includes('/settings')" class="mt-0.5 text-[12px] font-medium text-slate-500">
          Manage your personal profile and preferences.
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
      <div class="flex items-center gap-3 pl-6 border-l border-slate-200 cursor-pointer group" @click="logout">
        <div class="w-10 h-10 rounded-full bg-indigo-50 overflow-hidden border-2 border-transparent group-hover:border-[#5138ed] transition-all flex items-center justify-center">
          <span class="text-sm font-bold text-[#5138ed]">DH</span>
        </div>
        <div class="hidden md:flex flex-col">
          <span class="text-sm font-bold text-slate-800">{{ authStore.user?.name || 'Dr. Head' }}</span>
          <span class="text-xs font-medium text-[#5138ed]">
            {{ authStore.user?.department?.code ? `${authStore.user.department.code} Department Head` : 'Department Head' }}
          </span>
        </div>
      </div>
    </div>
  </header>
</template>
