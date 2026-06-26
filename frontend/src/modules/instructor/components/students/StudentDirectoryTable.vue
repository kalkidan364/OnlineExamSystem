<script setup lang="ts">
const students = [
  {
    id: 1,
    name: 'Abebe Tesfaye',
    email: 'abebe.tesfaye@wollo.edu.et',
    avatar: 'https://i.pravatar.cc/150?u=a042581f4e29026024d',
    idNumber: 'WU/2021/CS/001',
    course: 'Computer Science',
    semester: '6th Semester',
    status: 'Active',
    score: 85.6,
    examsTaken: 12
  },
  {
    id: 2,
    name: 'Selamawit Getachew',
    email: 'selamawit.get@wollo.edu.et',
    avatar: 'https://i.pravatar.cc/150?u=a042581f4e29026704d',
    idNumber: 'WU/2021/SE/015',
    course: 'Software Engineering',
    semester: '6th Semester',
    status: 'Active',
    score: 78.3,
    examsTaken: 10
  },
  {
    id: 3,
    name: 'Kebede Assefa',
    email: 'kebede.assefa@wollo.edu.et',
    avatar: 'https://i.pravatar.cc/150?u=a042581f4e29026703d',
    idNumber: 'WU/2022/CS/027',
    course: 'Computer Science',
    semester: '4th Semester',
    status: 'Active',
    score: 68.7,
    examsTaken: 8
  },
  {
    id: 4,
    name: 'Hanna Mengesha',
    email: 'hanna.mengesha@wollo.edu.et',
    avatar: 'https://i.pravatar.cc/150?u=a042581f4e29026705d',
    idNumber: 'WU/2022/IT/031',
    course: 'Information Technology',
    semester: '4th Semester',
    status: 'Active',
    score: 82.1,
    examsTaken: 9
  },
  {
    id: 5,
    name: 'Mekonnen Worku',
    email: 'mekonnen.worku@wollo.edu.et',
    avatar: 'https://i.pravatar.cc/150?u=a042581f4e29026706d',
    idNumber: 'WU/2021/CS/045',
    course: 'Computer Science',
    semester: '6th Semester',
    status: 'Inactive',
    score: 45.2,
    examsTaken: 6
  },
  {
    id: 6,
    name: 'Rahel Solomon',
    email: 'rahel.solomon@wollo.edu.et',
    avatar: 'https://i.pravatar.cc/150?u=a042581f4e29026707d',
    idNumber: 'WU/2023/SE/058',
    course: 'Software Engineering',
    semester: '2nd Semester',
    status: 'Active',
    score: 75.9,
    examsTaken: 4
  },
  {
    id: 7,
    name: 'Yonas Alemu',
    email: 'yonas.alemu@wollo.edu.et',
    avatar: 'https://i.pravatar.cc/150?u=a042581f4e29026708d',
    idNumber: 'WU/2023/CS/064',
    course: 'Computer Science',
    semester: '2nd Semester',
    status: 'Active',
    score: 62.4,
    examsTaken: 4
  },
  {
    id: 8,
    name: 'Lidya Gebremedhin',
    email: 'lidya.gebremedhin@wollo.edu.et',
    avatar: 'https://i.pravatar.cc/150?u=a042581f4e29026709d',
    idNumber: 'WU/2023/IT/072',
    course: 'Information Technology',
    semester: '2nd Semester',
    status: 'Active',
    score: 88.6,
    examsTaken: 5
  }
]

