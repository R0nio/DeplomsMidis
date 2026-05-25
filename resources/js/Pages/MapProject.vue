<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import GoogleMapExample from "@/Components/Main/GoogleMapExample.vue";
import { computed } from 'vue';
import TitlePage from "@/Layouts/TitlePage.vue";

const props = defineProps({
    projects: {
        type: Array,
        default: () => []
    }
});

// ========== ЦВЕТОВАЯ СХЕМА ==========
const mainColor = "#436343";

// Центр карты (средняя точка всех проектов или дефолт)
const mapCenter = computed(() => {
    if (props.projects.length === 0) {
        return { lat: 55.160278, lng: 61.402457 }; // Челябинск по умолчанию
    }
    
    const avgLat = props.projects.reduce((sum, p) => sum + parseFloat(p.latitude), 0) / props.projects.length;
    const avgLng = props.projects.reduce((sum, p) => sum + parseFloat(p.longitude), 0) / props.projects.length;
    
    return { lat: avgLat, lng: avgLng };
});

// Подсчет проектов
const projectsCount = computed(() => props.projects.length);
</script>

<template>
    <Head title="Карта проектов" />

    <AuthenticatedLayout>
        <template #header>
            <TitlePage id="Main" value="Карта проектов"></TitlePage>
        </template>

        <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16" :style="{ backgroundColor: mainColor }">
            <!-- Карта -->
            <GoogleMapExample 
                :projects="projects"
                :center="mapCenter"
            />
        </div>
    </AuthenticatedLayout>
</template>