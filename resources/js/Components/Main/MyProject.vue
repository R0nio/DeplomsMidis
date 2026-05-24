<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import slider3 from "../../../images/free-icon-image-gallery-3342137.png";

const props = defineProps({
    project: {
        type: Object,
        required: true
    }
});

const projectImage = computed(() => {
    if (props.project.photos && props.project.photos.length > 0) {
        return `/storage/${props.project.photos[0].photo_path}`;
    }
    return slider3;
});

const formatNumber = (number) => {
    return Number(number).toLocaleString('ru-RU');
};

const editProject = (event) => {
    event.preventDefault();
    event.stopPropagation();
    router.visit(route('projects.edit', props.project.id));
};

</script>

<template>
    <article
        class="w-full h-[500px] lg:h-[550px] flex flex-col justify-between bg-blue-300 rounded-xl relative overflow-hidden transition-shadow duration-300"
        :aria-labelledby="`my-project-title-${project.id}`"
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

        <div class="w-full flex absolute top-2 left-2 right-2">
            <p class="bg-[#7390C5] font-normal text-xl lg:text-3xl p-2.5 text-white mr-auto rounded-xl flex items-center h-min border-b-2 border-r-2 border-white">
                <span class="sr-only">Статус проекта:</span>{{ project.status }}
            </p>
        </div>

        <div class="bg-[#267FBE] w-full text-white px-4 lg:px-6 py-5 rounded-xl absolute bottom-0 border-t-2 border-white  backdrop-blur-sm ">
            <dl class="grid md:grid-cols-2 grid-cols-1 gap-2 text-sm lg:text-xl mb-4">
                <div>
                    <dt class="font-semibold inline">Название:</dt>
                    <dd :id="`my-project-title-${project.id}`" class="inline">{{ project.title }}</dd>
                </div>
                <div v-if="project.number_date_realise">
                    <dt class="font-semibold inline">Срок:</dt>
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

             <div v-if="project.status !== 'Отклонен' && project.status !== 'Заблокирован' ">
                <button
                    type="button"
                    @click="editProject"
                    class="text-black text-xl lg:text-2xl w-full mt-4 flex items-center justify-center gap-2 bg-amber-500 rounded-xl p-3 hover:bg-amber-600 hover:text-white transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-700"
                    :aria-label="`Редактировать проект ${project.title}`"
                >
                    <span>Редактировать</span>
                </button>
             </div>
        </div>
    </article>
</template>
