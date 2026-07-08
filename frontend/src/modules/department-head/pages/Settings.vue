<script setup lang="ts">
import { ref } from 'vue'

const saved = ref(false)

const profile = ref({
  fullName: 'Dr. Department Head',
  email: 'head.cs@wou.edu.et',
  phone: '+251 91 123 4567',
  office: 'Block A, Room 204'
})

const security = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const notifications = ref({
  emailOnInstructorJoin: true,
  emailOnCourseCreate: true,
  emailOnExamPublish: false,
  weeklyReport: true
})

const saveSettings = () => {
  saved.value = true
  setTimeout(() => { saved.value = false }, 3000)
}
</script>

<template>
  <div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-[22px] font-bold text-slate-800">Account Settings</h1>
        <p class="text-[13px] text-slate-500 mt-1">Manage your personal profile and notification preferences.</p>
      </div>
      <button @click="saveSettings" class="flex items-center gap-2 px-5 py-2.5 text-[13px] font-bold rounded-xl transition-all shadow-sm"
        :class="saved ? 'bg-emerald-500 text-white shadow-emerald-200' : 'bg-[#5138ed] hover:bg-indigo-700 text-white shadow-indigo-200'">
        <svg v-if="saved" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
        {{ saved ? 'Saved!' : 'Save Changes' }}
      </button>
    </div>

    <div class="grid grid-cols-2 gap-6">

      <!-- Profile Information -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-9 h-9 bg-indigo-50 rounded-xl flex items-center justify-center">
            <svg class="w-4.5 h-4.5 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
          </div>
          <div><h3 class="text-[14px] font-bold text-slate-800">Profile Information</h3><p class="text-[11px] text-slate-400">Your personal details</p></div>
        </div>
        <div class="space-y-4">
          <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Full Name</label><input v-model="profile.fullName" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
          <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Email Address</label><input v-model="profile.email" disabled class="w-full border border-slate-200 bg-slate-50 text-slate-500 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none"></div>
          <div class="grid grid-cols-2 gap-3">
            <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Phone Number</label><input v-model="profile.phone" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
            <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Office</label><input v-model="profile.office" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
          </div>
        </div>
      </div>

      <!-- Security -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-9 h-9 bg-rose-50 rounded-xl flex items-center justify-center">
            <svg class="w-4.5 h-4.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
          </div>
          <div><h3 class="text-[14px] font-bold text-slate-800">Security</h3><p class="text-[11px] text-slate-400">Update your password</p></div>
        </div>
        <div class="space-y-4">
          <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Current Password</label><input v-model="security.currentPassword" type="password" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
          <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">New Password</label><input v-model="security.newPassword" type="password" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
          <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Confirm New Password</label><input v-model="security.confirmPassword" type="password" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
        </div>
      </div>

      <!-- Notifications -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 col-span-2">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-9 h-9 bg-sky-50 rounded-xl flex items-center justify-center">
            <svg class="w-4.5 h-4.5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
          </div>
          <div><h3 class="text-[14px] font-bold text-slate-800">Notification Preferences</h3><p class="text-[11px] text-slate-400">When should we email you?</p></div>
        </div>
        <div class="grid grid-cols-2 gap-x-12 gap-y-4">
          <div v-for="(item) in [
            { label:'Email me when a new instructor joins', key:'emailOnInstructorJoin' },
            { label:'Email me when a new course is created', key:'emailOnCourseCreate' },
            { label:'Email me when an exam is published', key:'emailOnExamPublish' },
            { label:'Send me a weekly department report', key:'weeklyReport' },
          ]" :key="item.key" class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
            <span class="text-[13px] font-semibold text-slate-600">{{ item.label }}</span>
            <button @click="(notifications as any)[item.key] = !(notifications as any)[item.key]"
              :class="[(notifications as any)[item.key] ? 'bg-[#5138ed]' : 'bg-slate-200', 'relative inline-flex w-10 h-5 rounded-full transition-colors duration-200']">
              <span :class="[(notifications as any)[item.key] ? 'translate-x-5' : 'translate-x-0.5', 'inline-block w-4 h-4 bg-white rounded-full shadow mt-0.5 transition-transform duration-200']"></span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
