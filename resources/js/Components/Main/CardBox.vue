<script setup>
import slider3 from "../../../images/pictures/slider3.png"
import { usePage } from '@inertiajs/vue3';

defineProps({
    project: {
        type: Object,
        required: true,
    }
})
const page = usePage();

// Проверка через computed для реактивности
import { computed } from 'vue';
const userRole = computed(() => page.props.auth.user?.role);

</script>

<template>
    <div class="w-full h-[550px] flex flex-col justify-between bg-blue-300 rounded-xl relative">
        <a :href="project.link" class="w-full h-full">
            <img :src="slider3" class="object-cover w-full h-full rounded-xl" alt="">
        </a>
        
        <div class="w-full flex absolute top-2 left-2">
            <div class="font-normal text-xl lg:text-3xl p-2.5 bg-[#7390C5] text-white rounded-xl flex items-center h-min border-b-2 border-r-2 border-white">
                <p>{{ project.status }}</p>
            </div>
        </div>
        
        <div class="bg-[#267FBE] w-full text-white px-4 lg:px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-2 rounded-xl absolute bottom-0 border-t-2 border-white text-sm lg:text-2xl">
            <p>Название: {{ project.name }}</p>
            <p>Срок реализации: {{ project.timeRelise }}</p>
            <p>Сумма инвестиций: {{ project.summInvest }}</p>
            <p>Собственность: {{ project.typeBuild }}</p>
            <div class="flex gap-3 flex-wrap" v-if="userRole === 'Admin'">
                <form class=" text-black">
                    <select class="h-full">
                        <option class="text-black" value="">Выберите статус</option>
                        <option class="text-black" value="">Не верное оформление</option>
                        <option class="text-black" value="">Заблокированно</option>
                    </select>
                </form>
                <form class="bg-red-700 p-3 rounded-xl hover:bg-red-300">
                    <p>Удалить</p>
                </form>
            </div>
        </div>
    </div>
</template>