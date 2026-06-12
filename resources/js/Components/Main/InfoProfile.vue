<script setup>
import { router } from '@inertiajs/vue3';
import { ref, computed, watch, nextTick, onMounted } from 'vue';
import FavoriteCard from './FavoriteCard.vue';
import MyProject from './MyProject.vue';
import ProfileinfoLabel from './ProfileinfoLabel.vue';
import { FwbSelect, FwbInput, FwbButton } from 'flowbite-vue';
import SearchInput from "@/Components/Main/SearchInput.vue";
import Paginate from "@/Components/Main/paginate.vue";

// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    brand: 'var(--color-brand)',
    brandDark: 'var(--color-brand-dark)',
    accent: 'var(--color-accent)',
    hover: 'var(--color-hover)',
    light: 'var(--color-light)',
    muted: 'var(--color-muted)',
    border: 'var(--color-border)',
    white: 'var(--color-white)',
    white80: 'rgba(255, 255, 255, 0.8)',
    white60: 'rgba(255, 255, 255, 0.6)',
    text: 'var(--color-text)',
    textMuted: 'var(--color-text-muted)',
};

const fonts = {
    heading: 'var(--font-heading)',
};

const shadows = {
    sm: 'var(--shadow-sm)',
};

const transitions = {
    normal: 'var(--transition-normal)',
};

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

const isFavoriteFiltering = ref(false);
const isMyProjectsFiltering = ref(false);
const srAnnouncement = ref('');

const announceResults = (section, count) => {
    const word = count === 1 ? 'проект' : count < 5 ? 'проекта' : 'проектов';
    const sectionName = section === 'favorite' ? 'избранных' : 'ваших';
    srAnnouncement.value = `Найдено ${count} ${word} ${sectionName}`;
    setTimeout(() => { srAnnouncement.value = ''; }, 3000);
};

const favoriteCurrentPage = ref(1);
const myProjectsCurrentPage = ref(1);
const itemsPerPage = 9;

const resetFavoritePage = () => { favoriteCurrentPage.value = 1; };
const resetMyProjectsPage = () => { myProjectsCurrentPage.value = 1; };

const getLevenshteinDistance = (a, b) => {
    if (a.length === 0) return b.length;
    if (b.length === 0) return a.length;
    const matrix = [];
    for (let i = 0; i <= b.length; i++) matrix[i] = [i];
    for (let j = 0; j <= a.length; j++) matrix[0][j] = j;
    for (let i = 1; i <= b.length; i++) {
        for (let j = 1; j <= a.length; j++) {
            const cost = a[j - 1] === b[i - 1] ? 0 : 1;
            matrix[i][j] = Math.min(matrix[i - 1][j] + 1, matrix[i][j - 1] + 1, matrix[i - 1][j - 1] + cost);
        }
    }
    return matrix[b.length][a.length];
};

const isSimilarWord = (word1, word2) => {
    if (word1 === word2) return true;
    if (word1.includes(word2) || word2.includes(word1)) return true;
    const maxLength = Math.max(word1.length, word2.length);
    const distance = getLevenshteinDistance(word1, word2);
    const similarity = (maxLength - distance) / maxLength;
    return similarity > 0.7;
};

const matchesSearch = (project, searchTerm) => {
    if (!searchTerm) return true;
    const searchLower = searchTerm.toLowerCase().trim();
    if (searchLower === '') return true;
    const title = (project.title || '').toLowerCase();
    const address = (project.address || '').toLowerCase();
    if (title.includes(searchLower)) return true;
    if (address.includes(searchLower)) return true;
    const titleWords = title.split(/\s+/);
    const addressWords = address.split(/\s+/);
    const searchWords = searchLower.split(/\s+/);
    for (const searchWord of searchWords) {
        if (searchWord.length < 3) continue;
        for (const titleWord of titleWords) {
            if (titleWord.length < 3) continue;
            if (isSimilarWord(titleWord, searchWord)) return true;
        }
        for (const addressWord of addressWords) {
            if (addressWord.length < 3) continue;
            if (isSimilarWord(addressWord, searchWord)) return true;
        }
    }
    return false;
};

const matchesCategory = (project, selectedCategory) => {
    if (!selectedCategory) return true;
    try {
        const projectCats = Array.isArray(project.category) ? project.category : JSON.parse(project.category || '[]');
        return projectCats.includes(selectedCategory);
    } catch(e) { return false; }
};

const matchesDateRealise = (project, selectedDateRealise) => {
    if (!selectedDateRealise) return true;
    const months = Number(project.number_date_realise);
    switch (selectedDateRealise) {
        case '0-12': return months <= 12;
        case '12-24': return months >= 12 && months <= 24;
        case '24-36': return months >= 24 && months <= 36;
        case '36+': return months > 36;
        default: return true;
    }
};

