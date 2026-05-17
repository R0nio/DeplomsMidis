<script setup>
import { usePage } from '@inertiajs/vue3';

defineProps({
    projectData: {
        type: Object,
        required: true,
    }
});

const cardColor = "#5B83CD";
const page = usePage();

// Проверка через computed для реактивности
import { computed } from 'vue';
const userRole = computed(() => page.props.auth.user?.role);

</script>

<template>
    <div :style="{backgroundColor: cardColor}" class="flex flex-wrap p-3 justify-between w-full text-base lg:text-xl text-white rounded-xl items-center gap-3">
        <p class="min-w-[100px]">{{ projectData.name }}</p>
        <p class="min-w-[120px]">{{ projectData.summInvest }}</p>
        <p class="min-w-[120px]">{{ projectData.timeRelise }}</p>
        <p class="min-w-[120px]">{{ projectData.typeBuild }}</p>
        <p class="min-w-[120px]">{{ projectData.indystri }}</p>
        <p class="min-w-[120px]">{{ projectData.status }}</p>
        <a :href="projectData.link" class="px-4 py-2 border-b-2 bg-[#7390C5] text-base lg:text-2xl rounded-xl hover:border-none hover:bg-[#267FBE] hover:text-black focus:bg-white focus:text-black">
            Смотреть подробно
        </a>

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
</template>