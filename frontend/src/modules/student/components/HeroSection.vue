<script setup lang="ts">
import type { StudentProfile } from '../types'

defineProps<{
  profile: StudentProfile
  stats?: {
    examsCompleted: number
    upcomingExams: number
  }
}>()
</script>

<template>
  <!-- Full Width Hero Container flush under Header -->
  <div class="relative w-full min-h-[460px] bg-white overflow-hidden flex items-center">
    
    <!-- Background Image: hero-bg1.png -->
    <div 
      class="absolute inset-0 bg-no-repeat bg-cover bg-right z-0"
      style="background-image: url('/images/hero-bg1.png');"
    ></div>

    <!-- Left Content Layer -->
    <div class="relative z-10 px-8 sm:px-12 md:px-16 max-w-[820px] w-full py-10 flex flex-col justify-between space-y-6">

      <!-- Greeting & Title -->
      <div>
        <p class="text-indigo-600 font-bold text-xs uppercase tracking-widest">GOOD MORNING,</p>
        <h1 class="text-5xl sm:text-6xl md:text-7xl font-black tracking-tight text-slate-700 leading-none mt-2">
          {{ profile.name.split(' ')[0] }}
        </h1>
        <p class="text-slate-600 text-sm sm:text-base leading-relaxed mt-4 font-normal max-w-[540px]">
          Access your exams, track your academic progress, and stay organized throughout the semester.
        </p>
      </div>

      <!-- Row of 4 Feature Badges -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-8">
        <!-- 1. Take Exams -->
        <div class="flex items-center gap-3 p-3 rounded-xl bg-[#fbfafccd] backdrop-blur-md border border-[#fbfafc9e]  shadow-sm hover:shadow transition-all">
          <div class="w-9 h-9 rounded-lg bg-indigo-100/80 text-indigo-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
          </div>
          <div>
            <h4 class="text-xs font-bold text-slate-900 leading-tight">Take Exams</h4>
            <p class="text-[10px] text-slate-500 leading-tight mt-0.5">Start and complete your exams online</p>
          </div>
        </div>

        <!-- 2. View Results -->
        <div class="flex items-center gap-3 p-3 rounded-xl bg-[#fbfafce4] backdrop-blur-md border border-[#fbfafc29] shadow-sm hover:shadow transition-all">
          <div class="w-9 h-9 rounded-lg bg-emerald-100/80 text-emerald-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a２ ２ ０ ０１－２ ２h－２a２ ２ ０ ０１－２－２z"/>
            </svg>
          </div>
          <div>
            <h4 class="text-xs font-bold text-slate-900 leading-tight">View Results</h4>
            <p class="text-[10px] text-slate-500 leading-tight mt-0.5">Check your latest results instantly</p>
          </div>
        </div>

        <!-- 3. Track Progress -->
        <div class="flex items-center gap-3 p-3 rounded-xl bg-[#fbfafce4] backdrop-blur-md border border-white/80 shadow-sm hover:shadow transition-all">
          <div class="w-9 h-9 rounded-lg bg-amber-100/80 text-amber-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
            </svg>
          </div>
          <div>
            <h4 class="text-xs font-bold text-slate-900 leading-tight">Track Progress</h4>
            <p class="text-[10px] text-slate-500 leading-tight mt-0.5">Monitor your academic performance</p>
          </div>
        </div>

        <!-- 4. Stay Updated -->
        <div class="flex items-center gap-3 p-3 rounded-xl bg-[#fbfafce4] backdrop-blur-md border border-white/80 shadow-sm hover:shadow transition-all">
          <div class="w-9 h-9 rounded-lg bg-blue-100/80 text-blue-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
          </div>
          <div>
            <h4 class="text-xs font-bold text-slate-900 leading-tight">Stay Updated</h4>
            <p class="text-[10px] text-slate-500 leading-tight mt-0.5">Get important academic announcements</p>
          </div>
        </div>
      </div>

      <!-- Quote Bar -->
      <div class="max-w-[620px] p-3 px-4 rounded-2xl bg-[#fbfafce4] backdrop-blur-md border border-white/80 shadow-sm flex items-center gap-3 text-xs">
        <span class="text-indigo-600 font-serif font-black text-xl leading-none">“</span>
        <p class="text-slate-600 font-medium italic flex-1 truncate sm:whitespace-normal">
          Education is the most powerful weapon which you can use to change the world.
        </p>
        <span class="text-indigo-600 font-bold text-[11px] whitespace-nowrap ml-auto">– Nelson Mandela</span>
      </div>

    </div>
  </div>
</template>
