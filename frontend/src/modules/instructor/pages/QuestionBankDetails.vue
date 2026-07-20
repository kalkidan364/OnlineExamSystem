<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import apiClient from '../../../core/api/apiClient'
import { useInstructorQbStore } from '../store/instructorQbStore'

const route = useRoute()
const router = useRouter()
const qbStore = useInstructorQbStore()

const isLoading = ref(true)
const error = ref('')

// Real data from API
const bank = ref<any>({
  id: route.params.id,
  title: '',
  description: '',
  course_code: '',
  course_name: '',
  status: 'Active',
})

const stats = ref({
  total: 0,
  mcq: 0,
  sa: 0,
  essay: 0,
  tf: 0,
})

const questions = ref<any[]>([])
const manualCategories = ref<{name: string, count: number}[]>([])
const categories = computed(() => {
  const map = new Map<string, number>()
  
  questions.value.forEach(q => {
    const topic = q.topic || q.chapter
    if (topic) {
      map.set(topic, (map.get(topic) || 0) + 1)
    }
  })
  
  manualCategories.value.forEach(cat => {
    if (!map.has(cat.name)) {
      map.set(cat.name, 0)
    }
  })
  
  const cats = Array.from(map.entries()).map(([name, count]) => ({ name, count }))
  return cats.sort((a, b) => b.count - a.count)
})

const showAllCategories = ref(false)
const displayedCategories = computed(() => {
  if (showAllCategories.value) return categories.value
  return categories.value.slice(0, 5)
})

const searchQuery = ref('')
const selectedType = ref('All Types')
const selectedDifficulty = ref('All Difficulties')
const selectedStatus = ref('All Statuses')
const selectedChapter = ref('All Chapters')

const availableChapters = computed(() => {
  const chapters = new Set<string>()
  questions.value.forEach(q => {
    if (q.chapter) chapters.add(q.chapter)
  })
  return ['All Chapters', ...Array.from(chapters)]
})

const activeTab = ref('All Questions')

// Computed tabs with real counts
const tabs = computed(() => [
  'All Questions',
  `MCQ (${stats.value.mcq})`,
  `Short Answer (${stats.value.sa})`,
  `Essay (${stats.value.essay})`,
  `True/False (${stats.value.tf})`,
])

// Map backend type values to display labels
const typeLabel = (type: string) => {
  const map: Record<string, string> = {
    multiple_choice: 'MCQ',
    short_answer: 'Short Answer',
    essay: 'Essay',
    true_false: 'True/False',
  }
  return map[type] || type
}

// Filter questions by active tab, filters, and search
const filteredQuestions = computed(() => {
  let result = questions.value
  if (activeTab.value !== 'All Questions') {
    const typeMap: Record<string, string> = {
      'MCQ': 'multiple_choice',
      'Short Answer': 'short_answer',
      'Essay': 'essay',
      'True/False': 'true_false',
    }
    const tabType = activeTab.value.replace(/\s*\(\d+\)/, '')
    const backendType = typeMap[tabType]
    if (backendType) result = result.filter(q => q.type === backendType)
  }
  
  if (selectedType.value !== 'All Types') {
    const typeMap: Record<string, string> = {
      'MCQ': 'multiple_choice',
      'Short Answer': 'short_answer',
      'Essay': 'essay',
      'True/False': 'true_false',
      'Matching': 'matching',
      'Fill in Blank': 'fill_in_blank',
      'Ordering': 'ordering'
    }
    const backendType = typeMap[selectedType.value]
    if (backendType) result = result.filter(q => q.type === backendType)
  }

  if (selectedDifficulty.value !== 'All Difficulties') {
    result = result.filter(q => q.difficulty === selectedDifficulty.value)
  }

  if (selectedStatus.value !== 'All Statuses') {
    result = result.filter(q => q.status === selectedStatus.value)
  }

  if (selectedChapter.value !== 'All Chapters') {
    result = result.filter(q => q.chapter === selectedChapter.value)
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(item => item.text?.toLowerCase().includes(q) || item.topic?.toLowerCase().includes(q) || item.chapter?.toLowerCase().includes(q))
  }
  return result
})

// Percentage helper
const pct = (count: number) => {
  if (stats.value.total === 0) return '0%'
  return ((count / stats.value.total) * 100).toFixed(1) + '%'
}

