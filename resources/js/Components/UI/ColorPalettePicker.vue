<script setup lang="ts">
import InputLabel from "@/Components/Forms/InputLabel.vue";
import { computed, ref, watch, nextTick } from "vue";

interface Color {
    name: string; // e.g., "Slate 50"
    value: string; // e.g., "slate-50" (Tailwind class name without prefix)
    class: string; // e.g., "bg-slate-50"
}

interface Palette {
    name: string; // e.g., "Slate"
    colors: Color[];
}

// Helper to calculate contrast and determine text color
function isDarkColor(hexColor: string): boolean {
    if (!hexColor || !hexColor.startsWith("#") || hexColor.length < 7)
        return false; // Basic check
    try {
        const hex = hexColor.substring(1);
        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);
        // Simple luminance calculation (YIQ formula)
        const luminance = (r * 299 + g * 587 + b * 114) / 1000;
        return luminance < 128; // Threshold for dark/light
    } catch (e) {
        console.error("Error parsing hex color:", hexColor, e);
        return false; // Default to light background -> black text
    }
}

// Derived from the provided OKLCH/Hex values using browser's computed style once.
const tailwindToHexMap: Record<string, string> = {
    "red-50": "#fef2f2",
    "red-100": "#fee2e2",
    "red-200": "#fecaca",
    "red-300": "#fca5a5",
    "red-400": "#f87171",
    "red-500": "#ef4444",
    "red-600": "#dc2626",
    "red-700": "#b91c1c",
    "red-800": "#991b1b",
    "red-900": "#7f1d1d",
    "red-950": "#450a0a",
    "orange-50": "#fff7ed",
    "orange-100": "#ffedd5",
    "orange-200": "#fed7aa",
    "orange-300": "#fdba74",
    "orange-400": "#fb923c",
    "orange-500": "#f97316",
    "orange-600": "#ea580c",
    "orange-700": "#c2410c",
    "orange-800": "#9a3412",
    "orange-900": "#7c2d12",
    "orange-950": "#431407",
    "amber-50": "#fffbeb",
    "amber-100": "#fef3c7",
    "amber-200": "#fde68a",
    "amber-300": "#fcd34d",
    "amber-400": "#fbbf24",
    "amber-500": "#f59e0b",
    "amber-600": "#d97706",
    "amber-700": "#b45309",
    "amber-800": "#92400e",
    "amber-900": "#78350f",
    "amber-950": "#451a03",
    "yellow-50": "#fefce8",
    "yellow-100": "#fef9c3",
    "yellow-200": "#fef08a",
    "yellow-300": "#fde047",
    "yellow-400": "#facc15",
    "yellow-500": "#eab308",
    "yellow-600": "#ca8a04",
    "yellow-700": "#a16207",
    "yellow-800": "#854d0e",
    "yellow-900": "#713f12",
    "yellow-950": "#422006",
    "lime-50": "#f7fee7",
    "lime-100": "#ecfccb",
    "lime-200": "#d9f99d",
    "lime-300": "#bef264",
    "lime-400": "#a3e635",
    "lime-500": "#84cc16",
    "lime-600": "#65a30d",
    "lime-700": "#4d7c0f",
    "lime-800": "#3f6212",
    "lime-900": "#365314",
    "lime-950": "#1a2e05",
    "green-50": "#f0fdf4",
    "green-100": "#dcfce7",
    "green-200": "#bbf7d0",
    "green-300": "#86efac",
    "green-400": "#4ade80",
    "green-500": "#22c55e",
    "green-600": "#16a34a",
    "green-700": "#15803d",
    "green-800": "#166534",
    "green-900": "#14532d",
    "green-950": "#052e16",
    "emerald-50": "#ecfdf5",
    "emerald-100": "#d1fae5",
    "emerald-200": "#a7f3d0",
    "emerald-300": "#6ee7b7",
    "emerald-400": "#34d399",
    "emerald-500": "#10b981",
    "emerald-600": "#059669",
    "emerald-700": "#047857",
    "emerald-800": "#065f46",
    "emerald-900": "#064e3b",
    "emerald-950": "#022c22",
    "teal-50": "#f0fdfa",
    "teal-100": "#ccfbf1",
    "teal-200": "#99f6e4",
    "teal-300": "#5eead4",
    "teal-400": "#2dd4bf",
    "teal-500": "#14b8a6",
    "teal-600": "#0d9488",
    "teal-700": "#0f766e",
    "teal-800": "#115e59",
    "teal-900": "#134e4a",
    "teal-950": "#042f2e",
    "cyan-50": "#ecfeff",
    "cyan-100": "#cffafe",
    "cyan-200": "#a5f3fc",
    "cyan-300": "#67e8f9",
    "cyan-400": "#22d3ee",
    "cyan-500": "#06b6d4",
    "cyan-600": "#0891b2",
    "cyan-700": "#0e7490",
    "cyan-800": "#155e75",
    "cyan-900": "#164e63",
    "cyan-950": "#083344",
    "sky-50": "#f0f9ff",
    "sky-100": "#e0f2fe",
    "sky-200": "#bae6fd",
    "sky-300": "#7dd3fc",
    "sky-400": "#38bdf8",
    "sky-500": "#0ea5e9",
    "sky-600": "#0284c7",
    "sky-700": "#0369a1",
    "sky-800": "#075985",
    "sky-900": "#0c4a6e",
    "sky-950": "#082f49",
    "blue-50": "#eff6ff",
    "blue-100": "#dbeafe",
    "blue-200": "#bfdbfe",
    "blue-300": "#93c5fd",
    "blue-400": "#60a5fa",
    "blue-500": "#3b82f6",
    "blue-600": "#2563eb",
    "blue-700": "#1d4ed8",
    "blue-800": "#1e40af",
    "blue-900": "#1e3a8a",
    "blue-950": "#172554",
    "indigo-50": "#eef2ff",
    "indigo-100": "#e0e7ff",
    "indigo-200": "#c7d2fe",
    "indigo-300": "#a5b4fc",
    "indigo-400": "#818cf8",
    "indigo-500": "#6366f1",
    "indigo-600": "#4f46e5",
    "indigo-700": "#4338ca",
    "indigo-800": "#3730a3",
    "indigo-900": "#312e81",
    "indigo-950": "#1e1b4b",
    "violet-50": "#f5f3ff",
    "violet-100": "#ede9fe",
    "violet-200": "#ddd6fe",
    "violet-300": "#c4b5fd",
    "violet-400": "#a78bfa",
    "violet-500": "#8b5cf6",
    "violet-600": "#7c3aed",
    "violet-700": "#6d28d9",
    "violet-800": "#5b21b6",
    "violet-900": "#4c1d95",
    "violet-950": "#2e1065",
    "purple-50": "#faf5ff",
    "purple-100": "#f3e8ff",
    "purple-200": "#e9d5ff",
    "purple-300": "#d8b4fe",
    "purple-400": "#c084fc",
    "purple-500": "#a855f7",
    "purple-600": "#9333ea",
    "purple-700": "#7e22ce",
    "purple-800": "#6b21a8",
    "purple-900": "#581c87",
    "purple-950": "#3b0764",
    "fuchsia-50": "#fdf4ff",
    "fuchsia-100": "#fae8ff",
    "fuchsia-200": "#f5d0fe",
    "fuchsia-300": "#f0abfc",
    "fuchsia-400": "#e879f9",
    "fuchsia-500": "#d946ef",
    "fuchsia-600": "#c026d3",
    "fuchsia-700": "#a21caf",
    "fuchsia-800": "#86198f",
    "fuchsia-900": "#701a75",
    "fuchsia-950": "#4a044e",
    "pink-50": "#fdf2f8",
    "pink-100": "#fce7f3",
    "pink-200": "#fbcfe8",
    "pink-300": "#f9a8d4",
    "pink-400": "#f472b6",
    "pink-500": "#ec4899",
    "pink-600": "#db2777",
    "pink-700": "#be185d",
    "pink-800": "#9d174d",
    "pink-900": "#831843",
    "pink-950": "#500724",
    "rose-50": "#fff1f2",
    "rose-100": "#ffe4e6",
    "rose-200": "#fecdd3",
    "rose-300": "#fda4af",
    "rose-400": "#fb7185",
    "rose-500": "#f43f5e",
    "rose-600": "#e11d48",
    "rose-700": "#be123c",
    "rose-800": "#9f1239",
    "rose-900": "#881337",
    "rose-950": "#4c0519",
    "slate-50": "#f8fafc",
    "slate-100": "#f1f5f9",
    "slate-200": "#e2e8f0",
    "slate-300": "#cbd5e1",
    "slate-400": "#94a3b8",
    "slate-500": "#64748b",
    "slate-600": "#475569",
    "slate-700": "#334155",
    "slate-800": "#1e293b",
    "slate-900": "#0f172a",
    "slate-950": "#020617",
    "gray-50": "#f9fafb",
    "gray-100": "#f3f4f6",
    "gray-200": "#e5e7eb",
    "gray-300": "#d1d5db",
    "gray-400": "#9ca3af",
    "gray-500": "#6b7280",
    "gray-600": "#4b5563",
    "gray-700": "#374151",
    "gray-800": "#1f2937",
    "gray-900": "#111827",
    "gray-950": "#030712",
    "zinc-50": "#fafafa",
    "zinc-100": "#f4f4f5",
    "zinc-200": "#e4e4e7",
    "zinc-300": "#d4d4d8",
    "zinc-400": "#a1a1aa",
    "zinc-500": "#71717a",
    "zinc-600": "#52525b",
    "zinc-700": "#3f3f46",
    "zinc-800": "#27272a",
    "zinc-900": "#18181b",
    "zinc-950": "#09090b",
    "neutral-50": "#fafafa",
    "neutral-100": "#f5f5f5",
    "neutral-200": "#e5e5e5",
    "neutral-300": "#d4d4d4",
    "neutral-400": "#a3a3a3",
    "neutral-500": "#737373",
    "neutral-600": "#525252",
    "neutral-700": "#404040",
    "neutral-800": "#262626",
    "neutral-900": "#171717",
    "neutral-950": "#0a0a0a",
    "stone-50": "#fafaf9",
    "stone-100": "#f5f5f4",
    "stone-200": "#e7e5e4",
    "stone-300": "#d6d3d1",
    "stone-400": "#a8a29e",
    "stone-500": "#78716c",
    "stone-600": "#57534e",
    "stone-700": "#44403c",
    "stone-800": "#292524",
    "stone-900": "#1c1917",
    "stone-950": "#0c0a09",
    black: "#000000",
    white: "#ffffff",
};

