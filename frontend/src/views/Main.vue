<script lang="ts" setup>
import { ref, onMounted, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import {
  getTravelOrders,
  type TravelOrder,
  type TravelOrderFilters,
  type TravelOrderStatus,
} from "@/services/travel-orders";
import { useAuth } from "@/composables/useAuth";
import { toast } from "vue-sonner";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { DateRangePicker } from "@/components/ui/date-range-picker";
import CreateTravelOrderModal from "@/components/CreateTravelOrderModal.vue";


const TravelOrderNotificationTypes = {
  TRAVEL_ORDER_APPROVED: "App\\Notifications\\TravelOrderApproved",
  TRAVEL_ORDER_CANCELLED: "App\\Notifications\\TravelOrderCancelled",
} as const;

type TravelOrderNotificationTypes = typeof TravelOrderNotificationTypes[keyof typeof TravelOrderNotificationTypes];

const router = useRouter();
const route = useRoute();
const { user, logout, isAdmin } = useAuth();
const travelOrders = ref<TravelOrder[]>([]);
const loading = ref(true);
const createModalRef = ref<InstanceType<typeof CreateTravelOrderModal> | null>(
  null
);

const filters = ref({
  status: (route.query.status as string) || "",
  destination: (route.query.destination as string) || "",
  dateRange: {
    start: route.query.start_date
      ? new Date(route.query.start_date as string)
      : null,
    end: route.query.end_date
      ? new Date(route.query.end_date as string)
      : null,
  },
});

const statusOptions = [
  { label: "Todos os Status", value: "all" },
  { label: "Pendente", value: "pendente" },
  { label: "Aprovado", value: "aprovado" },
  { label: "Cancelado", value: "cancelado" },
];

const updateQueryString = () => {
  const query: Record<string, string> = {};

  if (filters.value.status) {
    query.status = filters.value.status;
  }
  if (filters.value.destination) {
    query.destination = filters.value.destination;
  }
  if (filters.value.dateRange.start) {
    query.start_date = filters.value.dateRange.start
      .toISOString()
      .split("T")[0];
  }
  if (filters.value.dateRange.end) {
    query.end_date = filters.value.dateRange.end
      .toISOString()
      .split("T")[0];
  }

  router.push({ query });
};

watch(
  () => [
    filters.value.status,
    filters.value.destination,
    filters.value.dateRange,
  ],
  () => {
    updateQueryString();
    fetchTravelOrders();
  },
  { deep: true }
);

watch(
  () => route.query,
  (newQuery) => {
    filters.value.status = (newQuery.status as string) || "";
    filters.value.destination = (newQuery.destination as string) || "";
    filters.value.dateRange = {
      start: newQuery.start_date
        ? new Date(newQuery.start_date as string)
        : null,
      end: newQuery.end_date
        ? new Date(newQuery.end_date as string)
        : null,
    };
    fetchTravelOrders();
  },
  { immediate: false }
);

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString("pt-BR");
};

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(amount);
};

const getStatusBadgeVariant = (status: string) => {
  switch (status.toLowerCase()) {
    case "aprovado":
      return "default";
    case "pendente":
      return "secondary";
    case "cancelado":
      return "destructive";
    default:
      return "outline";
  }
};

const viewDetails = (orderId: string) => {
  router.push(`/travel-order/${orderId}`);
};

const fetchTravelOrders = async () => {
  loading.value = true;
  try {
    const filterParams: TravelOrderFilters = {};

    if (filters.value.status) {
      filterParams.status =
        filters.value.status !== "all" ? filters.value.status : "";
    }
    if (filters.value.destination) {
      filterParams.destination = filters.value.destination;
    }
    if (filters.value.dateRange.start) {
      filterParams.start_date = filters.value.dateRange.start
        .toISOString()
        .split("T")[0];
    }
    if (filters.value.dateRange.end) {
      filterParams.end_date = filters.value.dateRange.end
        .toISOString()
        .split("T")[0];
    }

    const response = await getTravelOrders(filterParams);
    travelOrders.value = response.data;

    // Toast com número de resultados (apenas quando há filtros aplicados)
    const hasFilters =
      filterParams.status ||
      filterParams.destination ||
      filterParams.start_date ||
      filterParams.end_date;
    if (hasFilters) {
      const count = response.data.length;
      toast.info(
        `${count} viage${count !== 1 ? "ns" : "m"} encontrada${count !== 1 ? "s" : ""
        }`
      );
    }
  } catch (error) {
    console.error("Failed to fetch travel orders:", error);
    toast.error("Erro ao carregar viagens");
  } finally {
    loading.value = false;
  }
};

const refreshData = async () => {
  await fetchTravelOrders();
  toast.success("Dados atualizados!");
};

const clearFilters = () => {
  filters.value = {
    status: "",
    destination: "",
    dateRange: { start: null, end: null },
  };
  toast.success("Filtros limpos com sucesso!");
};

