<script lang="ts" setup>
import { ref } from "vue";
import { Listbox, ListboxLabel, ListboxButton, ListboxOptions, ListboxOption } from "@headlessui/vue";
import resolveConfig from "tailwindcss/resolveConfig";
import tailwindConfig from "../../../../../tailwind.config.js";

let props = defineProps({
    hex: String,
});

const emit = defineEmits(["updated"]);

const fullConfig = resolveConfig(tailwindConfig);

const colors = [
    { hex: fullConfig.theme.colors.red["300"] },
    { hex: fullConfig.theme.colors.red["500"] },
    { hex: fullConfig.theme.colors.red["700"] },
    { hex: fullConfig.theme.colors.orange["300"] },
    { hex: fullConfig.theme.colors.orange["500"] },
    { hex: fullConfig.theme.colors.orange["700"] },
    { hex: fullConfig.theme.colors.amber["300"] },
    { hex: fullConfig.theme.colors.amber["500"] },
    { hex: fullConfig.theme.colors.amber["700"] },
    { hex: fullConfig.theme.colors.yellow["300"] },
    { hex: fullConfig.theme.colors.yellow["500"] },
    { hex: fullConfig.theme.colors.yellow["700"] },
    { hex: fullConfig.theme.colors.lime["300"] },
    { hex: fullConfig.theme.colors.lime["500"] },
    { hex: fullConfig.theme.colors.lime["700"] },
    { hex: fullConfig.theme.colors.green["300"] },
    { hex: fullConfig.theme.colors.green["500"] },
    { hex: fullConfig.theme.colors.green["700"] },
    { hex: fullConfig.theme.colors.emerald["300"] },
    { hex: fullConfig.theme.colors.emerald["500"] },
    { hex: fullConfig.theme.colors.emerald["700"] },
    { hex: fullConfig.theme.colors.teal["300"] },
    { hex: fullConfig.theme.colors.teal["500"] },
    { hex: fullConfig.theme.colors.teal["700"] },
    { hex: fullConfig.theme.colors.cyan["300"] },
    { hex: fullConfig.theme.colors.cyan["500"] },
    { hex: fullConfig.theme.colors.cyan["700"] },
    { hex: fullConfig.theme.colors.sky["300"] },
    { hex: fullConfig.theme.colors.sky["500"] },
    { hex: fullConfig.theme.colors.sky["700"] },
    { hex: fullConfig.theme.colors.blue["300"] },
    { hex: fullConfig.theme.colors.blue["500"] },
    { hex: fullConfig.theme.colors.blue["700"] },
    { hex: fullConfig.theme.colors.indigo["300"] },
    { hex: fullConfig.theme.colors.indigo["500"] },
    { hex: fullConfig.theme.colors.indigo["700"] },
    { hex: fullConfig.theme.colors.violet["300"] },
    { hex: fullConfig.theme.colors.violet["500"] },
    { hex: fullConfig.theme.colors.violet["700"] },
    { hex: fullConfig.theme.colors.purple["300"] },
    { hex: fullConfig.theme.colors.purple["500"] },
    { hex: fullConfig.theme.colors.purple["700"] },
    { hex: fullConfig.theme.colors.fuchsia["300"] },
    { hex: fullConfig.theme.colors.fuchsia["500"] },
    { hex: fullConfig.theme.colors.fuchsia["700"] },
    { hex: fullConfig.theme.colors.pink["300"] },
    { hex: fullConfig.theme.colors.pink["500"] },
    { hex: fullConfig.theme.colors.pink["700"] },
    { hex: fullConfig.theme.colors.rose["300"] },
    { hex: fullConfig.theme.colors.rose["500"] },
    { hex: fullConfig.theme.colors.rose["700"] },
];
const selectedColor = ref(colors.find(({ hex }) => hex === props.hex));
</script>

<template>
    <div>
        <Listbox v-model="selectedColor">
            <div class="relative mt-1">
                <ListboxButton
                    class="relative z-10 w-full cursor-pointer rounded-md bg-white p-1 text-left shadow-md focus:outline-none focus-visible:border-indigo-500 focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-orange-300 sm:text-sm">
                    <span
                        :style="'background-color:' + selectedColor.hex"
                        class="block h-8 w-8 rounded-md"></span>
                </ListboxButton>

                <transition
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0">
                    <ListboxOptions
                        class="absolute right-full top-0 z-20 mr-2 grid w-52 grid-cols-6 gap-1 rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm">
                        <ListboxOption
                            v-for="color in colors"
                            :key="color.hex"
                            v-slot="{ active, selected }"
                            :value="color"
                            as="template"
                            @click="
                                emit('updated', {
                                    name: 'hex',
                                    value: color.hex,
                                })
                            ">
                            <li>
                                <span
                                    :class="[
                                        active ? 'scale-125 ring-2 ring-white' : '',
                                        'block h-8 w-8 rounded-md transition ',
                                    ]"
                                    :style="'background-color:' + color.hex"></span>
                            </li>
                        </ListboxOption>
                    </ListboxOptions>
                </transition>
            </div>
        </Listbox>
    </div>
</template>
