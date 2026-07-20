<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useInstructorQbStore } from '../store/instructorQbStore'

const route = useRoute()
const router = useRouter()
const qbStore = useInstructorQbStore()

const bankId = route.params.id as string
const bank = ref<any>(null)
const isLoading = ref(true)

// Form State (Mocked)
const questionType = ref('Multiple Choice (MCQ)')
const difficulty = ref('Medium')
const score = ref(1)
const chapter = ref('Chapter 1')

const title = ref('')
const description = ref('')
const questionText = ref('')

const options = ref([
  { id: 1, label: 'A', text: '' },
  { id: 2, label: 'B', text: '' },
  { id: 3, label: 'C', text: '' },
  { id: 4, label: 'D', text: '' }
])
const correctAnswer = ref('')
const explanation = ref('')

// Matching question data
const matchingQuestions = ref([
  { id: 1, label: 'A', text: 'Database' },
  { id: 2, label: 'B', text: 'Primary Key' },
  { id: 3, label: 'C', text: 'SQL' },
  { id: 4, label: 'D', text: 'Foreign Key' },
  { id: 5, label: 'E', text: 'Normalization' }
])
const matchingOptions = ref([
  { id: 1, label: 'A', text: 'A unique identifier for a record' },
  { id: 2, label: 'B', text: 'A structured collection of related data.' },
  { id: 3, label: 'C', text: 'Structured Query Language.' },
  { id: 4, label: 'D', text: 'A key used to link two tables together.' },
  { id: 5, label: 'E', text: 'The process of organizing data to reduce redundancy.' }
])

// Settings
const shuffleChoices = ref(true)
const requiredQuestion = ref(true)
const allowPartialMarks = ref(false)
const negativeMarking = ref(false)
const showExplanation = ref(true)

onMounted(async () => {
  try {
    const data = await qbStore.fetchQuestionBank(bankId)
    bank.value = data.bank
  } catch (err) {
    console.error("Failed to load bank", err)
  } finally {
    isLoading.value = false
  }
})

const formatDate = (iso: string) => {
  if (!iso) return '—'
  return new Date(iso).toLocaleDateString('en-US', {
    month: 'short', day: '2-digit', year: 'numeric'
  })
}

const addOption = () => {
  const nextLabel = String.fromCharCode(65 + options.value.length)
  options.value.push({ id: Date.now(), label: nextLabel, text: '' })
}

const removeOption = (index: number) => {
  options.value.splice(index, 1)
  options.value.forEach((opt, i) => {
    opt.label = String.fromCharCode(65 + i)
  })
}

// Matching helpers
const addMatchingRow = () => {
  const nextLabel = String.fromCharCode(65 + matchingQuestions.value.length)
  matchingQuestions.value.push({ id: Date.now(), label: nextLabel, text: '' })
}

const removeMatchingRow = (index: number) => {
  matchingQuestions.value.splice(index, 1)
  matchingQuestions.value.forEach((q, i) => {
    q.label = String.fromCharCode(65 + i)
  })
}

const addMatchingOption = () => {
  const nextLabel = String.fromCharCode(65 + matchingOptions.value.length)
  matchingOptions.value.push({ id: Date.now(), label: nextLabel, text: '' })
}

const removeMatchingOption = (index: number) => {
  matchingOptions.value.splice(index, 1)
  matchingOptions.value.forEach((o, i) => {
    o.label = String.fromCharCode(65 + i)
  })
}

const handleCancel = () => {
  router.push('/instructor/question-banks')
}
</script>

