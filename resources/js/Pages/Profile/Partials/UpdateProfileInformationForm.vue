<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section aria-labelledby="profile-info-heading">
        <header>
            <h2 id="profile-info-heading" class="text-lg font-medium text-gray-900">
                Информация профиля
            </h2>

            <p class="mt-1 text-sm text-gray-800">
                Обновите имя и адрес электронной почты вашего аккаунта.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
            novalidate
        >
            <div>
                <InputLabel for="profile-name" value="Имя" :required="true" />

                <TextInput
                    id="profile-name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="given-name"
                    :invalid="!!form.errors.name"
                    described-by="profile-name-error"
                />

                <InputError id="profile-name-error" class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="profile-email" value="Email" :required="true" />

                <TextInput
                    id="profile-email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="email"
                    :invalid="!!form.errors.email"
                    described-by="profile-email-error"
                />

                <InputError id="profile-email-error" class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-900">
                    Ваш email не подтверждён.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-900 underline hover:text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700"
                    >
                        Отправить письмо повторно.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-700"
                    role="status"
                    aria-live="polite"
                >
                    На ваш email отправлена новая ссылка для подтверждения.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton
                    type="submit"
                    :disabled="form.processing"
                    :aria-busy="form.processing ? 'true' : 'false'"
                >Сохранить</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-800"
                        role="status"
                        aria-live="polite"
                    >
                        Сохранено.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
