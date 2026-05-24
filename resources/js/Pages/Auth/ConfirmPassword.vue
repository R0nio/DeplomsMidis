<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Подтверждение пароля — InvestProject" />

        <h1 class="text-2xl text-black mb-4">Подтверждение пароля</h1>

        <p class="mb-4 text-sm text-gray-800">
            Это защищённая область. Пожалуйста, подтвердите ваш пароль, прежде чем продолжить.
        </p>

        <form @submit.prevent="submit" novalidate>
            <div>
                <InputLabel for="password" value="Пароль" :required="true" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                    :invalid="!!form.errors.password"
                    described-by="password-error"
                />
                <InputError id="password-error" class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 flex justify-end">
                <PrimaryButton
                    type="submit"
                    class="ms-4"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                    :aria-busy="form.processing ? 'true' : 'false'"
                >
                    Подтвердить
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
