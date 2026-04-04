<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head} from "@inertiajs/vue3";
import GoogleMapExample from "@/Components/Main/GoogleMapExample.vue";


const mainColor = "#C9D8EF";
const footerColor = "#A9BCDB";



</script>

<template>
  <Head title="Карта проектов" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">
        Карта проектов
      </h2>
    </template>
    <div :class="`mx-auto py-6 px-4 sm:px-10 lg:px-16 bg-[${mainColor}] bg-[#C9D8EF]`">
      <GoogleMapExample></GoogleMapExample>
    </div>
  </AuthenticatedLayout>
</template>