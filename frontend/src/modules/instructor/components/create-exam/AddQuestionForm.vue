<script setup lang="ts">
import { ref, computed, onUnmounted } from 'vue'
import { useCreateExamStore } from '../../store/createExamStore'
import RichTextEditor from './RichTextEditor.vue'
import { useInstructorExamStore } from '../../store/instructorExamStore'
import { useInstructorQbStore } from '../../store/instructorQbStore'

const examStore = useInstructorExamStore()
const qbStore = useInstructorQbStore()

const props = defineProps<{
  isSaving?: boolean
}>()

const emit = defineEmits(['cancel', 'next', 'save-draft'])
const formStore = useCreateExamStore()

const questionType = ref('mcq')
const questionText = ref('')
const questionMarks = ref(5)
const difficulty = ref('medium')

// ── MCQ State ──
const mcqOptions = ref([
  { id: 1, text: '', isCorrect: true },
  { id: 2, text: '', isCorrect: false },
  { id: 3, text: '', isCorrect: false },
  { id: 4, text: '', isCorrect: false },
])
const nextMcqId = ref(5)

const addMcqOption = () => {
  mcqOptions.value.push({ id: nextMcqId.value++, text: '', isCorrect: false })
}
const removeMcqOption = (index: number) => {
  if (mcqOptions.value.length <= 2) return
  const removed = mcqOptions.value.splice(index, 1)[0]
  if (removed.isCorrect && mcqOptions.value.length > 0) mcqOptions.value[0].isCorrect = true
}
const setMcqCorrect = (index: number) => {
  mcqOptions.value.forEach((opt, i) => opt.isCorrect = i === index)
}

// ── True/False State ──
const tfAnswer = ref('true')

// ── Multiple True/False State ──
const mtfStatements = ref([
  { id: 1, text: '', answer: 'true' },
  { id: 2, text: '', answer: 'true' },
])
const nextMtfId = ref(3)

const addMtfStatement = () => {
  mtfStatements.value.push({ id: nextMtfId.value++, text: '', answer: 'true' })
}
const removeMtfStatement = (index: number) => {
  if (mtfStatements.value.length <= 2) return
  mtfStatements.value.splice(index, 1)
}

// ── Fill in the Blank State ──
const fibAnswer = ref('')

// ── Short Answer State ──
const saAnswer = ref('')
const saMaxWords = ref(100)

// ── Essay State ──
const essayGuidelines = ref('')
const essayMaxWords = ref(500)

// ── Matching State ──
const matchingPairs = ref([
  { id: 1, left: '', right: '' },
  { id: 2, left: '', right: '' },
  { id: 3, left: '', right: '' },
])
const nextMatchId = ref(4)

const addMatchingPair = () => {
  matchingPairs.value.push({ id: nextMatchId.value++, left: '', right: '' })
}
const removeMatchingPair = (index: number) => {
  if (matchingPairs.value.length <= 2) return
  matchingPairs.value.splice(index, 1)
}

// ── Helpers ──
const getLetter = (index: number) => String.fromCharCode(65 + index)

const correctMcqOption = computed(() => {
  const index = mcqOptions.value.findIndex(o => o.isCorrect)
  if (index === -1) return null
  return { letter: getLetter(index), text: mcqOptions.value[index].text }
})



