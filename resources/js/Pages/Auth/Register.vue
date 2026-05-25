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
    organization_name: '',
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
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-3">
            <h1 class="mx-auto text-2xl font-semibold" style="color: #F8D794">Регистрация</h1>
            
            <!-- Имя -->
            <div>
                <InputLabel for="name" value="Имя" style="color: #F8D794" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    style="background-color: #284139; border: 2px solid #886830; color: white; border-radius: 12px;"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Фамилия -->
            <div>
                <InputLabel for="middlename" value="Фамилия" style="color: #F8D794" />
                <TextInput
                    id="middlename"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.middlename"
                    required
                    autocomplete="middlename"
                    style="background-color: #284139; border: 2px solid #886830; color: white; border-radius: 12px;"
                />
                <InputError class="mt-2" :message="form.errors.middlename" />
            </div>

            <!-- Отчество -->
            <div>
                <InputLabel for="lastname" value="Отчество" style="color: #F8D794" />
                <TextInput
                    id="lastname"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.lastname"
                    autocomplete="lastname"
                    style="background-color: #284139; border: 2px solid #886830; color: white; border-radius: 12px;"
                />
                <InputError class="mt-2" :message="form.errors.lastname" />
            </div>

            <!-- Логин -->
            <div>
                <InputLabel for="login" value="Логин" style="color: #F8D794" />
                <TextInput
                    id="login"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.login"
                    required
                    autocomplete="login"
                    style="background-color: #284139; border: 2px solid #886830; color: white; border-radius: 12px;"
                />
                <InputError class="mt-2" :message="form.errors.login" />
            </div>

            <!-- Почта -->
            <div>
                <InputLabel for="email" value="Почта" style="color: #F8D794" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    style="background-color: #284139; border: 2px solid #886830; color: white; border-radius: 12px;"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Телефон -->
            <div>
                <InputLabel for="number" value="Телефон" style="color: #F8D794" />
                <MaskInput 
                    v-model="form.number" 
                    mask="+7 (###) ###-##-##"
                    placeholder="+7 (___) ___-__-__"
                    required
                    class="mt-1 block w-full h-12 rounded-xl"
                    style="background-color: #284139; border: 2px solid #886830; color: white;"
                />
                <InputError class="mt-2" :message="form.errors.number" />
            </div>

            <!-- Роль -->
            <div>
                <InputLabel for="role" value="Ваша роль" style="color: #F8D794" />
                <Select
                    id="role"
                    type="select"
                    class="mt-1 block w-full rounded-xl"
                    v-model="form.role"
                    required
                    style="background-color: #284139; border: 2px solid #886830; color: white;"
                />
                <InputError class="mt-2" :message="form.errors.role" />
            </div>

            <!-- Название организации -->
            <div v-if="form.role === 'Organisator'">
                <InputLabel for="organization_name" value="Название организации" style="color: #F8D794" />
                <TextInput
                    id="organization_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.organization_name"
                    autocomplete="organization_name"
                    style="background-color: #284139; border: 2px solid #886830; color: white; border-radius: 12px;"
                />
                <InputError class="mt-2" :message="form.errors.organization_name" />
            </div>

            <!-- Пароль -->
            <div>
                <InputLabel for="password" value="Пароль" style="color: #F8D794" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    style="background-color: #284139; border: 2px solid #886830; color: white; border-radius: 12px;"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Подтверждение пароля -->
            <div>
                <InputLabel for="password_confirmation" value="Подтвердите пароль" style="color: #F8D794" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    style="background-color: #284139; border: 2px solid #886830; color: white; border-radius: 12px;"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <!-- Согласие -->
            <div class="mt-2">
                <label class="flex items-center">
                    <Checkbox name="acceptPersonalDate" v-model:checked="form.acceptPersonalDate" required style="border-color: #886830" />
                    <span class="ms-2 text-sm" style="color: #F8D794">Согласие на обработку персональных данных</span>
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end flex-col">
                <Link
                    :href="route('login')"
                    class="mb-2 rounded-xl w-full text-base text-end hover:opacity-80 transition-colors"
                    style="color: #F8D794"
                >
                    Уже зарегистрированы?
                </Link>

                <PrimaryButton
                    class="w-full flex justify-center"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                    style="background-color: #809076; border: 2px solid #886830; color: white; border-radius: 12px; padding: 10px;"
                >
                    Зарегистрироваться
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>