<script setup lang="ts">
import { ref, computed } from 'vue'

const search = ref('')
const semesterFilter = ref('all')
const yearFilter = ref('all')
const examTypeFilter = ref('all')
const statusFilter = ref('all')
const currentPage = ref(1)
const perPage = 8

const currentView = ref<'list' | 'detail' | 'questions'>('list')
const selectedExam = ref<any>(null)

const openDetail = (exam: any) => {
  selectedExam.value = exam
  currentView.value = 'detail'
}
const backToList = () => {
  currentView.value = 'list'
}
const backToDetail = () => {
  currentView.value = 'detail'
}

// ── Dummy Data Matching the Image ──
const allExams = ref([
  { id: 1, title: 'Database Systems Midterm', code: 'EXM-2026-001', courseName: 'Database Systems', courseCode: 'CS-301', type: 'Midterm', date: 'Jun 10, 2026', time: '09:00 AM', duration: '1h 30m', questions: 40, marks: 100, status: 'Scheduled' },
  { id: 2, title: 'Software Engineering Final', code: 'EXM-2026-002', courseName: 'Software Engineering', courseCode: 'SE-201', type: 'Final', date: 'Jun 18, 2026', time: '02:00 PM', duration: '2h 00m', questions: 50, marks: 100, status: 'Scheduled' },
  { id: 3, title: 'Data Structures Quiz 3', code: 'EXM-2026-003', courseName: 'Data Structures', courseCode: 'CS-202', type: 'Quiz', date: 'May 30, 2026', time: '10:00 AM', duration: '30m', questions: 20, marks: 20, status: 'Published' },
  { id: 4, title: 'Operating Systems Midterm', code: 'EXM-2026-004', courseName: 'Operating Systems', courseCode: 'CS-401', type: 'Midterm', date: 'May 28, 2026', time: '09:00 AM', duration: '1h 45m', questions: 45, marks: 100, status: 'Completed' },
  { id: 5, title: 'Web Development Quiz 2', code: 'EXM-2026-005', courseName: 'Web Development', courseCode: 'SE-302', type: 'Quiz', date: 'May 24, 2026', time: '11:00 AM', duration: '20m', questions: 15, marks: 15, status: 'Published' },
  { id: 6, title: 'Information Systems Final', code: 'EXM-2026-006', courseName: 'Information Systems', courseCode: 'IS-201', type: 'Final', date: 'Jun 05, 2026', time: '09:00 AM', duration: '2h 30m', questions: 60, marks: 100, status: 'Scheduled' },
  { id: 7, title: 'Artificial Intelligence Exam', code: 'EXM-2026-007', courseName: 'Artificial Intelligence', courseCode: 'AI-301', type: 'Final', date: 'May 29, 2026', time: '01:00 PM', duration: '2h 00m', questions: 70, marks: 100, status: 'Draft' },
  { id: 8, title: 'Computer Networks Quiz', code: 'EXM-2026-008', courseName: 'Computer Networks', courseCode: 'CS-303', type: 'Quiz', date: 'May 21, 2026', time: '10:00 AM', duration: '25m', questions: 15, marks: 15, status: 'Completed' },
])

const filtered = computed(() => {
  return allExams.value.filter(e => {
    const matchSearch = e.title.toLowerCase().includes(search.value.toLowerCase()) || e.code.toLowerCase().includes(search.value.toLowerCase()) || e.courseName.toLowerCase().includes(search.value.toLowerCase())
    const matchStatus = statusFilter.value === 'all' || e.status.toLowerCase() === statusFilter.value.toLowerCase()
    return matchSearch && matchStatus
  })
})

