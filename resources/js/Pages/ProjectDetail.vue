<script setup>
import { ref, computed, reactive, watch, onMounted } from 'vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, router } from "@inertiajs/vue3";
import { GoogleMap, Marker, CustomMarker, Polygon } from "vue3-google-map";
import SliderInDetailPage from "@/Components/Main/SliderInDetailPage.vue";
import axios from 'axios';
import chelyabinskOblastGeoJson from "../../../public/geo/chelyabinsk-oblast.json";
import { MapPin, Calendar, RussianRuble, Users, Building, Hammer, Briefcase,
         Mail, Phone, TrendingUp, ArrowLeft, X, Edit2 } from 'lucide-vue-next';
// Images
import sliderFallback from "../../images/LogoInvestProject.png";
import FavoriteIcon from "../../images/Favorite.png";
import FavoriteActiveIcon from "../../images/FavoriteActivity.png";
import defaultImage from "../../images/LogoInvestProject.png";

// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
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
    white70: 'rgba(255, 255, 255, 0.7)',
    red: 'var(--color-error)',
    redDark: '#b91c1c',
    green: 'var(--color-success)',
};

const fonts = {
    brand: 'var(--font-brand)',
    heading: 'var(--font-heading)',
    body: 'var(--font-body)',
};

const fontSizes = {
    xs: 'var(--text-xs)',
    sm: 'var(--text-sm)',
    base: 'var(--text-base)',
    md: 'var(--text-md)',
    lg: 'var(--text-xl)',
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

// Состояния для скринридера
const srAnnouncement = ref('');

const imageLoading = ref(true);
const imageError = ref(false);

const hasPhotos = computed(() => {
    return props.project.photos && props.project.photos.length > 0;
});

const handleImageLoad = () => {
    imageLoading.value = false;
};

const handleImageError = (event) => {
    imageError.value = true;
    imageLoading.value = false;
    event.target.src = defaultImage;
};

// Опции полигона
const polygonOptions = {
    strokeColor: colors.accent,
    strokeOpacity: 0.8,
    strokeWeight: 3,
    fillColor: colors.accent,
    fillOpacity: 0.15,
    clickable: false,
    editable: false,
    zIndex: 1,
};

// Props
const props = defineProps({
    project: {
        type: Object,
        required: true
    },
    isFavorited: {
        type: [Boolean, Array],
        default: false
    }
});

// Обновите функцию getProjectImage
const getProjectImage = (project) => {
    if (imageError.value) return defaultImage;
    if (hasPhotos.value) {
        const photoPath = project.photos[0].photo_path;
        if (photoPath && photoPath.startsWith('/')) return photoPath;
        return `/storage/${photoPath}`;
    }
    return defaultImage;
};

// Текущий пользователь
const page = usePage();
const authUser = computed(() => page.props.auth?.user ?? null);
const userRole = computed(() => authUser.value?.role);

// Слайдер
const imagesSlider = computed(() => {
    if (props.project.photos && props.project.photos.length > 0) {
        return props.project.photos.map((photo, index) => ({
            src: `/storage/${photo.photo_path}`,
            alt: `Фото проекта ${index + 1}`,
            id: props.project.id
        }));
    }
    return [];
});

// Форматирование числа
const formatNumber = (number) => {
    if (!number) return '0';
    return Number(number).toLocaleString('ru-RU');
};

// Прогресс сбора средств
const progress = computed(() => {
    const collected = Number(props.project.collected_total_investment) || 0;
    const total = Number(props.project.total_investment) || 1;
    return (collected / total) * 100;
});

// Категории
const categories = computed(() => {
    if (!props.project.category) return [];
    try {
        const cats = Array.isArray(props.project.category)
            ? props.project.category
            : JSON.parse(props.project.category);
        return cats.filter(c => c && c.trim() !== '');
    } catch {
        return [];
    }
});

// Инвестиции (статьи расходов)
const investments = computed(() => {
    if (props.project.investments && props.project.investments.length > 0) {
        return props.project.investments;
    }
    return [];
});

// Прогнозы
const forecasts = computed(() => {
    if (props.project.forecasts && props.project.forecasts.length > 0) {
        return props.project.forecasts;
    }
    return [];
});

// Контактные данные организатора
const organizerContacts = computed(() => {
    if (props.project.user) {
        return {
            name: props.project.user.name,
            middlename: props.project.user.middlename,
            lastname: props.project.user.lastname,
            email: props.project.user.email,
            phone: props.project.user.number,
            organization: props.project.user.organization_name
        };
    }
    return null;
});

// Проверка избранного
const checkIsFavorited = () => {
    if (Array.isArray(props.isFavorited)) {
        return props.isFavorited.some(fav => fav.project_id === props.project.id || fav.id === props.project.id);
    }
    return Boolean(props.isFavorited);
};

const isFavorite = ref(checkIsFavorited());

watch(() => props.isFavorited, () => {
    isFavorite.value = checkIsFavorited();
}, { deep: true, immediate: true });

watch(() => props.project.id, () => {
    isFavorite.value = checkIsFavorited();
});

const toggleFavorite = async (event) => {
    event.preventDefault();
    event.stopPropagation();

    if (!authUser.value) {
        alert('Необходимо авторизоваться');
        srAnnouncement.value = 'Необходимо авторизоваться';
        setTimeout(() => { srAnnouncement.value = ''; }, 3000);
        return;
    }

    const previousState = isFavorite.value;
    isFavorite.value = !isFavorite.value;
    srAnnouncement.value = isFavorite.value ? 'Проект добавлен в избранное' : 'Проект удален из избранного';

    try {
        const response = await axios.post(route('favorites.toggle', props.project.id));
        
        if (response.data.success) {
            isFavorite.value = response.data.isFavorited;
        } else {
            isFavorite.value = previousState;
            alert('Не удалось изменить статус избранного');
        }
    } catch (error) {
        console.error('Ошибка при добавлении в избранное:', error);
        isFavorite.value = previousState;
        
        if (error.response?.status === 401) {
            alert('Необходимо авторизоваться');
        } else {
            alert('Произошла ошибка. Попробуте позже.');
        }
    }
    setTimeout(() => { srAnnouncement.value = ''; }, 3000);
};

// Переход на страницу редактирования
const goToEdit = () => {
    router.visit(route('projects.edit', props.project.id));
};

// ========== ДЛЯ АДМИНКИ: ИЗМЕНЕНИЕ СТАТУСА ==========
const selectedAdminStatus = ref(props.project.status);
const statusOptions = [
    { value: 'На модерации', label: 'На модерации' },
    { value: 'Одобрен', label: 'Одобрен' },
    { value: 'Отклонен', label: 'Отклонен' },
    { value: 'Заблокирован', label: 'Заблокирован' },
];

const changeAdminStatus = async () => {
    if (!confirm('Изменить статус проекта?')) {
        selectedAdminStatus.value = props.project.status;
        return;
    }

    try {
        await axios.patch(route('admin.projects.update-status', props.project.id), {
            status: selectedAdminStatus.value
        });
        srAnnouncement.value = `Статус проекта изменен на "${selectedAdminStatus.value}"`;
        await router.reload();
    } catch (error) {
        console.error('Ошибка при изменении статуса:', error);
        selectedAdminStatus.value = props.project.status;
        alert('Не удалось изменить статус проекта');
    }
    setTimeout(() => { srAnnouncement.value = ''; }, 3000);
};

// Полигон границы Челябинской области
const regionPolygon = ref(null);

// Загрузка полигона из GeoJSON
const loadRegionPolygon = () => {
    if (chelyabinskOblastGeoJson && chelyabinskOblastGeoJson.features) {
        const feature = chelyabinskOblastGeoJson.features[0];
        if (feature && feature.geometry) {
            let coordinates = feature.geometry.coordinates;
            
            if (coordinates[0] && Array.isArray(coordinates[0][0])) {
                coordinates = coordinates[0];
            } else if (coordinates[0] && Array.isArray(coordinates[0])) {
                coordinates = coordinates[0];
            }
            
            regionPolygon.value = coordinates.map(coord => ({
                lat: coord[1],
                lng: coord[0]
            }));
        }
    }
};

// Карта
const api = import.meta.env.VITE_GOOGLE_MAPS_API_KEY || "";
const mapZoom = ref(10);

const hasCoordinates = computed(() => {
    return props.project.latitude && props.project.longitude;
});

// Центр карты - координаты проекта или Челябинск
const mapCenter = computed(() => {
    if (hasCoordinates.value) {
        return {
            lat: parseFloat(props.project.latitude),
            lng: parseFloat(props.project.longitude)
        };
    }
    return { lat: 55.160278, lng: 61.402457 };
});

// Загружаем полигон при монтировании
loadRegionPolygon();

const projectMarker = computed(() => ({
    id: props.project.id,
    position: {
        lat: parseFloat(props.project.latitude),
        lng: parseFloat(props.project.longitude)
    },
    title: props.project.title,
    image: getProjectImage(props.project),
    shortDescription: props.project.short_description,
    totalInvestment: props.project.total_investment,
    status: props.project.status,
    address: props.project.address,
    latitude: props.project.latitude,
    longitude: props.project.longitude
}));

const selectedProjectId = ref(props.project.id);

const selectProject = () => {
    selectedProjectId.value = selectedProjectId.value === props.project.id ? null : props.project.id;
};

const closeMarker = () => {
    selectedProjectId.value = null;
};

const goBack = () => {
    router.visit('/projects');
};

// Красный пин
const buildRedPinIcon = (active = false) => {
    const size = active ? 44 : 40;
    const fill = colors.error;
    const stroke = colors.redDark;

    const svg = `
<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 64 64">
  <path
    d="M32 4c-11.6 0-21 9.4-21 21 0 15.2 21 35 21 35s21-19.8 21-35c0-11.6-9.4-21-21-21z"
    fill="red" stroke="black"
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

onMounted(() => {
    window.scrollTo({
        top: 0,
        behavior: 'instant'
    });
});
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="project.title">
            <meta name="description" :content="project.short_description">
        </Head>
        
        <!-- Область для объявлений скринридера -->
        <div role="status" aria-live="polite" class="sr-only">{{ srAnnouncement }}</div>
        
        <div class="min-h-screen" :style="{ backgroundColor: colors.page }">
            <div class="relative">
                <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16">
        
                    <!-- Кнопки: назад, избранное, редактировать -->
                    <div class="flex justify-between items-center mb-6 mt-6">
                        <button
                            @click="goBack"
                            @keydown.enter="goBack"
                            @keydown.space.prevent="goBack"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2"
                            :style="{ 
                                backgroundColor: colors.brand, 
                                color: colors.white,
                                border: `1px solid ${colors.accent}`,
                                transition: transitions.normal
                            }"
                            @mouseenter="$event.target.style.backgroundColor = colors.hover"
                            @mouseleave="$event.target.style.backgroundColor = colors.brand"
                            :styleFocus="{ ringColor: colors.accent }"
                            aria-label="Вернуться к списку проектов"
                            tabindex="0"
                        >
                            <ArrowLeft class="w-4 h-4" aria-hidden="true" />
                            <span>Назад к проектам</span>
                        </button>

                        <div class="flex gap-3">
                            <!-- Кнопка избранного (для инвестора) -->
                            <div v-if="userRole === 'Investor'">
                                <button 
                                    @click="toggleFavorite"
                                    @keydown.enter="toggleFavorite"
                                    @keydown.space.prevent="toggleFavorite"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2"
                                    :style="{ 
                                        backgroundColor: colors.brand, 
                                        border: `1px solid ${colors.accent}`,
                                        transition: transitions.normal
                                    }"
                                    @mouseenter="$event.target.style.backgroundColor = colors.hover"
                                    @mouseleave="$event.target.style.backgroundColor = colors.brand"
                                    :styleFocus="{ ringColor: colors.accent }"
                                    :aria-label="isFavorite ? 'Удалить из избранного' : 'Добавить в избранное'"
                                    :aria-pressed="isFavorite"
                                    tabindex="0"
                                >
                                    <img 
                                        :src="isFavorite ? FavoriteActiveIcon : FavoriteIcon" 
                                        alt="" 
                                        class="w-5 h-5"
                                        aria-hidden="true"
                                    >
                                    <span :style="{ color: colors.accent }">{{ isFavorite ? 'В избранном' : 'В избранное' }}</span>
                                </button>
                            </div>
                            
                            <!-- Кнопка редактировать (для организатора) -->
                            <div v-if="userRole === 'Organisator' && authUser?.id === project.user?.id">
                                <button 
                                    @click="goToEdit"
                                    @keydown.enter="goToEdit"
                                    @keydown.space.prevent="goToEdit"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2"
                                    :style="{ 
                                        backgroundColor: colors.brand, 
                                        border: `1px solid ${colors.accent}`,
                                        color: colors.white,
                                        transition: transitions.normal
                                    }"
                                    @mouseenter="$event.target.style.backgroundColor = colors.hover; $event.target.style.color = colors.white"
                                    @mouseleave="$event.target.style.backgroundColor = colors.brand; $event.target.style.color = colors.accent"
                                    :styleFocus="{ ringColor: colors.accent }"
                                    aria-label="Редактировать проект"
                                    tabindex="0"
                                >
                                    <Edit2 class="w-4 h-4" aria-hidden="true" />
                                    <span>Редактировать</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Слайдер -->
                    <div v-if="imagesSlider.length > 0" class="mb-8 rounded-2xl overflow-hidden border-2" :style="{ borderColor: colors.accent }">
                        <SliderInDetailPage :photos="imagesSlider" />
                    </div>
                    
                    <!-- Если нет фото, показываем заглушку -->
                    <div v-else class="mb-8 rounded-2xl overflow-hidden border-2" :style="{ borderColor: colors.accent }">
                        <div class="flex items-center justify-center h-[500px] sm:h-[600px] md:h-[600px] lg:h-[700px] xl:h-[800px]" :style="{ backgroundColor: colors.brand }">
                            <img 
                                :src="sliderFallback" 
                                alt="Логотип проекта"
                                class="max-w-[120px] max-h-[120px] sm:max-w-[150px] sm:max-h-[150px] md:max-w-[180px] md:max-h-[180px] object-contain opacity-60"
                            >
                        </div>
                    </div>

                    <!-- Панель администратора -->
                    <div v-if="userRole === 'Admin'" class="mb-6 p-4 rounded-xl flex flex-wrap items-center justify-between gap-4" :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.accent}` }">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-semibold" :style="{ color: colors.white }">Управление проектом:</span>
                            
                            <div 
                                v-if="project.is_moderated === true" 
                                class="px-3 py-1.5 rounded-lg text-xs text-center"
                                :style="{ backgroundColor: colors.success, color: colors.white }"
                                tabindex="0"
                            >
                                Прошла модерацию
                            </div>
                            <div 
                                v-else-if="project.is_moderated === false" 
                                class="px-3 py-1.5 rounded-lg text-xs text-center"
                                :style="{ backgroundColor: colors.error, color: colors.white }"
                                tabindex="0"
                            >
                                Не прошла модерацию
                            </div>
                            <div 
                                v-else 
                                class="px-3 py-1.5 rounded-lg text-xs text-center"
                                :style="{ backgroundColor: colors.light, color: colors.brand }"
                                tabindex="0"
                            >
                                Ожидает модерации
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <span class="text-sm" :style="{ color: colors.white80 }">Изменить статус:</span>
                            <select 
                                v-model="selectedAdminStatus"
                                @change="changeAdminStatus"
                                class="px-3 py-1.5 rounded-lg text-sm focus:outline-none cursor-pointer focus:ring-2"
                                :style="{ 
                                    backgroundColor: colors.light, 
                                    color: colors.brand, 
                                    border: `1px solid ${colors.accent}`,
                                    ringColor: colors.accent
                                }"
                                aria-label="Изменить статус проекта"
                                tabindex="0"
                            >
                                <option 
                                    v-for="option in statusOptions" 
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Основная плашка -->
                    <div class="rounded-xl overflow-hidden border-2 shadow-lg" :style="{ backgroundColor: colors.light, borderColor: colors.accent, boxShadow: shadows.lg }">
                        
                        <!-- Шапка -->
                        <div class="p-6 border-b-2" :style="{ borderColor: colors.accent }">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h1 
                                        class="text-2xl sm:text-3xl font-heading font-bold mb-3"
                                        :style="{ color: colors.brand, fontFamily: fonts.heading }"
                                        tabindex="0"
                                        role="heading"
                                        aria-level="1"
                                    >
                                        {{ project.title }}
                                    </h1>
                                    <div 
                                        class="flex flex-wrap gap-2"
                                        tabindex="0"
                                        role="group"
                                        aria-label="Категории проекта"
                                    >
                                        <span 
                                            v-for="cat in categories" 
                                            :key="cat"
                                            class="px-4 py-2 text-base font-medium rounded-full border"
                                            :style="{ backgroundColor: colors.brand, color: colors.white, borderColor: colors.accent }"
                                            tabindex="0"
                                        >
                                            {{ cat }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3 колонки -->
                        <div class="grid gap-6 p-6">
                            
                            <!-- Левая колонка - информация -->
                            <div class="lg:col-span-2 space-y-6">
                                <!-- Прогресс сбора -->
                                <div 
                                    v-if="project.total_investment || project.collected_total_investment"
                                    class="p-4 rounded-lg "
                                    role="region"
                                    aria-label="Прогресс сбора средств"
                                    tabindex="0"
                                >
                                    <div class="flex items-start gap-3 mb-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: colors.brand }" aria-hidden="true">
                                            <TrendingUp class="w-6 h-6" :style="{ color: colors.white }" />
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xl mb-1" :style="{ color: colors.textMuted }">Собрано средств</p>
                                            <p class="font-semibold text-xl" :style="{ color: colors.accent }">
                                                {{ formatNumber(project.collected_total_investment) }} ₽
                                            </p>
                                            <p class="text-base" :style="{ color: colors.textMuted }">
                                                из {{ formatNumber(project.total_investment) }} ₽
                                            </p>
                                        </div>
                                    </div>
                                    <div class="w-full h-3 rounded-full overflow-hidden" :style="{ backgroundColor: colors.page }" role="progressbar" :aria-valuenow="Math.min(progress, 100)" aria-valuemin="0" aria-valuemax="100">
                                        <div 
                                            class="h-full rounded-full transition-all duration-300"
                                            :style="{ width: `${Math.min(progress, 100)}%`, backgroundColor: colors.accent }"
                                        ></div>
                                    </div>
                                    <p class="text-base font-medium mt-2" :style="{ color: colors.brand }">{{ Math.min(progress, 100).toFixed(1) }}% от цели</p>
                                </div>

                                <!-- Показатели -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div v-if="project.total_investment" class="flex items-start gap-3 p-3 rounded-lg " tabindex="0" role="group" :aria-label="`Требуемые инвестиции: ${formatNumber(project.total_investment)} рублей`">
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: colors.brand }" aria-hidden="true">
                                            <RussianRuble class="w-5 h-5" :style="{ color: colors.white }" />
                                        </div>
                                        <div>
                                            <p class="text-base" :style="{ color: colors.textMuted }">Требуемые инвестиции</p>
                                            <p class="text-xl font-semibold" :style="{ color: colors.brand }">{{ formatNumber(project.total_investment) }} ₽</p>
                                        </div>
                                    </div>
                                    <div v-if="project.number_date_realise" class="flex items-start gap-3 p-3 rounded-lg" tabindex="0" role="group" :aria-label="`Срок реализации: ${project.number_date_realise} месяцев`">
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: colors.brand }" aria-hidden="true">
                                            <Calendar class="w-5 h-5" :style="{ color: colors.white }" />
                                        </div>
                                        <div>
                                            <p class="text-base" :style="{ color: colors.textMuted }">Срок реализации</p>
                                            <p class="text-xl font-semibold" :style="{ color: colors.brand }">{{ project.number_date_realise }} мес.</p>
                                        </div>
                                    </div>
                                    <div v-if="project.count_new_job" class="flex items-start gap-3 p-3 rounded-lg " tabindex="0" role="group" :aria-label="`Количество рабочих мест: ${formatNumber(project.count_new_job)}`">
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: colors.brand }" aria-hidden="true">
                                            <Users class="w-5 h-5" :style="{ color: colors.white }" />
                                        </div>
                                        <div>
                                            <p class="text-base" :style="{ color: colors.textMuted }">Рабочих мест</p>
                                            <p class="text-xl font-semibold" :style="{ color: colors.brand }">{{ formatNumber(project.count_new_job) }}</p>
                                        </div>
                                    </div>
                                    <div v-if="project.ownership" class="flex items-start gap-3 p-3 rounded-lg" tabindex="0" role="group" :aria-label="`Форма собственности: ${project.ownership}`">
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: colors.brand }" aria-hidden="true">
                                            <Building class="w-5 h-5" :style="{ color: colors.white }" />
                                        </div>
                                        <div>
                                            <p class="text-base" :style="{ color: colors.textMuted }">Собственность</p>
                                            <p class="text-xl font-semibold" :style="{ color: colors.brand }">{{ project.ownership }}</p>
                                        </div>
                                    </div>
                                    <div v-if="project.type_build" class="flex items-start gap-3 p-3 rounded-lg" tabindex="0" role="group" :aria-label="`Вид строительства: ${project.type_build}`">
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: colors.brand }" aria-hidden="true">
                                            <Hammer class="w-5 h-5" :style="{ color: colors.white }" />
                                        </div>
                                        <div>
                                            <p class="text-base" :style="{ color: colors.textMuted }">Вид строительства</p>
                                            <p class="text-xl font-semibold" :style="{ color: colors.brand }">{{ project.type_build }}</p>
                                        </div>
                                    </div>
                                    <div v-if="project.activity" class="flex items-start gap-3 p-3 rounded-lg" tabindex="0" role="group" :aria-label="`Вид деятельности: ${project.activity}`">
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: colors.brand }" aria-hidden="true">
                                            <Briefcase class="w-5 h-5" :style="{ color: colors.white }" />
                                        </div>
                                        <div>
                                            <p class="text-base" :style="{ color: colors.textMuted }">Деятельность</p>
                                            <p class="text-xl font-semibold" :style="{ color: colors.brand }">{{ project.activity }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Адрес -->
                                <div v-if="project.address" class="flex items-start gap-3 p-3 rounded-lg" tabindex="0" role="group" :aria-label="`Адрес: ${project.address}`">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: colors.brand }" aria-hidden="true">
                                        <MapPin class="w-5 h-5" :style="{ color: colors.white }" />
                                    </div>
                                    <div>
                                        <p class="text-base" :style="{ color: colors.textMuted }">Адрес</p>
                                        <p class="text-xl font-semibold" :style="{ color: colors.brand }">{{ project.address }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Правая колонка - контакты -->
                            <div v-if="organizerContacts" class="lg:col-span-2">
                                <div class="p-4 rounded-lg " >
                                    <h4 class="font-heading font-semibold text-xl mb-3" :style="{ color: colors.accent, fontFamily: fonts.heading }">Инициатор проекта</h4>
                                    <div class="space-y-2 mb-4">
                                        <div class="flex gap-1" tabindex="0"> 
                                            <p class="font-medium text-xl" :style="{ color: colors.brand }">{{ organizerContacts?.middlename }}</p>
                                            <p class="font-medium text-xl" :style="{ color: colors.brand }">{{ organizerContacts?.name }}</p>
                                            <p class="font-medium text-xl" :style="{ color: colors.brand }">{{ organizerContacts?.lastname }}</p>
                                        </div>
                                        <p v-if="organizerContacts?.organization" class="text-base" tabindex="0">{{ organizerContacts?.organization }}</p>
                                    </div>

                                    <div v-if="organizerContacts?.email || organizerContacts?.phone" class="my-3 border-t" :style="{ borderColor: colors.divider }"></div>

                                    <h4 class="font-heading font-semibold text-xl mb-3" :style="{ color: colors.accent, fontFamily: fonts.heading }">Контакты</h4>
                                    <div class="space-y-3">
                                        <div v-if="organizerContacts?.email" class="flex items-start gap-2">
                                            <Mail class="w-5 h-5 mt-0.5 flex-shrink-0" :style="{ color: colors.accent }" aria-hidden="true" />
                                            <a 
                                                :href="`mailto:${organizerContacts.email}`" 
                                                class="text-xl hover:opacity-80 transition-opacity break-words focus:outline-none focus:ring-2 rounded-md px-1 inline-block"
                                                :style="{ color: colors.brand, transition: transitions.fast }"
                                                :aria-label="`Email: ${organizerContacts.email}`"
                                                tabindex="0"
                                                :styleFocus="{ ringColor: colors.accent }"
                                            >
                                                {{ organizerContacts.email }}
                                            </a>
                                        </div>
                                        <div v-if="organizerContacts?.phone" class="flex items-start gap-2">
                                            <Phone class="w-5 h-5 mt-0.5 flex-shrink-0" :style="{ color: colors.accent }" aria-hidden="true" />
                                            <a 
                                                :href="`tel:${organizerContacts.phone}`" 
                                                class="text-xl hover:opacity-80 transition-opacity focus:outline-none focus:ring-2 rounded-md px-1 inline-block"
                                                :style="{ color: colors.brand, transition: transitions.fast }"
                                                :aria-label="`Телефон: ${organizerContacts.phone}`"
                                                tabindex="0"
                                                :styleFocus="{ ringColor: colors.accent }"
                                            >
                                                {{ organizerContacts.phone }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Остальной контент -->
            <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16">
                <!-- Описание -->
                <div v-if="project.full_description || project.short_description" class="mb-6 rounded-2xl p-6 border-2 shadow-sm" :style="{ backgroundColor: colors.light, borderColor: colors.accent, boxShadow: shadows.sm }" tabindex="0" role="region" aria-label="Описание проекта">
                    <h2 class="text-2xl font-heading font-semibold mb-3" :style="{ color: colors.accent, fontFamily: fonts.heading }">Описание</h2>
                    <p class="leading-relaxed text-2xl" :style="{ color: colors.brand }">
                        {{ project.full_description || project.short_description }}
                    </p>
                </div>

                <!-- Структура инвестиций -->
                <div 
                    v-if="investments.length > 0" 
                    class="mb-6 rounded-2xl p-6 border-2 shadow-sm overflow-x-auto"
                    :style="{ backgroundColor: colors.light, borderColor: colors.accent, boxShadow: shadows.sm }"
                    tabindex="0"
                    role="region"
                    aria-label="Структура инвестиций"
                >
                    <h2 class="text-2xl font-heading font-semibold mb-4" :style="{ color: colors.accent, fontFamily: fonts.heading }">Структура инвестиций</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left min-w-[500px]" role="table" aria-label="Структура инвестиций по статьям расходов">
                            <thead>
                                <tr class="border-b-2" :style="{ borderColor: colors.accent }">
                                    <th class="pb-2 font-semibold text-xl" :style="{ color: colors.accent }" scope="col">Статья расходов</th>
                                    <th class="pb-2 text-right font-semibold text-xl" :style="{ color: colors.accent }" scope="col">Сумма (₽)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(investment, idx) in investments" :key="idx" class="border-b" :style="{ borderColor: colors.accent + '30' }">
                                    <td class="py-3 text-xl" :style="{ color: colors.brand }" tabindex="0">{{ investment.item_name }}</td>
                                    <td class="py-3 text-right text-xl" :style="{ color: colors.brand }" tabindex="0">{{ formatNumber(investment.amount) }} ₽</td>
                                </tr>
                                <tr class="font-semibold" :style="{ backgroundColor: colors.white + '30' }">
                                    <td class="pt-3 text-xl" :style="{ color: colors.accent }" tabindex="0">ИТОГО:</td>
                                    <td class="pt-3 text-right text-xl" :style="{ color: colors.accent }" tabindex="0">
                                        {{ formatNumber(investments.reduce((sum, inv) => sum + (Number(inv.amount) || 0), 0)) }} ₽
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Прогнозы -->
                <div 
                    v-if="forecasts.length > 0" 
                    class="mb-6 rounded-2xl p-6 border-2 shadow-sm overflow-x-auto"
                    :style="{ backgroundColor: colors.light, borderColor: colors.accent, boxShadow: shadows.sm }"
                    tabindex="0"
                    role="region"
                    aria-label="Прогноз доходов и расходов"
                >
                    <h2 class="text-2xl font-heading font-semibold mb-4" :style="{ color: colors.accent, fontFamily: fonts.heading }">Прогноз доходов и расходов</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left min-w-[600px]" role="table" aria-label="Прогноз доходов и расходов по годам">
                            <thead>
                                <tr class="border-b-2" :style="{ borderColor: colors.accent }">
                                    <th class="pb-2 font-semibold text-xl" :style="{ color: colors.accent }" scope="col">Год</th>
                                    <th class="pb-2 text-right font-semibold text-xl" :style="{ color: colors.accent }" scope="col">Доходы (₽)</th>
                                    <th class="pb-2 text-right font-semibold text-xl" :style="{ color: colors.accent }" scope="col">Расходы (₽)</th>
                                    <th class="pb-2 text-right font-semibold text-xl" :style="{ color: colors.accent }" scope="col">Прибыль (₽)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(forecast, idx) in forecasts" :key="idx" class="border-b" :style="{ borderColor: colors.accent + '30' }">
                                    <td class="py-3 text-xl" :style="{ color: colors.brand }" tabindex="0">{{ forecast.year }}</td>
                                    <td class="py-3 text-right text-xl" :style="{ color: colors.brand }" tabindex="0">{{ formatNumber(forecast.expected_revenue) }} ₽</td>
                                    <td class="py-3 text-right text-xl" :style="{ color: colors.brand }" tabindex="0">{{ formatNumber(forecast.expected_expenses) }} ₽</td>
                                    <td class="py-3 text-right text-xl" :style="{ color: colors.brand }" tabindex="0">{{ formatNumber((Number(forecast.expected_revenue) || 0) - (Number(forecast.expected_expenses) || 0)) }} ₽</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Карта -->
                <div v-if="hasCoordinates" class="rounded-2xl overflow-hidden border-2 shadow-sm" :style="{ backgroundColor: colors.light, borderColor: colors.accent, boxShadow: shadows.sm }">
                    <div class="p-4 sm:p-5">
                        <h2 class="text-2xl font-heading font-semibold mb-4" :style="{ color: colors.accent, fontFamily: fonts.heading }">Расположение</h2>
                        <div class="w-full h-[80vh] rounded-xl overflow-hidden border-2" :style="{ borderColor: colors.accent }">
                            <GoogleMap
                                :api-key="api"
                                class="w-full h-full"
                                :center="mapCenter"
                                :zoom="mapZoom"
                                @click="closeMarker"
                                aria-label="Карта с местоположением проекта"
                                tabindex="0"
                                :styles="[
                                    {
                                        featureType: 'poi',
                                        elementType: 'labels',
                                        stylers: [{ visibility: 'off' }]
                                    }
                                ]"
                            >
                                <Polygon
                                    v-if="regionPolygon && regionPolygon.length > 0"
                                    :options="{
                                        paths: regionPolygon,
                                        ...polygonOptions
                                    }"
                                />

                                <Marker
                                    :options="{ 
                                        position: projectMarker.position,
                                        title: projectMarker.title,
                                        icon: buildRedPinIcon(selectedProjectId === props.project.id),
                                        zIndex: selectedProjectId === props.project.id ? 999 : 1
                                    }"
                                    @click="selectProject"
                                />

                                <CustomMarker
                                    v-if="selectedProjectId === props.project.id && hasCoordinates"
                                    :options="{
                                        position: projectMarker.position,
                                        anchorPoint: 'TOP_CENTER'
                                    }"
                                >
                                    <div class="relative mt-3">
                                        <div class="w-80 rounded-2xl overflow-hidden shadow-2xl border-2" :style="{ borderColor: colors.accent, backgroundColor: colors.brand, boxShadow: shadows.xl }">
                                            <!-- Изображение -->
                                            <div class="relative h-36" :style="{ backgroundImage: `linear-gradient(to bottom right, ${colors.hover}30, ${colors.hover}10)` }">
                                                <div v-if="imageLoading && !imageError && hasPhotos" class="absolute inset-0 flex items-center justify-center z-10" :style="{ backgroundColor: colors.brand + '80' }">
                                                    <svg class="animate-spin w-6 h-6" :style="{ color: colors.accent }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                </div>
                                                
                                                <img 
                                                    v-if="hasPhotos && !imageError"
                                                    :src="getProjectImage(project)" 
                                                    :alt="`Изображение проекта ${project.title}`"
                                                    class="w-full h-full object-cover"
                                                    loading="lazy"
                                                    @load="handleImageLoad"
                                                    @error="handleImageError"
                                                />
                                                
                                                <div v-if="!hasPhotos || imageError" class="absolute inset-0 flex items-center justify-center" :style="{ backgroundColor: colors.hover }">
                                                    <img :src="defaultImage" alt="Логотип проекта" class="w-[60%] h-[60%] object-contain opacity-60">
                                                </div>
                                                
                                                <button
                                                    class="absolute top-2 right-2 w-7 h-7 rounded-full flex items-center justify-center backdrop-blur-sm transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2"
                                                    :style="{ backgroundColor: colors.brand + '99', color: colors.white, transition: transitions.normal }"
                                                    @click.stop="closeMarker"
                                                    :styleFocus="{ ringColor: colors.accent }"
                                                    aria-label="Закрыть карточку проекта"
                                                >
                                                    <X class="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                            
                                            <!-- Контент -->
                                            <div class="p-4">
                                                <div class="flex items-start justify-between gap-2 mb-2">
                                                    <h3 class="text-base font-heading font-bold leading-tight line-clamp-2 flex-1" :style="{ color: colors.white, fontFamily: fonts.heading }">
                                                        {{ project.title }}
                                                    </h3>
                                                </div>
                                                
                                                <p class="text-base mb-3 line-clamp-2" :style="{ color: colors.white80 }">
                                                    {{ project.short_description?.slice(0, 80) || "Описание проекта отсутствует" }}
                                                </p>
                                                
                                                <!-- Прогресс сбора -->
                                                <div class="mb-3">
                                                    <div class="flex justify-between text-lg mb-1">
                                                        <span :style="{ color: colors.white80 }">Собрано средств</span>
                                                        <span class="font-semibold" :style="{ color: colors.white }">{{ Math.min(progress, 100).toFixed(0) }}%</span>
                                                    </div>
                                                    <div class="w-full h-1.5 rounded-full overflow-hidden" :style="{ backgroundColor: colors.page }">
                                                        <div class="h-full rounded-full transition-all" :style="{ width: `${Math.min(progress, 100)}%`, backgroundColor: colors.accent }"></div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Инвестиции и адрес -->
                                                <div class="space-y-2 text-base mb-3">
                                                    <div v-if="project.total_investment" class="flex items-center justify-between">
                                                        <div class="flex items-center gap-1.5" :style="{ color: colors.white80 }">
                                                            <TrendingUp class="w-3.5 h-3.5" />
                                                            <span>Инвестиции:</span>
                                                        </div>
                                                        <span class="font-semibold" :style="{ color: colors.white }">{{ formatNumber(project.total_investment) }} ₽</span>
                                                    </div>
                                                    <div v-if="project.count_new_job" class="flex items-center justify-between">
                                                        <div class="flex items-center gap-1.5" :style="{ color: colors.white80 }">
                                                            <Users class="w-3.5 h-3.5" />
                                                            <span>Рабочих мест:</span>
                                                        </div>
                                                        <span class="font-semibold" :style="{ color: colors.white }">{{ formatNumber(project.count_new_job) }}</span>
                                                    </div>
                                                    <div v-if="project.address" class="flex items-start gap-1.5">
                                                        <MapPin class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" :style="{ color: colors.white80 }" />
                                                        <span class="text-base leading-relaxed line-clamp-2" :style="{ color: colors.white80 }">{{ project.address }}</span>
                                                    </div>
                                                    <div v-if="project.activity" class="flex items-center gap-1.5">
                                                        <Briefcase class="w-3.5 h-3.5 flex-shrink-0" :style="{ color: colors.white80 }" />
                                                        <span class="text-base" :style="{ color: colors.white80 }">{{ project.activity }}</span>
                                                    </div>
                                                </div>
                                            
                                            </div>
                                        </div>
                                    </div>
                                </CustomMarker>
                            </GoogleMap>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>