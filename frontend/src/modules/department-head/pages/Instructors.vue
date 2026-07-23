<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import apiClient from '../../../core/api/apiClient'

// ── View State ──
const currentView = ref<'list' | 'add' | 'detail' | 'edit'>('list')

// ── State ──
const deptName = ref('Loading...')
const search = ref('')
const statusFilter = ref('all')
const yearFilter = ref('all')
const currentPage = ref(1)
const perPage = 8
const showDeleteModal = ref(false)
const selectedInstructor = ref<any>(null)
const isLoading = ref(false)
const allInstructors = ref<any[]>([])

const addForm = ref({
  fullName: '',
  email: '',
  phone: '',
  gender: '',
  profilePicture: null as File | null,
  employeeId: '',
  yearLevel: '',
  username: '',
  password: '',
  confirmPassword: ''
})
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const resetAddForm = () => {
  addForm.value = {
    fullName: '', email: '', phone: '', gender: '', profilePicture: null,
    employeeId: '', yearLevel: '',
    username: '', password: '', confirmPassword: ''
  }
}

const openAddPage = () => { resetAddForm(); currentView.value = 'add' }
const backToList = () => { currentView.value = 'list' }

// ── Edit Instructor Form ──
const editForm = ref({
  fullName: '',
  email: '',
  phone: '',
  gender: '',
  profilePicture: null as File | null,
  course: '',
  section: '',
  employeeId: '',
  semester: '',
  year: '',
  username: '',
  password: '',
  confirmPassword: '',
  employmentType: '',
  qualification: '',
  permissions: {
    createExams: false,
    viewResults: false,
    manageResults: false,
    manageQuestions: false,
    gradeExams: false,
    generateReports: false,
  }
})
const showEditPassword = ref(false)
const showEditConfirmPassword = ref(false)

const openEditPage = (instructor: any) => {
  selectedInstructor.value = instructor
  editForm.value = {
    fullName: instructor.name || '',
    email: instructor.email || '',
    phone: '+251 9XX XXX XXX',
    gender: 'Male',
    profilePicture: null,
    course: 'CS-301',
    section: 'Section A',
    employeeId: instructor.id_code || '',
    semester: 'Semester 1',
    year: instructor.year || '',
    username: instructor.name ? instructor.name.toLowerCase().replace(' ', '.') : '',
    password: '',
    confirmPassword: '',
    employmentType: instructor.status === 'active' ? 'Full Time' : 'Part Time',
    qualification: 'PhD in Computer Science',
    permissions: {
      createExams: true,
      viewResults: true,
      manageResults: true,
      manageQuestions: true,
      gradeExams: true,
      generateReports: true,
    }
  }
  currentView.value = 'edit'
}

const handleEditFileUpload = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) editForm.value.profilePicture = target.files[0]
}

const saveEditInstructor = async () => {
  if (!editForm.value.fullName || !editForm.value.email) return
  isLoading.value = true
  try {
    await apiClient.put(`/dept-head/instructors/${selectedInstructor.value.id}`, {
      name: editForm.value.fullName,
      email: editForm.value.email,
      ...(editForm.value.password ? { password: editForm.value.password } : {}),
      semester: editForm.value.semester || null,
    })
    await fetchInstructors()
    currentView.value = 'list'
  } catch (err: any) {
    let msg = err.response?.data?.message || 'Failed to update instructor.'
    if (err.response?.data?.errors) msg += '\n' + Object.values(err.response.data.errors).flat().join('\n')
    alert(msg)
  } finally { isLoading.value = false }
}

const handleFileUpload = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files[0]) addForm.value.profilePicture = target.files[0]
}

// ── Fetch Department Info ──
const fetchDeptInfo = async () => {
  try {
    const res = await apiClient.get('/user')
    deptName.value = res.data?.department?.name || 'My Department'
  } catch { deptName.value = 'My Department' }
}

const years = ['2018', '2019', '2020', '2021', '2022', '2023', '2024']
// ── Fetch Instructors ──
const fetchInstructors = async () => {
  try {
    const res = await apiClient.get('/dept-head/instructors')
    allInstructors.value = (res.data.data || []).map((i: any) => ({
      ...i,
      avatar: i.name ? i.name.split(' ').map((w: string) => w[0]).join('').toUpperCase().slice(0, 2) : '??',
      id_code: i.id_no || 'N/A',
      courses: i.assigned_courses?.length ? i.assigned_courses.map((c: any) => c.title).join(', ') : 'No Courses',
      credit: i.assigned_courses?.length ? i.assigned_courses.reduce((sum: number, c: any) => sum + (Number(c.credits) || 0), 0) : 0,
      year: i.year_level || 'N/A',
      status: i.status || 'active',
      joined: new Date(i.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
    }))
  } catch (err) { console.error('Failed to fetch instructors:', err) }
}

onMounted(async () => { await fetchDeptInfo(); await fetchInstructors() })

// ── Computed ──
const filtered = computed(() => {
  return allInstructors.value.filter(i => {
    const matchSearch = i.name.toLowerCase().includes(search.value.toLowerCase()) ||
                        i.email.toLowerCase().includes(search.value.toLowerCase()) ||
                        i.id_code.toLowerCase().includes(search.value.toLowerCase())
    const matchStatus = statusFilter.value === 'all' || i.status === statusFilter.value
    const matchYear = yearFilter.value === 'all' || i.year === yearFilter.value
    return matchSearch && matchStatus && matchYear
  })
})
const totalPages  = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)))
const paginated   = computed(() => filtered.value.slice((currentPage.value - 1) * perPage, currentPage.value * perPage))

