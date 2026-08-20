<script setup lang="ts">
import { ref, computed } from 'vue'

interface LogEntry {
  id: number
  time: string
  user: string
  email: string
  role: 'Super Admin' | 'Instructor' | 'Student' | 'System'
  action: string
  actionType: 'Login' | 'Created' | 'Updated' | 'Deleted' | 'Submitted' | 'Exported' | 'Backup' | 'System Event' | 'Login Failed'
  module: string
  description: string
  ipAddress: string
  status: 'Success' | 'Failed'
}

const filterType = ref('All Activities')
const currentPage = ref(1)
const perPage = 10

const filterTypes = [
  { label: 'All Activities' },
  { label: 'Successful' },
  { label: 'Failed' },
  { label: 'Logins' },
  { label: 'Data Changes' },
  { label: 'System Events' },
]

const logs = ref<LogEntry[]>([
  { id: 1, time: 'May 23, 2025\n10:34:35 AM', user: 'Dr. Abebe Kebede', email: 'abebe.kebede@wu.edu.et', role: 'Super Admin', action: 'Login', actionType: 'Login', module: 'Authentication', description: 'User logged in to the system', ipAddress: '192.168.1.10', status: 'Success' },
  { id: 2, time: 'May 23, 2025\n10:15:12 AM', user: 'Selamawit Getachew', email: 'selamawit.g@wu.edu.et', role: 'Instructor', action: 'Created', actionType: 'Created', module: 'Exams', description: 'Created a new exam "Database Midterm"', ipAddress: '192.168.1.23', status: 'Success' },
  { id: 3, time: 'May 23, 2025\n10:02:47 AM', user: 'Daniel Kassa', email: 'daniel.k@wu.edu.et', role: 'Instructor', action: 'Updated', actionType: 'Updated', module: 'Questions', description: 'Updated question in Question Bank', ipAddress: '192.168.1.23', status: 'Success' },
  { id: 4, time: 'May 23, 2025\n09:45:30 AM', user: 'Medhanit Alemu', email: 'medhanit.a@wu.edu.et', role: 'Student', action: 'Submitted', actionType: 'Submitted', module: 'Exams', description: 'Submitted exam "Database Quiz 2"', ipAddress: '192.168.1.45', status: 'Success' },
  { id: 5, time: 'May 23, 2025\n09:30:18 AM', user: 'Admin User', email: 'admin@wu.edu.et', role: 'Super Admin', action: 'Created', actionType: 'Created', module: 'Users', description: 'Created new instructor account', ipAddress: '192.168.1.10', status: 'Success' },
  { id: 6, time: 'May 23, 2025\n19:12:09 AM', user: 'Yonas Alemu', email: 'yonas.a@wu.edu.et', role: 'Instructor', action: 'Deleted', actionType: 'Deleted', module: 'Exams', description: 'Deleted exam "Old Practice Test"', ipAddress: '192.168.1.23', status: 'Success' },
  { id: 7, time: 'May 23, 2025\n08:55:44 AM', user: 'Rahab Solomon', email: 'rahab.s@wu.edu.et', role: 'Student', action: 'Login Failed', actionType: 'Login Failed', module: 'Authentication', description: 'Failed login attempt', ipAddress: '192.168.1.45', status: 'Failed' },
  { id: 8, time: 'May 23, 2025\n08:40:22 AM', user: 'Dr. Abebe Kebede', email: 'abebe.kebede@wu.edu.et', role: 'Super Admin', action: 'Backup', actionType: 'Backup', module: 'System', description: 'System backup completed', ipAddress: '192.168.1.10', status: 'Success' },
  { id: 9, time: 'May 23, 2025\n08:20:11 AM', user: 'Hanna Mengesha', email: 'hanna.m@wu.edu.et', role: 'Instructor', action: 'Exported', actionType: 'Exported', module: 'Reports', description: 'Exported exam results report', ipAddress: '192.168.1.23', status: 'Success' },
  { id: 10, time: 'May 23, 2025\n08:05:58 AM', user: 'System', email: 'system@wu.edu.et', role: 'System', action: 'System Event', actionType: 'System Event', module: 'System', description: 'System auto cleanup completed', ipAddress: '192.168.1.1', status: 'Success' }
])

