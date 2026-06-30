<script setup lang="ts">
const emit = defineEmits<{
  (e: 'action', actionKey: 'take-exam' | 'view-results' | 'download-results' | 'update-profile' | 'academic-calendar'): void
}>()

const actions = [
  {
    key: 'take-exam' as const,
    title: 'Take Exam',
    description: 'Unlock and begin your current active paper with live AI proctoring',
    color: 'bg-indigo-50 text-indigo-700 border-indigo-100 hover:border-indigo-300 hover:bg-indigo-100/50',
    iconRaw: '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>'
  },
  {
    key: 'view-results' as const,
    title: 'View Results',
    description: 'Read graded papers, points breakdown, and direct instructor feedback',
    color: 'bg-emerald-50 text-emerald-700 border-emerald-100 hover:border-emerald-300 hover:bg-emerald-100/50',
    iconRaw: '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>'
  },
  {
    key: 'download-results' as const,
    title: 'Print Transcript',
    description: 'Generate an official-looking Wollo University printable result slip',
    color: 'bg-blue-50 text-blue-700 border-blue-100 hover:border-blue-300 hover:bg-blue-100/50',
    iconRaw: '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
  },
  {
    key: 'update-profile' as const,
    title: 'Update Profile',
    description: 'Review your registered software engineering code and metadata details',
    color: 'bg-amber-50 text-amber-700 border-amber-100 hover:border-amber-300 hover:bg-amber-100/50',
    iconRaw: '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>'
  },
  {
    key: 'academic-calendar' as const,
    title: 'Academic Calendar',
    description: 'Check dates, lecture schedules, national holidays, and deadlines',
    color: 'bg-purple-50 text-purple-700 border-purple-100 hover:border-purple-300 hover:bg-purple-100/50',
    iconRaw: '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>'
  }
]
</script>

<template>
  <div class="space-y-4">
    <div>
      <h3 class="text-lg font-bold text-slate-900">Student Quick Actions</h3>
      <p class="text-xs text-slate-500 font-medium">Quick shortcuts to vital examination operations</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
      <button
        v-for="act in actions"
        :key="act.key"
        @click="emit('action', act.key)"
        :class="['flex flex-col items-start text-left p-5 rounded-xl border transition-all duration-200 active:scale-[0.98]', act.color]"
      >
        <div class="rounded-lg bg-white p-2.5 shadow-xs border border-inherit mb-4" v-html="act.iconRaw">
        </div>
        <h4 class="text-sm font-bold text-slate-800">{{ act.title }}</h4>
        <p class="mt-1.5 text-xs text-slate-500 leading-relaxed font-sans line-clamp-2">
          {{ act.description }}
        </p>
      </button>
    </div>
  </div>
</template>
