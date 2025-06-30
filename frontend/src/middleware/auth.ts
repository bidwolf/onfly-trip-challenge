import { useAuth } from '@/composables/useAuth'
import type { NavigationGuardNext, RouteLocationNormalized } from 'vue-router'

export const authGuard = async (
  to: RouteLocationNormalized,
  _from: RouteLocationNormalized,
  next: NavigationGuardNext
) => {
  const { checkAuth } = useAuth()

  const isAuthenticated = await checkAuth()

  const protectedRoutes = ['/dashboard', '/travel-order']
  const isProtectedRoute = protectedRoutes.some(route => to.path.startsWith(route))

  const guestOnlyRoutes = ['/login', '/register']
  const isGuestOnlyRoute = guestOnlyRoutes.includes(to.path)

  if (isProtectedRoute && !isAuthenticated) {
    next('/login')
  } else if (isGuestOnlyRoute && isAuthenticated) {
    next('/dashboard')
  } else {
    next()
  }
}
