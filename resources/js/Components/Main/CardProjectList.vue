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

    try {
        const response = await axios.post(route('favorites.toggle', props.project.id));
        
        if (response.data.success) {
            isFavorite.value = response.data.isFavorited;
        }
    } catch (error) {
        console.error('Ошибка при добавлении в избранное:', error);
        if (error.response?.status === 401) {
            alert('Необходимо авторизоваться');
        }
    }
};

// Форматирование чисел
const formatNumber = (number) => {
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

    axios.patch(route('admin.projects.update-status', props.project.id), {
        status: selectedStatus.value
    });
    //alert('Обновление успешно завершено!');
    await router.reload();
};
const cardColor = '#5B83CD';
</script>

<template>
    <div :style="{backgroundColor: cardColor}" class="flex flex-wrap bg-[#7390C5] p-3 justify-between w-full text-base lg:text-xl text-white rounded-xl items-center gap-3">
        <p class="min-w-[100px]">{{ project.title }}</p>
        <p class="min-w-[120px]">{{ formatNumber(project.total_investment) }} ₽</p>
        <p class="min-w-[120px]">{{ project.number_date_realise }}</p>
        <p class="min-w-[120px]">{{ project.type_build }}</p>
        <p class="min-w-[120px]">{{ project.activity }}</p>
        <a :href="project.link" class="px-4 py-2 border-b-2 bg-[#7390C5] text-base lg:text-2xl rounded-xl hover:border-none hover:bg-[#267FBE] hover:text-black focus:bg-white focus:text-black">
            Смотреть подробно
        </a>
        
        <div v-if="userRole === 'Investor'">

            <!-- Кнопка избранного (если не админ) -->
            <button 
                v-if="userRole !== 'Admin'"
                @click="toggleFavorite" 
                class="mr-8 cursor-pointer hover:scale-110 transition-transform "
                :title="isFavorite ? 'Удалить из избранного' : 'Добавить в избранное'"
            >
                <img 
                    :src="isFavorite ? favoriteActiveIcon : favoriteIcon" 
                    alt="Избранное" 
                    class="w-8 h-8 lg:w-10 lg:h-10"
                >
            </button>
        
        </div>
        <!-- Админ панель -->
            <div v-if="userRole === 'Admin'" class="flex gap-3 flex-wrap border-white/30">
                <!-- Изменение статуса -->
                <!-- Выбор статуса -->
                <div v-if="project.is_moderated === true" class="min-w-[120px]">
                <h1 class="bg-green-600 px-7 h-full p-3 rounded-xl text-2xl">
                    Прошла модерацию
                </h1>
            </div>
            <div v-else-if="project.is_moderated === false" class="min-w-[120px]">
                <h1 class="bg-red-600  h-full p-3 rounded-xl text-2xl">
                    Не прошла модерацию
                </h1>
            </div>
                <select 
                    v-model="selectedStatus"
                    @change="changeStatus"
                    class="px-3 py-2 text-black rounded-lg border-2 border-white focus:border-blue-500 focus:outline-none text-sm lg:text-base min-w-[180px]"
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