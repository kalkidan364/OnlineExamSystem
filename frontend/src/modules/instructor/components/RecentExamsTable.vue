<script setup lang="ts">
const recentExams = [
  { id: 1, title: 'Database Systems Mid Exam', course: 'Database Systems', date: 'May 25, 2025 10:00 AM', status: 'Upcoming' },
  { id: 2, title: 'Web Programming Quiz 1', course: 'Web Programming', date: 'May 20, 2025 09:00 AM', status: 'Completed' },
  { id: 3, title: 'Data Structures Final Exam', course: 'Data Structures', date: 'May 18, 2025 02:00 PM', status: 'Completed' },
  { id: 4, title: 'Operating Systems Mid Exam', course: 'Operating Systems', date: 'May 30, 2025 11:00 AM', status: 'Upcoming' },
  { id: 5, title: 'Software Engineering Quiz 1', course: 'Software Engineering', date: 'Jun 02, 2025 09:00 AM', status: 'Upcoming' },
]
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <h2 class="text-[16px] font-bold text-slate-800">Recent Exams</h2>
      </div>
      <button class="text-sm font-semibold text-[#5138ed] bg-indigo-50 px-4 py-1.5 rounded-lg hover:bg-indigo-100 transition-colors">
        View All
      </button>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-100 text-[13px] font-bold text-slate-400 uppercase tracking-wide">
            <th class="pb-4 pr-4 font-semibold">Exam Title</th>
            <th class="pb-4 px-4 font-semibold">Course</th>
            <th class="pb-4 px-4 font-semibold">Date & Time</th>
            <th class="pb-4 px-4 font-semibold">Status</th>
            <th class="pb-4 pl-4 text-center font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="exam in recentExams" :key="exam.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors last:border-0 group">
            <td class="py-4 pr-4 text-[14px] font-semibold text-slate-700">{{ exam.title }}</td>
            <td class="py-4 px-4 text-[14px] text-slate-500 font-medium">{{ exam.course }}</td>
            <td class="py-4 px-4 text-[14px] text-slate-500 font-medium">{{ exam.date }}</td>
            <td class="py-4 px-4">
              <span 
                class="px-3 py-1 text-[12px] font-bold rounded-full"
                :class="exam.status === 'Upcoming' ? 'bg-indigo-50 text-[#5138ed]' : 'bg-emerald-50 text-emerald-600'"
              >
                {{ exam.status }}
              </span>
            </td>
            <td class="py-4 pl-4 text-center">
              <button class="text-slate-400 hover:text-[#5138ed] transition-colors p-1 rounded hover:bg-indigo-50">
                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
