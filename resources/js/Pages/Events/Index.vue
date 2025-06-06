<script setup lang="ts">
import EventCard from "@/Components/Events/EventCard.vue";
import Button from "@/Components/UI/Button.vue";
import Container from "@/Components/UI/Container.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { useEventStore } from "@/stores/eventStore";
import type { EventType, TeamEventData } from "@/types/event";
import { router } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { route } from "ziggy-js";
import { storeToRefs } from "pinia";

const props = defineProps<{
    events: EventType[];
    team_event_data: TeamEventData | null;
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
                                <span
                                    v-if="props.team_event_data"
                                    class="ml-2 text-sm font-normal text-gray-500 dark:text-dark-text-secondary"
                                >
                                    ({{ props.team_event_data.event_count }}/{{
                                        props.team_event_data
                                            .has_unlimited_events
                                            ? "∞"
                                            : props.team_event_data.event_limit
                                    }})
                                </span>
                            </h1>
                            <p
                                class="text-sm dark:text-dark-text-secondary text-text-muted"
                            >
                                View and manage your created events.
                                <span
                                    v-if="props.team_event_data?.plan"
                                    class="font-medium"
                                >
                                    {{ props.team_event_data.plan.name }} Plan
                                </span>
                                <br v-if="props.team_event_data?.plan" />
                                <span
                                    v-if="
                                        props.team_event_data
                                            ?.has_unlimited_events
                                    "
                                    class="text-green-600 dark:text-green-400"
                                >
                                    Unlimited events available.
                                </span>
                                <span
                                    v-else-if="
                                        props.team_event_data &&
                                        props.team_event_data.remaining_slots >
                                            0
                                    "
                                >
                                    You have
                                    {{ props.team_event_data.remaining_slots }}
                                    event slot{{
                                        props.team_event_data
                                            .remaining_slots === 1
                                            ? ""
                                            : "s"
                                    }}
                                    remaining.
                                </span>
                                <span
                                    v-else-if="
                                        props.team_event_data &&
                                        props.team_event_data
                                            .remaining_slots === 0
                                    "
                                    class="text-orange-600 dark:text-orange-400"
                                >
                                    You have reached your event limit.
                                </span>
                            </p>
                        </div>
                        <Button
                            @click="navigateToCreateEvent"
                            variant="primary"
                            v-if="
                                props.team_event_data?.can_create_event !==
                                false
                            "
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
                                :variant="
                                    props.team_event_data?.can_create_event !==
                                    false
                                        ? 'outline-primary'
                                        : 'outline'
                                "
                                :disabled="
                                    props.team_event_data?.can_create_event ===
                                    false
                                "
                                :text="
                                    props.team_event_data?.can_create_event ===
                                    false
                                        ? 'Event Limit Reached'
                                        : 'Create Your First Event'
                                "
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
