<script setup lang="ts">
import Chart from "chart.js/auto";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

let props = defineProps<{
    monthlyTotals: App.Data.MonthlyTotalData[];
}>();

const donut = ref();

const page = usePage();

const currencyFormatter = createCurrencyFormatter(page.props.currency.code);

const initDonut = () => {
    const currentMonthlyTotals = props.monthlyTotals.find((item) => item.isCurrent === true);
    const labels = currentMonthlyTotals.categoryMonthlyTotals.map((entry) => entry.category.name);
    const dataValues = currentMonthlyTotals.categoryMonthlyTotals.map((entry) => entry.amount);
    const backgroundColors = currentMonthlyTotals.categoryMonthlyTotals.map((entry) => entry.category.hex);

    new Chart(donut.value, {
        type: "doughnut",
        data: {
            labels: labels,
            datasets: [
                {
                    data: dataValues,
                    backgroundColor: backgroundColors,
                    hoverOffset: 4,
                },
            ],
        },
        options: {
            spacing: 1,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return currencyFormatter.format(context.formattedValue);
                        },
                    },
                },
                legend: {
                    position: "bottom",
                },
            },
        },
    });
};

onMounted(() => {
    initDonut();
});
</script>

<template>
    <div class="mx-auto">
        <canvas ref="donut"></canvas>
    </div>
</template>
