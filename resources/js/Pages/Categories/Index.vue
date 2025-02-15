<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import CategoryFormSidebar from "@/Pages/Categories/Partials/CategoryFormSidebar.vue";
import { useCategoryStore } from "@/Stores/CategoryStore";
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { IconEdit, IconEye } from "@tabler/icons-vue";

const categoryStore = useCategoryStore();

let props = defineProps<{
    categories: App.Data.CategoryData[];
    totals: App.Data.CategoryTotalData[];
    canCreate: boolean;
}>();

const page = usePage();

const currencyFormatter = createCurrencyFormatter(page.props.currency.code);

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
                    >Add category
                </SecondaryButton>
            </div>
        </template>

        <template #before-content>
            <CategoryFormSidebar />
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <div
                        v-for="category in categories"
                        :key="category.id"
                        class="divide-y rounded-md bg-white shadow">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2 p-4 text-lg">
                                <div :style="'color:' + category.hex">
                                    <CategoryIcon :category="category" />
                                </div>
                                <div>{{ category.name }}</div>
                            </div>
                            <div>
                                <button
                                    @click.prevent="categoryStore.showSidebar(category)"
                                    class="inline-flex h-10 w-10 items-center justify-center text-gray-400 hover:text-gray-800">
                                    <IconEdit />
                                </button>
                                <Link
                                    :href="route('categories.show', { category: category })"
                                    class="inline-flex h-10 w-10 items-center justify-center border-0 text-gray-400 hover:text-gray-800">
                                    <IconEye />
                                </Link>
                            </div>
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
                                    {{
                                        grandTotal == 0
                                            ? "0"
                                            : parseInt((getTotalByCategory(category.id) * 100) / grandTotal)
                                    }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
