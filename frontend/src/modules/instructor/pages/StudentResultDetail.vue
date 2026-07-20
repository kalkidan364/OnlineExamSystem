<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const showSuccessModal = ref(false)

const route = useRoute()
const router = useRouter()
const examId = route.params.examId

const activeTab = ref('all')

const tabs = ref([
  { key: 'all', label: 'All Questions', count: 60 },
  { key: 'mcq', label: 'MCQ', count: 40 },
  { key: 'short', label: 'Short Answer', count: 10 },
  { key: 'essay', label: 'Essay', count: 10 },
])
</script>

<template>
  <div class="max-w-[1500px] mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Student Result Details</h1>
        <p class="text-[13px] text-slate-500 mt-1">Review student answers and assign marks.</p>
        <div class="flex items-center gap-2 text-[12px] text-slate-400 mt-2">
          <router-link to="/instructor/results" class="hover:text-[#5138ed] transition-colors">Results Dashboard</router-link>
          <span>&gt;</span>
          <router-link :to="`/instructor/results/${examId}`" class="hover:text-[#5138ed] transition-colors">Database Systems Mid Examination</router-link>
          <span>&gt;</span>
          <router-link :to="`/instructor/results/${examId}`" class="hover:text-[#5138ed] transition-colors">Student Results</router-link>
          <span>&gt;</span>
          <span class="text-slate-600 font-medium">Michael Tadesse</span>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <button @click="router.push(`/instructor/results/${examId}`)" class="flex items-center gap-2 px-4 py-2 border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          Back to Student Results
        </button>
        <button class="flex items-center gap-2 px-4 py-2 bg-[#5138ed] text-white text-sm font-semibold rounded-xl hover:bg-[#4530d1] transition-colors shadow-sm shadow-indigo-200">
          Next Student
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </button>
      </div>
    </div>

    <!-- Student Info Card -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm mb-6 flex flex-wrap lg:flex-nowrap items-center gap-6 lg:gap-10">
      <div class="flex items-center gap-4 flex-1 min-w-[300px]">
        <div class="relative">
          <div class="w-14 h-14 rounded-full bg-indigo-50 text-[#5138ed] font-black text-xl flex items-center justify-center">MT</div>
          <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></div>
        </div>
        <div class="flex flex-col">
          <div class="flex items-center gap-2 mb-1">
            <h2 class="text-lg font-bold text-slate-800">Michael Tadesse</h2>
            <span class="px-2 py-0.5 text-[9px] font-bold bg-emerald-50 text-emerald-600 rounded">Submitted</span>
          </div>
          <div class="flex items-center gap-3 text-[11px] text-slate-500 font-medium">
            <span>ID: STU2021001</span>
            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
            <span>Section: CS 304 - A</span>
            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
            <span>Email: michael.t@wolou.edu.et</span>
          </div>
        </div>
      </div>
      <div class="flex items-center gap-8 shrink-0">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
          <div class="flex flex-col"><span class="text-[10px] font-bold text-slate-400">Submitted On</span><span class="text-[12px] font-bold text-slate-700">May 25, 2025</span><span class="text-[10px] text-slate-500">10:29 AM</span></div>
        </div>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
          <div class="flex flex-col"><span class="text-[10px] font-bold text-slate-400">Time Taken</span><span class="text-[12px] font-bold text-slate-700">78 min</span><span class="text-[10px] text-slate-500">of 90 min</span></div>
        </div>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg></div>
          <div class="flex flex-col"><span class="text-[10px] font-bold text-slate-400">Attempt</span><span class="text-[12px] font-bold text-slate-700">First Attempt</span></div>
        </div>
      </div>
    </div>

    <!-- Main Layout -->
    <div class="flex flex-col xl:flex-row gap-6">
      
      <!-- Left Content (Questions) -->
      <div class="flex-1 min-w-0">
        
        <!-- Tabs -->
        <div class="flex items-center gap-6 border-b border-slate-100 mb-6">
          <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key"
            class="pb-3 text-[13px] font-bold transition-colors relative"
            :class="activeTab === tab.key ? 'text-[#5138ed]' : 'text-slate-500 hover:text-slate-800'">
            {{ tab.label }} ({{ tab.count }})
            <div v-if="activeTab === tab.key" class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#5138ed] rounded-t-full"></div>
          </button>
        </div>

        <!-- Scores Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm flex flex-col items-center justify-center">
            <span class="text-[11px] font-bold text-blue-500 mb-1 flex items-center gap-1"><div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div> Auto Score (MCQ)</span>
            <div class="flex items-baseline gap-2"><span class="text-2xl font-black text-slate-800">32</span><span class="text-[13px] font-bold text-slate-400">/ 40</span><span class="text-[11px] font-bold text-blue-500 ml-2">80.0%</span></div>
          </div>
          <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm flex flex-col items-center justify-center">
            <span class="text-[11px] font-bold text-orange-500 mb-1 flex items-center gap-1"><div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div> Manual Score</span>
            <div class="flex items-baseline gap-2"><span class="text-2xl font-black text-slate-800">15</span><span class="text-[13px] font-bold text-slate-400">/ 20</span><span class="text-[11px] font-bold text-orange-500 ml-2">75.0%</span></div>
          </div>
          <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm flex flex-col items-center justify-center">
            <span class="text-[11px] font-bold text-emerald-500 mb-1 flex items-center gap-1"><div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div> Final Score</span>
            <div class="flex items-baseline gap-2"><span class="text-2xl font-black text-slate-800">47</span><span class="text-[13px] font-bold text-slate-400">/ 60</span><span class="text-[11px] font-bold text-emerald-500 ml-2">78.3%</span></div>
          </div>
          <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm flex flex-col items-center justify-center">
            <span class="text-[11px] font-bold text-[#5138ed] mb-1 flex items-center gap-1"><div class="w-1.5 h-1.5 rounded-full bg-[#5138ed]"></div> Grade</span>
            <div class="flex items-baseline"><span class="text-2xl font-black text-[#5138ed]">B+</span></div>
          </div>
        </div>

        <!-- Question List -->
        <div class="space-y-6">
          
          <!-- Q1 MCQ -->
          <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row gap-6">
            <div class="flex-1">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                  <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-[12px] font-bold shrink-0">1</div>
                  <span class="text-[12px] font-bold text-slate-500">MCQ (Single Choice)</span>
                </div>
                <div class="flex items-center gap-4 text-[12px] font-bold">
                  <span class="text-slate-500">Marks: 1</span>
                  <span class="text-emerald-500">Scored: 1 / 1</span>
                </div>
              </div>
              <p class="text-[14px] text-slate-800 font-medium mb-4">What is the main purpose of a database management system?</p>
              <div class="space-y-2">
                <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50 opacity-60">
                  <div class="w-5 h-5 rounded-full border border-slate-300 flex items-center justify-center text-[10px] font-bold text-slate-400">A</div>
                  <span class="text-[13px] text-slate-600">To create presentations</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl border-2 border-emerald-500 bg-emerald-50 relative">
                  <div class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold">B</div>
                  <span class="text-[13px] font-bold text-emerald-800">To store, manage and retrieve data efficiently</span>
                  <span class="absolute right-4 text-[11px] font-bold text-emerald-600">Correct Answer</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50 opacity-60">
                  <div class="w-5 h-5 rounded-full border border-slate-300 flex items-center justify-center text-[10px] font-bold text-slate-400">C</div>
                  <span class="text-[13px] text-slate-600">To design graphics</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50 opacity-60">
                  <div class="w-5 h-5 rounded-full border border-slate-300 flex items-center justify-center text-[10px] font-bold text-slate-400">D</div>
                  <span class="text-[13px] text-slate-600">To browse the internet</span>
                </div>
              </div>
            </div>
            
            <div class="w-full md:w-[220px] shrink-0 border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-6 flex flex-col gap-4">
              <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500">Correct Answer</span>
                <span class="text-[13px] font-black text-slate-800">B</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500">Your Answer</span>
                <span class="text-[13px] font-black text-slate-800">B</span>
              </div>
              <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <span class="text-[11px] font-bold text-slate-500">Status</span>
                <span class="text-[11px] font-bold text-emerald-500 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Correct</span>
              </div>
              <div>
                <span class="text-[11px] font-bold text-slate-800 mb-1 block">Explanation</span>
                <p class="text-[11px] text-slate-500 leading-relaxed">A DBMS is used to store, manage, and retrieve data efficiently.</p>
              </div>
            </div>
          </div>

          <!-- Q2 True/False -->
          <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row gap-6">
            <div class="flex-1">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                  <div class="w-7 h-7 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-[12px] font-bold shrink-0">2</div>
                  <span class="text-[12px] font-bold text-slate-500">True / False</span>
                </div>
                <div class="flex items-center gap-4 text-[12px] font-bold">
                  <span class="text-slate-500">Marks: 1</span>
                  <span class="text-rose-500">Scored: 0 / 1</span>
                </div>
              </div>
              <p class="text-[14px] text-slate-800 font-medium mb-4">Normalization in database systems is used to reduce data redundancy.</p>
              <div class="space-y-2">
                <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50">
                  <div class="w-5 h-5 rounded-full border border-slate-300 flex items-center justify-center text-[10px] font-bold text-slate-400">A</div>
                  <span class="text-[13px] text-slate-600">True</span>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-xl border-2 border-rose-500 bg-rose-50 relative">
                  <div class="w-5 h-5 rounded-full bg-rose-500 text-white flex items-center justify-center text-[10px] font-bold">B</div>
                  <span class="text-[13px] font-bold text-rose-800">False</span>
                  <span class="absolute right-4 text-[11px] font-bold text-rose-600 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg> Your Answer</span>
                </div>
              </div>
            </div>
            
            <div class="w-full md:w-[220px] shrink-0 border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-6 flex flex-col gap-4">
              <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500">Correct Answer</span>
                <span class="text-[13px] font-black text-slate-800">A</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500">Your Answer</span>
                <span class="text-[13px] font-black text-slate-800">B</span>
              </div>
              <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <span class="text-[11px] font-bold text-slate-500">Status</span>
                <span class="text-[11px] font-bold text-rose-500 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg> Incorrect</span>
              </div>
              <div>
                <span class="text-[11px] font-bold text-slate-800 mb-1 block">Explanation</span>
                <p class="text-[11px] text-slate-500 leading-relaxed">Normalization is a process used to reduce redundancy and improve data integrity.</p>
              </div>
            </div>
          </div>

          <!-- Q3 Essay (Manual Grading) -->
          <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-[12px] font-bold shrink-0">3</div>
                <span class="text-[12px] font-bold text-slate-500">Essay Question</span>
              </div>
              <div class="flex items-center gap-3">
                <span class="text-[12px] font-bold text-slate-500">Marks: 10</span>
                <div class="flex items-center gap-2">
                  <span class="text-[12px] font-bold text-slate-800">Your Score:</span>
                  <input type="number" value="8" class="w-16 h-8 text-center border-2 border-[#5138ed] rounded-lg text-[13px] font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#5138ed]/20" />
                  <span class="text-[12px] font-bold text-slate-400">/ 10</span>
                </div>
              </div>
            </div>
            <p class="text-[14px] text-slate-800 font-bold mb-4">Explain ACID properties in database systems with suitable examples.</p>
            
            <div class="flex flex-col md:flex-row gap-6 mt-4">
              <div class="flex-1">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-2 block">Student Answer</span>
                <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl">
                  <p class="text-[13px] text-slate-700 leading-relaxed">
                    ACID stands for Atomicity, Consistency, Isolation, and Durability. Atomicity ensures that a transaction is all or nothing. Consistency ensures that the database remains in a valid state before and after the transaction. Isolation ensures that concurrent transactions do not interfere with each other. Durability ensures that once a transaction is committed, it remains saved even in case of system failure. Example: In a bank transfer, money is deducted from one account and added to another. If the system fails in between, Atomicity ensures the transaction is rolled back.
                  </p>
                </div>
              </div>
              <div class="w-full md:w-[250px] shrink-0">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wide mb-2 block">Feedback (Optional)</span>
                <textarea class="w-full h-full min-h-[100px] p-3 border border-slate-200 rounded-xl text-[12px] text-slate-700 focus:outline-none focus:border-[#5138ed] focus:ring-1 focus:ring-[#5138ed] resize-none" placeholder="Write feedback here...">Good explanation. You covered all ACID properties with a relevant example.</textarea>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Right Sidebar -->
      <div class="w-full xl:w-[280px] shrink-0 space-y-4">
        
        <!-- Score Summary -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
          <h3 class="text-[13px] font-bold text-slate-800 mb-4">Score Summary</h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between border-b border-slate-50 pb-2"><span class="text-[12px] text-slate-500 font-medium">Total Marks</span><span class="text-[13px] font-bold text-slate-800">60</span></div>
            <div class="flex items-center justify-between border-b border-slate-50 pb-2"><span class="text-[12px] text-slate-500 font-medium">Auto Score (MCQ)</span><span class="text-[13px] font-bold text-blue-500">32 / 40 (80.0%)</span></div>
            <div class="flex items-center justify-between border-b border-slate-50 pb-2"><span class="text-[12px] text-slate-500 font-medium">Manual Score</span><span class="text-[13px] font-bold text-orange-500">15 / 20 (75.0%)</span></div>
            <div class="flex items-center justify-between border-b border-slate-50 pb-2"><span class="text-[12px] text-slate-500 font-medium">Final Score</span><span class="text-[13px] font-bold text-emerald-500">47 / 60 (78.3%)</span></div>
            <div class="flex items-center justify-between pt-1"><span class="text-[12px] text-slate-500 font-medium">Grade</span><span class="text-[14px] font-black text-[#5138ed]">B+</span></div>
          </div>
        </div>

        <!-- Question Breakdown -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
          <h3 class="text-[13px] font-bold text-slate-800 mb-4">Question Breakdown</h3>
          <div class="space-y-2.5">
            <div class="flex items-center justify-between"><div class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div><span class="text-[12px] text-slate-600">MCQ Questions</span></div><span class="text-[12px] font-bold text-slate-800">40</span></div>
            <div class="flex items-center justify-between"><div class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-[#5138ed]"></div><span class="text-[12px] text-slate-600">Short Answer Questions</span></div><span class="text-[12px] font-bold text-slate-800">10</span></div>
            <div class="flex items-center justify-between mb-2"><div class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-orange-400"></div><span class="text-[12px] text-slate-600">Essay Questions</span></div><span class="text-[12px] font-bold text-slate-800">10</span></div>
            <div class="h-px bg-slate-100 my-3"></div>
            <div class="flex items-center justify-between"><div class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div><span class="text-[12px] text-slate-600">Correct</span></div><span class="text-[12px] font-bold text-slate-800">28</span></div>
            <div class="flex items-center justify-between"><div class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-rose-500"></div><span class="text-[12px] text-slate-600">Incorrect</span></div><span class="text-[12px] font-bold text-slate-800">8</span></div>
            <div class="flex items-center justify-between"><div class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div><span class="text-[12px] text-slate-600">Pending Grading</span></div><span class="text-[12px] font-bold text-slate-800">12</span></div>
          </div>
        </div>

        <!-- Grading Progress -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
          <h3 class="text-[13px] font-bold text-slate-800 mb-4">Grading Progress</h3>
          <div class="flex items-center gap-4">
            <div class="relative w-16 h-16 shrink-0">
              <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="40" fill="none" stroke="#f1f5f9" stroke-width="12"></circle>
                <circle cx="50" cy="50" r="40" fill="none" stroke="#3b82f6" stroke-width="12" stroke-linecap="round" stroke-dasharray="188.5 251.3" stroke-dashoffset="0"></circle>
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-[14px] font-black text-slate-800 leading-none mt-0.5">75%</span>
              </div>
            </div>
            <div class="flex flex-col">
              <span class="text-[12px] font-bold text-slate-800">Graded</span>
              <span class="text-[11px] text-slate-500 mb-1">45 / 60 Marks</span>
              <span class="text-[10px] font-bold text-orange-500 bg-orange-50 px-2 py-0.5 rounded self-start">12 Marks Pending</span>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="space-y-2">
          <h3 class="text-[13px] font-bold text-slate-800 mb-3 ml-1">Actions</h3>
          <button @click="showSuccessModal = true" class="w-full py-3 bg-[#5138ed] text-white text-[13px] font-bold rounded-xl shadow-sm hover:bg-[#4530d1] transition-colors flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            Save Marks
          </button>
          <button class="w-full py-3 bg-white border border-slate-200 text-[#5138ed] text-[13px] font-bold rounded-xl hover:bg-slate-50 transition-colors flex items-center justify-center gap-2">
            Save & Next Student
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
          </button>
          <button class="w-full py-3 bg-white border border-slate-200 text-emerald-600 text-[13px] font-bold rounded-xl hover:bg-emerald-50 transition-colors flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Mark as Graded
          </button>
        </div>

        <!-- Info Banner -->
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3 mt-6">
          <div class="w-6 h-6 rounded-lg bg-white flex items-center justify-center text-blue-500 shrink-0 shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
          <p class="text-[10px] text-blue-700 font-medium leading-relaxed">Results will be visible to students only after publishing.</p>
        </div>

      </div>

    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
      <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-[480px] relative overflow-hidden flex flex-col items-center p-8 border border-slate-100">
        
        <!-- Close button -->
        <button @click="showSuccessModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <!-- Icon & Confetti -->
        <div class="relative w-32 h-32 flex items-center justify-center mb-2">
          <!-- decorative dots -->
          <div class="absolute top-4 left-6 w-1.5 h-1.5 rounded-full bg-[#5138ed]"></div>
          <div class="absolute top-10 right-4 w-2 h-2 rounded-full bg-orange-400"></div>
          <div class="absolute bottom-6 left-2 w-2 h-2 rounded-full bg-emerald-400"></div>
          <div class="absolute bottom-4 right-8 w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
          <div class="absolute top-1/2 right-1 w-1 h-1 rounded-full bg-[#5138ed]"></div>
          <div class="absolute top-6 left-1/2 w-1.5 h-1.5 rounded-full bg-amber-500"></div>
          <div class="absolute bottom-1/2 left-2 w-1.5 h-1.5 rounded-full bg-blue-500"></div>
          <div class="absolute top-1/4 left-1/4 w-1 h-1 rounded-full bg-rose-400"></div>
          <div class="absolute bottom-1/4 right-1/4 w-2 h-2 rounded-full bg-blue-400"></div>

          <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-500 z-10 shadow-sm border-4 border-white">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
          </div>
        </div>

        <h3 class="text-[22px] font-extrabold text-slate-800 mb-2">Marks Saved Successfully!</h3>
        <p class="text-[13px] text-slate-500 font-medium mb-8">The student's marks have been saved.</p>

        <div class="w-full border border-slate-100 rounded-2xl p-5 mb-8 space-y-4 shadow-sm bg-white">
          <div class="flex items-start justify-between">
            <span class="text-[12px] font-medium text-slate-500">Student Name</span>
            <span class="text-[12px] font-bold text-slate-800">Michael Tadesse</span>
          </div>
          <div class="flex items-start justify-between">
            <span class="text-[12px] font-medium text-slate-500">Exam</span>
            <span class="text-[12px] font-bold text-slate-800 text-right">Database Systems Mid Examination</span>
          </div>
          <div class="flex items-start justify-between">
            <span class="text-[12px] font-medium text-slate-500">Total Marks</span>
            <span class="text-[12px] font-bold text-slate-800">60</span>
          </div>
          <div class="flex items-start justify-between">
            <span class="text-[12px] font-medium text-slate-500">Final Score</span>
            <span class="text-[12px] font-bold text-slate-800">47 / 60 (78.3%)</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-[12px] font-medium text-slate-500">Grade</span>
            <span class="px-2 py-0.5 bg-indigo-50 text-[#5138ed] font-black text-[11px] rounded-md">B+</span>
          </div>
          <div class="flex items-start justify-between">
            <span class="text-[12px] font-medium text-slate-500">Saved On</span>
            <span class="text-[12px] font-bold text-slate-800">May 27, 2026 at 10:35 AM</span>
          </div>
        </div>

        <button @click="showSuccessModal = false" class="px-8 py-3 w-[140px] bg-[#5138ed] text-white text-[13px] font-bold rounded-xl shadow-sm hover:bg-[#4530d1] transition-colors flex items-center justify-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
          OK
        </button>

      </div>
    </div>
  </div>
</template>
