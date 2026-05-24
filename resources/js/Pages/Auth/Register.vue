<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';
import Select from '@/Components/Select.vue';
import { MaskInput } from 'vue-mask-next';



const form = useForm({
    name: '',
    middlename: '',
    lastname: '',
    email: '',
    login: '',
    number: '',
    role: '',
    password: '',
    organization_name: 'none',
    password_confirmation: '',
    acceptPersonalDate: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Регистрация — InvestProject" />

        <form @submit.prevent="submit" class="flex flex-col gap-2" aria-labelledby="register-title" novalidate>
            <h1 id="register-title" class="mx-auto text-black text-2xl">Регистрация</h1>
            <!-- имя -->
            <div>
                <InputLabel for="name" value="Имя" :required="true" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="given-name"
                    :invalid="!!form.errors.name"
                    described-by="name-error"
                />

                <InputError id="name-error" class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Фамилия -->
            <div class="mt-2">
                <InputLabel for="middlename" value="Фамилия" :required="true" />

                <TextInput
                    id="middlename"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.middlename"
                    required
                    autocomplete="family-name"
                    :invalid="!!form.errors.middlename"
                    described-by="middlename-error"
                />

                <InputError id="middlename-error" class="mt-2" :message="form.errors.middlename" />
            </div>

            <!-- Отчество -->
            <div class="mt-2">
                <InputLabel for="lastname" value="Отчество" />

                <TextInput
                    id="lastname"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.lastname"
                    autocomplete="additional-name"
                    :invalid="!!form.errors.lastname"
                    described-by="lastname-error"
                />

                <InputError id="lastname-error" class="mt-2" :message="form.errors.lastname" />
            </div>

            <!-- Логин -->
            <div class="mt-2">
                <InputLabel for="login" value="Логин" :required="true" />

                <TextInput
                    id="login"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.login"
                    required
                    autocomplete="username"
                    :invalid="!!form.errors.login"
                    described-by="login-error"
                />

                <InputError id="login-error" class="mt-2" :message="form.errors.login" />
            </div>

            <!-- Почта -->
            <div class="mt-2">
                <InputLabel for="email" value="Почта" :required="true" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="email"
                    :invalid="!!form.errors.email"
                    described-by="email-error"
                />

                <InputError id="email-error" class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Телефон -->
            <div class="mt-2">
                <InputLabel for="number" value="Телефон" :required="true" />

                <MaskInput
                    id="number"
                    v-model="form.number"
                    mask="+7 (###) ###-##-##"
                    placeholder="+7 (___) ___-__-__"
                    required
                    autocomplete="tel"
                    inputmode="tel"
                    :aria-invalid="!!form.errors.number || undefined"
                    aria-describedby="number-error"
                    class="mt-1 block w-full rounded-md shadow-sm border-none focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-600 bg-[#E8E8E8] h-12 px-3"
                />

                <InputError id="number-error" class="mt-2" :message="form.errors.number" />
            </div>

            <!-- Роль -->
            <div class="mt-2">
                <InputLabel for="role" value="Ваша роль" :required="true" />

                <Select
                    id="role"
                    class="mt-1 block w-full"
                    v-model="form.role"
                    required
                    :invalid="!!form.errors.role"
                    described-by="role-error"
                />

                <InputError id="role-error" class="mt-2" :message="form.errors.role" />
            </div>

            <!-- Название организации -->
            <div v-if="form.role === 'Organisator'" class="mt-2">
                <InputLabel for="organization_name" value="Название организации" />

                <TextInput
                    id="organization_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.organization_name"
                    autocomplete="organization"
                    :invalid="!!form.errors.organization_name"
                    described-by="organization-error"
                />

                <InputError id="organization-error" class="mt-2" :message="form.errors.organization_name" />
            </div>

            <!-- Пароль -->
            <div class="mt-2">
                <InputLabel for="password" value="Пароль" :required="true" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    :invalid="!!form.errors.password"
                    described-by="password-error"
                />

                <InputError id="password-error" class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Подтверждение пароля -->
            <div class="mt-2">
                <InputLabel
                    for="password_confirmation"
                    value="Подтвердите пароль"
                    :required="true"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    :invalid="!!form.errors.password_confirmation"
                    described-by="password-confirmation-error"
                />

                <InputError
                    id="password-confirmation-error"
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>


            <div class="mt-4 flex items-center justify-end flex-col">
                <div class="mt-2 block w-full mb-4">
                    <label for="acceptPersonalDate" class="flex items-center gap-2 cursor-pointer">
                        <Checkbox
                            id="acceptPersonalDate"
                            name="acceptPersonalDate"
                            v-model:checked="form.acceptPersonalDate"
                            required
                            value="1"
                        />
                        <span class="ms-2 text-sm text-gray-900">Согласие на обработку персональных данных <span class="text-red-700" aria-hidden="true">*</span><span class="sr-only">обязательное поле</span></span>
                    </label>
                </div>
                <Link
                    :href="route('login')"
                    class="mb-2 rounded-xl w-full text-sm text-gray-900 underline hover:text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700"
                >
                    Уже зарегистрированы?
                </Link>

                <PrimaryButton
                    type="submit"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                    :aria-busy="form.processing ? 'true' : 'false'"
                >
                    Зарегистрироваться
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
