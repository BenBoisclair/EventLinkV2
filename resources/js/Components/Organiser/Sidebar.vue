<script setup lang="ts">
import { useEventStore } from "@/stores/eventStore";
import { computed, ref, watch } from "vue";
import SidebarMenuItem from "./SidebarMenuItem.vue";
import EventSelector from "./EventSelector.vue";

const props = defineProps<{
    currentEventId?: number;
    activeSubPage?: string;
}>();
const emit = defineEmits(["change-subpage"]);

const eventStore = useEventStore();

const selectedEvent = ref(
    props.currentEventId?.toString() ||
        eventStore.currentEvent?.id?.toString() ||
        ""
);
const isCollapsed = ref(false);

watch(
    () => eventStore.currentEvent,
    (newEvent) => {
        selectedEvent.value = newEvent?.id?.toString() || "";
    }
);

const menuItems = computed(() => {
    const eventId = selectedEvent.value;
    if (!eventId) return [];

    return [
        {
            key: "website",
            name: "Website",
            icon: "$web",
        },
        {
            key: "attendees",
            name: "Attendees",
            icon: "$accountGroupOutline",
        },
        {
            key: "exhibitors",
            name: "Exhibitors",
            icon: "$storeOutline",
        },
        {
            key: "settings",
            name: "Settings",
            icon: "$cogOutline",
        },
    ];
});

const handleMenuItemClick = (key: string) => {
    emit("change-subpage", key);
};

// Watch for window resize
window.addEventListener("resize", () => {
    isCollapsed.value = window.innerWidth < 800;
});

// Initial check
isCollapsed.value = window.innerWidth < 800;
</script>

<template>
    <div
        class="dark:border-dark-border dark:bg-dark-background sticky top-0 flex h-[calc(100vh)] flex-col border-r border-[#E2E8F0] transition-all duration-300"
        :class="isCollapsed ? 'w-[72px]' : 'w-[264px]'"
    >
        <!-- Events Section -->
        <div class="flex flex-col gap-4 p-4" :class="{ 'px-2': isCollapsed }">
            <h2
                class="dark:text-dark-primary text-[24px] font-bold text-[#193CB8]"
                :class="{ hidden: isCollapsed }"
            >
                Event
            </h2>
            <EventSelector
                :current-event-id="parseInt(selectedEvent) || undefined"
                :is-collapsed="isCollapsed"
            />
        </div>

        <!-- Menu Section -->
        <div
            v-if="selectedEvent"
            class="dark:border-dark-border flex flex-col gap-4 border-t border-[#E2E8F0] p-4"
            :class="{ 'px-2': isCollapsed }"
        >
            <h2
                class="dark:text-dark-primary text-[24px] font-bold text-[#193CB8]"
                :class="{ hidden: isCollapsed }"
            >
                Menu
            </h2>
            <div class="flex flex-col">
                <template v-for="(item, index) in menuItems" :key="item.key">
                    <SidebarMenuItem
                        :name="item.name"
                        :icon="item.icon"
                        :is-active="props.activeSubPage === item.key"
                        :is-collapsed="isCollapsed"
                        @click="handleMenuItemClick(item.key)"
                    />
                    <div
                        v-if="index !== menuItems.length - 1"
                        class="dark:border-dark-border h-[16px] border-l-2 border-[#E2E8F0]"
                    ></div>
                </template>
            </div>
        </div>
    </div>
</template>
