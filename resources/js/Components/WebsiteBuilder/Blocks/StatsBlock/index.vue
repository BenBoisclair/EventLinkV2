<script setup lang="ts">
import BlockContainer from "@/Components/WebsiteBuilder/Renderer/BlockContainer.vue";
import type { StatsBlockProps } from "@/types/blocks";
import { computed, withDefaults } from "vue";
import { useThemeColors } from "@/Composables/useThemeColors";

const props = withDefaults(defineProps<StatsBlockProps & {
    theme?: {
        primary: string;
        secondary: string;
        accent: string;
        background: string;
    };
}>(), {});

const { colors } = useThemeColors(props.theme);

const blockBackgroundColor = computed(() => {
    if (props.useThemeBackground !== false) {
        return colors.value.backgroundPrimary;
    }
    return props.backgroundColor || colors.value.backgroundPrimary;
});
</script>

<template>
    <BlockContainer
        :background-color="blockBackgroundColor"
        class="py-12 md:py-16"
        :style="{ color: colors.textPrimary }"
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
