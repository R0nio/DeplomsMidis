<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Восстановление пароля — InvestProject" />

        <h1 class="text-2xl text-black mb-4">Восстановление пароля</h1>

        <p class="mb-4 text-sm text-gray-800">
            Забыли пароль? Не беда. Сообщите нам ваш email, и мы вышлем
            ссылку для сброса пароля.
        </p>

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-700"
            role="status"
            aria-live="polite"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" novalidate>
            <div>
                <InputLabel for="email" value="Email" :required="true" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="email"
                    :invalid="!!form.errors.email"
                    described-by="email-error"
                />

                <InputError id="email-error" class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    type="submit"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                    :aria-busy="form.processing ? 'true' : 'false'"
                >
                    Отправить ссылку для сброса пароля
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
