<script setup lang="ts">
import { ref, computed } from 'vue'

// ── View State ──
const currentView = ref<'list' | 'add' | 'detail' | 'edit'>('list')

const search = ref('')
const statusFilter = ref('all')
const yearFilter = ref('all')
const sectionFilter = ref('all')
const currentPage = ref(1)
const perPage = 8

// ── Add Student Form ──
const addForm = ref({
  fullName: '',
  email: '',
  phone: '',
  dateOfBirth: '',
  gender: '',
  profilePicture: null as File | null,
  studentId: '',
  admissionNumber: '',
  department: '',
  year: '',
  semester: '',
  section: '',
})
const isLoading = ref(false)

const resetAddForm = () => {
  addForm.value = {
    fullName: '', email: '', phone: '', dateOfBirth: '', gender: '',
    profilePicture: null, studentId: '', admissionNumber: '', department: '',
    year: '', semester: '', section: ''
  }
}

const openAddPage = () => { resetAddForm(); currentView.value = 'add' }
const backToList  = () => { currentView.value = 'list' }

const handleFileUpload = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) addForm.value.profilePicture = target.files[0]
}

const saveStudent = () => {
  isLoading.value = true
  setTimeout(() => { isLoading.value = false; currentView.value = 'list' }, 1000)
}

// ── Detail & Delete & Edit ──
const selectedStudent = ref<any>(null)
const showDeleteModal = ref(false)

const openView = (stu: any) => {
  selectedStudent.value = stu
  currentView.value = 'detail'
}

const confirmDelete = (stu: any) => {
  selectedStudent.value = stu
  showDeleteModal.value = true
}

const deleteStudent = () => {
  isLoading.value = true
  setTimeout(() => {
    allStudents.value = allStudents.value.filter(s => s.id !== selectedStudent.value.id)
    showDeleteModal.value = false
    isLoading.value = false
  }, 1000)
}

// ── Edit Student Form ──
const editForm = ref({
  fullName: '',
  email: '',
  phone: '',
  dateOfBirth: '',
  gender: '',
  profilePicture: null as File | null,
  studentId: '',
  admissionNumber: '',
  department: '',
  year: '',
  semester: '',
  section: '',
})

const openEditPage = (stu: any) => {
  selectedStudent.value = stu
  editForm.value = {
    fullName: stu.name || '',
    email: stu.email || '',
    phone: '+251 9XX XXX XXX',
    dateOfBirth: '2002-01-12',
    gender: 'Male',
    profilePicture: null,
    studentId: stu.id || '',
    admissionNumber: 'ADM-' + (stu.id ? stu.id.split('-')[1] + '-' + stu.id.split('-')[2] : ''),
    department: 'Computer Science',
    year: stu.year || '',
    semester: 'Spring 2026',
    section: stu.section || '',
  }
  currentView.value = 'edit'
}

const handleEditFileUpload = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) editForm.value.profilePicture = target.files[0]
}

const saveEditStudent = () => {
  isLoading.value = true
  setTimeout(() => {
    isLoading.value = false
    currentView.value = 'list'
  }, 1000)
}

