<script setup>
import SearchInput from "@/Components/Main/SearchInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted } from "vue";
import { FwbSelect, FwbInput, FwbButton } from 'flowbite-vue';
import CardProjectList from "@/Components/Main/CardProjectList.vue";
import CardBox from "@/Components/Main/CardBox.vue";
import { usePage, router } from '@inertiajs/vue3';
// image import
import list from "../../images/list.png";
import cards from "../../images/Cards.png";
import TitlePage from "@/Layouts/TitlePage.vue";
import Paginate from "@/Components/Main/paginate.vue";

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

// ==========================================
// ОБРАБОТКА КАТЕГОРИИ ИЗ URL ИЛИ SESSIONSTORAGE
// ==========================================
/**
 * Инициализация фильтра категории из параметров URL или sessionStorage
 */
onMounted(() => {
    // 1. Проверяем параметры в URL
    const urlParams = new URLSearchParams(window.location.search);
    const categoryId = urlParams.get('category_id');
    const categoryName = urlParams.get('category_name');
    
    if (categoryId && categoryName) {
        selectedCategory.value = categoryName;
        // Очищаем URL от параметров категории без перезагрузки страницы
        const newUrl = window.location.pathname;
        window.history.replaceState({}, '', newUrl);
    }
    // 2. Проверяем sessionStorage
    else {
        const savedCategory = sessionStorage.getItem('selectedCategory');
        if (savedCategory) {
            try {
                const category = JSON.parse(savedCategory);
                selectedCategory.value = category.name;
                // Очищаем sessionStorage после использования
                sessionStorage.removeItem('selectedCategory');
            } catch(e) {
                console.error('Error parsing saved category:', e);
            }
        }
    }
});

// ==========================================
// ПОЛЬЗОВАТЕЛЬ И ПРОЕКТЫ
// ==========================================
/**
 * Получение роли текущего пользователя
 * Используется для отображения разных наборов проектов и фильтров
 */
const userRole = computed(() => page.props.auth.user?.role);
/**
 * Определение какого массива проектов использовать
 * Админ видит все проекты, обычный пользователь - только опубликованные
 */
const allProjects = computed(() => {
    return userRole.value === 'Admin' ? props.Adminprojects : props.projects;
});
/**
 * Создание Set с ID избранных проектов для быстрой проверки
 */
const favoriteIds = computed(() => {
    return new Set(props.favorites.map(f => f.project_id));
});
/**
 * Проверка находится ли проект в избранном
 * @param {number} projectId - ID проекта
 * @returns {boolean}
 */
const isProjectFavorited = (projectId) => {
    return favoriteIds.value.has(projectId);
};
// ==========================================
// UI СОСТОЯНИЕ
// ==========================================
const mainColor = "#436343";
const switcherViewCard = ref(false); // false = карточки, true = список
// Пагинация
const currentPage = ref(1);
const itemsPerPage = 12;
/**
 * Сброс пагинации на первую страницу
 * Вызывается при изменении фильтров
 */
const resetPage = () => {
    currentPage.value = 1;
};
// ==========================================
// ФИЛЬТРЫ С АВТОПРИМЕНИЕМ
// ==========================================
const searchQuery = ref('');
const sumFrom = ref('');
const sumBefore = ref('');
const jobsFrom = ref('');
const selectedIndustry = ref('');
const selectedOwnership = ref('');
const selectedTypeBuild = ref('');
const selectedCategory = ref('');
const selectedDateRealise = ref('');
const selectedStatus = ref('');

/**
 * Применение категории из параметров URL или sessionStorage
 * Вызывается при монтировании компонента
 */
