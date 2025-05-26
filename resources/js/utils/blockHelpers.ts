import { blockDefaults } from '@/Components/WebsiteBuilder/Blocks/blockDefaults';
import {
    Block,
    CountdownBlockProps,
    ExhibitorShowcaseBlockProps,
    StatsBlockProps,
} from '@/types/blocks';

import { AttendeesFormBlockProps } from '@/types/blocks';

import { DescriptionBlockProps, HeroBlockProps } from '@/types/blocks';
import { EventType } from '@/types/event';

export type BlockSpecificPropsInitializer = (
    props:
        | HeroBlockProps
        | DescriptionBlockProps
        | CountdownBlockProps
        | StatsBlockProps
        | AttendeesFormBlockProps
        | ExhibitorShowcaseBlockProps, // Add to union
    event: EventType | null,
) => Record<string, any>;

/**
 * A map of block types to functions that provide specific initial properties
 * beyond the defaults, often incorporating theme colors or event data.
 */
const blockSpecificInitializers: Partial<
    Record<Block['type'], BlockSpecificPropsInitializer>
> = {
    Hero: (props) => {
        const heroProps = props as HeroBlockProps;
        return {
            ...heroProps,
            backgroundColor: heroProps.backgroundColor,
        };
    },
    AttendeesForm: (props) => {
        const attendeesFormProps = props as AttendeesFormBlockProps;
        return {
            ...attendeesFormProps,
            backgroundColor: attendeesFormProps.backgroundColor,
        };
    },
    Description: (props) => {
        // Correction: Use DescriptionBlockProps type assertion
        const descriptionProps = props as DescriptionBlockProps;
        return {
            ...descriptionProps,
            backgroundColor: descriptionProps.backgroundColor,
        };
    },
    Countdown: (props) => {
        const countdownProps = props as CountdownBlockProps;
        return {
            ...countdownProps,
            backgroundColor: countdownProps.backgroundColor,
            textColor: countdownProps.textColor ?? '#FFFFFF', // Default text color on primary background
        };
    },
    Stats: (props) => {
        const statsProps = props as StatsBlockProps;
        return {
            ...statsProps,
            backgroundColor: statsProps.backgroundColor,
            textColor: statsProps.textColor,
        };
    },
    ExhibitorShowcase: (props) => {
        const exhibitorProps = props as ExhibitorShowcaseBlockProps;
        return {
            ...exhibitorProps,
            // Default background to theme light if not set
            backgroundColor: exhibitorProps.backgroundColor,
        };
    },
};

/**
 * Calculates the initial properties for a new block, merging defaults
 * with type-specific initializations.
 * @param blockType - The type of the block to create.
 * @param  - The currently active theme's colors.
 * @param event - The current event context, if any.
 * @returns An object containing the fully initialized properties for the new block.
 */
export function calculateInitialProps(
    blockType: keyof typeof blockDefaults,
    event: EventType | null,
): Record<string, any> {
    // Return type is appropriate here
    const defaults = blockDefaults[blockType];
    // Start with a clone of the default props for this block type
    const baseProps = { ...defaults.props };

    const initializer = blockSpecificInitializers[blockType];

    // If a specific initializer exists for this block type, call it
    if (initializer) {
        // Type assertion needed because the initializer expects a specific prop type union
        const specificBaseProps = baseProps as
            | HeroBlockProps
            | DescriptionBlockProps
            | CountdownBlockProps
            | StatsBlockProps
            | AttendeesFormBlockProps
            | ExhibitorShowcaseBlockProps; // Add to union

        // Get the specifically initialized props
        const initializedProps = initializer(
            specificBaseProps,
            event, // Pass minimal event data
        );

        // Merge the base props with the initialized props (initialized overrides base)
        return { ...baseProps, ...initializedProps };
    } else {
        // If no specific initializer, just return the base default props
        return baseProps;
    }
}
