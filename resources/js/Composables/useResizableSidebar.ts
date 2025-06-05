import { onUnmounted, ref } from "vue";

interface ResizableSidebarOptions {
    initialWidth?: number;
    minWidth?: number;
    maxWidth?: number;
}

export function useResizableSidebar(options: ResizableSidebarOptions = {}) {
    const { initialWidth = 432, minWidth = 300, maxWidth = 800 } = options;

    const sidebarWidth = ref(initialWidth);
    const isDragging = ref(false);
    const startX = ref(0);
    const startWidth = ref(0);

    const handleMouseMove = (event: MouseEvent) => {
        if (!isDragging.value) return;
        const dx = event.clientX - startX.value;
        let newWidth = startWidth.value + dx;

        newWidth = Math.max(minWidth, Math.min(newWidth, maxWidth));

        sidebarWidth.value = newWidth;
    };

    const handleMouseUp = () => {
        if (isDragging.value) {
            isDragging.value = false;
            window.removeEventListener("mousemove", handleMouseMove);
            window.removeEventListener("mouseup", handleMouseUp);
            document.body.style.userSelect = "";
        }
    };

    const handleMouseDown = (event: MouseEvent) => {
        isDragging.value = true;
        startX.value = event.clientX;
        startWidth.value = sidebarWidth.value;
        window.addEventListener("mousemove", handleMouseMove);
        window.addEventListener("mouseup", handleMouseUp);
        document.body.style.userSelect = "none";
        event.preventDefault();
    };

    // Cleanup listeners on component unmount
    onUnmounted(() => {
        window.removeEventListener("mousemove", handleMouseMove);
        window.removeEventListener("mouseup", handleMouseUp);
        if (isDragging.value) {
            document.body.style.userSelect = "";
        }
    });

    return {
        sidebarWidth,
        handleMouseDown,
    };
}
