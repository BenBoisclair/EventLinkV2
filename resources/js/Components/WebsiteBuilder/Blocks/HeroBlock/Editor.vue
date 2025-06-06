<script setup lang="ts">
import Input from "@/Components/Forms/Input.vue";
import InputLabel from "@/Components/Forms/InputLabel.vue";
import Button from "@/Components/UI/Button.vue";
import Checkbox from "@/Components/UI/Checkbox.vue";
import Toggle from "@/Components/Forms/Toggle.vue";
import Upload from "@/Components/UI/Upload.vue";
import ColorPalettePicker from "@/Components/UI/ColorPalettePicker.vue";
import { useBlockEditor } from "@/Composables/useBlockEditor";
import type { HeroBlockProps } from "@/types/blocks";
import { rgbaToHex } from "@/utils/color";
import { computed, ref, onMounted, onUnmounted, type PropType } from "vue";
import Spacer from "@/Components/UI/Spacer.vue";

const props = defineProps({
    initialProps: {
        type: Object as PropType<HeroBlockProps>,
        required: true,
    },
    websiteId: {
        type: Number,
        required: true,
    },
    blockIdProp: {
        type: String,
        required: true,
    },
});

const { currentProps, updateProperty } = useBlockEditor<HeroBlockProps>(
    props.blockIdProp
);

// Local state for image upload
const selectedFile = ref<File | null>(null);
const fileInputRef = ref<HTMLInputElement | null>(null);
const uploadError = ref(false);
const uploadErrorMessage = ref<string | null>(null);
const isDescriptionIconTooltipOpen = ref(false);

// Edit mode toggle - track manually to avoid switching back to color when imageUrl is cleared
const editMode = ref<"image" | "color">("color");

// Map icon file names to MDI aliases
const availableIconsMap: Record<string, string> = {
    "clock.svg": "$clockOutline",
    "location.svg": "$mapMarkerOutline",
    "calendar.svg": "$calendarOutline",
    "ticket.svg": "$ticketOutline",
    "info.svg": "$informationOutline",
    "attendees.svg": "$accountGroupOutline",
    "venue.svg": "$domain",
    "price.svg": "$currencyUsd",
    "speaker.svg": "$microphoneOutline",
    "food.svg": "$foodOutline",
};
const availableIcons = Object.keys(availableIconsMap);

// Preview URL for selected file
const previewImageUrl = computed(() => {
    if (selectedFile.value) {
        return URL.createObjectURL(selectedFile.value);
    }
    return currentProps.value?.imageUrl || null;
});

const handleFileSelect = (event: Event): void => {
    uploadError.value = false;
    uploadErrorMessage.value = null;

    const input = event.target as HTMLInputElement;
    const files = input.files;
    if (!files || files.length === 0) return;

    const file = files[0];

    if (!["image/png", "image/jpeg", "image/jpg"].includes(file.type)) {
        uploadError.value = true;
        uploadErrorMessage.value = "Please select a PNG, JPG, or JPEG file.";
        selectedFile.value = null;
        if (fileInputRef.value) fileInputRef.value.value = "";
        return;
    }

    // Check file size (10MB = 10,485,760 bytes)
    if (file.size > 10485760) {
        uploadError.value = true;
        uploadErrorMessage.value = "File size must be 10MB or smaller.";
        selectedFile.value = null;
        if (fileInputRef.value) fileInputRef.value.value = "";
        return;
    }

    // Store the file for later upload when save is clicked
    selectedFile.value = file;
    updateProperty("_pendingFile_imageUrl", file);
    // Set a preview URL for the selected file
    const previewUrl = URL.createObjectURL(file);
    updateProperty("imageUrl", previewUrl);
    updateProperty("backgroundColor", null);
};

const handleDescriptionIconSelect = (icon: string) => {
    updateProperty("descriptionIcon", availableIconsMap[icon] || null);
    isDescriptionIconTooltipOpen.value = false;
};

const handleDescriptionIconRemove = () => {
    updateProperty("descriptionIcon", null);
};

const selectTextPosition = (position: HeroBlockProps["textPosition"]) => {
    updateProperty("textPosition", position);
};

const switchToImageMode = () => {
    editMode.value = "image";
    // Only clear backgroundColor when actually switching to image mode
    // Don't clear it if we're just toggling the theme background setting
    updateProperty("backgroundColor", null);
    updateProperty("useThemeBackground", null);
};

