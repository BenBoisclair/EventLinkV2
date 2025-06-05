<script setup lang="ts">
import Input from "@/Components/Forms/Input.vue";
import InputLabel from "@/Components/Forms/InputLabel.vue";
import Select from "@/Components/Forms/Select.vue";
import Button from "@/Components/UI/Button.vue";
import Checkbox from "@/Components/UI/Checkbox.vue";
import IconButton from "@/Components/UI/IconButton.vue";
import Section from "@/Components/UI/Section.vue";
import type { AttendeesFormBlockProps } from "@/types/blocks";
import { useBlockEditor } from "@/Composables/useBlockEditor";
import { ref, type PropType } from "vue";

const props = defineProps({
    initialProps: {
        type: Object as PropType<AttendeesFormBlockProps>,
        required: true,
    },
    websiteId: { type: Number, required: true },
    blockIdProp: { type: String, required: true },
});

const {
    currentProps,
    updateProperty,
    updateArrayItem,
    addArrayItem,
    removeArrayItem,
} = useBlockEditor<AttendeesFormBlockProps>(props.blockIdProp);

const expandedFields = ref<number[]>([]);

const fieldTypeOptions = [
    { value: "text", label: "Text" },
    { value: "email", label: "Email" },
    { value: "tel", label: "Phone" },
    { value: "textarea", label: "Text Area" },
];

const toggleFieldExpansion = (index: number) => {
    const currentIndex = expandedFields.value.indexOf(index);
    if (currentIndex === -1) {
        expandedFields.value.push(index);
    } else {
        expandedFields.value.splice(currentIndex, 1);
    }
};

const addFormField = () => {
    addArrayItem("fields", {
        name: `custom_${Date.now()}`,
        label: "New Field",
        type: "text",
        required: false,
        enabled: true,
        deletable: true,
        options: [],
    });
};

const addOption = (fieldIndex: number) => {
    const field = currentProps.value?.fields?.[fieldIndex];
    if (field) {
        updateArrayItem("fields", fieldIndex, {
            options: [...(field.options || []), ""],
        });
    }
};

const updateOption = (
    fieldIndex: number,
    optionIndex: number,
    value: string
) => {
    const field = currentProps.value?.fields?.[fieldIndex];
    if (field?.options) {
        const newOptions = [...field.options];
        newOptions[optionIndex] = value;
        updateArrayItem("fields", fieldIndex, { options: newOptions });
    }
};

const removeOption = (fieldIndex: number, optionIndex: number) => {
    const field = currentProps.value?.fields?.[fieldIndex];
    if (field?.options) {
        const newOptions = field.options.filter((_, i) => i !== optionIndex);
        updateArrayItem("fields", fieldIndex, { options: newOptions });
    }
};
</script>

