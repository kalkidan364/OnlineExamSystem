<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm mb-6">
    <div class="mb-6">
      <h2 class="text-[15px] font-bold text-slate-800">Exam Information</h2>
      <p class="text-[12px] text-slate-500 mt-1">Enter the basic details for your exam.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
      <!-- Exam Title -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Exam Title <span class="text-rose-500">*</span></label>
        <input type="text" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" placeholder="Enter exam title">
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Example: Database Systems Mid Exam</p>
      </div>

      <!-- Exam Code -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Exam Code <span class="text-slate-400 font-normal">(Optional)</span></label>
        <input type="text" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" placeholder="Enter exam code">
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Example: DBMS-MID-2025</p>
      </div>

      <!-- Course -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Course <span class="text-rose-500">*</span></label>
        <select class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_1rem_center]">
          <option value="" disabled selected hidden>Select course</option>
          <option>Database Systems</option>
          <option>Web Programming</option>
        </select>
      </div>

      <!-- Semester -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Semester <span class="text-rose-500">*</span></label>
        <select class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_1rem_center]">
          <option value="" disabled selected hidden>Select semester</option>
          <option>Semester 1</option>
          <option>Semester 2</option>
        </select>
      </div>

      <!-- Exam Type -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Exam Type <span class="text-rose-500">*</span></label>
        <select class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_1rem_center]">
          <option value="" disabled selected hidden>Select exam type</option>
          <option>Mid Exam</option>
          <option>Final Exam</option>
          <option>Quiz</option>
        </select>
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Mid Exam, Final Exam, Quiz, Assignment, etc.</p>
      </div>

      <!-- Total Marks -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Total Marks <span class="text-rose-500">*</span></label>
        <input type="number" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" placeholder="Enter total marks">
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Total marks for the exam</p>
      </div>

      <!-- Description -->
      <div class="md:row-span-2 h-full">
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Description</label>
        <textarea class="w-full h-[120px] border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] resize-none" placeholder="Enter exam description (optional)"></textarea>
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Briefly describe the purpose of this exam</p>
      </div>

      <!-- Passing Marks -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Passing Marks <span class="text-rose-500">*</span></label>
        <input type="number" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" placeholder="Enter passing marks">
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Minimum marks required to pass</p>
      </div>
    </div>
  </div>

  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm mb-6">
    <div class="mb-6">
      <h2 class="text-[15px] font-bold text-slate-800">Exam Duration & Schedule</h2>
      <p class="text-[12px] text-slate-500 mt-1">Set the duration and schedule for the exam.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <!-- Duration -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Duration <span class="text-rose-500">*</span></label>
        <div class="flex items-center gap-2">
          <input type="text" value="01" class="w-14 border border-slate-200 rounded-xl px-3 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] text-center font-semibold">
          <span class="text-slate-400 font-bold">:</span>
          <input type="text" value="30" class="w-14 border border-slate-200 rounded-xl px-3 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] text-center font-semibold">
          <select class="flex-1 border border-transparent rounded-xl py-3 text-[13px] text-slate-500 font-medium focus:outline-none appearance-none bg-transparent">
            <option>Hours</option>
            <option>Minutes</option>
          </select>
        </div>
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Set the total time duration for the exam</p>
      </div>

      <!-- Exam Date -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Exam Date <span class="text-rose-500">*</span></label>
        <div class="relative">
          <input type="text" placeholder="mm/dd/yyyy" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] font-medium">
          <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          </div>
        </div>
      </div>

      <!-- Start Time -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Start Time <span class="text-rose-500">*</span></label>
        <div class="relative">
          <input type="text" value="09:00 AM" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] font-medium">
          <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
        </div>
      </div>

      <!-- End Time -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">End Time</label>
        <div class="relative">
          <input type="text" value="10:30 AM" readonly class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 font-medium bg-slate-50 cursor-not-allowed">
          <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
        </div>
        <p class="text-[11px] text-slate-400 mt-2 font-medium">End time will be calculated automatically</p>
      </div>

    </div>
  </div>
</template>
