<script setup lang="ts">
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";

let props = defineProps<{
    expense: App.Data.ExpenseData;
}>();

const currencyFormatter = new Intl.NumberFormat("it-IT", {
    style: "currency",
    currency: "EUR",
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});
</script>

<template>
    <div class="flex w-full items-center space-x-4 px-3 py-2 hover:bg-neutral-50">
        <slot name="prefix">
            <div :style="'color:' + expense.category.hex">
                <CategoryIcon :category="expense.category" />
            </div>
        </slot>
        <div class="min-w-[80px] text-right font-mono">{{ currencyFormatter.format(expense.amount / 100) }}</div>
        <div class="text-gray-500">{{ expense.notes }}</div>
    </div>
</template>
