<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import InfoProfile from '@/Components/Main/InfoProfile.vue';
import TitlePage from '@/Layouts/TitlePage.vue';

// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    page: 'var(--color-page)',
};

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
</script>

<template>
    <Head title="Профиль">
        <meta name="description" content="Личный кабинет пользователя">
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <TitlePage value="Личный кабинет" />
        </template>

        <div class="min-h-screen" :style="{ backgroundColor: colors.page }">
            <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16">
                <InfoProfile 
                    :user="user"
                    :favoriteProjects="favoriteProjects"
                    :myProjects="myProjects"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>