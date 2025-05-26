import { useWebsiteBuilderStore } from "@/stores/websiteBuilderStore";
import { computed } from "vue";

/**
 * Generic composable for block editors.
 * Provides helpers for updating block properties and array fields in a type-safe way.
 *
 * @template T - The type of the block props
 * @param blockIdProp - The block ID (for future extensibility)
 */
export function useBlockEditor<
    T extends Record<string, any> = Record<string, any>
>(blockIdProp: string) {
    const store = useWebsiteBuilderStore();

    // Reactive reference to the current editing block's props
    const currentProps = computed(() => store.editingBlockProps as T);

    // Update a single property
    const updateProperty = (key: string, value: any) => {
        store.updateEditingBlockProps({ [key]: value });
    };

    // Update multiple properties at once
    const updateProperties = (newProps: Record<string, any>) => {
        store.updateEditingBlockProps(newProps);
    };

    // Update an item in an array property
    const updateArrayItem = (arrayKey: string, index: number, updates: any) => {
        const currentArray = currentProps.value?.[arrayKey] || [];
        const newArray = currentArray.map((item: any, i: number) =>
            i === index ? { ...item, ...updates } : item
        );
        updateProperty(arrayKey, newArray);
    };

    // Add an item to an array property
    const addArrayItem = (arrayKey: string, newItem: any) => {
        const currentArray = currentProps.value?.[arrayKey] || [];
        updateProperty(arrayKey, [...currentArray, newItem]);
    };

    // Remove an item from an array property
    const removeArrayItem = (arrayKey: string, index: number) => {
        const currentArray = currentProps.value?.[arrayKey] || [];
        updateProperty(
            arrayKey,
            currentArray.filter((_: any, i: number) => i !== index)
        );
    };

    return {
        currentProps,
        updateProperty,
        updateProperties,
        updateArrayItem,
        addArrayItem,
        removeArrayItem,
    };
}
