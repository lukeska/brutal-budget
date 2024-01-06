<script setup lang="ts">
import { ref, computed } from "vue";
import { Link } from "@inertiajs/vue3";
import { IconCategory, IconCalendar } from "@tabler/icons-vue";

const props = defineProps<{
    expensesView: string;
}>();

const isDailyView = ref(props.expensesView === "daily");

const getCategoriesViewUrl = computed(() => {
    return route("expenses.index", {
        year: route().params.year,
        month: route().params.month,
        type: route().params.type,
        view: null,
    });
});

const getDailyViewUrl = computed(() => {
    return route("expenses.index", {
        year: route().params.year,
        month: route().params.month,
        type: route().params.type,
        view: "daily",
    });
});
</script>

<template>
    <div class="relative flex w-20 rounded-full bg-gray-100 p-1 text-sm shadow-inner">
        <div
            :class="[
                isDailyView ? 'translate-x-full' : '',
                'absolute h-6 w-9 transform rounded-full bg-white p-1 text-center shadow-sm transition',
            ]"></div>
        <Link
            :class="[
                isDailyView ? 'text-gray-400' : 'text-black',
                'relative inline-flex h-6 w-9 items-center justify-center p-1 transition',
            ]"
            :href="getCategoriesViewUrl"
            @click="isDailyView = false">
            <IconCategory :size="18" />
        </Link>
        <Link
            :class="[
                !isDailyView ? 'text-gray-400' : 'text-black',
                'relative inline-flex h-6 w-9 items-center justify-center p-1 transition',
            ]"
            :href="getDailyViewUrl"
            @click="isDailyView = true">
            <IconCalendar :size="18" />
        </Link>
    </div>
</template>
