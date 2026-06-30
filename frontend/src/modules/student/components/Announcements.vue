<script setup lang="ts">
import { ref } from 'vue'
import type { Announcement } from '../types'

const props = defineProps<{
  announcements: Announcement[]
}>()

const selectedAnn = ref<Announcement | null>(null)

const getCategoryStyles = (category: string) => {
  switch (category) {
    case 'Urgent':
      return {
        bg: 'bg-rose-50 text-rose-700 border-rose-200',
        dot: 'bg-rose-500',
        iconColor: 'text-rose-500'
      }
    case 'Schedule':
      return {
        bg: 'bg-indigo-50 text-indigo-700 border-indigo-200',
        dot: 'bg-indigo-500',
        iconColor: 'text-indigo-500'
      }
    case 'System':
      return {
        bg: 'bg-slate-50 text-slate-700 border-slate-200',
        dot: 'bg-slate-500',
        iconColor: 'text-slate-500'
      }
    default:
      return {
        bg: 'bg-amber-50 text-amber-700 border-amber-200',
        dot: 'bg-amber-500',
        iconColor: 'text-amber-500'
      }
  }
}
</script>

<template>
  <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs h-full flex flex-col justify-between">
    <div>
      <div class="flex items-center justify-between mb-5">
        <div>
          <h3 class="text-lg font-bold text-slate-900">Official Notices</h3>
          <p class="text-xs text-slate-500 font-medium">Senate and ICT Department Announcements</p>
        </div>
        <svg class="h-5 w-5 text-indigo-600 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
      </div>

      <div class="space-y-3">
        <div
          v-for="ann in announcements"
          :key="ann.id"
          @click="selectedAnn = ann"
          class="group flex flex-col justify-between gap-2 rounded-xl border border-slate-100 bg-slate-50/50 p-4 transition-all hover:bg-slate-50 hover:border-slate-300 cursor-pointer"
        >
          <div class="flex items-center justify-between">
            <span :class="['inline-flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider', getCategoryStyles(ann.category).bg]">
              <span :class="['h-1.5 w-1.5 rounded-full', getCategoryStyles(ann.category).dot]"></span>
              {{ ann.category }}
            </span>
            <span class="text-[10px] text-slate-400 font-medium">{{ ann.date }}</span>
          </div>

          <div class="mt-1">
            <h4 class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 transition-colors flex items-center gap-1">
              <span>{{ ann.title }}</span>
              <svg class="h-3.5 w-3.5 text-slate-300 group-hover:text-indigo-500 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </h4>
            <p class="mt-1 text-xs text-slate-500 line-clamp-2 leading-relaxed">
              {{ ann.content }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-5 text-center">
      <p class="text-[11px] font-semibold text-slate-400">All student alerts comply with Wollo Uni Senate regulations</p>
    </div>

    <!-- Announcement Detail Modal -->
    <div v-if="selectedAnn" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs">
      <div class="relative w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl animate-in fade-in zoom-in-95 duration-150">
        <button
          @click="selectedAnn = null"
          class="absolute top-4 right-4 rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="flex items-center gap-2 mb-3">
          <span :class="['inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-wider', getCategoryStyles(selectedAnn.category).bg]">
            <span :class="['h-1.5 w-1.5 rounded-full', getCategoryStyles(selectedAnn.category).dot]"></span>
            {{ selectedAnn.category }}
          </span>
          <span class="text-xs text-slate-400 font-medium">{{ selectedAnn.date }}</span>
        </div>

        <h3 class="text-lg font-black text-slate-900 pr-8">{{ selectedAnn.title }}</h3>
        
        <p class="mt-4 text-xs font-mono text-indigo-600 flex items-center gap-1">
          <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
          <span>Issued by: {{ selectedAnn.sender }}</span>
        </p>

        <hr class="my-4 border-slate-100" />

        <div class="rounded-xl bg-slate-50 p-4 border border-slate-100">
          <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-wrap font-sans">
            {{ selectedAnn.content }}
          </p>
        </div>

        <button
          @click="selectedAnn = null"
          class="mt-6 w-full rounded-xl bg-slate-900 py-2.5 text-xs font-bold text-white hover:bg-slate-800 transition-colors"
        >
          Close Announcement
        </button>
      </div>
    </div>
  </div>
</template>
