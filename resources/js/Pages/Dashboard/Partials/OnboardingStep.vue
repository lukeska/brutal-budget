<script setup lang="ts">
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import { IconCheck } from "@tabler/icons-vue";
import { computed } from "vue";

let props = defineProps<{
    onboardingStatus: App.Data.OnboardingStatusData;
}>();

const form = useForm({
    id: props.onboardingStatus.id,
});

const skip = () => {
    form.patch(route("onboarding-status.skip", form.id), {
        preserveScroll: true,
        onSuccess: () => {
            //emit("skipped");
        },
    });
};

const done = computed((): boolean => {
    return props.onboardingStatus.skipped_at !== null || props.onboardingStatus.completed_at !== null;
});
</script>

<template>
    <div class="flex space-x-5">
        <div
            :class="[
                done ? 'bg-indigo-500' : 'bg-gray-400',
                'flex h-12 w-12 items-center justify-center rounded-full  text-lg font-semibold text-white',
            ]">
            <IconCheck v-if="done" />
            <slot
                v-else
                name="index"
                >1</slot
            >
        </div>
        <div>
            <div class="mb-3 text-xl font-semibold">
                <slot name="title"></slot>
            </div>
            <div
                class="flex space-x-4"
                v-if="!done">
                <slot name="action"></slot>
                <SecondaryButton @click.prevent="skip">Skip </SecondaryButton>
            </div>
        </div>
    </div>
</template>
