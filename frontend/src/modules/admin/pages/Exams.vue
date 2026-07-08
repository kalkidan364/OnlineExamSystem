<script setup lang="ts">
import { ref, computed } from 'vue'

const search = ref('')
const statusFilter = ref('all')
const currentPage = ref(1)
const perPage = 8
const showDeleteModal = ref(false)
const selectedExam = ref<any>(null)

const allExams = ref([
  { id: 1,  title: 'Database Systems Midterm',    course: 'Database Systems',     code:'CS-301', instructor: 'Dr. Abebe Kebede',    students: 98,  duration: 90,  totalMarks: 100, status: 'completed', date: 'May 27, 2025', passRate: 82 },
  { id: 2,  title: 'Data Structures Quiz 3',      course: 'Data Structures',      code:'IS-201', instructor: 'Prof. Dawit Solomon', students: 72,  duration: 45,  totalMarks: 50,  status: 'completed', date: 'May 26, 2025', passRate: 78 },
  { id: 3,  title: 'Software Engineering Final',  course: 'Software Engineering', code:'CS-401', instructor: 'Prof. Yonas Tadesse', students: 110, duration: 120, totalMarks: 100, status: 'completed', date: 'May 25, 2025', passRate: 91 },
  { id: 4,  title: 'Web Development Quiz 2',      course: 'Web Development',      code:'IS-302', instructor: 'Prof. Rahel Tesfaye', students: 88,  duration: 60,  totalMarks: 60,  status: 'completed', date: 'May 24, 2025', passRate: 74 },
  { id: 5,  title: 'Operating Systems Midterm',   course: 'Computer Networks',    code:'EN-302', instructor: 'Dr. Tigist Haile',    students: 55,  duration: 90,  totalMarks: 100, status: 'completed', date: 'May 23, 2025', passRate: 68 },
  { id: 6,  title: 'Algorithms Final Exam',       course: 'Algorithms',           code:'CS-202', instructor: 'Dr. Meron Bekele',    students: 120, duration: 180, totalMarks: 100, status: 'active',    date: 'Jun 10, 2025', passRate: 0  },
  { id: 7,  title: 'Calculus Semester Exam',      course: 'Calculus & Analysis',  code:'MT-201', instructor: 'Dr. Fikirte Girma',   students: 76,  duration: 120, totalMarks: 100, status: 'scheduled', date: 'Jun 15, 2025', passRate: 0  },
  { id: 8,  title: 'Linear Algebra Quiz 1',       course: 'Linear Algebra',       code:'MT-301', instructor: 'Prof. Sara Teferra',  students: 62,  duration: 45,  totalMarks: 50,  status: 'scheduled', date: 'Jun 18, 2025', passRate: 0  },
  { id: 9,  title: 'Applied Physics Lab Test',    course: 'Applied Physics',      code:'PH-101', instructor: 'Dr. Tewodros Abay',   students: 48,  duration: 60,  totalMarks: 50,  status: 'draft',     date: 'Jun 20, 2025', passRate: 0  },
  { id: 10, title: 'Digital Electronics Midterm', course: 'Digital Electronics',  code:'EN-201', instructor: 'Dr. Berhane Alemu',   students: 66,  duration: 90,  totalMarks: 100, status: 'draft',     date: 'Jun 25, 2025', passRate: 0  },
])

const filtered = computed(() =>
  allExams.value.filter(e => {
    const matchSearch = e.title.toLowerCase().includes(search.value.toLowerCase()) ||
                        e.course.toLowerCase().includes(search.value.toLowerCase()) ||
                        e.instructor.toLowerCase().includes(search.value.toLowerCase())
    const matchStatus = statusFilter.value === 'all' || e.status === statusFilter.value
    return matchSearch && matchStatus
  })
)

const totalPages = computed(() => Math.ceil(filtered.value.length / perPage))
const paginated  = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const stats = computed(() => ({
  total:     allExams.value.length,
  active:    allExams.value.filter(e => e.status === 'active').length,
  scheduled: allExams.value.filter(e => e.status === 'scheduled').length,
  completed: allExams.value.filter(e => e.status === 'completed').length,
}))

const statusConfig: Record<string, { badge: string, dot: string, label: string }> = {
  active:    { badge: 'bg-emerald-50 text-emerald-700 border border-emerald-200', dot: 'bg-emerald-500', label: 'Active'    },
  completed: { badge: 'bg-indigo-50 text-indigo-700 border border-indigo-200',   dot: 'bg-indigo-400',  label: 'Completed' },
  scheduled: { badge: 'bg-amber-50 text-amber-700 border border-amber-200',      dot: 'bg-amber-400',   label: 'Scheduled' },
  draft:     { badge: 'bg-slate-100 text-slate-500 border border-slate-200',     dot: 'bg-slate-400',   label: 'Draft'     },
}

const confirmDelete = (exam: any) => { selectedExam.value = exam; showDeleteModal.value = true }
const deleteExam    = () => { allExams.value = allExams.value.filter(e => e.id !== selectedExam.value.id); showDeleteModal.value = false }
</script>

