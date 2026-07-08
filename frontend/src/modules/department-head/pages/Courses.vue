<script setup lang="ts">
import { ref, computed } from 'vue'

const deptName = 'Computer Science'

const search = ref('')
const statusFilter = ref('all')
const showAddModal = ref(false)
const showDeleteModal = ref(false)
const selectedCourse = ref<any>(null)

// ── Dummy Data ──
const allCourses = ref([
  { id: 1, title: 'Database Systems',      code: 'CS-301', instructor: 'Dr. Abebe Kebede',    credits: 4, students: 142, status: 'active' },
  { id: 2, title: 'Software Engineering',  code: 'CS-401', instructor: 'Prof. Yonas Tadesse', credits: 4, students: 167, status: 'active' },
  { id: 3, title: 'Algorithms',            code: 'CS-202', instructor: 'Dr. Meron Bekele',    credits: 3, students: 134, status: 'active' },
  { id: 4, title: 'Operating Systems',     code: 'CS-302', instructor: 'Dr. Samuel Getachew', credits: 4, students: 95,  status: 'active' },
  { id: 5, title: 'Artificial Intelligence',code:'CS-405', instructor: 'Prof. Helen Assefa',  credits: 3, students: 76,  status: 'inactive'},
])

// ── New Course Form ──
const newCourse = ref({ title: '', code: '', instructor: '', credits: 3 })

const filtered = computed(() => {
  return allCourses.value.filter(c => {
    const matchSearch = c.title.toLowerCase().includes(search.value.toLowerCase()) || c.code.toLowerCase().includes(search.value.toLowerCase())
    const matchStatus = statusFilter.value === 'all' || c.status === statusFilter.value
    return matchSearch && matchStatus
  })
})

const stats = computed(() => ({
  total: allCourses.value.length,
  active: allCourses.value.filter(c => c.status === 'active').length,
  totalStudents: allCourses.value.reduce((acc, curr) => acc + curr.students, 0)
}))

// ── Actions ──
const openAdd = () => { newCourse.value = { title:'', code:'', instructor:'', credits:3 }; showAddModal.value = true }
const addCourse = () => {
  if (!newCourse.value.title || !newCourse.value.code) return
  allCourses.value.push({ id: Date.now(), ...newCourse.value, students: 0, status: 'active' })
  showAddModal.value = false
}
const confirmDelete = (course: any) => { selectedCourse.value = course; showDeleteModal.value = true }
const deleteCourse = () => {
  allCourses.value = allCourses.value.filter(c => c.id !== selectedCourse.value.id)
  showDeleteModal.value = false
}
const toggleStatus = (course: any) => { course.status = course.status === 'active' ? 'inactive' : 'active' }

const getCourseColor = (id: number) => {
  const c = ['bg-indigo-50 text-indigo-700','bg-emerald-50 text-emerald-700','bg-amber-50 text-amber-700','bg-rose-50 text-rose-700','bg-sky-50 text-sky-700']
  return c[id % c.length]
}
</script>

