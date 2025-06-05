<script setup lang="ts">
import BlockContainer from "@/Components/WebsiteBuilder/Renderer/BlockContainer.vue";
import type { DescriptionBlockProps } from "@/types/blocks";
import { computed, withDefaults } from "vue";
import BlockTitle from "../BlockTitle.vue";
import { useThemeColors } from "@/Composables/useThemeColors";

interface Props extends DescriptionBlockProps {
    theme?: {
        primary: string;
        secondary: string;
        accent: string;
        background: string;
    };
}

const props = withDefaults(
    defineProps<Props>(),
    {}
);

const { colors } = useThemeColors(props.theme);
</script>

<template>
    <BlockContainer
        :background-color="colors.backgroundPrimary"
        class="px-6 py-12 sm:px-8 md:px-16 md:py-16"
    >
        <div class="max-w-3xl mx-auto">
            <BlockTitle
                :title="props.title"
                :title-color="colors.textPrimary"
                tag="h2"
                text-align="left"
                default-classes="mb-4 text-2xl font-bold sm:text-3xl md:mb-6"
            />
            <p
                class="text-base whitespace-pre-line sm:text-lg"
                :style="{ color: colors.textPrimary }"
            >
                {{ props.description }}
            </p>
        </div>
    </BlockContainer>
</template>
