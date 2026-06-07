<template>
    <div v-if="totalPages > 1" class="flex justify-center mt-8 pb-6" role="navigation" aria-label="Пагинация">
        <div class="flex items-center gap-2 flex-wrap justify-center">
            <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                class="px-3 py-2 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2"
                :style="{ 
                    backgroundColor: colors.brand, 
                    color: colors.white, 
                    border: `1px solid ${colors.accent}`,
                    transition: transitions.normal
                }"
                @mouseenter="$event.target.style.backgroundColor = colors.hover"
                @mouseleave="$event.target.style.backgroundColor = colors.brand"
                :styleFocus="{ ringColor: colors.accent }"
                :aria-label="'Предыдущая страница'" 
                :aria-disabled="currentPage === 1" 
                :tabindex="currentPage === 1 ? -1 : 0">
                ← Назад
            </button>
            
            <template v-for="page in pageNumbers" :key="page">
                <button v-if="page !== '...'" @click="goToPage(page)"
                    class="px-3 py-2 rounded-lg transition-all min-w-[40px] focus:outline-none focus:ring-2"
                    :class="currentPage === page ? 'font-semibold' : ''"
                    :style="currentPage === page 
                        ? { backgroundColor: colors.accent, color: colors.brand, border: `1px solid ${colors.accent}`, ringColor: colors.accent }
                        : { backgroundColor: colors.brand, color: colors.white, border: `1px solid ${colors.hover}`, transition: transitions.normal }"
                    @mouseenter="currentPage !== page && ($event.target.style.backgroundColor = colors.hover)"
                    @mouseleave="currentPage !== page && ($event.target.style.backgroundColor = colors.brand)"
                    :styleFocus="{ ringColor: colors.accent }"
                    :aria-label="'Перейти на страницу ' + page" 
                    :aria-current="currentPage === page ? 'page' : undefined"
                    :aria-pressed="currentPage === page ? 'true' : 'false'">
                    {{ page }}
                </button>
                <span v-else class="px-2" :style="{ color: colors.white }" aria-hidden="true">...</span>
            </template>
            
            <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                class="px-3 py-2 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2"
                :style="{ 
                    backgroundColor: colors.brand, 
                    color: colors.white, 
                    border: `1px solid ${colors.accent}`,
                    transition: transitions.normal
                }"
                @mouseenter="$event.target.style.backgroundColor = colors.hover"
                @mouseleave="$event.target.style.backgroundColor = colors.brand"
                :styleFocus="{ ringColor: colors.accent }"
                :aria-label="'Следующая страница'" 
                :aria-disabled="currentPage === totalPages" 
                :tabindex="currentPage === totalPages ? -1 : 0">
                Вперед →
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

// ===== ЦВЕТА И СТИЛИ КОМПОНЕНТА =====
const colors = {
    brand: 'var(--color-brand)',
    brandDark: 'var(--color-brand-dark)',
    accent: 'var(--color-accent)',
    hover: 'var(--color-hover)',
    white: 'var(--color-white)',
};

const transitions = {
    normal: 'var(--transition-normal)',
};

const props = defineProps({
    currentPage: { type: Number, required: true },
    totalPages: { type: Number, required: true }
});
const emit = defineEmits(['update:page']);

const pageNumbers = computed(() => {
    const pages = [], range = [], rangeWithDots = [];
    const delta = 2;
    for (let i = 1; i <= props.totalPages; i++) {
        if (i === 1 || i === props.totalPages || (i >= props.currentPage - delta && i <= props.currentPage + delta)) range.push(i);
    }
    let l;
    for (let i of range) {
        if (l) {
            if (i - l === 2) rangeWithDots.push(l + 1);
            else if (i - l !== 1) rangeWithDots.push('...');
        }
        rangeWithDots.push(i);
        l = i;
    }
    return rangeWithDots;
});

const goToPage = (page) => {
    if (page >= 1 && page <= props.totalPages) {
        emit('update:page', page);
        setTimeout(() => {
            const activeButton = document.querySelector('[aria-current="page"]');
            if (activeButton) activeButton.focus();
        }, 100);
    }
};
</script>