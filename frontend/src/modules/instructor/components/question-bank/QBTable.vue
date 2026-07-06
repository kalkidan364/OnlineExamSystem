<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useInstructorQbStore } from '../../store/instructorQbStore'

const qbStore = useInstructorQbStore()
const router = useRouter()

const searchQuery = ref('')
const selectedType = ref('All Types')
const selectedStatus = ref('All Statuses')

// Format ISO date strings
const formatDate = (iso: string | null) => {
  if (!iso) return '—'
  return new Date(iso).toLocaleDateString('en-US', {
    month: 'short', day: '2-digit', year: 'numeric'
  })
}

const filteredBanks = computed(() => {
  let result = qbStore.banks

  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(b => b.title.toLowerCase().includes(q) || (b.description && b.description.toLowerCase().includes(q)))
  }

  if (selectedStatus.value !== 'All Statuses') {
    result = result.filter(b => b.status.toLowerCase() === selectedStatus.value.toLowerCase())
  }

  return result
})

const handleView = (bank: any) => {
  router.push(`/instructor/question-banks/${bank.id}`)
}

const handleEdit = (bank: any) => {
  alert(`Editing question bank: ${bank.title}\n(Edit modal coming soon)`)
}

const handleDelete = async (id: number) => {
  if (confirm("Are you sure you want to delete this question bank? This action cannot be undone.")) {
    await qbStore.deleteQuestionBank(id)
  }
}
</script>

<template>
  <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
    
    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 mb-6">
      <div class="relative flex-1">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <input 
          v-model="searchQuery"
          type="text" 
          class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] transition-colors"
          placeholder="Search question banks..."
        >
      </div>
      
      <div class="flex gap-3">
        <select v-model="selectedType" class="border border-slate-200 rounded-xl text-sm px-3 py-2 text-slate-600 focus:outline-none focus:border-[#5138ed]">
          <option>All Types</option>
          <option>MCQ</option>
          <option>Short Answer</option>
        </select>
        
        <select v-model="selectedStatus" class="border border-slate-200 rounded-xl text-sm px-3 py-2 text-slate-600 focus:outline-none focus:border-[#5138ed]">
          <option>All Statuses</option>
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
        
        <button class="flex items-center gap-2 px-4 py-2 border border-indigo-100 text-[#5138ed] text-sm font-semibold rounded-xl hover:bg-indigo-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
          Filter
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse whitespace-nowrap">
        <thead>
          <tr class="border-b border-slate-100 text-[12px] font-bold text-slate-400 uppercase tracking-wide">
            <th class="pb-4 pr-4 font-semibold">Question Bank</th>
            <th class="pb-4 px-4 font-semibold">Course</th>
            <th class="pb-4 px-4 text-center font-semibold">Questions</th>
            <th class="pb-4 px-4 font-semibold">Type Distribution</th>
            <th class="pb-4 px-4 font-semibold">Last Updated</th>
            <th class="pb-4 px-4 text-center font-semibold">Status</th>
            <th class="pb-4 pl-4 text-center font-semibold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <!-- Loading state -->
          <tr v-if="qbStore.isLoading">
            <td colspan="7" class="py-8 text-center text-slate-500">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 inline text-[#5138ed]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Loading question banks...
            </td>
          </tr>
          
          <!-- Empty State -->
          <tr v-else-if="filteredBanks.length === 0">
             <td colspan="7" class="py-8 text-center text-slate-500 font-medium">No question banks found.</td>
          </tr>

          <!-- Data Rows -->
          <tr v-else v-for="bank in filteredBanks" :key="bank.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors last:border-0 group">
            <td class="py-4 pr-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0 text-[#5138ed]">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                </div>
                <div class="flex flex-col">
                  <span class="text-[13px] font-bold text-slate-700">{{ bank.title }}</span>
                  <span class="text-[11px] text-slate-400">{{ bank.description || 'No description' }}</span>
                </div>
              </div>
            </td>
            <td class="py-4 px-4">
              <div class="flex flex-col">
                <span class="text-[13px] font-medium text-slate-600">{{ bank.course_name }}</span>
                <span class="text-[11px] text-slate-400">{{ bank.course_code }}</span>
              </div>
            </td>
            <td class="py-4 px-4 text-[13px] text-slate-600 font-bold text-center">{{ bank.total_questions }}</td>
            <td class="py-4 px-4">
              <div class="flex items-center gap-1.5">
                <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-50 text-blue-600 rounded">MCQ {{ bank.types.mcq }}</span>
                <span class="px-2 py-0.5 text-[10px] font-bold bg-purple-50 text-purple-600 rounded">SA {{ bank.types.sa }}</span>
                <span class="px-2 py-0.5 text-[10px] font-bold bg-orange-50 text-orange-600 rounded">Essay {{ bank.types.essay }}</span>
              </div>
            </td>
            <td class="py-4 px-4 text-[13px] text-slate-500 font-medium">{{ formatDate(bank.updated_at) }}</td>
            <td class="py-4 px-4 text-center">
              <span 
                class="px-2.5 py-1 text-[11px] font-bold rounded-md"
                :class="bank.status === 'Active' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'"
              >
                {{ bank.status }}
              </span>
            </td>
            <td class="py-4 pl-4 text-center">
              <div class="flex items-center justify-center gap-1">
                <button @click="handleView(bank)" class="p-1.5 text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 rounded-lg transition-colors" title="View Questions">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>
                <button @click="handleEdit(bank)" class="p-1.5 text-slate-400 hover:text-[#5138ed] hover:bg-indigo-50 rounded-lg transition-colors" title="Edit Bank">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
                <button @click="handleDelete(bank.id)" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Bank">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-100">
      <span class="text-[13px] text-slate-500 font-medium">Showing 1 to 8 of 12 question banks</span>
      <div class="flex items-center gap-1">
        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#5138ed] text-white font-bold text-sm shadow-sm shadow-indigo-200">1</button>
        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-sm transition-colors">2</button>
        <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
      </div>
    </div>
  </div>
</template>
