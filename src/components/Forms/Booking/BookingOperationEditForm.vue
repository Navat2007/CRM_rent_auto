<script setup>
import Divider from "primevue/divider";
import {useAuthStore} from "@stores";
import {computed, onMounted, reactive, ref, unref, watch} from "vue";
import {useVuelidate} from "@vuelidate/core";
import DateTimePickerWithMask from "@components/Inputs/DateTimePickerWithMask.vue";
import Select from "primevue/select";
import {isNumber} from "lodash";
import DirectoryService from "@services/DirectoryService.js";
import {helpers, minValue, required} from "@vuelidate/validators";
import moment from "moment/moment.js";
import AddOperationTypeDrawer from "@components/Drawers/Directory/AddOperationTypeDrawer.vue";
import AddPaymentTypeDrawer from "@components/Drawers/Directory/AddPaymentTypeDrawer.vue";

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
    },
    carState:{
        type: Object,
        required: true
    },
});
const emit = defineEmits(['onSubmit', 'onDelete']);

const {user} = useAuthStore();

const state = reactive({
    companyId: user.company_id,
    carId: props.carState.carId,
    carClassId: props.carState.carClassId,
    date: props.item.operation_datetime ? moment(props.item.operation_datetime).format('DD.MM.YYYY HH:mm') : null,
    directory_operation_types_id: parseInt(props.item.directory_operation_types_id),
    directory_payment_types_id: parseInt(props.item.directory_payment_types_id),
    is_income: parseInt(props.item.is_income) === 1 ? "true" : "false",
    period_from: props.item.period_from ? moment(props.item.period_from).format('DD.MM.YYYY HH:mm') : null,
    period_to: props.item.period_to ? moment(props.item.period_to).format('DD.MM.YYYY HH:mm') : null,
    directory_services_id: parseInt(props.item.directory_services_id),
    directory_services_name: props.item.directory_services_name,
    tariff: props.item.tariff && parseInt(props.item.tariff) > 0 ? parseInt(props.item.tariff) : null,
    quantity: parseInt(props.item.quantity),
    accrued: props.item.accrued && parseInt(props.item.accrued) > 0 ? parseInt(props.item.accrued) : null,
    paid: props.item.paid && parseInt(props.item.paid) > 0 ? parseInt(props.item.paid) : null,
});
const rules = computed(() => {
    return {
        directory_operation_types_id: {
            required: helpers.withMessage("Нужно выбрать тип операции", required),
            minValue: helpers.withMessage("Нужно выбрать тип операции", minValue(1)),
            $lazy: true
        },
        directory_payment_types_id: {
            required: helpers.withMessage("Нужно выбрать вид оплаты", required),
            minValue: helpers.withMessage("Нужно выбрать вид оплаты", minValue(1)),
            $lazy: true
        },
    }
});
const v$ = useVuelidate(rules, state);

const loadingOperationTypes = ref(true);
const isOperationTypesDrawerOpen = ref(false);
const operationTypes = ref([]);

const loadingPaymentTypes = ref(true);
const isPaymentTypesDrawerOpen = ref(false);
const paymentTypes = ref([]);

const loadingCarClassServicePrices = ref(true);
const isCarClassServicePricesDrawerOpen = ref(false);
const carClassServicePrices = ref([]);

const loadingServices = ref(true);
const isServicesDrawerOpen = ref(false);
const services = ref([]);

const setIsIncomeByOperationType = () => {
    if(state.directory_operation_types_id !== 0){
        const operationType = operationTypes.value.find(item => item.id === state.directory_operation_types_id);
        state.is_income = parseInt(operationType.is_income) === 1 ? 'true' : 'false';
    }
}
const setTariff = () => {
    state.tariff = null;

    const servicePrice = carClassServicePrices.value.find(item => item.id === state.directory_services_id);

    if(servicePrice && servicePrice.price > 0){
        state.tariff = servicePrice.price;
    }
}
const setAccrued = () => {
    state.accrued = null;

    if(state.tariff && isNumber(state.tariff)){
        state.accrued = state.tariff * state.quantity;
    }
}

