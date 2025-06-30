<script lang="ts" setup>
import { loginFormValidationSchema } from "@/schemas/login-schema";
import { useForm } from "vee-validate";
import { login as loginApi, me } from "@/services/auth/index";
import { useAuth } from "@/composables/useAuth";
import { toast } from "vue-sonner";
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { useRouter } from "vue-router";

const { login } = useAuth();

const form = useForm({
    validationSchema: loginFormValidationSchema,
});
const router = useRouter();
const goToLanding = () => {
    router.push("/");
};
const onSubmit = form.handleSubmit(async (values) => {
    try {
        const response = await loginApi({
            email: values.email,
            password: values.password,
        });
        if (response.token) {
            localStorage.setItem("token", response.token);
            const userData = await me();
            login(userData, response.token);
            router.push("/dashboard");
        }
        toast.success("Login realizado com sucesso!");
    } catch (error: any) {
        toast.error(
            error.response?.data?.message ||
                "Erro ao fazer login. Verifique suas credenciais."
        );
    }
});
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
                    @click="goToLanding"
                    class="w-8 h-8 bg-sky-500 text-sky-100 backdrop-blur-sm rounded-lg flex items-center justify-center hover:cursor-pointer"
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
        </nav>
        <div
            class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow w-full"
        >
            <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">
                Fazer Login
            </h1>
            <form @submit="onSubmit" class="space-y-6">
                <FormField name="email" v-slot="{ componentField }">
                    <FormItem class="space-y-1">
                        <FormLabel
                            class="block text-sm font-medium text-gray-700"
                        >
                            Email
                        </FormLabel>
                        <FormControl>
                            <Input
                                type="email"
                                placeholder="Digite seu e-mail"
                                v-bind="componentField"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                            />
                        </FormControl>
                        <FormDescription class="text-xs text-gray-500">
                            Digite um e-mail válido.
                        </FormDescription>
                        <FormMessage class="text-xs text-red-600" />
                    </FormItem>
                </FormField>

                <FormField name="password" v-slot="{ componentField }">
                    <FormItem class="space-y-1">
                        <FormLabel
                            class="block text-sm font-medium text-gray-700"
                        >
                            Senha
                        </FormLabel>
                        <FormControl>
                            <Input
                                type="password"
                                placeholder="Digite sua senha"
                                v-bind="componentField"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                            />
                        </FormControl>
                        <FormDescription class="text-xs text-gray-500">
                            Sua senha deve conter pelo menos 8 caracteres.
                        </FormDescription>
                        <FormMessage class="text-xs text-red-600" />
                    </FormItem>
                </FormField>

                <Button
                    variant="default"
                    type="submit"
                    class="w-full py-2 px-4 font-semibold rounded focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"
                >
                    Fazer Login
                </Button>

                <div class="text-center">
                    <RouterLink
                        to="/register"
                        class="text-sky-500 hover:text-sky-800 text-sm"
                    >
                        Não possui uma conta? Cadastre-se
                    </RouterLink>
                </div>
            </form>
        </div>

        <div class="mt-16 text-center">
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
</template>
