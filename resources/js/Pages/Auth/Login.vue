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
            <h1 class="mx-auto text-2xl font-semibold" style="color: #F8D794">Авторизация</h1>
            
            <div>
                <InputLabel for="login" value="Логин" style="color: #F8D794" />
                <TextInput
                    id="login"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.login"
                    required
                    autofocus
                    autocomplete="login"
                    style="background-color: #284139; border: 2px solid #886830; color: white; border-radius: 12px;"
                />
                <InputError class="mt-2" :message="form.errors.login" />
            </div>

            <div>
                <InputLabel for="password" value="Пароль" style="color: #F8D794" />
                <TextInput
                    id="password"
                    type="password"
                    class="block w-full mt-1"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    style="background-color: #284139; border: 2px solid #886830; color: white; border-radius: 12px;"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-2 w-full flex flex-col items-center justify-center">
                <Link
                    :href="route('register')"
                    class="mb-2 rounded-xl w-full text-base text-end hover:opacity-80 transition-colors"
                    style="color: #F8D794"
                >
                    Регистрация
                </Link>

                <PrimaryButton
                    class="w-full flex justify-center mt-2"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                    style="background-color: #809076; border: 2px solid #886830; color: white; border-radius: 12px; padding: 10px;"
                >
                    Войти
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>