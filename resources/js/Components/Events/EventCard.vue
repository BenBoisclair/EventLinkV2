<script setup lang="ts">
import type { EventType } from "@/types/event";
import { computed } from "vue";
import { formatDate } from "@/utils/date";
import Pill from "@/Components/UI/Pill.vue";
import Button from "@/Components/UI/Button.vue";

interface Props {
    event: EventType;
}

interface Emits {
    (e: "manage", eventId: number): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const isUpcoming = computed(() => {
    if (!props.event.start_date) return false;
    return new Date(props.event.start_date) > new Date();
});

const isOngoing = computed(() => {
    if (!props.event.start_date || !props.event.end_date) return false;
    return (
        new Date(props.event.start_date) <= new Date() &&
        new Date(props.event.end_date) >= new Date()
    );
});

const handleManage = () => {
    emit("manage", props.event.id);
};
</script>

<template>
    <div
        class="relative overflow-hidden transition-all duration-300 bg-white rounded-lg shadow-lg group hover:-translate-y-2 hover:shadow-xl dark:bg-dark-surface"
    >
        <!-- Banner Section -->
        <div class="relative h-48 overflow-hidden">
            <img
                v-if="event.banner_path"
                :src="event.banner_path"
                :alt="event.name"
                class="object-cover w-full h-full"
            />
            <div
                v-else
                class="absolute inset-0 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600 opacity-90"
            />

            <!-- Status Badge -->
            <div class="absolute right-4 top-4">
                <Pill
                    :variant="event.is_active ? 'success' : 'secondary'"
                    :text="event.is_active ? 'Active' : 'Inactive'"
                />
            </div>

            <!-- Upcoming/Live Badge -->
            <div v-if="isUpcoming" class="absolute left-4 top-4">
                <div
                    class="px-3 py-1 text-xs font-medium text-white bg-blue-500 rounded-md"
                >
                    Upcoming
                </div>
            </div>
            <div v-else-if="isOngoing" class="absolute left-4 top-4">
                <div
                    class="px-3 py-1 text-xs font-medium text-white bg-red-500 rounded-md animate-pulse"
                >
                    Live Now
                </div>
            </div>

            <!-- Logo -->
            <div v-if="event.logo_path" class="absolute -bottom-8 left-6">
                <div class="w-16 h-16 p-1 bg-white rounded-full shadow-lg">
                    <img
                        :src="event.logo_path"
                        :alt="`${event.name} logo`"
                        class="object-cover w-full h-full rounded-full"
                    />
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="px-6 pt-4 pb-4">
            <h3
                class="text-xl font-bold text-gray-900 transition-colors duration-200 group-hover:text-blue-600 dark:text-dark-primary dark:group-hover:text-blue-400"
            >
                {{ event.name }}
            </h3>
            <p
                v-if="event.description"
                class="mt-2 text-sm text-gray-600 line-clamp-2 dark:text-dark-text-secondary"
            >
                {{ event.description }}
            </p>

            <!-- Event Details -->
            <div class="mt-4 space-y-3">
                <div
                    v-if="event.start_date && event.end_date"
                    class="flex items-center text-sm text-gray-600 dark:text-dark-text-secondary"
                >
                    <svg
                        class="w-4 h-4 mr-2 text-blue-500"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                    </svg>
                    <span
                        >{{ formatDate(event.start_date) }} -
                        {{ formatDate(event.end_date) }}</span
                    >
                </div>

                <div
                    v-if="event.location"
                    class="flex items-center text-sm text-gray-600 dark:text-dark-text-secondary"
                >
                    <svg
                        class="w-4 h-4 mr-2 text-red-500"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                    </svg>
                    <span class="truncate">{{ event.location }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="px-6 py-4 border-t border-gray-100 dark:border-dark-border">
            <div class="flex gap-2">
                <Button @click="handleManage" variant="primary" class="flex-1">
                    <svg
                        class="w-4 h-4 mr-2"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                    </svg>
                    Manage
                </Button>
                <Button
                    variant="secondary"
                    class="border-gray-300 hover:border-gray-400 hover:bg-gray-50 dark:border-dark-border dark:hover:border-dark-primary dark:hover:bg-dark-surface-hover"
                >
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
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                        />
                    </svg>
                </Button>
            </div>
        </div>
    </div>
</template>
