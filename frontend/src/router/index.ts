import Login from '@/views/Login.vue'
import { createMemoryHistory, createRouter } from 'vue-router'
import Landing from '@/views/Landing.vue'
import Main from '../views/Main.vue'
import Register from '../views/Register.vue'
import TravelOrderDetails from '../views/TravelOrderDetails.vue'
import { authGuard } from '@/middleware/auth'

const routes = [
  {
    path: '/',
    name: 'Landing',
    component: Landing,
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
  },
  {
    path: '/dashboard',
    name: 'Main',
    component: Main,
  },
  {
    path: '/travel-order/:id',
    name: 'TravelOrderDetails',
    component: TravelOrderDetails,
    props: true,
  },
]

const router = createRouter({
  history: createMemoryHistory(),
  routes,
})

router.beforeEach(authGuard)

export default router