<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useInstructorResultStore } from '../store/instructorResultStore'

const route = useRoute()
const router = useRouter()
const resultStore = useInstructorResultStore()

const examId = Number(route.params.examId)
const exam = computed(() => resultStore.results.find(e => e.id === examId))

// Filter & Tab state
const searchQuery = ref('')
const selectedStatus = ref('All Status')
const selectedGrade = ref('All Grades')
const selectedSection = ref('All Sections')
const activeTab = ref('all')

// Mock student data
const students = ref([
  { id: 1, studentId: 'STU2021001', name: 'Michael Tadesse', submittedOn: '2025-05-25T10:29:00', autoScore: 32, autoTotal: 40, manualScore: 15, manualTotal: 20, finalScore: 47, totalMarks: 60, grade: 'B+', status: 'Graded' },
  { id: 2, studentId: 'STU2021002', name: 'Sara Lemma', submittedOn: '2025-05-25T10:28:00', autoScore: 28, autoTotal: 40, manualScore: 10, manualTotal: 20, finalScore: 38, totalMarks: 60, grade: 'C+', status: 'Pending' },
  { id: 3, studentId: 'STU2021003', name: 'Abebe Tesfaye', submittedOn: '2025-05-25T10:27:00', autoScore: 35, autoTotal: 40, manualScore: 18, manualTotal: 20, finalScore: 53, totalMarks: 60, grade: 'A', status: 'Graded' },
  { id: 4, studentId: 'STU2021004', name: 'Hana Gebre', submittedOn: '2025-05-25T10:26:00', autoScore: 30, autoTotal: 40, manualScore: 12, manualTotal: 20, finalScore: 42, totalMarks: 60, grade: 'B', status: 'Pending' },
  { id: 5, studentId: 'STU2021005', name: 'Daniel Kibret', submittedOn: '2025-05-25T10:25:00', autoScore: 36, autoTotal: 40, manualScore: 20, manualTotal: 20, finalScore: 56, totalMarks: 60, grade: 'A+', status: 'Graded' },
  { id: 6, studentId: 'STU2021006', name: 'Liya Mekonnen', submittedOn: '2025-05-25T10:24:00', autoScore: 24, autoTotal: 40, manualScore: 8, manualTotal: 20, finalScore: 32, totalMarks: 60, grade: 'C', status: 'Pending' },
  { id: 7, studentId: 'STU2021007', name: 'Robel Hailu', submittedOn: '2025-05-25T10:23:00', autoScore: 33, autoTotal: 40, manualScore: 14, manualTotal: 20, finalScore: 47, totalMarks: 60, grade: 'B+', status: 'Graded' },
  { id: 8, studentId: 'STU2021008', name: 'Yared Bekele', submittedOn: '2025-05-25T10:22:00', autoScore: 26, autoTotal: 40, manualScore: 6, manualTotal: 20, finalScore: 32, totalMarks: 60, grade: 'C', status: 'Pending' },
  { id: 9, studentId: 'STU2021009', name: 'Mekdes Alemu', submittedOn: '2025-05-25T10:22:00', autoScore: 38, autoTotal: 40, manualScore: 20, manualTotal: 20, finalScore: 58, totalMarks: 60, grade: 'A+', status: 'Graded' },
  { id: 10, studentId: 'STU2021010', name: 'Natnael Araya', submittedOn: '2025-05-25T10:21:00', autoScore: 0, autoTotal: 40, manualScore: 0, manualTotal: 20, finalScore: 0, totalMarks: 60, grade: 'F', status: 'Absent' },
])

const tabs = computed(() => [
  { key: 'all', label: 'All Students', count: 125 },
  { key: 'submitted', label: 'Submitted', count: 122 },
  { key: 'graded', label: 'Graded', count: 90 },
  { key: 'pending', label: 'Pending Grading', count: 32 },
  { key: 'absent', label: 'Absent', count: 3 },
])

