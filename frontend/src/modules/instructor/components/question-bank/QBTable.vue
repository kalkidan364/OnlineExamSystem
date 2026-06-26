<script setup lang="ts">
import { ref } from 'vue'

const searchQuery = ref('')
const selectedCourse = ref('All Courses')
const selectedType = ref('All Types')
const selectedStatus = ref('All Statuses')

const banks = [
  { id: 1, title: 'Database Systems Questions', subtitle: 'All database related questions', course: 'Database Systems', total: 78, types: { mcq: 60, sa: 12, essay: 6 }, date: 'May 20, 2025', status: 'Active' },
  { id: 2, title: 'Web Programming Questions', subtitle: 'HTML, CSS, JavaScript, PHP', course: 'Web Programming', total: 95, types: { mcq: 72, sa: 15, essay: 8 }, date: 'May 18, 2025', status: 'Active' },
  { id: 3, title: 'Data Structures Questions', subtitle: 'All data Structures topics', course: 'Data Structures', total: 92, types: { mcq: 70, sa: 14, essay: 8 }, date: 'May 15, 2025', status: 'Active' },
  { id: 4, title: 'Operating Systems Questions', subtitle: 'OS concepts and principles', course: 'Operating Systems', total: 65, types: { mcq: 48, sa: 10, essay: 7 }, date: 'May 12, 2025', status: 'Active' },
  { id: 5, title: 'Software Engineering Questions', subtitle: 'SE concepts and practices', course: 'Software Engineering', total: 88, types: { mcq: 66, sa: 13, essay: 9 }, date: 'May 10, 2025', status: 'Active' },
  { id: 6, title: 'Computer Networks Questions', subtitle: 'Networking fundamentals', course: 'Computer Networks', total: 74, types: { mcq: 56, sa: 11, essay: 7 }, date: 'May 08, 2025', status: 'Inactive' },
  { id: 7, title: 'Theory of Computation', subtitle: 'Automata, Regex, CFL, etc.', course: 'Theory of Computation', total: 58, types: { mcq: 42, sa: 9, essay: 7 }, date: 'May 05, 2025', status: 'Inactive' },
  { id: 8, title: 'AI Fundamentals Questions', subtitle: 'Artificial Intelligence basics', course: 'Artificial Intelligence', total: 59, types: { mcq: 45, sa: 8, essay: 6 }, date: 'May 01, 2025', status: 'Active' },
]
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    
    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 mb-6">
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
          placeholder="Search question banks..."
        >
      </div>
      
      <div class="flex gap-3">
        <select v-model="selectedCourse" class="border border-slate-200 rounded-xl text-sm px-3 py-2 text-slate-600 focus:outline-none focus:border-[#5138ed]">
          <option>All Courses</option>
          <option>Database Systems</option>
          <option>Web Programming</option>
        </select>
        
        <select v-model="selectedType" class="border border-slate-200 rounded-xl text-sm px-3 py-2 text-slate-600 focus:outline-none focus:border-[#5138ed]">
          <option>All Types</option>
          <option>MCQ</option>
          <option>Short Answer</option>
        </select>
        
        <select v-model="selectedStatus" class="border border-slate-200 rounded-xl text-sm px-3 py-2 text-slate-600 focus:outline-none focus:border-[#5138ed]">
          <option>All Statuses</option>
          <option>Active</option>
          <option>Inactive</option>
        </select>
        
        <button class="flex items-center gap-2 px-4 py-2 border border-indigo-100 text-[#5138ed] text-sm font-semibold rounded-xl hover:bg-indigo-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
          Filter
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse whitespace-nowrap">
        <thead>
          <tr class="border-b border-slate-100 text-[12px] font-bold text-slate-400 uppercase tracking-wide">
            <th class="pb-4 pr-4 font-semibold">Question Bank</th>
            <th class="pb-4 px-4 font-semibold">Course</th>
            <th class="pb-4 px-4 text-center font-semibold">Questions</th>
            <th class="pb-4 px-4 font-semibold">Type Distribution</th>
            <th class="pb-4 px-4 font-semibold">Last Updated</th>
            <th class="pb-4 px-4 text-center font-semibold">Status</th>
            <th class="pb-4 pl-4 text-center font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="bank in banks" :key="bank.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors last:border-0 group">
            <td class="py-4 pr-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0 text-[#5138ed]">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                </div>
                <div class="flex flex-col">
                  <span class="text-[13px] font-bold text-slate-700">{{ bank.title }}</span>
                  <span class="text-[11px] text-slate-400">{{ bank.subtitle }}</span>
                </div>
              </div>
            </td>
            <td class="py-4 px-4 text-[13px] text-slate-600 font-medium">{{ bank.course }}</td>
            <td class="py-4 px-4 text-[13px] text-slate-600 font-bold text-center">{{ bank.total }}</td>
            <td class="py-4 px-4">
              <div class="flex items-center gap-1.5">
                <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-50 text-blue-600 rounded">MCQ {{ bank.types.mcq }}</span>
                <span class="px-2 py-0.5 text-[10px] font-bold bg-purple-50 text-purple-600 rounded">SA {{ bank.types.sa }}</span>
                <span class="px-2 py-0.5 text-[10px] font-bold bg-orange-50 text-orange-600 rounded">Essay {{ bank.types.essay }}</span>
              </div>
            </td>
            <td class="py-4 px-4 text-[13px] text-slate-500 font-medium">{{ bank.date }}</td>
            <td class="py-4 px-4 text-center">
              <span 
                class="px-2.5 py-1 text-[11px] font-bold rounded-md"
                :class="bank.status === 'Active' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'"
              >
                {{ bank.status }}
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
      <span class="text-[13px] text-slate-500 font-medium">Showing 1 to 8 of 12 question banks</span>
      <div class="flex items-center gap-1">
        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#5138ed] text-white font-bold text-sm shadow-sm shadow-indigo-200">1</button>
        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm transition-colors">2</button>
        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
      </div>
    </div>
  </div>
</template>
