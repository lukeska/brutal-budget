<script setup lang="ts">
import { ref, computed, watchEffect } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import FormSection from "@/Components/FormSection.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import {
    Combobox,
    ComboboxInput,
    ComboboxButton,
    ComboboxOptions,
    ComboboxOption,
    TransitionRoot,
} from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/20/solid";

const props = defineProps<{
    user: Object;
}>();

const page = usePage();

const form = useForm({
    currency_id: props.user.currency_id,
    secondary_currency_id: props.user.secondary_currency_id,
});

const currencies = page.props.currencies;

let selectedCurrency = ref(currencies.find((currency) => currency.id === props.user.currency_id));
let selectedSecondaryCurrency = ref(currencies.find((currency) => currency.id === props.user.secondary_currency_id));

let queryCurrency = ref("");
let querySecondaryCurrency = ref("");

const filterCurrencies = (query) =>
    query === ""
        ? currencies
        : currencies.filter(
            (currency) =>
                currency.name
                    .toLowerCase()
                    .replace(/\s+/g, "")
                    .includes(query.toLowerCase().replace(/\s+/g, "")) ||
                currency.code
                    .toLowerCase()
                    .replace(/\s+/g, "")
                    .includes(query.toLowerCase().replace(/\s+/g, "")),
        );

let filteredCurrencies = computed(() => filterCurrencies(queryCurrency.value));
let filteredSecondaryCurrencies = computed(() => filterCurrencies(querySecondaryCurrency.value));

const updateSettings = () => {
    form.patch(route("user-settings.update"), {
        preserveScroll: true,
    });
};

watchEffect(() => {
    form.currency_id = selectedCurrency.value?.id || null;
    form.secondary_currency_id = selectedSecondaryCurrency.value?.id || null;
});
</script>

<template>
    <FormSection @submitted="updateSettings">
        <template #title> User Settings</template>

        <template #description> Update your account's currency preference.</template>

        <template #form>
            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel
                    for="currency"
                    value="Currency" />
                <div>
                    <Combobox v-model="selectedCurrency">
                        <div class="relative mt-1">
                            <div
                                class="relative w-full cursor-default overflow-hidden rounded-lg border border-gray-300 bg-white text-left shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-teal-300 sm:text-sm">
                                <ComboboxInput
                                    class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 focus:ring-0"
                                    :displayValue="(currency) => `${currency.code} - ${currency.name}`"
                                    @change="queryCurrency = $event.target.value" />
                                <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
                                    <ChevronUpDownIcon
                                        class="h-5 w-5 text-gray-400"
                                        aria-hidden="true" />
                                </ComboboxButton>
                            </div>
                            <TransitionRoot
                                leave="transition ease-in duration-100"
                                leaveFrom="opacity-100"
                                leaveTo="opacity-0"
                                @after-leave="queryCurrency = ''">
                                <ComboboxOptions
                                    class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm">
                                    <div
                                        v-if="filteredCurrencies.length === 0 && queryCurrency !== ''"
                                        class="relative cursor-default select-none px-4 py-2 text-gray-700">
                                        Nothing found.
                                    </div>

                                    <ComboboxOption
                                        v-for="currency in filteredCurrencies"
                                        as="template"
                                        :key="currency.code"
                                        :value="currency"
                                        v-slot="{ selected, active }">
                                        <li
                                            class="relative cursor-default select-none py-2 pl-10 pr-4"
                                            :class="{
                                                'bg-teal-600 text-white': active,
                                                'text-gray-900': !active,
                                            }">
                                            <span
                                                class="block truncate"
                                                :class="{ 'font-medium': selected, 'font-normal': !selected }">
                                                {{ currency.code }} - {{ currency.name }}
                                            </span>
                                            <span
                                                v-if="selected"
                                                class="absolute inset-y-0 left-0 flex items-center pl-3"
                                                :class="{ 'text-white': active, 'text-teal-600': !active }">
                                                <CheckIcon
                                                    class="h-5 w-5"
                                                    aria-hidden="true" />
                                            </span>
                                        </li>
                                    </ComboboxOption>
                                </ComboboxOptions>
                            </TransitionRoot>
                        </div>
                    </Combobox>
                </div>

                <InputLabel
                    for="secondary_currency"
                    value="Secondary Currency" class="mt-6" />
                <div>
                    <Combobox v-model="selectedSecondaryCurrency">
                        <div class="relative mt-1">
                            <div
                                class="relative w-full cursor-default overflow-hidden rounded-lg border border-gray-300 bg-white text-left shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-teal-300 sm:text-sm">
                                <ComboboxInput
                                    class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 focus:ring-0"
                                    :displayValue="(currency) => `${currency.code} - ${currency.name}`"
                                    @change="querySecondaryCurrency = $event.target.value" />
                                <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
                                    <ChevronUpDownIcon
                                        class="h-5 w-5 text-gray-400"
                                        aria-hidden="true" />
                                </ComboboxButton>
                            </div>
                            <TransitionRoot
                                leave="transition ease-in duration-100"
                                leaveFrom="opacity-100"
                                leaveTo="opacity-0"
                                @after-leave="querySecondaryCurrency = ''">
                                <ComboboxOptions
                                    class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm">
                                    <div
                                        v-if="filteredSecondaryCurrencies.length === 0 && querySecondaryCurrency !== ''"
                                        class="relative cursor-default select-none px-4 py-2 text-gray-700">
                                        Nothing found.
                                    </div>

                                    <ComboboxOption
                                        v-for="currency in filteredSecondaryCurrencies"
                                        as="template"
                                        :key="currency.code"
                                        :value="currency"
                                        v-slot="{ selected, active }">
                                        <li
                                            class="relative cursor-default select-none py-2 pl-10 pr-4"
                                            :class="{
                                                'bg-teal-600 text-white': active,
                                                'text-gray-900': !active,
                                            }">
                                            <span
                                                class="block truncate"
                                                :class="{ 'font-medium': selected, 'font-normal': !selected }">
                                                {{ currency.code }} - {{ currency.name }}
                                            </span>
                                            <span
                                                v-if="selected"
                                                class="absolute inset-y-0 left-0 flex items-center pl-3"
                                                :class="{ 'text-white': active, 'text-teal-600': !active }">
                                                <CheckIcon
                                                    class="h-5 w-5"
                                                    aria-hidden="true" />
                                            </span>
                                        </li>
                                    </ComboboxOption>
                                </ComboboxOptions>
                            </TransitionRoot>
                        </div>
                    </Combobox>
                </div>
                <p class="mt-2 text-sm text-gray-400">Selecting a secondary currency will display a button to toggle between the two currencies on all pages. This will make it easier to switch between the two.</p>
            </div>
        </template>

        <template #actions>
            <ActionMessage
                :on="form.recentlySuccessful"
                class="me-3">
                Saved.
            </ActionMessage>

            <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing">
                Save
            </PrimaryButton>
        </template>
    </FormSection>
</template>
