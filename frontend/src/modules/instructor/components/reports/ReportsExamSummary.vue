<script setup lang="ts">
const summaries = [
  {
    id: 1,
    title: 'Database Systems Mid Exam',
    type: 'Mid Exam',
    typeClass: 'bg-indigo-50 text-[#5138ed]',
    students: 98,
    avgScore: '82.6%',
    passRate: '78.3%',
    failRate: '21.7%'
  },
  {
    id: 2,
    title: 'Web Programming Quiz 1',
    type: 'Quiz',
    typeClass: 'bg-emerald-50 text-emerald-600',
    students: 95,
    avgScore: '81.4%',
    passRate: '76.5%',
    failRate: '23.5%'
  },
  {
    id: 3,
    title: 'Data Structures Final Exam',
    type: 'Final Exam',
    typeClass: 'bg-orange-50 text-orange-600',
    students: 92,
    avgScore: '75.6%',
    passRate: '70.4%',
    failRate: '29.6%'
  },
  {
    id: 4,
    title: 'Operating Systems Mid Exam',
    type: 'Mid Exam',
    typeClass: 'bg-indigo-50 text-[#5138ed]',
    students: 88,
    avgScore: '79.8%',
    passRate: '74.2%',
    failRate: '25.8%'
  },
  {
    id: 5,
    title: 'Theory of Computation Final',
    type: 'Final Exam',
    typeClass: 'bg-orange-50 text-orange-600',
    students: 65,
    avgScore: '58.2%',
    passRate: '45.1%',
    failRate: '54.9%',
    badFail: true
  }
]
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
    
    <div class="p-6 border-b border-slate-100">
      <h2 class="text-[14px] font-bold text-slate-800">Exam Summary</h2>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-white border-b border-slate-100 text-[10px] font-bold text-slate-500 tracking-wider">
            <th class="px-6 py-3">Exam Title</th>
            <th class="px-4 py-3">Exam Type</th>
            <th class="px-4 py-3 text-center">Total Students</th>
            <th class="px-4 py-3 text-center">Average Score</th>
            <th class="px-4 py-3 text-center">Pass Rate</th>
            <th class="px-4 py-3 text-center">Fail Rate</th>
            <th class="px-6 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="exam in summaries" :key="exam.id" class="hover:bg-slate-50/50 transition-colors group">
            <td class="px-6 py-4">
              <span class="text-[12px] font-bold text-slate-800">{{ exam.title }}</span>
            </td>
            <td class="px-4 py-4">
              <span :class="`inline-block px-2.5 py-1 text-[10px] font-bold rounded-lg ${exam.typeClass}`">
                {{ exam.type }}
              </span>
            </td>
            <td class="px-4 py-4 text-center">
              <span class="text-[11px] font-semibold text-slate-600">{{ exam.students }}</span>
            </td>
            <td class="px-4 py-4 text-center">
              <span class="text-[11px] font-bold text-slate-800">{{ exam.avgScore }}</span>
            </td>
            <td class="px-4 py-4 text-center">
              <span :class="`text-[11px] font-bold ${exam.badFail ? 'text-rose-500' : 'text-emerald-500'}`">
                {{ exam.passRate }}
              </span>
            </td>
            <td class="px-4 py-4 text-center">
              <span :class="`text-[11px] font-bold ${exam.badFail ? 'text-rose-500' : 'text-rose-500'}`">
                {{ exam.failRate }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button class="p-1.5 text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 rounded-lg transition-colors" title="View Report">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>
                <button class="p-1.5 text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 rounded-lg transition-colors" title="Export">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination & View All -->
    <div class="p-4 border-t border-slate-100 flex items-center justify-between">
      <span class="text-[11px] font-medium text-slate-500 pl-2">Showing 1 to 5 of 18 exams</span>
      <button class="text-[12px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors flex items-center gap-1.5 pr-2">
        View All Reports
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
      </button>
    </div>

  </div>
</template>
