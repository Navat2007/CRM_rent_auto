<script setup>
import {computed, onMounted, reactive, ref, unref, watchEffect} from "vue";
import {useAuthStore} from "@stores";
import {useVuelidate} from '@vuelidate/core';
import {helpers, minValue, required} from '@vuelidate/validators';
import moment from "moment";
import Divider from "primevue/divider";
import AutoService from "@services/AutoService.js";
import ClientsService from "@services/ClientsService.js";
import Car from "@components/Cards/Car.vue";
import DateTimePickerWithMask from "@components/Inputs/DateTimePickerWithMask.vue";
import Client from "@components/Cards/Client.vue";
import DirectoryService from "@services/DirectoryService.js";
import AddDirectoryDrawer from "@components/Drawers/Directory/AddDirectoryDrawer.vue";
import {Icon} from "@vicons/utils";
import {RefreshOutlined} from "@vicons/material";

const {user} = useAuthStore();

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
const emit = defineEmits(['onSubmit']);

const loadingCars = ref(true);
const cars = ref([]);
const currentCar = ref(null);

const loadingClients = ref(true);
const clients = ref([]);
const currentClient = ref(null);

const loadingTerritoryUse = ref(true);
const isTerritoryUseDrawerOpen = ref(false);
const territories = ref([]);

const loadingCarClasses = ref(true);
const isCarClassesDrawerOpen = ref(false);
const carClasses = ref([]);

const state = reactive({
    companyId: user.company_id,
    carId: 0,
    clientId: 0,
    directory_territory_car_use_id: 0,
    address_give_out: '',
    address_take_back: '',
    start_date: null,
    end_date: null,
    deposit: 0,
});
const rules = computed(() => {
    return {
        carId: {
            required: helpers.withMessage("Нужно выбрать автомобиль", required),
            minValue: helpers.withMessage("Нужно выбрать автомобиль", minValue(1)),
            $lazy: true
        },
        clientId: {
            required: helpers.withMessage("Нужно выбрать клиента", required),
            minValue: helpers.withMessage("Нужно выбрать клиента", minValue(1)),
            $lazy: true
        },
        start_date: {
            required: helpers.withMessage("Нужно выбрать дату начала", required),
            minValue: helpers.withMessage("Дата начала должна быть до даты возврата", value => {
                return moment(value, 'DD.MM.YYYY HH:mm').isBefore(moment(state.end_date, 'DD.MM.YYYY HH:mm'))
            }),
            $lazy: true
        },
        end_date: {
            required: helpers.withMessage("Нужно выбрать дату возврата", required),
            minValue: helpers.withMessage("Дата возврата должна быть после даты начала", value => {
                return moment(value, 'DD.MM.YYYY HH:mm').isAfter(moment(state.start_date, 'DD.MM.YYYY HH:mm'))
            }),
            $lazy: true
        },
    }
});
const v$ = useVuelidate(rules, state);

const onFormSubmit = async (e) => {
    const isFormCorrect = await unref(v$).$validate();

    if (isFormCorrect) {
        emit('onSubmit', state);
    }
};

async function fetchCars() {
    cars.value = (await AutoService.getAllForBooking(user.company_id)).filter(item => item.archive === "Активен");
    loadingCars.value = false;
}

async function fetchClients() {
    clients.value = (await ClientsService.getAllForBooking(user.company_id)).filter(item => item.status === "Активен");
    loadingClients.value = false;
}

async function fetchTerritoryUse() {
    territories.value = (await DirectoryService.getAll({
        directory: 'directory_territory_car_use',
        company_id: user.company_id
    })).filter(item => item.archive === "Активен");
    loadingTerritoryUse.value = false;
}

async function fetchCarClasses() {
    carClasses.value = (await DirectoryService.getAll({
        directory: 'directory_car_classes',
        company_id: user.company_id
    })).filter(item => item.archive === "Активен");
    loadingCarClasses.value = false;
}

async function onDirectoryTerritoryUseAdd(id) {
    loadingTerritoryUse.value = true;
    await fetchTerritoryUse();
    state.directory_territory_car_use_id = id;
    isTerritoryUseDrawerOpen.value = false;
}

const setDeposit = () => {
    if (currentCar.value && currentCar.value.class_id) {
        const carClass = carClasses.value.find(carClass => carClass.id === currentCar.value.class_id);
        state.deposit = carClass && carClass.deposit ? parseInt(carClass.deposit) : 0;
    }
}

watchEffect(() => {
    if (state.carId !== 0) {
        currentCar.value = cars.value.find(car => car.id === state.carId);
        setDeposit();
    } else {
        currentCar.value = null;
    }

    if (state.clientId !== 0) {
        currentClient.value = clients.value.find(client => client.id === state.clientId);
    } else {
        currentClient.value = null;
    }
});

onMounted(() => {
    fetchCars();
    fetchClients();
    fetchTerritoryUse();
    fetchCarClasses();
});
</script>

