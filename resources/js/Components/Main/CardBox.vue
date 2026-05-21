<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import slider3 from "../../../images/free-icon-image-gallery-3342137.png";
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

// Получить первое фото проекта или заглушку
const projectImage = computed(() => {
    if (props.project.photos && props.project.photos.length > 0) {
        return `/storage/${props.project.photos[0].photo_path}`;
    }
    return slider3;
});

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
</script>

<template>
    <div class="w-full h-[550px] flex flex-col justify-between bg-blue-300 rounded-xl relative overflow-hidden hover:shadow-xl transition-shadow duration-300">
        <!-- Изображение проекта -->
        <div 
            @click="goToProject"
            class="w-full h-full cursor-pointer"
        >
            <img 
                :src="projectImage" 
                class="object-cover w-full h-full rounded-xl hover:scale-105 transition-transform duration-300" 
                alt="Проект"
            >
        </div>
        
        <!-- Верхняя панель (статус и избранное) -->
        <div class="w-full flex absolute top-2 left-2 right-2 z-10 justify-between">
            <!-- Статус -->
            <!-- <div class="font-normal text-xl lg:text-3xl p-2.5 bg-[#7390C5] text-white rounded-xl flex items-center h-min border-b-2 border-r-2 border-white shadow-lg">
                <p>{{ project.status }}</p>
            </div> -->
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
        </div>
        
        <!-- Нижняя панель с информацией -->
        <div class="bg-[#267FBE] w-full text-white px-4 lg:px-6 py-5 rounded-xl absolute bottom-0 border-t-2 border-white shadow-lg backdrop-blur-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm lg:text-xl mb-4">
                <p><span class="font-semibold">Название:</span> {{ project.title }}</p>
                <p v-if="project.number_date_realise">
                    <span class="font-semibold">Срок реализации:</span> {{ project.number_date_realise }} мес.
                </p>
                <p v-if="project.total_investment">
                    <span class="font-semibold">Инвестиции:</span> {{ formatNumber(project.total_investment) }} ₽
                </p>
                <p v-if="project.type_build">
                    <span class="font-semibold">Собственность:</span> {{ project.type_build }}
                </p>
            </div>

            <!-- Админ панель -->
            <div v-if="userRole === 'Admin'" class="flex gap-3 flex-wrap mt-4 pt-4 border-t-2 border-white/30 justify-between">
                <!-- Изменение статуса -->
                <!-- Выбор статуса -->
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

                <div v-if="project.is_moderated === true">
                    <h1 class="bg-green-600 h-full p-3 rounded-xl text-2xl">
                        Прошла модерацию
                    </h1>
                </div>
                <div v-else-if="project.is_moderated === false">
                    <h1 class="bg-red-600 h-full p-3 rounded-xl text-2xl">
                        Не прошла модерацию
                    </h1>
                </div>
            </div>
        </div>
    </div>
</template>