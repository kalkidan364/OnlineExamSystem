<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useInstructorQbStore } from '../store/instructorQbStore'

const route = useRoute()
const router = useRouter()
const qbStore = useInstructorQbStore()

// We'd normally fetch the bank name based on route.params.id
const bankId = route.params.id
const questionId = route.params.questionId as string | undefined
const isEditMode = !!questionId
const bankTitle = ref('Database Systems Questions Bank')

// Form State
const questionType = ref('Multiple Choice (MCQ)')
const difficulty = ref('Medium')
const chapter = ref('SQL')
const topic = ref('SELECT Statement')

const editorRef = ref<HTMLDivElement | null>(null)
const questionText = ref('Which SQL command is used to retrieve data from a database?')

const reverseMapType = (backendType: string) => {
  const map: Record<string, string> = {
    multiple_choice: 'Multiple Choice (MCQ)',
    short_answer: 'Short Answer',
    essay: 'Essay',
    true_false: 'True/False',
  }
  return map[backendType] || 'Multiple Choice (MCQ)'
}

onMounted(async () => {
  if (editorRef.value && !isEditMode) {
    editorRef.value.innerHTML = questionText.value
  }
  // If in edit mode, fetch question data and populate the form
  if (isEditMode) {
    try {
      const { default: apiClient } = await import('../../../core/api/apiClient')
      const res = await apiClient.get(`/instructor/question-banks/${bankId}`)
      const data = res.data.data
      bankTitle.value = data.bank.title
      const q = data.questions.find((item: any) => String(item.id) === String(questionId))
      if (q) {
        questionType.value = reverseMapType(q.type)
        difficulty.value = q.difficulty || 'Medium'
        chapter.value = q.chapter || ''
        topic.value = q.topic || ''
        questionText.value = q.text || ''
        marks.value = q.marks || 1
        negativeMarks.value = q.negative_marks || 0
        timeSeconds.value = q.time_seconds || 0
        status.value = q.status === 'Active' || q.status === true
        tags.value = q.tags || ''
        if (q.type === 'multiple_choice' && q.options) {
          options.value = q.options.map((opt: any, i: number) => ({
            id: String.fromCharCode('A'.charCodeAt(0) + i),
            text: typeof opt === 'string' ? opt : opt.text
          }))
          correctAnswer.value = q.correct_answer || 'A'
        } else if (q.type === 'true_false') {
          trueFalseAnswer.value = q.correct_answer || 'True'
        } else if (q.type === 'short_answer') {
          shortAnswerText.value = q.correct_answer || ''
        } else if (q.type === 'essay') {
          essayModelAnswer.value = q.correct_answer || ''
        }
        // Populate the rich text editor
        if (editorRef.value) {
          editorRef.value.innerHTML = questionText.value
        }
      }
    } catch (err) {
      console.error('Failed to load question for editing', err)
    }
  }
})

// Formatting using native browser execCommand for true WYSIWYG
const execFormat = (command: string, value: string = '') => {
  document.execCommand(command, false, value)
  // Ensure the Vue state stays in sync after formatting
  if (editorRef.value) {
    questionText.value = editorRef.value.innerHTML
    editorRef.value.focus()
  }
}

const updateContent = (e: Event) => {
  const target = e.target as HTMLDivElement
  questionText.value = target.innerHTML
}

const insertLink = () => {
  const url = prompt('Enter link URL (e.g., https://example.com):')
  if (url) {
    execFormat('createLink', url)
  }
}

// Answer Options State
const options = ref([
  { id: 'A', text: 'INSERT' },
  { id: 'B', text: 'UPDATE' },
  { id: 'C', text: 'SELECT' },
  { id: 'D', text: 'DELETE' }
])
const correctAnswer = ref('C')
const shortAnswerText = ref('')
const trueFalseAnswer = ref('True')
const essayModelAnswer = ref('')

// Settings State
const marks = ref(2)
const negativeMarks = ref(0)
const timeSeconds = ref(0)
const status = ref(true)
const tags = ref('sql, select, queries, database')