const stats = computed(() => [
  { label: 'Total Instructors', value: allInstructors.value.length || 18, change: '↑ 2 this semester', bg: 'bg-indigo-50', ic: 'text-[#5138ed]', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', color: 'text-emerald-500' },
  { label: 'Full Time', value: allInstructors.value.filter(i => i.status === 'active').length || 12, change: '↑ 1 this semester', bg: 'bg-emerald-50', ic: 'text-emerald-500', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', color: 'text-emerald-500' },
  { label: 'Part Time', value: allInstructors.value.filter(i => i.status === 'part time').length || 6, change: '↑ 1 this semester', bg: 'bg-sky-50', ic: 'text-sky-500', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', color: 'text-emerald-500' },
  { label: 'On Leave', value: 0, change: 'No change', bg: 'bg-amber-50', ic: 'text-amber-500', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', color: 'text-slate-400' }
])

// ── Helpers ──
const avatarColor = (id: number) => {
  const colors = ['bg-indigo-500','bg-sky-500','bg-emerald-500','bg-violet-500','bg-amber-500','bg-rose-500','bg-teal-500','bg-orange-500','bg-cyan-500','bg-purple-500']
  return colors[id % colors.length]
}
const statusBadge = (s: string) => s === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600'

// ── Save Instructor ──
const saveInstructor = async () => {
  if (!addForm.value.fullName || !addForm.value.email || !addForm.value.password) return
  isLoading.value = true
  try {
    await apiClient.post('/dept-head/instructors', {
      name: addForm.value.fullName,
      email: addForm.value.email,
      phone: addForm.value.phone,
      gender: addForm.value.gender,
      id_no: addForm.value.employeeId,
      year_level: addForm.value.yearLevel,
      username: addForm.value.username,
      password: addForm.value.password,
    })
    await fetchInstructors()
    currentView.value = 'list'
  } catch (err: any) {
    let msg = err.response?.data?.message || 'Failed to create instructor.'
    if (err.response?.data?.errors) msg += '\n' + Object.values(err.response.data.errors).flat().join('\n')
    alert(msg)
  } finally { isLoading.value = false }
}

// ── Actions ──
const openView = (instructor: any) => { selectedInstructor.value = instructor; currentView.value = 'detail' }
const confirmDelete = (instructor: any) => { selectedInstructor.value = instructor; showDeleteModal.value = true }
const deleteInstructor = async () => {
  if (!selectedInstructor.value) return
  isLoading.value = true
  try {
    await apiClient.delete(`/dept-head/instructors/${selectedInstructor.value.id}`)
    await fetchInstructors()
    showDeleteModal.value = false
  } catch (err: any) {
    alert(err.response?.data?.message || 'Failed to delete instructor.')
  } finally { isLoading.value = false }
}

// Extract distinct years for dropdown
const uniqueYears = computed(() => {
  const set = new Set(allInstructors.value.map(i => i.year))
  return Array.from(set).filter(Boolean)
})
</script>

<template>
  <div class="space-y-6">

    <!-- ══════════════════════════ ADD INSTRUCTOR VIEW ══════════════════════════ -->
    <template v-if="currentView === 'add'">

      <!-- Header -->
      <div class="flex items-start justify-between">
        <div>
          <h1 class="text-[22px] font-bold text-slate-800">Add Instructor</h1>
          <p class="text-[13px] text-slate-500 mt-1">Create a new instructor profile. Fill in the details and assign to the course.</p>
        </div>
        <button @click="backToList" class="px-5 py-2 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          Back to Instructors
        </button>
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

            <!-- Row 2: Gender | Employee ID | Academic Year Level -->
            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Gender</label>
                <div class="relative">
                  <select v-model="addForm.gender" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-shadow">
                    <option value="">Select gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Employee ID</label>
                <input v-model="addForm.employeeId" type="text" placeholder="Enter employee ID" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Academic Year Level</label>
                <div class="relative">
                  <select v-model="addForm.yearLevel" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-shadow">
                    <option value="">Select year level</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                    <option value="5th Year">5th Year</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
            </div>

            <!-- Row 3: Profile Picture -->
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Profile Picture</label>
              <label class="flex flex-col items-center justify-center w-full h-[80px] border-2 border-dashed border-slate-200 rounded-xl cursor-pointer hover:border-[#5138ed] hover:bg-indigo-50/30 transition-colors group">
                <svg class="w-6 h-6 text-slate-300 group-hover:text-[#5138ed] mb-1 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                <p class="text-[12px] font-bold text-slate-600 group-hover:text-[#5138ed]">{{ addForm.profilePicture ? addForm.profilePicture.name : 'Upload Photo' }}</p>
                <p class="text-[11px] text-slate-400">PNG, JPG up to 2MB</p>
                <input type="file" accept="image/*" @change="handleFileUpload" class="hidden" />
              </label>
            </div>
          </div>
        </div>


        <!-- Account Information -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-8">
          <div class="flex items-center gap-3 mb-7">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h2 class="text-[16px] font-bold text-slate-800">Account Information</h2>
          </div>

          <div class="grid grid-cols-3 gap-5">
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Username <span class="text-rose-500">*</span></label>
              <input v-model="addForm.username" type="text" placeholder="Enter username" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
            </div>
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Password <span class="text-rose-500">*</span></label>
              <div class="relative">
                <input v-model="addForm.password" :type="showPassword ? 'text' : 'password'" placeholder="Enter password" class="w-full border border-slate-200 rounded-xl px-4 py-3 pr-11 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
                <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                  <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                </button>
              </div>
            </div>
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Confirm Password <span class="text-rose-500">*</span></label>
              <div class="relative">
                <input v-model="addForm.confirmPassword" :type="showConfirmPassword ? 'text' : 'password'" placeholder="Confirm password" class="w-full border border-slate-200 rounded-xl px-4 py-3 pr-11 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
                <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                  <svg v-if="!showConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                </button>
              </div>
            </div>
          </div>
        </div>


        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4 pb-4">
          <button @click="backToList" class="px-6 py-3 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
          <button @click="saveInstructor" :disabled="isLoading" class="flex items-center gap-2 px-6 py-3 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-200 transition-all disabled:opacity-70 disabled:cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            {{ isLoading ? 'Saving...' : 'Save Instructor' }}
          </button>
        </div>

      </div>
    </template>

    <!-- ══════════════════════════ EDIT INSTRUCTOR VIEW ══════════════════════════ -->
    <template v-else-if="currentView === 'edit' && selectedInstructor">

      <!-- Header -->
      <div class="flex items-start gap-4">
        <button @click="backToList" class="mt-1 w-9 h-9 rounded-xl flex items-center justify-center border border-slate-200 text-slate-500 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-700 transition-all shrink-0">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <div>
          <h1 class="text-[22px] font-bold text-slate-800">Edit Instructor</h1>
          <p class="text-[13px] text-slate-500 mt-1">Update the instructor profile for <span class="font-bold text-slate-700">{{ selectedInstructor.name }}</span>.</p>
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
            <!-- Row 1: Full Name | Email | Phone -->
            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Full Name <span class="text-rose-500">*</span></label>
                <input v-model="editForm.fullName" type="text" placeholder="Enter full name" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Email Address <span class="text-rose-500">*</span></label>
                <input v-model="editForm.email" type="email" placeholder="Enter email address" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Phone Number <span class="text-rose-500">*</span></label>
                <input v-model="editForm.phone" type="text" placeholder="Enter phone number" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
              </div>
            </div>

            <!-- Row 2: Gender | Profile Picture -->
            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Gender</label>
                <div class="relative">
                  <select v-model="editForm.gender" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-shadow">
                    <option value="">Select gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div class="col-span-2">
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Profile Picture</label>
                <label class="flex flex-col items-center justify-center w-full h-[80px] border-2 border-dashed border-slate-200 rounded-xl cursor-pointer hover:border-[#5138ed] hover:bg-indigo-50/30 transition-colors group">
                  <svg class="w-6 h-6 text-slate-300 group-hover:text-[#5138ed] mb-1 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                  <p class="text-[12px] font-bold text-slate-600 group-hover:text-[#5138ed]">{{ editForm.profilePicture ? editForm.profilePicture.name : 'Upload New Photo' }}</p>
                  <p class="text-[11px] text-slate-400">PNG, JPG up to 2MB</p>
                  <input type="file" accept="image/*" @change="handleEditFileUpload" class="hidden" />
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Professional Information -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-8">
          <div class="flex items-center gap-3 mb-7">
            <div class="w-10 h-10 bg-sky-50 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <h2 class="text-[16px] font-bold text-slate-800">Professional Information</h2>
          </div>

          <div class="space-y-5">
            <!-- Row 1: Course | Section | Employee ID -->
            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Course <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select v-model="editForm.course" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-shadow">
                    <option value="">Select course</option>
                    <option value="CS-101">CS-101 Introduction to Programming</option>
                    <option value="CS-201">CS-201 Data Structures</option>
                    <option value="CS-301">CS-301 Algorithms</option>
                    <option value="CS-401">CS-401 Database Systems</option>
                    <option value="CS-501">CS-501 Software Engineering</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Section <span class="text-rose-500">*</span></label>
                <div class="relative">
                  <select v-model="editForm.section" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-shadow">
                    <option value="">Select section</option>
                    <option value="Section A">Section A</option>
                    <option value="Section B">Section B</option>
                    <option value="Section C">Section C</option>
                    <option value="Section D">Section D</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Employee ID</label>
                <input v-model="editForm.employeeId" type="text" placeholder="Enter employee ID" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
              </div>
            </div>

            <!-- Row 2: Semester | Year | Employment Type -->
            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Semester</label>
                <div class="relative">
                  <select v-model="editForm.semester" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-shadow">
                    <option value="">Select semester</option>
                    <option value="Semester 1">Semester 1</option>
                    <option value="Semester 2">Semester 2</option>
                    <option value="Summer">Summer</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Year</label>
                <div class="relative">
                  <select v-model="editForm.year" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-shadow">
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
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Employment Type</label>
                <div class="relative">
                  <select v-model="editForm.employmentType" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 bg-white appearance-none focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-shadow">
                    <option value="">Select type</option>
                    <option value="Full Time">Full Time</option>
                    <option value="Part Time">Part Time</option>
                  </select>
                  <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
              </div>
            </div>

            <!-- Row 3: Qualification -->
            <div class="grid grid-cols-3 gap-5">
              <div>
                <label class="block text-[12px] font-semibold text-slate-700 mb-2">Qualification</label>
                <input v-model="editForm.qualification" type="text" placeholder="Enter qualification" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
              </div>
            </div>
          </div>
        </div>

        <!-- Account Information -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-8">
          <div class="flex items-center gap-3 mb-7">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h2 class="text-[16px] font-bold text-slate-800">Account Information</h2>
          </div>

          <div class="grid grid-cols-3 gap-5">
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Username <span class="text-rose-500">*</span></label>
              <input v-model="editForm.username" type="text" placeholder="Enter username" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
            </div>
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">New Password <span class="text-[11px] text-slate-400 font-normal">(leave blank to keep current)</span></label>
              <div class="relative">
                <input v-model="editForm.password" :type="showEditPassword ? 'text' : 'password'" placeholder="Enter new password" class="w-full border border-slate-200 rounded-xl px-4 py-3 pr-11 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
                <button type="button" @click="showEditPassword = !showEditPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                  <svg v-if="!showEditPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                </button>
              </div>
            </div>
            <div>
              <label class="block text-[12px] font-semibold text-slate-700 mb-2">Confirm New Password</label>
              <div class="relative">
                <input v-model="editForm.confirmPassword" :type="showEditConfirmPassword ? 'text' : 'password'" placeholder="Confirm new password" class="w-full border border-slate-200 rounded-xl px-4 py-3 pr-11 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] placeholder:text-slate-400 transition-shadow" />
                <button type="button" @click="showEditConfirmPassword = !showEditConfirmPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                  <svg v-if="!showEditConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                  <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Instructor Permissions -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-8">
          <div class="flex items-center gap-3 mb-7">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <h2 class="text-[16px] font-bold text-slate-800">Instructor Permissions</h2>
          </div>

          <div class="grid grid-cols-3 gap-5">
            <label class="flex items-start gap-3 p-4 border border-slate-100 rounded-xl hover:border-indigo-200 hover:bg-indigo-50/20 transition-colors cursor-pointer group">
              <input type="checkbox" v-model="editForm.permissions.createExams" class="mt-0.5 w-4 h-4 accent-[#5138ed] rounded cursor-pointer" />
              <div>
                <p class="text-[13px] font-bold text-slate-800 group-hover:text-[#5138ed] transition-colors">Create Exams</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Create and manage exams</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-4 border border-slate-100 rounded-xl hover:border-indigo-200 hover:bg-indigo-50/20 transition-colors cursor-pointer group">
              <input type="checkbox" v-model="editForm.permissions.viewResults" class="mt-0.5 w-4 h-4 accent-[#5138ed] rounded cursor-pointer" />
              <div>
                <p class="text-[13px] font-bold text-slate-800 group-hover:text-[#5138ed] transition-colors">View &amp; Results</p>
                <p class="text-[11px] text-slate-400 mt-0.5">View student results and analytics</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-4 border border-slate-100 rounded-xl hover:border-indigo-200 hover:bg-indigo-50/20 transition-colors cursor-pointer group">
              <input type="checkbox" v-model="editForm.permissions.manageResults" class="mt-0.5 w-4 h-4 accent-[#5138ed] rounded cursor-pointer" />
              <div>
                <p class="text-[13px] font-bold text-slate-800 group-hover:text-[#5138ed] transition-colors">Manage Results</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Create and manage results</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-4 border border-slate-100 rounded-xl hover:border-indigo-200 hover:bg-indigo-50/20 transition-colors cursor-pointer group">
              <input type="checkbox" v-model="editForm.permissions.manageQuestions" class="mt-0.5 w-4 h-4 accent-[#5138ed] rounded cursor-pointer" />
              <div>
                <p class="text-[13px] font-bold text-slate-800 group-hover:text-[#5138ed] transition-colors">Manage Questions</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Add, edit and manage questions</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-4 border border-slate-100 rounded-xl hover:border-indigo-200 hover:bg-indigo-50/20 transition-colors cursor-pointer group">
              <input type="checkbox" v-model="editForm.permissions.gradeExams" class="mt-0.5 w-4 h-4 accent-[#5138ed] rounded cursor-pointer" />
              <div>
                <p class="text-[13px] font-bold text-slate-800 group-hover:text-[#5138ed] transition-colors">Grade Exams</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Grade student submissions</p>
              </div>
            </label>
            <label class="flex items-start gap-3 p-4 border border-slate-100 rounded-xl hover:border-indigo-200 hover:bg-indigo-50/20 transition-colors cursor-pointer group">
              <input type="checkbox" v-model="editForm.permissions.generateReports" class="mt-0.5 w-4 h-4 accent-[#5138ed] rounded cursor-pointer" />
              <div>
                <p class="text-[13px] font-bold text-slate-800 group-hover:text-[#5138ed] transition-colors">Generate Reports</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Create and export reports</p>
              </div>
            </label>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4 pb-4">
          <button @click="backToList" class="px-6 py-3 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
          <button @click="saveEditInstructor" :disabled="isLoading" class="flex items-center gap-2 px-6 py-3 text-[13px] font-bold text-white bg-[#5138ed] hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-200 transition-all disabled:opacity-70 disabled:cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ isLoading ? 'Saving...' : 'Update Instructor' }}
          </button>
        </div>

      </div>
    </template>

    <!-- ══════════════════════════ DETAIL VIEW ══════════════════════════ -->
    <template v-else-if="currentView === 'detail' && selectedInstructor">
      <div class="space-y-6">
        <!-- Breadcrumbs & Header -->
        <div class="flex items-start justify-between">
          <div>
            <div class="flex items-center gap-2 text-[13px] font-medium text-[#5138ed] mb-2">
              <span class="cursor-pointer hover:underline" @click="backToList">Instructors</span>
              <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
              <span class="text-slate-600">Instructor Details</span>
            </div>
            <h1 class="text-[22px] font-bold text-slate-800">Instructor Details</h1>
            <p class="text-[13px] text-slate-500 mt-1">View complete information about the instructor.</p>
          </div>
          <div class="flex flex-col items-end gap-3">
            <div class="flex items-center gap-2 text-[12px] font-semibold text-slate-500">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
              May 27, 2026 <span class="text-slate-400 font-normal">Tuesday</span>
            </div>
            <button @click="backToList" class="flex items-center gap-2 text-[12px] font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 px-4 py-2 rounded-xl transition-colors shadow-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
              Back to Instructors
            </button>
          </div>
        </div>

        <!-- Top Profile Card -->
        <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm flex items-center justify-between">
          <div class="flex items-center gap-6">
            <div class="w-24 h-24 rounded-full bg-indigo-50 border-4 border-white shadow-md overflow-hidden flex items-center justify-center shrink-0">
               <div :class="[avatarColor(selectedInstructor.id), 'w-full h-full flex items-center justify-center text-[32px] font-bold text-white']">{{ selectedInstructor.avatar }}</div>
            </div>
            <div>
              <h2 class="text-[20px] font-bold text-slate-800">{{ selectedInstructor.name }}</h2>
              <p class="text-[13px] font-semibold text-slate-500 mt-1 mb-3">{{ selectedInstructor.id_code }}</p>
              <div class="flex items-center gap-4 text-[13px] text-slate-500 font-medium">
                <div class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg> {{ selectedInstructor.email }}</div>
                <div class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg> +251 9XX XXX XXX</div>
              </div>
              <div class="mt-4 inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold capitalize bg-emerald-50 text-emerald-600 gap-1.5 border border-emerald-100">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                {{ selectedInstructor.status }}
              </div>
            </div>
          </div>

          <div class="flex items-center gap-12 pr-6">
            <div class="text-center">
              <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center mx-auto mb-3">
                <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
              </div>
              <p class="text-[20px] font-bold text-slate-800">{{ selectedInstructor.courses }}</p>
              <p class="text-[11px] font-semibold text-slate-500 leading-tight">Courses<br>Assigned</p>
            </div>

            <div class="text-center">
              <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center mx-auto mb-3">
                <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
              </div>
              <p class="text-[16px] font-bold text-slate-800 capitalize">{{ selectedInstructor.status === 'active' ? 'Full Time' : 'Part Time' }}</p>
              <p class="text-[11px] font-semibold text-slate-500 leading-tight">Employment<br>Status</p>
            </div>
          </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-3 gap-6">
          <!-- Personal Information -->
          <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
              <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
              <h3 class="text-[15px] font-bold text-slate-800">Personal Information</h3>
            </div>
            <div class="space-y-4">
              <div class="flex justify-between items-start">
                <span class="text-[12px] font-semibold text-slate-500">Full Name</span>
                <span class="text-[12.5px] font-bold text-slate-800 text-right">{{ selectedInstructor.name }}</span>
              </div>
              <div class="flex justify-between items-start">
                <span class="text-[12px] font-semibold text-slate-500">Email Address</span>
                <span class="text-[12.5px] font-bold text-slate-800 text-right">{{ selectedInstructor.email }}</span>
              </div>
              <div class="flex justify-between items-start">
                <span class="text-[12px] font-semibold text-slate-500">Phone Number</span>
                <span class="text-[12.5px] font-bold text-slate-800 text-right">+251 9XX XXX XXX</span>
              </div>
              <div class="flex justify-between items-start">
                <span class="text-[12px] font-semibold text-slate-500">Gender</span>
                <span class="text-[12.5px] font-bold text-slate-800 text-right">Male</span>
              </div>
              <div class="flex justify-between items-center mt-2">
                <span class="text-[12px] font-semibold text-slate-500">Profile Picture</span>
                <div class="w-10 h-10 rounded-lg bg-indigo-50 overflow-hidden flex items-center justify-center">
                  <div :class="[avatarColor(selectedInstructor.id), 'w-full h-full flex items-center justify-center text-[12px] font-bold text-white']">{{ selectedInstructor.avatar }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Professional Information -->
          <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
              <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
              <h3 class="text-[15px] font-bold text-slate-800">Professional Information</h3>
            </div>
            <div class="space-y-4">
              <div class="flex justify-between items-start">
                <span class="text-[12px] font-semibold text-slate-500">Courses</span>
                <span class="text-[12.5px] font-bold text-slate-800 text-right">{{ selectedInstructor.courses }} courses assigned</span>
              </div>
              <div class="flex justify-between items-start">
                <span class="text-[12px] font-semibold text-slate-500">Section</span>
                <span class="text-[12.5px] font-bold text-slate-800 text-right">Computer Science Department</span>
              </div>
              <div class="flex justify-between items-start">
                <span class="text-[12px] font-semibold text-slate-500">Employee ID</span>
                <span class="text-[12.5px] font-bold text-slate-800 text-right">{{ selectedInstructor.id_code }}</span>
              </div>
              <div class="flex justify-between items-start">
                <span class="text-[12px] font-semibold text-slate-500">Semester</span>
                <span class="text-[12.5px] font-bold text-slate-800 text-right">--</span>
              </div>
              <div class="flex justify-between items-start">
                <span class="text-[12px] font-semibold text-slate-500">Year</span>
                <span class="text-[12.5px] font-bold text-slate-800 text-right">{{ selectedInstructor.year }}</span>
              </div>
              <div class="flex justify-between items-start">
                <span class="text-[12px] font-semibold text-slate-500">Employment Type</span>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 capitalize text-right">{{ selectedInstructor.status === 'active' ? 'Full Time' : 'Part Time' }}</span>
              </div>
              <div class="flex justify-between items-start">
                <span class="text-[12px] font-semibold text-slate-500">Qualification</span>
                <span class="text-[12.5px] font-bold text-slate-800 text-right">PhD in Computer Science</span>
              </div>
            </div>
          </div>

          <!-- Account & Permissions -->
          <div class="flex flex-col gap-6">
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm flex-1">
              <div class="flex items-center gap-3 mb-6">
                <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-[15px] font-bold text-slate-800">Account Information</h3>
              </div>
              <div class="space-y-4">
                <div class="flex justify-between items-start">
                  <span class="text-[12px] font-semibold text-slate-500">Username</span>
                  <span class="text-[12.5px] font-bold text-slate-800 text-right">{{ selectedInstructor.name.toLowerCase().replace(' ', '.') }}</span>
                </div>
                <div class="flex justify-between items-start">
                  <span class="text-[12px] font-semibold text-slate-500">Account Status</span>
                  <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-right capitalize">{{ selectedInstructor.status }}</span>
                </div>
                <div class="flex justify-between items-start">
                  <span class="text-[12px] font-semibold text-slate-500">Member Since</span>
                  <span class="text-[12.5px] font-bold text-slate-800 text-right">{{ selectedInstructor.joined }}</span>
                </div>
                <div class="flex justify-between items-start">
                  <span class="text-[12px] font-semibold text-slate-500">Last Updated</span>
                  <span class="text-[12.5px] font-bold text-slate-800 text-right">May 27, 2026</span>
                </div>
              </div>
            </div>
            
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm flex-1">
              <div class="flex items-center gap-3 mb-5">
                <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <h3 class="text-[15px] font-bold text-slate-800">Permissions</h3>
              </div>
              <div class="grid grid-cols-2 gap-y-3 gap-x-2">
                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><span class="text-[12px] font-bold text-slate-600">Create Exams</span></div>
                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><span class="text-[12px] font-bold text-slate-600">Manage Results</span></div>
                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><span class="text-[12px] font-bold text-slate-600">Manage Questions</span></div>
                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><span class="text-[12px] font-bold text-slate-600">Grade Students</span></div>
                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><span class="text-[12px] font-bold text-slate-600">View & Results</span></div>
                <div class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><span class="text-[12px] font-bold text-slate-600">Generate Reports</span></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Course Assignments Table -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden mt-6">
          <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-100">
            <svg class="w-5 h-5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            <h3 class="text-[15px] font-bold text-slate-800">Course Assignments (4)</h3>
          </div>
          <table class="w-full">
            <thead>
              <tr class="bg-white border-b border-slate-100">
                <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course Code</th>
                <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Course Title</th>
                <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Section</th>
                <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Semester</th>
                <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
              <tr class="hover:bg-slate-50/40 transition-colors">
                <td class="px-6 py-4 text-[12.5px] font-semibold text-slate-700">CS-301</td>
                <td class="px-6 py-4 text-[12.5px] font-medium text-slate-600">Data Structures</td>
                <td class="px-6 py-4 text-[12.5px] font-medium text-slate-600">Section A</td>
                <td class="px-6 py-4 text-[12.5px] font-medium text-slate-600">Spring 2026</td>
                <td class="px-6 py-4"><span class="text-[11px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-600">Active</span></td>
              </tr>
              <tr class="hover:bg-slate-50/40 transition-colors">
                <td class="px-6 py-4 text-[12.5px] font-semibold text-slate-700">CS-302</td>
                <td class="px-6 py-4 text-[12.5px] font-medium text-slate-600">Algorithms</td>
                <td class="px-6 py-4 text-[12.5px] font-medium text-slate-600">Section B</td>
                <td class="px-6 py-4 text-[12.5px] font-medium text-slate-600">Spring 2026</td>
                <td class="px-6 py-4"><span class="text-[11px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-600">Active</span></td>
              </tr>
              <tr class="hover:bg-slate-50/40 transition-colors">
                <td class="px-6 py-4 text-[12.5px] font-semibold text-slate-700">CS-401</td>
                <td class="px-6 py-4 text-[12.5px] font-medium text-slate-600">Database Systems</td>
                <td class="px-6 py-4 text-[12.5px] font-medium text-slate-600">Section A</td>
                <td class="px-6 py-4 text-[12.5px] font-medium text-slate-600">Spring 2026</td>
                <td class="px-6 py-4"><span class="text-[11px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-600">Active</span></td>
              </tr>
              <tr class="hover:bg-slate-50/40 transition-colors">
                <td class="px-6 py-4 text-[12.5px] font-semibold text-slate-700">CS-402</td>
                <td class="px-6 py-4 text-[12.5px] font-medium text-slate-600">Software Engineering</td>
                <td class="px-6 py-4 text-[12.5px] font-medium text-slate-600">Section C</td>
                <td class="px-6 py-4 text-[12.5px] font-medium text-slate-600">Spring 2026</td>
                <td class="px-6 py-4"><span class="text-[11px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-600">Active</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>

    <!-- ══════════════════════════ LIST VIEW ══════════════════════════ -->
    <template v-else>

      <!-- Header -->
      <div class="flex items-start justify-between">
        <div>
          <h1 class="text-[22px] font-bold text-slate-800">Instructors</h1>
          <p class="text-[13px] text-slate-500 mt-1">Manage and monitor department instructors.</p>
        </div>
        <div class="flex items-center gap-3">
          <button @click="openAddPage" class="flex items-center gap-2 bg-[#5138ed] hover:bg-indigo-700 text-white text-[13px] font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Instructor
          </button>
          <button class="flex items-center gap-2 bg-white border border-slate-200 hover:border-[#5138ed] hover:text-[#5138ed] text-slate-600 text-[13px] font-bold px-4 py-2.5 rounded-xl shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export
          </button>
        </div>
      </div>

      <!-- KPIs -->
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
        <!-- Filters -->
        <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-slate-100">
          <div class="relative flex-1 max-w-sm">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input v-model="search" type="text" placeholder="Search instructors by name, email or ID..." class="w-full pl-9 pr-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
          </div>
          <div class="flex items-center gap-3">
            <div class="relative">
              <select v-model="statusFilter" class="text-[13px] font-medium border border-slate-200 rounded-xl pl-4 pr-8 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white appearance-none">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="part time">Part Time</option>
              </select>
              <svg class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            <div class="relative">
              <select v-model="yearFilter" class="text-[13px] font-medium border border-slate-200 rounded-xl pl-4 pr-8 py-2.5 focus:outline-none focus:border-[#5138ed] text-slate-600 bg-white appearance-none">
                <option value="all">All Years</option>
                <option v-for="y in uniqueYears" :key="y" :value="y">{{ y }}</option>
              </select>
              <svg class="w-3.5 h-3.5 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            <button class="flex items-center gap-2 text-[13px] font-bold text-[#5138ed] border border-indigo-100 hover:bg-indigo-50 px-4 py-2.5 rounded-xl transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
              Filter
            </button>

          </div>
        </div>

        <!-- TABLE -->
        <table class="w-full">
          <thead>
            <tr class="bg-white border-b border-slate-100">
              <th class="text-left px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Instructor</th>
              <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">ID</th>
              <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Email</th>
              <th class="text-center px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Courses</th>
              <th class="text-center px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Credit</th>
              <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
              <th class="text-left px-4 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Year</th>
              <th class="text-center px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr v-for="inst in paginated" :key="inst.id" class="hover:bg-slate-50/40 transition-colors group">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div :class="[avatarColor(inst.id), 'w-10 h-10 rounded-full flex items-center justify-center text-[12px] font-bold text-white shrink-0']">{{ inst.avatar }}</div>
                  <div>
                    <p class="text-[13px] font-bold text-slate-800">{{ inst.name }}</p>
                    <p class="text-[11px] font-medium text-slate-400 mt-0.5">{{ inst.email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4"><span class="text-[12px] font-semibold text-slate-600">{{ inst.id_code }}</span></td>
              <td class="px-4 py-4"><span class="text-[12px] font-medium text-slate-600">{{ inst.email }}</span></td>
              <td class="px-4 py-4 text-center"><span class="text-[13px] font-semibold text-slate-700">{{ inst.courses }}</span></td>
              <td class="px-4 py-4 text-center"><span class="text-[13px] font-semibold text-slate-700">{{ inst.credit }}</span></td>
              <td class="px-4 py-4">
                <span :class="[statusBadge(inst.status), 'text-[11px] font-bold px-2.5 py-1 rounded-md capitalize']">{{ inst.status === 'active' ? 'Active' : 'Part Time' }}</span>
              </td>
              <td class="px-4 py-4"><span class="text-[12px] text-slate-600">{{ inst.year }}</span></td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                  <button @click="openView(inst)" class="w-7 h-7 rounded-lg flex items-center justify-center text-[#5138ed] bg-indigo-50 hover:bg-indigo-100 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                  <button @click="openEditPage(inst)" class="w-7 h-7 rounded-lg flex items-center justify-center text-sky-500 bg-sky-50 hover:bg-sky-100 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                  <button @click="confirmDelete(inst)" class="w-7 h-7 rounded-lg flex items-center justify-center text-rose-500 bg-rose-50 hover:bg-rose-100 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                </div>
              </td>
            </tr>
            <tr v-if="paginated.length === 0">
              <td colspan="7" class="px-6 py-12 text-center text-[13px] text-slate-400">No instructors found. Click "Add Instructor" to create one.</td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="flex items-center justify-between px-6 py-5 border-t border-slate-100 bg-white">
          <p class="text-[13px] text-slate-500 font-medium">Showing {{ Math.min((currentPage - 1) * perPage + 1, filtered.length) }} to {{ Math.min(currentPage * perPage, filtered.length) }} of {{ filtered.length }} instructors</p>
          <div class="flex items-center gap-2">
            <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-slate-50 disabled:opacity-40 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
            <button v-for="p in totalPages" :key="p" @click="currentPage = p" :class="[currentPage === p ? 'bg-[#5138ed] text-white border border-[#5138ed]' : 'text-slate-500 border border-slate-200 hover:bg-slate-50', 'w-8 h-8 rounded-lg text-[13px] font-bold transition-colors']">{{ p }}</button>
            <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 border border-slate-200 hover:bg-slate-50 disabled:opacity-40 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
          </div>
        </div>
      </div>

      <!-- ── Delete Modal ── -->
      <Teleport to="body">
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
          <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6">
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg></div>
            <h3 class="text-[16px] font-bold text-slate-800 mb-2">Remove Instructor?</h3>
            <p class="text-[13px] text-slate-500 mb-6">Are you sure you want to remove <span class="font-bold text-slate-700">{{ selectedInstructor?.name }}</span>?</p>
            <div class="flex gap-3">
              <button @click="showDeleteModal = false" class="flex-1 py-2.5 text-[13px] font-bold text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">Cancel</button>
              <button @click="deleteInstructor" :disabled="isLoading" class="flex-1 py-2.5 text-[13px] font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors disabled:opacity-70">
                {{ isLoading ? 'Removing...' : 'Remove' }}
              </button>
            </div>
          </div>
        </div>
      </Teleport>

    </template>

  </div>
</template>
