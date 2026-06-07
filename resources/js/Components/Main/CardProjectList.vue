<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import slider3 from "../../../images/pictures/slider3.png";
import favoriteIcon from "../../../images/Favorite.png";
import favoriteActiveIcon from "../../../images/FavoriteActivity.png";

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
    isFavorited: {
        type: Boolean,
        default: false
    }
});

const colors = {
    bgPage: '#436343',
    bgLight: '#809076',
    bgDark: '#284139',
    bgImage: '#4a7a6a',
    accent: '#F8D794',
    border: '#886830',
    textLight: '#e8f0ee',
    textDark: '#111A19',
    white: '#ffffff',
    white80: 'rgba(255, 255, 255, 0.8)',
    white60: 'rgba(255, 255, 255, 0.6)',
};

const page = usePage();
const userRole = computed(() => page.props.auth.user?.role);
const isFavorite = ref(props.isFavorited);

// Получение изображения проекта
const getProjectImage = () => {
    if (props.project.photos && props.project.photos.length > 0) {
        const photoPath = props.project.photos[0].photo_path;
        return `/storage/${photoPath}`;
    }
    return slider3;
};

// Категории
const categories = computed(() => {
    if (!props.project.category) return [];
    try {
        const cats = Array.isArray(props.project.category)
            ? props.project.category
            : JSON.parse(props.project.category);
        return cats;
    } catch {
        return [];
    }
});

// Прогресс
const progress = computed(() => {
    const collected = Number(props.project.collected_total_investment) || 0;
    const total = Number(props.project.total_investment) || 1;
    return (collected / total) * 100;
});

const toggleFavorite = async (event) => {
    event.preventDefault();
    event.stopPropagation();

    if (!page.props.auth.user) {
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
        }
    } catch (error) {
        console.error('Ошибка:', error);
        isFavorite.value = previousState;
        if (error.response?.status === 401) {
            alert('Необходимо авторизоваться');
        }
    }
};

const formatNumber = (number) => {
    if (!number) return '0';
    return Number(number).toLocaleString('ru-RU');
};

const goToProject = () => {
    router.visit(route('projects.show', props.project.id));
};

// Для админки
const selectedStatus = ref(props.project.status);
const statusOptions = [
    { value: 'На модерации', label: 'На модерации' },
    { value: 'Одобрен', label: 'Одобрен' },
    { value: 'Отклонен', label: 'Отклонен' },
    { value: 'Заблокирован', label: 'Заблокирован' },
];

const changeStatus = async () => {
    if (!confirm('Изменить статус проекта?')) {
        selectedStatus.value = props.project.status;
        return;
    }

    try {
        await axios.patch(route('admin.projects.update-status', props.project.id), {
            status: selectedStatus.value
        });
        await router.reload();
    } catch (error) {
        console.error('Ошибка при изменении статуса:', error);
    }
};
</script>

