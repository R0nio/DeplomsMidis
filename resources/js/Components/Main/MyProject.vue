<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import slider3 from "../../../images/free-icon-image-gallery-3342137.png";

const props = defineProps({
    project: {
        type: Object,
        required: true
    }
});

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

// Редактировать проект
const editProject = (event) => {
    event.preventDefault();
    event.stopPropagation();
    router.visit(route('projects.edit', props.project.id));
};

</script>

<template>
    <div class="w-full h-[500px] lg:h-[550px] flex flex-col justify-between bg-blue-300 rounded-xl relative overflow-hidden transition-shadow duration-300">
        <!-- Изображение проекта -->
        <div 
            @click="goToProject"
            class="w-full h-full cursor-pointer"
        >
            <img 
                :src="projectImage" 
                alt="Проект" 
                class="w-full h-full object-cover rounded-xl hover:scale-105 transition-transform duration-300"
            >
        </div>
        
        <!-- Верхняя панель (статус) -->
        <div class="w-full flex absolute top-2 left-2 right-2">
            <div class=" bg-[#7390C5] font-normal text-xl lg:text-3xl p-2.5 text-white mr-auto rounded-xl flex items-center h-min border-b-2 border-r-2 border-white"            >
                {{ project.status }}
            </div>
        </div>
        
        <!-- Нижняя панель с информацией -->
        <div class="bg-[#267FBE] w-full text-white px-4 lg:px-6 py-5 rounded-xl absolute bottom-0 border-t-2 border-white  backdrop-blur-sm ">
            <div class="grid md:grid-cols-2 grid-cols-1 gap-2 text-sm lg:text-xl mb-4">
                <p><span class="font-semibold">Название:</span> {{ project.title }}</p>
                <p v-if="project.number_date_realise">
                    <span class="font-semibold">Срок:</span> {{ project.number_date_realise }} мес.
                </p>
                <p v-if="project.total_investment">
                    <span class="font-semibold">Инвестиции:</span> {{ formatNumber(project.total_investment) }} ₽
                </p>
                <p v-if="project.type_build">
                    <span class="font-semibold">Собственность:</span> {{ project.type_build }}
                </p>
            </div>
            
            <!-- Кнопка редактирования -->
             <div v-if="project.status !== 'Отклонен' && project.status !== 'Заблокирован' ">
                <button
                    @click="editProject"
                    class="text-black text-xl lg:text-2xl w-full mt-4 flex items-center justify-center gap-2 bg-amber-500 rounded-xl p-3 hover:bg-amber-600 hover:text-white transition-colors duration-200"
                >
                    <p>Редактировать</p>
                </button>
             </div>
        </div>
    </div>
</template>