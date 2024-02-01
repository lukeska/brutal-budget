<script lang="ts" setup>
import Chart from "chart.js/auto";

import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Components/Welcome.vue";
import { onMounted, ref } from "vue";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage } from "@inertiajs/vue3";

let props = defineProps<{
    monthlyTotals: App.Data.MonthlyTotalData[];
}>();

const myChart = ref();

const page = usePage();

const currencyFormatter = createCurrencyFormatter(page.props.auth.user.currency);

onMounted(() => {
    const labels = props.monthlyTotals[0].categoryMonthlyTotals.map((entry) => entry.category.name);
    const dataValues = props.monthlyTotals[0].categoryMonthlyTotals.map((entry) => entry.amount);
    const backgroundColors = props.monthlyTotals[0].categoryMonthlyTotals.map((entry) => entry.category.hex);

    new Chart(myChart.value, {
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
            spacing: 5,
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
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="p-4 lg:flex lg:gap-x-4">
                        <div class="lg:w-1/2">
                            <canvas ref="myChart"></canvas>
                        </div>
                        <div class="lg:w-1/2">some other graph</div>
                    </div>

                    <Welcome />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
