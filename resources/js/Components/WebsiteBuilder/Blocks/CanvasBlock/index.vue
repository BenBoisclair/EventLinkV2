<script setup lang="ts">
import BlockContainer from "@/Components/WebsiteBuilder/Renderer/BlockContainer.vue";
import type { CanvasBlockProps } from "@/types/blocks";
import axios from "axios";
import { debounce } from "lodash";
import { onMounted, onUnmounted, ref, watch, computed } from "vue";
import { useThemeColors } from "@/Composables/useThemeColors";

declare global {
    interface Window {
        Echo?: any;
    }
}

const props = defineProps<
    CanvasBlockProps & {
        websiteId: string | number;
        theme?: {
            primary: string;
            secondary: string;
            accent: string;
            background: string;
        };
    }
>();

const { colors } = useThemeColors(props.theme);

const blockBackgroundColor = computed(() => {
    if (props.useThemeBackground !== false) {
        return colors.value.backgroundPrimary;
    }
    return props.backgroundColor || colors.value.backgroundPrimary;
});

const canvasRef = ref<HTMLCanvasElement | null>(null);
const isDrawing = ref(false);
const lastX = ref(0);
const lastY = ref(0);
const isSaving = ref(false);
const saveError = ref<string | null>(null);

// Dynamic theme-based color palette
const availableColors = computed(() => [
    colors.value.themePrimary,
    colors.value.themeSecondary,
    colors.value.themeAccent,
    "#FFB6C1", // Light Pink
    "#ADD8E6", // Baby Blue
]);
const selectedColor = ref(availableColors.value[0]);

const availableStrokeSizes = ref([1, 3, 5]); // Small, Medium, Large
const selectedStrokeSize = ref(availableStrokeSizes.value[1]);
// --- End New State ---

const getContext = (): CanvasRenderingContext2D | null => {
    return canvasRef.value?.getContext("2d") || null;
};

const startDrawing = (event: MouseEvent | TouchEvent) => {
    const canvas = canvasRef.value;
    if (!canvas) return;
    isDrawing.value = true;
    const { x, y } = getCoordinates(event);
    [lastX.value, lastY.value] = [x, y];

    const ctx = getContext();
    if (ctx) {
        drawDot(ctx, x, y);
    }
};

const draw = (event: MouseEvent | TouchEvent) => {
    if (!isDrawing.value) return;
    const ctx = getContext();
    if (!ctx) return;

    const { x, y } = getCoordinates(event);

    ctx.strokeStyle = selectedColor.value;
    ctx.lineWidth = selectedStrokeSize.value;
    ctx.lineCap = "round";
    ctx.lineJoin = "round";

    ctx.beginPath();
    ctx.moveTo(lastX.value, lastY.value);
    ctx.lineTo(x, y);
    ctx.stroke();

    [lastX.value, lastY.value] = [x, y];
};

const stopDrawing = () => {
    if (!isDrawing.value) return;
    isDrawing.value = false;
    debouncedSaveCanvasStateToBackend();
};

const drawDot = (ctx: CanvasRenderingContext2D, x: number, y: number) => {
    ctx.fillStyle = selectedColor.value;
    ctx.beginPath();
    ctx.arc(x, y, selectedStrokeSize.value / 2, 0, Math.PI * 2);
    ctx.fill();
};

const getCoordinates = (
    event: MouseEvent | TouchEvent
): { x: number; y: number } => {
    const canvas = canvasRef.value;
    if (!canvas) return { x: 0, y: 0 };
    const rect = canvas.getBoundingClientRect();
    let clientX, clientY;

    if (event instanceof MouseEvent) {
        clientX = event.clientX;
        clientY = event.clientY;
    } else if (event.touches && event.touches.length > 0) {
        clientX = event.touches[0].clientX;
        clientY = event.touches[0].clientY;
    } else {
        return { x: 0, y: 0 };
    }

    return {
        x: clientX - rect.left,
        y: clientY - rect.top,
    };
};

const debouncedSaveCanvasStateToBackend = debounce(async () => {
    const canvas = canvasRef.value;
    if (!canvas || !props.id) {
        return;
    }

    const dataUrl = canvas.toDataURL("image/png");
    isSaving.value = true;
    saveError.value = null;

    try {
        const response = await axios.patch(
            `/websites/${props.websiteId}/blocks/${props.id}/canvas`,
            { canvasData: dataUrl }
        );

        if (!response.data.success) {
            throw new Error(
                response.data.error || "Failed to save canvas data."
            );
        }
    } catch (error: any) {
        saveError.value =
            error.response?.data?.error ||
            error.message ||
            "An unknown error occurred while saving.";
    } finally {
        isSaving.value = false;
    }
}, 3000);

const loadCanvasFromDataUrl = (dataUrl: string) => {
    const canvas = canvasRef.value;
    const ctx = getContext();
    if (!canvas || !ctx) return;

    const img = new Image();
    img.onload = () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0);
    };
    img.onerror = () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    };
    img.src = dataUrl;
};

const loadCanvasState = () => {
    if (props.canvasData) {
        loadCanvasFromDataUrl(props.canvasData);
    } else {
        const canvas = canvasRef.value;
        const ctx = getContext();
        ctx?.clearRect(0, 0, canvas?.width ?? 0, canvas?.height ?? 0);
    }
};

