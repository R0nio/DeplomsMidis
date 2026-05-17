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
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-2">
            <h1 class="mx-auto text-black text-2xl">Регистрация</h1>
            <!-- имя -->
            <div>
                <InputLabel for="name" value="Имя" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Фамилия -->
            <div class="mt-2">
                <InputLabel for="middlename" value="Фамилия" />

                <TextInput
                    id="middlename"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.middlename"
                    required
                    autofocus
                    autocomplete="middlename"
                />

                <InputError class="mt-2" :message="form.errors.middlename" />
            </div>

            <!-- Отчество -->
            <div class="mt-2">
                <InputLabel for="lastname" value="Отчество" />

                <TextInput
                    id="lastname"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.lastname"
                    autofocus
                    autocomplete="lastname"
                />

                <InputError class="mt-2" :message="form.errors.lastname" />
            </div>

            <!-- Логин -->
            <div class="mt-2">
                <InputLabel for="login" value="Логин" />

                <TextInput
                    id="login"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.login"
                    required
                    autocomplete="login"
                />

                <InputError class="mt-2" :message="form.errors.login" />
            </div>

            <!-- Почта -->
            <div class="mt-2">
                <InputLabel for="email" value="Почта" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Телефон -->
            <div class="mt-2">
                <InputLabel for="number" value="Телефон" />

                <MaskInput 
                    v-model="form.number" 
                    mask="+7 (###) ###-##-##"
                    placeholder="+7 (___) ___-__-__"
                    required
                    class="mt-1 block w-full rounded-md shadow-sm border-none focus:border-blue-200 focus:ring-blue-200  bg-[#E8E8E8] h-12"
                />

                <InputError class="mt-2" :message="form.errors.number" />
            </div>

            <!-- Роль -->
            <div class="mt-2">
                <InputLabel for="role" value="Ваша роль" />

                <Select
                    id="role"
                    type="select"
                    class="mt-1 block w-full"
                    v-model="form.role"
                    required
                    autocomplete="role"
                />

                <InputError class="mt-2" :message="form.errors.role" />
            </div>

            <!-- Пароль -->
            <div class="mt-2">
                <InputLabel for="password" value="Пароль" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Подтверждение пароля -->
            <div class="mt-2">
                <InputLabel
                    for="password_confirmation"
                    value="Подтвердите пароль"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            
            <div class="mt-4 flex items-center justify-end flex-col">
                <div class="mt-2 block w-full mb-4">
                    <label class="flex items-center">
                        <Checkbox name="acceptPersonalDate" v-model:checked="form.acceptPersonalDate" required/>
                        <span class="ms-2 text-sm text-gray-800 focus:outline-none focus:text-blue-2"
                            >Согласие на обработку персональных данных</span
                        >
                    </label>
                </div>
                <Link
                    :href="route('login')"
                    class="mb-2 rounded-xl w-full text-sm text-gray-800 underline hover:text-gray-300 focus:outline-none focus:text-blue-200"
                >
                    Уже зарегистрированы?
                </Link>

                <PrimaryButton
                    class=""
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Зарегистрироваться
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
