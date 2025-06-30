<script setup lang="ts">
import { ref, computed } from 'vue'
import { DateFormatter, getLocalTimeZone } from '@internationalized/date'
import { Calendar } from '@/components/ui/calendar'
import { Button } from '@/components/ui/button'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { type DateValue } from 'reka-ui'

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
const df = new DateFormatter('pt-BR', {
  dateStyle: 'medium',
})

const value = computed({
  get: () => props.modelValue || { start: null, end: null },
  set: (val) => emit('update:modelValue', val)
})

const displayValue = computed(() => {
  if (value.value.start && value.value.end) {
    return `${df.format(value.value.start)} - ${df.format(value.value.end)}`
  }
  if (value.value.start) {
    return df.format(value.value.start)
  }
  return props.placeholder
})

const calendarValue = ref<{ start: DateValue | undefined; end: DateValue | undefined }>({
  start: undefined,
  end: undefined
})

const handleDateSelect = (date: DateValue | undefined) => {
  if (!date) return

  const jsDate = date.toDate(getLocalTimeZone())

  if (!calendarValue.value.start || (calendarValue.value.start && calendarValue.value.end)) {
    calendarValue.value = { start: date, end: undefined }
    value.value = { start: jsDate, end: null }
  } else if (calendarValue.value.start && !calendarValue.value.end) {
    if (calendarValue.value.start && date >= calendarValue.value.start) {
      calendarValue.value.end = date
      value.value = { start: value.value.start, end: jsDate }
      isOpen.value = false
    } else {

      calendarValue.value = { start: date, end: undefined }
      value.value = { start: jsDate, end: null }
    }
  }
}

const clearSelection = () => {
  calendarValue.value = { start: undefined, end: undefined }
  value.value = { start: null, end: null }
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
    <PopoverContent class="w-auto p-0" align="start">
      <div class="p-3">
        <Calendar v-model="calendarValue.start as DateValue" mode="single" @update:model-value="handleDateSelect"
          :selected="calendarValue" class="rounded-md border" />
        <div class="flex justify-between mt-3">
          <Button variant="outline" size="sm" @click="clearSelection">
            Limpar
          </Button>
          <Button size="sm" @click="isOpen = false">
            Fechar
          </Button>
        </div>
      </div>
    </PopoverContent>
  </Popover>
</template>
