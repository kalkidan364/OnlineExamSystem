<script setup lang="ts">
import DeptHeadSidebar from './components/DeptHeadSidebar.vue'
import DeptHeadHeader from './components/DeptHeadHeader.vue'
</script>

<template>
  <div class="min-h-screen bg-[#f8fafc] font-sans flex">
    
    <!-- Sidebar -->
    <DeptHeadSidebar />

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col pl-56 min-w-0">
      
      <!-- Header -->
      <DeptHeadHeader />

      <!-- Page Content -->
      <main class="flex-1 p-8">
        <router-view :key="$route.fullPath" />
      </main>

    </div>
  </div>
</template>

<style scoped>
</style>
