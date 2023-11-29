<script setup>
import { computed, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import CategoryLabel from "@/Pages/Categories/Partials/CategoryLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const page = usePage();

const categories = computed(() => page.props.categories);

const emit = defineEmits(["created", "updated", "cancel"]);

let props = defineProps({
    expense: {
        type: Object,
        default: () => ({
            id: null,
            amount: 0,
            date: new Date().toISOString().split("T")[0],
            notes: null,
            category_id: null,
        }),
    },
});

let form = useForm({
    amount: props.expense.amount / 100,
    date: props.expense.date,
    notes: props.expense.notes,
    category_id: props.expense.category_id,
});

watch(
    () => props.expense,
    (newExpense) => {
        form.amount = newExpense.amount / 100;
        form.date = newExpense.date;
        form.notes = newExpense.notes;
        form.category_id = newExpense.category_id;
    },
);

const submit = (action) => {
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
            <PrimaryButton
                v-text="expense.id == null ? 'Create' : 'Update'"
                @click.prevent="submit(expense.id == null ? 'create' : 'update')"></PrimaryButton>
            <SecondaryButton @click.prevent="emit('cancel')">Cancel </SecondaryButton>
        </div>
    </form>
</template>
