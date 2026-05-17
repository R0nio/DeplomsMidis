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
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";


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

const mainColor = "#8EB6FF";
</script>

<template>
  <Head title="Инвесторам" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">
        Создание проекта
      </h2>
    </template>
    <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16">
        <form @submit.prevent="submit" class="flex flex-col gap-2">
            <h1 class=" text-black text-2xl">Регистрация</h1>
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


            
            <div class="mt-4 flex items-center justify-end flex-col">
                <PrimaryButton
                    class=""
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Создать проект
                </PrimaryButton>
            </div>
        </form>
    </div>
  </AuthenticatedLayout>
</template>