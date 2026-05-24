<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Подтверждение почты — InvestProject" />

        <h1 class="text-2xl text-black mb-4">Подтверждение почты</h1>

        <p class="mb-4 text-sm text-gray-800">
            Спасибо за регистрацию! Перед тем как начать, подтвердите ваш
            email, перейдя по ссылке из письма. Если письмо не пришло, мы можем
            отправить ещё одно.
        </p>

        <p
            class="mb-4 text-sm font-medium text-green-700"
            role="status"
            aria-live="polite"
            v-if="verificationLinkSent"
        >
            На указанный при регистрации адрес отправлена новая ссылка для подтверждения.
        </p>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton
                    type="submit"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                    :aria-busy="form.processing ? 'true' : 'false'"
                >
                    Отправить письмо повторно
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="rounded-md text-sm text-gray-700 underline hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700"
                >Выйти</Link>
            </div>
        </form>
    </GuestLayout>
</template>
