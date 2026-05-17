<script setup>
import slider3 from "../../../images/pictures/slider3.png"

defineProps({
    project:{
        type: Array,
        required: true,
    }
})
</script>

<template>
        <div class="w-full h-[550px] flex flex-col  justify-between bg-blue-300 rounded-xl relative">
            <a :href="project.link" class="w-full h-full ">
                <img :src="slider3" class="object-cover rounded-xl" alt="">
            </a>
            <div class="w-full flex absolute">
                <div class="font-normal text-3xl p-2.5 bg-[#7390C5] text-white mr-auto rounded-xl flex items-center h-min border-b-2 border-r-2 border-white">
                    <!-- Статус {{ project.status }}-->
                    <p>{{ project.status }}</p>
                </div>
            </div>
            <div class="bg-[#267FBE] w-full text-white px-6 py-5 grid grid-cols-2 grid-rows-2 rounded-xl text-2xl absolute bottom-0 border-t-2 border-white">
                <!-- Название {{ project.name }}-->
                <p>Название: {{ project.name }}</p>

                <!-- Время реализации {{ project.name }}-->
                <p>Срок реализации: {{ project.name }}</p>

                <!-- Сумма инвестиций {{ project.name }}-->
                <p>Сумма инвестиций: {{ project.name }}</p>

                <!-- Собственность {{ project.name }}-->
                <p>Собственность: {{ project.name }}</p>
            </div>
        </div>
</template>