const switchToColorMode = () => {
    editMode.value = "color";
    // Clean up any object URLs to prevent memory leaks
    const currentImageUrl = currentProps.value?.imageUrl;
    if (currentImageUrl && currentImageUrl.startsWith("blob:")) {
        URL.revokeObjectURL(currentImageUrl);
    }
    updateProperty("imageUrl", null);
    updateProperty("_pendingFile_imageUrl", null);
    // Set defaults for color mode if not already set
    if (currentProps.value?.useThemeBackground === undefined) {
        updateProperty("useThemeBackground", true); // Default to theme background
    }
    if (
        !currentProps.value?.backgroundColor &&
        !currentProps.value?.useThemeBackground
    ) {
        updateProperty("backgroundColor", "#3b82f6"); // Default blue color
    }
    selectedFile.value = null;
    if (fileInputRef.value) fileInputRef.value.value = "";
};

onMounted(() => {
    if (
        currentProps.value?.imageUrl ||
        currentProps.value?._pendingFile_imageUrl
    ) {
        editMode.value = "image";
    } else {
        editMode.value = "color";
    }
});

onUnmounted(() => {
    const currentImageUrl = currentProps.value?.imageUrl;
    if (currentImageUrl && currentImageUrl.startsWith("blob:")) {
        URL.revokeObjectURL(currentImageUrl);
    }
});
</script>