const getScoreColor = (score: number) => {
  if (score >= 80) return 'text-emerald-500'
  if (score >= 60) return 'text-orange-500'
  return 'text-rose-500'
}
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl shadow-sm mb-6 overflow-hidden">
    
    <div class="px-6 pt-6 pb-4 border-b border-slate-100">
      <h2 class="text-[14px] font-bold text-slate-800 mb-4">Students Directory</h2>
      
      <!-- Filter Bar -->
      <div class="flex flex-wrap items-center gap-4">
        
        <!-- Search -->
        <div class="relative flex-1 min-w-[200px]">
          <svg class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input type="text" placeholder="Search students..." class="w-full pl-11 pr-4 py-2 text-[12px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] text-slate-700 placeholder:text-slate-400">
        </div>

        <!-- Course Dropdown -->
        <div class="relative min-w-[140px]">
          <select class="w-full appearance-none pl-4 pr-10 py-2 text-[12px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] text-slate-700 bg-white cursor-pointer font-medium">
            <option>All Courses</option>
          </select>
          <svg class="w-4 h-4 text-slate-400 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>

        <!-- Status Dropdown -->
        <div class="relative min-w-[120px]">
          <select class="w-full appearance-none pl-4 pr-10 py-2 text-[12px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] text-slate-700 bg-white cursor-pointer font-medium">
            <option>All Status</option>
          </select>
          <svg class="w-4 h-4 text-slate-400 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>

        <!-- Semester Dropdown -->
        <div class="relative min-w-[140px]">
          <select class="w-full appearance-none pl-4 pr-10 py-2 text-[12px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] text-slate-700 bg-white cursor-pointer font-medium">
            <option>All Semesters</option>
          </select>
          <svg class="w-4 h-4 text-slate-400 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>

        <!-- Filter Button -->
        <button class="px-5 py-2 border border-[#5138ed] text-[#5138ed] font-bold text-[12px] rounded-xl hover:bg-indigo-50 transition-colors flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
          Filter
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-white border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
            <th class="px-6 py-3">Student</th>
            <th class="px-4 py-3">ID Number</th>
            <th class="px-4 py-3">Course</th>
            <th class="px-4 py-3">Semester</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-center">Average Score</th>
            <th class="px-4 py-3 text-center">Exams Taken</th>
            <th class="px-6 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="student in students" :key="student.id" class="hover:bg-slate-50/50 transition-colors group">
            <td class="px-6 py-3.5">
              <div class="flex items-center gap-3">
                <img :src="student.avatar" alt="Avatar" class="w-8 h-8 rounded-full object-cover border border-slate-200" />
                <div>
                  <span class="block text-[12px] font-bold text-slate-800">{{ student.name }}</span>
                  <span class="block text-[10px] font-medium text-slate-500 mt-0.5">{{ student.email }}</span>
                </div>
              </div>
            </td>
            <td class="px-4 py-3.5">
              <span class="text-[11px] font-semibold text-slate-600">{{ student.idNumber }}</span>
            </td>
            <td class="px-4 py-3.5">
              <span class="text-[11px] font-medium text-slate-600">{{ student.course }}</span>
            </td>
            <td class="px-4 py-3.5">
              <span class="text-[11px] font-medium text-slate-600">{{ student.semester }}</span>
            </td>
            <td class="px-4 py-3.5">
              <span class="text-[11px] font-bold" :class="student.status === 'Active' ? 'text-emerald-500' : 'text-rose-500'">
                {{ student.status }}
              </span>
            </td>
            <td class="px-4 py-3.5 text-center">
              <span class="text-[11px] font-black" :class="getScoreColor(student.score)">
                {{ student.score }}%
              </span>
            </td>
            <td class="px-4 py-3.5 text-center">
              <span class="text-[12px] font-semibold text-slate-600">{{ student.examsTaken }}</span>
            </td>
            <td class="px-6 py-3.5">
              <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button class="p-1.5 text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 rounded-lg transition-colors" title="View Profile">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>
                <button class="p-1.5 text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 rounded-lg transition-colors" title="View Progress">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                </button>
                <button class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="p-4 border-t border-slate-100 flex items-center justify-between">
      <span class="text-[11px] font-medium text-slate-500 pl-2">Showing 1 to 8 of 320 students</span>
      <div class="flex items-center gap-1 pr-2">
        <button class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors disabled:opacity-50" disabled>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button class="w-7 h-7 flex items-center justify-center rounded-lg bg-[#5138ed] text-white font-bold text-[11px] shadow-sm">1</button>
        <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-600 font-bold text-[11px] transition-colors">2</button>
        <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-600 font-bold text-[11px] transition-colors">3</button>
        <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-600 font-bold text-[11px] transition-colors">...</button>
        <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-50 text-slate-600 font-bold text-[11px] transition-colors">40</button>
        <button class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
      </div>
    </div>

  </div>
</template>
