<script setup lang="ts">
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { useForm } from "@inertiajs/vue3";

let props = defineProps<{
    onboardingStatus: App.Data.OnboardingStatusData;
}>();

const stepsData = [
    {
        step: "expense-created",
        title: "Expense Created",
        description: "Description for Expense Created",
    },
    {
        step: "team-member-invited",
        title: "Team Member Invited",
        description: "Description for Team Member Invited",
    },
    {
        step: "project-created",
        title: "Project Created",
        description: "Description for Project Created",
    },
];

const form = useForm({
    id: props.onboardingStatus.id,
});

const skip = () => {
    form.patch(route("onboarding-status.skip", form.id), {
        preserveScroll: true,
        onSuccess: () => {
            emit("skipped");
        },
    });
};

const getTitle = () => {
    const stepObj = stepsData.find((item) => item.step === props.onboardingStatus.onboarding_step);
    return stepObj ? stepObj.title : "";
};

const getDescription = () => {
    const stepObj = stepsData.find((item) => item.step === props.onboardingStatus.onboarding_step);
    return stepObj ? stepObj.description : "";
};
</script>

<template>
    <div>
        <div class="mb-3 text-xl font-semibold">
            <slot name="title"></slot>
        </div>
        <div class="flex space-x-4">
            <slot name="action"></slot>
            <SecondaryButton
                @click.prevent="skip"
                v-if="!onboardingStatus.skipped_at"
                >Skip</SecondaryButton
            >
        </div>
    </div>
</template>
