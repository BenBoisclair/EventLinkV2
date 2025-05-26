<template>
    <div class="flex flex-col gap-component p-section">
        <div class="flex items-center justify-between">
            <div>
                <h1
                    class="font-bold dark:text-dark-primary text-heading-primary text-primary"
                >
                    Exhibitors
                </h1>
                <p
                    class="dark:text-dark-text-secondary text-body text-text-muted"
                >
                    Manage the exhibitor for your event
                </p>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="w-full">
            <StatisticCard :statistic="statistic" />
        </div>

        <Spacer />

        <!-- Exhibitors Content -->
        <div>
            <div class="flex items-center justify-between">
                <h2
                    class="font-bold dark:text-dark-primary text-heading-secondary text-primary"
                >
                    Exhibitors
                </h2>
                <div class="flex gap-small">
                    <Button
                        text="Add Exhibitor"
                        variant="secondary"
                        icon="$plus"
                        @click="showCreateModal = true"
                    />
                    <Button
                        text="Invite Exhibitor"
                        variant="primary"
                        icon="$plus"
                        iconType="mdi"
                        disabled
                    />
                </div>
            </div>

            <Input
                v-model="searchQuery"
                placeholder="Search Exhibitor by name, email, company..."
                icon="$magnify"
                iconType="mdi"
                class="mt-small max-w-input"
            />

            <ExhibitorTable
                :exhibitors="filteredExhibitors"
                @edit-exhibitor="handleEditExhibitor"
                @delete-exhibitor="handleDeleteExhibitor"
            />
        </div>

        <!-- Use the new Create Exhibitor Modal component -->
        <ExhibitorFormModal v-model="showCreateModal" :event="props.event" />

        <!-- Edit Exhibitor Modal (reusing CreateExhibitorModal structure for now) -->
        <ExhibitorFormModal
            v-if="showEditModal"
            v-model="showEditModal"
            :event="props.event"
            :exhibitorToEdit="editingExhibitor"
            @updated="handleExhibitorUpdated"
        />
    </div>
</template>

<script setup lang="ts">
import ExhibitorFormModal from "@/Components/Exhibitor/ExhibitorFormModal.vue";
import ExhibitorTable from "@/Components/Exhibitor/ExhibitorTable.vue";
import Input from "@/Components/Forms/Input.vue";
import StatisticCard from "@/Components/Organiser/StatisticCard.vue";
import Button from "@/Components/UI/Button.vue";
import Spacer from "@/Components/UI/Spacer.vue";
import { Event } from "@/types/event";
import { Exhibitor } from "@/types/exhibitor";
import { router } from "@inertiajs/vue3";
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from "vue";

// Assuming Echo is globally available as window.Echo
declare const window: any;

const props = defineProps<{
    exhibitors: Exhibitor[];
    event: Event;
}>();

// Use a local reactive ref for exhibitors to allow modification
const localExhibitors = ref<Exhibitor[]>([]); // Initialize as empty

// Watch for changes in the prop and update the local ref
watch(
    () => props.exhibitors,
    (newExhibitors) => {
        localExhibitors.value = newExhibitors ? [...newExhibitors] : []; // Create a copy
    },
    { immediate: true, deep: true } // immediate to populate initially, deep for nested changes if any
);

const searchQuery = ref("");
const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingExhibitor = ref<Exhibitor | null>(null);

const statistic = computed(() => ({
    name: "Total Exhibitors",
    count: localExhibitors.value.length, // <-- Use local ref
    icon: "$storeOutline",
    title: "Exhibitors",
}));

const filteredExhibitors = computed(() => {
    if (!localExhibitors.value) return []; // <-- Use local ref

    let filtered = localExhibitors.value; // <-- Use local ref

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter((exhibitor) => {
            const nameMatch = exhibitor.name.toLowerCase().includes(query);
            const emailMatch = exhibitor.contact_email
                .toLowerCase()
                .includes(query);
            // Add logo/banner paths to exhibitor type if needed for display
            // exhibitor.logo_path = exhibitor.logo_path || null;
            // exhibitor.banner_path = exhibitor.banner_path || null;
            return nameMatch || emailMatch;
        });
    }

    return filtered;
});

// Function to handle the broadcasted event
const handleImageProcessed = (eventData: any) => {
    const exhibitorId = eventData.exhibitor_id;
    const fieldName = eventData.field_name; // 'logo_path' or 'banner_path'
    const imageUrl = eventData.image_url;

    const exhibitorIndex = localExhibitors.value.findIndex(
        (ex) => ex.id === exhibitorId
    );

    if (exhibitorIndex !== -1) {
        // Ensure the property exists before assigning
        if (fieldName === "logo_path" || fieldName === "banner_path") {
            // Important: The actual property might be different (e.g., logoUrl)
            // Adjust this based on how you store/use the URL in your Exhibitor type and table
            // For now, assuming direct path update. Add these fields to your Exhibitor type if needed.
            localExhibitors.value[exhibitorIndex] = {
                ...localExhibitors.value[exhibitorIndex],
                [fieldName]: imageUrl, // Update the specific field
            };
        }
    } else {
        // Optionally, you could re-fetch the exhibitor list here if an exhibitor was added concurrently
    }
};

// Function to handle the edit event from ExhibitorTable
const handleEditExhibitor = (exhibitor: Exhibitor) => {
    editingExhibitor.value = exhibitor;
    showEditModal.value = true;
};

// Function to handle the update event from the Edit Modal
const handleExhibitorUpdated = async (updatedExhibitor: Exhibitor) => {
    const index = localExhibitors.value.findIndex(
        (ex) => ex.id === updatedExhibitor.id
    );
    if (index !== -1) {
        localExhibitors.value[index] = updatedExhibitor;
        // Ensure reactivity if needed, although direct assignment should work with ref
        // localExhibitors.value = [...localExhibitors.value];
    }
    showEditModal.value = false; // Close modal after update
    await nextTick(); // Wait for DOM update if necessary
    editingExhibitor.value = null; // Clear editing state
};

// Function to handle the delete event from ExhibitorTable
const handleDeleteExhibitor = (exhibitorToDelete: Exhibitor) => {
    router.delete(
        route("organiser.event.exhibitors.destroy", {
            event: props.event.id,
            exhibitor: exhibitorToDelete.id,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                // Optimistically remove the exhibitor from the local list
                // Or rely on Inertia to refetch the list
                const index = localExhibitors.value.findIndex(
                    (ex) => ex.id === exhibitorToDelete.id
                );
                if (index !== -1) {
                    localExhibitors.value.splice(index, 1);
                }
                // Optionally, show a success notification
            },
            onError: (errors) => {
                console.error("Error deleting exhibitor:", errors);
                // Optionally, show an error notification
            },
        }
    );
};

// Echo listener setup
onMounted(() => {
    if (typeof window.Echo !== "undefined" && props.event?.id) {
        window.Echo.private(`event.${props.event.id}.exhibitors`).listen(
            ".exhibitor.image.processed",
            handleImageProcessed
        );
    } else {
        console.error("Echo not configured or event ID missing.");
    }
});

onUnmounted(() => {
    if (typeof window.Echo !== "undefined" && props.event?.id) {
        window.Echo.leaveChannel(`event.${props.event.id}.exhibitors`);
    }
});
</script>
