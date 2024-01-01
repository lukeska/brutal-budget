<script setup lang="ts">
import moment from "moment/moment";
import { computed } from "vue";
import { IconChevronRight, IconChevronLeft } from "@tabler/icons-vue";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage } from "@inertiajs/vue3";

let props = defineProps<{
    totalExpenses: number;
    totalExpensesPreviousMonth: number;
    totalExpensesFollowingMonth: number;
    year: number;
    month: number;
}>();

const page = usePage();

const previousMonth = computed(() => {
    return moment({ year: props.year, month: props.month - 1 })
        .clone()
        .subtract(1, "months");
});

const followingMonth = computed(() => {
    return moment({ year: props.year, month: props.month - 1 })
        .clone()
        .add(1, "months");
});

const currencyFormatter = createCurrencyFormatter(page.props.auth.user.currency);
</script>

<template>
    <div class="flex items-center justify-between space-x-6">
        <a
            :href="
                route('expenses.index', {
                    year: previousMonth.format('YYYY'),
                    month: previousMonth.format('MM'),
                })
            "
            class="flex items-center space-x-1">
            <IconChevronLeft class="text-gray-300" />
            <div>
                <div class="text-xs text-gray-500">
                    {{ previousMonth.format("MMMM YYYY") }}
                </div>
                <div class="font-mono text-lg">
                    {{ currencyFormatter.format(totalExpensesPreviousMonth / 100) }}
                </div>
            </div>
        </a>
        <div class="h-px flex-1 border-t border-dotted border-gray-400"></div>
        <div class="text-center">
            <div class="text-sm text-gray-500">
                {{ moment({ year, month: month - 1 }).format("MMMM YYYY") }}
            </div>
            <div class="font-mono text-xl">
                {{ currencyFormatter.format(totalExpenses / 100) }}
            </div>
        </div>
        <div class="h-px flex-1 border-t border-dotted border-gray-400"></div>
        <a
            :href="
                route('expenses.index', {
                    year: followingMonth.format('YYYY'),
                    month: followingMonth.format('MM'),
                })
            "
            class="flex items-center space-x-1 text-right">
            <div>
                <div class="text-xs text-gray-500">
                    {{
                        moment({ year, month: month - 1 })
                            .add(1, "month")
                            .format("MMMM YYYY")
                    }}
                </div>
                <div class="font-mono text-lg">
                    {{ currencyFormatter.format(totalExpensesFollowingMonth / 100) }}
                </div>
            </div>
            <IconChevronRight class="text-gray-300" />
        </a>
    </div>
</template>
