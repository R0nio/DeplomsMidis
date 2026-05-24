<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    login: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Авторизация — InvestProject" />

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-700"
            role="status"
            aria-live="polite"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-5" aria-labelledby="login-title" novalidate>
            <h1 id="login-title" class="mx-auto text-black text-2xl">Авторизация</h1>
            <div>
                <InputLabel for="login" value="Логин" :required="true" />

                <TextInput
                    id="login"
                    type="text"
                    class="block w-full"
                    v-model="form.login"
                    required
                    autofocus
                    autocomplete="username"
                    :invalid="!!form.errors.login"
                    described-by="login-error"
                />

                <InputError id="login-error" class="mt-2" :message="form.errors.login" />
            </div>

            <div class="">
                <InputLabel for="password" value="Пароль" :required="true" />

                <TextInput
                    id="password"
                    type="password"
                    class="block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    :invalid="!!form.errors.password"
                    described-by="password-error"
                />

                <InputError id="password-error" class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-2 w-full flex flex-col items-center justify-center ">
                <Link
                    v-if="canResetPassword"
                    :href="route('register')"
                    class="mb-2 rounded-xl w-full text-sm text-gray-900 underline hover:text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700"
                >
                    Регистрация
                </Link>

                <PrimaryButton
                    type="submit"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                    :aria-busy="form.processing ? 'true' : 'false'"
                >
                    Войти
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
