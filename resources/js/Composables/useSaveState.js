import { ref, computed, watch, onUnmounted } from "vue";
import { formatRelativeTime } from "@/utils/date";
import { TimeoutManager } from "@/utils/timeoutManager";

export function useSaveState(websiteBuilderStore) {
    const isSavingDebounced = ref(false);
    const timeoutManager = new TimeoutManager();
    const SAVE_DEBOUNCE_KEY = "save-debounce";

    watch(
        () => websiteBuilderStore.saveState,
        (newValue, oldValue) => {
            timeoutManager.clear(SAVE_DEBOUNCE_KEY);
            if (newValue === "saving") {
                isSavingDebounced.value = true;
            } else if (oldValue === "saving") {
                timeoutManager.set(
                    SAVE_DEBOUNCE_KEY,
                    () => {
                        isSavingDebounced.value = false;
                    },
                    1000
                );
            } else {
                isSavingDebounced.value = false;
            }
        }
    );

    const saveButtonState = computed(() => {
        if (isSavingDebounced.value) {
            return {
                text: "Saving...",
                variant: "primary",
                icon: "$contentSave",
            };
        }
        switch (websiteBuilderStore.saveState) {
            case "saved":
                return { text: "Saved", variant: "success", icon: "$check" };
            case "error":
                return {
                    text: "Error Saving",
                    variant: "outline-danger",
                    icon: "$alertCircleOutline",
                };
            default:
                return {
                    text: "Save",
                    variant: "primary",
                    icon: "$contentSave",
                };
        }
    });

    const handleSave = () => {
        websiteBuilderStore.saveWebsite();
    };

    const lastUpdatedText = computed(() => {
        const formattedTime = formatRelativeTime(
            websiteBuilderStore.lastUpdatedAt
        );
        return formattedTime ? `Last saved: ${formattedTime}` : "";
    });

    onUnmounted(() => {
        timeoutManager.clearAll();
    });

    return {
        isSavingDebounced,
        saveButtonState,
        handleSave,
        lastUpdatedText,
    };
}
