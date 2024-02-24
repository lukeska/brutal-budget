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
    <div class="flex w-full items-center justify-between px-3 py-2 hover:bg-neutral-50">
        <div class="w-2/3 text-left">
            <div>
                <slot name="prefix">
                    <div :style="'color:' + expense.category.hex">
                        <CategoryIcon :category="expense.category" />
                    </div>
                </slot>
            </div>
            <div class="text-gray-500">{{ expense.notes }}</div>
        </div>

        <div class="w-1/3 text-right font-mono text-lg">{{ currencyFormatter.format(expense.amount) }}</div>
    </div>
</template>
