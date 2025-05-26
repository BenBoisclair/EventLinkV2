import { ref, computed, watch, onUnmounted } from "vue";
import { TimeoutManager } from "@/utils/timeoutManager";

export function useShareUrl(websiteBuilderStore) {
    const isShareModalOpen = ref(false);
    const copyStatus = ref("idle"); // 'idle' | 'copied' | 'error'
    const timeoutManager = new TimeoutManager();
    const COPY_TIMEOUT_KEY = "copy-timeout";

    const shareUrl = computed(() => {
        if (!websiteBuilderStore.websiteSlug) {
            return "";
        }
        const domain = window.location.origin;
        const url = `${domain}/site/${websiteBuilderStore.websiteSlug}`;
        return url;
    });

    const copyShareUrl = () => {
        if (!shareUrl.value || copyStatus.value === "copied") return;
        timeoutManager.clear(COPY_TIMEOUT_KEY);
        navigator.clipboard
            .writeText(shareUrl.value)
            .then(() => {
                copyStatus.value = "copied";
                timeoutManager.set(
                    COPY_TIMEOUT_KEY,
                    () => {
                        copyStatus.value = "idle";
                    },
                    2000
                );
            })
            .catch((err) => {
                console.error("Failed to copy link: ", err);
                copyStatus.value = "error";
                timeoutManager.set(
                    COPY_TIMEOUT_KEY,
                    () => {
                        copyStatus.value = "idle";
                    },
                    2000
                );
            });
    };

    watch(isShareModalOpen, (isOpen) => {
        if (!isOpen) {
            timeoutManager.clearAll();
            copyStatus.value = "idle";
        }
    });

    onUnmounted(() => {
        timeoutManager.clearAll();
    });

    return {
        isShareModalOpen,
        copyStatus,
        shareUrl,
        copyShareUrl,
    };
}
