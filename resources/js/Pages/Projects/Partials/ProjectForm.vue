<script setup lang="ts">
import { useForm, usePage } from "@inertiajs/vue3";
import { IconTrash } from "@tabler/icons-vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { iconComponents } from "@/Pages/Categories/Partials/CategoryIcons.js";
import { computed, ref } from "vue";
import { useProjectStore } from "@/Stores/ProjectStore";
import resolveConfig from "tailwindcss/resolveConfig";
import tailwindConfig from "../../../../../tailwind.config";

const emit = defineEmits<{
    created: [];
    updated: [];
    cancel: [];
    deleted: [];
}>();

const projectStore = useProjectStore();

const page = usePage();

const form = useForm({
    name: projectStore.project.name,
    icon: projectStore.project.icon,
    hex: projectStore.project.hex,
});

const submit = (action: String) => {
    if (form.processing) {
        return;
    }

    if (action === "update") {
        form.patch(route("projects.update", projectStore.project.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("updated");
            },
        });
    } else if (action === "create") {
        form.put(route("projects.create"), {
            preserveScroll: true,
            onSuccess: (page) => {
                // update the current expense, so the UI will switch from create to update.
                // This will avoid creating multiple expenses if the user clicks "create" again before the form closes
                projectStore.project = page.props.flash.project;

                emit("created", page.props.flash.project);

                form.reset();
            },
        });
    } else if (action === "delete") {
        form.delete(route("projects.delete", projectStore.project.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("deleted");
            },
        });
    }
};

const canUpdate = computed((): boolean => {
    if (projectStore.isNewProject) {
        return true;
    }

    return projectStore.project.permissions?.update;
});

const canDelete = computed((): boolean => {
    if (projectStore.isNewProject) {
        return true;
    }

    return projectStore.project.permissions?.delete;
});

const selectHex = (hex: string) => {
    if (!canUpdate.value) {
        return;
    }

    form.hex = hex;
};

const fullConfig = resolveConfig(tailwindConfig);

const colors = [
    { hex: fullConfig.theme.colors.red["300"] },
    { hex: fullConfig.theme.colors.red["500"] },
    { hex: fullConfig.theme.colors.red["700"] },
    { hex: fullConfig.theme.colors.orange["300"] },
    { hex: fullConfig.theme.colors.orange["500"] },
    { hex: fullConfig.theme.colors.orange["700"] },
    { hex: fullConfig.theme.colors.amber["300"] },
    { hex: fullConfig.theme.colors.amber["500"] },
    { hex: fullConfig.theme.colors.amber["700"] },
    { hex: fullConfig.theme.colors.yellow["300"] },
    { hex: fullConfig.theme.colors.yellow["500"] },
    { hex: fullConfig.theme.colors.yellow["700"] },
    { hex: fullConfig.theme.colors.lime["300"] },
    { hex: fullConfig.theme.colors.lime["500"] },
    { hex: fullConfig.theme.colors.lime["700"] },
    { hex: fullConfig.theme.colors.green["300"] },
    { hex: fullConfig.theme.colors.green["500"] },
    { hex: fullConfig.theme.colors.green["700"] },
    { hex: fullConfig.theme.colors.emerald["300"] },
    { hex: fullConfig.theme.colors.emerald["500"] },
    { hex: fullConfig.theme.colors.emerald["700"] },
    { hex: fullConfig.theme.colors.teal["300"] },
    { hex: fullConfig.theme.colors.teal["500"] },
    { hex: fullConfig.theme.colors.teal["700"] },
    { hex: fullConfig.theme.colors.cyan["300"] },
    { hex: fullConfig.theme.colors.cyan["500"] },
    { hex: fullConfig.theme.colors.cyan["700"] },
    { hex: fullConfig.theme.colors.sky["300"] },
    { hex: fullConfig.theme.colors.sky["500"] },
    { hex: fullConfig.theme.colors.sky["700"] },
    { hex: fullConfig.theme.colors.blue["300"] },
    { hex: fullConfig.theme.colors.blue["500"] },
    { hex: fullConfig.theme.colors.blue["700"] },
    { hex: fullConfig.theme.colors.indigo["300"] },
    { hex: fullConfig.theme.colors.indigo["500"] },
    { hex: fullConfig.theme.colors.indigo["700"] },
    { hex: fullConfig.theme.colors.violet["300"] },
    { hex: fullConfig.theme.colors.violet["500"] },
    { hex: fullConfig.theme.colors.violet["700"] },
    { hex: fullConfig.theme.colors.purple["300"] },
    { hex: fullConfig.theme.colors.purple["500"] },
    { hex: fullConfig.theme.colors.purple["700"] },
    { hex: fullConfig.theme.colors.fuchsia["300"] },
    { hex: fullConfig.theme.colors.fuchsia["500"] },
    { hex: fullConfig.theme.colors.fuchsia["700"] },
    { hex: fullConfig.theme.colors.pink["300"] },
    { hex: fullConfig.theme.colors.pink["500"] },
    { hex: fullConfig.theme.colors.pink["700"] },
    { hex: fullConfig.theme.colors.rose["300"] },
    { hex: fullConfig.theme.colors.rose["500"] },
    { hex: fullConfig.theme.colors.rose["700"] },
];
const selectedColor = ref(colors.find(({ hex }) => hex === projectStore.project.hex));
</script>

<template>
    <form
        class="relative flex h-full flex-col"
        @keydown.enter.prevent="submit(projectStore.isNewProject ? 'create' : 'update')">
        <div class="flex-1 overflow-x-hidden overflow-y-scroll px-6 py-4">
            <div class="flex flex-col gap-y-6">
                <!-- Name -->
                <div>
                    <label
                        class="block text-sm font-medium leading-6 text-gray-900"
                        for="name"
                        >Name</label
                    >
                    <div class="mt-2">
                        <input
                            id="name"
                            v-model="form.name"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="name"
                            type="text"
                            :disabled="!canUpdate"
                            data-1p-ignore />
                    </div>

                    <div
                        v-if="form.errors.name"
                        class="mt-1 text-xs text-red-500"
                        v-text="form.errors.name"></div>
                </div>

                <!-- Colors -->
                <div>
                    <label
                        class="block text-sm font-medium leading-6 text-gray-900"
                        for="hex"
                        >Color</label
                    >
                    <div class="grid grid-cols-5 gap-2 sm:grid-cols-7">
                        <button
                            v-for="color in colors"
                            :key="color.hex"
                            @click.prevent="selectHex(color.hex)">
                            <div
                                :class="[
                                    form.hex === color.hex ? 'outline outline-black' : '',
                                    'inline-flex h-12 w-12 rounded-md',
                                ]"
                                :style="'background:' + color.hex"></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div
                class="flex w-full space-x-3 bg-gray-100 px-6 py-4"
                v-if="canUpdate">
                <div class="relative flex flex-1 items-center space-x-px">
                    <PrimaryButton
                        @click.prevent="submit(projectStore.isNewProject ? 'create' : 'update')"
                        class="h-10 flex-1 rounded-md bg-indigo-400 shadow hover:bg-indigo-500 focus:bg-indigo-500"
                        :disabled="form.processing">
                        <span class="mx-auto">
                            {{ !projectStore.isNewProject ? "Update" : "Create" }}
                        </span>
                    </PrimaryButton>
                </div>

                <button
                    type="submit"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-white text-red-400 shadow disabled:opacity-50"
                    @click.prevent="projectStore.isNewProject ? emit('cancel') : submit('delete')"
                    :disabled="form.processing || !canDelete">
                    <IconTrash />
                </button>
            </div>
        </div>
    </form>
</template>
