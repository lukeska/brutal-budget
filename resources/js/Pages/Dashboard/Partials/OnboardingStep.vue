<script setup lang="ts">
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
        <div>
            {{ getTitle() }}
        </div>
        <div>
            {{ getDescription() }}
        </div>
    </div>
</template>
