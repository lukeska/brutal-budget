<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import CategoryForm from "@/Pages/Categories/Partials/CategoryForm.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref } from "vue";

let props = defineProps<{
    categories: App.Data.CategoryData[];
}>();

let showCreateForm = ref(false);
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Categories</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg lg:p-8">
                    <PrimaryButton @click.prevent="showCreateForm = true">Create new category</PrimaryButton>
                    <div class="mt-3 bg-zinc-50 px-5">
                        <CategoryForm
                            v-if="showCreateForm"
                            @cancel="showCreateForm = false" />
                    </div>

                    <ul
                        class="divide-y divide-gray-100"
                        role="list">
                        <li
                            v-for="category in categories"
                            :key="category.id">
                            <CategoryForm :category="category" />
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
