<template>
    <Section title="Meta Information">
        <p class="mt-1 text-sm dark:text-dark-text-secondary text-text-muted">
            Set the title and description that appear in search results and when
            sharing your website.
        </p>
        <div class="flex flex-col gap-4 mt-4">
            <div>
                <label
                    for="metaTitle"
                    class="block text-sm font-medium text-gray-700 dark:text-dark-text-primary"
                    >Meta Title</label
                >
                <input
                    type="text"
                    id="metaTitle"
                    v-model="metaTitle"
                    placeholder="Enter meta title"
                    class="w-full p-2 mt-1 border border-gray-300 rounded-md shadow-sm dark:bg-dark-surface dark:border-dark-border dark:text-dark-text-primary dark:placeholder-dark-text-tertiary dark:focus:border-dark-primary dark:focus:ring-dark-primary focus:border-primary focus:ring-primary"
                />
                <p
                    class="mt-1 text-xs dark:text-dark-text-tertiary text-text-dimmed"
                >
                    Recommended length: 50-60 characters.
                </p>
            </div>
            <div>
                <label
                    for="metaDescription"
                    class="block text-sm font-medium text-gray-700 dark:text-dark-text-primary"
                    >Meta Description</label
                >
                <textarea
                    id="metaDescription"
                    v-model="metaDescription"
                    rows="3"
                    placeholder="Enter meta description"
                    class="w-full p-2 mt-1 border border-gray-300 rounded-md shadow-sm dark:bg-dark-surface dark:border-dark-border dark:text-dark-text-primary dark:placeholder-dark-text-tertiary dark:focus:border-dark-primary dark:focus:ring-dark-primary focus:border-primary focus:ring-primary"
                ></textarea>
                <p
                    class="mt-1 text-xs dark:text-dark-text-tertiary text-text-dimmed"
                >
                    Recommended length: 150-160 characters.
                </p>
            </div>
            <div class="flex justify-start">
                <Button
                    :disabled="
                        !isDirty || isSaving || isSavingTemporarilyDisabled
                    "
                    text="Save"
                    icon="$contentSave"
                    @click="saveMeta"
                    :loading="isSaving"
                />
                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="showSuccessMessage"
                        class="ml-4 text-sm text-gray-600 dark:text-dark-text-secondary"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </div>
    </Section>
</template>

<script setup>
import Button from "@/Components/UI/Button.vue";
import Section from "@/Components/UI/Section.vue";
import { computed, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";

const props = defineProps({
    websiteId: {
        type: Number,
        required: true,
    },
    currentMetaTitle: {
        type: String,
        default: "",
    },
    currentMetaDescription: {
        type: String,
        default: "",
    },
    eventId: {
        type: Number,
        required: true,
    },
});

const metaTitle = ref(props.currentMetaTitle);
const metaDescription = ref(props.currentMetaDescription);
const isSaving = ref(false);
const showSuccessMessage = ref(false);
const isSavingTemporarilyDisabled = ref(false);

watch(
    () => props.currentMetaTitle,
    (newVal) => (metaTitle.value = newVal)
);
watch(
    () => props.currentMetaDescription,
    (newVal) => (metaDescription.value = newVal)
);

const isDirty = computed(() => {
    if (isSaving.value || isSavingTemporarilyDisabled.value) {
        return false;
    }
    return (
        metaTitle.value !== props.currentMetaTitle ||
        metaDescription.value !== props.currentMetaDescription
    );
});

const saveMeta = async () => {
    if (!isDirty.value) return;

    isSaving.value = true;
    showSuccessMessage.value = false;
    isSavingTemporarilyDisabled.value = false;

    router.patch(
        route("website.meta.update", {
            event: props.eventId,
            website: props.websiteId,
        }),
        {
            meta_title: metaTitle.value,
            meta_description: metaDescription.value,
        },
        {
            onSuccess: () => {
                showSuccessMessage.value = true;
                isSavingTemporarilyDisabled.value = true;
                setTimeout(() => {
                    showSuccessMessage.value = false;
                    isSavingTemporarilyDisabled.value = false;
                }, 3000);
            },
            onFinish: () => {
                isSaving.value = false;
            },
        }
    );
};
</script>
