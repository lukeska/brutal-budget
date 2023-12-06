<script lang="ts" setup>
import { computed, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import CategoryLabel from "@/Pages/Categories/Partials/CategoryLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { route, current } from "momentum-trail";

let props = defineProps<{
    expense?: App.Data.ExpenseData;
}>();

const emit = defineEmits<{
    created: [];
    updated: [];
    cancel: [];
}>();

const page = usePage();

const categories = computed(() => page.props.categories);

let form = useForm({
    amount: props.expense ? props.expense.amount / 100 : 0,
    date: props.expense ? props.expense.date : new Date().toISOString().split("T")[0],
    notes: props.expense ? props.expense.notes : null,
    category_id: props.expense ? props.expense.category?.id : null,
});

watch(
    () => props.expense,
    (newExpense) => {
        form.clearErrors();
        form.amount = newExpense ? newExpense.amount / 100 : 0;
        form.date = newExpense ? newExpense.date : new Date().toISOString().split("T")[0];
        form.notes = newExpense ? newExpense.notes : null;
        form.category_id = newExpense ? newExpense.category?.id : null;
    },
);

const submit = (action: String) => {
    if (action === "update") {
        form.patch(route("expenses.update", props.expense.id), {
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
    }
};
</script>

<template>
    <form
        action=""
        class="space-y-6">
        <div>
            <label
                class="block text-sm font-medium leading-6 text-gray-900"
                for="amount"
                >Amount</label
            >
            <div class="relative mt-2 rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="text-gray-500 sm:text-sm">€</span>
                </div>
                <input
                    id="amount"
                    v-model="form.amount"
                    aria-describedby="amount-currency"
                    class="block w-full rounded-md border-0 py-1.5 pl-7 pr-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    name="amount"
                    placeholder="0.00"
                    type="text" />
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <span
                        id="amount-currency"
                        class="text-gray-500 sm:text-sm"
                        >EUR</span
                    >
                </div>
            </div>

            <div
                v-if="form.errors.amount"
                class="mt-1 text-xs text-red-500"
                v-text="form.errors.amount"></div>
        </div>

        <div>
            <label
                class="block text-sm font-medium leading-6 text-gray-900"
                for="date"
                >Date</label
            >
            <div class="mt-2">
                <input
                    id="date"
                    v-model="form.date"
                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    name="date"
                    type="date" />
            </div>
            <div
                v-if="form.errors.date"
                class="mt-1 text-xs text-red-500"
                v-text="form.errors.date"></div>
        </div>

        <div>
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

        <div>
            <div
                v-if="form.errors.category_id"
                class="mb-1 text-xs text-red-500"
                v-text="form.errors.category_id"></div>
            <div class="flex flex-wrap gap-2">
                <template
                    v-for="category in categories"
                    :key="category.id">
                    <button
                        :class="[
                            form.category_id === category.id ? 'ring-2 ring-black ring-offset-2' : '',
                            'inline-block rounded',
                        ]"
                        @click.prevent="form.category_id = category.id">
                        <CategoryLabel :category="category" />
                    </button>
                </template>
            </div>
        </div>

        <div class="space-x-4">
            <PrimaryButton @click.prevent="submit(expense == null ? 'create' : 'update')">
                {{ expense == null ? "Create" : "Update" }}
            </PrimaryButton>
            <SecondaryButton @click.prevent="emit('cancel')">Cancel</SecondaryButton>
        </div>
    </form>
</template>
