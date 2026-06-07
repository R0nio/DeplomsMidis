<script setup>
import { ref, computed, reactive, watch } from 'vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, router } from "@inertiajs/vue3";
import { GoogleMap, Marker, CustomMarker, Polygon } from "vue3-google-map";
import SliderInDetailPage from "@/Components/Main/SliderInDetailPage.vue";
import axios from 'axios';
import chelyabinskOblastGeoJson from "../../../public/geo/chelyabinsk-oblast.json";
import {
  MapPin,
  Calendar,
  RussianRuble,
  Users,
  Building,
  Hammer,
  Briefcase,
  Mail,
  Phone,
  TrendingUp,
  ArrowLeft,
  X,
  Edit2
} from 'lucide-vue-next';
// Images
import sliderFallback from "../../images/LogoInvestProject.png";
import FavoriteIcon from "../../images/Favorite.png";
import FavoriteActiveIcon from "../../images/FavoriteActivity.png";
import defaultImage from "../../images/LogoInvestProject.png";

// ========== ЦВЕТОВАЯ СХЕМА И ШРИФТЫ ==========
const a11y = {
  colors: {
    bgPage: '#436343',
    bgLight: '#809076',
    bgDark: '#284139',
    cardBg: '#243a33',
    cardBgSoft: '#2f4d43',
    buttonBg: '#809076',
    accent: '#F8D794',
    border: '#886830',
    textLight: '#e8f0ee',
    textDark: '#111A19',
    white: '#ffffff',
    white80: 'rgba(255, 255, 255, 0.8)',
    white60: 'rgba(255, 255, 255, 0.6)',
    red: '#ef4444',
    redDark: '#b91c1c',
    green: '#4caf50'
  },
  fonts: {
    base: '14px',
    sm: '12px',
    lg: '16px',
    xl: '18px',
    xxl: '24px'
  }
};

// Опции полигона
const polygonOptions = {
    strokeColor: "#F8D794",
    strokeOpacity: 0.8,
    strokeWeight: 3,
    fillColor: "#F8D794",
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

const getProjectImage = (project) => {
    if (project.photos && project.photos.length > 0) {
        const photoPath = project.photos[0].photo_path;
        return `/storage/${photoPath}`;
    }
    return sliderFallback;
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
            alt: `Фото проекта ${index + 1}`
        }));
    }
    return [{ src: sliderFallback, alt: 'Фото проекта' }];
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
        return;
    }

    const previousState = isFavorite.value;
    isFavorite.value = !isFavorite.value;

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
        await router.reload();
    } catch (error) {
        console.error('Ошибка при изменении статуса:', error);
        selectedAdminStatus.value = props.project.status;
        alert('Не удалось изменить статус проекта');
    }
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
            
            console.log('Polygon loaded, points count:', regionPolygon.value.length);
        }
    }
};

// Карта
const api = import.meta.env.VITE_GOOGLE_MAPS_API_KEY || "AIzaSyDfKb4UVvQwK3cANRf-7EVCLlGy0fi2yno";
const mapZoom = ref(5);

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

const selectedProjectId = ref(props.project.id); // По умолчанию показываем popup

const selectProject = () => {
    selectedProjectId.value = selectedProjectId.value === props.project.id ? null : props.project.id;
};

const closeMarker = () => {
    selectedProjectId.value = null;
};

const goToProject = (id) => {
    router.visit(route('projects.show', id));
};

const goBack = () => {
    router.visit('/projects');
};

