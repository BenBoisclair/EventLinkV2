<script setup lang="ts">
import BlockContainer from "@/Components/WebsiteBuilder/Renderer/BlockContainer.vue";
import type { ExhibitorShowcaseBlockProps } from "@/types/blocks";
import type { Exhibitor } from "@/types/exhibitor";
import axios from "axios";
import { onMounted, ref, watch, withDefaults } from "vue";
import { route } from "ziggy-js";
import ExhibitorBannerDisplay from "../../../Exhibitor/ExhibitorBannerDisplay.vue";
import ExhibitorCard from "../../../Exhibitor/ExhibitorCard.vue";
import BlockTitle from "../BlockTitle.vue";
import { useThemeColors } from "@/Composables/useThemeColors";

interface Props extends ExhibitorShowcaseBlockProps {
    id: string;
    event?: {
        id: number;
        name?: string;
    };
    theme?: {
        primary: string;
        secondary: string;
        accent: string;
        background: string;
    };
}

const props = withDefaults(defineProps<Props>(), {
    title: "Meet Our Exhibitors",
    event: undefined,
});

const { colors } = useThemeColors(props.theme);



const exhibitors = ref<Exhibitor[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);

const isModalOpen = ref(false);
const selectedExhibitor = ref<Exhibitor | null>(null);

const fetchExhibitors = async (eventId: number) => {
    loading.value = true;
    error.value = null;
    exhibitors.value = [];
    const apiUrl = route("api.exhibitors.listForEvent", { event: eventId });
    try {
        const response = await axios.get<{ data: Exhibitor[] }>(apiUrl);
        exhibitors.value = response.data?.data ?? [];
    } catch (err) {
        if (axios.isAxiosError(err)) {
            if (err.response?.status === 404) {
                error.value = "Exhibitor data not found for this event.";
            } else {
                error.value = "Failed to load exhibitors.";
            }
        } else {
            error.value = "An unexpected error occurred while fetching exhibitors.";
        }
    } finally {
        loading.value = false;
    }
};

const openExhibitorModal = (exhibitor: Exhibitor) => {
    selectedExhibitor.value = exhibitor;
    isModalOpen.value = true;
};

onMounted(() => {
    if (props.event?.id) {
        fetchExhibitors(props.event.id);
    }
});

watch(
    () => props.event?.id,
    (newEventId, oldEventId) => {
        if (newEventId && newEventId !== oldEventId) {
            fetchExhibitors(newEventId);
        } else if (!newEventId) {
            exhibitors.value = [];
        }
    },
    { immediate: false }
);

</script>

<template>
    <BlockContainer
        :background-color="colors.backgroundPrimary"
    >
        <div class="px-4 py-16 sm:px-6 lg:px-8">
            <BlockTitle
                :title="props.title ?? ''"
                :title-color="colors.textPrimary"
                default-classes="text-3xl font-bold"
                text-align="center"
            />

            <div class="px-2 mx-auto mt-10">
                <div
                    v-if="loading"
                    class="flex min-h-[200px] items-center justify-center"
                >
                    <p class="text-gray-500">Loading exhibitors...</p>
                </div>

                <div
                    v-else-if="error"
                    class="flex items-center justify-center gap-2 px-4 py-8 text-center text-red-600 border border-red-200 rounded bg-red-50"
                >
                    <v-icon icon="$alertCircleOutline" size="small"></v-icon>
                    <span>{{ error }}</span>
                </div>

                <div
                    v-else-if="!exhibitors || exhibitors.length === 0"
                    class="py-8 text-center text-gray-500"
                >
                    No exhibitors to display yet.
                </div>

                <div
                    v-else
                    class="grid grid-cols-3 gap-4"
                >
                    <ExhibitorCard
                        v-for="exhibitor in exhibitors"
                        :key="exhibitor.id"
                        :exhibitor="exhibitor"
                        :card-background-color="colors.backgroundPrimary"
                        @click="openExhibitorModal(exhibitor)"
                    />
                </div>
            </div>
        </div>
    </BlockContainer>

    <ExhibitorBannerDisplay
        v-model="isModalOpen"
        :exhibitor="selectedExhibitor"
        :background-color="colors.backgroundPrimary"
    />
</template>
