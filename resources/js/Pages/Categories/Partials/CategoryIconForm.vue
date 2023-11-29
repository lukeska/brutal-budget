<script setup>
import { iconComponents } from "@/Pages/Categories/Partials/CategoryIcons.js";
import { Listbox, ListboxLabel, ListboxButton, ListboxOptions, ListboxOption } from "@headlessui/vue";
import { ref } from "vue";

let props = defineProps({
    icon: String,
});

const emit = defineEmits(["updated"]);

const selectedIcon = ref(iconComponents().find(({ name }) => name === props.icon));
</script>

<template>
    <div>
        <Listbox v-model="selectedIcon">
            <div class="relative mt-1">
                <ListboxButton
                    class="relative z-10 w-full cursor-pointer rounded-md bg-white p-1 text-left shadow-md focus:outline-none focus-visible:border-indigo-500 focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-orange-300 sm:text-sm">
                    <component
                        :is="selectedIcon.component"
                        :size="32" />
                </ListboxButton>

                <transition
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0">
                    <ListboxOptions
                        class="absolute right-full top-0 z-20 mr-2 grid w-52 grid-cols-6 gap-1 rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm">
                        <ListboxOption
                            v-for="icon in iconComponents()"
                            :key="icon.name"
                            v-slot="{ active, selected }"
                            :value="icon"
                            as="template"
                            @click="
                                emit('updated', {
                                    name: 'icon',
                                    value: icon.name,
                                })
                            ">
                            <li>
                                <div
                                    :class="[
                                        active ? 'scale-125 ring-2 ring-white' : '',
                                        'block h-8 w-8 rounded-md transition ',
                                    ]">
                                    <component
                                        :is="icon.component"
                                        :size="32" />
                                </div>
                            </li>
                        </ListboxOption>
                    </ListboxOptions>
                </transition>
            </div>
        </Listbox>
    </div>
</template>
