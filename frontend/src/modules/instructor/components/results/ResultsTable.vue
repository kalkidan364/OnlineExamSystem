<script setup lang="ts">
const results = [
  {
    id: 1,
    exam: 'Database Systems Mid Exam',
    course: 'CS 304',
    student: 'Michael Tadesse',
    studentId: 'ID: STU2021001',
    score: '48 / 60',
    percentage: '80.0%',
    grade: 'A',
    status: 'Passed',
    completedOn: 'May 25, 2025',
    completedTime: '10:30 AM',
    iconClass: 'text-indigo-500',
    bgClass: 'bg-indigo-50 border-indigo-100'
  },
  {
    id: 2,
    exam: 'Web Programming Quiz 1',
    course: 'CS 206',
    student: 'Sara Lemma',
    studentId: 'ID: STU2021002',
    score: '38 / 45',
    percentage: '84.4%',
    grade: 'A',
    status: 'Passed',
    completedOn: 'May 20, 2025',
    completedTime: '09:45 AM',
    iconClass: 'text-emerald-500',
    bgClass: 'bg-emerald-50 border-emerald-100'
  },
  {
    id: 3,
    exam: 'Data Structures Final Exam',
    course: 'CS 202',
    student: 'Abebe Tesfaye',
    studentId: 'ID: STU2021003',
    score: '72 / 100',
    percentage: '72.0%',
    grade: 'B',
    status: 'Passed',
    completedOn: 'May 18, 2025',
    completedTime: '02:30 PM',
    iconClass: 'text-orange-500',
    bgClass: 'bg-orange-50 border-orange-100'
  },
  {
    id: 4,
    exam: 'Operating Systems Mid Exam',
    course: 'CS 305',
    student: 'Hana Gebre',
    studentId: 'ID: STU2021004',
    score: '41 / 60',
    percentage: '68.3%',
    grade: 'C',
    status: 'Passed',
    completedOn: 'May 12, 2025',
    completedTime: '11:15 AM',
    iconClass: 'text-indigo-500',
    bgClass: 'bg-indigo-50 border-indigo-100'
  },
  {
    id: 5,
    exam: 'Software Engineering Quiz 1',
    course: 'SE 302',
    student: 'Daniel Kibret',
    studentId: 'ID: STU2021005',
    score: '22 / 30',
    percentage: '73.3%',
    grade: 'B',
    status: 'Passed',
    completedOn: 'May 10, 2025',
    completedTime: '09:10 AM',
    iconClass: 'text-emerald-500',
    bgClass: 'bg-emerald-50 border-emerald-100'
  },
  {
    id: 6,
    exam: 'Computer Networks Mid Exam',
    course: 'CS 307',
    student: 'Liya Mekonnen',
    studentId: 'ID: STU2021006',
    score: '31 / 60',
    percentage: '51.7%',
    grade: 'D',
    status: 'Failed',
    completedOn: 'May 08, 2025',
    completedTime: '10:05 AM',
    iconClass: 'text-orange-500',
    bgClass: 'bg-orange-50 border-orange-100'
  },
  {
    id: 7,
    exam: 'Artificial Intelligence Quiz 1',
    course: 'CS 405',
    student: 'Robel Haile',
    studentId: 'ID: STU2021007',
    score: '25 / 30',
    percentage: '83.3%',
    grade: 'A',
    status: 'Passed',
    completedOn: 'May 05, 2025',
    completedTime: '09:35 AM',
    iconClass: 'text-indigo-500',
    bgClass: 'bg-indigo-50 border-indigo-100'
  },
  {
    id: 8,
    exam: 'Theory of Computation Final',
    course: 'CS 404',
    student: 'Yared Bekele',
    studentId: 'ID: STU2021008',
    score: '68 / 100',
    percentage: '68.0%',
    grade: 'C',
    status: 'Passed',
    completedOn: 'May 02, 2025',
    completedTime: '01:20 PM',
    iconClass: 'text-rose-500',
    bgClass: 'bg-rose-50 border-rose-100'
  }
]

