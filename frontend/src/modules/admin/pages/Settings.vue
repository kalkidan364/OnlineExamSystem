<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useSettingsStore } from '../../../store/settingsStore'
import { useAuthStore } from '../../auth/store/authStore'
import apiClient from '../../../core/api/apiClient'

const settingsStore = useSettingsStore()
const authStore = useAuthStore()
const saved = ref(false)

onMounted(() => {
  authStore.fetchCurrentUser()
})

// Profile Photo States
const fileInputRef = ref<HTMLInputElement | null>(null)
const isUploadingPhoto = ref(false)
const photoPreview = ref<string | null>(null)
const photoStatus = ref<{ type: 'success' | 'error' | null; message: string }>({ type: null, message: '' })

const profilePhotoUrl = computed(() => {
  if (photoPreview.value) return photoPreview.value
  const pic = authStore.user?.profile_picture_url || authStore.user?.profile_picture
  if (!pic) return 'https://i.pravatar.cc/150?u=admin123'
  if (pic.startsWith('http://') || pic.startsWith('https://') || pic.startsWith('data:')) {
    return pic
  }
  return `http://localhost:8000/storage/${pic}`
})

const hasCustomPhoto = computed(() => {
  return !!(authStore.user?.profile_picture || photoPreview.value)
})

const initials = computed(() => {
  const name = authStore.user?.name || 'Super Admin'
  const parts = name.trim().split(/\s+/)
  return parts.length >= 2
    ? (parts[0][0] + parts[1][0]).toUpperCase()
    : name.slice(0, 2).toUpperCase()
})

const triggerFileInput = () => {
  fileInputRef.value?.click()
}

