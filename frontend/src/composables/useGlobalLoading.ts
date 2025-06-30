import { ref } from 'vue'

const isGlobalLoading = ref(false)
const loadingMessage = ref('')

export const useGlobalLoading = () => {
  const showGlobalLoading = (message: string = 'Carregando...') => {
    loadingMessage.value = message
    isGlobalLoading.value = true
  }

  const hideGlobalLoading = () => {
    isGlobalLoading.value = false
    loadingMessage.value = ''
  }

  return {
    isGlobalLoading,
    loadingMessage,
    showGlobalLoading,
    hideGlobalLoading
  }
}
