<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import ExpensesByDate from "@/Pages/Expenses/Partials/ExpensesByDate.vue";
import { useExpenseStore } from "@/Stores/ExpenseStore";
import CategoryMonthlyTotalItem from "@/Pages/Expenses/Partials/CategoryMonthlyTotalItem.vue";
import ExpensesTotals from "@/Pages/Expenses/Partials/ExpensesTotals.vue";
import ExpenseTypeDropdown from "@/Pages/Expenses/Partials/ExpenseTypeDropdown.vue";
import { computed } from "vue";
import ViewSelector from "@/Pages/Expenses/Partials/ViewSelector.vue";

let props = defineProps<{
    expenses: App.Data.ExpenseData[];
    monthlyTotals: App.Data.MonthlyTotalData[];
    expensesView: string;
}>();

const findTotalsByCategoryId = (categoryId: number) => {
    return props.monthlyTotals.map((item) => {
        const filteredCategoryMonthlyTotals = item.categoryMonthlyTotals.filter(
            (item) => item.category.id === categoryId,
        );

        return {
            ...item,
            categoryMonthlyTotals: filteredCategoryMonthlyTotals,
        };
    });
};

const findExpensesByCategoryId = (categoryId: number, collection: App.Data.ExpenseData[]): App.Data.ExpenseData[] => {
    return collection.filter((item) => item.category.id === categoryId);
};

const currentMonthlyTotal = computed(() => {
    return props.monthlyTotals.find((item) => {
        return item.isCurrent === true;
    });
});
</script>

<template>
    <AppLayout title="Expenses">
        <template #header>
            <div class="flex w-full items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Expenses</h2>

                <div class="justify-end-center flex flex-col items-end space-y-1 sm:flex-row sm:space-x-4">
                    <div>
                        <ViewSelector :expenses-view="expensesView" />
                    </div>
                    <div>
                        <ExpenseTypeDropdown />
                    </div>
                </div>
            </div>
        </template>

        <div class="container mx-auto max-w-4xl px-2 py-12">
            <div class="mb-6">
                <ExpensesTotals :monthly-totals="monthlyTotals" />
            </div>

            <div class="space-y-3">
                <template v-if="expensesView == 'categories'">
                    <div
                        v-for="total in currentMonthlyTotal.categoryMonthlyTotals"
                        :key="total.id">
                        <CategoryMonthlyTotalItem
                            :monthly-totals="findTotalsByCategoryId(total.category.id)"
                            :expenses="findExpensesByCategoryId(total.category.id, expenses)" />
                    </div>
                </template>
                <template v-if="expensesView == 'daily'">
                    <ExpensesByDate :expenses="expenses" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
