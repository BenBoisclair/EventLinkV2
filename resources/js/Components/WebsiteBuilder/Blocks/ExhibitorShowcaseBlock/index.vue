<script setup lang="ts">
import BlockContainer from "@/Components/WebsiteBuilder/Renderer/BlockContainer.vue";
import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import type { ExhibitorShowcaseBlockProps } from "@/types/blocks";
import type { Exhibitor } from "@/types/exhibitor";
import type { DeviceType } from "@/types/websiteBuilder";
import axios from "axios";
import { onMounted, ref, watch } from "vue";
import { route } from "ziggy-js";
import ExhibitorBannerDisplay from "../../../Exhibitor/ExhibitorBannerDisplay.vue";
import ExhibitorCard from "../../../Exhibitor/ExhibitorCard.vue";
import BlockTitle from "../BlockTitle.vue";

interface Props extends ExhibitorShowcaseBlockProps {
    id: string;
    isEditorMode?: boolean;
    device?: DeviceType;
    event?: {
        id: number;
        name?: string;
    };
}

const props = withDefaults(defineProps<Props>(), {
    isEditorMode: false,
    device: "desktop",
    title: "Meet Our Exhibitors",
    titleColor: "#000000",
    backgroundColor: "#FFFFFF",
    event: undefined,
});

const emit = defineEmits<{
    (e: "delete", blockId: string): void;
    (
        e: "updateBlock",
        blockId: string,
        newProps: Partial<ExhibitorShowcaseBlockProps>
    ): void;
}>();

const store = useWebsiteBuilderStore();

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
    console.log(`[ExhibitorShowcase] Fetching exhibitors from: ${apiUrl}`);
    try {
        const response = await axios.get<{ data: Exhibitor[] }>(apiUrl);
        console.log("[ExhibitorShowcase] API Response Received:", response);
        exhibitors.value = response.data?.data ?? [];
        console.log(
            `[ExhibitorShowcase] Parsed ${exhibitors.value.length} exhibitors.`
        );
    } catch (err) {
        console.error(
            "[ExhibitorShowcase] Error fetching exhibitors (Full Error):",
            err
        );
        if (axios.isAxiosError(err)) {
            console.error("[ExhibitorShowcase] Axios error details:", {
                message: err.message,
                response: err.response?.data,
                status: err.response?.status,
            });
            if (err.response?.status === 404) {
                error.value = "Exhibitor data not found for this event.";
            } else {
                error.value =
                    "Failed to load exhibitors. Please check the console for details.";
            }
        } else {
            error.value =
                "An unexpected error occurred while fetching exhibitors.";
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
    } else if (props.isEditorMode) {
        error.value = "Link an event to display exhibitors.";
    }
});

watch(
    () => props.event?.id,
    (newEventId, oldEventId) => {
        if (newEventId && newEventId !== oldEventId) {
            fetchExhibitors(newEventId);
        } else if (!newEventId && props.isEditorMode) {
            error.value = "Link an event to display exhibitors.";
            exhibitors.value = [];
        }
    },
    { immediate: false }
);

const handleDelete = () => {
    if (!props.id) return;
    emit("delete", props.id);
};

const handleEditClick = () => {
    if (!props.id) return;
    store.beginEditingBlock(props.id);
};
</script>

<template>
    <BlockContainer
        :id="props.id"
        :background-color="props.backgroundColor ?? ''"
        :is-editor-mode="props.isEditorMode"
        @delete="handleDelete"
        @edit="handleEditClick"
    >
        <div class="px-4 py-16 sm:px-6 lg:px-8">
            <BlockTitle
                :title="props.title ?? ''"
                :title-color="props.titleColor ?? ''"
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
                    class="gap-4"
                    :class="{
                        'flex flex-col items-center': props.device === 'mobile',
                        'grid grid-cols-3': props.device !== 'mobile',
                    }"
                >
                    <ExhibitorCard
                        v-for="exhibitor in exhibitors"
                        :key="exhibitor.id"
                        :exhibitor="exhibitor"
                        :card-background-color="props.backgroundColor ?? ''"
                        @click="openExhibitorModal(exhibitor)"
                    />
                </div>
            </div>
        </div>
    </BlockContainer>

    <ExhibitorBannerDisplay
        v-model="isModalOpen"
        :exhibitor="selectedExhibitor"
        :background-color="props.backgroundColor ?? ''"
    />
</template>
