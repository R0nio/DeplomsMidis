<script setup>
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Filler
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale, Filler);

const props = defineProps({
    expectedRevenue: { type: Number, default: 0 },
    expectedExpenses: { type: Number, default: 0 },
    months: { type: Number, default: 12 }
});

const years = computed(() => {
    const y = Math.ceil(props.months / 12);
    return y > 0 ? y : 1;
});

const labels = computed(() =>
    Array.from({ length: years.value }, (_, i) => `Год ${i + 1}`)
);

// Накопленный поток = (выручка - расходы) нарастающим итогом
const cashFlowData = computed(() => {
    const annualFlow = Number(props.expectedRevenue) - Number(props.expectedExpenses);
    return Array.from({ length: years.value }, (_, i) => annualFlow * (i + 1));
});

const chartData = computed(() => ({
    labels: labels.value,
    datasets: [
        {
            label: 'Накопленный денежный поток (₽)',
            data: cashFlowData.value,
            borderColor: 'rgba(96, 165, 250, 1)',
            backgroundColor: 'rgba(96, 165, 250, 0.15)',
            borderWidth: 3,
            pointBackgroundColor: 'rgba(96, 165, 250, 1)',
            pointRadius: 5,
            pointHoverRadius: 7,
            fill: true,
            tension: 0.4,
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
            text: 'Прогноз накопленного денежного потока',
            color: '#ffffff',
            font: { size: 20 }
        },
        tooltip: {
            callbacks: {
                label: (ctx) => ` ${Number(ctx.raw).toLocaleString('ru-RU')} ₽`
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
    <Line :data="chartData" :options="chartOptions" />
</template>