const applyCategoryFromRoute = () => {
    // Сначала проверяем URL параметры
    const urlParams = new URLSearchParams(window.location.search);
    const categoryFromUrl = urlParams.get('category_name');
    
    if (categoryFromUrl) {
        selectedCategory.value = categoryFromUrl;
        // Очищаем sessionStorage после применения
        sessionStorage.removeItem('selectedCategory');
        
        // Объявляем для screen readers
        srAnnouncement.value = `Применён фильтр: категория ${categoryFromUrl}`;
        setTimeout(() => {
            srAnnouncement.value = '';
        }, 3000);
        
        return;
    }
    
    // Если в URL нет, проверяем sessionStorage
    const savedCategory = sessionStorage.getItem('selectedCategory');
    if (savedCategory) {
        try {
            const category = JSON.parse(savedCategory);
            selectedCategory.value = category.name;
            
            // Очищаем после применения
            sessionStorage.removeItem('selectedCategory');
            
            // Объявляем для screen readers
            srAnnouncement.value = `Применён фильтр: категория ${category.name}`;
            setTimeout(() => {
                srAnnouncement.value = '';
            }, 3000);
        } catch (e) {
            console.error('Ошибка парсинга категории из sessionStorage', e);
        }
    }
};
// Применяем категорию при монтировании
onMounted(() => {
    applyCategoryFromRoute();
});

// Временные значения для числовых инпутов (для debounce)
const sumFromInput = ref('');
const sumBeforeInput = ref('');
const jobsFromInput = ref('');
// ==========================================
// DEBOUNCE ДЛЯ ЧИСЛОВЫХ ПОЛЕЙ
// ==========================================
// Числовые поля обновляются с задержкой 500ms для избежания лагов при вводе
let sumFromTimeout = null;
let sumBeforeTimeout = null;
let jobsFromTimeout = null;
watch(sumFromInput, (newValue) => {
    clearTimeout(sumFromTimeout);
    sumFromTimeout = setTimeout(() => {
        sumFrom.value = newValue;
    }, 500);
});
watch(sumBeforeInput, (newValue) => {
    clearTimeout(sumBeforeTimeout);
    sumBeforeTimeout = setTimeout(() => {
        sumBefore.value = newValue;
    }, 500);
});
watch(jobsFromInput, (newValue) => {
    clearTimeout(jobsFromTimeout);
    jobsFromTimeout = setTimeout(() => {
        jobsFrom.value = newValue;
    }, 500);
});
// ==========================================
// АЛГОРИТМЫ ПОИСКА И СРАВНЕНИЯ
// ==========================================
/**
 * Вычисление расстояния Левенштейна между двумя строками
 * Используется для определения похожести слов при нечётком поиске
 * @param {string} a - первая строка
 * @param {string} b - вторая строка
 * @returns {number} - расстояние (количество изменений для преобразования a в b)
 */
const getLevenshteinDistance = (a, b) => {
    if (a.length === 0) return b.length;
    if (b.length === 0) return a.length;
    
    const matrix = [];
    for (let i = 0; i <= b.length; i++) {
        matrix[i] = [i];
    }
    for (let j = 0; j <= a.length; j++) {
        matrix[0][j] = j;
    }
    for (let i = 1; i <= b.length; i++) {
        for (let j = 1; j <= a.length; j++) {
            const cost = a[j - 1] === b[i - 1] ? 0 : 1;
            matrix[i][j] = Math.min(
                matrix[i - 1][j] + 1,
                matrix[i][j - 1] + 1,
                matrix[i - 1][j - 1] + cost
            );
        }
    }
    return matrix[b.length][a.length];
};
/**
 * Проверка являются ли два слова похожими
 * Используется для "умного" поиска с учётом опечаток
 * @param {string} word1 - первое слово
 * @param {string} word2 - второе слово
 * @returns {boolean} - true если слова похожи (70%+ совпадения)
 */
const isSimilarWord = (word1, word2) => {
    if (word1 === word2) return true;
    if (word1.includes(word2) || word2.includes(word1)) return true;
    
    const maxLength = Math.max(word1.length, word2.length);
    const distance = getLevenshteinDistance(word1, word2);
    const similarity = (maxLength - distance) / maxLength;
    
    return similarity > 0.7; // 70% схожести достаточно
};
/**
 * Проверка соответствует ли проект поисковому запросу
 * Ищет по названию и адресу, поддерживает нечёткий поиск
 * @param {Object} project - объект проекта
 * @param {string} searchTerm - поисковый запрос
 * @returns {boolean} - true если проект подходит под запрос
 */
