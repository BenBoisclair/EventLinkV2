import type {
    BaseBlockProps,
    CountdownBlockProps,
    DescriptionBlockProps,
    ExhibitorShowcaseBlockProps,
    HeroBlockProps,
    StatsBlockProps,
    AttendeesFormBlockProps,
} from "./blocks";

export interface Block<T = Record<string, any>> {
    id: string;
    type: string;
    props: T;
}

export interface BlockSection {
    isOpen: boolean;
    items: Block[];
}

export type DeviceType = "desktop" | "tablet" | "mobile";

export type SaveState = "idle" | "saving" | "saved" | "error";

export interface Theme {
    primary: string;
    secondary: string;
    accent: string;
    background: string;
}

export interface Styling {
    borderRadius: "none" | "small" | "medium" | "large" | "full";
    buttonSize: "xs" | "sm" | "md" | "lg" | "xl";
    shadow: "none" | "xs" | "sm" | "md" | "lg" | "xl";
    buttonStyle: "solid" | "outline" | "ghost";
    animationSpeed: "none" | "slow" | "normal" | "fast";
    fontWeight: "light" | "normal" | "medium" | "semibold" | "bold";
    letterSpacing: "tight" | "normal" | "wide";
}
export interface BlockOption {
    type: string;
    title: string;
    description: string;
    icon: string;
}

export enum SaveErrorType {
    NETWORK = "network",
    VALIDATION = "validation",
    PERMISSION = "permission",
    NOT_FOUND = "not_found",
    CONFLICT = "conflict",
    UPLOAD_QUEUE = "upload_queue",
    SNAPSHOT_FAILED = "snapshot_failed",
    UNKNOWN = "unknown",
}

export interface SaveError {
    type: SaveErrorType;
    message: string;
    details?: any;
}
export interface HeroEditingState {
    _pendingFile?: File | null;
    _originalImageUrl?: string | null;
    imageUrl?: string | null;
}

/**
 * Represents the reactive props object for the block currently being edited.
 * It's a deep clone of the original block's props, potentially augmented
 * with temporary editing state (like `HeroEditingState` for Hero blocks).
 * Null if no block is being edited.
 */
export type EditingProps =
    | BaseBlockProps
    | HeroBlockProps
    | StatsBlockProps
    | CountdownBlockProps
    | AttendeesFormBlockProps
    | ExhibitorShowcaseBlockProps
    | null;
