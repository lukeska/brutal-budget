<script lang="ts" setup>
import { iconComponents } from "@/Pages/Categories/Partials/CategoryIcons.js";
import { ref, watch } from "vue";

let props = defineProps<{
    category: App.Data.CategoryData;
}>();

const selectedIcon = ref(iconComponents().find(({ name }) => name === props.category.icon));

watch(
    () => props.category.icon,
    (newIcon, oldIcon) => {
        selectedIcon.value = iconComponents().find(({ name }) => name === newIcon);
    },
);
</script>

<template>
    <component
        :is="selectedIcon.component"
        :size="20" />
</template>
