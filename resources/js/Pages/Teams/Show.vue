<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import DeleteTeamForm from "@/Pages/Teams/Partials/DeleteTeamForm.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import TeamMemberManager from "@/Pages/Teams/Partials/TeamMemberManager.vue";
import UpdateTeamNameForm from "@/Pages/Teams/Partials/UpdateTeamNameForm.vue";

defineProps<{
    team: Object;
    availableRoles: Array;
    permissions: Object;
}>();
</script>

<template>
    <AppLayout title="Team Settings">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Team Settings</h2>
        </template>

        <div>
            <div class="mx-auto max-w-7xl py-10 sm:px-6 lg:px-8">
                <UpdateTeamNameForm
                    :permissions="permissions"
                    :team="team" />

                <TeamMemberManager
                    :available-roles="availableRoles"
                    :team="team"
                    :user-permissions="permissions"
                    class="mt-10 sm:mt-0" />

                <template v-if="permissions.canDeleteTeam && !team.personal_team">
                    <SectionBorder />

                    <DeleteTeamForm
                        :team="team"
                        class="mt-10 sm:mt-0" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