// ── Add Question to Exam ──
const addQuestionToExam = () => {
  if (!questionText.value.trim()) return

  let questionData: any = {
    text: questionText.value,
    type: questionType.value,
    marks: questionMarks.value,
    difficulty: difficulty.value,
  }

  switch (questionType.value) {
    case 'mcq':
      questionData.options = mcqOptions.value.map(o => o.text)
      questionData.correct_answer = correctMcqOption.value?.letter || 'A'
      // Keep backward compat: also set type to 'multiple_choice'
      questionData.type = 'multiple_choice'
      break
    case 'true_false':
      questionData.options = ['True', 'False']
      questionData.correct_answer = tfAnswer.value === 'true' ? 'A' : 'B'
      break
    case 'multiple_true_false':
      questionData.statements = mtfStatements.value.map(s => ({ text: s.text, answer: s.answer }))
      questionData.correct_answer = mtfStatements.value.map(s => s.answer).join(',')
      break
    case 'fill_blank':
      questionData.correct_answer = fibAnswer.value
      break
    case 'short_answer':
      questionData.correct_answer = saAnswer.value
      questionData.max_words = saMaxWords.value
      break
    case 'essay':
      questionData.guidelines = essayGuidelines.value
      questionData.max_words = essayMaxWords.value
      questionData.correct_answer = ''
      break
    case 'matching':
      questionData.pairs = matchingPairs.value.map(p => ({ left: p.left, right: p.right }))
      questionData.correct_answer = matchingPairs.value.map((p, i) => `${i + 1}-${p.right}`).join(',')
      break
  }

  formStore.questions.push(questionData)
  resetForm()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const resetForm = () => {
  questionText.value = ''
  questionMarks.value = 5
  mcqOptions.value = [
    { id: 1, text: '', isCorrect: true },
    { id: 2, text: '', isCorrect: false },
    { id: 3, text: '', isCorrect: false },
    { id: 4, text: '', isCorrect: false },
  ]
  nextMcqId.value = 5
  tfAnswer.value = 'true'
  mtfStatements.value = [
    { id: 1, text: '', answer: 'true' },
    { id: 2, text: '', answer: 'true' },
  ]
  nextMtfId.value = 3
  fibAnswer.value = ''
  saAnswer.value = ''
  saMaxWords.value = 100
  essayGuidelines.value = ''
  essayMaxWords.value = 500
  matchingPairs.value = [
    { id: 1, left: '', right: '' },
    { id: 2, left: '', right: '' },
    { id: 3, left: '', right: '' },
  ]
  nextMatchId.value = 4
}

const handleNext = () => {
  if (questionText.value.trim()) addQuestionToExam()
  emit('next')
}

const setDifficulty = (val: string) => {
  difficulty.value = val
}

// ── Fullscreen Toggle ──
const formContainer = ref<HTMLElement | null>(null)
const isFullscreen = ref(false)
const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    if (formContainer.value) {
      formContainer.value.requestFullscreen().catch(err => {
        console.error(`Error attempting to enable fullscreen: ${err.message}`)
      })
      isFullscreen.value = true
    }
  } else {
    document.exitFullscreen()
    isFullscreen.value = false
  }
}

// Ensure fullscreen state is updated if user exits via Esc key
const fullscreenChangeHandler = () => {
  isFullscreen.value = !!document.fullscreenElement
}
document.addEventListener('fullscreenchange', fullscreenChangeHandler)
onUnmounted(() => {
  document.removeEventListener('fullscreenchange', fullscreenChangeHandler)
})

// ── Select Existing Modal ──
const showSelectExistingModal = ref(false)

const openSelectExistingModal = async () => {
  showSelectExistingModal.value = true
  if (examStore.exams.length === 0) {
    await examStore.fetchExams()
  }
}

// ── Select Question Bank Modal ──
const showSelectQbModal = ref(false)

const openSelectQbModal = async () => {
  showSelectQbModal.value = true
  if (qbStore.banks.length === 0) {
    await qbStore.fetchQuestionBanks()
  }
}

const formatDate = (iso: string | null) => {
  if (!iso) return '-'
  return new Intl.DateTimeFormat('en-US', { month: 'short', day: 'numeric', year: 'numeric' }).format(new Date(iso))
}
const formatTime = (iso: string | null) => {
  if (!iso) return '-'
  return new Intl.DateTimeFormat('en-US', { hour: '2-digit', minute: '2-digit' }).format(new Date(iso))
}

const showImportSuccessModal = ref(false)
const importSummary = ref({
  count: 0,
  bankTitle: '',
  courseCode: '',
  date: ''
})
const isImporting = ref(false)

const selectQbForQuestions = async (bankId: number) => {
  try {
    isImporting.value = true
    const data = await qbStore.fetchQuestionBank(bankId)
    const questionsToImport = data.questions || []
    
    // Add to exam store
    if (questionsToImport.length > 0) {
      formStore.questions.push(...questionsToImport)
    }

    importSummary.value = {
      count: questionsToImport.length,
      bankTitle: data.bank?.title || 'Unknown Bank',
      courseCode: data.bank?.course_code || 'N/A',
      date: formatDate(new Date().toISOString()) + ' ' + formatTime(new Date().toISOString())
    }

    showSelectQbModal.value = false
    showImportSuccessModal.value = true
  } catch (error) {
    console.error("Failed to import questions:", error)
    alert("Failed to import questions. Please try again.")
  } finally {
    isImporting.value = false
  }
}

