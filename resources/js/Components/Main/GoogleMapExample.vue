<script setup>
import { GoogleMap, Marker, CustomMarker, Polygon } from "vue3-google-map";
import { ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import defaultImage from "../../../images/LogoInvestProject.png";
import chelyabinskOblastGeoJson from "../../../../public/geo/chelyabinsk-oblast.json";

const props = defineProps({
    projects: {
        type: Array,
        required: true,
    },
    center: {
        type: Object,
        default: () => ({ lat: 55.160278, lng: 61.402457 }),
    },
});

const colors = {
    bgDark: "#1f2937",
    cardBg: "#243a33",
    cardBgSoft: "#2f4d43",
    accent: "#f8d794",
    border: "#7a5d2d",
    white: "#ffffff",
    white80: "rgba(255,255,255,0.82)",
    red: "#ef4444",
    redDark: "#b91c1c",
};
// Полигон (граница области)
const polygonOptions = {
    strokeColor: "#B22222",
    strokeOpacity: 0.8,
    strokeWeight: 3,
    fillColor: "#008000",
    fillOpacity: 0.15,
    clickable: false,
    editable: false,
    zIndex: 1,
};
const api = import.meta.env.VITE_GOOGLE_MAPS_API_KEY || "";
const mainColor = "#436343";

const selectedProjectId = ref(null);
const mapZoom = ref(7);

// Полигон границы Челябинской области
const regionPolygon = ref(null);

// Загрузка полигона из GeoJSON
onMounted(() => {
    if (chelyabinskOblastGeoJson && chelyabinskOblastGeoJson.features) {
        const feature = chelyabinskOblastGeoJson.features[0];
        if (feature && feature.geometry) {
            // Извлекаем координаты полигона
            let coordinates = feature.geometry.coordinates;
            
            // GeoJSON может быть вложенным массивом
            if (coordinates[0] && Array.isArray(coordinates[0][0])) {
                coordinates = coordinates[0];
            } else if (coordinates[0] && Array.isArray(coordinates[0])) {
                coordinates = coordinates[0];
            }
            
            // Преобразуем координаты из [lng, lat] в {lat, lng} для Google Maps
            regionPolygon.value = coordinates.map(coord => ({
                lat: coord[1],
                lng: coord[0]
            }));
            
            console.log('Polygon loaded, points count:', regionPolygon.value.length);
        }
    }
});

const getProjectImage = (project) => {
    if (project.photos && project.photos.length > 0) {
        return `/storage/${project.photos[0].photo_path}`;
    }
    return defaultImage;
};

const projectMarkers = computed(() => {
    return (props.projects || [])
        .filter((project) => project.latitude && project.longitude)
        .map((project) => ({
            ...project,
            position: {
                lat: parseFloat(project.latitude),
                lng: parseFloat(project.longitude),
            },
        }));
});

const selectedProject = computed(() => {
    if (!selectedProjectId.value) return null;
    return projectMarkers.value.find((p) => p.id === selectedProjectId.value) || null;
});

// Центр карты - всегда Челябинск (не зависит от проектов)
const mapCenter = computed(() => {
    return { lat: 55.160278, lng: 61.402457 };
});

const selectProject = (project) => {
    selectedProjectId.value = selectedProjectId.value === project.id ? null : project.id;
};

const closePopup = () => {
    selectedProjectId.value = null;
};

const goToProject = (id) => {
    router.visit(route("projects.show", id));
};

const formatNumber = (number) => {
    if (!number) return "0";
    return Number(number).toLocaleString("ru-RU");
};

const buildRedPinIcon = (active = false) => {
    const size = active ? 44 : 40;
    const fill = "#ef4444";
    const stroke = "#b91c1c";

    const svg = `
<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 64 64">
  <path
    d="M32 4c-11.6 0-21 9.4-21 21 0 15.2 21 35 21 35s21-19.8 21-35c0-11.6-9.4-21-21-21z"
    fill="${fill}"
    stroke="${stroke}"
    stroke-width="2.5"
  />
  <circle cx="32" cy="25" r="9.2" fill="#fff" />
</svg>`;

    return {
        url: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`,
        scaledSize: { width: size, height: size },
        anchor: { x: size / 2, y: size - 2 },
    };
};


</script>

<template>
    <div
        class="mx-auto h-[90vh]"
        :style="{ backgroundColor: mainColor }"
        role="application"
        aria-label="Интерактивная карта с маркерами проектов и границей Челябинской области"
    >
        <div class="w-full h-[90vh] rounded-xl overflow-hidden border-2" :style="{ borderColor: colors.border }">
            <GoogleMap
                :api-key="api"
                class="w-full h-full"
                :center="mapCenter"
                :zoom="mapZoom"
                @click="closePopup"
                aria-label="Карта Google Maps"
                :styles="[
                    {
                        featureType: 'poi',
                        elementType: 'labels',
                        stylers: [{ visibility: 'off' }]
                    }
                ]"
            >
                <!-- Полигон границы Челябинской области -->
                <Polygon
                    v-if="regionPolygon && regionPolygon.length > 0"
                    :options="{
                        paths: regionPolygon,
                        ...polygonOptions
                    }"
                />

                <Marker
                    v-for="project in projectMarkers"
                    :key="project.id"
                    :options="{
                        position: project.position,
                        title: project.title,
                        icon: buildRedPinIcon(selectedProjectId === project.id),
                        zIndex: selectedProjectId === project.id ? 999 : 1
                    }"
                    @click="selectProject(project)"
                />

                <!-- Полностью кастомный popup без белой обводки -->
                <CustomMarker
                    v-if="selectedProject"
                        :options="{
                            position: selectedProject.position,
                            anchorPoint: 'TOP_CENTER'
                        }"
                    >
                    <div class="relative mt-3">
                        <div
                            class="w-72 rounded-2xl overflow-hidden shadow-2xl border"
                            :style="{ backgroundColor: colors.cardBg, borderColor: colors.border }"
                            @click.stop
                        >
                            <div class="relative h-40" :style="{ backgroundColor: colors.cardBgSoft }">
                                <img
                                    v-if="selectedProject.photos?.length"
                                    :src="getProjectImage(selectedProject)"
                                    :alt="`Изображение проекта ${selectedProject.title}`"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                />
                                <div
                                    v-else
                                    class="w-full h-full flex items-center justify-center"
                                    :style="{ backgroundColor: '#3f5f54' }"
                                >
                                    <img :src="defaultImage" class="w-24 opacity-90" alt="Изображение не загружено" />
                                </div>

                                <button
                                    class="absolute top-2 right-2 w-8 h-8 rounded-full bg-black/40 text-white hover:bg-black/55 transition-all duration-200 hover:scale-105 flex items-center justify-center"
                                    @click.stop="closePopup"
                                    aria-label="Закрыть карточку проекта"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="p-4">
                                <h3 class="text-lg font-bold mb-1 leading-tight line-clamp-2" :style="{ color: colors.accent }">
                                    {{ selectedProject.title }}
                                </h3>

                                <p class="text-sm mb-3 line-clamp-2" :style="{ color: colors.white80 }">
                                    {{ selectedProject.short_description?.slice(0, 95) || "Описание проекта отсутствует" }}
                                </p>

                                <div class="space-y-1.5 text-sm mb-3">
                                    <div v-if="selectedProject.total_investment" class="flex items-center justify-between">
                                        <span class="text-xs" :style="{ color: colors.white80 }">Инвестиции:</span>
                                        <span class="font-semibold" :style="{ color: colors.accent }">{{ formatNumber(selectedProject.total_investment) }} ₽</span>
                                    </div>
                                    <div v-if="selectedProject.address" class="flex items-start gap-1">
                                        <svg class="w-3 h-3 mt-0.5 flex-shrink-0" :style="{ color: colors.accent }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="text-xs line-clamp-2" :style="{ color: colors.white80 }">{{ selectedProject.address }}</span>
                                    </div>
                                    <div v-if="selectedProject.activity" class="flex items-center gap-1">
                                        <svg class="w-3 h-3 flex-shrink-0" :style="{ color: colors.accent }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs" :style="{ color: colors.white80 }">{{ selectedProject.activity }}</span>
                                    </div>
                                </div>

                                <button
                                    class="w-full py-2.5 rounded-lg font-semibold transition-all duration-200 hover:opacity-90 hover:scale-[1.02]"
                                    :style="{ backgroundColor: '#35594d', color: colors.accent }"
                                    @click="goToProject(selectedProject.id)"
                                    :aria-label="`Подробнее о проекте ${selectedProject.title}`"
                                >
                                    Подробнее
                                </button>
                            </div>
                        </div>
                    </div>
                </CustomMarker>
            </GoogleMap>
        </div>
    </div>
</template>