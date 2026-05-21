<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
import { GoogleMap, Marker, InfoWindow } from "vue3-google-map";
import SliderInDetailPage from "@/Components/Main/SliderInDetailPage.vue";
import BarChart from "@/Components/Main/BarChart.vue";
import axios from 'axios';
import RevenueExpensesChart from "@/Components/Main/RevenueExpensesChart.vue";
import CashFlowChart from "@/Components/Main/CashFlowChart.vue";
import InvestmentStructureChart from "@/Components/Main/InvestmentStructureChart.vue";
// Images
import sliderFallback from "../../images/free-icon-image-gallery-3342137.png";
import FavoriteIcon from "../../images/Favorite.png";
import FavoriteActiveIcon from "../../images/FavoriteActivity.png";

// Props
const props = defineProps({
    project: {
        type: Object,
        required: true
    },
    isFavorited: {
        type: Boolean,
        default: false
    }
});

const getProjectImage = (project) => {
    if (project.photos && project.photos.length > 0) {
        const photoPath = project.photos[0].photo_path;
        console.log('Photo path:', photoPath);
        return `/storage/${photoPath}`;
    }
    return defaultImage;
};

// Текущий пользователь (null если не авторизован)
const page = usePage();
const authUser = computed(() => page.props.auth?.user ?? null);

// Слайдер — реальные фото проекта или заглушка
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
    if (!number) return '—';
    return Number(number).toLocaleString('ru-RU');
};

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

// Избранное
const isFavorite = ref(props.isFavorited);
const favoriteLoading = ref(false);

const toggleFavorite = async () => {
    if (!authUser.value) {
        alert('Необходимо авторизоваться');
        return;
    }
    if (favoriteLoading.value) return;

    try {
        favoriteLoading.value = true;
        const response = await axios.post(route('favorites.toggle', props.project.id));
        if (response.data.success) {
            isFavorite.value = response.data.isFavorited;
        }
    } catch (error) {
        console.error('Ошибка при добавлении в избранное:', error);
        if (error.response?.status === 401) {
            alert('Необходимо авторизоваться');
        }
    } finally {
        favoriteLoading.value = false;
    }
};

// Карта
const api = "AIzaSyC6L3uzc8_Ug01uxrFdYhN_Eo5mIwyaPZY";

const hasCoordinates = computed(() => {
    return props.project.latitude && props.project.longitude;
});

const mapCenter = computed(() => {
    if (hasCoordinates.value) {
        return {
            lat: parseFloat(props.project.latitude),
            lng: parseFloat(props.project.longitude)
        };
    }
    return { lat: 55.160278, lng: 61.402457 };
});

const mapMarker = computed(() => ({
    id: props.project.id,
    name: props.project.title,
    desc: props.project.short_description,
    lat: parseFloat(props.project.latitude),
    lng: parseFloat(props.project.longitude),
    image: imagesSlider.value[0]?.src ?? sliderFallback
}));

const selectedProject = ref(null);

const selectProject = (project) => {
    if (selectedProject.value?.id === project.id) {
        selectedProject.value = null;
        setTimeout(() => { selectedProject.value = project; }, 0);
    } else {
        selectedProject.value = project;
    }
};

const closeMarker = () => {
    selectedProject.value = null;
};

// Прогноз (первый элемент из forecasts)
const forecast = computed(() => {
    if (props.project.forecasts && props.project.forecasts.length > 0) {
        return props.project.forecasts[0];
    }
    return null;
});

// Структура расходов
const investments = computed(() => props.project.investments ?? []);














// Срок в месяцах
const months = computed(() => Number(props.project.number_date_realise) || 12);

// Годовая прибыль
const annualProfit = computed(() => {
    return Number(forecast.value?.expected_revenue ?? 0) - Number(forecast.value?.expected_expenses ?? 0);
});

// Общая прибыль за весь срок
const totalProfit = computed(() => {
    const years = Math.ceil(months.value / 12);
    return annualProfit.value * years;
});

// Среднегодовая выручка
const avgAnnualRevenue = computed(() => Number(forecast.value?.expected_revenue ?? 0));

// Максимальная годовая прибыль (в данном случае = annualProfit)
const maxAnnualProfit = computed(() => annualProfit.value);

// Срок окупаемости расчётный
const paybackPeriod = computed(() => {
    if (annualProfit.value <= 0) return '—';
    const totalInv = Number(props.project.total_investment ?? 0);
    const years = totalInv / annualProfit.value;
    return years.toFixed(1) + ' лет';
});







</script>