// Helper to get Hex from map, provides fallback
function getHexFromMap(tailwindName: string): string {
    return tailwindToHexMap[tailwindName] || "#000000"; // Default to black if not found
}

// Helper to find Tailwind name from Hex (or return null)
function findTailwindNameFromHex(hexValue: string): string | null {
    for (const name in tailwindToHexMap) {
        if (tailwindToHexMap[name] === hexValue) {
            return name;
        }
    }
    return null;
}

const props = defineProps<{
    modelValue: string; // Expecting Hex, but might receive Tailwind name initially
    id?: string;
    label?: string;
    class?: string;
}>();

const emit = defineEmits<{
    (e: "update:modelValue", value: string): void; // Always emit Hex
}>();

const activeTab = ref<"styles" | "custom">("styles");
const internalModelValue = ref<string>("#000000"); // Always store Hex
const customColor = ref("#000000"); // Local state for custom input, always Hex
const isPickerVisible = ref(false); // State for picker visibility
const popoverPosition = ref({ top: 0, left: 0 }); // Position for fixed popover

const initializeState = () => {
    const initialValue = props.modelValue;
    if (initialValue) {
        if (initialValue.startsWith("#")) {
            internalModelValue.value = initialValue;
            customColor.value = initialValue;
            // Determine if the initial hex corresponds to a known style
            const isKnownStyleHex =
                Object.values(tailwindToHexMap).includes(initialValue);
            activeTab.value = isKnownStyleHex ? "styles" : "custom";
        } else {
            // Assume it's a Tailwind name
            internalModelValue.value = getHexFromMap(initialValue);
            activeTab.value = "styles";
            // customColor.value = internalModelValue.value; // Optional: Sync custom input even if starting on styles tab
        }
    } else {
        // Default to black if no initial value
        internalModelValue.value = "#000000";
        customColor.value = "#000000";
        activeTab.value = "styles";
    }
};

