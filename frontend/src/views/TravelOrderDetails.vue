<script lang="ts" setup>
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import {
    getTravelOrderById,
    approveTravelOrder,
    cancelTravelOrder,
    type TravelOrderStatus,
} from "@/services/travel-orders";
import { useAuth } from "@/composables/useAuth";
import { useGlobalLoading } from "@/composables/useGlobalLoading";
import { toast } from "vue-sonner";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

interface TravelOrder {
    id: number;
    destination: string;
    departure_date: string;
    return_date: string;
    status: TravelOrderStatus;
    price: number;
    description?: string;
    accommodation?: string;
    transportation?: string;
    created_at: string;
    updated_at: string;
}

const route = useRoute();
const router = useRouter();
const { isAdmin, user,logout} = useAuth();
const { showGlobalLoading, hideGlobalLoading } = useGlobalLoading();
const travelOrder = ref<TravelOrder | null>(null);
const loading = ref(true);
const actionLoading = ref(false);

const orderId = computed(() => Number(route.params.id));

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString("pt-BR", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
    }).format(amount);
};

const getStatusBadgeVariant = (status: TravelOrderStatus) => {
    switch (status) {
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

const canApprove = computed(() => {
    return travelOrder.value?.status !== "aprovado";
});

const canCancel = computed(() => {
    return travelOrder.value?.status === "pendente";
});

const approveOrder = async () => {
    if (!travelOrder.value) return;

    showGlobalLoading("Aprovando viagem...");
    actionLoading.value = true;
    try {
        await approveTravelOrder(travelOrder.value.id);
        travelOrder.value.status = "aprovado";
        toast.success("Viagem aprovada com sucesso!");
    } catch (error: any) {
        toast.error(error.response?.data?.message || "Erro ao aprovar viagem");
    } finally {
        actionLoading.value = false;
        hideGlobalLoading();
    }
};

const cancelOrder = async () => {
    if (!travelOrder.value) return;

    showGlobalLoading("Cancelando viagem...");
    actionLoading.value = true;
    try {
        await cancelTravelOrder(travelOrder.value.id);
        travelOrder.value.status = "cancelado";
        toast.success("Viagem cancelada com sucesso!");
    } catch (error: any) {
        console.error("Failed to cancel order:", error);
        toast.error(error.response?.data?.message || "Erro ao cancelar viagem");
    } finally {
        actionLoading.value = false;
        hideGlobalLoading();
    }
};

const goBack = () => {
    router.push("/dashboard");
};
const echo = window.Echo;

onMounted(async () => {
    if (orderId.value) {
        const approvalChannelNotification = `order.approved.${orderId.value}`;
        console.log(approvalChannelNotification);
        echo.private(approvalChannelNotification)
            .listen("TravelOrderApproved", (e) => {
                toast.info("Pedido aprovado!");
                console.log(e);
            })
            .error((error) => {
                console.error(
                    `Error subscribing to channel ${channelName}:`,
                    error
                );
                subscriptionError.value = error.message || "Unknown error";
            });
    }
    try {
        const response = await getTravelOrderById(orderId.value);
        travelOrder.value = response.data;
    } catch (error) {
        console.error("Failed to fetch travel order:", error);
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow">
            <nav
                class="relative z-20 flex justify-between items-center p-6 md:px-12"
            >
                <div class="flex items-center space-x-2">
                    <div
                        class="w-8 h-8 bg-sky-500 text-sky-100 backdrop-blur-sm rounded-lg flex items-center justify-center"
                    >
                        <svg
                            class="size-5 text-sky-100"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill="currentColor"
                                d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12c5.16-1.26 9-6.45 9-12V5zm0 4.68c.5 0 .95.43.95.95v3.48L18 13.26v1.27l-5.05-1.58v3.47l1.26.95v.95L12 17.68l-2.21.64v-.95l1.26-.95v-3.47L6 14.53v-1.27l5.05-3.15V6.63c0-.52.45-.95.95-.95"
                            />
                        </svg>
                    </div>
                    <span class="text-sky-500 font-bold text-xl">Onfly</span>
                </div>

                <div class="flex space-x-3">
                    <template v-if="!user">
                        <Button @click="goToLogin" variant="outline" size="sm">
                            Login
                        </Button>
                        <Button @click="goToRegister" size="sm">
                            Cadastrar
                        </Button>
                    </template>
                    <template v-else>
                        <span class="text-sky-500 font-medium"
                            >Olá, {{ user.name }}</span
                        >
                        <Button
                            @click="logout"
                            variant="outline"
                            size="sm"
                            class="gap-2 hover:text-white"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    fill="currentColor"
                                    d="M5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h7v2H5v14h7v2zm11-4l-1.375-1.45l2.55-2.55H9v-2h8.175l-2.55-2.55L16 7l5 5z"
                                />
                            </svg>
                            Logout
                        </Button>
                    </template>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <div class="flex items-center pt-8 px-8">
            <Button
                @click="goBack"
                variant="ghost"
                class="mr-4 flex items-center gap-2"
            >
                <i class="pi pi-arrow-left size-4" /> Voltar
            </Button>
            <h1 class="text-3xl font-bold text-gray-900">Detalhes da Viagem</h1>
        </div>
        <main class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div v-if="loading" class="text-center py-8">
                    <div
                        class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"
                    ></div>
                    <p class="mt-2 text-gray-500">
                        Carregando detalhes da viagem...
                    </p>
                </div>

                <div v-else-if="!travelOrder" class="text-center py-8">
                    <p class="text-gray-500">Viagem não encontrada.</p>
                </div>

                <div v-else class="space-y-6">
                    <Card>
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <div>
                                    <CardTitle class="text-2xl">{{
                                        travelOrder.destination
                                    }}</CardTitle>
                                    <CardDescription>
                                        Pedido #{{ travelOrder.id }} • Criado em
                                        {{ formatDate(travelOrder.created_at) }}
                                    </CardDescription>
                                </div>
                                <Badge
                                    class="capitalize"
                                    :variant="
                                        getStatusBadgeVariant(
                                            travelOrder.status
                                        )
                                    "
                                >
                                    {{ travelOrder.status }}
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <!-- Dates -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3
                                        class="text-sm font-medium text-gray-500 mb-1"
                                    >
                                        Data de Ida
                                    </h3>
                                    <p class="text-lg">
                                        {{
                                            formatDate(
                                                travelOrder.departure_date
                                            )
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <h3
                                        class="text-sm font-medium text-gray-500 mb-1"
                                    >
                                        Data de Volta
                                    </h3>
                                    <p class="text-lg">
                                        {{
                                            formatDate(travelOrder.return_date)
                                        }}
                                    </p>
                                </div>
                            </div>

                            <!-- Amount -->
                            <div>
                                <h3
                                    class="text-sm font-medium text-gray-500 mb-1"
                                >
                                    Valor Total
                                </h3>
                                <p class="text-3xl font-bold text-green-600">
                                    {{ formatCurrency(travelOrder.price) }}
                                </p>
                            </div>

                            <!-- Description -->
                            <div v-if="travelOrder.description">
                                <h3
                                    class="text-sm font-medium text-gray-500 mb-1"
                                >
                                    Descrição
                                </h3>
                                <p class="text-gray-900">
                                    {{ travelOrder.description }}
                                </p>
                            </div>

                            <!-- Additional Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div v-if="travelOrder.accommodation">
                                    <h3
                                        class="text-sm font-medium text-gray-500 mb-1"
                                    >
                                        Acomodação
                                    </h3>
                                    <p class="text-gray-900">
                                        {{ travelOrder.accommodation }}
                                    </p>
                                </div>
                                <div v-if="travelOrder.transportation">
                                    <h3
                                        class="text-sm font-medium text-gray-500 mb-1"
                                    >
                                        Transporte
                                    </h3>
                                    <p class="text-gray-900">
                                        {{ travelOrder.transportation }}
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Actions -->
                    <Card v-if="isAdmin">
                        <CardHeader>
                            <CardTitle>Ações Administrativas</CardTitle>
                            <CardDescription>
                                Gerencie o status desta viagem (apenas
                                administradores)
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="flex space-x-4">
                                <Button
                                    v-if="canApprove"
                                    @click="approveOrder"
                                    :disabled="actionLoading"
                                    class="bg-green-600 hover:bg-green-700"
                                >
                                    {{
                                        actionLoading
                                            ? "Aprovando..."
                                            : "Aprovar Viagem"
                                    }}
                                </Button>

                                <Button
                                    v-if="canCancel"
                                    @click="cancelOrder"
                                    :disabled="actionLoading"
                                    variant="destructive"
                                >
                                    {{
                                        actionLoading
                                            ? "Cancelando..."
                                            : "Cancelar Viagem"
                                    }}
                                </Button>

                                <Button
                                    v-if="!canApprove && !canCancel"
                                    disabled
                                    variant="outline"
                                >
                                    Nenhuma ação disponível
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Info for non-admin users -->
                    <Card v-if="!isAdmin">
                        <CardHeader>
                            <CardTitle>Status da Solicitação</CardTitle>
                            <CardDescription>
                                Acompanhe o status da sua solicitação de viagem
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="text-center py-4">
                                <Badge
                                    :variant="
                                        getStatusBadgeVariant(
                                            travelOrder?.status || ''
                                        )
                                    "
                                    class="text-lg px-4 py-2 capitalize"
                                >
                                    {{ travelOrder?.status }}
                                </Badge>
                                <p class="text-sm text-gray-500 mt-2">
                                    Aguarde a análise do administrador para
                                    alterações no status
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Timeline/History -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Histórico</CardTitle>
                            <CardDescription>
                                Acompanhe as mudanças desta viagem
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-2 h-2 bg-blue-600 rounded-full"
                                    ></div>
                                    <div>
                                        <p class="text-sm font-medium">
                                            Viagem criada
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{
                                                formatDate(
                                                    travelOrder.created_at
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    v-if="
                                        travelOrder.updated_at !==
                                        travelOrder.created_at
                                    "
                                    class="flex items-center space-x-3"
                                >
                                    <div
                                        class="w-2 h-2 bg-green-600 rounded-full"
                                    ></div>
                                    <div>
                                        <p class="text-sm font-medium">
                                            Última atualização
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{
                                                formatDate(
                                                    travelOrder.updated_at
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </main>
    </div>
</template>