// ── Sample data ──
const allStudents = ref([
  { id: 'STU-2022-001', name: 'Nahom Gebreyesus', email: 'nahom.gebreyesus@wu.edu.et', section: 'Section A', year: '4th Year', status: 'active',   admissionDate: 'Sep 15, 2022', avatar: 'NG' },
  { id: 'STU-2022-002', name: 'Seiam Tesfaye',    email: 'seiam.tesfaye@wu.edu.et',    section: 'Section B', year: '4th Year', status: 'active',   admissionDate: 'Sep 15, 2022', avatar: 'ST' },
  { id: 'STU-2023-003', name: 'Yonas Mekonnen',   email: 'yonas.mekonnen@wu.edu.et',   section: 'Section A', year: '3rd Year', status: 'active',   admissionDate: 'Sep 10, 2023', avatar: 'YM' },
  { id: 'STU-2023-004', name: 'Hana Abebe',       email: 'hana.abebe@wu.edu.et',       section: 'Section C', year: '3rd Year', status: 'active',   admissionDate: 'Sep 10, 2023', avatar: 'HA' },
  { id: 'STU-2023-005', name: 'Abel Tadesse',     email: 'abel.tadesse@wu.edu.et',     section: 'Section B', year: '3rd Year', status: 'active',   admissionDate: 'Sep 10, 2023', avatar: 'AT' },
  { id: 'STU-2024-006', name: 'Betelhem Assefa',  email: 'betelhem.assefa@wu.edu.et',  section: 'Section A', year: '2nd Year', status: 'active',   admissionDate: 'Sep 09, 2024', avatar: 'BA' },
  { id: 'STU-2024-007', name: 'Dawit Alemu',      email: 'dawit.alemu@wu.edu.et',      section: 'Section D', year: '2nd Year', status: 'inactive', admissionDate: 'Sep 09, 2024', avatar: 'DA' },
  { id: 'STU-2024-008', name: 'Rahel Solomon',    email: 'rahel.solomon@wu.edu.et',    section: 'Section B', year: '2nd Year', status: 'active',   admissionDate: 'Sep 09, 2024', avatar: 'RS' },
  { id: 'STU-2022-009', name: 'Kalkidan Hailu',   email: 'kalkidan.hailu@wu.edu.et',   section: 'Section C', year: '4th Year', status: 'active',   admissionDate: 'Sep 15, 2022', avatar: 'KH' },
  { id: 'STU-2023-010', name: 'Tigist Bekele',    email: 'tigist.bekele@wu.edu.et',    section: 'Section A', year: '3rd Year', status: 'active',   admissionDate: 'Sep 10, 2023', avatar: 'TB' },
])

