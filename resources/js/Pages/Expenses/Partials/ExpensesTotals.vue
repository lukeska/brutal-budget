<script setup lang="ts">
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import { getDate } from "@/Helpers/DatesHelper";

let props = defineProps<{
    monthlyTotals: App.Data.MonthlyTotalData[];
}>();

const page = usePage();

const currencyFormatter = createCurrencyFormatter(page.props.currency.code, 0, 0);
</script>

<template>
    <div class="mb-4 flex justify-between gap-x-3">
        <template
            v-for="monthlyTotal in monthlyTotals"
            :key="monthlyTotal.monthYear">
            <Link
                :href="
                    route('expenses.index', {
                        year: getDate(monthlyTotal.yearMonth).format('YYYY'),
                        month: getDate(monthlyTotal.yearMonth).format('MM'),
                    })
                "
                class="flex-1 shrink-0 rounded-md border-2 bg-white p-2 text-center shadow-sm first:hidden last:hidden md:first:block md:last:block"
                :class="[monthlyTotal.isCurrent ? 'border-indigo-500' : 'border-transparent']">
                <div class="mb-2 text-sm text-gray-400">{{ getDate(monthlyTotal.yearMonth).format("MMM YYYY") }}</div>
                <div class="font-mono">{{ currencyFormatter.format(monthlyTotal.total) }}</div>
            </Link>
        </template>
    </div>
</template>