<template>
  <div class="pb-24">
    <!-- Breadcrumbs & Header -->
    <div class="max-w-[1400px] mx-auto mb-6">
      <div class="flex flex-col gap-2">
        <div>
          <h1 class="text-2xl font-bold text-slate-800">Create Question</h1>
          <p class="text-[14px] text-slate-500 mt-1">Create a new question inside this Question Bank.</p>
        </div>
        <div class="flex items-center gap-2 text-[13px] text-slate-500 font-medium mt-2">
          <router-link to="/instructor/question-banks" class="hover:text-[#5138ed] transition-colors">Question Banks</router-link>
          <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          <span v-if="!isLoading" class="hover:text-[#5138ed] transition-colors cursor-pointer">{{ bank?.title || 'Loading...' }}</span>
          <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
          <span class="text-[#5138ed]">Create Question</span>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="max-w-[1400px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6">
      
      <!-- Left Column (Main Form) -->
      <div class="lg:col-span-8 space-y-6">
        
        <!-- Question Bank Information -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h2 class="text-[15px] font-bold text-slate-800">Question Bank Information</h2>
          </div>
          
          <div v-if="isLoading" class="text-sm text-slate-500">Loading bank details...</div>
          <div v-else class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
              <p class="text-[12px] font-bold text-slate-800 mb-1">Question Bank</p>
              <p class="text-[13px] text-slate-600">{{ bank?.title }}</p>
            </div>
            <div class="md:col-span-2">
              <p class="text-[12px] font-bold text-slate-800 mb-1">Description</p>
              <p class="text-[13px] text-slate-600">{{ bank?.description || 'No description available.' }}</p>
            </div>
            <div>
              <p class="text-[12px] font-bold text-slate-800 mb-1">Created Date</p>
              <div class="flex items-center gap-1.5 text-[13px] text-slate-600">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                {{ formatDate(bank?.created_at || bank?.updated_at) }}
              </div>
            </div>
          </div>
        </div>

        <!-- Question Information -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
            <h2 class="text-[15px] font-bold text-slate-800">Question Information</h2>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
              <label class="block text-[12px] font-bold text-slate-800 mb-2">Question Type <span class="text-rose-500">*</span></label>
              <select v-model="questionType" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] appearance-none cursor-pointer">
                <option>Multiple Choice (MCQ)</option>
                <option>Short Answer</option>
                <option>Essay</option>
                <option>True / False</option>
                <option>Matching</option>
                <option>Fill in the Blank</option>
              </select>
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-800 mb-2">Question Difficulty <span class="text-rose-500">*</span></label>
              <select v-model="difficulty" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] appearance-none cursor-pointer">
                <option>Easy</option>
                <option>Medium</option>
                <option>Hard</option>
              </select>
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-800 mb-2">Question Score <span class="text-rose-500">*</span></label>
              <input v-model="score" type="number" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed]" min="1" />
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-800 mb-2">Chapter</label>
              <select v-model="chapter" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] appearance-none cursor-pointer">
                <option>Chapter 1</option>
                <option>Chapter 2</option>
                <option>Chapter 3</option>
                <option>Chapter 4</option>
                <option>Chapter 5</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Question Content -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
            </div>
            <h2 class="text-[15px] font-bold text-slate-800">Question Content</h2>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
              <label class="block text-[12px] font-bold text-slate-800 mb-2">Question Title <span class="text-rose-500">*</span></label>
              <input v-model="title" type="text" placeholder="Enter a short title for this question" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed]" />
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-800 mb-2">Question Description (Optional)</label>
              <input v-model="description" type="text" placeholder="Enter a brief description or additional notes about this question..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed]" />
            </div>
          </div>

          <div>
            <label class="block text-[12px] font-bold text-slate-800 mb-2">Question Text <span class="text-rose-500">*</span></label>
            <div class="border border-slate-200 rounded-xl overflow-hidden">
              <!-- Toolbar mock -->
              <div class="bg-slate-50 border-b border-slate-200 px-4 py-2 flex items-center gap-4 text-slate-600">
                <select class="bg-transparent text-[13px] font-medium focus:outline-none cursor-pointer">
                  <option>Paragraph</option>
                  <option>Heading 1</option>
                  <option>Heading 2</option>
                </select>
                <div class="w-px h-4 bg-slate-300"></div>
                <button class="hover:text-slate-800 transition-colors font-bold">B</button>
                <button class="hover:text-slate-800 transition-colors italic">I</button>
                <button class="hover:text-slate-800 transition-colors underline">U</button>
                <button class="hover:text-slate-800 transition-colors line-through">S</button>
                <div class="w-px h-4 bg-slate-300"></div>
                <button class="hover:text-slate-800 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
                <button class="hover:text-slate-800 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path></svg></button>
                <div class="w-px h-4 bg-slate-300"></div>
                <button class="hover:text-slate-800 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></button>
                <button class="hover:text-slate-800 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg></button>
                <button class="hover:text-slate-800 transition-colors italic font-serif">fx</button>
                <button class="hover:text-slate-800 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg></button>
              </div>
              <textarea v-model="questionText" rows="5" class="w-full bg-white px-4 py-4 text-[13px] text-slate-700 focus:outline-none resize-none" placeholder="Type or paste the question text here..."></textarea>
              <div class="px-4 pb-2 text-right text-[11px] text-slate-400">0 / 3000</div>
            </div>
          </div>
        </div>

        <!-- Answer Options (MCQ) -->
        <div v-if="questionType === 'Multiple Choice (MCQ)'" class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <h2 class="text-[15px] font-bold text-slate-800">Answer Options <span class="text-slate-500 font-normal">(Multiple Choice)</span></h2>
          </div>
          
          <div class="space-y-3 mb-4">
            <div v-for="(option, index) in options" :key="option.id" class="flex items-center gap-4 group">
              <button class="text-slate-300 hover:text-slate-500 cursor-move transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
              </button>
              <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-200 text-[13px] font-bold text-slate-700 flex-shrink-0">
                {{ option.label }}
              </div>
              <input v-model="option.text" type="text" :placeholder="`Enter option ${option.label}`" class="flex-1 bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed]" />
              <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center cursor-pointer">
                <div v-if="correctAnswer === option.label" class="w-2.5 h-2.5 rounded-full bg-[#5138ed]"></div>
              </div>
              <button @click="removeOption(index)" class="w-8 h-8 flex items-center justify-center rounded-lg text-rose-400 hover:bg-rose-50 hover:text-rose-600 transition-colors opacity-0 group-hover:opacity-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </div>
          </div>

          <button @click="addOption" class="flex items-center gap-1.5 text-[13px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Option
          </button>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-100">
            <div>
              <label class="block text-[12px] font-bold text-slate-800 mb-2">Correct Answer <span class="text-rose-500">*</span></label>
              <select v-model="correctAnswer" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] appearance-none cursor-pointer">
                <option value="" disabled>Select the correct option</option>
                <option v-for="opt in options" :key="opt.id" :value="opt.label">Option {{ opt.label }}</option>
              </select>
            </div>
            <div>
              <label class="block text-[12px] font-bold text-slate-800 mb-2">Explanation (Optional)</label>
              <textarea v-model="explanation" rows="2" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] resize-none" placeholder="Explain why this is the correct answer..."></textarea>
              <div class="text-right text-[11px] text-slate-400 mt-1">0 / 1000</div>
            </div>
          </div>
        </div>

        <!-- True / False Options -->
        <div v-if="questionType === 'True / False'" class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-[15px] font-bold text-slate-800">True / False Options</h2>
          </div>
          
          <div class="mb-6">
            <label class="block text-[12px] font-bold text-slate-800 mb-3">Correct Answer <span class="text-rose-500">*</span></label>
            <div class="flex gap-4">
              <label class="flex-1 relative cursor-pointer" @click="correctAnswer = 'True'">
                <div :class="[
                  'flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl border transition-all',
                  correctAnswer === 'True' ? 'border-[#5138ed] bg-indigo-50/30 text-[#5138ed] shadow-[0_0_0_1px_#5138ed]' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'
                ]">
                  <div :class="[
                    'w-4 h-4 rounded-full border-2 flex items-center justify-center',
                    correctAnswer === 'True' ? 'border-[#5138ed]' : 'border-slate-300'
                  ]">
                    <div v-if="correctAnswer === 'True'" class="w-2 h-2 rounded-full bg-[#5138ed]"></div>
                  </div>
                  <span class="text-[13px] font-bold">True</span>
                </div>
              </label>
              
              <label class="flex-1 relative cursor-pointer" @click="correctAnswer = 'False'">
                <div :class="[
                  'flex items-center justify-center gap-2 w-full px-4 py-3 rounded-xl border transition-all',
                  correctAnswer === 'False' ? 'border-[#5138ed] bg-indigo-50/30 text-[#5138ed] shadow-[0_0_0_1px_#5138ed]' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'
                ]">
                  <div :class="[
                    'w-4 h-4 rounded-full border-2 flex items-center justify-center',
                    correctAnswer === 'False' ? 'border-[#5138ed]' : 'border-slate-300'
                  ]">
                    <div v-if="correctAnswer === 'False'" class="w-2 h-2 rounded-full bg-[#5138ed]"></div>
                  </div>
                  <span class="text-[13px] font-bold">False</span>
                </div>
              </label>
            </div>
          </div>

          <div>
            <label class="block text-[12px] font-bold text-slate-800 mb-2">Explanation (Optional)</label>
            <textarea v-model="explanation" rows="3" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] resize-none" placeholder="Provide an explanation..."></textarea>
            <div class="text-right text-[11px] text-slate-400 mt-1">{{ explanation.length }} / 1000</div>
          </div>
        </div>

        <!-- Matching Question Builder -->
        <div v-if="questionType === 'Matching'" class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12M8 12h12M8 17h12M4 7h.01M4 12h.01M4 17h.01"></path></svg>
            </div>
            <h2 class="text-[15px] font-bold text-slate-800">Matching Question Builder</h2>
            <div class="w-5 h-5 rounded-full border border-slate-300 flex items-center justify-center text-slate-400 cursor-help" title="Match items from Column A to Column B">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
          </div>

          <!-- Two Column Headers -->
          <div class="grid grid-cols-2 gap-8 mb-4">
            <div>
              <p class="text-[12px] font-bold text-slate-800 mb-1">Column A (Questions)</p>
              <p class="text-[11px] text-slate-400">- Enter the correct answer key</p>
            </div>
            <div>
              <p class="text-[12px] font-bold text-slate-800 mb-1">Column B (Options)</p>
              <p class="text-[11px] text-slate-400">- Enter the answer options</p>
            </div>
          </div>

          <!-- Matching Rows -->
          <div class="space-y-3 mb-4">
            <div v-for="(pair, index) in matchingQuestions" :key="pair.id" class="grid grid-cols-2 gap-4 items-center">
              <!-- Column A -->
              <div class="flex items-center gap-3 group">
                <button class="text-slate-300 hover:text-slate-500 cursor-move transition-colors flex-shrink-0">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                </button>
                <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-50 border border-indigo-100 text-[13px] font-bold text-[#5138ed] flex-shrink-0">
                  {{ pair.label }}
                </div>
                <input v-model="pair.text" type="text" :placeholder="`Enter term ${pair.label}`" class="flex-1 bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed]" />
              </div>
              <!-- Arrow -->
              <div class="flex items-center gap-3">
                <span class="text-slate-300 font-bold text-[13px] flex-shrink-0">↔</span>
                <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-200 text-[13px] font-bold text-slate-700 flex-shrink-0">
                  {{ matchingOptions[index]?.label || '' }}
                </div>
                <input v-model="matchingOptions[index].text" type="text" :placeholder="`Enter description ${matchingOptions[index]?.label}`" class="flex-1 bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed]" />
                <button @click="removeMatchingRow(index); removeMatchingOption(index)" class="w-8 h-8 flex items-center justify-center rounded-lg text-rose-400 hover:bg-rose-50 hover:text-rose-600 transition-colors flex-shrink-0">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Add Row / Add Option Buttons -->
          <div class="flex items-center gap-6 mb-6">
            <button @click="addMatchingRow(); addMatchingOption()" class="flex items-center gap-1.5 text-[13px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
              Add Row
            </button>
            <button @click="addMatchingOption" class="flex items-center gap-1.5 text-[13px] font-bold text-[#5138ed] hover:text-indigo-700 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
              Add Option
            </button>
          </div>

          <!-- Info Note -->
          <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl px-4 py-3 flex items-start gap-3">
            <svg class="w-4 h-4 text-[#5138ed] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-[12px] text-slate-600 leading-relaxed">Students will see the options in Column B in random order during the exam. The letters you enter in Column A will be used as the correct answers.</p>
          </div>
        </div>

        <!-- Fill in the Blank (simple textarea) -->
        <div v-if="questionType === 'Fill in the Blank'" class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <h2 class="text-[15px] font-bold text-slate-800">Correct Answer <span class="text-slate-500 font-normal">(Fill in the Blank)</span></h2>
          </div>
          <div>
            <label class="block text-[12px] font-bold text-slate-800 mb-2">Expected Answer <span class="text-rose-500">*</span></label>
            <input type="text" placeholder="Enter the correct answer for the blank" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed]" />
            <p class="text-[11px] text-slate-400 mt-2">Use <strong>____</strong> (underscores) in the Question Text above to indicate where the blank should appear.</p>
          </div>
          <div class="mt-4">
            <label class="block text-[12px] font-bold text-slate-800 mb-2">Explanation (Optional)</label>
            <textarea v-model="explanation" rows="2" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] resize-none" placeholder="Explain why this is the correct answer..."></textarea>
          </div>
        </div>

        <!-- Short Answer Settings -->
        <div v-if="questionType === 'Short Answer'" class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <h2 class="text-[15px] font-bold text-slate-800">Short Answer Settings</h2>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
            <div>
              <label class="block text-[12px] font-bold text-slate-800 mb-2">Expected Answer / Answer Key <span class="text-rose-500">*</span></label>
              <textarea rows="3" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] resize-none" placeholder="Normalization is the process of organizing data in a database to reduce redundancy and improve data integrity."></textarea>
              <div class="text-right text-[11px] text-slate-400 mt-1">105 / 3000</div>
            </div>
            <div>
              <div class="flex items-center gap-1.5 mb-1">
                <label class="block text-[12px] font-bold text-slate-800">Case Sensitivity</label>
                <div class="w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center text-slate-400 cursor-help" title="Case sensitivity rules">
                  <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
              </div>
              <p class="text-[11px] text-slate-500 mb-4">Determine if the student answer is case sensitive.</p>
              
              <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer group">
                  <div class="w-4 h-4 rounded-full border-2 border-[#5138ed] flex items-center justify-center">
                    <div class="w-2 h-2 rounded-full bg-[#5138ed]"></div>
                  </div>
                  <span class="text-[13px] font-bold text-[#5138ed]">Not Case Sensitive (Recommended)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                  <div class="w-4 h-4 rounded-full border-2 border-slate-300 flex items-center justify-center group-hover:border-slate-400 transition-colors">
                  </div>
                  <span class="text-[13px] font-medium text-slate-600">Case Sensitive</span>
                </label>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-[12px] font-bold text-slate-800 mb-2">Additional Notes for Grading (Optional)</label>
            <textarea v-model="explanation" rows="2" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] resize-none" placeholder="Any additional information for graders..."></textarea>
            <div class="text-right text-[11px] text-slate-400 mt-1">0 / 1000</div>
          </div>
        </div>

        <!-- Essay Question Settings -->
        <div v-if="questionType === 'Essay'" class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h2 class="text-[15px] font-bold text-slate-800">Essay Question Settings</h2>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
            <!-- Left: Reference Answer -->
            <div>
              <label class="block text-[12px] font-bold text-slate-800 mb-1">Reference Answer / Model Answer (Optional)</label>
              <p class="text-[11px] text-slate-500 mb-2">Provide a reference answer or key points that describe an ideal response.</p>
              <textarea rows="4" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] resize-none" placeholder="ACID stands for Atomicity, Consistency, Isolation, Durability. Explain each property with definition, importance, and example."></textarea>
              <div class="text-right text-[11px] text-slate-400 mt-1">112 / 5000</div>
            </div>
            
            <!-- Right: Grading Notes -->
            <div>
              <label class="block text-[12px] font-bold text-slate-800 mb-1">Grading Notes / Rubric (Optional)</label>
              <p class="text-[11px] text-slate-500 mb-2">Provide guidelines for grading this essay question.</p>
              <textarea rows="4" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed] resize-none" placeholder="• Clear understanding of each property (2 marks)&#10;• Explanation with proper examples (2 marks)&#10;• Overall organization and clarity (1 mark)"></textarea>
              <div class="text-right text-[11px] text-slate-400 mt-1">121 / 2000</div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Left: Min Words -->
            <div>
              <label class="block text-[12px] font-bold text-slate-800 mb-1">Minimum Words (Optional)</label>
              <p class="text-[11px] text-slate-500 mb-2">Set a minimum word limit for the answer.</p>
              <input type="number" placeholder="150" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed]" />
            </div>
            
            <!-- Right: Max Words -->
            <div>
              <label class="block text-[12px] font-bold text-slate-800 mb-1">Maximum Words (Optional)</label>
              <p class="text-[11px] text-slate-500 mb-2">Set a maximum word limit for the answer.</p>
              <input type="number" placeholder="1000" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] text-slate-700 focus:outline-none focus:border-[#5138ed]" />
            </div>
          </div>
        </div>

      </div>

      <!-- Right Column (Sidebar) -->
      <div class="lg:col-span-4 space-y-6">
        
        <!-- Question Image -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
              <h3 class="text-[14px] font-bold text-slate-800">Question Image <span class="text-slate-400 font-normal">(Optional)</span></h3>
            </div>
          </div>
          
          <div class="border-2 border-dashed border-indigo-100 bg-indigo-50/30 rounded-xl p-8 text-center flex flex-col items-center justify-center transition-colors hover:bg-indigo-50/50 cursor-pointer mb-4">
            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-[#5138ed] shadow-sm mb-3">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            </div>
            <p class="text-[13px] font-bold text-slate-700 mb-1">Drag & Drop image here</p>
            <p class="text-[12px] text-slate-500 mb-4">or</p>
            <button class="px-4 py-2 border border-[#5138ed] text-[#5138ed] bg-white rounded-lg text-[13px] font-bold hover:bg-indigo-50 transition-colors">Browse Image</button>
            <p class="text-[11px] text-slate-400 mt-4">Supported: PNG, JPG, JPEG (Max 5 MB)</p>
          </div>

          <!-- Mock Uploaded Image -->
          <div class="border border-slate-100 rounded-xl p-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-slate-100 rounded-lg overflow-hidden flex items-center justify-center">
                <!-- Mock image grid graphic -->
                <svg class="w-6 h-6 text-slate-300" fill="currentColor" viewBox="0 0 24 24"><path d="M4 4h16v16H4V4zm2 2v12h12V6H6zm2 2h8v2H8V8zm0 4h8v2H8v-2z"></path></svg>
              </div>
              <div>
                <p class="text-[12px] font-bold text-slate-700 truncate w-32">database_relationship.png</p>
                <p class="text-[11px] text-slate-400">234 KB</p>
              </div>
            </div>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg text-rose-500 hover:bg-rose-50 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
          </div>
        </div>

        <!-- Question Settings -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
          <div class="flex items-center gap-2 mb-6">
            <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <h3 class="text-[14px] font-bold text-slate-800">Question Settings</h3>
          </div>
          
          <div class="space-y-4">
            <label class="flex items-center gap-3 cursor-pointer group">
              <div class="relative flex items-center justify-center w-5 h-5 rounded border border-indigo-500 bg-[#5138ed]">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
              </div>
              <span class="text-[13px] text-slate-700 font-medium select-none">Shuffle Choices</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer group">
              <div class="relative flex items-center justify-center w-5 h-5 rounded border border-indigo-500 bg-[#5138ed]">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
              </div>
              <span class="text-[13px] text-slate-700 font-medium select-none">Required Question</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer group">
              <div class="relative flex items-center justify-center w-5 h-5 rounded border border-slate-300 bg-white group-hover:border-slate-400 transition-colors">
              </div>
              <span class="text-[13px] text-slate-700 font-medium select-none">Allow Partial Marks</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer group">
              <div class="relative flex items-center justify-center w-5 h-5 rounded border border-slate-300 bg-white group-hover:border-slate-400 transition-colors">
              </div>
              <span class="text-[13px] text-slate-700 font-medium select-none">Negative Marking</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer group">
              <div class="relative flex items-center justify-center w-5 h-5 rounded border border-indigo-500 bg-[#5138ed]">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
              </div>
              <span class="text-[13px] text-slate-700 font-medium select-none">Show Explanation After Exam</span>
            </label>
          </div>
        </div>

        <!-- Question Tags -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
          <div class="flex items-center gap-2 mb-6">
            <svg class="w-4 h-4 text-[#5138ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            <h3 class="text-[14px] font-bold text-slate-800">Question Tags</h3>
          </div>
          
          <div class="flex flex-wrap gap-2 mb-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-[12px] font-bold">
              Algorithms
              <button class="hover:text-indigo-900 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-[12px] font-bold">
              Database
              <button class="hover:text-indigo-900 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-[12px] font-bold">
              Data Structures
              <button class="hover:text-indigo-900 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-[12px] font-bold">
              Programming
              <button class="hover:text-indigo-900 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-[12px] font-bold">
              Networking
              <button class="hover:text-indigo-900 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </span>
          </div>

          <button class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-500 text-[12px] font-bold hover:bg-slate-50 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Tag
          </button>
        </div>

      </div>
    </div>

    <!-- Sticky Footer Actions -->
    <div class="fixed bottom-0 left-0 lg:left-64 right-0 bg-white border-t border-slate-200 px-8 py-4 z-40 flex items-center justify-between">
      <!-- Add Other Question Button (left side, for ALL types) -->
      <button class="hidden sm:flex items-center gap-3 px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors group">
        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-[#5138ed] group-hover:bg-indigo-100 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </div>
        <div class="text-left">
          <p class="text-[13px] font-bold text-slate-800">Add Other Question</p>
          <p class="text-[11px] text-slate-400">Add a different type of question to this bank</p>
        </div>
      </button>
      <div class="flex items-center gap-4 w-full sm:w-auto justify-end">
        <button @click="handleCancel" class="px-6 py-2.5 rounded-xl border border-slate-200 text-[14px] font-bold text-slate-600 hover:bg-slate-50 transition-colors flex-1 sm:flex-none text-center">
          Cancel
        </button>
        <button class="px-6 py-2.5 rounded-xl border border-slate-200 text-[14px] font-bold text-slate-700 hover:bg-slate-50 transition-colors flex items-center justify-center gap-2 flex-1 sm:flex-none">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
          Save as Draft
        </button>
        <button class="px-6 py-2.5 rounded-xl bg-[#5138ed] text-white text-[14px] font-bold hover:bg-indigo-600 transition-colors shadow-sm flex items-center justify-center gap-2 flex-1 sm:flex-none">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
          Save Question
        </button>
      </div>
    </div>
  </div>
</template>
