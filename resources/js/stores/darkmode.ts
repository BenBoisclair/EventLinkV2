import { defineStore } from 'pinia';
import { computed, isRef, ref, watch } from 'vue';

export type Theme = 'light' | 'dark' | 'system';

// Keep instance outside the defineStore factory function scope
let _vuetifyInstance: any = null;

// Helper function to apply the theme class and update Vuetify
const applyThemePreference = (theme: Theme) => {
    // Use the instance variable directly
    if (!_vuetifyInstance) {
        console.error('Vuetify instance not initialized in theme store.');
        return;
    }

    const isDark =
        theme === 'dark' ||
        (theme === 'system' &&
            window.matchMedia('(prefers-color-scheme: dark)').matches);

    // Apply class to HTML element for Tailwind
    document.documentElement.classList.toggle('dark', isDark);

    // Update Vuetify theme
    if (_vuetifyInstance.theme?.global?.name) {
        // Check if it's a ref before accessing .value
        if (isRef(_vuetifyInstance.theme.global.name)) {
            _vuetifyInstance.theme.global.name.value = isDark
                ? 'dark'
                : 'light';
        } else {
            console.error(
                'Vuetify theme global name is not a ref:',
                _vuetifyInstance.theme.global.name,
            );
        }
    } else {
        console.error('Vuetify theme properties are missing.');
    }
};

export const useThemeStore = defineStore('theme', () => {
    const currentTheme = ref<Theme>('system');
    // REMOVED: const vuetify = ref<any>(null);

    const initializeTheme = (vuetifyInstance: any) => {
        // Store the instance in the module-scoped variable
        _vuetifyInstance = vuetifyInstance;
        const savedTheme = (localStorage.getItem('theme') as Theme) || 'system';
        setTheme(savedTheme);

        // Watch for system theme changes
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', handleSystemThemeChange);
    };

    const setTheme = (newTheme: Theme) => {
        currentTheme.value = newTheme;
        localStorage.setItem('theme', newTheme);
        // Pass only the theme, applyThemePreference will use the stored instance
        applyThemePreference(newTheme);
    };

    const handleSystemThemeChange = () => {
        if (currentTheme.value === 'system') {
            // Pass only the theme
            applyThemePreference('system');
        }
    };

    // Watch for changes in the store's theme state
    watch(currentTheme, (newTheme) => {
        // Pass only the theme
        applyThemePreference(newTheme);
    });

    // Computed property to determine if the effective theme is dark
    const isDark = computed(() => {
        const theme = currentTheme.value;
        return (
            theme === 'dark' ||
            (theme === 'system' &&
                window.matchMedia('(prefers-color-scheme: dark)').matches)
        );
    });

    return {
        currentTheme,
        initializeTheme,
        setTheme,
        isDark, // Expose the computed property
    };
});
