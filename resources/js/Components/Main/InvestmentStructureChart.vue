<script setup>
import { computed } from 'vue';
import { Doughnut } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement);

const props = defineProps({
    investments: {
        type: Array,
        default: () => []
    }
});

const colors = [
    'rgba(96,  165, 250, 0.8)',
    'rgba(74,  222, 128, 0.8)',
    'rgba(251, 191, 36,  0.8)',
    'rgba(248, 113, 113, 0.8)',
    'rgba(167, 139, 250, 0.8)',
    'rgba(251, 146, 60,  0.8)',
    'rgba(34,  211, 238, 0.8)',
    'rgba(244, 114, 182, 0.8)',
];

const chartData = computed(() => ({
    labels: props.investments.map(inv => inv.item_name),
    datasets: [
        {
            data: props.investments.map(inv => Number(inv.amount)),
            backgroundColor: props.investments.map((_, i) => colors[i % colors.length]),
            borderColor: 'rgba(255,255,255,0.2)',
            borderWidth: 2,
            hoverOffset: 8,
        }
    ]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                color: '#ffffff',
                font: { size: 16 },
                padding: 20,
            }
        },
        title: {
            display: true,
            text: 'Структура запрашиваемых инвестиций',
            color: '#ffffff',
            font: { size: 20 }
        },
        tooltip: {
            callbacks: {
                label: (ctx) => {
                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                    const pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : 0;
                    return ` ${Number(ctx.raw).toLocaleString('ru-RU')} ₽ (${pct}%)`;
                }
            }
        }
    }
};

// Есть ли данные
const hasData = computed(() => props.investments && props.investments.length > 0);
</script>

<template>
    <div class="w-full h-full flex items-center justify-center">
        <Doughnut v-if="hasData" :data="chartData" :options="chartOptions" />
        <p v-else></p>
    </div>
</template>