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
        <router-view v-slot="{ Component, route }">
          <transition name="fade" mode="out-in">
            <component :is="Component" :key="route.path" />
          </transition>
        </router-view>
      </main>

    </div>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
