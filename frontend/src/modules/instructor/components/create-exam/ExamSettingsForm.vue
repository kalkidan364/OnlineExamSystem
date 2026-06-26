<script setup lang="ts">
const emit = defineEmits(['cancel', 'next'])
</script>

<template>
  <div class="mb-6">
    <!-- Header -->
    <div class="mb-8">
      <h2 class="text-[16px] font-bold text-slate-800">Exam Settings</h2>
      <p class="text-[13px] text-slate-500 mt-1">Configure the behavior and rules for this exam.</p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
      
      <!-- General Settings Card -->
      <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
          <h3 class="text-[13px] font-bold text-slate-800">General Settings</h3>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
          <div>
            <label class="block text-[12px] font-bold text-slate-700 mb-2">Exam Duration <span class="text-rose-500">*</span></label>
            <div class="flex items-center gap-2">
              <input type="text" value="01" class="w-16 border border-slate-200 rounded-xl px-3 py-2.5 text-[13px] text-center text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              <span class="text-slate-400 font-bold">:</span>
              <input type="text" value="30" class="w-16 border border-slate-200 rounded-xl px-3 py-2.5 text-[13px] text-center text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              <select class="flex-1 border border-slate-200 rounded-xl px-3 py-2.5 text-[13px] text-slate-600 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_0.75rem_center]">
                <option>Hours</option>
                <option>Minutes</option>
              </select>
            </div>
            <p class="text-[10px] text-slate-400 mt-2 font-medium">Set the total duration for the exam</p>
          </div>
          <div>
            <label class="block text-[12px] font-bold text-slate-700 mb-2">Passing Marks <span class="text-rose-500">*</span></label>
            <div class="relative">
              <input type="number" value="60" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[13px] text-slate-400 font-bold">%</span>
            </div>
            <p class="text-[10px] text-slate-400 mt-2 font-medium">Minimum percentage required to pass</p>
          </div>
        </div>

        <div>
          <label class="block text-[12px] font-bold text-slate-700 mb-2">Maximum Attempts</label>
          <select class="w-[200px] border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-600 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_1rem_center]">
            <option>1</option>
            <option>2</option>
            <option>Unlimited</option>
          </select>
          <p class="text-[10px] text-slate-400 mt-2 font-medium">Number of attempts allowed</p>
        </div>
      </div>

      <!-- Availability Card -->
      <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          <h3 class="text-[13px] font-bold text-slate-800">Availability</h3>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-5">
          <div>
            <label class="block text-[12px] font-bold text-slate-700 mb-2">Start Date <span class="text-rose-500">*</span></label>
            <div class="relative">
              <input type="text" value="05/25/2025" class="w-full border border-slate-200 rounded-xl pl-4 pr-10 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              <svg class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
          </div>
          <div>
            <label class="block text-[12px] font-bold text-slate-700 mb-2">Start Time <span class="text-rose-500">*</span></label>
            <div class="relative">
              <input type="text" value="09:00 AM" class="w-full border border-slate-200 rounded-xl pl-4 pr-10 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              <svg class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
          <div>
            <label class="block text-[12px] font-bold text-slate-700 mb-2">End Date <span class="text-rose-500">*</span></label>
            <div class="relative">
              <input type="text" value="05/25/2025" class="w-full border border-slate-200 rounded-xl pl-4 pr-10 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              <svg class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
          </div>
          <div>
            <label class="block text-[12px] font-bold text-slate-700 mb-2">End Time <span class="text-rose-500">*</span></label>
            <div class="relative">
              <input type="text" value="10:30 AM" class="w-full border border-slate-200 rounded-xl pl-4 pr-10 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              <svg class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
          </div>
        </div>

        <div>
          <label class="block text-[12px] font-bold text-slate-700 mb-2">Time Zone</label>
          <select class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-600 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_1rem_center]">
            <option>(UTC+03:00) Addis Ababa, Nairobi</option>
          </select>
          <p class="text-[10px] text-slate-400 mt-2 font-medium">Exam time will follow this time zone</p>
        </div>
      </div>

    </div>

    <!-- Exam Behavior Card -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm mb-6">
      <div class="flex items-center gap-2 mb-6">
        <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
        <h3 class="text-[13px] font-bold text-slate-800">Exam Behavior</h3>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
        <div class="flex gap-3">
          <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in mt-0.5">
            <input type="checkbox" name="toggle" id="shuffleQuestions" checked class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 border-[#5138ed] appearance-none cursor-pointer transition-transform duration-200 ease-in-out translate-x-5"/>
            <label for="shuffleQuestions" class="toggle-label block overflow-hidden h-5 rounded-full bg-[#5138ed] cursor-pointer"></label>
          </div>
          <div>
            <label for="shuffleQuestions" class="text-[12px] font-bold text-slate-700 cursor-pointer block mb-0.5">Shuffle Questions</label>
            <p class="text-[10px] text-slate-400 font-medium leading-snug">Randomize the order of questions for each student</p>
          </div>
        </div>

        <div class="flex gap-3">
          <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in mt-0.5">
            <input type="checkbox" name="toggle" id="showReview" checked class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 border-[#5138ed] appearance-none cursor-pointer transition-transform duration-200 ease-in-out translate-x-5"/>
            <label for="showReview" class="toggle-label block overflow-hidden h-5 rounded-full bg-[#5138ed] cursor-pointer"></label>
          </div>
          <div>
            <label for="showReview" class="text-[12px] font-bold text-slate-700 cursor-pointer block mb-0.5">Show Review Screen</label>
            <p class="text-[10px] text-slate-400 font-medium leading-snug">Allow students to review answers before submission</p>
          </div>
        </div>

        <div class="flex gap-3">
          <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in mt-0.5">
            <input type="checkbox" name="toggle" id="shuffleAnswers" checked class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 border-[#5138ed] appearance-none cursor-pointer transition-transform duration-200 ease-in-out translate-x-5"/>
            <label for="shuffleAnswers" class="toggle-label block overflow-hidden h-5 rounded-full bg-[#5138ed] cursor-pointer"></label>
          </div>
          <div>
            <label for="shuffleAnswers" class="text-[12px] font-bold text-slate-700 cursor-pointer block mb-0.5">Shuffle Answer Options</label>
            <p class="text-[10px] text-slate-400 font-medium leading-snug">Randomize the order of answer options</p>
          </div>
        </div>

        <div class="flex gap-3">
          <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in mt-0.5">
            <input type="checkbox" name="toggle" id="allowBacktracking" checked class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 border-[#5138ed] appearance-none cursor-pointer transition-transform duration-200 ease-in-out translate-x-5"/>
            <label for="allowBacktracking" class="toggle-label block overflow-hidden h-5 rounded-full bg-[#5138ed] cursor-pointer"></label>
          </div>
          <div>
            <label for="allowBacktracking" class="text-[12px] font-bold text-slate-700 cursor-pointer block mb-0.5">Allow Backtracking</label>
            <p class="text-[10px] text-slate-400 font-medium leading-snug">Students can go back to previous questions</p>
          </div>
        </div>

        <div class="flex gap-3">
          <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in mt-0.5">
            <input type="checkbox" name="toggle" id="oneQuestion" class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 border-slate-300 appearance-none cursor-pointer transition-transform duration-200 ease-in-out"/>
            <label for="oneQuestion" class="toggle-label block overflow-hidden h-5 rounded-full bg-slate-300 cursor-pointer"></label>
          </div>
          <div>
            <label for="oneQuestion" class="text-[12px] font-bold text-slate-700 cursor-pointer block mb-0.5">Show One Question at a Time</label>
            <p class="text-[10px] text-slate-400 font-medium leading-snug">Students can see one question at a time</p>
          </div>
        </div>

        <div class="flex gap-3">
          <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in mt-0.5">
            <input type="checkbox" name="toggle" id="autoSubmit" checked class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 border-[#5138ed] appearance-none cursor-pointer transition-transform duration-200 ease-in-out translate-x-5"/>
            <label for="autoSubmit" class="toggle-label block overflow-hidden h-5 rounded-full bg-[#5138ed] cursor-pointer"></label>
          </div>
          <div>
            <label for="autoSubmit" class="text-[12px] font-bold text-slate-700 cursor-pointer block mb-0.5">Auto Submit on Time Finish</label>
            <p class="text-[10px] text-slate-400 font-medium leading-snug">Automatically submit when time is up</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Security Settings Card -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm mb-6">
      <div class="flex items-center gap-2 mb-6">
        <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
        <h3 class="text-[13px] font-bold text-slate-800">Security Settings</h3>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
        <div class="flex gap-3">
          <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in mt-0.5">
            <input type="checkbox" name="toggle" id="fullscreen" checked class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 border-[#5138ed] appearance-none cursor-pointer transition-transform duration-200 ease-in-out translate-x-5"/>
            <label for="fullscreen" class="toggle-label block overflow-hidden h-5 rounded-full bg-[#5138ed] cursor-pointer"></label>
          </div>
          <div>
            <label for="fullscreen" class="text-[12px] font-bold text-slate-700 cursor-pointer block mb-0.5">Enable Fullscreen Mode</label>
            <p class="text-[10px] text-slate-400 font-medium leading-snug">Prevent students from leaving the exam screen</p>
          </div>
        </div>

        <div class="flex gap-3">
          <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in mt-0.5">
            <input type="checkbox" name="toggle" id="tabMonitoring" checked class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 border-[#5138ed] appearance-none cursor-pointer transition-transform duration-200 ease-in-out translate-x-5"/>
            <label for="tabMonitoring" class="toggle-label block overflow-hidden h-5 rounded-full bg-[#5138ed] cursor-pointer"></label>
          </div>
          <div>
            <label for="tabMonitoring" class="text-[12px] font-bold text-slate-700 cursor-pointer block mb-0.5">Enable Browser Tab Monitoring</label>
            <p class="text-[10px] text-slate-400 font-medium leading-snug">Detect if student switches tab or window</p>
          </div>
        </div>

        <div class="flex gap-3">
          <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in mt-0.5">
            <input type="checkbox" name="toggle" id="disableRightClick" checked class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 border-[#5138ed] appearance-none cursor-pointer transition-transform duration-200 ease-in-out translate-x-5"/>
            <label for="disableRightClick" class="toggle-label block overflow-hidden h-5 rounded-full bg-[#5138ed] cursor-pointer"></label>
          </div>
          <div>
            <label for="disableRightClick" class="text-[12px] font-bold text-slate-700 cursor-pointer block mb-0.5">Disable Right Click</label>
            <p class="text-[10px] text-slate-400 font-medium leading-snug">Prevent right click on the exam screen</p>
          </div>
        </div>

        <div class="flex gap-3">
          <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in mt-0.5">
            <input type="checkbox" name="toggle" id="allowCalculator" class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 border-slate-300 appearance-none cursor-pointer transition-transform duration-200 ease-in-out"/>
            <label for="allowCalculator" class="toggle-label block overflow-hidden h-5 rounded-full bg-slate-300 cursor-pointer"></label>
          </div>
          <div>
            <label for="allowCalculator" class="text-[12px] font-bold text-slate-700 cursor-pointer block mb-0.5">Allow Calculator</label>
            <p class="text-[10px] text-slate-400 font-medium leading-snug">Provide on-screen calculator for students</p>
          </div>
        </div>

        <div class="flex gap-3">
          <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in mt-0.5">
            <input type="checkbox" name="toggle" id="disableCopyPaste" checked class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 border-[#5138ed] appearance-none cursor-pointer transition-transform duration-200 ease-in-out translate-x-5"/>
            <label for="disableCopyPaste" class="toggle-label block overflow-hidden h-5 rounded-full bg-[#5138ed] cursor-pointer"></label>
          </div>
          <div>
            <label for="disableCopyPaste" class="text-[12px] font-bold text-slate-700 cursor-pointer block mb-0.5">Disable Copy & Paste</label>
            <p class="text-[10px] text-slate-400 font-medium leading-snug">Prevent copy and paste operations</p>
          </div>
        </div>

        <div class="flex gap-3">
          <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in mt-0.5">
            <input type="checkbox" name="toggle" id="webcamMonitoring" class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 border-slate-300 appearance-none cursor-pointer transition-transform duration-200 ease-in-out"/>
            <label for="webcamMonitoring" class="toggle-label block overflow-hidden h-5 rounded-full bg-slate-300 cursor-pointer"></label>
          </div>
          <div>
            <label for="webcamMonitoring" class="text-[12px] font-bold text-slate-700 cursor-pointer block mb-0.5">Webcam Monitoring</label>
            <p class="text-[10px] text-slate-400 font-medium leading-snug">Record or monitor exam using webcam</p>
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
      <button @click="emit('next')" class="px-8 py-2.5 bg-[#5138ed] hover:bg-indigo-600 text-white font-bold text-[13px] rounded-xl shadow-sm transition-colors flex items-center gap-2">
        Next: Review & Publish
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
      </button>
    </div>
  </div>
</template>

<style scoped>
/* Base styles for the custom toggle to handle checked/unchecked borders accurately */
.toggle-checkbox:checked {
  right: 0;
  border-color: #5138ed;
}
.toggle-checkbox:checked + .toggle-label {
  background-color: #5138ed;
}
.toggle-checkbox:not(:checked) {
  right: auto;
  left: 0;
  border-color: #cbd5e1; /* slate-300 */
}
.toggle-checkbox:not(:checked) + .toggle-label {
  background-color: #cbd5e1; /* slate-300 */
}
</style>
