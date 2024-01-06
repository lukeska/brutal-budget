<script setup lang="ts">
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import { getDate } from "@/Helpers/DatesHelper";

let props = defineProps<{
    monthlyTotals: App.Data.MonthlyTotalData[];
}>();

const page = usePage();

const currencyFormatter = createCurrencyFormatter(page.props.auth.user.currency);
</script>

<template>
    <div class="mb-4 flex justify-between gap-x-2">
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
                class="flex-1 rounded border p-2 text-center first:hidden last:hidden md:first:block md:last:block"
                :class="{ 'border-indigo-500': monthlyTotal.isCurrent }">
                <div class="mb-2 text-sm text-gray-400">{{ getDate(monthlyTotal.yearMonth).format("MMM YYYY") }}</div>
                <div class="font-mono">{{ currencyFormatter.format(monthlyTotal.total / 100) }}</div>
            </Link>
        </template>
    </div>
</template>
