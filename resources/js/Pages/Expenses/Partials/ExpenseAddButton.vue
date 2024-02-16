<script setup lang="ts">
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useExpenseStore } from "@/Stores/ExpenseStore";
import { IconSquareRoundedPlus } from "@tabler/icons-vue";

const expenseStore = useExpenseStore();

export interface Props {
    shape?: string;
}

withDefaults(defineProps<Props>(), {
    shape: "null",
});
</script>

<template>
    <template v-if="shape == 'circular'">
        <button
            type="button"
            class="inline-flex h-16 w-16 items-center justify-center rounded-full border border-transparent bg-gray-800 p-0 text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 disabled:opacity-50"
            @click.prevent="expenseStore.showSidebar()"
            data-cy="expense-add-button-circular">
            <IconSquareRoundedPlus size="38" />
        </button>
    </template>
    <PrimaryButton
        v-else
        @click.prevent="expenseStore.showSidebar()"
        data-cy="expense-add-button"
        >+ Expense
    </PrimaryButton>
</template>