const getGradeClass = (grade: string) => {
  switch (grade) {
    case 'A': return 'bg-emerald-50 text-emerald-600'
    case 'B': return 'bg-blue-50 text-blue-600'
    case 'C': return 'bg-yellow-50 text-yellow-600'
    case 'D': return 'bg-orange-50 text-orange-600'
    case 'F': return 'bg-rose-50 text-rose-600'
    default: return 'bg-slate-50 text-slate-600'
  }
}
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl shadow-sm mb-6 overflow-hidden">
    
    <!-- Filter Bar -->
    <div class="p-4 border-b border-slate-100 flex flex-wrap items-center gap-3">
      
      <!-- Search -->
      <div class="relative flex-1 min-w-[180px]">
        <svg class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        <input type="text" placeholder="Search exams or students..." class="w-full pl-10 pr-4 py-2 text-[12px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] text-slate-700 placeholder:text-slate-400">
      </div>

      <!-- Filters -->
      <div class="relative min-w-[120px]">
        <select class="w-full appearance-none pl-3 pr-8 py-2 text-[11px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] text-slate-700 bg-white cursor-pointer font-medium">
          <option>All Courses</option>
        </select>
        <svg class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
      </div>

      <div class="relative min-w-[120px]">
        <select class="w-full appearance-none pl-3 pr-8 py-2 text-[11px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] text-slate-700 bg-white cursor-pointer font-medium">
          <option>All Exam Types</option>
        </select>
        <svg class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
      </div>

      <div class="relative min-w-[110px]">
        <select class="w-full appearance-none pl-3 pr-8 py-2 text-[11px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] text-slate-700 bg-white cursor-pointer font-medium">
          <option>All Status</option>
        </select>
        <svg class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
      </div>

      <div class="relative min-w-[200px]">
        <div class="w-full pl-3 pr-8 py-2 text-[11px] border border-slate-200 rounded-xl text-slate-700 bg-white cursor-pointer font-medium flex items-center justify-between">
          <span>May 01, 2025 - May 25, 2025</span>
          <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
      </div>

      <button class="px-4 py-2 border border-[#5138ed] text-[#5138ed] font-bold text-[11px] rounded-xl hover:bg-indigo-50 transition-colors flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
        Filter
      </button>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-white border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
            <th class="px-5 py-3">Exam & Course</th>
            <th class="px-4 py-3">Student</th>
            <th class="px-4 py-3 text-center">Score</th>
            <th class="px-4 py-3 text-center">Percentage</th>
            <th class="px-4 py-3 text-center">Grade</th>
            <th class="px-4 py-3 text-center">Status</th>
            <th class="px-4 py-3">Completed On</th>
            <th class="px-5 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="res in results" :key="res.id" class="hover:bg-slate-50/50 transition-colors group">
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-3">
                <div :class="`w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 border ${res.bgClass}`">
                  <svg :class="`w-4 h-4 ${res.iconClass}`" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                  <span class="block text-[11px] font-bold text-slate-800">{{ res.exam }}</span>
                  <span class="block text-[10px] font-medium text-slate-500 mt-0.5">{{ res.course }}</span>
                </div>
              </div>
            </td>
            <td class="px-4 py-3.5">
              <span class="block text-[11px] font-bold text-slate-700">{{ res.student }}</span>
              <span class="block text-[10px] font-medium text-slate-500 mt-0.5">{{ res.studentId }}</span>
            </td>
            <td class="px-4 py-3.5 text-center">
              <span class="text-[11px] font-semibold text-slate-700">{{ res.score }}</span>
            </td>
            <td class="px-4 py-3.5 text-center">
              <span class="text-[11px] font-bold text-slate-800">{{ res.percentage }}</span>
            </td>
            <td class="px-4 py-3.5 text-center">
              <span :class="`inline-flex items-center justify-center w-6 h-6 rounded text-[10px] font-bold ${getGradeClass(res.grade)}`">
                {{ res.grade }}
              </span>
            </td>
            <td class="px-4 py-3.5 text-center">
              <span :class="`inline-block px-2.5 py-1 text-[9px] font-bold rounded-md ${res.status === 'Passed' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'}`">
                {{ res.status }}
              </span>
            </td>
            <td class="px-4 py-3.5">
              <span class="block text-[11px] font-medium text-slate-700">{{ res.completedOn }}</span>
              <span class="block text-[10px] font-medium text-slate-500 mt-0.5">{{ res.completedTime }}</span>
            </td>
            <td class="px-5 py-3.5">
              <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button class="p-1.5 text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 rounded-lg transition-colors" title="View details">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>
                <button class="p-1.5 text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 rounded-lg transition-colors" title="Performance">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="p-4 border-t border-slate-100 flex items-center justify-between">
      <span class="text-[11px] font-medium text-slate-500 pl-2">Showing 1 to 8 of 256 results</span>
      <div class="flex items-center gap-1 pr-2">
        <button class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors disabled:opacity-50" disabled>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button class="w-7 h-7 flex items-center justify-center rounded-lg bg-[#5138ed] text-white font-bold text-[11px] shadow-sm">1</button>
        <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-600 font-bold text-[11px] transition-colors">2</button>
        <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-600 font-bold text-[11px] transition-colors">3</button>
        <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-600 font-bold text-[11px] transition-colors">...</button>
        <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-600 font-bold text-[11px] transition-colors">32</button>
        <button class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
      </div>
    </div>

  </div>
</template>
