<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted } from 'vue';
import TitlePage from '@/Layouts/TitlePage.vue';

// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    page: 'var(--color-page)',
    light: 'var(--color-light)',
    brand: 'var(--color-brand)',
    accent: 'var(--color-accent)',
    hover: 'var(--color-hover)',
    white: 'var(--color-white)',
    text: 'var(--color-text)',
    error: 'var(--color-error)',
    success: 'var(--color-success)',
};

const fonts = {
    heading: 'var(--font-heading)',
    body: 'var(--font-body)',
};

const shadows = {
    lg: 'var(--shadow-lg)',
};

const transitions = {
    normal: 'var(--transition-normal)',
};

const props = defineProps({
    project: {
        type: Object,
        required: true
    }
});

// Массивы
const categories = ref(['']);
const expenses = ref([{ item_name: '', amount: '' }]);
const revenues = ref([{ year: 1, amount: '' }]);
const yearlyExpenses = ref([{ year: 1, amount: '' }]);

// Массив для превью фотографий
const existingPhotos = ref([]);
const photoPreviews = ref([]);
const photosToDelete = ref([]);

const form = useForm({
    name: '',
    shotr_descr: '',
    full_descr: '',
    categories: [],
    fotos: [],
    deleted_photos: [],
    ownShip: '',
    activity: '',
    type_building: '',
    addres: '',
    latitude: '',
    longitude: '',
    total_investment: '',
    number_date_realise: '',
    count_new_job: '',
    collected_total_investment: '',
    revenues: [],
    yearly_expenses: [],
    expenses: [],
    _method: 'PATCH'
});

// Инициализация данных при загрузке
onMounted(() => {
    form.name = props.project.title || '';
    form.shotr_descr = props.project.short_description || '';
    form.full_descr = props.project.full_description || '';
    
    if (props.project.category) {
        const cats = Array.isArray(props.project.category) ? props.project.category : JSON.parse(props.project.category);
        categories.value = cats.length > 0 ? cats : [''];
    }
    
    form.ownShip = props.project.ownership || '';
    form.activity = props.project.activity || '';
    form.type_building = props.project.type_build || '';
    form.addres = props.project.address || '';
    form.latitude = props.project.latitude || '';
    form.longitude = props.project.longitude || '';
    
    form.total_investment = props.project.total_investment || '';
    form.number_date_realise = props.project.number_date_realise || '';
    form.count_new_job = props.project.count_new_job || '';
    form.collected_total_investment = props.project.collected_total_investment || '';
    
    if (props.project.photos && props.project.photos.length > 0) {
        existingPhotos.value = props.project.photos.map(photo => ({
            id: photo.id,
            url: `/storage/${photo.photo_path}`,
            name: photo.photo_path.split('/').pop()
        }));
    }
    
    if (props.project.investments && props.project.investments.length > 0) {
        expenses.value = props.project.investments.map(inv => ({ item_name: inv.item_name, amount: inv.amount }));
    }
    
    if (props.project.forecasts && props.project.forecasts.length > 0) {
        revenues.value = props.project.forecasts.map(forecast => ({ year: forecast.year, amount: forecast.expected_revenue }));
        yearlyExpenses.value = props.project.forecasts.map(forecast => ({ year: forecast.year, amount: forecast.expected_expenses }));
    }
});

// ========== КАТЕГОРИИ ==========
const addCategory = () => { if (categories.value.length < 4) categories.value.push(''); };
const removeCategory = (index) => { if (categories.value.length > 1) categories.value.splice(index, 1); };

// ========== СТРУКТУРА ТРАТ ==========
const addExpense = () => expenses.value.push({ item_name: '', amount: '' });
const removeExpense = (index) => { if (expenses.value.length > 1) expenses.value.splice(index, 1); };

// ========== ДОХОДЫ ПО ГОДАМ ==========
const addRevenue = () => {
    const nextYear = revenues.value.length + 1;
    revenues.value.push({ year: nextYear, amount: '' });
};
const removeRevenue = (index) => {
    if (revenues.value.length > 1) {
        revenues.value.splice(index, 1);
        revenues.value.forEach((item, idx) => { item.year = idx + 1; });
    }
};