const handleFileChange = async (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return

  // Validate format
  const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']
  if (!validTypes.includes(file.type)) {
    photoStatus.value = { type: 'error', message: 'Please select a valid image file (JPG, PNG, GIF, or WEBP).' }
    return
  }

  // Validate size (max 2MB)
  if (file.size > 2 * 1024 * 1024) {
    photoStatus.value = { type: 'error', message: 'Image size must be less than 2MB.' }
    return
  }

  // Show local preview immediately
  const reader = new FileReader()
  reader.onload = (e) => {
    photoPreview.value = e.target?.result as string
  }
  reader.readAsDataURL(file)

  isUploadingPhoto.value = true
  photoStatus.value = { type: null, message: '' }

  try {
    const formData = new FormData()
    formData.append('profile_picture', file)

    const response = await apiClient.post('/user/profile-photo', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    const newUrl = response.data.profile_picture_url
    const newPath = response.data.profile_picture

    authStore.updateProfilePhoto(newUrl, newPath)
    photoStatus.value = { type: 'success', message: 'Profile photo updated successfully!' }
    setTimeout(() => {
      photoStatus.value.type = null
      photoStatus.value.message = ''
    }, 4000)
  } catch (error: any) {
    console.error('Failed to upload profile photo:', error)
    photoPreview.value = null
    photoStatus.value = {
      type: 'error',
      message: error.response?.data?.message || 'Failed to upload profile photo. Please try again.',
    }
  } finally {
    isUploadingPhoto.value = false
    if (target) target.value = ''
  }
}

const handleRemovePhoto = async () => {
  if (!confirm('Are you sure you want to remove your profile photo?')) return

  isUploadingPhoto.value = true
  photoStatus.value = { type: null, message: '' }

  try {
    await apiClient.delete('/user/profile-photo')
    photoPreview.value = null
    authStore.updateProfilePhoto('', '')
    photoStatus.value = { type: 'success', message: 'Profile photo removed successfully.' }
    setTimeout(() => {
      photoStatus.value.type = null
      photoStatus.value.message = ''
    }, 4000)
  } catch (error: any) {
    console.error('Failed to remove profile photo:', error)
    photoStatus.value = {
      type: 'error',
      message: error.response?.data?.message || 'Failed to remove profile photo.',
    }
  } finally {
    isUploadingPhoto.value = false
  }
}

// General Settings
const general = ref({
  universityName: 'Wollo University',
  systemTitle:    'Online Examination System',
  timezone:       'Africa/Addis_Ababa',
  language:       'English',
  academicYear:   settingsStore.academicYear,
  semester:       settingsStore.semester,
})

// Sync form with store when store loads
watch(
  () => [settingsStore.academicYear, settingsStore.semester],
  ([newYear, newSem]) => {
    general.value.academicYear = newYear
    general.value.semester = newSem
  },
  { immediate: true }
)

// Security / Change Password Settings
const security = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const passwordStatus = ref<{ type: 'success' | 'error' | null; message: string }>({ type: null, message: '' })
const isChangingPassword = ref(false)

const changePassword = async () => {
  if (!security.value.currentPassword) {
    passwordStatus.value = { type: 'error', message: 'Please enter your current password.' }
    return
  }
  if (security.value.newPassword.length < 8) {
    passwordStatus.value = { type: 'error', message: 'New password must be at least 8 characters long.' }
    return
  }
  if (security.value.newPassword !== security.value.confirmPassword) {
    passwordStatus.value = { type: 'error', message: 'New passwords do not match.' }
    return
  }

  isChangingPassword.value = true
  passwordStatus.value = { type: null, message: '' }

  try {
    await apiClient.put('/user/change-password', {
      current_password:          security.value.currentPassword,
      new_password:              security.value.newPassword,
      new_password_confirmation: security.value.confirmPassword,
    })

    passwordStatus.value = { type: 'success', message: 'Password changed successfully!' }
    security.value.currentPassword = ''
    security.value.newPassword = ''
    security.value.confirmPassword = ''
    setTimeout(() => { passwordStatus.value.type = null }, 4000)

  } catch (error: any) {
    const errData = error.response?.data
    if (errData?.errors) {
      const firstField = Object.keys(errData.errors)[0]
      passwordStatus.value = { type: 'error', message: errData.errors[firstField][0] }
    } else {
      passwordStatus.value = {
        type: 'error',
        message: errData?.message || 'Failed to change password. Please try again.',
      }
    }
  } finally {
    isChangingPassword.value = false
  }
}

const saveSettings = async () => {
  try {
    await settingsStore.updateTerm(general.value.academicYear, general.value.semester)
    saved.value = true
    setTimeout(() => { saved.value = false }, 3000)
  } catch (error) {
    alert('Failed to save settings. Please try again.')
  }
}
</script>

<template>
  <div class="space-y-6">

    <!-- Top Admin Profile & Photo Card -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-6">

        <div class="flex flex-col sm:flex-row items-center gap-6 text-center sm:text-left">
          <!-- Profile Avatar with Camera Overlay -->
          <div class="relative group">
            <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-slate-50 shadow-md bg-slate-100 flex items-center justify-center ring-2 ring-slate-100">
              <img
                v-if="profilePhotoUrl"
                :src="profilePhotoUrl"
                alt="Admin Profile"
                class="w-full h-full object-cover"
              />
              <span v-else class="text-2xl font-black text-slate-500">
                {{ initials }}
              </span>
            </div>

            <!-- Camera button overlay -->
            <button
              type="button"
              @click="triggerFileInput"
              class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-[#5138ed] hover:bg-indigo-700 text-white shadow-md flex items-center justify-center transition-all hover:scale-110"
              title="Change Profile Photo"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
            </button>
          </div>

          <!-- User Info & Guidelines -->
          <div>
            <div class="flex items-center justify-center sm:justify-start gap-2.5">
              <h2 class="text-lg font-bold text-slate-800">{{ authStore.user?.name || 'Super Admin' }}</h2>
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-100">
                Administrator
              </span>
            </div>
            <p class="text-[13px] text-slate-500 font-medium mt-0.5">{{ authStore.user?.email || 'admin@wollo.edu.et' }}</p>
            <div class="flex items-center justify-center sm:justify-start gap-1.5 mt-2 text-[11px] text-slate-400">
              <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <span>Allowed formats: JPG, PNG, GIF, WEBP. Maximum file size: 2MB.</span>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3">
          <input
            type="file"
            ref="fileInputRef"
            accept="image/png, image/jpeg, image/jpg, image/gif, image/webp"
            class="hidden"
            @change="handleFileChange"
          />

          <button
            type="button"
            @click="triggerFileInput"
            :disabled="isUploadingPhoto"
            class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-[13px] font-bold transition-all shadow-sm flex items-center gap-2 disabled:opacity-50"
          >
            <svg v-if="isUploadingPhoto" class="animate-spin w-4 h-4 text-[#5138ed]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <svg v-else class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            {{ isUploadingPhoto ? 'Uploading...' : 'Change Photo' }}
          </button>

          <button
            v-if="hasCustomPhoto"
            type="button"
            @click="handleRemovePhoto"
            :disabled="isUploadingPhoto"
            class="px-3.5 py-2.5 rounded-xl border border-rose-100 bg-rose-50/60 hover:bg-rose-100 text-rose-600 text-[13px] font-bold transition-all flex items-center gap-1.5 disabled:opacity-50"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            Remove
          </button>
        </div>

      </div>

      <!-- Photo Status Message -->
      <div v-if="photoStatus.message" :class="photoStatus.type === 'success' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100'" class="mt-4 p-3 text-[12px] font-semibold border rounded-xl flex items-center gap-2">
        <svg v-if="photoStatus.type === 'success'" class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <svg v-else class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span>{{ photoStatus.message }}</span>
      </div>
    </div>

    <!-- Page Actions -->
    <div class="flex items-center justify-end">
      <button @click="saveSettings" :disabled="settingsStore.isLoading" class="flex items-center gap-2 px-5 py-2.5 text-[13px] font-bold rounded-xl transition-all shadow-sm disabled:opacity-50"
        :class="saved ? 'bg-emerald-500 text-white shadow-emerald-200' : 'bg-[#5138ed] hover:bg-indigo-700 text-white shadow-indigo-200'">
        <svg v-if="settingsStore.isLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        <svg v-else-if="saved" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
        {{ settingsStore.isLoading ? 'Saving...' : (saved ? 'Saved!' : 'Save Changes') }}
      </button>
    </div>

    <!-- 2 Columns Grid: General Settings & Change Password -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <!-- General Settings -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-9 h-9 bg-indigo-50 rounded-xl flex items-center justify-center">
            <svg class="w-4.5 h-4.5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
          </div>
          <div><h3 class="text-[14px] font-bold text-slate-800">General Settings</h3><p class="text-[11px] text-slate-400">Institution and system info</p></div>
        </div>
        <div class="space-y-4">
          <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">University Name</label><input v-model="general.universityName" readonly class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] bg-slate-50 text-slate-500 cursor-not-allowed focus:outline-none"></div>
          <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">System Title</label><input v-model="general.systemTitle" readonly class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] bg-slate-50 text-slate-500 cursor-not-allowed focus:outline-none"></div>
          <div class="grid grid-cols-2 gap-3">
            <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Timezone</label>
              <input v-model="general.timezone" readonly class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] bg-slate-50 text-slate-500 cursor-not-allowed focus:outline-none">
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-600 mb-1.5">Language</label>
              <!-- English only as requested -->
              <input v-model="general.language" readonly class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] bg-slate-50 text-slate-500 cursor-not-allowed focus:outline-none font-medium">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Academic Year</label><input v-model="general.academicYear" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
            <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Semester</label>
              <select v-model="general.semester" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] bg-white">
                <option>First Semester</option><option>Second Semester</option><option>Summer</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Security / Change Password Settings -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-9 h-9 bg-rose-50 rounded-xl flex items-center justify-center">
            <svg class="w-4.5 h-4.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
          </div>
          <div><h3 class="text-[14px] font-bold text-slate-800">Change Password</h3><p class="text-[11px] text-slate-400">Update your account password</p></div>
        </div>
        <div class="space-y-4">
          
          <div v-if="passwordStatus.type" :class="passwordStatus.type === 'success' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100'" class="p-3 text-[12px] font-medium border rounded-xl flex items-center gap-2">
            <svg v-if="passwordStatus.type === 'success'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ passwordStatus.message }}
          </div>

          <div>
            <label class="block text-[12px] font-bold text-slate-600 mb-1.5">Current Password</label>
            <input v-model="security.currentPassword" type="password" placeholder="Enter your current password" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
          </div>
          <div>
            <label class="block text-[12px] font-bold text-slate-600 mb-1.5">New Password</label>
            <input v-model="security.newPassword" type="password" placeholder="Minimum 8 characters" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
          </div>
          <div>
            <label class="block text-[12px] font-bold text-slate-600 mb-1.5">Confirm New Password</label>
            <input v-model="security.confirmPassword" type="password" placeholder="Re-enter new password" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
          </div>

          <div class="pt-2">
            <button @click="changePassword" :disabled="isChangingPassword || !security.currentPassword || !security.newPassword" 
              class="w-full flex justify-center items-center gap-2 px-5 py-2.5 text-[13px] font-bold rounded-xl transition-all shadow-sm bg-slate-900 hover:bg-slate-800 text-white shadow-slate-200 disabled:opacity-50 disabled:cursor-not-allowed">
              <svg v-if="isChangingPassword" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              {{ isChangingPassword ? 'Updating Password...' : 'Update Password' }}
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<style scoped>
/* Scoped styles */
</style>
