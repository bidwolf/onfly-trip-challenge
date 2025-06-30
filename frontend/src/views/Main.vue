<script lang="ts" setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { getTravelOrders, type TravelOrderFilters } from '@/services/travel-orders'
import { useAuth } from '@/composables/useAuth'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Badge } from '@/components/ui/badge'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table'
import { DateRangePicker } from '@/components/ui/date-range-picker'
import CreateTravelOrderModal from '@/components/CreateTravelOrderModal.vue'

interface TravelOrder {
  id: number
  destination: string
  departure_date: string
  return_date: string
  status: string
  price: number
}

const router = useRouter()
const route = useRoute()
const { user, logout, isAdmin } = useAuth()
const travelOrders = ref<TravelOrder[]>([])
const loading = ref(true)
const createModalRef = ref<InstanceType<typeof CreateTravelOrderModal> | null>(null)

// Filter states
const filters = ref({
  status: (route.query.status as string) || '',
  destination: (route.query.destination as string) || '',
  dateRange: {
    start: route.query.start_date ? new Date(route.query.start_date as string) : null,
    end: route.query.end_date ? new Date(route.query.end_date as string) : null,
  }
})

const statusOptions = [
  { label: 'Todos os Status', value: 'all' },
  { label: 'Pendente', value: 'pendente' },
  { label: 'Aprovado', value: 'aprovado' },
  { label: 'Cancelado', value: 'cancelado' },
]

// Update query string when filters change
const updateQueryString = () => {
  const query: Record<string, string> = {}

  if (filters.value.status) {
    query.status = filters.value.status
  }
  if (filters.value.destination) {
    query.destination = filters.value.destination
  }
  if (filters.value.dateRange.start) {
    query.start_date = filters.value.dateRange.start.toISOString().split('T')[0]
  }
  if (filters.value.dateRange.end) {
    query.end_date = filters.value.dateRange.end.toISOString().split('T')[0]
  }

  router.push({ query })
}

// Watch for filter changes and update URL + fetch data
watch(() => [filters.value.status, filters.value.destination, filters.value.dateRange], () => {
  updateQueryString()
  fetchTravelOrders()
}, { deep: true })

// Watch for route query changes to sync filters
watch(() => route.query, (newQuery) => {
  filters.value.status = (newQuery.status as string) || ''
  filters.value.destination = (newQuery.destination as string) || ''
  filters.value.dateRange = {
    start: newQuery.start_date ? new Date(newQuery.start_date as string) : null,
    end: newQuery.end_date ? new Date(newQuery.end_date as string) : null,
  }
  fetchTravelOrders()
}, { immediate: false })

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('pt-BR')
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(amount)
}

const getStatusBadgeVariant = (status: string) => {
  switch (status.toLowerCase()) {
    case 'aprovado':
      return 'default' // green
    case 'pendente':
      return 'secondary' // yellow  
    case 'cancelado':
      return 'destructive' // red
    default:
      return 'outline'
  }
}

const viewDetails = (orderId: number) => {
  router.push(`/travel-order/${orderId}`)
}

const handleLogout = () => {
  logout()
}

const fetchTravelOrders = async () => {
  loading.value = true
  try {
    const filterParams: TravelOrderFilters = {}

    if (filters.value.status) {
      filterParams.status = filters.value.status !== 'all' ? filters.value.status : ''
    }
    if (filters.value.destination) {
      filterParams.destination = filters.value.destination
    }
    if (filters.value.dateRange.start) {
      filterParams.start_date = filters.value.dateRange.start.toISOString().split('T')[0]
    }
    if (filters.value.dateRange.end) {
      filterParams.end_date = filters.value.dateRange.end.toISOString().split('T')[0]
    }

    const response = await getTravelOrders(filterParams)
    travelOrders.value = response.data

    // Toast com número de resultados (apenas quando há filtros aplicados)
    const hasFilters = filterParams.status || filterParams.destination || filterParams.start_date || filterParams.end_date
    if (hasFilters) {
      const count = response.data.length
      toast.info(`${count} viagem${count !== 1 ? 'ns' : ''} encontrada${count !== 1 ? 's' : ''}`)
    }
  } catch (error) {
    console.error('Failed to fetch travel orders:', error)
    toast.error('Erro ao carregar viagens')
  } finally {
    loading.value = false
  }
}