<template>
    <div v-if="currentProps" class="flex flex-col h-full gap-5">
        <div>
            <InputLabel
                value="Heading Text"
                class="mb-1 dark:text-dark-text-primary"
            />
            <Input
                :model-value="currentProps.headingText || ''"
                @update:model-value="updateProperty('headingText', $event)"
                type="text"
                class="w-full text-sm dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
                placeholder="Enter heading text"
            />
        </div>

        <div>
            <InputLabel
                value="Description Text"
                class="mb-1 dark:text-dark-text-primary"
            />
            <div class="flex items-start gap-2">
                <div class="relative flex-shrink-0">
                    <button
                        @click="
                            isDescriptionIconTooltipOpen =
                                !isDescriptionIconTooltipOpen
                        "
                        class="flex items-center justify-center w-10 h-10 text-gray-600 border rounded dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-secondary dark:hover:bg-dark-surface-elevated/80 bg-gray-50 hover:bg-gray-100"
                        title="Select description icon"
                        type="button"
                    >
                        <v-icon
                            v-if="currentProps.descriptionIcon"
                            :icon="currentProps.descriptionIcon"
                            size="small"
                            aria-label="Selected icon"
                        />
                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="dark:stroke-dark-text-secondary"
                        >
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </button>
                    <div
                        v-if="isDescriptionIconTooltipOpen"
                        class="absolute left-0 z-20 w-48 p-2 mt-1 border rounded-md shadow-lg bg-surface dark:bg-dark-surface-elevated dark:border-dark-border"
                    >
                        <div class="grid grid-cols-5 gap-1">
                            <button
                                v-for="icon in availableIcons"
                                :key="icon"
                                @click="handleDescriptionIconSelect(icon)"
                                class="flex items-center justify-center p-1 rounded dark:hover:bg-dark-accent hover:bg-gray-100"
                            >
                                <v-icon
                                    :icon="availableIconsMap[icon]"
                                    size="small"
                                    :aria-label="icon"
                                    class="dark:text-dark-text-primary"
                                />
                            </button>
                        </div>
                        <button
                            v-if="currentProps.descriptionIcon"
                            @click="handleDescriptionIconRemove"
                            class="w-full p-1 mt-2 text-xs text-red-600 rounded dark:text-dark-status-red dark:hover:bg-dark-status-red/10 hover:bg-red-50"
                        >
                            Remove Icon
                        </button>
                    </div>
                </div>
                <textarea
                    :model-value="currentProps.descriptionText"
                    @input="
                        updateProperty(
                            'descriptionText',
                            ($event.target as HTMLTextAreaElement).value
                        )
                    "
                    class="dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary dark:focus:ring-dark-primary w-full rounded-md border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 resize-none"
                    placeholder="Enter description text"
                    rows="3"
                ></textarea>
            </div>
        </div>

        <div>
            <InputLabel
                value="Text Position"
                class="mb-1 dark:text-dark-text-primary"
            />
            <div
                class="grid grid-cols-3 gap-2 p-1 border rounded-md dark:border-dark-border"
            >
                <button
                    v-for="position in [
                        'top-left' as const,
                        'top-center' as const,
                        'top-right' as const,
                        'middle-left' as const,
                        'middle' as const,
                        'middle-right' as const,
                        'bottom-left' as const,
                        'bottom-center' as const,
                        'bottom-right' as const,
                    ]"
                    :key="position"
                    @click="selectTextPosition(position)"
                    class="flex items-center justify-center p-1 text-xs rounded dark:hover:bg-dark-accent hover:bg-gray-100"
                    :class="[
                        currentProps.textPosition === position
                            ? 'dark:bg-dark-primary-dimmed dark:text-dark-text-primary bg-blue-100 text-blue-700'
                            : 'dark:bg-dark-surface-elevated bg-gray-50',
                    ]"
                    :title="position"
                >
                    <span class="w-2 h-2 bg-current rounded-full"></span>
                </button>
            </div>
        </div>

        <div>
            <InputLabel
                value="Background"
                class="mb-1 dark:text-dark-text-primary"
            />
            <div
                class="flex p-1 space-x-1 border rounded-md dark:bg-dark-surface-elevated dark:border-dark-border bg-gray-50"
            >
                <Button
                    text="Image"
                    :variant="
                        editMode === 'image' ? 'primary' : 'outline-primary'
                    "
                    class="flex-1 !text-xs !py-1"
                    @click="switchToImageMode"
                />
                <Button
                    text="Color"
                    :variant="
                        editMode === 'color' ? 'primary' : 'outline-primary'
                    "
                    class="flex-1 !text-xs !py-1"
                    @click="switchToColorMode"
                />
            </div>

            <div v-if="editMode === 'image'" class="space-y-2">
                <div v-if="previewImageUrl" class="mt-2">
                    <img
                        :src="previewImageUrl"
                        :alt="currentProps.altText || 'Hero background'"
                        class="object-cover w-full h-32 rounded-md"
                    />
                </div>
                <input
                    ref="fileInputRef"
                    type="file"
                    class="hidden"
                    accept=".png,.jpg,.jpeg"
                    @change="handleFileSelect"
                />
                <Upload
                    :preview-image-url="previewImageUrl"
                    :text="
                        currentProps._imageUrl_uploadingToS3
                            ? 'Uploading to S3...'
                            : selectedFile
                            ? 'File Selected - Will upload on save'
                            : currentProps.imageUrl
                            ? 'Change Image'
                            : 'Select Image'
                    "
                    :subtext="
                        currentProps._imageUrl_uploadingToS3
                            ? 'Please wait, processing in background...'
                            : selectedFile
                            ? selectedFile.name
                            : currentProps.imageUrl
                            ? currentProps.imageUrl.split('/').pop()
                            : 'PNG, JPG up to 10MB'
                    "
                    @click="fileInputRef?.click()"
                    :error="uploadError"
                    :disabled="currentProps._imageUrl_uploadingToS3"
                    class="h-24 dark:bg-dark-surface-elevated dark:border-dark-border"
                />
                <p
                    v-if="uploadError && uploadErrorMessage"
                    class="mt-1 text-xs text-red-600 dark:text-dark-status-red"
                >
                    {{ uploadErrorMessage }}
                </p>
                <Spacer />

                <Toggle
                    :model-value="currentProps.overlayEnabled ?? false"
                    @update:model-value="
                        updateProperty('overlayEnabled', $event)
                    "
                    label="Enable Overlay"
                />
            </div>

            <div v-if="editMode === 'color'" class="space-y-3">
                <Toggle
                    :model-value="currentProps.useThemeBackground ?? true"
                    @update:model-value="
                        (value) => {
                            updateProperty('useThemeBackground', value);
                            // If switching to custom color and no backgroundColor is set, provide a default
                            if (!value && !currentProps.backgroundColor) {
                                updateProperty('backgroundColor', '#3b82f6');
                            }
                        }
                    "
                    label="Use Theme Color"
                    class="mt-2"
                />

                <div v-if="currentProps.useThemeBackground === false">
                    <ColorPalettePicker
                        :model-value="currentProps.backgroundColor || '#3b82f6'"
                        @update:model-value="
                            updateProperty('backgroundColor', $event)
                        "
                        label="Custom Background Color"
                        id="hero-background-color"
                        class="w-full h-10"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
