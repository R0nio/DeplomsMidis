<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import axios from 'axios';
import slider3 from "../../../images/free-icon-image-gallery-3342137.png";
import favoriteActiveIcon from "../../../images/FavoriteActivity.png";

const props = defineProps({
    project: {
        type: Object,
        required: true,
    }
});

const isFavorite = ref(true);

const toggleFavorite = async (event) => {
    event.preventDefault();
    event.stopPropagation();

    if (!confirm('Удалить проект из избранного?')) {
        return;
    }

    try {
        const response = await axios.post(route('favorites.toggle', props.project.id));

        if (response.data.success) {
            router.reload();
        }
    } catch (error) {
        console.error('Ошибка при удалении из избранного:', error);
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
</script>

<template>
    <article
        class="w-full h-[450px] lg:h-[500px] flex flex-col justify-between bg-blue-300 rounded-xl relative overflow-hidden transition-shadow duration-300"
        :aria-labelledby="`fav-project-title-${project.id}`"
    >
        <Link
            :href="route('projects.show', project.id)"
            class="w-full h-full focus:outline-none focus-visible:ring-4 focus-visible:ring-inset focus-visible:ring-blue-700"
            :aria-label="`Подробнее о проекте: ${project.title}`"
        >
            <img
                :src="projectImage"
                :alt="`Изображение проекта: ${project.title}`"
                class="w-full h-full object-cover rounded-xl hover:scale-105 transition-transform duration-300"
            >
        </Link>

        <div class="w-full flex absolute top-2 left-2 right-2 z-10">
            <p class="font-normal text-xl lg:text-3xl p-2.5 bg-[#7390C5] text-white mr-auto rounded-xl flex items-center h-min border-b-2 border-r-2 border-white">
                <span class="sr-only">Статус проекта:</span>{{ project.status }}
            </p>

            <button
                type="button"
                @click="toggleFavorite"
                class="mt-2 mr-8 cursor-pointer hover:scale-110 transition-transform p-2 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700"
                aria-label="Удалить из избранного"
            >
                <img
                    :src="favoriteActiveIcon"
                    alt=""
                    aria-hidden="true"
                    class="w-8 h-8 lg:w-10 lg:h-10"
                >
            </button>
        </div>

        <div class="bg-[#267FBE] w-full text-white px-4 lg:px-6 py-5 rounded-xl absolute bottom-0 border-t-2 border-white  backdrop-blur-sm">
            <dl class="grid md:grid-cols-2 grid-cols-1 gap-2 text-sm lg:text-xl">
                <div>
                    <dt class="font-semibold inline">Название:</dt>
                    <dd :id="`fav-project-title-${project.id}`" class="inline">{{ project.title }}</dd>
                </div>
                <div v-if="project.number_date_realise">
                    <dt class="font-semibold inline">Срок:</dt>
                    <dd class="inline">{{ project.number_date_realise }} мес.</dd>
                </div>
                <div v-if="project.total_investment">
                    <dt class="font-semibold inline">Инвестиции:</dt>
                    <dd class="inline">{{ formatNumber(project.total_investment) }} ₽</dd>
                </div>
                <div v-if="project.ownership">
                    <dt class="font-semibold inline">Собственность:</dt>
                    <dd class="inline">{{ project.ownership }}</dd>
                </div>
            </dl>
        </div>
    </article>
</template>
