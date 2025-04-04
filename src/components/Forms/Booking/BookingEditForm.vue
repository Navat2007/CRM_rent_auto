<script setup>
import {computed, onMounted, reactive, ref, unref, watch, watchEffect} from "vue";
import {useAuthStore} from "@stores";
import {useVuelidate} from '@vuelidate/core';
import {helpers, minValue, required} from '@vuelidate/validators';
import moment from "moment";
import PizZip from "pizzip";
import Docxtemplater from "docxtemplater";
import {saveAs} from "file-saver";
import {isNumber} from "lodash";
import Select from "primevue/select";
import Divider from "primevue/divider";

import AutoService from "@services/AutoService.js";
import ClientsService from "@services/ClientsService.js";
import Car from "@components/Cards/Car.vue";
import DateTimePickerWithMask from "@components/Inputs/DateTimePickerWithMask.vue";
import Client from "@components/Cards/Client.vue";
import DirectoryService from "@services/DirectoryService.js";
import AddDirectoryDrawer from "@components/Drawers/Directory/AddDirectoryDrawer.vue";
import SearchAddress from "@components/Inputs/SearchAddress.vue";
import PopUpBookingOperation from "@components/Popups/PopUpBookingOperation.vue";
import BookingOperationsService from "@services/BookingOperationsService.js";
import BookingService from "@services/BookingService.js";
import AddClientDrawer from "@components/Drawers/Client/AddClientDrawer.vue";
import UserService from "@services/UserService.js";
import LegalPersonsService from "@services/LegalPersonsService.js";
import AddLegalPersonDrawer from "@components/Drawers/LegalPerson/AddLegalPersonDrawer.vue";
import AddEmployerDrawer from "@components/Drawers/Employer/AddEmployerDrawer.vue";
import ContractsTemplateService from "@services/ContractsTemplateService.js";

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
const emit = defineEmits(['onSubmit', 'onArchive', 'onDelete']);

const loadingCars = ref(true);
const cars = ref([]);
const currentCar = ref(null);
const currentCarClass = ref(null);

const loadingClients = ref(true);
const isClientAddDrawerOpen = ref(false);
const clients = ref([]);
const currentClient = ref(null);

const loadingEmployers = ref(true);
const isEmployerAddDrawerOpen = ref(false);
const employers = ref([]);

const loadingLegalPersons = ref(true);
const isLegalPersonAddDrawerOpen = ref(false);
const legalPersons = ref([]);
const currentLegalPerson = ref(null);

const loadingTerritoryUse = ref(true);
const isTerritoryUseDrawerOpen = ref(false);
const territories = ref([]);

const loadingCarClasses = ref(true);
const isCarClassesDrawerOpen = ref(false);
const carClasses = ref([]);

const loadingOperations = ref(true);
const isOperationPopUpOpen = ref(false);
const operationItem = ref(null);
const operationAddItem = ref(null);
const operations = ref([]);

const loadingOperationTypes = ref(true);
const isOperationTypesDrawerOpen = ref(false);
const operationTypes = ref([]);

const dialogVisible = ref(false);
const dialogHeader = ref('');
const dialogText = ref('');

const loadingTotals = ref(false);

