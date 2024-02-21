<script setup lang="ts">
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from "@headlessui/vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";
import { useProjectStore } from "@/Stores/ProjectStore";
import ProjectForm from "@/Pages/Projects/Partials/ProjectForm.vue";

const projectStore = useProjectStore();
</script>

<template>
    <TransitionRoot
        :show="projectStore.sidebarOpen"
        as="template">
        <Dialog
            as="div"
            class="relative z-50"
            @close="projectStore.sidebarOpen = false">
            <TransitionChild
                as="template"
                enter="transition-opacity ease-linear duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="transition-opacity ease-linear duration-300"
                leave-from="opacity-100"
                leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-900/80" />
            </TransitionChild>

            <div class="fixed inset-0 flex justify-end">
                <TransitionChild
                    as="template"
                    enter="transition ease-in-out duration-300 transform"
                    enter-from="translate-x-full"
                    enter-to="translate-x-0"
                    leave="transition ease-in-out duration-300 transform"
                    leave-from="translate-x-0"
                    leave-to="translate-x-full">
                    <DialogPanel class="relative ml-16 flex w-full max-w-md flex-1">
                        <!-- Sidebar component, swap this element with another sidebar if you like -->
                        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white">
                            <ProjectForm
                                @cancel="projectStore.hideSidebar()"
                                @created="projectStore.hideSidebar()"
                                @updated="projectStore.hideSidebar()"
                                @deleted="projectStore.hideSidebar()" />
                        </div>

                        <!-- Close button -->
                        <TransitionChild
                            as="template"
                            enter="ease-in-out duration-300"
                            enter-from="opacity-0"
                            enter-to="opacity-100"
                            leave="ease-in-out duration-300"
                            leave-from="opacity-100"
                            leave-to="opacity-0">
                            <div class="absolute right-full top-0 flex w-16 justify-center pt-5">
                                <button
                                    class="-m-2.5 p-2.5"
                                    type="button"
                                    @click="projectStore.hideSidebar()">
                                    <span class="sr-only">Close sidebar</span>
                                    <XMarkIcon
                                        aria-hidden="true"
                                        class="h-6 w-6 text-white" />
                                </button>
                            </div>
                        </TransitionChild>
                    </DialogPanel>
                </TransitionChild>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