initializeState();

// --- Color Palette Generation ---
const colorNames = [
    "slate",
    "gray",
    "zinc",
    "neutral",
    "stone",
    "red",
    "orange",
    "amber",
    "yellow",
    "lime",
    "green",
    "emerald",
    "teal",
    "cyan",
    "sky",
    "blue",
    "indigo",
    "violet",
    "purple",
    "fuchsia",
    "pink",
    "rose",
];
const shades = [
    "50",
    "100",
    "200",
    "300",
    "400",
    "500",
    "600",
    "700",
    "800",
    "900",
    "950",
];

const palettes = computed<Palette[]>(() => {
    const generatedPalettes = colorNames.map((name) => ({
        name: name.charAt(0).toUpperCase() + name.slice(1),
        colors: shades.map((shade) => ({
            name: `${name.charAt(0).toUpperCase() + name.slice(1)} ${shade}`,
            value: `${name}-${shade}`,
            class: `bg-${name}-${shade}`,
        })),
    }));

    // Add Black and White
    generatedPalettes.push({
        name: "Other Colors",
        colors: [
            { name: "White", value: "white", class: "bg-white" },
            { name: "Black", value: "black", class: "bg-black" },
        ],
    });

    return generatedPalettes;
});

// Watch for external changes to modelValue
watch(
    () => props.modelValue,
    (newValue) => {
        let newHexValue = "#000000";
        let switchToTab: "styles" | "custom" | null = null;

        if (newValue) {
            if (newValue.startsWith("#")) {
                newHexValue = newValue;
                const isKnownStyleHex =
                    Object.values(tailwindToHexMap).includes(newValue);
                switchToTab = isKnownStyleHex ? "styles" : "custom";
            } else {
                // Assume Tailwind name
                newHexValue = getHexFromMap(newValue);
                switchToTab = "styles";
            }
        }

        // Only update if the derived hex is different from the current internal hex
        if (newHexValue !== internalModelValue.value) {
            internalModelValue.value = newHexValue;
            customColor.value = newHexValue; // Keep custom input synced

            // Switch tab only if needed
            if (switchToTab && activeTab.value !== switchToTab) {
                activeTab.value = switchToTab;
            }
        } else if (!newValue && internalModelValue.value !== "#000000") {
            // Handle case where modelValue becomes empty/null externally
            internalModelValue.value = "#000000";
            customColor.value = "#000000";
            activeTab.value = "styles";
        }
    }
);

