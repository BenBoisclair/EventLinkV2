<script setup lang="ts">
import { ref, computed } from "vue";
import { useEventStore } from "@/stores/eventStore";
import { router } from "@inertiajs/vue3";
import type { Event } from "@/types/event";

interface Props {
    currentEventId?: number;
    isCollapsed?: boolean;
}

const props = defineProps<Props>();
const eventStore = useEventStore();

const menu = ref(false);
const selectedEvent = computed(() => {
    return eventStore.events.find((event) => event.id === props.currentEventId);
});

const handleEventSelect = (eventId: number) => {
    router.visit(`/organiser/events/${eventId}/select`);
    menu.value = false;
};

const handleAllEventsClick = () => {
    router.visit("/organiser/events");
    menu.value = false;
};
</script>

<template>
    <v-menu
        v-model="menu"
        :close-on-content-click="false"
        location="bottom start"
        :offset="4"
        transition="slide-y-transition"
        width="280"
    >
        <template v-slot:activator="{ props: menuProps }">
            <button
                v-bind="menuProps"
                class="w-full flex items-center justify-between px-3 py-2.5 bg-surface dark:bg-dark-surface border border-gray-300 dark:border-dark-border rounded-lg hover:border-[#193CB8] dark:hover:border-dark-primary transition-colors"
                :class="{ 'opacity-0': isCollapsed }"
            >
                <div class="flex items-center gap-2 text-left">
                    <v-icon
                        icon="$calendarOutline"
                        size="20"
                        class="text-gray-500 dark:text-dark-text-secondary"
                    />
                    <span
                        class="text-sm font-medium text-gray-900 truncate dark:text-dark-text-primary"
                    >
                        {{ selectedEvent?.name || "Select an event" }}
                    </span>
                </div>
                <v-icon
                    :icon="menu ? '$chevronUp' : '$chevronDown'"
                    size="20"
                    class="flex-shrink-0 text-gray-400 dark:text-dark-text-secondary"
                />
            </button>
        </template>

        <v-list
            class="py-1 border border-gray-200 rounded-lg shadow-lg bg-surface dark:bg-dark-surface dark:border-dark-border"
        >
            <!-- Events Header -->
            <div
                class="px-4 py-2 border-b border-gray-200 dark:border-dark-border"
            >
                <h3
                    class="text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-dark-text-secondary"
                >
                    Your Events
                </h3>
            </div>

            <!-- Events List -->
            <div class="overflow-y-auto max-h-64">
                <div
                    v-for="event in eventStore.events"
                    :key="event.id"
                    @click="handleEventSelect(event.id)"
                    class="relative transition-all cursor-pointer"
                    :class="{
                        'bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 border-l-4 border-[#193CB8] dark:border-blue-400':
                            event.id === currentEventId,
                        'hover:bg-gray-50 dark:hover:bg-gray-800 border-l-4 border-transparent':
                            event.id !== currentEventId,
                    }"
                >
                    <v-list-item class="!bg-transparent">
                        <v-list-item-title
                            class="text-sm font-medium"
                            :class="{
                                'text-[#193CB8] dark:text-blue-400':
                                    event.id === currentEventId,
                                'text-gray-900 dark:text-gray-100':
                                    event.id !== currentEventId,
                            }"
                        >
                            {{ event.name }}
                        </v-list-item-title>
                        <v-list-item-subtitle
                            class="text-xs"
                            :class="{
                                'text-blue-600 dark:text-blue-300':
                                    event.id === currentEventId,
                                'text-gray-500 dark:text-gray-400':
                                    event.id !== currentEventId,
                            }"
                        >
                            {{ event.venue || "No venue set" }}
                        </v-list-item-subtitle>
                    </v-list-item>
                </div>
            </div>

            <!-- No Events State -->
            <div
                v-if="eventStore.events.length === 0"
                class="px-4 py-8 text-center"
            >
                <v-icon
                    icon="$calendarBlankOutline"
                    size="48"
                    class="mb-2 text-gray-300 dark:text-dark-text-secondary"
                />
                <p class="text-sm text-gray-500 dark:text-dark-text-secondary">
                    No events yet
                </p>
            </div>

            <!-- All Events Link -->
            <v-divider class="my-1" />
            <v-list-item
                @click="handleAllEventsClick"
                class="cursor-pointer hover:bg-gray-50 dark:hover:bg-dark-surface-elevated"
            >
                <template v-slot:prepend>
                    <v-icon
                        icon="$viewGridOutline"
                        size="18"
                        class="text-gray-500 dark:text-dark-text-secondary"
                    />
                </template>
                <v-list-item-title
                    class="text-sm font-medium text-gray-900 dark:text-dark-text-primary"
                >
                    View All Events
                </v-list-item-title>
                <template v-slot:append>
                    <v-icon
                        icon="$arrowRight"
                        size="16"
                        class="text-gray-400 dark:text-dark-text-secondary"
                    />
                </template>
            </v-list-item>
        </v-list>
    </v-menu>
</template>
