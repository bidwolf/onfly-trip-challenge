<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { Label } from '@/components/ui/label'

interface Props {
  modelValue?: { start: Date | null; end: Date | null }
  placeholder?: string
}

interface Emits {
  (e: 'update:modelValue', value: { start: Date | null; end: Date | null }): void
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Selecione o período'
})

const emit = defineEmits<Emits>()

const isOpen = ref(false)
const startDate = ref('')
const endDate = ref('')

const value = computed({
  get: () => props.modelValue || { start: null, end: null },
  set: (val) => emit('update:modelValue', val)
})

const displayValue = computed(() => {
  if (value.value.start && value.value.end) {
    const start = value.value.start.toLocaleDateString('pt-BR')
    const end = value.value.end.toLocaleDateString('pt-BR')
    return `${start} - ${end}`
  }
  if (value.value.start) {
    return value.value.start.toLocaleDateString('pt-BR')
  }
  return props.placeholder
})

// Initialize form values from prop
watch(() => props.modelValue, (newValue) => {
  if (newValue?.start) {
    startDate.value = newValue.start.toISOString().split('T')[0]
  } else {
    startDate.value = ''
  }

  if (newValue?.end) {
    endDate.value = newValue.end.toISOString().split('T')[0]
  } else {
    endDate.value = ''
  }
}, { immediate: true })

const updateValue = () => {
  const start = startDate.value ? new Date(startDate.value) : null
  const end = endDate.value ? new Date(endDate.value) : null

  value.value = { start, end }
}

const clearSelection = () => {
  startDate.value = ''
  endDate.value = ''
  value.value = { start: null, end: null }
}

const applySelection = () => {
  updateValue()
  isOpen.value = false
}
</script>

<template>
  <Popover v-model:open="isOpen">
    <PopoverTrigger as-child>
      <Button variant="outline" :class="[
        'w-full justify-start text-left font-normal',
        !value.start && 'text-muted-foreground'
      ]">
        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        {{ displayValue }}
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-80 p-4" align="start">
      <div class="space-y-4">
        <div class="space-y-2">
          <Label for="start-date">Data de Início</Label>
          <Input id="start-date" v-model="startDate" type="date" @change="updateValue" />
        </div>

        <div class="space-y-2">
          <Label for="end-date">Data de Fim</Label>
          <Input id="end-date" v-model="endDate" type="date" @change="updateValue" :min="startDate" />
        </div>

        <div class="flex justify-between pt-2">
          <Button variant="outline" size="sm" @click="clearSelection">
            Limpar
          </Button>
          <Button size="sm" @click="applySelection">
            Aplicar
          </Button>
        </div>
      </div>
    </PopoverContent>
  </Popover>
</template>
