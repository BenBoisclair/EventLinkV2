<template>
    <Modal
        :model-value="modelValue"
        :title="isEditing ? 'Edit Exhibitor' : 'Add New Exhibitor'"
        :close-on-click-outside="false"
        @update:modelValue="$emit('update:modelValue', $event)"
    >
        <form @submit.prevent="handleSubmit">
            <div class="space-y-4">
                <!-- Exhibitor Details Section -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Left Column: Basic Info -->
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1">
                            <InputLabel value="Name" forInput="name" />
                            <Input
                                v-model="form.name"
                                :error="form.errors.name"
                                required
                            />
                        </div>
                        <div class="flex flex-col gap-1">
                            <InputLabel
                                value="Contact Email"
                                forInput="contact_email"
                            />
                            <Input
                                v-model="form.contact_email"
                                type="email"
                                :error="form.errors.contact_email"
                                required
                            />
                        </div>
                        <div class="flex flex-col gap-1">
                            <InputLabel
                                value="Contact Phone"
                                forInput="contact_phone"
                            />
                            <Input
                                v-model="form.contact_phone"
                                :error="form.errors.contact_phone"
                            />
                        </div>
                    </div>
                    <!-- Right Column: Description -->
                    <div class="flex flex-col gap-1">
                        <InputLabel
                            value="Description"
                            forInput="description"
                        />
                        <Textarea
                            v-model="form.description"
                            :error="form.errors.description"
                            required
                            class="h-full"
                        />
                    </div>
                </div>

                <!-- Media Upload Section -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Logo Upload -->
                    <div class="flex flex-col gap-1">
                        <InputLabel value="Logo" forInput="logo-upload" />
                        <input
                            ref="logoInputRef"
                            type="file"
                            class="hidden"
                            accept=".png,.jpg,.jpeg,.gif"
                            @change="handleLogoSelect"
                        />
                        <Upload
                            id="logo-upload"
                            :preview-image-url="
                                logoPreviewUrl ||
                                (isEditing && exhibitorToEdit?.logo_path
                                    ? resolveStorageUrl(
                                          exhibitorToEdit.logo_path,
                                      )
                                    : null)
                            "
                            :text="form.logo ? 'Change Logo' : 'Upload Logo'"
                            :subtext="
                                form.logo
                                    ? form.logo.name
                                    : 'PNG, JPG, GIF up to 10MB'
                            "
                            variant="row"
                            :loading="logoLoading"
                            :error="!!form.errors.logo"
                            @click="logoInputRef?.click()"
                        />
                        <p v-if="form.errors.logo" class="text-sm text-red-600">
                            {{ form.errors.logo }}
                        </p>
                    </div>

                    <!-- Banner Upload -->
                    <div class="flex flex-col gap-1">
                        <InputLabel value="Banner" forInput="banner-upload" />
                        <input
                            ref="bannerInputRef"
                            type="file"
                            class="hidden"
                            accept=".png,.jpg,.jpeg,.gif"
                            @change="handleBannerSelect"
                        />
                        <Upload
                            id="banner-upload"
                            :preview-image-url="
                                bannerPreviewUrl ||
                                (isEditing && exhibitorToEdit?.banner_path
                                    ? resolveStorageUrl(
                                          exhibitorToEdit.banner_path,
                                      )
                                    : null)
                            "
                            :text="
                                form.banner ? 'Change Banner' : 'Upload Banner'
                            "
                            :subtext="
                                form.banner
                                    ? form.banner.name
                                    : 'PNG, JPG, GIF up to 10MB'
                            "
                            variant="row"
                            :loading="bannerLoading"
                            :error="!!form.errors.banner"
                            @click="bannerInputRef?.click()"
                        />
                        <p
                            v-if="form.errors.banner"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.banner }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-5 flex justify-end gap-small">
                <Button
                    text="Cancel"
                    variant="secondary"
                    type="button"
                    :disabled="form.processing"
                    @click="$emit('update:modelValue', false)"
                />
                <Button
                    :text="isEditing ? 'Save Changes' : 'Add'"
                    variant="primary"
                    type="submit"
                    :loading="form.processing || logoLoading || bannerLoading"
                    :disabled="
                        form.processing ||
                        logoLoading ||
                        bannerLoading ||
                        !form.name ||
                        !form.contact_email ||
                        !form.description
                    "
                >
                </Button>
            </div>
            <!-- General Error -->
            <p v-if="form.errors.general" class="mt-small text-sm text-red-600">
                {{ form.errors.general }}
            </p>
        </form>
    </Modal>
</template>

<script setup lang="ts">
import Input from '@/Components/Forms/Input.vue';
import Textarea from '@/Components/Forms/Textarea.vue';
import Button from '@/Components/UI/Button.vue';
import Modal from '@/Components/UI/Modal.vue';
import Upload from '@/Components/UI/Upload.vue';
import { Event } from '@/types/event';
import { Exhibitor } from '@/types/exhibitor';
import { resolveStorageUrl } from '@/utils/storage';
import { useForm } from '@inertiajs/vue3';
import { computed, onUnmounted, ref, Ref, watch } from 'vue';
import InputLabel from '../Forms/InputLabel.vue';

