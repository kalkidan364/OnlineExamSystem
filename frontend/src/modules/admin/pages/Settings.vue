<script setup lang="ts">
import { ref } from 'vue'

const saved = ref(false)

const general = ref({
  universityName: 'Wollo University',
  systemTitle:    'Online Examination System',
  timezone:       'Africa/Addis_Ababa',
  language:       'English',
  academicYear:   '2024/2025',
  semester:       'Second Semester',
})

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
  sessionTimeout:    30,
  maxLoginAttempts:  5,
  require2FA:        false,
  enforceStrongPass: true,
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
        <h1 class="text-[22px] font-bold text-slate-800">System Settings</h1>
        <p class="text-[13px] text-slate-500 mt-1">Configure global system preferences and defaults.</p>
      </div>
      <button @click="saveSettings" class="flex items-center gap-2 px-5 py-2.5 text-[13px] font-bold rounded-xl transition-all shadow-sm"
        :class="saved ? 'bg-emerald-500 text-white shadow-emerald-200' : 'bg-[#5138ed] hover:bg-indigo-700 text-white shadow-indigo-200'">
        <svg v-if="saved" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
        {{ saved ? 'Saved!' : 'Save Changes' }}
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
          <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">University Name</label><input v-model="general.universityName" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
          <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">System Title</label><input v-model="general.systemTitle" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
          <div class="grid grid-cols-2 gap-3">
            <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Timezone</label>
              <select v-model="general.timezone" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] bg-white">
                <option>Africa/Addis_Ababa</option><option>UTC</option><option>Africa/Nairobi</option>
              </select>
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

      <!-- Exam Defaults -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-9 h-9 bg-amber-50 rounded-xl flex items-center justify-center">
            <svg class="w-4.5 h-4.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
          </div>
          <div><h3 class="text-[14px] font-bold text-slate-800">Exam Defaults</h3><p class="text-[11px] text-slate-400">Default values for new exams</p></div>
        </div>
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-3">
            <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Default Duration (min)</label><input v-model="exam.defaultDuration" type="number" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
            <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Pass Mark Default (%)</label><input v-model="exam.passMarkDefault" type="number" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
          </div>
          <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Max Attempts Per Exam</label><input v-model="exam.maxAttempts" type="number" min="1" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
          <!-- Toggle switches -->
          <div class="space-y-3 pt-1">
            <div v-for="(item, key) in [
              { label:'Auto-publish results after grading', key:'autoPublishResults', model: exam.autoPublishResults },
              { label:'Allow late submissions',              key:'allowLateSubmit',    model: exam.allowLateSubmit    },
              { label:'Shuffle questions by default',        key:'shuffleQuestions',   model: exam.shuffleQuestions   },
            ]" :key="key" class="flex items-center justify-between">
              <span class="text-[12px] font-semibold text-slate-600">{{ item.label }}</span>
              <button @click="(exam as any)[item.key] = !(exam as any)[item.key]"
                :class="[(exam as any)[item.key] ? 'bg-[#5138ed]' : 'bg-slate-200', 'relative inline-flex w-10 h-5 rounded-full transition-colors duration-200']">
                <span :class="[(exam as any)[item.key] ? 'translate-x-5' : 'translate-x-0.5', 'inline-block w-4 h-4 bg-white rounded-full shadow mt-0.5 transition-transform duration-200']"></span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Notification Settings -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-9 h-9 bg-sky-50 rounded-xl flex items-center justify-center">
            <svg class="w-4.5 h-4.5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
          </div>
          <div><h3 class="text-[14px] font-bold text-slate-800">Notifications</h3><p class="text-[11px] text-slate-400">Email and system notification triggers</p></div>
        </div>
        <div class="space-y-4">
          <div v-for="(item) in [
            { label:'Email when student submits exam',  key:'emailOnSubmit'  },
            { label:'Email when results are published', key:'emailOnPublish' },
            { label:'Email when exam is graded',        key:'emailOnGrade'   },
            { label:'Enable system-wide alerts',        key:'systemAlerts'   },
          ]" :key="item.key" class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
            <span class="text-[13px] font-semibold text-slate-600">{{ item.label }}</span>
            <button @click="(notifications as any)[item.key] = !(notifications as any)[item.key]"
              :class="[(notifications as any)[item.key] ? 'bg-[#5138ed]' : 'bg-slate-200', 'relative inline-flex w-10 h-5 rounded-full transition-colors duration-200']">
              <span :class="[(notifications as any)[item.key] ? 'translate-x-5' : 'translate-x-0.5', 'inline-block w-4 h-4 bg-white rounded-full shadow mt-0.5 transition-transform duration-200']"></span>
            </button>
          </div>
        </div>
      </div>

      <!-- Security Settings -->
      <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-9 h-9 bg-rose-50 rounded-xl flex items-center justify-center">
            <svg class="w-4.5 h-4.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
          </div>
          <div><h3 class="text-[14px] font-bold text-slate-800">Security</h3><p class="text-[11px] text-slate-400">Access control and session policies</p></div>
        </div>
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-3">
            <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Session Timeout (min)</label><input v-model="security.sessionTimeout" type="number" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
            <div><label class="block text-[12px] font-bold text-slate-600 mb-1.5">Max Login Attempts</label><input v-model="security.maxLoginAttempts" type="number" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]"></div>
          </div>
          <div class="space-y-3 pt-1">
            <div v-for="item in [
              { label:'Require 2-Factor Authentication', key:'require2FA'        },
              { label:'Enforce strong passwords',        key:'enforceStrongPass' },
            ]" :key="item.key" class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
              <span class="text-[13px] font-semibold text-slate-600">{{ item.label }}</span>
              <button @click="(security as any)[item.key] = !(security as any)[item.key]"
                :class="[(security as any)[item.key] ? 'bg-[#5138ed]' : 'bg-slate-200', 'relative inline-flex w-10 h-5 rounded-full transition-colors duration-200']">
                <span :class="[(security as any)[item.key] ? 'translate-x-5' : 'translate-x-0.5', 'inline-block w-4 h-4 bg-white rounded-full shadow mt-0.5 transition-transform duration-200']"></span>
              </button>
            </div>
          </div>
          <!-- Danger Zone -->
          <div class="mt-4 p-4 bg-rose-50/60 border border-rose-100 rounded-xl">
            <p class="text-[12px] font-bold text-rose-600 mb-2">Danger Zone</p>
            <div class="flex items-center justify-between">
              <div><p class="text-[12px] font-bold text-slate-700">Reset All Settings</p><p class="text-[11px] text-slate-400">Restore all settings to factory defaults</p></div>
              <button class="px-3 py-1.5 text-[11px] font-bold text-rose-600 border border-rose-200 rounded-lg hover:bg-rose-100 transition-colors">Reset</button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
