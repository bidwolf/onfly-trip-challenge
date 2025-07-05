<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner'
import GlobalLoading from '@/components/GlobalLoading.vue'
import 'vue-sonner/style.css'

</script>

<template>
  <RouterView />
  <Toaster position="top-right" richColors />
  <GlobalLoading />
</template>