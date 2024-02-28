<script setup lang="ts">
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ExpenseAddButton from "@/Pages/Expenses/Partials/ExpenseAddButton.vue";

let props = defineProps<{
    onboardingStatus: App.Data.OnboardingStatusData;
}>();
</script>
<template>
    <div>
        <div class="mb-3 text-xl font-semibold">Create your first expense</div>
        <div class="flex space-x-4">
            <ExpenseAddButton></ExpenseAddButton>
            <SecondaryButton>Skip</SecondaryButton>
        </div>
    </div>
</template>
