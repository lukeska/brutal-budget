<script setup lang="ts">
import moment from "moment/moment";
import Chart from "chart.js/auto";
import { onMounted, ref } from "vue";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage } from "@inertiajs/vue3";

let props = defineProps<{
    monthlyTotals: App.Data.MonthlyTotalData[];
    indexAxis?: string;
}>();

const bars = ref();

const page = usePage();

const currencyFormatter = createCurrencyFormatter(page.props.currency.code);

const initBars = () => {
    const monthlyTotals = props.monthlyTotals;

    const allCategoriesSet = new Set();
    monthlyTotals.forEach((entry) => {
        entry.categoryMonthlyTotals.forEach((category) => {
            const { name, hex } = category.category;
            allCategoriesSet.add(JSON.stringify({ name, hex }));
        });
    });

    const allCategories = Array.from(allCategoriesSet).map((category) => JSON.parse(category));

    const labels = monthlyTotals.map((entry) => moment(entry.yearMonth, "YYYYMM").format("MM/YY"));
    const datasets = monthlyTotals.map((entry) =>
        entry.categoryMonthlyTotals.reduce((acc, category) => {
            acc[category.category.name] = category.converted_amount;
            return acc;
        }, {}),
    );

    const data = {
        labels: labels,
        datasets: allCategories.map((category) => ({
            label: category.name,
            data: datasets.map((data) => data[category.name] || 0),
            backgroundColor: category.hex,
            stack: "Stack 0",
        })),
    };

    let ticksX = {};
    let ticksY = {};
    const callback = {
        callback: function (value, index, values) {
            return currencyFormatter.format(value);
        },
    };

    if(props.indexAxis == 'y') {
        ticksX = callback;
    } else {
        ticksY = callback;
    }

    new Chart(bars.value, {
        type: "bar",
        data: data,
        options: {
            maintainAspectRatio: false,
            indexAxis: props.indexAxis == 'y' ? 'y' : 'x',
            scales: {
                x: {
                    stacked: true,
                    ticks: ticksX,
                },
                y: {
                    stacked: true,
                    ticks: ticksY,
                },
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.dataset.label + ": " + currencyFormatter.format(context.formattedValue);
                        },
                    },
                },
                legend: {
                    display: false,
                    //position: "bottom",
                },
            },
        },
    });
};

onMounted(() => {
    initBars();
});
</script>

<template>
    <canvas ref="bars"></canvas>
</template>
