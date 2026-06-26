<script setup lang="ts">
import { ref } from 'vue'

const searchQuery = ref('')
const selectedCourse = ref('All Courses')
const selectedStatus = ref('All Status')
const selectedSemester = ref('All Semesters')

const exams = [
  { id: 1, title: 'Database Systems Mid Exam', subtitle: 'Mid Term Examination', course: 'Database Systems', code: 'CS 304', type: 'Mid Exam', date: 'May 25, 2025', time: '10:00 AM', duration: '90 min', status: 'Upcoming' },
  { id: 2, title: 'Web Programming Quiz 1', subtitle: 'First Quiz', course: 'Web Programming', code: 'CS 206', type: 'Quiz', date: 'May 20, 2025', time: '09:00 AM', duration: '45 min', status: 'Published' },
  { id: 3, title: 'Data Structures Final Exam', subtitle: 'Final Examination', course: 'Data Structures', code: 'CS 202', type: 'Final Exam', date: 'May 18, 2025', time: '02:00 PM', duration: '120 min', status: 'Completed' },
  { id: 4, title: 'Operating Systems Mid Exam', subtitle: 'Mid Term Examination', course: 'Operating Systems', code: 'CS 305', type: 'Mid Exam', date: 'May 30, 2025', time: '11:00 AM', duration: '90 min', status: 'Upcoming' },
  { id: 5, title: 'Software Engineering Quiz 1', subtitle: 'First Quiz', course: 'Software Engineering', code: 'SE 302', type: 'Quiz', date: 'Jun 02, 2025', time: '09:00 AM', duration: '30 min', status: 'Upcoming' },
  { id: 6, title: 'Theory of Computation Final', subtitle: 'Final Examination', course: 'Theory of Computation', code: 'CS 404', type: 'Final Exam', date: 'Jun 10, 2025', time: '01:00 PM', duration: '120 min', status: 'Draft' },
  { id: 7, title: 'Computer Networks Mid Exam', subtitle: 'Mid Term Examination', course: 'Computer Networks', code: 'CS 307', type: 'Mid Exam', date: 'Jun 15, 2025', time: '10:00 AM', duration: '90 min', status: 'Draft' },
  { id: 8, title: 'Artificial Intelligence Quiz 1', subtitle: 'First Quiz', course: 'Artificial Intelligence', code: 'CS 405', type: 'Quiz', date: 'Jun 18, 2025', time: '09:00 AM', duration: '30 min', status: 'Draft' },
]

const getTypeColor = (type: string) => {
  switch(type) {
    case 'Mid Exam': return 'bg-indigo-50 text-indigo-600'
    case 'Quiz': return 'bg-emerald-50 text-emerald-600'
    case 'Final Exam': return 'bg-orange-50 text-orange-600'
    default: return 'bg-slate-50 text-slate-600'
  }
}

const getStatusColor = (status: string) => {
  switch(status) {
    case 'Upcoming': return 'bg-indigo-50 text-[#5138ed]'
    case 'Published': return 'bg-emerald-50 text-emerald-600'
    case 'Completed': return 'bg-slate-100 text-slate-600'
    case 'Draft': return 'bg-white text-slate-500'
    default: return 'bg-slate-50 text-slate-600'
  }
}
</script>

<template>
  <div class="p-6">
    
    <!-- Filter Bar -->
    <div class="flex flex-col xl:flex-row gap-4 mb-6">
      <div class="relative flex-1">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <input 
          v-model="searchQuery"
          type="text" 
          class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors"
          placeholder="Search exams..."
        >
      </div>
      
      <div class="flex gap-3 overflow-x-auto pb-2 xl:pb-0">
        <select v-model="selectedCourse" class="border border-slate-200 rounded-xl text-sm px-3 py-2 text-slate-600 focus:outline-none focus:border-[#5138ed]">
          <option>All Courses</option>
          <option>Database Systems</option>
          <option>Web Programming</option>
        </select>
        
        <select v-model="selectedStatus" class="border border-slate-200 rounded-xl text-sm px-3 py-2 text-slate-600 focus:outline-none focus:border-[#5138ed]">
          <option>All Status</option>
          <option>Upcoming</option>
          <option>Published</option>
          <option>Completed</option>
          <option>Draft</option>
        </select>
        
        <select v-model="selectedSemester" class="border border-slate-200 rounded-xl text-sm px-3 py-2 text-slate-600 focus:outline-none focus:border-[#5138ed]">
          <option>All Semesters</option>
          <option>Semester 1</option>
          <option>Semester 2</option>
        </select>
        
        <button class="flex flex-shrink-0 items-center gap-2 px-4 py-2 border border-indigo-100 text-[#5138ed] text-sm font-semibold rounded-xl hover:bg-indigo-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
          Filter
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse whitespace-nowrap">
        <thead>
          <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wide">
            <th class="pb-4 pr-4 font-semibold">Exam Title</th>
            <th class="pb-4 px-4 font-semibold">Course</th>
            <th class="pb-4 px-4 font-semibold text-center">Type</th>
            <th class="pb-4 px-4 font-semibold">Date & Time</th>
            <th class="pb-4 px-4 font-semibold text-center">Duration</th>
            <th class="pb-4 px-4 font-semibold text-center">Status</th>
            <th class="pb-4 pl-4 text-center font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="exam in exams" :key="exam.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors last:border-0 group">
            <td class="py-4 pr-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" :class="getTypeColor(exam.type).replace('text-', 'bg-').replace('50', '50/50') + ' text-' + getTypeColor(exam.type).split(' ')[1]">
                  <svg v-if="exam.type === 'Mid Exam'" class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                  <svg v-else-if="exam.type === 'Quiz'" class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                  <svg v-else class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div class="flex flex-col">
                  <span class="text-[13px] font-bold text-slate-700">{{ exam.title }}</span>
                  <span class="text-[11px] text-slate-400">{{ exam.subtitle }}</span>
                </div>
              </div>
            </td>
            <td class="py-4 px-4">
              <div class="flex flex-col">
                <span class="text-[13px] font-medium text-slate-600">{{ exam.course }}</span>
                <span class="text-[11px] text-slate-400">{{ exam.code }}</span>
              </div>
            </td>
            <td class="py-4 px-4 text-center">
              <span class="px-2.5 py-1 text-[10px] font-bold rounded-md" :class="getTypeColor(exam.type)">
                {{ exam.type }}
              </span>
            </td>
            <td class="py-4 px-4">
              <div class="flex flex-col">
                <span class="text-[13px] font-medium text-slate-600">{{ exam.date }}</span>
                <span class="text-[11px] text-slate-400">{{ exam.time }}</span>
              </div>
            </td>
            <td class="py-4 px-4 text-[13px] text-slate-600 font-medium text-center">{{ exam.duration }}</td>
            <td class="py-4 px-4 text-center">
              <span class="px-2.5 py-1 text-[10px] font-bold rounded-md" :class="getStatusColor(exam.status)">
                {{ exam.status }}
              </span>
            </td>
            <td class="py-4 pl-4 text-center">
              <div class="flex items-center justify-center gap-1">
                <button class="p-1.5 text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 rounded-lg transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>
                <button class="p-1.5 text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 rounded-lg transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
                <button class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-100">
      <span class="text-[13px] text-slate-500 font-medium">Showing 1 to 8 of 18 exams</span>
      <div class="flex items-center gap-1">
        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#5138ed] text-white font-bold text-sm shadow-sm shadow-indigo-200">1</button>
        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm transition-colors">2</button>
        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm transition-colors">3</button>
        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
      </div>
    </div>
  </div>
</template>
