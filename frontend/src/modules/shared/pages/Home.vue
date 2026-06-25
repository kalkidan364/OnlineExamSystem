<script setup lang="ts">
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex flex-col justify-center items-center font-sans">
    <div class="bg-white p-10 rounded-2xl shadow-xl text-center max-w-lg w-full">
      <div class="mb-6 flex justify-center">
        <div class="h-20 w-20 bg-blue-600 rounded-full flex items-center justify-center shadow-lg border-4 border-blue-100">
          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21v-7"></path></svg>
        </div>
      </div>
      <h1 class="text-3xl font-extrabold text-slate-900 mb-2 tracking-tight">Wollo University</h1>
      <p class="text-lg font-medium text-blue-600 mb-6">Online Examination System</p>
      <p class="text-slate-500 mb-8">
        The enterprise frontend architecture is successfully initialized and ready for development.
      </p>
      <div class="flex justify-center gap-4">
        <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg">
          Super Admin
        </button>
        <button class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-2 px-6 rounded-lg transition-colors duration-200">
          Student
        </button>
      </div>
    </div>
  </div>
</template>
