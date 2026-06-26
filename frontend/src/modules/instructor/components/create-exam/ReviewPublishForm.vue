<script setup lang="ts">
const emit = defineEmits(['cancel', 'edit-step'])
</script>

<template>
  <div class="mb-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h2 class="text-[18px] font-bold text-slate-800">Review & Publish</h2>
        <p class="text-[13px] text-slate-500 mt-1">Review all exam details before publishing. You can go back to previous steps to make changes.</p>
      </div>
      <button class="px-5 py-2 border border-[#5138ed] text-[#5138ed] text-[12px] font-bold rounded-xl hover:bg-indigo-50 transition-colors flex items-center gap-2">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
        Edit Sections
      </button>
    </div>

    <div class="space-y-6 mb-6">
      
      <!-- 1. Exam Information -->
      <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-[14px] font-bold text-slate-800">1. Exam Information</h3>
          </div>
          <button @click="emit('edit-step', 1)" class="flex items-center gap-1.5 text-[12px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            Edit
          </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
          <div>
            <span class="block text-[11px] font-bold text-slate-500 mb-1">Exam Title</span>
            <span class="text-[13px] font-medium text-slate-800">Database Systems Mid Exam</span>
          </div>
          <div>
            <span class="block text-[11px] font-bold text-slate-500 mb-1">Course</span>
            <span class="text-[13px] font-medium text-slate-800">Database Systems (CS 304)</span>
          </div>
          <div>
            <span class="block text-[11px] font-bold text-slate-500 mb-1">Exam Type</span>
            <span class="inline-block px-3 py-1 bg-indigo-50 text-[#5138ed] text-[11px] font-bold rounded-lg">Mid Exam</span>
          </div>
          <div>
            <span class="block text-[11px] font-bold text-slate-500 mb-1">Total Marks</span>
            <span class="text-[13px] font-medium text-slate-800">100</span>
          </div>
          <div>
            <span class="block text-[11px] font-bold text-slate-500 mb-1">Passing Marks</span>
            <span class="text-[13px] font-medium text-slate-800">60 (60%)</span>
          </div>
        </div>
        <div>
          <span class="block text-[11px] font-bold text-slate-500 mb-1">Description</span>
          <p class="text-[12px] text-slate-600 font-medium leading-relaxed">This mid term exam covers the fundamental concepts of database systems including ER modeling, SQL, normalization, and transactions.</p>
        </div>
      </div>

      <!-- 2. Questions -->
      <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
            </div>
            <h3 class="text-[14px] font-bold text-slate-800">2. Questions</h3>
          </div>
          <button @click="emit('edit-step', 2)" class="flex items-center gap-1.5 text-[12px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            Edit
          </button>
        </div>

        <div class="flex flex-wrap gap-4 mb-6">
          <div class="flex-1 min-w-[120px] p-4 border border-slate-100 rounded-xl bg-slate-50 flex flex-col items-center justify-center">
            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-[#5138ed] mb-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <span class="text-[11px] font-bold text-slate-500 mb-0.5">Total Questions</span>
            <span class="text-[16px] font-black text-slate-800">40</span>
          </div>

          <div class="flex-1 min-w-[120px] p-4 border border-slate-100 rounded-xl bg-white flex flex-col items-center justify-center">
            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 mb-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </div>
            <span class="text-[11px] font-bold text-slate-500 mb-0.5">MCQ</span>
            <span class="text-[16px] font-black text-slate-800">20 <span class="text-[11px] text-slate-400 font-medium">(50%)</span></span>
          </div>

          <div class="flex-1 min-w-[120px] p-4 border border-slate-100 rounded-xl bg-white flex flex-col items-center justify-center">
            <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center text-orange-500 mb-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </div>
            <span class="text-[11px] font-bold text-slate-500 mb-0.5">Short Answer</span>
            <span class="text-[16px] font-black text-slate-800">10 <span class="text-[11px] text-slate-400 font-medium">(25%)</span></span>
          </div>

          <div class="flex-1 min-w-[120px] p-4 border border-slate-100 rounded-xl bg-white flex flex-col items-center justify-center">
            <div class="w-8 h-8 rounded-full bg-rose-50 flex items-center justify-center text-rose-500 mb-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <span class="text-[11px] font-bold text-slate-500 mb-0.5">Essay</span>
            <span class="text-[16px] font-black text-slate-800">8 <span class="text-[11px] text-slate-400 font-medium">(20%)</span></span>
          </div>

          <div class="flex-1 min-w-[120px] p-4 border border-slate-100 rounded-xl bg-white flex flex-col items-center justify-center">
            <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500 mb-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
            </div>
            <span class="text-[11px] font-bold text-slate-500 mb-0.5">True/False</span>
            <span class="text-[16px] font-black text-slate-800">2 <span class="text-[11px] text-slate-400 font-medium">(5%)</span></span>
          </div>
        </div>

        <div class="flex items-center text-[12px] font-bold text-slate-500">
          Total Marks: <span class="text-slate-800 ml-1">100</span>
          <span class="mx-3 text-slate-300">|</span>
          Average Marks per Question: <span class="text-slate-800 ml-1">2.5</span>
        </div>
      </div>

      <!-- 3. Exam Settings -->
      <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <h3 class="text-[14px] font-bold text-slate-800">3. Exam Settings</h3>
          </div>
          <button @click="emit('edit-step', 3)" class="flex items-center gap-1.5 text-[12px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            Edit
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="space-y-4">
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-[#5138ed] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Duration</span>
                <span class="text-[11px] font-medium text-slate-500">1h 30m</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-[#5138ed] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Passing Marks</span>
                <span class="text-[11px] font-medium text-slate-500">60%</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-[#5138ed] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Maximum Attempts</span>
                <span class="text-[11px] font-medium text-slate-500">1</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-[#5138ed] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Time Zone</span>
                <span class="text-[11px] font-medium text-slate-500">(UTC+03:00) Addis Ababa</span>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-[#5138ed] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Start Date</span>
                <span class="text-[11px] font-medium text-slate-500">May 25, 2025 09:00 AM</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-[#5138ed] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">End Date</span>
                <span class="text-[11px] font-medium text-slate-500">May 25, 2025 10:30 AM</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-[#5138ed] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Attempts Allowed</span>
                <span class="text-[11px] font-medium text-slate-500">1</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-[#5138ed] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Late Submission</span>
                <span class="text-[11px] font-medium text-slate-500">Not Allowed</span>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Shuffle Questions</span>
                <span class="text-[11px] font-medium text-slate-500">Enabled</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Shuffle Answer Options</span>
                <span class="text-[11px] font-medium text-slate-500">Enabled</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Show Review Screen</span>
                <span class="text-[11px] font-medium text-slate-500">Enabled</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Auto Submit on Time Finish</span>
                <span class="text-[11px] font-medium text-slate-500">Enabled</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 4. Security Settings -->
      <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <h3 class="text-[14px] font-bold text-slate-800">4. Security Settings</h3>
          </div>
          <button @click="emit('edit-step', 3)" class="flex items-center gap-1.5 text-[12px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            Edit
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="space-y-4">
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Fullscreen Mode</span>
                <span class="text-[11px] font-medium text-slate-500">Enabled</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Right Click</span>
                <span class="text-[11px] font-medium text-slate-500">Disabled</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Copy & Paste</span>
                <span class="text-[11px] font-medium text-slate-500">Disabled</span>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Browser Tab Monitoring</span>
                <span class="text-[11px] font-medium text-slate-500">Enabled</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Calculator</span>
                <span class="text-[11px] font-medium text-slate-500">Disabled</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Webcam Monitoring</span>
                <span class="text-[11px] font-medium text-slate-500">Disabled</span>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-emerald-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">Allow Backtracking</span>
                <span class="text-[11px] font-medium text-slate-500">Enabled</span>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <svg class="w-4 h-4 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              <div>
                <span class="block text-[11px] font-bold text-slate-800">One Question at a Time</span>
                <span class="text-[11px] font-medium text-slate-500">Disabled</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between pt-2 pb-10">
      <button @click="emit('cancel')" class="px-6 py-2.5 border border-slate-200 text-slate-600 font-bold text-[13px] rounded-xl hover:bg-slate-50 transition-colors">
        Cancel
      </button>
      
      <div class="flex items-center gap-3">
        <button class="px-6 py-2.5 border border-slate-200 text-[#5138ed] font-bold text-[13px] rounded-xl hover:border-indigo-200 hover:bg-indigo-50 transition-colors">
          Save as Draft
        </button>
        <button class="px-8 py-2.5 bg-[#5138ed] hover:bg-indigo-600 text-white font-bold text-[13px] rounded-xl shadow-sm transition-colors flex items-center gap-2">
          Publish Exam
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </button>
      </div>
    </div>
  </div>
</template>
