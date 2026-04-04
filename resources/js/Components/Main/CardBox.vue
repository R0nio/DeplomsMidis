<script setup>
defineProps({
    project:{
        type: Array,
        required: true,
    }
})
</script>

<template>
    <div class="w-full h-[550px] flex flex-col  justify-between bg-blue-300 rounded-xl">
        <div class="w-full flex ">
            <div class="font-normal text-xs p-2.5 bg-white text-black mr-auto rounded-xl flex items-center h-min">
                <!-- Статус {{ project.status }}-->
                <p>{{ project.status }}</p>
            </div>
        </div>
        <div class="bg-[#E8E8E8] w-full px-6 py-5 grid grid-cols-2 grid-rows-2 rounded-xl">
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