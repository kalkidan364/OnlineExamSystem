<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import type { ActiveExam } from '../types'

const props = defineProps<{
  exam: ActiveExam
}>()

const emit = defineEmits<{
  (e: 'start-exam'): void
}>()

const timeLeft = ref({ hours: 1, minutes: 31, seconds: 45 })
let timer: number | null = null

onMounted(() => {
  timer = window.setInterval(() => {
    if (timeLeft.value.seconds > 0) {
      timeLeft.value.seconds--
    } else if (timeLeft.value.minutes > 0) {
      timeLeft.value.minutes--
      timeLeft.value.seconds = 59
    } else if (timeLeft.value.hours > 0) {
      timeLeft.value.hours--
      timeLeft.value.minutes = 59
      timeLeft.value.seconds = 59
    } else {
      if (timer) clearInterval(timer)
    }
  }, 1000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})

const formatNumber = (num: number) => num.toString().padStart(2, '0')
</script>

<template>
  <div class="relative overflow-hidden rounded-2xl border-2 border-indigo-500 bg-white p-6 shadow-xl ring-8 ring-indigo-500/5 transition-all hover:shadow-2xl">
    <!-- Top Banner Accent -->
    <div class="absolute top-0 left-0 right-0 h-1.5 bg-linear-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

    <!-- Live Exam Header Status -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="relative flex h-3 w-3">
          <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
          <span class="relative inline-flex h-3 w-3 rounded-full bg-rose-500"></span>
        </span>
        <span class="text-xs font-extrabold uppercase tracking-widest text-rose-600">LIVE EXAM WINDOW OPEN</span>
      </div>
      <span class="rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 border border-indigo-100">
        Weight: 50% of Grade
      </span>
    </div>

    <!-- Exam Name & Course -->
    <div class="mt-5">
      <p class="text-sm font-mono font-bold tracking-wider text-indigo-600 uppercase">
        {{ exam.courseCode }}
      </p>
      <h2 class="mt-1 text-2xl font-black leading-tight tracking-tight text-slate-900 md:text-3xl">
        {{ exam.courseName }}
      </h2>
      <p class="mt-1.5 text-sm font-medium text-slate-500">
        Evaluation: <span class="text-slate-800 font-semibold">{{ exam.examTitle }}</span>
      </p>
    </div>

    <hr class="my-5 border-slate-100" />

    <!-- Exam Parameters Grid -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
      <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Instructor</p>
        <p class="mt-0.5 text-xs font-bold text-slate-800 truncate">{{ exam.instructor }}</p>
      </div>
      <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Duration</p>
        <p class="mt-0.5 text-xs font-bold text-slate-800">{{ exam.durationMinutes }} Minutes</p>
      </div>
      <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Format</p>
        <p class="mt-0.5 text-xs font-bold text-slate-800">{{ exam.totalQuestions }} Questions</p>
      </div>
      <div class="rounded-xl bg-slate-50 p-3 border border-slate-100">
        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Marks</p>
        <p class="mt-0.5 text-xs font-bold text-slate-800">{{ exam.totalMarks }} Marks</p>
      </div>
    </div>

    <!-- Interactive Countdown block -->
    <div class="mt-6 rounded-2xl bg-slate-950 p-5 text-white border border-slate-800">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <p class="text-xs font-bold text-indigo-400 uppercase tracking-widest flex items-center gap-1.5">
            <svg class="h-4 w-4 text-orange-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path></svg>
            Portal Access Timer
          </p>
          <p class="mt-0.5 text-xs text-slate-400">You must submit before this clock hits zero.</p>
        </div>
        <div class="flex items-center gap-2">
          <!-- Ticking Units -->
          <div class="flex flex-col items-center">
            <span class="rounded-lg bg-white/10 px-3 py-1 font-mono text-xl font-bold tracking-tight text-white backdrop-blur-md">
              {{ formatNumber(timeLeft.hours) }}
            </span>
            <span class="text-[9px] uppercase font-bold tracking-wider text-slate-500 mt-1">HRS</span>
          </div>
          <span class="text-xl font-extrabold text-slate-600 animate-pulse">:</span>
          <div class="flex flex-col items-center">
            <span class="rounded-lg bg-white/10 px-3 py-1 font-mono text-xl font-bold tracking-tight text-white backdrop-blur-md">
              {{ formatNumber(timeLeft.minutes) }}
            </span>
            <span class="text-[9px] uppercase font-bold tracking-wider text-slate-500 mt-1">MIN</span>
          </div>
          <span class="text-xl font-extrabold text-slate-600 animate-pulse">:</span>
          <div class="flex flex-col items-center">
            <span class="rounded-lg bg-rose-500/20 px-3 py-1 font-mono text-xl font-bold tracking-tight text-rose-400 border border-rose-500/20">
              {{ formatNumber(timeLeft.seconds) }}
            </span>
            <span class="text-[9px] uppercase font-bold tracking-wider text-slate-500 mt-1">SEC</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Security & System Check logs -->
    <div class="mt-5 rounded-xl bg-indigo-50/50 p-4 border border-indigo-100 text-xs text-indigo-950">
      <div class="flex items-start gap-3">
        <svg class="h-5 w-5 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
        <div class="space-y-1">
          <p class="font-bold text-slate-900">Secure Online Proctoring Enabled</p>
          <p class="text-slate-600 leading-relaxed">
            By pressing <strong class="text-indigo-600 font-bold">Start Academic Exam</strong>, you authorize full screen lockdown, window change detection, and automated AI head-pose verification. Ensure a quiet room.
          </p>
        </div>
      </div>
    </div>

    <!-- CTA Button -->
    <button
      @click="emit('start-exam')"
      class="group mt-6 flex w-full items-center justify-center gap-2.5 rounded-xl bg-indigo-600 py-4 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition-all hover:bg-indigo-700 hover:shadow-xl hover:shadow-indigo-600/35 active:scale-[0.98]"
    >
      <svg class="h-4 w-4 fill-current text-white group-hover:scale-110 transition-transform" viewBox="0 0 24 24"><path d="M5 3l14 9-14 9V3z"></path></svg>
      <span>Start Academic Exam</span>
    </button>
  </div>
</template>
