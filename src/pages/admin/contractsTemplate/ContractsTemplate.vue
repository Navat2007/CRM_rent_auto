<script setup>
import {useAuthStore} from "@stores";
import {onMounted, reactive, ref, unref} from "vue";
import useClipboard from "vue-clipboard3";
import {useToast} from "primevue/usetoast";

import {ContentCopyFilled} from "@vicons/material";
import {Icon} from "@vicons/utils";
import DocumentSelect from "@components/Inputs/DocumentSelect.vue";
import ContractsTemplateService from "@services/ContractsTemplateService.js";

const {user} = useAuthStore();
const {toClipboard} = useClipboard();
const toast = useToast();

const props = defineProps({
    sending: {
        type: Boolean,
        required: false,
        default: false
    },
});

const state = reactive({
    companyId: user.company_id,
    bookingContractTemplate: "",
    bookingContractTemplateFileToSave: null,
});
const loading = ref(true);

const tags = ref([
    {
        label: 'атрибуты проката',
        list: [
            {
                label: 'номер договора проката',
                value: '{booking_id}'
            },
            {
                label: 'дата начала аренды',
                value: '{booking_start_d}'
            },
            {
                label: 'Тариф за сутки',
                value: '{rental_rate}'
            },
            {
                label: 'Срок аренды',
                value: '{rental_days}'
            },
            {
                label: 'Размер депозита',
                value: '{deposit_booking}'
            },
            {
                label: 'Территория эксплуатации',
                value: '{booking_territory}'
            },
        ]
    },
    {
        label: 'атрибуты клиента',
        list: [
            {
                label: 'идентификатор клиента',
                value: '{client_id}'
            },
            {
                label: 'Фамилия клиента',
                value: '{client_first}'
            },
            {
                label: 'Имя клиента',
                value: '{client_second}'
            },
            {
                label: 'Отчество клиента',
                value: '{client_middle# '
            },
            {
                label: 'Фамилия И.О. клиента',
                value: '{client_fio_short}'
            },
            {
                label: 'Дата рождения',
                value: '{client_bd}'
            },
            {
                label: 'Адрес регистрации',
                value: '{client_registration}'
            },
            {
                label: 'Адрес фактический',
                value: '{client_fact}'
            },
            {
                label: 'Паспорт: серия и номер',
                value: '{client_ser_num}'
            },
            {
                label: 'Паспорт: Кем выдан',
                value: '{client_issued_by}'
            },
            {
                label: 'Паспорт: Дата выдачи',
                value: '{client_issued_date}'
            },
            {
                label: 'Телефон клиента',
                value: '{client_phone}'
            },
        ]
    },
    {
        label: 'атрибуты авто',
        list: [
            {
                label: 'ПТС Номер кузова ',
                value: '{cars_body_number}'
            },
            {
                label: 'Резина авто',
                value: '{car_tires_type}'
            },
            {
                label: 'Марка',
                value: '{car_brand}'
            },
            {
                label: 'модель',
                value: ' #car_model}'
            },
            {
                label: 'Рег. номер',
                value: '{car_state_number}'
            },
            {
                label: 'VIN',
                value: '{car_vin}'
            },
            {
                label: 'Цвет кузова',
                value: '{car_color}'
            },
            {
                label: 'Год выпуска',
                value: '{car_year}'
            },
            {
                label: 'СТС Серия',
                value: '{sts_series}'
            },
            {
                label: 'СТС Номер',
                value: '{sts_number}'
            },
            {
                label: 'Объем бака',
                value: '{fuel_tank}'
            },
            {
                label: 'Тип топлива',
                value: '{car_fuel_type}'
            },
            {
                label: 'Стоимость 1 км перепробега',
                value: '{cost_extra_mileage}'
            },
            {
                label: 'Стоимость ТС',
                value: '{cost_assessment}'
            },
        ]
    },
    {
        label: 'атрибуты фирмы-арендодателя',
        list: [
            {
                label: 'Полное наименование',
                value: '{firm_full_name}'
            },
            {
                label: 'ОГРН',
                value: '{firm_ogrn}'
            },
            {
                label: 'Краткое наименование',
                value: '{firm_short_name}'
            },
            {
                label: 'Дата регистрации',
                value: '{firm_registration_date}'
            },
            {
                label: 'ОГРНИП',
                value: '{firm_ogrnip}'
            },
            {
                label: 'ИНН',
                value: '{firm_inn}'
            },
            {
                label: 'КПП',
                value: '{firm_kpp}'
            },
            {
                label: 'Юридический адрес',
                value: '{firm_legal_address}'
            },
            {
                label: 'Индекс Юр адреса',
                value: '{firm_index_legal_address}'
            },
            {
                label: 'Почтовый адрес',
                value: '{firm_address}'
            },
            {
                label: 'Индекс Почтового адреса',
                value: '{firm_index_address}'
            },
            {
                label: 'Должность руководителя',
                value: '{firm_manager_position}'
            },
            {
                label: 'ФИО руководителя',
                value: '{firm_manager_fio}'
            },
            {
                label: 'ФИО контактного лица',
                value: '{firm_contact_fio}'
            },
            {
                label: 'телефон контактного лица',
                value: '{firm_contact_phone}'
            },
            {
                label: 'ФИО Бухгалтера',
                value: '{firm_bookkeeper_fio}'
            },
            {
                label: 'Расчетный счет юр лица',
                value: '{firm_rs_legal_person}'
            },
            {
                label: 'Банк',
                value: '{firm_bank}'
            },
            {
                label: 'БИК Банка',
                value: '{firm_bank_bik}'
            },
            {
                label: 'корреспондентский счет Банка',
                value: '{firm_bank_ks}'
            },
        ]
    },
    {
        label: 'атрибуты сотрудника',
        list: [
            {
                label: 'Фамилия сотрудника',
                value: '{employee_first}'
            },
            {
                label: 'Имя сотрудника',
                value: '{employee_second}'
            },
            {
                label: 'Отчество сотрудника',
                value: '{employee_middle}'
            },
            {
                label: 'Фамилия И.О. сотрудника',
                value: '{employee_fio_short}'
            },
            {
                label: 'Действует на основании',
                value: '{employee_acts_on_basis}'
            },
        ]
    }
]);

