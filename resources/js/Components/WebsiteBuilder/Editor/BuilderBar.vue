<script setup lang="ts">
import Button from "@/Components/UI/Button.vue";
import Modal from "@/Components/UI/Modal.vue";
import Pill from "@/Components/UI/Pill.vue";
import { formatRelativeTime } from "@/utils/date";
import { computed, ref, watch, onMounted, onUnmounted } from "vue";
import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import PublishButton from "@/Components/WebsiteBuilder/Components/PublishButton.vue";
import { useSaveState } from "@/Composables/useSaveState";
import { usePublishState } from "@/Composables/usePublishState";
import { useShareUrl } from "@/Composables/useShareUrl";
import { router } from "@inertiajs/vue3";

const websiteBuilderStore = useWebsiteBuilderStore();

const { isSavingDebounced, saveButtonState, handleSave, lastUpdatedText } =
    useSaveState(websiteBuilderStore);

const shareState = useShareUrl(websiteBuilderStore);
const { isShareModalOpen, copyStatus, shareUrl, copyShareUrl } = shareState;

const {
    isShowingPublishedConfirmation,
    isUnpublishing,
    publishButtonState,
    handlePublish,
    handleUnpublish,
} = usePublishState(websiteBuilderStore, shareState);

const deviceConfigs = [
    { name: "mobile", icon: "$cellphone" },
    { name: "desktop", icon: "$monitor" },
] as const;

const eventMenu = ref(false);

const handleDeviceChange = (device: string) => {
    websiteBuilderStore.setPreviewDevice(device as any);
};

const handleUndo = () => {
    websiteBuilderStore.undo();
};

const handleRedo = () => {
    websiteBuilderStore.redo();
};

const canUndo = computed(() => websiteBuilderStore.undoStack.length > 0);
const canRedo = computed(() => websiteBuilderStore.redoStack.length > 0);

const isMac = computed(() => {
    return (
        typeof navigator !== "undefined" && navigator.platform.includes("Mac")
    );
});

// Keyboard shortcuts for undo/redo
const handleKeyDown = (event: KeyboardEvent) => {
    // Check if Cmd (Mac) or Ctrl (Windows/Linux) is pressed
    const isModifierPressed = event.metaKey || event.ctrlKey;

    if (isModifierPressed && event.key === "z" && !event.shiftKey) {
        event.preventDefault();
        if (canUndo.value) {
            handleUndo();
        }
    } else if (isModifierPressed && event.key === "z" && event.shiftKey) {
        event.preventDefault();
        if (canRedo.value) {
            handleRedo();
        }
    } else if (isModifierPressed && event.key === "y") {
        // Ctrl+Y for redo on Windows
        event.preventDefault();
        if (canRedo.value) {
            handleRedo();
        }
    } else if (isModifierPressed && event.key === "s") {
        // Cmd/Ctrl+S for save
        event.preventDefault();
        if (!isSavingDebounced.value) {
            handleSave();
        }
    }
};

function handleBackToDashboard() {
    router.visit(route("dashboard"));
}

onMounted(() => {
    window.addEventListener("keydown", handleKeyDown);
});

onUnmounted(() => {
    window.removeEventListener("keydown", handleKeyDown);
});
</script>

