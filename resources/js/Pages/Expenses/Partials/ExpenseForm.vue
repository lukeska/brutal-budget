<script lang="ts" setup>
import { computed, ref, watchEffect } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import CategoryLabel from "@/Pages/Categories/Partials/CategoryLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useExpenseStore } from "@/Stores/ExpenseStore";
import moment from "moment";
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";
import { Switch } from "@headlessui/vue";
import { Listbox, ListboxLabel, ListboxButton, ListboxOptions, ListboxOption } from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/20/solid";
import { getCurrencySymbol } from "@/Helpers/CurrencyFormatter";
import CurrencyInput from "@/Helpers/CurrencyInput.vue";
import { IconTrash, IconChevronRight } from "@tabler/icons-vue";

const emit = defineEmits<{
    created: [];
    updated: [];
    cancel: [];
    deleted: [];
}>();

const expenseStore = useExpenseStore();

const page = usePage();

const showAdvancedOptions = ref(false);

const categories = computed(() => page.props.categories);
const projects = computed(() => {
    const originalProjects = page.props.projects || [];
    const defaultProject = { id: null, name: "None" };

    return [defaultProject, ...originalProjects];
});

const showCategoryList = ref(false);

const formatDate = (date: string): string => {
    return moment(date, "DD-MM-YYYY").format("YYYY-MM-DD");
};

let form = useForm({
    amount: expenseStore.expense.amount ? expenseStore.expense.amount / 100 : null,
    date: formatDate(expenseStore.expense.date), //: new Date().toISOString().split("T")[0],
    notes: expenseStore.expense.notes,
    category_id: expenseStore.expense.category?.id,
    project_id: expenseStore.expense.project?.id,
    is_regular: expenseStore.expense.is_regular,
});

const submit = (action: String) => {
    form.amount = form.amount * 100;

    if (action === "update") {
        form.patch(route("expenses.update", expenseStore.expense.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("updated");
            },
        });
    } else if (action === "create") {
        form.put(route("expenses.create"), {
            preserveScroll: true,
            onSuccess: () => {
                emit("created");
            },
        });
    } else if (action === "delete") {
        form.delete(route("expenses.delete", expenseStore.expense.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit("deleted");
            },
        });
    }
};

const categoryById = computed(() => {
    if (form.category_id == null) {
        return categories.value[0];
    }

    return categories.value.find((item) => item.id === form.category_id);
});

const selectCategory = (categoryId: number) => {
    form.category_id = categoryId;
    showCategoryList.value = false;
};

const selectedProject = ref(projects.value.find((item) => item.id === form.project_id));

watchEffect(() => {
    form.project_id = selectedProject.value?.id || null;
});
</script>

