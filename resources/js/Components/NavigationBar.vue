<template>
    <nav
        class="dark:bg-dark-background dark:border-dark-border border-b border-gray-200 bg-accent px-6 py-[14px]"
    >
        <div class="flex items-center justify-between w-full">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <Link href="/">
                    <img
                        src="/logo.svg"
                        alt="EventLink Logo"
                        class="h-[30px]"
                    />
                </Link>
            </div>

            <!-- Avatar with Menu -->
            <div class="flex-shrink-0">
                <v-menu
                    v-model="menu"
                    :close-on-content-click="true"
                    location="bottom end"
                    :offset="10"
                >
                    <template v-slot:activator="{ props }">
                        <div
                            v-bind="props"
                            class="flex items-center gap-2 px-2 py-2 rounded-md cursor-pointer dark:hover:bg-dark-surface-elevated hover:bg-black/20"
                        >
                            <Avatar
                                :name="auth.user ? auth.user.name : ''"
                                :image="
                                    auth.user && auth.user.avatar
                                        ? auth.user.avatar
                                        : ''
                                "
                                bgClass="dark:bg-dark-accent bg-primary"
                                textClass="font-semibold text-white dark:text-dark-text-primary"
                                size="h-[40px] w-[40px]"
                            />
                            <span
                                v-if="auth.user"
                                class="text-sm font-medium text-white dark:text-dark-text-primary"
                                >{{ auth.user.name }}</span
                            >
                            <v-icon
                                v-if="auth.user"
                                color="white"
                                class="dark:text-dark-text-primary"
                                icon="$chevronDown"
                            ></v-icon>
                        </div>
                    </template>

                    <v-list
                        density="compact"
                        min-width="250"
                        rounded="lg"
                        :slim="true"
                        class="dark:bg-dark-surface dark:text-dark-text-primary"
                    >
                        <template v-if="auth.user">
                            <v-list-item
                                class="mb-2 font-medium profile-list-item hover:cursor-default"
                                :slim="true"
                                density="compact"
                                rounded="lg"
                                :value="'profile'"
                                :ripple="false"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="dark:bg-dark-accent flex h-[40px] w-[40px] flex-shrink-0 items-center justify-center rounded-lg bg-primary"
                                    >
                                        <span
                                            class="font-semibold text-white dark:text-dark-text-primary"
                                            >{{
                                                auth.user.name
                                                    .charAt(0)
                                                    .toUpperCase()
                                            }}</span
                                        >
                                    </div>
                                    <div>
                                        <div
                                            class="text-sm font-medium dark:text-dark-text-primary"
                                        >
                                            {{ auth.user.name }}
                                        </div>
                                        <div
                                            class="text-xs text-gray-500 dark:text-dark-text-secondary"
                                        >
                                            {{ auth.user.email }}
                                        </div>
                                    </div>
                                </div>
                            </v-list-item>

                            <v-divider
                                :thickness="2"
                                class="dark:border-dark-border"
                            ></v-divider>

                            <!-- <v-list-item
                                title="View your Events"
                                :value="'view-events'"
                                :href="route('organiser.events.index')"
                                prepend-icon="$listBox"
                                class="text-sm dark:text-dark-text-primary"
                                :slim="true"
                                density="compact"
                            >
                            </v-list-item> -->

                            <!-- <v-menu location="start" open-on-hover :offset="0">
                                <template v-slot:activator="{ props }">
                                    <v-list-item
                                        v-bind="props"
                                        @click.stop
                                        title="Appearance"
                                        :value="'appearance'"
                                        prepend-icon="$brightness6"
                                        append-icon="$chevronRight"
                                        class="text-sm dark:text-dark-text-primary"
                                        :slim="true"
                                        density="compact"
                                    >
                                    </v-list-item>
                                </template>
                                <v-list
                                    density="compact"
                                    rounded="lg"
                                    :slim="true"
                                    class="dark:bg-dark-surface-elevated dark:text-dark-text-primary"
                                >
                                    <v-list-item
                                        title="Light"
                                        :value="'light'"
                                        prepend-icon="$weatherSunny"
                                        class="text-sm dark:text-dark-text-primary"
                                        @click="themeStore.setTheme('light')"
                                        :active="
                                            themeStore.currentTheme === 'light'
                                        "
                                        :slim="true"
                                        density="compact"
                                    >
                                    </v-list-item>
                                    <v-list-item
                                        title="Dark"
                                        :value="'dark'"
                                        prepend-icon="$weatherNight"
                                        class="text-sm dark:text-dark-text-primary"
                                        @click="themeStore.setTheme('dark')"
                                        :active="
                                            themeStore.currentTheme === 'dark'
                                        "
                                        :slim="true"
                                        density="compact"
                                    >
                                    </v-list-item>
                                    <v-list-item
                                        title="System Theme"
                                        :value="'system'"
                                        prepend-icon="$desktopTowerMonitor"
                                        class="text-sm dark:text-dark-text-primary"
                                        @click="themeStore.setTheme('system')"
                                        :active="
                                            themeStore.currentTheme === 'system'
                                        "
                                        :slim="true"
                                        density="compact"
                                    >
                                    </v-list-item>
                                </v-list>
                            </v-menu> -->

                            <v-divider
                                class="dark:border-dark-border"
                            ></v-divider>

                            <v-list-item
                                title="Logout"
                                :value="'logout'"
                                @click="logout"
                                prepend-icon="$logout"
                                class="text-sm dark:text-dark-text-primary"
                            >
                            </v-list-item>
                        </template>
                        <template v-else>
                            <v-list-item
                                title="Login"
                                :value="'login'"
                                :href="route('login')"
                                A
                                prepend-icon="$login"
                                class="text-sm dark:text-dark-text-primary"
                            >
                            </v-list-item>
                        </template>
                    </v-list>
                </v-menu>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { useThemeStore } from "@/stores/darkmode"; // Import the theme store
import { Link, router, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import Avatar from "@/Components/UI/Avatar.vue";
import { ensureCsrfCookie } from "@/utils/csrf.js";

const page = usePage();
const auth = page.props.auth;
const menu = ref(false);

const themeStore = useThemeStore(); // Use the store

const logout = async () => {
    try {
        // Get fresh CSRF cookie before logout
        await ensureCsrfCookie();
        
        // Perform logout
        router.post(route("logout"), {}, {
            onError: () => {
                // Even if logout fails, redirect to login for security
                window.location.href = route("login");
            },
            onFinish: () => {
                // Force a complete page reload to get fresh CSRF token
                window.location.href = route("login");
            }
        });
    } catch (error) {
        // Fallback: redirect to login if CSRF setup fails
        window.location.href = route("login");
    }
};
</script>

<style scoped>
.profile-list-item:hover :deep(.v-list-item__overlay) {
    opacity: 0 !important;
}
/* Additional styles can be added here if needed */
</style>
