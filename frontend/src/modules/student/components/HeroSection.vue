<script setup lang="ts">
import type { StudentProfile } from '../../types'

defineProps<{
  profile: StudentProfile
}>()
</script>

<template>
  <div class="relative overflow-hidden rounded-2xl border border-indigo-100 bg-linear-to-r from-indigo-900 via-indigo-950 to-slate-950 p-6 text-white shadow-xl md:p-8">
    <!-- Absolute Decorative Vector elements for premium layout -->
    <div class="absolute top-0 right-0 -translate-y-12 translate-x-12 opacity-10">
      <svg class="h-64 w-64" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" /></svg>
    </div>
    <div class="absolute -bottom-8 -left-8 h-48 w-48 rounded-full bg-indigo-600/10 blur-3xl"></div>
    <div class="absolute -top-8 right-1/4 h-32 w-32 rounded-full bg-purple-500/10 blur-2xl"></div>

    <!-- Main Grid Content -->
    <div class="relative flex flex-col justify-between gap-6 md:flex-row md:items-center">
      <!-- Student Welcome / Details -->
      <div class="space-y-4">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold backdrop-blur-md">
          <svg class="h-3.5 w-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
          <span class="tracking-wide">Wollo University • Dessie Campus</span>
        </div>

        <div class="space-y-1.5">
          <h1 class="text-3xl font-bold tracking-tight md:text-4xl">
            Welcome back, {{ profile.name.split(' ')[0] }} 👋
          </h1>
          <p class="text-sm font-medium text-slate-300">
            {{ profile.program }} in <span class="text-white font-semibold">{{ profile.department }}</span>
          </p>
        </div>

        <div class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-slate-400">
          <div class="flex items-center gap-1.5">
            <span class="font-semibold text-slate-300">Student ID:</span>
            <span class="font-mono text-slate-100 bg-white/5 px-1.5 py-0.5 rounded">{{ profile.id }}</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="font-semibold text-slate-300">Dean List Rank:</span>
            <span class="text-indigo-400 font-bold">Top 2%</span>
          </div>
          <div class="flex items-center gap-1.5">
            <svg class="h-3 w-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span>KIOT Computing block</span>
          </div>
        </div>
      </div>

      <!-- Small Elegant KPI Board within Hero -->
      <div class="grid grid-cols-2 gap-3 min-w-[280px]">
        <div class="rounded-xl bg-white/5 p-4 border border-white/10 backdrop-blur-xs hover:bg-white/10 transition-colors">
          <div class="flex items-center gap-2 text-slate-300 mb-1">
            <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
            <span class="text-xs font-medium uppercase tracking-wider">Current CGPA</span>
          </div>
          <p class="text-2xl font-bold font-mono tracking-tight text-white">{{ profile.cgpa.toFixed(2) }}</p>
          <p class="text-[10px] text-emerald-400 font-semibold mt-0.5">First Class Honors</p>
        </div>

        <div class="rounded-xl bg-white/5 p-4 border border-white/10 backdrop-blur-xs hover:bg-white/10 transition-colors">
          <div class="flex items-center gap-2 text-slate-300 mb-1">
            <svg class="h-4 w-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            <span class="text-xs font-medium uppercase tracking-wider">Credits Done</span>
          </div>
          <p class="text-2xl font-bold font-mono tracking-tight text-white">{{ profile.creditsCompleted }}</p>
          <p class="text-[10px] text-slate-400 mt-0.5">140 required for B.Sc.</p>
        </div>
      </div>
    </div>
  </div>
</template>
