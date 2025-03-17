<script setup>
import {computed, onMounted, reactive, ref, unref} from "vue";
import {useAuthStore} from "@stores";
import {useVuelidate} from '@vuelidate/core';
import moment from "moment";
import Divider from "primevue/divider";
import DirectoryService from "@services/DirectoryService.js";
import DatePickerWithMask from "@components/Inputs/DatePickerWithMask.vue";
import AvatarSelect from "@components/Inputs/AvatarSelect.vue";
import FileGallery from "@components/Inputs/FileGallery.vue";
import AddModelDrawer from "@components/Drawers/Directory/Car/AddModelDrawer.vue";
import AddGenerationDrawer from "@components/Drawers/Directory/Car/AddGenerationDrawer.vue";
import AddConfigurationDrawer from "@components/Drawers/Directory/Car/AddConfigurationDrawer.vue";
import AddClassDrawer from "@components/Drawers/Directory/Car/AddClassDrawer.vue";
import AddDirectoryDrawer from "@components/Drawers/Directory/AddDirectoryDrawer.vue";
import LegalPersonsService from "@services/LegalPersonsService.js";
import ClientsService from "@services/ClientsService.js";
import {helpers, minValue, required} from "@vuelidate/validators";
import BookingService from "@services/BookingService.js";

const {user} = useAuthStore();

const props = defineProps({
    sending: {
        type: Boolean,
        required: false,
        default: false
    },
    item: {
        type: Object,
        required: true
    }
});
const emit = defineEmits(['onSubmit', 'onArchive', 'onDelete']);

const directoryClasses = ref([]);
const loadingDirectoryClasses = ref(true);
const isDirectoryClassesAddDrawerOpen = ref(false);

const directoryBrands = ref([]);
const loadingDirectoryBrands = ref(true);
const isDirectoryBrandsAddDrawerOpen = ref(false);

const directoryModels = ref([]);
const loadingDirectoryModels = ref(true);
const isDirectoryModelsAddDrawerOpen = ref(false);

const directoryGenerations = ref([]);
const loadingDirectoryGenerations = ref(true);
const isDirectoryGenerationsAddDrawerOpen = ref(false);

const directoryConfigurations = ref([]);
const loadingDirectoryConfigurations = ref(true);
const isDirectoryConfigurationsAddDrawerOpen = ref(false);

const directoryStatuses = ref([]);
const loadingDirectoryStatuses = ref(true);

const directoryCarBodies = ref([]);
const loadingDirectoryCarBodies = ref(true);
const isDirectoryCarBodiesAddDrawerOpen = ref(false);

const directoryCarFuelTypes = ref([]);
const loadingDirectoryCarFuelTypes = ref(true);
const isDirectoryCarFuelTypesAddDrawerOpen = ref(false);

const directoryCarWheelDrive = ref([]);
const loadingDirectoryCarWheelDrive = ref(true);
const isDirectoryCarWheelDriveAddDrawerOpen = ref(false);

const directoryCarWheelSize = ref([]);
const loadingDirectoryCarWheelSize = ref(true);
const isDirectoryCarWheelSizeAddDrawerOpen = ref(false);

const directoryCarTransmissions = ref([]);
const loadingDirectoryCarTransmissions = ref(true);
const isDirectoryCarTransmissionsAddDrawerOpen = ref(false);

const directoryCarColors = ref([]);
const loadingDirectoryCarColors = ref(true);
const isDirectoryCarColorsAddDrawerOpen = ref(false);
const isDirectoryCarCharacteristicColorsAddDrawerOpen = ref(false);

const directoryCarInteriorMateria = ref([]);
const loadingDirectoryCarInteriorMateria = ref(true);
const isDirectoryCarInteriorMateriaAddDrawerOpen = ref(false);

const directoryCarPtsOwnership = ref([]);
const loadingDirectoryCarPtsOwnership = ref(true);
const isDirectoryCarPtsOwnershipAddDrawerOpen = ref(false);

const directoryCarTiresType = ref([]);
const loadingDirectoryCarTiresType = ref(true);
const isDirectoryCarTiresTypeAddDrawerOpen = ref(false);

const directoryPricePeriods = ref([]);
const loadingDirectoryPricePeriods = ref(true);

const legalPersons = ref([]);
const loadingLegalPersons = ref(true);

const clients = ref([]);
const loadingClients = ref(true);

const booking = ref(null);
const loadingBooking = ref(true);

const conditions = ref([
    {label: 'Неизвестно', value: 0},
    {label: 'Чистый', value: 1},
    {label: 'Грязный', value: 2},
]);
const fuelLevels = ref([
    {label: 'Неизвестно', value: 0},
    {label: 'Низкий уровень топлива', value: 1},
    {label: 'Средний уровень топлива', value: 2},
    {label: 'Полный бак', value: 3},
]);

