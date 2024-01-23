<script setup lang="ts">
import FormSection from "@/Components/FormSection.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { onMounted, ref } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps<{
    user: Object;
}>();

const form = useForm({
    endpoint: null,
    key: null,
    token: null,
});

const formTestNotification = useForm({});

const notificationsEnabled = ref(false);
const pushEnabled = ref(false);

const enableNotificationsAndPush = () => {
    Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
            notificationsEnabled.value = true;

            enablePush();
        }
    });
};

const enablePush = () => {
    navigator.serviceWorker.ready.then((sw) => {
        // subscribe
        sw.pushManager
            .subscribe({
                userVisibleOnly: true,
                applicationServerKey: import.meta.env.VITE_VAPID_PUBLIC_KEY,
            })
            .then((subscription) => {
                pushEnabled.value = true;

                const subscriptionJson = subscription.toJSON();
                form.endpoint = subscriptionJson.endpoint;
                form.key = subscriptionJson.keys.p256dh;
                form.token = subscriptionJson.keys.auth;

                form.post(route("user-push-settings.subscribe"), {
                    preserveScroll: true,
                });
            });
    });
};

const disableNotifications = () => {
    navigator.serviceWorker.ready.then((sw) => {
        sw.pushManager.getSubscription().then((subscription) => {
            console.log(subscription);
            subscription.unsubscribe().then((successful) => {
                if (successful) {
                    // delete subscription on the server
                    form.endpoint = subscription.endpoint;
                    form.delete(route("user-push-settings.unsubscribe"), {
                        preserveScroll: true,
                    });

                    pushEnabled.value = false;
                }
            });
        });
    });
};

const testNotification = async () => {
    formTestNotification.post(route("user-push-settings.push-test"), {
        preserveScroll: true,
    });
};

onMounted(() => {
    // check if notifications are granted
    notificationsEnabled.value = Notification.permission === "granted";

    // check if push are granted
    navigator.serviceWorker.ready.then((sw) => {
        sw.pushManager.getSubscription().then((subscription) => {
            if (!subscription) {
                pushEnabled.value = false;
                return;
            }

            axios
                .post(route("user-push-settings.subscribed"), {
                    endpoint: subscription.endpoint,
                })
                .then((response) => {
                    // Log the response to the console
                    pushEnabled.value = response.data.subscribed;
                })
                .catch((error) => {
                    // Log any errors to the console
                    console.error("Error:", error);
                });
        });
    });
});
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
                <div v-if="notificationsEnabled && pushEnabled">
                    <div>Notifications are currently enabled.</div>
                </div>
                <div v-else>
                    <div>Notifications are currently disabled.</div>
                </div>
            </div>
        </template>

        <template #actions>
            <div class="flex space-x-4">
                <PrimaryButton
                    v-if="!notificationsEnabled && !pushEnabled"
                    @click.prevent="enableNotificationsAndPush"
                    :disabled="form.processing"
                    >Enable
                </PrimaryButton>
                <PrimaryButton
                    v-if="notificationsEnabled && !pushEnabled"
                    @click.prevent="enablePush"
                    :disabled="form.processing"
                    >Enable
                </PrimaryButton>
                <SecondaryButton
                    v-if="notificationsEnabled && pushEnabled"
                    @click.prevent="testNotification"
                    :disabled="form.processing || formTestNotification.processing"
                    >Test notification
                </SecondaryButton>
                <DangerButton
                    v-if="notificationsEnabled && pushEnabled"
                    @click.prevent="disableNotifications"
                    :disabled="form.processing || formTestNotification.processing"
                    >Disable</DangerButton
                >
            </div>
        </template>
    </FormSection>
</template>
