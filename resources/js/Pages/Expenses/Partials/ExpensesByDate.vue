<script setup lang="ts">
import { computed } from "vue";
import moment from "moment/moment";
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";
import { useExpenseStore } from "@/Stores/ExpenseStore";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage } from "@inertiajs/vue3";
import ExpensesByDateItem from "@/Pages/Expenses/Partials/ExpensesByDateItem.vue";

let props = defineProps<{
    expenses: App.Data.ExpenseData[];
}>();

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
</script>

<template>
    <div class="divide-y overflow-hidden rounded-md bg-white shadow-sm">
        <div
            v-for="(expenseByDate, date) in expensesByDate"
            :key="date">
            <ExpensesByDateItem
                :date="date"
                :expenses="expenseByDate"
                :total="expenses.reduce((total, item) => total + item.amount, 0)" />
        </div>
    </div>
</template>
