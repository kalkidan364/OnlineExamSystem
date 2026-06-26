<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import type { StudentProfile, Announcement } from '../../types'

const props = defineProps<{
  profile: StudentProfile
  announcements: Announcement[]
}>()

const emit = defineEmits<{
  (e: 'open-profile'): void
  (e: 'open-notifications'): void
}>()

const time = ref<string>('')
let timer: number | null = null

onMounted(() => {
  const updateTime = () => {
    const now = new Date()
    time.value = now.toLocaleTimeString('en-US', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      hour12: true
    })
  }
  updateTime()
  timer = window.setInterval(updateTime, 1000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>

<template>
  <header class="sticky top-0 z-40 w-full border-b border-slate-200 bg-white/85 backdrop-blur-md">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6">
      <!-- Brand/Logo Section -->
      <div class="flex items-center gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-md shadow-indigo-600/10">
          <span class="font-serif text-lg font-bold tracking-tight">W</span>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <span class="font-serif text-base font-bold tracking-tight text-slate-900">WOLLO UNIVERSITY</span>
            <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold text-indigo-600 uppercase tracking-wider">Senate Certified</span>
          </div>
          <p class="text-xs font-medium text-slate-500">Online Examination System</p>
        </div>
      </div>

      <!-- Action Items / Statuses -->
      <div class="flex items-center gap-6">
        <!-- Live System Time -->
        <div class="hidden items-center gap-2 rounded-lg bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600 md:flex border border-slate-100">
          <svg class="h-3.5 w-3.5 text-indigo-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          <span class="font-mono text-slate-700">{{ time || '04:28:41 AM' }}</span>
        </div>

        <!-- Academic Term Badge -->
        <span class="hidden rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700 sm:inline-block border border-slate-200">
          {{ profile.semester }} • AY {{ profile.academicYear.split(' ')[0] }}
        </span>

        <!-- Alerts / Notifications Trigger -->
        <button
          @click="emit('open-notifications')"
          class="relative rounded-xl p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-800"
          aria-label="View Announcements"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
          <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-amber-500 ring-2 ring-white"></span>
        </button>

        <!-- Student Profile Pill -->
        <div class="flex items-center gap-3 border-l border-slate-200 pl-6">
          <button
            @click="emit('open-profile')"
            class="flex items-center gap-3 text-left focus:outline-none group"
          >
            <div class="relative">
              <img
                :src="profile.avatar"
                :alt="profile.name"
                class="h-9 w-9 rounded-full object-cover ring-2 ring-indigo-500/20 transition-all group-hover:ring-indigo-500"
                referrerpolicy="no-referrer"
              />
              <span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white" title="Connected to Secure Exam Server"></span>
            </div>
            <div class="hidden lg:block">
              <p class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ profile.name }}</p>
              <p class="text-[10px] font-mono text-slate-500 uppercase tracking-wider">{{ profile.id }}</p>
            </div>
          </button>
        </div>

        <!-- Security Indicator & Logout -->
        <div class="flex items-center gap-2">
          <div class="hidden xl:flex items-center gap-1.5 text-[10px] uppercase tracking-wider font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            <span>Verified IP</span>
          </div>
          <button
            @click="() => alert('To log out or switch student sessions, use your general dashboard settings.')"
            class="rounded-xl p-2 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all"
            title="Logout from Exam Portal"
          >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
          </button>
        </div>
      </div>
    </div>
  </header>
</template>
