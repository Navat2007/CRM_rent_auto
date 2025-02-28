<script setup>
import Divider from "primevue/divider";
import {useAuthStore} from "@stores";
import {computed, reactive, unref} from "vue";
import {useVuelidate} from "@vuelidate/core";

const props = defineProps({
    sending: {
        type: Boolean,
        required: false,
        default: false
    },
    item: {
        type: Object,
        required: false,
        default: null
    }
});
const emit = defineEmits(['onSubmit', 'onDelete']);

const {user} = useAuthStore();

const state = reactive({
    companyId: user.company_id,
    carId: 0,
    clientId: 0,
    directory_territory_car_use_id: 0,
    address_give_out: props.item.address_give_out,
    address_take_back: props.item.address_take_back,
    start_date: props.item.start_date ? moment(props.item.start_date).format('DD.MM.YYYY HH:mm') : null,
    end_date: props.item.end_date ? moment(props.item.end_date).format('DD.MM.YYYY HH:mm') : null,
    deposit: props.item.deposit === 0 ? null : props.item.deposit,
    car_issued: parseInt(props.item.car_issued) === 1,
    car_returned: parseInt(props.item.car_returned) === 1,
    rental_days: parseInt(props.item.rental_days),
    rental_rate: props.item.rental_rate,
    rental_rate_text: '',
    rental_cost: props.item.rental_cost,
    note_rental_cost: props.item.note_rental_cost,
    mileage_start: props.item.mileage_start === 0 ? null : props.item.mileage_start,
    mileage_end: props.item.mileage_end === 0 ? null : props.item.mileage_end,
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
    <Card class="xl:w-8/12 w-full">
        <template #content>
            <form @submit.prevent="onFormSubmit" autocomplete="off">
                <div class="grid gap-6 grid-cols-1">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Депозит -->
                        <div class="flex flex-col justify-end">
                            <label
                                for="deposit"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Залог (руб.)
                            </label>
                            <div class="flex gap-2 justify-start items-center">
                                <InputNumber id="deposit" v-model="state.deposit" :min="0" fluid/>
                                <Button
                                    icon="pi pi-replay" severity="contrast" variant="text" rounded
                                    v-tooltip.top="{ value: 'Выполнить автоматический расчет'}"
                                    @click="setDeposit"
                                />
                            </div>
                        </div>
                        <!-- Лимит пробега в сутки (км) -->
                        <div class="flex flex-col justify-end">
                            <label
                                for="limit"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Лимит пробега в сутки (км)
                            </label>
                            <InputNumber id="limit"
                                         :model-value="currentCarClass != null ? currentCarClass.limit : 0"
                                         disabled fluid/>
                        </div>
                        <!-- Стоимость 1 км перепробега (руб.) -->
                        <div class="flex flex-col justify-end">
                            <label
                                for="limit"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Стоимость 1 км перепробега (руб.)
                            </label>
                            <InputNumber id="limit"
                                         :model-value="currentCarClass != null ? currentCarClass.cost_extra_mileage : 0"
                                         disabled fluid/>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Пробег начало (км) -->
                        <div class="flex flex-col justify-end">
                            <label
                                for="mileage_start"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Пробег начало (км)
                            </label>
                            <InputNumber id="mileage_start" v-model="state.mileage_start" fluid/>
                        </div>
                        <!-- Пробег конец (км) -->
                        <div class="flex flex-col justify-end">
                            <label
                                for="mileage_end"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Пробег конец (км)
                            </label>
                            <div class="flex gap-2 justify-start items-center">
                                <InputNumber id="mileage_end" v-model="state.mileage_end" fluid/>
                                <Button
                                    icon="pi pi-save" severity="contrast" variant="text" rounded
                                    v-tooltip.top="{ value: 'Сохранить пробег'}"
                                    @click="saveMileage"
                                />
                            </div>
                        </div>
                        <!-- Километраж (км) -->
                        <div class="flex flex-col justify-end">
                            <label
                                for="mileage"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Километраж (км)
                            </label>
                            <InputNumber
                                id="mileage"
                                :model-value="mileage"
                                disabled fluid
                            />
                        </div>
                        <!-- Перепробег (км) -->
                        <div v-if="over_mileage > 0" class="flex flex-col justify-end">
                            <label
                                for="over_mileage"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                Перепробег (км)
                            </label>
                            <InputNumber
                                id="over_mileage"
                                :model-value="over_mileage"
                                disabled fluid
                            />
                        </div>
                        <!-- За перепробег (руб.) -->
                        <div v-if="over_mileage > 0" class="flex flex-col justify-end">
                            <label
                                for="over_mileage_cost"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >
                                За перепробег (руб.)
                            </label>
                            <InputNumber
                                id="over_mileage_cost"
                                :model-value="over_mileage_cost"
                                disabled fluid
                            />
                        </div>
                    </div>
                </div>
                <Divider v-if="user.access.booking === 2" type="dashed"/>
                <p v-for="error of v$.$errors" :key="error.$uid" class="text-red-500 mb-4">
                    {{ error.$message }}
                </p>
                <div v-if="user.access.booking === 2">
                    <Button type="submit" icon="pi pi-save" label="Сохранить" :loading="sending" outlined/>
                    <Button icon="pi pi-trash" label="Удалить" class="ml-4" severity="danger"
                            :loading="sending"
                            @click="emit('onDelete');" outlined/>
                </div>
            </form>
        </template>
    </Card>
</template>