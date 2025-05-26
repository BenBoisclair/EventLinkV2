<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import Input from "@/Components/Forms/Input.vue";
import InputError from "@/Components/Forms/InputError.vue";
import InputLabel from "@/Components/Forms/InputLabel.vue";
import Button from "@/Components/UI/Button.vue";
import Checkbox from "@/Components/UI/Checkbox.vue";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    terms: false,
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Register" />
    <div class="w-full min-h-screen dark:bg-dark-background">
        <div class="flex flex-col min-h-screen lg:flex-row">
            <div
                class="hidden dark:bg-dark-primary bg-primary lg:flex lg:w-1/2"
            ></div>
            <div
                class="dark:bg-dark-surface flex w-full items-center justify-center lg:w-[50%]"
            >
                <div class="w-full max-w-[400px] space-y-8">
                    <img
                        :src="'/logo-alternative.svg'"
                        alt="Logo"
                        class="w-auto pr-8 h-14"
                    />
                    <h2
                        class="mt-6 text-3xl font-bold tracking-tight text-gray-900 dark:text-dark-text-primary"
                    >
                        Create an account
                    </h2>
                    <!-- Registration Form -->
                    <form @submit.prevent="submit" class="mt-8 space-y-6">
                        <div class="space-y-4 rounded-md">
                            <div>
                                <InputLabel for="name" value="Name" />
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="block w-full mt-1 dark:bg-dark-surface-elevated dark:text-dark-text-primary dark:border-dark-border"
                                    autocomplete="name"
                                    autofocus
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.name"
                                />
                            </div>
                            <div>
                                <InputLabel for="email" value="Email address" />
                                <Input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="block w-full mt-1 dark:bg-dark-surface-elevated dark:text-dark-text-primary dark:border-dark-border"
                                    autocomplete="username"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.email"
                                />
                            </div>
                            <div>
                                <InputLabel for="password" value="Password" />
                                <Input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    required
                                    class="block w-full mt-1 dark:bg-dark-surface-elevated dark:text-dark-text-primary dark:border-dark-border"
                                    autocomplete="new-password"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.password"
                                />
                            </div>
                            <div>
                                <InputLabel
                                    for="password_confirmation"
                                    value="Confirm Password"
                                />
                                <Input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    required
                                    class="block w-full mt-1 dark:bg-dark-surface-elevated dark:text-dark-text-primary dark:border-dark-border"
                                    autocomplete="new-password"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.password_confirmation"
                                />
                            </div>
                        </div>
                        <div
                            v-if="
                                $page.props.jetstream
                                    .hasTermsAndPrivacyPolicyFeature
                            "
                        >
                            <Checkbox
                                id="terms"
                                v-model:checked="form.terms"
                                required
                            >
                                <span class="dark:text-dark-text-secondary"
                                    >I agree to the</span
                                >
                                <Link
                                    :href="route('terms.show')"
                                    class="font-medium dark:text-dark-primary dark:hover:text-dark-primary-dimmed text-primary hover:text-primary/80"
                                    target="_blank"
                                    >Terms of Service</Link
                                >
                                <span class="dark:text-dark-text-secondary"
                                    >and</span
                                >
                                <Link
                                    :href="route('policy.show')"
                                    class="font-medium dark:text-dark-primary dark:hover:text-dark-primary-dimmed text-primary hover:text-primary/80"
                                    target="_blank"
                                    >Privacy Policy</Link
                                >
                            </Checkbox>
                            <InputError
                                class="mt-2"
                                :message="form.errors.terms"
                            />
                        </div>
                        <div>
                            <Button
                                type="submit"
                                :text="
                                    form.processing
                                        ? 'Creating account...'
                                        : 'Create account'
                                "
                                variant="primary"
                                class="w-full"
                                :disabled="form.processing"
                            />
                        </div>
                    </form>
                    <p
                        class="mt-2 text-sm text-center text-gray-600 dark:text-dark-text-secondary"
                    >
                        Already have an account?
                        <Link
                            :href="route('login')"
                            class="font-medium dark:text-dark-primary dark:hover:text-dark-primary-dimmed text-primary hover:text-primary/80"
                        >
                            Sign in
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