const isSubmitting = ref(false)
const errorMsg = ref('')

const goBack = () => {
  router.push(`/instructor/question-banks/${bankId}`)
}

const addOption = () => {
  const nextId = String.fromCharCode('A'.charCodeAt(0) + options.value.length)
  options.value.push({ id: nextId, text: '' })
}

const removeOption = (index: number) => {
  if (options.value.length > 2) {
    options.value.splice(index, 1)
    
    // Reassign IDs (A, B, C...)
    options.value.forEach((opt, idx) => {
      opt.id = String.fromCharCode('A'.charCodeAt(0) + idx)
    })
    
    // Reset correct answer if it was removed or if it's no longer in the list
    const validIds = options.value.map(o => o.id)
    if (!validIds.includes(correctAnswer.value)) {
      correctAnswer.value = 'A'
    }
  }
}

const resetForm = () => {
  questionText.value = ''
  options.value = [
    { id: 'A', text: 'Option A' },
    { id: 'B', text: 'Option B' },
    { id: 'C', text: 'Option C' },
    { id: 'D', text: 'Option D' }
  ]
  correctAnswer.value = 'A'
  shortAnswerText.value = ''
  trueFalseAnswer.value = 'True'
  essayModelAnswer.value = ''
  errorMsg.value = ''
}

const mapType = (uiType: string) => {
  if (uiType === 'Multiple Choice (MCQ)') return 'multiple_choice'
  if (uiType === 'Short Answer') return 'short_answer'
  if (uiType === 'Essay') return 'essay'
  if (uiType === 'True/False') return 'true_false'
  return 'multiple_choice'
}

