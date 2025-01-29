<script setup lang="ts">
import { onMounted, ref } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const emailInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};

onMounted(() => {
    // Small delay to let the browser settle
    setTimeout(() => {
        emailInput.value?.focus();
    }, 100);
});
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <h2 class="mb-4 text-2xl text-center font-bold text-primary">
            Welcome to Home Inventory
        </h2>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    ref="emailInput"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password"  />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-tertiary/80">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="underline text-sm text-tertiary/80 hover:text-tertiary rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-border"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-primary/70">
                    Don't have an account?
                    <Link
                        :href="route('register')"
                        class="font-medium text-indigo-600 hover:text-border focus:outline-none focus:ring-2 focus:ring-border focus:ring-offset-2"
                    >
                        Sign up now
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