const filtered = computed(() => {
  return allStudents.value.filter(s => {
    const matchSearch  = s.name.toLowerCase().includes(search.value.toLowerCase()) ||
                         s.id.toLowerCase().includes(search.value.toLowerCase()) ||
                         s.email.toLowerCase().includes(search.value.toLowerCase())
    const matchStatus  = statusFilter.value === 'all' || s.status === statusFilter.value
    const matchYear    = yearFilter.value === 'all' || s.year === yearFilter.value
    const matchSection = sectionFilter.value === 'all' || s.section === sectionFilter.value
    return matchSearch && matchStatus && matchYear && matchSection
  })
})

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))
const paginated  = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const stats = computed(() => [
  { label: 'Total Students',    value: 524, change: '↑ 28 this semester', bg: 'bg-indigo-50', ic: 'text-[#5138ed]', color: 'text-emerald-500', icon: 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z' },
  { label: 'Active Students',   value: 498, change: '↑ 25 this semester', bg: 'bg-emerald-50', ic: 'text-emerald-500', color: 'text-emerald-500', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
  { label: 'New Students',      value: 63,  change: '↑ 8 this semester',  bg: 'bg-amber-50', ic: 'text-amber-500', color: 'text-emerald-500', icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z' },
  { label: 'Inactive Students', value: 26,  change: '↓ 3 this semester',  bg: 'bg-rose-50', ic: 'text-rose-500', color: 'text-rose-500', icon: 'M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6' },
])

const avatarColor = (name: string) => {
  const colors = ['bg-indigo-500','bg-sky-500','bg-emerald-500','bg-violet-500','bg-amber-500','bg-rose-500','bg-teal-500','bg-orange-500','bg-cyan-500','bg-purple-500']
  return colors[name.charCodeAt(0) % colors.length]
}

const statusBadge = (s: string) =>
  s === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-500'

const years    = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year']
const sections = ['Section A', 'Section B', 'Section C', 'Section D']
const departments = ['Computer Science', 'Software Engineering', 'Information Systems', 'Data Science', 'Cybersecurity']

const displayPages = computed(() => {
  const tp = totalPages.value
  if (tp <= 5) return Array.from({ length: tp }, (_, i) => i + 1)
  const pages: (number | '...')[] = [1, 2, 3]
  if (currentPage.value > 4) pages.push('...')
  if (currentPage.value > 3 && currentPage.value < tp - 1) pages.push(currentPage.value)
  pages.push('...')
  pages.push(tp)
  return pages
})
</script>

<template>
  <div class="space-y-6">

    <!-- ══════════════════════════ ADD STUDENT VIEW ══════════════════════════ -->
    <template v-if="currentView === 'add'">

      <!-- Header with back button + breadcrumb -->
      <div>
        <div class="flex items-center gap-3 mb-1">
          <button @click="backToList" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
          </button>
          <h1 class="text-[22px] font-bold text-slate-800">Add New Student</h1>
        </div>
        <div class="flex items-center gap-1 text-[12px] text-slate-400 ml-11">
          <button @click="backToList" class="hover:text-[#5138ed] transition-colors">Students</button>
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          <span class="text-slate-600 font-medium">Add New Student</span>
        </div>
      </div>

      <!-- Form Sections -->
      <div class="space-y-6">

        <!-- Personal Information -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-8">
          <h2 class="text-[15px] font-bold text-slate-800 mb-6">Personal Information</h2>

          <div class="space-y-5">
            <!-- Row 1: Full Name | Email | Phone -->
            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Full Name <span class="text-rose-500">*</span></label>
                <input v-model="addForm.fullName" type="text" placeholder="Enter full name" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Email Address <span class="text-rose-500">*</span></label>
                <input v-model="addForm.email" type="email" placeholder="Enter email address" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Phone Number <span class="text-rose-500">*</span></label>
                <input v-model="addForm.phone" type="text" placeholder="Enter phone number" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
              </div>
            </div>

            <!-- Row 2: Date of Birth | Gender | Profile Picture -->
            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Date of Birth <span class="text-rose-500">*</span></label>
                <input v-model="addForm.dateOfBirth" type="date" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-shadow" />
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Gender <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select v-model="addForm.gender" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-shadow">
                    <option value="">Select gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Profile Picture</label>
                <label class="flex flex-col items-center justify-center w-full h-[50px] border-2 border-dashed border-slate-200 rounded-xl cursor-pointer hover:border-[#5138ed] hover:bg-indigo-50/30 transition-colors group">
                  <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-[#5138ed] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    <span class="text-[12px] font-bold text-slate-600 group-hover:text-[#5138ed]">{{ addForm.profilePicture ? addForm.profilePicture.name : 'Upload Photo' }}</span>
                  </div>
                  <p class="text-[10px] text-slate-400">PNG, JPG up to 2MB</p>
                  <input type="file" accept="image/*" @change="handleFileUpload" class="hidden" />
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Academic Information -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-8">
          <h2 class="text-[15px] font-bold text-slate-800 mb-6">Academic Information</h2>

          <div class="space-y-5">
            <!-- Row 1: Student ID | Admission Number | Department -->
            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Student ID <span class="text-rose-500">*</span></label>
                <input v-model="addForm.studentId" type="text" placeholder="Enter student ID" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Admission Number <span class="text-rose-500">*</span></label>
                <input v-model="addForm.admissionNumber" type="text" placeholder="Enter admission no." class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Department <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select v-model="addForm.department" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-shadow">
                    <option value="">Select department</option>
                    <option v-for="d in departments" :key="d" :value="d">{{ d }}</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
            </div>

            <!-- Row 2: Year | Semester | Section -->
            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Year <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select v-model="addForm.year" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-shadow">
                    <option value="">Select year</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                    <option value="5th Year">5th Year</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Semester <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select v-model="addForm.semester" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-shadow">
                    <option value="">Select semester</option>
                    <option value="Semester 1">Semester 1</option>
                    <option value="Semester 2">Semester 2</option>
                    <option value="Summer">Summer</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Section <span class="text-rose-500">*</span></label>
                <input v-model="addForm.section" type="text" placeholder="Enter section" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4 pb-4">
          <button @click="backToList" class="flex items-center gap-2 px-6 py-3 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            Cancel
          </button>
          <button @click="saveStudent" :disabled="isLoading" class="flex items-center gap-2 px-6 py-3 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-200 transition-all disabled:opacity-70 disabled:cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            {{ isLoading ? 'Saving...' : 'Save Student' }}
          </button>
        </div>

      </div>
    </template>

    <!-- ══════════════════════════ DETAIL VIEW ══════════════════════════ -->
    <template v-else-if="currentView === 'detail' && selectedStudent">
      <div class="space-y-6">
        
        <!-- Breadcrumbs & Header -->
        <div>
          <div class="flex items-center gap-3 mb-1">
            <button @click="backToList" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <h1 class="text-[22px] font-bold text-slate-800">Student Details</h1>
          </div>
          <div class="flex items-center gap-1 text-[12px] text-slate-400 ml-11 mb-2">
            <button @click="backToList" class="hover:text-[#5138ed] transition-colors">Students</button>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="text-slate-600 font-medium">Student Details</span>
          </div>
          <div class="flex items-center justify-between ml-11">
            <p class="text-[13px] text-slate-500">View complete information about the student.</p>
            <button @click="backToList" class="flex items-center gap-2 px-4 py-2 text-[12px] font-bold text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
              Back to Students
            </button>
          </div>
        </div>

        <!-- Top Profile Section -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-6">
              <div class="relative">
                <div :class="[avatarColor(selectedStudent.name), 'w-24 h-24 rounded-full flex items-center justify-center text-[28px] font-bold text-white shadow-md border-4 border-white']">
                  {{ selectedStudent.avatar }}
                </div>
                <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 flex items-center gap-1 bg-white border border-slate-100 shadow-sm px-2.5 py-0.5 rounded-full">
                  <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                  <span class="text-[10px] font-bold text-emerald-600">Active</span>
                </div>
              </div>
              <div>
                <h2 class="text-[24px] font-bold text-slate-800 leading-tight">{{ selectedStudent.name }}</h2>
                <p class="text-[14px] text-slate-500 mb-3">{{ selectedStudent.id }}</p>
                <div class="flex flex-col gap-2">
                  <div class="flex items-center gap-2 text-[13px] text-slate-600">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    {{ selectedStudent.email }}
                  </div>
                  <div class="flex items-center gap-2 text-[13px] text-slate-600">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    +251 9XX XXX XXX
                  </div>
                </div>
              </div>
            </div>
            
            <div class="flex gap-10 pr-6">
              <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center mt-1">
                  <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <div>
                  <p class="text-[11px] font-semibold text-slate-500 mb-1">Level</p>
                  <p class="text-[14px] font-bold text-slate-800">Level 4</p>
                  <p class="text-[10px] text-slate-400 mt-1">Current Level</p>
                </div>
              </div>
              <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center mt-1">
                  <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                </div>
                <div>
                  <p class="text-[11px] font-semibold text-slate-500 mb-1">Program</p>
                  <p class="text-[14px] font-bold text-slate-800">Computer Science</p>
                  <p class="text-[10px] text-slate-400 mt-1">Program</p>
                </div>
              </div>
              <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mt-1">
                  <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                  <p class="text-[11px] font-semibold text-slate-500 mb-1">Admission Date</p>
                  <p class="text-[14px] font-bold text-slate-800">{{ selectedStudent.admissionDate }}</p>
                  <p class="text-[10px] text-slate-400 mt-1">Admission Date</p>
                </div>
              </div>
              <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center mt-1">
                  <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                  <p class="text-[11px] font-semibold text-slate-500 mb-1">Status</p>
                  <p class="text-[14px] font-bold text-slate-800 capitalize">{{ selectedStudent.status }}</p>
                  <p class="text-[10px] text-slate-400 mt-1">Student Status</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 3 Columns Section -->
        <div class="grid grid-cols-3 gap-6">
          
          <!-- Personal Information -->
          <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
            <div class="flex items-center gap-2 mb-6">
              <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
              <h3 class="text-[14px] font-bold text-slate-800">Personal Information</h3>
            </div>
            <div class="space-y-4">
              <div class="grid grid-cols-2 gap-2">
                <span class="text-[12px] text-slate-500">Full Name</span>
                <span class="text-[13px] font-semibold text-slate-800">{{ selectedStudent.name }}</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <span class="text-[12px] text-slate-500">Email Address</span>
                <span class="text-[13px] font-medium text-slate-700 truncate">{{ selectedStudent.email }}</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <span class="text-[12px] text-slate-500">Phone Number</span>
                <span class="text-[13px] font-medium text-slate-700">+251 9XX XXX XXX</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <span class="text-[12px] text-slate-500">Date of Birth</span>
                <span class="text-[13px] font-medium text-slate-700">Jan 12, 2002</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <span class="text-[12px] text-slate-500">Gender</span>
                <span class="text-[13px] font-medium text-slate-700">Male</span>
              </div>
              <div class="grid grid-cols-2 gap-2 items-center">
                <span class="text-[12px] text-slate-500">Profile Picture</span>
                <div :class="[avatarColor(selectedStudent.name), 'w-10 h-10 rounded-full flex items-center justify-center text-[12px] font-bold text-white shadow-sm border-2 border-white']">
                  {{ selectedStudent.avatar }}
                </div>
              </div>
            </div>
          </div>

          <!-- Academic Information -->
          <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
            <div class="flex items-center gap-2 mb-6">
              <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
              <h3 class="text-[14px] font-bold text-slate-800">Academic Information</h3>
            </div>
            <div class="space-y-4">
              <div class="grid grid-cols-2 gap-2">
                <span class="text-[12px] text-slate-500">Student ID</span>
                <span class="text-[13px] font-semibold text-slate-800">{{ selectedStudent.id }}</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <span class="text-[12px] text-slate-500">Admission Number</span>
                <span class="text-[13px] font-medium text-slate-700">ADM-2022-001</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <span class="text-[12px] text-slate-500">Department</span>
                <span class="text-[13px] font-medium text-slate-700">Computer Science</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <span class="text-[12px] text-slate-500">Program</span>
                <span class="text-[13px] font-medium text-slate-700">Computer Science</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <span class="text-[12px] text-slate-500">Level</span>
                <span class="text-[13px] font-medium text-slate-700">Level 4</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <span class="text-[12px] text-slate-500">Current Semester</span>
                <span class="text-[13px] font-medium text-slate-700">Spring 2026</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <span class="text-[12px] text-slate-500">Section</span>
                <span class="text-[13px] font-medium text-slate-700">{{ selectedStudent.section }}</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <span class="text-[12px] text-slate-500">Admission Date</span>
                <span class="text-[13px] font-medium text-slate-700">{{ selectedStudent.admissionDate }}</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <span class="text-[12px] text-slate-500">Expected Graduation</span>
                <span class="text-[13px] font-medium text-slate-700">June 2026</span>
              </div>
            </div>
          </div>

          <!-- Account & Quick Actions -->
          <div class="space-y-6">
            <!-- Account Information -->
            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
              <div class="flex items-center gap-2 mb-6">
                <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <h3 class="text-[14px] font-bold text-slate-800">Account Information</h3>
              </div>
              <div class="space-y-4">
                <div class="grid grid-cols-2 gap-2">
                  <span class="text-[12px] text-slate-500">Account Status</span>
                  <div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-600">Active</span>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                  <span class="text-[12px] text-slate-500">User Account</span>
                  <span class="text-[13px] font-medium text-slate-700 truncate">nahom.gebreyesus</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                  <span class="text-[12px] text-slate-500">Member Since</span>
                  <span class="text-[13px] font-medium text-slate-700">{{ selectedStudent.admissionDate }}</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                  <span class="text-[12px] text-slate-500">Last Login</span>
                  <span class="text-[13px] font-medium text-slate-700">May 27, 2026 10:30 AM</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                  <span class="text-[12px] text-slate-500">Last Updated</span>
                  <span class="text-[13px] font-medium text-slate-700">May 27, 2026</span>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
              <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <h3 class="text-[14px] font-bold text-slate-800">Quick Actions</h3>
              </div>
              <div class="flex flex-col gap-2">
                <button class="w-full flex items-center justify-between px-4 py-3 text-[13px] font-medium text-slate-600 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:text-[#5138ed] hover:bg-indigo-50/50 transition-all">
                  <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    View Academic Record
                  </div>
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                <button class="w-full flex items-center justify-between px-4 py-3 text-[13px] font-medium text-slate-600 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:text-[#5138ed] hover:bg-indigo-50/50 transition-all">
                  <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    View Exam Results
                  </div>
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                <button class="w-full flex items-center justify-between px-4 py-3 text-[13px] font-medium text-slate-600 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:text-[#5138ed] hover:bg-indigo-50/50 transition-all">
                  <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    View Enrolled Courses
                  </div>
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                <button @click="openEditPage(selectedStudent)" class="w-full flex items-center justify-between px-4 py-3 text-[13px] font-medium text-slate-600 border border-slate-100 rounded-xl hover:border-[#5138ed] hover:text-[#5138ed] hover:bg-indigo-50/50 transition-all">
                  <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    Edit Student Information
                  </div>
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Enrolled Courses Table -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
          <div class="flex items-center gap-2 mb-6">
            <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            <h3 class="text-[14px] font-bold text-slate-800">Enrolled Courses (This Semester)</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="border-b border-slate-100">
                  <th class="text-left py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course Code</th>
                  <th class="text-left py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course Title</th>
                  <th class="text-left py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Instructor</th>
                  <th class="text-left py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Section</th>
                  <th class="text-center py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Credits</th>
                  <th class="text-left py-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50">
                <tr class="hover:bg-slate-50/40 transition-colors">
                  <td class="py-4 text-[12px] font-semibold text-slate-700">CS-401</td>
                  <td class="py-4 text-[12px] text-slate-600">Database Systems</td>
                  <td class="py-4 text-[12px] text-slate-600">Dr. Solomon Abate</td>
                  <td class="py-4 text-[12px] text-slate-600">Section A</td>
                  <td class="py-4 text-[12px] text-center text-slate-600 font-semibold">3</td>
                  <td class="py-4"><span class="text-[10px] font-bold px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600">Enrolled</span></td>
                </tr>
                <tr class="hover:bg-slate-50/40 transition-colors">
                  <td class="py-4 text-[12px] font-semibold text-slate-700">CS-403</td>
                  <td class="py-4 text-[12px] text-slate-600">Operating Systems</td>
                  <td class="py-4 text-[12px] text-slate-600">Dr. Abebe Kebede</td>
                  <td class="py-4 text-[12px] text-slate-600">Section A</td>
                  <td class="py-4 text-[12px] text-center text-slate-600 font-semibold">3</td>
                  <td class="py-4"><span class="text-[10px] font-bold px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600">Enrolled</span></td>
                </tr>
                <tr class="hover:bg-slate-50/40 transition-colors">
                  <td class="py-4 text-[12px] font-semibold text-slate-700">CS-405</td>
                  <td class="py-4 text-[12px] text-slate-600">Computer Networks</td>
                  <td class="py-4 text-[12px] text-slate-600">Dr. Yeshimebet Desta</td>
                  <td class="py-4 text-[12px] text-slate-600">Section A</td>
                  <td class="py-4 text-[12px] text-center text-slate-600 font-semibold">3</td>
                  <td class="py-4"><span class="text-[10px] font-bold px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600">Enrolled</span></td>
                </tr>
                <tr class="hover:bg-slate-50/40 transition-colors">
                  <td class="py-4 text-[12px] font-semibold text-slate-700">CS-407</td>
                  <td class="py-4 text-[12px] text-slate-600">Software Project Management</td>
                  <td class="py-4 text-[12px] text-slate-600">Mr. Getachew Tadesse</td>
                  <td class="py-4 text-[12px] text-slate-600">Section A</td>
                  <td class="py-4 text-[12px] text-center text-slate-600 font-semibold">3</td>
                  <td class="py-4"><span class="text-[10px] font-bold px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600">Enrolled</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </template>

    <!-- ══════════════════════════ EDIT STUDENT VIEW ══════════════════════════ -->
    <template v-else-if="currentView === 'edit' && selectedStudent">
      
      <!-- Header -->
      <div class="flex items-start gap-4">
        <button @click="backToList" class="mt-1 w-9 h-9 rounded-xl flex items-center justify-center border border-slate-200 text-slate-500 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-700 transition-all shrink-0">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <div>
          <h1 class="text-[22px] font-bold text-slate-800">Edit Student</h1>
          <p class="text-[13px] text-slate-500 mt-1">Update the profile information for <span class="font-bold text-slate-700">{{ selectedStudent.name }}</span>.</p>
        </div>
      </div>

      <!-- Form Sections -->
      <div class="space-y-6">

        <!-- Personal Information -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-8">
          <div class="flex items-center gap-3 mb-7">
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <h2 class="text-[16px] font-bold text-slate-800">Personal Information</h2>
          </div>

          <div class="space-y-5">
            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Full Name <span class="text-rose-500">*</span></label>
                <input v-model="editForm.fullName" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" />
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Email Address <span class="text-rose-500">*</span></label>
                <input v-model="editForm.email" type="email" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" />
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Phone Number <span class="text-rose-500">*</span></label>
                <input v-model="editForm.phone" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" />
              </div>
            </div>

            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Date of Birth <span class="text-rose-500">*</span></label>
                <input v-model="editForm.dateOfBirth" type="date" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" />
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Gender <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select v-model="editForm.gender" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Profile Picture</label>
                <label class="flex flex-col items-center justify-center w-full h-[50px] border-2 border-dashed border-slate-200 rounded-xl cursor-pointer hover:border-[#5138ed] hover:bg-indigo-50/30 transition-colors group">
                  <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    <span class="text-[12px] font-bold text-slate-600 group-hover:text-[#5138ed]">{{ editForm.profilePicture ? editForm.profilePicture.name : 'Change Photo' }}</span>
                  </div>
                  <input type="file" accept="image/*" @change="handleEditFileUpload" class="hidden" />
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Academic Information -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-8">
          <div class="flex items-center gap-3 mb-7">
            <div class="w-10 h-10 bg-sky-50 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
            </div>
            <h2 class="text-[16px] font-bold text-slate-800">Academic Information</h2>
          </div>

          <div class="space-y-5">
            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Student ID <span class="text-rose-500">*</span></label>
                <input v-model="editForm.studentId" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" />
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Admission Number <span class="text-rose-500">*</span></label>
                <input v-model="editForm.admissionNumber" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" />
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Department <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select v-model="editForm.department" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
                    <option v-for="d in departments" :key="d" :value="d">{{ d }}</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Year <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select v-model="editForm.year" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
                    <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Semester <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select v-model="editForm.semester" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
                    <option value="Semester 1">Semester 1</option>
                    <option value="Semester 2">Semester 2</option>
                    <option value="Summer">Summer</option>
                    <option value="Spring 2026">Spring 2026</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Section <span class="text-rose-500">*</span></label>
                <input v-model="editForm.section" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" />
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4 pb-4">
          <button @click="backToList" class="px-6 py-3 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
          <button @click="saveEditStudent" :disabled="isLoading" class="flex items-center gap-2 px-6 py-3 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-200 transition-all disabled:opacity-70 disabled:cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ isLoading ? 'Saving...' : 'Update Student' }}
          </button>
        </div>

      </div>
    </template>

    <!-- ══════════════════════════ LIST VIEW ══════════════════════════ -->
    <template v-else>

      <!-- Header -->
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Students</h1>
        <p class="text-[13px] text-slate-500 mt-1">Manage and monitor students in your department.</p>
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
          <div class="relative flex-1 max-w-xs">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input v-model="search" type="text" placeholder="Search students by name, email or ID..." class="w-full pl-9 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400">
          </div>
          <div class="relative">
            <select v-model="statusFilter" class="pl-4 pr-8 py-2.5 text-[13px] font-medium border border-slate-200 rounded-xl text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed]">
              <option value="all">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
            <svg class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
          <div class="relative">
            <select v-model="yearFilter" class="pl-4 pr-8 py-2.5 text-[13px] font-medium border border-slate-200 rounded-xl text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed]">
              <option value="all">All Years</option>
              <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
            </select>
            <svg class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
          <div class="relative">
            <select v-model="sectionFilter" class="pl-4 pr-8 py-2.5 text-[13px] font-medium border border-slate-200 rounded-xl text-slate-600 bg-white appearance-none focus:outline-none focus:border-[#5138ed]">
              <option value="all">All Sections</option>
              <option v-for="s in sections" :key="s" :value="s">{{ s }}</option>
            </select>
            <svg class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </div>
          <button class="flex items-center gap-2 text-[13px] font-bold text-[#5138ed] border border-indigo-200 hover:bg-indigo-50 px-4 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            Import
          </button>
          <button @click="openAddPage" class="flex items-center gap-2 bg-[#5138ed] hover:bg-indigo-700 text-white text-[13px] font-bold px-4 py-2.5 rounded-xl shadow-sm shadow-indigo-200 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Student
          </button>
        </div>

        <!-- Table -->
        <table class="w-full">
          <thead>
            <tr class="border-b border-slate-100">
              <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Student</th>
              <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">ID</th>
              <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Email</th>
              <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Section</th>
              <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Year</th>
              <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
              <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Admission Date</th>
              <th class="text-center px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr v-for="stu in paginated" :key="stu.id" class="hover:bg-slate-50/40 transition-colors group">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div :class="[avatarColor(stu.name), 'w-9 h-9 rounded-full flex items-center justify-center text-[11px] font-bold text-white shrink-0']">{{ stu.avatar }}</div>
                  <p class="text-[13px] font-bold text-slate-800">{{ stu.name }}</p>
                </div>
              </td>
              <td class="px-4 py-4"><span class="text-[12px] font-semibold text-slate-600">{{ stu.id }}</span></td>
              <td class="px-4 py-4"><span class="text-[12px] text-slate-600">{{ stu.email }}</span></td>
              <td class="px-4 py-4"><span class="text-[12px] text-slate-600">{{ stu.section }}</span></td>
              <td class="px-4 py-4"><span class="text-[12px] font-semibold text-slate-600">{{ stu.year }}</span></td>
              <td class="px-4 py-4">
                <span :class="[statusBadge(stu.status), 'text-[11px] font-bold px-2.5 py-1 rounded-md capitalize']">{{ stu.status === 'active' ? 'Active' : 'Inactive' }}</span>
              </td>
              <td class="px-4 py-4"><span class="text-[12px] text-slate-500">{{ stu.admissionDate }}</span></td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                  <button @click="openView(stu)" class="w-7 h-7 rounded-lg flex items-center justify-center text-[#5138ed] bg-indigo-50 hover:bg-indigo-100 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                  <button @click="openEditPage(stu)" class="w-7 h-7 rounded-lg flex items-center justify-center text-sky-500 bg-sky-50 hover:bg-sky-100 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                  <button @click="confirmDelete(stu)" class="w-7 h-7 rounded-lg flex items-center justify-center text-rose-500 bg-rose-50 hover:bg-rose-100 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                </div>
              </td>
            </tr>
            <tr v-if="paginated.length === 0">
              <td colspan="8" class="px-6 py-12 text-center text-[13px] text-slate-400">No students found.</td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex items-center justify-between px-6 py-5 border-t border-slate-100 bg-white">
          <p class="text-[13px] text-slate-500 font-medium">
            Showing {{ Math.min((currentPage - 1) * perPage + 1, filtered.length) }} to {{ Math.min(currentPage * perPage, filtered.length) }} of {{ filtered.length }} students
          </p>
          <div class="flex items-center gap-2">
            <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-slate-50 disabled:opacity-40 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <template v-for="p in displayPages" :key="p">
              <span v-if="p === '...'" class="w-8 h-8 flex items-center justify-center text-slate-400 text-[13px]">...</span>
              <button v-else @click="currentPage = (p as number)" :class="[currentPage === p ? 'bg-[#5138ed] text-white border border-[#5138ed]' : 'text-slate-500 border border-slate-200 hover:bg-slate-50', 'w-8 h-8 rounded-lg text-[13px] font-bold transition-colors']">{{ p }}</button>
            </template>
            <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-slate-50 disabled:opacity-40 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
          </div>
        </div>
      </div>

    </template>

    <!-- ── Delete Modal ── -->
    <Teleport to="body">
      <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6">
          <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg></div>
          <h3 class="text-[16px] font-bold text-slate-800 mb-2">Remove Student?</h3>
          <p class="text-[13px] text-slate-500 mb-6">Are you sure you want to remove <span class="font-bold text-slate-700">{{ selectedStudent?.name }}</span>?</p>
          <div class="flex gap-3">
            <button @click="showDeleteModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="deleteStudent" :disabled="isLoading" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors disabled:opacity-70">
              {{ isLoading ? 'Removing...' : 'Remove' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>
