<script setup lang="ts">
import ResizableHandle from "@/Components/UI/ResizableHandle.vue";
import BuilderSidebar from "@/Components/WebsiteBuilder/Editor/BuilderSidebar.vue";
import BuilderBar from "@/Components/WebsiteBuilder/Editor/BuilderBar.vue";
import WebsiteEditorRenderer from "@/Components/WebsiteBuilder/Renderer/WebsiteEditorRenderer.vue";
import { useResizableSidebar } from "@/Composables/useResizableSidebar";
import { useEventStore } from "@/stores/eventStore";
import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import type { PageProps } from "@/types";
import type { EventType } from "@/types/event";
import type { BlockType } from "@/types/blocks";
import { Head, router, usePage } from "@inertiajs/vue3";
import { storeToRefs } from "pinia";
import { computed, onMounted, onUnmounted, ref } from "vue";
import { useWebsiteBuilderEcho } from "@/Composables/useWebsiteBuilderEcho";

const page = usePage<
    PageProps<{
        event: EventType;
        websiteId: number;
        websiteSlug: string;
        blocks: BlockType[];
        websiteSettings: any;
        isPublished: boolean;
        lastUpdatedAt: string;
    }>
>();

const eventStore = useEventStore();
const websiteBuilderStore = useWebsiteBuilderStore();

const { previewDevice, blocks } = storeToRefs(websiteBuilderStore);

const isMounted = ref(false);

const { sidebarWidth, handleMouseDown } = useResizableSidebar({
    initialWidth: 300,
    minWidth: 300,
    maxWidth: 1000,
});

onMounted(() => {
    websiteBuilderStore.initializeBuilder({
        websiteId: page.props.websiteId ?? null,
        websiteSlug: page.props.websiteSlug ?? null,
        blocks: page.props.blocks ?? [],
        isPublished: page.props.isPublished,
        event: page.props.event,
        lastUpdatedAt: page.props.lastUpdatedAt,
        websiteSettings: page.props.websiteSettings,
    });

    if (page.props.event) {
        eventStore.setCurrentEvent(page.props.event);
    } else {
        eventStore.setCurrentEvent(null);
    }

    isMounted.value = true;
});

const handleBeforeUnload = (event: BeforeUnloadEvent) => {
    if (websiteBuilderStore.isDirty) {
        event.preventDefault();
        return "";
    }
};

onMounted(() => {
    window.addEventListener("beforeunload", handleBeforeUnload);
});

onUnmounted(() => {
    window.removeEventListener("beforeunload", handleBeforeUnload);
    websiteBuilderStore.stopAutoSave();
});

useWebsiteBuilderEcho(
    page.props.websiteId ?? null,
    (eventData: { blockId: string; imageUrl: string; propName: string }) => {
        websiteBuilderStore.handleBlockImageProcessed(eventData);
    }
);

const builderSidebarRef = ref<InstanceType<typeof BuilderSidebar> | null>(null);

const handleRequestAddBlock = () => {
    builderSidebarRef.value?.showAddBlockView();
};
</script>

<template>
    <div class="flex flex-col h-screen overflow-hidden">
        <Head>
            <title>Website Builder</title>
        </Head>
        <BuilderBar />
        <div class="flex flex-1 overflow-hidden">
            <BuilderSidebar
                ref="builderSidebarRef"
                :style="{ width: `${sidebarWidth}px` }"
                class="flex-shrink-0 h-full overflow-y-auto"
            />
            <ResizableHandle @mousedown="handleMouseDown" />
            <div class="flex-1 h-full p-6 overflow-y-auto bg-white">
                <div
                    class="flex flex-col min-h-full mx-auto transition-all duration-300"
                    :class="{
                        'max-w-[1200px]': previewDevice === 'desktop',
                        'max-w-[768px]': previewDevice === 'tablet',
                        'max-w-[375px]': previewDevice === 'mobile',
                    }"
                    :style="{
                        minHeight:
                            previewDevice === 'mobile' ? '667px' : 'auto',
                    }"
                >
                    <WebsiteEditorRenderer
                        :event="page.props.event"
                        class="rounded-md shadow-lg"
                        @request-add-block="handleRequestAddBlock"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
