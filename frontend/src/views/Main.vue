<script lang="ts" setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { getTravelOrders } from '@/services/travel-orders'
import { useAuth } from '@/composables/useAuth'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Badge } from '@/components/ui/badge'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table'
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
const { user, logout, isAdmin } = useAuth()
const travelOrders = ref<TravelOrder[]>([])
const loading = ref(true)
const searchTerm = ref('')
const createModalRef = ref<InstanceType<typeof CreateTravelOrderModal> | null>(null)

const filteredOrders = computed(() => {
  if (!searchTerm.value) return travelOrders.value

  return travelOrders.value.filter(order =>
    order.destination.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
    order.status.toLowerCase().includes(searchTerm.value.toLowerCase())
  )
})

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
    case 'approved':
      return 'default' // green
    case 'pending':
      return 'secondary' // yellow  
    case 'cancelled':
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

const refreshData = async () => {
  loading.value = true
  try {
    const response = await getTravelOrders()
    travelOrders.value = response.data
  } catch (error) {
    toast.error('Erro ao carregar viagens')
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  try {
    const response = await getTravelOrders()
    travelOrders.value = response.data
  } catch (error) {
    toast.error('Erro ao carregar viagens')
  } finally {
    loading.value = false
  }
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
        <!-- Actions Bar -->
        <div class="flex justify-between items-center mb-6">
          <div class="flex-1 max-w-lg">
            <Input v-model="searchTerm" type="text" placeholder="Buscar por destino ou status..." class="w-full" />
          </div>
          <CreateTravelOrderModal ref="createModalRef" :on-success="refreshData" />
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

            <div v-else-if="filteredOrders.length === 0" class="text-center py-8">
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
                  <TableRow v-for="order in filteredOrders" :key="order.id" class="hover:bg-gray-50 cursor-pointer"
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
