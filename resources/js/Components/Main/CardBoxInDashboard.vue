<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import slider3 from "../../../images/LogoInvestProject.png";

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

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
    isFavorited: {
        type: [Boolean, Array],
        default: false
    }
});

const page = usePage();

// Роль пользователя
const userRole = computed(() => page.props.auth?.user?.role ?? null);
const user = computed(() => page.props.auth?.user ?? null);

// Проверка, находится ли проект в избранном
const checkIsFavorited = () => {
    if (!props.isFavorited) return false;
    if (Array.isArray(props.isFavorited)) {
        return props.isFavorited.some(fav => fav?.project_id === props.project?.id || fav?.id === props.project?.id);
    }
    return Boolean(props.isFavorited);
};

// Состояние избранного
const isFavorite = ref(checkIsFavorited());

// Переключение избранного
const toggleFavorite = async (event) => {
    event.preventDefault();
    event.stopPropagation();

    if (!user.value) {
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

// Состояние ошибки загрузки изображения
const imageError = ref(false);

// Обработчик ошибки загрузки
const handleImageError = () => {
    imageError.value = true;
};

// Проверка, является ли изображение заглушкой
const isPlaceholder = computed(() => {
    return currentImage.value === slider3 || imageError.value;
});

// Текущее изображение с обработкой ошибок
const currentImage = computed(() => {
    if (imageError.value) {
        return slider3;
    }
    
    if (props.project?.photos && props.project.photos.length > 0) {
        return `/storage/${props.project.photos[0].photo_path}`;
    }
    
    return slider3;
});

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
const selectedStatus = ref(props.project?.status || '');
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
        class="w-full h-[450px] sm:h-[500px] lg:h-[550px] flex flex-col rounded-2xl relative overflow-hidden transition-all duration-300 group "
        :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}` }"
    >
        <!-- Изображение проекта -->
        <div 
            @click="goToProject"
            class="flex-1 cursor-pointer flex items-center justify-center overflow-hidden rounded-t-xl"
            :style="{ backgroundColor: colors.bgImage }"
        >
            <img 
                :src="currentImage"
                @error="handleImageError"
                class="transition-transform duration-300 group-hover:scale-105"
                :class="isPlaceholder ? 'object-contain' : 'object-cover w-full h-full'"
                :style="isPlaceholder ? 'max-width: 100%; max-height: 100%; width: auto; height: auto;' : ''"
                alt="Проект"
                loading="lazy"
            >
        </div>
        
        <!-- Нижняя панель с информацией -->
        <div class="rounded-b-xl flex-shrink-0" :style="{ backgroundColor: colors.bgLight }">
            <div class="p-3 sm:p-4">
                <!-- Информация о проекте -->
                <div class="grid grid-cols-1 gap-x-4 gap-y-2 text-lg sm:text-xl lg:text-xl">
                    <p class="truncate ">
                        <span class="font-semibold" :style="{ color: colors.accent }">Название: </span> 
                        <span class="" :style="{ color: colors.white }">{{ project.title }}</span>
                    </p>
                    
                    <p v-if="project.number_date_realise" class="truncate ">
                        <span class="font-semibold" :style="{ color: colors.accent }">Срок: </span> 
                        <span class="" :style="{ color: colors.white }">{{ project.number_date_realise }} мес.</span>
                    </p>
                    
                    <p v-if="project.total_investment" class="truncate ">
                        <span class="font-semibold" :style="{ color: colors.accent }">Инвестиции: </span> 
                        <span class="" :style="{ color: colors.white }">{{ formatNumber(project.total_investment) }} ₽</span>
                    </p>
                    
                    <p v-if="project.type_build" class="truncate ">
                        <span class="font-semibold" :style="{ color: colors.accent }">Собственность: </span> 
                        <span class="" :style="{ color: colors.white }">{{ project.type_build }}</span>
                    </p>
                </div>

                <!-- Админ панель -->
                <div v-if="userRole === 'Admin'" class="flex flex-col sm:flex-row gap-2 mt-3 pt-3" :style="{ borderTop: `1px solid ${colors.border}` }">
                    <select 
                        v-model="selectedStatus"
                        @change="changeStatus"
                        class="px-2 py-1.5 rounded-lg border focus:outline-none text-sm"
                        :style="{ borderColor: colors.border, backgroundColor: colors.bgDark, color: colors.white }"
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

                    <div v-if="project.is_moderated === true" class="px-2 py-1.5 rounded-lg text-xs text-center whitespace-nowrap" style="background-color: #4caf50; color: white">
                        Прошла модерацию
                    </div>
                    <div v-else-if="project.is_moderated === false" class="px-2 py-1.5 rounded-lg text-xs text-center whitespace-nowrap" style="background-color: #f44336; color: white">
                        Не прошла модерацию
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
