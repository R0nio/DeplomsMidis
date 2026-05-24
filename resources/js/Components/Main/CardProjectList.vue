<script setup>
import { ref, computed } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3';
import axios from 'axios';
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

const userRole = computed(() => page.props.auth.user?.role);

const isFavorite = ref(props.isFavorited);

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

const formatNumber = (number) => {
    return Number(number).toLocaleString('ru-RU');
};

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

    axios.patch(route('admin.projects.update-status', props.project.id), {
        status: selectedStatus.value
    });
    await router.reload();
};
const cardColor = '#5B83CD';
const statusSelectId = `status-list-${props.project.id}`;
</script>

<template>
    <article :style="{backgroundColor: cardColor}" class="flex flex-wrap bg-[#7390C5] p-3 justify-between w-full text-base lg:text-xl text-white rounded-xl items-center gap-3">
        <h3 class="min-w-[100px] m-0 text-base lg:text-xl font-normal">{{ project.title }}</h3>
        <p class="min-w-[120px]"><span class="sr-only">Инвестиции:</span>{{ formatNumber(project.total_investment) }} ₽</p>
        <p class="min-w-[120px]"><span class="sr-only">Срок реализации:</span>{{ project.number_date_realise }}</p>
        <p class="min-w-[120px]"><span class="sr-only">Тип:</span>{{ project.type_build }}</p>
        <p class="min-w-[120px]"><span class="sr-only">Сфера:</span>{{ project.activity }}</p>
        <Link
            :href="route('projects.show', project.id)"
            class="px-4 py-2 border-b-2 bg-[#7390C5] text-base lg:text-2xl rounded-xl hover:border-none hover:bg-[#267FBE] hover:text-black focus:bg-white focus:text-black focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white text-white"
            :aria-label="`Подробнее о проекте: ${project.title}`"
        >
            Смотреть подробно
        </Link>

        <div v-if="userRole === 'Investor'">
            <button
                v-if="userRole !== 'Admin'"
                type="button"
                @click="toggleFavorite"
                class="mr-8 cursor-pointer hover:scale-110 transition-transform focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white rounded"
                :aria-pressed="isFavorite ? 'true' : 'false'"
                :aria-label="isFavorite ? 'Удалить из избранного' : 'Добавить в избранное'"
            >
                <img
                    :src="isFavorite ? favoriteActiveIcon : favoriteIcon"
                    alt=""
                    aria-hidden="true"
                    class="w-8 h-8 lg:w-10 lg:h-10"
                >
            </button>
        </div>
        <div v-if="userRole === 'Admin'" class="flex gap-3 flex-wrap border-white/30">
            <p v-if="project.is_moderated === true" class="min-w-[120px] bg-green-700 px-7 h-full p-3 rounded-xl text-2xl">
                Прошла модерацию
            </p>
            <p v-else-if="project.is_moderated === false" class="min-w-[120px] bg-red-700 h-full p-3 rounded-xl text-2xl">
                Не прошла модерацию
            </p>
            <label :for="statusSelectId" class="sr-only">Изменить статус проекта</label>
            <select
                :id="statusSelectId"
                v-model="selectedStatus"
                @change="changeStatus"
                class="px-3 py-2 text-black rounded-lg border-2 border-white focus:border-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700 text-sm lg:text-base min-w-[180px]"
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
    </article>
</template>
