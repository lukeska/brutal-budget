<script setup lang="ts">
import OnboardingStep from "@/Pages/Dashboard/Partials/OnboardingStep.vue";
import OnboardingStepExpenseCreated from "./OnboardingStepExpenseCreated.vue";
import OnboardingStepTeamMemberInvited from "./OnboardingStepTeamMemberInvited.vue";
import OnboardingStepProjectCreated from "./OnboardingStepProjectCreated.vue";
import ExpenseAddButton from "@/Pages/Expenses/Partials/ExpenseAddButton.vue";
import { Link, usePage } from "@inertiajs/vue3";

let props = defineProps<{
    onboardingStatuses: App.Data.OnboardingStatusData[];
}>();

const page = usePage();

const currentStep = () => {
    return false;
};
</script>

<template>
    <div>
        this is the onboarding experience

        <div class="space-y-6">
            <OnboardingStep :onboarding-status="onboardingStatuses[0]">
                <template #title> Create your first expense </template>
                <template #action>
                    <ExpenseAddButton></ExpenseAddButton>
                </template>
            </OnboardingStep>

            <OnboardingStep :onboarding-status="onboardingStatuses[1]">
                <template #title> Invite a friend to join your team </template>
                <template #action>
                    <Link
                        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 disabled:opacity-50"
                        :href="route('teams.show', { team: page.props.auth.user.current_team_id })"
                        >+ Invite</Link
                    >
                </template>
            </OnboardingStep>

            <OnboardingStep :onboarding-status="onboardingStatuses[2]">
                <template #title> Create your first project </template>
                <template #action>
                    <Link
                        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 disabled:opacity-50"
                        :href="route('projects.index')"
                        >+ Project</Link
                    >
                </template>
            </OnboardingStep>
        </div>
    </div>
</template>

<style scoped></style>
