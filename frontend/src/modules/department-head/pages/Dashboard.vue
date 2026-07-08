<script setup lang="ts">
import { ref } from 'vue'

const deptName = ref('Computer Science')

// Updated KPIs based on user request
const stats = [
  { label: 'Total Instructors', value: '14',  change: '+2',  bg: 'bg-indigo-50',  ic: 'text-[#5138ed]',  icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
  { label: 'Total Students',    value: '441', change: '+45', bg: 'bg-sky-50',     ic: 'text-sky-500',    icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
  { label: 'Total Courses',     value: '18',  change: '+1',  bg: 'bg-emerald-50', ic: 'text-emerald-500',icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253' },
  { label: 'Total Exams',       value: '87',  change: '+12', bg: 'bg-amber-50',   ic: 'text-amber-500',  icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
]

// Recent Activity Feed
const activities = [
  { id: 1, type: 'exam_published', title: 'Database Systems Final Published',  user: 'Dr. Abebe Kebede', time: '2 hours ago', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', color: 'text-emerald-500 bg-emerald-50' },
  { id: 2, type: 'instructor_added',title: 'New Instructor Onboarded',       user: 'Prof. Yonas Tadesse', time: '5 hours ago', icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', color: 'text-[#5138ed] bg-indigo-50' },
  { id: 3, type: 'exam_completed', title: 'Algorithms Midterm Completed',      user: 'Dr. Meron Bekele',   time: '1 day ago',   icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', color: 'text-sky-500 bg-sky-50' },
  { id: 4, type: 'course_updated', title: 'Software Engineering Syllabus updated', user: 'Dr. Dawit Solomon', time: '2 days ago',  icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', color: 'text-amber-500 bg-amber-50' },
]

// Upcoming Exams
const upcomingExams = [
  { id: 1, title: 'Data Structures Quiz', course: 'CS-201', date: 'Tomorrow, 10:00 AM', students: 110 },
  { id: 2, title: 'Network Security Midterm', course: 'CS-302', date: 'Oct 15, 2:00 PM', students: 85 },
  { id: 3, title: 'Operating Systems Final', course: 'CS-401', date: 'Oct 18, 9:00 AM', students: 134 },
  { id: 4, title: 'Machine Learning Basics', course: 'CS-405', date: 'Oct 20, 1:00 PM', students: 92 },
]

// Quick Actions
const quickActions = [
  { label: 'Add New Instructor', icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', path: '/dept-head/instructors', color: 'text-[#5138ed]', bg: 'bg-indigo-50' },
  { label: 'Create New Course', icon: 'M12 4v16m8-8H4', path: '/dept-head/courses', color: 'text-emerald-500', bg: 'bg-emerald-50' },
  { label: 'View Reports', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', path: '/dept-head/reports', color: 'text-sky-500', bg: 'bg-sky-50' },
  { label: 'Manage Settings', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', path: '/dept-head/settings', color: 'text-amber-500', bg: 'bg-amber-50' },
]

</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Department Dashboard</h1>
        <p class="text-[13px] text-slate-500 mt-1">Overview for the <span class="font-bold text-[#5138ed]">{{ deptName }}</span> department.</p>
      </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-4 gap-4">
      <div v-for="s in stats" :key="s.label" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div :class="[s.bg, 'w-11 h-11 rounded-xl flex items-center justify-center shrink-0']">
          <svg class="w-5 h-5" :class="s.ic" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="s.icon"></path></svg>
        </div>
        <div>
          <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">{{ s.label }}</p>
          <div class="flex items-center gap-2 mt-0.5">
            <span class="text-[20px] font-bold text-slate-800">{{ s.value }}</span>
            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md">{{ s.change }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-3 gap-6">
      
      <!-- Left Column -->
      <div class="col-span-2 space-y-6">
        
        <!-- Activity Feed -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
          <div class="flex items-center justify-between mb-5">
            <h3 class="text-[15px] font-bold text-slate-800">Recent Activities</h3>
            <button class="text-[12px] font-bold text-[#5138ed] hover:underline">View All</button>
          </div>
          <div class="space-y-4">
            <div v-for="act in activities" :key="act.id" class="flex items-start gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors">
              <div :class="[act.color, 'w-10 h-10 rounded-xl flex items-center justify-center shrink-0']">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="act.icon"></path></svg>
              </div>
              <div class="flex-1">
                <p class="text-[13px] font-bold text-slate-800">{{ act.title }}</p>
                <p class="text-[12px] text-slate-500 mt-0.5">by <span class="font-semibold text-slate-700">{{ act.user }}</span></p>
              </div>
              <span class="text-[11px] font-medium text-slate-400 shrink-0">{{ act.time }}</span>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5">
          <h3 class="text-[14px] font-bold text-slate-800 mb-4">Quick Actions</h3>
          <div class="grid grid-cols-4 gap-4">
            <router-link v-for="action in quickActions" :key="action.label" :to="action.path" class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-indigo-200 hover:shadow-sm transition-all group cursor-pointer">
              <div :class="[action.bg, action.color, 'w-9 h-9 rounded-lg flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300']">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="action.icon"></path></svg>
              </div>
              <span class="text-[11px] font-bold text-slate-700 group-hover:text-[#5138ed] transition-colors leading-tight">{{ action.label }}</span>
            </router-link>
          </div>
        </div>

      </div>

      <!-- Right Column -->
      <div class="space-y-6">
        
        <!-- Upcoming Exams -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
          <div class="flex items-center justify-between mb-5">
            <h3 class="text-[15px] font-bold text-slate-800">Upcoming Exams</h3>
            <router-link to="/dept-head/exams" class="text-[12px] font-bold text-[#5138ed] hover:underline">View All</router-link>
          </div>
          <div class="space-y-4">
            <div v-for="exam in upcomingExams" :key="exam.id" class="p-4 rounded-xl border border-slate-100 hover:border-indigo-200 hover:shadow-sm transition-all group">
              <div class="flex items-start justify-between mb-2">
                <h4 class="text-[13px] font-bold text-slate-800 group-hover:text-[#5138ed] transition-colors leading-tight">{{ exam.title }}</h4>
                <span class="text-[10px] font-bold font-mono bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">{{ exam.course }}</span>
              </div>
              <div class="flex items-center justify-between mt-3">
                <div class="flex items-center gap-1.5 text-[11px] text-slate-500 font-medium">
                  <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  {{ exam.date }}
                </div>
                <div class="flex items-center gap-1.5 text-[11px] text-slate-500 font-medium">
                  <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                  {{ exam.students }} students
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>
</template>
