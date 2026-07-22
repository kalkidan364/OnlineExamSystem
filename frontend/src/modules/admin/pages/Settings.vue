<script setup lang="ts">
import { ref, watch } from 'vue'
import { useSettingsStore } from '../../../store/settingsStore'
import apiClient from '../../../core/api/apiClient'

const settingsStore = useSettingsStore()
const saved = ref(false)

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

const exam = ref({
  defaultDuration:    90,
  maxAttempts:        1,
  autoPublishResults: true,
  allowLateSubmit:    false,
  shuffleQuestions:   true,
  passMarkDefault:    50,
})

const notifications = ref({
  emailOnSubmit:    true,
  emailOnPublish:   true,
  emailOnGrade:     false,
  systemAlerts:     true,
})

const security = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const passwordStatus = ref<{type: 'success' | 'error' | null, message: string}>({ type: null, message: '' })
const isChangingPassword = ref(false)

const changePassword = async () => {
  // Client-side validation
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
    // Clear the form
    security.value.currentPassword = ''
    security.value.newPassword = ''
    security.value.confirmPassword = ''
    setTimeout(() => { passwordStatus.value.type = null }, 4000)

  } catch (error: any) {
    // Laravel validation errors come as: { errors: { current_password: ['...'] } }
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

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">System Settings</h1>
        <p class="text-[13px] text-slate-500 mt-1">Configure global system preferences and defaults.</p>
      </div>
      <button @click="saveSettings" :disabled="settingsStore.isLoading" class="flex items-center gap-2 px-5 py-2.5 text-[13px] font-bold rounded-xl transition-all shadow-sm disabled:opacity-50"
        :class="saved ? 'bg-emerald-500 text-white shadow-emerald-200' : 'bg-[#5138ed] hover:bg-indigo-700 text-white shadow-indigo-200'">
        <svg v-if="settingsStore.isLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        <svg v-else-if="saved" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
        {{ settingsStore.isLoading ? 'Saving...' : (saved ? 'Saved!' : 'Save Changes') }}
      </button>
    </div>

    <div class="grid grid-cols-2 gap-6">

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
            <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Language</label>
              <select v-model="general.language" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] bg-white">
                <option>English</option><option>Amharic</option>
              </select>
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
