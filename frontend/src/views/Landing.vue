<script lang="ts" setup>
import { useRouter } from "vue-router";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { useAuth } from "@/composables/useAuth";

const router = useRouter();
const { user, logout } = useAuth();

const goToLogin = () => {
    router.push("/login");
};

const goToRegister = () => {
    router.push("/register");
};
const goToDashboard = () => {
    router.push("/dashboard");
};
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-br from-sky-50 via-sky-100 to-white flex flex-col"
    >
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
                    <Button @click="goToRegister" size="sm"> Cadastrar </Button>
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

        <div class="flex-1 flex items-center justify-center p-4">
            <div class="relative z-10 w-full max-w-6xl mx-auto">
                <div class="text-center text-sky-500 mb-12">
                    <div class="mb-8 flex gap-8 items-center justify-center">
                        <div>
                            <h1
                                class="text-5xl md:text-7xl font-bold mb-6 leading-tight"
                            >
                                <span class="block">Onfly</span>
                                <span
                                    class="block text-3xl md:text-4xl font-light text-sky-700"
                                    >Business Travel</span
                                >
                            </h1>
                        </div>
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 bg-sky-500 backdrop-blur-sm rounded-full mb-6"
                        >
                            <svg
                                class="w-10 h-10 text-sky-100"
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
                    </div>
                    <p class="text-lg text-sky-600 mb-12 max-w-2xl mx-auto">
                        Controle total sobre solicitações, aprovações e
                        orçamentos de viagem em uma plataforma intuitiva e
                        moderna
                    </p>
                </div>
                <template v-if="user">
                    <Button
                        @click="goToDashboard"
                        variant="default"
                        size="lg"
                        class="border-white/50 w-full my-8 backdrop-blur-sm"
                    >
                        Ir para o Dashboard
                    </Button>
                </template>
                <div class="grid md:grid-cols-3 gap-6 mb-12">
                    <Card
                        class="bg-zinc-50 backdrop-blur-sm border-white/20 text-sky-500"
                    >
                        <CardContent class="p-6 text-center">
                            <div
                                class="w-12 h-12 bg-sky-500 text-sky-100 rounded-lg flex items-center justify-center mx-auto mb-4"
                            >
                                <svg
                                    class="w-6 h-6"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        fill="currentColor"
                                        d="M7 5h15v16l-2.5-1.68L17 21l-2.5-1.68L12 21l-2.5-1.68L7 21zm11 4h-7v2h7zm-7 4v2h5v-2zM3 7c-.6 0-1 .4-1 1s.4 1 1 1h2V7zm-2 5c0-.6.4-1 1-1h3v2H2c-.6 0-1-.4-1-1m0 3c-.6 0-1 .4-1 1s.4 1 1 1h4v-2z"
                                    />
                                </svg>
                            </div>
                            <h3 class="font-semibold text-lg mb-2">
                                Solicitações Simples
                            </h3>
                            <p class="text-sky-700 text-sm">
                                Crie e gerencie solicitações de viagem de forma
                                rápida e intuitiva
                            </p>
                        </CardContent>
                    </Card>

                    <Card
                        class="bg-zinc-50 backdrop-blur-sm border text-sky-500"
                    >
                        <CardContent class="p-6 text-center">
                            <div
                                class="w-12 h-12 bg-sky-500 text-sky-100 rounded-lg flex items-center justify-center mx-auto mb-4"
                            >
                                <svg
                                    class="w-6 h-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <h3 class="font-semibold text-lg mb-2">
                                Aprovações Ágeis
                            </h3>
                            <p class="text-sky-700 text-sm">
                                Fluxo de aprovação automatizado com notificações
                                em tempo real
                            </p>
                        </CardContent>
                    </Card>

                    <Card
                        class="bg-zinc-50 backdrop-blur-sm border-white/20 text-sky-500"
                    >
                        <CardContent class="p-6 text-center">
                            <div
                                class="w-12 h-12 bg-sky-500 text-sky-100 rounded-lg flex items-center justify-center mx-auto mb-4"
                            >
                                <svg
                                    class="w-6 h-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                    />
                                </svg>
                            </div>
                            <h3 class="font-semibold text-lg mb-2">
                                Controle de Custos
                            </h3>
                            <p class="text-sky-700 text-sm">
                                Monitore e controle os gastos com viagens em
                                tempo real
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <div class="mt-16 text-center">
                    <div class="inline-flex items-center text-sky-600 text-xl">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="48"
                            height="48"
                            viewBox="0 0 48 48"
                        >
                            <defs>
                                <mask id="ipTLightning0">
                                    <path
                                        fill="#555555"
                                        stroke="#fff"
                                        stroke-linejoin="round"
                                        stroke-width="4"
                                        d="M19 4h18L26 18h15L17 44l5-19H8z"
                                    />
                                </mask>
                            </defs>
                            <path
                                fill="currentColor"
                                d="M0 0h48v48H0z"
                                mask="url(#ipTLightning0)"
                            />
                        </svg>
                        Feito por Henrique de Paula Rodrigues
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
