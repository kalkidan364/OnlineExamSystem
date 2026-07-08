<script setup lang="ts">
import { useAuthStore } from '../../modules/auth/store/authStore'
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const authStore = useAuthStore()
const route = useRoute()

// Map routes to dynamic titles
const pageTitle = computed(() => {
  if (route.path.includes('/users')) return 'User Management'
  if (route.path.includes('/departments')) return 'Department & Course Management'
  if (route.path.includes('/settings')) return 'System Settings'
  return 'Super Admin Dashboard'
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
        <div v-if="route.path.includes('/dashboard')" class="mt-0.5 text-[12px] font-medium text-slate-500">
          System-wide overview and key metrics.
        </div>
        <div v-else-if="route.path.includes('/users')" class="mt-0.5 text-[12px] font-medium text-slate-500">
          Manage system users, instructors, and students.
        </div>
        <div v-else-if="route.path.includes('/departments')" class="mt-0.5 text-[12px] font-medium text-slate-500">
          Manage departments and course assignments.
        </div>
        <div v-else-if="route.path.includes('/settings')" class="mt-0.5 text-[12px] font-medium text-slate-500">
          Global application settings and configuration.
        </div>
      </div>
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
        <div class="w-10 h-10 rounded-full bg-slate-200 overflow-hidden border-2 border-transparent group-hover:border-rose-500 transition-all">
          <img src="https://i.pravatar.cc/150?u=admin123" alt="Profile" class="w-full h-full object-cover" />
        </div>
        <div class="hidden md:flex flex-col">
          <span class="text-sm font-bold text-slate-800">{{ authStore.user?.name || 'Super Admin' }}</span>
          <span class="text-xs font-medium text-rose-500">Administrator</span>
        </div>
      </div>
    </div>
  </header>
</template>