const echo = window.Echo;
onMounted(async () => {
  if (user.value) {
    const userId = user.value.id || '';
    echo.private('App.Models.User.' + userId)
      .notification((notification: {
        type: TravelOrderNotificationTypes;
        amount: number;
        id: string;
        status: TravelOrderStatus;
      }) => {
        if (notification.type === TravelOrderNotificationTypes.TRAVEL_ORDER_APPROVED) {
          toast.success("Seu pedido foi aprovado!", {
            description: `Parabéns, seu pedido de viagem com budget de ${Intl.NumberFormat("pt-BR", {
              style: "currency",
              currency: "BRL",
            }).format(notification.amount)
              } foi aprovado.`,
            duration: 5000,
            action: {
              label: "Ver detalhes",
              onClick: () => {
                viewDetails(notification.id);
              },
            },
          });
        }
        if (notification.type === TravelOrderNotificationTypes.TRAVEL_ORDER_CANCELLED) {
          toast.error("Sua solicitação foi cancelada.", {
            description: `Seu pedido de viagem #${notification.id} foi cancelado. `,
            duration: 5000,
            action: {
              label: "Ver detalhes",
              onClick: () => {
                viewDetails(notification.id);
              },
            },
          });
        }
      });

  }
  await fetchTravelOrders();
});
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow">
      <nav class="relative z-20 flex justify-between items-center p-6 md:px-12">
        <div class="flex items-center space-x-2">
          <div class="w-8 h-8 bg-sky-500 text-sky-100 backdrop-blur-sm rounded-lg flex items-center justify-center">
            <svg class="size-5 text-sky-100" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
              viewBox="0 0 24 24">
              <path fill="currentColor"
                d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12c5.16-1.26 9-6.45 9-12V5zm0 4.68c.5 0 .95.43.95.95v3.48L18 13.26v1.27l-5.05-1.58v3.47l1.26.95v.95L12 17.68l-2.21.64v-.95l1.26-.95v-3.47L6 14.53v-1.27l5.05-3.15V6.63c0-.52.45-.95.95-.95" />
            </svg>
          </div>
          <span class="text-sky-500 font-bold text-xl">Onfly</span>
        </div>

        <div class="flex space-x-3">
          <template v-if="user">
            <span class="text-sky-500 font-medium">Olá, {{ user.name }}</span>
            <Button @click="logout" variant="outline" size="sm" class="gap-2 hover:text-white">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path fill="currentColor"
                  d="M5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h7v2H5v14h7v2zm11-4l-1.375-1.45l2.55-2.55H9v-2h8.175l-2.55-2.55L16 7l5 5z" />
              </svg>
              Logout
            </Button>
          </template>
        </div>
      </nav>
    </header>
    <div class="pl-8 pt-8">
      <h1 class="text-3xl font-bold text-gray-900">Sistema de Viagens</h1>
      <p v-if="user" class="text-sm text-gray-600 mt-1">
        Bem-vindo, {{ user?.name }}
        <Badge v-if="isAdmin" variant="secondary" class="ml-2">Admin</Badge>
      </p>
    </div>
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
          <div class="flex flex-wrap gap-4 items-end">
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

            <div class="flex-1 min-w-[200px]">
              <Label for="destination-filter" class="text-sm font-medium">Destino</Label>
              <Input id="destination-filter" v-model="filters.destination" type="text"
                placeholder="Filtrar por destino..." class="w-full" />
            </div>

            <div class="flex-1 min-w-[250px]">
              <Label class="text-sm font-medium">Período</Label>
              <DateRangePicker v-model="filters.dateRange" placeholder="Selecione o período" />
            </div>

            <div>
              <Button variant="outline" @click="clearFilters" class="w-full">
                Limpar Filtros
              </Button>
            </div>

            <div>
              <CreateTravelOrderModal ref="createModalRef" :on-success="refreshData" />
            </div>
          </div>
        </div>

        <div class="bg-white shadow rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Minhas Viagens
            </h3>

            <div v-if="loading" class="text-center py-8">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
              <p class="mt-2 text-gray-500">
                Carregando viagens...
              </p>
            </div>

            <div v-else-if="travelOrders.length === 0" class="text-center py-8">
              <p class="text-gray-500">
                Nenhuma viagem encontrada.
              </p>
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
                      {{
                        formatDate(order.departure_date)
                      }}
                    </TableCell>
                    <TableCell>
                      {{ formatDate(order.return_date) }}
                    </TableCell>
                    <TableCell>
                      <Badge :variant="getStatusBadgeVariant(
                        order.status
                      )
                        " class="capitalize">
                        {{ order.status }}
                      </Badge>
                    </TableCell>
                    <TableCell>
                      {{ formatCurrency(order.price) }}
                    </TableCell>
                    <TableCell class="text-right">
                      <Button @click.stop="
                        viewDetails(order.id)
                        " variant="outline" size="sm" class="hover:text-white">
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