// ==========================================
// ИЗБРАННЫЕ ПРОЕКТЫ
// ==========================================
const favoriteSearchQuery = ref('');
const favoriteSumFrom = ref('');
const favoriteSumBefore = ref('');
const favoriteJobsFrom = ref('');
const favoriteSelectedIndustry = ref('');
const favoriteSelectedOwnership = ref('');
const favoriteSelectedTypeBuild = ref('');
const favoriteSelectedCategory = ref('');
const favoriteSelectedDateRealise = ref('');

const favoriteSumFromInput = ref('');
const favoriteSumBeforeInput = ref('');
const favoriteJobsFromInput = ref('');

let favoriteSumFromTimeout = null, favoriteSumBeforeTimeout = null, favoriteJobsFromTimeout = null;

watch(favoriteSumFromInput, (newValue) => {
    clearTimeout(favoriteSumFromTimeout);
    isFavoriteFiltering.value = true;
    favoriteSumFromTimeout = setTimeout(() => { favoriteSumFrom.value = newValue; isFavoriteFiltering.value = false; }, 500);
});
watch(favoriteSumBeforeInput, (newValue) => {
    clearTimeout(favoriteSumBeforeTimeout);
    isFavoriteFiltering.value = true;
    favoriteSumBeforeTimeout = setTimeout(() => { favoriteSumBefore.value = newValue; isFavoriteFiltering.value = false; }, 500);
});
watch(favoriteJobsFromInput, (newValue) => {
    clearTimeout(favoriteJobsFromTimeout);
    isFavoriteFiltering.value = true;
    favoriteJobsFromTimeout = setTimeout(() => { favoriteJobsFrom.value = newValue; isFavoriteFiltering.value = false; }, 500);
});

const favoriteIndustries = computed(() => {
    if (!props.favoriteProjects) return [{ value: '', name: 'Все деятельности' }];
    const unique = [...new Set(props.favoriteProjects.map(p => p.activity).filter(Boolean))];
    return [{ value: '', name: 'Все деятельности' }, ...unique.map(item => ({ value: item, name: item }))];
});

const favoriteOwnerships = computed(() => {
    if (!props.favoriteProjects) return [{ value: '', name: 'Вся собственность' }];
    const unique = [...new Set(props.favoriteProjects.map(p => p.ownership).filter(Boolean))];
    return [{ value: '', name: 'Вся собственность' }, ...unique.map(item => ({ value: item, name: item }))];
});

const favoriteTypeBuilds = computed(() => {
    if (!props.favoriteProjects) return [{ value: '', name: 'Все виды' }];
    const unique = [...new Set(props.favoriteProjects.map(p => p.type_build).filter(Boolean))];
    return [{ value: '', name: 'Все виды' }, ...unique.map(item => ({ value: item, name: item }))];
});

const favoriteAllCategories = computed(() => {
    if (!props.favoriteProjects) return [{ value: '', name: 'Все категории' }];
    const catsSet = new Set();
    props.favoriteProjects.forEach(project => {
        if (project.category) {
            try {
                const cats = Array.isArray(project.category) ? project.category : JSON.parse(project.category);
                cats.forEach(cat => catsSet.add(cat));
            } catch(e) {}
        }
    });
    return [{ value: '', name: 'Все категории' }, ...Array.from(catsSet).map(cat => ({ value: cat, name: cat }))];
});

const favoriteDateRealiseOptions = [
    { value: '', name: 'Все сроки' },
    { value: '0-12', name: 'до 12 месяцев' },
    { value: '12-24', name: '12-24 месяца' },
    { value: '24-36', name: '24-36 месяцев' },
    { value: '36+', name: 'более 36 месяцев' }
];

const exactFilteredFavoriteProjects = computed(() => {
    if (!props.favoriteProjects) return [];
    return props.favoriteProjects.filter(project => {
        if (!matchesSearch(project, favoriteSearchQuery.value)) return false;
        if (favoriteSumFrom.value && (!project.total_investment || project.total_investment < Number(favoriteSumFrom.value))) return false;
        if (favoriteSumBefore.value && (!project.total_investment || project.total_investment > Number(favoriteSumBefore.value))) return false;
        if (favoriteJobsFrom.value && (!project.count_new_job || project.count_new_job < Number(favoriteJobsFrom.value))) return false;
        if (favoriteSelectedIndustry.value && project.activity !== favoriteSelectedIndustry.value) return false;
        if (favoriteSelectedOwnership.value && project.ownership !== favoriteSelectedOwnership.value) return false;
        if (favoriteSelectedTypeBuild.value && project.type_build !== favoriteSelectedTypeBuild.value) return false;
        if (!matchesCategory(project, favoriteSelectedCategory.value)) return false;
        if (!matchesDateRealise(project, favoriteSelectedDateRealise.value)) return false;
        return true;
    });
});

const similarFavoriteProjects = computed(() => {
    if (exactFilteredFavoriteProjects.value.length > 0 || !props.favoriteProjects) return [];
    return props.favoriteProjects;
});

const displayFavoriteProjects = computed(() => {
    if (exactFilteredFavoriteProjects.value.length > 0) return exactFilteredFavoriteProjects.value;
    return similarFavoriteProjects.value;
});

