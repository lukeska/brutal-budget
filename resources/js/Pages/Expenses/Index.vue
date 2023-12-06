<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import CategoryLabel from "@/Pages/Categories/Partials/CategoryLabel.vue";
import ExpenseForm from "@/Pages/Expenses/Partials/ExpenseForm.vue";
import { ref } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from "@inertiajs/vue3";

let props = defineProps<{
    expenses: App.Data.ExpenseData[];
    categoryMonthlyTotals: App.Data.CategoryMonthlyTotalData[];
    totalExpenses: number;
}>();

let form = useForm({});

let currentExpense = ref(null);

let showExpenseForm = ref(false);

let showCreateSuccessMessage = ref(false);
let showUpdateSuccessMessage = ref(false);

const showCreateExpense = () => {
    showExpenseForm.value = true;
    currentExpense.value = null;
};

const showEditExpense = (expense: App.Data.ExpenseData) => {
    showExpenseForm.value = true;
    currentExpense.value = expense;
};

const flashCreateSuccessMessage = () => {
    showExpenseForm.value = false;
    showCreateSuccessMessage.value = true;
    setTimeout(function () {
        showCreateSuccessMessage.value = false;
    }, 3000);
};

const flashUpdateSuccessMessage = () => {
    showExpenseForm.value = false;
    showUpdateSuccessMessage.value = true;
    setTimeout(function () {
        showUpdateSuccessMessage.value = false;
    }, 3000);
};

const deleteExpense = (expense: App.Data.ExpenseData) => {
    form.delete(route("expenses.delete", expense.id), {
        preserveScroll: true,
    });
};

const currencyFormatter = new Intl.NumberFormat("it-IT", {
    style: "currency",
    currency: "EUR",
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});
</script>

<template>
    <AppLayout title="Expenses">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Expenses</h2>
        </template>

        <div class="container mx-auto py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="mb-4 flex overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg lg:p-8">
                    <h2 class="w-1/2 text-xl">
                        Total month expense:
                        {{ currencyFormatter.format(totalExpenses / 100) }}
                    </h2>

                    <div class="w-1/2 space-y-3">
                        <div
                            v-for="total in categoryMonthlyTotals"
                            :key="total.id"
                            class="flex items-center space-x-4">
                            <CategoryLabel :category="total.category" />
                            <div class="flex-1 border-b border-dotted border-gray-600"></div>
                            <div>
                                {{ currencyFormatter.format(total.amount / 100) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="flow-root w-2/3 p-6 lg:p-8">
                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead>
                                        <tr>
                                            <th
                                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                                scope="col">
                                                Date
                                            </th>
                                            <th
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                                scope="col">
                                                Amount
                                            </th>
                                            <th
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                                scope="col">
                                                Category
                                            </th>
                                            <th
                                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                                scope="col">
                                                Description
                                            </th>
                                            <th
                                                class="relative py-3.5 pl-3 pr-4 sm:pr-0"
                                                scope="col">
                                                <span class="sr-only">Edit</span>
                                            </th>
                                            <th
                                                class="relative py-3.5 pl-3 pr-4 sm:pr-0"
                                                scope="col">
                                                <span class="sr-only">Delete</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        <tr
                                            v-for="expense in expenses"
                                            :key="expense.id">
                                            <td class="whitespace-nowrap py-5 pl-4 pr-3 text-sm sm:pl-0">
                                                <div class="flex items-center">
                                                    {{ expense.date }}
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                                                {{ currencyFormatter.format(expense.amount / 100) }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                                                <CategoryLabel :category="expense.category" />
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                                                {{ expense.notes }}
                                            </td>
                                            <td
                                                class="relative whitespace-nowrap py-5 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                                <a
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                    href="#"
                                                    @click.prevent="showEditExpense(expense)"
                                                    >Edit</a
                                                >
                                            </td>
                                            <td
                                                class="relative whitespace-nowrap py-5 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                                <a
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                    href="#"
                                                    @click.prevent="deleteExpense(expense)"
                                                    >Delete</a
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="w-1/3 bg-zinc-50 p-6 lg:p-8">
                        <PrimaryButton @click.prevent="showCreateExpense">Create Expense</PrimaryButton>

                        <div
                            v-if="showCreateSuccessMessage"
                            class="py-6 text-xs text-green-500">
                            Expense created!
                        </div>

                        <div
                            v-if="showUpdateSuccessMessage"
                            class="py-6 text-xs text-green-500">
                            Expense updated!
                        </div>

                        <div class="py-6">
                            <ExpenseForm
                                v-if="showExpenseForm"
                                :expense="currentExpense"
                                @cancel="showExpenseForm = false"
                                @updated="flashUpdateSuccessMessage" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