const getStatusClass = (status: string) => {
  if (status === 'Graded') return 'text-emerald-600'
  if (status === 'Pending') return 'text-orange-500'
  if (status === 'Absent') return 'text-rose-500'
  return 'text-slate-500'
}

const getGradeClass = (grade: string) => {
  if (grade.startsWith('A')) return 'text-emerald-600 font-bold'
  if (grade.startsWith('B')) return 'text-blue-600 font-bold'
  if (grade.startsWith('C')) return 'text-orange-500 font-bold'
  if (grade.startsWith('D')) return 'text-orange-600 font-bold'
  if (grade === 'F') return 'text-rose-500 font-bold'
  return 'text-slate-600 font-bold'
}
</script>

<template>
  <div class="max-w-[1500px] mx-auto">

    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Exam Results - Students</h1>
        <p class="text-[13px] text-slate-500 mt-1">View and manage student results for this exam.</p>
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-[12px] text-slate-400 mt-2">
          <router-link to="/instructor/results" class="hover:text-[#5138ed] transition-colors">Results Dashboard</router-link>
          <span>&gt;</span>
          <span class="text-slate-600 font-medium">{{ exam?.title || 'Exam' }}</span>
          <span>&gt;</span>
          <span class="text-slate-600 font-medium">Student Results</span>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <button @click="router.push('/instructor/results')" class="flex items-center gap-2 px-4 py-2 border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          Back to Results Dashboard
        </button>
        <button class="flex items-center gap-2 px-4 py-2 bg-[#5138ed] text-white text-sm font-semibold rounded-xl hover:bg-[#4530d1] transition-colors shadow-sm shadow-indigo-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
          Export Results
        </button>
      </div>
    </div>

    <!-- Exam Info Card -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm mb-6">
      <div class="flex items-start gap-5 mb-5">
        <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-[#5138ed] shrink-0">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <div>
          <div class="flex items-center gap-3 mb-1">
            <h2 class="text-xl font-extrabold text-slate-800">{{ exam?.title || 'Database Systems Mid Examination' }}</h2>
            <span class="px-2.5 py-0.5 text-[10px] font-bold bg-indigo-50 text-[#5138ed] rounded-md">{{ exam?.type || 'Mid Exam' }}</span>
          </div>
        </div>
      </div>

      <!-- Exam metadata row -->
      <div class="flex flex-wrap items-center gap-8 text-[12px]">
        <div class="flex flex-col"><span class="text-slate-400 font-bold text-[10px] uppercase mb-0.5">Course</span><span class="text-slate-700 font-bold">Database Systems (CS 304)</span></div>
        <div class="flex flex-col"><span class="text-slate-400 font-bold text-[10px] uppercase mb-0.5">Exam Type</span><span class="text-slate-700 font-bold">Mid Examination</span></div>
        <div class="flex flex-col"><span class="text-slate-400 font-bold text-[10px] uppercase mb-0.5">Date</span><span class="text-slate-700 font-bold">May 25, 2025 — 10:30 AM</span></div>
        <div class="flex flex-col"><span class="text-slate-400 font-bold text-[10px] uppercase mb-0.5">Duration</span><span class="text-slate-700 font-bold">90 min</span></div>
        <div class="flex flex-col"><span class="text-slate-400 font-bold text-[10px] uppercase mb-0.5">Total Marks</span><span class="text-slate-700 font-bold">60</span></div>
        <div class="flex flex-col"><span class="text-slate-400 font-bold text-[10px] uppercase mb-0.5">Students</span><span class="text-slate-700 font-bold">125</span></div>
        <div class="flex flex-col"><span class="text-slate-400 font-bold text-[10px] uppercase mb-0.5">Submitted</span><span class="text-emerald-600 font-bold">122 (97.6%)</span></div>
        <div class="flex flex-col"><span class="text-slate-400 font-bold text-[10px] uppercase mb-0.5">Published</span><span class="text-rose-500 font-bold flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg> Not Published</span></div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="flex flex-col xl:flex-row gap-6">

      <!-- Left Column -->
      <div class="flex-1 min-w-0">

        <!-- Search & Filters -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm mb-4 p-4">
          <div class="flex items-center gap-3 flex-nowrap overflow-x-auto">
            <div class="relative min-w-[200px] flex-1">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
              </div>
              <input type="text" v-model="searchQuery" class="block w-full pl-9 pr-3 py-2 border border-slate-200 rounded-xl leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-[#5138ed]/20 focus:border-[#5138ed] text-sm transition-colors" placeholder="Search students by name or ID...">
            </div>
            <div class="relative shrink-0">
              <select v-model="selectedStatus" class="appearance-none border border-slate-200 rounded-xl text-sm pl-3 pr-8 py-2 text-slate-600 focus:outline-none focus:border-[#5138ed] bg-white w-[120px]">
                <option>All Status</option><option>Graded</option><option>Pending</option><option>Absent</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
            </div>
            <div class="relative shrink-0">
              <select v-model="selectedGrade" class="appearance-none border border-slate-200 rounded-xl text-sm pl-3 pr-8 py-2 text-slate-600 focus:outline-none focus:border-[#5138ed] bg-white w-[120px]">
                <option>All Grades</option><option>A</option><option>B</option><option>C</option><option>D</option><option>F</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
            </div>
            <div class="relative shrink-0">
              <select v-model="selectedSection" class="appearance-none border border-slate-200 rounded-xl text-sm pl-3 pr-8 py-2 text-slate-600 focus:outline-none focus:border-[#5138ed] bg-white w-[130px]">
                <option>All Sections</option><option>Section A</option><option>Section B</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></div>
            </div>
            <button class="flex items-center gap-1.5 px-4 py-2 border border-slate-200 text-[#5138ed] text-sm font-semibold rounded-xl hover:bg-indigo-50 transition-colors shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
              Filter
            </button>
            <button class="flex items-center gap-1.5 px-4 py-2 border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
              Reset
            </button>
          </div>
        </div>

        <!-- Tabs -->
        <div class="flex items-center gap-1 mb-4 overflow-x-auto pb-1">
          <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key"
            class="px-4 py-2 text-[12px] font-bold rounded-xl whitespace-nowrap transition-colors"
            :class="activeTab === tab.key ? 'bg-[#5138ed] text-white shadow-sm shadow-indigo-200' : 'text-slate-600 hover:bg-slate-100'">
            {{ tab.label }} ({{ tab.count }})
          </button>
        </div>

        <!-- Students Table -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
              <thead>
                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                  <th class="py-3 pl-4 pr-2 font-semibold">#</th>
                  <th class="py-3 px-3 font-semibold">Student ID</th>
                  <th class="py-3 px-3 font-semibold">Student Name</th>
                  <th class="py-3 px-3 font-semibold">Submitted On</th>
                  <th class="py-3 px-3 font-semibold text-center">Auto Score<br><span class="text-[8px] text-slate-300 normal-case">(MCQ)</span></th>
                  <th class="py-3 px-3 font-semibold text-center">Manual Score<br><span class="text-[8px] text-slate-300 normal-case">(Subjective)</span></th>
                  <th class="py-3 px-3 font-semibold text-center">Final Score<br><span class="text-[8px] text-slate-300 normal-case">(Out of 60)</span></th>
                  <th class="py-3 px-3 font-semibold text-center">Status</th>
                  <th class="py-3 px-3 font-semibold text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(student, index) in students" :key="student.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors last:border-0">
                  <td class="py-3 pl-4 pr-2 text-[12px] font-bold text-slate-500">{{ index + 1 }}</td>
                  <td class="py-3 px-3 text-[12px] font-medium text-slate-600">{{ student.studentId }}</td>
                  <td class="py-3 px-3 text-[12px] font-bold text-slate-800">{{ student.name }}</td>
                  <td class="py-3 px-3">
                    <div class="flex flex-col">
                      <span class="text-[12px] font-medium text-slate-700">{{ new Date(student.submittedOn).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) }}</span>
                      <span class="text-[10px] text-slate-400">{{ new Date(student.submittedOn).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }}</span>
                    </div>
                  </td>
                  <td class="py-3 px-3 text-[12px] font-bold text-slate-700 text-center">{{ student.autoScore }} / {{ student.autoTotal }}</td>
                  <td class="py-3 px-3 text-[12px] font-bold text-slate-700 text-center">{{ student.manualScore }} / {{ student.manualTotal }}</td>
                  <td class="py-3 px-3 text-[12px] font-bold text-slate-800 text-center">{{ student.finalScore }} / {{ student.totalMarks }}</td>
                  <td class="py-3 px-3 text-center"><span class="text-[11px] font-bold" :class="getStatusClass(student.status)">{{ student.status }}</span></td>
                  <td class="py-3 px-3 text-center">
                    <div class="flex items-center justify-center gap-1 text-slate-400">
                      <button @click="router.push({ name: 'StudentResultDetail', params: { examId: route.params.examId, studentId: student.id } })" class="p-1 hover:text-[#5138ed] hover:bg-indigo-50 rounded-lg transition-colors" title="View"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                      <button class="p-1 hover:text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Grade"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="p-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[12px] text-slate-500 font-medium">Showing 1 to 10 of 125 students</span>
            <div class="flex items-center gap-1">
              <button class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 disabled:opacity-50" disabled><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
              <button class="w-7 h-7 flex items-center justify-center rounded-lg bg-[#5138ed] text-white font-bold text-[11px] shadow-sm">1</button>
              <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-600 font-bold text-[11px]">2</button>
              <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-600 font-bold text-[11px]">3</button>
              <button class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 text-[11px]">…</button>
              <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-600 font-bold text-[11px]">13</button>
              <button class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Sidebar -->
      <div class="w-full xl:w-[260px] space-y-4">

        <!-- Exam Summary -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
          <h3 class="text-[12px] font-bold text-slate-800 mb-3">Exam Summary</h3>
          <div class="space-y-2.5">
            <div class="flex items-center justify-between"><span class="text-[11px] text-slate-500 font-medium">Total Students</span><span class="text-[12px] font-bold text-slate-800">125</span></div>
            <div class="flex items-center justify-between"><span class="text-[11px] text-slate-500 font-medium">Submitted</span><span class="text-[12px] font-bold text-[#5138ed]">122 (97.6%)</span></div>
            <div class="flex items-center justify-between"><span class="text-[11px] text-slate-500 font-medium">Graded</span><span class="text-[12px] font-bold text-[#5138ed]">90 (72.6%)</span></div>
            <div class="flex items-center justify-between"><span class="text-[11px] text-slate-500 font-medium">Pending Grading</span><span class="text-[12px] font-bold text-[#5138ed]">32 (25.8%)</span></div>
            <div class="flex items-center justify-between"><span class="text-[11px] text-slate-500 font-medium">Absent</span><span class="text-[12px] font-bold text-[#5138ed]">3 (2.4%)</span></div>
          </div>
        </div>

        <!-- Score Distribution -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
          <h3 class="text-[12px] font-bold text-slate-800 mb-4">Score Distribution (Final Score)</h3>
          <div class="flex flex-col items-center mb-4">
            <div class="relative w-32 h-32 mb-2">
              <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="40" fill="none" stroke="#f1f5f9" stroke-width="12"></circle>
                <circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="12" stroke-dasharray="56.6 251.3" stroke-dashoffset="0"></circle>
                <circle cx="50" cy="50" r="40" fill="none" stroke="#3b82f6" stroke-width="12" stroke-dasharray="72.4 251.3" stroke-dashoffset="-56.6"></circle>
                <circle cx="50" cy="50" r="40" fill="none" stroke="#f59e0b" stroke-width="12" stroke-dasharray="62.3 251.3" stroke-dashoffset="-129"></circle>
                <circle cx="50" cy="50" r="40" fill="none" stroke="#fb923c" stroke-width="12" stroke-dasharray="36.2 251.3" stroke-dashoffset="-191.3"></circle>
                <circle cx="50" cy="50" r="40" fill="none" stroke="#ef4444" stroke-width="12" stroke-dasharray="24.1 251.3" stroke-dashoffset="-227.5"></circle>
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-[18px] font-black text-slate-800 leading-none">125</span>
                <span class="text-[8px] font-bold text-slate-400 mt-0.5">Students</span>
              </div>
            </div>
          </div>
          <div class="space-y-1.5">
            <div class="flex items-center justify-between"><div class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-emerald-500"></div><span class="text-[10px] text-slate-600">A (80-100%)</span></div><span class="text-[10px] font-bold text-slate-700">28 (22.4%)</span></div>
            <div class="flex items-center justify-between"><div class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-blue-500"></div><span class="text-[10px] text-slate-600">B (70-79%)</span></div><span class="text-[10px] font-bold text-slate-700">36 (28.8%)</span></div>
            <div class="flex items-center justify-between"><div class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-amber-500"></div><span class="text-[10px] text-slate-600">C (60-69%)</span></div><span class="text-[10px] font-bold text-slate-700">31 (24.8%)</span></div>
            <div class="flex items-center justify-between"><div class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-orange-400"></div><span class="text-[10px] text-slate-600">D (50-59%)</span></div><span class="text-[10px] font-bold text-slate-700">18 (14.4%)</span></div>
            <div class="flex items-center justify-between"><div class="flex items-center gap-1.5"><div class="w-2 h-2 rounded-full bg-rose-500"></div><span class="text-[10px] text-slate-600">F (0-49%)</span></div><span class="text-[10px] font-bold text-slate-700">12 (9.6%)</span></div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
          <h3 class="text-[12px] font-bold text-slate-800 mb-3">Quick Actions</h3>
          <div class="space-y-2">
            <button class="w-full flex items-center gap-2 group">
              <div class="w-6 h-6 rounded-md bg-indigo-50 text-[#5138ed] flex items-center justify-center shrink-0"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></div>
              <span class="text-[11px] font-bold text-[#5138ed] group-hover:underline">Continue Grading</span>
            </button>
            <button class="w-full flex items-center gap-2 group">
              <div class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
              <span class="text-[11px] font-bold text-[#5138ed] group-hover:underline">View Pending Students</span>
            </button>
            <button class="w-full flex items-center gap-2 group">
              <div class="w-6 h-6 rounded-md bg-orange-50 text-orange-500 flex items-center justify-center shrink-0"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg></div>
              <span class="text-[11px] font-bold text-[#5138ed] group-hover:underline">Publish Results</span>
            </button>
            <button class="w-full flex items-center gap-2 group">
              <div class="w-6 h-6 rounded-md bg-blue-50 text-blue-500 flex items-center justify-center shrink-0"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg></div>
              <span class="text-[11px] font-bold text-[#5138ed] group-hover:underline">Download Results (Excel)</span>
            </button>
            <button class="w-full flex items-center gap-2 group">
              <div class="w-6 h-6 rounded-md bg-rose-50 text-rose-500 flex items-center justify-center shrink-0"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg></div>
              <span class="text-[11px] font-bold text-[#5138ed] group-hover:underline">Print Result Summary</span>
            </button>
          </div>
        </div>

        <!-- Info Banner -->
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex items-start gap-3">
          <div class="w-7 h-7 rounded-lg bg-white flex items-center justify-center text-blue-500 shrink-0 shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <p class="text-[10px] text-blue-700 font-medium leading-relaxed">Results will be visible to students only after publishing.</p>
        </div>

      </div>
    </div>

  </div>
</template>
