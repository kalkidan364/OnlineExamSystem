<script setup lang="ts">
import { ref } from 'vue'
import type { StudentProfile } from '../types'

const props = defineProps<{
  profile: StudentProfile
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'update-profile', updated: StudentProfile): void
}>()

const name = ref(props.profile.name)
const email = ref(props.profile.email)
const avatar = ref(props.profile.avatar)
const isSaved = ref(false)

const handleSubmit = () => {
  emit('update-profile', {
    ...props.profile,
    name: name.value,
    email: email.value,
    avatar: avatar.value
  })
  isSaved.value = true
  setTimeout(() => {
    isSaved.value = false
    emit('close')
  }, 1500)
}
</script>

<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs">
    <div class="relative w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl animate-in fade-in zoom-in-95 duration-150">
      <button
        @click="emit('close')"
        class="absolute top-4 right-4 rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors"
      >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
      </button>

      <div class="flex items-center gap-3 mb-6">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
        <div>
          <h3 class="text-base font-black text-slate-900">Registered Profile</h3>
          <p class="text-xs text-slate-500 font-medium">Verify or adjust your academic enrollment details</p>
        </div>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        
        <!-- Avatar preview and input -->
        <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100 mb-4">
          <img
            :src="avatar"
            alt="Avatar Preview"
            class="h-16 w-16 rounded-full object-cover ring-2 ring-indigo-500/20"
            referrerpolicy="no-referrer"
          />
          <div class="space-y-1 flex-1">
            <label class="text-[10px] uppercase font-bold text-slate-400 block">Avatar URL</label>
            <input
              type="text"
              v-model="avatar"
              class="w-full text-xs font-mono text-slate-600 bg-white border border-slate-200 px-3 py-1.5 rounded-lg focus:border-indigo-500 focus:outline-hidden"
            />
          </div>
        </div>

        <div class="space-y-1.5">
          <label class="text-[10px] uppercase font-bold text-slate-400 block">Student Spelling Name</label>
          <input
            type="text"
            required
            v-model="name"
            class="w-full text-xs font-sans text-slate-800 bg-white border border-slate-200 px-3.5 py-2.5 rounded-xl focus:border-indigo-500 focus:outline-hidden"
          />
        </div>

        <div class="space-y-1.5">
          <label class="text-[10px] uppercase font-bold text-slate-400 block">Registered Email Address</label>
          <input
            type="email"
            required
            v-model="email"
            class="w-full text-xs font-mono text-slate-800 bg-white border border-slate-200 px-3.5 py-2.5 rounded-xl focus:border-indigo-500 focus:outline-hidden"
          />
        </div>

        <!-- Academic Meta fields (read only for security!) -->
        <div class="grid grid-cols-2 gap-3 pt-2">
          <div class="bg-slate-50/50 p-3 rounded-lg border border-slate-100">
            <span class="text-[9px] uppercase font-bold text-slate-400 block">Department code</span>
            <span class="text-xs font-bold text-slate-800 mt-0.5 block">{{ profile.department }}</span>
          </div>
          <div class="bg-slate-50/50 p-3 rounded-lg border border-slate-100">
            <span class="text-[9px] uppercase font-bold text-slate-400 block">Academic ID Code</span>
            <span class="text-xs font-mono font-bold text-slate-800 mt-0.5 block">{{ profile.id }}</span>
          </div>
        </div>

        <div class="rounded-lg bg-emerald-50 p-3 border border-emerald-100 text-[11px] text-emerald-800 flex items-start gap-2">
          <svg class="h-4.5 w-4.5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
          <span>Profile verification complete. These details are synchronized securely with the Wollo University student directory.</span>
        </div>

        <!-- Form CTAs -->
        <div class="pt-4 flex gap-3">
          <button
            type="button"
            @click="emit('close')"
            class="flex-1 rounded-xl border border-slate-200 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors"
          >
            Discard Changes
          </button>
          <button
            type="submit"
            class="flex-1 rounded-xl bg-indigo-600 py-2.5 text-xs font-bold text-white hover:bg-indigo-700 transition-colors flex items-center justify-center gap-1.5"
          >
            <template v-if="isSaved">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h6M16 3h5m0 0v5m0-5l-6 6"></path></svg>
              <span>Details Saved!</span>
            </template>
            <template v-else>
              <span>Save Registry</span>
            </template>
          </button>
        </div>

      </form>
    </div>
  </div>
</template>
