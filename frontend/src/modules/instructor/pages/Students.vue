<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const activeDropdown = ref<number | null>(null)

const toggleDropdown = (id: number) => {
  activeDropdown.value = activeDropdown.value === id ? null : id
}

const students = ref([
  { id: 1, name: 'Selamawit Getachew', studentId: 'WU/2021/CS/001', email: 'selamawit.get@wollo.edu.et', section: 'CS-304-A', completed: 4, total: 6, progress: 67, status: 'Active', avatar: 'https://i.pravatar.cc/150?u=a042581f4e290267041' },
  { id: 2, name: 'Kebede Assefa', studentId: 'WU/2021/CS/002', email: 'kebede.assefa@wollo.edu.et', section: 'CS-304-A', completed: 6, total: 6, progress: 100, status: 'Completed All Exams', avatar: 'https://i.pravatar.cc/150?u=a042581f4e290267042' },
  { id: 3, name: 'Hanna Mengesha', studentId: 'WU/2022/CS/015', email: 'hanna.mengesha@wollo.edu.et', section: 'CS-304-A', completed: 3, total: 6, progress: 50, status: 'Pending Exams', avatar: 'https://i.pravatar.cc/150?u=a042581f4e290267043' },
  { id: 4, name: 'Yonas Alemu', studentId: 'WU/2022/CS/018', email: 'yonas.alemu@wollo.edu.et', section: 'CS-304-A', completed: 1, total: 6, progress: 17, status: 'At Risk', avatar: 'https://i.pravatar.cc/150?u=a042581f4e290267044' },
  { id: 5, name: 'Lidya Gebremedhin', studentId: 'WU/2022/CS/022', email: 'lidya.gebremedhin@wollo.edu.et', section: 'CS-304-A', completed: 2, total: 6, progress: 33, status: 'Pending Exams', avatar: 'https://i.pravatar.cc/150?u=a042581f4e290267045' },
  { id: 6, name: 'Daniel Kassa', studentId: 'WU/2023/CS/031', email: 'daniel.kassa@wollo.edu.et', section: 'CS-304-A', completed: 0, total: 6, progress: 0, status: 'Inactive', avatar: 'https://i.pravatar.cc/150?u=a042581f4e290267046' },
  { id: 7, name: 'Rahel Solomon', studentId: 'WU/2023/CS/032', email: 'rahel.solomon@wollo.edu.et', section: 'CS-304-A', completed: 1, total: 6, progress: 17, status: 'At Risk', avatar: 'https://i.pravatar.cc/150?u=a042581f4e290267047' },
  { id: 8, name: 'Bereket Mulugeta', studentId: 'WU/2023/CS/035', email: 'bereket.mulugeta@wollo.edu.et', section: 'CS-304-A', completed: 0, total: 6, progress: 0, status: 'Inactive', avatar: 'https://i.pravatar.cc/150?u=a042581f4e290267048' },
])

const getStatusStyles = (status: string) => {
  if (status === 'Active') return 'bg-emerald-50 text-emerald-600'
  if (status === 'Completed All Exams') return 'bg-blue-50 text-blue-600'
  if (status === 'Pending Exams') return 'bg-orange-50 text-orange-500'
  if (status === 'At Risk') return 'bg-rose-50 text-rose-600'
  return 'bg-slate-50 text-slate-500'
}

const getProgressColor = (status: string) => {
  if (status === 'Active') return 'bg-emerald-500'
  if (status === 'Completed All Exams') return 'bg-blue-500'
  if (status === 'Pending Exams') return 'bg-orange-500'
  if (status === 'At Risk') return 'bg-rose-500'
  return 'bg-slate-300'
}

const getDotColor = (status: string) => {
  if (status === 'Active') return 'bg-emerald-500'
  if (status === 'Completed All Exams') return 'bg-blue-500'
  if (status === 'Pending Exams') return 'bg-orange-500'
  if (status === 'At Risk') return 'bg-rose-500'
  return 'bg-slate-400'
}

</script>

