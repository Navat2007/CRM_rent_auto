<script setup>
import {onMounted, ref} from "vue";
import router from "@router";
import {FilterMatchMode, FilterOperator} from "@primevue/core/api";
import {useAuthStore} from "@stores";

import Table from "@components/Table/Table.vue";
import PageContainer from "@components/Containers/Admin/PageContainer.vue";
import AutoService from "@services/AutoService.js";
import DirectoryService from "@services/DirectoryService.js";
import {Icon} from "@vicons/utils";
import BodyStatus0 from "@assets/icons/body_status_3.svg";
import BodyStatus1 from "@assets/icons/body_status_1.svg";
import BodyStatus2 from "@assets/icons/body_status_2.svg";
import FuelStatus0 from "@assets/icons/fuel_level_1.svg";
import FuelStatus1 from "@assets/icons/fuel_level_2.svg";
import FuelStatus2 from "@assets/icons/fuel_level_3.svg";
import FuelStatus3 from "@assets/icons/fuel_level_4.svg";
import InteriorStatus0 from "@assets/icons/interior_status_2.svg";
import InteriorStatus1 from "@assets/icons/interior_status_1.svg";
import InteriorStatus2 from "@assets/icons/interior_status_3.svg";
import TrunkStatus0 from "@assets/icons/trunk_status_3.svg";
import TrunkStatus1 from "@assets/icons/trunk_status_1.svg";
import TrunkStatus2 from "@assets/icons/trunk_status_2.svg";
import NeedService0 from "@assets/icons/pass_TO_1.svg";
import NeedService1 from "@assets/icons/pass_TO_2.svg";
import Failure from "@assets/icons/has_comment_2.svg";
import CriticalFailure from "@assets/icons/has_comment_3.svg";
import DataTable from "primevue/datatable";
import moment from "moment/moment.js";

const {user} = useAuthStore();

const items = ref([]);
const models = ref([]);
const brands = ref([]);
const classes = ref([]);
const colors = ref([]);
const carStatuses = ref([]);
const conditions = ref([
    {label: 'Неизвестно', value: 0},
    {label: 'Чистый', value: 1},
    {label: 'Грязный', value: 2},
]);
const hasFailure = ref([
    {label: 'Нет', value: false},
    {label: 'Да', value: true},
]);
const needService = ref([
    {label: 'Нет', value: 0},
    {label: 'Да', value: 1},
]);
const fuelLevels = ref([
    {label: 'Неизвестно', value: 0},
    {label: 'Низкий уровень топлива', value: 1},
    {label: 'Средний уровень топлива', value: 2},
    {label: 'Полный бак', value: 3},
]);
const loading = ref(true);

const breadcrumbs = ref([
    {
        name: 'Авто',
        route: null
    },
]);
const statuses = ref(['Активен', 'В архиве']);
const filters = ref({
    id: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
    state_number: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
    brand: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
    model: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
    class: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
    color: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
    ['booking.fio']: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
    ['booking.phone']: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.CONTAINS}]},
    ['booking.start_date']: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.DATE_IS}]},
    ['booking.end_date']: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.DATE_IS}]},
    status: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
    archive: {operator: FilterOperator.OR, constraints: [{value: "Активен", matchMode: FilterMatchMode.EQUALS}]},
    body_condition: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
    interior_condition: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
    trunk_condition: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
    need_refuel: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
    need_service: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
    has_failure: {operator: FilterOperator.OR, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
});
const filterFields = ref([
    'id',
    'state_number',
    'brand',
    'model',
    'class',
    'color',
    'status',
    'body_condition',
    'interior_condition',
    'trunk_condition',
    'need_refuel',
    'need_service',
    'has_failure',
    'archive'
]);

const contextMenu = ref();
const contextMenuItems = ref([]);

