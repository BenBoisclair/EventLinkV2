<script setup lang="ts">
import BlockContainer from "@/Components/WebsiteBuilder/Renderer/BlockContainer.vue";
import type { HeroBlockProps } from "@/types/blocks";
import { hexToRgba } from "@/utils/color";
import { computed, withDefaults } from "vue";
import { useThemeColors } from "@/Composables/useThemeColors";

const props = withDefaults(
    defineProps<
        HeroBlockProps & {
            websiteId: string | number;
        }
    >(),
    {
        textPosition: "middle",
        overlayEnabled: false,
    }
);

const { colors, utils } = useThemeColors();



const showImage = computed(() => !!props.imageUrl);

const backgroundStyle = computed(() => {
    if (!showImage.value) {
        return { backgroundColor: colors.value.backgroundPrimary };
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
    if (!props.overlayEnabled) return {};
    // Always apply 40% opacity using theme primary color
    const color = utils.addAlpha(colors.value.themePrimary, 0.4);
    return { backgroundColor: color };
});

const headingStyle = computed(() => {
    // Use theme-based text color that contrasts with background
    return { 
        color: showImage.value ? "#FFFFFF" : colors.value.textPrimary 
    };
});

const descriptionStyle = computed(() => {
    // Use theme-based secondary text color that contrasts with background
    return { 
        color: showImage.value ? "#FFFFFF" : colors.value.textSecondary 
    };
});
</script>

<template>
    <BlockContainer
        :background-color="showImage ? 'transparent' : colors.backgroundPrimary"
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
