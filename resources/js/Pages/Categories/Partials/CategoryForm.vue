<script setup lang="ts">
import { useCategoryStore } from "@/Stores/CategoryStore";
import { useForm, usePage } from "@inertiajs/vue3";
import { IconTrash } from "@tabler/icons-vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { iconComponents } from "@/Pages/Categories/Partials/CategoryIcons.js";
import { ListboxOption } from "@headlessui/vue";

const emit = defineEmits<{
    created: [];
    updated: [];
    cancel: [];
    deleted: [];
}>();

const categoryStore = useCategoryStore();

const page = usePage();

const form = useForm({
    name: categoryStore.category.name,
    icon: categoryStore.category.icon,
    hex: categoryStore.category.hex,
});

const submit = (action: String) => {
    if (form.processing) {
        return;
    }

    if (action === "update") {
        form.patch(route("categories.update", categoryStore.category.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("updated");
            },
        });
    } else if (action === "create") {
        form.put(route("categories.create"), {
            preserveScroll: true,
            onSuccess: (page) => {
                // update the current expense, so the UI will switch from create to update.
                // This will avoid creating multiple expenses if the user clicks "create" again before the form closes
                categoryStore.category = page.props.flash.category;

                emit("created");
            },
        });
    } else if (action === "delete") {
        form.delete(route("categories.delete", categoryStore.category.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("deleted");
            },
        });
    }
};

const selectIcon = (icon: string, hex: string) => {
    form.icon = icon;
    form.hex = hex;
};
</script>

<template>
    <form
        class="relative flex h-full flex-col"
        @keydown.enter.prevent="submit(categoryStore.isNewCategory ? 'create' : 'update')">
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
                            data-1p-ignore />
                    </div>

                    <div
                        v-if="form.errors.name"
                        class="mt-1 text-xs text-red-500"
                        v-text="form.errors.name"></div>

                    <div
                        v-if="form.errors.limit"
                        class="mt-1 text-xs text-red-500"
                        v-text="form.errors.limit"></div>
                </div>

                <!-- Tcon -->
                <div>
                    <label
                        class="block text-sm font-medium leading-6 text-gray-900"
                        for="icon"
                        >Icon</label
                    >
                    <div class="grid grid-cols-5 gap-2 sm:grid-cols-7">
                        <button
                            v-for="icon in iconComponents()"
                            :key="icon.name"
                            @click.prevent="selectIcon(icon.name, icon.hex)">
                            <div
                                :class="[
                                    form.icon === icon.name ? 'border-indigo-500 bg-indigo-50' : '',
                                    'block inline-flex h-12 w-12 items-center justify-center rounded rounded-md border-[3px] transition',
                                ]">
                                <component
                                    :is="icon.component"
                                    :style="'color:' + icon.hex"
                                    :size="32" />
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="flex w-full space-x-3 bg-gray-100 px-6 py-4">
                <div class="relative flex flex-1 items-center space-x-px">
                    <PrimaryButton
                        @click.prevent="submit(categoryStore.isNewCategory ? 'create' : 'update')"
                        class="h-10 flex-1 rounded-md bg-indigo-400 shadow hover:bg-indigo-500 focus:bg-indigo-500"
                        :disabled="form.processing">
                        <span class="mx-auto">
                            {{ !categoryStore.isNewCategory ? "Update" : "Create" }}
                        </span>
                    </PrimaryButton>
                </div>

                <button
                    type="submit"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-white text-red-400 shadow disabled:opacity-50"
                    @click.prevent="categoryStore.isNewCategory ? emit('cancel') : submit('delete')"
                    :disabled="form.processing || !categoryStore.category.permissions.delete">
                    <IconTrash />
                </button>
            </div>
        </div>
    </form>
</template>