<template>
  <div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Exam Management</h1>
        <p class="text-[13px] text-slate-500 mt-1">Monitor and manage all exams across all courses system-wide.</p>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-4 gap-4">
      <div v-for="(s, i) in [
        { label:'Total Exams',  val: stats.total,     bg:'bg-indigo-50', ic:'text-[#5138ed]', icon:'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
        { label:'Active Now',   val: stats.active,    bg:'bg-emerald-50',ic:'text-emerald-500',icon:'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
        { label:'Scheduled',    val: stats.scheduled, bg:'bg-amber-50',  ic:'text-amber-500', icon:'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
        { label:'Completed',    val: stats.completed, bg:'bg-sky-50',    ic:'text-sky-500',   icon:'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
      ]" :key="i" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div :class="[s.bg, 'w-11 h-11 rounded-xl flex items-center justify-center shrink-0']">
          <svg class="w-5 h-5" :class="s.ic" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="s.icon"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">{{ s.label }}</p><p class="text-[22px] font-bold text-slate-800">{{ s.val }}</p></div>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
      <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-slate-100">
        <div class="relative flex-1 max-w-sm">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input v-model="search" placeholder="Search exams, courses or instructors..." class="w-full pl-9 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
        </div>
        <div class="flex items-center gap-3">
          <select v-model="statusFilter" class="text-[13px] border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="scheduled">Scheduled</option>
            <option value="completed">Completed</option>
            <option value="draft">Draft</option>
          </select>
          <span class="text-[12px] font-semibold text-slate-400">{{ filtered.length }} exams</span>
        </div>
      </div>

      <table class="w-full">
        <thead>
          <tr class="bg-slate-50/60 border-b border-slate-100">
            <th class="text-left px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Exam</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Instructor</th>
            <th class="text-center px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Students</th>
            <th class="text-center px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Duration</th>
            <th class="text-center px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pass Rate</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Date</th>
            <th class="text-left px-4 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
            <th class="text-right px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
          <tr v-for="exam in paginated" :key="exam.id" class="hover:bg-slate-50/40 transition-colors group">
            <td class="px-6 py-4">
              <div>
                <p class="text-[13px] font-bold text-slate-800 leading-snug">{{ exam.title }}</p>
                <div class="flex items-center gap-1.5 mt-0.5">
                  <span class="text-[10px] font-bold font-mono text-slate-400">{{ exam.code }}</span>
                  <span class="text-slate-300">·</span>
                  <span class="text-[11px] text-slate-400 font-medium">{{ exam.course }}</span>
                </div>
              </div>
            </td>
            <td class="px-4 py-4"><p class="text-[12px] font-semibold text-slate-600">{{ exam.instructor }}</p></td>
            <td class="px-4 py-4 text-center"><span class="text-[13px] font-bold text-slate-800">{{ exam.students }}</span></td>
            <td class="px-4 py-4 text-center"><span class="text-[12px] font-semibold text-slate-600">{{ exam.duration }}m</span></td>
            <td class="px-4 py-4">
              <div class="flex items-center justify-center gap-2">
                <template v-if="exam.status === 'completed'">
                  <div class="w-12 bg-slate-100 rounded-full h-1.5"><div class="h-1.5 rounded-full" :class="exam.passRate >= 80 ? 'bg-emerald-500' : exam.passRate >= 60 ? 'bg-amber-500' : 'bg-rose-500'" :style="{ width: exam.passRate + '%' }"></div></div>
                  <span class="text-[12px] font-bold" :class="exam.passRate >= 80 ? 'text-emerald-600' : exam.passRate >= 60 ? 'text-amber-600' : 'text-rose-600'">{{ exam.passRate }}%</span>
                </template>
                <span v-else class="text-[11px] text-slate-300 font-medium">—</span>
              </div>
            </td>
            <td class="px-4 py-4"><p class="text-[12px] text-slate-500 font-medium">{{ exam.date }}</p></td>
            <td class="px-4 py-4">
              <div class="flex items-center gap-1.5">
                <span :class="[statusConfig[exam.status]?.dot, 'w-1.5 h-1.5 rounded-full']"></span>
                <span :class="[statusConfig[exam.status]?.badge, 'text-[11px] font-bold px-2 py-0.5 rounded-md']">{{ statusConfig[exam.status]?.label }}</span>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>
                <button @click="confirmDelete(exam)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="paginated.length === 0">
            <td colspan="8" class="py-16 text-center">
              <div class="flex flex-col items-center gap-3">
                <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center"><svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg></div>
                <p class="text-[14px] font-bold text-slate-600">No exams found</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 bg-slate-50/30">
        <p class="text-[12px] text-slate-500 font-medium">Showing {{ Math.min((currentPage-1)*perPage+1, filtered.length) }}–{{ Math.min(currentPage*perPage, filtered.length) }} of {{ filtered.length }} exams</p>
        <div class="flex items-center gap-1">
          <button @click="currentPage = Math.max(1, currentPage-1)" :disabled="currentPage===1" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
          </button>
          <button v-for="p in totalPages" :key="p" @click="currentPage = p" :class="[currentPage===p?'bg-[#5138ed] text-white':'text-slate-500 hover:bg-slate-100','w-8 h-8 rounded-lg flex items-center justify-center text-[12px] font-bold transition-colors']">{{ p }}</button>
          <button @click="currentPage = Math.min(totalPages, currentPage+1)" :disabled="currentPage===totalPages" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
          <div class="p-6 text-center">
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg></div>
            <h3 class="text-[16px] font-bold text-slate-800 mb-2">Delete Exam?</h3>
            <p class="text-[13px] text-slate-500">Delete <span class="font-bold text-slate-700">{{ selectedExam?.title }}</span>? All student results will also be removed.</p>
          </div>
          <div class="flex items-center gap-3 px-6 pb-6">
            <button @click="showDeleteModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="deleteExam" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors">Delete</button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>
