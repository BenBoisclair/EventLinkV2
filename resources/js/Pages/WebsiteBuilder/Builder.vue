<script setup lang="ts">
import ResizableHandle from "@/Components/UI/ResizableHandle.vue";
import BuilderSidebar from "@/Components/WebsiteBuilder/Editor/BuilderSidebar.vue";
import BuilderBar from "@/Components/WebsiteBuilder/Editor/BuilderBar.vue";
import WebsiteRenderer from "@/Components/WebsiteBuilder/Renderer/WebsiteRenderer.vue";
import { useResizableSidebar } from "@/Composables/useResizableSidebar";
import { useEventStore } from "@/stores/eventStore";
import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import type { PageProps } from "@/types";
import type { EventType } from "@/types/event";
import type { BlockType } from "@/types/blocks";
import { router, usePage } from "@inertiajs/vue3";
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
    initialWidth: 432,
    minWidth: 300,
    maxWidth: 800,
});

onMounted(() => {
    websiteBuilderStore.initializeBuilder({
        websiteId: page.props.websiteId ?? null,
        websiteSlug: page.props.websiteSlug ?? null,
        blocks: page.props.blocks ?? [],
        isPublished: page.props.isPublished,
        event: page.props.event,
        lastUpdatedAt: page.props.lastUpdatedAt,
    });

    if (page.props.event) {
        eventStore.setCurrentEvent(page.props.event);
    } else {
        eventStore.setCurrentEvent(null);
    }

    isMounted.value = true;
});

useWebsiteBuilderEcho(
    page.props.websiteId ?? null,
    (eventData: { blockId: string; imageUrl: string; propName: string }) => {
        websiteBuilderStore.handleBlockImageProcessed(eventData);
    }
);

const handleDeleteBlock = (blockId: string) => {
    websiteBuilderStore.deleteBlock(blockId);
};

const handleUpdateBlock = (blockId: string, newProps: Record<string, any>) => {
    websiteBuilderStore.updateBlock(blockId, newProps);
};

const hasBlocks = computed(() => blocks.value.length > 0);
</script>

<template>
    <div class="flex flex-col h-screen overflow-hidden">
        <BuilderBar />
        <div class="flex flex-1 overflow-hidden">
            <BuilderSidebar
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
                    <WebsiteRenderer
                        :blocks="blocks"
                        :is-editor-mode="true"
                        :device="previewDevice"
                        :editing-block-id="websiteBuilderStore.currentBlockId"
                        :editing-block-props="
                            websiteBuilderStore.editingBlockProps
                        "
                        :event="page.props.event"
                        @delete-block="handleDeleteBlock"
                        @update-block="handleUpdateBlock"
                        class="rounded-md shadow-lg"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
