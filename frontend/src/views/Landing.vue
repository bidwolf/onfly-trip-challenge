<script lang="ts" setup>
import { useRouter } from 'vue-router'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const { user, logout } = useAuth()

const goToLogin = () => {
  router.push('/login')
}

const goToRegister = () => {
  router.push('/register')
}
const goToDashboard = () => {
  router.push('/dashboard')
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-sky-400 via-sky-500 to-blue-600 flex flex-col">
    <nav class="relative z-20 flex justify-between items-center p-6 md:px-12">
      <div class="flex items-center space-x-2">
        <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
          </svg>
        </div>
        <span class="text-white font-bold text-xl">Onfly</span>
      </div>

      <div class="flex space-x-3">

        <template v-if="!user">
          <Button @click="goToLogin" variant="outline" size="sm"
            class="border-white/50 text-white hover:bg-white hover:text-blue-600 backdrop-blur-sm bg-white/10">
            Login
          </Button>
          <Button @click="goToRegister" size="sm" class="bg-white text-blue-600 hover:bg-gray-50">
            Cadastrar
          </Button>
        </template>
        <template v-else>
          <span class="text-white font-medium">Olá, {{ user.name }}</span>
          <Button @click="logout" variant="outline" size="sm"
            class="border-white/50 text-white hover:bg-white hover:text-blue-600 backdrop-blur-sm bg-white/10">
            Logout
          </Button>
        </template>
      </div>
    </nav>

    <div class="flex-1 flex items-center justify-center p-4">

      <div class="relative z-10 w-full max-w-6xl mx-auto">
        <div class="text-center text-white mb-12">
          <div class="mb-8">
            <div
              class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full mb-6">
              <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
            </div>
          </div>

          <!-- Main Heading -->
          <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
            <span class="block">Onfly</span>
            <span class="block text-3xl md:text-4xl font-light text-sky-100">Business Travel</span>
          </h1>

          <p class="text-lg text-sky-200 mb-12 max-w-2xl mx-auto">
            Controle total sobre solicitações, aprovações e orçamentos de viagem em uma plataforma intuitiva e moderna
          </p>
        </div>
        <template v-if="user">
          <Button @click="goToDashboard" variant="default" size="lg"
            class="border-white/50 w-full my-8 bg-sky-900 backdrop-blur-sm ">
            Ir para o Dashboard
          </Button>
        </template>
        <div class="grid md:grid-cols-3 gap-6 mb-12">
          <Card class="bg-white/10 backdrop-blur-sm border-white/20 text-white">
            <CardContent class="p-6 text-center">
              <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <h3 class="font-semibold text-lg mb-2">Solicitações Simples</h3>
              <p class="text-sky-100 text-sm">
                Crie e gerencie solicitações de viagem de forma rápida e intuitiva
              </p>
            </CardContent>
          </Card>

          <Card class="bg-white/10 backdrop-blur-sm border-white/20 text-white">
            <CardContent class="p-6 text-center">
              <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="font-semibold text-lg mb-2">Aprovações Ágeis</h3>
              <p class="text-sky-100 text-sm">
                Fluxo de aprovação automatizado com notificações em tempo real
              </p>
            </CardContent>
          </Card>

          <Card class="bg-white/10 backdrop-blur-sm border-white/20 text-white">
            <CardContent class="p-6 text-center">
              <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </div>
              <h3 class="font-semibold text-lg mb-2">Controle de Custos</h3>
              <p class="text-sky-100 text-sm">
                Monitore e controle os gastos com viagens em tempo real
              </p>
            </CardContent>
          </Card>
        </div>


        <div class="mt-16 text-center">
          <div class="inline-flex items-center text-sky-200 text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
            </svg>
            Feito por Henrique de Paula Rodrigues <v-icon name="fc-globe" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes float {

  0%,
  100% {
    transform: translateY(0px);
  }

  50% {
    transform: translateY(-20px);
  }
}

.float {
  animation: float 6s ease-in-out infinite;
}

.gradient-text {
  background: linear-gradient(135deg, #ffffff 0%, #e0f2fe 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
</style>