<template>
    <Head :title="project.title" />

    <AuthenticatedLayout>
        <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16">

            <!-- Основной контент -->
            <div class="flex flex-col xl:flex-row border-b-2 border-white pb-6">

                <!-- Слайдер -->
                <div class="w-full xl:w-8/12 h-[400px] sm:h-[500px] xl:h-[600px] mb-8">
                    <SliderInDetailPage :photos="imagesSlider" />
                </div>

                <!-- Информация о проекте -->
                <div class="flex flex-col w-full xl:w-4/12 max-xl:mt-32 max-xl:border-t-2 max-xl:pt-4 px-4 sm:px-6 xl:ml-8 xl:border-l-2 border-white mt-6 xl:mt-0">

                    <!-- Заголовок + Избранное -->
                    <div class="flex items-start justify-between w-full gap-4 mb-6">
                        <h1 class="font-bold text-2xl sm:text-3xl flex-1 text-white">
                            {{ project.title }}
                        </h1>

                        <!-- Кнопка избранного — только для авторизованных -->
                         <div v-if="props.user === 'Investor'">
                            <button
                                v-if="authUser"
                                @click="toggleFavorite"
                                :disabled="favoriteLoading"
                                class="flex-shrink-0 cursor-pointer hover:scale-110 transition-transform disabled:opacity-50"
                                :title="isFavorite ? 'Убрать из избранного' : 'Добавить в избранное'"
                            >
                                <img
                                    :src="isFavorite ? FavoriteActiveIcon : FavoriteIcon"
                                    alt="Избранное"
                                    class="w-8 h-8 sm:w-10 sm:h-10"
                                >
                            </button>
                        </div>
                    </div>

                    <!-- Категории -->
                    <div v-if="categories.length > 0" class="flex flex-wrap gap-2 mb-4">
                        <span
                            v-for="(cat, i) in categories"
                            :key="i"
                            class="bg-white/20 text-white text-sm px-3 py-1 rounded-full border border-white/40"
                        >
                            {{ cat }}
                        </span>
                    </div>

                    <!-- Детали -->
                    <div class="flex flex-col gap-3 text-white">
                        <p class="text-lg sm:text-xl lg:text-2xl" v-if="project.number_date_realise">
                            <span class="font-semibold">Срок реализации:</span> {{ project.number_date_realise }} мес.
                        </p>
                        <p class="text-lg sm:text-xl lg:text-2xl" v-if="project.ownership">
                            <span class="font-semibold">Собственность:</span> {{ project.ownership }}
                        </p>
                        <p class="text-lg sm:text-xl lg:text-2xl" v-if="project.total_investment">
                            <span class="font-semibold">Сумма инвестиций:</span> {{ formatNumber(project.total_investment) }} ₽
                        </p>
                        <p class="text-lg sm:text-xl lg:text-2xl" v-if="project.user">
                            <span class="font-semibold">Организатор:</span> {{ project.user.name }}
                        </p>
                        <p class="text-lg sm:text-xl lg:text-2xl" v-if="project.activity">
                            <span class="font-semibold">Деятельность:</span> {{ project.activity }}
                        </p>
                        <p class="text-lg sm:text-xl lg:text-2xl" v-if="project.address">
                            <span class="font-semibold">Расположение:</span> {{ project.address }}
                        </p>
                        <p class="text-lg sm:text-xl lg:text-2xl" v-if="project.type_build">
                            <span class="font-semibold">Вид строительства:</span> {{ project.type_build }}
                        </p>
                        <p class="text-lg sm:text-xl lg:text-2xl" v-if="project.count_new_job">
                            <span class="font-semibold">Новых рабочих мест:</span> {{ formatNumber(project.count_new_job) }}
                        </p>
                        <p class="text-lg sm:text-xl lg:text-2xl" v-if="project.collected_total_investment">
                            <span class="font-semibold">Собрано средств:</span> {{ formatNumber(project.collected_total_investment) }} ₽
                        </p>
                    </div>
                </div>
            </div>

            <!-- Описание -->
            <div class="flex flex-col border-b-2 border-white pb-6 mt-8">
                <h2 class="text-2xl sm:text-3xl mb-6 text-white font-semibold">Описание</h2>
                <p class="text-lg sm:text-xl lg:text-2xl whitespace-pre-line leading-normal text-white">
                    {{ project.full_description || project.short_description }}
                </p>
            </div>

             <!-- Графики -->
            <div class="my-8">
                <h2 class="text-2xl sm:text-3xl mb-6 text-white     ">Графики выгодности</h2>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">
                    <!-- График 1 -->
                    <div class="border-2 border-white w-full h-[400px] sm:h-[500px] lg:h-[500px] rounded-xl p-4 sm:p-6">
                        <RevenueExpensesChart
                            :expected-revenue="forecast?.expected_revenue ?? 0"
                            :expected-expenses="forecast?.expected_expenses ?? 0"
                            :months="months"
                        />
                    </div>
                    <!-- График 2 -->
                    <div class="border-2 border-white w-full h-[400px] sm:h-[500px] lg:h-[500px] rounded-xl p-4 sm:p-6">
                        <CashFlowChart
                            :expected-revenue="forecast?.expected_revenue ?? 0"
                            :expected-expenses="forecast?.expected_expenses ?? 0"
                            :months="months"
                        />
                    </div>
                     <!-- График 3 -->
                    <div class="border-2 border-white w-full h-[400px] sm:h-[500px] lg:h-[500px] rounded-xl p-4 sm:p-6">
                        <InvestmentStructureChart :investments="investments" />
                    </div>

                    <!-- Индикаторы -->
                    <div class="w-full h-[400px] sm:h-[500px] lg:h-[500px] grid grid-cols-2 gap-4">
                        <!-- Срок окупаемости -->
                        <div class="border-2 border-white rounded-xl flex flex-col items-center justify-center text-white p-4 gap-2">

                            <p class="text-sm sm:text-base text-white0 text-center">Срок окупаемости</p>
                            <p class="text-xl sm:text-2xl  text-white text-center">{{ paybackPeriod }}</p>
                        </div>

                        <!-- Общая прибыль -->
                        <div class="border-2 border-white rounded-xl flex flex-col items-center justify-center text-white p-4 gap-2">

                            <p class="text-sm sm:text-base text-white text-center">Общая прибыль за весь срок</p>
                            <p class="text-xl sm:text-2xl  text-white text-center">{{ formatNumber(totalProfit) }} ₽</p>
                        </div>

                        <!-- Среднегодовая выручка -->
                        <div class="border-2 border-white rounded-xl flex flex-col items-center justify-center text-white p-4 gap-2">

                            <p class="text-sm sm:text-base text-white text-center">Среднегодовая выручка</p>
                            <p class="text-xl sm:text-2xl d text-white text-center">{{ formatNumber(avgAnnualRevenue) }} ₽</p>
                        </div>

                        <!-- Максимальная годовая прибыль -->
                        <div class="border-2 border-white rounded-xl flex flex-col items-center justify-center text-white p-4 gap-2">

                            <p class="text-sm sm:text-base text-white text-center">Максимальная годовая прибыль</p>
                            <p class="text-xl sm:text-2xl text-white text-center">{{ formatNumber(maxAnnualProfit) }} ₽</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Карта -->
            <div class="my-8 pt-8 border-t-2 border-white">
                <h2 class="text-2xl sm:text-3xl mb-4 text-white font-semibold">Место расположения</h2>

                <!-- Нет координат -->
                <div
                    v-if="!hasCoordinates"
                    class="border-2 border-white/30 rounded-xl p-8 text-center text-white/60 text-xl"
                >
                    Координаты для данного проекта не указаны
                </div>

                <!-- Карта с маркером -->
                <div v-else>
                    <GoogleMap
                        :api-key="api"
                        class="w-full h-[500px] sm:h-[700px] rounded-xl"
                        :center="mapCenter"
                        :zoom="15"
                        @click="closeMarker"
                    >
                        <Marker
                            :options="{ position: { lat: mapMarker.lat, lng: mapMarker.lng } }"
                            @click="selectProject(mapMarker)"
                        />

                        <InfoWindow
                            v-if="project"
                            :options="{ 
                                position: { 
                                    lat: parseFloat(project.latitude), 
                                    lng: parseFloat(project.longitude) 
                                } 
                            }"
                        >
                            <div
                                class="w-64 sm:w-72 bg-[#8EB6FF] rounded-lg overflow-hidden shadow-xl cursor-pointer hover:shadow-2xl transition-shadow duration-300 p-3"
                                @click="goToProject(project.id)"
                            >
                                <!-- Изображение -->
                                <div class="relative h-40">
                                    <img
                                        v-if="project.photos && project.photos.length > 0"
                                        :src="getProjectImage(project)"
                                        :alt="project.title"
                                        class="w-full h-full object-cover rounded-xl"
                                    />
                                    <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Информация -->
                                <div class="p-4">
                                    <h3 class="text-lg font-bold text-white mb-1">
                                        {{ project.title }}
                                    </h3>
                                    
                                    <p class="text-sm text-white mb-1">
                                        {{ project.short_description }}
                                    </p>

                                    <!-- Детали -->
                                    <div class="space-y-2 text-sm mb-3">
                                        <div v-if="project.total_investment" class="flex items-center text-white">
                                            <span class="font-semibold">{{ formatNumber(project.total_investment) }} ₽</span>
                                        </div>

                                        <div v-if="project.address" class="flex items-start text-white">
                                            <span class="text-xs line-clamp-2">{{ project.address }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </InfoWindow>
                    </GoogleMap>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>