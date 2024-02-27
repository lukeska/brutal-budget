<script setup lang="ts">
let props = defineProps<{
    onboardingStatuses: App.Data.OnboardingStatusData[];
}>();
</script>

<template>
    <div>
        this is the onboarding experience

        <div
            v-for="onboardingStatus in onboardingStatuses"
            :key="onboardingStatus.onboarding_step">
            {{ onboardingStatus.onboarding_step }}
        </div>
    </div>
</template>

<style scoped></style>
