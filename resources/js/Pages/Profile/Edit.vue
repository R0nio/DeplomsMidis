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

const mainColor = "#8EB6FF";
</script>

<template>
    <Head title="Личный кабинет — InvestProject" />

    <AuthenticatedLayout>
        <template #header :style="{backgroundColor: mainColor}">
            <h1 :style="{backgroundColor: mainColor}" class="text-3xl font-semibold leading-tight text-gray-900">
                Личный кабинет
            </h1>
        </template>

        <div :style="{backgroundColor: mainColor}" class="relative h-max z-0 mx-auto py-6 px-4 sm:px-10 lg:px-16">
            <InfoProfile 
                :user="user"
                :favoriteProjects="favoriteProjects"
                :myProjects="myProjects"
            />
        </div>
    </AuthenticatedLayout>
</template>