<template>
    <div class="sticky top-0 z-20 shrink-0">
        <div
            class="flex items-center justify-between px-6 py-4 border-b bg-surface dark:bg-dark-surface dark:border-dark-border border-border"
        >
            <div class="flex items-center space-x-4">
                <!-- <v-menu
                    v-model="eventMenu"
                    :close-on-content-click="true"
                    location="bottom start"
                    :offset="10"
                >
                    <template v-slot:activator="{ props: menuProps }">
                        <button
                            type="button"
                            v-bind="menuProps"
                            class="flex items-center gap-2 px-2 py-2 rounded-md cursor-pointer dark:hover:bg-dark-surface-elevated hover:bg-gray-100"
                        >
                            <img
                                :src="'/eventlink-logo.png'"
                                alt="EventLink Logo"
                                class="h-[30px] w-auto"
                            />
                            <span
                                class="font-medium text-gray-800 dark:text-dark-text-primary"
                                >{{
                                    websiteBuilderStore.currentEvent?.name
                                        ? websiteBuilderStore.currentEvent.name
                                        : "Event"
                                }}</span
                            >
                            <v-icon
                                color="grey-darken-1"
                                icon="$chevronDown"
                            ></v-icon>
                        </button>
                    </template>
                    <v-list
                        density="compact"
                        min-width="180"
                        rounded="lg"
                        :slim="true"
                        class="dark:bg-dark-surface-elevated dark:text-dark-text-primary"
                    >
                        <v-list-item
                            title="Go to Dashboard"
                            prepend-icon="$chevronLeft"
                            class="text-sm font-medium"
                            :slim="true"
                            density="compact"
                            :href="route('dashboard')"
                            :value="'dashboard'"
                        >
                        </v-list-item>
                    </v-list>
                </v-menu> -->
                <Button
                    variant="outline-primary"
                    @click="handleBackToDashboard"
                >
                    <v-icon icon="$chevronLeft" size="small" />
                    Back to Dashboard
                </Button>
            </div>

            <div class="flex items-center space-x-4">
                <Pill
                    :text="publishButtonState.statusText"
                    :variant="publishButtonState.statusVariant"
                />
                <div
                    class="dark:border-dark-border hidden h-10 items-center overflow-hidden rounded-[6px] border-[1px] border-border md:flex"
                >
                    <button
                        v-for="device in deviceConfigs"
                        :key="device.name"
                        @click="handleDeviceChange(device.name)"
                        :class="[
                            'flex h-full items-center justify-center px-[12px] py-[8px] transition-colors', // Ensure button takes full height
                            websiteBuilderStore.previewDevice === device.name
                                ? 'dark:bg-dark-primary bg-[#1a1f36] text-white'
                                : 'dark:text-dark-text-secondary dark:hover:bg-dark-surface-elevated text-gray-600 hover:bg-gray-200',
                        ]"
                    >
                        <v-icon
                            :icon="device.icon"
                            size="small"
                            :color="
                                websiteBuilderStore.previewDevice ===
                                device.name
                                    ? 'white'
                                    : 'grey-darken-1'
                            "
                        />
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <v-tooltip
                        :text="publishButtonState.disabledReason"
                        location="top"
                        :disabled="
                            !publishButtonState.disabled ||
                            !publishButtonState.disabledReason
                        "
                    >
                        <template v-slot:activator="{ props: activatorProps }">
                            <PublishButton
                                :state="publishButtonState"
                                :is-showing-published-confirmation="
                                    isShowingPublishedConfirmation
                                "
                                :is-unpublishing="isUnpublishing"
                                :share-url="shareUrl"
                                :copy-status="copyStatus"
                                :is-share-modal-open="isShareModalOpen"
                                @publish="handlePublish"
                                @unpublish="handleUnpublish"
                                @copy="copyShareUrl"
                                @update:is-share-modal-open="(val: boolean) => isShareModalOpen = val"
                                v-bind="activatorProps"
                            />
                        </template>
                    </v-tooltip>
                </div>
            </div>
        </div>
        <!-- Actions Sub Bar -->
        <div
            class="flex items-center h-10 px-6 py-2 border-b bg-gray-50 dark:bg-dark-surface-elevated dark:border-dark-border border-border"
        >
            <div class="flex items-center space-x-1">
                <div class="flex items-center -space-x-2">
                    <v-tooltip location="bottom">
                        <template v-slot:activator="{ props: activatorProps }">
                            <v-btn
                                icon="$undo"
                                size="small"
                                variant="text"
                                @click="handleUndo"
                                :disabled="!canUndo"
                                v-bind="activatorProps"
                                class="text-gray-600 dark:text-dark-text-secondary"
                            />
                        </template>
                        <span>Undo ({{ isMac ? "⌘" : "Ctrl" }}+Z)</span>
                    </v-tooltip>
                    <v-tooltip location="bottom">
                        <template v-slot:activator="{ props: activatorProps }">
                            <v-btn
                                icon="$redo"
                                size="small"
                                variant="text"
                                @click="handleRedo"
                                :disabled="!canRedo"
                                v-bind="activatorProps"
                                class="text-gray-600 dark:text-dark-text-secondary"
                            />
                        </template>
                        <span
                            >Redo ({{
                                isMac ? "⌘+Shift" : "Ctrl+Shift"
                            }}+Z)</span
                        >
                    </v-tooltip>
                </div>
                <v-tooltip location="bottom">
                    <template v-slot:activator="{ props: activatorProps }">
                        <v-btn
                            :icon="saveButtonState.icon"
                            size="small"
                            variant="text"
                            @click="handleSave"
                            :disabled="isSavingDebounced"
                            v-bind="activatorProps"
                            class="text-gray-600 dark:text-dark-text-secondary"
                        />
                    </template>
                    <span>{{ lastUpdatedText || `Save changes (${isMac ? "⌘" : "Ctrl"}+S)` }}</span>
                </v-tooltip>
            </div>
        </div>
    </div>

    <!-- Saved Toast Notification -->
    <v-snackbar
        v-model="websiteBuilderStore.showSavedMessage"
        :timeout="3000"
        location="bottom center"
        color="black"
        rounded="lg"
        class="mb-8"
    >
        <div class="flex items-center justify-center gap-2">
            <v-icon icon="$check" size="small" />
            <span class="font-medium">Changes saved.</span>
        </div>
    </v-snackbar>
</template>