async function fetchBookingContract() {
    const result = await ContractsTemplateService.getBookingContractTemplate(state.companyId);

    if(result){
        state.bookingContractTemplate = result;
        state.bookingContractTemplate.name = result.file_name + "." + result.file_ext;
        state.bookingContractTemplate.full_url = import.meta.env.VITE_FILE_URL + result.file_url;
    }

    loading.value = false;
}

async function handleBookingContractTemplateAdd() {
    await ContractsTemplateService.addBookingContractTemplate({
        company_id: state.companyId,
        file: state.bookingContractTemplateFileToSave
    });

    toast.add({ severity: 'success', summary: 'Файл добавлен!', detail: state.bookingContractTemplateFileToSave.name, group: 'br', life: 3000 });

    state.bookingContractTemplateFileToSave = null;

    await fetchBookingContract();
}

async function handleBookingContractTemplateDelete() {
    await ContractsTemplateService.deleteBookingContractTemplate(state.companyId);

    toast.add({ severity: 'success', summary: 'Файл удален!', detail: state.bookingContractTemplate.name, group: 'br', life: 3000 });

    state.bookingContractTemplate = null;
    state.bookingContractTemplateFileToSave = null;

    await fetchBookingContract();
}

onMounted(async () => {
    await fetchBookingContract();
});
</script>

<template>
    <Card v-if="!loading" class="xl:w-8/12 w-full">
        <template #title>Шаблоны договоров</template>
        <template #content>
            <div class="grid gap-4 sm:grid-cols-1">
                <!-- ContractTemplate -->
                <Fieldset legend="Договор проката">
                    <div class="flex flex-col gap-4">
                        <DocumentSelect
                            :value="state.bookingContractTemplate"
                            @onSelect="e => state.bookingContractTemplateFileToSave = e.files[0]"
                            @onSave="handleBookingContractTemplateAdd"
                            @onDelete="handleBookingContractTemplateDelete"
                        />
                    </div>
                </Fieldset>

                <!-- Tags -->
                <Fieldset legend="Персо-тэги">
                    <Accordion>
                        <AccordionPanel v-for="tag in tags" :key="tag.label" :value="tag.label">
                            <AccordionHeader>{{ tag.label }}</AccordionHeader>
                            <AccordionContent>
                                <div class="flex flex-col gap-4">
                                    <div class="flex justify-between gap-2" v-for="data in tag.list" :key="data.label">
                                        <div v-if="data.value !== ''" class="flex items-center gap-2">
                                            <span>{{ data.value }}</span>
                                            <Icon size="14" class="cursor-pointer" @click="() => {
                                                toClipboard(data.value);
                                                toast.add({ severity: 'success', summary: 'Скопировано!', detail: data.value, group: 'br', life: 3000 });
                                            }">
                                                <ContentCopyFilled/>
                                            </Icon>
                                        </div>
                                        <span>{{ data.label }}</span>
                                    </div>
                                </div>
                            </AccordionContent>
                        </AccordionPanel>
                    </Accordion>
                </Fieldset>
            </div>
        </template>
    </Card>

    <Toast position="bottom-right" group="br"/>
</template>