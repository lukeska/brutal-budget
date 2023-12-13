<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref } from "vue";

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
    </AppLayout>
</template>
