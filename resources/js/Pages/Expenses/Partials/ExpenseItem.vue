<script setup lang="ts">
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage, Link, useForm } from "@inertiajs/vue3";
import { computed, ref, watchEffect } from "vue";
import { useExpenseStore } from "@/Stores/ExpenseStore";
import { IconTrash } from "@tabler/icons-vue";

let props = defineProps<{
    expense: App.Data.ExpenseData;
}>();

const expenseStore = useExpenseStore();

const page = usePage();

const currencyFormatter = createCurrencyFormatter(page.props.currency.code);

const startX = ref(0);
const currentX = ref(0);
const distance = computed(() => (moving.value ? Math.max(0, currentX.value - startX.value) : 0));
const moving = ref(false);
//watchEffect(() => console.log("watch effect " + distance.value + " " + moving.value));

let form = useForm({});

const deleteExpense = () => {
    form.delete(route("expenses.delete", props.expense.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("deleted");
        },
    });
};
</script>

<template>
    <div class="relative">
        <div class="absolute inset-0 flex h-full w-full items-center space-x-2 bg-red-500 px-3 py-2 text-white">
            <IconTrash />
            <span
                class="text-lg font-semibold"
                v-text="form.processing ? 'Deleting..' : 'Delete'"></span>
        </div>
        <div
            :style="'transform: translateX(' + distance + 'px)'"
            :class="[
                form.processing ? 'opacity-0' : 'opacity-100',
                'relative flex w-full transform select-none items-center justify-between bg-white px-3 py-2',
            ]">
            <div class="w-2/3 text-left">
                <div class="flex items-center space-x-4">
                    <div>
                        <slot name="prefix">
                            <div :style="'color:' + expense.category.hex">
                                <CategoryIcon :category="expense.category" />
                            </div>
                        </slot>
                    </div>
                    <div v-if="expense.project">
                        <Link
                            :href="route('projects.show', { project: expense.project.id })"
                            class="relative z-20 flex items-center space-x-1 rounded-full pl-1 pr-2 text-sm hover:bg-gray-200">
                            <div
                                class="h-3 w-3 rounded-full"
                                :style="'background:' + expense.project.hex"></div>
                            <span>{{ expense.project.name }}</span>
                        </Link>
                    </div>
                </div>
                <div class="text-gray-500">{{ expense.notes ? expense.notes : "&nbsp;" }}</div>
            </div>

            <div class="w-1/3 text-right font-mono text-lg">{{ currencyFormatter.format(expense.converted_amount) }}</div>
        </div>
        <div
            class="absolute inset-0 h-full w-full"
            @click="expenseStore.showSidebar(expense)"
            @touchstart="
                moving = true;
                startX = currentX = $event.touches[0].clientX;
            "
            @touchmove="currentX = $event.touches[0].clientX"
            @touchend="
                if (distance > 150) deleteExpense();
                moving = false;
            "></div>
    </div>
</template>
