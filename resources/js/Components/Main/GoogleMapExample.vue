<script setup>
import { GoogleMap, Marker, InfoWindow } from "vue3-google-map";
import { ref, computed } from "vue";
import { router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import defaultImage from "../../../images/LogoInvestProject.png";

const page = usePage();
const props = defineProps({
    projects: {
        type: Array,
        required: true
    },
    center: {
        type: Object,
        default: () => ({ lat: 55.160278, lng: 61.402457 })
    }
});

const colors = {
    bgDark: '#284139',
    bgLight: '#809076',
    accent: '#F8D794',
    border: '#886830',
    cardBg: '#4A7A6A',
    buttonBg: '#809076',
    white: '#ffffff',
    white80: 'rgba(255, 255, 255, 0.8)',
};

const api = "AIzaSyDfKb4UVvQwK3cANRf-7EVCLlGy0fi2yno";
const mainColor = "#436343";

const selectedProject = ref(null);

const getProjectImage = (project) => {
    if (project.photos && project.photos.length > 0) {
        const photoPath = project.photos[0].photo_path;
        return `/storage/${photoPath}`;
    }
    return defaultImage;
};

// Маркеры проектов
const projectMarkers = computed(() => {
    if (!props.projects || props.projects.length === 0) {
        return [];
    }
    
    return props.projects
        .filter(project => project.latitude && project.longitude)
        .map(project => ({
            id: project.id,
            position: {
                lat: parseFloat(project.latitude),
                lng: parseFloat(project.longitude)
            },
            title: project.title,
            image: getProjectImage(project),
            shortDescription: project.short_description,
            totalInvestment: project.total_investment,
            status: project.status,
            address: project.address
        }));
});

// Центр карты
const mapCenter = computed(() => {
    if (props.projects && props.projects.length > 0) {
        const firstProject = props.projects.find(p => p.latitude && p.longitude);
        if (firstProject) {
            return {
                lat: parseFloat(firstProject.latitude),
                lng: parseFloat(firstProject.longitude)
            };
        }
    }
    return { lat: 55.160278, lng: 61.402457 };
});

// Выбор проекта
const selectProject = (markerProject) => {
    const fullProject = props.projects.find(p => p.id === markerProject.id);
    
    if (selectedProject.value?.id === markerProject.id) {
        selectedProject.value = null;
        setTimeout(() => {
            selectedProject.value = fullProject;
        }, 0);
    } else {
        selectedProject.value = fullProject;
    }
};

const goToProject = (id) => {
    router.visit(route('projects.show', id));
};

const closeMarker = () => {
    selectedProject.value = null;
};

const formatNumber = (number) => {
    if (!number) return '0';
    return Number(number).toLocaleString('ru-RU');
};
</script>

<template>
    <div 
        class="mx-auto py-6 px-4 sm:px-10 lg:px-16 h-[1000px]" 
        :style="{ backgroundColor: mainColor }"
        role="application"
        aria-label="Интерактивная карта с маркерами проектов"
    >
        <div 
            class="w-full h-[900px] rounded-xl overflow-hidden"
            :style="{ border: `2px solid ${colors.border}` }"
        >
            <GoogleMap
                :api-key="api"
                class="w-full h-full"
                :center="mapCenter"
                :zoom="12"
                @click="closeMarker"
                aria-label="Карта Google Maps"
            >
                <!-- Маркеры проектов -->
                <Marker
                    v-for="marker in projectMarkers"
                    :key="marker.id"
                    :options="{ 
                        position: marker.position,
                        title: marker.title
                    }"
                    @click="selectProject(marker)"
                    :aria-label="`Маркер проекта ${marker.title}`"
                />

                <!-- Информационное окно -->
                <InfoWindow
                    v-if="selectedProject"
                    :options="{ 
                        position: { 
                            lat: parseFloat(selectedProject.latitude), 
                            lng: parseFloat(selectedProject.longitude) 
                        } 
                    }"
                >
                    <div
                        class="w-64 sm:w-72 rounded-xl overflow-hidden cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#F8D794]"
                        :style="{ backgroundColor: colors.cardBg, border: `2px solid ${colors.border}` }"
                        @click="goToProject(selectedProject.id)"
                        @keydown.enter="goToProject(selectedProject.id)"
                        @keydown.space.prevent="goToProject(selectedProject.id)"
                        role="button"
                        tabindex="0"
                        :aria-label="`Открыть страницу проекта ${selectedProject.title}`"
                    >
                        <!-- Изображение -->
                        <div class="relative h-40" :style="{ backgroundColor: '#4a7a6a' }">
                            <img
                                v-if="selectedProject.photos && selectedProject.photos.length > 0"
                                :src="getProjectImage(selectedProject)"
                                :alt="`Изображение проекта ${selectedProject.title}`"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            />
                            <div v-else class="w-full h-full flex items-center justify-center" :style="{ backgroundColor: colors.border }">
                                <img :src="defaultImage" class="w-[100px]" alt="Изображение не загружено">
                            </div>
                        </div>

                        <!-- Информация -->
                        <div class="p-4">
                            <h3 class="text-lg font-bold mb-1" :style="{ color: colors.accent }">
                                {{ selectedProject.title }}
                            </h3>
                            
                            <p class="text-sm mb-2 line-clamp-2" :style="{ color: colors.white80 }">
                                {{ selectedProject.short_description?.slice(0, 80) }}...
                            </p>

                            <!-- Детали -->
                            <div class="space-y-2 text-sm mb-3">
                                <div v-if="selectedProject.total_investment" class="flex items-center">
                                    <span class="font-semibold" :style="{ color: colors.accent }">{{ formatNumber(selectedProject.total_investment) }} ₽</span>
                                    <span class="sr-only">сумма инвестиций</span>
                                </div>

                                <div v-if="selectedProject.address" class="flex items-start">
                                    <span class="text-xs line-clamp-2" :style="{ color: colors.white80 }">{{ selectedProject.address }}</span>
                                    <span class="sr-only">адрес</span>
                                </div>
                            </div>

                            <!-- Кнопка -->
                            <div class="pt-3" :style="{ borderTop: `1px solid ${colors.border}` }">
                                <button 
                                    class="w-full font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-[#F8D794]"
                                    :style="{ backgroundColor: colors.buttonBg, color: colors.accent }"
                                    :aria-label="`Подробнее о проекте ${selectedProject.title}`"
                                >
                                    <span>Подробнее</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </InfoWindow>
            </GoogleMap>
        </div>
    </div>
</template>