const onFormSubmit = async (e) => {
    const isFormCorrect = await unref(v$).$validate();

    if (isFormCorrect) {
        emit('onSubmit', state);
    }
};
async function onDirectoryOperationTypesAdd(id) {
    loadingOperationTypes.value = true;
    await fetchOperationTypes();
    state.directory_operation_types_id = id;
    isOperationTypesDrawerOpen.value = false;
}
async function onDirectoryPaymentTypesAdd(id) {
    loadingPaymentTypes.value = true;
    await fetchPaymentTypes();
    state.directory_payment_types_id = id;
    isPaymentTypesDrawerOpen.value = false;
}

async function fetchOperationTypes() {
    const result = await DirectoryService.getAll({
        directory: 'directory_operation_types',
        company_id: user.company_id
    });

    operationTypes.value = result
        .map(item => {
            item.name = item.name + (parseInt(item.is_income) === 1 ? ' (доход)' : ' (расход)');

            return item;
        })
        .filter(item => item.archive === "Активен");

    loadingOperationTypes.value = false;
}
async function fetchPaymentTypes() {
    paymentTypes.value = (await DirectoryService.getAll({
        directory: 'directory_payment_types',
        company_id: user.company_id
    })).filter(item => item.archive === "Активен");
    loadingPaymentTypes.value = false;
}
async function fetchServices() {
    services.value = (await DirectoryService.getAll({directory: 'directory_services', company_id: user.company_id}))
        .filter(item => item.archive === "Активен");
    loadingServices.value = false;
}
async function fetchCarClassServicePrices() {
    const result = await DirectoryService.getCarClassServicePriceByClassId({id: state.carClassId, company_id: user.company_id});

    carClassServicePrices.value = result;
    loadingCarClassServicePrices.value = false;
}

watch(() => state.directory_operation_types_id, () => {
    setIsIncomeByOperationType();
});

watch(() => state.directory_services_name, () => {
    if(isNumber(state.directory_services_name)){
        state.directory_services_id = state.directory_services_name;
        state.directory_services_name = services.value.find(item => item.id === state.directory_services_name).name;
    }
    else if(!services.value.find(item => item.name === state.directory_services_name)){
        state.directory_services_id = 0;
    }
    else if(state.directory_services_name === "" || state.directory_services_name === null){
        state.directory_services_id = 0;
    }
});

watch(() => state.directory_services_id, () => {
    if(state.directory_services_id > 0){
        setTariff();
    }
});

onMounted(() => {
    fetchOperationTypes();
    fetchPaymentTypes();
    fetchServices();
    fetchCarClassServicePrices();
});
</script>

