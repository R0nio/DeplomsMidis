<script setup>
import { ref, computed, reactive, watch } from 'vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { GoogleMap, Marker, InfoWindow } from "vue3-google-map";
import SliderInDetailPage from "@/Components/Main/SliderInDetailPage.vue";
import axios from 'axios';
import { router } from '@inertiajs/vue3';
// Images
import sliderFallback from "../../images/LogoInvestProject.png";
import FavoriteIcon from "../../images/Favorite.png";
import FavoriteActiveIcon from "../../images/FavoriteActivity.png";
import defaultImage from "../../images/LogoInvestProject.png";

// Props
const props = defineProps({
    project: {
        type: Object,
        required: true
    },
    // Теперь isFavorited может быть Boolean или Array
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


// Функция для проверки, находится ли проект в избранном
const checkIsFavorited = () => {
    // Если isFavorited - массив (список избранных проектов)
    if (Array.isArray(props.isFavorited)) {
        return props.isFavorited.some(fav => fav.project_id === props.project.id || fav.id === props.project.id);
    }
    // Если isFavorited - булево значение
    return Boolean(props.isFavorited);
};

// Инициализируем состояние
const isFavorite = ref(checkIsFavorited());

// Следим за изменением props.isFavorited
watch(() => props.isFavorited, () => {
    console.log('props.isFavorited изменился');
    isFavorite.value = checkIsFavorited();
}, { deep: true, immediate: true });

// Следим за изменением ID проекта
watch(() => props.project.id, () => {
    console.log('ID проекта изменился');
    isFavorite.value = checkIsFavorited();
});

// Переключение избранного
const toggleFavorite = async (event) => {
    event.preventDefault();
    event.stopPropagation();

    if (!authUser.value) {
        alert('Необходимо авторизоваться');
        return;
    }

    // Сохраняем текущее состояние для отката при ошибке
    const previousState = isFavorite.value;
    
    // Оптимистичное обновление UI
    isFavorite.value = !isFavorite.value;

    try {
        console.log('Отправка запроса для проекта:', props.project.id);
        const response = await axios.post(route('favorites.toggle', props.project.id));
        
        console.log('Ответ сервера:', response.data);
        
        if (response.data.success) {
            // Обновляем состояние из ответа сервера
            isFavorite.value = response.data.isFavorited;
            console.log('Новое состояние избранного:', response.data.isFavorited);
            
            // Если нужно обновить родительский компонент, можно вызвать событие
            // emit('update:favorited', response.data.isFavorited);
        } else {
            // Если сервер вернул ошибку, возвращаем предыдущее состояние
            isFavorite.value = previousState;
            alert('Не удалось изменить статус избранного');
        }
    } catch (error) {
        console.error('Ошибка при добавлении в избранное:', error);
        // Возвращаем предыдущее состояние при ошибке
        isFavorite.value = previousState;
        
        if (error.response?.status === 401) {
            alert('Необходимо авторизоваться');
        } else {
            alert('Произошла ошибка. Попробуйте позже.');
        }
    }
};


// Карта
// const api = "AIzaSyC6L3uzc8_Ug01uxrFdYhN_Eo5mIwyaPZY";
const api = "AIzaSyDfKb4UVvQwK3cANRf-7EVCLlGy0fi2yno";

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

// Маркер проекта
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
    status: props.project.status
}));

// Выбранный проект для инфоокна
const selectedProject = ref(null);

const selectProject = (marker) => {
    if (selectedProject.value?.id === marker.id) {
        selectedProject.value = null;
        setTimeout(() => {
            selectedProject.value = props.project;
        }, 0);
    } else {
        selectedProject.value = props.project;
    }
};

const closeMarker = () => {
    selectedProject.value = null;
};

const goToProject = (id) => {
    router.visit(route('projects.show', id));
};

