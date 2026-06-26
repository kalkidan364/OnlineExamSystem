<script setup lang="ts">
import { useRoute } from 'vue-router'

const route = useRoute()

const navItems = [
  { name: 'Dashboard', path: '/instructor/dashboard', icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z' },
  { name: 'Question Banks', path: '/instructor/question-banks', icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' },
  { name: 'Exams', path: '/instructor/exams', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
  { name: 'Schedule Exams', path: '/instructor/schedule', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
  { name: 'Students', path: '/instructor/students', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
  { name: 'Results', path: '/instructor/results', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
  { name: 'Reports', path: '/instructor/reports', icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
  { name: 'Profile', path: '/instructor/profile', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
  { name: 'Settings', path: '/instructor/settings', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' }
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
          <span class="text-[10px] text-slate-500 font-medium">Online Examination System</span>
        </div>
      </div>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 pt-4 pb-6 space-y-1">
      <router-link 
        v-for="item in navItems" 
        :key="item.name"
        :to="item.path"
        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group"
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

    <!-- Help Card -->
    <div class="p-4 mt-auto">
      <div class="bg-[#5138ed] rounded-2xl p-5 text-center shadow-md shadow-indigo-200">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mx-auto mb-3">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
        </div>
        <h4 class="text-white font-bold text-[14px] mb-1">Need Help?</h4>
        <p class="text-indigo-100 text-[11px] font-medium leading-relaxed mb-4">Visit the user guide or contact support.</p>
        <button class="w-full bg-white text-[#5138ed] text-[12px] font-bold py-2.5 rounded-xl hover:bg-indigo-50 transition-colors shadow-sm">
          Contact Support
        </button>
      </div>
    </div>

    <!-- Bottom Graphic (Placeholder matching the architectural style) -->
    <div class="p-6 mt-auto">
      <div class="w-full flex flex-col items-center justify-center opacity-60">
        <div class="w-16 h-16 border-2 border-slate-200 rounded-t-full mb-2"></div>
        <div class="flex gap-1 mb-2">
           <div class="w-3 h-4 border border-slate-200"></div>
           <div class="w-3 h-4 border border-slate-200"></div>
           <div class="w-3 h-4 border border-slate-200"></div>
        </div>
        <span class="text-xs font-bold text-[#2b4c7e] tracking-wide uppercase">Wollo University</span>
        <span class="text-[9px] text-slate-400 font-medium">Excellence and Service!</span>
      </div>
    </div>

  </aside>
</template>
