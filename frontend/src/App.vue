<script setup lang="ts">
import { RouterView } from 'vue-router'
import { useSettingsStore } from './store/settingsStore'
import { onMounted } from 'vue'

const settingsStore = useSettingsStore()

onMounted(() => {
  // Only fetch settings when the user is already authenticated.
  // Calling this without a token causes a 401 → redirect loop on the login page.
  if (localStorage.getItem('auth_token')) {
    settingsStore.fetchSettings()
  }
})
</script>

<template>
  <RouterView />
</template>
