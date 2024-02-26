<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { computed, ref } from "vue";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { Link, usePage } from "@inertiajs/vue3";
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";
import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/20/solid";
import NoExpenses from "@/Pages/Expenses/Partials/NoExpenses.vue";
import DonutChart from "@/Pages/Dashboard/Partials/DonutChart.vue";
import BarsChart from "@/Pages/Dashboard/Partials/BarsChart.vue";
import Onboarding from "@/Pages/Dashboard/Partials/Onboarding.vue";

let props = defineProps<{
    monthlyTotals: App.Data.MonthlyTotalData[];
    month: number;
    year: number;
}>();

const page = usePage();

const currencyFormatter = createCurrencyFormatter(page.props.auth.user.currency);

const currentMonthlyTotal = computed(() => {
    return props.monthlyTotals.find((item) => item.isCurrent === true);
});

const expensesAvailable = computed(() => {
    return currentMonthlyTotal.value.total > 0;
});

const generateMonthYear = () => {
    const currentDate = new Date();
    const twelveMonthsAgo = new Date(currentDate);
    twelveMonthsAgo.setMonth(currentDate.getMonth() - 12);
    const twelveMonthsInTheFuture = new Date(currentDate);
    twelveMonthsInTheFuture.setMonth(currentDate.getMonth() + 12);

    const months = [];
    const options = { month: "short", year: "numeric" };

    for (let date = new Date(twelveMonthsAgo); date <= twelveMonthsInTheFuture; date.setMonth(date.getMonth() + 1)) {
        months.push({
            month: date.getMonth() + 1,
            year: date.getFullYear(),
            label: new Intl.DateTimeFormat("en-US", options).format(date),
        });
    }

    return months;
};

const monthYears = generateMonthYear();
const selectedMonthYear = ref(monthYears.find((item) => item.month === props.month && item.year === props.year));
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
            <div class="text-sm text-gray-400">{{ $page.props.auth.user.current_team.name }}</div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Onboarding />
                </div>

                <div class="divide-y overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="divide-y md:flex md:divide-x md:divide-y-0">
                        <div class="md:w-1/2">
                            <div class="flex items-center justify-between px-3 py-3 md:px-6">
                                <div class="">
                                    <div class="mb-1 text-xs text-gray-500">Expenses so far</div>

                                    <div class="mb-1 text-2xl">
                                        {{ currencyFormatter.format(currentMonthlyTotal.total) }}
                                    </div>
                                    <Link
                                        :href="route('expenses.index')"
                                        class="text-sm text-indigo-500">
                                        See all &rarr;
                                    </Link>
                                </div>
                                <div>
                                    <Listbox v-model="selectedMonthYear">
                                        <div class="relative mt-1">
                                            <ListboxButton
                                                class="relative w-full cursor-default rounded-lg border bg-white py-2 pl-3 pr-10 text-left focus:outline-none focus-visible:border-indigo-500 focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-orange-300 sm:text-sm">
                                                <span class="block truncate">{{ selectedMonthYear.label }}</span>
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
                                                    class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm">
                                                    <ListboxOption
                                                        v-slot="{ active, selected }"
                                                        v-for="monthYear in monthYears"
                                                        :key="monthYear.label"
                                                        :value="monthYear"
                                                        as="template">
                                                        <li
                                                            :class="[
                                                                active ? 'bg-indigo-50' : 'text-gray-900',
                                                                'relative cursor-default select-none py-2 pl-8 pr-4',
                                                            ]">
                                                            <a
                                                                :href="
                                                                    route('dashboard', {
                                                                        year: monthYear.year,
                                                                        month:
                                                                            monthYear.month < 10
                                                                                ? '0' + monthYear.month
                                                                                : monthYear.month,
                                                                    })
                                                                "
                                                                :class="[
                                                                    selected ? 'font-medium' : 'font-normal',
                                                                    'block truncate',
                                                                ]"
                                                                >{{ monthYear.label }}</a
                                                            >
                                                            <span
                                                                v-if="selected"
                                                                class="absolute inset-y-0 left-0 flex items-center pl-2 text-indigo-600">
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
                            </div>
                        </div>
                        <div class="md:w-1/2">
                            <div
                                v-if="expensesAvailable"
                                class="px-3 py-3 md:px-6">
                                <div class="mb-1 text-xs text-gray-500">Top categories</div>
                                <div class="flex w-full">
                                    <template
                                        v-for="monthlyTotal in currentMonthlyTotal.categoryMonthlyTotals.slice(0, 3)"
                                        :key="monthlyTotal.id">
                                        <div class="w-1/3">
                                            <div class="text-xl">
                                                {{ currencyFormatter.format(monthlyTotal.amount) }}
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <span :style="'color:' + monthlyTotal.category.hex">
                                                    <CategoryIcon :category="monthlyTotal.category" />
                                                </span>
                                                <span class="text-sm text-gray-700">{{
                                                    monthlyTotal.category.name
                                                }}</span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div
                            v-if="expensesAvailable"
                            class="space-y-6 px-4 py-8 lg:flex lg:items-center lg:gap-x-4 lg:space-y-0">
                            <div class="lg:w-1/2">
                                <DonutChart :monthly-totals="monthlyTotals" />
                            </div>
                            <div class="min-h-[600px] lg:w-1/2">
                                <BarsChart :monthly-totals="monthlyTotals" />
                            </div>
                        </div>
                        <div v-else>
                            <NoExpenses />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
