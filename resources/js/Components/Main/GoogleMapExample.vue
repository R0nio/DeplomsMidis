<script setup>
import { GoogleMap, Marker, InfoWindow } from "vue3-google-map";
import { ref, computed } from "vue";
import { router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import defaultImage from "../../../images/free-icon-image-gallery-3342137.png";


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

const api = "AIzaSyC6L3uzc8_Ug01uxrFdYhN_Eo5mIwyaPZY";
const mainColor = "#8EB6FF";

const selectedProject = ref(null);

const getProjectImage = (project) => {
    if (project.photos && project.photos.length > 0) {
        const photoPath = project.photos[0].photo_path;
        console.log('Photo path:', photoPath);
        return `/storage/${photoPath}`;
    }
    return defaultImage;
};

// Маркеры проектов
const projectMarkers = computed(() => {
    return props.projects.map(project => ({
        id: project.id,
        position: {
            lat: parseFloat(project.latitude),
            lng: parseFloat(project.longitude)
        },
        title: project.title,
        image: getProjectImage(project),
        shortDescription: project.short_description,
        totalInvestment: project.total_investment,
        status: project.status
    }));
});

// Выбор проекта
const selectProject = (project) => {
    const fullProject = props.projects.find(p => p.id === project.id);
    
    if (selectedProject.value?.id === project.id) {
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

// Закрыть маркер
const closeMarker = () => {
    selectedProject.value = null;
};


const formatNumber = (number) => {
    return Number(number).toLocaleString('ru-RU');
};


const projectImage = computed(() => {
    if (props.selectedProject.photos && props.selectedProject.photos.length > 0) {
        return `/storage/${props.selectedProject.photos[0].photo_path}`;
    }
    return slider3;
});
</script>

<template>
    <div :style="{backgroundColor: mainColor}" class="mx-auto py-6 px-4 sm:px-10 lg:px-16">
        <GoogleMap
            :api-key="api"
            class="w-full h-[600px] lg:h-[800px]  shadow-2xl border-2 border-white" 
            :center="center"
            :zoom="12"
            @click="closeMarker"
        >
            <!-- Маркеры проектов -->
            <Marker
            class="bg-[#8EB6FF]"
                v-for="marker in projectMarkers"
                :key="marker.id"
                :options="{ 
                    position: marker.position,
                    title: marker.title,

                }"
                @click="selectProject(marker)"
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
                    class="w-64 sm:w-72 bg-[#8EB6FF] rounded-lg overflow-hidden shadow-xl cursor-pointer hover:shadow-2xl transition-shadow duration-300 p-3"
                    @click="goToProject(selectedProject.id)"
                >
                    <!-- Изображение -->
                    <div class="relative h-40">
                        <img
                            v-if="selectedProject.photos && selectedProject.photos.length > 0"
                            :src="getProjectImage(selectedProject)"
                            :alt="selectedProject.title"
                            class="w-full h-full object-cover rounded-xl"
                        />
                        <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                              <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                              </svg>
                        </div>

                        <!-- Статус
                        <div class="absolute top-2 left-2">
                            <span 
                                class="px-3 py-1 text-xs font-semibold text-white rounded-lg shadow-md"
                                :style="{ backgroundColor: '#7390C5' }"
                            >
                                {{ selectedProject.status }}
                            </span>
                        </div> -->
                    </div>

                    <!-- Информация -->
                    <div class="p-4">
                        <h3 class="text-lg font-bold text-white mb-1">
                            {{ selectedProject.title }}
                        </h3>
                        
                        <p class="text-sm text-white mb-1">
                            {{ selectedProject.short_description }}
                        </p>

                        <!-- Детали -->
                        <div class="space-y-2 text-sm mb-3">
                            <div v-if="selectedProject.total_investment" class="flex items-center text-white">
                                <span class="font-semibold">{{ formatNumber(selectedProject.total_investment) }} ₽</span>
                            </div>

                            <div v-if="selectedProject.address" class="flex items-start text-white">
                                <span class="text-xs line-clamp-2">{{ selectedProject.address }}</span>
                            </div>
                        </div>

                        <!-- Кнопка -->
                        <div class="pt-3 border-t-2 border-white">
                            <button class="w-full bg-[#7390C5] hover:bg-[#267FBE] hover:text-blac text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                Подробнее
                            </button>
                        </div>
                    </div>
                </div>
            </InfoWindow>
        </GoogleMap>
    </div>
</template>