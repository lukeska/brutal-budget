<script setup lang="ts">
import FormSection from "@/Components/FormSection.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { computed, ref } from "vue";
import { useForm, router } from "@inertiajs/vue3";

const props = defineProps<{
    user: Object;
}>();

const form = useForm({
    endpoint: null,
    key: null,
    token: null,
});

const notificationPermission = ref(Notification.permission);
const notificationsAreEnabled = computed((): boolean => {
    return notificationPermission.value === "granted";
});

const disableNotifications = () => {
    //Notification.requestPermission() = "denied";
};

const enableNotifications = () => {
    Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
            // get service worker
            navigator.serviceWorker.ready.then((sw) => {
                // subscribe
                sw.pushManager
                    .subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: import.meta.env.VITE_VAPID_PUBLIC_KEY,
                    })
                    .then((subscription) => {
                        //console.log(JSON.parse(JSON.stringify(subscription)));
                        const subscriptionJson = JSON.parse(JSON.stringify(subscription));
                        console.log(subscriptionJson);
                        form.endpoint = subscriptionJson.endpoint;
                        form.key = subscriptionJson.keys.p256dh;
                        form.token = subscriptionJson.keys.auth;
                        //return;
                        router.post(route("user-push-settings.subscribe"), form);
                        // subscription successful
                        /*fetch("/api/push-subscribe", {
                            method: "post",
                            body: JSON.stringify(subscription),
                        }).then(alert("ok"));*/
                    });
            });
        }
    });
};

const testNotification = async () => {
    router.post(route("user-push-settings.push-test"));
};
</script>

<template>
    <!-- Notification settings -->
    <FormSection>
        <template #title>Notifications</template>

        <template #description>
            Get notifications about new expenses being added by other people in your teams.
        </template>

        <template #form>
            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <div v-if="notificationsAreEnabled">
                    <div>Notifications are currently enabled.</div>
                </div>
                <div v-else>
                    <div>Notifications are currently disabled.</div>
                </div>
            </div>
        </template>

        <template #actions>
            <div
                v-if="notificationsAreEnabled"
                class="flex space-x-4">
                <SecondaryButton @click.prevent="testNotification">Test notification</SecondaryButton>
                <DangerButton @click.prevent="disableNotifications">Disable</DangerButton>
            </div>
            <div v-else>
                <PrimaryButton @click.prevent="enableNotifications">Enable</PrimaryButton>
            </div>
        </template>
    </FormSection>
</template>