const matchesSearch = (project, searchTerm) => {
    if (!searchTerm) return true;
    
    const searchLower = searchTerm.toLowerCase().trim();
    if (searchLower === '') return true;
    
    const title = (project.title || '').toLowerCase();
    const address = (project.address || '').toLowerCase();
    
    // Точное совпадение
    if (title.includes(searchLower)) return true;
    if (address.includes(searchLower)) return true;
    
    // Разбиваем на слова для нечёткого поиска
    const titleWords = title.split(/\s+/);
    const addressWords = address.split(/\s+/);
    const searchWords = searchLower.split(/\s+/);
    
    for (const searchWord of searchWords) {
        if (searchWord.length < 3) continue;
        
        // Ищем похожие слова в названии
        for (const titleWord of titleWords) {
            if (titleWord.length < 3) continue;
            if (isSimilarWord(titleWord, searchWord)) return true;
        }
        
        // Ищем похожие слова в адресе
        for (const addressWord of addressWords) {
            if (addressWord.length < 3) continue;
            if (isSimilarWord(addressWord, searchWord)) return true;
        }
    }
    
    return false;
};
/**
 * Поиск похожих проектов по текстовому запросу
 * Используется когда точное совпадение не найдено
 * Ранжирует проекты по релевантности и возвращает топ-8
 * @returns {Array} - массив похожих проектов
 */
const findSimilarProjects = () => {
    if (!searchQuery.value) return [];
    
    const searchLower = searchQuery.value.toLowerCase().trim();
    if (searchLower === '') return [];
    
    const scored = allProjects.value.map(project => {
        let score = 0;
        const title = (project.title || '').toLowerCase();
        const address = (project.address || '').toLowerCase();
        const titleWords = title.split(/\s+/);
        const addressWords = address.split(/\s+/);
        const searchWords = searchLower.split(/\s+/);
        
        for (const searchWord of searchWords) {
            if (searchWord.length < 2) continue;
            
            // Точное совпадение в названии - максимальный балл
            if (title.includes(searchWord)) {
                score += 100;
            }
            // Точное совпадение в адресе - чуть меньше баллов
            else if (address.includes(searchWord)) {
                score += 80;
            }
            // Похожие слова - меньше баллов
            else {
                for (const titleWord of titleWords) {
                    if (titleWord.length < 3) continue;
                    if (isSimilarWord(titleWord, searchWord)) {
                        score += 50;
                        break;
                    }
                }
                for (const addressWord of addressWords) {
                    if (addressWord.length < 3) continue;
                    if (isSimilarWord(addressWord, searchWord)) {
                        score += 40;
                        break;
                    }
                }
            }
        }
        
        return { project, score };
    });
    
    return scored
        .filter(item => item.score > 0)
        .sort((a, b) => b.score - a.score)
        .slice(0, 8)
        .map(item => item.project);
};
/**
 * Проверка соответствует ли проект выбранной категории
 * Категории хранятся как JSON-массив
 * @param {Object} project - объект проекта
 * @returns {boolean} - true если проект подходит под категорию
 */
const matchesCategory = (project) => {
    if (!selectedCategory.value) return true;
    try {
        const projectCats = Array.isArray(project.category) 
            ? project.category 
            : JSON.parse(project.category || '[]');
        return projectCats.includes(selectedCategory.value);
    } catch(e) {
        return false;
    }
};
/**
 * Проверка соответствует ли проект выбранному сроку реализации
 * @param {Object} project - объект проекта
 * @returns {boolean} - true если проект подходит под срок
 */
const matchesDateRealise = (project) => {
    if (!selectedDateRealise.value) return true;
    const months = Number(project.number_date_realise);
    switch (selectedDateRealise.value) {
        case '0-12': return months <= 12;
        case '12-24': return months >= 12 && months <= 24;
        case '24-36': return months >= 24 && months <= 36;
        case '36+': return months > 36;
        default: return true;
    }
};
/**
 * Поиск проектов с частичным совпадением фильтров
 * Используется когда строгие фильтры не дали результата
 * Каждое совпадение фильтра даёт баллы, проекты ранжируются
 * @returns {Array} - массив частично подходящих проектов
 */
