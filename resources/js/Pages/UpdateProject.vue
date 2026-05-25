<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, onMounted } from 'vue';
import TitlePage from '@/Layouts/TitlePage.vue';

const props = defineProps({
    project: {
        type: Object,
        required: true
    }
});

// Цветовая схема
const colors = {
    bgDark: '#284139',
    bgLight: '#809076',
    accent: '#F8D794',
    border: '#886830',
    white: '#ffffff',
};

// Массивы для категорий и трат
const categories = ref(['']);
const expenses = ref([{ item_name: '', amount: '' }]);

// Массив для превью фотографий (существующие и новые)
const existingPhotos = ref([]);
const photoPreviews = ref([]);
const photosToDelete = ref([]);

const form = useForm({
    name: props.project.title || '',
    shotr_descr: props.project.short_description || '',
    full_descr: props.project.full_description || '',
    categories: [],
    fotos: [],
    deleted_photos: [],
    ownShip: props.project.ownership || '',
    activity: props.project.activity || '',
    type_building: props.project.type_build || '',
    addres: props.project.address || '',
    latitude: props.project.latitude || '',
    longitude: props.project.longitude || '',
    total_investment: props.project.total_investment || '',
    number_date_realise: props.project.number_date_realise || '',
    count_new_job: props.project.count_new_job || '',
    collected_total_investment: props.project.collected_total_investment || '',
    expected_revenue: '',
    expected_expenses: '',
    expenses: [],
    _method: 'PATCH'
});

// Инициализация данных при загрузке
onMounted(() => {
    if (props.project.category) {
        const cats = Array.isArray(props.project.category) 
            ? props.project.category 
            : JSON.parse(props.project.category);
        categories.value = cats.length > 0 ? cats : [''];
    }

    if (props.project.photos && props.project.photos.length > 0) {
        existingPhotos.value = props.project.photos.map(photo => ({
            id: photo.id,
            url: `/storage/${photo.photo_path}`,
            name: photo.photo_path.split('/').pop()
        }));
    }

    if (props.project.investments && props.project.investments.length > 0) {
        expenses.value = props.project.investments.map(inv => ({
            item_name: inv.item_name,
            amount: inv.amount
        }));
    }

    if (props.project.forecasts && props.project.forecasts.length > 0) {
        const forecast = props.project.forecasts[0];
        form.expected_revenue = forecast.expected_revenue || '';
        form.expected_expenses = forecast.expected_expenses || '';
    }
});

const addCategory = () => {
    if (categories.value.length < 4) {
        categories.value.push('');
    }
};

const removeCategory = (index) => {
    if (categories.value.length > 1) {
        categories.value.splice(index, 1);
    }
};

const addExpense = () => {
    expenses.value.push({ item_name: '', amount: '' });
};

const removeExpense = (index) => {
    if (expenses.value.length > 1) {
        expenses.value.splice(index, 1);
    }
};

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
            photoPreviews.value.push({
                url: e.target.result,
                name: file.name,
                size: (file.size / 1024).toFixed(2)
            });
        };
        reader.readAsDataURL(file);
    });
    
    event.target.value = '';
};

const removePhoto = (index) => {
    form.fotos.splice(index, 1);
    photoPreviews.value.splice(index, 1);
};

const submit = () => {
    form.categories = categories.value.filter(cat => cat && cat.trim() !== '');
    form.deleted_photos = photosToDelete.value;
    
    form.expenses = expenses.value.filter(exp => {
        const hasItemName = exp.item_name && String(exp.item_name).trim() !== '';
        const hasAmount = exp.amount !== '' && exp.amount !== null && exp.amount !== undefined;
        return hasItemName && hasAmount;
    });

    form.post(route('projects.update', props.project.id), {
        forceFormData: true,
        onSuccess: () => {},
        onError: (errors) => {
            console.log('Ошибки валидации:', errors);
        }
    });
};
</script>

