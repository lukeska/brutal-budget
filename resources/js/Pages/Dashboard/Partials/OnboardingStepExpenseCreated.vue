<script setup lang="ts">
import SecondaryButton from "@/Components/SecondaryButton.vue";
import ExpenseAddButton from "@/Pages/Expenses/Partials/ExpenseAddButton.vue";
import { useForm } from "@inertiajs/vue3";

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
            emit("skipped");
        },
    });
};
</script>
<template>
    <div>
        <div class="mb-3 text-xl font-semibold">Create your first expense</div>
        <div class="flex space-x-4">
            <ExpenseAddButton></ExpenseAddButton>
            <SecondaryButton
                @click.prevent="skip"
                v-if="!onboardingStatus.skipped_at"
                >Skip</SecondaryButton
            >
        </div>
    </div>
</template>
