<script setup lang="ts">
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage, Link } from "@inertiajs/vue3";
import { computed, ref, watchEffect } from "vue";

let props = defineProps<{
    expense: App.Data.ExpenseData;
}>();

const page = usePage();

const currencyFormatter = createCurrencyFormatter(page.props.auth.user.currency);

const startX = ref(0);
const currentX = ref(0);
const distance = computed(() => Math.max(0, currentX.value - startX.value));
watchEffect(() => console.log("watch effect " + distance.value));
</script>

<template>
    <div
        class="relative flex w-full transform select-none items-center justify-between px-3 py-2"
        :style="'transform: translateX(' + distance + 'px)'">
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

        <div class="w-1/3 text-right font-mono text-lg">{{ currencyFormatter.format(expense.amount) }}</div>
        <div
            class="absolute inset-0 h-full w-full bg-red-100/50"
            @touchstart="startX = currentX = $event.touches[0].clientX"
            @touchmove="currentX = $event.touches[0].clientX"
            @touchend="console.log('touchend')"></div>
    </div>
</template>
