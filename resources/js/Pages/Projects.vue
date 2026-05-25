<script setup>
import SearchInput from "@/Components/Main/SearchInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { FwbSelect, FwbInput, FwbButton } from 'flowbite-vue';
import CardProjectList from "@/Components/Main/CardProjectList.vue";
import CardBox from "@/Components/Main/CardBox.vue";
import { usePage } from '@inertiajs/vue3';
// image import
import list from "../../images/list.png";
import cards from "../../images/Cards.png";
import TitlePage from "@/Layouts/TitlePage.vue";

const props = defineProps({
    projects: {
        type: Array,
        default: () => []
    },
    Adminprojects: {
        type: Array,
        default: () => []
    },
    favorites: {
        type: Array,
        default: () => []
    }
});

const page = usePage();

// Роль пользователя
const userRole = computed(() => page.props.auth.user?.role);

// Определяем какой массив проектов использовать
const currentProjects = computed(() => {
    return userRole.value === 'Admin' ? props.Adminprojects : props.projects;
});

const favoriteIds = computed(() => {
    return new Set(props.favorites.map(f => f.project_id));
});

// Функция проверки, в избранном ли проект
const isProjectFavorited = (projectId) => {
    return favoriteIds.value.has(projectId);
};

const mainColor = "#436343";
const switcherViewCard = ref(false); // false = карточки, true = список

// Фильтры
const searchQuery = ref('');
const sumFrom = ref('');
const sumBefore = ref('');
const selectedIndustry = ref('');
const selectedStatus = ref('');
const selectedOwnership = ref('');
const selectedTypeBuild = ref('');

// Динамические опции для фильтров
const industries = computed(() => {
    const unique = [...new Set(props.projects
        .map(p => p.activity)
        .filter(Boolean)
    )];
    return unique.map(item => ({ 
        value: item, 
        name: item 
    }));
});

const statuses = computed(() => {
    const unique = [...new Set(currentProjects.value 
        .map(p => p.status)
        .filter(Boolean)
    )];
    return unique.map(item => ({ value: item, name: item }));
});

const ownerships = computed(() => {
    const unique = [...new Set(props.projects
        .map(p => p.ownership)
        .filter(Boolean)
    )];
    return unique.map(item => ({ 
        value: item, 
        name: item 
    }));
});

const typeBuilds = computed(() => {
    const unique = [...new Set(props.projects
        .map(p => p.type_build)
        .filter(Boolean)
    )];
    return unique.map(item => ({ 
        value: item, 
        name: item 
    }));
});

// Фильтрация проектов
const filteredProjects = computed(() => {
    return currentProjects.value.filter(project => {
        if (searchQuery.value && 
            !project.title.toLowerCase().includes(searchQuery.value.toLowerCase())) {
            return false;
        }
        if (sumFrom.value && 
            (!project.total_investment || project.total_investment < Number(sumFrom.value))) {
            return false;
        }
        if (sumBefore.value && 
            (!project.total_investment || project.total_investment > Number(sumBefore.value))) {
            return false;
        }
        if (selectedIndustry.value && project.activity !== selectedIndustry.value) {
            return false;
        }
        if (selectedStatus.value && project.status !== selectedStatus.value) {
            return false;
        }
        if (selectedOwnership.value && project.ownership !== selectedOwnership.value) {
            return false;
        }
        if (selectedTypeBuild.value && project.type_build !== selectedTypeBuild.value) {
            return false;
        }
        return true;
    });
});

const projectsCount = computed(() => filteredProjects.value.length);

const clearFilters = () => {
    searchQuery.value = '';
    sumFrom.value = '';
    sumBefore.value = '';
    selectedIndustry.value = '';
    selectedStatus.value = '';
    selectedOwnership.value = '';
    selectedTypeBuild.value = '';
};

const handleSearch = (query) => {
    searchQuery.value = query;
};

const hasActiveFilters = computed(() => {
    return searchQuery.value || 
           sumFrom.value || 
           sumBefore.value || 
           selectedIndustry.value || 
           selectedStatus.value || 
           selectedOwnership.value || 
           selectedTypeBuild.value;
});

