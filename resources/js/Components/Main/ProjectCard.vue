<script setup>
import { Building2, TrendingUp, Users, Clock } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    project: {
        type: Array,
        required: true
    }
});

const emit = defineEmits(['invest']);

// Текстовые метки для рисков
const riskLabels = computed(() => ({
    low: 'Низкий риск',
    medium: 'Средний риск',
    high: 'Высокий риск'
}));

// Текущий класс для риска
const riskClass = computed(() => riskColors.value[props.riskLevel]);
const riskLabel = computed(() => riskLabels.value[props.riskLevel]);

// Прогресс сбора средств (очистка от нецифровых символов)
const progress = computed(() => {
    const raisedNumber = parseFloat(props.totalRaised.replace(/[^\d]/g, ''));
    const goalNumber = parseFloat(props.goal.replace(/[^\d]/g, ''));
    
    if (isNaN(raisedNumber) || isNaN(goalNumber) || goalNumber === 0) return 0;
    return (raisedNumber / goalNumber) * 100;
});

// Случайное число инвесторов (как в React версии)
const investorsCount = computed(() => Math.floor(Math.random() * 200 + 50));

// Обработчик инвестирования
const handleInvest = () => {
    emit('invest', {
        title: props.title,
        category: props.category
    });
};
</script>

<template>
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 group">
        <!-- Изображение и бейджи -->
        <div class="relative h-48 overflow-hidden">
            <img
                :src="project.image"
                :alt="title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            
            <!-- Бейдж риска (правый верхний угол) -->
            <div class="absolute top-4 right-4">
            </div>
            
            <!-- Бейдж категории (левый верхний угол) -->
            <div class="absolute top-4 left-4">
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-white/90 text-gray-900">
                    {{ category }}
                </span>
            </div>
        </div>

        <!-- Контент карточки -->
        <div class="p-6">
            <!-- Заголовок и описание -->
            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ title }}</h3>
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ description }}</p>

            <!-- Доходность и минимальная инвестиция -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="flex items-start gap-2">
                    <TrendingUp class="text-green-600 mt-1" :size="18" />
                    <div>
                        <p class="text-xs text-gray-500">Доходность</p>
                        <p class="font-semibold text-gray-900">{{ expectedReturn }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-2">
                    <Building2 class="text-blue-600 mt-1" :size="18" />
                    <div>
                        <p class="text-xs text-gray-500">Минимум</p>
                        <p class="font-semibold text-gray-900">{{ minInvestment }}</p>
                    </div>
                </div>
            </div>

            <!-- Прогресс-бар сбора средств -->
            <div class="mb-4">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-500">Собрано</span>
                    <span class="font-medium text-gray-900">{{ totalRaised }} / {{ goal }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-blue-500 to-indigo-600 h-full rounded-full transition-all"
                        :style="{ width: `${Math.min(progress, 100)}%` }"
                    ></div>
                </div>
            </div>

            <!-- Время и инвесторы -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2 text-gray-500">
                    <Clock :size="16" />
                    <span class="text-sm">{{ timeLeft }}</span>
                </div>
                <div class="flex items-center gap-2 text-gray-500">
                    <Users :size="16" />
                    <span class="text-sm">{{ investorsCount }} инвесторов</span>
                </div>
            </div>

            <!-- Кнопка инвестирования -->
            <button
                @click="handleInvest"
                class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-700 hover:to-indigo-800 transition-all"
            >
                Инвестировать
            </button>
        </div>
    </div>
</template>