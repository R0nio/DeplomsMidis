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
    <div class="px-[80px] max-xl:px-4 py-6">
        <div class="flex xl:flex-row flex-col gap-8">
            <!-- Левая колонка - данные пользователя -->
            <div class="w-full xl:w-auto flex flex-col gap-4 xl:border-r-2 pr-16 pb-4 xl:border-white max-xl:border-b-2 border-white max-xl:pb-10">
                <h1 class="text-3xl mx-auto mb-4 font-medium text-white">Ваши данные</h1>
                
                <!-- FIO -->
                <div>
                    <h3 class="text-2xl text-black ml-3 mb-1">ФИО</h3>
                    <div class="text-xl lg:text-2xl p-3 bg-white w-auto rounded-xl flex justify-center items-center">
                        <p>{{ user.middlename }} {{ user.name }} {{ user.lastname }}</p>
                    </div>
                </div>
                
                <ProfileinfoLabel title="Логин" :info="user.login"></ProfileinfoLabel>
                <ProfileinfoLabel title="Почта" :info="user.email"></ProfileinfoLabel>
                
                <div>
                    <h3 class="text-2xl text-black ml-3 mb-1">Телефон</h3>
                    <div class="text-xl lg:text-2xl p-3 px-10 bg-white xl:w-max w-full rounded-xl flex justify-center items-center">
                        <p>{{ user.number }}</p>
                    </div>
                </div>
                
                <!-- role -->
                <div>
                    <h3 class="text-2xl text-black ml-3 mb-1">Роль</h3>
                    <div class="text-xl lg:text-2xl p-3 bg-white w-auto rounded-xl flex justify-center items-center">
                        <p v-if="user.role == 'Investor'">Инвестор</p>
                        <p v-if="user.role == 'Organisator'">Организатор</p>
                    </div>
                </div>
                
                <div v-if="user.role === 'Organisator'">
                    <a :href="route('create')">
                        <div class="text-white text-xl lg:text-2xl w-full mt-4 flex items-center justify-center bg-green-700 rounded-xl p-3 hover:bg-green-600 transition duration-300">
                            <p>Создать проект</p>
                        </div>
                    </a>
                </div>
            </div>
            
            <!-- Правая колонка - избранные проекты / мои проекты -->
            <div v-if="user.role === 'Investor'" class="w-full flex flex-col gap-8">
                <h1 class="text-2xl lg:text-3xl mx-auto mb-4 font-medium text-white">Избранные проекты</h1>
                <div class="w-full grid md:grid-cols-2 grid-cols-1 gap-8 mx-auto overflow-y-auto max-h-[800px] px-4 pb-10">
                    <FavoriteCard></FavoriteCard>
                    <FavoriteCard></FavoriteCard>
                    <FavoriteCard></FavoriteCard>
                </div>
            </div>
            
            <div v-else-if="user.role === 'Organisator'" class="w-full flex flex-col gap-8">
                <h1 class="text-2xl lg:text-3xl mx-auto mb-4 font-medium text-white">Мои проекты</h1>
                <div class="w-full grid md:grid-cols-2 grid-cols-1 gap-8 mx-auto overflow-y-auto max-h-[800px] px-4 pb-10">
                    <MyProject></MyProject>
                    <MyProject></MyProject>
                    <MyProject></MyProject>
                    <MyProject></MyProject>
                </div>
            </div>
        </div>
    </div>
</template>