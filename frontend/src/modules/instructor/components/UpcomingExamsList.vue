<script setup lang="ts">
const upcomingExams = [
  {
    id: 1,
    date: '25',
    month: 'MAY',
    title: 'Database Systems Mid Exam',
    time: '10:00 AM - 11:30 AM',
    course: 'Database Systems'
  },
  {
    id: 2,
    date: '30',
    month: 'MAY',
    title: 'Operating Systems Mid Exam',
    time: '11:00 AM - 12:30 PM',
    course: 'Operating Systems'
  },
  {
    id: 3,
    date: '02',
    month: 'JUN',
    title: 'Software Engineering Quiz 1',
    time: '09:00 AM - 10:00 AM',
    course: 'Software Engineering'
  }
]
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        <h2 class="text-[16px] font-bold text-slate-800">Upcoming Exams</h2>
      </div>
      <button class="text-sm font-semibold text-[#5138ed] hover:text-indigo-700 transition-colors">
        View All
      </button>
    </div>

    <div class="space-y-5">
      <div v-for="exam in upcomingExams" :key="exam.id" class="flex gap-4 items-start group cursor-pointer">
        <!-- Date Badge -->
        <div class="flex flex-col items-center justify-center w-12 h-14 rounded-xl border border-indigo-100 bg-white group-hover:bg-[#5138ed] group-hover:border-[#5138ed] transition-colors flex-shrink-0">
          <span class="text-lg font-extrabold text-[#5138ed] group-hover:text-white leading-none">{{ exam.date }}</span>
          <span class="text-[10px] font-bold text-slate-400 group-hover:text-indigo-100 mt-1 uppercase">{{ exam.month }}</span>
        </div>
        
        <!-- Exam Info -->
        <div class="flex flex-col flex-1 pt-0.5">
          <h4 class="text-[14px] font-bold text-slate-800 group-hover:text-[#5138ed] transition-colors line-clamp-1">{{ exam.title }}</h4>
          <div class="flex items-center gap-1.5 mt-1 text-slate-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-[12px] font-medium">{{ exam.time }}</span>
          </div>
          <div class="flex items-center gap-1.5 mt-1 text-slate-500">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span class="text-[12px] font-medium">{{ exam.course }}</span>
          </div>
        </div>
      </div>
    </div>

    <button class="w-full mt-6 py-2.5 rounded-xl border border-indigo-100 text-[14px] font-bold text-[#5138ed] hover:bg-[#5138ed] hover:text-white transition-colors">
      View Full Schedule
    </button>
  </div>
</template>
