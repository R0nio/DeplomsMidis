<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import InfoProfile from '@/Components/Main/InfoProfile.vue';

const user = usePage().props.auth.user;

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    favoriteProjects: {
        type: Array,
        default: null,
    },
    myProjects: {
        type: Array,
        default: null,
    },
});

// Изменен цвет фона
const mainColor = "#436343";
</script>

<template>
    <Head title="Профиль" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-semibold leading-tight" :style="{ color: '#F8D794' }">
                Личный кабинет
            </h2>
        </template>

        <div :style="{ backgroundColor: mainColor }" class="relative h-max z-0 mx-auto py-6 px-4 sm:px-10 lg:px-16">
            <InfoProfile 
                :user="user"
                :favoriteProjects="favoriteProjects"
                :myProjects="myProjects"
            />
        </div>
    </AuthenticatedLayout>
</template>