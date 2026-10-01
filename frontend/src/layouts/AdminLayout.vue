<script setup lang="ts">
import AdminSidebar from './components/AdminSidebar.vue'
import AdminHeader from './components/AdminHeader.vue'
import { ref, provide } from 'vue'

const sidebarOpen = ref(true)
const toggleSidebar = () => { sidebarOpen.value = !sidebarOpen.value }

provide('sidebarOpen', sidebarOpen)
provide('toggleSidebar', toggleSidebar)
</script>

<template>
  <div class="min-h-screen bg-[#f8fafc] font-sans flex overflow-hidden">
    
    <!-- Sidebar: always rendered, handles its own expanded/collapsed state internally -->
    <AdminSidebar />

    <!-- Overlay for mobile when sidebar is open -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 bg-black/20 z-20 lg:hidden"
      @click="toggleSidebar"
    />

    <!-- Main Content Area: adjusts left padding when sidebar is open/closed -->
    <div
      class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto scrollbar-hide transition-all duration-300"
      :class="sidebarOpen ? 'pl-56' : 'pl-20'"
    >
      
      <!-- Header -->
      <AdminHeader />

      <!-- Page Content -->
      <main class="flex-1 p-8">
        <router-view :key="$route.fullPath" />
      </main>

    </div>
  </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
/* Sidebar slide transition */
.sidebar-enter-active,
.sidebar-leave-active {
  transition: transform 0.3s ease, opacity 0.3s ease;
}
.sidebar-enter-from,
.sidebar-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}
</style>