// ========== РАСХОДЫ ПО ГОДАМ ==========
const addYearlyExpense = () => {
    const nextYear = yearlyExpenses.value.length + 1;
    yearlyExpenses.value.push({ year: nextYear, amount: '' });
};
const removeYearlyExpense = (index) => {
    if (yearlyExpenses.value.length > 1) {
        yearlyExpenses.value.splice(index, 1);
        yearlyExpenses.value.forEach((item, idx) => { item.year = idx + 1; });
    }
};

// ========== ФОТО ==========
const removeExistingPhoto = (index) => {
    const photo = existingPhotos.value[index];
    photosToDelete.value.push(photo.id);
    existingPhotos.value.splice(index, 1);
};

const handleFileUpload = (event) => {
    const files = Array.from(event.target.files);
    form.fotos = [...form.fotos, ...files];
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreviews.value.push({ url: e.target.result, name: file.name, size: (file.size / 1024).toFixed(2) });
        };
        reader.readAsDataURL(file);
    });
    event.target.value = '';
};

const removePhoto = (index) => {
    form.fotos.splice(index, 1);
    photoPreviews.value.splice(index, 1);
};

// ========== ОТПРАВКА ФОРМЫ ==========
const submit = () => {
    form.categories = categories.value.filter(cat => cat && cat.trim() !== '');
    form.deleted_photos = photosToDelete.value;
    form.expenses = expenses.value.filter(exp => {
        const hasItemName = exp.item_name && String(exp.item_name).trim() !== '';
        const hasAmount = exp.amount !== '' && exp.amount !== null && exp.amount !== undefined;
        return hasItemName && hasAmount;
    });
    form.revenues = revenues.value.filter(rev => rev.amount !== '' && rev.amount !== null && rev.amount !== undefined);
    form.yearly_expenses = yearlyExpenses.value.filter(exp => exp.amount !== '' && exp.amount !== null && exp.amount !== undefined);

    form.post(route('projects.update', props.project.id), {
        forceFormData: true,
        onSuccess: () => { console.log('Проект успешно обновлен'); },
        onError: (errors) => { console.log('Ошибки валидации:', errors); }
    });
};
</script>

