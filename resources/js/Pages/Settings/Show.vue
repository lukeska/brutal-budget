<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import UpdateProfileInformationForm from "@/Pages/Profile/Partials/UpdateProfileInformationForm.vue";
import UpdateSettingsForm from "@/Pages/Settings/Partials/UpdateSettingsForm.vue";
import UpdatePushSettingsForm from "@/Pages/Settings/Partials/UpdatePushSettingsForm.vue";
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Settings</h2>
        </template>

        <div>
            <div class="mx-auto max-w-7xl py-10 sm:px-6 lg:px-8">
                <div>
                    <UpdateSettingsForm :user="$page.props.auth.user" />
                    <SectionBorder />
                    <UpdatePushSettingsForm :user="$page.props.auth.user" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
