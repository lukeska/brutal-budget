<script lang="ts" setup>
import Chart from "chart.js/auto";

import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Components/Welcome.vue";
import { onMounted, ref } from "vue";

let props = defineProps<{
    monthlyTotals: App.Data.MonthlyTotalData[];
}>();

const myChart = ref();

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
                    label: "Monthly expenses",
                    data: dataValues,
                    backgroundColor: backgroundColors,
                    hoverOffset: 4,
                },
            ],
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
                    <div class="max-w-[600px]">
                        <canvas ref="myChart"></canvas>
                    </div>

                    <Welcome />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
