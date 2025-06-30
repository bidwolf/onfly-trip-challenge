<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from 'vee-validate'

import { createTravelOrder } from '@/services/travel-orders'
import { toast } from 'vue-sonner'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
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
import { Textarea } from '@/components/ui/textarea'
import { travelOrderSchema } from '@/schemas/travel-order-schema'

const props = defineProps<{
  onSuccess?: () => void
}>()

const isOpen = ref(false)
const isLoading = ref(false)



const form = useForm({
  validationSchema: travelOrderSchema
})

const onSubmit = form.handleSubmit(async (values) => {
  isLoading.value = true
  try {
    await createTravelOrder({
      requester_name: values.requester_name,
      destination: values.destination,
      departure_date: values.departure_date,
      return_date: values.return_date,
      price: values.price,
      description: values.description,
      hosting: values.accommodation,
      transportation: values.transportation,
    })

    toast.success('Solicitação de viagem criada com sucesso!')
    isOpen.value = false
    form.resetForm()
    props.onSuccess?.()
  } catch (error: any) {
    toast.error(error.response?.data?.message || 'Erro ao criar solicitação de viagem')
  } finally {
    isLoading.value = false
  }
})

const openModal = () => {
  isOpen.value = true
}

defineExpose({
  openModal
})
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogTrigger asChild>
      <Button class="bg-blue-600 hover:bg-blue-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nova Viagem
      </Button>
    </DialogTrigger>

    <DialogContent class="sm:max-w-[500px] max-h-[80vh] overflow-y-auto">
      <DialogHeader>
        <DialogTitle>Nova Solicitação de Viagem</DialogTitle>
        <DialogDescription>
          Preencha os dados da sua viagem corporativa
        </DialogDescription>
      </DialogHeader>

      <form @submit="onSubmit" class="space-y-4">
        <FormField name="requester_name" v-slot="{ componentField }">
          <FormItem>
            <FormLabel>Quem vai viajar?</FormLabel>
            <FormControl>
              <Input type="text" placeholder="Henrique Rodrigues" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField name="destination" v-slot="{ componentField }">
          <FormItem>
            <FormLabel>Destino</FormLabel>
            <FormControl>
              <Input type="text" placeholder="Ex: São Paulo, SP" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>

        <div class="grid grid-cols-2 gap-4">
          <FormField name="departure_date" v-slot="{ componentField }">
            <FormItem>
              <FormLabel>Data de Ida</FormLabel>
              <FormControl>
                <Input type="date" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <FormField name="return_date" v-slot="{ componentField }">
            <FormItem>
              <FormLabel>Data de Volta</FormLabel>
              <FormControl>
                <Input type="date" v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <FormField name="accommodation" v-slot="{ componentField }">
            <FormItem>
              <FormLabel>Acomodação</FormLabel>
              <FormControl>
                <Input type="text" placeholder="Ex: Hotel Ibis, Airbnb..." v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
          <FormField name="transportation" v-slot="{ componentField }">
            <FormItem>
              <FormLabel>Transporte</FormLabel>
              <FormControl>
                <Input type="text" placeholder="Ex: Avião, Carro, Ônibus..." v-bind="componentField" />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
        </div>
        <FormField name="price" v-slot="{ componentField }">
          <FormItem>
            <FormLabel>Valor Total (R$)</FormLabel>
            <FormControl>
              <Input type="number" step="0.01" min="0" placeholder="0,00" v-bind="componentField" />
            </FormControl>
            <FormDescription>
              Valor estimado total da viagem
            </FormDescription>
            <FormMessage />
          </FormItem>
        </FormField>

        <FormField name="description" v-slot="{ componentField }">
          <FormItem>
            <FormLabel>Descrição</FormLabel>
            <FormControl>
              <Textarea placeholder="Descreva o propósito da viagem..." v-bind="componentField" rows="3" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>


        <DialogFooter>
          <Button type="button" variant="outline" @click="isOpen = false" :disabled="isLoading">
            Cancelar
          </Button>
          <Button type="submit" :disabled="isLoading" class="bg-blue-600 hover:bg-blue-700">
            {{ isLoading ? 'Criando...' : 'Criar Solicitação' }}
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
