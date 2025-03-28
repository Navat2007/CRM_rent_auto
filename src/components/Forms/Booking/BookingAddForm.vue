<script setup>
import {computed, onMounted, reactive, ref, unref, watch, watchEffect} from "vue";
import {debounce, isNumber} from 'lodash';
import {useAuthStore} from "@stores";
import {useVuelidate} from '@vuelidate/core';
import {helpers, minValue, required} from '@vuelidate/validators';
import moment from "moment";
import Divider from "primevue/divider";
import Select from "primevue/select";
import AutoService from "@services/AutoService.js";
import ClientsService from "@services/ClientsService.js";
import Car from "@components/Cards/Car.vue";
import DateTimePickerWithMask from "@components/Inputs/DateTimePickerWithMask.vue";
import SearchAddress from "@components/Inputs/SearchAddress.vue";
import Client from "@components/Cards/Client.vue";
import DirectoryService from "@services/DirectoryService.js";
import DaDataService from "@services/DaDataService.js";
import AddDirectoryDrawer from "@components/Drawers/Directory/AddDirectoryDrawer.vue";
import AddClientDrawer from "@components/Drawers/Directory/AddClientDrawer.vue";
import UserService from "@services/UserService.js";
import LegalPersonsService from "@services/LegalPersonsService.js";

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
const currentCarClass = ref(null);

const loadingClients = ref(true);
const isClientAddDrawerOpen = ref(false);
const clients = ref([]);
const currentClient = ref(null);

const loadingUsers = ref(true);
const users = ref([]);
const currentUser = ref(null);

const loadingLegalPersons = ref(true);
const legalPersons = ref([]);
const currentLegalPerson = ref(null);

const loadingTerritoryUse = ref(true);
const isTerritoryUseDrawerOpen = ref(false);
const territories = ref([]);

const loadingCarClasses = ref(true);
const isCarClassesDrawerOpen = ref(false);
const carClasses = ref([]);

const dialogVisible = ref(false);
const dialogHeader = ref('');
const dialogText = ref('');

