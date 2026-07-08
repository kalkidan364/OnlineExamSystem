<script setup lang="ts">
import AdminSidebar from './components/AdminSidebar.vue'
import AdminHeader from './components/AdminHeader.vue'
</script>

<template>
  <div class="min-h-screen bg-[#f8fafc] font-sans flex">
    
    <!-- Sidebar -->
    <AdminSidebar />

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col pl-56 min-w-0">
      
      <!-- Header -->
      <AdminHeader />

      <!-- Page Content -->
      <main class="flex-1 p-8">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <Suspense>
              <template #default>
                <component :is="Component" />
              </template>
              <template #fallback>
                <div class="flex items-center justify-center min-h-[60vh]">
                  <div class="flex flex-col items-center gap-4">
                    <div class="w-10 h-10 rounded-full border-4 border-rose-500/20 border-t-rose-500 animate-spin"></div>
                    <p class="text-[13px] font-semibold text-slate-400">Loading page...</p>
                  </div>
                </div>
              </template>
            </Suspense>
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
