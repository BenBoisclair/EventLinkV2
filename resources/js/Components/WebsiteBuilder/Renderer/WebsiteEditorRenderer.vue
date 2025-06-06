<script setup lang="ts">
import { getBlockComponent } from "@/utils/blockRegistry";
import { buildBlockProps } from "@/utils/blockPropsBuilder";
import { extractWebsiteId } from "@/utils/websiteHelpers";
import type { Block } from "@/types/websiteBuilder";
import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import { usePage } from "@inertiajs/vue3";
import { computed, ref, StyleValue } from "vue";
import { storeToRefs } from "pinia";
import type { EventType } from "@/types/event";
import Button from "@/Components/UI/Button.vue";

interface Props {
    event?: EventType;
}

const props = defineProps<Props>();

const websiteBuilderStore = useWebsiteBuilderStore();
const { blocks, currentBlockId, editingBlockProps, previewDevice } =
    storeToRefs(websiteBuilderStore);

const effectiveDevice = computed(() => previewDevice.value || "desktop");

// Track which block is being hovered
const hoveredBlockId = ref<string | null>(null);

const handleBlockMouseEnter = (blockId: string) => {
    hoveredBlockId.value = blockId;
};

const handleBlockMouseLeave = () => {
    hoveredBlockId.value = null;
};

const prepareBlockProps = (block: Block) => {
    return buildBlockProps(
        block,
        currentBlockId.value,
        editingBlockProps.value,
        true, // Always in editor mode
        effectiveDevice.value,
        props.event,
        extractWebsiteId(usePage().props)
    );
};

const handleDeleteBlock = (blockId: string) => {
    websiteBuilderStore.deleteBlock(blockId);
};

const handleUpdateBlock = (blockId: string, newProps: Record<string, any>) => {
    websiteBuilderStore.updateBlock(blockId, newProps);
};

// Get selected font from store settings
const selectedFont = computed(() => {
    return websiteBuilderStore.websiteSettings?.design?.selectedFont || "Inter";
});

const rendererStyle = computed((): StyleValue => {
    return {
        fontFamily: selectedFont.value
            ? `"${selectedFont.value}", sans-serif`
            : "sans-serif",
    };
});

const emit = defineEmits<{
    requestAddBlock: [];
}>();
</script>

<template>
    <div class="flex flex-col min-h-full bg-white" :style="rendererStyle">
        <div class="flex-1">
            <div
                v-if="!blocks || blocks.length === 0"
                class="flex min-h-[500px] flex-1 items-center justify-center p-6"
            >
                <div class="text-center">
                    <Button
                        @click="emit('requestAddBlock')"
                        variant="primary"
                        icon="$plus"
                        text="Add Block"
                        size="lg"
                    />
                </div>
            </div>
            <div v-else class="w-full">
                <template v-for="block in blocks" :key="block.id">
                    <div
                        class="relative"
                        @mouseenter="handleBlockMouseEnter(block.id)"
                        @mouseleave="handleBlockMouseLeave"
                    >
                        <component
                            :is="getBlockComponent(block.type)"
                            v-bind="prepareBlockProps(block)"
                            @delete="handleDeleteBlock"
                            @updateBlock="handleUpdateBlock"
                        />
                        <!-- Darkening overlay for non-selected blocks -->
                        <div
                            v-if="currentBlockId && currentBlockId !== block.id"
                            class="absolute inset-0 z-40 transition-opacity duration-200 pointer-events-none bg-black/30"
                        />
                        <div
                            v-if="currentBlockId === block.id"
                            class="absolute inset-0 z-50 -my-1 border-[5px] border-purple-500 rounded-sm -mx-4"
                        >
                            <!-- <div
                                class="absolute px-2 py-1 text-sm font-medium text-white -translate-x-1/2 bg-purple-500 rounded-sm -top-7 left-1/2"
                            >
                                Block Name
                            </div> -->
                        </div>

                        <!-- Editor controls overlay -->
                        <div
                            v-if="
                                hoveredBlockId === block.id &&
                                currentBlockId !== block.id &&
                                editingBlockProps === null
                            "
                            class="absolute z-50 pointer-events-none top-4 left-4"
                        >
                            <div class="flex gap-2">
                                <Button
                                    @click="
                                        websiteBuilderStore.beginEditingBlock(
                                            block.id
                                        )
                                    "
                                    variant="primary"
                                    size="sm"
                                    title="Edit block"
                                    class="shadow-lg pointer-events-auto"
                                >
                                    <v-icon
                                        icon="$squareEditOutline"
                                        size="small"
                                        class="mr-1"
                                    />
                                    Edit
                                </Button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
