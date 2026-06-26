<script setup lang="ts">
import { useAuthStore } from '../../../auth/store/authStore'

const authStore = useAuthStore()
</script>

<template>
  <div>
    <h2 class="text-[15px] font-bold text-slate-800 mb-6">Personal Information</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
      
      <!-- Full Name -->
      <div class="space-y-2">
        <label class="block text-[11px] font-bold text-slate-700">Full Name</label>
        <input 
          type="text" 
          :value="authStore.user?.name || 'Dr. Abebe Kebede'"
          class="w-full px-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors bg-white text-slate-800"
        />
      </div>

      <!-- Department -->
      <div class="space-y-2">
        <label class="block text-[11px] font-bold text-slate-700">Department</label>
        <input 
          type="text" 
          value="Computer Science"
          class="w-full px-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors bg-white text-slate-800"
        />
      </div>

      <!-- Email Address -->
      <div class="space-y-2">
        <label class="block text-[11px] font-bold text-slate-700">Email Address</label>
        <input 
          type="email" 
          :value="authStore.user?.email || 'abebe.kebede@wu.edu.et'"
          class="w-full px-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors bg-white text-slate-800"
        />
      </div>

      <!-- Position -->
      <div class="space-y-2">
        <label class="block text-[11px] font-bold text-slate-700">Position</label>
        <input 
          type="text" 
          value="Instructor"
          class="w-full px-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors bg-white text-slate-800"
        />
      </div>

      <!-- Phone Number -->
      <div class="space-y-2">
        <label class="block text-[11px] font-bold text-slate-700">Phone Number</label>
        <input 
          type="tel" 
          value="+251 91 234 5678"
          class="w-full px-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors bg-white text-slate-800"
        />
      </div>

      <!-- Office Location -->
      <div class="space-y-2">
        <label class="block text-[11px] font-bold text-slate-700">Office Location</label>
        <input 
          type="text" 
          value="CS Building, Room 205"
          class="w-full px-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors bg-white text-slate-800"
        />
      </div>

      <!-- Gender -->
      <div class="space-y-2">
        <label class="block text-[11px] font-bold text-slate-700">Gender</label>
        <div class="relative">
          <select class="w-full appearance-none px-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] bg-white text-slate-800 cursor-pointer">
            <option>Male</option>
            <option>Female</option>
            <option>Other</option>
          </select>
          <svg class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </div>
      </div>

      <!-- Address -->
      <div class="space-y-2">
        <label class="block text-[11px] font-bold text-slate-700">Address</label>
        <input 
          type="text" 
          value="Dessie, Wollo, Ethiopia"
          class="w-full px-4 py-2.5 text-[13px] border border-slate-200 rounded-xl focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors bg-white text-slate-800"
        />
      </div>

    </div>

    <div class="mt-8">
      <button class="px-6 py-2.5 bg-[#5138ed] text-white text-[13px] font-bold rounded-xl hover:bg-indigo-600 transition-colors shadow-sm">
        Save Changes
      </button>
    </div>
    
  </div>
</template>