<template>
    <Card class="xl:w-8/12 w-full">
        <template #title>Добавление договора проката</template>
        <template #content>
            <Tabs value="0" scrollable>
                <TabList>
                    <Tab value="0" class="flex gap-2">Основная информация</Tab>
                    <Tab value="1" class="flex gap-2">Расчет</Tab>
                </TabList>
                <form @submit.prevent="onFormSubmit" autocomplete="off">
                    <TabPanels>
                        <TabPanel value="0">
                            <div class="grid gap-4 sm:grid-cols-1">
                                <!-- Авто -->
                                <div>
                                    <label for="position"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Авто*</label>
                                    <Select
                                        v-model="state.carId" :loading="loadingCars" :options="cars"
                                        optionValue="id" placeholder="Выберите авто" showClear
                                        filter filter-placeholder="Поиск по номеру, марке и модели"
                                        :filter-fields="['state_number', 'brand', 'model']"
                                        class="w-full"
                                    >
                                        <template #value="slotProps">
                                            <div v-if="slotProps.value" class="flex items-center gap-4">
                                                <div class="flex gap-1">
                                                    <span>{{
                                                            cars.find(car => car.id === slotProps.value).brand
                                                        }}</span>
                                                    <span>{{
                                                            cars.find(car => car.id === slotProps.value).model
                                                        }}</span>
                                                    <span>({{
                                                            cars.find(car => car.id === slotProps.value).state_number
                                                        }})</span>
                                                </div>
                                            </div>
                                            <span v-else>
                          {{ slotProps.placeholder }}
                      </span>
                                        </template>
                                        <template #option="slotProps">
                                            <div class="flex gap-4">
                                                <div>
                                                    <img v-if="slotProps.option.avatar" :alt="slotProps.option.label"
                                                         :src="slotProps.option.avatar"
                                                         class="w-24 h-full object-cover"
                                                    />
                                                    <img v-else :alt="slotProps.option.label"
                                                         src="@/assets/images/car-placeholder2.png"
                                                         class="w-24 h-full object-cover"
                                                    />
                                                </div>
                                                <div class="flex flex-col gap-0.5">
                                                    <span>Номер: {{ slotProps.option.state_number }} </span>
                                                    <span>Марка: {{ slotProps.option.brand }}</span>
                                                    <span>Модель {{ slotProps.option.model }}</span>
                                                </div>
                                            </div>
                                        </template>
                                    </Select>
                                </div>
                                <!-- Клиент -->
                                <div>
                                    <label for="position"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Клиент*</label>
                                    <Select v-model="state.clientId" :loading="loadingClients" :options="clients"
                                            optionLabel="full_name"
                                            optionValue="id" placeholder="Выберите клиента" showClear filter
                                            class="w-full">
                                    </Select>
                                </div>
                                <!-- Начало -->
                                <div>
                                    <label for="start_date"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Начало*</label>
                                    <div>
                                        <DateTimePickerWithMask :value="state.start_date" :key="state.start_date"
                                                                @onChange="e => state.start_date = e"/>
                                    </div>
                                </div>
                                <!-- Возврат -->
                                <div>
                                    <label for="end_date"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Возврат*</label>
                                    <div>
                                        <DateTimePickerWithMask :value="state.end_date" :key="state.end_date"
                                                                @onChange="e => state.end_date = e"/>
                                    </div>
                                </div>
                                <!-- Территория использования -->
                                <div>
                                    <label for="position"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Территория
                                        использования</label>
                                    <Select v-model="state.directory_territory_car_use_id"
                                            :loading="loadingTerritoryUse" :options="territories"
                                            optionLabel="name"
                                            optionValue="id" placeholder="Выберите территорию использования" showClear
                                            filter class="w-full">
                                        <template v-if="user.access.directory === 2" #header>
                                            <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить"
                                                    outlined
                                                    @click="isTerritoryUseDrawerOpen = true"/>
                                        </template>
                                    </Select>
                                </div>
                                <!-- Адрес выдачи -->
                                <div>
                                    <label for="address_give_out"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Адрес
                                        выдачи</label>
                                    <input
                                        v-model="state.address_give_out"
                                        type="text" id="address_give_out"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="Введите адрес (поиск начинается с 4 символов)"
                                    >
                                </div>
                                <!-- Адрес приема -->
                                <div>
                                    <label for="address_take_back "
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Адрес
                                        приема</label>
                                    <input
                                        v-model="state.address_take_back "
                                        type="text" id="address_take_back "
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="Введите адрес (поиск начинается с 4 символов)"
                                    >
                                </div>
                                <div class="flex flex-col gap-4 sm:grid-cols-2 grid-cols-1">
                                    <div v-if="currentCar">
                                        <Car :item="currentCar" layout="OneString"/>
                                    </div>
                                    <div v-if="currentClient">
                                        <Client :item="currentClient" layout="OneString"/>
                                    </div>
                                </div>
                            </div>
                        </TabPanel>
                        <TabPanel value="1">
                            <div class="grid gap-4 sm:grid-cols-1">
                                <!-- Депозит -->
                                <div>
                                    <label for="deposit"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Залог
                                        (руб.)</label>
                                    <div class="flex gap-2 justify-center items-center">
                                        <InputNumber id="deposit" v-model="state.deposit" :min="0"
                                                     :useGrouping="false" fluid/>
                                        <Icon size="32" color="green"
                                              v-tooltip.top="{ value: 'Выполнить автоматический расчет'}"
                                              @click="setDeposit"
                                        >
                                            <RefreshOutlined/>
                                        </Icon>
                                    </div>
                                </div>
                            </div>
                        </TabPanel>
                    </TabPanels>
                    <Divider v-if="user.access.booking === 2" type="dashed"/>
                    <p v-for="error of v$.$errors" :key="error.$uid" class="text-red-500 mb-4">
                        {{ error.$message }}
                    </p>
                    <div v-if="user.access.booking === 2">
                        <Button type="submit" icon="pi pi-save" label="Добавить" :loading="sending" outlined/>
                    </div>
                </form>
            </Tabs>
        </template>
    </Card>
    <AddDirectoryDrawer
        title="Добавление территории использования" directory="directory_territory_car_use"
        :visible="isTerritoryUseDrawerOpen"
        @onAdd="onDirectoryTerritoryUseAdd" @onClose="isTerritoryUseDrawerOpen = false"
    />
</template>