const findPartiallyMatchingProjects = () => {
    const scored = allProjects.value.map(project => {
        let score = 0;
        // Точные совпадения категориальных фильтров
        if (selectedIndustry.value && project.activity === selectedIndustry.value) score += 3;
        if (selectedOwnership.value && project.ownership === selectedOwnership.value) score += 2;
        if (selectedTypeBuild.value && project.type_build === selectedTypeBuild.value) score += 2;
        if (selectedCategory.value && matchesCategory(project)) score += 2;
        if (selectedStatus.value && project.status === selectedStatus.value) score += 1;
        // Числовые фильтры с "мягкими" границами (допускаем отклонение 30%)
        if (sumFrom.value) {
            const diff = project.total_investment - Number(sumFrom.value);
            if (diff >= 0) score += 3; // Полное совпадение
            else if (Math.abs(diff) / Number(sumFrom.value) < 0.3) score += 1; // Близкое значение
        }
        if (sumBefore.value) {
            const diff = Number(sumBefore.value) - project.total_investment;
            if (diff >= 0) score += 3;
            else if (Math.abs(diff) / Number(sumBefore.value) < 0.3) score += 1;
        }
        if (jobsFrom.value) {
            if (project.count_new_job >= Number(jobsFrom.value)) score += 2;
            else if (project.count_new_job >= Number(jobsFrom.value) * 0.7) score += 1; // 70% от требуемого
        }
        return { project, score };
    });
    return scored
        .filter(item => item.score > 0)
        .sort((a, b) => b.score - a.score)
        .slice(0, 8)
        .map(item => item.project);
};
// ==========================================
// ДИНАМИЧЕСКИЕ ОПЦИИ ДЛЯ ФИЛЬТРОВ
// ==========================================
/**
 * Получение уникальных отраслей из всех проектов
 * Используется для заполнения select-списка
 */
const industries = computed(() => {
    const unique = [...new Set(allProjects.value.map(p => p.activity).filter(Boolean))];
    return [{ value: '', name: 'Все отрасли' }, ...unique.map(item => ({ value: item, name: item }))];
});
/**
 * Получение уникальных типов собственности из всех проектов
 */
const ownerships = computed(() => {
    const unique = [...new Set(allProjects.value.map(p => p.ownership).filter(Boolean))];
    return [{ value: '', name: 'Вся собственность' }, ...unique.map(item => ({ value: item, name: item }))];
});
/**
 * Получение уникальных видов строительства из всех проектов
 */
const typeBuilds = computed(() => {
    const unique = [...new Set(allProjects.value.map(p => p.type_build).filter(Boolean))];
    return [{ value: '', name: 'Все виды' }, ...unique.map(item => ({ value: item, name: item }))];
});
/**
 * Получение всех уникальных категорий из всех проектов
 * Категории хранятся как JSON, нужно их распарсить
 */
const allCategories = computed(() => {
    const catsSet = new Set();
    allProjects.value.forEach(project => {
        if (project.category) {
            try {
                const cats = Array.isArray(project.category) 
                    ? project.category 
                    : JSON.parse(project.category);
                cats.forEach(cat => catsSet.add(cat));
            } catch(e) {}
        }
    });
    return [{ value: '', name: 'Все категории' }, ...Array.from(catsSet).map(cat => ({ value: cat, name: cat }))];
});
/**
 * Статические опции для фильтра по сроку реализации
 */
const dateRealiseOptions = [
    { value: '', name: 'Все сроки' },
    { value: '0-12', name: 'до 12 месяцев' },
    { value: '12-24', name: '12-24 месяца' },
    { value: '24-36', name: '24-36 месяцев' },
    { value: '36+', name: 'более 36 месяцев' }
];
/**
 * Получение уникальных статусов проектов (только для админа)
 */
const statusOptions = computed(() => {
    const unique = [...new Set(allProjects.value.map(p => p.status).filter(Boolean))];
    return [{ value: '', name: 'Все статусы' }, ...unique.map(item => ({ value: item, name: item }))];
});
// ==========================================
// ФИЛЬТРАЦИЯ ПРОЕКТОВ
// ==========================================
/**
 * ТОЧНАЯ фильтрация проектов
 * Применяет все активные фильтры со строгими условиями
 * @returns {Array} - массив проектов, точно соответствующих фильтрам
 */
