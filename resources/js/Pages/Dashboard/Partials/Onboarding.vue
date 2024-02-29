<script setup lang="ts">
import OnboardingStep from "@/Pages/Dashboard/Partials/OnboardingStep.vue";
import OnboardingStepExpenseCreated from "./OnboardingStepExpenseCreated.vue";
import OnboardingStepTeamMemberInvited from "./OnboardingStepTeamMemberInvited.vue";
import OnboardingStepProjectCreated from "./OnboardingStepProjectCreated.vue";

let props = defineProps<{
    onboardingStatuses: App.Data.OnboardingStatusData[];
}>();

const stepsData = [
    {
        step: "expense-created",
        component: OnboardingStepExpenseCreated,
    },
    {
        step: "team-member-invited",
        component: OnboardingStepTeamMemberInvited,
    },
    {
        step: "project-created",
        component: OnboardingStepProjectCreated,
    },
];

const getComponent = (step: App.Enums.OnboardingSteps) => {
    const stepObj = stepsData.find((item) => item.step === step);
    return stepObj ? stepObj.component : null;
};
</script>

<template>
    <div>
        this is the onboarding experience

        <div class="space-y-6">
            <div
                v-for="onboardingStatus in onboardingStatuses"
                :key="onboardingStatus.onboarding_step">
                <component
                    :is="getComponent(onboardingStatus.onboarding_step)"
                    :onboarding-status="onboardingStatus" />
            </div>
        </div>
    </div>
</template>

<style scoped></style>