onMounted(() => {
    loadCanvasState();

    const canvas = canvasRef.value;
    if (canvas) {
        canvas.addEventListener("mousedown", startDrawing);
        canvas.addEventListener("mousemove", draw);
        canvas.addEventListener("mouseup", stopDrawing);
        canvas.addEventListener("mouseleave", stopDrawing);
        canvas.addEventListener("touchstart", startDrawing, { passive: false });
        canvas.addEventListener("touchmove", draw, { passive: false });
        canvas.addEventListener("touchend", stopDrawing);
        canvas.addEventListener("touchcancel", stopDrawing);
    }

    // Use global Echo instance
    listenForCanvasUpdates();
});

onUnmounted(() => {
    const canvas = canvasRef.value;
    if (canvas) {
        canvas.removeEventListener("mousedown", startDrawing);
        canvas.removeEventListener("mousemove", draw);
        canvas.removeEventListener("mouseup", stopDrawing);
        canvas.removeEventListener("mouseleave", stopDrawing);
        canvas.removeEventListener("touchstart", startDrawing);
        canvas.removeEventListener("touchmove", draw);
        canvas.removeEventListener("touchend", stopDrawing);
        canvas.removeEventListener("touchcancel", stopDrawing);
    }
    debouncedSaveCanvasStateToBackend.cancel();
    stopListeningForCanvasUpdates(); // Stop listening
});

watch(
    () => props.canvasData,
    (newData, oldData) => {
        if (newData !== oldData && !isDrawing.value) {
            loadCanvasState();
        }
    }
);

// Adjust listener functions to use global window.Echo
const listenForCanvasUpdates = () => {
    // Check if window.Echo is available
    if (!props.id || typeof window === "undefined" || !window.Echo) {
        return;
    }

    window.Echo.private(`canvas-block.${props.id}`)
        .listen(
            ".canvas.updated",
            (event: { blockId: string; updated_at: number }) => {
                if (event.blockId === props.id && !isDrawing.value) {
                    fetchLatestCanvasData();
                }
            }
        )
        .error(() => {
            // Handle Echo channel errors silently
        });
};

const fetchLatestCanvasData = async () => {
    if (!props.id) return;
    try {
        const response = await axios.get(`/blocks/${props.id}/canvas`);
        if (response.data.success && response.data.canvasData) {
            // Use the existing function to load the fetched data
            loadCanvasFromDataUrl(response.data.canvasData);
        } else if (response.data.success && !response.data.canvasData) {
            // Handle case where canvas might be cleared
            const canvas = canvasRef.value;
            const ctx = getContext();
            ctx?.clearRect(0, 0, canvas?.width ?? 0, canvas?.height ?? 0);
        }
    } catch (error: any) {
        // Handle fetch errors silently
    }
};

const stopListeningForCanvasUpdates = () => {
    if (props.id && typeof window !== "undefined" && window.Echo) {
        window.Echo.leaveChannel(`private-canvas-block.${props.id}`);
    }
};
</script>

<template>
    <BlockContainer
        class="relative flex max-h-[700px] items-center justify-center overflow-hidden p-0"
    >
        <canvas
            ref="canvasRef"
            width="1500"
            height="1000"
            class="border cursor-crosshair"
            :style="{
                touchAction: 'none',
                backgroundColor: blockBackgroundColor,
                borderColor: colors.borderColor,
            }"
        ></canvas>

        <!-- Color & Size Controls -->
        <div class="absolute flex flex-col gap-2 left-2 top-2">
            <!-- Color Palette -->
            <div
                class="flex gap-1 p-1 rounded shadow"
                :style="{ backgroundColor: colors.backgroundSecondary }"
            >
                <button
                    v-for="color in availableColors"
                    :key="color"
                    @click="selectedColor = color"
                    :style="{ backgroundColor: color }"
                    class="w-6 h-6 border-2 rounded-full"
                ></button>
            </div>
            <!-- Stroke Size Selector -->
            <div
                class="flex items-center gap-1 p-1 rounded shadow w-fit"
                :style="{ backgroundColor: colors.backgroundSecondary }"
            >
                <button
                    v-for="size in availableStrokeSizes"
                    :key="size"
                    @click="selectedStrokeSize = size"
                    class="flex items-center justify-center w-6 h-6 text-xs transition-colors border-2 rounded"
                    :style="{
                        borderColor:
                            selectedStrokeSize === size
                                ? colors.themePrimary
                                : colors.borderColor,
                        backgroundColor:
                            selectedStrokeSize === size
                                ? colors.backgroundSecondary
                                : colors.backgroundPrimary,
                        color: colors.textPrimary,
                    }"
                >
                    {{ size }}px
                </button>
            </div>
        </div>
        <!-- End Controls -->

        <div
            v-if="isSaving"
            class="absolute px-2 py-1 text-xs text-white bg-gray-700 rounded opacity-75 bottom-2 right-2"
        >
            Saving...
        </div>
        <div
            v-if="saveError"
            class="absolute px-2 py-1 text-xs text-white bg-red-700 rounded bottom-2 left-2 opacity-90"
        >
            Error: {{ saveError }}
        </div>
    </BlockContainer>
</template>
