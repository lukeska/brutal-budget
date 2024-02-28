<script setup lang="ts">
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Link, usePage } from "@inertiajs/vue3";

let props = defineProps<{
    onboardingStatus: App.Data.OnboardingStatusData;
}>();

const page = usePage();
</script>
<template>
    <div>
        <div class="mb-3 text-xl font-semibold">Invite a friend to join your team</div>
        <div class="flex space-x-4">
            <Link
                class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 disabled:opacity-50"
                :href="route('teams.show', { team: page.props.auth.user.current_team_id })"
                >Invite</Link
            >
            <SecondaryButton>Skip</SecondaryButton>
        </div>
    </div>
</template>
