<script setup lang="ts">
import BlockContainer from "@/Components/WebsiteBuilder/Renderer/BlockContainer.vue";
import type { DescriptionBlockProps } from "@/types/blocks";
import { getContrastingTextColor } from "@/utils/color";
import { computed, withDefaults } from "vue";
import BlockTitle from "../BlockTitle.vue";
import { useThemeColors } from "@/Composables/useThemeColors";

const props = withDefaults(
    defineProps<DescriptionBlockProps>(),
    {}
);

const { colors } = useThemeColors();


const textColor = computed(() => {
    return colors.value.textPrimary;
});
</script>

<template>
    <BlockContainer
        :background-color="colors.backgroundPrimary"
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
