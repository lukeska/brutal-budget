<script setup lang="ts">
import moment from "moment/moment";
import Chart from "chart.js/auto";
import { onMounted, ref } from "vue";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage } from "@inertiajs/vue3";

let props = defineProps<{
    monthlyTotals: App.Data.MonthlyTotalData[];
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

    new Chart(bars.value, {
        type: "bar",
        data: data,
        options: {
            maintainAspectRatio: false,
            //indexAxis: "y",
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                    ticks: {
                        callback: function (value, index, values) {
                            return currencyFormatter.format(value);
                        },
                    },
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