const saveQuestion = async (addNew = false) => {
  errorMsg.value = ''
  
  if (!questionText.value.trim()) {
    errorMsg.value = 'Question text is required.'
    return
  }

  isSubmitting.value = true

  let payloadOptions = null
  let payloadCorrectAnswer = null

  if (questionType.value === 'Multiple Choice (MCQ)') {
    payloadOptions = options.value.map(o => o.text)
    payloadCorrectAnswer = correctAnswer.value
  } else if (questionType.value === 'True/False') {
    payloadOptions = ['True', 'False']
    payloadCorrectAnswer = trueFalseAnswer.value
  } else if (questionType.value === 'Short Answer') {
    payloadCorrectAnswer = shortAnswerText.value
  } else if (questionType.value === 'Essay') {
    payloadCorrectAnswer = essayModelAnswer.value
  }

  const payload = {
    type: mapType(questionType.value),
    difficulty: difficulty.value,
    chapter: chapter.value,
    topic: topic.value,
    text: questionText.value,
    options: payloadOptions,
    correct_answer: payloadCorrectAnswer,
    marks: marks.value,
    negative_marks: negativeMarks.value,
    time_seconds: timeSeconds.value,
    status: status.value,
    tags: tags.value
  }

  try {
    if (isEditMode) {
      await qbStore.updateQuestion(questionId as string, payload)
    } else {
      await qbStore.addQuestion(bankId as string, payload)
    }
    if (addNew) {
      resetForm()
    } else {
      goBack()
    }
  } catch (error: any) {
    errorMsg.value = error.response?.data?.message || 'Failed to save question.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="max-w-[1600px] mx-auto space-y-6">
    
    <!-- Breadcrumb -->
    <div class="flex items-center text-[13px] text-slate-500 font-medium">
      <router-link to="/instructor/question-banks" class="hover:text-[#5138ed] transition-colors">Question Banks</router-link>
      <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
      <router-link :to="`/instructor/question-banks/${bankId}`" class="hover:text-[#5138ed] transition-colors">{{ bankTitle }}</router-link>
      <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
      <span class="text-slate-800 font-bold">{{ isEditMode ? 'Edit Question' : 'Create Question' }}</span>
    </div>

    <div class="flex flex-col xl:flex-row gap-6 pb-20">
      
      <!-- Main Content Area -->
      <div class="flex-1 space-y-6 min-w-0">
        
        <!-- Info Alert -->
        <div class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-4 flex items-start gap-4">
          <div class="w-10 h-10 rounded-full bg-indigo-100 text-[#5138ed] flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <div>
            <h3 class="text-[14px] font-bold text-slate-800">Question Information</h3>
            <p class="text-[13px] text-slate-600 mt-1">Add a new question to your question bank</p>
          </div>
        </div>

        <div v-if="errorMsg" class="bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3 text-[13px] font-medium mb-6">
          {{ errorMsg }}
        </div>

        <!-- Form Card -->
        <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm space-y-6">
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-2">Question Type <span class="text-rose-500">*</span></label>
              <select v-model="questionType" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] appearance-none">
                <option>Multiple Choice (MCQ)</option>
                <option>Short Answer</option>
                <option>Essay</option>
                <option>True/False</option>
              </select>
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-2">Difficulty Level <span class="text-rose-500">*</span></label>
              <select v-model="difficulty" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] appearance-none">
                <option>Easy</option>
                <option>Medium</option>
                <option>Hard</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-2">Chapter</label>
              <select v-model="chapter" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] appearance-none">
                <option>SQL</option>
                <option>Normalization</option>
                <option>Transactions</option>
              </select>
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-2">Topic</label>
              <select v-model="topic" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] appearance-none">
                <option>SELECT Statement</option>
                <option>JOINs</option>
                <option>Subqueries</option>
              </select>
            </div>
          </div>

          <!-- Question Text -->
          <div>
            <label class="block text-[12px] font-bold text-slate-700 mb-2">Question Text <span class="text-rose-500">*</span></label>
            <div class="border border-slate-200 rounded-xl overflow-hidden focus-within:border-[#5138ed] focus-within:ring-1 focus-within:ring-[#5138ed] transition-shadow">
              <!-- Toolbar -->
              <div class="bg-slate-50 border-b border-slate-200 px-3 py-2 flex items-center gap-1">
                <button @click.prevent="execFormat('bold')" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-200 rounded font-bold transition-colors" title="Bold">B</button>
                <button @click.prevent="execFormat('italic')" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-200 rounded italic transition-colors" title="Italic">I</button>
                <button @click.prevent="execFormat('underline')" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-200 rounded underline transition-colors" title="Underline">U</button>
                <div class="w-px h-4 bg-slate-300 mx-1"></div>
                <button @click.prevent="execFormat('justifyLeft')" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-200 rounded transition-colors" title="Align Left">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <button @click.prevent="execFormat('justifyCenter')" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-200 rounded transition-colors" title="Align Center">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path></svg>
                </button>
                <div class="w-px h-4 bg-slate-300 mx-1"></div>
                <button @click.prevent="execFormat('superscript')" class="px-2 text-[11px] font-medium text-slate-600 hover:bg-slate-200 rounded transition-colors" title="Superscript">x²</button>
                <button @click.prevent="execFormat('subscript')" class="px-2 text-[11px] font-medium text-slate-600 hover:bg-slate-200 rounded transition-colors" title="Subscript">x₂</button>
                <div class="w-px h-4 bg-slate-300 mx-1"></div>
                <button @click.prevent="insertLink" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-200 rounded transition-colors" title="Insert Link">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                </button>
              </div>
              
              <!-- WYSIWYG Content Editable Area -->
              <div 
                ref="editorRef" 
                contenteditable="true" 
                @input="updateContent"
                class="w-full px-4 py-3 text-[13px] text-slate-700 min-h-[120px] focus:outline-none" 
                style="white-space: pre-wrap;"
                placeholder="Type your question here...">
              </div>
            </div>
            <div class="text-right mt-2 text-[11px] text-slate-400 font-medium">Characters: {{ questionText.length }}</div>
          </div>

          <!-- Answer Options -->
          <div v-if="questionType === 'Multiple Choice (MCQ)'">
            <div class="flex items-center justify-between mb-4">
              <label class="block text-[12px] font-bold text-slate-700">Answer Options <span class="text-rose-500">*</span></label>
              <label class="block text-[12px] font-bold text-slate-700">Correct Answer</label>
            </div>
            
            <div class="space-y-3">
              <div v-for="(opt, index) in options" :key="opt.id" class="flex items-center gap-4 group">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-[13px] shrink-0 transition-colors"
                     :class="correctAnswer === opt.id ? 'bg-[#5138ed] text-white' : 'bg-indigo-50 text-[#5138ed]'">
                  {{ opt.id }}
                </div>
                <input type="text" v-model="opt.text" class="flex-1 bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors">
                
                <!-- Remove option button (only show if > 2 options) -->
                <button v-if="options.length > 2" @click="removeOption(index)" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
                <div v-else class="w-8"></div>

                <div class="w-12 flex justify-end shrink-0">
                  <div @click="correctAnswer = opt.id" class="w-5 h-5 rounded-full border-2 cursor-pointer flex items-center justify-center transition-colors"
                       :class="correctAnswer === opt.id ? 'border-[#5138ed]' : 'border-slate-300'">
                    <div v-if="correctAnswer === opt.id" class="w-2.5 h-2.5 rounded-full bg-[#5138ed] transition-transform"></div>
                  </div>
                </div>
              </div>
            </div>

            <button @click="addOption" class="mt-4 w-full py-3 border-2 border-dashed border-[#5138ed]/30 hover:border-[#5138ed] text-[#5138ed] rounded-xl font-bold text-[13px] flex items-center justify-center gap-2 transition-colors bg-indigo-50/30 hover:bg-indigo-50">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
              Add Option
            </button>
          </div>

          <div v-else-if="questionType === 'True/False'">
            <label class="block text-[12px] font-bold text-slate-700 mb-4">Correct Answer <span class="text-rose-500">*</span></label>
            <div class="flex items-center gap-4">
              <label class="flex-1 cursor-pointer" @click="trueFalseAnswer = 'True'">
                <div class="flex items-center gap-3 p-4 rounded-xl border-2 transition-colors"
                     :class="trueFalseAnswer === 'True' ? 'border-[#5138ed] bg-indigo-50' : 'border-slate-200 bg-white'">
                  <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                       :class="trueFalseAnswer === 'True' ? 'border-[#5138ed]' : 'border-slate-300'">
                    <div v-if="trueFalseAnswer === 'True'" class="w-2.5 h-2.5 rounded-full bg-[#5138ed] transition-transform"></div>
                  </div>
                  <span class="text-[13px] font-bold text-slate-700">True</span>
                </div>
              </label>
              
              <label class="flex-1 cursor-pointer" @click="trueFalseAnswer = 'False'">
                <div class="flex items-center gap-3 p-4 rounded-xl border-2 transition-colors"
                     :class="trueFalseAnswer === 'False' ? 'border-[#5138ed] bg-indigo-50' : 'border-slate-200 bg-white'">
                  <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                       :class="trueFalseAnswer === 'False' ? 'border-[#5138ed]' : 'border-slate-300'">
                    <div v-if="trueFalseAnswer === 'False'" class="w-2.5 h-2.5 rounded-full bg-[#5138ed] transition-transform"></div>
                  </div>
                  <span class="text-[13px] font-bold text-slate-700">False</span>
                </div>
              </label>
            </div>
          </div>

          <div v-else-if="questionType === 'Short Answer'">
            <label class="block text-[12px] font-bold text-slate-700 mb-2">Acceptable Answer(s) <span class="text-rose-500">*</span></label>
            <input type="text" v-model="shortAnswerText" placeholder="e.g. Relational Database" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors">
            <p class="text-[11px] text-slate-400 mt-2">Enter the exact word or phrase you expect. Case-insensitive matching will be used.</p>
          </div>

          <div v-else-if="questionType === 'Essay'">
            <label class="block text-[12px] font-bold text-slate-700 mb-2">Model Answer / Grading Rubric (Optional)</label>
            <textarea v-model="essayModelAnswer" rows="4" placeholder="Enter keywords or a model answer to assist in manual grading..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors"></textarea>
            <p class="text-[11px] text-slate-400 mt-2">Essay questions are manually graded by the instructor.</p>
          </div>

          <!-- Bottom Actions -->
          <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
            <button @click="goBack" :disabled="isSubmitting" class="px-6 py-2.5 border border-slate-200 hover:border-slate-300 text-slate-600 rounded-xl font-bold text-[13px] transition-colors disabled:opacity-50">
              Cancel
            </button>
            <div class="flex items-center gap-3">
              <button @click="saveQuestion(true)" :disabled="isSubmitting" class="px-6 py-2.5 bg-white border border-[#5138ed] text-[#5138ed] hover:bg-indigo-50 rounded-xl font-bold text-[13px] transition-colors flex items-center gap-2 disabled:opacity-50">
                <svg v-if="isSubmitting" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Save & Add New
              </button>
              <button @click="saveQuestion(false)" :disabled="isSubmitting" class="px-6 py-2.5 bg-[#5138ed] hover:bg-indigo-600 text-white rounded-xl font-bold text-[13px] transition-colors flex items-center gap-2 shadow-sm disabled:opacity-50">
                <svg v-if="isSubmitting" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Save Question
              </button>
            </div>
          </div>

        </div>

      </div>

      <!-- Right Sidebar Column -->
      <div class="w-full xl:w-[360px] space-y-6">
        
        <!-- Settings Block -->
        <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-[#5138ed] flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <h3 class="text-[14px] font-bold text-slate-800">Question Settings</h3>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-2">Marks <span class="text-rose-500">*</span></label>
              <input type="number" v-model.number="marks" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-2">Negative Marks</label>
              <input type="number" v-model.number="negativeMarks" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              <p class="text-[10px] text-slate-400 mt-1">Enter 0 if no negative marking</p>
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-700 mb-2">Time (Seconds)</label>
              <input type="number" v-model.number="timeSeconds" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              <p class="text-[10px] text-slate-400 mt-1">0 for no specific time limit</p>
            </div>
            <div class="flex items-center justify-between py-2 border-t border-slate-100 mt-2">
              <label class="block text-[12px] font-bold text-slate-700">Status</label>
              <div class="relative inline-block w-10 mr-2 align-middle select-none cursor-pointer" @click="status = !status">
                <div class="absolute block w-5 h-5 rounded-full bg-white border-2 border-slate-200 shadow shadow-slate-200 transition-transform duration-200 ease-in-out z-10" :class="status ? 'translate-x-5 !border-[#5138ed]' : ''"></div>
                <div class="block overflow-hidden h-5 rounded-full bg-slate-200 transition-colors duration-200 ease-in-out" :class="status ? '!bg-[#5138ed]' : ''"></div>
              </div>
            </div>
            <div class="pt-2 border-t border-slate-100">
              <label class="block text-[12px] font-bold text-slate-700 mb-2">Tags</label>
              <input type="text" v-model="tags" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
              <p class="text-[10px] text-slate-400 mt-1">Add tags to help organize and search</p>
            </div>
          </div>
        </div>

        <!-- Preview Block -->
        <div class="bg-indigo-50/30 border border-indigo-100 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-[#5138ed] flex items-center justify-center shrink-0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            </div>
            <h3 class="text-[14px] font-bold text-slate-800">Question Preview</h3>
          </div>
          
          <div class="mt-4">
            <p class="text-[13px] text-slate-700 font-medium mb-4" v-html="questionText || 'Question text will appear here...'"></p>
            
            <div v-if="questionType === 'Multiple Choice (MCQ)'" class="space-y-2">
              <div v-for="opt in options" :key="opt.id" class="flex items-center gap-3">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold"
                     :class="correctAnswer === opt.id ? 'bg-emerald-100 text-emerald-600' : 'bg-white text-slate-400 border border-slate-200'">
                  {{ opt.id }}
                </div>
                <span class="text-[12px] text-slate-600">{{ opt.text || `Option ${opt.id}` }}</span>
              </div>
              <div class="mt-5 py-2 px-3 bg-emerald-50 text-emerald-600 rounded-lg text-[11px] font-bold inline-block border border-emerald-100">
                Correct Answer: Option {{ correctAnswer }}
              </div>
            </div>

            <div v-else-if="questionType === 'True/False'" class="space-y-2">
              <div class="flex items-center gap-3">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold"
                     :class="trueFalseAnswer === 'True' ? 'bg-emerald-100 text-emerald-600' : 'bg-white text-slate-400 border border-slate-200'">
                  T
                </div>
                <span class="text-[12px] text-slate-600">True</span>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold"
                     :class="trueFalseAnswer === 'False' ? 'bg-emerald-100 text-emerald-600' : 'bg-white text-slate-400 border border-slate-200'">
                  F
                </div>
                <span class="text-[12px] text-slate-600">False</span>
              </div>
              <div class="mt-5 py-2 px-3 bg-emerald-50 text-emerald-600 rounded-lg text-[11px] font-bold inline-block border border-emerald-100">
                Correct Answer: {{ trueFalseAnswer }}
              </div>
            </div>

            <div v-else-if="questionType === 'Short Answer'">
              <div class="mt-2 py-2 px-3 bg-emerald-50 text-emerald-600 rounded-lg text-[11px] font-bold inline-block border border-emerald-100">
                Expected Answer: {{ shortAnswerText || '[Not provided]' }}
              </div>
            </div>

            <div v-else-if="questionType === 'Essay'">
              <div class="w-full h-24 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-center">
                <span class="text-[12px] text-slate-400 font-medium">Student text area</span>
              </div>
              <div v-if="essayModelAnswer" class="mt-3 py-2 px-3 bg-indigo-50 text-[#5138ed] rounded-lg text-[11px] font-bold border border-indigo-100">
                Has Rubric/Model Answer
              </div>
            </div>

          </div>
        </div>

        <!-- Tips -->
        <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
          <div class="flex items-center gap-2 text-emerald-500 font-bold text-[13px] mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
            Tips for Good Questions
          </div>
          <ul class="space-y-2 text-[12px] text-slate-600 font-medium">
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              Use clear and concise language
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              Ensure only one correct answer
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              Avoid negative wording
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              Make options similar in length
            </li>
            <li class="flex items-start gap-2">
              <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              Review before saving
            </li>
          </ul>
        </div>

        <!-- Shortcuts -->
        <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
          <h3 class="text-[13px] font-bold text-slate-800 mb-4">Shortcuts</h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between text-[12px]">
              <div class="flex items-center gap-1.5">
                <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded border border-slate-200 font-mono text-[10px]">Ctrl</span>
                <span class="text-slate-400">+</span>
                <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded border border-slate-200 font-mono text-[10px]">S</span>
              </div>
              <span class="text-slate-500 font-medium">Save Question</span>
            </div>
            <div class="flex items-center justify-between text-[12px]">
              <div class="flex items-center gap-1.5">
                <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded border border-slate-200 font-mono text-[10px]">Ctrl</span>
                <span class="text-slate-400">+</span>
                <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded border border-slate-200 font-mono text-[10px]">P</span>
              </div>
              <span class="text-slate-500 font-medium">Preview Question</span>
            </div>
            <div class="flex items-center justify-between text-[12px]">
              <div class="flex items-center gap-1.5">
                <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded border border-slate-200 font-mono text-[10px]">Esc</span>
              </div>
              <span class="text-slate-500 font-medium">Cancel</span>
            </div>
            <div class="flex items-center justify-between text-[12px]">
              <div class="flex items-center gap-1.5">
                <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded border border-slate-200 font-mono text-[10px]">Ctrl</span>
                <span class="text-slate-400">+</span>
                <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded border border-slate-200 font-mono text-[10px]">Enter</span>
              </div>
              <span class="text-slate-500 font-medium">Save & Add New</span>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</template>

<style scoped>
/* Custom Toggle Switch styling additions if necessary, though inline works fine for now */
</style>
