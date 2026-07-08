<script setup lang="ts">
import { useRoute } from 'vue-router'

const route = useRoute()

const navItems = [
  { name: 'Dashboard',   path: '/dept-head/dashboard',    icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z' },
  { name: 'Instructors', path: '/dept-head/instructors',  icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
  { name: 'Students',    path: '/dept-head/students',     icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
  { name: 'Courses',     path: '/dept-head/courses',      icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' },
  { name: 'Exams',       path: '/dept-head/exams',        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
  { name: 'Reports',     path: '/dept-head/reports',      icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
  { name: 'Settings',    path: '/dept-head/settings',     icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
]
</script>

<template>
  <aside class="w-56 bg-white border-r border-slate-100 flex flex-col h-screen fixed left-0 top-0 overflow-hidden">
    
    <!-- Logo Area -->
    <div class="flex items-center px-5 pt-10 pb-4 border-b border-slate-50">
      <div class="flex items-center gap-2">
        <img src="../../assets/images/logo.png" alt="Wollo University" class="w-9 h-9 object-contain rounded-full shadow-sm" />
        <div class="flex flex-col">
          <span class="text-[14px] font-bold text-slate-900 leading-tight">Wollo University</span>
          <span class="text-[10px] text-slate-500 font-medium">Department Head</span>
        </div>
      </div>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 pt-4 pb-6 space-y-1 overflow-y-auto">
      <router-link 
        v-for="item in navItems" 
        :key="item.name"
        :to="item.path"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative"
        :class="[
          route.path.startsWith(item.path) 
            ? 'bg-indigo-50 text-[#5138ed] font-semibold' 
            : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'
        ]"
      >
        <svg 
          class="w-5 h-5 flex-shrink-0 transition-colors duration-200" 
          :class="route.path.startsWith(item.path) ? 'text-[#5138ed]' : 'text-slate-400 group-hover:text-slate-600'"
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
        </svg>
        <span class="text-[13px] tracking-wide font-medium">{{ item.name }}</span>
      </router-link>
    </nav>

    <!-- Bottom Graphic -->
    <div class="p-6 mt-auto">
      <div class="w-full flex flex-col items-center justify-center opacity-60">
        <div class="w-16 h-16 border-2 border-slate-200 rounded-t-full mb-2 flex items-center justify-center">
          <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <span class="text-xs font-bold text-slate-500 tracking-wide uppercase">Department</span>
        <span class="text-[9px] text-slate-400 font-medium">Head Portal</span>
      </div>
    </div>
  </aside>
</template>
