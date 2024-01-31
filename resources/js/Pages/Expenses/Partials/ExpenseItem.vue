<script setup lang="ts">
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage } from "@inertiajs/vue3";

let props = defineProps<{
    expense: App.Data.ExpenseData;
}>();

const page = usePage();

const currencyFormatter = createCurrencyFormatter(page.props.auth.user.currency);
</script>

<template>
    <div class="flex w-full items-center space-x-4 px-3 py-2 hover:bg-neutral-50">
        <slot name="prefix">
            <div :style="'color:' + expense.category.hex">
                <CategoryIcon :category="expense.category" />
            </div>
        </slot>
        <div class="min-w-[80px] text-right font-mono">{{ currencyFormatter.format(expense.amount) }}</div>
        <div class="text-gray-500">{{ expense.notes }}</div>
    </div>
</template>
