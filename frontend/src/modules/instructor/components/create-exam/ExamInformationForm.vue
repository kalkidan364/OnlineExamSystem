<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { useCreateExamStore } from '../../store/createExamStore'

const formStore = useCreateExamStore()

const localExamType = ref('Mid Exam')

onMounted(() => {
  if (['Mid Exam', 'Final Exam', 'Quiz', ''].includes(formStore.examType)) {
    localExamType.value = formStore.examType || 'Mid Exam'
  } else {
    localExamType.value = 'Other'
  }
})

watch(localExamType, (newVal) => {
  if (newVal !== 'Other') {
    formStore.examType = newVal
  } else if (formStore.examType === 'Mid Exam' || formStore.examType === 'Final Exam' || formStore.examType === 'Quiz') {
    formStore.examType = ''
  }
})
</script>

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
        <input v-model="formStore.title" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" placeholder="Enter exam title">
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Example: Database Systems Mid Exam</p>
      </div>

      <!-- Exam Code -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Course Code <span class="text-slate-400 font-normal">(Optional)</span></label>
        <input v-model="formStore.courseCode" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" placeholder="Enter course code">
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Example: CS-304</p>
      </div>

      <!-- Exam Type -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Exam Type <span class="text-rose-500">*</span></label>
        <div class="flex flex-col gap-3">
          <select v-model="localExamType" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_1rem_center]">
            <option value="" disabled hidden>Select exam type</option>
            <option value="Mid Exam">Mid Exam</option>
            <option value="Final Exam">Final Exam</option>
            <option value="Quiz">Quiz</option>
            <option value="Other">Other</option>
          </select>
          <input 
            v-if="localExamType === 'Other'" 
            v-model="formStore.examType" 
            type="text" 
            class="w-full border border-indigo-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] shadow-sm shadow-indigo-50" 
            placeholder="Type your custom exam type"
          >
        </div>
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Mid Exam, Final Exam, Quiz, Assignment, etc.</p>
      </div>

      <!-- Total Marks -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Total Marks <span class="text-rose-500">*</span></label>
        <input v-model="formStore.totalMarks" type="number" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" placeholder="Enter total marks">
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Total marks for the exam</p>
      </div>

      <!-- Description -->
      <div class="md:row-span-2 h-full">
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Description</label>
        <textarea v-model="formStore.description" class="w-full h-[120px] border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] resize-none" placeholder="Enter exam description (optional)"></textarea>
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Briefly describe the purpose of this exam</p>
      </div>

      <!-- Passing Marks -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Passing Marks <span class="text-rose-500">*</span></label>
        <input v-model="formStore.passingMarks" type="number" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]" placeholder="Enter passing marks">
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Minimum marks required to pass</p>
      </div>
    </div>
  </div>

  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm mb-6">
    <div class="mb-6">
      <h2 class="text-[15px] font-bold text-slate-800">Exam Duration & Schedule</h2>
      <p class="text-[12px] text-slate-500 mt-1">Set the duration and schedule for the exam.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Duration -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Duration (minutes) <span class="text-rose-500">*</span></label>
        <input v-model="formStore.durationMinutes" type="number" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] font-semibold">
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Set the total time duration in minutes</p>
      </div>

      <!-- Exam Date -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Exam Date <span class="text-rose-500">*</span></label>
        <div class="relative">
          <input v-model="formStore.scheduledDate" type="date" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] font-medium">
        </div>
      </div>

      <!-- Start Time -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Start Time <span class="text-rose-500">*</span></label>
        <div class="relative">
          <input v-model="formStore.scheduledTime" type="time" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] font-medium">
        </div>
      </div>

    </div>
  </div>
</template>
