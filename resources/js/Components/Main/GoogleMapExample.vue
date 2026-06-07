<script setup>
import { GoogleMap, Marker, CustomMarker, Polygon } from "vue3-google-map";
import { ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import defaultImage from "../../../images/LogoInvestProject.png";
import chelyabinskOblastGeoJson from "../../../../public/geo/chelyabinsk-oblast.json";
import { MapPin, X, TrendingUp, Users, Briefcase } from 'lucide-vue-next';

// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА (из глобальных переменных) =====
const colors = {
    brand: 'var(--color-brand)',
    brandDark: 'var(--color-brand-dark)',
    accent: 'var(--color-accent)',
    focus: 'var(--color-focus)',
    hover: 'var(--color-hover)',
    page: 'var(--color-page)',
    surface: 'var(--color-surface)',
    light: 'var(--color-light)',
    muted: 'var(--color-muted)',
    text: 'var(--color-text)',
    textMuted: 'var(--color-text-muted)',
    textSoft: 'var(--color-text-soft)',
    border: 'var(--color-border)',
    divider: 'var(--color-divider)',
    error: 'var(--color-error)',
    success: 'var(--color-success)',
    info: 'var(--color-info)',
    white: 'var(--color-white)',
    white80: 'rgba(255, 255, 255, 0.8)',
    white60: 'rgba(255, 255, 255, 0.6)',
    red: 'var(--color-error)',
    redDark: '#b91c1c',
};

const fonts = {
    brand: 'var(--font-brand)',
    heading: 'var(--font-heading)',
    body: 'var(--font-body)',
};

const fontSizes = {
    xs: 'var(--text-base)',
    sm: 'var(--text-base)',
    base: 'var(--text-base)',
    md: 'var(--text-md)',
    lg: 'var(--text-lg)',
    xl: 'var(--text-xl)',
    '2xl': 'var(--text-2xl)',
    '3xl': 'var(--text-3xl)',
    '4xl': 'var(--text-4xl)',
};

const shadows = {
    sm: 'var(--shadow-sm)',
    md: 'var(--shadow-md)',
    lg: 'var(--shadow-lg)',
    xl: 'var(--shadow-xl)',
};

const transitions = {
    fast: 'var(--transition-fast)',
    normal: 'var(--transition-normal)',
    slow: 'var(--transition-slow)',
};

// Вспомогательная функция для получения цвета
const getColor = (colorName) => colors[colorName] || colorName;

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

const api = import.meta.env.VITE_GOOGLE_MAPS_API_KEY || "";

const selectedProjectId = ref(null);
const mapZoom = ref(7);
const imageLoading = ref(true);
const imageError = ref(false);

const regionPolygon = ref(null);

onMounted(() => {
    if (chelyabinskOblastGeoJson && chelyabinskOblastGeoJson.features) {
        const feature = chelyabinskOblastGeoJson.features[0];
        if (feature && feature.geometry) {
            let coordinates = feature.geometry.coordinates;
            if (coordinates[0] && Array.isArray(coordinates[0][0])) coordinates = coordinates[0];
            else if (coordinates[0] && Array.isArray(coordinates[0])) coordinates = coordinates[0];
            regionPolygon.value = coordinates.map(coord => ({ lat: coord[1], lng: coord[0] }));
        }
    }
});

const polygonOptions = {
    strokeColor: colors.accent,
    strokeOpacity: 0.8,
    strokeWeight: 3,
    fillColor: colors.accent,
    fillOpacity: 0.1,
    clickable: false,
    editable: false,
    zIndex: 1,
};

const hasPhotos = (project) => {
    return project.photos && project.photos.length > 0;
};

const getProjectImage = (project) => {
    if (hasPhotos(project)) {
        const photoPath = project.photos[0].photo_path;
        if (photoPath && photoPath.startsWith('/')) return photoPath;
        return `/storage/${photoPath}`;
    }
    return defaultImage;
};

const handleImageLoad = () => { imageLoading.value = false; };
const handleImageError = (event) => {
    imageError.value = true;
    imageLoading.value = false;
    event.target.src = defaultImage;
};

const projectMarkers = computed(() => {
    return (props.projects || [])
        .filter((project) => project.latitude && project.longitude)
        .map((project) => ({ ...project, position: { lat: parseFloat(project.latitude), lng: parseFloat(project.longitude) } }));
});

const selectedProject = computed(() => {
    if (!selectedProjectId.value) return null;
    return projectMarkers.value.find((p) => p.id === selectedProjectId.value) || null;
});

const mapCenter = computed(() => ({ lat: 55.160278, lng: 61.402457 }));

const selectProject = (project) => { selectedProjectId.value = selectedProjectId.value === project.id ? null : project.id; };
const closePopup = () => { selectedProjectId.value = null; };
const goToProject = (id) => { router.visit(route("projects.show", id)); };

const formatNumber = (number) => {
    if (!number) return "0";
    return Number(number).toLocaleString("ru-RU");
};

const buildRedPinIcon = (active = false) => {
    const size = active ? 44 : 40;
    const fill = colors.accent;
    const stroke = colors.redDark;
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 64 64">
        <path d="M32 4c-11.6 0-21 9.4-21 21 0 15.2 21 35 21 35s21-19.8 21-35c0-11.6-9.4-21-21-21z" fill="red" stroke="black" stroke-width="2.5"/>
        <circle cx="32" cy="25" r="9.2" fill="#fff"/>
    </svg>`;
    return { url: `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`, scaledSize: { width: size, height: size }, anchor: { x: size / 2, y: size - 2 } };
};

const progress = (project) => {
    const collected = Number(project.collected_total_investment) || 0;
    const total = Number(project.total_investment) || 1;
    return (collected / total) * 100;
};
</script>

<template>
    <div 
        class="mx-auto h-[90vh] rounded-xl overflow-hidden border-2 shadow-lg" 
        :style="{ borderColor: colors.accent, boxShadow: shadows.lg }"
        role="application" 
        aria-label="Интерактивная карта с маркерами проектов и границей Челябинской области"
    >
        <GoogleMap
            :api-key="api"
            class="w-full h-full"
            :center="mapCenter"
            :zoom="mapZoom"
            @click="closePopup"
            aria-label="Карта Google Maps"
            :styles="[{ featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] }]"
        >
            <Polygon v-if="regionPolygon && regionPolygon.length > 0" :options="{ paths: regionPolygon, ...polygonOptions }" />

            <Marker 
                v-for="project in projectMarkers" 
                :key="project.id" 
                :options="{ position: project.position, title: project.title, icon: buildRedPinIcon(selectedProjectId === project.id), zIndex: selectedProjectId === project.id ? 999 : 1 }" 
                @click="selectProject(project)" 
            />

            <CustomMarker v-if="selectedProject" :options="{ position: selectedProject.position, anchorPoint: 'TOP_CENTER' }">
                <div class="relative mt-3">
                    <div 
                        class="w-80 rounded-2xl overflow-hidden shadow-2xl border-2"
                        :style="{ borderColor: colors.accent, backgroundColor: colors.brand }"
                    >
                        <div class="relative h-36 bg-gradient-to-br" :style="{ backgroundImage: `linear-gradient(to bottom right, ${colors.hover}30, ${colors.hover}10)` }">
                            <div v-if="imageLoading && !imageError && hasPhotos(selectedProject)" class="absolute inset-0 flex items-center justify-center z-10" :style="{ backgroundColor: colors.brand + '80' }">
                                <svg class="animate-spin w-6 h-6" :style="{ color: colors.accent }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <img 
                                v-if="hasPhotos(selectedProject) && !imageError"
                                :src="getProjectImage(selectedProject)" 
                                :alt="`Изображение проекта ${selectedProject.title}`"
                                class="w-full h-full object-cover"
                                @load="handleImageLoad"
                                @error="handleImageError"
                            >
                            <div v-if="!hasPhotos(selectedProject) || imageError" class="absolute inset-0 flex items-center justify-center" :style="{ backgroundColor: colors.hover }">
                                <img :src="defaultImage" alt="Логотип" class="w-[60%] h-[60%] object-contain opacity-60">
                            </div>
                            <button 
                                class="absolute top-2 right-2 w-7 h-7 rounded-full flex items-center justify-center backdrop-blur-sm transition-all duration-200 hover:scale-105" 
                                :style="{ backgroundColor: colors.brand + '99', color: colors.white }"
                                @click.stop="closePopup" 
                                aria-label="Закрыть карточку проекта"
                            >
                                <X class="w-3.5 h-3.5" />
                            </button>
                        </div>
                        
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h3 class="text-base font-bold leading-tight line-clamp-2 flex-1" :style="{ fontFamily: fonts.heading, color: colors.white }">
                                    {{ selectedProject.title }}
                                </h3>
                            </div>
                            
                            <p class="text-base mb-3 line-clamp-2" :style="{ color: colors.white80 }">
                                {{ selectedProject.short_description?.slice(0, 80) || "Описание проекта отсутствует" }}
                            </p>
                            
                            <div class="mb-3">
                                <div class="flex justify-between text-base mb-1">
                                    <span :style="{ color: colors.white80 }">Собрано средств</span>
                                    <span class="font-semibold" :style="{ color: colors.white }">{{ Math.min(progress(selectedProject), 100).toFixed(0) }}%</span>
                                </div>
                                <div class="w-full h-1.5 rounded-full overflow-hidden" :style="{ backgroundColor: colors.page }">
                                    <div class="h-full rounded-full transition-all" :style="{ width: `${Math.min(progress(selectedProject), 100)}%`, backgroundColor: colors.accent }"></div>
                                </div>
                            </div>
                            
                            <div class="space-y-2 text-base mb-3">
                                <div v-if="selectedProject.total_investment" class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5" :style="{ color: colors.white80 }">
                                        <TrendingUp class="w-3.5 h-3.5" />
                                        <span>Инвестиции:</span>
                                    </div>
                                    <span class="font-semibold" :style="{ color: colors.white }">{{ formatNumber(selectedProject.total_investment) }} ₽</span>
                                </div>
                                <div v-if="selectedProject.count_new_job" class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5" :style="{ color: colors.white80 }">
                                        <Users class="w-3.5 h-3.5" />
                                        <span>Рабочих мест:</span>
                                    </div>
                                    <span class="font-semibold" :style="{ color: colors.white }">{{ selectedProject.count_new_job }}</span>
                                </div>
                                <div v-if="selectedProject.address" class="flex items-start gap-1.5">
                                    <MapPin class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" :style="{ color: colors.white }" />
                                    <span class="text-base leading-relaxed line-clamp-2" :style="{ color: colors.white80 }">{{ selectedProject.address }}</span>
                                </div>
                                <div v-if="selectedProject.activity" class="flex items-center gap-1.5">
                                    <Briefcase class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: colors.white }" />
                                    <span class="text-base" :style="{ color: colors.white80 }">{{ selectedProject.activity }}</span>
                                </div>
                            </div>
                            
                            <button 
                                class="w-full py-2 rounded-lg font-semibold text-base transition-all duration-200 hover:opacity-90" 
                                :style="{ backgroundColor: colors.accent, color: colors.white }"
                                @click="goToProject(selectedProject.id)" 
                                :aria-label="`Подробнее о проекте ${selectedProject.title}`"
                            >
                                Подробнее →
                            </button>
                        </div>
                    </div>
                </div>
            </CustomMarker>
        </GoogleMap>
    </div>
</template>