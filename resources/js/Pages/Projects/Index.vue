<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref } from "vue";
import ProjectForm from "@/Pages/Projects/Partials/ProjectForm.vue";

let props = defineProps<{
    projects: App.Data.ProjectData[];
}>();

let showCreateForm = ref(false);
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Projects</h2>
        </template>

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
