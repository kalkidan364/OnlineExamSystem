<script setup lang="ts">
import { useAuthStore } from '../../auth/store/authStore'
import StatCard from '../components/StatCard.vue'
import PerformanceChart from '../components/PerformanceChart.vue'
import RecentExamsTable from '../components/RecentExamsTable.vue'
import UpcomingExamsList from '../components/UpcomingExamsList.vue'
import QuickActions from '../components/QuickActions.vue'

const authStore = useAuthStore()

const stats = [
  {
    title: 'Total Exams',
    value: '12',
    subtitle: 'All Created Exams',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>',
    colorClass: 'text-[#5138ed]',
    bgClass: 'bg-indigo-50'
  },
  {
    title: 'Upcoming Exams',
    value: '5',
    subtitle: 'Scheduled Exams',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>',
    colorClass: 'text-emerald-500',
    bgClass: 'bg-emerald-50'
  },
  {
    title: 'Total Students',
    value: '320',
    subtitle: 'Across all my courses',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>',
    colorClass: 'text-blue-500',
    bgClass: 'bg-blue-50'
  },
  {
    title: 'Average Score',
    value: '72.4%',
    subtitle: 'Overall Performance',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>',
    colorClass: 'text-orange-500',
    bgClass: 'bg-orange-50'
  }
]
</script>

<template>
  <div class="max-w-7xl mx-auto flex flex-col xl:flex-row gap-6">
    
    <!-- Main Left Column -->
    <div class="flex-1 space-y-6">
      
      <!-- Greeting -->
      <div class="mb-2">
        <h1 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">
          Welcome back, {{ authStore.user?.name || 'Dr. Abebe Kebede' }} <span class="text-2xl">👋</span>
        </h1>
        <p class="text-[14px] text-slate-500 mt-1">Here's what's happening with your exams today.</p>
      </div>

      <!-- 4 Stat Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard v-for="(stat, index) in stats" :key="index" v-bind="stat" />
      </div>

      <!-- Recent Exams -->
      <RecentExamsTable />

      <!-- Performance Overview -->
      <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-[16px] font-bold text-slate-800">Performance Overview</h2>
          <select class="text-sm border border-slate-200 text-slate-600 rounded-lg px-3 py-1.5 focus:outline-none focus:border-[#5138ed]">
            <option>This Semester</option>
            <option>Last Semester</option>
            <option>This Year</option>
          </select>
        </div>
        <div class="flex">
           <!-- Chart Section -->
           <div class="flex-1 border-r border-slate-100 pr-4">
              <PerformanceChart />
           </div>
           <!-- Key Metrics on the right side of chart -->
           <div class="w-48 pl-6 flex flex-col justify-center gap-6">
              <div>
                 <div class="flex items-center gap-1.5 text-blue-500 mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                 </div>
                 <div class="text-xl font-extrabold text-slate-800">72.4%</div>
                 <div class="text-[11px] font-semibold text-slate-400">Average Score</div>
              </div>
              <div>
                 <div class="flex items-center gap-1.5 text-emerald-500 mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                 </div>
                 <div class="text-xl font-extrabold text-slate-800">85.2%</div>
                 <div class="text-[11px] font-semibold text-slate-400">Highest Score</div>
              </div>
              <div>
                 <div class="flex items-center gap-1.5 text-rose-500 mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                 </div>
                 <div class="text-xl font-extrabold text-slate-800">45.3%</div>
                 <div class="text-[11px] font-semibold text-slate-400">Lowest Score</div>
              </div>
           </div>
        </div>
      </div>

    </div>

    <!-- Right Sidebar Column -->
    <div class="w-full xl:w-[320px] space-y-6">
      <UpcomingExamsList />
      <QuickActions />
      
      <!-- Mock Calendar Widget -->
      <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4 text-slate-800">
          <svg class="w-4 h-4 text-slate-400 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
          <span class="text-[14px] font-bold">May 2025</span>
          <svg class="w-4 h-4 text-slate-400 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </div>
        <div class="grid grid-cols-7 gap-1 text-center mb-2">
          <div class="text-[11px] font-semibold text-slate-400 py-1" v-for="day in ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']" :key="day">{{ day }}</div>
        </div>
        <div class="grid grid-cols-7 gap-1 text-center text-[12px] font-medium text-slate-600">
          <!-- Padding -->
          <div class="py-1.5 text-slate-300">27</div>
          <div class="py-1.5 text-slate-300">28</div>
          <div class="py-1.5 text-slate-300">29</div>
          <div class="py-1.5 text-slate-300">30</div>
          <!-- Days -->
          <div class="py-1.5 hover:bg-slate-50 cursor-pointer rounded-lg" v-for="i in 24" :key="i">{{ i }}</div>
          <!-- Selected Day -->
          <div class="py-1.5 bg-[#5138ed] text-white cursor-pointer rounded-lg shadow-sm shadow-indigo-200">25</div>
          <div class="py-1.5 hover:bg-slate-50 cursor-pointer rounded-lg" v-for="i in 6" :key="`post-${i}`">{{ i + 25 }}</div>
        </div>
      </div>
      
    </div>

  </div>
</template>