const activitySummary = computed(() => ({
  all: 245,
  successful: 212,
  failed: 15,
  logins: 48,
  dataChanges: 97,
  systemEvents: 85,
}))

const topActiveUsers = computed(() => [
  { name: 'Dr. Abebe Kebede', role: 'Super Admin', count: 128 },
  { name: 'Selamawit Getachew', role: 'Instructor', count: 86 },
  { name: 'Daniel Kassa', role: 'Instructor', count: 64 },
  { name: 'Medhanit Alemu', role: 'Instructor', count: 42 },
  { name: 'Yonas Alemu', role: 'Instructor', count: 35 },
])

const filteredLogs = computed(() => {
  if (filterType.value === 'All Activities') return logs.value
  if (filterType.value === 'Successful') return logs.value.filter(l => l.status === 'Success')
  if (filterType.value === 'Failed') return logs.value.filter(l => l.status === 'Failed')
  if (filterType.value === 'Logins') return logs.value.filter(l => l.actionType === 'Login' || l.actionType === 'Login Failed')
  if (filterType.value === 'Data Changes') return logs.value.filter(l => ['Created','Updated','Deleted','Exported'].includes(l.actionType))
  if (filterType.value === 'System Events') return logs.value.filter(l => l.actionType === 'System Event' || l.actionType === 'Backup')
  return logs.value
})

const totalPages = computed(() => 25)
const paginatedLogs = computed(() => filteredLogs.value)

const getRoleBadge = (role: string) => {
  if (role === 'Super Admin') return 'bg-indigo-50 text-[#5138ed]'
  if (role === 'Instructor') return 'bg-blue-50 text-blue-500'
  if (role === 'Student') return 'bg-emerald-50 text-emerald-500'
  return 'bg-slate-50 text-slate-500'
}

