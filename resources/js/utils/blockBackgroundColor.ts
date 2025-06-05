import type { BaseBlockProps } from "@/types/blocks";
import { useThemeColors } from "@/Composables/useThemeColors";
import { computed, toRef, type MaybeRef } from "vue";

/**
 * Determines the background color for a block based on its properties
 * @param blockProps - The block properties containing backgroundColor and useThemeBackground
 * @returns A computed ref with the hex color to use for the block background
 */
export function useBlockBackgroundColor(blockProps: MaybeRef<BaseBlockProps>) {
    const { colors } = useThemeColors();
    const props = toRef(blockProps);

    return computed(() => {
        const currentProps = props.value;
        
        // If explicitly set to not use theme background and has a custom color
        if (currentProps.useThemeBackground === false && currentProps.backgroundColor) {
            return currentProps.backgroundColor;
        }

        // Default to theme background color
        return colors.value.backgroundPrimary;
    });
}