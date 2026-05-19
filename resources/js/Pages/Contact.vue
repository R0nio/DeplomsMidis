<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";       
import ContactsInfo from "@/Components/Main/ContactsInfo.vue";
import Vk from "../../images/VK com.png";

const phonesInfo = [
    {number: "+7(999)999-99-99"},
    {number: "+7(999)999-99-98"},
]
const emails = [
    {pochta: "Example1@invesProject.ya"},
    {pochta: "Example2@invesProject.ya"},
]
const socials = [
    {name: "Cheliabinsk_Project_Invest", img: Vk}
]
</script>

<template>
    <Head title="Contact" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-3xl font-semibold leading-tight text-gray-800">
                Контакты
            </h2>
            <ContactsInfo class="mb-[120px]" :phonesdata="phonesInfo" :emails="emails" :socials="socials"></ContactsInfo>
        </template>

    </AuthenticatedLayout>
</template>