const getActionIcon = (type: string) => {
  if (type === 'Login') return { icon: 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1', color: 'text-emerald-500' }
  if (type === 'Created') return { icon: 'M12 4v16m8-8H4', color: 'text-emerald-500' }
  if (type === 'Updated') return { icon: 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z', color: 'text-amber-500' }
  if (type === 'Deleted') return { icon: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16', color: 'text-rose-500' }
  if (type === 'Submitted') return { icon: 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8', color: 'text-[#5138ed]' }
  if (type === 'Exported') return { icon: 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4', color: 'text-[#5138ed]' }
  if (type === 'Backup') return { icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4', color: 'text-[#5138ed]' }
  if (type === 'System Event') return { icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', color: 'text-[#5138ed]' }
  if (type === 'Login Failed') return { icon: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', color: 'text-rose-500' }
  return { icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', color: 'text-slate-400' }
}

const getAvatarColor = (role: string) => {
  if (role === 'Super Admin') return 'bg-indigo-100 text-[#5138ed]'
  if (role === 'Instructor') return 'bg-blue-100 text-blue-600'
  if (role === 'Student') return 'bg-emerald-100 text-emerald-600'
  return 'bg-slate-100 text-slate-500'
}

const getAvatarInitials = (name: string) => {
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2)
}
</script>

<template>
  <div class="max-w-[1500px] mx-auto">

    <!-- Header -->
    <div class="flex items-start justify-between mb-6">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center shrink-0">
          <svg class="w-6 h-6 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <h1 class="text-[22px] font-bold text-slate-800">Active Logs</h1>
          <p class="text-[13px] text-slate-500">Monitor all recent activities and system events in real-time.</p>
          <div class="flex items-center gap-1 mt-1 text-[12px] text-slate-400">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Dashboard</span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-800 font-bold">Active Logs</span>
          </div>
        </div>
      </div>

      <!-- Filters Right -->
      <div class="flex items-center gap-3">
        <div class="relative">
          <select v-model="filterType" class="appearance-none border border-slate-200 rounded-xl px-4 py-2.5 pr-10 text-[13px] text-slate-700 font-bold bg-white focus:outline-none focus:border-[#5138ed] shadow-sm">
            <option v-for="f in filterTypes" :key="f.label" :value="f.label">⚙️ {{ f.label }}</option>
          </select>
          <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
        <div class="flex items-center gap-2 border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 font-bold bg-white shadow-sm">
          <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          <span>May 18, 2025 - May 23, 2025</span>
        </div>
        <button class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-600 transition-colors shadow-sm shadow-indigo-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
          Filter
        </button>
      </div>
    </div>

    <!-- Main Content + Sidebar Grid -->
    <div class="flex flex-col gap-6">

      <!-- Top: Log Table -->
      <div class="w-full bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col h-[fit-content]">
        <div class="flex-1">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/60 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                <th class="px-5 py-3.5">Time</th>
                <th class="px-4 py-3.5">User</th>
                <th class="px-4 py-3.5">Role</th>
                <th class="px-4 py-3.5">Action</th>
                <th class="px-4 py-3.5">Module</th>
                <th class="px-4 py-3.5">Description</th>
                <th class="px-4 py-3.5">IP Address</th>
                <th class="px-4 py-3.5">Status</th>
                <th class="px-4 py-3.5 text-center">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
              <tr v-for="log in paginatedLogs" :key="log.id" class="hover:bg-slate-50/50 transition-colors">
                <!-- Time -->
                <td class="px-5 py-3 whitespace-nowrap">
                  <span class="text-[11px] font-bold text-slate-500">{{ log.time.split('\n')[0] }}</span><br>
                  <span class="text-[11px] font-bold text-slate-800">{{ log.time.split('\n')[1] }}</span>
                </td>

                <!-- User -->
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <div :class="getAvatarColor(log.role)" class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold shrink-0">
                      {{ getAvatarInitials(log.user) }}
                    </div>
                    <div>
                      <p class="text-[12px] font-bold text-slate-800 whitespace-nowrap">{{ log.user }}</p>
                      <p class="text-[11px] text-slate-400">{{ log.email }}</p>
                    </div>
                  </div>
                </td>

                <!-- Role -->
                <td class="px-4 py-3">
                  <span :class="getRoleBadge(log.role)" class="px-2.5 py-1 rounded-md text-[10px] font-bold whitespace-nowrap">
                    {{ log.role }}
                  </span>
                </td>

                <!-- Action -->
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <svg :class="getActionIcon(log.actionType).color" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getActionIcon(log.actionType).icon"/>
                    </svg>
                    <span class="text-[12px] font-bold text-slate-800 whitespace-nowrap">{{ log.action }}</span>
                  </div>
                </td>

                <!-- Module -->
                <td class="px-4 py-3 text-[12px] font-bold text-slate-800 whitespace-nowrap">{{ log.module }}</td>

                <!-- Description -->
                <td class="px-4 py-3 text-[12px] text-slate-500 max-w-[200px] truncate" :title="log.description">{{ log.description }}</td>

                <!-- IP -->
                <td class="px-4 py-3 text-[11px] font-bold text-slate-500 whitespace-nowrap">{{ log.ipAddress }}</td>

                <!-- Status -->
                <td class="px-4 py-3">
                  <span v-if="log.status === 'Success'" class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-emerald-50 text-emerald-500 capitalize">
                    {{ log.status }}
                  </span>
                  <span v-else class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-rose-50 text-rose-500 capitalize">
                    {{ log.status }}
                  </span>
                </td>

                <!-- View -->
                <td class="px-4 py-3 text-center">
                  <button class="w-7 h-7 rounded-full bg-indigo-50 text-[#5138ed] flex items-center justify-center hover:bg-[#5138ed] hover:text-white transition-colors mx-auto">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between mt-auto">
          <span class="text-[12px] font-bold text-slate-400">
            Showing 1 to 10 of 245 activities
          </span>
          <div class="flex items-center gap-1.5">
            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#5138ed] text-white font-bold text-[12px]">1</button>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-50 font-bold text-[12px]">2</button>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-50 font-bold text-[12px]">3</button>
            <span class="w-8 h-8 flex items-center justify-center text-slate-400 text-[12px]">...</span>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-50 font-bold text-[12px]">25</button>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
          </div>
        </div>
      </div>

                <!-- Bottom Cards -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Activity Summary Card -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
          <h3 class="text-[14px] font-bold text-slate-800 mb-5">Activity Summary</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </div>
                <span class="text-[12px] font-bold text-slate-800">All Activities</span>
              </div>
              <span class="text-[13px] font-black text-[#5138ed]">{{ activitySummary.all }}</span>
            </div>
            
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[12px] font-bold text-slate-800">Successful</span>
              </div>
              <span class="text-[13px] font-black text-emerald-500">{{ activitySummary.successful }}</span>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-rose-50 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[12px] font-bold text-slate-800">Failed</span>
              </div>
              <span class="text-[13px] font-black text-rose-500">{{ activitySummary.failed }}</span>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                </div>
                <span class="text-[12px] font-bold text-slate-800">Logins</span>
              </div>
              <span class="text-[13px] font-black text-[#5138ed]">{{ activitySummary.logins }}</span>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </div>
                <span class="text-[12px] font-bold text-slate-800">Data Changes</span>
              </div>
              <span class="text-[13px] font-black text-amber-500">{{ activitySummary.dataChanges }}</span>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-purple-50 flex items-center justify-center shrink-0">
                  <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-[12px] font-bold text-slate-800">System Events</span>
              </div>
              <span class="text-[13px] font-black text-purple-500">{{ activitySummary.systemEvents }}</span>
            </div>

          </div>
        </div>

        <!-- Top Active Users -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
          <h3 class="text-[14px] font-bold text-slate-800 mb-5">Top Active Users</h3>
          <div class="space-y-4">
            <div v-for="user in topActiveUsers" :key="user.name" class="flex items-center gap-3">
              <div :class="getAvatarColor(user.role)" class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-bold shrink-0">
                {{ getAvatarInitials(user.name) }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-[12px] font-bold text-slate-800 truncate">{{ user.name }}</p>
                <p class="text-[10px] font-medium text-slate-400">{{ user.role }}</p>
              </div>
              <span class="text-[11px] font-bold text-slate-600 whitespace-nowrap">{{ user.count }} actions</span>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
          <h3 class="text-[14px] font-bold text-slate-800 mb-4">Quick Actions</h3>
          <div class="space-y-3">
            <button class="flex items-center justify-between w-full p-2.5 text-left border border-slate-100 rounded-xl hover:border-slate-200 transition-colors group">
              <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span class="font-bold text-[12px] text-slate-700 group-hover:text-[#5138ed] transition-colors">Export Logs</span>
              </div>
            </button>
            <button class="flex items-center justify-between w-full p-2.5 text-left border border-slate-100 rounded-xl hover:border-slate-200 transition-colors group">
              <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span class="font-bold text-[12px] text-slate-700 group-hover:text-rose-500 transition-colors">Clear Old Logs</span>
              </div>
            </button>
            <button class="flex items-center justify-between w-full p-2.5 text-left border border-slate-100 rounded-xl hover:border-slate-200 transition-colors group">
              <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="font-bold text-[12px] text-slate-700 group-hover:text-[#5138ed] transition-colors">System Audit Report</span>
              </div>
            </button>
            <button class="flex items-center justify-between w-full p-2.5 text-left border border-slate-100 rounded-xl hover:border-slate-200 transition-colors group">
              <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span class="font-bold text-[12px] text-slate-700 group-hover:text-emerald-500 transition-colors">Security Logs</span>
              </div>
            </button>
          </div>
        </div>
      </div>

              </div>
  </div>
</template>
