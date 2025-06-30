import { ref, computed } from 'vue'
import { me, logout as logoutApi, type User } from '@/services/auth/index'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'

const user = ref<User | null>(null)
const isLoading = ref(false)

export const useAuth = () => {
  const router = useRouter()

  const isAuthenticated = computed(() => !!user.value)
  const isAdmin = computed(() => user.value?.is_admin)
  const token = computed(() => localStorage.getItem('token'))

  const setUser = (userData: User) => {
    user.value = userData
  }

  const clearUser = () => {
    user.value = null
    localStorage.removeItem('token')
  }

  const fetchUser = async () => {
    if (!token.value) return false

    isLoading.value = true
    try {
      const userData = await me()
      setUser(userData)
      return true
    } catch (error) {
      clearUser()
      return false
    } finally {
      isLoading.value = false
    }
  }

  const logout = async () => {
    try {
      if (token.value) {
        await logoutApi()
      }
    } catch (error) {
      toast.error('Erro ao fazer logout. Por favor, tente novamente.')
    } finally {
      clearUser()
      router.push('/')
    }
  }

  const login = (userData: User, authToken: string) => {
    localStorage.setItem('token', authToken)
    setUser(userData)
    router.push('/dashboard')
  }

  const checkAuth = async () => {
    if (token.value && !user.value) {
      return await fetchUser()
    }
    return !!user.value
  }

  return {
    user: computed(() => user.value),
    isAuthenticated,
    isAdmin,
    isLoading: computed(() => isLoading.value),
    token,
    setUser,
    clearUser,
    fetchUser,
    logout,
    login,
    checkAuth
  }
}
