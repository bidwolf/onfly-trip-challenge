<script lang="ts" setup>
import { loginFormValidationSchema } from "@/schemas/login-schema";
import { useForm } from "vee-validate";
import { login as loginApi, me } from "@/services/auth/index"
import { useAuth } from "@/composables/useAuth"
import { toast } from "vue-sonner"
import {
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage
} from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { useRouter } from "vue-router";

const { login } = useAuth()

const form = useForm({
  validationSchema: loginFormValidationSchema
});
const router = useRouter()

const onSubmit = form.handleSubmit(
  async values => {
    try {
      const response = await loginApi({
        email: values.email,
        password: values.password
      })
      if (response.token) {
        localStorage.setItem('token', response.token)
        const userData = await me()
        login(userData, response.token)
        toast.success("Cadastro realizado com sucesso! Faça login para continuar.")
        router.push('/dashboard')
      }
      toast.success("Login realizado com sucesso!")
    } catch (error: any) {
      toast.error(error.response?.data?.message || "Erro ao fazer login. Verifique suas credenciais.")
    }
  }
)
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
    </nav>
    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow w-full">
      <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Fazer Login</h1>
      <form @submit="onSubmit" class="space-y-6">
        <FormField name="email" v-slot="{ componentField }">
          <FormItem class="space-y-1">
            <FormLabel class="block text-sm font-medium text-gray-700">
              Email
            </FormLabel>
            <FormControl>
              <Input type="email" placeholder="Digite seu e-mail" v-bind="componentField"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" />
            </FormControl>
            <FormDescription class="text-xs text-gray-500">
              Digite um e-mail válido.
            </FormDescription>
            <FormMessage class="text-xs text-red-600" />
          </FormItem>
        </FormField>

        <FormField name="password" v-slot="{ componentField }">
          <FormItem class="space-y-1">
            <FormLabel class="block text-sm font-medium text-gray-700">
              Senha
            </FormLabel>
            <FormControl>
              <Input type="password" placeholder="Digite sua senha" v-bind="componentField"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500" />
            </FormControl>
            <FormDescription class="text-xs text-gray-500">
              Sua senha deve conter pelo menos 8 caracteres.
            </FormDescription>
            <FormMessage class="text-xs text-red-600" />
          </FormItem>
        </FormField>


        <Button variant="default" type="submit"
          class="w-full py-2 px-4 bg-sky-500 text-white font-semibold rounded hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
          Fazer Login
        </Button>

        <div class="text-center">
          <RouterLink to="/register" class="text-sky-500 hover:text-sky-800 text-sm">
            Não possui uma conta? Cadastre-se
          </RouterLink>
        </div>
      </form>
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

</template>
