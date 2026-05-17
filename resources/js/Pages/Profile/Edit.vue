<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import InfoProfile from '@/Components/Main/InfoProfile.vue';

const user = usePage().props.auth.user;

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const mainColor = "#8EB6FF";
</script>


<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header :style="{backgroundColor: mainColor}">
            <h2 :style="{backgroundColor: mainColor}" class="text-3xl font-semibold leading-tight text-gray-800"
            >
                Личный кабинет
            </h2>
        </template>

        <div :style="{backgroundColor: mainColor}" class="relative h-max z-0 mx-auto py-6 px-4 sm:px-10 lg:px-16">
            <InfoProfile :user="user"></InfoProfile>
        </div>
        
        <!-- <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div> -->
    </AuthenticatedLayout>
</template>