const state = reactive({
    companyId: user.company_id,
    carId: 0,
    clientId: 0,
    userId: 0,
    legal_person_Id: 0,
    directory_territory_car_use_id: 0,
    address_give_out: '',
    address_take_back: '',
    start_date: null,
    end_date: null,
    deposit: null,
    car_issued: false,
    car_returned: false,
    rental_days: 1,
    rental_rate: 0,
    rental_rate_text: '',
    rental_cost: 0,
    note_rental_cost: '',
    mileage_start: null,
    mileage_end: null,
    payments: [],
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

const mileage = computed(() => {
    if (state.mileage_start > state.mileage_end)
        return 0;

    return state.mileage_end - state.mileage_start;
});
const over_mileage = computed(() => {
    if (currentCarClass.value && mileage.value > 0) {
        return mileage.value - (state.rental_days * parseFloat(currentCarClass.value.limit));
    }

    return 0;
});
const over_mileage_cost = computed(() => {
    if (currentCarClass.value) {
        return over_mileage.value * parseFloat(currentCarClass.value.cost_extra_mileage);
    }

    return 0;
});

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
    clients.value = (await ClientsService.getAllForBooking(user.company_id))
        .filter(item => item.status === "Активен")
        .sort((a, b) => a.full_name.localeCompare(b.full_name));

    loadingClients.value = false;
}

async function fetchUsers() {
    users.value = (await UserService.getUsers(user.company_id))
        .filter(item => item.status === "Активен")
        .sort((a, b) => a.full_name.localeCompare(b.full_name));

  loadingUsers.value = false;
}

async function fetchLegalPersons() {
  legalPersons.value = (await LegalPersonsService.getLegalPersons(user.company_id))
      .filter(item => item.status === "Активен")
      .sort((a, b) => a.full_name.localeCompare(b.full_name));

  loadingLegalPersons.value = false;
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

async function onClientAdd(id) {
    loadingClients.value = true;
    await fetchClients();
    state.clientId = id;
    loadingClients.value = false;
}

const calculateEndDate = () => {
    if (state.start_date === null) {
        dialogHeader.value = "Ошибка";
        dialogText.value = "Нужно выбрать дату начала";
        dialogVisible.value = true;
        return;
    }

    state.end_date = moment(state.start_date, 'DD.MM.YYYY HH:mm').add(state.rental_days, 'days').format('DD.MM.YYYY HH:mm');
}

const setDeposit = () => {
    if (!currentCar.value) {
        dialogHeader.value = "Ошибка";
        dialogText.value = "Нужно выбрать автомобиль";
        dialogVisible.value = true;
        return;
    }

    if (currentCar.value && currentCar.value.class_id) {
        state.deposit = currentCarClass.value && currentCarClass.value.deposit ? parseInt(currentCarClass.value.deposit) : 0;
    }
}

const saveMileage = () => {
    if (!currentCar.value) {
        dialogHeader.value = "Ошибка";
        dialogText.value = "Нужно выбрать автомобиль";
        dialogVisible.value = true;
        return;
    }

    if (state.mileage_end === null || state.mileage_end === 0 || state.mileage_end === '') {
        dialogHeader.value = "Ошибка";
        dialogText.value = "Нужно ввести пробег на момент приема";
        dialogVisible.value = true;
        return;
    }

    if (state.mileage_end === currentCar.value.mileage) {
        dialogHeader.value = "Ошибка";
        dialogText.value = "Пробег совпадает с пробегом на автомобиле";
        dialogVisible.value = true;
        return;
    }

    AutoService.updateMileage({id: currentCar.value.id, mileage: state.mileage_end})
}

const calculateTariff = () => {
    let price = 0;
    state.rental_rate_text = '';

    currentCar.value.saved_price_periods.map(period => {
        if (state.rental_days >= period.days_from && state.rental_days <= period.days_until) {
            if (isNumber(period.price)) {
                price = period.price;
                state.rental_rate_text = period.name;
            }
        } else if (period.days_until === 0 && state.rental_days >= period.days_from) {
            price = period.price;
            state.rental_rate_text = period.name;
        } else if (period.days_from === 0 && state.rental_days <= period.days_from) {
            price = period.price;
            state.rental_rate_text = period.name;
        }
    })

    state.rental_rate = price;
}

const calculateRentalCost = () => {
    state.rental_cost = state.rental_rate * state.rental_days;
}

watchEffect(() => {
    if (state.carId !== 0) {
        currentCar.value = cars.value.find(car => car.id === state.carId);
        state.mileage_start = currentCar.value.mileage;

        if (currentCar.value) {
            currentCarClass.value = carClasses.value.find(carClass => carClass.id === currentCar.value.class_id);
            setDeposit();
            calculateTariff();
        }
    } else {
        currentCar.value = null;
    }

    if (state.clientId !== 0) {
        currentClient.value = clients.value.find(client => client.id === state.clientId);
    } else {
        currentClient.value = null;
    }
});

watch(() => state.rental_days, () => {
    if (state.start_date !== null) {
        calculateEndDate();
    }

    calculateTariff();
    calculateRentalCost();
});

watch(() => state.rental_rate, () => {
    calculateRentalCost();
});

onMounted(() => {
    fetchCars();
    fetchClients();
    fetchUsers();
    fetchLegalPersons();
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
                            <div class="flex flex-col gap-4">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <div class="space-y-2 flex flex-col justify-end">
                                        <!-- Авто -->
                                        <div>
                                            <label for="position"
                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Авто*</label>
                                            <Select
                                                v-model="state.carId" :loading="loadingCars" :options="cars"
                                                optionValue="id" placeholder="Выберите авто" showClear
                                                filter filter-placeholder="Поиск по номеру, марке и модели"
                                                :filter-fields="['state_number', 'brand', 'model']"
                                                class="w-full text-sm"
                                            >
                                                <template #value="slotProps">
                                                    <div v-if="slotProps.value" class="flex items-center gap-4">
                                                        <div class="flex flex-col gap-1 whitespace-pre-wrap">
                                                            <div class="flex gap-1">
                                                                <span>{{
                                                                        cars.find(car => car.id === slotProps.value).brand
                                                                    }}</span>
                                                                <span>{{
                                                                        cars.find(car => car.id === slotProps.value).model
                                                                    }}</span>
                                                            </div>
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
                                                            <img v-if="slotProps.option.avatar"
                                                                 :alt="slotProps.option.label"
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
                                        <!-- Автомобиль выдан? -->
                                        <div class="flex items-center justify-between space-y-2">
                                            <label for="car_issued">Автомобиль выдан</label>
                                            <ToggleSwitch inputId="car_issued" v-model="state.car_issued"/>
                                        </div>
                                        <!-- Начало -->
                                        <div class="flex flex-col justify-end">
                                            <label for="start_date"
                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Начало*</label>
                                            <div>
                                                <DateTimePickerWithMask :value="state.start_date"
                                                                        :key="state.start_date"
                                                                        @onChange="e => state.start_date = e"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-center items-end gap-2">
                                        <div class="flex flex-col items-center">
                                            <label for="rental_days"
                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дней*</label>
                                            <div class="flex gap-2 justify-start items-center">
                                                <Button
                                                    icon="pi pi-chevron-left" severity="contrast" variant="text" rounded
                                                    @click="() => {
                                                        if(state.rental_days > 1) {
                                                            state.rental_days -= 1
                                                        }
                                                    }"
                                                />
                                                <InputNumber id="rental_days" v-model="state.rental_days"
                                                             style="max-width: 52px;" :min="1" fluid/>
                                                <Button
                                                    icon="pi pi-chevron-right" severity="contrast" variant="text"
                                                    rounded
                                                    @click="() => {
                                                        state.rental_days += 1
                                                    }"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="space-y-2 flex flex-col justify-end">
                                        <!-- Клиент -->
                                        <div>
                                            <label for="position"
                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Клиент*</label>
                                            <Select v-model="state.clientId" :loading="loadingClients"
                                                    :options="clients"
                                                    optionLabel="full_name"
                                                    optionValue="id" placeholder="Выберите клиента" showClear filter
                                                    class="w-full"
                                                    :pt="{
                                                        label: 'text-sm text-wrap',
                                                    }"
                                            >
                                                <template v-if="user.access.clients === 2" #header>
                                                    <Button class="mt-4 ml-4" type="button" icon="pi pi-plus"
                                                            label="Добавить"
                                                            outlined
                                                            @click="isClientAddDrawerOpen = true"/>
                                                </template>
                                            </Select>
                                        </div>
                                        <!-- Автомобиль возвращен? -->
                                        <div class="flex text-center items-center justify-between space-y-2">
                                            <label for="car_returned">Автомобиль возвращен</label>
                                            <ToggleSwitch inputId="car_returned" v-model="state.car_returned"/>
                                        </div>
                                        <!-- Возврат -->
                                        <div>
                                            <label for="end_date"
                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Возврат*</label>
                                            <div class="flex gap-2 justify-start items-center">
                                                <DateTimePickerWithMask :value="state.end_date" :key="state.end_date"
                                                                        @onChange="e => state.end_date = e"/>
                                                <Button
                                                    icon="pi pi-replay" severity="contrast" variant="text" rounded
                                                    v-tooltip.top="{ value: 'Выполнить расчет даты возврата'}"
                                                    @click="calculateEndDate"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                              <div class="grid gap-2">
                              </div>

                                <div class="grid gap-2" v-if="currentCar">
                                    <Divider type="dashed"/>

                                    <!-- Тариф -->
                                    <div class="grid gap-2">
                                        <div class="flex flex-col">
                                            <label for="rental_rate"
                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Тариф</label>
                                            <div class="flex items-center gap-2">
                                                <InputNumber
                                                    id="rental_rate" v-model="state.rental_rate" :min="0"
                                                />
                                                <Button
                                                    icon="pi pi-replay" severity="contrast" variant="text" rounded
                                                    v-tooltip.top="{ value: 'Выполнить автоматический расчет'}"
                                                    @click="calculateTariff"
                                                />
                                                <span><i>{{ state.rental_rate_text }}</i></span>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <!-- Стоимость проката -->
                                            <div class="flex flex-col">
                                                <label for="rental_cost"
                                                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Стоимость
                                                    проката</label>
                                                <div class="flex items-center gap-2">
                                                    <InputNumber
                                                        id="rental_cost" v-model="state.rental_cost" disabled
                                                    />
                                                </div>
                                            </div>
                                            <!-- Примечание -->
                                            <div class="flex flex-col">
                                                <label for="note_rental_cost"
                                                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Примечание</label>
                                                <div class="flex items-center gap-2">
                                                    <InputText
                                                        id="note_rental_cost" v-model="state.note_rental_cost" fluid
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <Divider type="dashed"/>

                                    <!-- Территория использования -->
                                    <div>
                                        <label for="position"
                                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Территория
                                            использования</label>
                                        <Select v-model="state.directory_territory_car_use_id"
                                                :loading="loadingTerritoryUse" :options="territories"
                                                optionLabel="name"
                                                optionValue="id" placeholder="Выберите территорию использования"
                                                showClear
                                                filter class="w-full">
                                            <template v-if="user.access.directory === 2" #header>
                                                <Button class="mt-4 ml-4" type="button" icon="pi pi-plus"
                                                        label="Добавить"
                                                        outlined
                                                        @click="isTerritoryUseDrawerOpen = true"/>
                                            </template>
                                        </Select>
                                    </div>
                                    <!-- Адрес выдачи -->
                                    <SearchAddress :input="state.address_give_out" label="Адрес выдачи"
                                                   @onAddressResult="(address) => {
                                        state.address_give_out = address;
                                    }"/>
                                    <!-- Адрес приема -->
                                    <SearchAddress :input="state.address_take_back" label="Адрес приема"
                                                   @onAddressResult="(address) => {
                                        state.address_take_back = address;
                                    }"/>
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
    <AddClientDrawer
        :visible="isClientAddDrawerOpen"
        @onAdd="onClientAdd" @onClose="isClientAddDrawerOpen = false"
    />
    <Dialog v-model:visible="dialogVisible" modal dismissable-mask base-z-index="20000" :header="dialogHeader"
            :style="{ width: '40vw' }" :breakpoints="{ '1199px': '60vw', '575px': '90vw' }">
        <template #header>
            <span class='text-red-500 text-2xl'>{{ dialogHeader }}</span>
        </template>

        <p class="m-0">
            {{ dialogText }}
        </p>
    </Dialog>
</template>