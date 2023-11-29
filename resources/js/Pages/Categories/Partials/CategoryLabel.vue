<script setup>
import { iconComponents } from "@/Pages/Categories/Partials/CategoryIcons.js";
import { ref } from "vue";

let props = defineProps({
    category: {
        type: Object,
    },
});

const selectedIcon = ref(iconComponents().find(({ name }) => name === props.category.icon));
</script>

<template>
    <div
        :style="'background-color:' + category.hex"
        class="inline-flex items-center space-x-1.5 rounded px-2 py-1.5 text-white">
        <component
            :is="selectedIcon.component"
            :size="20" />
        <span>{{ category.name }}</span>
    </div>
</template>