<template>
    <Head title="Редактирование проекта">
        <meta name="description" content="Форма редактирования инвестиционного проекта">
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <TitlePage id="createProject" value="Редактирование проекта">
            </TitlePage> 
        </template>
        
        <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <form @submit.prevent="submit" class="flex flex-col gap-6 rounded-xl p-6" :style="{ border: `2px solid ${colors.border}`, backgroundColor: colors.bgLight }">
                
                <h1 class="text-2xl pb-2" :style="{ color: colors.accent, borderBottom: `2px solid ${colors.border}` }">
                    Описание проекта
                </h1>
                
                <div class="flex flex-col lg:flex-row gap-4 mb-4">
                    <div class="flex flex-col gap-3 w-full lg:w-auto">
                        <div class="w-full lg:w-[450px]">
                            <InputLabel for="name" value="Название проекта" :style="{ color: colors.accent }" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-2 block w-full"
                                v-model="form.name"
                                required
                                placeholder="Введите название проекта"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>
                        
                        <div class="w-full lg:w-[450px]">
                            <InputLabel for="shotr_descr" value="Короткое описание" :style="{ color: colors.accent }" />
                            <TextInput
                                id="shotr_descr"
                                type="text"
                                class="mt-3 block w-full"
                                v-model="form.shotr_descr"
                                required
                                placeholder="Краткое описание проекта"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            />
                            <InputError class="mt-2" :message="form.errors.shotr_descr" />
                        </div>
                    </div>

                    <div class="flex-1 min-w-[300px]">
                        <InputLabel for="full_descr" value="Подробное описание" :style="{ color: colors.accent }" />
                        <textarea
                            id="full_descr"
                            class="mt-1 block w-full h-[150px] rounded-xl focus:outline-none p-2"
                            v-model="form.full_descr"
                            required
                            placeholder="Подробное описание проекта"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.full_descr" />
                    </div>
                </div>

                <!-- Категории -->
                <div>
                    <InputLabel value="Категории проекта (до 4 категорий)" :style="{ color: colors.accent }" />
                    
                    <div class="space-y-3 mt-2 grid 2xl:grid-cols-4 xl:grid-cols-3 lg:grid-cols-2 grid-cols-1 gap-3">
                        <div 
                            v-for="(category, index) in categories" 
                            :key="index" 
                            class="flex items-center gap-3"
                        >
                            <TextInput
                                :id="`category-${index}`"
                                type="text"
                                class="block w-full sm:w-[450px]"
                                v-model="categories[index]"
                                :placeholder="`Категория ${index + 1}`"
                                :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                            />
                            
                            <button 
                                v-if="categories.length > 1"
                                type="button"
                                @click="removeCategory(index)"
                                class="text-white p-3 rounded-xl transition duration-200 flex items-center justify-center w-12 h-12 bg-red-500"
                                :style="{ border: `2px solid ${colors.border}` }"
                                :aria-label="`Удалить категорию ${category || index + 1}`"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            
                            <button 
                                v-if="index === categories.length - 1 && categories.length < 4"
                                type="button"
                                @click="addCategory"
                                class="text-white p-3 rounded-xl transition duration-200 flex items-center justify-center w-12 h-12 bg-green-500"
                                :style="{ border: `2px solid ${colors.border}` }"
                                aria-label="Добавить новую категорию"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <p v-if="categories.length >= 4" class="text-base mt-2" :style="{ color: colors.accent }" role="status">
                        Достигнуто максимальное количество категорий (4)
                    </p>
                    <InputError class="mt-2" :message="form.errors.categories" />
                </div>

                <!-- Фото проекта -->
                <div class="pt-6" :style="{ borderTop: `2px solid ${colors.border}` }">
                    <InputLabel for="fotos" value="Фото проекта" class="mb-3" :style="{ color: colors.accent }" />
                    
                    <div class="mb-4">
                        <label 
                            for="fotos" 
                            class="cursor-pointer inline-flex items-center px-6 py-3 font-semibold rounded-xl transition duration-200"
                            :style="{ backgroundColor: colors.bgDark, color: colors.accent, border: `2px solid ${colors.border}` }"
                        >
                            Добавить изображения
                        </label>
                        <input
                            id="fotos"
                            type="file"
                            @change="handleFileUpload"
                            accept="image/*"
                            multiple
                            class="hidden"
                            aria-label="Выберите изображения для проекта"
                        />
                    </div>
                    
                    <InputError class="mt-2" :message="form.errors.fotos" />

                    <!-- Существующие фотографии -->
                    <div v-if="existingPhotos.length > 0" class="mt-3">
                        <p class="text-sm mb-2" :style="{ color: colors.accent }">Текущие фотографии:</p>
                        <div class="flex flex-wrap gap-3" role="list" aria-label="Существующие фотографии проекта">
                            <div 
                                v-for="(photo, index) in existingPhotos" 
                                :key="photo.id"
                                class="relative w-24 h-24 rounded-lg overflow-hidden"
                                :style="{ border: `2px solid ${colors.border}` }"
                                role="listitem"
                            >
                                <img :src="photo.url" class="absolute w-full h-full object-cover" :alt="`Фото проекта ${index + 1}`" />
                                <button
                                    type="button"
                                    @click="removeExistingPhoto(index)"
                                    class="absolute z-20 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 shadow-md"
                                    style="top: -1px; right: -1px;"
                                    :aria-label="`Удалить фото ${index + 1}`"
                                >✕</button>
                            </div>
                        </div>
                    </div>

                    <!-- Новые фотографии -->
                    <div v-if="photoPreviews.length > 0" class="mt-3">
                        <p class="text-sm mb-2" :style="{ color: colors.accent }">Новые фотографии:</p>
                        <div class="flex flex-wrap gap-3" role="list" aria-label="Новые фотографии проекта">
                            <div 
                                v-for="(preview, index) in photoPreviews" 
                                :key="index"
                                class="relative w-24 h-24 rounded-lg overflow-hidden"
                                :style="{ border: `2px solid ${colors.border}` }"
                                role="listitem"
                            >
                                <img :src="preview.url" class="w-full h-full object-cover" :alt="`Новое фото ${index + 1}`" />
                                <button
                                    type="button"
                                    @click="removePhoto(index)"
                                    class="absolute z-10 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 shadow-md"
                                    style="top: -1px; right: -1px;"
                                    :aria-label="`Удалить новое фото ${index + 1}`"
                                >✕</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Дополнительная информация -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <InputLabel for="ownShip" value="Собственность" :style="{ color: colors.accent }" />
                        <TextInput
                            id="ownShip"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.ownShip"
                            placeholder="Тип собственности"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        />
                        <InputError class="mt-2" :message="form.errors.ownShip" />
                    </div>

                    <div>
                        <InputLabel for="activity" value="Деятельность" :style="{ color: colors.accent }" />
                        <TextInput
                            id="activity"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.activity"
                            placeholder="Вид деятельности"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        />
                        <InputError class="mt-2" :message="form.errors.activity" />
                    </div>

                    <div>
                        <InputLabel for="type_building" value="Вид строительства" :style="{ color: colors.accent }" />
                        <TextInput
                            id="type_building"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.type_building"
                            placeholder="Тип строительства"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        />
                        <InputError class="mt-2" :message="form.errors.type_building" />
                    </div>
                </div>

                <!-- Геоданные -->
                <h1 class="text-2xl pb-2" :style="{ color: colors.accent, borderBottom: `2px solid ${colors.border}` }">
                    Геоданные
                </h1>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-3">
                        <InputLabel for="addres" value="Адрес" :style="{ color: colors.accent }" />
                        <TextInput
                            id="addres"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.addres"
                            placeholder="Адрес проекта"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        />
                        <InputError class="mt-2" :message="form.errors.addres" />
                    </div>

                    <div>
                        <InputLabel for="latitude" value="Широта" :style="{ color: colors.accent }" />
                        <TextInput
                            id="latitude"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.latitude"
                            placeholder="55.7558"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        />
                        <InputError class="mt-2" :message="form.errors.latitude" />
                    </div>

                    <div>
                        <InputLabel for="longitude" value="Долгота" :style="{ color: colors.accent }" />
                        <TextInput
                            id="longitude"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.longitude"
                            placeholder="37.6173"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        />
                        <InputError class="mt-2" :message="form.errors.longitude" />
                    </div>
                </div>

                <!-- Финансовые показатели -->
                <h1 class="text-2xl pb-2" :style="{ color: colors.accent, borderBottom: `2px solid ${colors.border}` }">
                    Финансовые показатели
                </h1>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <InputLabel for="total_investment" value="Требуемая сумма (₽)" :style="{ color: colors.accent }" />
                        <TextInput
                            id="total_investment"
                            type="number"
                            class="mt-1 block w-full"
                            v-model="form.total_investment"
                            placeholder="1000000"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        />
                        <InputError class="mt-2" :message="form.errors.total_investment" />
                    </div>

                    <div>
                        <InputLabel for="number_date_realise" value="Срок окупаемости (мес.)" :style="{ color: colors.accent }" />
                        <TextInput
                            id="number_date_realise"
                            type="number"
                            class="mt-1 block w-full"
                            v-model="form.number_date_realise"
                            placeholder="24"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        />
                        <InputError class="mt-2" :message="form.errors.number_date_realise" />
                    </div>

                    <div>
                        <InputLabel for="count_new_job" value="Рабочие места" :style="{ color: colors.accent }" />
                        <TextInput
                            id="count_new_job"
                            type="number"
                            class="mt-1 block w-full"
                            v-model="form.count_new_job"
                            placeholder="10"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        />
                        <InputError class="mt-2" :message="form.errors.count_new_job" />
                    </div>

                    <div>
                        <InputLabel for="collected_total_investment" value="Собрано (₽)" :style="{ color: colors.accent }" />
                        <TextInput
                            id="collected_total_investment"
                            type="number"
                            class="mt-1 block w-full"
                            v-model="form.collected_total_investment"
                            placeholder="500000"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        />
                        <InputError class="mt-2" :message="form.errors.collected_total_investment" />
                    </div>

                    <div>
                        <InputLabel for="expected_revenue" value="Ожидаемая доходность (%)" :style="{ color: colors.accent }" />
                        <TextInput
                            id="expected_revenue"
                            type="number"
                            step="0.01"
                            class="mt-1 block w-full"
                            v-model="form.expected_revenue"
                            placeholder="15.5"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        />
                        <InputError class="mt-2" :message="form.errors.expected_revenue" />
                    </div>

                    <div>
                        <InputLabel for="expected_expenses" value="Ожидаемые расходы (₽)" :style="{ color: colors.accent }" />
                        <TextInput
                            id="expected_expenses"
                            type="number"
                            class="mt-1 block w-full"
                            v-model="form.expected_expenses"
                            placeholder="800000"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}`, color: colors.white }"
                        />
                        <InputError class="mt-2" :message="form.errors.expected_expenses" />
                    </div>
                </div>

                <!-- Структура трат -->
                <div class="pt-4" :style="{ borderTop: `2px solid ${colors.border}` }">                    
                    <div class="grid grid-cols-1 xl:grid-cols-2 2xl:grid-cols-3 gap-4">
                        <div 
                            v-for="(expense, index) in expenses" 
                            :key="index" 
                            class="flex flex-col gap-3 rounded-lg transition-colors duration-300 p-4"
                            :style="{ backgroundColor: colors.bgDark, border: `2px solid ${colors.border}` }"
                        >
                            <div class="w-full">
                                <InputLabel 
                                    :for="`item_name-${index}`" 
                                    :value="`Статья расходов ${index + 1}`" 
                                    class="text-sm font-semibold"
                                    :style="{ color: colors.accent }" 
                                />
                                <TextInput
                                    :id="`item_name-${index}`"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="expenses[index].item_name"
                                    placeholder="Ваши расходы для проекта..."
                                    :style="{ backgroundColor: colors.bgLight, border: `2px solid ${colors.border}`, color: colors.white }"
                                />
                            </div>
                            
                            <div class="w-full">
                                <InputLabel 
                                    :for="`amount-${index}`" 
                                    value="Сумма (₽)" 
                                    class="text-sm font-semibold"
                                    :style="{ color: colors.accent }" 
                                />
                                <TextInput
                                    :id="`amount-${index}`"
                                    type="number"
                                    class="mt-1 block w-full"
                                    v-model="expenses[index].amount"
                                    placeholder="100000"
                                    min="0"
                                    step="0.01"
                                    :style="{ backgroundColor: colors.bgLight, border: `2px solid ${colors.border}`, color: colors.white }"
                                />
                            </div>
                            
                            <div class="flex gap-2 justify-end mt-2">
                                <button 
                                    v-if="expenses.length > 1"
                                    type="button"
                                    @click="removeExpense(index)"
                                    class="text-white px-4 py-2 rounded-xl transition duration-600 flex items-center gap-2 bg-red-500"
                                    :style="{ border: `2px solid ${colors.border}` }"
                                    :aria-label="`Удалить статью расходов ${index + 1}`"
                                    title="Удалить статью расходов"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                
                                <button 
                                    v-if="index === expenses.length - 1"
                                    type="button"
                                    @click="addExpense"
                                    class="text-white px-4 py-2 rounded-xl transition duration-600 flex items-center gap-2 bg-green-500"
                                    :style="{ border: `2px solid ${colors.border}` }"
                                    aria-label="Добавить новую статью расходов"
                                    title="Добавить новую статью расходов"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>                    
                    <InputError class="mt-2" :message="form.errors.expenses" />
                </div>
                
                <!-- Кнопка отправки -->
                <div class="flex justify-center mt-6">
                    <PrimaryButton
                        class="px-8 py-3 text-lg rounded-xl"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        :style="{ backgroundColor: colors.bgDark, color: colors.accent, border: `2px solid ${colors.border}` }"
                        :aria-label="form.processing ? 'Сохранение проекта...' : 'Сохранить изменения'"
                    >
                        {{ form.processing ? 'Сохранение...' : 'Сохранить изменения' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
