<script lang="ts" setup>
import { registerFormValidationSchema } from "@/schemas/register-schema";
import { useForm } from "vee-validate";
import { me, register as registerApi } from "@/services/auth/index"
import { useRouter } from 'vue-router'
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
import { useAuth } from "@/composables/useAuth";
const { login } = useAuth()

const router = useRouter()

const form = useForm({
  validationSchema: registerFormValidationSchema
});

const onSubmit = form.handleSubmit(
  async values => {
    try {
      const response = await registerApi({
        name: values.name,
        email: values.email,
        password: values.password,
        password_confirmation: values.password_confirmation
      })
      if (response.token) {
        localStorage.setItem('token', response.token)
        const userData = await me()
        login(userData, response.token)
        toast.success("Cadastro realizado com sucesso! Faça login para continuar.")
        router.push('/dashboard')
        return
      }
      toast.error("Erro ao criar conta. Tente novamente.")
    } catch (error: any) {
      toast.error(error.response?.data?.message || "Erro ao criar conta. Tente novamente.")
    }
  }
)
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-sky-400 via-sky-500 to-blue-600 flex flex-col">
    <div class="flex gap-10 items-center justify-center">
      <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight text-sky-100 text-center">
        <span class="block">Onfly</span>
        <span class="block text-3xl md:text-4xl font-light text-sky-100">Business Travel</span>
      </h1>
      <div class="">
        <div class="inline-flex items-center justify-center size-24 bg-white/20 backdrop-blur-sm rounded-full">
          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
          </svg>
        </div>
      </div>
    </div>
    <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow col-span-4 w-full">
      <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Cadastro</h1>
      <form @submit="onSubmit" class="space-y-6">
        <FormField name="name" v-slot="{ componentField }">
          <FormItem class="space-y-1">
            <FormLabel class="block text-sm font-medium text-gray-700">
              Nome
            </FormLabel>
            <FormControl>
              <Input type="text" placeholder="Digite seu nome completo" v-bind="componentField"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </FormControl>
            <FormDescription class="text-xs text-gray-500">
              Digite seu nome completo.
            </FormDescription>
            <FormMessage class="text-xs text-red-600" />
          </FormItem>
        </FormField>

        <FormField name="email" v-slot="{ componentField }">
          <FormItem class="space-y-1">
            <FormLabel class="block text-sm font-medium text-gray-700">
              Email
            </FormLabel>
            <FormControl>
              <Input type="email" placeholder="Digite seu e-mail" v-bind="componentField"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
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
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </FormControl>
            <FormDescription class="text-xs text-gray-500">
              Sua senha deve conter pelo menos 8 caracteres.
            </FormDescription>
            <FormMessage class="text-xs text-red-600" />
          </FormItem>
        </FormField>

        <FormField name="password_confirmation" v-slot="{ componentField }">
          <FormItem class="space-y-1">
            <FormLabel class="block text-sm font-medium text-gray-700">
              Confirmar Senha
            </FormLabel>
            <FormControl>
              <Input type="password" placeholder="Confirme sua senha" v-bind="componentField"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
            </FormControl>
            <FormDescription class="text-xs text-gray-500">
              Digite a senha novamente para confirmar.
            </FormDescription>
            <FormMessage class="text-xs text-red-600" />
          </FormItem>
        </FormField>

        <Button type="submit"
          class="w-full py-2 px-4  font-semibold rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
          Cadastrar
        </Button>

        <div class="text-center">
          <RouterLink to="/login" class="text-blue-600 hover:text-blue-800 text-sm">
            Já tem uma conta? Faça login
          </RouterLink>
        </div>
      </form>
    </div>
  </div>

</template>