// Watch for internal changes (always Hex) and emit / sync custom input
watch(internalModelValue, (newHexValue) => {
    if (newHexValue !== props.modelValue) {
        emit("update:modelValue", newHexValue);
    }
    // Keep custom color input synced
    if (newHexValue !== customColor.value) {
        customColor.value = newHexValue;
    }
});

function selectStyleColor(tailwindColorName: string) {
    activeTab.value = "styles";
    internalModelValue.value = getHexFromMap(tailwindColorName);
    isPickerVisible.value = false; // Close picker on selection
}

function handleCustomColorInput(event: Event) {
    const target = event.target as HTMLInputElement;
    const newHexValue = target.value;
    activeTab.value = "custom";
    // Update internal state. v-model handles customColor ref update.
    internalModelValue.value = newHexValue;
    // Debounce closing or wait for blur? For now, let's keep it open.
    // isPickerVisible.value = false; // Optional: close on input change
}

const isSelectedStyle = (tailwindColorName: string) => {
    const styleHex = getHexFromMap(tailwindColorName);
    return (
        activeTab.value === "styles" && internalModelValue.value === styleHex
    );
};

const isCustomColorSelected = () => {
    return activeTab.value === "custom";
};

const calculatePopoverPosition = (buttonRect: DOMRect, popoverHeight: number) => {
    const popoverWidth = 320; // w-80 = 20rem = 320px
    const margin = 8;
    const viewportHeight = window.innerHeight;
    const viewportWidth = window.innerWidth;
    const spaceBelow = viewportHeight - buttonRect.bottom;
    const spaceAbove = buttonRect.top;

    // Calculate vertical position - default to below
    let top: number;
    if (spaceBelow >= popoverHeight + margin) {
        // Position below (preferred)
        top = buttonRect.bottom + margin;
    } else if (spaceAbove >= popoverHeight) {
        // Position above, directly adjacent
        top = buttonRect.top - popoverHeight;
    } else {
        // Constrained space - choose best available position
        if (spaceBelow >= 200 || spaceBelow >= spaceAbove * 1.5) {
            top = Math.min(buttonRect.bottom + margin, viewportHeight - popoverHeight - margin);
        } else {
            top = Math.max(margin, buttonRect.top - popoverHeight);
        }
    }

    // Calculate horizontal position
    let left = buttonRect.left;
    if (left + popoverWidth > viewportWidth - margin) {
        left = buttonRect.right - popoverWidth;
    }
    if (left < margin) {
        left = margin;
    }

    return { top, left };
};

