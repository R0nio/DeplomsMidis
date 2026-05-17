<script setup>
import FavoriteCard from './FavoriteCard.vue';
import MyProject from './MyProject.vue';
import ProfileinfoLabel from './ProfileinfoLabel.vue';

defineProps({
    user: {
        type: Object,
        required: true,
    }
})

</script>

<template>
    <div class="flex xl:flex-row flex-col">
        <div class="w-max flex flex-col gap-4 xl:border-r-2 pr-16 pb-4 xl:border-white max-xl:border-b-2 border-white max-xl:w-full max-xl:mb-10 max-xl:pl-10">
            <h1 class="text-3xl mx-auto mb-4 font-medium text-white">Ваши данные</h1>
            <!-- FIO -->
            <div >
                <h3 class="text-2xl text-black ml-3 mb-1">ФИО</h3>
                <div class="text-2xl p-3 bg-white w-auto rounded-xl flex justify-center items-center">
                    <p>{{ user.middlename }} {{ user.name }} {{ user.lastname }}</p>
                </div>
            </div>
            <ProfileinfoLabel title="Логин" :info="user.login"></ProfileinfoLabel>
            <ProfileinfoLabel title="Почта" :info="user.email"></ProfileinfoLabel>
            <div class="">
                <h3 class="text-2xl text-black ml-3 mb-1">Телефон</h3>
                <div class="text-2xl p-3 px-10 bg-white xl:w-max w-full rounded-xl flex justify-center items-center">
                    <p>{{ user.number }}</p>
                </div>
            </div>
            <!-- role -->
            <div >
                <h3 class="text-2xl text-black ml-3 mb-1">Роль</h3>
                <div class="text-2xl p-3 bg-white w-auto rounded-xl flex justify-center items-center">
                    <p v-if="user.role == 'Investor'">Инвестор</p>
                    <p v-if="user.role == 'Organisator'">Организатор</p>
                </div>
            </div>
            <div v-if="user.role === 'Organisator'">
                <a :href="route('create')">
                    <div class="text-white text-2xl w-full mt-4 flex items-center justify-center bg-green-700 rounded-xl p-3 hover:bg-green-600 hover:text-whit">
                        <p>Создать</p>
                    </div>
                </a>
            </div>
        </div>
        <div v-if="user.role === 'Investor'" class="w-full flex flex-col gap-8 mx-auto">
            <h1 class="text-2xl mx-auto mb-4 font-medium">Избранные проекты</h1>
            <div class="w-full grid xl:grid-cols-2 grid-cols-1 gap-8 mx-auto overflow-y-scroll h-[1000px] px-10 pb-10 ">
                <FavoriteCard></FavoriteCard>
                <FavoriteCard></FavoriteCard>
                <FavoriteCard></FavoriteCard>
            </div>
        </div>
        <div v-else-if="user.role === 'Organisator'" class="w-full flex flex-col gap-8 mx-auto">
            <h1 class="text-3xl mx-auto mb-4 font-medium text-white">Мои проекты</h1>
            <div class="w-full grid xl:grid-cols-2 grid-cols-1 gap-8 mx-auto overflow-y-scroll h-[1000px] px-10 pb-10 ">
                <MyProject></MyProject>
                <MyProject></MyProject>
                <MyProject></MyProject>
                <MyProject></MyProject>
            </div>
        </div>
    </div>

</template>