<template>
    <form
        class="relative flex h-full flex-col"
        @keydown.enter="submit(expenseStore.isNewExpense ? 'create' : 'update')">
        <div class="flex-1 overflow-x-hidden overflow-y-scroll px-6 py-4">
            <div class="flex flex-col gap-y-6">
                <!-- Amount -->
                <div class="order-2">
                    <div class="flex items-center space-x-2">
                        <div class="relative flex-1 rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 sm:text-sm">
                                    {{ getCurrencySymbol(page.props.auth.user.currency) }}
                                </span>
                            </div>
                            <CurrencyInput
                                id="amount"
                                class="block h-10 w-full rounded-md border-0 py-1.5 pl-7 pr-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                name="amount"
                                v-model="form.amount"
                                placeholder="0.00"
                                :options="{ currency: page.props.auth.user.currency, currencyDisplay: 'hidden' }" />
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <span
                                    id="amount-currency"
                                    class="text-gray-500 sm:text-sm"
                                    >{{ page.props.auth.user.currency }}</span
                                >
                            </div>
                        </div>

                        <button
                            type="button"
                            @click.prevent="showCategoryList = !showCategoryList"
                            class="flex h-10 w-10 items-center justify-center rounded p-1.5 text-white"
                            :style="'background-color:' + categoryById.hex">
                            <CategoryIcon :category="categoryById" />
                        </button>
                    </div>
                    <div
                        v-if="form.errors.amount"
                        class="mt-1 text-xs text-red-500"
                        v-text="form.errors.amount"></div>

                    <!-- Category list -->
                    <div
                        class="mt-6"
                        v-if="showCategoryList">
                        <div
                            v-if="form.errors.category_id"
                            class="mb-1 text-xs text-red-500"
                            v-text="form.errors.category_id"></div>
                        <div class="grid grid-cols-3 gap-3">
                            <template
                                v-for="category in categories"
                                :key="category.id">
                                <button
                                    class="inline-block rounded text-sm"
                                    @click.prevent="selectCategory(category.id)">
                                    <CategoryLabel :category="category" />
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Date -->
                <div class="order-1">
                    <div class="mx-auto w-36">
                        <input
                            id="date"
                            v-model="form.date"
                            class="block w-full rounded-md border-0 py-1.5 text-sm text-gray-900 ring-inset ring-gray-300 placeholder:text-gray-400 hover:shadow-sm hover:ring-1 focus:shadow-sm focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:leading-6"
                            name="date"
                            type="date" />
                    </div>
                    <div
                        v-if="form.errors.date"
                        class="mt-1 text-xs text-red-500"
                        v-text="form.errors.date"></div>
                </div>

                <!-- Notes -->
                <div class="order-3">
                    <label
                        class="block text-sm font-medium leading-6 text-gray-900"
                        for="notes"
                        >Notes</label
                    >
                    <div class="mt-2">
                        <input
                            id="notes"
                            v-model="form.notes"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            name="notes"
                            type="text" />
                    </div>
                </div>

                <!-- Advanced options -->
                <div class="order-4">
                    <button
                        class="flex items-center space-x-px text-sm text-indigo-500"
                        @click.prevent="showAdvancedOptions = !showAdvancedOptions">
                        <span>Advanced options</span>
                        <IconChevronRight
                            :class="{ 'rotate-90': showAdvancedOptions }"
                            class="inline transition"
                            :size="16" />
                    </button>

                    <div
                        v-if="showAdvancedOptions"
                        class="mt-4 flex flex-col gap-y-8">
                        <div class="">
                            <div class="mb-2 text-sm font-medium leading-6 text-gray-900">
                                Is this part of a project?
                            </div>
                            <Listbox v-model="selectedProject">
                                <div class="relative mt-1">
                                    <ListboxButton
                                        class="relative w-full cursor-default rounded-lg border bg-white py-2 pl-3 pr-10 text-left shadow-md focus:outline-none focus-visible:border-indigo-500 focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-orange-300 sm:text-sm">
                                        <span class="block truncate">{{
                                            selectedProject ? selectedProject.name : "Pick a project"
                                        }}</span>
                                        <span
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                            <ChevronUpDownIcon
                                                class="h-5 w-5 text-gray-400"
                                                aria-hidden="true" />
                                        </span>
                                    </ListboxButton>

                                    <transition
                                        leave-active-class="transition duration-100 ease-in"
                                        leave-from-class="opacity-100"
                                        leave-to-class="opacity-0">
                                        <ListboxOptions
                                            class="absolute z-20 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm">
                                            <ListboxOption
                                                v-slot="{ active, selected }"
                                                v-for="project in projects"
                                                :key="project.name"
                                                :value="project"
                                                as="template">
                                                <li
                                                    :class="[
                                                        active ? 'bg-amber-100 text-amber-900' : 'text-gray-900',
                                                        'relative cursor-default select-none py-2 pl-10 pr-4',
                                                    ]">
                                                    <span
                                                        :class="[
                                                            selected ? 'font-medium' : 'font-normal',
                                                            'block truncate',
                                                        ]"
                                                        >{{ project.name }}</span
                                                    >
                                                    <span
                                                        v-if="selected"
                                                        class="absolute inset-y-0 left-0 flex items-center pl-3 text-amber-600">
                                                        <CheckIcon
                                                            class="h-5 w-5"
                                                            aria-hidden="true" />
                                                    </span>
                                                </li>
                                            </ListboxOption>
                                        </ListboxOptions>
                                    </transition>
                                </div>
                            </Listbox>
                        </div>

                        <div>
                            <div class="flex items-center space-x-2">
                                <Switch
                                    v-model="form.is_regular"
                                    :class="form.is_regular ? 'bg-indigo-400' : 'bg-gray-400'"
                                    class="relative inline-flex h-[28px] w-[64px] shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75">
                                    <span class="sr-only">Is regular</span>
                                    <span
                                        aria-hidden="true"
                                        :class="form.is_regular ? 'translate-x-9' : 'translate-x-0'"
                                        class="pointer-events-none inline-block h-[24px] w-[24px] transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out" />
                                </Switch>
                                <div>This is a regular expense</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex w-full space-x-3 bg-gray-100 px-6 py-4">
            <PrimaryButton
                type="submit"
                @click.prevent="submit(expenseStore.isNewExpense ? 'create' : 'update')"
                class="h-10 flex-1 bg-indigo-400 shadow hover:bg-indigo-500 focus:bg-indigo-500">
                <span class="mx-auto">{{ expenseStore.isNewExpense ? "Create" : "Update" }}</span>
            </PrimaryButton>

            <button
                type="submit"
                class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-white text-red-400 shadow"
                @click.prevent="expenseStore.isNewExpense ? emit('cancel') : submit('delete')">
                <IconTrash />
            </button>
        </div>
    </form>
</template>
