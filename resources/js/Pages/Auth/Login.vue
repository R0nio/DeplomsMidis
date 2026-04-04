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
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>
        
        <form @submit.prevent="submit" class="flex flex-col gap-5">
            <h1 class="mx-auto text-black text-2xl">Авторизация</h1>
            <div>
                <InputLabel for="login" value="Логин" />

                <TextInput
                    id="login"
                    type="text"
                    class="block w-full"
                    v-model="form.login"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.login" />
            </div>

            <div class="mt-2">
                <InputLabel for="password" value="Пароль" />

                <TextInput
                    id="password"
                    type="password"
                    class="block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-2 w-full flex flex-col items-center justify-center ">
                <Link
                    v-if="canResetPassword"
                    :href="route('register')"
                    class="mb-2 rounded-xl w-full text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:text-blue-200"
                >
                    Регистрация
                </Link>

                <PrimaryButton
                    class=""
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Войти
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