const colors = reactive({
    bgPage: '#436343',
    bgLight: '#809076',
    bgDark: '#284139',
    cardBg: '#4A7A6A',
    accent: '#F8D794',
    border: '#886830',
    textLight: '#e8f0ee',
    textDark: '#111A19',
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="min-h-screen" :style="{ backgroundColor: colors.bgPage }">
            <!-- секция со слайдером и информацией -->
            <div class="relative">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
                    <div class="flex flex-col lg:flex-row lg:gap-6">
                        
                        <!-- Слайдер -->
                        <div class="w-full lg:w-3/5 mb-6 lg:mb-0">
                            <div class="overflow-hidden" :style="{ border: `2px solid ${colors.border}`, borderRadius: '16px', backgroundColor: colors.bgDark }">
                                <SliderInDetailPage :photos="imagesSlider" />
                            </div>
                        </div>

                        <!-- Информация -->
                        <div class="w-full lg:w-2/5">
                            <div class="h-full flex flex-col" :style="{ border: `2px solid ${colors.border}`, borderRadius: '16px', padding: '20px', backgroundColor: colors.bgLight }">
                                
                                <!-- Заголовок -->
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <h1 class="text-2xl sm:text-3xl font-medium leading-tight flex-1" :style="{ color: colors.accent }">
                                        {{ project.title }}
                                    </h1>
                                    
                                    <!-- Кнопка избранного только для инвесторов -->
                                    <div v-if="userRole === 'Investor'">
                                        <button 
                                            @click="toggleFavorite" 
                                            class="cursor-pointer hover:scale-110 transition-transform "
                                            :title="isFavorite ? 'Удалить из избранного' : 'Добавить в избранное'"
                                        >
                                            <img 
                                                :src="isFavorite ? FavoriteActiveIcon : FavoriteIcon" 
                                                alt="Избранное" 
                                                class="w-10 h-10"
                                            >
                                        </button>
                                    </div>
                                </div>

                                <!-- Для отладки - можно удалить после проверки -->
                                <!-- <div class="text-xs text-white mb-2">В избранном: {{ isFavorite ? 'Да' : 'Нет' }}</div> -->

                                <!-- Категории -->
                                <div v-if="categories.length > 0" class="flex flex-wrap gap-2 mb-5">
                                    <span
                                        v-for="(cat, i) in categories"
                                        :key="i"
                                        class="text-white text-xs sm:text-sm px-3 py-1"
                                        :style="{ backgroundColor: colors.bgDark, borderRadius: '20px' }"
                                    >
                                        {{ cat }}
                                    </span>
                                </div>

                                <!-- Ключевые показатели -->
                                <div class="grid grid-cols-2 gap-3 mb-5">
                                    <div :style="{ backgroundColor: colors.bgDark, borderRadius: '12px', padding: '10px', textAlign: 'center' }">
                                        <p class="text-white/70 text-xs mb-1">Инвестиции</p>
                                        <p class="text-white font-medium text-base">{{ formatNumber(project.total_investment) }} ₽</p>
                                    </div>
                                    <div :style="{ backgroundColor: colors.bgDark, borderRadius: '12px', padding: '10px', textAlign: 'center' }">
                                        <p class="text-white/70 text-xs mb-1">Срок</p>
                                        <p class="text-white font-medium text-base">{{ project.number_date_realise }} мес.</p>
                                    </div>
                                    <div v-if="project.collected_total_investment" :style="{ backgroundColor: colors.bgDark, borderRadius: '12px', padding: '10px', textAlign: 'center' }">
                                        <p class="text-white/70 text-xs mb-1">Собрано</p>
                                        <p class="text-white font-medium text-base">{{ formatNumber(project.collected_total_investment) }} ₽</p>
                                    </div>
                                    <div v-if="project.count_new_job" :style="{ backgroundColor: colors.bgDark, borderRadius: '12px', padding: '10px', textAlign: 'center' }">
                                        <p class="text-white/70 text-xs mb-1">Рабочие места</p>
                                        <p class="text-white font-medium text-base">{{ project.count_new_job }}</p>
                                    </div>
                                </div>

                                <!-- Детали -->
                                <div class="space-y-2 flex-1">
                                    <div v-if="project.ownership" class="flex flex-wrap gap-2">
                                        <span :style="{ color: colors.accent, minWidth: '110px' }">Собственность:</span>
                                        <span class="text-white">{{ project.ownership }}</span>
                                    </div>
                                    <div v-if="project.type_build" class="flex flex-wrap gap-2">
                                        <span :style="{ color: colors.accent, minWidth: '110px' }">Вид строительства:</span>
                                        <span class="text-white">{{ project.type_build }}</span>
                                    </div>
                                    <div v-if="project.activity" class="flex flex-wrap gap-2">
                                        <span :style="{ color: colors.accent, minWidth: '110px' }">Деятельность:</span>
                                        <span class="text-white">{{ project.activity }}</span>
                                    </div>
                                    <div v-if="project.user" class="flex flex-wrap gap-2">
                                        <span :style="{ color: colors.accent, minWidth: '110px' }">Организатор:</span>
                                        <span class="text-white">{{ project.user.name }}</span>
                                    </div>
                                    <div v-if="project.address" class="flex flex-wrap gap-2">
                                        <span :style="{ color: colors.accent, minWidth: '110px' }">Адрес:</span>
                                        <span class="text-white">{{ project.address }}</span>
                                    </div>
                                </div>

                                <!-- Прогресс-бар -->
                                <div v-if="project.total_investment && project.collected_total_investment" class="mt-4 pt-3">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span :style="{ color: colors.accent }">Собрано средств</span>
                                        <span :style="{ color: colors.accent }">{{ Math.round((project.collected_total_investment / project.total_investment) * 100) }}%</span>
                                    </div>
                                    <div :style="{ backgroundColor: colors.border, borderRadius: '4px', height: '12px', overflow: 'hidden' }">
                                        <div 
                                            :style="{ backgroundColor: colors.accent, height: '100%', borderRadius: '4px', transition: 'width 0.3s', width: `${Math.min((project.collected_total_investment / project.total_investment) * 100, 100)}%` }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Остальной контент (описание, детали, карта) -->
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <!-- Описание -->
                <div class="mb-6" :style="{ border: `2px solid ${colors.border}`, borderRadius: '16px', padding: '20px', backgroundColor: colors.bgLight }">
                    <h2 class="text-xl font-medium mb-3" :style="{ color: colors.accent }">Описание</h2>
                    <p class="text-white leading-relaxed text-base">
                        {{ project.full_description || project.short_description }}
                    </p>
                </div>

                <!-- Детальная информация -->
                <div class="mb-6" :style="{ border: `2px solid ${colors.border}`, borderRadius: '16px', padding: '20px', backgroundColor: colors.bgLight }">
                    <h2 class="text-xl font-medium mb-4" :style="{ color: colors.accent }">Детали проекта</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm" :style="{ color: colors.accent }">Название</p>
                            <p class="text-white">{{ project.title }}</p>
                        </div>
                        <div v-if="categories.length">
                            <p class="text-sm" :style="{ color: colors.accent }">Категории</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                <span v-for="(cat, i) in categories" :key="i" class="text-white text-sm px-2 py-0.5" :style="{ backgroundColor: colors.bgDark, borderRadius: '12px' }">
                                    {{ cat }}
                                </span>
                            </div>
                        </div>
                        <div v-if="project.number_date_realise">
                            <p class="text-sm" :style="{ color: colors.accent }">Срок реализации</p>
                            <p class="text-white">{{ project.number_date_realise }} месяцев</p>
                        </div>
                        <div v-if="project.total_investment">
                            <p class="text-sm" :style="{ color: colors.accent }">Общая сумма инвестиций</p>
                            <p class="text-white">{{ formatNumber(project.total_investment) }} ₽</p>
                        </div>
                        <div v-if="project.collected_total_investment">
                            <p class="text-sm" :style="{ color: colors.accent }">Собрано инвестиций</p>
                            <p class="text-white">{{ formatNumber(project.collected_total_investment) }} ₽</p>
                        </div>
                        <div v-if="project.type_build">
                            <p class="text-sm" :style="{ color: colors.accent }">Тип строительства</p>
                            <p class="text-white">{{ project.type_build }}</p>
                        </div>
                        <div v-if="project.ownership">
                            <p class="text-sm" :style="{ color: colors.accent }">Форма собственности</p>
                            <p class="text-white">{{ project.ownership }}</p>
                        </div>
                        <div v-if="project.activity">
                            <p class="text-sm" :style="{ color: colors.accent }">Вид деятельности</p>
                            <p class="text-white">{{ project.activity }}</p>
                        </div>
                        <div v-if="project.count_new_job">
                            <p class="text-sm" :style="{ color: colors.accent }">Новые рабочие места</p>
                            <p class="text-white">{{ project.count_new_job }}</p>
                        </div>
                        <div v-if="project.address" class="md:col-span-2">
                            <p class="text-sm" :style="{ color: colors.accent }">Адрес</p>
                            <p class="text-white">{{ project.address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Карта -->
                <div v-if="hasCoordinates" class="rounded-xl overflow-hidden" :style="{ border: `2px solid ${colors.border}`, backgroundColor: colors.bgLight }">
                    <div class="p-4 sm:p-5">
                        <h2 class="text-xl font-medium mb-4" :style="{ color: colors.accent }">Расположение</h2>
                        
                        <div class="w-full h-[400px] sm:h-[500px] rounded-xl overflow-hidden" :style="{ border: `2px solid ${colors.border}` }">
                            <GoogleMap
                                :api-key="api"
                                class="w-full h-full"
                                :center="mapCenter"
                                :zoom="15"
                                @click="closeMarker"
                            >
                                <Marker
                                    :options="{ 
                                        position: projectMarker.position,
                                        title: projectMarker.title
                                    }"
                                    @click="selectProject(projectMarker)"
                                />

                                <InfoWindow
                                    v-if="selectedProject"
                                    :options="{ 
                                        position: { 
                                            lat: parseFloat(selectedProject.latitude), 
                                            lng: parseFloat(selectedProject.longitude) 
                                        } 
                                    }"
                                >
                                    <!-- Меняем backgroundColor с colors.bgDark на colors.cardBg -->
                                    <div
                                        class="w-64 sm:w-72 rounded-xl overflow-hidden cursor-pointer transition-all duration-300 hover:scale-[1.02]"
                                        :style="{ backgroundColor: colors.cardBg, border: `2px solid ${colors.border}` }"
                                        @click="goToProject(selectedProject.id)"
                                    >
                                        <div class="relative h-40" :style="{ backgroundColor: '#4a7a6a' }">
                                            <img
                                                v-if="selectedProject.photos && selectedProject.photos.length > 0"
                                                :src="getProjectImage(selectedProject)"
                                                :alt="selectedProject.title"
                                                class="w-full h-full object-cover"
                                            />
                                            <div v-else class="w-full h-full flex items-center justify-center" :style="{ backgroundColor: colors.border }">
                                                <svg class="w-16 h-16" :style="{ color: colors.accent }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="p-4">
                                            <h3 class="text-lg font-bold mb-1" :style="{ color: colors.accent }">
                                                {{ selectedProject.title }}
                                            </h3>
                                            
                                            <p class="text-sm mb-2 line-clamp-2" :style="{ color: colors.white80 }">
                                                {{ selectedProject.short_description?.slice(0, 80) }}...
                                            </p>

                                            <div class="space-y-2 text-sm mb-3">
                                                <div v-if="selectedProject.total_investment" class="flex items-center">
                                                    <span class="font-semibold" :style="{ color: colors.accent }">{{ formatNumber(selectedProject.total_investment) }} ₽</span>
                                                </div>

                                                <div v-if="selectedProject.address" class="flex items-start">
                                                    <span class="text-xs line-clamp-2" :style="{ color: colors.white80 }">{{ selectedProject.address }}</span>
                                                </div>
                                            </div>

                                            <div class="pt-3" :style="{ borderTop: `1px solid ${colors.border}` }">
                                                <button 
                                                    class="w-full font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 hover:opacity-90"
                                                    :style="{ backgroundColor: colors.buttonBg, color: colors.accent }"
                                                >
                                                    <span>Подробнее</span>
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>