const togglePicker = () => {
    if (!isPickerVisible.value) {
        const button = document.getElementById(props.id || "colorSwatchButton");
        if (button) {
            const rect = button.getBoundingClientRect();
            
            // Show popover first, then calculate position with actual dimensions
            isPickerVisible.value = true;
            
            nextTick(() => {
                const popover = document.getElementById(`color-picker-popover-${props.id || "default"}`);
                const popoverHeight = popover?.offsetHeight ?? 400;
                
                popoverPosition.value = calculatePopoverPosition(rect, popoverHeight);
            });
            
            return;
        }
    }
    isPickerVisible.value = !isPickerVisible.value;
};

// --- Swatch Display Logic ---
const swatchStyle = computed(() => {
    const color = internalModelValue.value; // Always use internal hex
    if (color && color.startsWith("#")) {
        return { backgroundColor: color };
    }
    return {};
});

const swatchClass = computed(() => {
    const tailwindName = findTailwindNameFromHex(internalModelValue.value);
    // Use Tailwind class only if it's a known one and not black/white (which are handled by hex)
    if (tailwindName && tailwindName !== "black" && tailwindName !== "white") {
        return `bg-${tailwindName}`; // Use original Tailwind class if found
    }
    // Fallback for custom hex, black, white or not found
    return "";
});

const swatchTextStyleClass = computed(() => {
    const color = internalModelValue.value; // Always use internal hex
    return {
        "text-white": isDarkColor(color),
        "text-black": !isDarkColor(color),
    };
});

const swatchDisplayText = computed(() => {
    return internalModelValue.value || "Select color";
});

// Close picker when clicking outside
function handleClickOutside(event: MouseEvent) {
    const target = event.target as HTMLElement;
    const wrapper = document.getElementById(
        `color-picker-wrapper-${props.id || "default"}`
    );
    const popover = document.getElementById(
        `color-picker-popover-${props.id || "default"}`
    );

    // Check if the click is outside both the wrapper and the popover
    if (
        wrapper &&
        !wrapper.contains(target) &&
        popover &&
        !popover.contains(target)
    ) {
        isPickerVisible.value = false;
    }
}

watch(isPickerVisible, (newValue) => {
    if (newValue) {
        document.addEventListener("click", handleClickOutside, true);
    } else {
        document.removeEventListener("click", handleClickOutside, true);
    }
});

// Ensure cleanup on unmount
import { onUnmounted } from "vue";
onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside, true);
});
</script>

