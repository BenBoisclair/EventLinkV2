<script setup lang="ts">
import EventCard from "@/Components/Events/EventCard.vue";
import Button from "@/Components/UI/Button.vue";
import Container from "@/Components/UI/Container.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { useEventStore } from "@/stores/eventStore";
import type { EventType } from "@/types/event";
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
                        <Button
                            @click="navigateToCreateEvent"
                            variant="primary"
                            text="Create Event"
                        >
                            <span class="flex items-center gap-2">
                                Create Event
                                <svg
                                    class="w-4 h-4"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v16m8-8H4"
                                    />
                                </svg>
                            </span>
                        </Button>
                    </div>

                    <!-- Separator -->
                    <div
                        class="mb-8 border-t border-gray-200 dark:border-dark-border"
                    ></div>

                    <div
                        v-if="storeEvents.length === 0"
                        class="flex items-center justify-center py-16"
                    >
                        <div class="text-center">
                            <p
                                class="mb-4 text-gray-500 dark:text-dark-text-secondary"
                            >
                                No events created yet. Create your first event
                                to get started.
                            </p>
                            <Button
                                @click="navigateToCreateEvent"
                                variant="outline-primary"
                                text="Create Your First Event"
                            />
                        </div>
                    </div>
                    <div
                        v-else
                        class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <EventCard
                            v-for="event in props.events"
                            :key="event.id"
                            :event="event"
                            @manage="navigateToEvent"
                        />
                    </div>
                </Container>
            </div>
        </div>
    </DashboardLayout>
</template>
