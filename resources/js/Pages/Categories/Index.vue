<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import CategoryFormSidebar from "@/Pages/Categories/Partials/CategoryFormSidebar.vue";
import { useCategoryStore } from "@/Stores/CategoryStore";
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const categoryStore = useCategoryStore();

let props = defineProps<{
    categories: App.Data.CategoryData[];
    totals: App.Data.CategoryTotalData[];
    canCreate: boolean;
}>();

const page = usePage();

const currencyFormatter = createCurrencyFormatter(page.props.auth.user.currency);

const grandTotal = computed(() => {
    let totalAmount = 0;

    for (let i = 0; i < props.totals.length; i++) {
        totalAmount += props.totals[i].total;
    }
    return totalAmount;
});

const getTotalByCategory = (categoryId: number): number => {
    const total = props.totals.find((item) => {
        return item.category_id === categoryId;
    });

    return total ? total.total : 0;
};
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Categories</h2>
                <SecondaryButton
                    v-if="props.canCreate"
                    @click.prevent="categoryStore.showSidebar()"
                    >Add category</SecondaryButton
                >
            </div>
        </template>

        <template #before-content>
            <CategoryFormSidebar />
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl px-2 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        class="divide-y rounded-md bg-white shadow"
                        @click.prevent="categoryStore.showSidebar(category)">
                        <div class="flex items-center space-x-4 p-4 text-lg">
                            <div :style="'color:' + category.hex">
                                <CategoryIcon :category="category" />
                            </div>
                            <div>{{ category.name }}</div>
                        </div>
                        <div class="grid grid-cols-3 divide-x text-left">
                            <div class="col-span-2 px-4 py-2">
                                <div class="mb-1 text-xs text-gray-500">Total expenses</div>
                                <div>
                                    {{ currencyFormatter.format(getTotalByCategory(category.id)) }}
                                </div>
                            </div>
                            <div class="inline-flex items-center justify-center">
                                <span class="text-lg font-semibold">
                                    {{ parseInt((getTotalByCategory(category.id) * 100) / grandTotal) }}%
                                </span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