const state = reactive({
    companyId: user.company_id,
    carId: 0,
    carClassId: 0,
    clientId: 0,
    employer_Id: 0,
    legal_person_Id: 0,
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
    accrued_total: props.item.accrued_total,
    paid_total: props.item.paid_total,
    balance: props.item.balance,
    bookingContractTemplate: null
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
        legal_person_Id: {
            required: helpers.withMessage("Нужно выбрать фирму-арендодателя", required),
            minValue: helpers.withMessage("Нужно выбрать фирму-арендодателя", minValue(1)),
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
        // mileage_end: {
        //     minValue: helpers.withMessage("Пробег на момент приема должен быть больше или равен пробегу на момент выдачи", value => {
        //         return value >= state.mileage_start;
        //     }),
        //     $lazy: true
        // },
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

async function fetchEmployers() {
    employers.value = (await UserService.getEmployersForBooking(user.company_id))
        .filter(item => item.status === "Активен")
        .sort((a, b) => a.full_name.localeCompare(b.full_name));
    
    loadingEmployers.value = false;
}

async function fetchLegalPersons() {
    legalPersons.value = (await LegalPersonsService.getLegalPersonsForBooking(user.company_id))
        .filter(item => item.status === "Активен")
        .filter(item => item.is_lessor === 1)
        .sort((a, b) => a.full_name.localeCompare(b.full_name));
    
    loadingLegalPersons.value = false;
}

async function fetchTerritoryUse() {
    territories.value = (await DirectoryService.getAll({
        directory: 'directory_territory_car_use',
        company_id: user.company_id
    })).filter(item => item.archive === "Активен");
    loadingTerritoryUse.value = false;
    
    state.directory_territory_car_use_id = props.item.directory_territory_car_use_id;
}

async function fetchCarClasses() {
    carClasses.value = (await DirectoryService.getAll({
        directory: 'directory_car_classes',
        company_id: user.company_id
    })).filter(item => item.archive === "Активен");
    loadingCarClasses.value = false;
}

async function fetchOperations() {
    operations.value = (await BookingOperationsService.getAll({
        id: props.item.id,
        company_id: user.company_id
    }));
    loadingOperations.value = false;
}

async function fetchOperationTypes() {
    const result = await DirectoryService.getAll({
        directory: 'directory_operation_types',
        company_id: user.company_id
    });
    
    operationTypes.value = result
        .filter(item => item.archive === "Активен");
    
    loadingOperationTypes.value = false;
}

async function fetchTotals() {
    const result = await BookingService.getTotals({
        id: props.item.id,
        company_id: user.company_id
    });
    
    state.accrued_total = result.accrued_total;
    state.paid_total = result.paid_total;
    state.balance = result.balance;
    
    loadingTotals.value = false;
}

async function fetchBookingContract() {
    const result = await ContractsTemplateService.getBookingContractTemplate(state.companyId);
    
    if (result) {
        state.bookingContractTemplate = result;
        state.bookingContractTemplate.name = result.file_name + "." + result.file_ext;
        state.bookingContractTemplate.full_url = import.meta.env.VITE_FILE_URL + result.file_url;
    }
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

async function onEmployerAdd(id) {
    loadingEmployers.value = true;
    await fetchEmployers();
    state.employer_Id = id;
    loadingEmployers.value = false;
}

async function onLegalPersonAdd(id) {
    loadingLegalPersons.value = true;
    await fetchLegalPersons();
    state.legal_person_Id = id;
    loadingLegalPersons.value = false;
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

const setTariffText = () => {
    state.rental_rate_text = '';
    
    currentCar.value.saved_price_periods.map(period => {
        if (state.rental_days >= period.days_from && state.rental_days <= period.days_until) {
            if (isNumber(period.price)) {
                state.rental_rate_text = period.name;
            }
        } else if (period.days_until === 0 && state.rental_days >= period.days_from) {
            state.rental_rate_text = period.name;
        } else if (period.days_from === 0 && state.rental_days <= period.days_from) {
            state.rental_rate_text = period.name;
        }
    })
}

const calculateRentalCost = () => {
    state.rental_cost = state.rental_rate * state.rental_days;
}

const handleAddOperationButtonClick = (template) => {
    if (!currentCar.value) {
        dialogHeader.value = "Ошибка";
        dialogText.value = "Нужно выбрать автомобиль";
        dialogVisible.value = true;
        return;
    }
    
    operationItem.value = null;
    let directory_operation_types_id = undefined;
    
    switch (template) {
        case 'pay_rent':
            directory_operation_types_id = operationTypes.value.find(item => item.used_for === 'pay_rent')?.id;
            
            operationAddItem.value = {
                directory_operation_types_id: directory_operation_types_id ? directory_operation_types_id : 0,
                period_from: state.start_date,
                period_to: state.end_date,
                directory_services_name: 'За прокат по договору №' + props.item.id,
                tariff: state.rental_rate,
                quantity: state.rental_days,
                accrued: state.rental_cost,
            };
            break;
        
        case 'pay_deposit':
            directory_operation_types_id = operationTypes.value.find(item => item.used_for === 'pay_deposit')?.id;
            
            operationAddItem.value = {
                directory_operation_types_id: directory_operation_types_id ? directory_operation_types_id : 0,
                period_from: state.start_date,
                period_to: state.end_date,
                directory_services_name: 'Залог',
                tariff: state.deposit,
                quantity: 1,
                accrued: state.deposit,
                paid: state.deposit
            };
            break;
        
        case 'return_deposit':
            directory_operation_types_id = operationTypes.value.find(item => item.used_for === 'return_deposit')?.id;
            
            operationAddItem.value = {
                directory_operation_types_id: directory_operation_types_id ? directory_operation_types_id : 0,
                period_from: state.start_date,
                period_to: state.end_date,
                directory_services_name: 'Возврат залога',
                tariff: state.deposit,
                quantity: 1,
                accrued: state.deposit,
                paid: state.deposit
            };
            break;
        
        case 'pay_over_mileage':
            directory_operation_types_id = operationTypes.value.find(item => item.used_for === 'pay_over_mileage')?.id;
            
            operationAddItem.value = {
                directory_operation_types_id: directory_operation_types_id ? directory_operation_types_id : 0,
                period_from: state.start_date,
                period_to: state.end_date,
                directory_services_name: 'Оплата перепробега',
                tariff: parseFloat(currentCarClass.value.cost_extra_mileage),
                quantity: over_mileage.value,
                accrued: over_mileage_cost.value,
                paid: over_mileage_cost.value
            };
            break;
        
        default:
            operationAddItem.value = null;
            break;
    }
    
    isOperationPopUpOpen.value = true;
}

const handleEditOperationButtonClick = (item) => {
    if (!currentCar.value) {
        dialogHeader.value = "Ошибка";
        dialogText.value = "Нужно выбрать автомобиль";
        dialogVisible.value = true;
        return;
    }
    
    operationItem.value = item.data;
    isOperationPopUpOpen.value = true;
}

const onOperationDone = () => {
    isOperationPopUpOpen.value = false;
    fetchOperations();
    fetchTotals();
}

const rowClass = (data) => {
    return [
        {'bg-green-200': parseInt(data.is_income) === 1},
        {'bg-red-200': parseInt(data.is_income) === 0},
    ];
};

const downloadContract = async () => {
    const replaceTags = (content) => {
        let shortFIO = currentClient.value.second_name;
        
        if (currentClient.value.first_name && currentClient.value.first_name.length > 0) {
            shortFIO += ' ' + currentClient.value.first_name.charAt(0) + '.';
        }
        if (currentClient.value.middle_name && currentClient.value.middle_name.length > 0) {
            shortFIO += ' ' + currentClient.value.middle_name.charAt(0) + '.';
        }
        
        const legalPerson = legalPersons.value.find(item => item.id === state.legal_person_Id);
        
        const employer = employers.value.find(item => item.id === state.employer_Id);
        
        let employerShortFIO = "";
        
        if (employer) {
            employerShortFIO = employer.second_name;
            
            if (employer.first_name && employer.first_name.length > 0) {
                employerShortFIO += ' ' + employer.first_name.charAt(0) + '.';
            }
            
            if (employer.middle_name && employer.middle_name.length > 0) {
                employerShortFIO += ' ' + employer.middle_name.charAt(0) + '.';
            }
        }
        
        return {
            'booking_id': props.item.id,
            'booking_start_d': state.start_date,
            'rental_rate': state.rental_rate,
            'rental_days': state.rental_days,
            'deposit_booking': state.deposit,
            'booking_territory': territories.value.find(item => item.id === state.directory_territory_car_use_id).name,
            
            'client_id': currentClient.value.id,
            'client_first': currentClient.value.second_name,
            'client_second': currentClient.value.first_name,
            'client_middle': currentClient.value.middle_name,
            'client_fio_short': shortFIO,
            'client_bd': currentClient.value.birth_date && currentClient.value.birth_date !== '' ? moment(currentClient.value.birth_date).format('DD.MM.YYYY') : "",
            'client_registration': currentClient.value.registration_address,
            'client_fact': currentClient.value.fact_address,
            'client_ser_num': currentClient.value.series_number,
            'client_issued_by': currentClient.value.issued_by_who,
            'client_issued_date': currentClient.value.issued_date && currentClient.value.issued_date !== '' ? moment(currentClient.value.issued_date).format('DD.MM.YYYY') : "",
            'client_phone': currentClient.value.phone,
            
            'cars_body_number': currentCar.value.body_number,
            'cars_tires_type': currentCar.value.tires_type,
            'cars_brand': currentCar.value.brand,
            'cars_model': currentCar.value.model,
            'cars_state_number': currentCar.value.state_number,
            'cars_vin': currentCar.value.vin,
            'cars_color': currentCar.value.color,
            'cars_year': currentCar.value.release_year,
            'sts_series': currentCar.value.sts_series,
            'sts_number': currentCar.value.sts_number,
            'fuel_tank': currentCar.value.fuel_tank_capacity,
            'car_fuel_type': currentCar.value.fuel_type,
            'cost_extra_mileage': currentCar.value.cost_extra_mileage,
            'cost_assessment': currentCar.value.cost_assessment,
            
            'firm_full_name': legalPerson ? legalPerson.full_name : "",
            'firm_ogrn': legalPerson ? legalPerson.ogrn : "",
            'firm_short_name': legalPerson ? legalPerson.short_name : "",
            'firm_registration_date': legalPerson ? (legalPerson.registration_date ? moment(legalPerson.registration_date).format('DD.MM.YYYY') : "") : "",
            'firm_ogrnip': legalPerson ? legalPerson.ogrnip : "",
            'firm_inn': legalPerson ? legalPerson.inn : "",
            'firm_kpp': legalPerson ? legalPerson.kpp : "",
            'firm_legal_address': legalPerson ? legalPerson.legal_address : "",
            'firm_index_legal_address': legalPerson ? legalPerson.index_legal_address : "",
            'firm_address': legalPerson ? legalPerson.address : "",
            'firm_index_address': legalPerson ? legalPerson.index_address : "",
            'firm_manager_position': legalPerson ? legalPerson.manager_position : "",
            'firm_manager_fio': legalPerson ? legalPerson.manager_fio : "",
            'firm_contact_fio': legalPerson ? legalPerson.contact_fio : "",
            'firm_contact_phone': legalPerson ? legalPerson.contact_phone : "",
            'firm_bookkeeper_fio': legalPerson ? legalPerson.bookkeeper_fio : "",
            'firm_rs_legal_person': legalPerson ? legalPerson.rs_legal_person : "",
            'firm_bank': legalPerson ? legalPerson.bank : "",
            'firm_bank_bik': legalPerson ? legalPerson.bank_bik : "",
            'firm_bank_ks': legalPerson ? legalPerson.bank_ks : "",
            
            'employee_first': employer ? employer.first_name : "",
            'employee_second': employer ? employer.second_name : "",
            'employee_middle': employer ? employer.middle_name : "",
            'employee_fio_short': employer ? shortFIO : "",
            'employee_acts_on_basis': employer ? employer.acts_on_basis : "",
        }
    }
    
    if (!state.bookingContractTemplate) {
        dialogHeader.value = "Ошибка";
        dialogText.value = "Договор проката не прикреплен";
        dialogVisible.value = true;
        return;
    }
    
    try {
        const response = await fetch(state.bookingContractTemplate.full_url);
        const arrayBuffer = await response.arrayBuffer();
        
        const zip = new PizZip(arrayBuffer);
        const xmlContent = zip.files['word/document.xml'].asText();
        
        console.log('Найденные теги:', xmlContent.match(/#\w+#/g));
        
        zip.file('word/document.xml',);
        
        const doc = new Docxtemplater(zip, {
            paragraphLoop: true,
            linebreaks: true
        });
        
        doc.render(replaceTags());
        
        const out = doc.getZip().generate({
            type: 'blob',
            mimeType: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        });
        
        saveAs(out, state.bookingContractTemplate.file_name + "." + state.bookingContractTemplate.file_ext);
    } catch (error) {
        dialogHeader.value = "Ошибка обработки .docx";
        dialogText.value = error;
        dialogVisible.value = true;
    }
}

watchEffect(() => {
    if (state.carId !== 0) {
        currentCar.value = cars.value.find(car => car.id === state.carId);
        
        if (currentCar.value) {
            currentCarClass.value = carClasses.value.find(carClass => carClass.id === currentCar.value.class_id);
            state.carClassId = currentCar.value.class_id;
            
            calculateTariff();
        }
    } else {
        currentCar.value = null;
        state.carClassId = null;
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

onMounted(async () => {
    await fetchCars();
    await fetchClients();
    await fetchEmployers();
    await fetchLegalPersons();
    await fetchTerritoryUse();
    await fetchCarClasses();
    await fetchOperations();
    await fetchOperationTypes();
    await fetchTotals();
    await fetchBookingContract();
    
    state.carId = props.item.carId;
    state.clientId = props.item.clientId;
    
    if (props.item.userId)
        state.employer_Id = props.item.userId;
    
    if (props.item.legal_person_Id)
        state.legal_person_Id = props.item.legal_person_Id;
    
    setTimeout(() => {
        state.rental_rate = props.item.rental_rate
    }, 10);
});
</script>

<template>
    <div class="flex flex-wrap gap-4 mb-4">
        <Button icon="pi pi-download" label="Скачать договор" :loading="sending" outlined
                @click="downloadContract"/>
    </div>
    <Card class="xl:w-8/12 w-full">
        <template #title>Редактирование договора проката</template>
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
                                                        {{slotProps.placeholder}}
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
                                                            <span>Номер: {{slotProps.option.state_number}} </span>
                                                            <span>Марка: {{slotProps.option.brand}}</span>
                                                            <span>Модель {{slotProps.option.model}}</span>
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
                                    <!-- Фирма-арендодатель -->
                                    <div>
                                        <label for="legal_person"
                                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Фирма-арендодатель*</label>
                                        <Select v-model="state.legal_person_Id" :loading="loadingLegalPersons"
                                                :options="legalPersons"
                                                optionLabel="full_name"
                                                optionValue="id" placeholder="Выберите фирму" showClear filter
                                                class="w-full"
                                                :pt="{label: 'text-sm text-wrap'}"
                                        >
                                            <template v-if="user.access.clients === 2" #header>
                                                <Button class="mt-4 ml-4" type="button" icon="pi pi-plus"
                                                        label="Добавить"
                                                        outlined
                                                        @click="isLegalPersonAddDrawerOpen = true"/>
                                            </template>
                                        </Select>
                                    </div>
                                    <!-- Сотрудник -->
                                    <div>
                                        <label for="user"
                                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Сотрудник</label>
                                        <Select v-model="state.employer_Id" :loading="loadingEmployers"
                                                :options="employers"
                                                optionLabel="full_name"
                                                optionValue="id" placeholder="Выберите сотрудника" showClear filter
                                                class="w-full"
                                                :pt="{label: 'text-sm text-wrap'}"
                                        >
                                            <template v-if="user.access.employers === 2" #header>
                                                <Button class="mt-4 ml-4" type="button" icon="pi pi-plus"
                                                        label="Добавить"
                                                        outlined
                                                        @click="isEmployerAddDrawerOpen = true"/>
                                            </template>
                                        </Select>
                                    </div>
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
                                                <span><i>{{state.rental_rate_text}}</i></span>
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
                                
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <!-- Начислено -->
                                    <div class="flex flex-col justify-end">
                                        <label
                                            for="accrued_total"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            Начислено
                                        </label>
                                        <InputNumber
                                            id="accrued_total"
                                            :model-value="state.accrued_total"
                                            disabled fluid
                                        />
                                    </div>
                                    <!-- Оплачено -->
                                    <div class="flex flex-col justify-end">
                                        <label
                                            for="paid_total"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            Оплачено
                                        </label>
                                        <InputNumber
                                            id="paid_total"
                                            :model-value="state.paid_total"
                                            disabled fluid
                                        />
                                    </div>
                                    <!-- Баланс -->
                                    <div class="flex flex-col justify-end">
                                        <label
                                            for="balance"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >
                                            Баланс
                                        </label>
                                        <InputNumber
                                            id="balance"
                                            :model-value="state.balance"
                                            disabled fluid
                                        />
                                    </div>
                                </div>
                                
                                <Divider type="dashed"/>
                                
                                <div class="">
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <Button class="mb-2" type="button" icon="pi pi-plus" label="Добавить" outlined
                                                @click="handleAddOperationButtonClick"/>
                                        <Button class="mb-2" type="button" label="Оплата аренды" outlined
                                                @click="() => {
                                                    handleAddOperationButtonClick('pay_rent')
                                                }"/>
                                        <Button class="mb-2" type="button" label="Оплата залога" outlined
                                                @click="() => {
                                                    handleAddOperationButtonClick('pay_deposit')
                                                }"/>
                                        <Button class="mb-2" type="button" label="Возврат залога" outlined
                                                @click="() => {
                                                    handleAddOperationButtonClick('return_deposit')
                                                }"/>
                                        <Button class="mb-2" type="button" label="Оплата перепробега" outlined
                                                @click="() => {
                                                    handleAddOperationButtonClick('pay_over_mileage')
                                                }"/>
                                    </div>
                                    <DataTable
                                        :value="operations" tableStyle="min-width: 50rem"
                                        :loading="loadingOperations" :rowClass="rowClass"
                                        @row-click="handleEditOperationButtonClick"
                                    >
                                        <Column field="operation_datetime" header="Дата" sortable>
                                            <template #body="{data}">
                                                {{
                                                    data.operation_datetime ? moment(data.operation_datetime).format('DD.MM.YYYY HH:mm') : null
                                                }}
                                            </template>
                                        </Column>
                                        <Column field="directory_operation_types_name" header="Операция"
                                                sortable></Column>
                                        <Column field="period_from" header="Период с" sortable>
                                            <template #body="{data}">
                                                {{
                                                    data.period_from ? moment(data.period_from).format('DD.MM.YYYY HH:mm') : null
                                                }}
                                            </template>
                                        </Column>
                                        <Column field="period_to" header="по дату" sortable>
                                            <template #body="{data}">
                                                {{
                                                    data.period_to ? moment(data.period_to).format('DD.MM.YYYY HH:mm') : null
                                                }}
                                            </template>
                                        </Column>
                                        <Column field="quantity" header="Кол-во" sortable></Column>
                                        <Column field="accrued" header="Начислено" sortable>
                                            <template #body="{data}">
                                                {{data.accrued && parseFloat(data.accrued) > 0 ? data.accrued : null}}
                                            </template>
                                        </Column>
                                        <Column field="paid" header="Оплачено" sortable>
                                            <template #body="{data}">
                                                {{data.paid && parseFloat(data.paid) > 0 ? data.paid : null}}
                                            </template>
                                        </Column>
                                        <Column field="directory_payment_types_name" header="Вид оплаты"
                                                sortable></Column>
                                        <Column field="directory_services_name" header="Услуга" sortable></Column>
                                    </DataTable>
                                </div>
                            </div>
                        </TabPanel>
                    </TabPanels>
                    <Divider v-if="user.access.booking === 2" type="dashed"/>
                    <p v-for="error of v$.$errors" :key="error.$uid" class="text-red-500 mb-4">
                        {{error.$message}}
                    </p>
                    <div v-if="user.access.booking === 2">
                        <Button type="submit" icon="pi pi-save" label="Сохранить" :loading="sending" outlined/>
                        <Button icon="pi pi-trash" label="В архив" class="ml-4" severity="secondary" :loading="sending"
                                @click="emit('onArchive');" outlined/>
                        <Button v-if="user.id === 1" icon="pi pi-trash" label="Удалить" class="ml-4" severity="danger"
                                :loading="sending"
                                @click="emit('onDelete');" outlined/>
                    </div>
                </form>
            </Tabs>
        </template>
    </Card>
    
    <PopUpBookingOperation
        :item="operationItem" :add-item="operationAddItem" :visible="isOperationPopUpOpen" :carState="state"
        @onClose="isOperationPopUpOpen = false" @onDone="onOperationDone"
    />
    <AddClientDrawer
        :visible="isClientAddDrawerOpen"
        @onAdd="onClientAdd" @onClose="isClientAddDrawerOpen = false"
    />
    <AddEmployerDrawer
        :visible="isEmployerAddDrawerOpen"
        @onAdd="onEmployerAdd" @onClose="isEmployerAddDrawerOpen = false"
    />
    <AddDirectoryDrawer
        title="Добавление территории использования" directory="directory_territory_car_use"
        :visible="isTerritoryUseDrawerOpen"
        @onAdd="onDirectoryTerritoryUseAdd" @onClose="isTerritoryUseDrawerOpen = false"
    />
    <AddLegalPersonDrawer
        :visible="isLegalPersonAddDrawerOpen"
        @onAdd="onLegalPersonAdd" @onClose="isLegalPersonAddDrawerOpen = false"
    />
    
    <Dialog v-model:visible="dialogVisible" modal dismissable-mask base-z-index="20000" :header="dialogHeader"
            :style="{ width: '50vw' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
        <p class="m-0">
            {{dialogText}}
        </p>
    </Dialog>
</template>