const exactFilteredProjects = computed(() => {
    return allProjects.value.filter(project => {
        // Поисковый запрос (с нечётким поиском)
        if (!matchesSearch(project, searchQuery.value)) return false;
        
        // Статус (только для админа)
        if (userRole.value === 'Admin' && selectedStatus.value && project.status !== selectedStatus.value) return false;
        
        // Диапазон суммы инвестиций
        if (sumFrom.value && (!project.total_investment || project.total_investment < Number(sumFrom.value))) return false;
        if (sumBefore.value && (!project.total_investment || project.total_investment > Number(sumBefore.value))) return false;
        
        // Минимум рабочих мест
        if (jobsFrom.value && (!project.count_new_job || project.count_new_job < Number(jobsFrom.value))) return false;
        
        // Категориальные фильтры
        if (selectedIndustry.value && project.activity !== selectedIndustry.value) return false;
        if (selectedOwnership.value && project.ownership !== selectedOwnership.value) return false;
        if (selectedTypeBuild.value && project.type_build !== selectedTypeBuild.value) return false;
        if (!matchesCategory(project)) return false;
        if (!matchesDateRealise(project)) return false;
        
        return true;
    });
});
/**
 * ПОХОЖИЕ проекты (показываются когда точные не найдены)
 * Применяет "мягкую" логику поиска в 3 этапа:
 * 1. Если есть поиск - убирает только поиск, остальные фильтры оставляет
 * 2. Если и так пусто - ищет похожие по тексту без фильтров
 * 3. Если поиска нет - частичное совпадение по фильтрам
 * 4. В крайнем случае - первые 8 проектов из всех
 * @returns {Array} - массив рекомендуемых проектов
 */
const similarProjects = computed(() => {
    // Если точные результаты есть - похожие не нужны
    if (exactFilteredProjects.value.length > 0) return [];
    // Если есть текстовый поиск
    if (searchQuery.value) {
        // Шаг 1: убираем только поиск, остальные фильтры оставляем
        const filtered = allProjects.value.filter(project => {
            if (userRole.value === 'Admin' && selectedStatus.value && project.status !== selectedStatus.value) return false;
            if (sumFrom.value && (!project.total_investment || project.total_investment < Number(sumFrom.value))) return false;
            if (sumBefore.value && (!project.total_investment || project.total_investment > Number(sumBefore.value))) return false;
            if (jobsFrom.value && (!project.count_new_job || project.count_new_job < Number(jobsFrom.value))) return false;
            if (selectedIndustry.value && project.activity !== selectedIndustry.value) return false;
            if (selectedOwnership.value && project.ownership !== selectedOwnership.value) return false;
            if (selectedTypeBuild.value && project.type_build !== selectedTypeBuild.value) return false;
            if (!matchesCategory(project)) return false;
            if (!matchesDateRealise(project)) return false;
            return true;
        });
        // Если после снятия поиска что-то есть - показываем это
        if (filtered.length > 0) return filtered.slice(0, 8);
        // Шаг 2: ищем похожие по тексту из ВСЕХ проектов (без всех фильтров)
        const byText = findSimilarProjects();
        return byText.length > 0 ? byText : allProjects.value.slice(0, 8);
    }
    // Нет поиска, но другие фильтры дали 0 результатов
    // Шаг 3: частичное совпадение по фильтрам
    const partial = findPartiallyMatchingProjects();
    return partial.length > 0 ? partial : allProjects.value.slice(0, 8);
});
/**
 * Итоговые проекты для отображения
 * Сначала пытается показать точные, если нет - похожие
 * @returns {Array} - массив проектов для показа
 */
const displayProjects = computed(() => {
    if (exactFilteredProjects.value.length > 0) {
        return exactFilteredProjects.value;
    }
    return similarProjects.value;
});
/**
 * Проекты для текущей страницы пагинации
 * @returns {Array} - массив проектов для текущей страницы
 */
const paginatedProjects = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return displayProjects.value.slice(start, end);
});
/**
 * Общее количество проектов после фильтрации
 */
const totalDisplayCount = computed(() => displayProjects.value.length);
/**
 * Общее количество страниц пагинации
 */