<template>
  <div class="max-w-[1500px] mx-auto px-4 py-2">

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-end gap-3 mb-6 -mt-12 absolute right-8 top-6 z-40 hidden lg:flex">
      <button class="flex items-center gap-2 px-4 py-2.5 border border-slate-200 text-[#5138ed] text-xs font-bold rounded-xl hover:bg-indigo-50 transition-colors bg-white">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        Import Students
      </button>
      <button class="flex items-center gap-2 px-4 py-2.5 bg-[#5138ed] text-white text-xs font-bold rounded-xl shadow-sm shadow-indigo-200 hover:bg-[#4530d1] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        Export Students
      </button>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">
      
      <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-[#5138ed] shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[10px] font-bold text-slate-500 uppercase">Total Students</span>
          <span class="text-2xl font-black text-slate-800 leading-none my-1">48</span>
          <span class="text-[10px] font-medium text-slate-400">All enrolled students</span>
        </div>
      </div>

      <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[10px] font-bold text-slate-500 uppercase">Active Students</span>
          <span class="text-2xl font-black text-slate-800 leading-none my-1">42</span>
          <span class="text-[10px] font-medium text-slate-400">87.5% of total students</span>
        </div>
      </div>

      <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[10px] font-bold text-slate-500 uppercase">Students Completed Exams</span>
          <span class="text-2xl font-black text-slate-800 leading-none my-1">28</span>
          <span class="text-[10px] font-medium text-slate-400">58.3% of total students</span>
        </div>
      </div>

      <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[10px] font-bold text-slate-500 uppercase">Students Pending Exams</span>
          <span class="text-2xl font-black text-slate-800 leading-none my-1">20</span>
          <span class="text-[10px] font-medium text-slate-400">41.7% of total students</span>
        </div>
      </div>

      <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-[#5138ed] shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <div class="flex flex-col">
          <span class="text-[10px] font-bold text-slate-500 uppercase">Average Attendance</span>
          <span class="text-2xl font-black text-slate-800 leading-none my-1">78.4%</span>
          <span class="text-[10px] font-medium text-slate-400">Overall class attendance</span>
        </div>
      </div>

    </div>

    <!-- Main Content Layout -->
    <div class="flex flex-col xl:flex-row gap-6">

      <!-- Left Column (Table) -->
      <div class="flex-1 min-w-0 flex flex-col">
        
        <!-- Search & Filter Bar -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm flex items-center gap-4 flex-wrap mb-6">
          <div class="relative flex-1 min-w-[200px]">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" class="w-full pl-9 pr-3 py-2 border-0 bg-transparent text-sm placeholder-slate-400 focus:outline-none" placeholder="Search by name, ID number or email...">
          </div>
          <div class="w-px h-6 bg-slate-200"></div>
          <select class="appearance-none bg-transparent text-[13px] font-semibold text-slate-700 focus:outline-none pr-6 cursor-pointer">
            <option>All Sections</option>
          </select>
          <svg class="w-3.5 h-3.5 text-slate-400 -ml-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          <div class="w-px h-6 bg-slate-200 ml-2"></div>
          <select class="appearance-none bg-transparent text-[13px] font-semibold text-slate-700 focus:outline-none pr-6 cursor-pointer">
            <option>All Academic Years</option>
          </select>
          <svg class="w-3.5 h-3.5 text-slate-400 -ml-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          <div class="w-px h-6 bg-slate-200 ml-2"></div>
          <select class="appearance-none bg-transparent text-[13px] font-semibold text-slate-700 focus:outline-none pr-6 cursor-pointer">
            <option>All Status</option>
          </select>
          <svg class="w-3.5 h-3.5 text-slate-400 -ml-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          <button class="ml-auto flex items-center gap-1.5 px-3 py-1.5 text-[#5138ed] text-[12px] font-bold rounded-lg hover:bg-indigo-50 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Reset Filter
          </button>
        </div>

        <!-- Students Table -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm flex-1 flex flex-col">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
              <thead>
                <tr class="text-[9px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                  <th class="py-4 pl-6 pr-3">Student</th>
                  <th class="py-4 px-3">ID Number</th>
                  <th class="py-4 px-3">Email</th>
                  <th class="py-4 px-3">Section</th>
                  <th class="py-4 px-3 w-48">Exam Progress</th>
                  <th class="py-4 px-3">Status</th>
                  <th class="py-4 px-6 text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="student in students" :key="student.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors last:border-0 group">
                  <td class="py-4 pl-6 pr-3">
                    <div class="flex items-center gap-3">
                      <img :src="student.avatar" class="w-8 h-8 rounded-full object-cover border border-slate-200" alt="Avatar" />
                      <span class="text-[13px] font-bold text-slate-800">{{ student.name }}</span>
                    </div>
                  </td>
                  <td class="py-4 px-3 text-[12px] font-semibold text-slate-600">{{ student.studentId }}</td>
                  <td class="py-4 px-3 text-[12px] font-medium text-slate-500">{{ student.email }}</td>
                  <td class="py-4 px-3 text-[12px] font-semibold text-slate-600">{{ student.section }}</td>
                  <td class="py-4 px-3">
                    <div class="flex flex-col gap-1 w-full max-w-[160px]">
                      <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-slate-500">{{ student.completed }} of {{ student.total }} Exams Completed</span>
                      </div>
                      <div class="flex items-center gap-2">
                        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                          <div class="h-full rounded-full transition-all duration-500" :class="getProgressColor(student.status)" :style="`width: ${student.progress}%`"></div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-700 w-6 text-right">{{ student.progress }}%</span>
                      </div>
                    </div>
                  </td>
                  <td class="py-4 px-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold" :class="getStatusStyles(student.status)">
                      <span class="w-1.5 h-1.5 rounded-full" :class="getDotColor(student.status)"></span>
                      {{ student.status }}
                    </span>
                  </td>
                  <td class="py-4 px-6">
                    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                      <button @click="router.push(`/instructor/students/${student.id}`)" class="w-7 h-7 rounded-lg flex items-center justify-center text-[#5138ed] hover:bg-indigo-50 transition-colors" title="View Details">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                      </button>
                      <button @click="router.push(`/instructor/students/${student.id}/results`)" class="w-7 h-7 rounded-lg flex items-center justify-center text-[#5138ed] hover:bg-indigo-50 transition-colors" title="Exam Results">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                      </button>
                      <button class="w-7 h-7 rounded-lg flex items-center justify-center text-[#5138ed] hover:bg-indigo-50 transition-colors" title="Send Message">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                      </button>
                      <div class="relative">
                        <button @click="toggleDropdown(student.id)" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors" title="More Options">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div v-if="activeDropdown === student.id" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50 overflow-hidden">
                          <button class="w-full text-left px-4 py-2.5 text-[12px] font-bold text-slate-700 hover:bg-slate-50 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Student Report
                          </button>
                          <button class="w-full text-left px-4 py-2.5 text-[12px] font-bold text-slate-700 hover:bg-slate-50 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Print Student Report
                          </button>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-auto p-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[11px] text-slate-500 font-medium">Showing 1 to 8 of 48 students</span>
            <div class="flex items-center gap-1">
              <button class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-slate-50 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
              <button class="w-7 h-7 flex items-center justify-center rounded-lg bg-[#5138ed] text-white font-bold text-[11px] shadow-sm">1</button>
              <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-600 font-bold text-[11px]">2</button>
              <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-600 font-bold text-[11px]">3</button>
              <button class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 text-[11px]">…</button>
              <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-600 font-bold text-[11px]">6</button>
              <button class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
            </div>
          </div>
        </div>

      </div>

      <!-- Right Sidebar -->
      <div class="w-full xl:w-[280px] shrink-0 space-y-4">
        
        <!-- Course Overview -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
          <div class="flex items-center gap-2 mb-4">
            <div class="w-6 h-6 rounded-md bg-indigo-50 text-[#5138ed] flex items-center justify-center"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div>
            <h3 class="text-[13px] font-bold text-slate-800">Course Overview</h3>
          </div>
          
          <div class="space-y-3">
            <div class="flex flex-col"><span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Course Name</span><span class="text-[12px] font-bold text-slate-800">Database Systems</span></div>
            <div class="flex flex-col"><span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Course Code</span><span class="text-[12px] font-bold text-slate-800">CS-304</span></div>
            <div class="flex flex-col"><span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Section</span><span class="text-[12px] font-bold text-slate-800">CS-304-A</span></div>
            <div class="flex flex-col"><span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Semester</span><span class="text-[12px] font-bold text-slate-800">Semester I</span></div>
            <div class="flex flex-col"><span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Academic Year</span><span class="text-[12px] font-bold text-slate-800">2025/2026</span></div>
            <div class="flex flex-col"><span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Instructor</span><span class="text-[12px] font-bold text-slate-800">Dr. Abebe Kebede</span></div>
          </div>
        </div>

        <!-- Student Progress Summary -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
          <div class="flex items-center gap-2 mb-5">
            <div class="w-6 h-6 rounded-md bg-indigo-50 text-[#5138ed] flex items-center justify-center"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></div>
            <h3 class="text-[13px] font-bold text-slate-800">Student Progress</h3>
          </div>
          
          <div class="space-y-4">
            <div class="flex flex-col gap-1">
              <div class="flex justify-between items-center"><span class="text-[10px] font-bold text-slate-600">Completed Exams</span><span class="text-[10px] font-bold text-slate-800">58.3%</span></div>
              <div class="h-1.5 w-full bg-slate-100 rounded-full"><div class="h-full bg-emerald-500 rounded-full" style="width: 58.3%"></div></div>
            </div>
            <div class="flex flex-col gap-1">
              <div class="flex justify-between items-center"><span class="text-[10px] font-bold text-slate-600">Pending Exams</span><span class="text-[10px] font-bold text-slate-800">41.7%</span></div>
              <div class="h-1.5 w-full bg-slate-100 rounded-full"><div class="h-full bg-orange-400 rounded-full" style="width: 41.7%"></div></div>
            </div>
            <div class="flex flex-col gap-1">
              <div class="flex justify-between items-center"><span class="text-[10px] font-bold text-slate-600">Average Attendance</span><span class="text-[10px] font-bold text-slate-800">78.4%</span></div>
              <div class="h-1.5 w-full bg-slate-100 rounded-full"><div class="h-full bg-blue-500 rounded-full" style="width: 78.4%"></div></div>
            </div>
            <div class="flex flex-col gap-1">
              <div class="flex justify-between items-center"><span class="text-[10px] font-bold text-slate-600">Average Exam Score</span><span class="text-[10px] font-bold text-slate-800">72.6%</span></div>
              <div class="h-1.5 w-full bg-slate-100 rounded-full"><div class="h-full bg-[#5138ed] rounded-full" style="width: 72.6%"></div></div>
            </div>
            <div class="flex flex-col gap-1">
              <div class="flex justify-between items-center"><span class="text-[10px] font-bold text-slate-600">Students At Risk</span><span class="text-[10px] font-bold text-slate-800">12.5%</span></div>
              <div class="h-1.5 w-full bg-slate-100 rounded-full"><div class="h-full bg-rose-500 rounded-full" style="width: 12.5%"></div></div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
          <div class="flex items-center gap-2 mb-4">
            <div class="w-6 h-6 rounded-md bg-indigo-50 text-[#5138ed] flex items-center justify-center"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg></div>
            <h3 class="text-[13px] font-bold text-slate-800">Quick Actions</h3>
          </div>
          <div class="space-y-2">
            <button class="w-full flex items-center gap-2.5 p-2 rounded-lg hover:bg-slate-50 transition-colors group text-left">
              <div class="text-[#5138ed]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg></div>
              <span class="text-[11px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors">Import Students</span>
            </button>
            <div class="h-px w-full bg-slate-50"></div>
            <button class="w-full flex items-center gap-2.5 p-2 rounded-lg hover:bg-slate-50 transition-colors group text-left">
              <div class="text-[#5138ed]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg></div>
              <span class="text-[11px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors">Export Student List</span>
            </button>
            <div class="h-px w-full bg-slate-50"></div>
            <button class="w-full flex items-center gap-2.5 p-2 rounded-lg hover:bg-slate-50 transition-colors group text-left">
              <div class="text-[#5138ed]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg></div>
              <span class="text-[11px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors">Print Attendance</span>
            </button>
            <div class="h-px w-full bg-slate-50"></div>
            <button class="w-full flex items-center gap-2.5 p-2 rounded-lg hover:bg-slate-50 transition-colors group text-left">
              <div class="text-[#5138ed]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div>
              <span class="text-[11px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors">Generate Student Report</span>
            </button>
            <div class="h-px w-full bg-slate-50"></div>
            <button class="w-full flex items-center gap-2.5 p-2 rounded-lg hover:bg-slate-50 transition-colors group text-left">
              <div class="text-[#5138ed]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg></div>
              <span class="text-[11px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors">Send Announcement</span>
            </button>
          </div>
        </div>

      </div>

    </div>
  </div>
</template>
