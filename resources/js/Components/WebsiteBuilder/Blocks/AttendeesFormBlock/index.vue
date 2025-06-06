<script setup lang="ts">
import BlockInput from "@/Components/Forms/BlockInput.vue";
import BlockInputLabel from "@/Components/Forms/BlockInputLabel.vue";
import BlockButton from "@/Components/UI/BlockButton.vue";
import BlockContainer from "@/Components/WebsiteBuilder/Renderer/BlockContainer.vue";
import type { AttendeesFormBlockProps } from "@/types/blocks";
import type { PageProps } from "@/types/index";
import { useForm, usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref, withDefaults, watch, reactive } from "vue";
import BlockTitle from "../BlockTitle.vue";
import { useThemeColors } from "@/Composables/useThemeColors";

interface Props extends AttendeesFormBlockProps {
    theme?: {
        primary: string;
        secondary: string;
        accent: string;
        background: string;
    };
}

const props = withDefaults(defineProps<Props>(), {});

const { colors } = useThemeColors(props.theme);

const blockBackgroundColor = computed(() => {
    if (props.useThemeBackground !== false) {
        return colors.value.backgroundPrimary;
    }
    return props.backgroundColor || colors.value.backgroundPrimary;
});

const page = usePage<PageProps>();

const enabledFields = computed(() => {
    return Array.isArray(props.fields)
        ? props.fields.filter((field) => field && field.enabled !== false)
        : [];
});

const formData = reactive<Record<string, any>>({});

const updateFormData = () => {
    enabledFields.value.forEach((field) => {
        if (!(field.name in formData)) {
            formData[field.name] = "";
        }
    });

    Object.keys(formData).forEach((key) => {
        if (!enabledFields.value.some((field) => field.name === key)) {
            delete formData[key];
        }
    });
};

let form: any = null;

const isSubmitted = ref(false);

watch(enabledFields, updateFormData, { deep: true, immediate: true });

const handleSubmit = () => {
    if (!props.id || !props.event?.id) {
        return;
    }

    const submitData = {
        ...formData,
        _block_id: props.id,
    };

    form = useForm(submitData);

    form.post(`/events/${props.event.id}/attendees/register`, {
        preserveScroll: true,
        onSuccess: () => {
            Object.keys(formData).forEach((key) => {
                formData[key] = "";
            });

            setTimeout(() => {
                isSubmitted.value = true;
            }, 100);

            setTimeout(() => {
                isSubmitted.value = false;
            }, 1000);
        },
    });
};
</script>

<template>
    <BlockContainer
        :background-color="blockBackgroundColor"
        class="py-12 md:py-16"
    >
        <div class="container px-8 mx-auto" id="register">
            <div class="max-w-lg mx-auto md:max-w-2xl">
                <BlockTitle
                    :title="props.title || 'Register for the Event'"
                    :title-color="colors.textPrimary"
                    tag="h2"
                    text-align="center"
                    default-classes="mb-6 text-xl font-bold md:mb-8 md:text-3xl"
                />
                <p
                    v-if="!enabledFields || enabledFields.length === 0"
                    class="italic text-center text-gray-500"
                >
                    No form fields configured.
                </p>
                <form
                    v-else
                    @submit.prevent="handleSubmit"
                    class="space-y-4 md:space-y-6"
                >
                    <div
                        v-for="(field, index) in enabledFields"
                        :key="field.name || `field-${index}`"
                        class="space-y-1 md:space-y-2"
                    >
                        <BlockInputLabel
                            :for="field.name"
                            class="text-sm md:text-base"
                            :style="{ color: colors.textSecondary }"
                        >
                            {{ field.label }}
                            <span v-if="field.required" class="text-red-500"
                                >*</span
                            >
                        </BlockInputLabel>
                        <BlockInput
                            :id="field.name"
                            :type="field.type || 'text'"
                            v-model="formData[field.name]"
                            :name="field.name"
                            :required="field.required"
                            class="w-full text-sm md:text-base"
                            :placeholder="(field as any).placeholder || ''"
                        />
                    </div>

                    <div
                        v-if="form?.errors?.message"
                        class="text-sm rounded text-danger md:text-base"
                        role="alert"
                    >
                        {{ form?.errors?.message }}
                    </div>
                    <div
                        v-if="
                            form?.errors && Object.keys(form.errors).length > 0
                        "
                        class="text-sm rounded text-danger md:text-base"
                        role="alert"
                    >
                        <div
                            v-for="(error, fieldName) in form?.errors || {}"
                            :key="String(fieldName)"
                        >
                            <span v-if="String(fieldName) !== 'message'">{{
                                error
                            }}</span>
                        </div>
                    </div>
                    <div
                        v-if="page.props.flash?.success"
                        class="text-sm rounded md:text-base text-success"
                        role="alert"
                    >
                        {{ page.props.flash.success }}
                    </div>

                    <BlockButton
                        type="submit"
                        :text="
                            isSubmitted ? 'Sent!' : props.buttonText || 'Submit'
                        "
                        :variant="isSubmitted ? 'success' : 'primary'"
                        :color="isSubmitted ? '#10b981' : colors.buttonPrimary"
                        :style="{
                            color: isSubmitted
                                ? '#FFFFFF'
                                : colors.buttonPrimaryText,
                            boxShadow: isSubmitted
                                ? '0 0 20px rgba(16, 185, 129, 0.5)'
                                : 'none',
                            transition:
                                'all 0.1s cubic-bezier(0.68, -0.55, 0.265, 1.55)',
                        }"
                        class="w-full py-2 text-sm md:py-3 md:text-base"
                        :class="{
                            'mt-12':
                                !form?.hasErrors && !page.props.flash?.success,
                        }"
                        :disabled="form?.processing || isSubmitted"
                        :loading="form?.processing"
                    >
                    </BlockButton>

                    <div class="mt-4 text-center">
                        <a
                            href="/terms"
                            target="_blank"
                            class="text-sm text-gray-600 underline hover:text-gray-800"
                            >Terms and Conditions</a
                        >
                    </div>
                </form>
            </div>
        </div>
    </BlockContainer>
</template>
