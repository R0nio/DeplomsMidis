<script setup>
import { ref, computed } from 'vue';
import { router, usePage, Link } from '@inertiajs/vue3';
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

const projectImage = computed(() => {
    if (props.project.photos && props.project.photos.length > 0) {
        return `/storage/${props.project.photos[0].photo_path}`;
    }
    return slider3;
});

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

const statusSelectId = `status-${props.project.id}`;
</script>

<template>
    <article
        class="w-full h-[550px] flex flex-col justify-between bg-blue-300 rounded-xl relative overflow-hidden hover:shadow-xl transition-shadow duration-300"
        :aria-labelledby="`project-title-${project.id}`"
    >
        <Link
            :href="route('projects.show', project.id)"
            class="w-full h-full focus:outline-none focus-visible:ring-4 focus-visible:ring-inset focus-visible:ring-blue-700"
            :aria-label="`Подробнее о проекте: ${project.title}`"
        >
            <img
                :src="projectImage"
                class="object-cover w-full h-full rounded-xl hover:scale-105 transition-transform duration-300"
                :alt="`Изображение проекта: ${project.title}`"
            >
        </Link>

        <div class="w-full flex absolute top-2 left-2 right-2 z-10 justify-between">
            <div v-if="userRole === 'Investor'">
                <button
                    v-if="userRole !== 'Admin'"
                    type="button"
                    @click="toggleFavorite"
                    class="mr-8 cursor-pointer hover:scale-110 transition-transform focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700 rounded"
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
        </div>

        <div class="bg-[#267FBE] w-full text-white px-4 lg:px-6 py-5 rounded-xl absolute bottom-0 border-t-2 border-white shadow-lg backdrop-blur-sm">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm lg:text-xl mb-4">
                <div>
                    <dt class="font-semibold inline">Название:</dt>
                    <dd :id="`project-title-${project.id}`" class="inline">{{ project.title }}</dd>
                </div>
                <div v-if="project.number_date_realise">
                    <dt class="font-semibold inline">Срок реализации:</dt>
                    <dd class="inline">{{ project.number_date_realise }} мес.</dd>
                </div>
                <div v-if="project.total_investment">
                    <dt class="font-semibold inline">Инвестиции:</dt>
                    <dd class="inline">{{ formatNumber(project.total_investment) }} ₽</dd>
                </div>
                <div v-if="project.type_build">
                    <dt class="font-semibold inline">Собственность:</dt>
                    <dd class="inline">{{ project.type_build }}</dd>
                </div>
            </dl>

            <div v-if="userRole === 'Admin'" class="flex gap-3 flex-wrap mt-4 pt-4 border-t-2 border-white/30 justify-between">
                <div>
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

                <p v-if="project.is_moderated === true" class="bg-green-700 h-full p-3 rounded-xl text-2xl">
                    Прошла модерацию
                </p>
                <p v-else-if="project.is_moderated === false" class="bg-red-700 h-full p-3 rounded-xl text-2xl">
                    Не прошла модерацию
                </p>
            </div>
        </div>
    </article>
</template>