const paginatedFavoriteProjects = computed(() => {
    const start = (favoriteCurrentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return displayFavoriteProjects.value.slice(start, end);
});

const favoriteTotalPages = computed(() => Math.ceil(displayFavoriteProjects.value.length / itemsPerPage));
const favoriteProjectsCount = computed(() => displayFavoriteProjects.value.length);

const hasFavoriteFilters = computed(() => {
    return favoriteSearchQuery.value || favoriteSumFrom.value || favoriteSumBefore.value || favoriteJobsFrom.value ||
        favoriteSelectedIndustry.value || favoriteSelectedOwnership.value || favoriteSelectedTypeBuild.value ||
        favoriteSelectedCategory.value || favoriteSelectedDateRealise.value;
});

const clearFavoriteFilters = () => {
    favoriteSearchQuery.value = '';
    favoriteSumFrom.value = '';
    favoriteSumBefore.value = '';
    favoriteJobsFrom.value = '';
    favoriteSumFromInput.value = '';
    favoriteSumBeforeInput.value = '';
    favoriteJobsFromInput.value = '';
    favoriteSelectedIndustry.value = '';
    favoriteSelectedOwnership.value = '';
    favoriteSelectedTypeBuild.value = '';
    favoriteSelectedCategory.value = '';
    favoriteSelectedDateRealise.value = '';
    resetFavoritePage();
    srAnnouncement.value = 'Фильтры избранных проектов сброшены';
    setTimeout(() => { srAnnouncement.value = ''; }, 2000);
};

const applyFavoriteFiltersManually = () => {
    favoriteSumFrom.value = favoriteSumFromInput.value;
    favoriteSumBefore.value = favoriteSumBeforeInput.value;
    favoriteJobsFrom.value = favoriteJobsFromInput.value;
    srAnnouncement.value = 'Фильтры избранных проектов применены';
    setTimeout(() => { srAnnouncement.value = ''; }, 2000);
};

const goToFavoritePage = (page) => {
    if (page >= 1 && page <= favoriteTotalPages.value && page !== favoriteCurrentPage.value) {
        favoriteCurrentPage.value = page;
        nextTick(() => announceResults('favorite', favoriteProjectsCount.value));
    }
};

const handleFavoriteFilterChange = () => {
    resetFavoritePage();
    nextTick(() => announceResults('favorite', favoriteProjectsCount.value));
};

watch(favoriteSearchQuery, handleFavoriteFilterChange);
watch(favoriteSumFrom, handleFavoriteFilterChange);
watch(favoriteSumBefore, handleFavoriteFilterChange);
watch(favoriteJobsFrom, handleFavoriteFilterChange);
watch(favoriteSelectedIndustry, handleFavoriteFilterChange);
watch(favoriteSelectedOwnership, handleFavoriteFilterChange);
watch(favoriteSelectedTypeBuild, handleFavoriteFilterChange);
watch(favoriteSelectedCategory, handleFavoriteFilterChange);
watch(favoriteSelectedDateRealise, handleFavoriteFilterChange);

// ==========================================
// МОИ ПРОЕКТЫ
// ==========================================
const myProjectsSearchQuery = ref('');
const myProjectsSumFrom = ref('');
const myProjectsSumBefore = ref('');
const myProjectsJobsFrom = ref('');
const myProjectsSelectedIndustry = ref('');
const myProjectsSelectedOwnership = ref('');
const myProjectsSelectedTypeBuild = ref('');
const myProjectsSelectedCategory = ref('');
const myProjectsSelectedDateRealise = ref('');
const myProjectsSelectedStatus = ref('');

const myProjectsSumFromInput = ref('');
const myProjectsSumBeforeInput = ref('');
const myProjectsJobsFromInput = ref('');

let myProjectsSumFromTimeout = null, myProjectsSumBeforeTimeout = null, myProjectsJobsFromTimeout = null;

watch(myProjectsSumFromInput, (newValue) => {
    clearTimeout(myProjectsSumFromTimeout);
    isMyProjectsFiltering.value = true;
    myProjectsSumFromTimeout = setTimeout(() => { myProjectsSumFrom.value = newValue; isMyProjectsFiltering.value = false; }, 500);
});
watch(myProjectsSumBeforeInput, (newValue) => {
    clearTimeout(myProjectsSumBeforeTimeout);
    isMyProjectsFiltering.value = true;
    myProjectsSumBeforeTimeout = setTimeout(() => { myProjectsSumBefore.value = newValue; isMyProjectsFiltering.value = false; }, 500);
});
watch(myProjectsJobsFromInput, (newValue) => {
    clearTimeout(myProjectsJobsFromTimeout);
    isMyProjectsFiltering.value = true;
    myProjectsJobsFromTimeout = setTimeout(() => { myProjectsJobsFrom.value = newValue; isMyProjectsFiltering.value = false; }, 500);
});

const myProjectsIndustries = computed(() => {
    if (!props.myProjects) return [{ value: '', name: 'Все деятельности' }];
    const unique = [...new Set(props.myProjects.map(p => p.activity).filter(Boolean))];
    return [{ value: '', name: 'Все деятельности' }, ...unique.map(item => ({ value: item, name: item }))];
});

const myProjectsOwnerships = computed(() => {
    if (!props.myProjects) return [{ value: '', name: 'Вся собственность' }];
    const unique = [...new Set(props.myProjects.map(p => p.ownership).filter(Boolean))];
    return [{ value: '', name: 'Вся собственность' }, ...unique.map(item => ({ value: item, name: item }))];
});

const myProjectsTypeBuilds = computed(() => {
    if (!props.myProjects) return [{ value: '', name: 'Все виды' }];
    const unique = [...new Set(props.myProjects.map(p => p.type_build).filter(Boolean))];
    return [{ value: '', name: 'Все виды' }, ...unique.map(item => ({ value: item, name: item }))];
});

const myProjectsAllCategories = computed(() => {
    if (!props.myProjects) return [{ value: '', name: 'Все категории' }];
    const catsSet = new Set();
    props.myProjects.forEach(project => {
        if (project.category) {
            try {
                const cats = Array.isArray(project.category) ? project.category : JSON.parse(project.category);
                cats.forEach(cat => catsSet.add(cat));
            } catch(e) {}
        }
    });
    return [{ value: '', name: 'Все категории' }, ...Array.from(catsSet).map(cat => ({ value: cat, name: cat }))];
});

const myProjectsDateRealiseOptions = [
    { value: '', name: 'Все сроки' },
    { value: '0-12', name: 'до 12 месяцев' },
    { value: '12-24', name: '12-24 месяца' },
    { value: '24-36', name: '24-36 месяцев' },
    { value: '36+', name: 'более 36 месяцев' }
];

const myProjectsStatusOptions = computed(() => {
    if (!props.myProjects) return [{ value: '', name: 'Все статусы' }];
    const unique = [...new Set(props.myProjects.map(p => p.status).filter(Boolean))];
    return [{ value: '', name: 'Все статусы' }, ...unique.map(item => ({ value: item, name: item }))];
});

const exactFilteredMyProjects = computed(() => {
    if (!props.myProjects) return [];
    return props.myProjects.filter(project => {
        if (!matchesSearch(project, myProjectsSearchQuery.value)) return false;
        if (myProjectsSelectedStatus.value && project.status !== myProjectsSelectedStatus.value) return false;
        if (myProjectsSumFrom.value && (!project.total_investment || project.total_investment < Number(myProjectsSumFrom.value))) return false;
        if (myProjectsSumBefore.value && (!project.total_investment || project.total_investment > Number(myProjectsSumBefore.value))) return false;
        if (myProjectsJobsFrom.value && (!project.count_new_job || project.count_new_job < Number(myProjectsJobsFrom.value))) return false;
        if (myProjectsSelectedIndustry.value && project.activity !== myProjectsSelectedIndustry.value) return false;
        if (myProjectsSelectedOwnership.value && project.ownership !== myProjectsSelectedOwnership.value) return false;
        if (myProjectsSelectedTypeBuild.value && project.type_build !== myProjectsSelectedTypeBuild.value) return false;
        if (!matchesCategory(project, myProjectsSelectedCategory.value)) return false;
        if (!matchesDateRealise(project, myProjectsSelectedDateRealise.value)) return false;
        return true;
    });
});

const similarMyProjects = computed(() => {
    if (exactFilteredMyProjects.value.length > 0 || !props.myProjects) return [];
    return props.myProjects;
});

const displayMyProjects = computed(() => {
    if (exactFilteredMyProjects.value.length > 0) return exactFilteredMyProjects.value;
    return similarMyProjects.value;
});

const paginatedMyProjects = computed(() => {
    const start = (myProjectsCurrentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return displayMyProjects.value.slice(start, end);
});

const myProjectsTotalPages = computed(() => Math.ceil(displayMyProjects.value.length / itemsPerPage));
const myProjectsCount = computed(() => displayMyProjects.value.length);

const hasMyProjectsFilters = computed(() => {
    return myProjectsSearchQuery.value || myProjectsSumFrom.value || myProjectsSumBefore.value || myProjectsJobsFrom.value ||
        myProjectsSelectedIndustry.value || myProjectsSelectedOwnership.value || myProjectsSelectedTypeBuild.value ||
        myProjectsSelectedCategory.value || myProjectsSelectedDateRealise.value || myProjectsSelectedStatus.value;
});

const clearMyProjectsFilters = () => {
    myProjectsSearchQuery.value = '';
    myProjectsSumFrom.value = '';
    myProjectsSumBefore.value = '';
    myProjectsJobsFrom.value = '';
    myProjectsSumFromInput.value = '';
    myProjectsSumBeforeInput.value = '';
    myProjectsJobsFromInput.value = '';
    myProjectsSelectedIndustry.value = '';
    myProjectsSelectedOwnership.value = '';
    myProjectsSelectedTypeBuild.value = '';
    myProjectsSelectedCategory.value = '';
    myProjectsSelectedDateRealise.value = '';
    myProjectsSelectedStatus.value = '';
    resetMyProjectsPage();
    srAnnouncement.value = 'Фильтры ваших проектов сброшены';
    setTimeout(() => { srAnnouncement.value = ''; }, 2000);
};

const applyMyProjectsFiltersManually = () => {
    myProjectsSumFrom.value = myProjectsSumFromInput.value;
    myProjectsSumBefore.value = myProjectsSumBeforeInput.value;
    myProjectsJobsFrom.value = myProjectsJobsFromInput.value;
    srAnnouncement.value = 'Фильтры ваших проектов применены';
    setTimeout(() => { srAnnouncement.value = ''; }, 2000);
};

const goToMyProjectsPage = (page) => {
    if (page >= 1 && page <= myProjectsTotalPages.value && page !== myProjectsCurrentPage.value) {
        myProjectsCurrentPage.value = page;
        nextTick(() => announceResults('myProjects', myProjectsCount.value));
    }
};

const handleMyProjectsFilterChange = () => {
    resetMyProjectsPage();
    nextTick(() => announceResults('myProjects', myProjectsCount.value));
};

watch(myProjectsSearchQuery, handleMyProjectsFilterChange);
watch(myProjectsSumFrom, handleMyProjectsFilterChange);
watch(myProjectsSumBefore, handleMyProjectsFilterChange);
watch(myProjectsJobsFrom, handleMyProjectsFilterChange);
watch(myProjectsSelectedIndustry, handleMyProjectsFilterChange);
watch(myProjectsSelectedOwnership, handleMyProjectsFilterChange);
watch(myProjectsSelectedTypeBuild, handleMyProjectsFilterChange);
watch(myProjectsSelectedCategory, handleMyProjectsFilterChange);
watch(myProjectsSelectedDateRealise, handleMyProjectsFilterChange);
watch(myProjectsSelectedStatus, handleMyProjectsFilterChange);
</script>

<template>
    <div class="mx-auto py-6 pb-0 px-4 sm:px-10 lg:px-16">
        <!-- Скрытая область для объявлений screen reader -->
        <div role="status" aria-live="polite" aria-atomic="true" class="sr-only">{{ srAnnouncement }}</div>

        <!-- Админ видит данные по центру -->
        <div v-if="user.role === 'Admin'" class="flex flex-col items-center justify-center">
            <section aria-labelledby="user-data-title" class="w-full max-w-2xl mx-auto mb-8">
                <div class="flex flex-col gap-4 pb-4 border-b-2" :style="{ borderColor: colors.accent }">
                    <h1 id="user-data-title" class="text-2xl sm:text-3xl text-center font-heading font-bold mb-2" :style="{ color: colors.brand, fontFamily: fonts.heading }" tabindex="0">Ваши данные</h1>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div tabindex="0" class="focus:outline-none rounded-lg p-1">
                            <h2 class="text-sm font-semibold mb-1.5 pl-3 uppercase tracking-wide" :style="{ color: colors.accent }">ФИО</h2>
                            <div class="text-2xl p-3 w-full rounded-xl flex justify-center items-center " :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.accent}`, color: colors.white }">
                                <p class="">{{ `${user.middlename || ''} ${user.name || ''} ${user.lastname || ''}`.trim() || '—' }}</p>
                            </div>
                        </div>
                        
                        <!-- <div v-if="user.login" tabindex="0" class="focus:outline-none rounded-lg p-1">
                            <h2 class="text-sm font-semibold mb-1.5 pl-3 uppercase tracking-wide" :style="{ color: colors.accent }">Логин</h2>
                            <div class="text-2xl p-3 w-full rounded-xl flex justify-center items-center" :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.accent}`, color: colors.white }">
                                <p class="truncate">{{ user.login }}</p>
                            </div>
                        </div> -->
                        
                        <div tabindex="0" class="focus:outline-none rounded-lg p-1">
                            <h2 class="text-sm font-semibold mb-1.5 pl-3 uppercase tracking-wide" :style="{ color: colors.accent }">Электронная почта</h2>
                            <div class="text-2xl p-3 w-full rounded-xl flex justify-center items-center" :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.accent}`, color: colors.white }">
                                <p class="truncate">{{ user.email || '—' }}</p>
                            </div>
                        </div>
                        
                        <div v-if="user.number" tabindex="0" class="focus:outline-none rounded-lg p-1">
                            <h2 class="text-sm font-semibold mb-1.5 pl-3 uppercase tracking-wide" :style="{ color: colors.accent }">Телефон</h2>
                            <div class="text-2xl p-3 w-full rounded-xl flex justify-center items-center" :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.accent}`, color: colors.white }">
                                <p class="truncate">{{ user.number }}</p>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2 focus:outline-none rounded-lg p-1" tabindex="0">
                            <h2 class="text-sm font-semibold mb-1.5 pl-3 uppercase tracking-wide" :style="{ color: colors.accent }">Роль</h2>
                            <div class="text-2xl p-3 w-full rounded-xl flex justify-center items-center" :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.accent}`, color: colors.white }">
                                <p class="flex items-center gap-2">{{ user.role === 'Admin' ? 'Администратор' : user.role }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <div class="w-full">
                <section v-if="favoriteProjects && favoriteProjects.length > 0" class="w-full mb-8">
                    <div class="flex flex-col gap-4">
                        <h1 class="text-2xl sm:text-2xl lg:text-3xl text-center font-heading font-medium" :style="{ color: colors.brandDark, fontFamily: fonts.heading }" tabindex="0">Избранные проекты</h1>
                        <div class="grid md:grid-cols-2 2xl:grid-cols-3 grid-cols-1 gap-6">
                            <FavoriteCard v-for="project in favoriteProjects.slice(0, 6)" :key="project.id" :project="project" />
                        </div>
                    </div>
                </section>
                
                <section v-if="myProjects && myProjects.length > 0">
                    <h1 class="text-2xl sm:text-2xl lg:text-3xl text-center font-heading font-medium mb-4" :style="{ color: colors.accent, fontFamily: fonts.heading }" tabindex="0">Все проекты</h1>
                    <div class="grid md:grid-cols-2 2xl:grid-cols-3 grid-cols-1 gap-6">
                        <MyProject v-for="project in myProjects.slice(0, 6)" :key="project.id" :project="project" />
                    </div>
                </section>
            </div>
        </div>
        
        <!-- Обычный пользователь (не админ) -->
        <div v-else class="flex xl:flex-row flex-col gap-8">
            <!-- Левая колонка - данные пользователя -->
            <section aria-labelledby="user-data-title" class="w-full xl:w-[600px]">
                <div class="flex flex-col gap-4 xl:pr-8 pb-4 max-xl:pb-10 xl:border-r-2 max-xl:border-b-2" :style="{ borderColor: colors.muted }">
                    <h1 id="user-data-title" class="text-2xl sm:text-3xl text-center font-heading font-bold mb-2" :style="{ color: colors.brand, fontFamily: fonts.heading }" tabindex="0">Ваши данные</h1>
                    
                    <div tabindex="0" class="focus:outline-none rounded-lg p-1">
                        <h2 class="text-sm font-semibold mb-1.5 pl-3 uppercase tracking-wide" :style="{ color: colors.accent }">ФИО</h2>
                        <div class="text-2xl p-3 w-full rounded-xl flex justify-center items-center" :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.accent}`, color: colors.white }">
                            <p class="">{{ `${user.middlename || ''} ${user.name || ''} ${user.lastname || ''}`.trim() || '—' }}</p>
                        </div>
                    </div>
                    
                    <!-- <div v-if="user.login" tabindex="0" class="focus:outline-none rounded-lg p-1">
                        <h2 class="text-sm font-semibold mb-1.5 pl-3 uppercase tracking-wide" :style="{ color: colors.accent }">Логин</h2>
                        <div class="text-2xl p-3 w-full rounded-xl flex justify-center items-center" :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.accent}`, color: colors.white }">
                            <p class="truncate">{{ user.login }}</p>
                        </div>
                    </div> -->
                    
                    <div tabindex="0" class="focus:outline-none rounded-lg p-1">
                        <h2 class="text-sm font-semibold mb-1.5 pl-3 uppercase tracking-wide" :style="{ color: colors.accent }">Электронная почта</h2>
                        <div class="text-2xl p-3 w-full rounded-xl flex justify-center items-center" :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.accent}`, color: colors.white }">
                            <p class="truncate">{{ user.email || '—' }}</p>
                        </div>
                    </div>
                    
                    <div v-if="user.number" tabindex="0" class="focus:outline-none rounded-lg p-1">
                        <h2 class="text-sm font-semibold mb-1.5 pl-3 uppercase tracking-wide" :style="{ color: colors.accent }">Телефон</h2>
                        <div class="text-2xl p-3 w-full rounded-xl flex justify-center items-center" :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.accent}`, color: colors.white }">
                            <p class="truncate">{{ user.number }}</p>
                        </div>
                    </div>
                    
                    <div tabindex="0" class="focus:outline-none rounded-lg p-1">
                        <h2 class="text-sm font-semibold mb-1.5 pl-3 uppercase tracking-wide" :style="{ color: colors.accent }">Роль</h2>
                        <div class="text-2xl p-3 w-full rounded-xl flex justify-center items-center" :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.accent}`, color: colors.white }">
                            <p v-if="user.role === 'Investor'" class="flex items-center gap-2">Инвестор</p>
                            <p v-else-if="user.role === 'Organisator'" class="flex items-center gap-2">Инициатор</p>
                            <p v-else>{{ user.role }}</p>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Избранные проекты (Инвестор) -->
            <section v-if="user.role === 'Investor'" class="w-full">
                <div class="flex flex-col gap-4">
                    <h1 class="text-2xl sm:text-2xl lg:text-3xl text-center font-heading font-medium" :style="{ color: colors.brandDark, fontFamily: fonts.heading }" tabindex="0">Избранные проекты</h1>

                    <div v-if="favoriteProjects && favoriteProjects.length > 0" class="rounded-lg p-4" :style="{ backgroundColor: colors.light, border: `1px solid ${colors.accent}` }">
                        <div class="flex flex-col sm:flex-row items-center gap-2 mb-3">
                            <div class="flex-1 w-full"><SearchInput v-model="favoriteSearchQuery" placeholder="Поиск по названию или адресу..." class="w-full" /></div>
                            <div class="flex h-[45px] gap-2 w-full sm:w-auto">
                                <!-- <fwb-button @click="applyFavoriteFiltersManually" color="default" size="sm" class="flex-1 bg-accent text-white hover:bg-hover">Применить</fwb-button> -->
                                <fwb-button @click="clearFavoriteFilters" color="light" size="sm" :class="{ 'opacity-50': !hasFavoriteFilters }" :disabled="!hasFavoriteFilters" class="bg-light flex-1  text-brand border border-accent hover:bg-hover/10">Сбросить</fwb-button>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-3">
                            <fwb-input v-model="favoriteSumFromInput" placeholder="Сумма от (₽)" type="number" size="sm" />
                            <fwb-input v-model="favoriteSumBeforeInput" placeholder="Сумма до (₽)" type="number" size="sm" />
                            <fwb-input v-model="favoriteJobsFromInput" placeholder="Рабочих мест от" type="number" size="sm" />
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                            <fwb-select v-model="favoriteSelectedCategory" :options="favoriteAllCategories" placeholder="Категория" size="sm" />
                            <fwb-select v-model="favoriteSelectedDateRealise" :options="favoriteDateRealiseOptions" placeholder="Срок реализации" size="sm" />
                            <fwb-select v-model="favoriteSelectedIndustry" :options="favoriteIndustries" placeholder="Деятельность" size="sm" />
                            <fwb-select v-model="favoriteSelectedOwnership" :options="favoriteOwnerships" placeholder="Собственность" size="sm" />
                        </div>

                        <div v-if="hasFavoriteFilters" class="mt-4 flex flex-wrap gap-2">
                            <span class="text-sm font-semibold mr-2" :style="{ color: colors.brand }">Активные фильтры:</span>
                            <span v-if="favoriteSearchQuery" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Поиск: "{{ favoriteSearchQuery }}"
                                <button @click="favoriteSearchQuery = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="favoriteSelectedCategory" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Категория: {{ favoriteSelectedCategory }}
                                <button @click="favoriteSelectedCategory = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="favoriteSelectedDateRealise" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Срок: {{ favoriteDateRealiseOptions.find(o => o.value === favoriteSelectedDateRealise)?.name }}
                                <button @click="favoriteSelectedDateRealise = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="favoriteSumFrom" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Сумма от: {{ Number(favoriteSumFrom).toLocaleString('ru-RU') }} ₽
                                <button @click="favoriteSumFrom = ''; favoriteSumFromInput = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="favoriteSumBefore" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Сумма до: {{ Number(favoriteSumBefore).toLocaleString('ru-RU') }} ₽
                                <button @click="favoriteSumBefore = ''; favoriteSumBeforeInput = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="favoriteJobsFrom" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Рабочих мест от: {{ favoriteJobsFrom }}
                                <button @click="favoriteJobsFrom = ''; favoriteJobsFromInput = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="favoriteSelectedIndustry" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Деятельность: {{ favoriteSelectedIndustry }}
                                <button @click="favoriteSelectedIndustry = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="favoriteSelectedOwnership" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Собственность: {{ favoriteSelectedOwnership }}
                                <button @click="favoriteSelectedOwnership = ''" class="hover:text-accent">×</button>
                            </span>
                        </div>
                    </div>

                    <div v-if="!paginatedFavoriteProjects || paginatedFavoriteProjects.length === 0" class="text-center py-12 rounded-xl" :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.accent}` }" role="status" tabindex="0">
                        <p class="text-white text-lg">{{ favoriteProjects && favoriteProjects.length > 0 ? 'Попробуйте изменить параметры фильтрации' : 'У вас пока нет избранных проектов' }}</p>
                    </div>

                    <div v-else>
                        <div class="w-full grid md:grid-cols-2 2xl:grid-cols-3 grid-cols-1 gap-6">
                            <FavoriteCard v-for="project in paginatedFavoriteProjects" :key="project.id" :project="project" />
                        </div>
                        <Paginate v-if="favoriteTotalPages > 1" :current-page="favoriteCurrentPage" :total-pages="favoriteTotalPages" @update:page="goToFavoritePage" />
                    </div>
                </div>
            </section>
            
            <!-- Мои проекты (Организатор) -->
            <section v-else-if="user.role === 'Organisator'" class="w-full">
                <div class="flex flex-col gap-4">
                    <h1 class="text-2xl sm:text-2xl lg:text-3xl text-center font-heading font-medium" :style="{ color: colors.brandDark, fontFamily: fonts.heading }" tabindex="0">Мои проекты</h1>

                    <div v-if="myProjects && myProjects.length > 0" class="rounded-lg p-4" :style="{ backgroundColor: colors.light, border: `1px solid ${colors.accent}` }">
                        <div class="flex flex-col sm:flex-row items-center gap-2 mb-3">
                            <div class="flex-1 w-full"><SearchInput v-model="myProjectsSearchQuery" placeholder="Поиск по названию или адресу..." class="w-full" /></div>
                            <div class="flex h-[45px] gap-2 w-full sm:w-auto">
                                <!-- <fwb-button @click="applyMyProjectsFiltersManually" color="default" size="sm" class="bg-accent flex-1 text-white hover:bg-hover">Применить</fwb-button> -->
                                <fwb-button @click="clearMyProjectsFilters" color="light" size="sm" :class="{ 'opacity-50': !hasMyProjectsFilters }" :disabled="!hasMyProjectsFilters" class="bg-light flex-1 text-brand border border-accent hover:bg-hover/10">Сбросить</fwb-button>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-3">
                            <fwb-input v-model="myProjectsSumFromInput" placeholder="Сумма от (₽)" type="number" size="sm" />
                            <fwb-input v-model="myProjectsSumBeforeInput" placeholder="Сумма до (₽)" type="number" size="sm" />
                            <fwb-input v-model="myProjectsJobsFromInput" placeholder="Рабочих мест от" type="number" size="sm" />
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2">
                            <fwb-select v-model="myProjectsSelectedCategory" :options="myProjectsAllCategories" placeholder="Категория" size="sm" />
                            <fwb-select v-model="myProjectsSelectedDateRealise" :options="myProjectsDateRealiseOptions" placeholder="Срок реализации" size="sm" />
                            <fwb-select v-model="myProjectsSelectedIndustry" :options="myProjectsIndustries" placeholder="Деятельность" size="sm" />
                            <fwb-select v-model="myProjectsSelectedOwnership" :options="myProjectsOwnerships" placeholder="Собственность" size="sm" />
                            <fwb-select v-model="myProjectsSelectedStatus" :options="myProjectsStatusOptions" placeholder="Статус" size="sm" />
                        </div>

                        <div v-if="hasMyProjectsFilters" class="mt-4 flex flex-wrap gap-2">
                            <span class="text-sm font-semibold mr-2" :style="{ color: colors.brand }">Активные фильтры:</span>
                            <span v-if="myProjectsSearchQuery" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Поиск: "{{ myProjectsSearchQuery }}"
                                <button @click="myProjectsSearchQuery = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="myProjectsSelectedCategory" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Категория: {{ myProjectsSelectedCategory }}
                                <button @click="myProjectsSelectedCategory = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="myProjectsSelectedDateRealise" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Срок: {{ myProjectsDateRealiseOptions.find(o => o.value === myProjectsSelectedDateRealise)?.name }}
                                <button @click="myProjectsSelectedDateRealise = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="myProjectsSelectedStatus" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Статус: {{ myProjectsSelectedStatus }}
                                <button @click="myProjectsSelectedStatus = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="myProjectsSumFrom" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Сумма от: {{ Number(myProjectsSumFrom).toLocaleString('ru-RU') }} ₽
                                <button @click="myProjectsSumFrom = ''; myProjectsSumFromInput = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="myProjectsSumBefore" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Сумма до: {{ Number(myProjectsSumBefore).toLocaleString('ru-RU') }} ₽
                                <button @click="myProjectsSumBefore = ''; myProjectsSumBeforeInput = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="myProjectsJobsFrom" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Рабочих мест от: {{ myProjectsJobsFrom }}
                                <button @click="myProjectsJobsFrom = ''; myProjectsJobsFromInput = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="myProjectsSelectedIndustry" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Деятельность: {{ myProjectsSelectedIndustry }}
                                <button @click="myProjectsSelectedIndustry = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="myProjectsSelectedOwnership" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Собственность: {{ myProjectsSelectedOwnership }}
                                <button @click="myProjectsSelectedOwnership = ''" class="hover:text-accent">×</button>
                            </span>
                            <span v-if="myProjectsSelectedTypeBuild" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                                Вид строительства: {{ myProjectsSelectedTypeBuild }}
                                <button @click="myProjectsSelectedTypeBuild = ''" class="hover:text-accent">×</button>
                            </span>
                        </div>
                    </div>

                    <div v-if="!paginatedMyProjects || paginatedMyProjects.length === 0" class="text-center py-12 rounded-xl" :style="{ backgroundColor: colors.brand, border: `1px solid ${colors.accent}` }" role="status" tabindex="0">
                        <p class="text-white text-lg">{{ myProjects && myProjects.length > 0 ? 'Попробуйте изменить параметры фильтрации' : 'У вас нет созданных проектов' }}</p>
                    </div>

                    <div v-else>
                        <div class="w-full grid md:grid-cols-2 2xl:grid-cols-3 grid-cols-1 gap-6">
                            <MyProject v-for="project in paginatedMyProjects" :key="project.id" :project="project" />
                        </div>
                        <Paginate v-if="myProjectsTotalPages > 1" :current-page="myProjectsCurrentPage" :total-pages="myProjectsTotalPages" @update:page="goToMyProjectsPage" />
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>