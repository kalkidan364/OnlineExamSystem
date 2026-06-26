<script setup lang="ts">
const emit = defineEmits(['cancel', 'next'])
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm mb-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h2 class="text-[15px] font-bold text-slate-800">Add New Question</h2>
        <p class="text-[12px] text-slate-500 mt-1">Create a new question or select from existing question banks.</p>
      </div>
      <div class="flex items-center bg-slate-50 p-1 rounded-xl border border-slate-100">
        <button class="px-4 py-2 rounded-lg bg-white border border-[#5138ed] text-[#5138ed] text-[12px] font-bold shadow-sm">
          Create New
        </button>
        <button class="px-4 py-2 rounded-lg text-slate-500 hover:text-slate-700 text-[12px] font-bold transition-colors">
          Select Existing
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8 mb-8">
      <!-- Question Type -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Question Type <span class="text-rose-500">*</span></label>
        <select class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_1rem_center]">
          <option>Multiple Choice Question (MCQ)</option>
        </select>
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Select the type of question</p>
      </div>

      <!-- Question Category -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Question Category</label>
        <select class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_1rem_center]">
          <option>Database Systems</option>
        </select>
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Organize questions by category</p>
      </div>

      <!-- Question Difficulty -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Question Difficulty</label>
        <div class="flex items-center gap-3">
          <button class="px-5 py-2.5 rounded-xl border border-emerald-100 bg-emerald-50 text-emerald-600 font-bold text-[12px] hover:bg-emerald-100 transition-colors">Easy</button>
          <button class="px-5 py-2.5 rounded-xl border border-amber-500 bg-white text-amber-600 font-bold text-[12px] shadow-[0_0_0_2px_rgba(245,158,11,0.1)] transition-colors">Medium</button>
          <button class="px-5 py-2.5 rounded-xl border border-rose-100 bg-rose-50 text-rose-600 font-bold text-[12px] hover:bg-rose-100 transition-colors">Hard</button>
        </div>
      </div>

      <!-- Marks -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Marks <span class="text-rose-500">*</span></label>
        <input type="number" value="5" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Marks awarded for correct answer</p>
      </div>
    </div>

    <!-- Question Text -->
    <div class="mb-8">
      <label class="block text-[13px] font-bold text-slate-700 mb-2">Question Text <span class="text-rose-500">*</span></label>
      <div class="border border-slate-200 rounded-xl overflow-hidden focus-within:border-[#5138ed] focus-within:ring-1 focus-within:ring-[#5138ed] transition-all">
        
        <!-- Toolbar -->
        <div class="bg-slate-50 border-b border-slate-200 px-3 py-2 flex items-center gap-1 overflow-x-auto">
          <select class="text-[12px] text-slate-700 bg-transparent border-none focus:outline-none py-1 mr-2 appearance-none pr-4 bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:8px_8px] bg-no-repeat bg-[position:right_center]">
            <option>Paragraph</option>
          </select>
          <div class="w-px h-4 bg-slate-300 mx-1"></div>
          
          <button class="w-7 h-7 flex items-center justify-center rounded hover:bg-slate-200 text-slate-700 font-bold text-sm">B</button>
          <button class="w-7 h-7 flex items-center justify-center rounded hover:bg-slate-200 text-slate-700 italic text-sm">I</button>
          <button class="w-7 h-7 flex items-center justify-center rounded hover:bg-slate-200 text-slate-700 underline text-sm">U</button>
          
          <div class="w-px h-4 bg-slate-300 mx-1"></div>
          
          <button class="w-7 h-7 flex items-center justify-center rounded hover:bg-slate-200 text-slate-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
          </button>
          <button class="w-7 h-7 flex items-center justify-center rounded hover:bg-slate-200 text-slate-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path></svg>
          </button>
          
          <div class="w-px h-4 bg-slate-300 mx-1"></div>
          
          <button class="w-7 h-7 flex items-center justify-center rounded hover:bg-slate-200 text-slate-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
          </button>
          <button class="w-7 h-7 flex items-center justify-center rounded hover:bg-slate-200 text-slate-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          </button>
          <button class="w-7 h-7 flex items-center justify-center rounded hover:bg-slate-200 text-slate-600 font-serif font-bold text-sm">Tx</button>
        </div>
        
        <!-- Editor Area -->
        <div class="p-4 bg-white min-h-[120px]">
          <p class="text-[13px] text-slate-800 font-medium">Which of the following is a primary key in a relational database?</p>
          <p class="text-[13px] text-slate-400 mt-2">Type or paste your question here...</p>
        </div>
      </div>
      <p class="text-[11px] text-slate-400 mt-2 font-medium">Brief, clear and unambiguous question text</p>
    </div>

    <!-- Options and Settings -->
    <div class="flex flex-col lg:flex-row gap-12">
      <!-- Answer Options (Left Side) -->
      <div class="flex-1">
        <label class="block text-[13px] font-bold text-slate-700 mb-4">Answer Options <span class="text-rose-500">*</span></label>
        
        <div class="space-y-3">
          <!-- Option A -->
          <div class="flex items-center gap-3">
            <div class="relative flex items-center justify-center">
              <input type="radio" name="correct" checked class="w-4 h-4 text-[#5138ed] border-slate-300 focus:ring-[#5138ed]">
              <span class="absolute inset-0 rounded-full border border-[#5138ed] shadow-[0_0_0_2px_white,0_0_0_3px_#5138ed]"></span>
            </div>
            <span class="w-6 text-[13px] font-bold text-slate-600 text-center">A</span>
            <input type="text" value="Student ID" class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
            <button class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
          </div>

          <!-- Option B -->
          <div class="flex items-center gap-3">
            <div class="relative flex items-center justify-center">
              <input type="radio" name="correct" class="w-4 h-4 text-[#5138ed] border-slate-300 focus:ring-[#5138ed]">
            </div>
            <span class="w-6 text-[13px] font-bold text-slate-600 text-center">B</span>
            <input type="text" value="Student Name" class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
            <button class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
          </div>

          <!-- Option C -->
          <div class="flex items-center gap-3">
            <div class="relative flex items-center justify-center">
              <input type="radio" name="correct" class="w-4 h-4 text-[#5138ed] border-slate-300 focus:ring-[#5138ed]">
            </div>
            <span class="w-6 text-[13px] font-bold text-slate-600 text-center">C</span>
            <input type="text" value="Department" class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
            <button class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
          </div>

          <!-- Option D -->
          <div class="flex items-center gap-3">
            <div class="relative flex items-center justify-center">
              <input type="radio" name="correct" class="w-4 h-4 text-[#5138ed] border-slate-300 focus:ring-[#5138ed]">
            </div>
            <span class="w-6 text-[13px] font-bold text-slate-600 text-center">D</span>
            <input type="text" value="Email Address" class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
            <button class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
          </div>
        </div>

        <button class="mt-4 flex items-center gap-2 text-[#5138ed] font-bold text-[13px] hover:text-indigo-700 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Add Option
        </button>
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Add at least 2 options for MCQ questions</p>
      </div>

      <!-- Settings (Right Side) -->
      <div class="w-full lg:w-[320px] shrink-0">
        <!-- Correct Answer -->
        <div class="mb-6">
          <label class="block text-[13px] font-bold text-slate-700 mb-2">Correct Answer <span class="text-rose-500">*</span></label>
          <select class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_1rem_center]">
            <option>A. Student ID</option>
          </select>
          <p class="text-[11px] text-slate-400 mt-2 font-medium">Select the correct answer</p>
        </div>

        <!-- Explanation -->
        <div>
          <label class="block text-[13px] font-bold text-slate-700 mb-2">Explanation <span class="text-slate-400 font-normal">(Optional)</span></label>
          <textarea class="w-full h-[120px] border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] resize-none" placeholder="Explain why this answer is correct..."></textarea>
          <p class="text-[11px] text-slate-400 mt-2 font-medium">Help students understand the correct answer</p>
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
        Add Question
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
      </button>
    </div>
  </div>
</template>