<template>
    <div>
        <form @submit.prevent="onFormSubmit" autocomplete="off">
            <div class="grid gap-6 grid-cols-1">
                <!-- Дата -->
                <div class="w-full sm:w-80">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата*</label>
                    <div>
                        <DateTimePickerWithMask :value="state.date"
                                                :key="state.date"
                                                @onChange="e => state.date = e"/>
                    </div>
                </div>
                <!-- Тип операции / Вид оплаты -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Тип операции -->
                    <div class="w-full">
                        <label for="position"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Тип операции*</label>
                        <Select v-model="state.directory_operation_types_id"
                                :loading="loadingOperationTypes" :options="operationTypes"
                                optionLabel="name"
                                optionValue="id" placeholder="Выберите тип операции"
                                showClear
                                filter class="w-full">
                            <template v-if="user.access.directory === 2" #header>
                                <Button class="mt-4 ml-4" type="button" icon="pi pi-plus"
                                        label="Добавить"
                                        outlined
                                        @click="isOperationTypesDrawerOpen = true"/>
                            </template>
                        </Select>
                    </div>
                    <!-- Вид оплаты -->
                    <div class="w-full">
                        <label for="position"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Вид оплаты*</label>
                        <Select v-model="state.directory_payment_types_id"
                                :loading="loadingPaymentTypes" :options="paymentTypes"
                                optionLabel="name"
                                optionValue="id" placeholder="Выберите тип операции"
                                showClear
                                filter class="w-full">
                            <template v-if="user.access.directory === 2" #header>
                                <Button class="mt-4 ml-4" type="button" icon="pi pi-plus"
                                        label="Добавить"
                                        outlined
                                        @click="isPaymentTypesDrawerOpen = true"/>
                            </template>
                        </Select>
                    </div>
                </div>
                <!-- Доходная или расходная -->
                <div class="flex flex-wrap gap-4 justify-center sm:justify-start">
                    <div class="flex items-center gap-2">
                        <RadioButton v-model="state.is_income" inputId="is_income1" name="income" value="true" />
                        <label for="is_income1">Доход</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <RadioButton v-model="state.is_income" inputId="is_income2" name="expence" value="false" />
                        <label for="is_income2">Расход</label>
                    </div>
                    <Button
                        icon="pi pi-replay" severity="contrast" variant="text" rounded
                        v-tooltip.top="{ value: 'Заполнить в соответствии с Типом операции'}"
                        @click="setIsIncomeByOperationType"
                    />
                </div>
                <!-- Период -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="w-full">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Период с</label>
                        <div>
                            <DateTimePickerWithMask :value="state.period_from"
                                                    :key="state.period_from"
                                                    @onChange="e => state.period_from = e"/>
                        </div>
                    </div>
                    <div class="w-full">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">по</label>
                        <div>
                            <DateTimePickerWithMask :value="state.period_to"
                                                    :key="state.period_to"
                                                    @onChange="e => state.period_to = e"/>
                        </div>
                    </div>
                </div>
                <!-- Услуга / Тариф / Кол-во -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Услуга -->
                    <div class="w-full">
                        <label for="position"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Услуга</label>
                        <Select v-model="state.directory_services_name"
                                :loading="loadingServices" :options="services"
                                optionLabel="name"
                                optionValue="id" placeholder="Выберите услугу или введите название"
                                showClear editable
                                filter class="w-full">
                        </Select>
                    </div>
                    <!-- Тариф -->
                    <div class="flex flex-col justify-end">
                        <label
                            for="deposit"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Тариф
                        </label>
                        <div class="flex gap-2 justify-start items-center">
                            <InputNumber id="deposit" v-model="state.tariff" :min="0" fluid/>
                            <Button
                                icon="pi pi-replay" severity="contrast" variant="text" rounded
                                v-tooltip.top="{ value: 'Заполнить в соответствии со стоимостью Услуги'}"
                                @click="setTariff"
                            />
                        </div>
                    </div>
                    <!-- Кол-во -->
                    <div class="flex justify-center items-end gap-2">
                        <div class="flex flex-col items-center">
                            <label for="rental_days"
                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Кол-во</label>
                            <div class="flex justify-start items-center">
                                <Button
                                    icon="pi pi-chevron-left" severity="contrast" variant="text" rounded
                                    @click="() => {
                                        if(state.quantity > 1) {
                                            state.quantity -= 1
                                        }
                                    }"
                                />
                                <InputNumber id="rental_days" class="min-w-20" v-model="state.quantity"
                                             style="max-width: 52px;" :min="1" fluid/>
                                <Button
                                    icon="pi pi-chevron-right" severity="contrast" variant="text"
                                    rounded
                                    @click="() => {
                                        state.quantity += 1
                                    }"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Начислено / Оплачено -->
                <div class="flex flex-col sm:flex-row gap-4 justify-between">
                    <!-- Начислено -->
                    <div class="flex flex-col justify-end">
                        <label
                            for="deposit"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Начислено
                        </label>
                        <div class="flex gap-2 justify-start items-center">
                            <InputNumber id="deposit" v-model="state.accrued" :min="0" fluid/>
                            <Button
                                icon="pi pi-replay" severity="contrast" variant="text" rounded
                                v-tooltip.top="{ value: 'Выполнить автоматический расчет'}"
                                @click="setAccrued"
                            />
                        </div>
                    </div>
                    <div class="flex justify-center items-end gap-2">
                        <Button
                            icon="pi pi-arrow-right" severity="contrast" variant="text" rounded
                            v-tooltip.top="{ value: 'Выполнить автоматический расчет'}"
                            @click="() => {state.paid = state.accrued}"
                        />
                    </div>
                    <!-- Оплачено -->
                    <div class="flex flex-col justify-end">
                        <label
                            for="deposit"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Оплачено
                        </label>
                        <InputNumber id="deposit" v-model="state.paid" :min="0" fluid/>
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
    </div>

    <AddOperationTypeDrawer
        :visible="isOperationTypesDrawerOpen"
        @onAdd="onDirectoryOperationTypesAdd" @onClose="isOperationTypesDrawerOpen = false"
    />
    <AddPaymentTypeDrawer
        :visible="isPaymentTypesDrawerOpen"
        @onAdd="onDirectoryPaymentTypesAdd" @onClose="isPaymentTypesDrawerOpen = false"
    />
</template>