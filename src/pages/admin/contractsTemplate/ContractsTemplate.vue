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
                value: '#booking_id#'
            },
            {
                label: 'дата начала аренды',
                value: '#booking_start_d#'
            },
            {
                label: 'Тариф за сутки',
                value: '#rental_rate#'
            },
            {
                label: 'Срок аренды',
                value: '#rental_days#'
            },
            {
                label: 'Размер депозита',
                value: '#deposit_booking#'
            },
            {
                label: 'Территория эксплуатации',
                value: '#booking_territory#'
            },
        ]
    },
    {
        label: 'атрибуты клиента',
        list: [
            {
                label: '',
                value: ''
            },
            {
                label: '',
                value: ''
            },
        ]
    },
    {
        label: 'атрибуты авто',
        list: [
            {
                label: '',
                value: ''
            },
            {
                label: '',
                value: ''
            },
        ]
    },
    {
        label: 'атрибуты фирмы-арендодателя',
        list: [
            {
                label: '',
                value: ''
            },
            {
                label: '',
                value: ''
            },
        ]
    },
    {
        label: 'атрибуты сотрудника',
        list: [
            {
                label: '',
                value: ''
            },
            {
                label: '',
                value: ''
            },
        ]
    }
]);

async function fetchBookingContract() {
    const result = await ContractsTemplateService.getBookingContractTemplate(state.companyId);

    if(result){
        state.bookingContractTemplate = result;
        state.bookingContractTemplate.name = result.file_name + "." + result.file_ext;
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