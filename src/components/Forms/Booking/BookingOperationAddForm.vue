<script setup>
import Divider from "primevue/divider";
import {computed, reactive, unref} from "vue";
import moment from "moment/moment.js";
import {helpers, minValue, required} from "@vuelidate/validators";
import {useVuelidate} from "@vuelidate/core";
import {useAuthStore} from "@stores";
import DateTimePickerWithMask from "@components/Inputs/DateTimePickerWithMask.vue";

const props = defineProps({
    sending: {
        type: Boolean,
        required: false,
        default: false
    },
});
const emit = defineEmits(['onSubmit']);

const {user} = useAuthStore();

const state = reactive({
    companyId: user.company_id,
    date: moment().format('DD.MM.YYYY HH:mm'),
    clientId: 0,
});
const rules = computed(() => {
    return {

    }
});
const v$ = useVuelidate(rules, state);

const onFormSubmit = async (e) => {
    const isFormCorrect = await unref(v$).$validate();

    if (isFormCorrect) {
        emit('onSubmit', state);
    }
};
</script>

<template>
    <div>
        <form @submit.prevent="onFormSubmit" autocomplete="off">
            <div class="grid gap-6 grid-cols-1">
                <!-- Дата -->
                <div class="col-span-1">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата*</label>
                    <div>
                        <DateTimePickerWithMask :value="state.date"
                                                :key="state.date"
                                                @onChange="e => state.date = e"/>
                    </div>
                </div>
            </div>
            <Divider v-if="user.access.booking === 2" type="dashed"/>
            <p v-for="error of v$.$errors" :key="error.$uid" class="text-red-500 mb-4">
                {{ error.$message }}
            </p>
            <div v-if="user.access.booking === 2">
                <Button type="submit" icon="pi pi-save" label="Добавить" :loading="sending" outlined/>
            </div>
        </form>
    </div>
</template>