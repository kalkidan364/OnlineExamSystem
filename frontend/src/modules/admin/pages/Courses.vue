<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import apiClient from '../../../core/api/apiClient'

const search = ref('')
const deptFilter = ref('all')
const showAddModal = ref(false)
const showDeleteModal = ref(false)
const showAssignModal = ref(false)
const selectedCourse = ref<any>(null)
const courseToAssign = ref<any>(null)
const assignInstructorId = ref('')
const isLoading = ref(false)

const allCourses = ref<any[]>([])
const allDepartments = ref<any[]>([])
const allInstructors = ref<any[]>([])

const newCourse = ref({ name: '', code: '', department_id: '', instructor_id: '' })

const fetchCourses = async () => {
  try {
    const res = await apiClient.get('/admin/courses')
    allCourses.value = (res.data.data || []).map((c: any) => ({
      ...c,
      name: c.title,
      dept: c.department?.name || '—',
      departmentName: c.department?.name || '—',
      instructor: c.instructor?.name || 'Unassigned',
      students: 0, // Not available in current schema directly
      exams: 0,
      status: c.status || 'active'
    }))
  } catch (err) { console.error('Failed to fetch courses:', err) }
}

const fetchDepartments = async () => {
  try {
    const res = await apiClient.get('/admin/departments')
    allDepartments.value = res.data.data || []
  } catch (err) { console.error('Failed to fetch departments:', err) }
}

const fetchInstructors = async () => {
  try {
    const res = await apiClient.get('/admin/users?role=instructor')
    allInstructors.value = res.data.data || []
  } catch (err) { console.error('Failed to fetch instructors:', err) }
}

onMounted(async () => {
  await Promise.all([fetchCourses(), fetchDepartments(), fetchInstructors()])
})

const availableInstructors = computed(() => {
  if (!newCourse.value.department_id) return []
  return allInstructors.value.filter(i => i.department_id === newCourse.value.department_id)
})

const instructorsForAssign = computed(() => {
  if (!courseToAssign.value?.department_id) return []
  return allInstructors.value.filter(i => i.department_id === courseToAssign.value.department_id)
})

const filtered = computed(() =>
  allCourses.value.filter(c => {
    const matchSearch = (c.name && c.name.toLowerCase().includes(search.value.toLowerCase())) ||
                        (c.code && c.code.toLowerCase().includes(search.value.toLowerCase())) ||
                        (c.instructor && c.instructor.toLowerCase().includes(search.value.toLowerCase()))
    const matchDept = deptFilter.value === 'all' || c.departmentName === deptFilter.value
    return matchSearch && matchDept
  })
)

const stats = computed(() => ({
  total:    allCourses.value.length,
  active:   allCourses.value.filter(c => c.status === 'active').length,
  students: allCourses.value.reduce((a, c) => a + (c.students || 0), 0),
  exams:    allCourses.value.reduce((a, c) => a + (c.exams || 0), 0),
}))

const deptColor = (dept: string) => {
  const map: Record<string, string> = {
    'Computer Science': 'bg-indigo-50 text-indigo-700', 'Mathematics': 'bg-sky-50 text-sky-700',
    'Physics': 'bg-amber-50 text-amber-700', 'Engineering': 'bg-emerald-50 text-emerald-700',
    'Information Systems': 'bg-violet-50 text-violet-700',
  }
  return map[dept] || 'bg-slate-100 text-slate-600'
}

const addCourse = async () => {
  if (!newCourse.value.name || !newCourse.value.code || !newCourse.value.department_id) return
  isLoading.value = true
  try {
    await apiClient.post('/admin/courses', {
      title: newCourse.value.name,
      code: newCourse.value.code,
      credits: 3, // Defaulting to 3 for now
      department_id: newCourse.value.department_id,
      instructor_id: newCourse.value.instructor_id || null,
    })
    await fetchCourses()
    showAddModal.value = false
  } catch (err: any) {
    let msg = 'Failed to create course.'
    if (err.response?.data?.message) {
      msg = err.response.data.message
      if (err.response.data.errors) {
        msg += '\n' + Object.values(err.response.data.errors).flat().join('\n')
      }
    }
    alert(msg)
  } finally {
    isLoading.value = false
  }
}