const selectExamForQuestions = (examId: number) => {
  alert('Questions imported from exam ' + examId + ' (Mock Implementation)')
  showSelectExistingModal.value = false
}
</script>

<template>
  <div ref="formContainer" :class="{'bg-white overflow-y-auto w-full h-full p-8': isFullscreen}">
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm mb-6" :class="{'border-none shadow-none mb-0 p-0': isFullscreen}">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h2 class="text-[15px] font-bold text-slate-800">Add New Question</h2>
        <p class="text-[12px] text-slate-500 mt-1">Create a new question or select from existing question banks.</p>
      </div>
      <div class="flex items-center gap-3">
        <!-- Fullscreen Button -->
        <button @click="toggleFullscreen" class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors" title="Toggle Fullscreen">
          <svg v-if="!isFullscreen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
          <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14h4v4m0-4L4 18m16-4h-4v4m0-4l4 4M4 10h4V6m0 4L4 6m16 4h-4V6m0 4l4-4"></path></svg>
        </button>
        
        <div class="flex items-center bg-slate-50 p-1 rounded-xl border border-slate-100">
          <button class="px-4 py-2 rounded-lg bg-white border border-[#5138ed] text-[#5138ed] text-[12px] font-bold shadow-sm">
            Create New
          </button>
          <button @click="openSelectExistingModal" class="px-4 py-2 rounded-lg text-slate-500 hover:text-slate-700 text-[12px] font-bold transition-colors border-r border-slate-200 rounded-none">
            Select Existing Exam
          </button>
          <button @click="openSelectQbModal" class="px-4 py-2 rounded-lg text-slate-500 hover:text-slate-700 text-[12px] font-bold transition-colors">
            Import from Question Bank
          </button>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8 mb-8">
      <!-- Question Type -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Question Type <span class="text-rose-500">*</span></label>
        <select v-model="questionType" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_1rem_center]">
          <option value="mcq">Multiple Choice Question (MCQ)</option>
          <option value="short_answer">Short Answer Question</option>
          <option value="essay">Essay Question</option>
          <option value="true_false">True/False Question</option>
          <option value="multiple_true_false">Multiple True/False</option>
          <option value="fill_blank">Fill in the Blank</option>
          <option value="matching">Matching Question</option>
        </select>
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Select the type of question</p>
      </div>



      <!-- Question Difficulty -->
      <div>
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Question Difficulty</label>
        <div class="flex items-center gap-3">
          <button @click="setDifficulty('easy')" :class="difficulty === 'easy' ? 'border-emerald-500 bg-emerald-50 shadow-[0_0_0_2px_rgba(16,185,129,0.1)]' : 'border-emerald-100 bg-white hover:bg-emerald-50'" class="px-5 py-2.5 rounded-xl border text-emerald-600 font-bold text-[12px] transition-colors">Easy</button>
          <button @click="setDifficulty('medium')" :class="difficulty === 'medium' ? 'border-amber-500 bg-amber-50 shadow-[0_0_0_2px_rgba(245,158,11,0.1)]' : 'border-amber-100 bg-white hover:bg-amber-50'" class="px-5 py-2.5 rounded-xl border text-amber-600 font-bold text-[12px] transition-colors">Medium</button>
          <button @click="setDifficulty('hard')" :class="difficulty === 'hard' ? 'border-rose-500 bg-rose-50 shadow-[0_0_0_2px_rgba(244,63,94,0.1)]' : 'border-rose-100 bg-white hover:bg-rose-50'" class="px-5 py-2.5 rounded-xl border text-rose-600 font-bold text-[12px] transition-colors">Hard</button>
        </div>
      </div>


    </div>

    <!-- Question Text -->
    <div class="mb-8">
      <label class="block text-[13px] font-bold text-slate-700 mb-2">
        Question Text <span class="text-rose-500">*</span>
        <span v-if="questionType === 'fill_blank'" class="text-[11px] text-indigo-500 font-medium ml-2">Use ___ (underscores) to mark the blank</span>
      </label>
      <RichTextEditor 
        v-model="questionText" 
        :placeholder="questionType === 'fill_blank' ? 'e.g. The capital of Ethiopia is ___.': 'Type or paste your question here...'" 
      />
      <p class="text-[11px] text-slate-400 mt-2 font-medium">Brief, clear and unambiguous question text</p>
    </div>

    <!-- ═══════════════════════════════════════════════ -->
    <!--  DYNAMIC ANSWER SECTION BASED ON QUESTION TYPE -->
    <!-- ═══════════════════════════════════════════════ -->

    <!-- ── MCQ Answer Options ── -->
    <div v-if="questionType === 'mcq'" class="flex flex-col lg:flex-row gap-12">
      <div class="flex-1">
        <label class="block text-[13px] font-bold text-slate-700 mb-4">Answer Options <span class="text-rose-500">*</span></label>
        <div class="space-y-3">
          <div v-for="(option, index) in mcqOptions" :key="option.id" class="flex items-center gap-3">
            <div class="relative flex items-center justify-center cursor-pointer" @click="setMcqCorrect(index)">
              <input type="radio" name="correct" :checked="option.isCorrect" class="w-4 h-4 text-[#5138ed] border-slate-300 focus:ring-[#5138ed] cursor-pointer">
              <span v-if="option.isCorrect" class="absolute inset-0 rounded-full border border-[#5138ed] shadow-[0_0_0_2px_white,0_0_0_3px_#5138ed]"></span>
            </div>
            <span class="w-6 text-[13px] font-bold text-slate-600 text-center">{{ getLetter(index) }}</span>
            <input type="text" v-model="option.text" :placeholder="'Option ' + getLetter(index)" class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
            <button @click="removeMcqOption(index)" class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
          </div>
        </div>
        <button @click="addMcqOption" class="mt-4 flex items-center gap-2 text-[#5138ed] font-bold text-[13px] hover:text-indigo-700 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Add Option
        </button>
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Add at least 2 options. Select the correct one using the radio button.</p>
      </div>
      <div class="w-full lg:w-[320px] shrink-0">
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Correct Answer <span class="text-rose-500">*</span></label>
        <div class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 bg-slate-50 truncate">
          {{ correctMcqOption ? `${correctMcqOption.letter}. ${correctMcqOption.text}` : 'None selected' }}
        </div>
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Select the correct answer using the radio buttons</p>
      </div>
    </div>

    <!-- ── True/False ── -->
    <div v-else-if="questionType === 'true_false'" class="flex flex-col lg:flex-row gap-12">
      <div class="flex-1">
        <label class="block text-[13px] font-bold text-slate-700 mb-4">Select the Correct Answer <span class="text-rose-500">*</span></label>
        <div class="flex gap-4">
          <button @click="tfAnswer = 'true'" :class="tfAnswer === 'true' ? 'border-emerald-500 bg-emerald-50 text-emerald-700 shadow-[0_0_0_2px_rgba(16,185,129,0.15)]' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'" class="flex-1 flex items-center justify-center gap-3 py-4 rounded-xl border font-bold text-[14px] transition-all cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            True
          </button>
          <button @click="tfAnswer = 'false'" :class="tfAnswer === 'false' ? 'border-rose-500 bg-rose-50 text-rose-700 shadow-[0_0_0_2px_rgba(244,63,94,0.15)]' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'" class="flex-1 flex items-center justify-center gap-3 py-4 rounded-xl border font-bold text-[14px] transition-all cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            False
          </button>
        </div>
        <p class="text-[11px] text-slate-400 mt-3 font-medium">Click to select the correct answer for this True/False question.</p>
      </div>
      <div class="w-full lg:w-[320px] shrink-0">
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Correct Answer</label>
        <div class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] bg-slate-50 font-semibold" :class="tfAnswer === 'true' ? 'text-emerald-600' : 'text-rose-600'">
          {{ tfAnswer === 'true' ? '✓ True' : '✗ False' }}
        </div>
      </div>
    </div>

    <!-- ── Multiple True/False ── -->
    <div v-else-if="questionType === 'multiple_true_false'">
      <label class="block text-[13px] font-bold text-slate-700 mb-4">Statements <span class="text-rose-500">*</span></label>
      <div class="space-y-3">
        <div v-for="(stmt, index) in mtfStatements" :key="stmt.id" class="flex items-center gap-3 p-3 border border-slate-100 rounded-xl bg-slate-50/50">
          <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-indigo-50 text-[#5138ed] font-bold text-[12px] shrink-0">{{ index + 1 }}</span>
          <input type="text" v-model="stmt.text" placeholder="Enter statement..." class="flex-1 border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 bg-white focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
          <div class="flex items-center gap-1 shrink-0">
            <button @click="stmt.answer = 'true'" :class="stmt.answer === 'true' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-slate-500 border-slate-200 hover:bg-emerald-50'" class="px-3 py-2 rounded-l-lg border text-[11px] font-bold transition-colors">True</button>
            <button @click="stmt.answer = 'false'" :class="stmt.answer === 'false' ? 'bg-rose-500 text-white border-rose-500' : 'bg-white text-slate-500 border-slate-200 hover:bg-rose-50'" class="px-3 py-2 rounded-r-lg border text-[11px] font-bold transition-colors">False</button>
          </div>
          <button @click="removeMtfStatement(index)" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
          </button>
        </div>
      </div>
      <button @click="addMtfStatement" class="mt-4 flex items-center gap-2 text-[#5138ed] font-bold text-[13px] hover:text-indigo-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add Statement
      </button>
      <p class="text-[11px] text-slate-400 mt-2 font-medium">Add statements and mark each as True or False.</p>
    </div>

    <!-- ── Fill in the Blank ── -->
    <div v-else-if="questionType === 'fill_blank'" class="flex flex-col lg:flex-row gap-12">
      <div class="flex-1">
        <label class="block text-[13px] font-bold text-slate-700 mb-4">Correct Answer <span class="text-rose-500">*</span></label>
        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/50">
          <input type="text" v-model="fibAnswer" placeholder="Enter the correct answer for the blank" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 bg-white focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
          <p class="text-[11px] text-slate-400 mt-3 font-medium">💡 Tip: Use the ___ symbol in your question text to indicate where the blank should appear.</p>
        </div>
      </div>
      <div class="w-full lg:w-[320px] shrink-0">
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Preview</label>
        <div class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-600 bg-slate-50">
          Answer: <span class="font-semibold text-[#5138ed]">{{ fibAnswer || '—' }}</span>
        </div>
      </div>
    </div>

    <!-- ── Short Answer ── -->
    <div v-else-if="questionType === 'short_answer'" class="flex flex-col lg:flex-row gap-12">
      <div class="flex-1">
        <label class="block text-[13px] font-bold text-slate-700 mb-4">Expected Answer <span class="text-rose-500">*</span></label>
        <textarea v-model="saAnswer" placeholder="Enter the expected short answer..." class="w-full min-h-[100px] border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] resize-none"></textarea>
        <p class="text-[11px] text-slate-400 mt-2 font-medium">This answer will be used as a reference for grading.</p>
      </div>
      <div class="w-full lg:w-[320px] shrink-0">
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Max Word Limit</label>
        <input type="number" v-model="saMaxWords" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Maximum words allowed for the student's answer</p>
      </div>
    </div>

    <!-- ── Essay ── -->
    <div v-else-if="questionType === 'essay'" class="flex flex-col lg:flex-row gap-12">
      <div class="flex-1">
        <label class="block text-[13px] font-bold text-slate-700 mb-4">Grading Guidelines <span class="text-slate-400 font-normal">(Optional)</span></label>
        <textarea v-model="essayGuidelines" placeholder="Enter grading guidelines or rubric criteria for evaluating the essay..." class="w-full min-h-[120px] border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] resize-none"></textarea>
        <p class="text-[11px] text-slate-400 mt-2 font-medium">These guidelines will help with consistent grading. Essays require manual grading.</p>
      </div>
      <div class="w-full lg:w-[320px] shrink-0">
        <label class="block text-[13px] font-bold text-slate-700 mb-2">Max Word Limit</label>
        <input type="number" v-model="essayMaxWords" class="w-full border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
        <p class="text-[11px] text-slate-400 mt-2 font-medium">Maximum words allowed for the student's essay</p>

        <div class="mt-5 p-3 bg-amber-50 border border-amber-200 rounded-xl">
          <div class="flex items-center gap-2 text-amber-700">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
            <span class="text-[11px] font-bold">Requires Manual Grading</span>
          </div>
          <p class="text-[10px] text-amber-600 mt-1">Essay questions cannot be auto-graded and will need instructor review.</p>
        </div>
      </div>
    </div>

    <!-- ── Matching ── -->
    <div v-else-if="questionType === 'matching'">
      <label class="block text-[13px] font-bold text-slate-700 mb-4">Matching Pairs <span class="text-rose-500">*</span></label>
      <div class="grid grid-cols-[auto_1fr_auto_1fr_auto] gap-3 items-center">
        <!-- Header -->
        <div></div>
        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wide pb-2">Column A (Premise)</div>
        <div></div>
        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wide pb-2">Column B (Response)</div>
        <div></div>

        <!-- Pairs -->
        <template v-for="(pair, index) in matchingPairs" :key="pair.id">
          <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-indigo-50 text-[#5138ed] font-bold text-[12px] shrink-0">{{ index + 1 }}</span>
          <input type="text" v-model="pair.left" placeholder="Enter premise..." class="border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
          <div class="flex items-center justify-center">
            <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
          </div>
          <input type="text" v-model="pair.right" placeholder="Enter matching response..." class="border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
          <button @click="removeMatchingPair(index)" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
          </button>
        </template>
      </div>
      <button @click="addMatchingPair" class="mt-4 flex items-center gap-2 text-[#5138ed] font-bold text-[13px] hover:text-indigo-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add Pair
      </button>
      <p class="text-[11px] text-slate-400 mt-2 font-medium">Students will match items from Column A to the correct item in Column B.</p>
    </div>

  </div>

  <!-- Action Buttons -->
  <div class="flex items-center justify-between pt-2 pb-10">
    <button @click="emit('cancel')" class="px-6 py-2.5 border border-slate-200 text-slate-600 font-bold text-[13px] rounded-xl hover:bg-slate-50 transition-colors">
      Cancel
    </button>
    
    <div class="flex items-center gap-3">
      <button @click="addQuestionToExam" class="px-6 py-2.5 bg-white border border-[#5138ed] text-[#5138ed] font-bold text-[13px] rounded-xl shadow-sm transition-colors flex items-center gap-2 hover:bg-indigo-50">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add Another Question
      </button>
      <button @click="emit('save-draft')" :disabled="isSaving" class="px-6 py-2.5 border border-slate-200 text-[#5138ed] font-bold text-[13px] rounded-xl hover:border-indigo-200 hover:bg-indigo-50 transition-colors disabled:opacity-50">
        {{ isSaving ? 'Saving...' : 'Save as Draft' }}
      </button>
      <button @click="handleNext" class="px-6 py-2.5 bg-[#5138ed] hover:bg-indigo-600 text-white font-bold text-[13px] rounded-xl shadow-sm transition-colors flex items-center gap-2">
        Next: Exam Settings
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
      </button>
    </div>
  </div>

  <!-- Select Existing Exam Modal -->
  <div v-if="showSelectExistingModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl overflow-hidden flex flex-col max-h-[90vh]">
      <div class="p-6 border-b border-slate-100 flex items-center justify-between shrink-0">
        <div>
          <h3 class="text-[18px] font-bold text-slate-800">Select Existing Exam</h3>
          <p class="text-[13px] text-slate-500 mt-1">Choose an exam to add questions from. Only exams for your current course are shown.</p>
        </div>
        <button @click="showSelectExistingModal = false" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>
      
      <div class="p-6 border-b border-slate-100 flex items-center gap-4 shrink-0">
        <div class="relative flex-1">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          </div>
          <input type="text" placeholder="Search exams..." class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
        </div>
        
        <select class="border border-slate-200 rounded-xl px-4 py-2 text-[13px] text-slate-600 focus:outline-none focus:border-[#5138ed] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:8px_8px] bg-no-repeat bg-[position:right_1rem_center] pr-10">
          <option>All Status</option>
        </select>
        
        <select class="border border-slate-200 rounded-xl px-4 py-2 text-[13px] text-slate-600 focus:outline-none focus:border-[#5138ed] appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:8px_8px] bg-no-repeat bg-[position:right_1rem_center] pr-10">
          <option>Newest First</option>
        </select>
        
        <button class="flex items-center gap-2 px-4 py-2 border border-slate-200 rounded-xl text-[13px] font-bold text-[#5138ed] hover:bg-slate-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
          Filter
        </button>
      </div>
      
      <div class="overflow-y-auto flex-1">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-100">
              <th class="py-4 px-6 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Exam Title</th>
              <th class="py-4 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date & Time</th>
              <th class="py-4 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Duration</th>
              <th class="py-4 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Marks</th>
              <th class="py-4 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Questions</th>
              <th class="py-4 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Status</th>
              <th class="py-4 px-6 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="exam in examStore.exams" :key="exam.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-indigo-50 text-[#5138ed] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                  </div>
                  <div>
                    <h4 class="text-[13px] font-bold text-slate-800">{{ exam.title }}</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">{{ exam.course_name }}</p>
                  </div>
                </div>
              </td>
              <td class="py-4 px-4">
                <div class="text-[12px] font-semibold text-slate-700">{{ formatDate(exam.scheduled_at || exam.created_at) }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">{{ formatTime(exam.scheduled_at || exam.created_at) }}</div>
              </td>
              <td class="py-4 px-4 text-[12px] font-bold text-slate-700">{{ exam.duration_minutes }} min</td>
              <td class="py-4 px-4 text-[12px] font-bold text-slate-700">{{ exam.total_marks }}</td>
              <td class="py-4 px-4 text-[12px] font-bold text-slate-700 text-center">{{ exam.questions_count || 0 }}</td>
              <td class="py-4 px-4 text-center">
                <span v-if="exam.status === 'scheduled'" class="px-2.5 py-1 rounded-md bg-indigo-50 text-[#5138ed] text-[10px] font-bold">Upcoming</span>
                <span v-else-if="exam.status === 'published'" class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600 text-[10px] font-bold">Active</span>
                <span v-else-if="exam.status === 'completed'" class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-600 text-[10px] font-bold">Completed</span>
                <span v-else-if="exam.status === 'draft'" class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 text-[10px] font-bold">Draft</span>
              </td>
              <td class="py-4 px-6 text-right">
                <button @click="selectExamForQuestions(exam.id)" class="px-5 py-1.5 rounded-lg border border-[#5138ed] text-[#5138ed] text-[12px] font-bold hover:bg-[#5138ed] hover:text-white transition-colors">Select</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

  <!-- Select From Question Bank Modal -->
  <div v-if="showSelectQbModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl h-[80vh] flex flex-col overflow-hidden">
      <div class="p-6 border-b border-slate-100 flex items-center justify-between shrink-0">
        <div>
          <h3 class="text-[18px] font-bold text-slate-800">Select Question Bank</h3>
          <p class="text-[13px] text-slate-500 mt-1">Choose a question bank to import questions from.</p>
        </div>
        <button @click="showSelectQbModal = false" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>
      
      <div class="overflow-y-auto flex-1">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-100">
              <th class="py-4 px-6 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bank Title</th>
              <th class="py-4 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Course</th>
              <th class="py-4 px-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Questions</th>
              <th class="py-4 px-6 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="bank in qbStore.banks" :key="bank.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                  </div>
                  <div>
                    <h4 class="text-[13px] font-bold text-slate-800">{{ bank.title }}</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">{{ bank.description || 'No description' }}</p>
                  </div>
                </div>
              </td>
              <td class="py-4 px-4">
                <div class="text-[12px] font-semibold text-slate-700">{{ bank.course_name }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">{{ bank.course_code }}</div>
              </td>
              <td class="py-4 px-4 text-[12px] font-bold text-slate-700 text-center">{{ bank.total_questions || 0 }}</td>
              <td class="py-4 px-6 text-right">
                <button @click="selectQbForQuestions(bank.id)" class="px-5 py-1.5 rounded-lg border border-[#5138ed] text-[#5138ed] text-[12px] font-bold hover:bg-[#5138ed] hover:text-white transition-colors">Select</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- Import Success Modal -->
  <div v-if="showImportSuccessModal" class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl flex flex-col overflow-hidden text-center p-10 relative">
      
      <!-- Checkmark Icon -->
      <div class="flex justify-center mb-6">
        <div class="relative w-20 h-20 flex items-center justify-center bg-emerald-50 rounded-full">
          <div class="absolute w-full h-full border border-dashed border-emerald-200 rounded-full animate-[spin_10s_linear_infinite]"></div>
          <svg class="w-10 h-10 text-emerald-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
          
          <!-- Decorative dots -->
          <div class="absolute -top-1 left-2 w-1.5 h-1.5 bg-amber-400 rounded-full"></div>
          <div class="absolute top-2 -right-2 w-2 h-2 bg-emerald-400 rounded-full"></div>
          <div class="absolute bottom-1 -left-2 w-1 h-1 bg-sky-400 rounded-full"></div>
          <div class="absolute -bottom-2 right-4 w-1.5 h-1.5 bg-rose-400 rounded-full"></div>
        </div>
      </div>
      
      <button @click="showImportSuccessModal = false" class="absolute top-6 right-6 w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
      </button>

      <h2 class="text-[22px] font-bold text-slate-800 mb-2">Questions Imported Successfully!</h2>
      <p class="text-[14px] text-slate-500 mb-8">Questions have been imported from the selected question bank.</p>

      <!-- Summary Card -->
      <div class="bg-white border border-slate-100 rounded-2xl p-5 mb-6 text-left shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)]">
        <div class="flex items-center gap-2 mb-4">
          <div class="w-6 h-6 rounded bg-emerald-50 text-emerald-500 flex items-center justify-center">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <h3 class="text-[14px] font-bold text-slate-800">Import Summary</h3>
        </div>

        <div class="grid grid-cols-4 gap-4">
          <div>
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-[#5138ed] flex items-center justify-center mb-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div class="text-[18px] font-black text-slate-800 leading-none mb-1">{{ importSummary.count }}</div>
            <div class="text-[11px] text-slate-500 font-medium">Questions Imported</div>
          </div>
          <div class="col-span-1">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
            </div>
            <div class="text-[11px] text-slate-400 font-medium mb-0.5">Question Bank</div>
            <div class="text-[12px] font-bold text-slate-800 leading-tight">{{ importSummary.bankTitle }}</div>
          </div>
          <div>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center mb-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div class="text-[11px] text-slate-400 font-medium mb-0.5">Course</div>
            <div class="text-[12px] font-bold text-slate-800">{{ importSummary.courseCode }}</div>
          </div>
          <div>
            <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-500 flex items-center justify-center mb-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div class="text-[11px] text-slate-400 font-medium mb-0.5">Imported On</div>
            <div class="text-[12px] font-bold text-slate-800 leading-tight">
              {{ importSummary.date.split(' ').slice(0, 3).join(' ') }}<br/>
              <span class="text-slate-500">{{ importSummary.date.split(' ').slice(3).join(' ') }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Info Box -->
      <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 text-left mb-8">
        <div class="flex items-center gap-2 mb-1">
          <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          <span class="text-[12px] font-bold text-[#5138ed]">What happens next?</span>
        </div>
        <p class="text-[11.5px] text-indigo-900/60 ml-6">You can review, edit, and customize the imported questions in the next step.</p>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-center gap-4">
        <button @click="showImportSuccessModal = false; showSelectQbModal = true" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-600 text-[13px] font-bold hover:bg-slate-50 transition-colors flex items-center gap-2">
          <span>+ Stay Here & Import More</span>
        </button>
        <button @click="showImportSuccessModal = false; emit('next')" class="px-6 py-3 rounded-xl bg-[#5138ed] text-white text-[13px] font-bold hover:bg-[#4630d1] transition-colors shadow-lg shadow-indigo-200/50 flex items-center gap-2">
          <span>Next: Exam Settings &rarr;</span>
        </button>
      </div>
    </div>
  </div>
</template>