const fetchBankDetails = async () => {
  isLoading.value = true
  error.value = ''
  try {
    const response = await apiClient.get(`/instructor/question-banks/${route.params.id}`)
    const data = response.data.data

    bank.value = {
      id: data.bank.id,
      title: data.bank.title,
      description: data.bank.description,
      course_code: data.bank.course_code,
      course_name: data.bank.course_name,
      status: data.bank.status,
    }

    stats.value = data.stats
    questions.value = data.questions
  } catch (err: any) {
    console.warn('API failed, falling back to mock bank and data', err)
    // Fallback Mock Data for Bank
    bank.value = {
      id: Number(route.params.id) || 1,
      title: 'Database Systems Mid Questions',
      description: 'Questions prepared for Semester I Mid Examination.',
      course_code: 'CS301',
      course_name: 'Database Management Systems',
      status: 'Active',
    }
  } finally {
    isLoading.value = false
  }

  // Inject mock questions if empty (so the UI always shows data for demonstration)
  if (!questions.value || questions.value.length === 0) {
    stats.value = {
      total: 6,
      mcq: 2,
      sa: 1,
      essay: 1,
      tf: 1,
      matching: 1
    }
    questions.value = [
      {
        id: 1,
        text: '<p>What does SQL stand for?</p>',
        type: 'multiple_choice',
        difficulty: 'Easy',
        marks: 1,
        chapter: 'Chapter 1: Intro to DB',
        topic: 'SQL Basics',
        status: 'Active',
        explanation: 'Structured Query Language is standard for relational databases.',
      },
      {
        id: 2,
        text: '<p>Describe the difference between inner and outer joins.</p>',
        type: 'essay',
        difficulty: 'Hard',
        marks: 5,
        chapter: 'Chapter 3: SQL Queries',
        topic: 'Joins',
        status: 'Active',
        explanation: 'Inner join requires match, outer join retains unmatched rows.',
      },
      {
        id: 3,
        text: '<p>A primary key must be unique and cannot be null.</p>',
        type: 'true_false',
        difficulty: 'Easy',
        marks: 1,
        chapter: 'Chapter 2: Relational Model',
        topic: 'Constraints',
        status: 'Active',
        explanation: 'Primary keys enforce entity integrity.',
      },
      {
        id: 4,
        text: '<p>Define 1NF.</p>',
        type: 'short_answer',
        difficulty: 'Medium',
        marks: 2,
        chapter: 'Chapter 4: Normalization',
        topic: '1NF',
        status: 'Active',
        explanation: 'First Normal Form requires atomic attributes.',
      },
      {
        id: 5,
        text: '<p>Which of the following is a NoSQL database?</p>',
        type: 'multiple_choice',
        difficulty: 'Medium',
        marks: 1,
        chapter: 'Chapter 1: Intro to DB',
        topic: 'NoSQL',
        status: 'Draft',
        explanation: 'MongoDB is a popular NoSQL database.',
      },
      {
        id: 6,
        text: '<p>Match the normal forms to their definitions.</p>',
        type: 'matching',
        difficulty: 'Hard',
        marks: 3,
        chapter: 'Chapter 4: Normalization',
        topic: 'Overview',
        status: 'Active',
        explanation: '1NF: Atomic, 2NF: No partial dependency, 3NF: No transitive dependency.',
      }
    ]
  }
}


// ===================== MODALS STATE =====================

// --- Edit Bank Modal ---
const showEditBankModal = ref(false)
const editBankTitle = ref('')
const editBankDescription = ref('')
const editBankSaving = ref(false)

const openEditBankModal = () => {
  editBankTitle.value = bank.value.title
  editBankDescription.value = bank.value.description || ''
  showEditBankModal.value = true
}

const saveEditBank = async () => {
  editBankSaving.value = true
  try {
    await qbStore.updateQuestionBank(bank.value.id, {
      title: editBankTitle.value,
      description: editBankDescription.value,
    })
    bank.value.title = editBankTitle.value
    bank.value.description = editBankDescription.value
    showEditBankModal.value = false
  } catch (err) {
    console.error('Failed to update bank', err)
  } finally {
    editBankSaving.value = false
  }
}

// --- Delete Bank Modal ---
const showDeleteBankModal = ref(false)
const deleteBankProcessing = ref(false)

const confirmDeleteBank = async () => {
  deleteBankProcessing.value = true
  try {
    await qbStore.deleteQuestionBank(bank.value.id)
    router.push('/instructor/question-banks')
  } catch (err) {
    console.error('Failed to delete bank', err)
  } finally {
    deleteBankProcessing.value = false
  }
}

// --- View Question Modal ---
const showViewQuestionModal = ref(false)
const viewingQuestion = ref<any>(null)

const getOptionLabel = (index: any) => {
  return String.fromCharCode(65 + Number(index))
}

const isOptionCorrect = (opt: any, index: any) => {
  if (!viewingQuestion.value) return false
  const correct = viewingQuestion.value.correct_answer
  return correct === getOptionLabel(index) || correct === opt
}

const openViewQuestion = (q: any) => {
  router.push(`/instructor/question-banks/${route.params.id}/question/${q.id}`)
}

const formatQuestionText = (html: string) => {
  if (!html) return ''
  const tempDiv = document.createElement('div')
  tempDiv.innerHTML = html
  const hasImage = tempDiv.querySelector('img') !== null
  const textContent = (tempDiv.textContent || tempDiv.innerText || '').trim()
  const truncatedText = textContent.length > 80 ? textContent.substring(0, 80) + '...' : textContent
  
  if (hasImage) {
    return truncatedText ? `🖼️ [Image Question] ${truncatedText}` : `🖼️ [Image Question]`
  }
  return truncatedText || '(Empty Question)'
}

// --- Edit Question (routes to CreateQuestion page) ---
const editQuestion = (q: any) => {
  router.push(`/instructor/question-banks/${route.params.id}/edit-question/${q.id}`)
}

// --- Delete Question Modal ---
const showDeleteQuestionModal = ref(false)
const questionToDelete = ref<any>(null)
const deleteQuestionProcessing = ref(false)

const openDeleteQuestion = (q: any) => {
  questionToDelete.value = q
  showDeleteQuestionModal.value = true
}

const confirmDeleteQuestion = async () => {
  if (!questionToDelete.value) return
  deleteQuestionProcessing.value = true
  try {
    await qbStore.deleteQuestion(questionToDelete.value.id)
    questions.value = questions.value.filter(q => q.id !== questionToDelete.value.id)
    stats.value.total--
    const type = questionToDelete.value.type
    if (type === 'multiple_choice') stats.value.mcq--
    else if (type === 'short_answer') stats.value.sa--
    else if (type === 'essay') stats.value.essay--
    else if (type === 'true_false') stats.value.tf--
    showDeleteQuestionModal.value = false
    questionToDelete.value = null
  } catch (err) {
    console.error('Failed to delete question', err)
  } finally {
    deleteQuestionProcessing.value = false
  }
}