const totalPages = computed(() => Math.ceil(totalDisplayCount.value / itemsPerPage));
/**
 * Флаг показывающий, что отображаются рекомендации (не точные результаты)
 */
const isShowingRecommendations = computed(() => exactFilteredProjects.value.length === 0 && similarProjects.value.length > 0);
/**
 * Текст сообщения о результатах фильтрации
 * Показывается когда точных результатов нет
 * @returns {string} - текст сообщения
 */
const messageText = computed(() => {
    if (exactFilteredProjects.value.length === 0 && hasActiveFilters.value) {
        if (searchQuery.value) {
            return `По запросу «${searchQuery.value}» ничего не найдено, но возможно вам понравится:`;
        }
        return 'По заданным фильтрам ничего не найдено. Похожие проекты:';
    }
    return '';
});
// ==========================================
// ОТСЛЕЖИВАНИЕ ИЗМЕНЕНИЙ
// ==========================================
/**
 * Сброс пагинации при изменении активных фильтров
 * При изменении любого фильтра возвращаемся на первую страницу
 */
watch(searchQuery, () => resetPage());
watch(sumFrom, () => resetPage());
watch(sumBefore, () => resetPage());
watch(jobsFrom, () => resetPage());
watch(selectedIndustry, () => resetPage());
watch(selectedOwnership, () => resetPage());
watch(selectedTypeBuild, () => resetPage());
watch(selectedCategory, () => resetPage());
watch(selectedDateRealise, () => resetPage());
watch(selectedStatus, () => resetPage());
/**
 * Проверка есть ли активные фильтры
 * Используется для показа кнопки "Сбросить" и активных фильтров
 */
const hasActiveFilters = computed(() => {
    return searchQuery.value || 
        sumFrom.value || 
        sumBefore.value || 
        jobsFrom.value ||
        selectedIndustry.value || 
        selectedOwnership.value || 
        selectedTypeBuild.value ||
        selectedCategory.value ||
        selectedDateRealise.value ||
        (userRole.value === 'Admin' && selectedStatus.value);
});
/**
 * Полный сброс всех фильтров
 * Очищает все значения фильтров и сбрасывает пагинацию
 */
const clearFilters = () => {
    searchQuery.value = '';
    sumFrom.value = '';
    sumBefore.value = '';
    jobsFrom.value = '';
    sumFromInput.value = '';
    sumBeforeInput.value = '';
    jobsFromInput.value = '';
    selectedIndustry.value = '';
    selectedOwnership.value = '';
    selectedTypeBuild.value = '';
    selectedCategory.value = '';
    selectedDateRealise.value = '';
    if (userRole.value === 'Admin') {
        selectedStatus.value = '';
    }
    resetPage();
};
/**
 * Ручное применение текущих значений фильтров (запасной вариант)
 * Копирует значения из input-полей в активные фильтры
 */
const applyFiltersManually = () => {
    // Для чисел берём значения из input
    sumFrom.value = sumFromInput.value;
    sumBefore.value = sumBeforeInput.value;
    jobsFrom.value = jobsFromInput.value;
    resetPage();
};
/**
 * Переход на указанную страницу пагинации
 * @param {number} page - номер страницы
 */
