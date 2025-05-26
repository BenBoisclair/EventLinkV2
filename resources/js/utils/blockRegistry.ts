import { defineAsyncComponent } from "vue";
import type { Component } from "vue";

export const blockRegistry = {
    Stats: defineAsyncComponent(
        () => import("@/Components/WebsiteBuilder/Blocks/StatsBlock/index.vue")
    ),
    Hero: defineAsyncComponent(
        () => import("@/Components/WebsiteBuilder/Blocks/HeroBlock/index.vue")
    ),
    AttendeesForm: defineAsyncComponent(
        () =>
            import(
                "@/Components/WebsiteBuilder/Blocks/AttendeesFormBlock/index.vue"
            )
    ),
    Description: defineAsyncComponent(
        () =>
            import(
                "@/Components/WebsiteBuilder/Blocks/DescriptionBlock/index.vue"
            )
    ),
    Countdown: defineAsyncComponent(
        () =>
            import(
                "@/Components/WebsiteBuilder/Blocks/CountdownBlock/index.vue"
            )
    ),
    ExhibitorShowcase: defineAsyncComponent(
        () =>
            import(
                "@/Components/WebsiteBuilder/Blocks/ExhibitorShowcaseBlock/index.vue"
            )
    ),
    Canvas: defineAsyncComponent(
        () => import("@/Components/WebsiteBuilder/Blocks/CanvasBlock/index.vue")
    ),
} as const;

export type BlockTypeKey = keyof typeof blockRegistry;

export function getBlockComponent(blockType: string): Component | null {
    if (!(blockType in blockRegistry)) {
        console.warn(`No component found for block type: ${blockType}`);
        return null;
    }
    return blockRegistry[blockType as BlockTypeKey];
}
