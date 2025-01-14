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

const {user} = useAuthStore();

const items = ref([]);
const models = ref([]);
const brands = ref([]);
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
  'status',
  'body_condition',
  'interior_condition',
  'trunk_condition',
  'need_refuel',
  'need_service',
  'has_failure',
  'archive'
]);

const handleAddButtonClick = (item) => {
  router.push('/Admin/auto/new');
}
const handleRowClick = (item) => {
  router.push('/Admin/auto/' + item.id);
}

const getCarCondition = (condition, data) => {
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

async function fetchData() {
  items.value = await AutoService.getAll({company_id: user.company_id});
  carStatuses.value = await DirectoryService.getAll({directory: 'directory_car_statuses', company_id: user.company_id});
  carStatuses.value = carStatuses.value.filter(item => item.archive === "Активен").map(item => item.name);
  models.value = await DirectoryService.getAll({directory: 'directory_car_models', company_id: user.company_id});
  models.value = models.value.filter(item => item.archive === "Активен").map(item => item.name);
  brands.value = await DirectoryService.getAll({directory: 'directory_car_brands', company_id: user.company_id});
  brands.value = brands.value.filter(item => item.archive === "Активен").map(item => item.name);

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
        :items="items"
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
            <Dropdown v-model="filterModel.value" :options="brands" placeholder="Все" class="p-column-filter"
                      showClear filter/>
          </template>
        </Column>
        <Column field="model" header="Модель" sortable headerStyle="width: 15rem; min-width: 10rem;">
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="models" placeholder="Все" class="p-column-filter"
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
            <Dropdown v-model="filterModel.value" :options="carStatuses" placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
        </Column>
        <Column field="body_condition" headerStyle="max-width: 3.5rem; min-width: 3.5rem;">
          <template #filterheader>
            <label for="state_number"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Состояние кузова</label>
          </template>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="conditions" option-label="label" option-value="value"
                      placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
          <template #body="{data}" class="flex justify-center items-center">
            <div class="flex items-center justify-center">
              <Icon size="24" v-tooltip.top="conditions.find(item => item.value === data.body_condition)?.label">
                <component :is="getCarCondition('body', data)"/>
              </Icon>
            </div>
          </template>
        </Column>
        <Column field="interior_condition" headerStyle="max-width: 3.5rem; min-width: 3.5rem;">
          <template #filterheader>
            <label for="state_number"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Состояние салона</label>
          </template>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="conditions" option-label="label" option-value="value"
                      placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
          <template #body="{data}" class="flex justify-center items-center">
            <div class="flex items-center justify-center">
              <Icon size="24" v-tooltip.top="conditions.find(item => item.value === data.interior_condition)?.label">
                <component :is="getCarCondition('interior', data)"/>
              </Icon>
            </div>
          </template>
        </Column>
        <Column field="trunk_condition" headerStyle="max-width: 3.5rem; min-width: 3.5rem;">
          <template #filterheader>
            <label for="state_number"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Состояние багажника</label>
          </template>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="conditions" option-label="label" option-value="value"
                      placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
          <template #body="{data}" class="flex justify-center items-center">
            <div class="flex items-center justify-center">
              <Icon size="24" v-tooltip.top="conditions.find(item => item.value === data.trunk_condition)?.label">
                <component :is="getCarCondition('trunk', data)"/>
              </Icon>
            </div>
          </template>
        </Column>
        <Column field="need_refuel" headerStyle="max-width: 3.5rem; min-width: 3.5rem;">
          <template #filterheader>
            <label for="state_number"
                   class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Уровень топлива</label>
          </template>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="fuelLevels" option-label="label" option-value="value"
                      placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
          <template #body="{data}" class="flex justify-center items-center">
            <div class="flex items-center justify-center">
              <Icon size="24" v-tooltip.top="fuelLevels.find(item => item.value === data.need_refuel)?.label">
                <component :is="getCarCondition('fuel', data)"/>
              </Icon>
            </div>
          </template>
        </Column>
        <Column field="need_service" headerStyle="max-width: 3.5rem; min-width: 3.5rem;">
          <template #filterheader>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Нужно обслужить?</label>
          </template>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="needService" option-label="label" option-value="value"
                      placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
          <template #body="{data}" class="flex justify-center items-center">
            <div class="flex items-center justify-center">
              <Icon size="24" v-tooltip.top="data.need_service === 0 ? 'Обслужен' : 'Нужно обслужить'">
                <component :is="getCarCondition('to', data)"/>
              </Icon>
            </div>
          </template>
        </Column>
        <Column field="has_failure" header="Неисправность" headerStyle="width: 12rem; min-width: 12rem;">
          <template #filterheader>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Есть неисправность?</label>
          </template>
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="hasFailure" option-label="label" option-value="value"
                      placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
          <template #body="{data}">
            <div class="grid grid-flow-row-dense grid-cols-5">
              <Icon v-if="data.failure != ''" v-tooltip.top="{ value: data.failure, autoHide: false }" size="24">
                <Failure/>
              </Icon>
              <Icon v-if="data.critical_failure != ''" v-tooltip.top="{ value: data.critical_failure, autoHide: false }" size="24">
                <CriticalFailure/>
              </Icon>
            </div>
          </template>
        </Column>
        <Column field="archive" header="Архив" sortable headerStyle="width: 10rem; min-width: 10rem;">
          <template #filter="{ filterModel }">
            <Dropdown v-model="filterModel.value" :options="statuses" placeholder="Все" class="p-column-filter"
                      showClear/>
          </template>
        </Column>
      </template>
      <template #buttons>
        <Button v-if="user.access.auto === 2" type="button" icon="pi pi-plus" label="Добавить" outlined
                @click="handleAddButtonClick"/>
      </template>
    </Table>
  </PageContainer>
</template>