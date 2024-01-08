<script lang="ts" setup>
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";
import { computed, ref } from "vue";
import { IconCirclePlus, IconChevronDown } from "@tabler/icons-vue";
import { useExpenseStore } from "@/Stores/ExpenseStore";
import ExpenseItem from "@/Pages/Expenses/Partials/ExpenseItem.vue";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage } from "@inertiajs/vue3";
import { getDate } from "@/Helpers/DatesHelper";

let props = defineProps<{
    expenses: App.Data.ExpenseData[];
    monthlyTotals: App.Data.MonthlyTotalData[];
}>();

const expenseStore = useExpenseStore();

const page = usePage();

let showExpenses = ref(false);

const percentage = computed(() => {
    return Math.round((currentCategoryMonthlyTotal.value.amount / currentMonthlyTotal.value.total) * 100);
});

const currentMonthlyTotal = computed(() => {
    return props.monthlyTotals.find((item) => {
        return item.isCurrent === true;
    });
});

const currentCategoryMonthlyTotal = computed(() => {
    let monthlyTotal = currentMonthlyTotal.value;

    return monthlyTotal.categoryMonthlyTotals[0];
});

const category = computed(() => {
    return currentCategoryMonthlyTotal.value.category;
});

const currencyFormatter = createCurrencyFormatter(page.props.auth.user.currency);
</script>

<template>
    <div class="divide-y overflow-hidden rounded-md bg-white shadow-sm">
        <div class="relative divide-y">
            <div class="relative flex justify-between py-1 pl-3 pr-1">
                <div class="flex items-center space-x-1">
                    <div class="w-6">
                        <IconChevronDown
                            :class="[
                                showExpenses ? '-rotate-180' : '',
                                'mx-auto text-gray-300 transition duration-500',
                            ]" />
                    </div>
                    <div
                        class="flex-1"
                        :style="'color:' + category.hex">
                        <CategoryIcon :category="category" />
                    </div>
                    <span class="block w-20 overflow-hidden text-ellipsis whitespace-nowrap font-semibold sm:w-full">{{
                        category.name
                    }}</span>
                </div>

                <div class="flex space-x-3">
                    <div class="text-right font-mono">
                        <div class="text-xl">
                            {{ currencyFormatter.format(currentCategoryMonthlyTotal.amount / 100) }}
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

                    <button
                        class="relative z-10 h-full w-8 rounded-lg bg-indigo-400 text-white sm:w-10"
                        @click.prevent="expenseStore.showSidebar(null, category)">
                        <IconCirclePlus class="mx-auto" />
                    </button>
                </div>
            </div>
            <div class="flex divide-x">
                <template
                    v-for="monthlyTotal in monthlyTotals"
                    :key="monthlyTotal.yearMonth">
                    <div class="flex-1 p-2 first:hidden last:hidden md:first:block md:last:block">
                        <div class="mb-2 flex justify-between text-xs text-gray-400">
                            <div>{{ getDate(monthlyTotal.yearMonth).format("MMM YY") }}</div>
                            <template v-if="monthlyTotal.categoryMonthlyTotals[0]">
                                <div
                                    :class="[
                                        monthlyTotal.categoryMonthlyTotals[0]?.previous_month_delta_amount > 0 ||
                                        monthlyTotal.categoryMonthlyTotals[0]?.previous_month_delta_amount == null
                                            ? 'text-red-500'
                                            : 'text-green-500',
                                    ]">
                                    <span
                                        v-if="
                                            monthlyTotal.categoryMonthlyTotals[0]?.previous_month_delta_amount > 0 ||
                                            monthlyTotal.categoryMonthlyTotals[0]?.previous_month_delta_amount == null
                                        "
                                        >+</span
                                    >

                                    {{
                                        currencyFormatter.format(
                                            (monthlyTotal.categoryMonthlyTotals[0].previous_month_delta_amount
                                                ? monthlyTotal.categoryMonthlyTotals[0].previous_month_delta_amount
                                                : monthlyTotal.categoryMonthlyTotals[0].amount) / 100,
                                        )
                                    }}
                                </div>
                            </template>
                        </div>
                        <div class="font-mono">
                            <span v-if="monthlyTotal.categoryMonthlyTotals.length > 0">
                                {{ currencyFormatter.format(monthlyTotal.categoryMonthlyTotals[0].amount / 100) }}
                            </span>
                            <span v-else>-</span>
                        </div>
                    </div>
                </template>
            </div>
            <button
                class="absolute inset-0 z-0 h-full w-full"
                @click.prevent="showExpenses = !showExpenses"></button>
        </div>

        <div v-if="showExpenses">
            <button
                v-for="expense in expenses"
                :key="expense.id"
                @click.prevent="expenseStore.showSidebar(expense)"
                class="w-full">
                <ExpenseItem :expense="expense">
                    <template v-slot:prefix>
                        <div class="text-sm">
                            {{ expense.date }}
                        </div>
                    </template>
                </ExpenseItem>
            </button>
        </div>
    </div>
</template>
