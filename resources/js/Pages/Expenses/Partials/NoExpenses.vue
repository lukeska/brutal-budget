<script setup lang="ts">
import { IconChartDonut4 } from "@tabler/icons-vue";
import ExpenseAddButton from "@/Pages/Expenses/Partials/ExpenseAddButton.vue";
</script>

<template>
    <div class="grid w-full grid-cols-1 grid-rows-1 place-items-center content-center bg-white px-4 py-10">
        <IconChartDonut4
            :size="400"
            class="col-[1] row-[1] text-gray-50" />
        <div class="col-[1] row-[1] max-w-[400px] text-center">
            <p class="mb-3">
                No expenses yet. Go ahead, click the button below and try the thrill of logging your first expense.
            </p>
            <ExpenseAddButton />
        </div>
    </div>
</template>