const refreshData = async () => {
  await fetchTravelOrders()
  toast.success('Dados atualizados!')
}

const clearFilters = () => {
  filters.value = {
    status: '',
    destination: '',
    dateRange: { start: null, end: null }
  }
  toast.success('Filtros limpos com sucesso!')
}

onMounted(async () => {
  await fetchTravelOrders()
})
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              Sistema de Viagens
            </h1>
            <p v-if="user" class="text-sm text-gray-600 mt-1">
              Bem-vindo, {{ user?.name }}
              <Badge v-if="isAdmin" variant="secondary" class="ml-2">Admin</Badge>
            </p>
          </div>
          <Button @click="handleLogout" variant="outline" class="text-red-600 border-red-600 hover:bg-red-50">
            Sair
          </Button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <!-- Filters Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
          <div class="flex flex-wrap gap-4 items-end">
            <!-- Status Filter -->
            <div class="flex-1 min-w-[200px]">
              <Label for="status-filter" class="text-sm font-medium">Status</Label>
              <Select v-model="filters.status">
                <SelectTrigger id="status-filter">
                  <SelectValue placeholder="Todos os Status" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="option in statusOptions" :key="option.value" :value="option.value">
                    {{ option.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>

            <!-- Destination Filter -->
            <div class="flex-1 min-w-[200px]">
              <Label for="destination-filter" class="text-sm font-medium">Destino</Label>
              <Input id="destination-filter" v-model="filters.destination" type="text"
                placeholder="Filtrar por destino..." class="w-full" />
            </div>

            <!-- Date Range Filter -->
            <div class="flex-1 min-w-[250px]">
              <Label class="text-sm font-medium">Período</Label>
              <DateRangePicker v-model="filters.dateRange" placeholder="Selecione o período" />
            </div>

            <!-- Clear Filters Button -->
            <div>
              <Button variant="outline" @click="clearFilters" class="w-full">
                Limpar Filtros
              </Button>
            </div>

            <!-- Create Order Button -->
            <div>
              <CreateTravelOrderModal ref="createModalRef" :on-success="refreshData" />
            </div>
          </div>
        </div>

        <!-- Travel Orders Table -->
        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Minhas Viagens
            </h3>

            <div v-if="loading" class="text-center py-8">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
              <p class="mt-2 text-gray-500">Carregando viagens...</p>
            </div>

            <div v-else-if="travelOrders.length === 0" class="text-center py-8">
              <p class="text-gray-500">Nenhuma viagem encontrada.</p>
            </div>

            <div v-else class="overflow-hidden">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Destino</TableHead>
                    <TableHead>Data de Ida</TableHead>
                    <TableHead>Data de Volta</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Valor Total</TableHead>
                    <TableHead class="text-right">Ações</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow v-for="order in travelOrders" :key="order.id" class="hover:bg-gray-50 cursor-pointer"
                    @click="viewDetails(order.id)">
                    <TableCell class="font-medium">
                      {{ order.destination }}
                    </TableCell>
                    <TableCell>
                      {{ formatDate(order.departure_date) }}
                    </TableCell>
                    <TableCell>
                      {{ formatDate(order.return_date) }}
                    </TableCell>
                    <TableCell>
                      <Badge :variant="getStatusBadgeVariant(order.status)">
                        {{ order.status }}
                      </Badge>
                    </TableCell>
                    <TableCell>
                      {{ formatCurrency(order.price) }}
                    </TableCell>
                    <TableCell class="text-right">
                      <Button @click.stop="viewDetails(order.id)" variant="outline" size="sm">
                        Ver Detalhes
                      </Button>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