// Красный пин как на карте проектов
const buildRedPinIcon = (active = false) => {
    const size = active ? 44 : 40;
    const fill = a11y.colors.red;
    const stroke = a11y.colors.redDark;

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
    <AuthenticatedLayout>
        <Head :title="project.title">
            <meta name="description" :content="project.short_description">
        </Head>
        
        <div class="min-h-screen" :style="{ backgroundColor: a11y.colors.bgPage }">
            <div class="relative">
                <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16">
        
                    <!-- Кнопки: назад, избранное, редактировать -->
                    <div class="flex justify-between items-center mb-6 mt-6">
                        <button
                            @click="goBack"
                            @keydown.enter="goBack"
                            @keydown.space.prevent="goBack"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200"
                            :style="{ backgroundColor: a11y.colors.bgDark, color: a11y.colors.accent, border: `1px solid ${a11y.colors.border}` }"
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
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200"
                                    :style="{ backgroundColor: a11y.colors.bgDark, border: `1px solid ${a11y.colors.border}` }"
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
                                    <span :style="{ color: a11y.colors.accent }">{{ isFavorite ? 'В избранном' : 'В избранное' }}</span>
                                </button>
                            </div>
                            
                            <!-- Кнопка редактировать (для организатора - владельца проекта) -->
                            <div v-if="userRole === 'Organisator' && authUser?.id === project.user?.id">
                                <button 
                                    @click="goToEdit"
                                    @keydown.enter="goToEdit"
                                    @keydown.space.prevent="goToEdit"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg transition-all duration-200 hover:scale-105"
                                    :style="{ backgroundColor: a11y.colors.bgDark, border: `1px solid ${a11y.colors.border}`, color: a11y.colors.accent }"
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
                    <div class="mb-8 rounded-xl overflow-hidden" :style="{ border: `2px solid ${a11y.colors.border}` }">
                        <SliderInDetailPage :photos="imagesSlider" />
                    </div>

                    <!-- Панель администратора (под слайдером, только для админа) -->
                    <div v-if="userRole === 'Admin'" class="mb-6 p-4 rounded-xl flex flex-wrap items-center justify-between gap-4" :style="{ backgroundColor: a11y.colors.bgDark, border: `1px solid ${a11y.colors.border}` }">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-semibold" :style="{ color: a11y.colors.accent }">Управление проектом:</span>
                            
                            <!-- Статус модерации -->
                            <div 
                                v-if="project.is_moderated === true" 
                                class="px-3 py-1.5 rounded-lg text-xs text-center"
                                style="background-color: #4caf50; color: white"
                            >
                                Прошла модерацию
                            </div>
                            <div 
                                v-else-if="project.is_moderated === false" 
                                class="px-3 py-1.5 rounded-lg text-xs text-center"
                                style="background-color: #f44336; color: white"
                            >
                                Не прошла модерацию
                            </div>
                            <div 
                                v-else 
                                class="px-3 py-1.5 rounded-lg text-xs text-center"
                                :style="{ backgroundColor: a11y.colors.bgLight, color: a11y.colors.white }"
                            >
                                Ожидает модерации
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <span class="text-sm" :style="{ color: a11y.colors.white80 }">Изменить статус:</span>
                            <select 
                                v-model="selectedAdminStatus"
                                @change="changeAdminStatus"
                                class="px-3 py-1.5 rounded-lg text-sm focus:outline-none cursor-pointer"
                                :style="{ backgroundColor: a11y.colors.bgLight, color: a11y.colors.white, border: `1px solid ${a11y.colors.border}` }"
                                aria-label="Изменить статус проекта"
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
                    <div class="rounded-xl overflow-hidden" :style="{ backgroundColor: a11y.colors.bgLight, border: `2px solid ${a11y.colors.border}` }">
                        
                        <!-- Шапка -->
                        <div class="p-6" :style="{ borderBottom: `2px solid ${a11y.colors.border}` }">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h1 
                                        class="text-2xl sm:text-3xl font-bold mb-3" 
                                        :style="{ color: a11y.colors.accent }"
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
                                            class="px-3 py-1 text-xs font-medium rounded-full"
                                            :style="{ backgroundColor: a11y.colors.bgDark, color: a11y.colors.accent, border: `1px solid ${a11y.colors.border}` }"
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
                                    class="p-4 rounded-lg" 
                                    :style="{ backgroundColor: a11y.colors.bgDark }"
                                    tabindex="0"
                                    role="region"
                                    :aria-label="`Прогресс сбора: собрано ${formatNumber(project.collected_total_investment)} рублей из ${formatNumber(project.total_investment)} рублей, ${Math.min(progress, 100).toFixed(1)} процентов от цели`"
                                >
                                    <div class="flex items-start gap-3 mb-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: a11y.colors.accent }" aria-hidden="true">
                                            <TrendingUp class="w-6 h-6" :style="{ color: a11y.colors.bgDark }" />
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm mb-1" :style="{ color: a11y.colors.white80 }">Собрано средств</p>
                                            <p class="font-semibold text-lg" :style="{ color: a11y.colors.accent }">
                                                {{ formatNumber(project.collected_total_investment) }} ₽
                                            </p>
                                            <p class="text-sm" :style="{ color: a11y.colors.white80 }">
                                                из {{ formatNumber(project.total_investment) }} ₽
                                            </p>
                                        </div>
                                    </div>
                                    <div class="w-full h-3 rounded-full overflow-hidden" :style="{ backgroundColor: a11y.colors.border }" role="progressbar" :aria-valuenow="Math.min(progress, 100)" aria-valuemin="0" aria-valuemax="100">
                                        <div 
                                            class="h-full rounded-full transition-all duration-300"
                                            :style="{ width: `${Math.min(progress, 100)}%`, backgroundColor: a11y.colors.accent }"
                                        ></div>
                                    </div>
                                    <p class="text-sm font-medium mt-2" :style="{ color: a11y.colors.accent }">{{ Math.min(progress, 100).toFixed(1) }}% от цели</p>
                                </div>

                                <!-- Показатели -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div 
                                        class="flex items-start gap-3"
                                        tabindex="0"
                                        role="group"
                                        :aria-label="`Требуемые инвестиции: ${formatNumber(project.total_investment)} рублей`"
                                    >
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: a11y.colors.bgDark }" aria-hidden="true">
                                            <RussianRuble class="w-5 h-5" :style="{ color: a11y.colors.accent }" />
                                        </div>
                                        <div>
                                            <p class="text-sm" :style="{ color: a11y.colors.white80 }">Требуемые инвестиции</p>
                                            <p class="text-sm font-medium" :style="{ color: a11y.colors.white }">{{ formatNumber(project.total_investment) }} ₽</p>
                                        </div>
                                    </div>
                                    <div 
                                        class="flex items-start gap-3"
                                        tabindex="0"
                                        role="group"
                                        :aria-label="`Срок реализации: ${project.number_date_realise || 'не указан'} месяцев`"
                                    >
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: a11y.colors.bgDark }" aria-hidden="true">
                                            <Calendar class="w-5 h-5" :style="{ color: a11y.colors.accent }" />
                                        </div>
                                        <div>
                                            <p class="text-sm" :style="{ color: a11y.colors.white80 }">Срок реализации</p>
                                            <p class="text-sm font-medium" :style="{ color: a11y.colors.white }">{{ project.number_date_realise || '—' }} мес.</p>
                                        </div>
                                    </div>
                                    <div 
                                        class="flex items-start gap-3"
                                        tabindex="0"
                                        role="group"
                                        :aria-label="`Количество рабочих мест: ${project.count_new_job || 'не указано'}`"
                                    >
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: a11y.colors.bgDark }" aria-hidden="true">
                                            <Users class="w-5 h-5" :style="{ color: a11y.colors.accent }" />
                                        </div>
                                        <div>
                                            <p class="text-sm" :style="{ color: a11y.colors.white80 }">Рабочих мест</p>
                                            <p class="text-sm font-medium" :style="{ color: a11y.colors.white }">{{ project.count_new_job || '—' }}</p>
                                        </div>
                                    </div>
                                    <div 
                                        class="flex items-start gap-3"
                                        tabindex="0"
                                        role="group"
                                        :aria-label="`Форма собственности: ${project.ownership || 'не указана'}`"
                                    >
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: a11y.colors.bgDark }" aria-hidden="true">
                                            <Building class="w-5 h-5" :style="{ color: a11y.colors.accent }" />
                                        </div>
                                        <div>
                                            <p class="text-sm" :style="{ color: a11y.colors.white80 }">Собственность</p>
                                            <p class="text-sm font-medium" :style="{ color: a11y.colors.white }">{{ project.ownership || '—' }}</p>
                                        </div>
                                    </div>
                                    <div 
                                        class="flex items-start gap-3"
                                        tabindex="0"
                                        role="group"
                                        :aria-label="`Вид строительства: ${project.type_build || 'не указан'}`"
                                    >
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: a11y.colors.bgDark }" aria-hidden="true">
                                            <Hammer class="w-5 h-5" :style="{ color: a11y.colors.accent }" />
                                        </div>
                                        <div>
                                            <p class="text-sm" :style="{ color: a11y.colors.white80 }">Вид строительства</p>
                                            <p class="text-sm font-medium" :style="{ color: a11y.colors.white }">{{ project.type_build || '—' }}</p>
                                        </div>
                                    </div>
                                    <div 
                                        class="flex items-start gap-3"
                                        tabindex="0"
                                        role="group"
                                        :aria-label="`Вид деятельности: ${project.activity || 'не указан'}`"
                                    >
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: a11y.colors.bgDark }" aria-hidden="true">
                                            <Briefcase class="w-5 h-5" :style="{ color: a11y.colors.accent }" />
                                        </div>
                                        <div>
                                            <p class="text-sm" :style="{ color: a11y.colors.white80 }">Деятельность</p>
                                            <p class="text-sm font-medium" :style="{ color: a11y.colors.white }">{{ project.activity || '—' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Адрес -->
                                <div 
                                    class="pt-2"
                                    tabindex="0"
                                    role="group"
                                    :aria-label="`Адрес: ${project.address || 'не указан'}`"
                                >
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" :style="{ backgroundColor: a11y.colors.bgDark }" aria-hidden="true">
                                            <MapPin class="w-5 h-5" :style="{ color: a11y.colors.accent }" />
                                        </div>
                                        <div>
                                            <p class="text-sm" :style="{ color: a11y.colors.white80 }">Адрес</p>
                                            <p class="text-sm font-medium" :style="{ color: a11y.colors.white }">{{ project.address || '—' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Правая колонка - контакты -->
                            <div class="lg:col-span-2">
                                <div class="p-4 rounded-lg" :style="{ backgroundColor: a11y.colors.bgDark }">
                                    <h4 class="font-semibold text-lg mb-3" :style="{ color: a11y.colors.accent }">Инициатор проекта</h4>
                                    <div class="space-y-2 mb-4">
                                        <div class="flex gap-2"> 
                                            <p class="font-medium text-sm" :style="{ color: a11y.colors.white }" tabindex="0">{{ organizerContacts?.middlename }}</p>
                                            <p class="font-medium text-sm" :style="{ color: a11y.colors.white }" tabindex="0">{{ organizerContacts?.name }}</p>
                                            <p class="font-medium text-sm" :style="{ color: a11y.colors.white }" tabindex="0">{{ organizerContacts?.lastname }}</p>
                                        </div>
                                        <p class="text-xs" :style="{ color: a11y.colors.white80 }" tabindex="0">{{ organizerContacts?.organization }}</p>
                                    </div>

                                    <div class="my-3" :style="{ borderTop: `1px solid ${a11y.colors.border}` }"></div>

                                    <h4 class="font-semibold text-sm mb-3" :style="{ color: a11y.colors.accent }">Контакты</h4>
                                    <div class="space-y-2">
                                        <div class="flex items-start gap-2">
                                            <Mail class="w-3 h-3 mt-0.5 flex-shrink-0" :style="{ color: a11y.colors.accent }" aria-hidden="true" />
                                            <a 
                                                :href="`mailto:${organizerContacts?.email}`" 
                                                class="text-xs hover:opacity-80 transition-opacity break-words" 
                                                :style="{ color: a11y.colors.white }" 
                                                :aria-label="`Email: ${organizerContacts?.email}`"
                                                tabindex="0"
                                            >
                                                {{ organizerContacts?.email }}
                                            </a>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <Phone class="w-3 h-3 mt-0.5 flex-shrink-0" :style="{ color: a11y.colors.accent }" aria-hidden="true" />
                                            <a 
                                                :href="`tel:${organizerContacts?.phone}`" 
                                                class="text-xs hover:opacity-80 transition-opacity" 
                                                :style="{ color: a11y.colors.white }" 
                                                :aria-label="`Телефон: ${organizerContacts?.phone}`"
                                                tabindex="0"
                                            >
                                                {{ organizerContacts?.phone }}
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
                <div 
                    class="mb-6" 
                    :style="{ border: `2px solid ${a11y.colors.border}`, borderRadius: '16px', padding: '20px', backgroundColor: a11y.colors.bgLight }"
                    tabindex="0"
                    role="region"
                    aria-label="Описание проекта"
                >
                    <h2 class="text-xl font-medium mb-3" :style="{ color: a11y.colors.accent }">Описание</h2>
                    <p class="leading-relaxed text-base" :style="{ color: a11y.colors.white }">
                        {{ project.full_description || project.short_description }}
                    </p>
                </div>

                <!-- Структура инвестиций -->
                <div 
                    v-if="investments.length > 0" 
                    class="mb-6" 
                    :style="{ border: `2px solid ${a11y.colors.border}`, borderRadius: '16px', padding: '20px', backgroundColor: a11y.colors.bgLight }"
                    tabindex="0"
                    role="region"
                    aria-label="Структура инвестиций"
                >
                    <h2 class="text-xl font-medium mb-4" :style="{ color: a11y.colors.accent }">Структура инвестиций</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left" role="table" aria-label="Структура инвестиций по статьям расходов">
                            <thead>
                                <tr :style="{ borderBottom: `2px solid ${a11y.colors.border}` }">
                                    <th class="pb-2" :style="{ color: a11y.colors.accent }" scope="col">Статья расходов</th>
                                    <th class="pb-2 text-right" :style="{ color: a11y.colors.accent }" scope="col">Сумма (₽)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(investment, idx) in investments" :key="idx" :style="{ borderBottom: `1px solid ${a11y.colors.border}` }">
                                    <td class="py-2" :style="{ color: a11y.colors.white }">{{ investment.item_name }}</td>
                                    <td class="py-2 text-right" :style="{ color: a11y.colors.white }">{{ formatNumber(investment.amount) }} ₽</td>
                                </tr>
                                <tr class="font-semibold" :style="{ borderTop: `2px solid ${a11y.colors.border}` }">
                                    <td class="pt-2" :style="{ color: a11y.colors.accent }">ИТОГО:</td>
                                    <td class="pt-2 text-right" :style="{ color: a11y.colors.accent }">
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
                    class="mb-6" 
                    :style="{ border: `2px solid ${a11y.colors.border}`, borderRadius: '16px', padding: '20px', backgroundColor: a11y.colors.bgLight }"
                    tabindex="0"
                    role="region"
                    aria-label="Прогноз доходов и расходов"
                >
                    <h2 class="text-xl font-medium mb-4" :style="{ color: a11y.colors.accent }">Прогноз доходов и расходов</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left" role="table" aria-label="Прогноз доходов и расходов по годам">
                            <thead>
                                <tr :style="{ borderBottom: `2px solid ${a11y.colors.border}` }">
                                    <th class="pb-2" :style="{ color: a11y.colors.accent }" scope="col">Год</th>
                                    <th class="pb-2 text-right" :style="{ color: a11y.colors.accent }" scope="col">Доходы (₽)</th>
                                    <th class="pb-2 text-right" :style="{ color: a11y.colors.accent }" scope="col">Расходы (₽)</th>
                                    <th class="pb-2 text-right" :style="{ color: a11y.colors.accent }" scope="col">Прибыль (₽)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(forecast, idx) in forecasts" :key="idx" :style="{ borderBottom: `1px solid ${a11y.colors.border}` }">
                                    <td class="py-2" :style="{ color: a11y.colors.white }">{{ forecast.year }}</td>
                                    <td class="py-2 text-right" :style="{ color: a11y.colors.white }">{{ formatNumber(forecast.expected_revenue) }} ₽</td>
                                    <td class="py-2 text-right" :style="{ color: a11y.colors.white }">{{ formatNumber(forecast.expected_expenses) }} ₽</td>
                                    <td class="py-2 text-right" :style="{ color: a11y.colors.white }">{{ formatNumber((Number(forecast.expected_revenue) || 0) - (Number(forecast.expected_expenses) || 0)) }} ₽</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Карта -->
                <div v-if="hasCoordinates" class="rounded-xl overflow-hidden" :style="{ border: `2px solid ${a11y.colors.border}`, backgroundColor: a11y.colors.bgLight }">
                    <div class="p-4 sm:p-5">
                        <h2 class="text-xl font-medium mb-4" :style="{ color: a11y.colors.accent }">Расположение</h2>
                        <div class="w-full h-[80vh] sm:h-[80vh] rounded-xl overflow-hidden" :style="{ border: `2px solid ${a11y.colors.border}` }">
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
                                <!-- Полигон границы Челябинской области -->
                                <Polygon
                                    v-if="regionPolygon && regionPolygon.length > 0"
                                    :options="{
                                        paths: regionPolygon,
                                        ...polygonOptions
                                    }"
                                />

                                <!-- Красный маркер как на странице проектов -->
                                <Marker
                                    :options="{ 
                                        position: projectMarker.position,
                                        title: projectMarker.title,
                                        icon: buildRedPinIcon(selectedProjectId === props.project.id),
                                        zIndex: selectedProjectId === props.project.id ? 999 : 1
                                    }"
                                    @click="selectProject"
                                />

                                <!-- Кастомный popup на месте пина -->
                                <CustomMarker
                                    v-if="selectedProjectId === props.project.id && hasCoordinates"
                                    :options="{
                                        position: projectMarker.position,
                                        anchorPoint: 'TOP_CENTER'
                                    }"
                                >
                                    <div class="relative mt-3">
                                        <div
                                            class="w-72 rounded-2xl overflow-hidden shadow-2xl border"
                                            :style="{ backgroundColor: a11y.colors.cardBg, borderColor: a11y.colors.border }"
                                            @click.stop
                                        >
                                            <div class="relative h-40" :style="{ backgroundColor: a11y.colors.cardBgSoft }">
                                                <img
                                                    v-if="project.photos?.length"
                                                    :src="getProjectImage(project)"
                                                    :alt="`Изображение проекта ${project.title}`"
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
                                                    @click.stop="closeMarker"
                                                    aria-label="Закрыть карточку проекта"
                                                >
                                                    <X class="w-4 h-4" />
                                                </button>
                                            </div>

                                            <div class="p-4">
                                                <h3 class="text-lg font-bold mb-1 leading-tight line-clamp-2" :style="{ color: a11y.colors.accent }">
                                                    {{ project.title }}
                                                </h3>

                                                <p class="text-sm mb-3 line-clamp-2" :style="{ color: a11y.colors.white80 }">
                                                    {{ project.short_description?.slice(0, 95) || "Описание проекта отсутствует" }}
                                                </p>

                                                <div class="space-y-2 text-sm mb-3">
                                                    <div v-if="project.total_investment" class="flex items-center justify-between">
                                                        <span class="text-xs" :style="{ color: a11y.colors.white80 }">Инвестиции:</span>
                                                        <span class="font-semibold" :style="{ color: a11y.colors.accent }">{{ formatNumber(project.total_investment) }} ₽</span>
                                                    </div>
                                                    <div v-if="project.address" class="flex items-start gap-1">
                                                        <MapPin class="w-3 h-3 mt-0.5 flex-shrink-0" :style="{ color: a11y.colors.accent }" />
                                                        <span class="text-xs line-clamp-2" :style="{ color: a11y.colors.white80 }">{{ project.address }}</span>
                                                    </div>
                                                </div>

                                                <button
                                                    class="w-full py-2.5 rounded-lg font-semibold transition-all duration-200 hover:opacity-90 hover:scale-[1.02]"
                                                    :style="{ backgroundColor: '#35594d', color: a11y.colors.accent }"
                                                    @click="goToProject(project.id)"
                                                    :aria-label="`Подробнее о проекте ${project.title}`"
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
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>