<script lang="ts" setup>
import { iconComponents } from "@/Pages/Categories/Partials/CategoryIcons.js";
import { ref } from "vue";

let props = defineProps<{
    category: App.Data.CategoryData;
}>();

const selectedIcon = ref(iconComponents().find(({ name }) => name === props.category.icon));
</script>

<template>
    <div
        :style="'background-color:' + category.hex"
        class="rounded px-2 py-1.5 text-white">
        <div class="mx-auto w-8">
            <component
                :is="selectedIcon.component"
                :size="32" />
        </div>
        <div class="overflow-hidden text-ellipsis whitespace-nowrap">{{ category.name }}</div>
    </div>
</template>
