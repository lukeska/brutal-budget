<script setup lang="ts">
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage } from "@inertiajs/vue3";
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";
import { useExpenseStore } from "@/Stores/ExpenseStore";
import { computed, ref } from "vue";

const page = usePage();

const props = defineProps<{
    expenses: App.Data.ExpenseData[];
    date: string;
    total: number;
}>();

const expenseStore = useExpenseStore();

const showExpenses = ref(false);

const currencyFormatter = createCurrencyFormatter(page.props.auth.user.currency);

const percentage = computed((): number => {
    let dailyTotal = props.expenses.reduce((total, item) => total + item.amount, 0);
    return Math.round((dailyTotal / props.total) * 100);
});
</script>

<template>
    <div class="relative flex flex w-full items-center justify-between px-3 py-2">
        <div class="min-w-[90px] font-semibold">{{ date }}</div>

        <div class="flex items-center space-x-3">
            <div class="text-right font-mono">
                <div class="text-xl">
                    {{ currencyFormatter.format(expenses.reduce((total, item) => total + item.amount, 0)) }}
                </div>
                <div class="flex items-center space-x-2">
                    <div class="relative h-1 w-16 bg-gray-200">
                        <div
                            class="h-full bg-black"
                            :style="'width:' + percentage + '%'"></div>
                    </div>
                    <div class="w-6 text-sm text-gray-400">{{ percentage }}%</div>
                </div>
            </div>

            <div class="relative z-10 h-full w-8 rounded-lg bg-white">
                <div class="rounded bg-gray-800 text-center text-sm text-white">{{ expenses.length }}</div>
            </div>
        </div>

        <button
            class="absolute inset-0 h-full w-full"
            @click.prevent="showExpenses = !showExpenses"></button>
    </div>
    <div
        v-if="showExpenses"
        class="border-t">
        <button
            v-for="expense in expenses"
            class="flex w-full items-center space-x-4 px-3 py-2 hover:bg-neutral-50"
            @click.prevent="expenseStore.showSidebar(expense)">
            <div :style="'color:' + expense.category.hex">
                <CategoryIcon :category="expense.category" />
            </div>
            <div class="min-w-[80px] text-right font-mono">
                {{ currencyFormatter.format(expense.amount) }}
            </div>
            <div class="text-gray-500">{{ expense.notes }}</div>
        </button>
    </div>
</template>