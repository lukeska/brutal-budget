<script lang="ts" setup>
import Chart from "chart.js/auto";

import AppLayout from "@/Layouts/AppLayout.vue";
import { computed, onMounted, ref } from "vue";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { Link, usePage } from "@inertiajs/vue3";
import moment from "moment";
import CategoryIcon from "@/Pages/Categories/Partials/CategoryIcon.vue";
import { IconChartDonut4, IconChartDonutFilled } from "@tabler/icons-vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useExpenseStore } from "@/Stores/ExpenseStore";
import { Listbox, ListboxButton, ListboxOptions, ListboxOption } from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/20/solid";

let props = defineProps<{
    monthlyTotals: App.Data.MonthlyTotalData[];
    month: number;
    year: number;
}>();

const donut = ref();
const bars = ref();

const page = usePage();

const expenseStore = useExpenseStore();

const currencyFormatter = createCurrencyFormatter(page.props.auth.user.currency);

const initDonut = () => {
    const currentMonthlyTotals = props.monthlyTotals.find((item) => item.isCurrent === true);
    const labels = currentMonthlyTotals.categoryMonthlyTotals.map((entry) => entry.category.name);
    const dataValues = currentMonthlyTotals.categoryMonthlyTotals.map((entry) => entry.amount);
    const backgroundColors = currentMonthlyTotals.categoryMonthlyTotals.map((entry) => entry.category.hex);

    new Chart(donut.value, {
        type: "doughnut",
        data: {
            labels: labels,
            datasets: [
                {
                    data: dataValues,
                    backgroundColor: backgroundColors,
                    hoverOffset: 4,
                },
            ],
        },
        options: {
            spacing: 3,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return currencyFormatter.format(context.formattedValue);
                        },
                    },
                },
                legend: {
                    position: "bottom",
                },
            },
        },
    });
};

const initBars = () => {
    const monthlyTotals = props.monthlyTotals;

    const allCategoriesSet = new Set();
    monthlyTotals.forEach((entry) => {
        entry.categoryMonthlyTotals.forEach((category) => {
            const { name, hex } = category.category;
            allCategoriesSet.add(JSON.stringify({ name, hex }));
        });
    });

    const allCategories = Array.from(allCategoriesSet).map((category) => JSON.parse(category));

    const labels = monthlyTotals.map((entry) => moment(entry.yearMonth, "YYYYMM").format("MM/YY"));
    const datasets = monthlyTotals.map((entry) =>
        entry.categoryMonthlyTotals.reduce((acc, category) => {
            acc[category.category.name] = category.amount;
            return acc;
        }, {}),
    );

    const data = {
        labels: labels,
        datasets: allCategories.map((category) => ({
            label: category.name,
            data: datasets.map((data) => data[category.name] || 0),
            backgroundColor: category.hex,
            stack: "Stack 0",
        })),
    };

    new Chart(bars.value, {
        type: "bar",
        data: data,
        options: {
            maintainAspectRatio: false,
            //indexAxis: "y",
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                    ticks: {
                        callback: function (value, index, values) {
                            return currencyFormatter.format(value);
                        },
                    },
                },
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return context.dataset.label + ": " + currencyFormatter.format(context.formattedValue);
                        },
                    },
                },
                legend: {
                    display: false,
                    //position: "bottom",
                },
            },
        },
    });
};

onMounted(() => {
    initDonut();
    initBars();
});

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
                                <div class="mx-auto">
                                    <canvas ref="donut"></canvas>
                                </div>
                            </div>
                            <div class="min-h-[600px] lg:w-1/2">
                                <canvas ref="bars"></canvas>
                            </div>
                        </div>
                        <div
                            v-else
                            class="grid w-full grid-cols-1 grid-rows-1 place-items-center content-center px-4 py-10">
                            <IconChartDonut4
                                :size="400"
                                class="col-[1] row-[1] text-gray-50" />
                            <div class="col-[1] row-[1] max-w-[400px] text-center">
                                <p class="mb-3">
                                    No expenses yet. Go ahead, click the button below and try the thrill of logging your
                                    first expense.
                                </p>
                                <PrimaryButton @click.prevent="expenseStore.showSidebar()">+ expense</PrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