const confirmDelete = (c: any) => { selectedCourse.value = c; showDeleteModal.value = true }
const deleteCourse  = async () => {
  if (!selectedCourse.value) return
  isLoading.value = true
  try {
    await apiClient.delete(`/admin/courses/${selectedCourse.value.id}`)
    await fetchCourses()
    showDeleteModal.value = false
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete course.')
  } finally {
    isLoading.value = false
  }
}
const toggleStatus  = async (c: any) => {
  const newStatus = c.status === 'active' ? 'inactive' : 'active'
  try {
    await apiClient.put(`/admin/courses/${c.id}`, { status: newStatus })
    c.status = newStatus
  } catch (err) {
    alert('Failed to update course status.')
  }
}

const openAssign = (course: any) => {
  courseToAssign.value = course
  // if course has an instructor, it should be course.instructor_id. Wait, `course` object might not have `instructor_id` mapped correctly.
  // Wait, in fetchCourses we mapped `instructor: c.instructor?.name`, but we didn't keep instructor_id.
  assignInstructorId.value = course.instructor_id || ''
  showAssignModal.value = true
}

const assignInstructor = async () => {
  if (!courseToAssign.value) return
  isLoading.value = true
  try {
    await apiClient.put(`/admin/courses/${courseToAssign.value.id}`, {
      instructor_id: assignInstructorId.value || null
    })
    await fetchCourses()
    showAssignModal.value = false
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to assign instructor.')
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div><h1 class="text-[22px] font-bold text-slate-800">Course Management</h1><p class="text-[13px] text-slate-500 mt-1">Manage courses, departments, and instructor assignments.</p></div>
      <button @click="showAddModal = true; newCourse = {name:'',code:'',department_id:'',instructor_id:''}" class="flex items-center gap-2 bg-[#5138ed] hover:bg-indigo-700 text-white text-[13px] font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-indigo-200 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add Course
      </button>
    </div>

    <div class="grid grid-cols-4 gap-4">
      <div v-for="(item, i) in [
        { label:'Total Courses', val: stats.total,    icon:'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', bg:'bg-indigo-50', ic:'text-[#5138ed]' },
        { label:'Active',        val: stats.active,   icon:'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                bg:'bg-emerald-50',ic:'text-emerald-500'},
        { label:'Total Students',val: stats.students, icon:'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', bg:'bg-sky-50', ic:'text-sky-500'},
        { label:'Total Exams',   val: stats.exams,    icon:'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', bg:'bg-amber-50', ic:'text-amber-500'},
      ]" :key="i" class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div :class="[item.bg, 'w-11 h-11 rounded-xl flex items-center justify-center shrink-0']">
          <svg class="w-5 h-5" :class="item.ic" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"></path></svg>
        </div>
        <div><p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">{{ item.label }}</p><p class="text-[22px] font-bold text-slate-800">{{ item.val }}</p></div>
      </div>
    </div>

    <!-- Course Grid -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
      <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-slate-100">
        <div class="relative flex-1 max-w-sm">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <input v-model="search" placeholder="Search courses or instructors..." class="w-full pl-9 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
        </div>
        <div class="flex items-center gap-3">
          <select v-model="deptFilter" class="text-[13px] border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white">
            <option value="all">All Departments</option>
            <option v-for="d in allDepartments" :key="d.id" :value="d.name">{{ d.name }}</option>
          </select>
          <span class="text-[12px] font-semibold text-slate-400">{{ filtered.length }} courses</span>
        </div>
      </div>

      <div class="p-6 grid grid-cols-2 xl:grid-cols-3 gap-4">
        <div v-for="course in filtered" :key="course.id" class="border border-slate-100 rounded-2xl p-5 hover:border-indigo-200 hover:shadow-md transition-all group">
          <div class="flex items-start justify-between mb-4">
            <div>
              <span class="text-[10px] font-black font-mono text-slate-400 uppercase tracking-widest">{{ course.code }}</span>
              <h3 class="text-[14px] font-bold text-slate-800 mt-0.5 leading-snug">{{ course.name }}</h3>
            </div>
            <span :class="[course.status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400', 'text-[10px] font-bold px-2 py-0.5 rounded-lg capitalize shrink-0 ml-2']">{{ course.status }}</span>
          </div>
          <div class="space-y-2 mb-4">
            <div class="flex items-center gap-2"><span :class="[deptColor(course.departmentName), 'text-[11px] font-bold px-2.5 py-1 rounded-lg']">{{ course.departmentName }}</span></div>
            <div class="flex items-center gap-2 text-[12px] text-slate-500"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg><span class="font-semibold">{{ course.instructor }}</span></div>
          </div>
          <div class="grid grid-cols-2 gap-2 mb-4">
            <div class="bg-indigo-50 rounded-xl p-3 text-center"><p class="text-[16px] font-bold text-[#5138ed]">{{ course.students }}</p><p class="text-[10px] font-semibold text-indigo-400">Students</p></div>
            <div class="bg-amber-50 rounded-xl p-3 text-center"><p class="text-[16px] font-bold text-amber-600">{{ course.exams }}</p><p class="text-[10px] font-semibold text-amber-400">Exams</p></div>
          </div>
          <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <button @click="toggleStatus(course)" :class="[course.status === 'active' ? 'text-amber-600 bg-amber-50 hover:bg-amber-100' : 'text-emerald-600 bg-emerald-50 hover:bg-emerald-100', 'flex-1 py-2 text-[11px] font-bold rounded-lg transition-colors']">{{ course.status === 'active' ? 'Deactivate' : 'Activate' }}</button>
            <button @click="openAssign(course)" class="flex-1 py-2 text-[11px] font-bold text-[#5138ed] bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">Assign</button>
            <button @click="confirmDelete(course)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
          </div>
        </div>
        <div v-if="filtered.length === 0" class="col-span-3 py-16 text-center">
          <p class="text-[14px] font-bold text-slate-600">No courses found</p>
          <p class="text-[12px] text-slate-400 mt-1">Try adjusting your search or filters.</p>
        </div>
      </div>
    </div>

    <!-- Add Modal -->
    <Teleport to="body">
      <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
          <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <div><h3 class="text-[16px] font-bold text-slate-800">Add New Course</h3><p class="text-[12px] text-slate-500 mt-0.5">Fill in course details and assign an instructor.</p></div>
            <button @click="showAddModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
          </div>
          <div class="px-6 py-5 space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Course Name <span class="text-rose-500">*</span></label><input v-model="newCourse.name" type="text" placeholder="e.g. Database Systems" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
              <div><label class="block text-[12px] font-bold text-slate-700 mb-1.5">Course Code <span class="text-rose-500">*</span></label><input v-model="newCourse.code" type="text" placeholder="e.g. CS-301" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] font-mono focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Assign to Department <span class="text-rose-500">*</span></label>
              <select v-model="newCourse.department_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] bg-white">
                <option value="" disabled>Select department</option>
                <option v-for="d in allDepartments" :key="d.id" :value="d.id">{{ d.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-1.5">Assigned Instructor</label>
              <select v-model="newCourse.instructor_id" :disabled="!newCourse.department_id" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] bg-white disabled:bg-slate-50 disabled:text-slate-400">
                <option value="" disabled>{{ newCourse.department_id ? 'Select instructor' : 'Select a department first' }}</option>
                <option v-for="inst in availableInstructors" :key="inst.id" :value="inst.id">{{ inst.name }}</option>
              </select>
            </div>
          </div>
          <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            <button @click="showAddModal = false" class="px-5 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">Cancel</button>
            <button @click="addCourse" class="px-5 py-2.5 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl shadow-sm transition-all">Add Course</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Delete Modal -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
          <div class="p-6 text-center">
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg></div>
            <h3 class="text-[16px] font-bold text-slate-800 mb-2">Delete Course?</h3>
            <p class="text-[13px] text-slate-500">Delete <span class="font-bold text-slate-700">{{ selectedCourse?.name }}</span>? This cannot be undone.</p>
          </div>
          <div class="flex items-center gap-3 px-6 pb-6">
            <button @click="showDeleteModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="deleteCourse" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors">Delete</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Assign Modal -->
    <Teleport to="body">
      <div v-if="showAssignModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
          <div class="p-6">
            <h3 class="text-[16px] font-bold text-slate-800 mb-2">Assign Instructor</h3>
            <p class="text-[13px] text-slate-500 mb-4">Select an instructor for <span class="font-bold text-slate-700">{{ courseToAssign?.name }}</span>.</p>
            <select v-model="assignInstructorId" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] bg-white">
              <option value="">Unassigned</option>
              <option v-for="inst in instructorsForAssign" :key="inst.id" :value="inst.id">{{ inst.name }}</option>
            </select>
          </div>
          <div class="flex items-center gap-3 px-6 pb-6">
            <button @click="showAssignModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="assignInstructor" :disabled="isLoading" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-600 rounded-xl transition-colors disabled:opacity-70">Save</button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>
