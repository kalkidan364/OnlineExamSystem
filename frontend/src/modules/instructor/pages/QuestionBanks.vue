<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useInstructorQbStore } from '../store/instructorQbStore'

import QBStatCards from '../components/question-bank/QBStatCards.vue'
import QBTable from '../components/question-bank/QBTable.vue'
import QBOverviewChart from '../components/question-bank/QBOverviewChart.vue'
import QBQuickActions from '../components/question-bank/QBQuickActions.vue'
import CreateBankModal from '../components/question-bank/CreateBankModal.vue'

const qbStore = useInstructorQbStore()
const showCreateModal = ref(false)

onMounted(() => {
  qbStore.fetchQuestionBanks()
})

const handleCreateBank = async (payload: { title: string, description: string }) => {
  await qbStore.createQuestionBank(payload)
}
</script>

<template>
  <div>
    <div class="max-w-[1400px] mx-auto flex flex-col xl:flex-row gap-6">
      
      <!-- Main Left Column -->
      <div class="flex-1 min-w-0 space-y-6">
        
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
          <div>
            <h1 class="text-2xl font-bold text-slate-800">Manage your question banks</h1>
            <p class="text-[14px] text-slate-500 mt-1">Create, organize and manage questions for your exams.</p>
          </div>
          <button @click="showCreateModal = true" class="flex items-center gap-2 bg-[#5138ed] hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition-colors w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create Question Bank
          </button>
        </div>

        <!-- Dev Banner: Mock Data Active -->
        <div
          v-if="qbStore.usingMockData"
          class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-sm font-medium flex items-center gap-2"
        >
          <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>
            <strong>Dev Mode:</strong> Backend is offline — showing mock data.
          </span>
        </div>

        <!-- Error Banner: Real API Error -->
        <div v-if="qbStore.error" class="bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3 text-sm font-medium flex items-center gap-2">
          <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          {{ qbStore.error }}
        </div>

        <!-- Stat Cards -->
        <QBStatCards />

        <!-- Main Table Area -->
        <QBTable />

      </div>

      <!-- Right Sidebar Column -->
      <div class="w-full xl:w-[320px] space-y-6">
        
        <!-- Overview Chart -->
        <QBOverviewChart :stats="qbStore.stats" />
        
        <!-- Quick Actions -->
        <QBQuickActions @create="showCreateModal = true" />

      </div>

    </div>

    <!-- Create Bank Modal -->
    <CreateBankModal 
      :show="showCreateModal" 
      @close="showCreateModal = false" 
      @submit="handleCreateBank" 
    />
  </div>
</template>
