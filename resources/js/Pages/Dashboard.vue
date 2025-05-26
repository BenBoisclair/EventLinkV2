<script setup lang="ts">
import Attendees from "@/Pages/Dashboard/Attendees.vue";
import Exhibitors from "@/Pages/Dashboard/Exhibitors.vue";
import Settings from "@/Pages/Dashboard/Settings.vue";
import Website from "@/Pages/Dashboard/Website.vue";
import Sidebar from "@/Components/Organiser/Sidebar.vue";
import { useEventStore } from "@/stores/eventStore";
import { useThemeStore } from "@/stores/darkmode";
import type { PageProps } from "@/types";
import { Head, router, usePage } from "@inertiajs/vue3";
import { computed, watchEffect, ref } from "vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

const page = usePage<PageProps>();

const subPages = ["website", "attendees", "exhibitors", "settings"] as const;
type SubPage = (typeof subPages)[number];

const activeSubPage = ref<SubPage>("website");

const pageComponentMap = {
    website: Website,
    attendees: Attendees,
    exhibitors: Exhibitors,
    settings: Settings,
};

const getCurrentComponent = computed(() => {
    return pageComponentMap[activeSubPage.value] || Website;
});

const eventStore = useEventStore();
eventStore.setEvents(page.props.events || []);
eventStore.setCurrentEvent(page.props.event || null);

const themeStore = useThemeStore();
const isDark = computed(() => themeStore.isDark);

const pageTitle = computed(() => {
    const pageName = activeSubPage.value;
    return pageName.charAt(0).toUpperCase() + pageName.slice(1);
});

watchEffect(() => {
    if (eventStore.currentEvent === null) {
        router.visit("/organiser/events");
    }
});

function handleSubPageChange(subPage: SubPage) {
    if (subPages.includes(subPage)) {
        activeSubPage.value = subPage;
    }
}
</script>

<template>
    <DashboardLayout :show-navigation="true">
        <Head>
            <title>{{ pageTitle }}</title>
        </Head>
        <div class="dark:bg-dark-background flex flex-1 bg-[#F1F5F9]">
            <Sidebar
                :current-event-id="page.props.event?.id"
                :is-dark="isDark"
                class="h-full"
                :active-sub-page="activeSubPage"
                @change-subpage="handleSubPageChange"
            />
            <div class="flex-1 overflow-y-scroll">
                <component
                    v-if="getCurrentComponent && page.props.event"
                    :is="getCurrentComponent"
                    :event="page.props.event"
                    :website="page.props.website"
                    :attendees="page.props.attendees"
                    :exhibitors="page.props.exhibitors"
                />
                <div
                    v-else
                    class="p-4 text-gray-500 dark:text-dark-text-secondary"
                >
                    {{
                        page.props.event
                            ? "Loading page content..."
                            : "No event selected or found."
                    }}
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