<template>
    <div v-if="currentProps" class="flex flex-col h-full gap-5">

        <!-- Form Title -->
        <Section class="flex-shrink-0 space-y-2 dark:bg-dark-surface">
            <InputLabel
                value="Form Title"
                class="dark:text-dark-text-primary"
            />
            <Input
                :model-value="currentProps.title"
                @update:model-value="updateProperty('title', $event)"
                class="w-full dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
            />
        </Section>

        <!-- Form Fields -->
        <Section
            class="pr-2 mb-4 overflow-y-auto dark:bg-dark-surface shrink-0"
        >
            <div class="flex items-center justify-between mb-2">
                <InputLabel
                    value="Form Fields"
                    class="font-medium dark:text-dark-text-primary"
                />
            </div>

            <div class="space-y-2">
                <div
                    v-for="(field, index) in currentProps.fields"
                    :key="index"
                    class="border border-gray-200 rounded-lg dark:border-dark-border"
                >
                    <!-- Field Header -->
                    <div
                        class="flex items-center justify-between p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-dark-surface-elevated"
                        @click="toggleFieldExpansion(index)"
                    >
                        <div class="flex items-center space-x-2">
                            <h3
                                class="text-sm font-medium text-gray-800 dark:text-dark-text-primary"
                            >
                                {{ field.label || "Unnamed Field" }}
                            </h3>
                            <span
                                v-if="field.required"
                                class="px-1.5 py-0.5 text-xs font-medium text-red-600 bg-red-100 rounded dark:bg-dark-status-red/20 dark:text-dark-status-red"
                            >
                                Required
                            </span>
                            <span
                                v-if="
                                    field.enabled === false && !field.required
                                "
                                class="px-1.5 py-0.5 text-xs text-gray-600 bg-gray-100 rounded dark:bg-dark-surface-elevated dark:text-dark-text-secondary"
                            >
                                Hidden
                            </span>
                        </div>

                        <div class="flex items-center space-x-1">
                            <IconButton
                                v-if="field.deletable !== false"
                                title="Delete field"
                                variant="danger"
                                size="sm"
                                @click.stop="removeArrayItem('fields', index)"
                            />
                            <div
                                class="p-1 text-gray-500 dark:text-dark-text-secondary"
                            >
                                <v-icon
                                    :icon="
                                        expandedFields.includes(index)
                                            ? '$chevronUp'
                                            : '$chevronDown'
                                    "
                                    size="small"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Expanded Field Details -->
                    <div
                        v-if="expandedFields.includes(index)"
                        class="p-4 space-y-4 border-t border-gray-200 bg-gray-50/50 dark:border-dark-border dark:bg-dark-surface-elevated/50"
                    >
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Field Name -->
                            <div>
                                <InputLabel
                                    value="Field Name"
                                    class="text-xs dark:text-dark-text-secondary"
                                />
                                <Input
                                    :model-value="field.name"
                                    @update:model-value="
                                        updateArrayItem('fields', index, {
                                            name: $event,
                                        })
                                    "
                                    :disabled="field.deletable === false"
                                    placeholder="e.g., company_name"
                                    class="w-full mt-1 text-sm dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary disabled:opacity-50"
                                />
                            </div>

                            <!-- Field Label -->
                            <div>
                                <InputLabel
                                    value="Field Label"
                                    class="text-xs dark:text-dark-text-secondary"
                                />
                                <Input
                                    :model-value="field.label"
                                    @update:model-value="
                                        updateArrayItem('fields', index, {
                                            label: $event,
                                        })
                                    "
                                    placeholder="Label shown to user"
                                    class="w-full mt-1 text-sm dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
                                />
                            </div>
                        </div>

                        <!-- Field Type -->
                        <div>
                            <InputLabel
                                value="Field Type"
                                class="text-xs dark:text-dark-text-secondary"
                            />
                            <Select
                                :model-value="field.type"
                                @update:model-value="
                                    updateArrayItem('fields', index, {
                                        type: $event,
                                    })
                                "
                                :options="fieldTypeOptions"
                                :disabled="field.deletable === false"
                                class="w-full mt-1 text-sm dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary disabled:opacity-50"
                            />
                        </div>

                        <!-- Options for Select Fields -->
                        <div
                            v-if="field.type === 'select'"
                            class="p-3 border border-gray-200 rounded dark:border-dark-border"
                        >
                            <InputLabel
                                value="Options"
                                class="mb-2 text-xs dark:text-dark-text-secondary"
                            />
                            <div class="space-y-2">
                                <div
                                    v-for="(
                                        option, optIndex
                                    ) in field.options ?? []"
                                    :key="optIndex"
                                    class="flex items-center space-x-2"
                                >
                                    <Input
                                        :model-value="option"
                                        @update:model-value="
                                            updateOption(
                                                index,
                                                optIndex,
                                                $event
                                            )
                                        "
                                        placeholder="Option label"
                                        class="flex-grow text-sm dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
                                    />
                                    <button
                                        @click="removeOption(index, optIndex)"
                                        class="text-red-500 hover:text-red-700 dark:text-dark-status-red dark:hover:text-dark-status-red/80"
                                    >
                                        &times;
                                    </button>
                                </div>
                            </div>
                            <Button
                                text="Add Option"
                                variant="outline-secondary"
                                size="xs"
                                class="mt-2"
                                @click="addOption(index)"
                            />
                        </div>

                        <!-- Field Settings -->
                        <div class="flex items-center space-x-4">
                            <Checkbox
                                :model-value="field.required"
                                @update:model-value="
                                    updateArrayItem('fields', index, {
                                        required: $event,
                                    })
                                "
                                :disabled="field.deletable === false"
                                label-class="dark:text-dark-text-primary"
                            >
                                Required
                            </Checkbox>
                            <Checkbox
                                :model-value="field.enabled"
                                @update:model-value="
                                    updateArrayItem('fields', index, {
                                        enabled: $event,
                                    })
                                "
                                :disabled="field.required"
                                label-class="dark:text-dark-text-primary"
                            >
                                Enabled
                            </Checkbox>
                        </div>
                    </div>
                </div>

                <Button
                    text="Add Field"
                    variant="outline-secondary"
                    size="xs"
                    @click="addFormField"
                />
            </div>
        </Section>

        <!-- Button Settings -->
        <Section class="flex-shrink-0 space-y-2 dark:bg-dark-surface">
            <InputLabel
                value="Button Text"
                class="dark:text-dark-text-primary"
            />
            <Input
                :model-value="currentProps.buttonText"
                @update:model-value="updateProperty('buttonText', $event)"
                class="w-full dark:bg-dark-surface-elevated dark:border-dark-border dark:text-dark-text-primary"
            />
        </Section>
    </div>
</template>
