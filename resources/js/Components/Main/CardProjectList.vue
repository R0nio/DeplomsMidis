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

const page = usePage();

// Роль пользователя
const userRole = computed(() => page.props.auth.user?.role);

// Состояние избранного
const isFavorite = ref(props.isFavorited);

// Переключение избранного
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
        console.error('Ошибка при добавлении в избранное:', error);
        isFavorite.value = previousState;
        if (error.response?.status === 401) {
            alert('Необходимо авторизоваться');
        }
    }
};

// Форматирование чисел
const formatNumber = (number) => {
    if (!number) return '0';
    return Number(number).toLocaleString('ru-RU');
};

// Перейти к проекту
const goToProject = () => {
    router.visit(route('projects.show', props.project.id));
};

// Для админки
const selectedStatus = ref(props.project.status);

// Список статусов для админа
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
    <div class="grid grid-cols-6 place-items-center w-full rounded-xl border-2 border-[#886830] bg-[#809076]">
        <!-- Название -->
        <div class="w-[250px] p-4 flex-shrink-0">
            <p class="font-semibold text-[#F8D794]">{{ project.title }}</p>
        </div>
        
        <!-- Инвестиции -->
        <div class="w-[180px] p-4 flex-shrink-0">
            <p class="text-sm text-white/70">Инвестиции</p>
            <p class="font-semibold text-white">{{ formatNumber(project.total_investment) }} ₽</p>
        </div>
        
        <!-- Срок -->
        <div class="w-[120px] p-4 flex-shrink-0">
            <p class="text-sm text-white/70">Срок</p>
            <p class="font-semibold text-white">{{ project.number_date_realise || '—' }} мес.</p>
        </div>
        
        <!-- Собственность -->
        <div class="w-[160px] p-4 flex-shrink-0">
            <p class="text-sm text-white/70">Собственность</p>
            <p class="font-semibold text-white">{{ project.type_build || '—' }}</p>
        </div>
        
        <!-- Деятельность -->
        <div class="w-[160px] p-4 flex-shrink-0">
            <p class="text-sm text-white/70">Деятельность</p>
            <p class="font-semibold text-white">{{ project.activity || '—' }}</p>
        </div>

        <!-- Кнопка подробнее -->
        <div class="w-[160px] p-4 flex-shrink-0">
            <button 
                @click="goToProject"
                class="w-full px-4 py-2 rounded-xl font-medium text-center transition-opacity hover:opacity-80 bg-[#284139] text-[#F8D794] border-2 border-[#886830]"
            >
                Смотреть подробно
            </button>
        </div>
        
        <!-- Кнопка избранного для инвестора -->
        <div v-if="userRole === 'Investor'" class="w-[80px] p-4 flex-shrink-0 flex justify-center">
            <button 
                @click="toggleFavorite" 
                class="p-2 rounded-full transition-opacity hover:opacity-80 bg-[#284139] border-2 border-[#886830]"
                :title="isFavorite ? 'Удалить из избранного' : 'Добавить в избранное'"
            >
                <img 
                    :src="isFavorite ? favoriteActiveIcon : favoriteIcon" 
                    alt="Избранное" 
                    class="w-6 h-6"
                >
            </button>
        </div>
        
        <!-- Админ панель -->
        <div v-if="userRole === 'Admin'" class="flex items-center gap-2 p-4 flex-shrink-0">
            <!-- Статус модерации -->
            <div v-if="project.is_moderated === true" class="w-[160px] px-3 py-2 rounded-xl text-sm text-center bg-green-600 text-white">
                Прошла модерацию
            </div>
            <div v-else-if="project.is_moderated === false" class="w-[160px] px-3 py-2 rounded-xl text-sm text-center bg-red-600 text-white">
                Не прошла модерацию
            </div>
            <div v-else class="w-[160px] px-3 py-2 rounded-xl text-sm text-center bg-[#284139] text-white/80 border-2 border-[#886830]">
                Ожидает модерации
            </div>
            
            <!-- Выбор статуса -->
            <select 
                v-model="selectedStatus"
                @change="changeStatus"
                class="w-[160px] px-3 py-2 rounded-lg border-2 focus:outline-none cursor-pointer bg-[#284139] text-[#F8D794] border-[#886830]"
            >
                <option disabled value="">Изменить статус</option>
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
</template>