const state = reactive({
    companyId: user.company_id,
    avatar: props.item.car_photo_avatar,
    state_number: props.item.state_number,
    directory_car_classes_id: props.item.directory_car_classes_id,
    release_year: props.item.release_year,
    directory_car_brands_id: props.item.directory_car_brands_id,
    directory_car_models_id: props.item.directory_car_models_id,
    directory_car_generations_id: props.item.directory_car_generations_id,
    directory_car_configurations_id: props.item.directory_car_configurations_id,
    date_park_enter: props.item.date_park_enter ? moment(props.item.date_park_enter).format('DD.MM.YYYY') : null,
    date_park_exit: props.item.date_park_exit ? moment(props.item.date_park_exit).format('DD.MM.YYYY') : null,
    mileage: props.item.mileage,
    directory_car_statuses_id: props.item.directory_car_statuses_id,
    body_condition: props.item.body_condition,
    interior_condition: props.item.interior_condition,
    trunk_condition: props.item.trunk_condition,
    need_refuel: props.item.need_refuel,
    need_service: props.item.need_service === 1,
    failure: props.item.failure,
    critical_failure: props.item.critical_failure,
    directory_car_bodies_id: props.item.directory_car_bodies_id,
    directory_car_fuel_types_id: props.item.directory_car_fuel_types_id,
    directory_car_wheel_drive_id: props.item.directory_car_wheel_drive_id,
    directory_car_transmissions_id: props.item.directory_car_transmissions_id,
    fuel_tank_capacity: props.item.fuel_tank_capacity,
    fuel_consumption: props.item.fuel_consumption,
    directory_car_colors_id: props.item.directory_car_colors_id,
    seats_count: props.item.seats_count,
    additional_parameter_1: props.item.additional_parameter_1,
    additional_parameter_2: props.item.additional_parameter_2,
    additional_parameter_3: props.item.additional_parameter_3,

    characteristic_rear_view_camera: props.item.characteristic_rear_view_camera === 1,
    characteristic_front_parking_sensors: props.item.characteristic_front_parking_sensors === 1,
    characteristic_rear_parking_sensors: props.item.characteristic_rear_parking_sensors === 1,
    characteristic_camera_360: props.item.characteristic_camera_360 === 1,
    characteristic_panorama: props.item.characteristic_panorama === 1,
    characteristic_AUX: props.item.characteristic_AUX === 1,
    characteristic_bluetooth: props.item.characteristic_bluetooth === 1,
    characteristic_carplay: props.item.characteristic_carplay === 1,
    characteristic_android_auto: props.item.characteristic_android_auto === 1,
    characteristic_heated_seats: props.item.characteristic_heated_seats === 1,
    characteristic_heated_windshield: props.item.characteristic_heated_windshield === 1,
    characteristic_driver_assistance: props.item.characteristic_driver_assistance === 1,
    characteristic_railings: props.item.characteristic_railings === 1,
    characteristic_isofix: props.item.characteristic_isofix === 1,
    characteristic_hatch: props.item.characteristic_hatch === 1,
    characteristic_cruise_control: props.item.characteristic_cruise_control === 1,
    characteristic_armrest: props.item.characteristic_armrest === 1,
    characteristic_airbag_switch: props.item.characteristic_airbag_switch === 1,
    characteristic_directory_car_interior_materia_id: props.item.characteristic_directory_car_interior_materia_id,
    characteristic_directory_car_colors_id: props.item.characteristic_directory_car_colors_id,
    characteristic_additional_parameter_1: props.item.characteristic_additional_parameter_1,
    characteristic_additional_parameter_2: props.item.characteristic_additional_parameter_2,
    characteristic_additional_parameter_3: props.item.characteristic_additional_parameter_3,

    to_directory_car_wheel_size_id: props.item.to_directory_car_wheel_size_id,
    to_directory_car_tires_type_id: props.item.to_directory_car_tires_type_id,
    to_radio_code: props.item.to_radio_code,
    to_secret: props.item.to_secret,
    to_beacon_place_1: props.item.to_beacon_place_1,
    to_beacon_place_2: props.item.to_beacon_place_2,

    pts_series: props.item.pts_series || "",
    pts_number: props.item.pts_number || "",
    pts_issued_by_who: props.item.pts_issued_by_who || "",
    pts_issued_date: props.item.pts_issued_date ? moment(props.item.pts_issued_date).format('DD.MM.YYYY') : null,
    pts_vin: props.item.pts_vin || "",
    pts_body_number: props.item.pts_body_number || "",
    pts_purchase_date: props.item.pts_purchase_date ? moment(props.item.pts_purchase_date).format('DD.MM.YYYY') : null,
    pts_cost_assessment: props.item.pts_cost_assessment || "",
    pts_cost_purchase: props.item.pts_cost_purchase || "",
    pts_directory_car_pts_ownership_id: props.item.pts_directory_car_pts_ownership_id || 0,
    pts_legal_person_id: props.item.pts_legal_person_id || 0,
    pts_files: props.item.pts_files || [],
    pts_upload_files: [],

    sts_client_id: props.item.sts_client_id || 0,
    sts_legal_person_id: props.item.sts_legal_person_id || 0,
    sts_series: props.item.sts_series || "",
    sts_number: props.item.sts_number || "",
    sts_issued_by_who: props.item.sts_issued_by_who || "",
    sts_issued_date: props.item.sts_issued_date ? moment(props.item.sts_issued_date).format('DD.MM.YYYY') : null,
    sts_expire_date: props.item.sts_expire_date ? moment(props.item.sts_expire_date).format('DD.MM.YYYY') : null,
    sts_files: props.item.sts_files || [],
    sts_upload_files: [],

    photo_files: props.item.photo_files || [],
    photo_upload_files: [],
    other_files: props.item.other_files || [],
    other_upload_files: [],

    active: parseInt(props.item.archive) === 0,
    archive: parseInt(props.item.archive),
});
const rules = computed(() => {
    return {
        directory_car_bodies_id: {
            required: helpers.withMessage("Нужно выбрать тип кузова", required),
            minValue: helpers.withMessage("Нужно выбрать тип кузова", minValue(1)),
            $lazy: true
        },
        directory_car_fuel_types_id: {
            required: helpers.withMessage("Нужно выбрать тип топлива", required),
            minValue: helpers.withMessage("Нужно выбрать тип топлива", minValue(1)),
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

async function fetchDirectory(directory, array, loading) {
    array.value = (await DirectoryService.getAll({
        directory: directory,
        company_id: user.company_id
    })).filter(item => item.archive === "Активен");
    loading.value = false;
}

async function fetchDirectoryPricePeriods() {
    directoryPricePeriods.value = (await DirectoryService.getPricePeriods({company_id: user.company_id}))
        .filter(item => item.archive === "Активен");

    state.price_periods = directoryPricePeriods.value.map((item) => {
        let price = null;

        if(props.item.saved_price_periods){
            props.item.saved_price_periods.forEach((saved_item) => {
                if(saved_item.id === item.id) {
                    price = saved_item.price;
                }
            });
        }

        return {
            id: item.id,
            name: item.name,
            price: price
        }
    });

    loadingDirectoryPricePeriods.value = false;
}

async function fetchLegalPersons() {
    legalPersons.value = (await LegalPersonsService.getLegalPersons(user.company_id))
        .filter(person => person.status === "Активен")
        .map(item => {
            item.name = item.full_name;
            return item;
        });
    loadingLegalPersons.value = false;
}

async function fetchClients() {
    clients.value = (await ClientsService.getClients(user.company_id))
        .filter(person => person.status === "Активен")
        .map(item => {
            item.name = item.full_name;
            return item;
        });
    loadingClients.value = false;
}

async function fetchBooking() {
    booking.value = (await BookingService.getBookingByCarId({id: props.item.id}))

    if(booking.value.length === 0)
        booking.value = null;

    loadingBooking.value = false;
}

async function onDirectoryClassAdd(id) {
    loadingDirectoryClasses.value = true;
    await fetchDirectory('directory_car_classes', directoryClasses, loadingDirectoryClasses);
    state.directory_car_classes_id = id;
    isDirectoryClassesAddDrawerOpen.value = false;
}

async function onDirectoryBrandAdd(id) {
    loadingDirectoryBrands.value = true;
    await fetchDirectory('directory_car_brands', directoryBrands, loadingDirectoryBrands);
    state.directory_car_brands_id = id;
    isDirectoryBrandsAddDrawerOpen.value = false;
}

async function onDirectoryModelAdd(id) {
    loadingDirectoryModels.value = true;
    await fetchDirectory('directory_car_models', directoryModels, loadingDirectoryModels);
    state.directory_car_models_id = id;
    isDirectoryModelsAddDrawerOpen.value = false;
}

async function onDirectoryGenerationAdd(id) {
    loadingDirectoryGenerations.value = true;
    await fetchDirectory('directory_car_generations', directoryGenerations, loadingDirectoryGenerations);
    state.directory_car_generations_id = id;
    isDirectoryGenerationsAddDrawerOpen.value = false;
}

async function onDirectoryConfigurationAdd(id) {
    loadingDirectoryConfigurations.value = true;
    await fetchDirectory('directory_car_configurations', directoryConfigurations, loadingDirectoryConfigurations);
    state.directory_car_configurations_id = id;
    isDirectoryConfigurationsAddDrawerOpen.value = false;
}

async function onDirectoryCarBodiesAdd(id) {
    loadingDirectoryCarBodies.value = true;
    await fetchDirectory('directory_car_bodies', directoryCarBodies, loadingDirectoryCarBodies);
    state.directory_car_bodies_id = id;
    isDirectoryCarBodiesAddDrawerOpen.value = false;
}

async function onDirectoryCarFuelTypesAdd(id) {
    loadingDirectoryCarFuelTypes.value = true;
    await fetchDirectory('directory_car_fuel_types', directoryCarFuelTypes, loadingDirectoryCarFuelTypes);
    state.directory_car_fuel_types_id = id;
    isDirectoryCarFuelTypesAddDrawerOpen.value = false;
}

async function onDirectoryCarWheelDriveAdd(id) {
    loadingDirectoryCarWheelDrive.value = true;
    await fetchDirectory('directory_car_wheel_drive', directoryCarWheelDrive, loadingDirectoryCarWheelDrive);
    state.directory_car_wheel_drive_id = id;
    isDirectoryCarWheelDriveAddDrawerOpen.value = false;
}

async function onDirectoryCarWheelSizeAdd(id) {
    loadingDirectoryCarWheelSize.value = true;
    await fetchDirectory('directory_car_wheel_size', directoryCarWheelSize, loadingDirectoryCarWheelSize);
    state.to_directory_car_wheel_size_id = id;
    isDirectoryCarWheelSizeAddDrawerOpen.value = false;
}

async function onDirectoryCarTransmissionsAdd(id) {
    loadingDirectoryCarTransmissions.value = true;
    await fetchDirectory('directory_car_transmissions', directoryCarTransmissions, loadingDirectoryCarTransmissions);
    state.directory_car_transmissions_id = id;
    isDirectoryCarTransmissionsAddDrawerOpen.value = false;
}

async function onDirectoryCarColorsAdd(id) {
    loadingDirectoryCarColors.value = true;
    await fetchDirectory('directory_car_colors', directoryCarColors, loadingDirectoryCarColors);
    state.directory_car_colors_id = id;
    isDirectoryCarColorsAddDrawerOpen.value = false;
}

async function onDirectoryCarCharacteristicColorsAdd(id) {
    loadingDirectoryCarColors.value = true;
    await fetchDirectory('directory_car_colors', directoryCarColors, loadingDirectoryCarColors);
    state.characteristic_directory_car_colors_id = id;
    isDirectoryCarCharacteristicColorsAddDrawerOpen.value = false;
}

async function onDirectoryCarInteriorMateriaAdd(id) {
    loadingDirectoryCarInteriorMateria.value = true;
    await fetchDirectory('directory_car_interior_materia', directoryCarInteriorMateria, loadingDirectoryCarInteriorMateria);
    state.characteristic_directory_car_interior_materia_id = id;
    isDirectoryCarInteriorMateriaAddDrawerOpen.value = false;
}

async function onDirectoryCarPtsOwnershipAdd(id) {
    loadingDirectoryCarPtsOwnership.value = true;
    await fetchDirectory('directory_car_pts_ownership', directoryCarPtsOwnership, loadingDirectoryCarPtsOwnership);
    state.pts_directory_car_pts_ownership_id = id;
    isDirectoryCarPtsOwnershipAddDrawerOpen.value = false;
}

async function onDirectoryCarTiresTypeAdd(id) {
    loadingDirectoryCarTiresType.value = true;
    await fetchDirectory('directory_car_tires_type', directoryCarTiresType, loadingDirectoryCarTiresType);
    state.to_directory_car_tires_type_id = id;
    isDirectoryCarTiresTypeAddDrawerOpen.value = false;
}

const openBooking = (id) => {
    window.open('/Admin/booking/rentalContracts/' + id, '_blank');
}

const openClient = (id) => {
    window.open('/Admin/clients/' + id, '_blank');
}

onMounted(() => {
    fetchDirectory('directory_car_statuses', directoryStatuses, loadingDirectoryStatuses);
    fetchDirectory('directory_car_classes', directoryClasses, loadingDirectoryClasses);
    fetchDirectory('directory_car_brands', directoryBrands, loadingDirectoryBrands);
    fetchDirectory('directory_car_models', directoryModels, loadingDirectoryModels);
    fetchDirectory('directory_car_generations', directoryGenerations, loadingDirectoryGenerations);
    fetchDirectory('directory_car_configurations', directoryConfigurations, loadingDirectoryConfigurations);
    fetchDirectory('directory_car_bodies', directoryCarBodies, loadingDirectoryCarBodies);
    fetchDirectory('directory_car_fuel_types', directoryCarFuelTypes, loadingDirectoryCarFuelTypes);
    fetchDirectory('directory_car_wheel_drive', directoryCarWheelDrive, loadingDirectoryCarWheelDrive);
    fetchDirectory('directory_car_wheel_size', directoryCarWheelSize, loadingDirectoryCarWheelSize);
    fetchDirectory('directory_car_transmissions', directoryCarTransmissions, loadingDirectoryCarTransmissions);
    fetchDirectory('directory_car_colors', directoryCarColors, loadingDirectoryCarColors);
    fetchDirectory('directory_car_interior_materia', directoryCarInteriorMateria, loadingDirectoryCarInteriorMateria);
    fetchDirectory('directory_car_pts_ownership', directoryCarPtsOwnership, loadingDirectoryCarPtsOwnership);
    fetchDirectory('directory_car_tires_type', directoryCarTiresType, loadingDirectoryCarTiresType);

    fetchDirectoryPricePeriods();
    fetchLegalPersons();
    fetchClients();
    fetchBooking();
});
</script>

<template>
    <Card class="xl:w-8/12 w-full">
        <template #title>Редактирование авто</template>
        <template #content>
            <Tabs value="0" scrollable>
                <TabList>
                    <Tab value="0" class="flex gap-2">Основная информация</Tab>
                    <Tab value="1" class="flex gap-2">ПТС</Tab>
                    <Tab value="2" class="flex gap-2">СТС</Tab>
                    <Tab value="3" class="flex gap-2">Техническое обслуживание</Tab>
                    <Tab value="4" class="flex gap-2">Фотографии</Tab>
                    <Tab value="5" class="flex gap-2">Документы</Tab>
                </TabList>
                <form @submit.prevent="onFormSubmit" autocomplete="off">
                    <TabPanels>
                        <TabPanel value="0">
                            <div class="grid gap-4 sm:grid-cols-1">
                                <!-- Заглавное фото авто -->
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Заглавное
                                        фото
                                        авто</label>
                                    <AvatarSelect :value="state.avatar" @onSelect="e => state.avatar = e.files[0]"
                                                  @onDelete="state.avatar = null"/>
                                </div>

                                <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4">
                                    <!-- Гос номер -->
                                    <div>
                                        <label for="state_number"
                                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Гос
                                            номер</label>
                                        <InputText id="state_number" v-model="state.state_number" fluid/>
                                    </div>
                                    <!--  Класс авто -->
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Класс
                                            авто</label>
                                        <Select v-model="state.directory_car_classes_id" :loading="loadingDirectoryClasses"
                                                :options="directoryClasses"
                                                optionLabel="name"
                                                optionValue="id" placeholder="Выберите класс авто" showClear filter
                                                class="w-full">
                                            <template v-if="user.access.auto === 2" #header>
                                                <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить"
                                                        outlined
                                                        @click="isDirectoryClassesAddDrawerOpen = true"/>
                                            </template>
                                        </Select>
                                    </div>
                                    <!-- Пробег -->
                                    <div>
                                        <label for="mileage"
                                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Пробег</label>
                                        <InputNumber id="mileage" v-model="state.mileage" :min="0" fluid/>
                                    </div>
                                    <!-- Статус авто -->
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Статус
                                            авто</label>
                                        <Select v-model="state.directory_car_statuses_id"
                                                :loading="loadingDirectoryStatuses"
                                                :options="directoryStatuses"
                                                optionLabel="name"
                                                optionValue="id" filter class="w-full">
                                        </Select>
                                    </div>
                                    <!--  Марка авто -->
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Марка
                                            авто</label>
                                        <Select v-model="state.directory_car_brands_id" :loading="loadingDirectoryBrands"
                                                :options="directoryBrands"
                                                optionLabel="name" optionValue="id" placeholder="Выберите марку авто"
                                                showClear filter
                                                class="w-full"
                                                @change="(e) => {
                            if(e.value === null)
                              state.directory_car_brands_id = 0;

                            state.directory_car_models_id = 0;
                            state.directory_car_generations_id = 0;
                            state.directory_car_configurations_id = 0;
                          }"
                                        >
                                            <template v-if="user.access.auto === 2" #header>
                                                <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить"
                                                        outlined
                                                        @click="isDirectoryBrandsAddDrawerOpen = true"/>
                                            </template>
                                        </Select>
                                    </div>
                                    <!--  Модель авто -->
                                    <div v-if="state.directory_car_brands_id !== 0">
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Модель
                                            авто</label>
                                        <Select v-model="state.directory_car_models_id" :loading="loadingDirectoryModels"
                                                :options="directoryModels.filter(item => parseInt(item.directory_car_brands_id) === state.directory_car_brands_id)"
                                                optionLabel="name" optionValue="id" placeholder="Выберите модель авто"
                                                showClear filter
                                                class="w-full"
                                                @change="(e) => {
                            if(e.value === null)
                              state.directory_car_models_id = 0;

                            state.directory_car_generations_id = 0;
                            state.directory_car_configurations_id = 0;
                          }"
                                        >
                                            <template v-if="user.access.auto === 2" #header>
                                                <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить"
                                                        outlined
                                                        @click="isDirectoryModelsAddDrawerOpen = true"/>
                                            </template>
                                        </Select>
                                    </div>
                                    <!--  Поколение авто -->
                                    <div v-if="state.directory_car_models_id !== 0">
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Поколение
                                            авто</label>
                                        <Select v-model="state.directory_car_generations_id"
                                                :loading="loadingDirectoryGenerations"
                                                :options="directoryGenerations.filter(item => parseInt(item.directory_car_models_id) === state.directory_car_models_id)"
                                                optionLabel="name"
                                                optionValue="id" placeholder="Выберите поколение авто" showClear filter
                                                class="w-full">
                                            <template v-if="user.access.auto === 2" #header>
                                                <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить"
                                                        outlined
                                                        @click="isDirectoryGenerationsAddDrawerOpen = true"/>
                                            </template>
                                        </Select>
                                    </div>
                                    <!--  Комплектация авто -->
                                    <div v-if="state.directory_car_models_id !== 0">
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Комплектация
                                            авто</label>
                                        <Select v-model="state.directory_car_configurations_id"
                                                :loading="loadingDirectoryConfigurations"
                                                :options="directoryConfigurations.filter(item => parseInt(item.directory_car_models_id) === state.directory_car_models_id)"
                                                optionLabel="name"
                                                optionValue="id" placeholder="Выберите комплектацию авто" showClear filter
                                                class="w-full">
                                            <template v-if="user.access.auto === 2" #header>
                                                <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить"
                                                        outlined
                                                        @click="isDirectoryConfigurationsAddDrawerOpen = true"/>
                                            </template>
                                        </Select>
                                    </div>
                                    <!-- Год выпуска -->
                                    <div>
                                        <label for="release_year"
                                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Год
                                            выпуска</label>
                                        <InputNumber id="release_year" v-model="state.release_year" :min="1900"
                                                     :max="moment().year()"
                                                     :useGrouping="false" fluid/>
                                    </div>
                                    <!-- Дата поступления в парк -->
                                    <div>
                                        <label for="date_park_enter"
                                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата
                                            поступления в
                                            парк</label>
                                        <div>
                                            <DatePickerWithMask :value="state.date_park_enter" :key="state.date_park_enter"
                                                                @onChange="e => state.date_park_enter = e"/>
                                        </div>
                                    </div>
                                    <!-- Дата исключения из парка -->
                                    <div>
                                        <label for="date_park_exit"
                                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата
                                            исключения из
                                            парка</label>
                                        <div>
                                            <DatePickerWithMask :value="state.date_park_exit" :key="state.date_park_exit"
                                                                @onChange="e => state.date_park_exit = e"/>
                                        </div>
                                    </div>
                                </div>

                                <Divider type="dashed"/>

                                <Panel header="Цены" toggleable collapsed>
                                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                        <thead class="text-xs text-gray-700 uppercase  dark:text-gray-200">
                                        <tr>
                                            <th scope="col" class="py-3">Дней аренды от-до</th>
                                            <th scope="col" class="py-3">Стоимость в день</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr
                                            v-for="(item, index) in state.price_periods" :key="item"
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200"
                                        >
                                            <th scope="row" class="py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ item.name }}</th>
                                            <td><InputNumber v-model="item.price" min="0" /></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </Panel>

                                <Panel header="Состояние авто" toggleable collapsed>
                                    <div class="grid gap-4 sm:grid-cols-1">
                                        <!-- Состояние кузова -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Состояние
                                                кузова</label>
                                            <Select v-model="state.body_condition" :options="conditions"
                                                    :modelValue="state.body_condition" optionLabel="label"
                                                    optionValue="value" placeholder="Выберите состояние"
                                                    class="w-full"/>
                                        </div>
                                        <!-- Состояние салона -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Состояние
                                                салона</label>
                                            <Select v-model="state.interior_condition" :options="conditions"
                                                    :modelValue="state.interior_condition" optionLabel="label"
                                                    optionValue="value" placeholder="Выберите состояние"
                                                    class="w-full"/>
                                        </div>
                                        <!-- Состояние багажника -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Состояние
                                                багажника</label>
                                            <Select v-model="state.trunk_condition" :options="conditions"
                                                    :modelValue="state.trunk_condition" optionLabel="label"
                                                    optionValue="value" placeholder="Выберите состояние"
                                                    class="w-full"/>
                                        </div>
                                        <!-- Нужно заправить -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Нужно
                                                заправить</label>
                                            <Select v-model="state.need_refuel" :options="fuelLevels"
                                                    :modelValue="state.need_refuel" optionLabel="label"
                                                    optionValue="value" placeholder="Выберите уровень топлива"
                                                    class="w-full"/>
                                        </div>
                                        <!-- Нужно обслужить -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="need_service" type="checkbox"
                                                       v-model="state.need_service"
                                                       class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
                                                <label for="need_service"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Нужно обслужить?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Неисправность -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Неисправность</label>
                                            <Textarea id="failure" v-model="state.failure" rows="3" cols="30" autoResize
                                                      fluid/>
                                        </div>
                                        <!-- Критическая неисправность -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Критическая
                                                неисправность</label>
                                            <Textarea id="critical_failure" v-model="state.critical_failure" rows="3"
                                                      cols="30" autoResize fluid/>
                                        </div>
                                    </div>
                                </Panel>

                                <Panel header="Характеристики" toggleable collapsed>
                                    <div class="grid gap-4 sm:grid-cols-1">
                                        <!--  Тип кузова -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Тип
                                                кузова</label>
                                            <Select v-model="state.directory_car_bodies_id"
                                                    :loading="loadingDirectoryCarBodies"
                                                    :options="directoryCarBodies"
                                                    optionLabel="name" optionValue="id"
                                                    placeholder="Выберите тип кузова" showClear filter
                                                    class="w-full"
                                            >
                                                <template v-if="user.access.auto === 2" #header>
                                                    <Button class="mt-4 ml-4" type="button" icon="pi pi-plus"
                                                            label="Добавить" outlined
                                                            @click="isDirectoryCarBodiesAddDrawerOpen = true"/>
                                                </template>
                                            </Select>
                                        </div>
                                        <!--  Тип топлива -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Тип
                                                топлива</label>
                                            <Select v-model="state.directory_car_fuel_types_id"
                                                    :loading="loadingDirectoryCarFuelTypes"
                                                    :options="directoryCarFuelTypes"
                                                    optionLabel="name" optionValue="id"
                                                    placeholder="Выберите тип топлива" showClear filter
                                                    class="w-full"
                                            >
                                                <template v-if="user.access.auto === 2" #header>
                                                    <Button class="mt-4 ml-4" type="button" icon="pi pi-plus"
                                                            label="Добавить" outlined
                                                            @click="isDirectoryCarFuelTypesAddDrawerOpen = true"/>
                                                </template>
                                            </Select>
                                        </div>
                                        <!--  Привод -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Привод</label>
                                            <Select v-model="state.directory_car_wheel_drive_id"
                                                    :loading="loadingDirectoryCarWheelDrive"
                                                    :options="directoryCarWheelDrive"
                                                    optionLabel="name" optionValue="id" placeholder="Выберите привод"
                                                    showClear filter
                                                    class="w-full"
                                            >
                                                <template v-if="user.access.auto === 2" #header>
                                                    <Button class="mt-4 ml-4" type="button" icon="pi pi-plus"
                                                            label="Добавить" outlined
                                                            @click="isDirectoryCarWheelDriveAddDrawerOpen = true"/>
                                                </template>
                                            </Select>
                                        </div>
                                        <!--  Трансмиссия -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Трансмиссия</label>
                                            <Select v-model="state.directory_car_transmissions_id"
                                                    :loading="loadingDirectoryCarTransmissions"
                                                    :options="directoryCarTransmissions"
                                                    optionLabel="name" optionValue="id"
                                                    placeholder="Выберите трансмиссию" showClear filter
                                                    class="w-full"
                                            >
                                                <template v-if="user.access.auto === 2" #header>
                                                    <Button class="mt-4 ml-4" type="button" icon="pi pi-plus"
                                                            label="Добавить" outlined
                                                            @click="isDirectoryCarTransmissionsAddDrawerOpen = true"/>
                                                </template>
                                            </Select>
                                        </div>
                                        <!-- Объем бака -->
                                        <div>
                                            <label for="fuel_tank_capacity"
                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Объем
                                                бака</label>
                                            <InputText id="fuel_tank_capacity" v-model="state.fuel_tank_capacity"
                                                       fluid/>
                                        </div>
                                        <!-- Расход топлива -->
                                        <div>
                                            <label for="fuel_consumption"
                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Расход
                                                топлива</label>
                                            <InputText id="fuel_consumption" v-model="state.fuel_consumption" fluid/>
                                        </div>
                                        <!--  Цвет кузова -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Цвет
                                                кузова</label>
                                            <Select v-model="state.directory_car_colors_id"
                                                    :loading="loadingDirectoryCarColors"
                                                    :options="directoryCarColors"
                                                    optionLabel="name" optionValue="id" placeholder="Выберите цвет"
                                                    showClear filter
                                                    class="w-full"
                                            >
                                                <template v-if="user.access.auto === 2" #header>
                                                    <Button class="mt-4 ml-4" type="button" icon="pi pi-plus"
                                                            label="Добавить" outlined
                                                            @click="isDirectoryCarColorsAddDrawerOpen = true"/>
                                                </template>
                                            </Select>
                                        </div>
                                        <!-- Количество мест -->
                                        <div>
                                            <label for="seats_count"
                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Количество
                                                мест</label>
                                            <InputNumber id="seats_count" v-model="state.seats_count" :min="0"
                                                         :useGrouping="false" fluid/>
                                        </div>
                                        <!-- Доп параметр 1 -->
                                        <div>
                                            <label for="additional_parameter_1"
                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Доп
                                                параметр 1</label>
                                            <InputText id="additional_parameter_1"
                                                       v-model="state.additional_parameter_1" fluid/>
                                        </div>
                                        <!-- Доп параметр 2 -->
                                        <div>
                                            <label for="additional_parameter_2"
                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Доп
                                                параметр 2</label>
                                            <InputText id="additional_parameter_2"
                                                       v-model="state.additional_parameter_2" fluid/>
                                        </div>
                                        <!-- Доп параметр 3 -->
                                        <div>
                                            <label for="additional_parameter_3"
                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Доп
                                                параметр 3</label>
                                            <InputText id="additional_parameter_3"
                                                       v-model="state.additional_parameter_3" fluid/>
                                        </div>
                                    </div>
                                </Panel>

                                <Panel header="Доп характеристики" toggleable collapsed>
                                    <div class="grid gap-4 sm:grid-cols-1">
                                        <!-- Камера заднего вида -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_rear_view_camera" type="checkbox"
                                                       v-model="state.characteristic_rear_view_camera"
                                                       class="checkbox"/>
                                                <label for="characteristic_rear_view_camera"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Камера заднего вида?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Парктроники передние -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_front_parking_sensors" type="checkbox"
                                                       v-model="state.characteristic_front_parking_sensors"
                                                       class="checkbox"/>
                                                <label for="characteristic_front_parking_sensors"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Парктроники передние?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Парктроники задние -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_rear_parking_sensors" type="checkbox"
                                                       v-model="state.characteristic_rear_parking_sensors"
                                                       class="checkbox"/>
                                                <label for="characteristic_rear_parking_sensors"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Парктроники задние?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Камеры 360 -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_camera_360" type="checkbox"
                                                       v-model="state.characteristic_camera_360"
                                                       class="checkbox"/>
                                                <label for="characteristic_camera_360"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Камеры 360?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Панорама -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_panorama" type="checkbox"
                                                       v-model="state.characteristic_panorama"
                                                       class="checkbox"/>
                                                <label for="characteristic_panorama"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Панорама?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- AUX -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_AUX" type="checkbox"
                                                       v-model="state.characteristic_AUX"
                                                       class="checkbox"/>
                                                <label for="characteristic_AUX"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    AUX?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Bluetooth -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_bluetooth" type="checkbox"
                                                       v-model="state.characteristic_bluetooth"
                                                       class="checkbox"/>
                                                <label for="characteristic_bluetooth"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Bluetooth?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Carplay -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_carplay" type="checkbox"
                                                       v-model="state.characteristic_carplay"
                                                       class="checkbox"/>
                                                <label for="characteristic_carplay"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Carplay?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- ANDROID AUTO -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_android_auto" type="checkbox"
                                                       v-model="state.characteristic_android_auto"
                                                       class="checkbox"/>
                                                <label for="characteristic_android_auto"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    ANDROID AUTO?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Подогрев сидений -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_heated_seats" type="checkbox"
                                                       v-model="state.characteristic_heated_seats"
                                                       class="checkbox"/>
                                                <label for="characteristic_heated_seats"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Подогрев сидений?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Подогрев лобового стекла -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_heated_windshield" type="checkbox"
                                                       v-model="state.characteristic_heated_windshield"
                                                       class="checkbox"/>
                                                <label for="characteristic_heated_windshield"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Подогрев лобового стекла?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Ассистенты помощи водителю -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_driver_assistance" type="checkbox"
                                                       v-model="state.characteristic_driver_assistance"
                                                       class="checkbox"/>
                                                <label for="characteristic_driver_assistance"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Ассистенты помощи водителю?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Рейлинги -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_railings" type="checkbox"
                                                       v-model="state.characteristic_railings"
                                                       class="checkbox"/>
                                                <label for="characteristic_railings"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Рейлинги?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Isofix -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_isofix" type="checkbox"
                                                       v-model="state.characteristic_isofix"
                                                       class="checkbox"/>
                                                <label for="characteristic_isofix"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Isofix?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Люк -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_hatch" type="checkbox"
                                                       v-model="state.characteristic_hatch"
                                                       class="checkbox"/>
                                                <label for="characteristic_hatch"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Люк?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Круиз контроль -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_cruise_control" type="checkbox"
                                                       v-model="state.characteristic_cruise_control"
                                                       class="checkbox"/>
                                                <label for="characteristic_cruise_control"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Круиз контроль?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Подлокотник -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_armrest" type="checkbox"
                                                       v-model="state.characteristic_armrest"
                                                       class="checkbox"/>
                                                <label for="characteristic_armrest"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Подлокотник?
                                                </label>
                                            </div>
                                        </div>
                                        <!-- Выключатель airbag -->
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <input id="characteristic_airbag_switch" type="checkbox"
                                                       v-model="state.characteristic_airbag_switch"
                                                       class="checkbox"/>
                                                <label for="characteristic_airbag_switch"
                                                       class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                    Выключатель airbag?
                                                </label>
                                            </div>
                                        </div>
                                        <!--  Материал салона -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Материал
                                                салона</label>
                                            <Select v-model="state.characteristic_directory_car_interior_materia_id"
                                                    :loading="loadingDirectoryCarInteriorMateria"
                                                    :options="directoryCarInteriorMateria"
                                                    optionLabel="name" optionValue="id"
                                                    placeholder="Выберите материал салона" showClear filter
                                                    class="w-full"
                                            >
                                                <template v-if="user.access.auto === 2" #header>
                                                    <!--                          <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить" outlined-->
                                                    <!--                                  @click="isDirectoryCarInteriorMateriaAddDrawerOpen = true"/>-->
                                                </template>
                                            </Select>
                                        </div>
                                        <!--  Цвет салона -->
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Цвет
                                                салона</label>
                                            <Select v-model="state.characteristic_directory_car_colors_id"
                                                    :loading="loadingDirectoryCarColors"
                                                    :options="directoryCarColors"
                                                    optionLabel="name" optionValue="id" placeholder="Выберите цвет"
                                                    showClear filter
                                                    class="w-full"
                                            >
                                                <template v-if="user.access.auto === 2" #header>
                                                    <Button class="mt-4 ml-4" type="button" icon="pi pi-plus"
                                                            label="Добавить" outlined
                                                            @click="isDirectoryCarCharacteristicColorsAddDrawerOpen = true"/>
                                                </template>
                                            </Select>
                                        </div>
                                        <!-- Доп параметр 1 -->
                                        <div>
                                            <label for="characteristic_additional_parameter_1"
                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Доп
                                                параметр 1</label>
                                            <InputText id="characteristic_additional_parameter_1"
                                                       v-model="state.characteristic_additional_parameter_1" fluid/>
                                        </div>
                                        <!-- Доп параметр 2 -->
                                        <div>
                                            <label for="characteristic_additional_parameter_2"
                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Доп
                                                параметр 2</label>
                                            <InputText id="characteristic_additional_parameter_2"
                                                       v-model="state.characteristic_additional_parameter_2" fluid/>
                                        </div>
                                        <!-- Доп параметр 3 -->
                                        <div>
                                            <label for="characteristic_additional_parameter_3"
                                                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Доп
                                                параметр 3</label>
                                            <InputText id="characteristic_additional_parameter_3"
                                                       v-model="state.characteristic_additional_parameter_3" fluid/>
                                        </div>
                                    </div>
                                </Panel>

                                <div v-if="!loadingBooking" class="flex flex-col sm:flex-row gap-4">
                                    <span>Активный договор проката:</span>
                                    <div v-if="booking" class="flex flex-col gap-1">
                                        <div>
                                            <span class="font-bold">Договор:</span>
                                            <span class="text-blue-700 cursor-pointer" @click="openBooking(booking.id)">
                                                №{{booking.id}} от {{moment(booking.start_date).format('DD.MM.YYYY')}}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="font-bold">У кого: </span>
                                            <span class="text-blue-700 cursor-pointer" @click="openClient(booking.client_id)">
                                                {{booking.fio}}
                                            </span>
                                            <span class="font-bold">
                                                , {{booking.phone}}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="font-bold">Возврат: {{moment(booking.end_date).format('DD.MM.YYYY HH:mm')}}</span>
                                        </div>
                                    </div>
                                    <span v-else>отсутствует</span>
                                </div>

                                <Divider type="dashed"/>

                                <!-- Активен? -->
                                <div class="flex items-center">
                                    <input id="active" type="checkbox"
                                           v-model="state.active"
                                           class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"/>
                                    <label for="active"
                                           class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                        Активен?
                                    </label>
                                </div>
                            </div>
                        </TabPanel>
                        <TabPanel value="1">
                            <div class="grid gap-4 sm:grid-cols-1">
                                <!-- ПТС. Серия -->
                                <div>
                                    <label for="pts_series"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Серия</label>
                                    <InputText id="pts_series" v-model="state.pts_series" fluid/>
                                </div>
                                <!-- ПТС. Номер -->
                                <div>
                                    <label for="pts_number"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Номер</label>
                                    <InputText id="pts_number" v-model="state.pts_number" fluid/>
                                </div>
                                <!-- ПТС. Кем выдан -->
                                <div>
                                    <label for="pts_issued_by_who"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Кем
                                        выдан</label>
                                    <InputText id="pts_issued_by_who" v-model="state.pts_issued_by_who" fluid/>
                                </div>
                                <!-- ПТС. Дата выдачи -->
                                <div>
                                    <label for="pts_issued_date"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата
                                        выдачи</label>
                                    <DatePickerWithMask :value="state.pts_issued_date" :key="state.pts_issued_date"
                                                        @onChange="e => state.pts_issued_date = e"/>
                                </div>
                                <!-- ПТС. VIN -->
                                <div>
                                    <label for="pts_vin"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">VIN</label>
                                    <InputText id="pts_vin" v-model="state.pts_vin" fluid/>
                                </div>
                                <!-- ПТС. Номер кузова -->
                                <div>
                                    <label for="pts_body_number"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Номер
                                        кузова</label>
                                    <InputText id="pts_body_number" v-model="state.pts_body_number" fluid/>
                                </div>
                                <!-- ПТС. Дата покупки -->
                                <div>
                                    <label for="pts_purchase_date"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата
                                        покупки</label>
                                    <DatePickerWithMask :value="state.pts_purchase_date" :key="state.pts_purchase_date"
                                                        @onChange="e => state.pts_purchase_date = e"/>
                                </div>
                                <!-- ПТС. Стоимость оценки авто -->
                                <div>
                                    <label for="pts_cost_assessment"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Стоимость
                                        оценки авто</label>
                                    <InputText id="pts_cost_assessment" v-model="state.pts_cost_assessment" fluid/>
                                </div>
                                <!-- ПТС. Стоимость покупки авто -->
                                <div>
                                    <label for="pts_cost_purchase"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Стоимость
                                        покупки авто</label>
                                    <InputText id="pts_cost_purchase" v-model="state.pts_cost_purchase" fluid/>
                                </div>
                                <!-- ПТС Форма собственности -->
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Форма
                                        собственности</label>
                                    <Select v-model="state.pts_directory_car_pts_ownership_id"
                                            :loading="loadingDirectoryCarPtsOwnership"
                                            :options="directoryCarPtsOwnership"
                                            optionLabel="name"
                                            optionValue="id" filter class="w-full">
                                        <template v-if="user.access.auto === 2" #header>
                                            <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить"
                                                    outlined
                                                    @click="isDirectoryCarPtsOwnershipAddDrawerOpen = true"/>
                                        </template>
                                    </Select>
                                </div>
                                <!-- ПТС Залогодержатель -->
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Залогодержатель</label>
                                    <Select v-model="state.pts_legal_person_id" :loading="loadingLegalPersons"
                                            :options="legalPersons"
                                            optionLabel="name"
                                            optionValue="id" filter class="w-full">
                                    </Select>
                                </div>
                                <Divider type="dashed"/>
                                <!-- ПТС. Файлы -->
                                <FileGallery :items="state.pts_files"
                                             @onSelect="e => state.pts_upload_files = e.files"/>
                            </div>
                        </TabPanel>
                        <TabPanel value="2">
                            <div class="grid gap-4 sm:grid-cols-1">
                                <!-- СТС Собственник-клиент -->
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Собственник-клиент</label>
                                    <Select v-model="state.sts_client_id" :loading="loadingClients"
                                            :options="clients"
                                            optionLabel="name"
                                            optionValue="id" filter class="w-full">
                                    </Select>
                                </div>
                                <!-- СТС Собственник-компания -->
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Собственник-компания</label>
                                    <Select v-model="state.sts_legal_person_id" :loading="loadingLegalPersons"
                                            :options="legalPersons"
                                            optionLabel="name"
                                            optionValue="id" filter class="w-full">
                                    </Select>
                                </div>
                                <!-- СТС. Серия -->
                                <div>
                                    <label for="sts_series"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Серия</label>
                                    <InputText id="sts_series" v-model="state.sts_series" fluid/>
                                </div>
                                <!-- СТС. Номер -->
                                <div>
                                    <label for="sts_number"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Номер</label>
                                    <InputText id="sts_number" v-model="state.sts_number" fluid/>
                                </div>
                                <!-- СТС. Кем выдан -->
                                <div>
                                    <label for="sts_issued_by_who"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Кем
                                        выдан</label>
                                    <InputText id="sts_issued_by_who" v-model="state.sts_issued_by_who" fluid/>
                                </div>
                                <!-- СТС. Дата выдачи -->
                                <div>
                                    <label for="sts_issued_date"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата
                                        выдачи</label>
                                    <DatePickerWithMask :value="state.sts_issued_date" :key="state.sts_issued_date"
                                                        @onChange="e => state.sts_issued_date = e"/>
                                </div>
                                <!-- СТС. Дата окончания -->
                                <div>
                                    <label for="sts_expire_date"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Дата
                                        окончания</label>
                                    <DatePickerWithMask :value="state.sts_expire_date" :key="state.sts_expire_date"
                                                        @onChange="e => state.sts_expire_date = e"/>
                                </div>
                                <Divider type="dashed"/>
                                <!-- СТС.. Файлы -->
                                <FileGallery :items="state.sts_files"
                                             @onSelect="e => state.sts_upload_files = e.files"/>
                            </div>
                        </TabPanel>
                        <TabPanel value="3">
                            <div class="grid gap-4 sm:grid-cols-1">
                                <!-- Размер колес -->
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Размер
                                        колес</label>
                                    <Select v-model="state.to_directory_car_wheel_size_id"
                                            :loading="loadingDirectoryCarWheelSize"
                                            :options="directoryCarWheelSize"
                                            optionLabel="name"
                                            optionValue="id" filter class="w-full">
                                        <template v-if="user.access.auto === 2" #header>
                                            <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить"
                                                    outlined
                                                    @click="isDirectoryCarWheelSizeAddDrawerOpen = true"/>
                                        </template>
                                    </Select>
                                </div>
                                <!-- Резина -->
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Резина</label>
                                    <Select v-model="state.to_directory_car_tires_type_id"
                                            :loading="loadingDirectoryCarTiresType"
                                            :options="directoryCarTiresType"
                                            optionLabel="name"
                                            optionValue="id" filter class="w-full">
                                        <template v-if="user.access.auto === 2" #header>
                                            <Button class="mt-4 ml-4" type="button" icon="pi pi-plus" label="Добавить"
                                                    outlined
                                                    @click="isDirectoryCarTiresTypeAddDrawerOpen = true"/>
                                        </template>
                                    </Select>
                                </div>
                                <!-- Код радио -->
                                <div>
                                    <label for="to_radio_code"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Код
                                        радио</label>
                                    <InputText id="to_radio_code" v-model="state.to_radio_code" fluid/>
                                </div>
                                <!-- Секретка -->
                                <div>
                                    <label for="to_secret"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Секретка</label>
                                    <InputText id="to_secret" v-model="state.to_secret" fluid/>
                                </div>
                                <!-- Место установки маяка 1 -->
                                <div>
                                    <label for="to_beacon_place_1"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Место
                                        установки маяка 1</label>
                                    <InputText id="to_beacon_place_1" v-model="state.to_beacon_place_1" fluid/>
                                </div>
                                <!-- Место установки маяка 2 -->
                                <div>
                                    <label for="to_beacon_place_2"
                                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Место
                                        установки маяка 2</label>
                                    <InputText id="to_beacon_place_2" v-model="state.to_beacon_place_2" fluid/>
                                </div>
                            </div>
                        </TabPanel>
                        <TabPanel value="4">
                            <div class="grid gap-4 sm:grid-cols-1">
                                <!-- Фото -->
                                <FileGallery :items="state.photo_files" accept="image/*"
                                             @onSelect="e => state.photo_upload_files = e.files"/>
                            </div>
                        </TabPanel>
                        <TabPanel value="5">
                            <!-- Прочие файлы -->
                            <FileGallery :items="state.other_files"
                                         @onSelect="e => state.other_upload_files = e.files"/>
                        </TabPanel>
                    </TabPanels>
                    <Divider v-if="user.access.auto === 2" type="dashed"/>
                    <p v-for="error of v$.$errors" :key="error.$uid" class="text-red-500">
                        {{ error.$message }}
                    </p>
                    <div v-if="user.access.auto === 2 && state.archive === 0">
                        <Button type="submit" icon="pi pi-save" label="Сохранить" :loading="sending" outlined/>
                        <Button icon="pi pi-trash" label="В архив" class="ml-4" severity="secondary" :loading="sending"
                                @click="emit('onArchive');" outlined/>
                        <Button v-if="user.id === 1" icon="pi pi-trash" label="Удалить" class="ml-4" severity="danger"
                                :loading="sending"
                                @click="emit('onDelete');" outlined/>
                    </div>
                    <div v-if="state.archive === 1">
                        <Tag severity="warn" value="Авто находится в архиве."/>
                        <Button v-if="user.id === 1" icon="pi pi-trash" label="Удалить" class="ml-4" severity="danger"
                                :loading="sending"
                                @click="emit('onDelete');" outlined/>
                    </div>
                </form>
            </Tabs>
        </template>
    </Card>

    <AddClassDrawer
        :visible="isDirectoryClassesAddDrawerOpen"
        @onAdd="onDirectoryClassAdd" @onClose="isDirectoryClassesAddDrawerOpen = false"
    />
    <AddDirectoryDrawer
        title="Добавление марки авто" directory="directory_car_brands"
        :visible="isDirectoryBrandsAddDrawerOpen"
        @onAdd="onDirectoryBrandAdd" @onClose="isDirectoryBrandsAddDrawerOpen = false"
    />
    <AddModelDrawer
        :brand="state.directory_car_brands_id" :visible="isDirectoryModelsAddDrawerOpen"
        @onAdd="onDirectoryModelAdd" @onClose="isDirectoryModelsAddDrawerOpen = false"
    />
    <AddGenerationDrawer
        :model="state.directory_car_models_id"
        :visible="isDirectoryGenerationsAddDrawerOpen"
        @onAdd="onDirectoryGenerationAdd" @onClose="isDirectoryGenerationsAddDrawerOpen = false"
    />
    <AddConfigurationDrawer
        :model="state.directory_car_models_id"
        :visible="isDirectoryConfigurationsAddDrawerOpen"
        @onAdd="onDirectoryConfigurationAdd" @onClose="isDirectoryConfigurationsAddDrawerOpen = false"
    />
    <AddDirectoryDrawer
        title="Добавление типа кузова" directory="directory_car_bodies"
        :visible="isDirectoryCarBodiesAddDrawerOpen"
        @onAdd="onDirectoryCarBodiesAdd" @onClose="isDirectoryCarBodiesAddDrawerOpen = false"
    />
    <AddDirectoryDrawer
        title="Добавление типа топлива" directory="directory_car_fuel_types"
        :visible="isDirectoryCarFuelTypesAddDrawerOpen"
        @onAdd="onDirectoryCarFuelTypesAdd" @onClose="isDirectoryCarFuelTypesAddDrawerOpen = false"
    />
    <AddDirectoryDrawer
        title="Добавление привод" directory="directory_car_wheel_drive"
        :visible="isDirectoryCarWheelDriveAddDrawerOpen"
        @onAdd="onDirectoryCarWheelDriveAdd" @onClose="isDirectoryCarWheelDriveAddDrawerOpen = false"
    />
    <AddDirectoryDrawer
        title="Добавление трансмиссии" directory="directory_car_transmissions"
        :visible="isDirectoryCarTransmissionsAddDrawerOpen"
        @onAdd="onDirectoryCarTransmissionsAdd" @onClose="isDirectoryCarTransmissionsAddDrawerOpen = false"
    />
    <AddDirectoryDrawer
        title="Добавление цвета кузова" directory="directory_car_colors"
        :visible="isDirectoryCarColorsAddDrawerOpen"
        @onAdd="onDirectoryCarColorsAdd" @onClose="isDirectoryCarColorsAddDrawerOpen = false"
    />
    <AddDirectoryDrawer
        title="Добавление цвета салона" directory="directory_car_colors"
        :visible="isDirectoryCarCharacteristicColorsAddDrawerOpen"
        @onAdd="onDirectoryCarCharacteristicColorsAdd"
        @onClose="isDirectoryCarCharacteristicColorsAddDrawerOpen = false"
    />
    <AddDirectoryDrawer
        title="Добавление формы собственности ПТС" directory="directory_car_pts_ownership"
        :visible="isDirectoryCarPtsOwnershipAddDrawerOpen"
        @onAdd="onDirectoryCarPtsOwnershipAdd" @onClose="isDirectoryCarPtsOwnershipAddDrawerOpen = false"
    />
    <AddDirectoryDrawer
        title="Добавление типа резины" directory="directory_car_tires_type"
        :visible="isDirectoryCarTiresTypeAddDrawerOpen"
        @onAdd="onDirectoryCarTiresTypeAdd" @onClose="isDirectoryCarTiresTypeAddDrawerOpen = false"
    />
    <AddDirectoryDrawer
        title="Добавление размера колес" directory="directory_car_wheel_size"
        :visible="isDirectoryCarWheelSizeAddDrawerOpen"
        @onAdd="onDirectoryCarWheelSizeAdd" @onClose="isDirectoryCarWheelSizeAddDrawerOpen = false"
    />
</template>

<style scoped>
.checkbox {
    @apply w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600;
}
</style>