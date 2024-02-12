<script setup lang="ts">
import { IconCategory, IconCalendar } from "@tabler/icons-vue";

const props = defineProps<{
    expensesView: string;
}>();

const emit = defineEmits<{
    updated: [expensesView: string];
}>();
</script>

<template>
    <div class="relative flex w-20 rounded-full bg-gray-100 p-1 text-sm shadow-inner">
        <div
            :class="[
                expensesView == 'daily' ? 'translate-x-full' : '',
                'absolute h-6 w-9 transform rounded-full bg-white p-1 text-center shadow-sm transition',
            ]"></div>
        <button
            :class="[
                expensesView == 'daily' ? 'text-gray-400' : 'text-black',
                'relative inline-flex h-6 w-9 items-center justify-center p-1 transition',
            ]"
            @click.prevent="$emit('updated', 'categories')">
            <IconCategory :size="18" />
        </button>
        <button
            :class="[
                expensesView != 'daily' ? 'text-gray-400' : 'text-black',
                'relative inline-flex h-6 w-9 items-center justify-center p-1 transition',
            ]"
            @click.prevent="$emit('updated', 'daily')">
            <IconCalendar :size="18" />
        </button>
    </div>
</template>
