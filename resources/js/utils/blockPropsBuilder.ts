import type { BlockType } from "@/types/blocks";
import type { DeviceType } from "@/types/websiteBuilder";

export function buildBlockProps(
    block: BlockType,
    editingBlockId: string | null,
    editingBlockProps: Record<string, any> | null,
    _isEditorMode: boolean,
    device: DeviceType,
    event: any,
    websiteId: string | number | undefined
) {
    const isCurrentlyEditing = editingBlockId === block.id;

    const blockProps =
        isCurrentlyEditing && editingBlockProps
            ? { ...block.props, ...editingBlockProps }
            : block.props;

    return {
        ...blockProps,
        id: block.id,
        device,
        event,
        websiteId,
    };
}
