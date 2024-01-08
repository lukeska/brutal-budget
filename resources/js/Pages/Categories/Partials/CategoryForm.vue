<script lang="ts" setup>
import { ref, nextTick } from "vue";
import { useForm } from "@inertiajs/vue3";
import CategoryColorForm from "@/Pages/Categories/Partials/CategoryColorForm.vue";
import CategoryIconForm from "@/Pages/Categories/Partials/CategoryIconForm.vue";
import DangerButton from "@/Components/DangerButton.vue";

let props = withDefaults(
    defineProps<{
        category?: App.Data.CategoryData;
    }>(),
    {
        category: {
            id: null,
            name: "",
            icon: "IconReceipt2",
            hex: "#fca5a5",
        },
    },
);

const emit = defineEmits(["created", "cancel"]);

let form = useForm({
    name: props.category.name,
    icon: props.category.icon,
    hex: props.category.hex,
});

const nameField = ref();
let showForm = ref(props.category.id == null);
let showCreateSuccessMessage = ref(false);

const submit = (action: String) => {
    if (action === "update") {
        if (form.name === props.category.name) {
            showForm.value = false;
            return;
        }

        form.patch(route("categories.update", props.category.id), {
            preserveScroll: true,
            onSuccess: () => clearForm(),
        });
    } else if (action === "create") {
        form.put(route("categories.create"), {
            preserveScroll: true,
            onSuccess: () => {
                form.name = props.category.name;
                emit("created");
                showCreateSuccessMessage.value = true;
                setTimeout(function () {
                    showCreateSuccessMessage.value = false;
                }, 3000);
            },
        });
    } else if (action === "delete") {
        form.delete(route("categories.delete", props.category.id), {
            preserveScroll: true,
        });
    }
};

const initAndShowForm = () => {
    showForm.value = true;

    form.name = props.category.name;

    form.clearErrors();

    nextTick(() => {
        nameField.value.focus();
    });
};

const clearForm = () => {
    showForm.value = false;
};

const cancelEdit = () => {
    showForm.value = false;

    emit("cancel");
};

const updateIcon = (params) => {
    form.icon = params.icon;
    form.hex = params.hex;

    if (props.category.id === null) {
        return;
    }

    form.post(route("categories.update-icon", props.category.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="flex items-center justify-between gap-x-6 py-5">
        <div class="flex flex-1 items-start gap-x-3">
            <button
                v-show="!showForm"
                class="block max-w-sm flex-1 cursor-text rounded-md border-0 px-3 py-1.5 text-left text-sm font-semibold leading-6 text-gray-900 ring-0 ring-inset ring-gray-100 hover:ring-1"
                @click.prevent="initAndShowForm()">
                {{ category.name }}
            </button>
            <form
                v-show="showForm"
                class="flex-1"
                @submit.prevent="submit">
                <div class="relative flex max-w-sm items-center space-x-2">
                    <div class="w-full">
                        <label
                            class="sr-only"
                            for="name"
                            >Name</label
                        >
                        <input
                            id="name"
                            ref="nameField"
                            v-model="form.name"
                            class="block w-full rounded-md border-0 py-1.5 pr-36 text-sm font-semibold leading-6 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="name"
                            data-1p-ignore
                            placeholder="Bills, House, Gym, etc."
                            type="text" />
                    </div>

                    <div class="absolute right-1 top-1 space-x-2">
                        <button
                            class="rounded bg-indigo-600 px-2 py-1 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                            type="submit"
                            @click="submit(props.category.id === null ? 'create' : 'update')">
                            {{ props.category.id === null ? "Create" : "Update" }}
                        </button>
                        <button
                            class="rounded bg-white px-2 py-1 text-xs font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                            type="button"
                            @click.prevent="cancelEdit()">
                            Cancel
                        </button>
                    </div>
                </div>

                <div
                    v-if="form.errors.name"
                    class="mt-1 text-xs text-red-500"
                    v-text="form.errors.name"></div>

                <div
                    v-if="showCreateSuccessMessage"
                    class="mt-1 text-xs text-green-500">
                    Category created!
                </div>
            </form>
        </div>

        <div>
            <CategoryIconForm
                :icon="category.icon"
                @updated="updateIcon" />
        </div>

        <div v-if="props.category.id !== null">
            <DangerButton
                @click="submit('delete')"
                :disabled="!category.permissions.delete"
                >Delete
            </DangerButton>
        </div>
    </div>
</template>
