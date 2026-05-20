<script setup>
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
    expectedRevenue: {
        type: Number,
        default: 0
    },
    expectedExpenses: {
        type: Number,
        default: 0
    },
    months: {
        type: Number,
        default: 12
    }
});

// Количество лет из месяцев
const years = computed(() => {
    const y = Math.ceil(props.months / 12);
    return y > 0 ? y : 1;
});

// Метки — Год 1, Год 2 ...
const labels = computed(() =>
    Array.from({ length: years.value }, (_, i) => `Год ${i + 1}`)
);

const chartData = computed(() => ({
    labels: labels.value,
    datasets: [
        {
            label: 'Выручка ₽',
            data: Array(years.value).fill(Number(props.expectedRevenue)),
            backgroundColor: 'rgb(0,183,125)',
            borderWidth: 2,
            borderRadius: 6,
        },
        {
            label: 'Расходы ₽',
            data: Array(years.value).fill(Number(props.expectedExpenses)),
            backgroundColor: 'rgb(183,55,0)',
            borderWidth: 2,
            borderRadius: 6,
        }
    ]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: { color: '#ffffff', font: { size: 13 } }
        },
        title: {
            display: true,
            text: 'Прогноз выручки и расходов по годам',
            color: '#ffffff',
            font: { size: 20 }
        },
        tooltip: {
            callbacks: {
                label: (ctx) => ` ${ctx.dataset.label}: ${Number(ctx.raw).toLocaleString('ru-RU')} ₽`
            }
        }
    },
    scales: {
        x: {
            ticks: { color: '#ffffff' },
            grid: { color: 'rgba(255,255,255,0.1)' }
        },
        y: {
            ticks: {
                color: '#ffffff',
                callback: (val) => val.toLocaleString('ru-RU') + ' ₽'
            },
            grid: { color: 'rgba(255,255,255,0.1)' }
        }
    }
};
</script>

<template>
    <Bar :data="chartData" :options="chartOptions" />
</template>