<template>
    <div :id="`color-picker-wrapper-${id || 'default'}`" class="relative">
        <InputLabel
            class="mb-1 dark:text-dark-text-secondary"
            v-if="label"
            :for="id || 'colorSwatchButton'"
            >{{ label }}</InputLabel
        >
        <button
            :id="id || 'colorSwatchButton'"
            type="button"
            class="flex items-center pl-3 text-left border border-gray-300 rounded dark:border-dark-border"
            :class="[swatchClass, props.class || 'w-14 h-14']"
            :style="swatchStyle"
            @click.stop="togglePicker"
        >
            <!-- <span :class="swatchTextStyleClass" class="text-sm">
                {{ swatchDisplayText }}
            </span> -->
        </button>

        <!-- Picker Popover -->
        <div
            v-if="isPickerVisible"
            :id="`color-picker-popover-${id || 'default'}`"
            class="fixed z-[9999] w-80 border border-gray-300 rounded-md bg-surface dark:bg-dark-surface dark:border-dark-border"
            :style="{
                top: popoverPosition.top + 'px',
                left: popoverPosition.left + 'px',
            }"
        >
            <!-- Tabs -->
            <div
                class="flex p-2 border-b border-gray-200 dark:border-dark-border"
            >
                <button
                    type="button"
                    @click="activeTab = 'styles'"
                    :class="[
                        'flex-1 rounded-lg px-4 py-2 text-center text-sm font-medium',
                        activeTab === 'styles'
                            ? 'dark:bg-dark-primary dark:text-dark-text-primary bg-blue-600 text-white'
                            : 'dark:text-dark-text-secondary dark:hover:bg-dark-surface-elevated dark:hover:text-dark-text-primary text-gray-500 hover:bg-gray-100 hover:text-gray-700',
                    ]"
                >
                    Styles
                </button>
                <button
                    type="button"
                    @click="activeTab = 'custom'"
                    :class="[
                        'flex-1 rounded-lg px-4 py-2 text-center text-sm font-medium',
                        activeTab === 'custom'
                            ? 'dark:bg-dark-primary dark:text-dark-text-primary bg-blue-600 text-white'
                            : 'dark:text-dark-text-secondary dark:hover:bg-dark-surface-elevated dark:hover:text-dark-text-primary text-gray-500 hover:bg-gray-100 hover:text-gray-700',
                    ]"
                >
                    Custom
                </button>
            </div>

            <!-- Tab Content -->
            <div class="p-4">
                <!-- Styles Tab -->
                <div
                    v-show="activeTab === 'styles'"
                    class="px-2 space-y-4 overflow-y-auto max-h-60"
                >
                    <div v-for="palette in palettes" :key="palette.name">
                        <h5
                            class="mb-2 text-xs font-semibold text-gray-500 uppercase dark:text-dark-text-tertiary"
                        >
                            {{ palette.name }}
                        </h5>
                        <div class="grid grid-cols-6 gap-2">
                            <button
                                v-for="color in palette.colors"
                                :key="color.value"
                                type="button"
                                :class="[
                                    'dark:border-dark-border h-6 w-6 rounded border border-gray-300 transition-transform duration-100 ease-in-out hover:scale-110',
                                    color.class,
                                    color.value === 'white'
                                        ? 'dark:border-dark-text-secondary border-gray-400'
                                        : '',
                                    isSelectedStyle(color.value)
                                        ? 'dark:ring-dark-primary dark:ring-offset-dark-surface ring-2 ring-blue-500 ring-offset-1'
                                        : '',
                                ]"
                                :title="`${color.name} (${getHexFromMap(
                                    color.value
                                )})`"
                                @click="selectStyleColor(color.value)"
                            ></button>
                        </div>
                    </div>
                </div>

                <!-- Custom Tab -->
                <div
                    v-show="activeTab === 'custom'"
                    class="flex items-center space-x-3"
                >
                    <!-- v-model handles binding with customColor ref -->
                    <input
                        id="customColorInput"
                        v-model="customColor"
                        type="color"
                        class="w-16 h-10 p-1 border border-gray-300 rounded-md cursor-pointer dark:border-dark-border"
                        @input="handleCustomColorInput"
                    />
                    <span
                        class="text-sm text-gray-700 dark:text-dark-text-secondary"
                    >
                        Hex: {{ internalModelValue }}
                        <!-- Display synced internal hex -->
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
