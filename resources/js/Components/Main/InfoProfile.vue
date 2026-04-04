<script setup>
import FavoriteCard from './FavoriteCard.vue';
import ProfileinfoLabel from './ProfileinfoLabel.vue';

defineProps({
    user: {
        type: Object,
        required: true,
    }
})

</script>

<template>
    <div class="flex">
        <div class="w-max flex flex-col gap-4 border-r-2 pr-16 pb-4 border-black">
            <h1 class="text-2xl mx-auto mb-4 font-medium">Ваши данные</h1>
            <!-- FIO -->
            <div >
                <h3 class="text-2xl text-black ml-3 mb-1">ФИО</h3>
                <div class="text-2xl px-14 py-2.5 bg-white w-max rounded-xl flex justify-center items-center">
                    <p>{{ user.middlename }} {{ user.name }} {{ user.lastname }}</p>
                </div>
            </div>
            <ProfileinfoLabel title="Логин" :info="user.login"></ProfileinfoLabel>
            <ProfileinfoLabel title="Почта" :info="user.email"></ProfileinfoLabel>
            <ProfileinfoLabel title="Телефон" :info="user.number"></ProfileinfoLabel>
            <!-- role -->
            <div >
                <h3 class="text-2xl text-black ml-3 mb-1">Роль</h3>
                <div class="text-2xl px-14 py-2.5 bg-white w-auto rounded-xl flex justify-center items-center">
                    <p v-if="user.role == 'Investor'">Инвестор</p>
                    <p v-if="user.role == 'Organisator'">Организатор</p>
                </div>
            </div>
        </div>
        <div class="w-full flex flex-col gap-8 mx-auto">
            <h1 class="text-2xl mx-auto mb-4 font-medium">Избранные проекты</h1>
            <div class="w-full grid grid-cols-2 gap-8 mx-auto overflow-y-scroll h-[1000px] px-10 pb-10 ">
                <FavoriteCard></FavoriteCard>
                <FavoriteCard></FavoriteCard>
                <FavoriteCard></FavoriteCard>
                <FavoriteCard></FavoriteCard>
                <FavoriteCard></FavoriteCard>
                <FavoriteCard></FavoriteCard>
                <FavoriteCard></FavoriteCard>
                <FavoriteCard></FavoriteCard>
                <FavoriteCard></FavoriteCard>
                <FavoriteCard></FavoriteCard>
            </div>
        </div>
    </div>

</template>