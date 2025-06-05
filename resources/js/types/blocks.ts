import type { FormField } from "@/types/form";
import type { DeviceType } from "@/types/websiteBuilder";

export interface BaseBlockProps {
    id?: string;
    device?: DeviceType;
}

export interface HeroBlockProps extends BaseBlockProps {
    imageUrl?: string | null;
    altText?: string;
    headingText?: string | null;
    descriptionText?: string | null;
    textPosition?:
        | "top-left"
        | "top-center"
        | "top-right"
        | "middle-left"
        | "middle"
        | "middle-right"
        | "bottom-left"
        | "bottom-center"
        | "bottom-right";
    icon?: string | null;
    descriptionIcon?: string | null;
    _pendingFile_imageUrl?: File | null;
    _originalImageUrl?: string | null;
    _imageUrl_uploadingToS3?: boolean;
    overlayEnabled?: boolean;
}

export interface DescriptionBlockProps extends BaseBlockProps {
    title?: string;
    description?: string;
}

export interface CountdownBlockProps extends BaseBlockProps {
    title?: string;
    startDate?: string | null;
    endDate?: string | null;
    useEventDates?: boolean;
    showDays?: boolean;
    showHours?: boolean;
    showMinutes?: boolean;
    showSeconds?: boolean;
    finishedText?: string;
    buttonText?: string;
    buttonLink?: string;
    buttonEnabled?: boolean;
}

export interface StatsBlockProps extends BaseBlockProps {
    stats?: Stat[];
}

export interface Stat {
    title: string;
    value: string;
}

export interface AttendeesFormBlockProps extends BaseBlockProps {
    title?: string;
    fields?: FormField[];
    event?: { id: string; name: string };
    buttonText?: string;
}

export interface ExhibitorShowcaseBlockProps extends BaseBlockProps {
    title?: string;
}

export interface CanvasBlockProps extends BaseBlockProps {
    canvasData?: string | null; // Base64 encoded image data
}

export interface BaseBlock {
    id: string;
}

export interface HeroBlock extends BaseBlock {
    type: "Hero";
    props: HeroBlockProps;
}

export interface DescriptionBlock extends BaseBlock {
    type: "Description";
    props: DescriptionBlockProps;
}

export interface CountdownBlock extends BaseBlock {
    type: "Countdown";
    props: CountdownBlockProps;
}

export interface StatsBlock extends BaseBlock {
    type: "Stats";
    props: StatsBlockProps;
}

export interface AttendeesFormBlock extends BaseBlock {
    type: "AttendeesForm";
    props: AttendeesFormBlockProps;
}

export interface ExhibitorShowcaseBlock extends BaseBlock {
    type: "ExhibitorShowcase";
    props: ExhibitorShowcaseBlockProps;
}

export interface CanvasBlock extends BaseBlock {
    type: "Canvas";
    props: CanvasBlockProps;
}

export type BlockType =
    | HeroBlock
    | DescriptionBlock
    | CountdownBlock
    | StatsBlock
    | AttendeesFormBlock
    | ExhibitorShowcaseBlock
    | CanvasBlock;
