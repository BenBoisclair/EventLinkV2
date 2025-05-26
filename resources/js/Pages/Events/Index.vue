<script setup lang="ts">
import CreateEventButton from "@/Components/Events/CreateEventButton.vue";
import Container from "@/Components/UI/Container.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { useEventStore } from "@/stores/eventStore";
import type { EventType } from "@/types/event";
import { formatDate } from "@/utils/date";
import { router } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { route } from "ziggy-js";
import { storeToRefs } from "pinia";

const props = defineProps<{
    events: EventType[];
}>();

const eventStore = useEventStore();
const { events: storeEvents } = storeToRefs(eventStore);

const navigateToEvent = (eventId: string | number) => {
    router.visit(route("organiser.events.select", { event: eventId }));
};

const navigateToCreateEvent = () => {
    router.visit(route("organiser.events.create"));
};

onMounted(() => {
    eventStore.setEvents(props.events);
});
</script>

<template>
    <DashboardLayout>
        <div class="dark:bg-dark-background flex flex-1 bg-[#F1F5F9]">
            <div class="flex-1 pt-5 overflow-y-scroll">
                <Container>
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1
                                class="text-2xl font-bold dark:text-dark-primary text-primary"
                            >
                                Your Events
                            </h1>
                            <p
                                class="text-sm dark:text-dark-text-secondary text-text-muted"
                            >
                                View and manage your created events.
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="storeEvents.length === 0"
                        class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <CreateEventButton @create="navigateToCreateEvent" />
                    </div>
                    <div
                        v-else
                        class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <div
                            v-for="event in props.events"
                            :key="event.id"
                            @click="navigateToEvent(event.id)"
                            class="bg-surface dark:bg-dark-surface dark:border-dark-border dark:hover:border-dark-primary h-[200px] cursor-pointer rounded-lg border p-6 transition-shadow hover:border-primary dark:border"
                        >
                            <h2
                                class="mb-2 text-xl font-medium dark:text-dark-primary text-primary"
                            >
                                {{ event.name }}
                            </h2>
                            <p
                                class="mb-4 dark:text-dark-text-secondary text-text-muted"
                            >
                                {{ event.description || "No description" }}
                            </p>
                            <div
                                class="flex items-center text-sm text-gray-500 dark:text-dark-text-tertiary"
                            >
                                <span>{{ formatDate(event.start_date) }}</span>
                                <span
                                    v-if="
                                        formatDate(event.start_date) &&
                                        formatDate(event.end_date)
                                    "
                                    class="mx-2"
                                    >-</span
                                >
                                <span>{{ formatDate(event.end_date) }}</span>
                            </div>
                        </div>
                        <CreateEventButton @create="navigateToCreateEvent" />
                    </div>
                </Container>
            </div>
        </div>
    </DashboardLayout>
</template>