const props = defineProps<{
    modelValue: boolean;
    event: Event;
    exhibitorToEdit?: Exhibitor | null;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void;
    (e: 'updated', exhibitor: Exhibitor): void;
}>();

interface ExhibitorForm extends Record<string, any> {
    name: string;
    description: string;
    contact_phone: string | null;
    contact_email: string;
    logo: File | null;
    banner: File | null;
}

const form = useForm<ExhibitorForm>({
    name: '',
    description: '',
    contact_email: '',
    contact_phone: null,
    logo: null,
    banner: null,
});

const logoLoading = ref(false);
const bannerLoading = ref(false);
const logoInputRef = ref<HTMLInputElement | null>(null);
const bannerInputRef = ref<HTMLInputElement | null>(null);

// Refs for temporary preview URLs
const logoPreviewUrl = ref<string | null>(null);
const bannerPreviewUrl = ref<string | null>(null);

const isEditing = computed(() => !!props.exhibitorToEdit);

// --- Helper to revoke Object URL ---
const revokePreviewUrl = (urlRef: Ref<string | null>) => {
    if (urlRef.value && urlRef.value.startsWith('blob:')) {
        URL.revokeObjectURL(urlRef.value);
    }
    urlRef.value = null;
};

const handleLogoSelect = (event: any) => {
    revokePreviewUrl(logoPreviewUrl); // Revoke previous preview
    const input = event.target as HTMLInputElement;
    if (!input || !input.files) return;
    const files = input.files;
    if (files && files.length > 0) {
        form.logo = files[0];
        form.clearErrors('logo');
        logoPreviewUrl.value = URL.createObjectURL(files[0]); // Create new preview
    } else {
        form.logo = null;
    }
    if (input) input.value = '';
};

const handleBannerSelect = (event: any) => {
    revokePreviewUrl(bannerPreviewUrl); // Revoke previous preview
    const input = event.target as HTMLInputElement;
    if (!input || !input.files) return;
    const files = input.files;
    if (files && files.length > 0) {
        form.banner = files[0];
        form.clearErrors('banner');
        bannerPreviewUrl.value = URL.createObjectURL(files[0]); // Create new preview
    } else {
        form.banner = null;
    }
    if (input) input.value = '';
};

const handleSubmit = () => {
    if (form.logo) logoLoading.value = true;
    if (form.banner) bannerLoading.value = true;

    const url = isEditing.value
        ? route('organiser.event.exhibitors.update', {
              event: props.event.id,
              exhibitor: props.exhibitorToEdit!.id,
          })
        : route('organiser.event.exhibitors.store', { event: props.event.id });

    form.transform((data) => ({
        ...data,
        ...(isEditing.value ? { _method: 'PUT' } : {}),
    })).post(url, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: (page: any) => {
            emit('update:modelValue', false);
            if (isEditing.value) {
                // Option 2: Just close modal. Parent (`Exhibitors.vue`) will get refreshed props from redirect.
                // The `handleExhibitorUpdated` in parent *won't* receive direct data, but list should update.
                // This is simpler if the redirect always happens and refreshes props.
            }
            form.reset();
            form.clearErrors();
            if (logoInputRef.value) logoInputRef.value.value = '';
            if (bannerInputRef.value) bannerInputRef.value.value = '';
        },
        onError: (errors) => {
            console.error(
                isEditing.value
                    ? 'Error updating exhibitor:'
                    : 'Error creating exhibitor:',
                errors,
            );
            logoLoading.value = false;
            bannerLoading.value = false;
            if (errors.logo) {
                form.logo = null;
                revokePreviewUrl(logoPreviewUrl);
            }
            if (errors.banner) {
                form.banner = null;
                revokePreviewUrl(bannerPreviewUrl);
            }
        },
        onFinish: () => {
            logoLoading.value = false;
            bannerLoading.value = false;
        },
    });
};

watch(
    [() => props.modelValue, () => props.exhibitorToEdit],
    (
        [newModelValue, newExhibitorToEdit],
        [oldModelValue, oldExhibitorToEdit],
    ) => {
        if (newModelValue === true && newExhibitorToEdit) {
            form.name = newExhibitorToEdit.name ?? '';
            form.description = newExhibitorToEdit.description ?? '';
            form.contact_email = newExhibitorToEdit.contact_email ?? '';
            form.contact_phone = newExhibitorToEdit.contact_phone ?? null;
            form.logo = null;
            form.banner = null;
            if (logoInputRef.value) logoInputRef.value.value = '';
            if (bannerInputRef.value) bannerInputRef.value.value = '';
            form.clearErrors();
        } else if (newModelValue === false) {
            form.reset();
            form.clearErrors();
            logoLoading.value = false;
            form.logo = null;
            form.banner = null;
            bannerLoading.value = false;
            if (logoInputRef.value) logoInputRef.value.value = '';
            if (bannerInputRef.value) bannerInputRef.value.value = '';
            // Revoke URLs when resetting based on exhibitor change
            revokePreviewUrl(logoPreviewUrl);
            revokePreviewUrl(bannerPreviewUrl);
        }
    },
    { immediate: true, deep: true },
);

onUnmounted(() => {
    revokePreviewUrl(logoPreviewUrl);
    revokePreviewUrl(bannerPreviewUrl);
});
</script>
