<script setup>
import { Head } from "@inertiajs/vue3";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";

defineProps({
    terms: String,
});
</script>

<template>
    <Head title="Terms of Service" />

    <div class="font-sans antialiased text-gray-900">
        <div class="pt-4 bg-gray-100">
            <div class="flex flex-col items-center min-h-screen pt-6 sm:pt-0">
                <div>
                    <AuthenticationCardLogo />
                </div>

                <div
                    class="w-full p-6 mt-6 overflow-hidden prose bg-white shadow-md sm:max-w-2xl sm:rounded-lg"
                    v-html="terms"
                />
            </div>
        </div>
    </div>
</template>
