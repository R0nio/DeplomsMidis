<script setup>
defineProps({
    project:{
        type: Object,
        required: true,
    }
})
</script>

<template>
    <div class="w-full h-[550px] flex flex-col  justify-between bg-blue-300 rounded-xl">
        <div class="w-full flex ">
            <div class="font-normal text-xs p-2.5 bg-white text-black mr-auto rounded-xl flex items-center h-min">
                <!-- Статус {{ project.status }}-->
                В процессе
            </div>
            <div class="mt-2 mr-2">
                <img src="../../../images/Email.png" alt="star">
            </div>
        </div>
        <div class="bg-[#E8E8E8] w-full px-6 py-5 grid grid-cols-2 grid-rows-2 rounded-xl">
            <!-- Название {{ project.name }}-->
            <p>Название: </p>

            <!-- Время реализации {{ project.name }}-->
            <p>Срок реализации: </p>

             <!-- Сумма инвестиций {{ project.name }}-->
            <p>Сумма инвестиций: </p>

            <!-- Собственность {{ project.name }}-->
            <p>Собственность: </p>
        </div>
    </div>
</template>