const onRowRightClick = (event, place, current, data) => {
    switch (place) {
        case 'body_condition':
            contextMenuItems.value = [
                {
                    label: 'Неизвестно', icon: BodyStatus0, command: () => {
                        handleUpdateStatus(0, 'body_condition', current.value === 0, data)
                    }, selected: current.value === 0
                },
                {
                    label: 'Чистый', icon: BodyStatus1, command: () => {
                        handleUpdateStatus(1, 'body_condition', current.value === 1, data)
                    }, selected: current.value === 1
                },
                {
                    label: 'Грязный', icon: BodyStatus2, command: () => {
                        handleUpdateStatus(2, 'body_condition', current.value === 2, data)
                    }, selected: current.value === 2
                }
            ];
            break;
        case 'interior_condition':
            contextMenuItems.value = [
                {
                    label: 'Неизвестно', icon: InteriorStatus0, command: () => {
                        handleUpdateStatus(0, 'interior_condition', current.value === 0, data)
                    }, selected: current.value === 0
                },
                {
                    label: 'Чистый', icon: InteriorStatus1, command: () => {
                        handleUpdateStatus(1, 'interior_condition', current.value === 1, data)
                    }, selected: current.value === 1
                },
                {
                    label: 'Грязный', icon: InteriorStatus2, command: () => {
                        handleUpdateStatus(2, 'interior_condition', current.value === 2, data)
                    }, selected: current.value === 2
                }
            ];
            break;
        case 'trunk_condition':
            contextMenuItems.value = [
                {
                    label: 'Неизвестно', icon: TrunkStatus0, command: () => {
                        handleUpdateStatus(0, 'trunk_condition', current.value === 0, data)
                    }, selected: current.value === 0
                },
                {
                    label: 'Чистый', icon: TrunkStatus1, command: () => {
                        handleUpdateStatus(1, 'trunk_condition', current.value === 1, data)
                    }, selected: current.value === 1
                },
                {
                    label: 'Грязный', icon: TrunkStatus2, command: () => {
                        handleUpdateStatus(2, 'trunk_condition', current.value === 2, data)
                    }, selected: current.value === 2
                }
            ];
            break;
        case 'need_refuel':
            contextMenuItems.value = [
                {
                    label: 'Неизвестно', icon: FuelStatus0, command: () => {
                        handleUpdateStatus(0, 'need_refuel', current.value === 0, data)
                    }, selected: current.value === 0
                },
                {
                    label: 'Низкий уровень топлива', icon: FuelStatus1, command: () => {
                        handleUpdateStatus(1, 'need_refuel', current.value === 1, data)
                    }, selected: current.value === 1
                },
                {
                    label: 'Средний уровень топлива', icon: FuelStatus2, command: () => {
                        handleUpdateStatus(2, 'need_refuel', current.value === 2, data)
                    }, selected: current.value === 2
                },
                {
                    label: 'Полный бак', icon: FuelStatus3, command: () => {
                        handleUpdateStatus(3, 'need_refuel', current.value === 3, data)
                    }, selected: current.value === 3
                }
            ];
            break;
        case 'need_service':
            contextMenuItems.value = [
                {
                    label: 'Нужно обслужить', icon: NeedService1, command: () => {
                        handleUpdateStatus(1, 'need_service', current === 1, data)
                    }, selected: current === 1
                },
                {
                    label: 'Обслужен', icon: NeedService0, command: () => {
                        handleUpdateStatus(0, 'need_service', current === 0, data)
                    }, selected: current === 0
                },
            ];
            break;
    }

    contextMenu.value.show(event);
};

const handleAddButtonClick = (item) => {
    router.push('/Admin/auto/new');
}
const handleRowClick = (item) => {
    router.push('/Admin/auto/' + item.id);
}
const handleUpdateStatus = async (status, place, selected, data) => {
    if (!selected) {
        await AutoService.updateStatus({status, place, id: data.id});
        items.value.find(item => item.id === data.id)[place] = status;
    }
}

const getCarCondition = (condition, data,) => {
    switch (condition) {
        case 'body':
            switch (data.body_condition) {
                case 0:
                    return BodyStatus0;
                case 1:
                    return BodyStatus1;
                case 2:
                    return BodyStatus2;
            }
            break;
        case 'interior':
            switch (data.interior_condition) {
                case 0:
                    return InteriorStatus0;
                case 1:
                    return InteriorStatus1;
                case 2:
                    return InteriorStatus2;
            }
            break;
        case 'trunk':
            switch (data.trunk_condition) {
                case 0:
                    return TrunkStatus0;
                case 1:
                    return TrunkStatus1;
                case 2:
                    return TrunkStatus2;
            }
            break;
        case 'fuel':
            switch (data.need_refuel) {
                case 0:
                    return FuelStatus0;
                case 1:
                    return FuelStatus1;
                case 2:
                    return FuelStatus2;
                case 3:
                    return FuelStatus3;
            }
            break;
        case 'to':
            switch (data.need_service) {
                case 0:
                    return NeedService0;
                case 1:
                    return NeedService1;
            }
            break;
    }
};

