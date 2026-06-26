<script setup lang="ts">
import { ref } from 'vue'

const tabs = ['All Exams', 'Upcoming', 'Active', 'Completed', 'Drafts', 'Archived']
const activeTab = ref('All Exams')
</script>

<template>
  <div class="border-b border-slate-200">
    <nav class="flex space-x-8 overflow-x-auto">
      <button
        v-for="tab in tabs"
        :key="tab"
        @click="activeTab = tab"
        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-[13px] transition-colors"
        :class="[
          activeTab === tab
            ? 'border-[#5138ed] text-[#5138ed] font-bold'
            : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'
        ]"
      >
        {{ tab }}
      </button>
    </nav>
  </div>
</template>
