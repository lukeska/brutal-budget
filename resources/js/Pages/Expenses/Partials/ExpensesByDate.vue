<script setup lang="ts">
import { computed } from "vue";
import moment from "moment/moment";
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";
import { useExpenseStore } from "@/Stores/ExpenseStore";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage } from "@inertiajs/vue3";

let props = defineProps<{
    expenses: App.Data.ExpenseData[];
}>();

const page = usePage();

const expenseStore = useExpenseStore();

const expensesByDate = computed(() => {
    const itemsByDate = {};

    props.expenses.forEach((item) => {
        const { date } = item;

        if (!itemsByDate[date]) {
            itemsByDate[date] = [];
        }

        itemsByDate[date].push(item);
    });

    // Convert object keys to an array and sort it in descending order (most recent to older)
    const sortedDates = Object.keys(itemsByDate).sort((a, b) => moment(b, "DD-MM-YYYY").diff(moment(a, "DD-MM-YYYY")));

    // Create a new object with sorted dates
    const sortedItemsByDate = {};
    sortedDates.forEach((date) => {
        sortedItemsByDate[date] = itemsByDate[date];
    });

    return sortedItemsByDate;
});

const currencyFormatter = createCurrencyFormatter(page.props.auth.user.currency);
</script>

<template>
    <div
        v-for="(expenseByDate, date) in expensesByDate"
        :key="date"
        class="rounded border">
        <div class="flex w-full border-b px-3 py-1">
            <div class="min-w-[90px]">{{ date }}</div>
            <div class="ml-4 rounded-full bg-gray-200 px-2">{{ expenseByDate.length }}</div>

            <div class="flex-1 text-right font-mono">
                {{ currencyFormatter.format(expenseByDate.reduce((total, item) => total + item.amount, 0) / 100) }}
            </div>
        </div>
        <button
            v-for="expense in expenseByDate"
            class="flex w-full items-center space-x-4 px-3 py-2 hover:bg-neutral-50"
            @click.prevent="expenseStore.showSidebar(expense)">
            <div :style="'color:' + expense.category.hex">
                <CategoryIcon :category="expense.category" />
            </div>
            <div class="min-w-[80px] text-right font-mono">{{ currencyFormatter.format(expense.amount / 100) }}</div>
            <div class="text-gray-500">{{ expense.notes }}</div>
        </button>
    </div>
</template>