const formatDate = (value, valueToUpdate) => {
    if (value.without_date)
        return '';

    return moment(value.booking[valueToUpdate]).format('DD.MM.YYYY HH:mm');
};


async function fetchData() {
    items.value = await AutoService.getAll({company_id: user.company_id});
    carStatuses.value = await DirectoryService.getAll({
        directory: 'directory_car_statuses',
        company_id: user.company_id
    });
    carStatuses.value = carStatuses.value.filter(item => item.archive === "Активен").map(item => item.name);
    models.value = await DirectoryService.getAll({directory: 'directory_car_models', company_id: user.company_id});
    models.value = models.value.filter(item => item.archive === "Активен").map(item => item.name);
    brands.value = await DirectoryService.getAll({directory: 'directory_car_brands', company_id: user.company_id});
    brands.value = brands.value.filter(item => item.archive === "Активен").map(item => item.name);
    classes.value = await DirectoryService.getAll({directory: 'directory_car_classes', company_id: user.company_id});
    classes.value = classes.value.filter(item => item.archive === "Активен").map(item => item.name);
    colors.value = await DirectoryService.getAll({directory: 'directory_car_colors', company_id: user.company_id});
    colors.value = colors.value.filter(item => item.archive === "Активен").map(item => item.name);

    items.value.map(item => {
        if (item.booking === null) {
            item.without_date = true;
            item.booking = {};
            item.booking.start_date = new Date('1900-01-01');
            item.booking.end_date = new Date('1900-01-01');
        }
        else {
            item.booking.start_date = new Date(item.booking.start_date);
            item.booking.end_date = new Date(item.booking.end_date);
            item.without_date = false;
        }
    });

    loading.value = false;
}

onMounted(() => {
    fetchData();
});
</script>

