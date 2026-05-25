<script setup>
import { router } from '@inertiajs/vue3';
import FavoriteCard from './FavoriteCard.vue';
import MyProject from './MyProject.vue';
import ProfileinfoLabel from './ProfileinfoLabel.vue';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    favoriteProjects: {
        type: Array,
        default: null,
    },
    myProjects: {
        type: Array,
        default: null,
    },
});

// Цветовая схема
const colors = {
    bgDark: '#284139',
    bgLight: '#809076',
    accent: '#F8D794',
    border: '#886830',
    white: '#ffffff',
    white80: 'rgba(255, 255, 255, 0.8)',
};
</script>

<template>
    <div class="px-4 sm:px-10 lg:px-20 py-6">
        <div class="flex xl:flex-row flex-col gap-8">
            <!-- Левая колонка - данные пользователя -->
            <section aria-labelledby="user-data-title" class="w-full xl:w-[400px]">
                <div 
                    class="flex flex-col gap-4 xl:pr-8 pb-4 max-xl:pb-10"
                    :class="'xl:border-r-2 max-xl:border-b-2'"
                    :style="{ borderColor: colors.border }"
                >
                    <h1 id="user-data-title" class="text-2xl sm:text-3xl mx-auto mb-4 font-medium" :style="{ color: colors.accent }">Ваши данные</h1>
                    
                    <!-- ФИО -->
                    <div>
                        <h2 class="text-xl sm:text-2xl mb-1 font-semibold pl-3" :style="{ color: colors.accent }">ФИО</h2>
                        <div 
                            class="text-lg sm:text-xl lg:text-2xl p-3 w-full rounded-xl flex justify-center items-center"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        >
                            <p>{{ user.middlename }} {{ user.name }} {{ user.lastname }}</p>
                        </div>
                    </div>
                    
                    <ProfileinfoLabel v-if="user.login" title="Логин" :info="user.login" />
                    <ProfileinfoLabel title="Почта" :info="user.email" />
                    
                    <div v-if="user.number">
                        <h2 class="text-xl sm:text-2xl mb-1 font-semibold pl-3" :style="{ color: colors.accent }">Телефон</h2>
                        <div 
                            class="text-lg sm:text-xl lg:text-2xl p-3 w-full rounded-xl flex justify-center items-center"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        >
                            <p>{{ user.number }}</p>
                        </div>
                    </div>
                    
                    <!-- role -->
                    <div>
                        <h2 class="text-xl sm:text-2xl mb-1 font-semibold pl-3" :style="{ color: colors.accent }">Роль</h2>
                        <div 
                            class="text-lg sm:text-xl lg:text-2xl p-3 w-full rounded-xl flex justify-center items-center"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        >
                            <p v-if="user.role === 'Investor'">Инвестор</p>
                            <p v-else-if="user.role === 'Organisator'">Организатор</p>
                            <p v-else>{{ user.role }}</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Правая колонка - избранные проекты для инвестора -->
            <section v-if="user.role === 'Investor'" aria-labelledby="favorites-title" class="w-full">
                <div class="flex flex-col gap-8">
                    <div class="flex items-center justify-between">
                        <h1 id="favorites-title" class="text-xl sm:text-2xl lg:text-3xl mx-auto font-medium" :style="{ color: colors.accent }">
                            Избранные проекты
                        </h1>
                    </div>

                    <!-- Если нет избранных -->
                    <div v-if="!favoriteProjects || favoriteProjects.length === 0" 
                        class="text-center py-12 rounded-xl"
                        :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}` }"
                        role="status"
                        aria-live="polite"
                    >
                        <p class="text-white text-lg sm:text-xl mt-4">У вас пока нет избранных проектов</p>
                    </div>

                    <!-- Список избранных -->
                    <div v-else>
                        <div class="w-full grid md:grid-cols-2 2xl:grid-cols-3 grid-cols-1 gap-6 mx-auto overflow-y-auto max-h-[800px] px-2 pb-10" role="list" aria-label="Список избранных проектов">
                            <FavoriteCard 
                                v-for="project in favoriteProjects" 
                                :key="project.id"
                                :project="project"
                                role="listitem"
                            />
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Правая колонка - мои проекты для организатора -->
            <section v-else-if="user.role === 'Organisator'" aria-labelledby="my-projects-title" class="w-full">
                <div class="flex flex-col gap-8">
                    <div class="flex items-center justify-between">
                        <h1 id="my-projects-title" class="text-xl sm:text-2xl lg:text-3xl mx-auto font-medium" :style="{ color: colors.accent }">
                            Мои проекты
                        </h1>
                    </div>

                    <!-- Если нет проектов -->
                    <div v-if="!myProjects || myProjects.length === 0" 
                        class="text-center py-12 rounded-xl"
                        :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}` }"
                        role="status"
                        aria-live="polite"
                    >
                        <p class="text-white text-lg sm:text-xl mt-4">У вас нет созданных проектов</p>
                    </div>

                    <!-- Список моих проектов -->
                    <div v-else>
                        <div class="w-full grid md:grid-cols-2 2xl:grid-cols-3 grid-cols-1 gap-6 mx-auto overflow-y-auto max-h-[800px] px-2 pb-10" role="list" aria-label="Список моих проектов">
                            <MyProject 
                                v-for="project in myProjects" 
                                :key="project.id"
                                :project="project"
                                role="listitem"
                            />
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
