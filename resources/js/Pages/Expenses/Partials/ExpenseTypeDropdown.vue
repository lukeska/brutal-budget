<script setup lang="ts">
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
</script>

<template>
    <Dropdown
        align="right"
        width="60">
        <template #trigger>
            <span class="inline-flex rounded-md">
                <button
                    class="inline-flex items-center rounded-md border border-gray-200 bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:bg-gray-50 focus:outline-none active:bg-gray-50"
                    type="button">
                    <span v-if="route().current('expenses.index', { type: 'regular' })">Only regular expenses</span>
                    <span v-else-if="route().current('expenses.index', { type: 'non-regular' })"
                        >Only regular expenses</span
                    >
                    <span v-else="route().current('expenses.index')">All expenses</span>
                    <svg
                        class="-me-0.5 ms-2 h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"
                            stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            </span>
        </template>

        <template #content>
            <div class="w-60">
                <div class="block px-4 py-2 text-xs text-gray-400">Expenses type</div>

                <DropdownLink :href="route('expenses.index')">All expenses</DropdownLink>

                <DropdownLink :href="route('expenses.index', { type: 'regular' })">Only regular expenses</DropdownLink>

                <DropdownLink :href="route('expenses.index', { type: 'non-regular' })"
                    >Only non-regular expenses</DropdownLink
                >
            </div>
        </template>
    </Dropdown>
</template>
