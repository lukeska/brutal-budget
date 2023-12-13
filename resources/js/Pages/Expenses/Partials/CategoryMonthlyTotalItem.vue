<script lang="ts" setup>
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";
import { computed, ref } from "vue";
import { IconCirclePlus, IconChevronDown } from "@tabler/icons-vue";
import { useExpenseStore } from "@/Stores/ExpenseStore";
import ExpenseItem from "@/Pages/Expenses/Partials/ExpenseItem.vue";

const expenseStore = useExpenseStore();

let props = defineProps<{
    categoryTotal: App.Data.CategoryMonthlyTotalData;
    categoryTotalPreviousMonth: App.Data.CategoryMonthlyTotalData;
    categoryTotalFollowingMonth: App.Data.CategoryMonthlyTotalData;
    totalExpenses: number;
    expenses: App.Data.ExpenseData[];
}>();

let showExpenses = ref(false);

const currencyFormatter = new Intl.NumberFormat("it-IT", {
    style: "currency",
    currency: "EUR",
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const percentage = computed(() => {
    return Math.round((props.categoryTotal.amount / props.totalExpenses) * 100);
});

const previousMonthDelta = computed(() => {
    return props.categoryTotal.amount - props.categoryTotalPreviousMonth.amount;
});

const followingMonthDelta = computed(() => {
    return props.categoryTotalFollowingMonth.amount - props.categoryTotal.amount;
});
</script>

<template>
    <div class="divide-y rounded border">
        <div class="relative flex justify-between py-1 pl-3 pr-1">
            <div
                class="flex items-center space-x-2"
                :style="'color:' + categoryTotal.category.hex">
                <CategoryIcon :category="categoryTotal.category" />
                <span class="font-semibold">{{ categoryTotal.category.name }}</span>
            </div>

            <div class="flex space-x-6">
                <div class="text-right font-mono">
                    <div class="mb-1 text-xl">{{ currencyFormatter.format(categoryTotal.amount / 100) }}</div>
                    <div class="flex items-center space-x-2">
                        <div class="relative h-1 w-20 bg-gray-200">
                            <div
                                class="h-full"
                                :style="
                                    'width:' + percentage + '%;background-color:' + categoryTotal.category.hex
                                "></div>
                        </div>
                        <div class="w-6 text-sm text-gray-400">{{ percentage }}%</div>
                    </div>
                </div>
                <div class="space-x-1">
                    <button
                        class="h-full w-12 rounded-lg bg-neutral-50"
                        @click.prevent="showExpenses = !showExpenses">
                        <IconChevronDown
                            :class="[showExpenses ? 'rotate-180' : '', 'mx-auto transition duration-500']" />
                    </button>
                    <button
                        class="h-full w-12 rounded-lg bg-neutral-50"
                        @click.prevent="expenseStore.showSidebar(null, categoryTotal.category)">
                        <IconCirclePlus class="mx-auto" />
                    </button>
                </div>
            </div>
        </div>
        <div class="flex divide-x">
            <div class="flex-1">
                <div
                    v-if="categoryTotalPreviousMonth"
                    class="flex justify-between space-x-2 divide-x text-sm text-gray-400">
                    <div class="px-3 py-2">
                        Previous month
                        <span class="font-mono text-black">{{
                            currencyFormatter.format(categoryTotalPreviousMonth.amount / 100)
                        }}</span>
                    </div>
                    <div :class="[previousMonthDelta > 0 ? 'text-red-500' : 'text-green-500', 'px-3 py-2 font-mono ']">
                        <span v-if="previousMonthDelta > 0">+</span
                        >{{ currencyFormatter.format(previousMonthDelta / 100) }}
                    </div>
                </div>
            </div>
            <div class="flex-1">
                <div
                    v-if="categoryTotalFollowingMonth"
                    class="flex justify-between space-x-2 divide-x text-sm text-gray-400">
                    <div class="px-3 py-2">
                        Following month
                        <span class="font-mono text-black">{{
                            currencyFormatter.format(categoryTotalFollowingMonth.amount / 100)
                        }}</span>
                    </div>
                    <div :class="[followingMonthDelta < 0 ? 'text-red-500' : 'text-green-500', 'px-3 py-2 font-mono ']">
                        <span v-if="followingMonthDelta > 0">+</span
                        >{{ currencyFormatter.format(followingMonthDelta / 100) }}
                    </div>
                </div>
            </div>
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
