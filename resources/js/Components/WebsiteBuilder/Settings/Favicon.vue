<template>
    <Section title="Favicon">
        <p class="mt-1 text-sm dark:text-dark-text-secondary text-text-muted">
            Upload a favicon to display in browser tabs and bookmarks.
        </p>
        <div class="flex flex-col gap-6 mt-4 md:flex-row md:gap-4">
            <div class="flex flex-col w-full gap-4">
                <BrowserPreview
                    :title="metaTitle"
                    url=""
                    :favicon-url="faviconPreviewForDisplay"
                    :is-dark="true"
                />
                <BrowserPreview
                    :title="metaTitle"
                    url=""
                    :favicon-url="faviconPreviewForDisplay"
                    :is-dark="false"
                />

                <p
                    v-if="!displayFaviconUrl"
                    class="text-xs dark:text-dark-text-tertiary text-text-dimmed"
                >
                    No favicon uploaded yet. Upload a .png or .jpg file.
                </p>
            </div>
            <div class="flex w-full flex-col md:max-w-[158px]">
                <!-- Hidden File Input -->
                <input
                    ref="fileInputRef"
                    type="file"
                    class="hidden"
                    accept=".png,.jpg,.jpeg,.ico"
                    @change="handleFileSelect"
                />

                <!-- Replace custom upload area with Upload component -->
                <Upload
                    class="flex-1 !p-6"
                    :loading="isSaving"
                    :preview-image-url="faviconPreviewForDisplay"
                    :error="saveError"
                    :text="
                        selectedFilePreviewUrl
                            ? 'File Selected'
                            : 'Upload Favicon'
                    "
                    :subtext="
                        selectedFilePreviewUrl && selectedFile
                            ? selectedFile.name
                            : 'PNG, JPG, ICO'
                    "
                    @click="triggerFileInput"
                />
                <!-- Display Save Error Message -->
                <p
                    v-if="saveError && saveErrorMessage"
                    class="mt-1 text-xs text-red-600 dark:text-dark-status-red"
                >
                    {{ saveErrorMessage }}
                </p>

                <Button
                    :disabled="!selectedFile || isSaving"
                    text="Save"
                    icon="$contentSave"
                    class="mt-2"
                    @click="saveFavicon"
                    :loading="isSaving"
                />
            </div>
        </div>
    </Section>
</template>

<script setup lang="ts">
import Button from "@/Components/UI/Button.vue";
import Section from "@/Components/UI/Section.vue";
import Upload from "@/Components/UI/Upload.vue";
import BrowserPreview from "@/Components/Organiser/BrowserPreview.vue";
import { computed, onUnmounted, Ref, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";

const props = defineProps({
    websiteId: {
        type: Number,
        required: true,
    },
    currentFaviconUrl: {
        type: String,
        default: undefined,
    },
    metaTitle: {
        type: String,
        default: "Your Website Title",
    },
    websiteSlug: {
        type: String,
        required: true,
    },
    eventId: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(["refresh-website"]);

const fileInputRef = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);
const isSaving = ref(false);
const saveError = ref(false);
const saveErrorMessage = ref<string | null>(null);
const displayFaviconUrl = ref<string | undefined>(props.currentFaviconUrl);
const selectedFilePreviewUrl = ref<string | null>(null);

// Helper to revoke Object URL
const revokePreviewUrl = (urlRef: Ref<string | null>) => {
    if (urlRef.value && urlRef.value.startsWith("blob:")) {
        URL.revokeObjectURL(urlRef.value);
    }
    urlRef.value = null;
};

watch(
    () => props.currentFaviconUrl,
    (newUrl, oldUrl) => {
        displayFaviconUrl.value = newUrl;
        revokePreviewUrl(selectedFilePreviewUrl);
    }
);


const triggerFileInput = () => {
    fileInputRef.value?.click();
};

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const files = target.files;
    revokePreviewUrl(selectedFilePreviewUrl);
    saveError.value = false;
    saveErrorMessage.value = null;

    if (files && files.length > 0) {
        const file = files[0];
        if (
            [
                "image/png",
                "image/jpeg",
                "image/jpg",
                "image/vnd.microsoft.icon",
                "image/x-icon",
            ].includes(file.type)
        ) {
            selectedFile.value = file;
            selectedFilePreviewUrl.value = URL.createObjectURL(file);
        } else {
            saveError.value = true;
            saveErrorMessage.value = "Please select a PNG, JPG, or ICO file.";
            selectedFile.value = null;
            revokePreviewUrl(selectedFilePreviewUrl);
        }
    }
    if (fileInputRef.value) {
        fileInputRef.value.value = "";
    }
};

const faviconPreviewForDisplay = computed(() => {
    return selectedFilePreviewUrl.value || displayFaviconUrl.value;
});

const saveFavicon = async () => {
    if (!selectedFile.value) return;
    
    isSaving.value = true;
    saveError.value = false;
    saveErrorMessage.value = null;
    const formData = new FormData();
    formData.append("favicon", selectedFile.value);
    
    const uploadUrl = route("website.favicon.update", {
        event: props.eventId,
        website: props.websiteId,
    });
    router.post(
        uploadUrl,
        formData,
        {
            forceFormData: true,
            onSuccess: () => {
                selectedFile.value = null;
                revokePreviewUrl(selectedFilePreviewUrl);
                emit("refresh-website");
            },
            onError: (errors) => {
                saveError.value = true;
                saveErrorMessage.value =
                    errors.favicon ||
                    "Failed to upload favicon. Please try again.";
                displayFaviconUrl.value = props.currentFaviconUrl;
                revokePreviewUrl(selectedFilePreviewUrl);
            },
            onFinish: () => {
                isSaving.value = false;
            },
        }
    );
};

onUnmounted(() => {
    revokePreviewUrl(selectedFilePreviewUrl);
});
</script>
