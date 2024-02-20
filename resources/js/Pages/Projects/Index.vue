<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { computed, ref } from "vue";
import ProjectForm from "@/Pages/Projects/Partials/ProjectForm.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { useProjectStore } from "@/Stores/ProjectStore";
import CategoryFormSidebar from "@/Pages/Categories/Partials/CategoryFormSidebar.vue";
import ProjectFormSidebar from "@/Pages/Projects/Partials/ProjectFormSidebar.vue";
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";
import { usePage } from "@inertiajs/vue3";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";

const projectStore = useProjectStore();

let props = defineProps<{
    projects: App.Data.ProjectData[];
    totals: App.Data.ProjectTotalData[];
    canCreate: boolean;
}>();

const page = usePage();

const currencyFormatter = createCurrencyFormatter(page.props.auth.user.currency);

let showCreateForm = ref(false);

const grandTotal = computed(() => {
    let totalAmount = 0;

    for (let i = 0; i < props.totals.length; i++) {
        totalAmount += props.totals[i].total;
    }
    return totalAmount;
});

const getTotalByProject = (projectId: number): number => {
    const total = props.totals.find((item) => {
        return item.project_id === projectId;
    });

    return total ? total.total : 0;
};
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Projects</h2>
                <SecondaryButton
                    v-if="props.canCreate"
                    @click.prevent="projectStore.showSidebar()"
                    >Add project</SecondaryButton
                >
            </div>
        </template>

        <template #before-content>
            <ProjectFormSidebar />
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl px-2 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <button
                        v-for="project in projects"
                        :key="project.id"
                        class="divide-y rounded-md bg-white text-left shadow"
                        @click.prevent="projectStore.showSidebar(project)">
                        <div class="flex items-center space-x-4 p-4 text-lg">
                            <div
                                :style="'background:' + project.hex"
                                class="h-8 w-8 shrink-0 rounded-md"></div>
                            <div>{{ project.name }}</div>
                        </div>
                        <div class="grid grid-cols-3 divide-x text-left">
                            <div class="col-span-2 px-4 py-2">
                                <div class="mb-1 text-xs text-gray-500">Total expenses</div>
                                <div>
                                    {{ currencyFormatter.format(getTotalByProject(project.id)) }}
                                </div>
                            </div>
                            <div class="inline-flex items-center justify-center">
                                <span class="text-lg font-semibold">
                                    {{
                                        grandTotal == 0
                                            ? "0"
                                            : parseInt((getTotalByProject(project.id) * 100) / grandTotal)
                                    }}%
                                </span>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-xl sm:rounded-lg lg:p-8">
                    <PrimaryButton
                        type="button"
                        @click.prevent="showCreateForm = true"
                        >Create new project</PrimaryButton
                    >
                    <div class="mt-3 bg-zinc-50 px-5">
                        <ProjectForm
                            v-if="showCreateForm"
                            @cancel="showCreateForm = false" />
                    </div>

                    <ul
                        class="divide-y divide-gray-100"
                        role="list">
                        <li
                            v-for="project in projects"
                            :key="project.id">
                            <ProjectForm :project="project" />
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
