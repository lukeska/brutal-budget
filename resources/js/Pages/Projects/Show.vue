<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import ExpensesByDate from "@/Pages/Expenses/Partials/ExpensesByDate.vue";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage } from "@inertiajs/vue3";

let props = defineProps<{
    project: App.Data.ProjectData;
    expenses: App.Data.ExpenseData[];
    total: number;
}>();

const page = usePage();

const currencyFormatter = createCurrencyFormatter(page.props.currency.code);
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Project: {{ project.name }}</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg lg:p-8">
                    <div class="mb-4 text-lg">Total expenses: {{ currencyFormatter.format(total / 100) }}</div>

                    <div class="space-y-3">
                        <ExpensesByDate :expenses="expenses" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
