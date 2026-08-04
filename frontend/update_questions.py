import sys

file_path = r"c:\Users\hp\Documents\OnlineExam\OnlineExamSystem\frontend\src\modules\instructor\components\create-exam\ReviewPublishForm.vue"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Add script variables
script_addition = """
const getLetter = (index: number | string) => String.fromCharCode(65 + Number(index))

const getDifficultyClass = (diff: string) => {
  switch (diff?.toLowerCase()) {
    case 'easy': return 'bg-emerald-50 text-emerald-700 border-emerald-200'
    case 'hard': return 'bg-rose-50 text-rose-700 border-rose-200'
    default: return 'bg-amber-50 text-amber-700 border-amber-200'
  }
}

const getLetterLabel = (index: number) => {
  let label = ''
  let num = index
  while (num >= 0) {
    label = String.fromCharCode(65 + (num % 26)) + label
    num = Math.floor(num / 26) - 1
  }
  return label
}

const groupedQuestions = computed(() => {
  const groups: any[] = []
  formStore.questions.forEach((q: any) => {
    let displayType = q.type.toUpperCase()
    if (q.type === 'multiple_choice' || q.type === 'mcq') displayType = 'MULTIPLE CHOICE'
    if (q.type === 'true_false' || q.type === 'true/false') displayType = 'TRUE / FALSE'
    if (q.type === 'short_answer') displayType = 'SHORT ANSWER'
    if (q.type === 'fill_blank' || q.type === 'fill_in_the_blank') displayType = 'FILL IN THE BLANK'
    if (q.type === 'matching' || q.type === 'Matching') displayType = 'MATCHING'

    let typeGroup = groups.find((g: any) => g.questionType === displayType)
    if (!typeGroup) {
       typeGroup = { questionType: displayType, instructionGroups: [] }
       groups.push(typeGroup)
    }

    const instructionStr = q.instruction || ''
    let instGroup = typeGroup.instructionGroups.find((ig: any) => ig.instruction === instructionStr)
    if (!instGroup) {
       instGroup = { instruction: instructionStr, questions: [] }
       typeGroup.instructionGroups.push(instGroup)
    }
    instGroup.questions.push(q)
  })
  return groups
})

const getQuestionCount = (typeGroup: any) => {
  let count = 0
  if (typeGroup.instructionGroups) {
    typeGroup.instructionGroups.forEach((instGroup: any) => {
      if (instGroup.questions) count += instGroup.questions.length
    })
  }
  return count
}
"""

content = content.replace("import { ref } from 'vue'", "import { ref, computed } from 'vue'")
content = content.replace("const getLetter = (index: number | string) => String.fromCharCode(65 + Number(index))", script_addition)

old_questions_section = """    <!-- 2. Questions -->
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
    </div>"""

