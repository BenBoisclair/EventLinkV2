import { ref, computed } from "vue";

export function usePublishState(websiteBuilderStore, shareState = null) {
    const isShowingPublishedConfirmation = ref(false);
    const isUnpublishing = ref(false);

    const handlePublish = async () => {
        if (
            websiteBuilderStore.isPublished ||
            isShowingPublishedConfirmation.value
        )
            return;

        const success = await websiteBuilderStore.publishWebsite();

        if (success) {
            isShowingPublishedConfirmation.value = true;

            // Open share modal on successful publish
            if (shareState && shareState.isShareModalOpen) {
                shareState.isShareModalOpen.value = true;
            }

            setTimeout(() => {
                isShowingPublishedConfirmation.value = false;
            }, 1000);
        }
    };

    const handleUnpublish = async () => {
        if (
            !websiteBuilderStore.isPublished ||
            isShowingPublishedConfirmation.value ||
            isUnpublishing.value
        )
            return;
        isUnpublishing.value = true;
        setTimeout(async () => {
            await websiteBuilderStore.unpublishWebsite();
            isUnpublishing.value = false;
        }, 1000);
    };

    function getBasePublishState() {
        if (websiteBuilderStore.isPublished) {
            return {
                text: "Unpublish",
                variant: "outline-danger",
                icon: "$cloudOffOutline",
                clickHandler: handleUnpublish,
                statusText: "Online",
                statusVariant: "success",
                disabled: false,
                disabledReason: "",
            };
        } else {
            return {
                text: "Publish",
                variant: "primary",
                icon: "$cloudUploadOutline",
                clickHandler: handlePublish,
                statusText: "Draft",
                statusVariant: "secondary",
                disabled: false,
                disabledReason: "",
            };
        }
    }

    function getConfirmationState(baseState) {
        if (isShowingPublishedConfirmation.value) {
            return {
                ...baseState,
                text: "Published",
                variant: "success",
                icon: "$checkCircleOutline",
                disabled: true,
                clickHandler: () => {},
            };
        }
        if (isUnpublishing.value) {
            return {
                ...baseState,
                text: "Unpublishing...",
                variant: "danger",
                icon: "$loading mdi-spin",
                disabled: true,
                clickHandler: () => {},
            };
        }
        return baseState;
    }

    function getValidationState(state) {
        let disabled = state.disabled;
        let disabledReason = state.disabledReason;
        if (websiteBuilderStore.saveState === "saving") {
            disabled = true;
            disabledReason = "Saving changes...";
        } else if (
            !websiteBuilderStore.isPublished &&
            !isShowingPublishedConfirmation.value
        ) {
            if (!websiteBuilderStore.blocks.length) {
                disabled = true;
                disabledReason = "Add content blocks before publishing";
            } else if (websiteBuilderStore.saveState === "error") {
                disabled = true;
                disabledReason =
                    "Cannot publish due to save error. Please save again.";
            }
        }
        if (isShowingPublishedConfirmation.value || isUnpublishing.value) {
            disabled = true;
        }
        return { ...state, disabled, disabledReason };
    }

    const publishButtonState = computed(() => {
        const baseState = getBasePublishState();
        const confirmationState = getConfirmationState(baseState);
        const validationState = getValidationState(confirmationState);
        return validationState;
    });

    return {
        isShowingPublishedConfirmation,
        isUnpublishing,
        publishButtonState,
        handlePublish,
        handleUnpublish,
    };
}
