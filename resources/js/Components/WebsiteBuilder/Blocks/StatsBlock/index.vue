<script setup lang="ts">
import BlockContainer from "@/Components/WebsiteBuilder/Renderer/BlockContainer.vue";
import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import type { StatsBlockProps } from "@/types/blocks";
import type { DeviceType } from "@/types/websiteBuilder";
import { computed } from "vue";

const props = withDefaults(
    defineProps<
        StatsBlockProps & {
            isEditorMode?: boolean;
            device?: DeviceType;
        }
    >(),
    {
        backgroundColor: undefined,
        textColor: "#FFFFFF",
        isEditorMode: false,
        device: "desktop",
    }
);

const emit = defineEmits<{
    (e: "delete", blockId: string): void;
}>();

const store = useWebsiteBuilderStore();

const blockStyle = computed(() => {
    return {
        backgroundColor: props.backgroundColor,
        color: props.textColor || "#FFFFFF",
    };
});

const handleEditClick = () => {
    if (!props.id) return;
    store.beginEditingBlock(props.id);
};

const handleDelete = () => {
    if (!props.id) return;
    emit("delete", props.id);
};
</script>

<template>
    <BlockContainer
        :id="props.id ?? ''"
        :style="blockStyle"
        :isEditorMode="props.isEditorMode"
        @edit="handleEditClick"
        @delete="handleDelete"
        class="py-12 md:py-16"
    >
        <div class="container px-4 mx-auto">
            <div
                class="flex flex-wrap justify-center gap-8 text-center md:gap-12"
            >
                <div
                    v-for="(stat, index) in props.stats"
                    :key="`stat-${index}-${stat.title}`"
                    class="w-full basis-full sm:w-1/2 sm:basis-1/2 md:w-1/4 md:basis-1/4"
                >
                    <div class="mb-1 text-4xl font-bold md:mb-2 md:text-5xl">
                        {{ stat.value }}
                    </div>
                    <div class="text-sm md:text-base">
                        {{ stat.title }}
                    </div>
                </div>
            </div>
        </div>
    </BlockContainer>
</template>