<template>
    <div 
        class="w-full rounded-xl overflow-hidden transition-all duration-300 hover:shadow-xl cursor-pointer relative flex flex-row"
        :style="{ backgroundColor: colors.bgDark, border: `1px solid ${colors.border}` }"
        @click="goToProject"
        @keydown.enter="goToProject"
        @keydown.space.prevent="goToProject"
        role="button"
        tabindex="0"
        :aria-label="`Проект: ${project.title}`"
    >
        <!-- Левая часть - фотография -->
        <div class="relative w-28 sm:w-32 md:w-36 flex-shrink-0 overflow-hidden" :style="{ backgroundColor: colors.bgImage }">
            <img 
                :src="getProjectImage()" 
                :alt="`Изображение проекта ${project.title}`"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                style="height: 100%; min-height: 140px;"
                loading="lazy"
            >
        </div>

        <!-- Правая часть - контент -->
        <div class="flex-1 p-3 sm:p-4">
            <!-- Кнопка избранного в правом верхнем углу для инвестора -->
            <div v-if="userRole === 'Investor'" class="absolute top-2 right-2 z-20">
                <button 
                    @click.stop="toggleFavorite"
                    class="p-1.5 sm:p-2 rounded-full transition-all hover:scale-110 shadow-md"
                    :style="{ backgroundColor: colors.bgDark, border: `1px solid ${colors.border}` }"
                    :title="isFavorite ? 'Удалить из избранного' : 'Добавить в избранное'"
                    :aria-label="isFavorite ? `Удалить проект ${project.title} из избранного` : `Добавить проект ${project.title} в избранное`"
                >
                    <img 
                        :src="isFavorite ? favoriteActiveIcon : favoriteIcon" 
                        alt="" 
                        class="w-4 h-4 sm:w-5 sm:h-5"
                        aria-hidden="true"
                    >
                </button>
            </div>

            <!-- Панель администратора -->
            <div v-if="userRole === 'Admin'" class="absolute top-2 right-2 z-20 flex max-sm:flex-col items-center gap-2">
                <!-- Статус модерации -->
                <div 
                    v-if="project.is_moderated === true" 
                    class="px-2 py-1 rounded-lg text-xs text-center whitespace-nowrap shadow-md"
                    style="background-color: #4caf50; color: white"
                >
                    Прошла модерацию
                </div>
                <div 
                    v-else-if="project.is_moderated === false" 
                    class="px-2 py-1 rounded-lg text-xs text-center whitespace-nowrap shadow-md"
                    style="background-color: #f44336; color: white"
                >
                    Не прошла модерацию
                </div>
                <div 
                    v-else 
                    class="px-2 py-1 rounded-lg text-xs text-center whitespace-nowrap shadow-md"
                    :style="{ backgroundColor: colors.bgLight, color: colors.white }"
                >
                    Ожидает модерации
                </div>
                
                <!-- Выбор статуса -->
                <select 
                    v-model="selectedStatus"
                    @change="changeStatus"
                    class="px-2 py-1 rounded-lg text-xs focus:outline-none cursor-pointer shadow-md"
                    :style="{ backgroundColor: colors.bgLight, color: colors.white, border: `1px solid ${colors.border}` }"
                    @click.stop
                    aria-label="Изменить статус проекта"
                >
                    <option disabled value="">Статус</option>
                    <option 
                        v-for="option in statusOptions" 
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </option>
                </select>
            </div>

            <!-- Категории -->
            <div class="flex max-sm:flex-col max-sm:w-max flex-wrap gap-1 mb-2 pr-16">
                <span 
                    v-for="cat in categories.slice(0, 3)" 
                    :key="cat"
                    class="px-2 py-0.5 text-xs font-medium rounded-full"
                    :style="{ backgroundColor: colors.bgLight, color: colors.white }"
                >
                    {{ cat }}
                </span>
                <span 
                    v-if="categories.length > 3"
                    class="px-2 py-0.5 text-xs font-medium rounded-full"
                    :style="{ backgroundColor: colors.bgLight, color: colors.white }"
                >
                    +{{ categories.length - 3 }}
                </span>
            </div>

            <!-- Название -->
            <h3 class="font-semibold text-base sm:text-lg mb-1 break-words line-clamp-2 pr-12" :style="{ color: colors.white }">
                {{ project.title }}
            </h3>

            <!-- Адрес -->
            <p class="text-xs sm:text-sm mb-2 break-words line-clamp-1" :style="{ color: colors.white80 }">
                <svg class="w-3 h-3 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
                {{ project.address || 'Адрес не указан' }}
            </p>

            <!-- Показатели -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 text-sm mb-3">
                <div class="min-w-0">
                    <p class="text-xs" :style="{ color: colors.white80 }">Требуется</p>
                    <p class="font-semibold text-xs sm:text-sm truncate" :style="{ color: colors.white }">
                        {{ (Number(project.total_investment) / 1000000).toFixed(1) }} млн ₽
                    </p>
                </div>
                <div class="min-w-0">
                    <p class="text-xs" :style="{ color: colors.white80 }">Собрано</p>
                    <p class="font-semibold text-xs sm:text-sm" :style="{ color: colors.white }">
                        {{ Math.min(progress, 100).toFixed(0) }}%
                    </p>
                </div>
                <div class="min-w-0">
                    <p class="text-xs" :style="{ color: colors.white80 }">Срок</p>
                    <p class="font-semibold text-xs sm:text-sm truncate" :style="{ color: colors.white }">
                        {{ project.number_date_realise || '—' }} мес.
                    </p>
                </div>
                <div class="min-w-0">
                    <p class="text-xs" :style="{ color: colors.white80 }">Рабочих мест</p>
                    <p class="font-semibold text-xs sm:text-sm truncate" :style="{ color: colors.white }">
                        {{ project.count_new_job || '—' }}
                    </p>
                </div>
            </div>

            <!-- Прогресс бар -->
            <div class="w-full h-1.5 rounded-full overflow-hidden" :style="{ backgroundColor: colors.border }">
                <div 
                    class="h-full rounded-full transition-all"
                    :style="{ width: `${Math.min(progress, 100)}%`, backgroundColor: colors.accent }"
                    role="progressbar"
                    :aria-valuenow="Math.min(progress, 100)"
                    aria-valuemin="0"
                    aria-valuemax="100"
                ></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}
</style>