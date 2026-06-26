<script setup lang="ts">
const courses = [
  { name: 'Database Systems', questions: 78 },
  { name: 'Web Programming', questions: 95 },
  { name: 'Data Structures', questions: 92 },
  { name: 'Software Engineering', questions: 88 },
  { name: 'Operating Systems', questions: 65 },
]
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    <h2 class="text-[14px] font-bold text-slate-800 mb-6">Top Courses</h2>
    
    <div class="space-y-0">
      <div 
        v-for="(course, index) in courses" 
        :key="index"
        class="flex items-center justify-between py-3.5 border-b border-slate-50 last:border-0"
      >
        <span class="text-[13px] font-medium text-slate-600">{{ course.name }}</span>
        <span class="text-[12px] font-bold text-slate-500">{{ course.questions }} Questions</span>
      </div>
    </div>

    <button class="w-full mt-4 py-2 border border-indigo-50 rounded-xl text-[13px] font-bold text-[#5138ed] hover:bg-indigo-50 transition-colors">
      View All Courses
    </button>
  </div>
</template>
