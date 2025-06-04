import type {
    AttendeesFormBlockProps,
    CanvasBlockProps,
    CountdownBlockProps,
    DescriptionBlockProps,
    ExhibitorShowcaseBlockProps,
    HeroBlockProps,
    StatsBlockProps,
} from "@/types/blocks";
import type { FormField } from "@/types/form";
import type { DeviceType } from "@/types/websiteBuilder";

interface BlockDefault<TProps> {
    component: string;
    props: TProps;
    title: string;
    description: string;
    icon: string;
}
interface BlockDefaultsMap {
    Stats: BlockDefault<StatsBlockProps>;
    Hero: BlockDefault<HeroBlockProps>;
    AttendeesForm: BlockDefault<AttendeesFormBlockProps>;
    Description: BlockDefault<DescriptionBlockProps>;
    Countdown: BlockDefault<CountdownBlockProps>;
    ExhibitorShowcase: BlockDefault<ExhibitorShowcaseBlockProps>;
    Canvas: BlockDefault<CanvasBlockProps>;
}

const createDefaultFormFields = (): FormField[] => [
    {
        name: "first_name",
        label: "First Name",
        type: "text",
        required: true,
        deletable: false,
    },
    {
        name: "last_name",
        label: "Last Name",
        type: "text",
        required: true,
        deletable: false,
    },
    {
        name: "email",
        label: "Email",
        type: "email",
        required: true,
        deletable: false,
    },
];

export const blockDefaults: BlockDefaultsMap = {
    Stats: {
        component: "StatsBlock",
        props: {
            stats: [
                { title: "Statistic 1", value: "00" },
                { title: "Statistic 2", value: "00" },
                { title: "Statistic 3", value: "00" },
                { title: "Statistic 4", value: "00" },
            ],
            device: "desktop" as DeviceType,
            backgroundColor: "#000000",
            textColor: "#FFFFFF",
        },
        title: "Stats Block",
        description:
            "Displays important metrics and key numbers with a customizable background.",
        icon: "ChartBarIcon",
    },
    Hero: {
        component: "HeroBlock",
        props: {
            imageUrl: null,
            altText: "Hero image description",
            device: "desktop" as DeviceType,
            headingText: "Headline Goes Here",
            descriptionText: "Enter a catchy description...",
            textPosition: "middle",
            headingTextColor: "#000000",
            descriptionTextColor: "#000000",
            backgroundColor: "#ffffff",
            overlayEnabled: false,
            overlayColor: "#000000",
            descriptionIcon: null,
        },
        title: "Hero Block",
        description: "A prominent full-width image or color block.",
        icon: "PhotoIcon",
    },
    AttendeesForm: {
        component: "AttendeesFormBlock",
        props: {
            title: "Register for the Event",
            fields: createDefaultFormFields(),
            device: "desktop" as DeviceType,
        },
        title: "Attendees Form",
        description: "A form for attendees to register for the event.",
        icon: "ClipboardDocumentListIcon",
    },
    Description: {
        component: "DescriptionBlock",
        props: {
            title: "About Section",
            description:
                "Provide details about your event or topic here. You can elaborate on the purpose, schedule, speakers, or any relevant information.",
            device: "desktop" as DeviceType,
        },
        title: "Description Block",
        description:
            "A block for displaying a title and detailed description text.",
        icon: "DocumentTextIcon",
    },
    Countdown: {
        component: "CountdownBlock",
        props: {
            title: "Event Starts In",
            startDate: null,
            endDate: null,
            device: "desktop" as DeviceType,
        },
        title: "Countdown",
        description: "Display a countdown timer to your event.",
        icon: "ClockIcon",
    },
    ExhibitorShowcase: {
        component: "ExhibitorShowcaseBlock",
        props: {
            title: "Meet Our Exhibitors",
            titleColor: null,
            backgroundColor: null,
            device: "desktop" as DeviceType,
        },
        title: "Exhibitor Showcase",
        description: "Displays a list of event exhibitors.",
        icon: "BuildingStorefrontIcon",
    },
    // Canvas: {
    //     component: "CanvasBlock",
    //     props: {
    //         canvasData: null,
    //         device: "desktop" as DeviceType,
    //     },
    //     title: "Canvas Block",
    //     description: "An interactive canvas for drawing.",
    //     icon: "PaintBrushIcon",
    // },
};
