import { computed } from "vue";
import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import { storeToRefs } from "pinia";
import type { Styling } from "@/types/websiteBuilder";

/**
 * Maps styling values to their corresponding CSS classes
 */
const STYLING_MAPPINGS = {
    borderRadius: {
        none: "rounded-none",
        small: "rounded",
        medium: "rounded-lg", 
        large: "rounded-xl",
        full: "rounded-full"
    },
    buttonSize: {
        xs: "text-xs px-2 py-1",
        sm: "text-sm px-3 py-1.5",
        md: "text-base px-4 py-2",
        lg: "text-lg px-5 py-2.5", 
        xl: "text-xl px-6 py-3"
    },
    shadow: {
        none: "shadow-none",
        xs: "shadow-xs",
        sm: "shadow-sm",
        md: "shadow-md",
        lg: "shadow-lg",
        xl: "shadow-xl"
    },
    buttonStyle: {
        solid: "border-none",
        outline: "border-2 bg-transparent",
        ghost: "border-none bg-transparent"
    },
    animationSpeed: {
        none: "duration-0",
        slow: "duration-300",
        normal: "duration-150",
        fast: "duration-75"
    },
    fontWeight: {
        light: "font-light",
        normal: "font-normal", 
        medium: "font-medium",
        semibold: "font-semibold",
        bold: "font-bold"
    },
    letterSpacing: {
        tight: "tracking-tight",
        normal: "tracking-normal",
        wide: "tracking-wide"
    }
};

/**
 * Composable that provides semantic styling mappings based on the current styling configuration
 */
export function useStyling() {
    const websiteBuilderStore = useWebsiteBuilderStore();
    const { styling } = storeToRefs(websiteBuilderStore);

    // Semantic styling mappings that update reactively with styling changes
    const styles = computed(() => {
        const currentStyling = styling.value;

        return {
            // Button styles
            buttonBase: `${STYLING_MAPPINGS.borderRadius[currentStyling.borderRadius]} ${STYLING_MAPPINGS.buttonSize[currentStyling.buttonSize]} ${STYLING_MAPPINGS.shadow[currentStyling.shadow]} ${STYLING_MAPPINGS.animationSpeed[currentStyling.animationSpeed]} ${STYLING_MAPPINGS.fontWeight[currentStyling.fontWeight]} ${STYLING_MAPPINGS.letterSpacing[currentStyling.letterSpacing]} transition-colors ease-in-out`,
            buttonSolid: `${STYLING_MAPPINGS.buttonStyle.solid}`,
            buttonOutline: `${STYLING_MAPPINGS.buttonStyle.outline}`,
            buttonGhost: `${STYLING_MAPPINGS.buttonStyle.ghost}`,

            // Individual style components
            borderRadius: STYLING_MAPPINGS.borderRadius[currentStyling.borderRadius],
            buttonSize: STYLING_MAPPINGS.buttonSize[currentStyling.buttonSize],
            shadow: STYLING_MAPPINGS.shadow[currentStyling.shadow],
            animationSpeed: STYLING_MAPPINGS.animationSpeed[currentStyling.animationSpeed],
            fontWeight: STYLING_MAPPINGS.fontWeight[currentStyling.fontWeight],
            letterSpacing: STYLING_MAPPINGS.letterSpacing[currentStyling.letterSpacing],

            // Card and container styles
            cardBase: `${STYLING_MAPPINGS.borderRadius[currentStyling.borderRadius]} ${STYLING_MAPPINGS.shadow[currentStyling.shadow]}`,
            
            // Input styles
            inputBase: `${STYLING_MAPPINGS.borderRadius[currentStyling.borderRadius]} ${STYLING_MAPPINGS.animationSpeed[currentStyling.animationSpeed]} transition-colors ease-in-out`,

            // Text styles
            headingBase: `${STYLING_MAPPINGS.fontWeight[currentStyling.fontWeight]} ${STYLING_MAPPINGS.letterSpacing[currentStyling.letterSpacing]}`,
            bodyBase: `${STYLING_MAPPINGS.letterSpacing[currentStyling.letterSpacing]}`,

            // Interactive element styles  
            interactiveBase: `${STYLING_MAPPINGS.animationSpeed[currentStyling.animationSpeed]} transition-all ease-in-out`,
        };
    });

    // Utility functions
    const utils = {
        /**
         * Get custom button class combination
         */
        getButtonClasses: (variant: "solid" | "outline" | "ghost" = "solid") => {
            const baseClasses = styles.value.buttonBase;
            const variantClasses = styles.value[`button${variant.charAt(0).toUpperCase() + variant.slice(1)}` as keyof typeof styles.value];
            return `${baseClasses} ${variantClasses}`;
        },

        /**
         * Get card classes with optional elevation override
         */
        getCardClasses: (shadowOverride?: keyof typeof STYLING_MAPPINGS.shadow) => {
            const borderRadius = styles.value.borderRadius;
            const shadow = shadowOverride ? STYLING_MAPPINGS.shadow[shadowOverride] : styles.value.shadow;
            return `${borderRadius} ${shadow}`;
        },

        /**
         * Get input classes
         */
        getInputClasses: () => {
            return styles.value.inputBase;
        },

        /**
         * Get heading classes with optional weight override
         */
        getHeadingClasses: (weightOverride?: keyof typeof STYLING_MAPPINGS.fontWeight) => {
            const letterSpacing = styles.value.letterSpacing;
            const fontWeight = weightOverride ? STYLING_MAPPINGS.fontWeight[weightOverride] : styles.value.fontWeight;
            return `${fontWeight} ${letterSpacing}`;
        },

        /**
         * Get specific styling class for a property
         */
        getStylingClass: (
            property: keyof typeof STYLING_MAPPINGS,
            value?: string
        ) => {
            const currentValue = value || styling.value[property];
            return STYLING_MAPPINGS[property][currentValue as keyof typeof STYLING_MAPPINGS[typeof property]];
        },
    };

    return {
        styles,
        utils,
        // Direct access to styling for edge cases
        styling,
        // Raw styling mappings for custom usage
        STYLING_MAPPINGS,
    };
}