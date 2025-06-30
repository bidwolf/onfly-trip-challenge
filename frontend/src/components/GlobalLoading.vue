<script setup lang="ts">
import { useGlobalLoading } from '@/composables/useGlobalLoading'

const { isGlobalLoading, loadingMessage } = useGlobalLoading()
</script>

<template>
  <Teleport to="body">
    <Transition enter-active-class="transition-opacity duration-300"
      leave-active-class="transition-opacity duration-300" enter-from-class="opacity-0" leave-to-class="opacity-0">
      <div v-if="isGlobalLoading"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-lg p-6 shadow-xl flex flex-col items-center space-y-4 max-w-sm mx-4">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <p class="text-gray-700 text-center">{{ loadingMessage }}</p>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
