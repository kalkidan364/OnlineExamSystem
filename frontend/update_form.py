import sys

file_path = r"c:\Users\hp\Documents\OnlineExam\OnlineExamSystem\frontend\src\modules\instructor\components\create-exam\ReviewPublishForm.vue"

with open(file_path, "r", encoding="utf-8") as f:
    lines = f.readlines()

new_content = """    <!-- Meta Info Row: 1, 3, 4 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      
      <!-- 1. Exam Information -->
      <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex flex-col hover:border-indigo-100 transition-colors">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-md bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-[13px] font-bold text-slate-800">1. Exam Info</h3>
          </div>
          <button @click="emit('edit-step', 1)" class="text-[11px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">Edit</button>
        </div>
        <div class="space-y-2.5 flex-1">
          <div class="flex justify-between items-center pb-2 border-b border-slate-50">
            <span class="text-[11px] font-bold text-slate-500">Title</span>
            <span class="text-[11px] font-medium text-slate-800 truncate max-w-[120px]" :title="formStore.title">{{ formStore.title || 'Untitled' }}</span>
          </div>
          <div class="flex justify-between items-center pb-2 border-b border-slate-50">
            <span class="text-[11px] font-bold text-slate-500">Course</span>
            <span class="text-[11px] font-medium text-slate-800 truncate max-w-[120px]">{{ formStore.courseCode || 'SWE-301' }}</span>
          </div>
          <div class="flex justify-between items-center pb-2 border-b border-slate-50">
            <span class="text-[11px] font-bold text-slate-500">Type</span>
            <span class="text-[10px] font-bold text-[#5138ed] bg-indigo-50 px-2 py-0.5 rounded">{{ formStore.examType }}</span>
          </div>
          <div class="flex justify-between items-center pb-2 border-b border-slate-50">
            <span class="text-[11px] font-bold text-slate-500">Marks</span>
            <span class="text-[11px] font-medium text-slate-800">{{ formStore.totalMarks }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-[11px] font-bold text-slate-500">Pass</span>
            <span class="text-[11px] font-medium text-slate-800">{{ formStore.passingMarks }} ({{ Math.round((formStore.passingMarks / formStore.totalMarks) * 100) || 0 }}%)</span>
          </div>
        </div>
      </div>

      <!-- 3. Exam Settings -->
      <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex flex-col hover:border-indigo-100 transition-colors">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-md bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <h3 class="text-[13px] font-bold text-slate-800">3. Exam Settings</h3>
          </div>
          <button @click="emit('edit-step', 3)" class="text-[11px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">Edit</button>
        </div>
        <div class="space-y-2.5 flex-1">
          <div class="flex justify-between items-center pb-2 border-b border-slate-50">
            <span class="text-[11px] font-bold text-slate-500">Duration</span>
            <span class="text-[11px] font-medium text-slate-800">{{ formStore.durationMinutes }} mins</span>
          </div>
          <div class="flex justify-between items-center pb-2 border-b border-slate-50">
            <span class="text-[11px] font-bold text-slate-500">Scheduled</span>
            <span class="text-[11px] font-medium text-slate-800 truncate max-w-[120px]">{{ formStore.scheduledDate }} {{ formStore.scheduledTime }}</span>
          </div>
          <div class="flex justify-between items-center pb-2 border-b border-slate-50">
            <span class="text-[11px] font-bold text-slate-500">Shuffle Q's</span>
            <span :class="['text-[11px] font-medium', formStore.shuffleQuestions ? 'text-emerald-600' : 'text-slate-400']">{{ formStore.shuffleQuestions ? 'Yes' : 'No' }}</span>
          </div>
          <div class="flex justify-between items-center pb-2 border-b border-slate-50">
            <span class="text-[11px] font-bold text-slate-500">Shuffle A's</span>
            <span :class="['text-[11px] font-medium', formStore.shuffleAnswers ? 'text-emerald-600' : 'text-slate-400']">{{ formStore.shuffleAnswers ? 'Yes' : 'No' }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-[11px] font-bold text-slate-500">Auto Submit</span>
            <span :class="['text-[11px] font-medium', formStore.autoSubmitOnTimeFinish ? 'text-emerald-600' : 'text-slate-400']">{{ formStore.autoSubmitOnTimeFinish ? 'Yes' : 'No' }}</span>
          </div>
        </div>
      </div>

      <!-- 4. Security Settings -->
      <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm flex flex-col hover:border-indigo-100 transition-colors">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-md bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <h3 class="text-[13px] font-bold text-slate-800">4. Security</h3>
          </div>
          <button @click="emit('edit-step', 3)" class="text-[11px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">Edit</button>
        </div>
        <div class="space-y-2.5 flex-1">
          <div class="flex justify-between items-center pb-2 border-b border-slate-50">
            <span class="text-[11px] font-bold text-slate-500">Fullscreen</span>
            <span :class="['text-[11px] font-medium', formStore.enableFullscreenMode ? 'text-emerald-600' : 'text-slate-400']">{{ formStore.enableFullscreenMode ? 'Yes' : 'No' }}</span>
          </div>
          <div class="flex justify-between items-center pb-2 border-b border-slate-50">
            <span class="text-[11px] font-bold text-slate-500">Right Click</span>
            <span :class="['text-[11px] font-medium', !formStore.disableRightClick ? 'text-emerald-600' : 'text-slate-400']">{{ formStore.disableRightClick ? 'Disabled' : 'Enabled' }}</span>
          </div>
          <div class="flex justify-between items-center pb-2 border-b border-slate-50">
            <span class="text-[11px] font-bold text-slate-500">Copy/Paste</span>
            <span :class="['text-[11px] font-medium', !formStore.disableCopyPaste ? 'text-emerald-600' : 'text-slate-400']">{{ formStore.disableCopyPaste ? 'Disabled' : 'Enabled' }}</span>
          </div>
          <div class="flex justify-between items-center pb-2 border-b border-slate-50">
            <span class="text-[11px] font-bold text-slate-500">Tab Monitor</span>
            <span :class="['text-[11px] font-medium', formStore.enableBrowserTabMonitoring ? 'text-emerald-600' : 'text-slate-400']">{{ formStore.enableBrowserTabMonitoring ? 'Yes' : 'No' }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-[11px] font-bold text-slate-500">Calculator</span>
            <span :class="['text-[11px] font-medium', formStore.allowCalculator ? 'text-emerald-600' : 'text-slate-400']">{{ formStore.allowCalculator ? 'Yes' : 'No' }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. Questions -->
    <div class="space-y-6 mb-6">
      <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
            </div>
            <h3 class="text-[14px] font-bold text-slate-800">2. Questions</h3>
          </div>
          <div class="flex items-center gap-3">
            <button @click="openQuestionsModal(false)" title="View all questions" class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
            </button>
            <button @click="openQuestionsModal(true)" class="flex items-center gap-1.5 text-[12px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
              Edit
            </button>
          </div>
        </div>

        <div class="flex flex-wrap gap-4 mb-6">
          <div class="flex-1 min-w-[120px] p-4 border border-slate-100 rounded-xl bg-slate-50 flex flex-col items-center justify-center">
            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-[#5138ed] mb-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <span class="text-[11px] font-bold text-slate-500 mb-0.5">Total Questions</span>
            <span class="text-[16px] font-black text-slate-800">{{ formStore.questions.length }}</span>
          </div>

          <div class="flex-1 min-w-[120px] p-4 border border-slate-100 rounded-xl bg-white flex flex-col justify-center">
            <template v-if="formStore.questions.length === 0">
              <span class="text-slate-400 text-center text-[12px]">No questions added yet.</span>
            </template>
            <template v-else>
              <ul class="text-[12px] text-slate-600 space-y-1">
                <li v-for="(q, idx) in formStore.questions.slice(0, 3)" :key="idx" class="truncate">
                  {{ idx + 1 }}. {{ q.text }}
                </li>
                <li v-if="formStore.questions.length > 3" class="text-indigo-500 font-bold">
                  + {{ formStore.questions.length - 3 }} more
                </li>
              </ul>
            </template>
          </div>
        </div>

        <div class="flex items-center text-[12px] font-bold text-slate-500">
          Total Marks: <span class="text-slate-800 ml-1">{{ formStore.totalMarks }}</span>
        </div>
      </div>
    </div>\n"""

new_lines = lines[:60] + [new_content] + lines[334:]

with open(file_path, "w", encoding="utf-8") as f:
    f.writelines(new_lines)
