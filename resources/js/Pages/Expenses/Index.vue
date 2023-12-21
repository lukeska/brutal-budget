<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import ExpensesByDate from "@/Pages/Expenses/Partials/ExpensesByDate.vue";
import { useForm } from "@inertiajs/vue3";
import { useExpenseStore } from "@/Stores/ExpenseStore";
import CategoryMonthlyTotalItem from "@/Pages/Expenses/Partials/CategoryMonthlyTotalItem.vue";
import ExpensesTotals from "@/Pages/Expenses/Partials/ExpensesTotals.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import Dropdown from "@/Components/Dropdown.vue";
import ExpenseTypeDropdown from "@/Pages/Expenses/Partials/ExpenseTypeDropdown.vue";

const expenseStore = useExpenseStore();

let props = defineProps<{
    expenses: App.Data.ExpenseData[];
    categoryMonthlyTotals: App.Data.CategoryMonthlyTotalData[];
    categoryMonthlyTotalsPreviousMonth: App.Data.CategoryMonthlyTotalData[];
    categoryMonthlyTotalsFollowingMonth: App.Data.CategoryMonthlyTotalData[];
    totalExpenses: number;
    totalExpensesPreviousMonth: number;
    totalExpensesFollowingMonth: number;
    year: number;
    month: number;
}>();

const findTotalByCategoryId = (categoryId: number, collection: App.Data.CategoryMonthlyTotalData[]) => {
    return collection.find((item) => item.category.id === categoryId);
};

const findExpensesByCategoryId = (categoryId: number, collection: App.Data.ExpenseData[]): App.Data.ExpenseData[] => {
    return collection.filter((item) => item.category.id === categoryId);
};
</script>

<template>
    <AppLayout title="Expenses">
        <template #header>
            <div class="flex w-full justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Expenses</h2>

                <div>
                    <!-- Expense Type Dropdown -->
                    <ExpenseTypeDropdown />
                </div>
            </div>
        </template>

        <div class="container mx-auto py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="flex overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="w-1/2 p-6">
                        <div class="mb-6">
                            <ExpensesTotals
                                :total-expenses-previous-month="totalExpensesPreviousMonth"
                                :month="month"
                                :year="year"
                                :total-expenses="totalExpenses"
                                :total-expenses-following-month="totalExpensesFollowingMonth" />
                        </div>

                        <div class="space-y-3">
                            <div
                                v-for="total in categoryMonthlyTotals"
                                :key="total.id">
                                <CategoryMonthlyTotalItem
                                    :expenses="findExpensesByCategoryId(total.category.id, expenses)"
                                    :category-total="total"
                                    :category-total-previous-month="
                                        findTotalByCategoryId(total.category.id, categoryMonthlyTotalsPreviousMonth)
                                    "
                                    :category-total-following-month="
                                        findTotalByCategoryId(total.category.id, categoryMonthlyTotalsFollowingMonth)
                                    "
                                    :total-expenses="totalExpenses" />
                            </div>
                        </div>
                    </div>

                    <div class="flow-root w-1/2 p-6 lg:p-8">
                        <div class="space-y-3">
                            <ExpensesByDate :expenses="expenses" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
