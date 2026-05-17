<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head} from "@inertiajs/vue3";
import GoogleMapExample from "@/Components/Main/GoogleMapExample.vue";


const mainColor = "#8EB6FF";



</script>

<template>
  <Head title="Карта проектов" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800">
        Карта проектов
      </h2>
    </template>
    <div :style="{backgroundColor: mainColor}" class="mx-auto py-6 px-4 sm:px-10 lg:px-16">
      <GoogleMapExample></GoogleMapExample>
    </div>
  </AuthenticatedLayout>
</template>