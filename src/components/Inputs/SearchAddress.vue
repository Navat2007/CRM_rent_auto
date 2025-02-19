<script setup>
import {debounce} from "lodash";
import DaDataService from "@services/DaDataService.js";
import {onMounted, reactive, ref, watch} from "vue";
import Select from "primevue/select";

const props = defineProps({
    input: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    }
});
const emit = defineEmits(['onAddressResult']);
const state = reactive({
    address: ''
});

const loadingAddresses = ref(false);
const addresses = ref([]);
const isKeyboardInput = ref(false);

const searchAddress = debounce(async (data) => {
    if (isKeyboardInput.value && data.length >= 4) {
        loadingAddresses.value = true;

        const result = await DaDataService.GetAddress(data);

        if (result.suggestions && result.suggestions.length > 0) {
            addresses.value = result.suggestions.map(suggestion => {
                return {
                    value: suggestion.value,
                    name: suggestion.value
                }
            });
        } else {
            addresses.value = [];
        }

        loadingAddresses.value = false;
    }
}, 1000);

watch(() => state.address, () => {
    if (state.address && state.address.length >= 4) {
        searchAddress(state.address);
    } else {
        addresses.value = [];
    }
});

onMounted(() => {
   state.address = props.input;
});
</script>

<template>
    <div>
        <label for="address_give_out"
               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
        >
            {{ label }}
        </label>
        <Select
            v-model="state.address"
            :loading="loadingAddresses"
            editable showClear
            :options="addresses"
            optionLabel="name"
            optionValue="value"
            placeholder="Введите адрес (поиск начинается с 4 символов)"
            class="w-full"
            @change="event => {
                if(event.originalEvent.type === 'input'){
                    isKeyboardInput = true;
                }
                else {
                    emit('onAddressResult', event.value);
                    isKeyboardInput = false;
                }
            }"
        />
    </div>
</template>