<template>
    <Head title="Редактирование проекта">
        <meta name="description" content="Форма редактирования инвестиционного проекта">
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <TitlePage id="editProject" value="Редактирование проекта" />
        </template>
        
        <div class="mx-auto py-6 px-4 sm:px-10 lg:px-16" :style="{ backgroundColor: colors.page }">
            <form @submit.prevent="submit" class="mx-auto max-w-7xl">
                <div class="rounded-2xl p-6 mb-6 shadow-lg border-2" :style="{ backgroundColor: colors.light, borderColor: colors.accent, boxShadow: shadows.lg }">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b-2" :style="{ borderColor: colors.accent }">
                        <h2 class="text-2xl font-heading font-bold" :style="{ color: colors.brand, fontFamily: fonts.heading }">Основная информация</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="name" value="Название проекта *" class="mb-2" :style="{ color: colors.brand }" />
                            <TextInput id="name" type="text" class="block w-full rounded-lg" v-model="form.name" placeholder="Введите название проекта" />
                            <InputError class="mt-2" :message="form.errors.name" role="alert" />
                        </div>
                        <div>
                            <InputLabel for="shotr_descr" value="Короткое описание *" class="mb-2" :style="{ color: colors.brand }" />
                            <TextInput id="shotr_descr" type="text" class="block w-full rounded-lg" v-model="form.shotr_descr" placeholder="Краткое описание проекта" />
                            <InputError class="mt-2" :message="form.errors.shotr_descr" role="alert" />
                        </div>
                        <div class="lg:col-span-2">
                            <InputLabel for="full_descr" value="Подробное описание *" class="mb-2" :style="{ color: colors.brand }" />
                            <textarea 
                                id="full_descr" 
                                class="block w-full h-32 rounded-xl px-4 shadow-sm transition-all duration-200 outline-none disabled:cursor-not-allowed disabled:opacity-60"
                                :style="{ 
                                    border: `1px solid ${colors.light}`, 
                                    backgroundColor: colors.page, 
                                    color: colors.brand,
                                    transition: transitions.normal
                                }"
                                v-model="form.full_descr"  
                                placeholder="Подробное описание проекта..."
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.full_descr" role="alert" />
                        </div>
                    </div>
                    
                    <!-- Категории -->
                    <div class="mt-6 pt-6 border-t-2" :style="{ borderColor: colors.accent }">
                        <InputLabel value="Категории проекта (до 4 категорий)" class="mb-4" :style="{ color: colors.brand }" />
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="(category, index) in categories" :key="index" class="flex items-center gap-3">
                                <TextInput :id="`category-${index}`" type="text" class="block w-full rounded-lg" v-model="categories[index]" :placeholder="`Категория ${index + 1}`" />
                                <button v-if="categories.length > 1" type="button" @click="removeCategory(index)" class="p-3 rounded-lg transition-all hover:scale-110 flex-shrink-0" :style="{ backgroundColor: colors.error }" aria-label="Удалить категорию">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="{ color: colors.white }"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                                <button v-if="index === categories.length - 1 && categories.length < 4" type="button" @click="addCategory" class="p-3 rounded-lg transition-all hover:scale-110 flex-shrink-0" :style="{ backgroundColor: colors.success }" aria-label="Добавить категорию">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="{ color: colors.white }"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                </button>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.categories" role="alert" />
                    </div>
                    
                    <!-- Фото -->
                    <div class="mt-6 pt-6 border-t-2" :style="{ borderColor: colors.accent }">
                        <InputLabel for="fotos" value="Изображения проекта" class="mb-4" :style="{ color: colors.brand }" />
                        <label for="fotos" class="cursor-pointer inline-flex items-center gap-2 px-6 py-3 font-semibold rounded-lg transition-all hover:scale-105" :style="{ backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.accent}` }">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Добавить изображения
                        </label>
                        <input id="fotos" type="file" @change="handleFileUpload" accept="image/*" multiple class="hidden" />
                        
                        <!-- Существующие фотографии -->
                        <div v-if="existingPhotos.length > 0" class="mt-6">
                            <p class="text-base mb-2" :style="{ color: colors.accent }">Текущие фотографии:</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                <div v-for="(photo, index) in existingPhotos" :key="photo.id" class="relative group rounded-lg overflow-hidden shadow-lg" :style="{ border: `1px solid ${colors.accent}` }">
                                    <img :src="photo.url" :alt="photo.name" class="w-full h-48 object-cover">
                                    <button type="button" @click="removeExistingPhoto(index)" class="absolute top-2 right-2 p-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity" :style="{ backgroundColor: colors.error }" aria-label="Удалить фото">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="{ color: colors.white }"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Новые фотографии -->
                        <div v-if="photoPreviews.length > 0" class="mt-6">
                            <p class="text-base mb-2" :style="{ color: colors.accent }">Новые фотографии:</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                <div v-for="(preview, index) in photoPreviews" :key="index" class="relative group rounded-lg overflow-hidden shadow-lg" :style="{ border: `1px solid ${colors.accent}` }">
                                    <img :src="preview.url" :alt="preview.name" class="w-full h-48 object-cover">
                                    <button type="button" @click="removePhoto(index)" class="absolute top-2 right-2 p-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity" :style="{ backgroundColor: colors.error }" aria-label="Удалить фото">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="{ color: colors.white }"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.fotos" role="alert" />
                    </div>
                </div>
                
                <div class="rounded-2xl p-6 mb-6 shadow-lg border-2" :style="{ backgroundColor: colors.light, borderColor: colors.accent, boxShadow: shadows.lg }">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b-2" :style="{ borderColor: colors.accent }">
                        <h2 class="text-2xl font-heading font-bold" :style="{ color: colors.brand, fontFamily: fonts.heading }">Характеристики проекта</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div><InputLabel for="ownShip" value="Собственность" class="mb-2" :style="{ color: colors.brand }" /><TextInput id="ownShip" type="text" class="block w-full rounded-lg" v-model="form.ownShip" placeholder="Тип собственности" /><InputError class="mt-2" :message="form.errors.ownShip" role="alert" /></div>
                        <div><InputLabel for="activity" value="Деятельность" class="mb-2" :style="{ color: colors.brand }" /><TextInput id="activity" type="text" class="block w-full rounded-lg" v-model="form.activity" placeholder="Вид деятельности" /><InputError class="mt-2" :message="form.errors.activity" role="alert" /></div>
                        <div><InputLabel for="type_building" value="Вид строительства" class="mb-2" :style="{ color: colors.brand }" /><TextInput id="type_building" type="text" class="block w-full rounded-lg" v-model="form.type_building" placeholder="Тип строительства" /><InputError class="mt-2" :message="form.errors.type_building" role="alert" /></div>
                        <div class="md:col-span-2 lg:col-span-3"><InputLabel for="addres" value="Адрес *" class="mb-2" :style="{ color: colors.brand }" /><TextInput id="addres" type="text" class="block w-full rounded-lg" v-model="form.addres" placeholder="Адрес проекта" /><InputError class="mt-2" :message="form.errors.addres" role="alert" /></div>
                        <div><InputLabel for="latitude" value="Широта *" class="mb-2" :style="{ color: colors.brand }" /><TextInput id="latitude" type="text" class="block w-full rounded-lg" v-model="form.latitude" placeholder="55.7558" /><InputError class="mt-2" :message="form.errors.latitude" role="alert" /></div>
                        <div><InputLabel for="longitude" value="Долгота *" class="mb-2" :style="{ color: colors.brand }" /><TextInput id="longitude" type="text" class="block w-full rounded-lg" v-model="form.longitude" placeholder="37.6173" /><InputError class="mt-2" :message="form.errors.longitude" role="alert" /></div>
                    </div>
                </div>
                
                <div class="rounded-2xl p-6 mb-6 shadow-lg border-2" :style="{ backgroundColor: colors.light, borderColor: colors.accent, boxShadow: shadows.lg }">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b-2" :style="{ borderColor: colors.accent }">
                        <h2 class="text-2xl font-heading font-bold" :style="{ color: colors.brand, fontFamily: fonts.heading }">Финансовые показатели</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        <div><InputLabel for="total_investment" value="Требуемая сумма (₽) *" class="mb-2" :style="{ color: colors.brand }" /><TextInput id="total_investment" type="number" class="block w-full rounded-lg" v-model="form.total_investment" placeholder="1000000" /><InputError class="mt-2" :message="form.errors.total_investment" role="alert" /></div>
                        <div><InputLabel for="collected_total_investment" value="Уже собрано (₽)" class="mb-2" :style="{ color: colors.brand }" /><TextInput id="collected_total_investment" type="number" class="block w-full rounded-lg" v-model="form.collected_total_investment" placeholder="500000" /><InputError class="mt-2" :message="form.errors.collected_total_investment" role="alert" /></div>
                        <div><InputLabel for="number_date_realise" value="Срок реализации (мес.) *" class="mb-2" :style="{ color: colors.brand }" /><TextInput id="number_date_realise" type="number" class="block w-full rounded-lg" v-model="form.number_date_realise" placeholder="24" /><InputError class="mt-2" :message="form.errors.number_date_realise" role="alert" /></div>
                        <div><InputLabel for="count_new_job" value="Рабочих мест *" class="mb-2" :style="{ color: colors.brand }" /><TextInput id="count_new_job" type="number" class="block w-full rounded-lg" v-model="form.count_new_job" placeholder="10" /><InputError class="mt-2" :message="form.errors.count_new_job" role="alert" /></div>
                    </div>
                    
                    <!-- ДОХОДЫ ПО ГОДАМ -->
                    <div class="mt-8 pt-6 border-t-2" :style="{ borderColor: colors.accent }">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-6 h-6" :style="{ color: colors.success }" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/></svg>
                            <InputLabel value="Ожидаемая доходность по годам" class="text-lg" :style="{ color: colors.brand }" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <div v-for="(revenue, index) in revenues" :key="`revenue-${index}`" class="rounded-lg p-4 transition-all border-2" :style="{ backgroundColor: colors.brand, borderColor: colors.success }">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-heading font-bold text-lg" :style="{ color: colors.white, fontFamily: fonts.heading }">Год {{ revenue.year }}</span>
                                    <div class="flex gap-2"><button v-if="revenues.length > 1" type="button" @click="removeRevenue(index)" class="p-2 rounded-lg transition-all hover:scale-110" :style="{ backgroundColor: colors.error }" aria-label="Удалить год"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="{ color: colors.white }"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button><button v-if="index === revenues.length - 1" type="button" @click="addRevenue" class="p-2 rounded-lg transition-all hover:scale-110" :style="{ backgroundColor: colors.success }" aria-label="Добавить год"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="{ color: colors.white }"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg></button></div>
                                </div>
                                <TextInput :id="`revenue-${index}`" type="number" step="0.01" class="block w-full rounded-lg" v-model.number="revenues[index].amount" placeholder="Доход в ₽" />
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.revenues" role="alert" />
                    </div>
                    
                    <!-- РАСХОДЫ ПО ГОДАМ -->
                    <div class="mt-8 pt-6 border-t-2" :style="{ borderColor: colors.accent }">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-6 h-6" :style="{ color: colors.error }" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd"/></svg>
                            <InputLabel value="Ожидаемые расходы по годам" class="text-lg" :style="{ color: colors.brand }" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <div v-for="(expense, index) in yearlyExpenses" :key="`yearly-expense-${index}`" class="rounded-lg p-4 transition-all border-2" :style="{ backgroundColor: colors.brand, borderColor: colors.error }">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-heading font-bold text-lg" :style="{ color: colors.white, fontFamily: fonts.heading }">Год {{ expense.year }}</span>
                                    <div class="flex gap-2"><button v-if="yearlyExpenses.length > 1" type="button" @click="removeYearlyExpense(index)" class="p-2 rounded-lg transition-all hover:scale-110" :style="{ backgroundColor: colors.error }" aria-label="Удалить год"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="{ color: colors.white }"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button><button v-if="index === yearlyExpenses.length - 1" type="button" @click="addYearlyExpense" class="p-2 rounded-lg transition-all hover:scale-110" :style="{ backgroundColor: colors.success }" aria-label="Добавить год"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="{ color: colors.white }"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg></button></div>
                                </div>
                                <TextInput :id="`yearly-expense-${index}`" type="number" step="0.01" class="block w-full rounded-lg" v-model.number="yearlyExpenses[index].amount" placeholder="Расход в ₽" />
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.yearly_expenses" role="alert" />
                    </div>
                    
                    <!-- СТРУКТУРА ТРАТ -->
                    <div class="mt-8 pt-6 border-t-2" :style="{ borderColor: colors.accent }">
                        <InputLabel value="Структура трат (детализация)" class="text-lg mb-4" :style="{ color: colors.brand }" />
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div v-for="(expense, index) in expenses" :key="`expense-${index}`" class="rounded-lg p-4 border-2" :style="{ backgroundColor: colors.brand, borderColor: colors.accent }">
                                <div class="space-y-3">
                                    <div><InputLabel :for="`item_name-${index}`" :value="`Статья ${index + 1}`" class="mb-2" :style="{ color: colors.white }" /><TextInput :id="`item_name-${index}`" type="text" class="block w-full rounded-lg" v-model="expenses[index].item_name" placeholder="Название статьи расходов" /></div>
                                    <div><InputLabel :for="`amount-${index}`" value="Сумма (₽)" class="mb-2" :style="{ color: colors.white }" /><TextInput :id="`amount-${index}`" type="number" step="0.01" class="block w-full rounded-lg" v-model.number="expenses[index].amount" placeholder="100000" /></div>
                                    <div class="flex gap-2 justify-end"><button v-if="expenses.length > 1" type="button" @click="removeExpense(index)" class="px-4 py-2 rounded-lg transition-all hover:scale-110" :style="{ backgroundColor: colors.error, color: colors.white }" aria-label="Удалить статью">Удалить</button><button v-if="index === expenses.length - 1" type="button" @click="addExpense" class="px-4 py-2 rounded-lg transition-all hover:scale-110" :style="{ backgroundColor: colors.success, color: colors.white }" aria-label="Добавить статью">Добавить</button></div>
                                </div>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.expenses" role="alert" />
                    </div>
                </div>
                
                <div class="flex justify-center">
                    <PrimaryButton 
                        class="px-12 py-4 text-xl rounded-2xl font-bold shadow-lg transition-all hover:scale-105"
                        :style="{ backgroundColor: colors.brand, color: colors.white }"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }" 
                        :disabled="form.processing" 
                        :aria-label="form.processing ? 'Сохранение проекта...' : 'Сохранить изменения'"
                        @mouseenter="$event.target.style.backgroundColor = colors.hover"
                        @mouseleave="$event.target.style.backgroundColor = colors.brand"
                    >
                        {{ form.processing ? 'Сохранение...' : 'Сохранить изменения' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>