<template>
    <PageContainer :breadcrumbs="breadcrumbs">
        <Table
            title="Авто"
            :items="items" :pageSize="15"
            :filters="filters" :filterFields="filterFields"
            :loading="loading" @onRowClick="handleRowClick"
        >
            <template #columns>
                <Column field="id" header="ID" dataType="numeric" headerStyle="width: 7rem; min-width: 7rem;" sortable>
                    <template #filter="{ filterModel }">
                        <InputText v-model="filterModel.value" type="number" placeholder="Поиск по ID"/>
                    </template>
                </Column>
                <Column field="brand" header="Марка" sortable headerStyle="width: 15rem; min-width: 10rem;">
                    <template #filter="{ filterModel }">
                        <Dropdown v-model="filterModel.value" :options="brands" placeholder="Все"
                                  class="p-column-filter"
                                  showClear filter/>
                    </template>
                </Column>
                <Column field="model" header="Модель" sortable headerStyle="width: 15rem; min-width: 10rem;">
                    <template #filter="{ filterModel }">
                        <Dropdown v-model="filterModel.value" :options="models" placeholder="Все"
                                  class="p-column-filter"
                                  showClear filter/>
                    </template>
                </Column>
                <Column field="state_number" header="Гос номер" sortable headerStyle="width: 15rem; min-width: 10rem;">
                    <template #filter="{ filterModel }">
                        <InputText v-model="filterModel.value" type="text" placeholder="Поиск по гос. номеру"/>
                    </template>
                </Column>
                <Column field="status" header="Статус" sortable headerStyle="width: 10rem; min-width: 10rem;">
                    <template #filter="{ filterModel }">
                        <Dropdown v-model="filterModel.value" :options="carStatuses" placeholder="Все"
                                  class="p-column-filter"
                                  showClear/>
                    </template>
                </Column>
                <Column field="body_condition" headerStyle="max-width: 3.5rem; min-width: 3.5rem;">
                    <template #filterheader>
                        <label for="state_number"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Состояние
                            кузова</label>
                    </template>
                    <template #filter="{ filterModel }">
                        <Dropdown v-model="filterModel.value" :options="conditions" option-label="label"
                                  option-value="value"
                                  placeholder="Все" class="p-column-filter"
                                  showClear/>
                    </template>
                    <template #body="{data}" class="flex justify-center items-center">
                        <div
                            @contextmenu="onRowRightClick($event, 'body_condition', conditions.find(item => item.value === data.body_condition), data)"
                            class="flex items-center justify-center"
                        >
                            <Icon size="24"
                                  v-tooltip.top="conditions.find(item => item.value === data.body_condition)?.label">
                                <component :is="getCarCondition('body', data)"/>
                            </Icon>
                        </div>
                    </template>
                </Column>
                <Column field="interior_condition" headerStyle="max-width: 3.5rem; min-width: 3.5rem;">
                    <template #filterheader>
                        <label for="state_number"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Состояние
                            салона</label>
                    </template>
                    <template #filter="{ filterModel }">
                        <Dropdown v-model="filterModel.value" :options="conditions" option-label="label"
                                  option-value="value"
                                  placeholder="Все" class="p-column-filter"
                                  showClear/>
                    </template>
                    <template #body="{data}" class="flex justify-center items-center">
                        <div
                            @contextmenu="onRowRightClick($event, 'interior_condition', conditions.find(item => item.value === data.interior_condition), data)"
                            class="flex items-center justify-center"
                        >
                            <Icon size="24"
                                  v-tooltip.top="conditions.find(item => item.value === data.interior_condition)?.label">
                                <component :is="getCarCondition('interior', data)"/>
                            </Icon>
                        </div>
                    </template>
                </Column>
                <Column field="trunk_condition" headerStyle="max-width: 3.5rem; min-width: 3.5rem;">
                    <template #filterheader>
                        <label for="state_number"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Состояние
                            багажника</label>
                    </template>
                    <template #filter="{ filterModel }">
                        <Dropdown v-model="filterModel.value" :options="conditions" option-label="label"
                                  option-value="value"
                                  placeholder="Все" class="p-column-filter"
                                  showClear/>
                    </template>
                    <template #body="{data}" class="flex justify-center items-center">
                        <div
                            @contextmenu="onRowRightClick($event, 'trunk_condition', conditions.find(item => item.value === data.trunk_condition), data)"
                            class="flex items-center justify-center"
                        >
                            <Icon size="24"
                                  v-tooltip.top="conditions.find(item => item.value === data.trunk_condition)?.label">
                                <component :is="getCarCondition('trunk', data)"/>
                            </Icon>
                        </div>
                    </template>
                </Column>
                <Column field="need_refuel" headerStyle="max-width: 3.5rem; min-width: 3.5rem;">
                    <template #filterheader>
                        <label for="state_number"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Уровень
                            топлива</label>
                    </template>
                    <template #filter="{ filterModel }">
                        <Dropdown v-model="filterModel.value" :options="fuelLevels" option-label="label"
                                  option-value="value"
                                  placeholder="Все" class="p-column-filter"
                                  showClear/>
                    </template>
                    <template #body="{data}" class="flex justify-center items-center">
                        <div
                            @contextmenu="onRowRightClick($event, 'need_refuel', fuelLevels.find(item => item.value === data.need_refuel), data)"
                            class="flex items-center justify-center"
                        >
                            <Icon size="24"
                                  v-tooltip.top="fuelLevels.find(item => item.value === data.need_refuel)?.label">
                                <component :is="getCarCondition('fuel', data)"/>
                            </Icon>
                        </div>
                    </template>
                </Column>
                <Column field="need_service" headerStyle="max-width: 3.5rem; min-width: 3.5rem;">
                    <template #filterheader>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Нужно
                            обслужить?</label>
                    </template>
                    <template #filter="{ filterModel }">
                        <Dropdown v-model="filterModel.value" :options="needService" option-label="label"
                                  option-value="value"
                                  placeholder="Все" class="p-column-filter"
                                  showClear/>
                    </template>
                    <template #body="{data}" class="flex justify-center items-center">
                        <div
                            @contextmenu="onRowRightClick($event, 'need_service', data.need_service, data)"
                            class="flex items-center justify-center"
                        >
                            <Icon size="24" v-tooltip.top="data.need_service === 0 ? 'Обслужен' : 'Нужно обслужить'">
                                <component :is="getCarCondition('to', data)"/>
                            </Icon>
                        </div>
                    </template>
                </Column>
                <Column field="has_failure" header="Неисправность" headerStyle="width: 12rem; min-width: 12rem;">
                    <template #filterheader>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Есть
                            неисправность?</label>
                    </template>
                    <template #filter="{ filterModel }">
                        <Dropdown v-model="filterModel.value" :options="hasFailure" option-label="label"
                                  option-value="value"
                                  placeholder="Все" class="p-column-filter"
                                  showClear/>
                    </template>
                    <template #body="{data}">
                        <div class="grid grid-flow-row-dense grid-cols-5">
                            <Icon v-if="data.failure != ''" v-tooltip.top="{ value: data.failure, autoHide: false }"
                                  size="24">
                                <Failure/>
                            </Icon>
                            <Icon v-if="data.critical_failure != ''"
                                  v-tooltip.top="{ value: data.critical_failure, autoHide: false }" size="24">
                                <CriticalFailure/>
                            </Icon>
                        </div>
                    </template>
                </Column>
                <Column field="class" header="Класс" sortable headerStyle="width: 15rem; min-width: 10rem;">
                    <template #filter="{ filterModel }">
                        <Dropdown v-model="filterModel.value" :options="classes" placeholder="Все"
                                  class="p-column-filter"
                                  showClear filter/>
                    </template>
                </Column>
                <Column field="color" header="Цвет" sortable headerStyle="width: 15rem; min-width: 10rem;">
                    <template #filter="{ filterModel }">
                        <Dropdown v-model="filterModel.value" :options="colors" placeholder="Все"
                                  class="p-column-filter"
                                  showClear filter/>
                    </template>
                </Column>
                <Column field="booking.fio" header="Клиент" sortable headerStyle="width: 15rem; min-width: 10rem;">
                    <template #filter="{ filterModel }">
                        <InputText v-model="filterModel.value" type="text" placeholder="Поиск по ФИО"/>
                    </template>
                </Column>
                <Column field="booking.phone" header="Телефон" sortable headerStyle="width: 15rem; min-width: 10rem;">
                    <template #filter="{ filterModel }">
                        <InputText v-model="filterModel.value" type="text" placeholder="Поиск по телефону"/>
                    </template>
                </Column>
                <Column field="booking.start_date" dataType="date" header="Начало" sortable>
                    <template #body="{ data }">
                        {{ formatDate(data, 'start_date') }}
                    </template>
                    <template #filter="{ filterModel }">
                        <DatePicker
                            v-model="filterModel.value"
                            dateFormat="dd.mm.yy" placeholder="дд.мм.гг"
                        />
                    </template>
                </Column>
                <Column field="booking.end_date" dataType="date" header="Возврат" sortable>
                    <template #body="{ data }">
                        {{ formatDate(data, 'end_date') }}
                    </template>
                    <template #filter="{ filterModel }">
                        <DatePicker
                            v-model="filterModel.value"
                            dateFormat="dd.mm.yy" placeholder="дд.мм.гг"
                        />
                    </template>
                </Column>
                <Column field="archive" header="Архив" sortable headerStyle="width: 10rem; min-width: 10rem;">
                    <template #filter="{ filterModel }">
                        <Dropdown v-model="filterModel.value" :options="statuses" placeholder="Все"
                                  class="p-column-filter"
                                  showClear/>
                    </template>
                </Column>
            </template>
            <template #buttons>
                <Button v-if="user.access.auto === 2" type="button" icon="pi pi-plus" label="Добавить" outlined
                        @click="handleAddButtonClick"/>
            </template>
        </Table>
        <ContextMenu ref="contextMenu" :model="contextMenuItems">
            <template #item="{ item, props }">
                <a v-ripple v-bind="props.action"
                   class="flex items-center"
                   :class="{ 'bg-gray-200': item.selected }"
                >
                    <Icon size="24">
                        <component :is="item.icon"/>
                    </Icon>
                    <span class="ml-2">{{ item.label }}</span>
                </a>
            </template>
        </ContextMenu>
    </PageContainer>
</template>