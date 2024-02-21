<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import { computed } from "vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { useProjectStore } from "@/Stores/ProjectStore";
import ProjectFormSidebar from "@/Pages/Projects/Partials/ProjectFormSidebar.vue";
import { usePage, Link } from "@inertiajs/vue3";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { IconEdit, IconEye } from "@tabler/icons-vue";

const projectStore = useProjectStore();

let props = defineProps<{
    projects: App.Data.ProjectData[];
    totals: App.Data.ProjectTotalData[];
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
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div
                        v-for="project in projects"
                        :key="project.id"
                        class="divide-y rounded-md bg-white text-left shadow">
                        <div class="flex items-center justify-between p-4">
                            <div class="flex items-center space-x-4 text-lg">
                                <div
                                    :style="'background:' + project.hex"
                                    class="h-8 w-8 shrink-0 rounded-md"></div>
                                <div>{{ project.name }}</div>
                            </div>
                            <div>
                                <button
                                    @click.prevent="projectStore.showSidebar(project)"
                                    class="inline-flex h-10 w-10 items-center justify-center text-gray-400 hover:text-gray-800">
                                    <IconEdit />
                                </button>
                                <Link
                                    :href="route('projects.show', { project: project })"
                                    class="inline-flex h-10 w-10 items-center justify-center border-0 text-gray-400 hover:text-gray-800">
                                    <IconEye />
                                </Link>
                            </div>
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
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