new_questions_section = """    <!-- 2. Questions -->
    <div class="mb-8 flex flex-col gap-8 bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
      <div class="flex items-center justify-between pb-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
            </div>
            <h3 class="text-[18px] font-bold text-slate-800">2. Added Questions ({{ formStore.questions.length }})</h3>
        </div>
        <button @click="emit('edit-step', 2)" class="px-5 py-2 bg-indigo-50 text-[#5138ed] text-[12px] font-bold rounded-xl hover:bg-indigo-100 transition-colors flex items-center gap-2">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
          Edit Questions
        </button>
      </div>

      <div v-if="formStore.questions.length === 0" class="p-16 text-center bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
        <div class="w-16 h-16 bg-white text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-100">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <h3 class="text-base font-bold text-slate-800 mb-1">No Questions Added</h3>
        <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">Go back to step 2 to add questions to your exam.</p>
        <button @click="emit('edit-step', 2)" class="px-5 py-2.5 bg-[#5138ed] text-white text-xs font-bold rounded-xl hover:bg-indigo-600 transition-colors">
          Add Questions
        </button>
      </div>

      <div v-else class="space-y-12">
        <!-- Iterate over Question Types -->
        <div v-for="(typeGroup, typeIdx) in groupedQuestions" :key="typeGroup.questionType">
          
          <!-- TYPE HEADER -->
          <div class="mb-6 border-b-2 border-slate-100 pb-3 flex items-center justify-between">
            <h2 class="text-[15px] font-black text-slate-800 uppercase tracking-widest flex items-center gap-3">
              <span class="w-1.5 h-5 bg-[#5138ed] rounded-full inline-block shadow-[0_0_10px_rgba(81,56,237,0.4)]"></span>
              {{ typeGroup.questionType }}
            </h2>
          </div>

          <!-- Iterate over Instructions within Type -->
          <div class="space-y-10 pl-0 sm:pl-6 border-l-[3px] border-slate-50 ml-1">
            <div v-for="(instGroup, instIdx) in typeGroup.instructionGroups" :key="instIdx">
              
              <!-- INSTRUCTION HEADER -->
              <div class="mb-5 relative">
                <div class="absolute -left-[30px] top-1/2 -translate-y-1/2 w-3 h-3 bg-white border-[3px] border-indigo-400 rounded-full"></div>
                <div v-if="instGroup.instruction">
                  <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block mb-1">Instructor Instruction:</span>
                  <h3 class="text-[14px] font-bold text-slate-700 italic px-2">"{{ instGroup.instruction }}"</h3>
                </div>
                <div v-else>
                  <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block mb-1">Instructor Instruction:</span>
                  <h3 class="text-[13px] font-bold text-slate-400 italic px-2">No specific instruction provided.</h3>
                </div>
              </div>

              <!-- Questions for this Instruction -->
              <div class="space-y-5 pl-4">
                <div 
                  v-for="(q, qIdx) in instGroup.questions" 
                  :key="qIdx" 
                  class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm shadow-slate-100 relative group"
                >
                  <!-- Question Metadata Bar -->
                  <div class="flex flex-wrap items-center justify-between gap-3 mb-6 border-b border-slate-50 pb-4">
                    <div class="flex items-center gap-3">
                      <!-- Question Number -->
                      <span class="w-7 h-7 rounded-lg bg-indigo-50 text-[#5138ed] flex items-center justify-center text-[12px] font-black border border-indigo-100/50">
                        {{ formStore.questions.findIndex((x: any) => x === q) + 1 }}
                      </span>
                      <span :class="getDifficultyClass(q.difficulty)" class="px-2.5 py-1 text-[10px] uppercase font-black rounded-md border tracking-widest">
                        {{ q.difficulty || 'Medium' }}
                      </span>
                      <span class="px-2.5 py-1 bg-slate-50 border border-slate-100 text-slate-600 text-[10px] font-black uppercase rounded-md tracking-widest">
                        {{ q.marks || 1 }} Mark{{ (q.marks || 1) > 1 ? 's' : '' }}
                      </span>
                      <span v-if="q.chapter" class="px-2.5 py-1 bg-slate-50 text-slate-500 text-[10px] font-bold uppercase rounded-md tracking-widest border border-slate-100">
                        {{ q.chapter }}
                      </span>
                    </div>
                  </div>

                  <!-- QUESTION CONTENT RENDERERS -->
                  <div class="text-sm text-slate-800">
                    
                    <!-- 1. MULTIPLE CHOICE / TRUE-FALSE -->
                    <template v-if="q.type === 'multiple_choice' || q.type === 'mcq' || q.type === 'true_false' || q.type === 'true/false' || q.type === 'Multiple Choice (MCQ)' || q.type === 'True / False'">
                      <div class="font-medium mb-5 text-[15px] leading-relaxed" v-html="q.text"></div>
                      <div v-if="q.image_url" class="mb-5">
                        <img :src="q.image_url" alt="Question Image" class="max-h-56 rounded-xl border border-slate-200 object-cover shadow-sm" />
                      </div>
                      
                      <!-- Options -->
                      <div v-if="q.options && q.options.length" class="space-y-3 mb-6 ml-2">
                        <div 
                          v-for="(opt, oIdx) in q.options" 
                          :key="oIdx"
                          class="flex items-start gap-4 p-2 rounded-lg"
                        >
                          <span class="w-7 h-7 shrink-0 rounded-lg bg-slate-50 text-slate-500 text-[11px] font-black flex items-center justify-center border border-slate-200 shadow-sm mt-0.5 uppercase">
                            {{ opt.label || getLetterLabel(Number(oIdx)) }}
                          </span>
                          <span class="text-[14px] leading-relaxed pt-0.5 text-slate-600">{{ opt.text || opt }}</span>
                        </div>
                      </div>
                      
                      <!-- Correct Answer Box -->
                      <div class="bg-indigo-50/40 border border-indigo-100/60 p-4 rounded-xl flex items-center gap-3 text-[12px]">
                        <span class="font-black text-slate-700 text-[12px] uppercase tracking-wider">Answer:</span>
                        <span class="font-bold text-[#5138ed] text-[14px]">{{ q.correct_answer || q.correctAnswer }}</span>
                      </div>
                    </template>

                    <!-- 2. SHORT ANSWER / FILL IN THE BLANK -->
                    <template v-else-if="q.type === 'short_answer' || q.type === 'fill_in_the_blank' || q.type === 'fill_blank' || q.type === 'Short Answer' || q.type === 'Fill in the Blank'">
                      <div class="font-medium mb-5 text-[15px] leading-relaxed" v-html="q.text"></div>
                      <div v-if="q.image_url" class="mb-5">
                        <img :src="q.image_url" alt="Question Image" class="max-h-56 rounded-xl border border-slate-200 object-cover shadow-sm" />
                      </div>

                      <div class="bg-indigo-50/40 border border-indigo-100/60 p-4 rounded-xl flex items-center gap-3 text-[12px]">
                        <span class="font-black text-slate-700 text-[12px] uppercase tracking-wider">Answer:</span>
                        <span class="font-bold text-[#5138ed] text-[14px]">{{ q.correct_answer || q.sample_answer || q.sampleAnswer || (q.question_data && q.question_data.answers ? q.question_data.answers.map((a: any)=>a.text).join(' OR ') : '') }}</span>
                      </div>
                    </template>

                    <!-- 3. ESSAY -->
                    <template v-else-if="q.type === 'essay' || q.type === 'Essay'">
                      <div class="font-medium mb-5 text-[15px] leading-relaxed" v-html="q.text"></div>
                      <div v-if="q.image_url" class="mb-5">
                        <img :src="q.image_url" alt="Question Image" class="max-h-56 rounded-xl border border-slate-200 object-cover shadow-sm" />
                      </div>

                      <div class="bg-indigo-50/30 border border-indigo-100/60 p-5 rounded-xl text-[12px]">
                        <span class="font-black text-[#5138ed] uppercase tracking-wider text-[11px] block mb-3">Sample Answer:</span>
                        <div class="text-slate-600 leading-relaxed text-[13px]" v-html="q.sample_answer || q.sampleAnswer || 'No sample answer provided.'"></div>
                      </div>
                    </template>

                    <!-- 4. MATCHING -->
                    <template v-else-if="q.type === 'matching' || q.type === 'Matching'">
                      <div v-if="q.text" class="font-medium mb-5 text-[15px] leading-relaxed" v-html="q.text"></div>

                      <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-8">
                        <!-- Column A -->
                        <div>
                          <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-4 pb-2 border-b border-slate-100">Column A</h4>
                          <div class="space-y-4">
                            <div v-for="(pair, cIdx) in (q.pairs || [])" :key="'l'+cIdx" class="flex gap-4">
                              <span class="font-bold text-slate-400 text-[14px] w-5 shrink-0 text-right">{{ Number(cIdx) + 1 }}.</span>
                              <div class="text-[13px] bg-slate-50 border border-slate-100 rounded-xl p-3 w-full flex-1 leading-relaxed text-slate-600">{{ pair.left }}</div>
                            </div>
                          </div>
                        </div>

                        <!-- Column B -->
                        <div>
                          <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-4 pb-2 border-b border-slate-100">Column B</h4>
                          <div class="space-y-4">
                            <div v-for="(pair, cIdx) in (q.pairs || [])" :key="'r'+cIdx" class="flex gap-4 items-center">
                              <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-500 text-[12px] font-black flex items-center justify-center border border-slate-200 shrink-0 uppercase shadow-sm">
                                {{ getLetterLabel(Number(cIdx)) }}
                              </span>
                              <div class="text-[13px] flex-1 text-slate-600 leading-relaxed bg-white">{{ pair.right }}</div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Correct Matches Box -->
                      <div class="bg-indigo-50/40 border border-indigo-100/60 p-4 rounded-xl flex items-center gap-3 text-[12px] flex-wrap">
                        <span class="font-black text-slate-700 text-[12px] uppercase tracking-wider mr-2">Correct Matches:</span>
                        <div v-for="(pair, cIdx) in (q.pairs || [])" :key="'ans'+cIdx" class="font-bold text-[#5138ed] text-[14px] mr-4 flex items-center gap-1.5">
                           <span>{{ Number(cIdx) + 1 }}</span>
                           <svg class="w-3 h-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                           <span>{{ getLetterLabel(Number(cIdx)) }}</span>
                        </div>
                      </div>
                    </template>

                  </div>

                </div>
              </div>

            </div>
          </div>
          
        </div>
      </div>
    </div>"""

if old_questions_section in content:
    content = content.replace(old_questions_section, new_questions_section)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("Success")
else:
    print("Could not find the old block.")
