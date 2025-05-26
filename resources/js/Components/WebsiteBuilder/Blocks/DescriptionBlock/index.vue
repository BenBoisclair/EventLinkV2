<script setup lang="ts">
import BlockContainer from "@/Components/WebsiteBuilder/Renderer/BlockContainer.vue";
import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import type { DescriptionBlockProps } from "@/types/blocks";
import type { DeviceType } from "@/types/websiteBuilder";
import { getContrastingTextColor } from "@/utils/color";
import { computed, withDefaults } from "vue";
import BlockTitle from "../BlockTitle.vue";

const props = withDefaults(
    defineProps<
        DescriptionBlockProps & {
            isEditorMode?: boolean;
            device?: DeviceType;
        }
    >(),
    {
        isEditorMode: false,
        device: "desktop",
        backgroundColor: "#FFFFFF",
    }
);

const emit = defineEmits<{
    (e: "delete", blockId: string): void;
}>();

const store = useWebsiteBuilderStore();

const handleEditClick = () => {
    if (!props.id) return;
    store.beginEditingBlock(props.id);
};

const backgroundStyle = computed(() => ({
    backgroundColor: props.backgroundColor ?? "#FFFFFF",
}));

const textColor = computed(() => {
    const bgColor = props.backgroundColor ?? "#FFFFFF";
    return getContrastingTextColor(bgColor);
});

const handleDelete = () => {
    if (!props.id) return;
    emit("delete", props.id);
};
</script>

<template>
    <BlockContainer
        :id="props.id ?? ''"
        :style="backgroundStyle"
        :isEditorMode="props.isEditorMode"
        @delete="handleDelete"
        @edit="handleEditClick"
        class="px-6 py-12 sm:px-8 md:px-16 md:py-16"
    >
        <div class="max-w-3xl mx-auto">
            <BlockTitle
                :title="props.title"
                :title-color="textColor"
                tag="h2"
                text-align="left"
                default-classes="mb-4 text-2xl font-bold sm:text-3xl md:mb-6"
            />
            <p
                class="text-base whitespace-pre-line sm:text-lg"
                :style="{ color: textColor }"
            >
                {{ props.description }}
            </p>
        </div>
    </BlockContainer>
</template>
