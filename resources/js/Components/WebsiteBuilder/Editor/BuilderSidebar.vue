<script setup lang="ts">
import SidebarEditorWrapper from "@/Components/WebsiteBuilder/Editor/SidebarEditorWrapper.vue";
import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import type { Block } from "@/types/websiteBuilder";
import { storeToRefs } from "pinia";
import { computed, defineAsyncComponent, ref, shallowRef, watch } from "vue";
import AddBlockSelector from "./AddBlockSelector.vue";
import BlocksSection from "./BlocksSection.vue";

const websiteBuilderStore = useWebsiteBuilderStore();

const { currentBlockId, currentBlockType } = storeToRefs(websiteBuilderStore);

const isAddingBlock = ref(false);

const blockEditorComponents = {
    Hero: () =>
        import("@/Components/WebsiteBuilder/Blocks/HeroBlock/Editor.vue").then(
            (m) => m.default
        ),
    Stats: () =>
        import("@/Components/WebsiteBuilder/Blocks/StatsBlock/Editor.vue").then(
            (m) => m.default
        ),
    Description: () =>
        import(
            "@/Components/WebsiteBuilder/Blocks/DescriptionBlock/Editor.vue"
        ).then((m) => m.default),
    Countdown: () =>
        import(
            "@/Components/WebsiteBuilder/Blocks/CountdownBlock/Editor.vue"
        ).then((m) => m.default),
    AttendeesForm: () =>
        import(
            "@/Components/WebsiteBuilder/Blocks/AttendeesFormBlock/Editor.vue"
        ).then((m) => m.default),
    ExhibitorShowcase: () =>
        import(
            "@/Components/WebsiteBuilder/Blocks/ExhibitorShowcaseBlock/Editor.vue"
        ).then((m) => m.default),
};

const currentEditorBlock = shallowRef<any | null>(null);

const getEditorComponent = (type: string | null) => {
    if (!type) return null;
    if (blockEditorComponents[type as keyof typeof blockEditorComponents]) {
        const loader = blockEditorComponents[
            type as keyof typeof blockEditorComponents
        ] as () => Promise<any>;
        return defineAsyncComponent({
            loader,
            loadingComponent: {
                template:
                    '<div class="p-4 dark:text-dark-text-secondary">Loading Editor...</div>',
            },
            errorComponent: {
                template:
                    '<div class="p-4 text-red-500 dark:text-dark-status-red">Error loading editor component. Check console.</div>',
            },
            timeout: 3000,
        });
    } else {
        return defineAsyncComponent({
            loader: async () => ({
                template: `<div class=\"p-4 text-orange-500\">Editor for block type '${type}' not found.</div>`,
            }),
        });
    }
};

watch(
    currentBlockType,
    (newType) => {
        currentEditorBlock.value = getEditorComponent(newType);
    },
    { immediate: true }
);

const editorTitle = computed(() => {
    if (!currentBlockType.value) return "";
    const formattedType = currentBlockType.value
        .replace(/([A-Z])/g, " $1")
        .trim();
    return `Edit ${formattedType} Block`;
});

const handleConfirmClick = () => {
    websiteBuilderStore.saveBlock();
};

const handleBackClick = () => {
    if (currentBlockId.value) {
        websiteBuilderStore.discardBlock();
    } else if (isAddingBlock.value) {
        hideAddBlockView();
    }
};

const showAddBlockView = () => {
    isAddingBlock.value = true;
};

const hideAddBlockView = () => {
    isAddingBlock.value = false;
};

const handleAddBlockSelected = (
    blockType: Block["type"],
    defaultProps?: any
) => {
    websiteBuilderStore.addBlock(blockType, defaultProps || {});
    hideAddBlockView();
};
</script>

<template>
    <div
        class="relative flex flex-col pb-6 border-r bg-surface dark:bg-dark-surface dark:border-dark-border scroll-fade-vertical border-border"
    >
        <div
            v-if="!currentBlockId && !isAddingBlock"
            class="flex flex-col h-full overflow-y-auto"
        >
            <BlocksSection @request-add-block="showAddBlockView" />
        </div>

        <Transition name="slide-editor" mode="out-in">
            <template v-if="currentBlockId || isAddingBlock">
                <SidebarEditorWrapper
                    :editor-title="
                        currentBlockId ? editorTitle : 'Add New Block'
                    "
                    :show-confirm="currentBlockId !== null"
                    confirm-text="Save Block"
                    :back-text="currentBlockId ? 'Discard Block' : 'Cancel'"
                    @confirm="handleConfirmClick"
                    @back="handleBackClick"
                    class="absolute inset-0 z-10 flex flex-col h-full bg-surface dark:bg-dark-surface"
                >
                    <template
                        v-if="
                            currentBlockId &&
                            currentEditorBlock &&
                            websiteBuilderStore.editingBlockProps &&
                            websiteBuilderStore.websiteId
                        "
                    >
                        <component
                            :is="currentEditorBlock"
                            :initial-props="
                                websiteBuilderStore.editingBlockProps
                            "
                            :website-id="websiteBuilderStore.websiteId"
                            :block-id-prop="currentBlockId"
                            class="flex-grow overflow-y-auto"
                        />
                    </template>
                    <template
                        v-else-if="
                            currentBlockId && !websiteBuilderStore.websiteId
                        "
                    >
                        <div
                            class="flex-grow p-4 overflow-y-auto text-sm text-gray-500 dark:text-dark-text-secondary"
                        >
                            Loading website data...
                        </div>
                    </template>
                    <template v-else-if="currentBlockId">
                        <div
                            class="flex-grow p-4 overflow-y-auto text-sm text-gray-500 dark:text-dark-text-secondary"
                        >
                            <p v-if="!currentEditorBlock">
                                Editor component not loaded or failed to load.
                            </p>
                            <p
                                v-if="
                                    currentEditorBlock &&
                                    !websiteBuilderStore.editingBlockProps
                                "
                            >
                                Temporary editing props not available.
                            </p>
                            <p>Check the browser console for more details.</p>
                        </div>
                    </template>
                    <template v-else-if="isAddingBlock">
                        <AddBlockSelector
                            class="flex-grow overflow-y-auto"
                            @select="handleAddBlockSelected"
                        />
                    </template>
                </SidebarEditorWrapper>
            </template>
        </Transition>
    </div>
</template>

<style scoped>
.slide-editor-enter-active,
.slide-editor-leave-active {
    transition: transform 0.3s ease-in-out;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--bg-surface, white); /* Fallback */
    @apply bg-surface dark:bg-dark-surface;
    z-index: 10;
}
.slide-editor-enter-from {
    transform: translateX(-100%);
}
.slide-editor-leave-to {
    transform: translateX(-100%);
}
</style>
