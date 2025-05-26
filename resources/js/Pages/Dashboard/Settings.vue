<template>
    <div class="flex flex-col gap-6 p-6">
        <div>
            <h1
                class="dark:text-dark-primary text-[36px] font-bold text-primary"
            >
                Settings
            </h1>
            <p
                class="dark:text-dark-text-secondary text-[14px] text-text-muted"
            >
                Configure your event settings
            </p>
        </div>

        <!-- Event Settings -->
        <Section title="Event Details">
            <form @submit.prevent="saveEventSettings" class="mt-4 space-y-4">
                <div>
                    <InputLabel forInput="eventName" value="Event Name" />
                    <Input
                        id="eventName"
                        v-model="form.name"
                        type="text"
                        class="w-full mt-1"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel
                        forInput="eventDescription"
                        value="Description"
                    />
                    <Textarea
                        id="eventDescription"
                        v-model="form.description"
                        class="mt-1"
                        rows="4"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.description"
                    />
                </div>

                <!-- Start Date and End Date -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <InputLabel forInput="startDate" value="Start Date" />
                        <Input
                            id="startDate"
                            v-model="form.start_date"
                            type="date"
                            class="w-full mt-1"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.start_date"
                        />
                    </div>
                    <div>
                        <InputLabel forInput="endDate" value="End Date" />
                        <Input
                            id="endDate"
                            v-model="form.end_date"
                            type="date"
                            class="w-full mt-1"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.end_date"
                        />
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <InputLabel forInput="location" value="Location" />
                    <Input
                        id="location"
                        v-model="form.location"
                        type="text"
                        class="w-full mt-1"
                    />
                    <InputError class="mt-2" :message="form.errors.location" />
                </div>


                <div class="flex items-center gap-4">
                    <Button
                        text="Save Changes"
                        variant="primary"
                        icon="$contentSave"
                        type="submit"
                        :disabled="
                            form.processing || isSavingTemporarilyDisabled
                        "
                        :class="{
                            'opacity-25':
                                form.processing || isSavingTemporarilyDisabled,
                        }"
                    />
                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p
                            v-if="form.recentlySuccessful"
                            class="text-sm text-gray-600 dark:text-dark-text-secondary"
                        >
                            Saved.
                        </p>
                    </Transition>
                </div>
            </form>
        </Section>

        <!-- Delete Website Section -->
        <Section v-if="hasWebsite" title="Website Settings">
            <div
                class="p-4 mt-4 border border-red-100 rounded-md dark:border-dark-status-red/30"
            >
                <p class="font-medium text-red-600 dark:text-dark-status-red">
                    Delete Website
                </p>
                <p
                    class="mt-1 text-sm dark:text-dark-text-secondary text-text-muted"
                >
                    Deleting your website will remove all content and settings.
                    This action cannot be undone.
                </p>
                <Button
                    text="Delete Website"
                    variant="danger"
                    class="w-full mt-3"
                    @click="showDeleteModal = true"
                />
            </div>
        </Section>

        <!-- Delete Website Confirmation Modal -->
        <Modal
            v-model="showDeleteModal"
            title="Delete Website?"
            :closeOnClickOutside="true"
        >
            <p class="mt-4 text-sm">
                Are you sure you want to delete your website? This action cannot
                be undone.
            </p>

            <p
                class="mt-2 text-sm dark:text-dark-text-secondary text-text-muted"
            >
                All website content, settings, and configurations will be
                permanently removed.
            </p>

            <div class="flex gap-3 mt-6">
                <Button
                    text="Cancel"
                    variant="primary"
                    class="flex-1"
                    @click="showDeleteModal = false"
                />
                <Button
                    :text="isDeleting ? 'Deleting...' : 'Yes, Delete'"
                    variant="outline-danger"
                    icon="$trashCanOutline"
                    class="flex-1"
                    :disabled="isDeleting"
                    @click="confirmDelete"
                />
            </div>
        </Modal>
    </div>
</template>

<script setup>
import Input from "@/Components/Forms/Input.vue";
import InputError from "@/Components/Forms/InputError.vue";
import InputLabel from "@/Components/Forms/InputLabel.vue";
import Textarea from "@/Components/Forms/Textarea.vue";
import Button from "@/Components/UI/Button.vue";
import Modal from "@/Components/UI/Modal.vue";
import Section from "@/Components/UI/Section.vue";
import { useEventStore } from "@/stores/eventStore";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const page = usePage();
const eventStore = useEventStore();
const event = computed(() => page.props.event);
const isSavingTemporarilyDisabled = ref(false);

// Helper function to format date for HTML input
const formatDateForInput = (dateString) => {
    if (!dateString) return "";
    // If it's already in YYYY-MM-DD format, return as is
    if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
        return dateString;
    }
    // Otherwise, parse and format
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
};

const form = useForm({
    name: event.value?.name ?? "",
    description: event.value?.description ?? "",
    start_date: formatDateForInput(event.value?.start_date),
    end_date: formatDateForInput(event.value?.end_date),
    location: event.value?.location ?? "",
});

const saveEventSettings = () => {
    if (!event.value?.id) {
        console.error("Event ID is missing, cannot update.");
        return;
    }
    form.patch(route("organiser.event.update", event.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            eventStore.updateCurrentEventDetails({
                name: form.name,
                description: form.description,
                start_date: form.start_date,
                end_date: form.end_date,
                location: form.location,
            });
            isSavingTemporarilyDisabled.value = true;
            setTimeout(() => {
                isSavingTemporarilyDisabled.value = false;
            }, 3000);
        },
        onError: (errors) => {
            console.error("Error updating event:", errors);
        },
    });
};

const hasWebsite = computed(() => event.value?.website !== null);
const showDeleteModal = ref(false);
const isDeleting = ref(false);
let deleteStartTime = 0;

const confirmDelete = () => {
    if (!event.value?.website?.id) {
        console.error("Website ID is missing, cannot delete.");
        showDeleteModal.value = false;
        return;
    }

    isDeleting.value = true;
    deleteStartTime = Date.now();

    router.delete(
        route("organiser.event.website.destroy", {
            event: event.value.id,
            website: event.value.website.id,
        }),
        {
            preserveScroll: true,
            onFinish: () => {
                const elapsedTime = Date.now() - deleteStartTime;
                const delay = Math.max(0, 3000 - elapsedTime);

                setTimeout(() => {
                    isDeleting.value = false;
                    showDeleteModal.value = false;
                }, delay);
            },
            onError: (errors) => {
                console.error("Error deleting website:", errors);
                const elapsedTime = Date.now() - deleteStartTime;
                const delay = Math.max(0, 3000 - elapsedTime);

                setTimeout(() => {
                    isDeleting.value = false;
                    showDeleteModal.value = false;
                }, delay);
            },
        }
    );
};
</script>
