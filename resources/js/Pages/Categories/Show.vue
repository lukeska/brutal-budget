<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";
import moment from "moment/moment";
import BarsChart from "@/Pages/Dashboard/Partials/BarsChart.vue";
import { usePage } from "@inertiajs/vue3";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";

let props = defineProps<{
    category: App.Data.CategoryData;
    monthlyTotals: App.Data.MonthlyTotalData[];
    month: number;
    year: number;
    total: number;
}>();

const page = usePage();

const currencyFormatter = createCurrencyFormatter(page.props.currency.code);
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="flex items-center space-x-2 text-xl font-semibold leading-tight text-gray-800">
                <div :style="'color:' + category.hex">
                    <CategoryIcon :category="category" />
                </div>
                <div>{{ category.name }}</div>
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="divide-y md:flex md:divide-x md:divide-y-0">
                        <div class="flex w-full items-center justify-between px-3 py-3 md:px-6">
                            <div>
                                <div class="mb-1 text-xs text-gray-500">
                                    Expenses for {{ props.category.name }} in the last 12 months
                                </div>

                                <div class="mb-1 text-2xl">
                                    {{ currencyFormatter.format(total) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-px border-y bg-gray-50 md:grid-cols-4 lg:grid-cols-6">
                        <div
                            v-for="monthlyTotal in props.monthlyTotals"
                            :key="monthlyTotal.yearMonth"
                            class="bg-white p-4">
                            <div class="flex justify-between text-xs text-gray-400">
                                <div>
                                    {{ moment(monthlyTotal.yearMonth, "YYYYMM").format("MM/YY") }}
                                </div>
                                <div>{{ parseInt((monthlyTotal.total * 100) / total) }}%</div>
                            </div>
                            {{ currencyFormatter.format(monthlyTotal.total) }}
                        </div>
                    </div>
                    <div class="h-[500px] px-4 py-8">
                        <BarsChart
                            :monthly-totals="monthlyTotals"
                            index-axis="y" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