const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value && page !== currentPage.value) {
        currentPage.value = page;
    }
};
// ==========================================
// ЦВЕТОВАЯ СХЕМА
// ==========================================
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
            <TitlePage value="Все проекты"></TitlePage>
        </template>
        <div class="mx-auto py-6 pb-0 px-4 sm:px-10 lg:px-16">
            <!-- Фильтрация -->
            <section aria-labelledby="filters-title">
                <div class="w-full rounded-xl p-4 sm:p-6 mb-6" :style="{ backgroundColor: colors.bgLight, border: `2px solid ${colors.border}` }">
                    <h2 id="filters-title" class="sr-only">Фильтры поиска проектов</h2>
                    
                    <!-- Поиск и кнопки управления -->
                    <div class="flex flex-col sm:flex-row items-center gap-3 mb-4">
                        <div class="flex-1 w-full">
                            <SearchInput v-model="searchQuery" placeholder="Введите название или адрес проекта..." class="w-full" />
                        </div>
                        
                        <div class="flex gap-2 w-full sm:w-auto">
                            <!-- Кнопка "Применить сейчас" (запасная, если автообновление глючит) -->
                            <fwb-button 
                                @click="applyFiltersManually"
                                color="default"
                                class="flex-1 sm:flex-none h-[46px]"
                                title="Применить фильтрацию и поиск"
                            >
                                Применить
                            </fwb-button>
                            
                            <!-- Кнопка "Сбросить" -->
                            <fwb-button 
                                @click="clearFilters"
                                color="light"
                                class="flex-1 sm:flex-none h-[46px]"
                                :class="{ 'opacity-50': !hasActiveFilters }"
                                :disabled="!hasActiveFilters"
                            >
                                Сбросить
                            </fwb-button>
                        </div>
                    </div>
                    <!-- Числовые и основные фильтры -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                        <fwb-input 
                            v-model="sumFromInput" 
                            placeholder="Сумма от (₽)" 
                            type="number" 
                            class="w-full"
                            title="Сумма от"
                        />
                        <fwb-input 
                            v-model="sumBeforeInput" 
                            placeholder="Сумма до (₽)" 
                            type="number" 
                            class="w-full"
                            title="Сумма до"
                        />
                        <fwb-input 
                            v-model="jobsFromInput" 
                            placeholder="Рабочих мест от" 
                            type="number" 
                            class="w-full"
                            title="Рабочих мест от количество"
                        />
                        <fwb-select v-model="selectedTypeBuild" :options="typeBuilds" placeholder="Вид строительства" class="w-full" />
                    </div>
                    <!-- Дополнительные фильтры -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <fwb-select v-model="selectedCategory" :options="allCategories" placeholder="Категория" class="w-full" />
                        <fwb-select v-model="selectedDateRealise" :options="dateRealiseOptions" placeholder="Срок реализации" class="w-full" />
                        <fwb-select v-model="selectedIndustry" :options="industries" placeholder="Отрасль" class="w-full" />
                        <fwb-select v-model="selectedOwnership" :options="ownerships" placeholder="Собственность" class="w-full" />
                    </div>
                    <!-- Фильтр по статусу (только для админа) -->
                    <div v-if="userRole === 'Admin'" class="mt-4">
                        <fwb-select v-model="selectedStatus" :options="statusOptions" placeholder="Статус проекта" class="w-full max-w-[250px]" />
                    </div>
                    <!-- Активные фильтры (чипсы) -->
                    <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
                        <span class="text-white text-sm font-semibold mr-2" :style="{ color: colors.accent }">Активные фильтры:</span>
                        
                        <span v-if="searchQuery" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            Поиск: "{{ searchQuery }}"
                            <button @click="searchQuery = ''" class="hover:text-red-600" :style="{ color: colors.accent }">×</button>
                        </span>
                        
                        <span v-if="selectedCategory" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            Категория: {{ selectedCategory }}
                            <button @click="selectedCategory = ''" class="hover:text-red-600" :style="{ color: colors.accent }">×</button>
                        </span>
                        
                        <span v-if="selectedDateRealise" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            Срок: {{ dateRealiseOptions.find(o => o.value === selectedDateRealise)?.name }}
                            <button @click="selectedDateRealise = ''" class="hover:text-red-600" :style="{ color: colors.accent }">×</button>
                        </span>
                        
                        <span v-if="selectedStatus && userRole === 'Admin'" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            Статус: {{ selectedStatus }}
                            <button @click="selectedStatus = ''" class="hover:text-red-600" :style="{ color: colors.accent }">×</button>
                        </span>
                        
                        <span v-if="sumFrom" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            Сумма от: {{ Number(sumFrom).toLocaleString('ru-RU') }} ₽
                            <button @click="sumFrom = ''; sumFromInput = ''" class="hover:text-red-600" :style="{ color: colors.accent }">×</button>
                        </span>
                        
                        <span v-if="sumBefore" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            Сумма до: {{ Number(sumBefore).toLocaleString('ru-RU') }} ₽
                            <button @click="sumBefore = ''; sumBeforeInput = ''" class="hover:text-red-600" :style="{ color: colors.accent }">×</button>
                        </span>
                        
                        <span v-if="jobsFrom" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            Рабочих мест от: {{ jobsFrom }}
                            <button @click="jobsFrom = ''; jobsFromInput = ''" class="hover:text-red-600" :style="{ color: colors.accent }">×</button>
                        </span>
                        
                        <span v-if="selectedIndustry" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            Отрасль: {{ selectedIndustry }}
                            <button @click="selectedIndustry = ''" class="hover:text-red-600" :style="{ color: colors.accent }">×</button>
                        </span>
                        
                        <span v-if="selectedOwnership" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            Собственность: {{ selectedOwnership }}
                            <button @click="selectedOwnership = ''" class="hover:text-red-600" :style="{ color: colors.accent }">×</button>
                        </span>
                        
                        <span v-if="selectedTypeBuild" class="px-3 py-1 rounded-full text-sm flex items-center gap-2" :style="{ backgroundColor: colors.bgDark, color: colors.white, border: `1px solid ${colors.border}` }">
                            Вид строительства: {{ selectedTypeBuild }}
                            <button @click="selectedTypeBuild = ''" class="hover:text-red-600" :style="{ color: colors.accent }">×</button>
                        </span>
                    </div>
                </div>
            </section>
            
            <!-- Проекты -->
            <section aria-labelledby="projects-title">
                <h2 id="projects-title" class="sr-only">Список проектов</h2>
                
                <div class="w-full border-2 rounded-xl border-white h-max flex flex-col mt-6 shadow-lg" :style="{ backgroundColor: mainColor }">
                    <!-- Сообщение о результатах -->
                    <div v-if="messageText" class="text-center py-6 px-4">
                        <h4 class="text-xl sm:text-2xl font-medium" :style="{ color: colors.accent }">{{ messageText }}</h4>
                        <p v-if="isShowingRecommendations" class="text-sm mt-2" :style="{ color: colors.white80 }">
                            Показаны проекты, которые могут вас заинтересовать
                        </p>
                    </div>
                    <!-- Счётчик проектов и переключатель вида -->
                    <div v-if="totalDisplayCount > 0" class="flex w-full justify-between items-center p-4 sm:p-6 flex-wrap gap-4">
                        <div class="text-white text-lg sm:text-xl font-semibold">
                            {{ totalDisplayCount }} {{ totalDisplayCount === 1 ? 'проект' : totalDisplayCount < 5 ? 'проекта' : 'проектов' }}
                        </div>
                        <div class="flex bg-white gap-4 p-3 rounded-xl shadow-md">
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
                    <!-- Нет проектов -->
                    <div v-if="totalDisplayCount === 0" class="text-center py-16 px-4">
                        <svg class="mx-auto h-24 w-24 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-4 text-2xl font-medium text-white">Проекты не найдены</h3>
                        <fwb-button 
                            v-if="hasActiveFilters"
                            @click="clearFilters"
                            class="mt-4"
                            color="light"
                        >
                            Сбросить фильтры
                        </fwb-button>
                    </div>
                    
                    <!-- Вид списком -->    
                    <div v-else-if="switcherViewCard && paginatedProjects.length > 0" class="px-4 sm:px-8 pb-6">
                        <div class="w-full flex flex-col my-5 gap-4">
                            <CardProjectList 
                                v-for="project in paginatedProjects" 
                                :key="project.id" 
                                :project="project"
                                :isFavorited="isProjectFavorited(project.id)"
                            />
                        </div>
                    </div>
                    
                    <!-- Вид карточками -->
                    <div v-else-if="!switcherViewCard && paginatedProjects.length > 0" class="px-4 sm:px-8 pb-6">
                        <div class="w-full grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 my-5 gap-6">
                            <CardBox 
                                v-for="project in paginatedProjects" 
                                :key="project.id" 
                                :project="project"
                                :isFavorited="isProjectFavorited(project.id)"                            
                            />
                        </div>
                    </div>
                    
                    <!-- Пагинация -->
                    <Paginate
                        v-if="totalPages > 1 && paginatedProjects.length > 0"
                        :current-page="currentPage"
                        :total-pages="totalPages"
                        @update:page="goToPage"
                    />
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>