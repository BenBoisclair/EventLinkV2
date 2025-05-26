<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import Input from "@/Components/Forms/Input.vue";
import InputError from "@/Components/Forms/InputError.vue";
import InputLabel from "@/Components/Forms/InputLabel.vue";
import Button from "@/Components/UI/Button.vue";
import Checkbox from "@/Components/UI/Checkbox.vue";
import { ref } from "vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: import.meta.env.DEV ? "organiser@example.com" : "",
    password: import.meta.env.DEV ? "password" : "",
    remember: false,
});

const processing = ref(false);

const submit = () => {
    processing.value = true;
    form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : "",
    })).post(route("login"), {
        onSuccess: () => {
            // Redirect handled by Laravel
        },
        onError: (errors) => {
            form.errors = errors;
            processing.value = false;
        },
        onFinish: () => {
            form.reset("password");
            if (processing.value) {
                processing.value = false;
            }
        },
    });
};
</script>

<template>
    <Head title="Log in" />
    <div class="w-full min-h-screen dark:bg-dark-background">
        <div class="flex flex-col min-h-screen lg:flex-row">
            <div
                class="hidden dark:bg-dark-primary bg-primary lg:flex lg:w-1/2"
            ></div>
            <div
                class="dark:bg-dark-surface flex w-full items-center justify-center px-6 lg:w-[50%]"
            >
                <div class="w-full max-w-[400px] space-y-8">
                    <!-- Logo -->
                    <div>
                        <img
                            :src="'/logo-alternative.svg'"
                            alt="Logo"
                            class="w-auto h-12 pr-8"
                        />
                        <h2
                            class="mt-6 text-3xl font-bold tracking-tight text-gray-900 dark:text-dark-text-primary"
                        >
                            Sign in to your account
                        </h2>
                    </div>
                    <!-- Status Message -->
                    <div
                        v-if="status"
                        class="mb-4 text-sm font-medium text-green-600"
                    >
                        {{ status }}
                    </div>
                    <!-- Login Form -->
                    <form class="mt-8 space-y-6" @submit.prevent="submit">
                        <div class="space-y-4 rounded-md">
                            <div>
                                <InputLabel for="email" value="Email address" />
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="block w-full mt-1 dark:bg-dark-surface-elevated dark:text-dark-text-primary dark:border-dark-border"
                                    required
                                    autofocus
                                    autocomplete="username"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.email"
                                />
                            </div>
                            <div class="mt-4">
                                <InputLabel for="password" value="Password" />
                                <Input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="block w-full mt-1 dark:bg-dark-surface-elevated dark:text-dark-text-primary dark:border-dark-border"
                                    required
                                    autocomplete="current-password"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.password"
                                />
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <div class="flex items-center">
                                <Checkbox
                                    id="remember-me"
                                    v-model:checked="form.remember"
                                    name="remember"
                                />
                                <span class="dark:text-dark-text-secondary ms-2"
                                    >Remember me</span
                                >
                            </div>
                            <div class="text-sm">
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="font-medium dark:text-dark-primary dark:hover:text-dark-primary-dimmed text-primary hover:text-primary/80"
                                >
                                    Forgot your password?
                                </Link>
                            </div>
                        </div>
                        <!-- Error Messages -->
                        <div
                            v-if="
                                form.hasErrors &&
                                !form.errors.email &&
                                !form.errors.password
                            "
                            class="space-y-1 text-sm text-red-600 dark:text-dark-status-red"
                        >
                            <p v-for="(error, key) in form.errors" :key="key">
                                {{ error }}
                            </p>
                        </div>
                        <div>
                            <Button
                                :text="processing ? 'Signing in...' : 'Sign in'"
                                variant="primary"
                                class="w-full"
                                :disabled="processing"
                                @click="submit"
                            />
                        </div>
                    </form>
                    <p
                        class="mt-2 text-sm text-center text-gray-600 dark:text-dark-text-secondary"
                    >
                        Don't have an account?
                        <Link
                            :href="route('register')"
                            class="font-medium dark:text-dark-primary dark:hover:text-dark-primary-dimmed text-primary hover:text-primary/80"
                        >
                            Sign up
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
