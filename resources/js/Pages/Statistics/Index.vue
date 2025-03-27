<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import BarsChart from "@/Pages/Dashboard/Partials/BarsChart.vue";
import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/20/solid";
import { createCurrencyFormatter } from "@/Helpers/CurrencyFormatter";
import { usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import moment from "moment";

let props = defineProps<{
    monthlyTotals: App.Data.MonthlyTotalData[];
    month: number;
    year: number;
    total: number;
}>();

const page = usePage();

const currencyFormatter = createCurrencyFormatter(page.props.currency.code);

const generateYears = () => {
    const currentDate = new Date();
    const years = [null];

    for (let year = currentDate.getFullYear() - 10; year <= currentDate.getFullYear() + 2; year++) {
        years.push(year);
    }

    return years;
};

const years = generateYears();
const selectedYear = ref(years.find((item) => item == props.year));

const yearLabel = (year: number | null) => {
    if (year === null) {
        return 'last 12 months';
    }
    return year;
};
</script>

<template>
    <AppLayout title="Statistics">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Statistics</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="divide-y md:flex md:divide-x md:divide-y-0">
                        <div class="flex items-center justify-between px-3 py-3 md:px-6 w-full">
                            <div>
                                <div class="mb-1 text-xs text-gray-500">Expenses for {{ yearLabel(props.year) }}</div>

                                <div class="mb-1 text-2xl">
                                    {{ currencyFormatter.format(total / 100) }}
                                </div>
                            </div>
                            <div>
                                <Listbox v-model="selectedYear">
                                    <div class="relative mt-1">
                                        <ListboxButton
                                            class="relative w-full cursor-default rounded-lg border bg-white py-2 pl-3 pr-10 text-left focus:outline-none focus-visible:border-indigo-500 focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-orange-300 sm:text-sm">
                                            <span class="block truncate capitalize">{{ yearLabel(selectedYear) }}</span>
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
                                                    v-for="year in years"
                                                    :key="year"
                                                    :value="year"
                                                    as="template">
                                                    <li
                                                        :class="[
                                                            active ? 'bg-indigo-50' : 'text-gray-900',
                                                            'relative cursor-default select-none py-2 pl-8 pr-4 capitalize',
                                                        ]">
                                                        <a
                                                            :href="
                                                                route('statistics.index', { year: year })
                                                            "
                                                            :class="[
                                                                selected ? 'font-medium' : 'font-normal',
                                                                'block truncate',
                                                            ]"
                                                        >{{ yearLabel(year) }}</a
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
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 bg-gray-50 gap-px border-y">
                        <div v-for="monthlyTotal in props.monthlyTotals" :key="monthlyTotal.yearMonth" class="p-4 bg-white">
                            <div class="text-xs text-gray-400 flex justify-between">
                                <div>
                                    {{ moment(monthlyTotal.yearMonth, "YYYYMM").format("MM/YY") }}
                                </div>
                                <div>
                                    {{ Math.round((monthlyTotal.total * 100) / (total / 100)) }}%
                                </div>
                            </div>
                            {{ currencyFormatter.format(monthlyTotal.total) }}
                        </div>
                    </div>
                    <div class="px-4 py-8 h-[500px]">
                        <BarsChart :monthly-totals="monthlyTotals" index-axis="y" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