const colors = {
    bgPage: '#e8f0ee',
    bgLight: '#809076',
    bgDark: '#284139',
    accent: '#F8D794',
    border: '#886830',
    white: '#ffffff',
    white80: 'rgba(255, 255, 255, 0.8)',
};
</script>

<template>
    <Head title="Проекты">
        <meta name="description" content="Каталог инвестиционных проектов. Фильтрация по отрасли, сумме инвестиций, статусу и другим параметрам.">
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <TitlePage id="Main" value="Все проекты"></TitlePage>
        </template>

        <div class="px-4 sm:px-10 lg:px-20 py-6">
            <!-- Фильтрация -->
            <section aria-labelledby="filters-title">
                <div class="w-full rounded-xl p-4 sm:p-6 mb-6" :style="{ backgroundColor: colors.bgLight, border: `2px solid ${colors.border}` }">
                    <h2 id="filters-title" class="sr-only">Фильтры поиска проектов</h2>
                    
                    <!-- Поиск -->
                    <div class="flex items-center mb-4 flex-wrap gap-3">
                        <label class="text-white text-lg sm:text-xl font-semibold min-w-[250px]" :style="{ color: colors.accent }" id="search-label">
                            Поиск по названию проекта
                        </label>
                        <SearchInput @search="handleSearch" class="flex-1 min-w-[200px]" :aria-labelledby="search-label"></SearchInput>
                    </div>
                    
                    <!-- Остальные фильтры -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
                        <fwb-select
                            v-model="selectedIndustry"
                            :options="industries"
                            placeholder="Выберите отрасль"
                            class="w-full"
                            aria-label="Фильтр по отрасли"
                        />
                        
                        <fwb-input
                            v-model="sumFrom"
                            placeholder="Сумма от (₽)"
                            type="number"
                            class="w-full"
                            aria-label="Минимальная сумма инвестиций"
                        />
                        
                        <fwb-input
                            v-model="sumBefore"
                            placeholder="Сумма до (₽)"
                            type="number"
                            class="w-full"
                            aria-label="Максимальная сумма инвестиций"
                        />
                        
                        <fwb-select
                            v-model="selectedOwnership"
                            :options="ownerships"
                            placeholder="Выберите собственность"
                            class="w-full"
                            aria-label="Фильтр по форме собственности"
                        />
                        
                        <fwb-select
                            v-model="selectedTypeBuild"
                            :options="typeBuilds"
                            placeholder="Выберите вид строительства"
                            class="w-full"
                            aria-label="Фильтр по виду строительства"
                        />
                        
                        <fwb-button 
                            @click="clearFilters"
                            color="light"
                            class="w-full sm:col-span-2 lg:col-span-1"
                            :class="{ 'opacity-50': !hasActiveFilters }"
                            :disabled="!hasActiveFilters"
                            aria-label="Сбросить все фильтры"
                        >
                            Очистить фильтры
                        </fwb-button>
                    </div>

                    <!-- Активные фильтры (теги) -->
                    <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2" role="group" aria-label="Активные фильтры">
                        <span class="text-white text-sm font-semibold mr-2" :style="{ color: colors.accent }">Активные фильтры:</span>
                        
                        <span v-if="searchQuery" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            Поиск: "{{ searchQuery }}"
                            <button @click="searchQuery = ''" class="hover:text-red-600" :style="{ color: colors.accent }" aria-label="Удалить фильтр поиска">×</button>
                        </span>
                        
                        <span v-if="sumFrom" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            От: {{ Number(sumFrom).toLocaleString('ru-RU') }} ₽
                            <button @click="sumFrom = ''" class="hover:text-red-600" :style="{ color: colors.accent }" aria-label="Удалить фильтр минимальной суммы">×</button>
                        </span>
                        
                        <span v-if="sumBefore" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            До: {{ Number(sumBefore).toLocaleString('ru-RU') }} ₽
                            <button @click="sumBefore = ''" class="hover:text-red-600" :style="{ color: colors.accent }" aria-label="Удалить фильтр максимальной суммы">×</button>
                        </span>
                        
                        <span v-if="selectedIndustry" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            Отрасль: {{ selectedIndustry }}
                            <button @click="selectedIndustry = ''" class="hover:text-red-600" :style="{ color: colors.accent }" aria-label="Удалить фильтр отрасли">×</button>
                        </span>
                        
                        <span v-if="selectedOwnership" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            Собственность: {{ selectedOwnership }}
                            <button @click="selectedOwnership = ''" class="hover:text-red-600" :style="{ color: colors.accent }" aria-label="Удалить фильтр собственности">×</button>
                        </span>
                        
                        <span v-if="selectedTypeBuild" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            Вид: {{ selectedTypeBuild }}
                            <button @click="selectedTypeBuild = ''" class="hover:text-red-600" :style="{ color: colors.accent }" aria-label="Удалить фильтр вида строительства">×</button>
                        </span>
                    </div>
                </div>
            </section>
            
            <!-- Проекты -->
            <section aria-labelledby="projects-title">
                <h2 id="projects-title" class="sr-only">Список проектов</h2>
                
                <div class="w-full border-2 rounded-xl border-white h-max flex flex-col mt-6 shadow-lg" :style="{ backgroundColor: mainColor }">
                    <!-- Переключатель вида и счетчик -->
                    <div class="flex w-full justify-between items-center p-4 sm:p-6 flex-wrap gap-4">
                        <div class="text-white text-lg sm:text-xl font-semibold" aria-live="polite">
                            Найдено проектов: {{ projectsCount }}
                        </div>

                        <!-- Переключатель вида -->
                        <div class="flex bg-white gap-4 p-3 rounded-xl shadow-md" role="group" aria-label="Переключение вида отображения">
                            <button 
                                :class="`rounded-xl cursor-pointer transition-opacity ${!switcherViewCard ? 'opacity-100' : 'opacity-40'}`" 
                                @click="switcherViewCard = false"
                                :aria-pressed="!switcherViewCard"
                                aria-label="Вид карточками"
                            >
                                <img :src="cards" alt="" class="w-8 h-8" aria-hidden="true">
                                <span class="sr-only">Вид карточками</span>
                            </button>
                            <button 
                                :class="`rounded-xl cursor-pointer transition-opacity ${switcherViewCard ? 'opacity-100' : 'opacity-40'}`" 
                                @click="switcherViewCard = true"
                                :aria-pressed="switcherViewCard"
                                aria-label="Вид списком"
                            >
                                <img :src="list" alt="" class="w-8 h-8" aria-hidden="true">
                                <span class="sr-only">Вид списком</span>
                            </button>
                        </div>
                    </div>

                    <!-- Если нет проектов -->
                    <div v-if="projectsCount === 0" class="text-center py-16 px-4" role="status" aria-live="polite">
                        <svg class="mx-auto h-24 w-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-4 text-2xl font-medium text-white">Проекты не найдены</h3>
                        <p class="text-white/70 mt-2">Попробуйте изменить параметры фильтрации</p>
                        <fwb-button 
                            v-if="hasActiveFilters"
                            @click="clearFilters"
                            class="mt-4"
                            color="light"
                            aria-label="Сбросить все фильтры"
                        >
                            Сбросить фильтры
                        </fwb-button>
                    </div>
                    
                    <!-- Вид списком -->
                    <div v-else-if="switcherViewCard" class="px-4 sm:px-8">
                        <div :class="`w-full flex flex-col my-5 gap-4 ${projectsCount >= 8 ? 'overflow-y-auto max-h-[80vh]' : ''}`" role="list" aria-label="Список проектов">
                            <CardProjectList 
                                v-for="project in filteredProjects" 
                                :key="project.id" 
                                :project="project"
                                :isFavorited="isProjectFavorited(project.id)"
                            />
                        </div>
                    </div>
                    
                    <!-- Вид карточками -->
                    <div v-else class="px-4 sm:px-8">
                        <div :class="`w-full grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 my-5 gap-6 ${projectsCount >= 8 ? 'overflow-y-auto max-h-[80vh]' : ''}`" role="list" aria-label="Карточки проектов">
                            <CardBox 
                                v-for="project in filteredProjects" 
                                :key="project.id" 
                                :project="project"
                                :isFavorited="isProjectFavorited(project.id)"                            
                            />
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>