// --- Add Category/Topic Modal ---
const showAddCategoryModal = ref(false)
const newCategoryName = ref('')

const addCategory = () => {
  if (!newCategoryName.value.trim()) return
  const name = newCategoryName.value.trim()
  if (!manualCategories.value.some(c => c.name === name) && !categories.value.some(c => c.name === name)) {
    manualCategories.value.push({ name, count: 0 })
  }
  newCategoryName.value = ''
  showAddCategoryModal.value = false
}

// --- Reorder Questions ---
const reorderMode = ref(false)

const moveQuestion = (index: number, direction: 'up' | 'down') => {
  const arr = questions.value
  const newIndex = direction === 'up' ? index - 1 : index + 1
  if (newIndex < 0 || newIndex >= arr.length) return
  const temp = arr[index]
  arr[index] = arr[newIndex]
  arr[newIndex] = temp
  questions.value = [...arr]
}

// --- Export Questions ---
const showExportDropdown = ref(false)

// Close dropdown when clicking outside
const closeExportDropdown = (e: MouseEvent) => {
  const target = e.target as HTMLElement
  if (!target.closest('.export-dropdown-container')) {
    showExportDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeExportDropdown)
})

onUnmounted(() => {
  document.removeEventListener('click', closeExportDropdown)
})

const exportAsCSV = () => {
  if (!questions.value.length) return alert('No questions to export.')
  
  const headers = ['ID', 'Type', 'Difficulty', 'Marks', 'Chapter', 'Topic', 'Question', 'Correct Answer']
  const rows = questions.value.map(q => {
    // Strip HTML from question text
    const tempDiv = document.createElement('div')
    tempDiv.innerHTML = q.text || ''
    const cleanText = tempDiv.textContent || tempDiv.innerText || ''
    
    return [
      q.id,
      q.type,
      q.difficulty,
      q.marks,
      q.chapter || '',
      q.topic || '',
      `"${cleanText.replace(/"/g, '""')}"`, // escape quotes for CSV
      `"${(q.correct_answer || '').replace(/"/g, '""')}"`
    ].join(',')
  })
  
  const csvContent = [headers.join(','), ...rows].join('\n')
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.setAttribute('href', url)
  link.setAttribute('download', `Question_Bank_${bank.value.title.replace(/\s+/g, '_')}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  showExportDropdown.value = false
}

const generateDocumentHTML = () => {
  let html = `
    <html>
      <head>
        <meta charset="utf-8">
        <title>${bank.value?.title || 'Question Bank'}</title>
        <style>
          body { font-family: Arial, sans-serif; padding: 20px; line-height: 1.6; max-width: 800px; margin: 0 auto; }
          h1 { color: #333; text-align: center; margin-bottom: 5px; }
          .subtitle { text-align: center; color: #666; margin-bottom: 30px; font-size: 14px; }
          .question-block { margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #eee; page-break-inside: avoid; }
          .meta { font-size: 12px; color: #666; margin-bottom: 8px; font-weight: bold; }
          .text { font-size: 15px; margin-bottom: 12px; }
          .options { margin-left: 20px; }
          .option-row { margin-bottom: 4px; }
          .correct { color: #059669; font-weight: bold; }
          hr { border: 0; border-top: 1px solid #ccc; margin-bottom: 30px; }
        </style>
      </head>
      <body>
        <h1>${bank.value?.title || 'Question Bank'}</h1>
        <div class="subtitle"><strong>Course:</strong> ${bank.value?.course_name || ''}</div>
        <hr>
  `
  
  questions.value.forEach((q, i) => {
    html += `<div class="question-block">
      <div class="meta">Q${i + 1} | ${q.marks} Marks | ${q.difficulty}</div>
      <div class="text">${q.text}</div>
    `
    if (q.options && q.options.length > 0) {
      html += `<div class="options">`
      q.options.forEach((opt: any, optIdx: number) => {
        const letter = String.fromCharCode(65 + optIdx)
        const isCorrect = q.correct_answer === letter || q.correct_answer === opt
        html += `<div class="option-row ${isCorrect ? 'correct' : ''}">${letter}) ${opt} ${isCorrect ? ' ✓' : ''}</div>`
      })
      html += `</div>`
    } else if (q.correct_answer) {
      html += `<div class="correct" style="margin-top: 8px;">Answer: ${q.correct_answer}</div>`
    }
    html += `</div>`
  })
  
  html += `</body></html>`
  return html
}

const exportAsWord = () => {
  if (!questions.value.length) return alert('No questions to export.')
  const html = generateDocumentHTML()
  const blobHtml = `
    <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
    <head><meta charset='utf-8'><title>Export</title></head><body>${html}</body></html>
  `
  const blob = new Blob([blobHtml], { type: 'application/msword' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `Question_Bank_${bank.value.title.replace(/\s+/g, '_')}.doc`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  showExportDropdown.value = false
}

const exportAsPDF = () => {
  if (!questions.value.length) return alert('No questions to export.')
  const html = generateDocumentHTML()
  
  const printWindow = window.open('', '_blank')
  if (printWindow) {
    printWindow.document.write(html)
    printWindow.document.close()
    printWindow.focus()
    setTimeout(() => {
      printWindow.print()
      // Note: We don't close the window automatically in case the user cancels the print dialog
    }, 500)
  }
  showExportDropdown.value = false
}

// --- Import Questions ---
const fileInput = ref<HTMLInputElement | null>(null)
const isImporting = ref(false)
const triggerImport = () => {
  if (fileInput.value) {
    fileInput.value.click()
  }
}
const handleImport = async (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    const file = target.files[0]
    const formData = new FormData()
    formData.append('file', file)
    
    isImporting.value = true
    try {
      const response = await qbStore.importQuestions(bank.value.id, formData)
      alert(response.message || 'Successfully imported questions')
      await fetchBankDetails()
    } catch (err: any) {
      alert('Failed to import questions. Please check the file format.')
      console.error(err)
    } finally {
      isImporting.value = false
      target.value = '' // reset
    }
  }
}

// --- Preview Bank Modal ---
const showPreviewBankModal = ref(false)
const openPreviewBank = () => {
  showPreviewBankModal.value = true
}

onMounted(() => {
  fetchBankDetails()
})
</script>

<template>
  <div class="max-w-[1600px] mx-auto space-y-6">

    <!-- Loading State -->
    <div v-if="isLoading" class="flex items-center justify-center py-20">
      <div class="flex flex-col items-center gap-3">
        <svg class="animate-spin w-8 h-8 text-[#5138ed]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        <span class="text-[13px] text-slate-500 font-medium">Loading question bank...</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-rose-50 border border-rose-200 rounded-2xl p-6 text-center">
      <p class="text-rose-600 font-medium text-[14px]">{{ error }}</p>
      <button @click="fetchBankDetails" class="mt-3 px-4 py-2 bg-rose-100 hover:bg-rose-200 text-rose-700 rounded-xl text-[13px] font-bold transition-colors">Retry</button>
    </div>

    <template v-else>
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex items-center flex-wrap text-[13px] text-slate-500 font-medium">
        <router-link to="/instructor/question-banks" class="hover:text-[#5138ed] transition-colors">Question Banks</router-link>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span class="hover:text-[#5138ed] cursor-pointer transition-colors">{{ bank.title }}</span>
        <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span class="text-[#5138ed] font-bold">Question Bank Details</span>
      </div>
      
      <div class="flex items-center gap-3">
        <button @click="openEditBankModal" class="bg-white border border-slate-200 hover:border-slate-300 text-slate-700 px-4 py-2.5 rounded-xl font-bold text-[13px] transition-colors flex items-center gap-2">
          <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
          Edit Question Bank
        </button>
        <button @click="router.push(`/instructor/question-banks/${route.params.id}/create-question`)" class="bg-[#5138ed] hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold text-[13px] shadow-sm transition-colors flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Create Question
        </button>
      </div>
    </div>

    <!-- Header Card -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm flex flex-col xl:flex-row gap-8">
      <!-- Left Info -->
      <div class="flex-1 space-y-6">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-indigo-50 text-[#5138ed] flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
          </div>
          <div>
            <div class="flex items-center gap-3">
              <h1 class="text-xl font-bold text-slate-800">{{ bank.title }}</h1>
              <span class="px-2.5 py-1 text-[11px] font-bold bg-emerald-50 text-emerald-600 rounded-md">Active</span>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div>
            <div class="text-[11px] font-semibold text-slate-400 mb-0.5">Course</div>
            <div class="text-[13px] font-medium text-slate-700">{{ bank.course_name }}</div>
          </div>
          <div>
            <div class="text-[11px] font-semibold text-slate-400 mb-0.5">Course Code</div>
            <div class="text-[13px] font-medium text-slate-700">{{ bank.course_code }}</div>
          </div>
          <div>
            <div class="text-[11px] font-semibold text-slate-400 mb-0.5">Status</div>
            <div class="text-[13px] font-medium text-emerald-600">{{ bank.status }}</div>
          </div>
          <div>
            <div class="text-[11px] font-semibold text-slate-400 mb-0.5">Total Questions</div>
            <div class="text-[13px] font-medium text-slate-700">{{ stats.total }}</div>
          </div>
        </div>

        <div>
          <div class="text-[11px] font-semibold text-slate-400 mb-1">Description</div>
          <p class="text-[13px] text-slate-600 leading-relaxed">{{ bank.description }}</p>
        </div>
      </div>

      <!-- Right Stats -->
      <div class="xl:w-[400px] grid grid-cols-2 gap-4">
        <!-- Total -->
        <div class="col-span-2 md:col-span-1 xl:col-span-2 border border-slate-100 rounded-xl p-4 flex items-center gap-4">
          <div class="w-10 h-10 rounded-lg bg-indigo-50 text-[#5138ed] flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
          </div>
          <div>
            <div class="text-[11px] font-semibold text-slate-400">Total Questions</div>
            <div class="text-xl font-extrabold text-slate-800">{{ stats.total }}</div>
          </div>
        </div>
        <!-- MCQ -->
        <div class="border border-slate-100 rounded-xl p-4 flex items-center gap-4">
          <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <div>
            <div class="text-[11px] font-semibold text-slate-400">MCQ Questions</div>
            <div class="text-[15px] font-bold text-slate-800">{{ stats.mcq }} <span class="text-[11px] text-slate-400 font-medium">({{ pct(stats.mcq) }})</span></div>
          </div>
        </div>
        <!-- Short Answer -->
        <div class="border border-slate-100 rounded-xl p-4 flex items-center gap-4">
          <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
          </div>
          <div>
            <div class="text-[11px] font-semibold text-slate-400">Short Answer</div>
            <div class="text-[15px] font-bold text-slate-800">{{ stats.sa }} <span class="text-[11px] text-slate-400 font-medium">({{ pct(stats.sa) }})</span></div>
          </div>
        </div>
        <!-- Essay -->
        <div class="border border-slate-100 rounded-xl p-4 flex items-center gap-4">
          <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          </div>
          <div>
            <div class="text-[11px] font-semibold text-slate-400">Essay Questions</div>
            <div class="text-[15px] font-bold text-slate-800">{{ stats.essay }} <span class="text-[11px] text-slate-400 font-medium">({{ pct(stats.essay) }})</span></div>
          </div>
        </div>
      </div>
    </div>

    <div class="flex flex-col xl:flex-row gap-6">
      
      <!-- Main Content Area -->
      <div class="flex-1 space-y-6 min-w-0">
        
        <!-- Controls Bar / Filter Bar -->
        <div class="flex flex-col xl:flex-row xl:items-center gap-4">
          <!-- Search -->
          <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" v-model="searchQuery" placeholder="Search questions..." class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-[13px] focus:outline-none focus:border-[#5138ed]">
          </div>
          
          <!-- Dropdowns -->
          <div class="flex items-center flex-wrap gap-3">
            <select v-model="selectedType" class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] font-medium text-slate-700 focus:outline-none focus:border-[#5138ed] outline-none cursor-pointer pr-8">
              <option value="All Types">All Types</option>
              <option value="MCQ">MCQ</option>
              <option value="Short Answer">Short Answer</option>
              <option value="Essay">Essay</option>
              <option value="True/False">True/False</option>
              <option value="Matching">Matching</option>
              <option value="Fill in Blank">Fill in Blank</option>
              <option value="Ordering">Ordering</option>
            </select>
            
            <select v-model="selectedDifficulty" class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] font-medium text-slate-700 focus:outline-none focus:border-[#5138ed] outline-none cursor-pointer pr-8">
              <option value="All Difficulties">All Difficulties</option>
              <option value="Easy">Easy</option>
              <option value="Medium">Medium</option>
              <option value="Hard">Hard</option>
            </select>
            
            <select v-model="selectedStatus" class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] font-medium text-slate-700 focus:outline-none focus:border-[#5138ed] outline-none cursor-pointer pr-8">
              <option value="All Statuses">All Statuses</option>
              <option value="Active">Active</option>
              <option value="Draft">Draft</option>
            </select>

            <select v-model="selectedChapter" class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] font-medium text-slate-700 focus:outline-none focus:border-[#5138ed] outline-none cursor-pointer pr-8">
              <option v-for="ch in availableChapters" :key="ch" :value="ch">{{ ch }}</option>
            </select>

            <button class="bg-white border border-slate-200 text-[#5138ed] px-4 py-2.5 rounded-xl font-bold text-[13px] hover:bg-slate-50 transition-colors flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
              Filter
            </button>
          </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
          
          <!-- Questions Table -->
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
              <thead>
                <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-800 uppercase tracking-wide bg-slate-50/50">
                  <th class="py-3 pl-6 pr-4">#</th>
                  <th class="py-3 px-4 w-[40%]">Question Title</th>
                  <th class="py-3 px-4 text-center">Type</th>
                  <th class="py-3 px-4 text-center">Difficulty</th>
                  <th class="py-3 px-4 text-center">Marks</th>
                  <th class="py-3 px-4 text-center">Chapter</th>
                  <th class="py-3 px-4 text-center">Status</th>
                  <th class="py-3 px-4 text-center">Created Date</th>
                  <th class="py-3 px-6 text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="filteredQuestions.length === 0">
                  <td colspan="9" class="py-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                      <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                      <p class="text-[13px] text-slate-400 font-medium">No questions yet. Click "Add Question" to get started!</p>
                    </div>
                  </td>
                </tr>
                <tr v-for="(q, idx) in filteredQuestions" :key="q.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors last:border-0 group">
                  <td class="py-4 pl-6 pr-4 text-[13px] font-bold text-slate-500">
                    <div v-if="reorderMode" class="flex flex-col items-center gap-0.5">
                      <button @click="moveQuestion(idx, 'up')" :disabled="idx === 0" class="p-0.5 rounded hover:bg-indigo-50 text-slate-400 hover:text-[#5138ed] disabled:opacity-30 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                      </button>
                      <span>{{ idx + 1 }}</span>
                      <button @click="moveQuestion(idx, 'down')" :disabled="idx === filteredQuestions.length - 1" class="p-0.5 rounded hover:bg-indigo-50 text-slate-400 hover:text-[#5138ed] disabled:opacity-30 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                      </button>
                    </div>
                    <span v-else>{{ idx + 1 }}</span>
                  </td>
                  <td class="py-4 px-4 text-[13px] text-slate-800 font-bold whitespace-normal">
                    {{ formatQuestionText(q.text) }}
                    <div class="text-[11px] text-slate-500 font-normal mt-0.5 truncate max-w-sm">{{ q.explanation || 'No description provided.' }}</div>
                  </td>
                  <td class="py-4 px-4 text-center">
                    <span 
                      class="px-2 py-0.5 text-[10px] font-bold rounded"
                      :class="{
                        'text-[#5138ed] bg-indigo-50': typeLabel(q.type) === 'MCQ',
                        'text-emerald-500 bg-emerald-50': typeLabel(q.type) === 'Short Answer',
                        'text-amber-500 bg-amber-50': typeLabel(q.type) === 'Essay',
                        'text-sky-500 bg-sky-50': typeLabel(q.type) === 'True/False',
                        'text-fuchsia-500 bg-fuchsia-50': typeLabel(q.type) === 'Matching',
                        'text-rose-500 bg-rose-50': typeLabel(q.type) === 'Fill in Blank',
                        'text-orange-500 bg-orange-50': typeLabel(q.type) === 'Ordering',
                      }"
                    >
                      {{ typeLabel(q.type) }}
                    </span>
                  </td>
                  <td class="py-4 px-4 text-center">
                    <span 
                      class="px-2 py-0.5 text-[10px] font-bold rounded"
                      :class="{
                        'text-emerald-500': q.difficulty === 'Easy',
                        'text-amber-500': q.difficulty === 'Medium',
                        'text-rose-500': q.difficulty === 'Hard'
                      }"
                    >
                      {{ q.difficulty }}
                    </span>
                  </td>
                  <td class="py-4 px-4 text-[13px] font-bold text-slate-700 text-center">{{ q.marks }}</td>
                  <td class="py-4 px-4 text-[12px] text-slate-600 font-medium text-center">{{ q.chapter || '—' }}</td>
                  <td class="py-4 px-4 text-center">
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-500">{{ q.status }}</span>
                  </td>
                  <td class="py-4 px-4 text-center text-[12px] text-slate-500 font-medium">May 24, 2026</td>
                  <td class="py-4 px-6 text-center">
                    <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                      <button @click="openViewQuestion(q)" class="p-1.5 text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 rounded-lg transition-colors" title="View Question">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                      </button>
                      <button @click="editQuestion(q)" class="p-1.5 text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 rounded-lg transition-colors" title="Edit Question">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                      </button>
                      <button @click="openDeleteQuestion(q)" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Question">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Table Footer -->
          <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-[12px] font-medium text-slate-500 bg-slate-50/50">
            <span>Showing {{ filteredQuestions.length }} of {{ stats.total }} questions</span>
            <div class="flex items-center gap-1">
              <button class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 hover:bg-white transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
              <button class="w-7 h-7 flex items-center justify-center rounded bg-[#5138ed] text-white font-bold shadow-sm">1</button>
              <button class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 hover:bg-white transition-colors">2</button>
              <button class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 hover:bg-white transition-colors">3</button>
              <button class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 hover:bg-white transition-colors">4</button>
              <span class="px-1 text-slate-400">...</span>
              <button class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 hover:bg-white transition-colors">13</button>
              <button class="w-7 h-7 flex items-center justify-center rounded border border-slate-200 hover:bg-white transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
            </div>
          </div>
          
        </div>
      </div>
      
      <!-- Right Sidebar -->
      <div class="w-full xl:w-[320px] space-y-6">
        
        <!-- Question Bank Summary -->
        <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
          <h3 class="text-[14px] font-bold text-slate-800 mb-6">Question Bank Summary</h3>
          
          <div class="space-y-5">
            <div>
              <div class="text-[11px] font-semibold text-slate-400 mb-1">Question Bank Name</div>
              <div class="text-[13px] font-bold text-slate-800">{{ bank.title }}</div>
            </div>
            
            <div>
              <div class="text-[11px] font-semibold text-slate-400 mb-1">Description</div>
              <p class="text-[12px] text-slate-600 leading-relaxed">{{ bank.description || 'No description provided.' }}</p>
            </div>
            
            <div>
              <div class="text-[11px] font-semibold text-slate-400 mb-1">Total Questions</div>
              <div class="text-[14px] font-bold text-slate-800">{{ stats.total }}</div>
            </div>
            
            <div>
              <div class="text-[11px] font-semibold text-slate-400 mb-2">Question Types</div>
              <div class="space-y-2.5">
                <div class="flex items-center justify-between text-[12px]">
                  <div class="flex items-center gap-2 text-slate-600 font-medium">
                    <div class="w-1.5 h-1.5 rounded-full bg-[#5138ed]"></div>
                    MCQ Questions
                  </div>
                  <span class="text-slate-500">{{ stats.mcq }} <span class="text-slate-400">({{ pct(stats.mcq) }})</span></span>
                </div>
                <div class="flex items-center justify-between text-[12px]">
                  <div class="flex items-center gap-2 text-slate-600 font-medium">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                    Short Answer
                  </div>
                  <span class="text-slate-500">{{ stats.sa }} <span class="text-slate-400">({{ pct(stats.sa) }})</span></span>
                </div>
                <div class="flex items-center justify-between text-[12px]">
                  <div class="flex items-center gap-2 text-slate-600 font-medium">
                    <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div>
                    Essay Questions
                  </div>
                  <span class="text-slate-500">{{ stats.essay }} <span class="text-slate-400">({{ pct(stats.essay) }})</span></span>
                </div>
                <div class="flex items-center justify-between text-[12px]">
                  <div class="flex items-center gap-2 text-slate-600 font-medium">
                    <div class="w-1.5 h-1.5 rounded-full bg-rose-500"></div>
                    Other Types
                  </div>
                  <span class="text-slate-500">{{ stats.tf }} <span class="text-slate-400">({{ pct(stats.tf) }})</span></span>
                </div>
              </div>
            </div>

            <div class="pt-2">
              <div class="text-[11px] font-semibold text-slate-400 mb-1">Created By</div>
              <div class="text-[13px] font-medium text-slate-700">Dr. Abebe Kebede</div>
            </div>
            
            <div>
              <div class="text-[11px] font-semibold text-slate-400 mb-1">Created Date</div>
              <div class="flex items-center gap-2 text-[13px] font-medium text-slate-700">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                May 24, 2026
              </div>
            </div>

            <div>
              <div class="text-[11px] font-semibold text-slate-400 mb-1">Last Updated</div>
              <div class="flex items-center gap-2 text-[13px] font-medium text-slate-700">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                May 30, 2026
              </div>
            </div>

            <div>
              <div class="text-[11px] font-semibold text-slate-400 mb-1">Status</div>
              <span class="px-2.5 py-1 text-[11px] font-bold bg-emerald-50 text-emerald-600 rounded-md">{{ bank.status }}</span>
            </div>
            
          </div>
        </div>
      </div>
    </div>
    </template>

    <!-- =================== MODALS =================== -->

    <!-- Edit Bank Modal -->
    <Teleport to="body">
      <div v-if="showEditBankModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showEditBankModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5 animate-in">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Edit Bank Information</h3>
            <button @click="showEditBankModal = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <div>
            <label class="block text-[12px] font-bold text-slate-700 mb-1">Title <span class="text-rose-500">*</span></label>
            <input v-model="editBankTitle" type="text" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
          </div>
          <div>
            <label class="block text-[12px] font-bold text-slate-700 mb-1">Description</label>
            <textarea v-model="editBankDescription" rows="3" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] resize-none"></textarea>
          </div>
          <div class="flex justify-end gap-3 pt-2">
            <button @click="showEditBankModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-[13px] font-bold text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="saveEditBank" :disabled="editBankSaving" class="px-5 py-2 bg-[#5138ed] hover:bg-indigo-600 text-white rounded-xl text-[13px] font-bold shadow-sm transition-colors disabled:opacity-50">
              {{ editBankSaving ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Delete Bank Confirmation Modal -->
    <Teleport to="body">
      <div v-if="showDeleteBankModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showDeleteBankModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-4 animate-in">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            <div>
              <h3 class="text-[15px] font-bold text-slate-800">Delete Question Bank</h3>
              <p class="text-[12px] text-slate-500 mt-0.5">This action cannot be undone.</p>
            </div>
          </div>
          <p class="text-[13px] text-slate-600 leading-relaxed">
            Are you sure you want to delete <strong>"{{ bank.title }}"</strong>? All <strong>{{ stats.total }}</strong> questions inside will be permanently removed.
          </p>
          <div class="flex justify-end gap-3 pt-2">
            <button @click="showDeleteBankModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-[13px] font-bold text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="confirmDeleteBank" :disabled="deleteBankProcessing" class="px-5 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-[13px] font-bold shadow-sm transition-colors disabled:opacity-50">
              {{ deleteBankProcessing ? 'Deleting...' : 'Delete Bank' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- View Question Modal -->
    <Teleport to="body">
      <div v-if="showViewQuestionModal && viewingQuestion" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showViewQuestionModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 space-y-5 animate-in max-h-[85vh] overflow-y-auto">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Question Details</h3>
            <button @click="showViewQuestionModal = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <div class="space-y-4">
            <div class="flex flex-wrap gap-2">
              <span class="px-2.5 py-1 text-[11px] font-bold bg-indigo-50 text-[#5138ed] rounded-lg">{{ typeLabel(viewingQuestion.type) }}</span>
              <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg" :class="{
                'bg-emerald-50 text-emerald-600': viewingQuestion.difficulty === 'Easy',
                'bg-amber-50 text-amber-600': viewingQuestion.difficulty === 'Medium',
                'bg-rose-50 text-rose-600': viewingQuestion.difficulty === 'Hard'
              }">{{ viewingQuestion.difficulty }}</span>
              <span class="px-2.5 py-1 text-[11px] font-bold bg-slate-100 text-slate-600 rounded-lg">{{ viewingQuestion.marks }} Marks</span>
            </div>
            <div>
              <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Question Text</label>
              <p class="text-[14px] text-slate-800 font-medium mt-1 leading-relaxed" v-html="viewingQuestion.text"></p>
            </div>
            <div v-if="viewingQuestion.chapter || viewingQuestion.topic" class="grid grid-cols-2 gap-4">
              <div v-if="viewingQuestion.chapter">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Chapter</label>
                <p class="text-[13px] text-slate-700 font-medium mt-0.5">{{ viewingQuestion.chapter }}</p>
              </div>
              <div v-if="viewingQuestion.topic">
                <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Topic</label>
                <p class="text-[13px] text-slate-700 font-medium mt-0.5">{{ viewingQuestion.topic }}</p>
              </div>
            </div>
            <div v-if="viewingQuestion.options && viewingQuestion.options.length" class="space-y-2">
              <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Options</label>
              <div v-for="(opt, i) in viewingQuestion.options" :key="i" class="flex items-center gap-3 p-3 rounded-xl border" :class="isOptionCorrect(opt, i) ? 'border-emerald-200 bg-emerald-50' : 'border-slate-100 bg-slate-50/50'">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold" :class="isOptionCorrect(opt, i) ? 'bg-emerald-500 text-white' : 'bg-white text-slate-400 border border-slate-200'">{{ getOptionLabel(i) }}</div>
                <span class="text-[13px] font-medium" :class="isOptionCorrect(opt, i) ? 'text-emerald-700' : 'text-slate-600'">{{ typeof opt === 'string' ? opt : opt.text }}</span>
              </div>
            </div>
            <div v-else>
              <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Correct Answer</label>
              <p class="text-[13px] text-emerald-600 font-bold mt-1 py-2 px-3 bg-emerald-50 rounded-lg inline-block border border-emerald-100">{{ viewingQuestion.correct_answer || 'N/A' }}</p>
            </div>
            <div v-if="viewingQuestion.tags">
              <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wide">Tags</label>
              <div class="flex flex-wrap gap-1.5 mt-1">
                <span v-for="tag in viewingQuestion.tags.split(',')" :key="tag" class="px-2 py-0.5 text-[11px] font-semibold bg-slate-100 text-slate-500 rounded-md">{{ tag.trim() }}</span>
              </div>
            </div>
          </div>
          <div class="flex justify-end gap-3 pt-2">
            <button @click="editQuestion(viewingQuestion); showViewQuestionModal = false" class="px-4 py-2 bg-[#5138ed] hover:bg-indigo-600 text-white rounded-xl text-[13px] font-bold transition-colors">Edit Question</button>
            <button @click="showViewQuestionModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-[13px] font-bold text-slate-600 hover:bg-slate-50 transition-colors">Close</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Delete Question Confirmation Modal -->
    <Teleport to="body">
      <div v-if="showDeleteQuestionModal && questionToDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showDeleteQuestionModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-4 animate-in">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            <div>
              <h3 class="text-[15px] font-bold text-slate-800">Delete Question</h3>
              <p class="text-[12px] text-slate-500 mt-0.5">This action cannot be undone.</p>
            </div>
          </div>
          <p class="text-[13px] text-slate-600 leading-relaxed">
            Are you sure you want to delete this question?
          </p>
          <div class="p-3 bg-slate-50 rounded-xl text-[12px] text-slate-700 font-medium border border-slate-100" v-html="questionToDelete.text"></div>
          <div class="flex justify-end gap-3 pt-2">
            <button @click="showDeleteQuestionModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-[13px] font-bold text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="confirmDeleteQuestion" :disabled="deleteQuestionProcessing" class="px-5 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-[13px] font-bold shadow-sm transition-colors disabled:opacity-50">
              {{ deleteQuestionProcessing ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Add Category/Topic Modal -->
    <Teleport to="body">
      <div v-if="showAddCategoryModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showAddCategoryModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-5 animate-in">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Add Category / Topic</h3>
            <button @click="showAddCategoryModal = false" class="p-1 text-slate-400 hover:text-slate-600 rounded-lg transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          <div>
            <label class="block text-[12px] font-bold text-slate-700 mb-1">Category Name <span class="text-rose-500">*</span></label>
            <input v-model="newCategoryName" type="text" placeholder="e.g. SQL Basics, Data Structures" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-[13px] focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed]">
          </div>
          <div class="flex justify-end gap-3 pt-2">
            <button @click="showAddCategoryModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-[13px] font-bold text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
            <button @click="addCategory" :disabled="!newCategoryName.trim()" class="px-5 py-2 bg-[#5138ed] hover:bg-indigo-600 text-white rounded-xl text-[13px] font-bold shadow-sm transition-colors disabled:opacity-50">Add Category</button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Preview Bank Modal -->
    <Teleport to="body">
      <div v-if="showPreviewBankModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showPreviewBankModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl p-0 flex flex-col animate-in max-h-[90vh] overflow-hidden">
          <!-- Header -->
          <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div>
              <h2 class="text-xl font-bold text-slate-800">{{ bank.title }}</h2>
              <p class="text-[13px] text-slate-500 mt-1 font-medium">{{ stats.total }} Questions Total</p>
            </div>
            <button @click="showPreviewBankModal = false" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-xl transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
          </div>
          
          <!-- Content -->
          <div class="flex-1 overflow-y-auto p-6 space-y-8 bg-slate-50/50">
            <div v-if="questions.length === 0" class="text-center py-10">
              <p class="text-slate-500 font-medium">This question bank is empty.</p>
            </div>
            
            <div v-for="(q, index) in questions" :key="q.id" class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
              <div class="flex items-start gap-4">
                <div class="w-8 h-8 rounded-full bg-indigo-50 text-[#5138ed] font-bold text-[13px] flex items-center justify-center shrink-0">
                  {{ index + 1 }}
                </div>
                <div class="flex-1 min-w-0">
                  <!-- Meta tags -->
                  <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="px-2 py-0.5 text-[11px] font-bold bg-slate-100 text-slate-500 rounded">{{ typeLabel(q.type) }}</span>
                    <span class="px-2 py-0.5 text-[11px] font-bold bg-slate-100 text-slate-500 rounded">{{ q.marks }} Marks</span>
                    <span class="px-2 py-0.5 text-[11px] font-bold rounded" :class="{
                      'bg-emerald-50 text-emerald-600': q.difficulty === 'Easy',
                      'bg-amber-50 text-amber-600': q.difficulty === 'Medium',
                      'bg-rose-50 text-rose-600': q.difficulty === 'Hard'
                    }">{{ q.difficulty }}</span>
                  </div>
                  
                  <!-- Question Text -->
                  <div class="text-[14px] text-slate-800 font-medium leading-relaxed prose prose-sm max-w-none" v-html="q.text"></div>
                  
                  <!-- Options -->
                  <div v-if="q.options && q.options.length" class="mt-5 space-y-2.5">
                    <div v-for="(opt, oIndex) in q.options" :key="oIndex" class="flex items-center gap-3">
                      <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center text-[10px] font-bold" :class="isOptionCorrect(opt, oIndex) ? 'border-emerald-500 bg-emerald-50 text-emerald-600' : 'border-slate-200 text-slate-400'">
                        {{ getOptionLabel(oIndex) }}
                      </div>
                      <span class="text-[13px]" :class="isOptionCorrect(opt, oIndex) ? 'font-bold text-emerald-700' : 'font-medium text-slate-600'">
                        {{ typeof opt === 'string' ? opt : opt.text }}
                      </span>
                    </div>
                  </div>
                  <div v-else class="mt-4">
                    <div class="inline-block px-3 py-2 bg-emerald-50 border border-emerald-100 rounded-lg">
                      <span class="text-[12px] font-bold text-emerald-600 tracking-wide uppercase mr-2">Answer:</span>
                      <span class="text-[13px] font-medium text-emerald-800">{{ q.correct_answer }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<style scoped>
.animate-in {
  animation: modalIn 0.2s ease-out;
}
@keyframes modalIn {
  from { opacity: 0; transform: scale(0.95) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
</style>
