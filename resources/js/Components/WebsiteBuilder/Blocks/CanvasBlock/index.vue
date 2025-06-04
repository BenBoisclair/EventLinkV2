<script setup lang="ts">
import BlockContainer from "@/Components/WebsiteBuilder/Renderer/BlockContainer.vue";
import type { CanvasBlockProps } from "@/types/blocks";
import axios from "axios";
import { debounce } from "lodash";
import { onMounted, onUnmounted, ref, watch } from "vue";

declare global {
    interface Window {
        Echo?: any;
    }
}

const props = defineProps<
    CanvasBlockProps & {
        websiteId: string | number;
    }
>();


const canvasRef = ref<HTMLCanvasElement | null>(null);
const isDrawing = ref(false);
const lastX = ref(0);
const lastY = ref(0);
const isSaving = ref(false);
const saveError = ref<string | null>(null);

// --- New State for Color and Stroke Size ---
const availableColors = ref([
    "#FFB6C1", // Light Pink
    "#ADD8E6", // Baby Blue
    "#98FB98", // Pale Green
    "#E6E6FA", // Lavender
    "#FFFACD",
]); // Black, Red, Blue, Green, Yellow
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
    console.log("Checking save conditions:", {
        canvasExists: !!canvas,
        blockId: props.id,
    });
    if (!canvas || !props.id) {
        console.error("Cannot save canvas: Missing canvas or block ID.");
        return;
    }

    const dataUrl = canvas.toDataURL("image/png");
    isSaving.value = true;
    saveError.value = null;

    try {
        console.log(`Saving canvas data for block ${props.id}...`);
        const response = await axios.patch(
            `/websites/${props.websiteId}/blocks/${props.id}/canvas`,
            { canvasData: dataUrl }
        );

        if (response.data.success) {
            console.log(
                `Canvas data saved successfully for block ${props.id}.`
            );
        } else {
            throw new Error(
                response.data.error || "Failed to save canvas data."
            );
        }
    } catch (error: any) {
        console.error("Error saving canvas data:", error);
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
        console.log(`Canvas loaded from data URL for block ${props.id}`);
    };
    img.onerror = (err) => {
        console.error("Error loading canvas image:", err);
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
            console.log(
                `Canvas data prop changed for block ${props.id}, reloading.`
            );
            loadCanvasState();
        }
    }
);

// Adjust listener functions to use global window.Echo
const listenForCanvasUpdates = () => {
    // Check if window.Echo is available
    if (
        !props.id ||
        typeof window === "undefined" ||
        !window.Echo
    ) {
        console.warn(
            "Echo not available, not listening for canvas updates."
        );
        return;
    }

    console.log(`Listening on channel: private-canvas-block.${props.id}`);
    window.Echo.private(`canvas-block.${props.id}`)
        .listen(
            ".canvas.updated",
            (event: { blockId: string; updated_at: number }) => {
                console.log("Received canvas update notification:", event);
                if (event.blockId === props.id && !isDrawing.value) {
                    // Instead of loading directly, fetch the latest data
                    console.log(
                        `Fetching latest canvas data for block ${props.id}...`
                    );
                    fetchLatestCanvasData();
                }
            }
        )
        .error((error: any) => {
            console.error(
                `Global Echo channel error for block ${props.id}:`,
                error
            );
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
            console.log(
                `Canvas cleared for block ${props.id} based on fetched data.`
            );
        } else {
            console.error(
                `Failed to fetch canvas data for block ${props.id}:`,
                response.data.error || "Unknown error"
            );
        }
    } catch (error: any) {
        console.error(
            `Error during fetchLatestCanvasData for block ${props.id}:`,
            error
        );
    }
};

const stopListeningForCanvasUpdates = () => {
    if (props.id && typeof window !== "undefined" && window.Echo) {
        console.log(
            `Stopping listening on channel: private-canvas-block.${props.id}`
        );
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
            class="bg-white border border-gray-300 dark:border-dark-border dark:bg-dark-surface cursor-crosshair"
            style="touch-action: none"
        ></canvas>

        <!-- Color & Size Controls -->
        <div class="absolute flex flex-col gap-2 left-2 top-2">
            <!-- Color Palette -->
            <div class="flex gap-1 p-1 rounded shadow">
                <button
                    v-for="color in availableColors"
                    :key="color"
                    @click="selectedColor = color"
                    :style="{ backgroundColor: color }"
                    class="w-6 h-6 border-2 rounded-full"
                    :class="{
                        'border-blue-500 ring-2 ring-blue-300':
                            selectedColor === color,
                        'border-gray-300': selectedColor !== color,
                    }"
                ></button>
            </div>
            <!-- Stroke Size Selector -->
            <div class="flex items-center gap-1 p-1 rounded shadow w-fit">
                <button
                    v-for="size in availableStrokeSizes"
                    :key="size"
                    @click="selectedStrokeSize = size"
                    class="flex items-center justify-center w-6 h-6 text-xs border-2 rounded"
                    :class="{
                        'border-blue-500 bg-blue-100 ring-2 ring-blue-300':
                            selectedStrokeSize === size,
                        'border-gray-300 bg-gray-50 hover:bg-gray-100':
                            selectedStrokeSize !== size,
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
