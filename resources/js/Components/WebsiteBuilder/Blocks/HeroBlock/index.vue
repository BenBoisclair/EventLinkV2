<script setup lang="ts">
import BlockContainer from "@/Components/WebsiteBuilder/Renderer/BlockContainer.vue";
import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import type { HeroBlockProps } from "@/types/blocks";
import type { DeviceType } from "@/types/websiteBuilder";
import { hexToRgba } from "@/utils/color";
import { computed, withDefaults } from "vue";

const props = withDefaults(
    defineProps<
        HeroBlockProps & {
            websiteId: string | number;
            isEditorMode?: boolean;
            device?: DeviceType;
        }
    >(),
    {
        isEditorMode: false,
        device: "desktop",
        textPosition: "middle",
        overlayEnabled: false,
        overlayColor: "#000000",
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

const handleDelete = () => {
    if (!props.id) return;
    emit("delete", props.id);
};

const showImage = computed(() => !!props.imageUrl);

const backgroundStyle = computed(() => {
    if (!showImage.value && props.backgroundColor) {
        return { backgroundColor: props.backgroundColor };
    }
    if (!showImage.value && !props.backgroundColor && props.isEditorMode) {
        return { backgroundColor: "#F3F4F6" };
    }
    return {};
});

const textPositionClasses = computed(() => {
    const baseClasses = "absolute inset-0 flex h-full w-full";
    const positionMap = {
        "top-left": "items-start justify-start",
        "top-center": "items-start justify-center",
        "top-right": "items-start justify-end",
        "middle-left": "items-center justify-start",
        middle: "items-center justify-center",
        "middle-right": "items-center justify-end",
        "bottom-left": "items-end justify-start",
        "bottom-center": "items-end justify-center",
        "bottom-right": "items-end justify-end",
    };
    return `${baseClasses} ${positionMap[props.textPosition || "middle"]}`;
});

const textAlignClasses = computed(() => {
    const position = props.textPosition || "middle";
    if (position.includes("left")) return "text-left";
    if (position.includes("right")) return "text-right";
    return "text-center";
});

const overlayStyle = computed(() => {
    if (!props.overlayEnabled || !props.overlayColor) return {};
    // Always apply 40% opacity
    const color = hexToRgba(props.overlayColor, 0.4);
    return { backgroundColor: color };
});

const headingStyle = computed(() => {
    const defaultColor =
        showImage.value || props.backgroundColor ? "#FFFFFF" : "#000000";
    return { color: props.headingTextColor || defaultColor };
});

const descriptionStyle = computed(() => {
    const defaultColor =
        showImage.value || props.backgroundColor ? "#FFFFFF" : "#000000";
    return { color: props.descriptionTextColor || defaultColor };
});
</script>

<template>
    <BlockContainer
        :id="props.id ?? ''"
        :is-editor-mode="props.isEditorMode"
        @edit="handleEditClick"
        @delete="handleDelete"
        class="relative p-0"
    >
        <div
            class="relative aspect-[16/9] w-full overflow-hidden"
            :style="backgroundStyle"
        >
            <img
                v-if="showImage"
                :src="props.imageUrl ?? ''"
                :alt="props.altText ?? 'Hero image'"
                class="absolute inset-0 object-cover w-full h-full"
                loading="lazy"
                @error="
                    (e: Event) =>
                        ((e.target as HTMLImageElement).style.display = 'none')
                "
            />
            <!-- Overlay -->
            <div
                v-if="showImage && props.overlayEnabled"
                class="absolute inset-0 w-full h-full"
                :style="overlayStyle"
            ></div>

            <div
                v-if="
                    !showImage && !props.backgroundColor && props.isEditorMode
                "
                class="flex items-center justify-center w-full h-full"
            ></div>

            <div
                v-if="props.headingText || props.descriptionText"
                :class="textPositionClasses"
            >
                <div
                    class="max-w-3xl p-4 mx-4 space-y-1 sm:space-y-2 sm:p-6 md:p-8"
                    :class="textAlignClasses"
                >
                    <h1
                        v-if="props.headingText"
                        class="text-3xl font-bold leading-tight sm:text-4xl md:text-5xl"
                        :style="headingStyle"
                    >
                        {{ props.headingText }}
                    </h1>
                    <div
                        v-if="props.descriptionText"
                        class="flex items-center"
                        :class="[
                            textAlignClasses === 'text-center'
                                ? 'justify-center'
                                : textAlignClasses === 'text-right'
                                ? 'justify-end'
                                : 'justify-start',
                        ]"
                    >
                        <v-icon
                            v-if="props.descriptionIcon"
                            :icon="props.descriptionIcon"
                            class="mr-2"
                            size="small"
                            :color="descriptionStyle.color"
                        />
                        <p
                            class="text-base sm:text-lg md:text-xl"
                            :style="descriptionStyle"
                        >
                            {{ props.descriptionText }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </BlockContainer>
</template>