const totalPages = computed(() => Math.ceil(filtered.value.length / perPage))
const paginated = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const stats = computed(() => [
  { label: 'Total Exams', value: '68', change: '↑ 5 this semester', bg: 'bg-indigo-50', ic: 'text-[#5138ed]', color: 'text-emerald-500', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
  { label: 'Upcoming Exams', value: '12', change: '↑ 3 this week', bg: 'bg-emerald-50', ic: 'text-emerald-500', color: 'text-emerald-500', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
  { label: 'Completed Exams', value: '42', change: '↑ 7 this semester', bg: 'bg-sky-50', ic: 'text-sky-500', color: 'text-emerald-500', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
  { label: 'Cancelled Exams', value: '1', change: 'No change', bg: 'bg-rose-50', ic: 'text-rose-500', color: 'text-slate-500', icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' },
])

const typeBadge = (t: string) => {
  if (t === 'Midterm') return 'bg-indigo-50 text-[#5138ed]'
  if (t === 'Final') return 'bg-amber-50 text-amber-500'
  return 'bg-emerald-50 text-emerald-500' // Quiz
}

const statusBadge = (s: string) => {
  if (s === 'Scheduled') return 'bg-sky-50 text-sky-500'
  if (s === 'Published') return 'bg-emerald-50 text-emerald-500'
  if (s === 'Completed') return 'bg-slate-100 text-slate-500'
  return 'bg-amber-50 text-amber-500' // Draft
}

const displayPages = computed(() => {
  const tp = Math.max(1, totalPages.value)
  if (tp <= 9) return Array.from({ length: Math.min(tp, 9) }, (_, i) => i + 1)
  return [1, 2, 3, 4, 5, '...', 9]
})
</script>

<template>
  <div class="space-y-6">

    <!-- ══════════════════════════ EXAM DETAILS VIEW ══════════════════════════ -->
    <template v-if="currentView === 'detail'">
      <!-- Top header with back button -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <button @click="backToList" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
          </button>
          <h1 class="text-[20px] font-bold text-slate-800">Department Head Dashboard</h1>
        </div>
      </div>

      <div class="flex items-start gap-6">
        <!-- Main Content (Left) -->
        <div class="flex-1 space-y-6">
          
          <!-- Exam Title Card -->
          <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center gap-4">
                <h2 class="text-[20px] font-bold text-slate-800">{{ selectedExam?.title }}</h2>
                <span :class="[statusBadge(selectedExam?.status), 'text-[11px] font-bold px-2.5 py-1 rounded-md capitalize']">{{ selectedExam?.status }}</span>
                <span class="text-[12px] font-semibold text-[#5138ed] bg-indigo-50 px-2.5 py-1 rounded-md">{{ selectedExam?.code }}</span>
              </div>
              <button @click="backToList" class="flex items-center gap-2 px-4 py-2 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Exam List
              </button>
            </div>
            
            <div class="grid grid-cols-3 gap-6 pt-6 border-t border-slate-100">
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <div>
                  <p class="text-[11px] font-bold text-slate-500 mb-0.5">Course</p>
                  <p class="text-[13px] font-semibold text-slate-800">{{ selectedExam?.courseName }} <span class="text-slate-400 font-medium ml-1">({{ selectedExam?.courseCode }})</span></p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <div>
                  <p class="text-[11px] font-bold text-slate-500 mb-0.5">Exam Date</p>
                  <p class="text-[13px] font-semibold text-slate-800">{{ selectedExam?.date }}</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                  <p class="text-[11px] font-bold text-slate-500 mb-0.5">Total Questions</p>
                  <p class="text-[13px] font-semibold text-slate-800">{{ selectedExam?.questions }}</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <div>
                  <p class="text-[11px] font-bold text-slate-500 mb-0.5">Instructor</p>
                  <p class="text-[13px] font-semibold text-slate-800">Abebe Kebede</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                  <p class="text-[11px] font-bold text-slate-500 mb-0.5">Total Marks</p>
                  <p class="text-[13px] font-semibold text-slate-800">{{ selectedExam?.marks }}</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                  <p class="text-[11px] font-bold text-slate-500 mb-0.5">Start Time</p>
                  <p class="text-[13px] font-semibold text-slate-800">{{ selectedExam?.time }}</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <div>
                  <p class="text-[11px] font-bold text-slate-500 mb-0.5">Exam Type</p>
                  <p class="text-[13px] font-semibold text-slate-800">{{ selectedExam?.type }}</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                  <p class="text-[11px] font-bold text-slate-500 mb-0.5">Duration</p>
                  <p class="text-[13px] font-semibold text-slate-800">{{ selectedExam?.duration }}</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                  <p class="text-[11px] font-bold text-slate-500 mb-0.5">End Time</p>
                  <p class="text-[13px] font-semibold text-slate-800">10:30 AM</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Exam Behavior -->
          <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
              <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
              <h3 class="text-[16px] font-bold text-slate-800">Exam Behavior</h3>
            </div>
            <div class="grid grid-cols-2 gap-y-6 gap-x-12">
              <div class="flex items-center gap-4">
                <div class="w-10 h-6 bg-[#5138ed] rounded-full relative shrink-0"><div class="w-4 h-4 bg-white rounded-full absolute right-1 top-1"></div></div>
                <div>
                  <p class="text-[13px] font-bold text-slate-700">Shuffle Questions</p>
                  <p class="text-[11px] text-slate-500">Randomize the order of questions for each student</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="w-10 h-6 bg-[#5138ed] rounded-full relative shrink-0"><div class="w-4 h-4 bg-white rounded-full absolute right-1 top-1"></div></div>
                <div>
                  <p class="text-[13px] font-bold text-slate-700">Exam Review Group</p>
                  <p class="text-[11px] text-slate-500">Allow students to review answers before submission</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="w-10 h-6 bg-[#5138ed] rounded-full relative shrink-0"><div class="w-4 h-4 bg-white rounded-full absolute right-1 top-1"></div></div>
                <div>
                  <p class="text-[13px] font-bold text-slate-700">Shuffle Answer Options</p>
                  <p class="text-[11px] text-slate-500">Randomize the order of answer options</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="w-10 h-6 bg-[#5138ed] rounded-full relative shrink-0"><div class="w-4 h-4 bg-white rounded-full absolute right-1 top-1"></div></div>
                <div>
                  <p class="text-[13px] font-bold text-slate-700">Allow Backtracking</p>
                  <p class="text-[11px] text-slate-500">Students can go back to previous questions</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="w-10 h-6 bg-slate-200 rounded-full relative shrink-0"><div class="w-4 h-4 bg-white rounded-full absolute left-1 top-1"></div></div>
                <div>
                  <p class="text-[13px] font-bold text-slate-700">Show One Question at a Time</p>
                  <p class="text-[11px] text-slate-500">Students see one question at a time</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="w-10 h-6 bg-[#5138ed] rounded-full relative shrink-0"><div class="w-4 h-4 bg-white rounded-full absolute right-1 top-1"></div></div>
                <div>
                  <p class="text-[13px] font-bold text-slate-700">Auto Submit on Time Finish</p>
                  <p class="text-[11px] text-slate-500">Automatically submit when time is up</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Security Settings -->
          <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
              <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
              <h3 class="text-[16px] font-bold text-slate-800">Security Settings</h3>
            </div>
            <div class="grid grid-cols-2 gap-y-6 gap-x-12">
              <div class="flex items-center gap-4">
                <div class="w-10 h-6 bg-[#5138ed] rounded-full relative shrink-0"><div class="w-4 h-4 bg-white rounded-full absolute right-1 top-1"></div></div>
                <div>
                  <p class="text-[13px] font-bold text-slate-700">Enable Fullscreen Mode</p>
                  <p class="text-[11px] text-slate-500">Prevent students from leaving the exam screen</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="w-10 h-6 bg-[#5138ed] rounded-full relative shrink-0"><div class="w-4 h-4 bg-white rounded-full absolute right-1 top-1"></div></div>
                <div>
                  <p class="text-[13px] font-bold text-slate-700">Enable Browser Tab Monitoring</p>
                  <p class="text-[11px] text-slate-500">Detect if student switches tab or window</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="w-10 h-6 bg-[#5138ed] rounded-full relative shrink-0"><div class="w-4 h-4 bg-white rounded-full absolute right-1 top-1"></div></div>
                <div>
                  <p class="text-[13px] font-bold text-slate-700">Disable Right Click</p>
                  <p class="text-[11px] text-slate-500">Prevent right click on the exam screen</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="w-10 h-6 bg-slate-200 rounded-full relative shrink-0"><div class="w-4 h-4 bg-white rounded-full absolute left-1 top-1"></div></div>
                <div>
                  <p class="text-[13px] font-bold text-slate-700">Allow Calculator</p>
                  <p class="text-[11px] text-slate-500">Provide an on-screen calculator for students</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="w-10 h-6 bg-[#5138ed] rounded-full relative shrink-0"><div class="w-4 h-4 bg-white rounded-full absolute right-1 top-1"></div></div>
                <div>
                  <p class="text-[13px] font-bold text-slate-700">Disable Copy & Paste</p>
                  <p class="text-[11px] text-slate-500">Prevent copy and paste operations</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="w-10 h-6 bg-slate-200 rounded-full relative shrink-0"><div class="w-4 h-4 bg-white rounded-full absolute left-1 top-1"></div></div>
                <div>
                  <p class="text-[13px] font-bold text-slate-700">Webcam Monitoring</p>
                  <p class="text-[11px] text-slate-500">Record or monitor exam using webcam</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Created Exam (Questions Preview) -->
          <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
              <div>
                <h3 class="text-[15px] font-bold text-slate-800">Created Exam (Questions Preview)</h3>
                <p class="text-[12px] text-slate-500">Total Questions: {{ selectedExam?.questions }} | Total Marks: {{ selectedExam?.marks }}</p>
              </div>
              <button @click="currentView = 'questions'" class="text-[12px] font-bold text-[#5138ed] hover:text-indigo-700">View All Questions</button>
            </div>
            
            <table class="w-full">
              <thead>
                <tr class="border-b border-slate-100">
                  <th class="text-left px-4 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider w-16">#</th>
                  <th class="text-left px-4 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider w-40">Question Type</th>
                  <th class="text-left px-4 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Question</th>
                  <th class="text-center px-4 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider w-24">Marks</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50">
                <tr class="hover:bg-slate-50 transition-colors">
                  <td class="px-4 py-4 text-[13px] font-bold text-slate-600">1</td>
                  <td class="px-4 py-4"><span class="text-[12px] font-bold text-sky-500 bg-sky-50 px-2 py-1 rounded">MCQ</span></td>
                  <td class="px-4 py-4 text-[13px] text-slate-700">Which of the following is a characteristic of a relational database?</td>
                  <td class="px-4 py-4 text-[13px] font-bold text-slate-600 text-center">1</td>
                </tr>
                <tr class="hover:bg-slate-50 transition-colors">
                  <td class="px-4 py-4 text-[13px] font-bold text-slate-600">2</td>
                  <td class="px-4 py-4"><span class="text-[12px] font-bold text-sky-500 bg-sky-50 px-2 py-1 rounded">MCQ</span></td>
                  <td class="px-4 py-4 text-[13px] text-slate-700">What is the primary key used for?</td>
                  <td class="px-4 py-4 text-[13px] font-bold text-slate-600 text-center">1</td>
                </tr>
                <tr class="hover:bg-slate-50 transition-colors">
                  <td class="px-4 py-4 text-[13px] font-bold text-slate-600">3</td>
                  <td class="px-4 py-4"><span class="text-[12px] font-bold text-emerald-500 bg-emerald-50 px-2 py-1 rounded">True/False</span></td>
                  <td class="px-4 py-4 text-[13px] text-slate-700">Normalization is the process of organizing data to reduce redundancy.</td>
                  <td class="px-4 py-4 text-[13px] font-bold text-slate-600 text-center">2</td>
                </tr>
                <tr class="hover:bg-slate-50 transition-colors">
                  <td class="px-4 py-4 text-[13px] font-bold text-slate-600">4</td>
                  <td class="px-4 py-4"><span class="text-[12px] font-bold text-amber-500 bg-amber-50 px-2 py-1 rounded">Short Answer</span></td>
                  <td class="px-4 py-4 text-[13px] text-slate-700">Explain the difference between DELETE and TRUNCATE statements.</td>
                  <td class="px-4 py-4 text-[13px] font-bold text-slate-600 text-center">5</td>
                </tr>
              </tbody>
            </table>
            <div @click="currentView = 'questions'" class="mt-4 border border-slate-100 rounded-xl py-3 flex justify-center hover:bg-slate-50 cursor-pointer transition-colors">
              <span class="text-[12px] font-bold text-[#5138ed]">Show More (36 Questions)</span>
            </div>
          </div>
        </div>

        <!-- Right Sidebar -->
        <div class="w-[320px] shrink-0 space-y-6">
          
          <!-- Exam Summary -->
          <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
            <h3 class="text-[15px] font-bold text-slate-800 mb-5">Exam Summary</h3>
            
            <div class="flex gap-4 mb-6">
              <div class="w-12 h-12 rounded-xl bg-indigo-50 text-[#5138ed] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
              </div>
              <div>
                <h4 class="text-[14px] font-bold text-slate-800 leading-tight">{{ selectedExam?.title }}</h4>
                <p class="text-[11px] font-medium text-slate-400 mt-1">{{ selectedExam?.code }}</p>
              </div>
            </div>

            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-[12px] text-slate-500 font-medium">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  Status
                </div>
                <span :class="[statusBadge(selectedExam?.status), 'text-[11px] font-bold px-2 py-0.5 rounded capitalize']">{{ selectedExam?.status }}</span>
              </div>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-[12px] text-slate-500 font-medium">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                  Exam Type
                </div>
                <span class="text-[12px] font-bold text-[#5138ed]">{{ selectedExam?.type }}</span>
              </div>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-[12px] text-slate-500 font-medium">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                  Created By
                </div>
                <span class="text-[12px] font-bold text-slate-700">Super Admin</span>
              </div>
              
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-[12px] text-slate-500 font-medium">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                  Created Date
                </div>
                <span class="text-[12px] font-bold text-slate-700">May 10, 2026 10:30 AM</span>
              </div>

              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-[12px] text-slate-500 font-medium">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                  Last Updated
                </div>
                <span class="text-[12px] font-bold text-slate-700">May 15, 2026 02:15 PM</span>
              </div>

              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-[12px] text-slate-500 font-medium">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                  Total Attempts
                </div>
                <span class="text-[12px] font-bold text-slate-700">256</span>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
            <h3 class="text-[15px] font-bold text-slate-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
              <button class="flex flex-col items-center justify-center gap-2 py-4 rounded-xl border border-slate-200 hover:border-indigo-200 hover:bg-indigo-50 text-slate-600 hover:text-[#5138ed] transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                <span class="text-[11px] font-bold">Preview Exam</span>
              </button>
              <button class="flex flex-col items-center justify-center gap-2 py-4 rounded-xl border border-slate-200 hover:border-emerald-200 hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span class="text-[11px] font-bold">Export Results</span>
              </button>
            </div>
          </div>
          
        </div>
      </div>
    </template>

    <!-- ══════════════════════════ ALL QUESTIONS VIEW ══════════════════════════ -->
    <template v-else-if="currentView === 'questions'">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-indigo-50 text-[#5138ed] flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          </div>
          <div>
            <h1 class="text-[20px] font-bold text-slate-800">All Questions</h1>
            <p class="text-[13px] text-slate-500 mt-0.5">View all questions in this exam.</p>
          </div>
        </div>
      </div>
      
      <!-- Breadcrumb -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2 text-[13px] font-medium text-slate-500">
          <span class="hover:text-slate-800 cursor-pointer">Exams</span>
          <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          <span class="hover:text-slate-800 cursor-pointer" @click="currentView = 'list'">Exam List</span>
          <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          <span class="hover:text-slate-800 cursor-pointer" @click="backToDetail">Exam Details</span>
          <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          <span class="text-slate-800 font-bold">All Questions</span>
        </div>
        <button @click="backToDetail" class="flex items-center gap-2 px-4 py-2 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors shadow-sm bg-white">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          Back to Exam Details
        </button>
      </div>

      <!-- Content Card -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
        <div class="flex items-center gap-4 text-[15px] font-bold text-slate-800 mb-8 pb-4 border-b border-slate-100">
          <span>Total Questions: 40</span>
          <div class="w-px h-4 bg-slate-300"></div>
          <span>Total Marks: 100</span>
        </div>

        <div class="space-y-6">
          <!-- Question 1 -->
          <div class="border border-slate-100 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-[#5138ed] font-bold text-[13px] flex items-center justify-center">Q1</span>
                <span class="text-[12px] font-bold text-[#5138ed] bg-indigo-50 px-2.5 py-1.5 rounded-lg">Multiple Choice</span>
              </div>
              <span class="text-[12px] font-bold text-[#5138ed] bg-indigo-50 px-2.5 py-1.5 rounded-lg">5 Marks</span>
            </div>
            
            <p class="text-[12px] font-bold text-slate-500 mb-2">Question</p>
            <p class="text-[14px] font-bold text-slate-800 mb-5">Which of the following is a characteristic of a relational database?</p>
            
            <p class="text-[12px] font-bold text-slate-500 mb-3">Options</p>
            <div class="space-y-3 mb-6">
              <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full border-[5px] border-[#5138ed] bg-white"></div>
                <span class="text-[13px] font-bold text-slate-600">A. Data is stored in tables with rows and columns</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full border border-slate-300"></div>
                <span class="text-[13px] font-medium text-slate-600">B. Data is stored as key-value pairs</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full border border-slate-300"></div>
                <span class="text-[13px] font-medium text-slate-600">C. Data is stored in a hierarchical structure</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full border border-slate-300"></div>
                <span class="text-[13px] font-medium text-slate-600">D. Data is stored in documents</span>
              </div>
            </div>
            
            <p class="text-[12px] font-bold text-slate-500 mb-1">Instruction</p>
            <p class="text-[13px] text-slate-600">Choose the most appropriate answer from the options given below.</p>
          </div>

          <!-- Question 2 -->
          <div class="border border-slate-100 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-[#5138ed] font-bold text-[13px] flex items-center justify-center">Q2</span>
                <span class="text-[12px] font-bold text-[#5138ed] bg-indigo-50 px-2.5 py-1.5 rounded-lg">Multiple Choice</span>
              </div>
              <span class="text-[12px] font-bold text-[#5138ed] bg-indigo-50 px-2.5 py-1.5 rounded-lg">5 Marks</span>
            </div>
            
            <p class="text-[12px] font-bold text-slate-500 mb-2">Question</p>
            <p class="text-[14px] font-bold text-slate-800 mb-5">What is the primary key used for?</p>
            
            <div class="space-y-3 mb-6">
              <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full border-[5px] border-[#5138ed] bg-white"></div>
                <span class="text-[13px] font-bold text-slate-600">A. To uniquely identify a record in a table</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full border border-slate-300"></div>
                <span class="text-[13px] font-medium text-slate-600">B. To encrypt data in a table</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full border border-slate-300"></div>
                <span class="text-[13px] font-medium text-slate-600">C. To define the relationship between tables</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full border border-slate-300"></div>
                <span class="text-[13px] font-medium text-slate-600">D. To sort data in ascending order</span>
              </div>
            </div>
            
            <p class="text-[12px] font-bold text-slate-500 mb-1">Instruction</p>
            <p class="text-[13px] text-slate-600">Choose the correct answer.</p>
          </div>

          <!-- Question 3 -->
          <div class="border border-slate-100 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-[#5138ed] font-bold text-[13px] flex items-center justify-center">Q3</span>
                <span class="text-[12px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1.5 rounded-lg">True/False</span>
              </div>
              <span class="text-[12px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1.5 rounded-lg">2 Marks</span>
            </div>
            
            <p class="text-[12px] font-bold text-slate-500 mb-2">Question</p>
            <p class="text-[14px] font-bold text-slate-800 mb-5">Normalization is the process of organizing data to reduce redundancy.</p>
            
            <p class="text-[12px] font-bold text-slate-500 mb-3">Answer</p>
            <div class="flex items-center gap-8 mb-6">
              <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full border-[5px] border-[#5138ed] bg-white"></div>
                <span class="text-[13px] font-bold text-slate-800">True</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-4 h-4 rounded-full border border-slate-300"></div>
                <span class="text-[13px] font-bold text-slate-800">False</span>
              </div>
            </div>
            
            <p class="text-[12px] font-bold text-slate-500 mb-1">Instruction</p>
            <p class="text-[13px] text-slate-600">Determine whether the statement is True or False.</p>
          </div>

          <!-- Question 4 -->
          <div class="border border-slate-100 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-[#5138ed] font-bold text-[13px] flex items-center justify-center">Q4</span>
                <span class="text-[12px] font-bold text-amber-600 bg-amber-50 px-2.5 py-1.5 rounded-lg">Short Answer</span>
              </div>
              <span class="text-[12px] font-bold text-amber-600 bg-amber-50 px-2.5 py-1.5 rounded-lg">5 Marks</span>
            </div>
            
            <p class="text-[12px] font-bold text-slate-500 mb-2">Question</p>
            <p class="text-[14px] font-bold text-slate-800 mb-5">Explain the difference between DELETE and TRUNCATE statements.</p>
            
            <p class="text-[12px] font-bold text-slate-500 mb-1">Instruction</p>
            <p class="text-[13px] text-slate-600 mb-4">Write your answer in the space provided. Your answer should be clear and concise.</p>
            
            <span class="inline-block text-[12px] font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-lg">Expected Answer Length: 50 - 100 words</span>
          </div>

          <!-- Question 5 -->
          <div class="border border-slate-100 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-[#5138ed] font-bold text-[13px] flex items-center justify-center">Q5</span>
                <span class="text-[12px] font-bold text-sky-600 bg-sky-50 px-2.5 py-1.5 rounded-lg">Matching</span>
              </div>
              <span class="text-[12px] font-bold text-sky-600 bg-sky-50 px-2.5 py-1.5 rounded-lg">5 Marks</span>
            </div>
            
            <p class="text-[12px] font-bold text-slate-500 mb-2">Question</p>
            <p class="text-[14px] font-bold text-slate-800 mb-5">Match the SQL clause with its correct description.</p>
            
            <div class="grid grid-cols-2 gap-8 mb-6">
              <div>
                <p class="text-[13px] font-bold text-slate-800 mb-3">SQL Clause</p>
                <div class="space-y-2">
                  <p class="text-[13px] text-slate-600 font-medium">1.<span class="ml-2">WHERE</span></p>
                  <p class="text-[13px] text-slate-600 font-medium">2.<span class="ml-2">GROUP BY</span></p>
                  <p class="text-[13px] text-slate-600 font-medium">3.<span class="ml-2">HAVING</span></p>
                  <p class="text-[13px] text-slate-600 font-medium">4.<span class="ml-2">ORDER BY</span></p>
                </div>
              </div>
              <div>
                <p class="text-[13px] font-bold text-slate-800 mb-3">Description</p>
                <div class="space-y-2">
                  <p class="text-[13px] text-slate-600 font-medium">A.<span class="ml-2">Filters groups based on a condition</span></p>
                  <p class="text-[13px] text-slate-600 font-medium">B.<span class="ml-2">Orders the result set</span></p>
                  <p class="text-[13px] text-slate-600 font-medium">C.<span class="ml-2">Groups rows that have the same values</span></p>
                  <p class="text-[13px] text-slate-600 font-medium">D.<span class="ml-2">Filters rows based on a condition</span></p>
                </div>
              </div>
            </div>
            
            <p class="text-[12px] font-bold text-slate-500 mb-1">Instruction</p>
            <p class="text-[13px] text-slate-600">Match each item in the first column with the most appropriate item in the second column.</p>
          </div>

          <!-- Question 6 -->
          <div class="border border-slate-100 rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-[#5138ed] font-bold text-[13px] flex items-center justify-center">Q6</span>
                <span class="text-[12px] font-bold text-[#5138ed] bg-indigo-50 px-2.5 py-1.5 rounded-lg">Fill in the Blanks</span>
              </div>
              <span class="text-[12px] font-bold text-[#5138ed] bg-indigo-50 px-2.5 py-1.5 rounded-lg">5 Marks</span>
            </div>
            
            <p class="text-[12px] font-bold text-slate-500 mb-2">Question</p>
            <p class="text-[14px] font-bold text-slate-800 mb-5">The ________ clause is used to retrieve specific columns from a table.</p>
            
            <p class="text-[12px] font-bold text-slate-500 mb-1">Instruction</p>
            <p class="text-[13px] text-slate-600 mb-4">Fill in the blank with the most appropriate term.</p>
            
            <div class="flex items-center gap-2">
              <p class="text-[13px] font-medium text-slate-600">Expected Answer:</p>
              <span class="inline-block text-[12px] font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-lg">SELECT</span>
            </div>
          </div>
        </div>

        <div class="text-center mt-10">
          <p class="text-[13px] text-slate-500 font-medium">Showing 1 to 6 of 40 questions</p>
        </div>
      </div>
    </template>

    <!-- ══════════════════════════ LIST VIEW ══════════════════════════ -->
    <template v-else>
      <!-- Header -->
    <div class="flex items-start justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Exams</h1>
        <p class="text-[13px] text-slate-500 mt-1">Manage and monitor department exams.</p>
      </div>
      <div class="flex flex-col items-end">
        <div class="flex items-center gap-2 text-[13px] font-semibold text-slate-700">
          <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          May 27, 2026
        </div>
        <p class="text-[12px] text-slate-500 mt-0.5">Tuesday</p>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-4 gap-6">
      <div v-for="s in stats" :key="s.label" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 flex items-center gap-5 hover:shadow-md transition-shadow">
        <div :class="[s.bg, 'w-14 h-14 rounded-2xl flex items-center justify-center shrink-0']">
          <svg class="w-6 h-6" :class="s.ic" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="s.icon"></path></svg>
        </div>
        <div>
          <p class="text-[12px] font-semibold text-slate-500">{{ s.label }}</p>
          <p class="text-[24px] font-bold text-slate-800 leading-tight mt-0.5">{{ s.value }}</p>
          <p :class="[s.color, 'text-[11px] font-bold mt-1']">{{ s.change }}</p>
        </div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
      
      <!-- Filter Row -->
      <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100">
        <!-- Search -->
        <div class="relative flex-1 max-w-sm">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input v-model="search" type="text" placeholder="Search exams by title, course or code..." class="w-full pl-9 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400">
        </div>

        <div class="relative">
          <select v-model="semesterFilter" class="pl-4 pr-8 py-2.5 text-[13px] font-medium border border-slate-200 rounded-xl text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed]">
            <option value="all">All Semesters</option>
          </select>
          <svg class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>

        <div class="relative">
          <select v-model="yearFilter" class="pl-4 pr-8 py-2.5 text-[13px] font-medium border border-slate-200 rounded-xl text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed]">
            <option value="all">All Years</option>
          </select>
          <svg class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>

        <div class="relative">
          <select v-model="examTypeFilter" class="pl-4 pr-8 py-2.5 text-[13px] font-medium border border-slate-200 rounded-xl text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed]">
            <option value="all">All Exam Types</option>
          </select>
          <svg class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>

        <div class="relative">
          <select v-model="statusFilter" class="pl-4 pr-8 py-2.5 text-[13px] font-medium border border-slate-200 rounded-xl text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed]">
            <option value="all">All Status</option>
          </select>
          <svg class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>

        <!-- Filter Button -->
        <button class="flex items-center gap-2 text-[13px] font-bold text-[#5138ed] border border-indigo-200 hover:bg-indigo-50 px-4 py-2.5 rounded-xl transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
          Filter
        </button>

      </div>

      <!-- Table -->
      <table class="w-full">
        <thead>
          <tr class="border-b border-slate-100">
            <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Exam Title</th>
            <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course</th>
            <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Exam Type</th>
            <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Date & Time</th>
            <th class="text-center px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Duration</th>
            <th class="text-center px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Questions</th>
            <th class="text-center px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Marks</th>
            <th class="text-center px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
            <th class="text-center px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <tr v-for="exam in paginated" :key="exam.id" class="hover:bg-slate-50/40 transition-colors group">
            <td class="px-6 py-4">
              <span class="block text-[13px] font-bold text-slate-800">{{ exam.title }}</span>
              <span class="block text-[11px] font-medium text-slate-400 mt-0.5">{{ exam.code }}</span>
            </td>
            <td class="px-4 py-4">
              <span class="block text-[13px] font-semibold text-slate-700">{{ exam.courseName }}</span>
              <span class="block text-[11px] font-medium text-slate-400 mt-0.5">{{ exam.courseCode }}</span>
            </td>
            <td class="px-4 py-4">
              <span :class="[typeBadge(exam.type), 'text-[11px] font-bold px-2.5 py-1 rounded-md']">{{ exam.type }}</span>
            </td>
            <td class="px-4 py-4">
              <div class="flex items-start gap-2">
                <svg class="w-4 h-4 text-slate-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <div>
                  <span class="block text-[12px] font-bold text-slate-700">{{ exam.date }}</span>
                  <span class="block text-[11px] font-medium text-slate-500 mt-0.5">{{ exam.time }}</span>
                </div>
              </div>
            </td>
            <td class="px-4 py-4 text-center">
              <span class="text-[12px] font-semibold text-slate-700">{{ exam.duration }}</span>
            </td>
            <td class="px-4 py-4 text-center">
              <span class="text-[13px] font-semibold text-slate-700">{{ exam.questions }}</span>
            </td>
            <td class="px-4 py-4 text-center">
              <span class="text-[13px] font-semibold text-slate-700">{{ exam.marks }}</span>
            </td>
            <td class="px-4 py-4 text-center">
              <span :class="[statusBadge(exam.status), 'text-[11px] font-bold px-2.5 py-1 rounded-md capitalize']">{{ exam.status }}</span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-center gap-2">
                <button @click="openDetail(exam)" class="w-7 h-7 rounded-lg flex items-center justify-center text-[#5138ed] bg-indigo-50 hover:bg-indigo-100 transition-colors" title="View Details">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="flex items-center justify-between px-6 py-5 border-t border-slate-100 bg-white">
        <p class="text-[13px] text-slate-500 font-medium">
          Showing 1 to 8 of 68 exams
        </p>
        <div class="flex items-center gap-2">
          <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-slate-50 disabled:opacity-40 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
          </button>
          <template v-for="p in displayPages" :key="p">
            <span v-if="p === '...'" class="w-8 h-8 flex items-center justify-center text-slate-400 text-[13px]">...</span>
            <button v-else @click="currentPage = (p as number)" :class="[currentPage === p ? 'bg-[#5138ed] text-white border border-[#5138ed]' : 'text-slate-500 border border-slate-200 hover:bg-slate-50', 'w-8 h-8 rounded-lg text-[13px] font-bold transition-colors']">{{ p }}</button>
          </template>
          <button @click="currentPage = Math.min(9, currentPage + 1)" :disabled="currentPage === 9" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-slate-50 disabled:opacity-40 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </button>
        </div>
      </div>

    </div>

    </template>

  </div>
</template>