<template>
  <div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Department Courses</h1>
        <p class="text-[13px] text-slate-500 mt-1">Manage courses and assignments for {{ deptName }}.</p>
      </div>
      <button @click="openAdd" class="flex items-center gap-2 bg-[#5138ed] hover:bg-indigo-700 text-white text-[13px] font-bold px-5 py-2.5 rounded-xl shadow-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add Course
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4">
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0"><svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg></div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase">Total Courses</p><p class="text-[22px] font-bold text-slate-800">{{ stats.total }}</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0"><svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase">Active Courses</p><p class="text-[22px] font-bold text-slate-800">{{ stats.active }}</p></div>
      </div>
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 bg-sky-50 rounded-xl flex items-center justify-center shrink-0"><svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase">Total Enrolled</p><p class="text-[22px] font-bold text-slate-800">{{ stats.totalStudents }}</p></div>
      </div>
    </div>

    <!-- Cards Grid -->
    <div class="flex items-center justify-between gap-4 py-2">
      <div class="relative w-96">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        <input v-model="search" type="text" placeholder="Search courses..." class="w-full pl-9 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-xl bg-white focus:border-[#5138ed]">
      </div>
      <select v-model="statusFilter" class="text-[13px] border border-slate-200 rounded-xl px-4 py-2.5 bg-white focus:border-[#5138ed] text-slate-600">
        <option value="all">All Status</option><option value="active">Active</option><option value="inactive">Inactive</option>
      </select>
    </div>

    <div class="grid grid-cols-3 gap-6">
      <div v-for="course in filtered" :key="course.id" class="bg-white border border-slate-100 rounded-2xl p-6 hover:shadow-md hover:border-indigo-100 transition-all group relative">
        <div class="flex items-start justify-between mb-4">
          <div :class="[getCourseColor(course.id), 'w-12 h-12 rounded-2xl flex items-center justify-center shrink-0']">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
          </div>
          <button class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-50 hover:text-[#5138ed] opacity-0 group-hover:opacity-100 transition-opacity">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
          </button>
        </div>
        <h3 class="text-[15px] font-bold text-slate-800 leading-snug mb-1">{{ course.title }}</h3>
        <p class="text-[12px] font-mono font-bold text-slate-400 mb-4">{{ course.code }}</p>
        <div class="space-y-2 mb-6">
          <div class="flex items-center justify-between text-[12px]">
            <span class="text-slate-500">Instructor:</span><span class="font-bold text-slate-700">{{ course.instructor || 'Unassigned' }}</span>
          </div>
          <div class="flex items-center justify-between text-[12px]">
            <span class="text-slate-500">Credits:</span><span class="font-bold text-slate-700">{{ course.credits }}</span>
          </div>
          <div class="flex items-center justify-between text-[12px]">
            <span class="text-slate-500">Students:</span><span class="font-bold text-slate-700">{{ course.students }}</span>
          </div>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
          <span :class="[course.status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500', 'text-[10px] font-bold px-2 py-1 rounded-md capitalize']">{{ course.status }}</span>
          <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <button @click="toggleStatus(course)" class="w-7 h-7 rounded bg-slate-50 text-slate-400 hover:text-amber-500 flex items-center justify-center"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg></button>
            <button @click="confirmDelete(course)" class="w-7 h-7 rounded bg-slate-50 text-slate-400 hover:text-rose-500 flex items-center justify-center"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Add Course Modal ── -->
    <Teleport to="body">
      <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
          <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div><h3 class="text-[16px] font-bold text-slate-800">Add New Course</h3><p class="text-[12px] text-slate-500 mt-0.5">Create a course for {{ deptName }}.</p></div>
            <button @click="showAddModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
          </div>
          <div class="px-6 py-5 space-y-4">
            <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Course Title <span class="text-rose-500">*</span></label><input v-model="newCourse.title" type="text" placeholder="e.g. Database Systems" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:border-[#5138ed]"></div>
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Course Code <span class="text-rose-500">*</span></label><input v-model="newCourse.code" type="text" placeholder="e.g. CS-301" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] font-mono focus:border-[#5138ed]"></div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Credits</label><input v-model="newCourse.credits" type="number" min="1" max="6" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:border-[#5138ed]"></div>
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Assign Instructor</label>
              <select v-model="newCourse.instructor" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:border-[#5138ed] bg-white">
                <option value="">Unassigned</option>
                <option>Dr. Abebe Kebede</option>
                <option>Prof. Yonas Tadesse</option>
                <option>Dr. Meron Bekele</option>
              </select>
            </div>
          </div>
          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            <button @click="showAddModal = false" class="px-5 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100">Cancel</button>
            <button @click="addCourse" class="px-5 py-2.5 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl shadow-sm">Create Course</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- ── Delete Modal ── -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6">
          <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg></div>
          <h3 class="text-[16px] font-bold text-slate-800 mb-2">Delete Course?</h3>
          <p class="text-[13px] text-slate-500 mb-6">Are you sure you want to delete <span class="font-bold text-slate-700">{{ selectedCourse?.title }}</span>?</p>
          <div class="flex gap-3"><button @click="showDeleteModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50">Cancel</button><button @click="deleteCourse" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl">Delete</button></div>
        </div>
      </div>
    </Teleport>

  </div>
</template>
