<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import CreateTeamForm from '@/Pages/Teams/Partials/CreateTeamForm.vue';
</script>

<template>
    <DashboardLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Team
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <CreateTeamForm />
            </div>
        </div>
    </DashboardLayout>
</template>
